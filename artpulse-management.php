<?php
/**
 * Plugin Name:     ArtPulse Management
 * Description:     Management plugin for ArtPulse.
 * Version:         1.1.5
 * Author:          craig
 * Text Domain:     artpulse
 * License:         GPL-2.0-or-later
 */

// Suppress deprecated notices when WP_DEBUG is enabled
if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
    @ini_set( 'display_errors', '0' );
    @error_reporting( E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED );
}

// Composer autoloader
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
    require_once __DIR__ . '/vendor/autoload.php';
}

// Optional debug check—only when WP_DEBUG is true
if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
    if ( class_exists( \ArtPulse\Core\Plugin::class ) ) {
        error_log( 'ArtPulse\Core\Plugin loaded successfully.' );
    } else {
        error_log( 'Error: ArtPulse\Core\Plugin failed to load.' );
    }
}

use ArtPulse\Core\Plugin;
use ArtPulse\Frontend\SubmissionForms; // Add this use statement

// Instantiate the plugin class
$plugin = new Plugin();

// Register activation and deactivation hooks
register_activation_hook(   __FILE__, [ $plugin, 'activate'   ] );
register_deactivation_hook( __FILE__, [ $plugin, 'deactivate' ] );

// All other hooks (init, enqueue, REST API, etc.) are registered inside Plugin::__construct()

/**
 * Plugin activation hook.
 */
function artpulse_activate() {
    // Call the register method to ensure post types are registered.
    SubmissionForms::register(); // Ensure post types are registered

    $post_types = [
        'artpulse_event',
        'artpulse_artist',
        'artpulse_artwork',
        //'organization',  // Correct post type names
        //'org_review',
        //'membership_request',
    ];

    $roles = ['administrator']; // You could also add a custom role here
    foreach ($post_types as $type) {
        $caps = SubmissionForms::generate_caps($type); // Use the class name
        foreach ($roles as $role_name) {
            if ($role = get_role($role_name)) {
                foreach ($caps as $cap) {
                    $role->add_cap($cap);
                }
            }
        }
    }

    // Optional: Create a new role with limited capabilities
    if (!get_role('artpulse_editor')) {
        add_role('artpulse_editor', 'ArtPulse Editor', []);
        $editor_role = get_role('artpulse_editor');
        foreach ($post_types as $type) {
            $caps = SubmissionForms::generate_caps($type); // Use the class name
            foreach ($caps as $cap) {
                $editor_role->add_cap($cap);
            }
        }
    }

    flush_rewrite_rules(); // Important:  Flush rewrite rules after activation
}

/**
 * Plugin deactivation hook.
 */
function artpulse_deactivate() {
    // Remove the custom role on deactivation (optional)
    remove_role('artpulse_editor');

    flush_rewrite_rules();  //Flush rewrite rules after deactivation too
}

// Register the activation and deactivation hooks using the global functions
register_activation_hook( __FILE__, 'artpulse_activate' );
register_deactivation_hook( __FILE__, 'artpulse_deactivate' );

// Initialize the plugin (example)
add_action('init', function() {
    SubmissionForms::register(); // Register shortcodes, forms, etc.
});