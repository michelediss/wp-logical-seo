<?php
function add_social_meta_tags() {
    if (is_singular(['post', 'page'])) {
        global $post;
        $seo_title = get_post_meta($post->ID, '_seo_title', true);
        $seo_description = get_post_meta($post->ID, '_seo_description', true);
        $post_thumbnail = get_the_post_thumbnail_url($post->ID);
        $site_favicon = get_site_icon_url();

        // Use the post thumbnail if available, otherwise use the site favicon
        $image_url = $post_thumbnail ? $post_thumbnail : $site_favicon;

        // Open Graph
        if ($seo_title && $seo_description) {
            echo '<meta property="og:title" content="' . esc_html($seo_title) . '" />';
            echo '<meta property="og:description" content="' . esc_html($seo_description) . '" />';
            echo '<meta property="og:url" content="' . get_permalink() . '" />';
            if ($image_url) {
                echo '<meta property="og:image" content="' . esc_url($image_url) . '" />';
                echo '<meta property="og:image:width" content="1200" />';
                echo '<meta property="og:image:height" content="630" />';
            }

            // Twitter Card
            echo '<meta name="twitter:card" content="summary_large_image" />';
            echo '<meta name="twitter:title" content="' . esc_html($seo_title) . '" />';
            echo '<meta name="twitter:description" content="' . esc_html($seo_description) . '" />';
            if ($image_url) {
                echo '<meta name="twitter:image" content="' . esc_url($image_url) . '" />';
            }
        }
    }
}
add_action('wp_head', 'add_social_meta_tags');
?>