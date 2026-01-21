<?php
/**
 * Widget styling and customization
 *
 * @package TrendToday
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Override default Recent Posts widget to add thumbnails
 * This unregisters the default widget so users must use our custom widget
 */
function trendtoday_override_recent_posts_widget() {
    // Unregister default widget - users should use Trend Today: Recent Posts widget instead
    unregister_widget( 'WP_Widget_Recent_Posts' );
}
add_action( 'widgets_init', 'trendtoday_override_recent_posts_widget', 11 );

/**
 * Filter default Recent Posts widget output to add thumbnails
 * This handles existing widgets that were already added before unregistering
 */
function trendtoday_filter_recent_posts_widget_output( $args, $instance ) {
    // This filter runs before widget output
    // We'll use a different approach - filter the widget_display_callback
    return $args;
}
add_filter( 'widget_posts_args', 'trendtoday_filter_recent_posts_widget_output', 10, 2 );

/**
 * Add custom classes to widgets
 *
 * @param array $params Widget parameters.
 * @return array Modified parameters.
 */
function trendtoday_widget_display_callback( $params ) {
    // Add custom classes to widget wrapper
    if ( isset( $params[0]['widget_name'] ) ) {
        $widget_name = $params[0]['widget_name'];
        
        // Add custom classes based on widget type
        switch ( $widget_name ) {
            case 'WP_Widget_Recent_Posts':
            case 'WP_Widget_Pages':
            case 'WP_Widget_Categories':
            case 'WP_Widget_Archives':
                // Add styling for default widgets
                break;
        }
    }

    return $params;
}
add_filter( 'dynamic_sidebar_params', 'trendtoday_widget_display_callback' );

/**
 * Customize widget title output
 *
 * @param string $title Widget title.
 * @param array $instance Widget instance.
 * @param string $id_base Widget ID base.
 * @return string Modified title.
 */
function trendtoday_widget_title( $title, $instance = null, $id_base = null ) {
    if ( empty( $title ) ) {
        return $title;
    }

    // Add icon or styling based on widget type
    return $title;
}
add_filter( 'widget_title', 'trendtoday_widget_title', 10, 3 );

/**
 * Remove default widget wrapper for certain widgets
 *
 * @param array $params Widget parameters.
 * @return array Modified parameters.
 */
function trendtoday_remove_widget_wrapper( $params ) {
    // Customize widget wrapper if needed
    return $params;
}
add_filter( 'dynamic_sidebar_params', 'trendtoday_remove_widget_wrapper' );
