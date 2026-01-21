<?php
/**
 * Custom Post Type Helper Functions
 *
 * @package TrendToday
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Get featured stories
 *
 * @param int    $number Number of posts to retrieve.
 * @param string $orderby Order by field.
 * @param string $order Order direction.
 * @return WP_Query Query object.
 */
function trendtoday_get_featured_stories( $number = 5, $orderby = 'meta_value_num', $order = 'DESC' ) {
    $args = array(
        'post_type'      => 'featured_story',
        'posts_per_page' => $number,
        'orderby'        => $orderby,
        'order'          => $order,
        'meta_key'       => 'featured_priority',
        'meta_query'     => array(
            'relation' => 'OR',
            array(
                'key'     => 'featured_expiry',
                'value'   => date( 'Y-m-d' ),
                'compare' => '>=',
            ),
            array(
                'key'     => 'featured_expiry',
                'compare' => 'NOT EXISTS',
            ),
        ),
    );

    return new WP_Query( $args );
}

/**
 * Get latest video news
 *
 * @param int $number Number of posts to retrieve.
 * @return WP_Query Query object.
 */
function trendtoday_get_latest_videos( $number = 6 ) {
    $args = array(
        'post_type'      => 'video_news',
        'posts_per_page' => $number,
        'orderby'        => 'date',
        'order'          => 'DESC',
    );

    return new WP_Query( $args );
}

/**
 * Get latest galleries
 *
 * @param int $number Number of posts to retrieve.
 * @return WP_Query Query object.
 */
function trendtoday_get_latest_galleries( $number = 6 ) {
    $args = array(
        'post_type'      => 'gallery',
        'posts_per_page' => $number,
        'orderby'        => 'date',
        'order'          => 'DESC',
    );

    return new WP_Query( $args );
}

/**
 * Get posts by post type
 *
 * @param string|array $post_types Post type(s).
 * @param int          $number Number of posts.
 * @param array        $args Additional query arguments.
 * @return WP_Query Query object.
 */
function trendtoday_get_posts_by_type( $post_types, $number = 10, $args = array() ) {
    $defaults = array(
        'post_type'      => $post_types,
        'posts_per_page' => $number,
        'orderby'        => 'date',
        'order'          => 'DESC',
    );

    $args = wp_parse_args( $args, $defaults );

    return new WP_Query( $args );
}

/**
 * Check if featured story is expired
 *
 * @param int $post_id Post ID.
 * @return bool True if expired, false otherwise.
 */
function trendtoday_is_featured_expired( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }

    $expiry = get_post_meta( $post_id, 'featured_expiry', true );

    if ( ! $expiry ) {
        return false;
    }

    return strtotime( $expiry ) < time();
}
