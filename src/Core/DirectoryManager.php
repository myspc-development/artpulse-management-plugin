<?php
namespace ArtPulse\Core;

use WP_REST_Request;

class DirectoryManager {
    public static function register() {
        add_shortcode('ap_directory',   [ self::class, 'renderDirectory' ]);
        add_action('wp_enqueue_scripts',[ self::class, 'enqueueAssets'  ]);
        add_action('rest_api_init',     [ self::class, 'registerRestRoutes' ]);
    }

    public static function enqueueAssets() {
        wp_enqueue_script(
            'ap-directory-js',
            plugins_url('assets/js/ap-directory.js', __FILE__),
            ['wp-api-fetch'],
            '1.0.0',
            true
        );
        wp_localize_script('ap-directory-js', 'ArtPulseApi', [
            'root'  => esc_url_raw(rest_url()),
            'nonce' => wp_create_nonce('wp_rest'),
        ]);
        wp_enqueue_style(
            'ap-directory-css',
            plugins_url('assets/css/ap-directory.css', __FILE__),
            [],
            '1.0.0'
        );
    }

    public static function registerRestRoutes() {
        register_rest_route('artpulse/v1', '/filter', [
            'methods'             => 'GET',
            'callback'            => [ self::class, 'handleFilter' ],
            'permission_callback' => '__return_true',
        ]);
    }

    public static function handleFilter(WP_REST_Request $request) {
        $type  = sanitize_text_field( $request->get_param('type') );
        $limit = intval( $request->get_param('limit') ?? 10 );

        $args = [
            'post_type'      => 'artpulse_' . $type,
            'posts_per_page' => $limit,
        ];
        $posts = get_posts($args);

        $data = array_map(function($p) use ($type) {
            $item = [
                'id'      => $p->ID,
                'title'   => $p->post_title,
                'link'    => get_permalink($p),
                'featured_media_url' => get_the_post_thumbnail_url($p, 'medium'),
            ];

            // Pull in meta fields per type
            if ($type === 'event') {
                $item['date']     = get_post_meta($p->ID, '_ap_event_date', true);
                $item['location'] = get_post_meta($p->ID, '_ap_event_location', true);
            } elseif ($type === 'artist') {
                $item['bio']       = get_post_meta($p->ID, '_ap_artist_bio', true);
                $item['org_id']    = (int) get_post_meta($p->ID, '_ap_artist_org', true);
            } elseif ($type === 'artwork') {
                $item['medium']     = get_post_meta($p->ID, '_ap_artwork_medium', true);
                $item['dimensions'] = get_post_meta($p->ID, '_ap_artwork_dimensions', true);
                $item['materials']  = get_post_meta($p->ID, '_ap_artwork_materials', true);
            } elseif ($type === 'org') {
                $item['address'] = get_post_meta($p->ID, '_ap_org_address', true);
                $item['website'] = get_post_meta($p->ID, '_ap_org_website', true);
            }

            return $item;
        }, $posts);

        return rest_ensure_response($data);
    }

    public static function renderDirectory($atts) {
        $atts = shortcode_atts([
            'type'  => 'event',
            'limit' => 10,
        ], $atts, 'ap_directory');

        ob_start(); ?>
        <div class="ap-directory" data-type="<?php echo esc_attr($atts['type']); ?>" data-limit="<?php echo esc_attr($atts['limit']); ?>">
            <div class="ap-directory-filters">
                <?php if ($atts['type'] === 'event'): ?>
                    <label><?php _e('Filter by Event Type','artpulse'); ?>:</label>
                    <select class="ap-filter-event-type"></select>
                <?php endif; ?>
                <label><?php _e('Limit','artpulse'); ?>:</label>
                <input type="number" class="ap-filter-limit" value="<?php echo esc_attr($atts['limit']); ?>" />
                <button class="ap-filter-apply"><?php _e('Apply','artpulse'); ?></button>
            </div>
            <div class="ap-directory-results"></div>
        </div>
        <?php
        return ob_get_clean();
    }
}
