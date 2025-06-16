<?php
namespace ArtPulse\Core;

class WooCommerceIntegration
{
    public static function register()
    {
        // Fires on order completion
        add_action('woocommerce_order_status_completed', [ self::class, 'handleCompletedOrder' ], 10, 1);
    }

    /**
     * When a WC order is marked completed, check for membership products and assign level.
     *
     * @param int $order_id
     */
    public static function handleCompletedOrder( $order_id )
    {
        if ( ! class_exists('WC_Order') ) {
            return;
        }

        $order   = wc_get_order( $order_id );
        $user_id = $order->get_user_id();
        if ( ! $user_id ) {
            return;
        }

        // Get product‐to‐level mappings from settings
        $opts = get_option('artpulse_settings', []);
        $map = [
            'Free'  => intval( $opts['woo_basic_product_id'] ?? 0 ),
            'Pro'   => intval( $opts['woo_pro_product_id']   ?? 0 ),
            'Org'   => intval( $opts['woo_org_product_id']   ?? 0 ),
        ];

        // Traverse line items
        foreach ( $order->get_items() as $item ) {
            $prod_id = $item->get_product_id();
            foreach ( $map as $level => $product_id ) {
                if ( $product_id && $prod_id === $product_id ) {
                    // Assign role and meta
                    $user = get_userdata( $user_id );
                    $user->set_role( 'subscriber' ); // keep base role
                    update_user_meta( $user_id, 'ap_membership_level', $level );

                    // Compute expiry: 1 month from now for Pro/Org, 1 year for Org if you prefer
                    $days = ( 'Org' === $level ) ? 365 : 30;
                    $expiry = strtotime( "+{$days} days", current_time('timestamp') );
                    update_user_meta( $user_id, 'ap_membership_expires', $expiry );

                    // Optionally send email
                    wp_mail(
                        $user->user_email,
                        sprintf( __('Your ArtPulse membership is now %s','artpulse'), $level ),
                        sprintf( __('Thank you for your purchase! Your membership level has been set to %s and expires on %s.','artpulse'),
                            $level,
                            date_i18n( get_option('date_format'), $expiry )
                        )
                    );
                    // Only honor one level per order
                    break 2;
                }
            }
        }
    }
}
