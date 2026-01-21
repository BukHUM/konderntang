<?php
/**
 * Theme Helper Functions
 *
 * @package KonDernTang
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get theme option
 *
 * @param string $option_name Option name
 * @param mixed  $default     Default value
 * @return mixed
 */
function konderntang_get_option($option_name, $default = false)
{
    return get_theme_mod($option_name, $default);
}

/**
 * Sanitize checkbox
 *
 * @param mixed $input Input value
 * @return string
 */
function konderntang_sanitize_checkbox($input)
{
    return (isset($input) && true === $input) ? '1' : '0';
}

/**
 * Get breadcrumb config
 *
 * @param string $page_name Page name
 * @return array|false
 */
function konderntang_get_breadcrumb_config($page_name)
{
    $config = require KONDERN_THEME_DIR . '/config/breadcrumb-config.php';
    return isset($config[$page_name]) ? $config[$page_name] : false;
}

/**
 * Set posts per page for archive
 */
function konderntang_posts_per_page($query)
{
    if (!is_admin() && $query->is_main_query()) {
        // Set posts per page for archive
        if (is_archive() || is_home()) {
            $posts_per_page = absint(konderntang_get_option('layout_posts_per_page', 12));
            if ($posts_per_page > 0) {
                $query->set('posts_per_page', $posts_per_page);
            }
        }
    }
}
add_action('pre_get_posts', 'konderntang_posts_per_page');

/**
 * Get post views count
 *
 * @param int $post_id Post ID.
 * @return int View count.
 */
function konderntang_get_post_view_count($post_id)
{
    // Use the same meta key as the AJAX handler: 'post_views_count'
    $count = get_post_meta($post_id, 'post_views_count', true);
    return $count ? absint($count) : 0;
}
