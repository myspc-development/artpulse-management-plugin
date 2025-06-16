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