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

// Instantiate the plugin class
$plugin = new Plugin();


// All other hooks (init, enqueue, REST API, etc.) are registered inside Plugin::__construct()
