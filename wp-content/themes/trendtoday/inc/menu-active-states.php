<?php
/**
 * Menu Active States
 *
 * @package TrendToday
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Add active class to current menu item
 *
 * @param array $classes CSS classes.
 * @param object $item Menu item.
 * @param array $args Menu arguments.
 * @return array Modified CSS classes.
 */
function trendtoday_nav_menu_active_classes( $classes, $item, $args ) {
    // Add active class for current page
    if ( in_array( 'current-menu-item', $classes ) ) {
        $classes[] = 'active';
        $classes[] = 'text-gray-900';
    }
    
    // Add active class for current page parent
    if ( in_array( 'current-menu-parent', $classes ) ) {
        $classes[] = 'active';
    }
    
    // Add active class for current page ancestor
    if ( in_array( 'current-menu-ancestor', $classes ) ) {
        $classes[] = 'active';
    }
    
    // Check if menu item is a category link and current post is in that category or its subcategories
    if ( is_single() || is_category() ) {
        // Get the category ID from menu item URL
        $menu_url = $item->url;
        
        // Check if menu item is a category link
        if ( strpos( $menu_url, '/category/' ) !== false ) {
            // Extract category slug from URL
            $url_parts = parse_url( $menu_url );
            if ( isset( $url_parts['path'] ) ) {
                $path_parts = explode( '/category/', $url_parts['path'] );
                if ( isset( $path_parts[1] ) ) {
                    $category_slug = trim( $path_parts[1], '/' );
                    $category_slug = explode( '/', $category_slug );
                    $category_slug = $category_slug[0]; // Get first part (main category slug)
                    
                    // Get category object
                    $menu_category = get_category_by_slug( $category_slug );
                    
                    if ( $menu_category ) {
                        $menu_category_id = $menu_category->term_id;
                        
                        // Get current post categories
                        if ( is_single() ) {
                            $post_categories = get_the_category();
                        } elseif ( is_category() ) {
                            $current_category = get_queried_object();
                            $post_categories = array( $current_category );
                        } else {
                            $post_categories = array();
                        }
                        
                        // Check if current post is in menu category or its subcategories
                        foreach ( $post_categories as $post_category ) {
                            // Check if it's the same category
                            if ( $post_category->term_id == $menu_category_id ) {
                                $classes[] = 'current-menu-item';
                                $classes[] = 'active';
                                $classes[] = 'text-gray-900';
                                break;
                            }
                            
                            // Check if it's a subcategory (check parent chain)
                            $category_ancestors = get_ancestors( $post_category->term_id, 'category' );
                            if ( in_array( $menu_category_id, $category_ancestors ) ) {
                                $classes[] = 'current-menu-ancestor';
                                $classes[] = 'active';
                                $classes[] = 'text-gray-900';
                                break;
                            }
                            
                            // Also check reverse: if menu category is a subcategory of post category
                            $menu_category_ancestors = get_ancestors( $menu_category_id, 'category' );
                            if ( in_array( $post_category->term_id, $menu_category_ancestors ) ) {
                                // This means menu is subcategory, but we want to highlight parent
                                // Actually, we want to highlight the menu item if post is in its subcategory
                                // So we already handled this above
                            }
                        }
                    }
                }
            }
        }
    }

    return $classes;
}
add_filter( 'nav_menu_css_class', 'trendtoday_nav_menu_active_classes', 10, 3 );

/**
 * Add active class to menu item link
 *
 * @param array $atts Link attributes.
 * @param object $item Menu item.
 * @param array $args Menu arguments.
 * @return array Modified link attributes.
 */
function trendtoday_nav_menu_link_active_class( $atts, $item, $args ) {
    $classes = empty( $item->classes ) ? array() : (array) $item->classes;
    
    // Check if menu item should be active (including subcategory check)
    $is_active = in_array( 'current-menu-item', $classes ) 
                 || in_array( 'current-menu-ancestor', $classes )
                 || in_array( 'current-menu-parent', $classes );
    
    // Also check for category/subcategory relationship
    if ( ! $is_active && ( is_single() || is_category() ) ) {
        $menu_url = $item->url;
        
        if ( strpos( $menu_url, '/category/' ) !== false ) {
            $url_parts = parse_url( $menu_url );
            if ( isset( $url_parts['path'] ) ) {
                $path_parts = explode( '/category/', $url_parts['path'] );
                if ( isset( $path_parts[1] ) ) {
                    $category_slug = trim( $path_parts[1], '/' );
                    $category_slug = explode( '/', $category_slug );
                    $category_slug = $category_slug[0];
                    
                    $menu_category = get_category_by_slug( $category_slug );
                    
                    if ( $menu_category ) {
                        $menu_category_id = $menu_category->term_id;
                        
                        if ( is_single() ) {
                            $post_categories = get_the_category();
                        } elseif ( is_category() ) {
                            $current_category = get_queried_object();
                            $post_categories = array( $current_category );
                        } else {
                            $post_categories = array();
                        }
                        
                        foreach ( $post_categories as $post_category ) {
                            if ( $post_category->term_id == $menu_category_id ) {
                                $is_active = true;
                                break;
                            }
                            
                            $category_ancestors = get_ancestors( $post_category->term_id, 'category' );
                            if ( in_array( $menu_category_id, $category_ancestors ) ) {
                                $is_active = true;
                                break;
                            }
                        }
                    }
                }
            }
        }
    }
    
    if ( $is_active ) {
        $existing_class = isset( $atts['class'] ) ? $atts['class'] : '';
        $atts['class'] = trim( $existing_class . ' active text-gray-900 font-medium border-b-2 border-accent pb-1' );
    }
    
    return $atts;
}
add_filter( 'nav_menu_link_attributes', 'trendtoday_nav_menu_link_active_class', 10, 3 );
