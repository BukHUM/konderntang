<?php
/**
 * Dynamic Content Functions
 *
 * @package TrendToday
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Get popular posts (with caching)
 *
 * @param int    $number Number of posts.
 * @param string $orderby Order by field (views, date, comment_count).
 * @return WP_Query Query object.
 */
function trendtoday_get_popular_posts( $number = 5, $orderby = 'views' ) {
    $cache_key = 'trendtoday_popular_' . $orderby . '_' . $number;
    $cache_duration = 30 * MINUTE_IN_SECONDS; // 30 minutes
    
    // Try to get from cache
    $cached_query = get_transient( $cache_key );
    
    if ( false !== $cached_query && is_array( $cached_query ) ) {
        // Reconstruct WP_Query from cached data
        $query = new WP_Query();
        $query->posts = $cached_query['posts'];
        $query->post_count = count( $cached_query['posts'] );
        $query->found_posts = $cached_query['found_posts'];
        $query->max_num_pages = $cached_query['max_num_pages'];
        $query->query_vars = $cached_query['query_vars'];
        return $query;
    }
    
    $args = array(
        'post_type'      => 'post',
        'posts_per_page' => $number,
        'post_status'    => 'publish',
        'ignore_sticky_posts' => true,
        'update_post_meta_cache' => true,
        'update_post_term_cache' => true,
    );

    switch ( $orderby ) {
        case 'views':
            // Order by views, but always return results
            // If no posts have views, fallback to date order
            // First try to get posts with views
            $args_with_views = $args;
            $args_with_views['meta_key'] = 'post_views';
            $args_with_views['meta_compare'] = 'EXISTS';
            $args_with_views['orderby'] = 'meta_value_num';
            $args_with_views['order'] = 'DESC';
            
            $query_with_views = new WP_Query( $args_with_views );
            
            // If we have posts with views, use them
            if ( $query_with_views->have_posts() ) {
                $query = $query_with_views;
            } else {
                // Fallback to latest posts if no posts have views
                $args['orderby'] = 'date';
                $args['order'] = 'DESC';
                $query = new WP_Query( $args );
            }
            
            // Cache the query results
            if ( $query->have_posts() ) {
                $cache_data = array(
                    'posts' => $query->posts,
                    'found_posts' => $query->found_posts,
                    'max_num_pages' => $query->max_num_pages,
                    'query_vars' => $query->query_vars,
                );
                set_transient( $cache_key, $cache_data, $cache_duration );
            }
            
            return $query;
            break;

        case 'comments':
            $args['orderby'] = 'comment_count';
            $args['order']   = 'DESC';
            break;

        case 'date':
        default:
            $args['orderby'] = 'date';
            $args['order']   = 'DESC';
            break;
    }
    
    // Only create query if not already created (for views case)
    if ( ! isset( $query ) ) {
        $query = new WP_Query( $args );
        
        // Cache the query results
        if ( $query->have_posts() ) {
            $cache_data = array(
                'posts' => $query->posts,
                'found_posts' => $query->found_posts,
                'max_num_pages' => $query->max_num_pages,
                'query_vars' => $query->query_vars,
            );
            set_transient( $cache_key, $cache_data, $cache_duration );
        }
    }
    
    return $query;
}

/**
 * Get trending tags
 *
 * @param int $number Number of tags.
 * @return array Array of tag objects.
 */
function trendtoday_get_trending_tags( $number = 10 ) {
    $tags = get_tags( array(
        'orderby'    => 'count',
        'order'      => 'DESC',
        'number'     => $number,
        'hide_empty' => true,
    ) );

    return $tags;
}

/**
 * Get breaking news posts (with caching)
 *
 * @param int $number Number of posts.
 * @return WP_Query Query object.
 */
function trendtoday_get_breaking_news( $number = 5, $category_id = null ) {
    // Include category ID in cache key if provided
    $cache_key = 'trendtoday_breaking_news_' . $number;
    if ( $category_id ) {
        $cache_key .= '_cat_' . $category_id;
    }
    $cache_duration = 15 * MINUTE_IN_SECONDS; // 15 minutes
    
    // Try to get from cache
    $cached_query = get_transient( $cache_key );
    
    if ( false !== $cached_query && is_array( $cached_query ) ) {
        // Reconstruct WP_Query from cached data
        $query = new WP_Query();
        $query->posts = $cached_query['posts'];
        $query->post_count = count( $cached_query['posts'] );
        $query->found_posts = $cached_query['found_posts'];
        $query->max_num_pages = $cached_query['max_num_pages'];
        $query->query_vars = $cached_query['query_vars'];
        return $query;
    }
    
    $args = array(
        'post_type'      => 'post',
        'posts_per_page' => $number,
        'post_status'    => 'publish',
        'meta_key'       => 'breaking_news',
        'meta_value'     => '1',
        'orderby'        => 'date',
        'order'          => 'DESC',
        'update_post_meta_cache' => true,
        'update_post_term_cache' => true,
    );
    
    // Filter by category if provided
    if ( $category_id ) {
        $args['cat'] = $category_id;
    }

    $query = new WP_Query( $args );
    
    // Cache the query results
    if ( $query->have_posts() ) {
        $cache_data = array(
            'posts' => $query->posts,
            'found_posts' => $query->found_posts,
            'max_num_pages' => $query->max_num_pages,
            'query_vars' => $query->query_vars,
        );
        set_transient( $cache_key, $cache_data, $cache_duration );
    }
    
    return $query;
}

/**
 * Get related posts
 *
 * @param int $post_id Post ID.
 * @param int $number Number of posts.
 * @return WP_Query Query object.
 */
function trendtoday_get_related_posts_query( $post_id = null, $number = 3 ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }

    // Check for manually selected related posts first
    $manual_related = trendtoday_get_related_posts( $post_id );
    if ( ! empty( $manual_related ) ) {
        $args = array(
            'post_type'      => 'any',
            'post__in'       => $manual_related,
            'posts_per_page' => $number,
            'orderby'        => 'post__in',
            'post_status'    => 'publish',
        );
        return new WP_Query( $args );
    }

    // Auto-generate related posts based on categories and tags
    $categories = wp_get_post_categories( $post_id );
    $tags = wp_get_post_tags( $post_id, array( 'fields' => 'ids' ) );

    $args = array(
        'post_type'      => 'post',
        'posts_per_page' => $number,
        'post__not_in'   => array( $post_id ),
        'post_status'    => 'publish',
    );

    if ( ! empty( $categories ) || ! empty( $tags ) ) {
        $args['tax_query'] = array( 'relation' => 'OR' );

        if ( ! empty( $categories ) ) {
            $args['tax_query'][] = array(
                'taxonomy' => 'category',
                'field'    => 'term_id',
                'terms'    => $categories,
            );
        }

        if ( ! empty( $tags ) ) {
            $args['tax_query'][] = array(
                'taxonomy' => 'post_tag',
                'field'    => 'term_id',
                'terms'    => $tags,
            );
        }
    } else {
        // Fallback to recent posts if no categories/tags
        $args['orderby'] = 'date';
        $args['order']   = 'DESC';
    }

    return new WP_Query( $args );
}

/**
 * Get latest posts by category
 *
 * @param int $category_id Category ID.
 * @param int $number Number of posts.
 * @return WP_Query Query object.
 */
function trendtoday_get_latest_by_category( $category_id, $number = 5 ) {
    $args = array(
        'post_type'      => 'post',
        'posts_per_page' => $number,
        'cat'            => $category_id,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
    );

    return new WP_Query( $args );
}

/**
 * Get posts by date range
 *
 * @param string $start_date Start date (Y-m-d).
 * @param string $end_date End date (Y-m-d).
 * @param int    $number Number of posts.
 * @return WP_Query Query object.
 */
function trendtoday_get_posts_by_date_range( $start_date, $end_date, $number = 10 ) {
    $args = array(
        'post_type'      => 'post',
        'posts_per_page' => $number,
        'post_status'    => 'publish',
        'date_query'     => array(
            array(
                'after'     => $start_date,
                'before'    => $end_date,
                'inclusive' => true,
            ),
        ),
        'orderby'        => 'date',
        'order'          => 'DESC',
    );

    return new WP_Query( $args );
}
