<?php
namespace ArtPulse\Community;

class NotificationHooks {
    /**
     * Register all action/event hooks.
     */
    public static function register() {
        // Notify on new comments (if desired)
        add_action('comment_post', [self::class, 'notify_on_comment'], 10, 3);

        // Membership/Payment logic
        add_action('ap_membership_level_changed', [self::class, 'notify_on_membership_change'], 10, 4);
        add_action('ap_membership_payment', [self::class, 'notify_on_payment'], 10, 4);

        // Extend for more, e.g., WooCommerce or custom events
    }

    /**
     * Notify post author of a new comment.
     */
    public static function notify_on_comment($comment_ID, $comment_approved, $commentdata) {
        if ($comment_approved !== 1) return;
        $post = get_post($commentdata['comment_post_ID']);
        if (!$post) return;
        $author_id = $post->post_author;
        if ($author_id && $author_id !== $commentdata['user_id']) {
            NotificationManager::add(
                $author_id,
                'comment',
                $post->ID,
                $commentdata['user_id'],
                sprintf('New comment on "%s"', $post->post_title)
            );
        }
    }

    /**
     * Notify user on membership level changes.
     * @param int $user_id
     * @param string $old_level
     * @param string $new_level
     * @param string $change_type ('upgrade', 'downgrade', 'expired', etc.)
     */
    public static function notify_on_membership_change($user_id, $old_level, $new_level, $change_type) {
        NotificationManager::add(
            $user_id,
            'membership_' . $change_type, // e.g., 'membership_upgrade'
            null, // object_id
            null, // related_id
            sprintf(
                'Your membership was %s: %s → %s.',
                esc_html($change_type),
                esc_html($old_level),
                esc_html($new_level)
            )
        );
    }

    /**
     * Notify user on membership payment events.
     * @param int $user_id
     * @param float $amount
     * @param string $currency
     * @param string $event_type ('paid', 'failed', 'refunded', etc.)
     */
    public static function notify_on_payment($user_id, $amount, $currency, $event_type) {
        $amount_display = number_format_i18n($amount, 2) . ' ' . strtoupper($currency);
        NotificationManager::add(
            $user_id,
            'payment_' . $event_type, // e.g., 'payment_paid', 'payment_failed'
            null,
            null,
            sprintf(
                'Payment %s: %s',
                esc_html($event_type),
                esc_html($amount_display)
            )
        );
    }
}

// Register hooks on init.
add_action('init', [\ArtPulse\Community\NotificationHooks::class, 'register']);
