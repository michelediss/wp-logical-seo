<?php
/*
Plugin Name: Logical SEO Toolbox
Plugin URI: https://github.com/michelediss/wp-logical-seo
Description: A minimal SEO plugin for managing meta tags, sitemap, robots.txt, noindex, canonical, and social optimization.
Version: 1.0
Author: Michele Paolino
Author URI: https://www.michelepaolino.me
*/

// Ensure the file is not accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Include necessary files
require_once plugin_dir_path(__FILE__) . 'includes/meta-box.php';
require_once plugin_dir_path(__FILE__) . 'includes/social-meta-tags.php';
require_once plugin_dir_path(__FILE__) . 'includes/settings.php';
require_once plugin_dir_path(__FILE__) . 'includes/noindex-nofollow.php';

// Activate sitemap generation at publication time
add_action('publish_post', 'generate_sitemap');
add_action('publish_page', 'generate_sitemap');

// Add canonical URL
function add_canonical_url() {
    if (is_singular()) {
        echo '<link rel="canonical" href="' . esc_url(get_permalink()) . '" />';
    }
}
add_action('wp_head', 'add_canonical_url');

// Generate sitemap
function generate_sitemap() {
    $posts = get_posts([
        'post_type' => ['post', 'page'],
        'posts_per_page' => -1,
        'post_status' => 'publish',
    ]);

    $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>');

    foreach ($posts as $post) {
        $url = $xml->addChild('url');
        $url->addChild('loc', get_permalink($post));
        $url->addChild('lastmod', get_the_modified_time('Y-m-d', $post));
        $url->addChild('changefreq', 'monthly');
        $url->addChild('priority', '0.8');
    }

    $sitemap_file = ABSPATH . 'sitemap.xml';
    $xml->asXML($sitemap_file);
}


