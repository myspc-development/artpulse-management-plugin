<?php
namespace ArtPulse\Community;

class FavoritesRestController {
    public static function register() {
        register_rest_route('artpulse/v1', '/favorite', [
            'methods' => 'POST',
            'callback' => [self::class, 'handle_toggle'],
            'permission_callback' => function() { return is_user_logged_in(); }
        ]);
        register_rest_route('artpulse/v1', '/favorites', [
            'methods' => 'GET',
            'callback' => [self::class, 'handle_get'],
            'permission_callback' => function() { return is_user_logged_in(); }
        ]);
    }

    public static function handle_toggle($request) {
        $user_id    = get_current_user_id();
        $object_id  = intval($request['object_id']);
        $object_type = sanitize_text_field($request['object_type']);
        $action     = sanitize_text_field($request['action']); // "add" or "remove"

        if (!$object_id || !$object_type) {
            return new \WP_Error('invalid', 'Missing object', ['status' => 400]);
        }

        if ($action === 'add') {
            FavoritesManager::add_favorite($user_id, $object_id, $object_type);
            $state = true;
        } else {
            FavoritesManager::remove_favorite($user_id, $object_id, $object_type);
            $state = false;
        }

        return [
            'success'   => true,
            'favorited' => $state,
        ];
    }

    public static function handle_get($request) {
        $user_id = get_current_user_id();
        $type    = sanitize_text_field($request['object_type'] ?? '');
        $favs    = FavoritesManager::get_user_favorites($user_id, $type ?: null);

        $results = [];
        foreach ($favs as $f) {
            $map = [
                'artist'  => 'artpulse_artist',
                'event'   => 'artpulse_event',
                'artwork' => 'artpulse_artwork',
                'org'     => 'artpulse_org',
            ];
            $post_type = $map[$f->object_type] ?? 'artpulse_' . $f->object_type;
            $post = get_post($f->object_id);
            if ($post && $post->post_type === $post_type) {
                $results[] = [
                    'object_id'          => $f->object_id,
                    'object_type'        => $f->object_type,
                    'favorited_on'       => $f->favorited_on,
                    'title'              => get_the_title($f->object_id),
                    'permalink'          => get_permalink($f->object_id),
                    'featured_media_url' => get_the_post_thumbnail_url($f->object_id, 'medium'),
                ];
            }
        }

        return rest_ensure_response($results);
    }
}

// Hook to REST API, using FQCN for Composer autoload
add_action('rest_api_init', [\ArtPulse\Community\FavoritesRestController::class, 'register']);
