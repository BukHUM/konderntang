<?php
/**
 * Theme Setup
 *
 * @package KonDernTang
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 */
function konderntang_setup() {
    // Make theme available for translation
    load_theme_textdomain( 'konderntang', KONDERN_THEME_DIR . '/languages' );

    // Add default posts and comments RSS feed links to head
    add_theme_support( 'automatic-feed-links' );

    // Let WordPress manage the document title
    add_theme_support( 'title-tag' );

    // Enable support for Post Thumbnails
    add_theme_support( 'post-thumbnails' );

    // Set default thumbnail size
    set_post_thumbnail_size( 1200, 675, true );

    // Add image sizes
    add_image_size( 'konderntang-hero', 1920, 1080, true );
    add_image_size( 'konderntang-featured', 800, 450, true );
    add_image_size( 'konderntang-card', 400, 225, true );

    // Register navigation menus
    register_nav_menus(
        array(
            'primary' => esc_html__( 'Primary Menu', 'konderntang' ),
            'footer'  => esc_html__( 'Footer Menu', 'konderntang' ),
        )
    );

    // Switch default core markup to output valid HTML5
    add_theme_support(
        'html5',
        array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'style',
            'script',
        )
    );

    // Add theme support for selective refresh for widgets
    add_theme_support( 'customize-selective-refresh-widgets' );

    // Add support for editor styles
    add_theme_support( 'editor-styles' );

    // Add support for responsive embedded content
    add_theme_support( 'responsive-embeds' );

    // Add support for custom logo
    add_theme_support(
        'custom-logo',
        array(
            'height'      => 100,
            'width'       => 400,
            'flex-width'  => true,
            'flex-height' => true,
        )
    );

    // Add support for post formats
    add_theme_support(
        'post-formats',
        array(
            'aside',
            'image',
            'video',
            'quote',
            'link',
            'gallery',
            'audio',
        )
    );

    // Add support for selective refresh for customizer
    add_theme_support( 'customize-selective-refresh-widgets' );
}
add_action( 'after_setup_theme', 'konderntang_setup' );

/**
 * Set the content width in pixels
 */
function konderntang_content_width() {
    $GLOBALS['content_width'] = apply_filters( 'konderntang_content_width', 1200 );
}
add_action( 'after_setup_theme', 'konderntang_content_width', 0 );
