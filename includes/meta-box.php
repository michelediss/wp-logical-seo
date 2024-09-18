<?php
// Add SEO meta fields
function seo_meta_fields() {
    add_meta_box('seo_meta_box', 'SEO Settings', 'seo_meta_box_callback', ['post', 'page', 'custom_post_type'], 'normal', 'high');
}
add_action('add_meta_boxes', 'seo_meta_fields');

// Callback for the SEO meta box
function seo_meta_box_callback($post) {
    $seo_title = get_post_meta($post->ID, '_seo_title', true);
    $seo_description = get_post_meta($post->ID, '_seo_description', true);
    $seo_keywords = get_post_meta($post->ID, '_seo_keywords', true);
    $post_url = get_permalink($post->ID);
    $site_name = get_bloginfo('name');
    $favicon_url = get_site_icon_url();
    $breadcrumbs = ''; // You can generate breadcrumbs based on your site's structure.

    // Prepare default values
    if (!$seo_title) {
        $seo_title = get_the_title($post->ID) . ' - ' . $site_name;
    }
    if (!$seo_description) {
        $seo_description = wp_trim_words(strip_tags($post->post_content), 30, '...');
    }
    if (!$breadcrumbs) {
        // Generate breadcrumbs based on post categories or hierarchy
        $categories = get_the_category($post->ID);
        if ($categories) {
            $breadcrumbs_array = [];
            foreach ($categories as $category) {
                $breadcrumbs_array[] = $category->name;
            }
            $breadcrumbs = implode(' › ', $breadcrumbs_array);
        } else {
            $breadcrumbs = $site_name;
        }
    }
    ?>
    <h3>Search Engine Preview</h3>
    <div id="seo-snippet-preview" style="border: 1px solid #dadce0; padding: 16px; background: #fff; border-radius: 8px; max-width: 600px; font-family: Arial, sans-serif;">
        <div style="display: flex; align-items: flex-start; margin-bottom: 8px;">
            <!-- Favicon -->
            <?php if ($favicon_url): ?>
                <img src="<?php echo esc_url($favicon_url); ?>" alt="Favicon" style="width:32px; height:32px; margin-right: 8px;">
            <?php else: ?>
                <!-- Placeholder Favicon -->
                <div style="width:32px; height:32px; background-color:#ccc; margin-right: 8px;"></div>
            <?php endif; ?>

            <!-- Site name and breadcrumbs -->
            <div style="display: flex; flex-direction: column;">
                <span style="font-size: 14px; color: #3c4043;"><?php echo esc_html($site_name); ?></span>
                <span style="color: #202124; font-size: 14px;">
                    <span id="seo-preview-url" style="display: flex; align-items: center;">
                        <span style="color: #5f6368;"><?php echo parse_url($post_url, PHP_URL_HOST); ?></span>
                        <span style="margin: 0 4px;">›</span>
                        <span style="color: #5f6368;"><?php echo esc_html($breadcrumbs); ?></span>
                    </span>
                </span>
            </div>
        </div>
        <div>
            <a href="#" id="seo-preview-title" style="color: #1a0dab; font-size: 20px; text-decoration: none; display: block; margin-bottom: 4px; line-height: 1.3;">
                <?php echo esc_html($seo_title); ?>
            </a>

            <div id="seo-preview-description" style="color: #4d5156; font-size: 14px; line-height: 1.58;">
                <?php echo esc_html($seo_description); ?>
            </div>
        </div>
    </div>

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

    <!-- JavaScript for real-time update -->
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        // Update the title in real-time
        $('#seo_title').on('input', function() {
            var title = $(this).val();
            if (title === '') {
                title = '<?php echo esc_js(get_the_title($post->ID) . ' - ' . $site_name); ?>';
            }
            $('#seo-preview-title').text(title);
        });

        // Update the description in real-time
        $('#seo_description').on('input', function() {
            var description = $(this).val();
            if (description === '') {
                description = '<?php echo esc_js(wp_trim_words(strip_tags($post->post_content), 30, '...')); ?>';
            }
            $('#seo-preview-description').text(description);
        });
    });
    </script>
    <?php
}

// Save SEO meta tags
function save_seo_meta($post_id) {
    if (isset($_POST['seo_title'])) {
        update_post_meta($post_id, '_seo_title', sanitize_text_field($_POST['seo_title']));
    }
    if (isset($_POST['seo_description'])) {
        update_post_meta($post_id, '_seo_description', sanitize_textarea_field($_POST['seo_description']));
    }
    if (isset($_POST['seo_keywords'])) {
        update_post_meta($post_id, '_seo_keywords', sanitize_text_field($_POST['seo_keywords']));
    }
}
add_action('save_post', 'save_seo_meta');
?>
