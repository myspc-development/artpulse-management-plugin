<?php
namespace ArtPulse\Core;

class MetaBoxRegistrar
{
    public static function register()
    {
        add_action('add_meta_boxes', [ self::class, 'addMetaBoxes' ]);
        add_action('save_post',        [ self::class, 'saveMetaBoxes' ]);
    }

    public static function addMetaBoxes()
    {
        // Event
        add_meta_box(
            'ap_event_details',
            __('Event Details', 'artpulse'),
            [ self::class, 'renderEventBox' ],
            'artpulse_event',
            'normal',
            'high'
        );

        // Artist
        add_meta_box(
            'ap_artist_bio',
            __('Artist Profile', 'artpulse'),
            [ self::class, 'renderArtistBox' ],
            'artpulse_artist',
            'normal',
            'high'
        );

        // Artwork
        add_meta_box(
            'ap_artwork_info',
            __('Artwork Info', 'artpulse'),
            [ self::class, 'renderArtworkBox' ],
            'artpulse_artwork',
            'normal',
            'high'
        );

        // Organization
        add_meta_box(
            'ap_org_info',
            __('Organization Info', 'artpulse'),
            [ self::class, 'renderOrgBox' ],
            'artpulse_org',
            'normal',
            'high'
        );
    }

    public static function renderEventBox(\WP_Post $post)
    {
        wp_nonce_field('ap_event_nonce', 'ap_event_nonce');
        $date     = get_post_meta($post->ID, '_ap_event_date', true);
        $location = get_post_meta($post->ID, '_ap_event_location', true);

        echo '<p><label>' . __('Date', 'artpulse') . '</label><br />';
        echo '<input type="date" name="ap_event_date" value="' . esc_attr($date) . '" /></p>';

        echo '<p><label>' . __('Location', 'artpulse') . '</label><br />';
        echo '<input type="text" name="ap_event_location" style="width:100%" value="' . esc_attr($location) . '" /></p>';
    }

    public static function renderArtistBox(\WP_Post $post)
    {
        wp_nonce_field('ap_artist_nonce', 'ap_artist_nonce');
        $bio = get_post_meta($post->ID, '_ap_artist_bio', true);
        $org = get_post_meta($post->ID, '_ap_artist_org', true);

        echo '<p><label>' . __('Biography', 'artpulse') . '</label><br />';
        echo '<textarea name="ap_artist_bio" style="width:100%">' . esc_textarea($bio) . '</textarea></p>';

        echo '<p><label>' . __('Organization ID', 'artpulse') . '</label><br />';
        echo '<input type="number" name="ap_artist_org" value="' . esc_attr($org) . '" /></p>';
    }

    public static function renderArtworkBox(\WP_Post $post)
    {
        wp_nonce_field('ap_artwork_nonce', 'ap_artwork_nonce');
        $medium     = get_post_meta($post->ID, '_ap_artwork_medium', true);
        $dimensions = get_post_meta($post->ID, '_ap_artwork_dimensions', true);
        $materials  = get_post_meta($post->ID, '_ap_artwork_materials', true);

        echo '<p><label>' . __('Medium', 'artpulse') . '</label><br />';
        echo '<input type="text" name="ap_artwork_medium" value="' . esc_attr($medium) . '" /></p>';

        echo '<p><label>' . __('Dimensions', 'artpulse') . '</label><br />';
        echo '<input type="text" name="ap_artwork_dimensions" value="' . esc_attr($dimensions) . '" /></p>';

        echo '<p><label>' . __('Materials', 'artpulse') . '</label><br />';
        echo '<input type="text" name="ap_artwork_materials" value="' . esc_attr($materials) . '" /></p>';
    }

    public static function renderOrgBox(\WP_Post $post)
    {
        wp_nonce_field('ap_org_nonce', 'ap_org_nonce');
        $address = get_post_meta($post->ID, '_ap_org_address', true);
        $website = get_post_meta($post->ID, '_ap_org_website', true);

        echo '<p><label>' . __('Address', 'artpulse') . '</label><br />';
        echo '<input type="text" name="ap_org_address" style="width:100%" value="' . esc_attr($address) . '" /></p>';

        echo '<p><label>' . __('Website', 'artpulse') . '</label><br />';
        echo '<input type="url" name="ap_org_website" style="width:100%" value="' . esc_attr($website) . '" /></p>';
    }

    public static function saveMetaBoxes($post_id)
    {
        // Bail on autosave or missing nonce
        foreach ( [ 'event', 'artist', 'artwork', 'org' ] as $type ) {
            $nonce_key = "ap_{$type}_nonce";
            if (
                ! isset( $_POST[ $nonce_key ] ) ||
                ! wp_verify_nonce( $_POST[ $nonce_key ], $nonce_key ) ||
                ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
            ) {
                continue;
            }

            switch ( $type ) {
                case 'event':
                    update_post_meta( $post_id, '_ap_event_date', sanitize_text_field( $_POST['ap_event_date'] ?? '' ) );
                    update_post_meta( $post_id, '_ap_event_location', sanitize_text_field( $_POST['ap_event_location'] ?? '' ) );
                    break;

                case 'artist':
                    update_post_meta( $post_id, '_ap_artist_bio', sanitize_textarea_field( $_POST['ap_artist_bio'] ?? '' ) );
                    update_post_meta( $post_id, '_ap_artist_org', absint( $_POST['ap_artist_org'] ?? 0 ) );
                    break;

                case 'artwork':
                    update_post_meta( $post_id, '_ap_artwork_medium', sanitize_text_field( $_POST['ap_artwork_medium'] ?? '' ) );
                    update_post_meta( $post_id, '_ap_artwork_dimensions', sanitize_text_field( $_POST['ap_artwork_dimensions'] ?? '' ) );
                    update_post_meta( $post_id, '_ap_artwork_materials', sanitize_text_field( $_POST['ap_artwork_materials'] ?? '' ) );
                    break;

                case 'org':
                    update_post_meta( $post_id, '_ap_org_address', sanitize_text_field( $_POST['ap_org_address'] ?? '' ) );
                    update_post_meta( $post_id, '_ap_org_website', esc_url_raw( $_POST['ap_org_website'] ?? '' ) );
                    break;
            }
        }
    }
}
