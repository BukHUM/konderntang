<?php
/**
 * SEO Enhancements
 *
 * @package KonDernTang
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add Schema.org structured data
 */
function konderntang_schema_markup()
{
    if (is_singular('post')) {
        global $post;
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => get_the_title(),
            'description' => wp_trim_words(get_the_excerpt(), 30),
            'image' => get_the_post_thumbnail_url($post->ID, 'full'),
            'datePublished' => get_the_date('c'),
            'dateModified' => get_the_modified_date('c'),
            'author' => array(
                '@type' => 'Person',
                'name' => get_the_author(),
            ),
            'publisher' => array(
                '@type' => 'Organization',
                'name' => get_bloginfo('name'),
                'logo' => array(
                    '@type' => 'ImageObject',
                    'url' => get_site_icon_url(),
                ),
            ),
        );
        
        // Add breadcrumb schema
        $breadcrumb_schema = konderntang_breadcrumb_schema();
        if ($breadcrumb_schema) {
            $schema['breadcrumb'] = $breadcrumb_schema;
        }
        
        echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
    } elseif (is_front_page()) {
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => get_bloginfo('name'),
            'description' => get_bloginfo('description'),
            'url' => home_url('/'),
        );
        
        echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
    }
}
add_action('wp_head', 'konderntang_schema_markup', 5);

/**
 * Generate breadcrumb schema
 */
function konderntang_breadcrumb_schema()
{
    $items = array();
    $position = 1;
    
    // Home
    $items[] = array(
        '@type' => 'ListItem',
        'position' => $position++,
        'name' => esc_html__('หน้าแรก', 'konderntang'),
        'item' => home_url('/'),
    );
    
    // Category
    if (is_singular('post')) {
        $categories = get_the_category();
        if (!empty($categories)) {
            $category = $categories[0];
            $items[] = array(
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => $category->name,
                'item' => get_category_link($category->term_id),
            );
        }
        
        // Current post
        $items[] = array(
            '@type' => 'ListItem',
            'position' => $position,
            'name' => get_the_title(),
            'item' => get_permalink(),
        );
    }
    
    if (count($items) > 1) {
        return array(
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $items,
        );
    }
    
    return false;
}

/**
 * Add Open Graph meta tags
 */
function konderntang_open_graph_tags()
{
    if (is_singular('post')) {
        global $post;
        
        $og_title = get_post_meta($post->ID, '_konderntang_og_title', true);
        $og_description = get_post_meta($post->ID, '_konderntang_og_description', true);
        $og_image = get_post_meta($post->ID, '_konderntang_og_image', true);
        
        $title = $og_title ? $og_title : get_the_title();
        $description = $og_description ? $og_description : wp_trim_words(get_the_excerpt(), 30);
        $image = $og_image ? $og_image : get_the_post_thumbnail_url($post->ID, 'full');
        
        if (!$image && has_post_thumbnail()) {
            $image = get_the_post_thumbnail_url($post->ID, 'full');
        }
        
        echo '<meta property="og:type" content="article" />' . "\n";
        echo '<meta property="og:title" content="' . esc_attr($title) . '" />' . "\n";
        echo '<meta property="og:description" content="' . esc_attr($description) . '" />' . "\n";
        echo '<meta property="og:url" content="' . esc_url(get_permalink()) . '" />' . "\n";
        if ($image) {
            echo '<meta property="og:image" content="' . esc_url($image) . '" />' . "\n";
        }
        echo '<meta property="og:site_name" content="' . esc_attr(get_bloginfo('name')) . '" />' . "\n";
    } elseif (is_front_page()) {
        echo '<meta property="og:type" content="website" />' . "\n";
        echo '<meta property="og:title" content="' . esc_attr(get_bloginfo('name')) . '" />' . "\n";
        echo '<meta property="og:description" content="' . esc_attr(get_bloginfo('description')) . '" />' . "\n";
        echo '<meta property="og:url" content="' . esc_url(home_url('/')) . '" />' . "\n";
        echo '<meta property="og:site_name" content="' . esc_attr(get_bloginfo('name')) . '" />' . "\n";
    }
}
add_action('wp_head', 'konderntang_open_graph_tags', 5);

/**
 * Add Twitter Card meta tags
 */
function konderntang_twitter_cards()
{
    if (is_singular('post')) {
        global $post;
        
        $og_title = get_post_meta($post->ID, '_konderntang_og_title', true);
        $og_description = get_post_meta($post->ID, '_konderntang_og_description', true);
        $og_image = get_post_meta($post->ID, '_konderntang_og_image', true);
        
        $title = $og_title ? $og_title : get_the_title();
        $description = $og_description ? $og_description : wp_trim_words(get_the_excerpt(), 30);
        $image = $og_image ? $og_image : get_the_post_thumbnail_url($post->ID, 'full');
        
        if (!$image && has_post_thumbnail()) {
            $image = get_the_post_thumbnail_url($post->ID, 'full');
        }
        
        echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
        echo '<meta name="twitter:title" content="' . esc_attr($title) . '" />' . "\n";
        echo '<meta name="twitter:description" content="' . esc_attr($description) . '" />' . "\n";
        if ($image) {
            echo '<meta name="twitter:image" content="' . esc_url($image) . '" />' . "\n";
        }
    }
}
add_action('wp_head', 'konderntang_twitter_cards', 5);

/**
 * Add meta description tag
 */
function konderntang_meta_description()
{
    if (is_singular('post')) {
        global $post;
        $meta_description = get_post_meta($post->ID, '_konderntang_meta_description', true);
        if ($meta_description) {
            echo '<meta name="description" content="' . esc_attr($meta_description) . '" />' . "\n";
        } elseif (has_excerpt()) {
            echo '<meta name="description" content="' . esc_attr(wp_trim_words(get_the_excerpt(), 30)) . '" />' . "\n";
        }
    } elseif (is_front_page()) {
        $description = get_bloginfo('description');
        if ($description) {
            echo '<meta name="description" content="' . esc_attr($description) . '" />' . "\n";
        }
    }
}
add_action('wp_head', 'konderntang_meta_description', 1);
