<?php
// Adds the "SEO" link under Settings in the admin menu
function minimal_seo_plugin_settings_menu() {
    add_options_page(
        'SEO Settings',  // Page title
        'SEO',           // Menu text
        'manage_options', // Capability required to access the page
        'minimal-seo-settings', // Page slug
        'minimal_seo_settings_page' // Callback function for the page
    );
}
add_action('admin_menu', 'minimal_seo_plugin_settings_menu');

// Callback function to display the SEO settings page
function minimal_seo_settings_page() {
    // Save settings
    if (isset($_POST['seo_settings_nonce']) && wp_verify_nonce($_POST['seo_settings_nonce'], 'save_seo_settings')) {
        // Save robots.txt content
        if (isset($_POST['robots_content'])) {
            $file = ABSPATH . 'robots.txt';
            file_put_contents($file, stripslashes($_POST['robots_content']));
            echo '<div class="updated"><p>robots.txt file updated.</p></div>';
        }

        // Save global noindex and nofollow option for the site
        update_option('minimal_seo_noindex', isset($_POST['seo_noindex']) ? '1' : '0');
        update_option('minimal_seo_nofollow', isset($_POST['seo_nofollow']) ? '1' : '0');

        echo '<div class="updated"><p>SEO settings saved.</p></div>';
    }

    // Retrieve robots.txt content, if it exists
    $robots_content = file_exists(ABSPATH . 'robots.txt') ? file_get_contents(ABSPATH . 'robots.txt') : '';
    
    // Retrieve global noindex and nofollow settings
    $noindex = get_option('minimal_seo_noindex', '0');
    $nofollow = get_option('minimal_seo_nofollow', '0');

    // Check for sitemap existence
    $sitemap_url = home_url('/sitemap.xml');
    $sitemap_exists = file_exists(ABSPATH . 'sitemap.xml');
    ?>

    <div class="wrap">
        <h1>General Settings for Minimal SEO Plugin</h1>

        <!-- Section for managing robots.txt file -->
        <h2>Robots.txt File Management</h2>
        <form method="post">
            <textarea name="robots_content" style="width:100%; height:300px;"><?php echo esc_textarea($robots_content); ?></textarea>
            <p><input type="submit" class="button button-primary" value="Save Robots.txt"></p>
            
            <?php wp_nonce_field('save_seo_settings', 'seo_settings_nonce'); ?>
        </form>

        <!-- Section for setting global noindex/nofollow -->
        <h2>Global Noindex / Nofollow Settings</h2>
        <form method="post">
            <p>
                <label><input type="checkbox" name="seo_noindex" value="1" <?php checked($noindex, '1'); ?>> Set entire site to Noindex</label>
            </p>
            <p>
                <label><input type="checkbox" name="seo_nofollow" value="1" <?php checked($nofollow, '1'); ?>> Set entire site to Nofollow</label>
            </p>
            <p><input type="submit" class="button button-primary" value="Save Settings"></p>

            <?php wp_nonce_field('save_seo_settings', 'seo_settings_nonce'); ?>
        </form>

        <!-- Section for Sitemap check -->
        <h2>XML Sitemap Status</h2>
        <?php if ($sitemap_exists): ?>
            <p>The sitemap has been generated successfully. You can view it at the following link:</p>
            <p><a href="<?php echo esc_url($sitemap_url); ?>" target="_blank"><?php echo esc_html($sitemap_url); ?></a></p>
        <?php else: ?>
            <p><strong>Warning:</strong> The sitemap does not exist. Verify that sitemap generation is enabled and that the <code>sitemap.xml</code> file has been created correctly.</p>
        <?php endif; ?>
    </div>
    <?php
}

// Adds global noindex and nofollow meta tags to the entire site if enabled
function minimal_seo_add_global_meta_tags() {
    // Retrieve noindex and nofollow settings
    $noindex = get_option('minimal_seo_noindex', '0');
    $nofollow = get_option('minimal_seo_nofollow', '0');

    // Build the robots tag content based on settings
    if ($noindex && $nofollow) {
        echo '<meta name="robots" content="noindex, nofollow" />' . "\n";
    } elseif ($noindex) {
        echo '<meta name="robots" content="noindex, follow" />' . "\n";
    } elseif ($nofollow) {
        echo '<meta name="robots" content="index, nofollow" />' . "\n";
    }
}
add_action('wp_head', 'minimal_seo_add_global_meta_tags');
