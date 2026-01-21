<?php
/**
 * Enqueue Scripts and Styles
 *
 * @package KonDernTang
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enqueue scripts and styles
 */
function konderntang_scripts()
{
    // Styles
    wp_enqueue_style(
        'konderntang-style',
        get_stylesheet_uri(),
        array(),
        KONDERN_THEME_VERSION
    );

    // Google Fonts
    wp_enqueue_style(
        'konderntang-fonts',
        'https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&family=Sarabun:wght@300;400;500;600&display=swap',
        array(),
        null
    );

    // Phosphor Icons
    wp_enqueue_script(
        'konderntang-phosphor-icons',
        'https://unpkg.com/@phosphor-icons/web',
        array(),
        null,
        false
    );

    // Widget Styles
    wp_enqueue_style(
        'konderntang-widgets',
        KONDERN_THEME_URI . '/assets/css/widgets.css',
        array('konderntang-style'),
        KONDERN_THEME_VERSION
    );

    // Table of Contents Styles (only on single posts)
    if (is_singular('post')) {
        wp_enqueue_style(
            'konderntang-toc',
            KONDERN_THEME_URI . '/assets/css/toc.css',
            array('konderntang-style'),
            KONDERN_THEME_VERSION
        );
    }

    // Main JavaScript
    wp_enqueue_script(
        'konderntang-main',
        KONDERN_THEME_URI . '/assets/js/main.js',
        array(),
        KONDERN_THEME_VERSION,
        true
    );

    // Utils JavaScript
    wp_enqueue_script(
        'konderntang-utils',
        KONDERN_THEME_URI . '/assets/js/utils.js',
        array('konderntang-main'),
        KONDERN_THEME_VERSION,
        true
    );

    // Hero Slider JavaScript (only on front page)
    if (is_front_page()) {
        wp_enqueue_script(
            'konderntang-hero-slider',
            KONDERN_THEME_URI . '/assets/js/hero-slider.js',
            array('konderntang-main'),
            KONDERN_THEME_VERSION,
            true
        );
    }

    // AJAX JavaScript
    wp_enqueue_script(
        'konderntang-ajax',
        KONDERN_THEME_URI . '/assets/js/ajax.js',
        array('konderntang-main'),
        KONDERN_THEME_VERSION,
        true
    );

    // Table of Contents JavaScript (only on single posts)
    if (is_singular('post')) {
        wp_enqueue_script(
            'konderntang-toc',
            KONDERN_THEME_URI . '/assets/js/toc.js',
            array('konderntang-main'),
            KONDERN_THEME_VERSION,
            true
        );
    }

    // Cookie Consent JavaScript (always load)
    wp_enqueue_script(
        'konderntang-cookie-consent',
        KONDERN_THEME_URI . '/assets/js/cookie-consent.js',
        array('konderntang-main'),
        KONDERN_THEME_VERSION,
        true
    );

    // Search JavaScript (on search page)
    if (is_search()) {
        wp_enqueue_script(
            'konderntang-search',
            KONDERN_THEME_URI . '/assets/js/search.js',
            array('konderntang-main'),
            KONDERN_THEME_VERSION,
            true
        );
    }

    // Countdown JavaScript (on promotion page)
    if (is_page('promotion') || is_page_template('page-promotion.php')) {
        wp_enqueue_script(
            'konderntang-countdown',
            KONDERN_THEME_URI . '/assets/js/countdown.js',
            array('konderntang-main'),
            KONDERN_THEME_VERSION,
            true
        );
    }

    // Contact JavaScript (on contact page)
    if (is_page('contact') || is_page_template('page-contact.php')) {
        wp_enqueue_script(
            'konderntang-contact',
            KONDERN_THEME_URI . '/assets/js/contact.js',
            array('konderntang-main'),
            KONDERN_THEME_VERSION,
            true
        );
    }

    // Auth JavaScript (on login page)
    if (is_page('login') || is_page_template('page-login.php')) {
        wp_enqueue_script(
            'konderntang-auth',
            KONDERN_THEME_URI . '/assets/js/auth.js',
            array('konderntang-main'),
            KONDERN_THEME_VERSION,
            true
        );
    }

    // News Archive JavaScript (on news archive page)
    if (is_post_type_archive('news') || is_page('news-archive') || is_page_template('page-news-archive.php')) {
        wp_enqueue_script(
            'konderntang-news-archive',
            KONDERN_THEME_URI . '/assets/js/news-archive.js',
            array('konderntang-main'),
            KONDERN_THEME_VERSION,
            true
        );
    }

    // Recently Viewed JavaScript (always load for tracking)
    wp_enqueue_script(
        'konderntang-recently-viewed',
        KONDERN_THEME_URI . '/assets/js/recently-viewed.js',
        array('konderntang-main'),
        KONDERN_THEME_VERSION,
        true
    );

    // Seasonal JavaScript (on seasonal page)
    if (is_page('seasonal') || is_page_template('page-seasonal.php')) {
        wp_enqueue_script(
            'konderntang-seasonal',
            KONDERN_THEME_URI . '/assets/js/seasonal.js',
            array('konderntang-main'),
            KONDERN_THEME_VERSION,
            true
        );
    }

    // Error Pages JavaScript (on 404/500 pages)
    if (is_404() || is_page_template('page-500.php')) {
        wp_enqueue_script(
            'konderntang-error-pages',
            KONDERN_THEME_URI . '/assets/js/error-pages.js',
            array('konderntang-main'),
            KONDERN_THEME_VERSION,
            true
        );
    }

    // Load breadcrumb config
    $breadcrumb_config = require KONDERN_THEME_DIR . '/config/breadcrumb-config.php';

    // Localize script
    wp_localize_script(
        'konderntang-main',
        'konderntangData',
        array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('konderntang-nonce'),
            'breadcrumbConfig' => $breadcrumb_config,
        )
    );

    // Comment reply script
    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}
add_action('wp_enqueue_scripts', 'konderntang_scripts');

/**
 * Enqueue customizer preview script
 */
function konderntang_customize_preview_js()
{
    wp_enqueue_script(
        'konderntang-customizer-preview',
        KONDERN_THEME_URI . '/inc/customizer-preview.js',
        array('customize-preview', 'jquery'),
        KONDERN_THEME_VERSION,
        true
    );
}
add_action('customize_preview_init', 'konderntang_customize_preview_js');

/**
 * Enqueue admin styles and scripts
 */
function konderntang_admin_scripts($hook)
{
    // Load on all admin pages for better coverage
    // Only load on our admin pages, post edit pages, and settings pages
    $allowed_hooks = array(
        'toplevel_page_konderntang',
        'konderntang_page_konderntang-settings',
        'konderntang_page_konderntang-docs',
        'post.php',
        'post-new.php',
        'edit.php',
    );
    
    // Check if current hook matches or contains 'konderntang'
    $should_load = false;
    if (in_array($hook, $allowed_hooks, true)) {
        $should_load = true;
    } elseif (strpos($hook, 'konderntang') !== false) {
        $should_load = true;
    }
    
    if (!$should_load) {
        return;
    }
    
    // Use filemtime for cache busting - forces reload when files change
    $css_path = KONDERN_THEME_DIR . '/assets/css/admin.css';
    $js_path = KONDERN_THEME_DIR . '/assets/js/admin.js';
    
    // Force cache busting by using current timestamp
    // This ensures CSS/JS always reloads during development
    $css_version = file_exists($css_path) 
        ? filemtime($css_path) 
        : time();
    
    // Add timestamp to force reload
    $css_version = $css_version . '.' . time();
    
    $js_version = file_exists($js_path) 
        ? filemtime($js_path) 
        : time();
    
    // Add timestamp to force reload
    $js_version = $js_version . '.' . time();
    
    // Admin CSS
    wp_enqueue_style(
        'konderntang-admin',
        KONDERN_THEME_URI . '/assets/css/admin.css',
        array(),
        $css_version
    );
    
    // Admin JavaScript
    wp_enqueue_script(
        'konderntang-admin',
        KONDERN_THEME_URI . '/assets/js/admin.js',
        array('jquery'),
        $js_version,
        true
    );
}
add_action('admin_enqueue_scripts', 'konderntang_admin_scripts');

/**
 * Add defer attribute to scripts
 */
function konderntang_defer_scripts($tag, $handle)
{
    $deferred_scripts = array(
        'konderntang-main',
        'konderntang-utils',
        'konderntang-hero-slider',
        'konderntang-ajax',
        'konderntang-toc',
        'konderntang-cookie-consent',
        'konderntang-search',
        'konderntang-countdown',
        'konderntang-contact',
        'konderntang-auth',
        'konderntang-news-archive',
        'konderntang-recently-viewed',
        'konderntang-seasonal',
        'konderntang-error-pages'
    );

    if (in_array($handle, $deferred_scripts)) {
        return str_replace(' src', ' defer="defer" src', $tag);
    }

    return $tag;
}
add_filter('script_loader_tag', 'konderntang_defer_scripts', 10, 2);
