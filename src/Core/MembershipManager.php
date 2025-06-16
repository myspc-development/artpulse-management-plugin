<?php
namespace ArtPulse\Core;

use Stripe\StripeClient;

class MembershipManager
{
    public static function register()
    {
        // Assign free membership on user registration
        add_action('user_register', [ self::class, 'assignFreeMembership' ]);

        // Register Stripe webhook endpoint
        add_action('rest_api_init', [ self::class, 'registerRestRoutes' ]);

        // Schedule daily expiry checks
        add_action('ap_daily_expiry_check', [ self::class, 'processExpirations' ]);
    }

    public static function assignFreeMembership($user_id)
    {
        $user = get_userdata($user_id);
        $user->set_role('subscriber');
        update_user_meta($user_id, 'ap_membership_level', 'Free');
    }

    public static function registerRestRoutes()
    {
        register_rest_route('artpulse/v1', '/stripe-webhook', [
            'methods'             => 'POST',
            'callback'            => [ self::class, 'handleStripeWebhook' ],
            'permission_callback' => '__return_true',
        ]);
    }

    public static function handleStripeWebhook(\WP_REST_Request $request)
    {
        $payload    = $request->get_body();
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
        $settings   = get_option('artpulse_settings', []);
        $secret     = $settings['stripe_webhook_secret'] ?? '';

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sig_header, $secret);
        } catch (\Exception $e) {
            return new \WP_Error('stripe_error', $e->getMessage(), ['status' => 400]);
        }

        switch ($event->type) {
            case 'checkout.session.completed':
                $session    = $event->data->object;
                // TODO: find the user by metadata (e.g. client_reference_id),
                // update_user_meta($user_id, 'ap_membership_level', 'Pro');
                // update_user_meta($user_id, 'ap_membership_expires', $expiryTimestamp);
                break;

            // TODO: handle other event types (subscription renewals, cancellations, etc.)
        }

        return rest_ensure_response(['received' => true]);
    }

    public static function processExpirations()
    {
        $today = current_time('timestamp');
        $expired_users = get_users([
            'meta_key'     => 'ap_membership_expires',
            'meta_value'   => $today,
            'meta_compare' => '<=',
        ]);

        foreach ($expired_users as $user) {
            $user->set_role('subscriber');
            update_user_meta($user->ID, 'ap_membership_level', 'Free');
            // Optionally send notification email here
        }
    }
}
