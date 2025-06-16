<?php
namespace ArtPulse\Core;

class PostTypeRegistrar
{
    public static function register()
    {
        // Events
        register_post_type('artpulse_event', [
            'label'        => __('Events', 'artpulse'),
            'public'       => true,
            'show_in_rest' => true,
            'supports'     => ['title','editor','thumbnail','excerpt'],
            'has_archive'  => true,
            'rewrite'      => ['slug' => 'events'],
            'taxonomies'   => ['artpulse_event_type'],
        ]);

        // Artists
        register_post_type('artpulse_artist', [
            'label'        => __('Artists', 'artpulse'),
            'public'       => true,
            'show_in_rest' => true,
            'supports'     => ['title','editor','thumbnail','custom-fields'],
            'has_archive'  => true,
            'rewrite'      => ['slug' => 'artists'],
        ]);

        // Artworks
        register_post_type('artpulse_artwork', [
            'label'        => __('Artworks', 'artpulse'),
            'public'       => true,
            'show_in_rest' => true,
            'supports'     => ['title','editor','thumbnail','custom-fields'],
            'has_archive'  => true,
            'rewrite'      => ['slug' => 'artworks'],
        ]);

        // Organizations
        register_post_type('artpulse_org', [
            'label'        => __('Organizations', 'artpulse'),
            'public'       => true,
            'show_in_rest' => true,
            'supports'     => ['title','editor','thumbnail'],
            'has_archive'  => true,
            'rewrite'      => ['slug' => 'organizations'],
        ]);

        // Event Types taxonomy
        register_taxonomy('artpulse_event_type', 'artpulse_event', [
            'label'        => __('Event Types', 'artpulse'),
            'public'       => true,
            'show_in_rest' => true,
            'hierarchical' => true,
            'rewrite'      => ['slug' => 'event-types'],
        ]);

        // Artwork Medium taxonomy
        register_taxonomy('artpulse_medium', 'artpulse_artwork', [
            'label'        => __('Medium', 'artpulse'),
            'public'       => true,
            'show_in_rest' => true,
            'hierarchical' => true,
            'rewrite'      => ['slug' => 'medium'],
        ]);
    }
}
