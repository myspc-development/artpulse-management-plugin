<?php
/**
 * Plugin Name:     ArtPulse Management
 * Description:     Management plugin for ArtPulse.
 * Version:         0.1.0
 * Author:          Your Name
 * Text Domain:     artpulse
 * License:         GPL2
 */

if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
    require_once __DIR__ . '/vendor/autoload.php';
}

/**
 * Runs on plugin activation.
 */
function artpulse_activate() {
    // Default options
    if ( false === get_option( 'artpulse_settings' ) ) {
        add_option( 'artpulse_settings', [ 'version' => '0.1.0' ] );
    }

    // Register CPTs so flush_rewrite_rules() picks them up
    \ArtPulse\Core\PostTypeRegistrar::register();
    flush_rewrite_rules();

    // Schedule daily expiry check (Phase 3)
    if ( ! wp_next_scheduled( 'ap_daily_expiry_check' ) ) {
        wp_schedule_event( time(), 'daily', 'ap_daily_expiry_check' );
    }
}
register_activation_hook( __FILE__, 'artpulse_activate' );

/**
 * Runs on plugin deactivation.
 */
function artpulse_deactivate() {
    flush_rewrite_rules();
    wp_clear_scheduled_hook( 'ap_daily_expiry_check' );
}
register_deactivation_hook( __FILE__, 'artpulse_deactivate' );

// Hook all core modules
add_action( 'init', function() {
    \ArtPulse\Core\PostTypeRegistrar::register();
    \ArtPulse\Core\MetaBoxRegistrar::register();        // Phase 2.1
    \ArtPulse\Core\SettingsPage::register();            // Phase 3
    \ArtPulse\Core\MembershipManager::register();       // Phase 3
    \ArtPulse\Core\AccessControlManager::register();    // Phase 3
});
