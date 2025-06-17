<?php

namespace ArtPulse\Core;

class PostTypeRegistrar
{
    public static function register()
    {
        $common = [
            'public'       => true,
            'show_in_rest' => true,
            'has_archive'  => true,
            'supports'     => ['title', 'editor', 'thumbnail'],
        ];

        $template_common = [
            ['core/paragraph', ['placeholder' => 'Write something...']],
        ];

        $locked = 'all';

        $post_types = [
            'artpulse_event' => [
                'label'     => __('Events', 'artpulse'),
                'menu_icon' => 'dashicons-calendar',
                'rewrite'   => ['slug' => 'events'],
                'taxonomies' => ['artpulse_event_type'],
            ],
            'artpulse_artist' => [
                'label'     => __('Artists', 'artpulse'),
                'menu_icon' => 'dashicons-admin-users',
                'rewrite'   => ['slug' => 'artists'],
            ],
            'artpulse_artwork' => [
                'label'     => __('Artworks', 'artpulse'),
                'menu_icon' => 'dashicons-art',
                'rewrite'   => ['slug' => 'artworks'],
            ],
            'artpulse_org' => [
                'label'     => __('Organizations', 'artpulse'),
                'menu_icon' => 'dashicons-building',
                'rewrite'   => ['slug' => 'organizations'],
            ],
            'artpulse_org_review' => [
                'label'     => __('Org Reviews', 'artpulse'),
                'menu_icon' => 'dashicons-visibility',
                'rewrite'   => ['slug' => 'org-reviews'],
                'public'    => false,
                'show_ui'   => true,
            ],
            'ap_membership_request' => [
                'label'     => __('Membership Requests', 'artpulse'),
                'menu_icon' => 'dashicons-id',
                'rewrite'   => ['slug' => 'membership-requests'],
                'public'    => false,
                'show_ui'   => true,
            ],
        ];

        foreach ($post_types as $type => $args) {
            $merged_args = array_merge(
                $common,
                $args,
                [
                    'capability_type' => $type,
                    'capabilities'    => self::generate_caps($type),
                    'map_meta_cap'    => true,
                    'template'        => $template_common,
                    'template_lock'   => $locked,
                    'show_in_rest'    => true,
                ]
            );

            // Override public and show_ui if explicitly set in $args
            if (isset($args['public'])) {
                $merged_args['public'] = $args['public'];
            }
            if (isset($args['show_ui'])) {
                $merged_args['show_ui'] = $args['show_ui'];
            }

            register_post_type($type, $merged_args);
        }

        // Register Taxonomies
        register_taxonomy('artpulse_event_type', 'artpulse_event', [
            'label'        => __('Event Types', 'artpulse'),
            'public'       => true,
            'show_in_rest' => true,
            'hierarchical' => true,
            'rewrite'      => ['slug' => 'event-types'],
        ]);

        register_taxonomy('artpulse_medium', 'artpulse_artwork', [
            'label'        => __('Medium', 'artpulse'),
            'public'       => true,
            'show_in_rest' => true,
            'hierarchical' => true,
            'rewrite'      => ['slug' => 'medium'],
        ]);
    }

    public static function generate_caps($type): array
    {
        return [
            "edit_{$type}",
            "read_{$type}",
            "delete_{$type}",
            "edit_{$type}s",
            "edit_others_{$type}s",
            "publish_{$type}s",
            "read_private_{$type}s",
            "delete_{$type}s",
            "delete_private_{$type}s",
            "delete_published_{$type}s",
            "delete_others_{$type}s",
            "edit_private_{$type}s",
            "edit_published_{$type}s",
            'read',
            'create_posts', // Added create_posts capability
        ];
    }
}