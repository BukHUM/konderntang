<?php
/**
 * Widget helper functions
 *
 * @package TrendToday
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Check if widget area is active
 *
 * @param string $sidebar_id Sidebar ID.
 * @return bool True if active, false otherwise.
 */
function trendtoday_is_sidebar_active( $sidebar_id ) {
    return is_active_sidebar( $sidebar_id );
}

/**
 * Get widget count for a sidebar
 *
 * @param string $sidebar_id Sidebar ID.
 * @return int Number of widgets.
 */
function trendtoday_get_widget_count( $sidebar_id ) {
    global $wp_registered_sidebars;
    
    if ( ! isset( $wp_registered_sidebars[ $sidebar_id ] ) ) {
        return 0;
    }

    $sidebars = wp_get_sidebars_widgets();
    
    if ( ! isset( $sidebars[ $sidebar_id ] ) ) {
        return 0;
    }

    return count( $sidebars[ $sidebar_id ] );
}

/**
 * Display widget area with fallback
 *
 * @param string $sidebar_id Sidebar ID.
 * @param string $fallback_content Fallback content if no widgets.
 */
function trendtoday_display_sidebar( $sidebar_id, $fallback_content = '' ) {
    if ( is_active_sidebar( $sidebar_id ) ) {
        dynamic_sidebar( $sidebar_id );
    } elseif ( ! empty( $fallback_content ) ) {
        echo $fallback_content;
    }
}

/**
 * Get all registered widget areas
 *
 * @return array Array of widget areas.
 */
function trendtoday_get_widget_areas() {
    global $wp_registered_sidebars;
    return $wp_registered_sidebars;
}
