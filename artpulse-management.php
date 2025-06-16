
<?php
/**
 * Plugin Name: ArtPulse Management
 * Description: Management plugin for ArtPulse.
 * Version:     0.1.0
 * Author:      Your Name
 * License:     GPL2
 */

if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
    require_once __DIR__ . '/vendor/autoload.php';
}

/**
 * Runs on plugin activation.
 * - Creates default options.
 * - Registers CPTs so rewrite rules can be flushed.
 */
function artpulse_activate() {
    // Example: create default options
    if ( false === get_option( 'artpulse_settings' ) ) {
        add_option( 'artpulse_settings', [
            'version' => '0.1.0',
            // add other default values here
        ] );
    }

    // Register your CPTs here (or call your Core setup class) so rewrite rules include them.
    // \ArtPulse\Core\Setup::register_post_types();

    // Flush rules so custom post types’ URLs work immediately.
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'artpulse_activate' );

/**
 * Runs on plugin deactivation.
 * - Flushes rewrite rules.
 */
function artpulse_deactivate() {
    // Flush rewrite rules to remove our CPT rules.
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'artpulse_deactivate' );

add_action('init', function() {
    \ArtPulse\Core\PostTypeRegistrar::register();
    \ArtPulse\Core\MetaBoxRegistrar::register();
});

