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

/**
 * Get all social media profiles
 *
 * @return array Array of social media profiles with platform as key and URL as value
 */
function konderntang_get_social_profiles()
{
    $platforms = array(
        'facebook'  => array(
            'url'   => konderntang_get_option('social_facebook', ''),
            'label' => __('Facebook', 'konderntang'),
            'icon'  => 'facebook',
            'color' => '#1877f2',
        ),
        'twitter'   => array(
            'url'   => konderntang_get_option('social_twitter', ''),
            'label' => __('X (Twitter)', 'konderntang'),
            'icon'  => 'twitter',
            'color' => '#000000',
        ),
        'instagram' => array(
            'url'   => konderntang_get_option('social_instagram', ''),
            'label' => __('Instagram', 'konderntang'),
            'icon'  => 'instagram',
            'color' => '#e4405f',
        ),
        'youtube'   => array(
            'url'   => konderntang_get_option('social_youtube', ''),
            'label' => __('YouTube', 'konderntang'),
            'icon'  => 'youtube',
            'color' => '#ff0000',
        ),
        'tiktok'    => array(
            'url'   => konderntang_get_option('social_tiktok', ''),
            'label' => __('TikTok', 'konderntang'),
            'icon'  => 'tiktok',
            'color' => '#000000',
        ),
        'line'      => array(
            'url'   => konderntang_get_option('social_line', ''),
            'label' => __('LINE', 'konderntang'),
            'icon'  => 'line',
            'color' => '#00b900',
        ),
        'pinterest' => array(
            'url'   => konderntang_get_option('social_pinterest', ''),
            'label' => __('Pinterest', 'konderntang'),
            'icon'  => 'pinterest',
            'color' => '#bd081c',
        ),
        'linkedin'  => array(
            'url'   => konderntang_get_option('social_linkedin', ''),
            'label' => __('LinkedIn', 'konderntang'),
            'icon'  => 'linkedin',
            'color' => '#0a66c2',
        ),
    );

    // Filter out empty URLs
    return array_filter($platforms, function ($platform) {
        return !empty($platform['url']);
    });
}

/**
 * Get social media display settings
 *
 * @return array Display settings
 */
function konderntang_get_social_settings()
{
    return array(
        'show_header'   => (bool) konderntang_get_option('social_show_header', false),
        'show_footer'   => (bool) konderntang_get_option('social_show_footer', true),
        'icon_style'    => konderntang_get_option('social_icon_style', 'default'),
        'icon_size'     => konderntang_get_option('social_icon_size', 'medium'),
        'open_new_tab'  => (bool) konderntang_get_option('social_open_new_tab', true),
    );
}

/**
 * Render social media icons
 *
 * @param string $location Location context ('header', 'footer', 'widget')
 * @param array  $args     Optional arguments to override default settings
 * @return void
 */
function konderntang_render_social_icons($location = 'footer', $args = array())
{
    $profiles = konderntang_get_social_profiles();
    
    if (empty($profiles)) {
        return;
    }

    $settings = konderntang_get_social_settings();
    
    // Check if we should display based on location
    if ($location === 'header' && !$settings['show_header']) {
        return;
    }
    if ($location === 'footer' && !$settings['show_footer']) {
        return;
    }

    // Merge with custom args
    $settings = wp_parse_args($args, $settings);

    $target = $settings['open_new_tab'] ? ' target="_blank" rel="noopener noreferrer"' : '';
    $style_class = 'konderntang-social-style-' . esc_attr($settings['icon_style']);
    $size_class = 'konderntang-social-size-' . esc_attr($settings['icon_size']);

    echo '<div class="konderntang-social-icons ' . esc_attr($style_class) . ' ' . esc_attr($size_class) . ' konderntang-social-' . esc_attr($location) . '">';
    
    foreach ($profiles as $platform => $data) {
        $icon_class = 'konderntang-social-icon konderntang-social-icon-' . esc_attr($platform);
        printf(
            '<a href="%s" class="%s" aria-label="%s" title="%s"%s style="--social-color: %s;">
                <span class="social-icon-inner"></span>
            </a>',
            esc_url($data['url']),
            esc_attr($icon_class),
            esc_attr(sprintf(__('Follow us on %s', 'konderntang'), $data['label'])),
            esc_attr($data['label']),
            $target,
            esc_attr($data['color'])
        );
    }
    
    echo '</div>';
}