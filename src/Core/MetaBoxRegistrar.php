<?php
namespace ArtPulse\Core;

class MetaBoxRegistrar
{
    public static function register()
    {
        add_action('add_meta_boxes', [self::class, 'addMetaBoxes']);
        add_action('save_post',        [self::class, 'saveMetaBoxes']);
    }

    public static function addMetaBoxes()
    {
        // Event Details
        add_meta_box(
            'artpulse_event_details',
            __('Event Details', 'artpulse'),
            [self::class, 'renderEventBox'],
            'artpulse_event',
            'normal',
            'high'
        );

        // TODO: Add boxes for Artists, Artworks, Organizations...
    }

    public static function renderEventBox(\WP_Post $post)
    {
        wp_nonce_field('artpulse_event_nonce', 'artpulse_event_nonce');

        $date     = get_post_meta($post->ID, '_artpulse_event_date', true);
        $location = get_post_meta($post->ID, '_artpulse_event_location', true);

        echo '<p><label for="artpulse_event_date">'.__('Date','artpulse').'</label><br />';
        echo '<input type="date" id="artpulse_event_date" name="artpulse_event_date" value="'.esc_attr($date).'" /></p>';

        echo '<p><label for="artpulse_event_location">'.__('Location','artpulse').'</label><br />';
        echo '<input type="text" id="artpulse_event_location" name="artpulse_event_location" value="'.esc_attr($location).'" style="width:100%;" /></p>';
    }

    public static function saveMetaBoxes($post_id)
    {
        // Verify nonce
        if ( ! ( isset($_POST['artpulse_event_nonce'])
              && wp_verify_nonce($_POST['artpulse_event_nonce'], 'artpulse_event_nonce') )
           ) {
            return;
        }

        // Bail on autosave
        if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
            return;
        }

        if ( isset($_POST['artpulse_event_date']) ) {
            update_post_meta($post_id, '_artpulse_event_date', sanitize_text_field($_POST['artpulse_event_date']));
        }
        if ( isset($_POST['artpulse_event_location']) ) {
            update_post_meta($post_id, '_artpulse_event_location', sanitize_text_field($_POST['artpulse_event_location']));
        }
    }
}
