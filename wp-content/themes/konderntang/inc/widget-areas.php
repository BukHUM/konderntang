<?php
/**
 * Widget Areas Registration
 *
 * @package KonDernTang
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register widget areas
 */
function konderntang_widgets_init() {
    // Primary Sidebar
    register_sidebar(
        array(
            'name'          => esc_html__( 'Primary Sidebar', 'konderntang' ),
            'id'            => 'sidebar-1',
            'description'   => esc_html__( 'Main sidebar for posts and pages', 'konderntang' ),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>',
        )
    );

    // Footer Widget Area 1
    register_sidebar(
        array(
            'name'          => esc_html__( 'Footer Widget Area 1', 'konderntang' ),
            'id'            => 'footer-1',
            'description'   => esc_html__( 'First footer widget area', 'konderntang' ),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h3 class="widget-title">',
            'after_title'   => '</h3>',
        )
    );

    // Footer Widget Area 2
    register_sidebar(
        array(
            'name'          => esc_html__( 'Footer Widget Area 2', 'konderntang' ),
            'id'            => 'footer-2',
            'description'   => esc_html__( 'Second footer widget area', 'konderntang' ),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h3 class="widget-title">',
            'after_title'   => '</h3>',
        )
    );

    // Footer Widget Area 3
    register_sidebar(
        array(
            'name'          => esc_html__( 'Footer Widget Area 3', 'konderntang' ),
            'id'            => 'footer-3',
            'description'   => esc_html__( 'Third footer widget area', 'konderntang' ),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h3 class="widget-title">',
            'after_title'   => '</h3>',
        )
    );

    // Footer Widget Area 4
    register_sidebar(
        array(
            'name'          => esc_html__( 'Footer Widget Area 4', 'konderntang' ),
            'id'            => 'footer-4',
            'description'   => esc_html__( 'Fourth footer widget area', 'konderntang' ),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h3 class="widget-title">',
            'after_title'   => '</h3>',
        )
    );

    // Homepage Sidebar
    register_sidebar(
        array(
            'name'          => esc_html__( 'Homepage Sidebar', 'konderntang' ),
            'id'            => 'homepage-sidebar',
            'description'   => esc_html__( 'Sidebar for homepage', 'konderntang' ),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>',
        )
    );

    // Archive Sidebar
    register_sidebar(
        array(
            'name'          => esc_html__( 'Archive Sidebar', 'konderntang' ),
            'id'            => 'archive-sidebar',
            'description'   => esc_html__( 'Sidebar for archive pages', 'konderntang' ),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>',
        )
    );

    // Single Post Sidebar
    register_sidebar(
        array(
            'name'          => esc_html__( 'Single Post Sidebar', 'konderntang' ),
            'id'            => 'single-sidebar',
            'description'   => esc_html__( 'Sidebar for single post pages', 'konderntang' ),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>',
        )
    );

    // After Content Widget Area
    register_sidebar(
        array(
            'name'          => esc_html__( 'After Content', 'konderntang' ),
            'id'            => 'after-content',
            'description'   => esc_html__( 'Widget area that appears after post content', 'konderntang' ),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h3 class="widget-title">',
            'after_title'   => '</h3>',
        )
    );

    // Before Content Widget Area
    register_sidebar(
        array(
            'name'          => esc_html__( 'Before Content', 'konderntang' ),
            'id'            => 'before-content',
            'description'   => esc_html__( 'Widget area that appears before post content', 'konderntang' ),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h3 class="widget-title">',
            'after_title'   => '</h3>',
        )
    );

    // Header Widget Area
    register_sidebar(
        array(
            'name'          => esc_html__( 'Header Widget Area', 'konderntang' ),
            'id'            => 'header-widget',
            'description'   => esc_html__( 'Optional widget area in the header', 'konderntang' ),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h3 class="widget-title">',
            'after_title'   => '</h3>',
        )
    );
}
add_action( 'widgets_init', 'konderntang_widgets_init' );
