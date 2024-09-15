<?php
// Add SEO meta fields
function seo_meta_fields() {
    add_meta_box('seo_meta_box', 'SEO Settings', 'seo_meta_box_callback', ['post', 'page', 'custom_post_type'], 'normal', 'high');
}
add_action('add_meta_boxes', 'seo_meta_fields');

// Callback function for SEO meta box
function seo_meta_box_callback($post) {
    $seo_title = get_post_meta($post->ID, '_seo_title', true);
    $seo_description = get_post_meta($post->ID, '_seo_description', true);
    $seo_keywords = get_post_meta($post->ID, '_seo_keywords', true);
    $post_url = get_permalink($post->ID);
    ?>
    <h3>Search Appearance Preview</h3>
    <div id="yoast-snippet-preview-container" style="border: 1px solid #ddd; padding: 10px; background: #f9f9f9;">
        <div style="margin-bottom: 8px;">
            <span style="color: #1a0dab; font-size: 18px; display: block;"><?php echo esc_html($seo_title ? $seo_title : get_the_title($post->ID)); ?></span>
            <span style="color: #006621; font-size: 14px;"><?php echo esc_url($post_url); ?></span>
        </div>
        <div style="color: #545454; font-size: 13px;">
            <?php echo esc_html($seo_description ? $seo_description : wp_trim_words(strip_tags($post->post_content), 30, '...')); ?>
        </div>
    </div>
    <hr>
    <p>
        <label for="seo_title">SEO Title:</label>
        <input type="text" id="seo_title" name="seo_title" value="<?php echo esc_attr($seo_title); ?>" style="width:100%;" />
    </p>
    <p>
        <label for="seo_description">Meta Description:</label>
        <textarea id="seo_description" name="seo_description" style="width:100%;"><?php echo esc_textarea($seo_description); ?></textarea>
    </p>
    <p>
        <label for="seo_keywords">Meta Keywords:</label>
        <input type="text" id="seo_keywords" name="seo_keywords" value="<?php echo esc_attr($seo_keywords); ?>" style="width:100%;" />
    </p>

    <?php
}

// Save SEO meta tags
function save_seo_meta($post_id) {
    if (isset($_POST['seo_title'])) {
        update_post_meta($post_id, '_seo_title', sanitize_text_field($_POST['seo_title']));
    }
    if (isset($_POST['seo_description'])) {
        update_post_meta($post_id, '_seo_description', sanitize_text_field($_POST['seo_description']));
    }
    if (isset($_POST['seo_keywords'])) {
        update_post_meta($post_id, '_seo_keywords', sanitize_text_field($_POST['seo_keywords']));
    }
}
add_action('save_post', 'save_seo_meta');
