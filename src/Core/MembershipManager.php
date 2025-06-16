<?php
namespace ArtPulse\Core;

use Stripe\StripeClient;
use WP_REST_Request;
use WP_Error;

class MembershipManager
{
    /**
     * Hook all actions.
     */
    public static function register()
    {
        // Assign free membership on user registration
        add_action('user_register', [ self::class, 'assignFreeMembership' ]);

        // Register Stripe webhook endpoint
        add_action('rest_api_init', [ self::class, 'registerRestRoutes' ]);

        // Schedule daily expiry checks
        add_action('ap_daily_expiry_check', [ self::class, 'processExpirations' ]);
    }

    /**
     * Give every new user the Free level.
     */
    public static function assignFreeMembership($user_id)
    {
        $user = get_userdata($user_id);
        $user->set_role('subscriber');
        update_user_meta($user_id, 'ap_membership_level', 'Free');
    }

    /**
     * Expose a public REST endpoint for Stripe webhooks.
     */
    public static function registerRestRoutes()
    {
        register_rest_route('artpulse/v1', '/stripe-webhook', [
            'methods'             => 'POST',
            'callback'            => [ self::class, 'handleStripeWebhook' ],
            'permission_callback' => '__return_true',
        ]);
    }

    /**
     * Handle incoming Stripe webhook events.
     */
    public static function handleStripeWebhook(WP_REST_Request $request)
    {
        $payload    = $request->get_body();
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
        $settings   = get_option('artpulse_settings', []);
        $secret     = $settings['stripe_webhook_secret'] ?? '';

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sig_header, $secret);
        } catch (\UnexpectedValueException $e) {
            return new WP_Error('invalid_payload', 'Invalid payload', ['status' => 400]);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return new WP_Error('invalid_signature', 'Invalid signature', ['status' => 400]);
        }

        switch ($event->type) {
            case 'checkout.session.completed':
                $session    = $event->data->object;
                $user_id    = isset($session->client_reference_id) ? absint($session->client_reference_id) : 0;
                if ($user_id) {
                    // Store Stripe customer ID
                    update_user_meta($user_id, 'stripe_customer_id', sanitize_text_field($session->customer));
                    // Upgrade to Pro
                    update_user_meta($user_id, 'ap_membership_level', 'Pro');
                    // Set expiry one month from now
                    $expiry = strtotime('+1 month', current_time('timestamp'));
                    update_user_meta($user_id, 'ap_membership_expires', $expiry);
                }
                break;

            // TODO: handle subscription renewals, cancellations, etc.

            default:
                // We’re not handling other events right now
                break;
        }

        return rest_ensure_response(['received' => true]);
    }

    /**
     * Demote any users whose membership has expired.
     */
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
            // optionally notify via email here
        }
    }
}
