<?php
/**
 * AJAX Handlers
 *
 * @package KonDernTang
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Load More Posts AJAX Handler
 */
function konderntang_load_more_posts()
{
    check_ajax_referer('konderntang-nonce', 'nonce');

    $page = isset($_POST['page']) ? absint($_POST['page']) : 1;
    $posts_per_page = isset($_POST['posts_per_page']) ? absint($_POST['posts_per_page']) : get_option('posts_per_page');
    $category = isset($_POST['category']) ? absint($_POST['category']) : 0;
    $post_type = isset($_POST['post_type']) ? sanitize_text_field($_POST['post_type']) : 'post';

    $args = array(
        'post_type' => $post_type,
        'posts_per_page' => $posts_per_page,
        'paged' => $page,
        'post_status' => 'publish',
    );

    if ($category > 0) {
        $args['cat'] = $category;
    }

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        ob_start();
        while ($query->have_posts()) {
            $query->the_post();
            konderntang_get_component('post-card', array('post' => get_post()));
        }
        wp_reset_postdata();
        $html = ob_get_clean();

        wp_send_json_success(array(
            'html' => $html,
            'has_more' => $query->max_num_pages > $page,
        ));
    } else {
        wp_send_json_error(array('message' => esc_html__('No more posts found.', 'konderntang')));
    }
}
add_action('wp_ajax_konderntang_load_more', 'konderntang_load_more_posts');
add_action('wp_ajax_nopriv_konderntang_load_more', 'konderntang_load_more_posts');

/**
 * Search Autocomplete AJAX Handler
 */
function konderntang_search_autocomplete()
{
    check_ajax_referer('konderntang-nonce', 'nonce');

    $search_term = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';

    if (strlen($search_term) < 2) {
        wp_send_json_error(array('message' => esc_html__('Search term too short.', 'konderntang')));
    }

    $args = array(
        'post_type' => 'post',
        'posts_per_page' => 5,
        's' => $search_term,
        'post_status' => 'publish',
    );

    $query = new WP_Query($args);

    $results = array();
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $results[] = array(
                'id' => get_the_ID(),
                'title' => get_the_title(),
                'url' => get_permalink(),
                'image' => has_post_thumbnail() ? get_the_post_thumbnail_url(get_the_ID(), 'thumbnail') : '',
            );
        }
        wp_reset_postdata();
    }

    wp_send_json_success(array('results' => $results));
}
add_action('wp_ajax_konderntang_search_autocomplete', 'konderntang_search_autocomplete');
add_action('wp_ajax_nopriv_konderntang_search_autocomplete', 'konderntang_search_autocomplete');

/**
 * Category Filter AJAX Handler
 */
function konderntang_filter_by_category()
{
    check_ajax_referer('konderntang-nonce', 'nonce');

    $category = isset($_POST['category']) ? absint($_POST['category']) : 0;
    $page = isset($_POST['page']) ? absint($_POST['page']) : 1;
    $posts_per_page = isset($_POST['posts_per_page']) ? absint($_POST['posts_per_page']) : get_option('posts_per_page');

    $args = array(
        'post_type' => 'post',
        'posts_per_page' => $posts_per_page,
        'paged' => $page,
        'post_status' => 'publish',
    );

    if ($category > 0) {
        $args['cat'] = $category;
    }

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        ob_start();
        while ($query->have_posts()) {
            $query->the_post();
            konderntang_get_component('post-card', array('post' => get_post()));
        }
        wp_reset_postdata();
        $html = ob_get_clean();

        wp_send_json_success(array(
            'html' => $html,
            'has_more' => $query->max_num_pages > $page,
            'found_posts' => $query->found_posts,
        ));
    } else {
        wp_send_json_error(array('message' => esc_html__('No posts found.', 'konderntang')));
    }
}
add_action('wp_ajax_konderntang_filter_category', 'konderntang_filter_by_category');
add_action('wp_ajax_nopriv_konderntang_filter_category', 'konderntang_filter_by_category');

/**
 * AJAX Comments Handler
 */
function konderntang_ajax_comments()
{
    check_ajax_referer('konderntang-nonce', 'nonce');

    $comment_post_ID = isset($_POST['comment_post_ID']) ? absint($_POST['comment_post_ID']) : 0;
    $comment_content = isset($_POST['comment']) ? trim($_POST['comment']) : '';
    $comment_author = isset($_POST['author']) ? sanitize_text_field($_POST['author']) : '';
    $comment_author_email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    $comment_author_url = isset($_POST['url']) ? esc_url_raw($_POST['url']) : '';

    if (empty($comment_content)) {
        wp_send_json_error(array('message' => esc_html__('Comment is required.', 'konderntang')));
    }

    $comment_data = array(
        'comment_post_ID' => $comment_post_ID,
        'comment_author' => $comment_author,
        'comment_author_email' => $comment_author_email,
        'comment_author_url' => $comment_author_url,
        'comment_content' => $comment_content,
        'comment_type' => 'comment',
        'comment_parent' => isset($_POST['comment_parent']) ? absint($_POST['comment_parent']) : 0,
    );

    $comment_id = wp_insert_comment($comment_data);

    if ($comment_id) {
        $comment = get_comment($comment_id);
        ob_start();
        wp_list_comments(array(
            'style' => 'ul',
            'short_ping' => true,
            'callback' => 'konderntang_comment_callback',
        ), array($comment));
        $html = ob_get_clean();

        wp_send_json_success(array(
            'html' => $html,
            'comment_id' => $comment_id,
        ));
    } else {
        wp_send_json_error(array('message' => esc_html__('Failed to submit comment.', 'konderntang')));
    }
}
add_action('wp_ajax_konderntang_ajax_comment', 'konderntang_ajax_comments');
add_action('wp_ajax_nopriv_konderntang_ajax_comment', 'konderntang_ajax_comments');

/**
 * Comment callback for AJAX comments
 */
function konderntang_comment_callback($comment, $args, $depth)
{
    $GLOBALS['comment'] = $comment;
    ?>
    <li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
        <div class="comment-body">
            <div class="comment-author vcard">
                <?php echo get_avatar($comment, 48); ?>
                <cite class="fn"><?php comment_author(); ?></cite>
            </div>
            <div class="comment-meta commentmetadata">
                <a href="<?php echo esc_url(get_comment_link($comment->comment_ID)); ?>">
                    <?php
                    printf(
                        esc_html__('%1$s at %2$s', 'konderntang'),
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
function konderntang_get_post_views()
{
    check_ajax_referer('konderntang-nonce', 'nonce');

    $post_id = isset($_POST['post_id']) ? absint($_POST['post_id']) : 0;

    if ($post_id <= 0) {
        wp_send_json_error(array('message' => esc_html__('Invalid post ID.', 'konderntang')));
    }

    // Increment view count
    $views = get_post_meta($post_id, 'post_views_count', true);
    $views = $views ? absint($views) + 1 : 1;
    update_post_meta($post_id, 'post_views_count', $views);

    wp_send_json_success(array('views' => $views));
}
add_action('wp_ajax_konderntang_get_views', 'konderntang_get_post_views');
add_action('wp_ajax_nopriv_konderntang_get_views', 'konderntang_get_post_views');

/**
 * Track Reading Time AJAX Handler
 */
function konderntang_track_reading_time_ajax()
{
    check_ajax_referer('konderntang-behavior-nonce', 'nonce');

    $post_id = isset($_POST['post_id']) ? absint($_POST['post_id']) : 0;
    $duration = isset($_POST['duration']) ? absint($_POST['duration']) : 0;

    if ($post_id <= 0 || $duration <= 0) {
        wp_send_json_error(array('message' => esc_html__('Invalid data.', 'konderntang')));
    }

    // Get effective user ID
    $user_id = konderntang_get_effective_user_id();

    // Track reading time
    $success = konderntang_track_reading_time($post_id, $user_id, $duration);

    if ($success) {
        wp_send_json_success(array('message' => 'Reading time tracked'));
    } else {
        wp_send_json_error(array('message' => 'Failed to track reading time'));
    }
}
add_action('wp_ajax_konderntang_track_reading_time', 'konderntang_track_reading_time_ajax');
add_action('wp_ajax_nopriv_konderntang_track_reading_time', 'konderntang_track_reading_time_ajax');

/**
 * Track Scroll Depth AJAX Handler
 */
function konderntang_track_scroll_depth_ajax()
{
    check_ajax_referer('konderntang-behavior-nonce', 'nonce');

    $post_id = isset($_POST['post_id']) ? absint($_POST['post_id']) : 0;
    $depth = isset($_POST['depth']) ? absint($_POST['depth']) : 0;

    if ($post_id <= 0 || $depth < 0 || $depth > 100) {
        wp_send_json_error(array('message' => esc_html__('Invalid data.', 'konderntang')));
    }

    // Get effective user ID
    $user_id = konderntang_get_effective_user_id();

    // Track post view
    $success = konderntang_track_post_view($post_id, $user_id);

    if ($success) {
        wp_send_json_success(array('message' => 'Scroll depth tracked'));
    } else {
        wp_send_json_error(array('message' => 'Failed to track scroll depth'));
    }
}
add_action('wp_ajax_konderntang_track_scroll', 'konderntang_track_scroll_depth_ajax');
add_action('wp_ajax_nopriv_konderntang_track_scroll', 'konderntang_track_scroll_depth_ajax');

/**
 * Track Search Keywords AJAX Handler
 */
function konderntang_track_search_ajax()
{
    check_ajax_referer('konderntang-behavior-nonce', 'nonce');

    $keywords = isset($_POST['keywords']) ? sanitize_text_field($_POST['keywords']) : '';

    if (empty($keywords)) {
        wp_send_json_error(array('message' => esc_html__('Invalid keywords.', 'konderntang')));
    }

    // Get effective user ID
    $user_id = konderntang_get_effective_user_id();

    // Track search keywords
    $success = konderntang_track_search_keywords($user_id, $keywords);

    if ($success) {
        wp_send_json_success(array('message' => 'Search keywords tracked'));
    } else {
        wp_send_json_error(array('message' => 'Failed to track search keywords'));
    }
}
add_action('wp_ajax_konderntang_track_search', 'konderntang_track_search_ajax');
add_action('wp_ajax_nopriv_konderntang_track_search', 'konderntang_track_search_ajax');

/**
 * Track Referrer AJAX Handler
 */
function konderntang_track_referrer_ajax()
{
    check_ajax_referer('konderntang-behavior-nonce', 'nonce');

    $referrer = isset($_POST['referrer']) ? esc_url_raw($_POST['referrer']) : '';

    if (empty($referrer)) {
        wp_send_json_error(array('message' => esc_html__('Invalid referrer.', 'konderntang')));
    }

    // Get effective user ID
    $user_id = konderntang_get_effective_user_id();

    // Track referrer
    $success = konderntang_track_referrer($user_id, $referrer);

    if ($success) {
        wp_send_json_success(array('message' => 'Referrer tracked'));
    } else {
        wp_send_json_error(array('message' => 'Failed to track referrer'));
    }
}
add_action('wp_ajax_konderntang_track_referrer', 'konderntang_track_referrer_ajax');
add_action('wp_ajax_nopriv_konderntang_track_referrer', 'konderntang_track_referrer_ajax');

/**
 * Track UTM Parameters AJAX Handler
 */
function konderntang_track_utm_ajax()
{
    check_ajax_referer('konderntang-behavior-nonce', 'nonce');

    $utm_source = isset($_POST['utm_source']) ? sanitize_text_field($_POST['utm_source']) : '';
    $utm_medium = isset($_POST['utm_medium']) ? sanitize_text_field($_POST['utm_medium']) : '';
    $utm_campaign = isset($_POST['utm_campaign']) ? sanitize_text_field($_POST['utm_campaign']) : '';

    $utm_params = array();
    if (!empty($utm_source)) {
        $utm_params['utm_source'] = $utm_source;
    }
    if (!empty($utm_medium)) {
        $utm_params['utm_medium'] = $utm_medium;
    }
    if (!empty($utm_campaign)) {
        $utm_params['utm_campaign'] = $utm_campaign;
    }

    if (empty($utm_params)) {
        wp_send_json_error(array('message' => esc_html__('Invalid UTM parameters.', 'konderntang')));
    }

    // Get effective user ID
    $user_id = konderntang_get_effective_user_id();

    // Track UTM parameters
    $success = konderntang_track_utm_parameters($user_id, $utm_params);

    if ($success) {
        wp_send_json_success(array('message' => 'UTM parameters tracked'));
    } else {
        wp_send_json_error(array('message' => 'Failed to track UTM parameters'));
    }
}
add_action('wp_ajax_konderntang_track_utm', 'konderntang_track_utm_ajax');
add_action('wp_ajax_nopriv_konderntang_track_utm', 'konderntang_track_utm_ajax');

