<?php
namespace ArtPulse\Admin;

class AdminListSorting {
    public static function register() {
        add_action('pre_get_posts', [self::class, 'sort_admin_columns']);

        // Make columns sortable
        add_filter('manage_edit-artpulse_artist_sortable_columns', function ($columns) {
            $columns['artist_featured'] = 'artist_featured';
            return $columns;
        });

        add_filter('manage_edit-artpulse_artwork_sortable_columns', function ($columns) {
            $columns['artwork_featured'] = 'artwork_featured';
            return $columns;
        });

        add_filter('manage_edit-artpulse_event_sortable_columns', function ($columns) {
            $columns['event_featured'] = 'event_featured';
            return $columns;
        });

        add_filter('manage_edit-artpulse_org_sortable_columns', function ($columns) {
            $columns['artpulse_org_type'] = 'artpulse_org_type';
            return $columns;
        });
    }

    public static function sort_admin_columns(\WP_Query $query) {
        if (!is_admin() || !$query->is_main_query()) return;

        $orderby = $query->get('orderby');

        $sortable_meta_keys = [
            'artist_featured',
            'artwork_featured',
            'event_featured',
            'artpulse_org_type',
        ];

        if (in_array($orderby, $sortable_meta_keys)) {
            $query->set('meta_key', $orderby);
            $query->set('orderby', 'meta_value');
        }
    }
}
