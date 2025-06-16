<?php
function artpulse_install_favorites_table() {
    global $wpdb;
    $table = $wpdb->prefix . 'ap_favorites';
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE $table (
        id BIGINT AUTO_INCREMENT PRIMARY KEY,
        user_id BIGINT NOT NULL,
        object_id BIGINT NOT NULL,
        object_type VARCHAR(32) NOT NULL,
        favorited_on DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY user_object (user_id, object_id, object_type)
    ) $charset_collate;";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
register_activation_hook(__FILE__, 'artpulse_install_favorites_table');
