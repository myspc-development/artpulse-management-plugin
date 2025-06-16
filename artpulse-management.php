<?php
/**
 * Plugin Name:     ArtPulse Management
 * Description:     Management plugin for ArtPulse.
 * Version:         1.1.5
 * Author:          craig
 * Text Domain:     artpulse
 * License:         GPL2
 */

if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
    @ini_set( 'display_errors', '0' );
    @error_reporting( E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED );
}

// Composer autoload
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
    require_once __DIR__ . '/vendor/autoload.php';
}

/**
 * Runs on plugin activation. Handles DB migrations.
 */
function artpulse_activate() {
    $plugin_version = '1.1.5';
    $db_version_option = 'artpulse_db_version';

    // Ensure settings exist and store plugin version
    if ( false === get_option( 'artpulse_settings' ) ) {
        add_option( 'artpulse_settings', [ 'version' => $plugin_version ] );
    } else {
        $settings = get_option('artpulse_settings');
        $settings['version'] = $plugin_version;
        update_option('artpulse_settings', $settings);
    }

    // Only run dbDelta if version changed or not set
    $stored_db_version = get_option($db_version_option);
    if ( $stored_db_version !== $plugin_version ) {
        \ArtPulse\Community\FavoritesManager::install_favorites_table();
        \ArtPulse\Community\ProfileLinkRequestManager::install_link_request_table();
        \ArtPulse\Community\FollowManager::install_follows_table();
        \ArtPulse\Community\NotificationManager::install_notifications_table();
        update_option($db_version_option, $plugin_version);
    }

    \ArtPulse\Core\PostTypeRegistrar::register();
    flush_rewrite_rules();

    $roles = [ 'administrator', 'editor' ];
    $caps  = [
        // CPT capabilities...
    ];
    foreach ( $roles as $role_name ) {
        if ( $role = get_role( $role_name ) ) {
            foreach ( $caps as $cap ) {
                $role->add_cap( $cap );
            }
        }
    }

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
 * Use bundled Salient templates for all ArtPulse CPTs.
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
    \ArtPulse\Core\FrontendMembershipPage::register();
    \ArtPulse\Community\ProfileLinkRequestManager::register();
    \ArtPulse\Core\MyFollowsShortcode::register();

    // WooCommerce purchase & lifecycle (if enabled)
    $opts = get_option( 'artpulse_settings', [] );
    if ( ! empty( $opts['woo_enabled'] ) ) {
        \ArtPulse\Core\WooCommerceIntegration::register();
        \ArtPulse\Core\PurchaseShortcode::register();
    }
    // Do NOT install tables here (only in activate!)
} );

/**
 * Enqueue front-end assets.
 */
add_action( 'wp_enqueue_scripts', function() {
    wp_enqueue_script(
        'ap-membership-account-js',
        plugins_url( 'assets/js/ap-membership-account.js', __FILE__ ),
        [ 'wp-api-fetch' ],
        '1.0.0',
        true
    );
    wp_enqueue_script(
        'ap-favorites-js',
        plugins_url('assets/js/ap-favorites.js', __FILE__),
        [],
        '1.0.0',
        true
    );
    wp_localize_script(
        'ap-membership-account-js',
        'ArtPulseApi',
        [
            'root'        => esc_url_raw( rest_url() ),
            'nonce'       => wp_create_nonce( 'wp_rest' ),
            'i18n'        => [
                'levelLabel'    => __( 'Membership Level', 'artpulse' ),
                'expiresLabel'  => __( 'Expires', 'artpulse' ),
                'errorFetching' => __( 'Unable to fetch account data. Please try again later.', 'artpulse' ),
            ],
        ]
    );
} );

// Register Notification REST routes on the CORRECT HOOK!
add_action(
    'rest_api_init',
    [\ArtPulse\Community\NotificationRestController::class, 'register']
);
