<?php
/**
 * Custom Navigation Walker for Trend Today Theme
 *
 * @package TrendToday
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Custom Walker Class for Navigation Menu
 */
class TrendToday_Walker_Nav_Menu extends Walker_Nav_Menu {

    /**
     * Start the element output.
     *
     * @param string $output Passed by reference. Used to append additional content.
     * @param object $item   Menu item data object.
     * @param int    $depth  Depth of menu item. Used for padding.
     * @param array  $args   An array of arguments. @see wp_nav_menu()
     * @param int    $id     Current item ID.
     */
    function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
        $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

        $classes = empty( $item->classes ) ? array() : (array) $item->classes;
        $classes[] = 'menu-item-' . $item->ID;

        /**
         * Filter the CSS class(es) applied to a menu item's list item element.
         */
        $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
        $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

        /**
         * Filter the ID applied to a menu item's list item element.
         */
        $id = apply_filters( 'nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args );
        $id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

        $output .= $indent . '<li' . $id . $class_names .'>';

        $attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
        $attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
        $attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
        $attributes .= ! empty( $item->url )         ? ' href="'   . esc_attr( $item->url        ) .'"' : '';

        // Get menu item icon
        $icon = function_exists( 'trendtoday_get_menu_item_icon' ) ? trendtoday_get_menu_item_icon( $item->ID ) : '';
        $icon_html = $icon ? '<i class="' . esc_attr( $icon ) . ' mr-1"></i>' : '';

        // Check if current menu item is active (including ancestor check for subcategories)
        $is_current = in_array( 'current-menu-item', $classes ) 
                     || in_array( 'current_page_item', $classes )
                     || in_array( 'current-menu-ancestor', $classes )
                     || in_array( 'current-menu-parent', $classes );
        
        // Also check for category/subcategory relationship
        if ( ! $is_current && ( is_single() || is_category() ) ) {
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
                                    $is_current = true;
                                    break;
                                }
                                
                                $category_ancestors = get_ancestors( $post_category->term_id, 'category' );
                                if ( in_array( $menu_category_id, $category_ancestors ) ) {
                                    $is_current = true;
                                    break;
                                }
                            }
                        }
                    }
                }
            }
        }
        
        $link_classes = $is_current 
            ? 'text-gray-900 font-medium border-b-2 border-accent pb-1' 
            : 'text-gray-500 hover:text-gray-900 transition font-medium';
        
        // Add custom classes for styling
        $item_output = isset( $args->before ) ? $args->before : '';
        $item_output .= '<a' . $attributes . ' class="' . esc_attr( $link_classes ) . '">';
        $item_output .= $icon_html;
        $item_output .= ( isset( $args->link_before ) ? $args->link_before : '' ) . apply_filters( 'the_title', $item->title, $item->ID ) . ( isset( $args->link_after ) ? $args->link_after : '' );
        $item_output .= '</a>';
        $item_output .= isset( $args->after ) ? $args->after : '';

        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
    }

    /**
     * End the element output.
     */
    function end_el( &$output, $item, $depth = 0, $args = null ) {
        $output .= "</li>\n";
    }
}

/**
 * Mobile Menu Walker
 */
class TrendToday_Walker_Nav_Menu_Mobile extends Walker_Nav_Menu {

    /**
     * Start the element output.
     */
    function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
        $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

        $classes = empty( $item->classes ) ? array() : (array) $item->classes;
        $classes[] = 'menu-item-' . $item->ID;

        $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
        $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

        $id = apply_filters( 'nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args );
        $id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

        $output .= $indent . '<li' . $id . $class_names .'>';

        $attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
        $attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
        $attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
        $attributes .= ! empty( $item->url )         ? ' href="'   . esc_attr( $item->url        ) .'"' : '';

        // Get menu item icon
        $icon = function_exists( 'trendtoday_get_menu_item_icon' ) ? trendtoday_get_menu_item_icon( $item->ID ) : '';
        $icon_html = $icon ? '<i class="' . esc_attr( $icon ) . ' mr-2"></i>' : '';

        // Check if current menu item is active
        $is_current = in_array( 'current-menu-item', $classes ) || in_array( 'current_page_item', $classes );
        $link_classes = $is_current 
            ? 'block px-3 py-2 rounded-md text-base font-medium text-black bg-gray-50' 
            : 'block px-3 py-2 rounded-md text-base font-medium text-gray-600 hover:bg-gray-50';
        
        $item_output = isset( $args->before ) ? $args->before : '';
        $item_output .= '<a' . $attributes . ' class="' . esc_attr( $link_classes ) . '" role="menuitem">';
        $item_output .= $icon_html;
        $item_output .= ( isset( $args->link_before ) ? $args->link_before : '' ) . apply_filters( 'the_title', $item->title, $item->ID ) . ( isset( $args->link_after ) ? $args->link_after : '' );
        $item_output .= '</a>';
        $item_output .= isset( $args->after ) ? $args->after : '';

        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
    }

    /**
     * End the element output.
     */
    function end_el( &$output, $item, $depth = 0, $args = null ) {
        $output .= "</li>\n";
    }
}
