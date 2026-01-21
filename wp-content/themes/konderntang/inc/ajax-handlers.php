<?php
/**
 * AJAX Handlers
 *
 * @package KonDernTang
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Load More Posts AJAX Handler
 */
function konderntang_load_more_posts() {
    check_ajax_referer( 'konderntang-nonce', 'nonce' );

    $page = isset( $_POST['page'] ) ? absint( $_POST['page'] ) : 1;
    $posts_per_page = isset( $_POST['posts_per_page'] ) ? absint( $_POST['posts_per_page'] ) : get_option( 'posts_per_page' );
    $category = isset( $_POST['category'] ) ? absint( $_POST['category'] ) : 0;
    $post_type = isset( $_POST['post_type'] ) ? sanitize_text_field( $_POST['post_type'] ) : 'post';

    $args = array(
        'post_type'      => $post_type,
        'posts_per_page' => $posts_per_page,
        'paged'          => $page,
        'post_status'    => 'publish',
    );

    if ( $category > 0 ) {
        $args['cat'] = $category;
    }

    $query = new WP_Query( $args );

    if ( $query->have_posts() ) {
        ob_start();
        while ( $query->have_posts() ) {
            $query->the_post();
            konderntang_get_component( 'post-card', array( 'post' => get_post() ) );
        }
        wp_reset_postdata();
        $html = ob_get_clean();

        wp_send_json_success( array(
            'html' => $html,
            'has_more' => $query->max_num_pages > $page,
        ) );
    } else {
        wp_send_json_error( array( 'message' => esc_html__( 'No more posts found.', 'konderntang' ) ) );
    }
}
add_action( 'wp_ajax_konderntang_load_more', 'konderntang_load_more_posts' );
add_action( 'wp_ajax_nopriv_konderntang_load_more', 'konderntang_load_more_posts' );

/**
 * Search Autocomplete AJAX Handler
 */
function konderntang_search_autocomplete() {
    check_ajax_referer( 'konderntang-nonce', 'nonce' );

    $search_term = isset( $_POST['search'] ) ? sanitize_text_field( $_POST['search'] ) : '';

    if ( strlen( $search_term ) < 2 ) {
        wp_send_json_error( array( 'message' => esc_html__( 'Search term too short.', 'konderntang' ) ) );
    }

    $args = array(
        'post_type'      => 'post',
        'posts_per_page' => 5,
        's'              => $search_term,
        'post_status'    => 'publish',
    );

    $query = new WP_Query( $args );

    $results = array();
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $results[] = array(
                'id'    => get_the_ID(),
                'title' => get_the_title(),
                'url'   => get_permalink(),
                'image' => has_post_thumbnail() ? get_the_post_thumbnail_url( get_the_ID(), 'thumbnail' ) : '',
            );
        }
        wp_reset_postdata();
    }

    wp_send_json_success( array( 'results' => $results ) );
}
add_action( 'wp_ajax_konderntang_search_autocomplete', 'konderntang_search_autocomplete' );
add_action( 'wp_ajax_nopriv_konderntang_search_autocomplete', 'konderntang_search_autocomplete' );

/**
 * Category Filter AJAX Handler
 */
function konderntang_filter_by_category() {
    check_ajax_referer( 'konderntang-nonce', 'nonce' );

    $category = isset( $_POST['category'] ) ? absint( $_POST['category'] ) : 0;
    $page = isset( $_POST['page'] ) ? absint( $_POST['page'] ) : 1;
    $posts_per_page = isset( $_POST['posts_per_page'] ) ? absint( $_POST['posts_per_page'] ) : get_option( 'posts_per_page' );

    $args = array(
        'post_type'      => 'post',
        'posts_per_page' => $posts_per_page,
        'paged'          => $page,
        'post_status'    => 'publish',
    );

    if ( $category > 0 ) {
        $args['cat'] = $category;
    }

    $query = new WP_Query( $args );

    if ( $query->have_posts() ) {
        ob_start();
        while ( $query->have_posts() ) {
            $query->the_post();
            konderntang_get_component( 'post-card', array( 'post' => get_post() ) );
        }
        wp_reset_postdata();
        $html = ob_get_clean();

        wp_send_json_success( array(
            'html' => $html,
            'has_more' => $query->max_num_pages > $page,
            'found_posts' => $query->found_posts,
        ) );
    } else {
        wp_send_json_error( array( 'message' => esc_html__( 'No posts found.', 'konderntang' ) ) );
    }
}
add_action( 'wp_ajax_konderntang_filter_category', 'konderntang_filter_by_category' );
add_action( 'wp_ajax_nopriv_konderntang_filter_category', 'konderntang_filter_by_category' );

/**
 * AJAX Comments Handler
 */
function konderntang_ajax_comments() {
    check_ajax_referer( 'konderntang-nonce', 'nonce' );

    $comment_post_ID = isset( $_POST['comment_post_ID'] ) ? absint( $_POST['comment_post_ID'] ) : 0;
    $comment_content = isset( $_POST['comment'] ) ? trim( $_POST['comment'] ) : '';
    $comment_author = isset( $_POST['author'] ) ? sanitize_text_field( $_POST['author'] ) : '';
    $comment_author_email = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
    $comment_author_url = isset( $_POST['url'] ) ? esc_url_raw( $_POST['url'] ) : '';

    if ( empty( $comment_content ) ) {
        wp_send_json_error( array( 'message' => esc_html__( 'Comment is required.', 'konderntang' ) ) );
    }

    $comment_data = array(
        'comment_post_ID'      => $comment_post_ID,
        'comment_author'       => $comment_author,
        'comment_author_email' => $comment_author_email,
        'comment_author_url'   => $comment_author_url,
        'comment_content'      => $comment_content,
        'comment_type'         => 'comment',
        'comment_parent'       => isset( $_POST['comment_parent'] ) ? absint( $_POST['comment_parent'] ) : 0,
    );

    $comment_id = wp_insert_comment( $comment_data );

    if ( $comment_id ) {
        $comment = get_comment( $comment_id );
        ob_start();
        wp_list_comments( array(
            'style'       => 'ul',
            'short_ping'  => true,
            'callback'    => 'konderntang_comment_callback',
        ), array( $comment ) );
        $html = ob_get_clean();

        wp_send_json_success( array(
            'html' => $html,
            'comment_id' => $comment_id,
        ) );
    } else {
        wp_send_json_error( array( 'message' => esc_html__( 'Failed to submit comment.', 'konderntang' ) ) );
    }
}
add_action( 'wp_ajax_konderntang_ajax_comment', 'konderntang_ajax_comments' );
add_action( 'wp_ajax_nopriv_konderntang_ajax_comment', 'konderntang_ajax_comments' );

/**
 * Comment callback for AJAX comments
 */
function konderntang_comment_callback( $comment, $args, $depth ) {
    $GLOBALS['comment'] = $comment;
    ?>
    <li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
        <div class="comment-body">
            <div class="comment-author vcard">
                <?php echo get_avatar( $comment, 48 ); ?>
                <cite class="fn"><?php comment_author(); ?></cite>
            </div>
            <div class="comment-meta commentmetadata">
                <a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>">
                    <?php
                    printf(
                        esc_html__( '%1$s at %2$s', 'konderntang' ),
                        get_comment_date(),
                        get_comment_time()
                    );
                    ?>
                </a>
            </div>
            <div class="comment-content">
                <?php comment_text(); ?>
            </div>
        </div>
    <?php
}

/**
 * Get Post Views (for tracking)
 */
function konderntang_get_post_views() {
    check_ajax_referer( 'konderntang-nonce', 'nonce' );

    $post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;

    if ( $post_id <= 0 ) {
        wp_send_json_error( array( 'message' => esc_html__( 'Invalid post ID.', 'konderntang' ) ) );
    }

    // Increment view count
    $views = get_post_meta( $post_id, 'post_views_count', true );
    $views = $views ? absint( $views ) + 1 : 1;
    update_post_meta( $post_id, 'post_views_count', $views );

    wp_send_json_success( array( 'views' => $views ) );
}
add_action( 'wp_ajax_konderntang_get_views', 'konderntang_get_post_views' );
add_action( 'wp_ajax_nopriv_konderntang_get_views', 'konderntang_get_post_views' );
