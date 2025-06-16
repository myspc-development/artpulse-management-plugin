<?php
namespace ArtPulse\Core;

use Stripe\StripeClient;

class MembershipManager
{
    public static function register()
    {
        add_action('user_register',       [self::class,'assignFreeMembership']);
        add_action('ap_stripe_webhook',   [self::class,'handleStripeWebhook'], 10, 1);
        add_action('ap_daily_expiry_check', [self::class,'processExpirations']);
    }

    public static function assignFreeMembership($user_id)
    {
        $role = 'subscriber';
        $user = get_userdata($user_id);
        $user->set_role($role);
        update_user_meta($user_id, 'ap_membership_level', 'Free');
    }

    public static function handleStripeWebhook($payload)
    {
        $stripe = new StripeClient(get_option('artpulse_settings')['stripe_secret']);
        // TODO: parse $payload, update user_meta with level/expiry, send emails
    }

    public static function processExpirations()
    {
        // TODO: query users with expired membership, downgrade role, notify
    }
}
