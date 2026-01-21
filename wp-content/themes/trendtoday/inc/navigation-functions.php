<?php
/**
 * Navigation menu helper functions
 *
 * @package TrendToday
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Get menu items for a specific location
 *
 * @param string $location Menu location.
 * @return array Array of menu items.
 */
function trendtoday_get_menu_items( $location = 'primary' ) {
    $locations = get_nav_menu_locations();
    
    if ( ! isset( $locations[ $location ] ) ) {
        return array();
    }

    $menu = wp_get_nav_menu_object( $locations[ $location ] );
    
    if ( ! $menu ) {
        return array();
    }

    return wp_get_nav_menu_items( $menu->term_id );
}

/**
 * Check if menu location has items
 *
 * @param string $location Menu location.
 * @return bool True if has items, false otherwise.
 */
function trendtoday_has_menu_items( $location = 'primary' ) {
    $items = trendtoday_get_menu_items( $location );
    return ! empty( $items );
}

/**
 * Get menu item count
 *
 * @param string $location Menu location.
 * @return int Number of menu items.
 */
function trendtoday_get_menu_item_count( $location = 'primary' ) {
    $items = trendtoday_get_menu_items( $location );
    return count( $items );
}

/**
 * Add active class to current menu item
 *
 * @param array $classes CSS classes.
 * @param object $item Menu item.
 * @return array Modified CSS classes.
 */
function trendtoday_nav_menu_css_class( $classes, $item ) {
    if ( in_array( 'current-menu-item', $classes ) ) {
        $classes[] = 'active';
    }
    return $classes;
}
add_filter( 'nav_menu_css_class', 'trendtoday_nav_menu_css_class', 10, 2 );

/**
 * Add active class to current menu item link
 *
 * @param array $atts Link attributes.
 * @param object $item Menu item.
 * @return array Modified link attributes.
 */
function trendtoday_nav_menu_link_attributes( $atts, $item ) {
    if ( in_array( 'current-menu-item', $item->classes ) ) {
        $atts['class'] = isset( $atts['class'] ) ? $atts['class'] . ' active' : 'active';
    }
    return $atts;
}
add_filter( 'nav_menu_link_attributes', 'trendtoday_nav_menu_link_attributes', 10, 2 );

/**
 * Register default menu items if no menu is set
 *
 * @return array Default menu items.
 */
function trendtoday_get_default_menu_items() {
    $items = array();
    
    // Home
    $items[] = array(
        'title' => __( 'หน้าแรก', 'trendtoday' ),
        'url'   => home_url( '/' ),
    );

    // Categories
    $categories = get_categories( array( 'number' => 5 ) );
    foreach ( $categories as $category ) {
        $items[] = array(
            'title' => $category->name,
            'url'   => get_category_link( $category->term_id ),
        );
    }

    return $items;
}
