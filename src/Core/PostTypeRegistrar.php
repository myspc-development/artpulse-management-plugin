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
        ];

        // 1) Events
        register_post_type('artpulse_event', array_merge($common, [
            'label'           => __('Events', 'artpulse'),
            'supports'        => ['title','editor','thumbnail','excerpt'],
            'rewrite'         => ['slug'=>'events'],
            'taxonomies'      => ['artpulse_event_type'],
            'capability_type' => 'artpulse_event',
            'capabilities'    => [
                'edit_post'           => 'edit_artpulse_event',
                'read_post'           => 'read_artpulse_event',
                'delete_post'         => 'delete_artpulse_event',
                'edit_posts'          => 'edit_artpulse_events',
                'edit_others_posts'   => 'edit_others_artpulse_events',
                'publish_posts'       => 'publish_artpulse_events',
                'read_private_posts'  => 'read_private_artpulse_events',
            ],
            'map_meta_cap'    => true,
        ]));

        // 2) Artists
        register_post_type('artpulse_artist', array_merge($common, [
            'label'           => __('Artists', 'artpulse'),
            'supports'        => ['title','editor','thumbnail','custom-fields'],
            'rewrite'         => ['slug'=>'artists'],
            'capability_type' => 'artpulse_artist',
            'capabilities'    => [
                'edit_post'           => 'edit_artpulse_artist',
                'read_post'           => 'read_artpulse_artist',
                'delete_post'         => 'delete_artpulse_artist',
                'edit_posts'          => 'edit_artpulse_artists',
                'edit_others_posts'   => 'edit_others_artpulse_artists',
                'publish_posts'       => 'publish_artpulse_artists',
                'read_private_posts'  => 'read_private_artpulse_artists',
            ],
            'map_meta_cap'    => true,
        ]));

        // 3) Artworks
        register_post_type('artpulse_artwork', array_merge($common, [
            'label'           => __('Artworks', 'artpulse'),
            'supports'        => ['title','editor','thumbnail','custom-fields'],
            'rewrite'         => ['slug'=>'artworks'],
            'capability_type' => 'artpulse_artwork',
            'capabilities'    => [
                'edit_post'           => 'edit_artpulse_artwork',
                'read_post'           => 'read_artpulse_artwork',
                'delete_post'         => 'delete_artpulse_artwork',
                'edit_posts'          => 'edit_artpulse_artworks',
                'edit_others_posts'   => 'edit_others_artpulse_artworks',
                'publish_posts'       => 'publish_artpulse_artworks',
                'read_private_posts'  => 'read_private_artpulse_artworks',
            ],
            'map_meta_cap'    => true,
        ]));

        // 4) Organizations
        register_post_type('artpulse_org', array_merge($common, [
            'label'           => __('Organizations', 'artpulse'),
            'supports'        => ['title','editor','thumbnail'],
            'rewrite'         => ['slug'=>'organizations'],
            'capability_type' => 'artpulse_org',
            'capabilities'    => [
                'edit_post'           => 'edit_artpulse_org',
                'read_post'           => 'read_artpulse_org',
                'delete_post'         => 'delete_artpulse_org',
                'edit_posts'          => 'edit_artpulse_orgs',
                'edit_others_posts'   => 'edit_others_artpulse_orgs',
                'publish_posts'       => 'publish_artpulse_orgs',
                'read_private_posts'  => 'read_private_artpulse_orgs',
            ],
            'map_meta_cap'    => true,
        ]));

        // Taxonomies
        register_taxonomy('artpulse_event_type','artpulse_event',[
            'label'=>__('Event Types','artpulse'),
            'public'=>true,'show_in_rest'=>true,'hierarchical'=>true,'rewrite'=>['slug'=>'event-types'],
        ]);
        register_taxonomy('artpulse_medium','artpulse_artwork',[
            'label'=>__('Medium','artpulse'),
            'public'=>true,'show_in_rest'=>true,'hierarchical'=>true,'rewrite'=>['slug'=>'medium'],
        ]);
    }
}
