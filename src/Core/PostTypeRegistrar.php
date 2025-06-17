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

        // Events
        register_post_type('artpulse_event', array_merge($common, [
            'label'           => __('Events', 'artpulse'),
            'supports'        => ['title','editor','thumbnail','excerpt'],
            'rewrite'         => ['slug'=>'events'],
            'taxonomies'      => ['artpulse_event_type'],
            'capability_type' => 'artpulse_event',
            'capabilities'    => [ /* ... */ ],
            'map_meta_cap'    => true,
        ]));

        // Register Event meta for REST
        register_post_meta('artpulse_event', '_ap_event_date', [
            'show_in_rest' => true,
            'single'       => true,
            'type'         => 'string',
        ]);
        register_post_meta('artpulse_event', '_ap_event_location', [
            'show_in_rest' => true,
            'single'       => true,
            'type'         => 'string',
        ]);

        // Artists
        register_post_type('artpulse_artist', array_merge($common, [
            'label'           => __('Artists', 'artpulse'),
            'supports'        => ['title','editor','thumbnail','custom-fields'],
            'rewrite'         => ['slug'=>'artists'],
            'capability_type' => 'artpulse_artist',
            'capabilities'    => [ /* ... */ ],
            'map_meta_cap'    => true,
        ]));

        // Register Artist meta for REST
        register_post_meta('artpulse_artist', '_ap_artist_bio', [
            'show_in_rest' => true,
            'single'       => true,
            'type'         => 'string',
        ]);
        register_post_meta('artpulse_artist', '_ap_artist_org', [
            'show_in_rest' => true,
            'single'       => true,
            'type'         => 'integer',
        ]);

        // Artworks
        register_post_type('artpulse_artwork', array_merge($common, [
            'label'           => __('Artworks', 'artpulse'),
            'supports'        => ['title','editor','thumbnail','custom-fields'],
            'rewrite'         => ['slug'=>'artworks'],
            'capability_type' => 'artpulse_artwork',
            'capabilities'    => [ /* ... */ ],
            'map_meta_cap'    => true,
        ]));

        // Register Artwork meta for REST
        register_post_meta('artpulse_artwork', '_ap_artwork_medium', [
            'show_in_rest' => true,
            'single'       => true,
            'type'         => 'string',
        ]);
        register_post_meta('artpulse_artwork', '_ap_artwork_dimensions', [
            'show_in_rest' => true,
            'single'       => true,
            'type'         => 'string',
        ]);
        register_post_meta('artpulse_artwork', '_ap_artwork_materials', [
            'show_in_rest' => true,
            'single'       => true,
            'type'         => 'string',
        ]);

        // Organizations
        register_post_type('artpulse_org', array_merge($common, [
            'label'           => __('Organizations', 'artpulse'),
            'supports'        => ['title','editor','thumbnail'],
            'rewrite'         => ['slug'=>'organizations'],
            'capability_type' => 'artpulse_org',
            'capabilities'    => [ /* ... */ ],
            'map_meta_cap'    => true,
        ]));

        // Register Org meta for REST
        register_post_meta('artpulse_org', '_ap_org_address', [
            'show_in_rest' => true,
            'single'       => true,
            'type'         => 'string',
        ]);
        register_post_meta('artpulse_org', '_ap_org_website', [
            'show_in_rest' => true,
            'single'       => true,
            'type'         => 'string',
        ]);

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
