ArtPulse Favorites UI — To-Do List

2️⃣ Output Favorite Button in Template/Shortcode



// Assume $post is the current artwork/event/etc.
$user_id = get_current_user_id();
$is_favorited = \ArtPulse\Community\FavoritesManager::is_favorited($user_id, $post->ID, get_post_type($post));
?>
<button class="ap-favorite-btn<?php if ($is_favorited) echo ' active'; ?>"
        data-object-id="<?php echo esc_attr($post->ID); ?>"
        data-object-type="<?php echo esc_attr(get_post_type($post)); ?>"
        aria-pressed="<?php echo $is_favorited ? 'true' : 'false'; ?>">
    ❤️
</button>
<?php



3️⃣ [Optional] Shortcode for User Favorites List



add_shortcode('ap_user_favorites', function($atts){
    $user_id = get_current_user_id();
    if (!$user_id) return '<em>Please log in to see your favorites.</em>';
    $favs = \ArtPulse\Community\FavoritesManager::get_user_favorites($user_id);
    if (!$favs) return '<em>No favorites yet.</em>';
    $out = '<ul class="ap-fav-list">';
    foreach ($favs as $fav) {
        $title = get_the_title($fav->object_id);
        $permalink = get_permalink($fav->object_id);
        $out .= "<li><a href='".esc_url($permalink)."'>".esc_html($title)."</a></li>";
    }
    $out .= '</ul>';
    return $out;
});



Check off items as you implement each feature!

ArtPulse Management Plugin

Overview

This plugin provides core functionality for the ArtPulse platform, including content types, membership, notifications, favorites, and organizational relationships.

Features Implemented

✅ Core Modules

Custom Post Types: artpulse_artist, artpulse_org, artpulse_event, artpulse_artwork

MetaBox registration for CPTs

Directory views

Member dashboard

WooCommerce integration (optional)

✅ Membership System

Levels: Basic, Pro, Org

Membership expiration and daily cron check

REST API for membership details

Membership change/payment hooks

✅ Favorites

Users can favorite objects (posts)

Stored in custom DB table ap_favorites

Notifications triggered on favoriting

✅ Follows

Users can follow other objects

Stored in custom DB table ap_follows

Notifications triggered on new follows

✅ Profile Link Requests

Artists request links to organizations

Stored as CPT ap_profile_link

Request/approve/deny with notifications

✅ Notifications System

Custom table: ap_notifications

REST API for fetching/marking notifications

Notification types: favorite, follower, comment, membership_change, payment_*, link_request_*

Email notifications (optional)

Mark individual or all as read

REST endpoints:

GET /artpulse/v1/notifications

POST /artpulse/v1/notifications/{id}/read

✅ Frontend

Notification widget with unread count badge

JS support to mark notifications read and fetch via API

Membership dashboard script and UI

To Do (In Progress / Planned)

🔜 Frontend UI Enhancements

Notification dropdown UI integration in header

Templating for notification center and dashboard display

🔜 Emails

Optional email template customization

Enable/disable notifications per type (per user?)

🔜 Admin Features

Manage notification types

View logs or notification analytics

🔜 Testing & QA

PHPUnit + Brain Monkey mocks

UI functional tests for notifications

Notes

All hooks are initialized in plugin main file and NotificationHooks

Tables created only on activation via artpulse_activate()

REST routes registered via NotificationRestController

Composer dependencies include Stripe + Parsedown

