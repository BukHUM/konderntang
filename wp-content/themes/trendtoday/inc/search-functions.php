<?php
/**
 * Search Functions
 *
 * @package TrendToday
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Modify search query based on settings
 *
 * @param WP_Query $query The WP_Query instance.
 */
function trendtoday_modify_search_query( $query ) {
    if ( ! is_admin() && $query->is_main_query() && $query->is_search() ) {
        // Get search settings
        $search_enabled = get_option( 'trendtoday_search_enabled', '1' );
        if ( $search_enabled !== '1' ) {
            $query->set( 'post__in', array( 0 ) ); // Return no results
            return;
        }
        
        $search_post_types = get_option( 'trendtoday_search_post_types', array( 'post' ) );
        $search_exclude_categories = get_option( 'trendtoday_search_exclude_categories', array() );
        $search_results_sort = get_option( 'trendtoday_search_results_sort', 'relevance' );
        
        // Set post types
        if ( ! empty( $search_post_types ) ) {
            $query->set( 'post_type', $search_post_types );
        }
        
        // Exclude categories
        if ( ! empty( $search_exclude_categories ) ) {
            $query->set( 'category__not_in', $search_exclude_categories );
        }
        
        // Set sort order
        switch ( $search_results_sort ) {
            case 'date_desc':
                $query->set( 'orderby', 'date' );
                $query->set( 'order', 'DESC' );
                break;
            case 'date_asc':
                $query->set( 'orderby', 'date' );
                $query->set( 'order', 'ASC' );
                break;
            case 'title_asc':
                $query->set( 'orderby', 'title' );
                $query->set( 'order', 'ASC' );
                break;
            case 'title_desc':
                $query->set( 'orderby', 'title' );
                $query->set( 'order', 'DESC' );
                break;
            case 'relevance':
            default:
                // WordPress default relevance search
                $query->set( 'orderby', 'relevance' );
                break;
        }
    }
}
add_action( 'pre_get_posts', 'trendtoday_modify_search_query' );
