<?php
// Add meta box for Noindex and Nofollow in post/page editor
function noindex_nofollow_meta_box() {
    add_meta_box('noindex_nofollow_box', 'SEO: Noindex/Nofollow', 'noindex_nofollow_meta_box_callback', ['post', 'page'], 'side', 'high');
}
add_action('add_meta_boxes', 'noindex_nofollow_meta_box');

// Callback function that shows the meta box with checkboxes for Noindex and Nofollow
function noindex_nofollow_meta_box_callback($post) {
    // Retrieve stored values for the current post
    $noindex = get_post_meta($post->ID, '_seo_noindex', true);
    $nofollow = get_post_meta($post->ID, '_seo_nofollow', true);

    // Create the interface for the checkboxes for Noindex and Nofollow
    ?>
    <p>
        <label><input type="checkbox" name="seo_noindex" value="1" <?php checked($noindex, '1'); ?>> Noindex</label>
    </p>
    <p>
        <label><input type="checkbox" name="seo_nofollow" value="1" <?php checked($nofollow, '1'); ?>> Nofollow</label>
    </p>
    <p><em>Il tag Noindex impedisce ai motori di ricerca di indicizzare questa pagina. Il tag Nofollow impedisce ai motori di seguire i link presenti in questa pagina.</em></p>
    <?php
}

// Save the values of Noindex and Nofollow when the post is updated
function save_noindex_nofollow($post_id) {
    // Save the value of Noindex
    update_post_meta($post_id, '_seo_noindex', isset($_POST['seo_noindex']) ? '1' : '0');
    // Save the value of Nofollow
    update_post_meta($post_id, '_seo_nofollow', isset($_POST['seo_nofollow']) ? '1' : '0');
}
add_action('save_post', 'save_noindex_nofollow');

// Add noindex and nofollow meta tags to the head
function add_noindex_nofollow_meta() {
    if (is_singular()) {
        global $post;

        // Check if the page has Noindex or Nofollow activated
        $noindex = get_post_meta($post->ID, '_seo_noindex', true);
        $nofollow = get_post_meta($post->ID, '_seo_nofollow', true);

        // Generate meta tags for search engines based on settings
        if ($noindex && $nofollow) {
            echo '<meta name="robots" content="noindex, nofollow" />' . "\n";
        } elseif ($noindex) {
            echo '<meta name="robots" content="noindex, follow" />' . "\n";
        } elseif ($nofollow) {
            echo '<meta name="robots" content="index, nofollow" />' . "\n";
        }
    }
}
add_action('wp_head', 'add_noindex_nofollow_meta');
