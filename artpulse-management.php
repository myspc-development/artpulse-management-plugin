<?php
/**
 * Plugin Name:     ArtPulse Management
 * Description:     Management plugin for ArtPulse.
 * Version:         0.1.0
 * Author:          Your Name
 * Text Domain:     artpulse
 * License:         GPL2
 */

// Hide deprecation notices in debug mode so they don’t break admin screens
if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
    @ini_set( 'display_errors', '0' );
    @error_reporting( E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED );
}

// Load Composer autoloader
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
    require_once __DIR__ . '/vendor/autoload.php';
}

/**
 * Runs on plugin activation.
 */
function artpulse_activate() {
    // 1) Default options
    if ( false === get_option( 'artpulse_settings' ) ) {
        add_option( 'artpulse_settings', [ 'version' => '0.1.0' ] );
    }

    // 2) Register CPTs and flush rewrite rules
    \ArtPulse\Core\PostTypeRegistrar::register();
    flush_rewrite_rules();

    // 3) Assign custom capabilities
    $roles = [ 'administrator', 'editor' ];
    $caps = [
        // Events
        'edit_artpulse_event', 'read_artpulse_event', 'delete_artpulse_event',
        'edit_artpulse_events', 'edit_others_artpulse_events',
        'publish_artpulse_events', 'read_private_artpulse_events',
        // Artists
        'edit_artpulse_artist', 'read_artpulse_artist', 'delete_artpulse_artist',
        'edit_artpulse_artists', 'edit_others_artpulse_artists',
        'publish_artpulse_artists', 'read_private_artpulse_artists',
        // Artworks
        'edit_artpulse_artwork', 'read_artpulse_artwork', 'delete_artpulse_artwork',
        'edit_artpulse_artworks', 'edit_others_artpulse_artworks',
        'publish_artpulse_artworks', 'read_private_artpulse_artworks',
        // Organizations
        'edit_artpulse_org', 'read_artpulse_org', 'delete_artpulse_org',
        'edit_artpulse_orgs', 'edit_others_artpulse_orgs',
        'publish_artpulse_orgs', 'read_private_artpulse_orgs',
    ];
    foreach ( $roles as $role_name ) {
        if ( $role = get_role( $role_name ) ) {
            foreach ( $caps as $cap ) {
                $role->add_cap( $cap );
            }
        }
    }

    // 4) Schedule daily expiry check
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

/**
 * Use our bundled Salient templates for all ArtPulse CPTs.
 */
add_filter( 'template_include', function( $template ) {
    $pt   = get_post_type();
    $cpts = [ 'artpulse_event', 'artpulse_artist', 'artpulse_artwork', 'artpulse_org' ];
    if ( in_array( $pt, $cpts, true ) ) {
        $single  = plugin_dir_path( __FILE__ ) . "templates/salient/content-{$pt}.php";
        $archive = plugin_dir_path( __FILE__ ) . "templates/salient/archive-{$pt}.php";
        if ( is_singular( $pt ) && file_exists( $single ) ) {
            return $single;
        }
        if ( is_post_type_archive( $pt ) && file_exists( $archive ) ) {
            return $archive;
        }
    }
    return $template;
} );

/**
 * Hook all core modules on init.
 */
add_action( 'init', function() {
    \ArtPulse\Core\PostTypeRegistrar::register();
    \ArtPulse\Core\MetaBoxRegistrar::register();
    \ArtPulse\Core\AdminDashboard::register();
    \ArtPulse\Core\ShortcodeManager::register();
    \ArtPulse\Core\SettingsPage::register();
    \ArtPulse\Core\MembershipManager::register();
    \ArtPulse\Core\AccessControlManager::register();
    \ArtPulse\Core\DirectoryManager::register();
    \ArtPulse\Core\UserDashboardManager::register();
    \ArtPulse\Core\AnalyticsManager::register();
    \ArtPulse\Core\AnalyticsDashboard::register();

    // Purchase shortcode is only useful when WooCommerce integration is on
    $opts = get_option( 'artpulse_settings', [] );
    if ( ! empty( $opts['woo_enabled'] ) ) {
        \ArtPulse\Core\WooCommerceIntegration::register();
        \ArtPulse\Core\PurchaseShortcode::register();
    }
} );
