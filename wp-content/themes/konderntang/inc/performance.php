<?php
/**
 * Performance Optimization Functions
 *
 * @package KonDernTang
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add resource hints
 * 
 * @param array  $urls   URLs to print for resource hints.
 * @param string $relation_type The relation type the URLs are printed for.
 * @return array Difference urls
 */
function konderntang_resource_hints($urls, $relation_type)
{
    if ('preconnect' === $relation_type) {
        $urls[] = array(
            'href' => 'https://fonts.gstatic.com',
            'crossorigin' => 'use-credentials',
        );
        $urls[] = array(
            'href' => 'https://fonts.googleapis.com',
            'crossorigin' => 'anonymous',
        );
    }
    return $urls;
}
add_filter('wp_resource_hints', 'konderntang_resource_hints', 10, 2);

/**
 * Add fetchpriority="high" to LCP images
 * 
 * @param array  $attr       Attributes for the image markup.
 * @param object $attachment Image attachment post object.
 * @param string|array $size Requested size.
 * @return array Modified attributes.
 */
function konderntang_optimize_lcp_image($attr, $attachment, $size)
{
    // Only target the main image on single posts or the first slide on home
    if (is_singular('post') && has_post_thumbnail()) {
        // Check if this is the featured image
        if (get_post_thumbnail_id() === $attachment->ID) {
            $attr['fetchpriority'] = 'high';
            // If we are prioritizing it, we probably don't want to lazy load it
            if (isset($attr['loading'])) {
                unset($attr['loading']);
            }
        }
    }

    return $attr;
}
add_filter('wp_get_attachment_image_attributes', 'konderntang_optimize_lcp_image', 10, 3);

/**
 * Enable lazy loading for images (except LCP images)
 * 
 * @param array  $attr       Attributes for the image markup.
 * @param object $attachment Image attachment post object.
 * @param string|array $size Requested size.
 * @return array Modified attributes.
 */
function konderntang_lazy_load_images($attr, $attachment, $size)
{
    // Don't lazy load if it's already set to high priority
    if (isset($attr['fetchpriority']) && $attr['fetchpriority'] === 'high') {
        return $attr;
    }
    
    // Don't lazy load featured images on single posts (handled by LCP function)
    if (is_singular('post') && has_post_thumbnail()) {
        if (get_post_thumbnail_id() === $attachment->ID) {
            return $attr;
        }
    }
    
    // Add lazy loading to other images
    if (!isset($attr['loading'])) {
        $attr['loading'] = 'lazy';
    }
    
    return $attr;
}
add_filter('wp_get_attachment_image_attributes', 'konderntang_lazy_load_images', 20, 3);

/**
 * Add width and height attributes to images for CLS prevention
 * 
 * @param array  $attr       Attributes for the image markup.
 * @param object $attachment Image attachment post object.
 * @param string|array $size Requested size.
 * @return array Modified attributes.
 */
function konderntang_add_image_dimensions($attr, $attachment, $size)
{
    if (!isset($attr['width']) && !isset($attr['height'])) {
        $image_meta = wp_get_attachment_metadata($attachment->ID);
        if ($image_meta) {
            if (isset($image_meta['width']) && isset($image_meta['height'])) {
                $attr['width'] = $image_meta['width'];
                $attr['height'] = $image_meta['height'];
            }
        }
    }
    
    return $attr;
}
add_filter('wp_get_attachment_image_attributes', 'konderntang_add_image_dimensions', 30, 3);

/**
 * Remove query strings from static resources (for better caching)
 * 
 * @param string $src Source URL.
 * @return string Modified URL.
 */
function konderntang_remove_query_strings($src)
{
    if (strpos($src, '?ver=')) {
        $src = remove_query_arg('ver', $src);
    }
    return $src;
}
// Note: This is commented out as it may break versioning
// add_filter('script_loader_src', 'konderntang_remove_query_strings', 15, 1);
// add_filter('style_loader_src', 'konderntang_remove_query_strings', 15, 1);

/**
 * Defer parsing of JavaScript
 * Already handled in enqueue-scripts.php via defer attribute
 */

/**
 * Optimize database queries
 */
function konderntang_optimize_queries($query)
{
    if (!is_admin() && $query->is_main_query()) {
        // Limit post meta queries
        if (is_archive() || is_home()) {
            $query->set('update_post_meta_cache', true);
            $query->set('update_post_term_cache', true);
        }
    }
}
add_action('pre_get_posts', 'konderntang_optimize_queries');