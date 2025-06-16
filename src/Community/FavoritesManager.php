<?php
namespace ArtPulse\Community;

class FavoritesManager {
    public static function add_favorite($user_id, $object_id, $object_type) {
        global $wpdb;
        $table = $wpdb->prefix . 'ap_favorites';
        $wpdb->replace($table, [
            'user_id'      => $user_id,
            'object_id'    => $object_id,
            'object_type'  => $object_type,
            'favorited_on' => current_time('mysql')
        ]);
    }

    public static function remove_favorite($user_id, $object_id, $object_type) {
        global $wpdb;
        $table = $wpdb->prefix . 'ap_favorites';
        $wpdb->delete($table, [
            'user_id'     => $user_id,
            'object_id'   => $object_id,
            'object_type' => $object_type,
        ]);
    }

    public static function is_favorited($user_id, $object_id, $object_type) {
        global $wpdb;
        $table = $wpdb->prefix . 'ap_favorites';
        return (bool) $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM $table WHERE user_id = %d AND object_id = %d AND object_type = %s",
                $user_id, $object_id, $object_type
            )
        );
    }

    public static function get_user_favorites($user_id, $object_type = null) {
        global $wpdb;
        $table = $wpdb->prefix . 'ap_favorites';
        $sql = "SELECT object_id, object_type, favorited_on FROM $table WHERE user_id = %d";
        $params = [ $user_id ];
        if ($object_type) {
            $sql .= " AND object_type = %s";
            $params[] = $object_type;
        }
        $sql .= " ORDER BY favorited_on DESC";
        return $wpdb->get_results($wpdb->prepare($sql, ...$params));
    }

    /**
     * Installer: create the favorites table
     */
    public static function install_favorites_table() {
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
}
