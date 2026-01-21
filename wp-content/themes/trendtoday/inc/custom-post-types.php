<?php
/**
 * Custom Post Types
 *
 * @package TrendToday
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Create main admin menu for Trend Today theme
 */
function trendtoday_add_admin_menu() {
    // Main menu page
    add_menu_page(
        __( 'Trend Today', 'trendtoday' ),
        __( 'Trend Today', 'trendtoday' ),
        'edit_posts',
        'trendtoday',
        'trendtoday_admin_page',
        'dashicons-admin-site-alt3',
        5
    );
    
    // Dashboard submenu (default page)
    add_submenu_page(
        'trendtoday',
        __( 'Dashboard', 'trendtoday' ),
        __( 'Dashboard', 'trendtoday' ),
        'edit_posts',
        'trendtoday',
        'trendtoday_admin_page'
    );
    
    // Theme Settings submenu
    add_submenu_page(
        'trendtoday',
        __( 'Theme Settings', 'trendtoday' ),
        __( 'Theme Settings', 'trendtoday' ),
        'manage_options',
        'trendtoday-settings',
        'trendtoday_settings_page'
    );
}
add_action( 'admin_menu', 'trendtoday_add_admin_menu', 9 );

/**
 * Admin page callback for Trend Today menu
 */
function trendtoday_admin_page() {
    // Get statistics
    $post_counts = wp_count_posts( 'post' );
    $page_counts = wp_count_posts( 'page' );
    $video_count = wp_count_posts( 'video_news' );
    $gallery_count = wp_count_posts( 'gallery' );
    $featured_count = wp_count_posts( 'featured_story' );
    $comment_count = wp_count_comments();
    $user_count = count_users();
    
    // Get image optimization stats
    $image_stats = function_exists( 'trendtoday_get_image_stats' ) ? trendtoday_get_image_stats() : array(
        'total_images' => 0,
        'total_size_mb' => 0,
        'optimized_images' => 0,
        'webp_images' => 0,
    );
    
    // Get recent posts
    $recent_posts = get_posts( array(
        'post_type' => 'post',
        'posts_per_page' => 5,
        'post_status' => 'publish',
        'orderby' => 'date',
        'order' => 'DESC',
    ) );
    
    // Get theme version
    $theme = wp_get_theme();
    $theme_version = $theme->get( 'Version' );
    
    // Get WordPress version
    global $wp_version;
    
    // Get PHP version
    $php_version = PHP_VERSION;
    
    // Get active plugins count
    $active_plugins = get_option( 'active_plugins', array() );
    $active_plugins_count = count( $active_plugins );
    
    // Get memory limit
    $memory_limit = ini_get( 'memory_limit' );
    
    // Get upload directory info
    $upload_dir = wp_upload_dir();
    $upload_size = 0;
    if ( isset( $upload_dir['basedir'] ) && is_dir( $upload_dir['basedir'] ) ) {
        $upload_size = trendtoday_get_directory_size( $upload_dir['basedir'] );
    }
    ?>
    <div class="wrap trendtoday-dashboard-wrap">
        <h1 class="trendtoday-dashboard-title">
            <span class="dashicons dashicons-admin-site-alt3"></span>
            <?php echo esc_html( get_admin_page_title() ); ?>
        </h1>
        
        <div class="trendtoday-dashboard">
            <!-- Statistics Grid -->
            <div class="trendtoday-dashboard-widgets">
                <!-- Content Statistics -->
                <div class="trendtoday-widget">
                    <div class="trendtoday-widget-header">
                        <h3>
                            <span class="dashicons dashicons-chart-bar"></span>
                            <?php _e( 'Content Statistics', 'trendtoday' ); ?>
                        </h3>
                    </div>
                    <div class="trendtoday-widget-content">
                        <div class="trendtoday-stats-grid">
                            <div class="trendtoday-stat-item">
                                <div class="stat-icon" style="background: #2271b1;">
                                    <span class="dashicons dashicons-admin-post"></span>
                                </div>
                                <div class="stat-info">
                                    <div class="stat-number"><?php echo number_format_i18n( $post_counts->publish ); ?></div>
                                    <div class="stat-label"><?php _e( 'บทความ', 'trendtoday' ); ?></div>
                                </div>
                            </div>
                            <div class="trendtoday-stat-item">
                                <div class="stat-icon" style="background: #00a32a;">
                                    <span class="dashicons dashicons-admin-page"></span>
                                </div>
                                <div class="stat-info">
                                    <div class="stat-number"><?php echo number_format_i18n( $page_counts->publish ); ?></div>
                                    <div class="stat-label"><?php _e( 'หน้า', 'trendtoday' ); ?></div>
                                </div>
                            </div>
                            <div class="trendtoday-stat-item">
                                <div class="stat-icon" style="background: #d63638;">
                                    <span class="dashicons dashicons-admin-comments"></span>
                                </div>
                                <div class="stat-info">
                                    <div class="stat-number"><?php echo number_format_i18n( $comment_count->approved ); ?></div>
                                    <div class="stat-label"><?php _e( 'ความคิดเห็น', 'trendtoday' ); ?></div>
                                </div>
                            </div>
                            <div class="trendtoday-stat-item">
                                <div class="stat-icon" style="background: #f0b849;">
                                    <span class="dashicons dashicons-admin-users"></span>
                                </div>
                                <div class="stat-info">
                                    <div class="stat-number"><?php echo number_format_i18n( $user_count['total_users'] ); ?></div>
                                    <div class="stat-label"><?php _e( 'ผู้ใช้', 'trendtoday' ); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Custom Post Types Statistics -->
                <div class="trendtoday-widget">
                    <div class="trendtoday-widget-header">
                        <h3>
                            <span class="dashicons dashicons-admin-generic"></span>
                            <?php _e( 'Custom Content', 'trendtoday' ); ?>
                        </h3>
                    </div>
                    <div class="trendtoday-widget-content">
                        <ul class="trendtoday-stats-list">
                            <li>
                                <a href="<?php echo admin_url( 'edit.php?post_type=video_news' ); ?>">
                                    <span class="dashicons dashicons-video-alt3"></span>
                                    <strong><?php _e( 'Video News:', 'trendtoday' ); ?></strong>
                                    <span class="stat-value"><?php echo number_format_i18n( $video_count->publish ); ?></span>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo admin_url( 'edit.php?post_type=gallery' ); ?>">
                                    <span class="dashicons dashicons-format-gallery"></span>
                                    <strong><?php _e( 'Photo Galleries:', 'trendtoday' ); ?></strong>
                                    <span class="stat-value"><?php echo number_format_i18n( $gallery_count->publish ); ?></span>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo admin_url( 'edit.php?post_type=featured_story' ); ?>">
                                    <span class="dashicons dashicons-star-filled"></span>
                                    <strong><?php _e( 'Featured Stories:', 'trendtoday' ); ?></strong>
                                    <span class="stat-value"><?php echo number_format_i18n( $featured_count->publish ); ?></span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <!-- Image Optimization Statistics -->
                <div class="trendtoday-widget">
                    <div class="trendtoday-widget-header">
                        <h3>
                            <span class="dashicons dashicons-images-alt2"></span>
                            <?php _e( 'Image Optimization', 'trendtoday' ); ?>
                        </h3>
                    </div>
                    <div class="trendtoday-widget-content">
                        <ul class="trendtoday-stats-list">
                            <li>
                                <span class="dashicons dashicons-format-image"></span>
                                <strong><?php _e( 'Total Images:', 'trendtoday' ); ?></strong>
                                <span class="stat-value"><?php echo number_format_i18n( $image_stats['total_images'] ); ?></span>
                            </li>
                            <li>
                                <span class="dashicons dashicons-performance"></span>
                                <strong><?php _e( 'Optimized:', 'trendtoday' ); ?></strong>
                                <span class="stat-value"><?php echo number_format_i18n( $image_stats['optimized_images'] ); ?></span>
                            </li>
                            <li>
                                <span class="dashicons dashicons-yes-alt"></span>
                                <strong><?php _e( 'WebP Images:', 'trendtoday' ); ?></strong>
                                <span class="stat-value"><?php echo number_format_i18n( $image_stats['webp_images'] ); ?></span>
                            </li>
                            <li>
                                <span class="dashicons dashicons-database"></span>
                                <strong><?php _e( 'Total Size:', 'trendtoday' ); ?></strong>
                                <span class="stat-value"><?php echo number_format( $image_stats['total_size_mb'], 2 ); ?> MB</span>
                            </li>
                        </ul>
                        <p style="margin-top: 15px;">
                            <a href="<?php echo admin_url( 'admin.php?page=trendtoday-settings#image-optimization' ); ?>" class="button button-small">
                                <?php _e( 'จัดการ Image Optimization', 'trendtoday' ); ?>
                            </a>
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Recent Posts & System Info -->
            <div class="trendtoday-dashboard-widgets">
                <!-- Recent Posts -->
                <div class="trendtoday-widget">
                    <div class="trendtoday-widget-header">
                        <h3>
                            <span class="dashicons dashicons-clock"></span>
                            <?php _e( 'Recent Posts', 'trendtoday' ); ?>
                        </h3>
                    </div>
                    <div class="trendtoday-widget-content">
                        <?php if ( ! empty( $recent_posts ) ) : ?>
                            <ul class="trendtoday-recent-posts">
                                <?php foreach ( $recent_posts as $post ) : setup_postdata( $post ); ?>
                                    <li>
                                        <a href="<?php echo get_edit_post_link( $post->ID ); ?>">
                                            <strong><?php echo esc_html( get_the_title( $post->ID ) ); ?></strong>
                                            <span class="post-date">
                                                <?php echo human_time_diff( get_the_time( 'U', $post->ID ), current_time( 'timestamp' ) ) . ' ' . __( 'ago', 'trendtoday' ); ?>
                                            </span>
                                        </a>
                                    </li>
                                <?php endforeach; wp_reset_postdata(); ?>
                            </ul>
                        <?php else : ?>
                            <p><?php _e( 'ยังไม่มีบทความ', 'trendtoday' ); ?></p>
                        <?php endif; ?>
                        <p style="margin-top: 15px;">
                            <a href="<?php echo admin_url( 'edit.php' ); ?>" class="button button-small">
                                <?php _e( 'ดูบทความทั้งหมด', 'trendtoday' ); ?>
                            </a>
                            <a href="<?php echo admin_url( 'post-new.php' ); ?>" class="button button-small">
                                <?php _e( 'เขียนบทความใหม่', 'trendtoday' ); ?>
                            </a>
                        </p>
                    </div>
                </div>
                
                <!-- System Information -->
                <div class="trendtoday-widget">
                    <div class="trendtoday-widget-header">
                        <h3>
                            <span class="dashicons dashicons-info"></span>
                            <?php _e( 'System Information', 'trendtoday' ); ?>
                        </h3>
                    </div>
                    <div class="trendtoday-widget-content">
                        <ul class="trendtoday-system-info">
                            <li>
                                <strong><?php _e( 'Theme Version:', 'trendtoday' ); ?></strong>
                                <span><?php echo esc_html( $theme_version ); ?></span>
                            </li>
                            <li>
                                <strong><?php _e( 'WordPress Version:', 'trendtoday' ); ?></strong>
                                <span><?php echo esc_html( $wp_version ); ?></span>
                            </li>
                            <li>
                                <strong><?php _e( 'PHP Version:', 'trendtoday' ); ?></strong>
                                <span><?php echo esc_html( $php_version ); ?></span>
                            </li>
                            <li>
                                <strong><?php _e( 'Memory Limit:', 'trendtoday' ); ?></strong>
                                <span><?php echo esc_html( $memory_limit ); ?></span>
                            </li>
                            <li>
                                <strong><?php _e( 'Active Plugins:', 'trendtoday' ); ?></strong>
                                <span><?php echo number_format_i18n( $active_plugins_count ); ?></span>
                            </li>
                            <li>
                                <strong><?php _e( 'Upload Directory Size:', 'trendtoday' ); ?></strong>
                                <span><?php echo size_format( $upload_size, 2 ); ?></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        .trendtoday-dashboard-wrap {
            max-width: 1400px;
        }
        .trendtoday-dashboard-title {
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .trendtoday-dashboard-title .dashicons {
            font-size: 32px;
            width: 32px;
            height: 32px;
        }
        .trendtoday-dashboard-widgets {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .trendtoday-widget {
            background: #fff;
            border: 1px solid #ccd0d4;
            box-shadow: 0 1px 1px rgba(0,0,0,.04);
            border-radius: 4px;
            overflow: hidden;
        }
        .trendtoday-widget-header {
            background: #f6f7f7;
            padding: 15px 20px;
            border-bottom: 1px solid #ccd0d4;
        }
        .trendtoday-widget-header h2,
        .trendtoday-widget-header h3 {
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 16px;
        }
        .trendtoday-widget-header .dashicons {
            font-size: 20px;
            width: 20px;
            height: 20px;
        }
        .trendtoday-widget-content {
            padding: 20px;
        }
        .trendtoday-stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }
        .trendtoday-stat-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            background: #f9f9f9;
            border-radius: 6px;
        }
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            flex-shrink: 0;
        }
        .stat-icon .dashicons {
            font-size: 24px;
            width: 24px;
            height: 24px;
        }
        .stat-info {
            flex: 1;
        }
        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #1d2327;
            line-height: 1.2;
        }
        .stat-label {
            font-size: 13px;
            color: #646970;
            margin-top: 2px;
        }
        .trendtoday-stats-list {
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .trendtoday-stats-list li {
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f1;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .trendtoday-stats-list li:last-child {
            border-bottom: none;
        }
        .trendtoday-stats-list li a {
            display: flex;
            align-items: center;
            gap: 10px;
            flex: 1;
            text-decoration: none;
            color: inherit;
        }
        .trendtoday-stats-list li a:hover {
            color: #2271b1;
        }
        .trendtoday-stats-list .dashicons {
            color: #646970;
            font-size: 18px;
            width: 18px;
            height: 18px;
        }
        .trendtoday-stats-list .stat-value {
            margin-left: auto;
            font-weight: bold;
            color: #2271b1;
        }
        .trendtoday-recent-posts {
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .trendtoday-recent-posts li {
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f1;
        }
        .trendtoday-recent-posts li:last-child {
            border-bottom: none;
        }
        .trendtoday-recent-posts li a {
            display: block;
            text-decoration: none;
            color: inherit;
        }
        .trendtoday-recent-posts li a:hover {
            color: #2271b1;
        }
        .trendtoday-recent-posts .post-date {
            display: block;
            font-size: 12px;
            color: #646970;
            margin-top: 4px;
        }
        .trendtoday-system-info {
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .trendtoday-system-info li {
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f1;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .trendtoday-system-info li:last-child {
            border-bottom: none;
        }
        .trendtoday-system-info strong {
            color: #1d2327;
        }
        .trendtoday-system-info span {
            color: #646970;
        }
    </style>
    <?php
}

/**
 * Get directory size recursively
 *
 * @param string $directory Directory path.
 * @return int Size in bytes.
 */
function trendtoday_get_directory_size( $directory ) {
    $size = 0;
    if ( is_dir( $directory ) ) {
        $files = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $directory ) );
        foreach ( $files as $file ) {
            if ( $file->isFile() ) {
                $size += $file->getSize();
            }
        }
    }
    return $size;
}

/**
 * Theme Settings page callback
 */
function trendtoday_settings_page() {
    // Save settings
    if ( isset( $_POST['trendtoday_save_settings'] ) && check_admin_referer( 'trendtoday_settings_nonce' ) ) {
        if ( current_user_can( 'manage_options' ) ) {
            // Save logo
            if ( isset( $_POST['trendtoday_logo'] ) ) {
                update_option( 'trendtoday_logo', sanitize_text_field( $_POST['trendtoday_logo'] ) );
            }
            
            // Save pagination type
            if ( isset( $_POST['trendtoday_pagination_type'] ) ) {
                $pagination_type = sanitize_text_field( $_POST['trendtoday_pagination_type'] );
                if ( in_array( $pagination_type, array( 'pagination', 'load_more' ), true ) ) {
                    update_option( 'trendtoday_pagination_type', $pagination_type );
                }
            }
            
            // Save social sharing settings
            // Enable/Disable
            $social_sharing_enabled = isset( $_POST['trendtoday_social_sharing_enabled'] ) ? '1' : '0';
            update_option( 'trendtoday_social_sharing_enabled', $social_sharing_enabled );
            
            // Selected platforms
            $available_platforms = array( 'facebook', 'twitter', 'line', 'linkedin', 'whatsapp', 'telegram', 'copy_link' );
            $selected_platforms = isset( $_POST['trendtoday_social_platforms'] ) && is_array( $_POST['trendtoday_social_platforms'] ) 
                ? array_intersect( $_POST['trendtoday_social_platforms'], $available_platforms )
                : array();
            update_option( 'trendtoday_social_platforms', $selected_platforms );
            
            // Display positions
            $display_positions = array();
            if ( isset( $_POST['trendtoday_social_display_single_top'] ) ) {
                $display_positions[] = 'single_top';
            }
            if ( isset( $_POST['trendtoday_social_display_single_bottom'] ) ) {
                $display_positions[] = 'single_bottom';
            }
            if ( isset( $_POST['trendtoday_social_display_floating'] ) ) {
                $display_positions[] = 'floating';
            }
            update_option( 'trendtoday_social_display_positions', $display_positions );
            
            // Button style
            if ( isset( $_POST['trendtoday_social_button_style'] ) ) {
                $button_style = sanitize_text_field( $_POST['trendtoday_social_button_style'] );
                if ( in_array( $button_style, array( 'icon_only', 'icon_text', 'button' ), true ) ) {
                    update_option( 'trendtoday_social_button_style', $button_style );
                }
            }
            
            // Button size
            if ( isset( $_POST['trendtoday_social_button_size'] ) ) {
                $button_size = sanitize_text_field( $_POST['trendtoday_social_button_size'] );
                if ( in_array( $button_size, array( 'small', 'medium', 'large' ), true ) ) {
                    update_option( 'trendtoday_social_button_size', $button_size );
                }
            }
            
            // Twitter handle
            if ( isset( $_POST['trendtoday_twitter_handle'] ) ) {
                $twitter_handle = sanitize_text_field( $_POST['trendtoday_twitter_handle'] );
                $twitter_handle = ltrim( $twitter_handle, '@' ); // Remove @ if present
                update_option( 'trendtoday_twitter_handle', $twitter_handle );
            }
            
            // Custom share text
            if ( isset( $_POST['trendtoday_custom_share_text'] ) ) {
                update_option( 'trendtoday_custom_share_text', sanitize_textarea_field( $_POST['trendtoday_custom_share_text'] ) );
            }
            
            // Save search settings
            // Enable/Disable
            $search_enabled = isset( $_POST['trendtoday_search_enabled'] ) ? '1' : '0';
            update_option( 'trendtoday_search_enabled', $search_enabled );
            
            $search_suggestions_enabled = isset( $_POST['trendtoday_search_suggestions_enabled'] ) ? '1' : '0';
            update_option( 'trendtoday_search_suggestions_enabled', $search_suggestions_enabled );
            
            $search_live_enabled = isset( $_POST['trendtoday_search_live_enabled'] ) ? '1' : '0';
            update_option( 'trendtoday_search_live_enabled', $search_live_enabled );
            
            // Search behavior
            if ( isset( $_POST['trendtoday_search_suggestions_count'] ) ) {
                $suggestions_count = absint( $_POST['trendtoday_search_suggestions_count'] );
                if ( $suggestions_count > 0 && $suggestions_count <= 20 ) {
                    update_option( 'trendtoday_search_suggestions_count', $suggestions_count );
                }
            }
            
            if ( isset( $_POST['trendtoday_search_debounce'] ) ) {
                $debounce = absint( $_POST['trendtoday_search_debounce'] );
                if ( $debounce >= 0 && $debounce <= 2000 ) {
                    update_option( 'trendtoday_search_debounce', $debounce );
                }
            }
            
            if ( isset( $_POST['trendtoday_search_min_length'] ) ) {
                $min_length = absint( $_POST['trendtoday_search_min_length'] );
                if ( $min_length >= 1 && $min_length <= 10 ) {
                    update_option( 'trendtoday_search_min_length', $min_length );
                }
            }
            
            // Post types to search
            $available_post_types = array( 'post', 'page', 'video_news', 'gallery', 'featured_story' );
            $search_post_types = isset( $_POST['trendtoday_search_post_types'] ) && is_array( $_POST['trendtoday_search_post_types'] ) 
                ? array_intersect( $_POST['trendtoday_search_post_types'], $available_post_types )
                : array( 'post' );
            update_option( 'trendtoday_search_post_types', $search_post_types );
            
            // Search fields
            $search_fields = array();
            if ( isset( $_POST['trendtoday_search_field_title'] ) ) {
                $search_fields[] = 'title';
            }
            if ( isset( $_POST['trendtoday_search_field_content'] ) ) {
                $search_fields[] = 'content';
            }
            if ( isset( $_POST['trendtoday_search_field_excerpt'] ) ) {
                $search_fields[] = 'excerpt';
            }
            if ( isset( $_POST['trendtoday_search_field_categories'] ) ) {
                $search_fields[] = 'categories';
            }
            if ( isset( $_POST['trendtoday_search_field_tags'] ) ) {
                $search_fields[] = 'tags';
            }
            if ( empty( $search_fields ) ) {
                $search_fields = array( 'title', 'content' ); // Default
            }
            update_option( 'trendtoday_search_fields', $search_fields );
            
            // Search display
            if ( isset( $_POST['trendtoday_search_suggestions_style'] ) ) {
                $suggestions_style = sanitize_text_field( $_POST['trendtoday_search_suggestions_style'] );
                if ( in_array( $suggestions_style, array( 'dropdown', 'modal', 'fullpage' ), true ) ) {
                    update_option( 'trendtoday_search_suggestions_style', $suggestions_style );
                }
            }
            
            $suggestions_display = array();
            if ( isset( $_POST['trendtoday_search_show_image'] ) ) {
                $suggestions_display[] = 'image';
            }
            if ( isset( $_POST['trendtoday_search_show_excerpt'] ) ) {
                $suggestions_display[] = 'excerpt';
            }
            
            // Save widget visibility settings
            $available_widgets = array( 'popular_posts', 'recent_posts', 'trending_tags' );
            if ( isset( $_POST['trendtoday_enabled_widgets'] ) && is_array( $_POST['trendtoday_enabled_widgets'] ) ) {
                $enabled_widgets = array_intersect( $_POST['trendtoday_enabled_widgets'], $available_widgets );
            } else {
                // If no checkboxes are selected, save empty array
                $enabled_widgets = array();
            }
            update_option( 'trendtoday_enabled_widgets', $enabled_widgets );
            if ( isset( $_POST['trendtoday_search_show_date'] ) ) {
                $suggestions_display[] = 'date';
            }
            if ( isset( $_POST['trendtoday_search_show_category'] ) ) {
                $suggestions_display[] = 'category';
            }
            update_option( 'trendtoday_search_suggestions_display', $suggestions_display );
            
            // Search results page
            if ( isset( $_POST['trendtoday_search_results_layout'] ) ) {
                $results_layout = sanitize_text_field( $_POST['trendtoday_search_results_layout'] );
                if ( in_array( $results_layout, array( 'list', 'grid', 'mixed' ), true ) ) {
                    update_option( 'trendtoday_search_results_layout', $results_layout );
                }
            }
            
            if ( isset( $_POST['trendtoday_search_results_sort'] ) ) {
                $results_sort = sanitize_text_field( $_POST['trendtoday_search_results_sort'] );
                if ( in_array( $results_sort, array( 'relevance', 'date_desc', 'date_asc', 'title_asc', 'title_desc' ), true ) ) {
                    update_option( 'trendtoday_search_results_sort', $results_sort );
                }
            }
            
            // Search placeholder
            if ( isset( $_POST['trendtoday_search_placeholder'] ) ) {
                update_option( 'trendtoday_search_placeholder', sanitize_text_field( $_POST['trendtoday_search_placeholder'] ) );
            }
            
            // Exclude categories
            $exclude_categories = isset( $_POST['trendtoday_search_exclude_categories'] ) && is_array( $_POST['trendtoday_search_exclude_categories'] ) 
                ? array_map( 'absint', $_POST['trendtoday_search_exclude_categories'] )
                : array();
            update_option( 'trendtoday_search_exclude_categories', $exclude_categories );
            
            // Save TOC settings
            // Enable/Disable
            $toc_enabled = isset( $_POST['trendtoday_toc_enabled'] ) ? '1' : '0';
            update_option( 'trendtoday_toc_enabled', $toc_enabled );
            
            $toc_mobile_enabled = isset( $_POST['trendtoday_toc_mobile_enabled'] ) ? '1' : '0';
            update_option( 'trendtoday_toc_mobile_enabled', $toc_mobile_enabled );
            
            // Position
            if ( isset( $_POST['trendtoday_toc_position'] ) ) {
                $toc_position = sanitize_text_field( $_POST['trendtoday_toc_position'] );
                if ( in_array( $toc_position, array( 'top', 'sidebar', 'floating' ), true ) ) {
                    update_option( 'trendtoday_toc_position', $toc_position );
                }
            }
            
            // Mobile position
            if ( isset( $_POST['trendtoday_toc_mobile_position'] ) ) {
                $toc_mobile_position = sanitize_text_field( $_POST['trendtoday_toc_mobile_position'] );
                if ( in_array( $toc_mobile_position, array( 'top', 'bottom', 'floating', 'collapsible' ), true ) ) {
                    update_option( 'trendtoday_toc_mobile_position', $toc_mobile_position );
                }
            }
            
            // Heading levels
            $toc_headings = array();
            if ( isset( $_POST['trendtoday_toc_heading_h2'] ) ) {
                $toc_headings[] = 'h2';
            }
            if ( isset( $_POST['trendtoday_toc_heading_h3'] ) ) {
                $toc_headings[] = 'h3';
            }
            if ( isset( $_POST['trendtoday_toc_heading_h4'] ) ) {
                $toc_headings[] = 'h4';
            }
            if ( isset( $_POST['trendtoday_toc_heading_h5'] ) ) {
                $toc_headings[] = 'h5';
            }
            if ( isset( $_POST['trendtoday_toc_heading_h6'] ) ) {
                $toc_headings[] = 'h6';
            }
            if ( empty( $toc_headings ) ) {
                $toc_headings = array( 'h2', 'h3', 'h4' ); // Default
            }
            update_option( 'trendtoday_toc_headings', $toc_headings );
            
            // Style
            if ( isset( $_POST['trendtoday_toc_style'] ) ) {
                $toc_style = sanitize_text_field( $_POST['trendtoday_toc_style'] );
                if ( in_array( $toc_style, array( 'simple', 'numbered', 'nested' ), true ) ) {
                    update_option( 'trendtoday_toc_style', $toc_style );
                }
            }
            
            // Features
            $toc_smooth_scroll = isset( $_POST['trendtoday_toc_smooth_scroll'] ) ? '1' : '0';
            update_option( 'trendtoday_toc_smooth_scroll', $toc_smooth_scroll );
            
            $toc_scroll_spy = isset( $_POST['trendtoday_toc_scroll_spy'] ) ? '1' : '0';
            update_option( 'trendtoday_toc_scroll_spy', $toc_scroll_spy );
            
            $toc_collapsible = isset( $_POST['trendtoday_toc_collapsible'] ) ? '1' : '0';
            update_option( 'trendtoday_toc_collapsible', $toc_collapsible );
            
            $toc_sticky = isset( $_POST['trendtoday_toc_sticky'] ) ? '1' : '0';
            update_option( 'trendtoday_toc_sticky', $toc_sticky );
            
            $toc_auto_collapse_mobile = isset( $_POST['trendtoday_toc_auto_collapse_mobile'] ) ? '1' : '0';
            update_option( 'trendtoday_toc_auto_collapse_mobile', $toc_auto_collapse_mobile );
            
            // Minimum headings count
            if ( isset( $_POST['trendtoday_toc_min_headings'] ) ) {
                $min_headings = absint( $_POST['trendtoday_toc_min_headings'] );
                if ( $min_headings >= 0 && $min_headings <= 20 ) {
                    update_option( 'trendtoday_toc_min_headings', $min_headings );
                }
            }
            
            // Custom title
            if ( isset( $_POST['trendtoday_toc_title'] ) ) {
                update_option( 'trendtoday_toc_title', sanitize_text_field( $_POST['trendtoday_toc_title'] ) );
            }
            
            // Save image optimization settings
            // Auto resize
            $image_auto_resize = isset( $_POST['trendtoday_image_auto_resize'] ) ? '1' : '0';
            update_option( 'trendtoday_image_auto_resize', $image_auto_resize );
            
            // Max dimensions
            if ( isset( $_POST['trendtoday_image_max_width'] ) ) {
                $max_width = absint( $_POST['trendtoday_image_max_width'] );
                if ( $max_width > 0 && $max_width <= 10000 ) {
                    update_option( 'trendtoday_image_max_width', $max_width );
                }
            }
            
            if ( isset( $_POST['trendtoday_image_max_height'] ) ) {
                $max_height = absint( $_POST['trendtoday_image_max_height'] );
                if ( $max_height > 0 && $max_height <= 10000 ) {
                    update_option( 'trendtoday_image_max_height', $max_height );
                }
            }
            
            // Maintain aspect ratio
            $maintain_aspect = isset( $_POST['trendtoday_image_maintain_aspect'] ) ? '1' : '0';
            update_option( 'trendtoday_image_maintain_aspect', $maintain_aspect );
            
            // JPEG Quality
            if ( isset( $_POST['trendtoday_image_jpeg_quality'] ) ) {
                $jpeg_quality = absint( $_POST['trendtoday_image_jpeg_quality'] );
                if ( $jpeg_quality >= 0 && $jpeg_quality <= 100 ) {
                    update_option( 'trendtoday_image_jpeg_quality', $jpeg_quality );
                    // Update WordPress default quality
                    add_filter( 'jpeg_quality', function() use ( $jpeg_quality ) {
                        return $jpeg_quality;
                    }, 10 );
                }
            }
            
            // Max file size before resize
            if ( isset( $_POST['trendtoday_image_max_file_size'] ) ) {
                $max_file_size = absint( $_POST['trendtoday_image_max_file_size'] );
                if ( $max_file_size >= 0 && $max_file_size <= 100 ) {
                    update_option( 'trendtoday_image_max_file_size', $max_file_size );
                }
            }
            
            // WebP settings
            $image_webp_enabled = isset( $_POST['trendtoday_image_webp_enabled'] ) ? '1' : '0';
            update_option( 'trendtoday_image_webp_enabled', $image_webp_enabled );
            
            if ( isset( $_POST['trendtoday_image_webp_quality'] ) ) {
                $webp_quality = absint( $_POST['trendtoday_image_webp_quality'] );
                if ( $webp_quality >= 0 && $webp_quality <= 100 ) {
                    update_option( 'trendtoday_image_webp_quality', $webp_quality );
                }
            }
            
            // Strip EXIF
            $strip_exif = isset( $_POST['trendtoday_image_strip_exif'] ) ? '1' : '0';
            update_option( 'trendtoday_image_strip_exif', $strip_exif );
            
            echo '<div class="notice notice-success is-dismissible"><p>' . __( 'Settings saved successfully!', 'trendtoday' ) . '</p></div>';
        }
    }
    
    // Get active tab from POST, GET parameter, or default to general
    // JavaScript will handle hash-based navigation, but we can also check for tab parameter
    $active_tab = 'general';
    if ( isset( $_POST['trendtoday_active_tab'] ) ) {
        $active_tab = sanitize_text_field( $_POST['trendtoday_active_tab'] );
    } elseif ( isset( $_GET['tab'] ) ) {
        $active_tab = sanitize_text_field( $_GET['tab'] );
    }
    
    if ( ! in_array( $active_tab, array( 'general', 'social-sharing', 'search', 'toc', 'image-optimization', 'widgets' ), true ) ) {
        $active_tab = 'general';
    }
    
    $logo_id = get_option( 'trendtoday_logo', '' );
    $logo_url = $logo_id ? wp_get_attachment_image_url( $logo_id, 'full' ) : '';
    $pagination_type = get_option( 'trendtoday_pagination_type', 'load_more' ); // Default to load_more
    
    // Get social sharing settings
    $social_sharing_enabled = get_option( 'trendtoday_social_sharing_enabled', '1' );
    $selected_platforms = get_option( 'trendtoday_social_platforms', array( 'facebook', 'twitter', 'line' ) );
    $display_positions = get_option( 'trendtoday_social_display_positions', array( 'single_bottom' ) );
    $button_style = get_option( 'trendtoday_social_button_style', 'icon_only' );
    $button_size = get_option( 'trendtoday_social_button_size', 'medium' );
    $twitter_handle = get_option( 'trendtoday_twitter_handle', '' );
    $custom_share_text = get_option( 'trendtoday_custom_share_text', '' );
    
    $available_platforms = array(
        'facebook' => __( 'Facebook', 'trendtoday' ),
        'twitter' => __( 'Twitter/X', 'trendtoday' ),
        'line' => __( 'Line', 'trendtoday' ),
        'linkedin' => __( 'LinkedIn', 'trendtoday' ),
        'whatsapp' => __( 'WhatsApp', 'trendtoday' ),
        'telegram' => __( 'Telegram', 'trendtoday' ),
        'copy_link' => __( 'Copy Link', 'trendtoday' ),
    );
    
    // Get search settings
    $search_enabled = get_option( 'trendtoday_search_enabled', '1' );
    $search_suggestions_enabled = get_option( 'trendtoday_search_suggestions_enabled', '1' );
    $search_live_enabled = get_option( 'trendtoday_search_live_enabled', '1' );
    $search_suggestions_count = get_option( 'trendtoday_search_suggestions_count', 5 );
    $search_debounce = get_option( 'trendtoday_search_debounce', 300 );
    $search_min_length = get_option( 'trendtoday_search_min_length', 2 );
    $search_post_types = get_option( 'trendtoday_search_post_types', array( 'post' ) );
    $search_fields = get_option( 'trendtoday_search_fields', array( 'title', 'content' ) );
    $search_suggestions_style = get_option( 'trendtoday_search_suggestions_style', 'dropdown' );
    $search_suggestions_display = get_option( 'trendtoday_search_suggestions_display', array( 'image', 'excerpt' ) );
    $search_results_layout = get_option( 'trendtoday_search_results_layout', 'list' );
    $search_results_sort = get_option( 'trendtoday_search_results_sort', 'relevance' );
    $search_placeholder = get_option( 'trendtoday_search_placeholder', __( 'พิมพ์คำค้นหา...', 'trendtoday' ) );
    $search_exclude_categories = get_option( 'trendtoday_search_exclude_categories', array() );
    
    $available_post_types = array(
        'post' => __( 'Posts', 'trendtoday' ),
        'page' => __( 'Pages', 'trendtoday' ),
        'video_news' => __( 'Video News', 'trendtoday' ),
        'gallery' => __( 'Gallery', 'trendtoday' ),
        'featured_story' => __( 'Featured Stories', 'trendtoday' ),
    );
    
    // Get all categories for exclude list
    $all_categories = get_categories( array( 'hide_empty' => false ) );
    
    // Get TOC settings
    $toc_enabled = get_option( 'trendtoday_toc_enabled', '1' );
    $toc_mobile_enabled = get_option( 'trendtoday_toc_mobile_enabled', '1' );
    $toc_position = get_option( 'trendtoday_toc_position', 'top' );
    $toc_mobile_position = get_option( 'trendtoday_toc_mobile_position', 'floating' );
    $toc_headings = get_option( 'trendtoday_toc_headings', array( 'h2', 'h3', 'h4' ) );
    $toc_style = get_option( 'trendtoday_toc_style', 'nested' );
    $toc_smooth_scroll = get_option( 'trendtoday_toc_smooth_scroll', '1' );
    $toc_scroll_spy = get_option( 'trendtoday_toc_scroll_spy', '1' );
    $toc_collapsible = get_option( 'trendtoday_toc_collapsible', '1' );
    $toc_sticky = get_option( 'trendtoday_toc_sticky', '0' );
    $toc_auto_collapse_mobile = get_option( 'trendtoday_toc_auto_collapse_mobile', '1' );
    $toc_min_headings = get_option( 'trendtoday_toc_min_headings', 2 );
    $toc_title = get_option( 'trendtoday_toc_title', __( 'สารบัญ', 'trendtoday' ) );
    
    // Get Image Optimization settings
    $image_auto_resize = get_option( 'trendtoday_image_auto_resize', '1' );
    $image_max_width = get_option( 'trendtoday_image_max_width', 1920 );
    $image_max_height = get_option( 'trendtoday_image_max_height', 1080 );
    $image_maintain_aspect = get_option( 'trendtoday_image_maintain_aspect', '1' );
    $image_jpeg_quality = get_option( 'trendtoday_image_jpeg_quality', 85 );
    $image_max_file_size = get_option( 'trendtoday_image_max_file_size', 0 );
    $image_webp_enabled = get_option( 'trendtoday_image_webp_enabled', '1' );
    $image_webp_quality = get_option( 'trendtoday_image_webp_quality', 85 );
    $image_strip_exif = get_option( 'trendtoday_image_strip_exif', '1' );
    
    // Get Widget visibility settings
    $available_widgets = array(
        'popular_posts' => __( 'Popular Posts Widget', 'trendtoday' ),
        'recent_posts' => __( 'Recent Posts Widget', 'trendtoday' ),
        'trending_tags' => __( 'Trending Tags Widget', 'trendtoday' ),
    );
    // Get enabled widgets - default to all enabled only if option doesn't exist
    $saved_widgets = get_option( 'trendtoday_enabled_widgets' );
    if ( $saved_widgets === false ) {
        // First time - default to all enabled
        $enabled_widgets = array_keys( $available_widgets );
    } else {
        // Use saved value (can be empty array if all unchecked)
        $enabled_widgets = is_array( $saved_widgets ) ? $saved_widgets : array();
    }
    
    // Get image statistics
    if ( function_exists( 'trendtoday_get_image_stats' ) ) {
        $image_stats = trendtoday_get_image_stats();
    } else {
        $image_stats = array(
            'total_images' => 0,
            'total_size_mb' => 0,
            'webp_images' => 0,
        );
    }
    
    // Check WebP support
    $webp_supported = function_exists( 'trendtoday_webp_supported' ) ? trendtoday_webp_supported() : false;
    ?>
    <div class="wrap trendtoday-settings-wrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
        
        <form method="post" action="" id="trendtoday-settings-form">
            <?php wp_nonce_field( 'trendtoday_settings_nonce' ); ?>
            
            <!-- Hidden input to store active tab -->
            <input type="hidden" name="trendtoday_active_tab" id="trendtoday_active_tab" value="<?php echo esc_attr( isset( $_POST['trendtoday_active_tab'] ) ? sanitize_text_field( $_POST['trendtoday_active_tab'] ) : 'general' ); ?>" />
            
            <!-- Tabs Navigation -->
            <nav class="nav-tab-wrapper trendtoday-nav-tabs">
                <a href="#general" class="nav-tab <?php echo $active_tab === 'general' ? 'nav-tab-active' : ''; ?>" data-tab="general">
                    <span class="dashicons dashicons-admin-settings"></span> <?php _e( 'General', 'trendtoday' ); ?>
                </a>
                <a href="#social-sharing" class="nav-tab <?php echo $active_tab === 'social-sharing' ? 'nav-tab-active' : ''; ?>" data-tab="social-sharing">
                    <span class="dashicons dashicons-share"></span> <?php _e( 'Social Sharing', 'trendtoday' ); ?>
                </a>
                <a href="#search" class="nav-tab <?php echo $active_tab === 'search' ? 'nav-tab-active' : ''; ?>" data-tab="search">
                    <span class="dashicons dashicons-search"></span> <?php _e( 'Search', 'trendtoday' ); ?>
                </a>
                <a href="#toc" class="nav-tab <?php echo $active_tab === 'toc' ? 'nav-tab-active' : ''; ?>" data-tab="toc">
                    <span class="dashicons dashicons-list-view"></span> <?php _e( 'Table of Contents', 'trendtoday' ); ?>
                </a>
                <a href="#image-optimization" class="nav-tab <?php echo $active_tab === 'image-optimization' ? 'nav-tab-active' : ''; ?>" data-tab="image-optimization">
                    <span class="dashicons dashicons-images-alt2"></span> <?php _e( 'Image Optimization', 'trendtoday' ); ?>
                </a>
                <a href="#widgets" class="nav-tab <?php echo $active_tab === 'widgets' ? 'nav-tab-active' : ''; ?>" data-tab="widgets">
                    <span class="dashicons dashicons-welcome-widgets-menus"></span> <?php _e( 'Widgets', 'trendtoday' ); ?>
                </a>
            </nav>
            
            <!-- General Settings Tab -->
            <div id="general-tab" class="trendtoday-tab-content <?php echo $active_tab === 'general' ? 'active' : ''; ?>">
                <div class="trendtoday-settings-section">
                    <h2 class="trendtoday-section-title">
                        <span class="dashicons dashicons-admin-customizer"></span>
                        <?php _e( 'General Settings', 'trendtoday' ); ?>
                    </h2>
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="trendtoday_logo"><?php _e( 'Website Logo', 'trendtoday' ); ?></label>
                            </th>
                            <td>
                                <div class="trendtoday-logo-upload">
                                    <input type="hidden" id="trendtoday_logo" name="trendtoday_logo" value="<?php echo esc_attr( $logo_id ); ?>" />
                                    <div id="trendtoday_logo_preview" class="trendtoday-logo-preview">
                                        <?php if ( $logo_url ) : ?>
                                            <img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php _e( 'Logo Preview', 'trendtoday' ); ?>" />
                                        <?php else : ?>
                                            <div class="trendtoday-logo-placeholder">
                                                <span class="dashicons dashicons-format-image"></span>
                                                <p><?php _e( 'No logo uploaded', 'trendtoday' ); ?></p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="trendtoday-logo-actions">
                                        <button type="button" class="button button-primary" id="trendtoday_upload_logo_btn">
                                            <span class="dashicons dashicons-upload"></span>
                                            <?php echo $logo_id ? __( 'Change Logo', 'trendtoday' ) : __( 'Upload Logo', 'trendtoday' ); ?>
                                        </button>
                                        <?php if ( $logo_id ) : ?>
                                            <button type="button" class="button" id="trendtoday_remove_logo_btn">
                                                <span class="dashicons dashicons-trash"></span>
                                                <?php _e( 'Remove', 'trendtoday' ); ?>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                    <p class="description">
                                        <?php _e( 'Upload a logo for your website. Recommended size: 200x60 pixels or larger.', 'trendtoday' ); ?>
                                    </p>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="trendtoday_pagination_type"><?php _e( 'Pagination Type', 'trendtoday' ); ?></label>
                            </th>
                            <td>
                                <fieldset class="trendtoday-radio-group">
                                    <label class="trendtoday-radio-option">
                                        <input type="radio" name="trendtoday_pagination_type" value="pagination" <?php checked( $pagination_type, 'pagination' ); ?> />
                                        <span class="radio-label">
                                            <strong><?php _e( 'Pagination', 'trendtoday' ); ?></strong>
                                            <small><?php _e( 'แสดงหมายเลขหน้า', 'trendtoday' ); ?></small>
                                        </span>
                                    </label>
                                    <label class="trendtoday-radio-option">
                                        <input type="radio" name="trendtoday_pagination_type" value="load_more" <?php checked( $pagination_type, 'load_more' ); ?> />
                                        <span class="radio-label">
                                            <strong><?php _e( 'Load More', 'trendtoday' ); ?></strong>
                                            <small><?php _e( 'โหลดข่าวเพิ่มเติม', 'trendtoday' ); ?></small>
                                        </span>
                                    </label>
                                </fieldset>
                                <p class="description">
                                    <?php _e( 'เลือกวิธีการแสดงผลบทความในหน้าแรก: แสดง pagination หรือปุ่มโหลดข่าวเพิ่มเติม', 'trendtoday' ); ?>
                                </p>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <!-- Social Sharing Settings Tab -->
            <div id="social-sharing-tab" class="trendtoday-tab-content <?php echo $active_tab === 'social-sharing' ? 'active' : ''; ?>">
                <div class="trendtoday-settings-section">
                    <h2 class="trendtoday-section-title">
                        <span class="dashicons dashicons-share"></span>
                        <?php _e( 'Social Sharing Settings', 'trendtoday' ); ?>
                    </h2>
                    <p class="trendtoday-section-description">
                        <?php _e( 'ตั้งค่าการแชร์เนื้อหาไปยัง Social Media platforms ต่างๆ', 'trendtoday' ); ?>
                    </p>
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <?php _e( 'Enable Social Sharing', 'trendtoday' ); ?>
                            </th>
                            <td>
                                <label class="trendtoday-toggle">
                                    <input type="checkbox" name="trendtoday_social_sharing_enabled" value="1" <?php checked( $social_sharing_enabled, '1' ); ?> />
                                    <span class="toggle-slider"></span>
                                    <span class="toggle-label"><?php _e( 'เปิดใช้งานการแชร์เนื้อหาไปยัง Social Media', 'trendtoday' ); ?></span>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label><?php _e( 'Social Platforms', 'trendtoday' ); ?></label>
                            </th>
                            <td>
                                <div class="trendtoday-platforms-grid">
                                    <?php 
                                    $platform_icons = array(
                                        'facebook' => 'fab fa-facebook-f',
                                        'twitter' => 'fab fa-twitter',
                                        'line' => 'fab fa-line',
                                        'linkedin' => 'fab fa-linkedin-in',
                                        'whatsapp' => 'fab fa-whatsapp',
                                        'telegram' => 'fab fa-telegram-plane',
                                        'copy_link' => 'fas fa-link',
                                    );
                                    foreach ( $available_platforms as $platform_key => $platform_name ) : 
                                        $icon = isset( $platform_icons[ $platform_key ] ) ? $platform_icons[ $platform_key ] : 'fas fa-share-alt';
                                    ?>
                                        <label class="trendtoday-platform-item">
                                            <input type="checkbox" name="trendtoday_social_platforms[]" value="<?php echo esc_attr( $platform_key ); ?>" 
                                                   <?php checked( in_array( $platform_key, $selected_platforms ), true ); ?> />
                                            <span class="platform-icon">
                                                <i class="<?php echo esc_attr( $icon ); ?>"></i>
                                            </span>
                                            <span class="platform-name"><?php echo esc_html( $platform_name ); ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                                <p class="description">
                                    <?php _e( 'เลือก Social Media platforms ที่ต้องการแสดงปุ่มแชร์', 'trendtoday' ); ?>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label><?php _e( 'Display Positions', 'trendtoday' ); ?></label>
                            </th>
                            <td>
                                <div class="trendtoday-positions-grid">
                                    <label class="trendtoday-position-item">
                                        <input type="checkbox" name="trendtoday_social_display_single_top" value="1" 
                                               <?php checked( in_array( 'single_top', $display_positions ), true ); ?> />
                                        <span class="position-icon"><span class="dashicons dashicons-arrow-up-alt"></span></span>
                                        <span class="position-label">
                                            <strong><?php _e( 'ด้านบนบทความ', 'trendtoday' ); ?></strong>
                                            <small><?php _e( 'Single Post', 'trendtoday' ); ?></small>
                                        </span>
                                    </label>
                                    <label class="trendtoday-position-item">
                                        <input type="checkbox" name="trendtoday_social_display_single_bottom" value="1" 
                                               <?php checked( in_array( 'single_bottom', $display_positions ), true ); ?> />
                                        <span class="position-icon"><span class="dashicons dashicons-arrow-down-alt"></span></span>
                                        <span class="position-label">
                                            <strong><?php _e( 'ด้านล่างบทความ', 'trendtoday' ); ?></strong>
                                            <small><?php _e( 'Single Post', 'trendtoday' ); ?></small>
                                        </span>
                                    </label>
                                    <label class="trendtoday-position-item">
                                        <input type="checkbox" name="trendtoday_social_display_floating" value="1" 
                                               <?php checked( in_array( 'floating', $display_positions ), true ); ?> />
                                        <span class="position-icon"><span class="dashicons dashicons-admin-generic"></span></span>
                                        <span class="position-label">
                                            <strong><?php _e( 'Floating Buttons', 'trendtoday' ); ?></strong>
                                            <small><?php _e( 'ด้านข้างหน้าจอ', 'trendtoday' ); ?></small>
                                        </span>
                                    </label>
                                </div>
                                <p class="description">
                                    <?php _e( 'เลือกตำแหน่งที่ต้องการแสดงปุ่มแชร์', 'trendtoday' ); ?>
                                </p>
                            </td>
                        </tr>
                <tr>
                    <th scope="row">
                        <label for="trendtoday_social_button_style"><?php _e( 'Button Style', 'trendtoday' ); ?></label>
                    </th>
                    <td>
                        <select name="trendtoday_social_button_style" id="trendtoday_social_button_style">
                            <option value="icon_only" <?php selected( $button_style, 'icon_only' ); ?>>
                                <?php _e( 'Icon Only (ไอคอนเท่านั้น)', 'trendtoday' ); ?>
                            </option>
                            <option value="icon_text" <?php selected( $button_style, 'icon_text' ); ?>>
                                <?php _e( 'Icon + Text (ไอคอนและข้อความ)', 'trendtoday' ); ?>
                            </option>
                            <option value="button" <?php selected( $button_style, 'button' ); ?>>
                                <?php _e( 'Button Style (สไตล์ปุ่ม)', 'trendtoday' ); ?>
                            </option>
                        </select>
                        <p class="description">
                            <?php _e( 'เลือกรูปแบบการแสดงผลปุ่มแชร์', 'trendtoday' ); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="trendtoday_social_button_size"><?php _e( 'Button Size', 'trendtoday' ); ?></label>
                    </th>
                    <td>
                        <select name="trendtoday_social_button_size" id="trendtoday_social_button_size">
                            <option value="small" <?php selected( $button_size, 'small' ); ?>>
                                <?php _e( 'Small (เล็ก)', 'trendtoday' ); ?>
                            </option>
                            <option value="medium" <?php selected( $button_size, 'medium' ); ?>>
                                <?php _e( 'Medium (กลาง)', 'trendtoday' ); ?>
                            </option>
                            <option value="large" <?php selected( $button_size, 'large' ); ?>>
                                <?php _e( 'Large (ใหญ่)', 'trendtoday' ); ?>
                            </option>
                        </select>
                        <p class="description">
                            <?php _e( 'เลือกขนาดปุ่มแชร์', 'trendtoday' ); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="trendtoday_twitter_handle"><?php _e( 'Twitter Handle', 'trendtoday' ); ?></label>
                    </th>
                    <td>
                        <input type="text" name="trendtoday_twitter_handle" id="trendtoday_twitter_handle" 
                               value="<?php echo esc_attr( $twitter_handle ); ?>" 
                               placeholder="@username" class="regular-text" />
                        <p class="description">
                            <?php _e( 'ใส่ Twitter/X username (ไม่ต้องใส่ @) เพื่อเพิ่มใน tweet', 'trendtoday' ); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="trendtoday_custom_share_text"><?php _e( 'Custom Share Text', 'trendtoday' ); ?></label>
                    </th>
                    <td>
                        <textarea name="trendtoday_custom_share_text" id="trendtoday_custom_share_text" 
                                  rows="3" class="large-text" 
                                  placeholder="<?php esc_attr_e( 'ข้อความที่ต้องการให้แสดงเมื่อแชร์ (ใช้ {title} สำหรับชื่อบทความ, {url} สำหรับลิงก์)', 'trendtoday' ); ?>"><?php echo esc_textarea( $custom_share_text ); ?></textarea>
                        <p class="description">
                            <?php _e( 'ข้อความที่ต้องการให้แสดงเมื่อแชร์ (ใช้ {title} สำหรับชื่อบทความ, {url} สำหรับลิงก์)', 'trendtoday' ); ?>
                        </p>
                    </td>
                </tr>
                    </table>
                </div>
            </div>
            
            <!-- Search Settings Tab -->
            <div id="search-tab" class="trendtoday-tab-content <?php echo $active_tab === 'search' ? 'active' : ''; ?>">
                <div class="trendtoday-settings-section">
                    <h2 class="trendtoday-section-title">
                        <span class="dashicons dashicons-search"></span>
                        <?php _e( 'Search Settings', 'trendtoday' ); ?>
                    </h2>
                    <p class="trendtoday-section-description">
                        <?php _e( 'ตั้งค่าการค้นหาและ Search Suggestions', 'trendtoday' ); ?>
                    </p>
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <?php _e( 'Enable Search', 'trendtoday' ); ?>
                            </th>
                            <td>
                                <label class="trendtoday-toggle">
                                    <input type="checkbox" name="trendtoday_search_enabled" value="1" <?php checked( $search_enabled, '1' ); ?> />
                                    <span class="toggle-slider"></span>
                                    <span class="toggle-label"><?php _e( 'เปิดใช้งานการค้นหา', 'trendtoday' ); ?></span>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <?php _e( 'Search Suggestions', 'trendtoday' ); ?>
                            </th>
                            <td>
                                <label class="trendtoday-toggle">
                                    <input type="checkbox" name="trendtoday_search_suggestions_enabled" value="1" <?php checked( $search_suggestions_enabled, '1' ); ?> />
                                    <span class="toggle-slider"></span>
                                    <span class="toggle-label"><?php _e( 'เปิดใช้งาน Search Suggestions (Autocomplete)', 'trendtoday' ); ?></span>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <?php _e( 'Live Search', 'trendtoday' ); ?>
                            </th>
                            <td>
                                <label class="trendtoday-toggle">
                                    <input type="checkbox" name="trendtoday_search_live_enabled" value="1" <?php checked( $search_live_enabled, '1' ); ?> />
                                    <span class="toggle-slider"></span>
                                    <span class="toggle-label"><?php _e( 'เปิดใช้งาน Live Search (ค้นหาขณะพิมพ์)', 'trendtoday' ); ?></span>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="trendtoday_search_suggestions_count"><?php _e( 'Suggestions Count', 'trendtoday' ); ?></label>
                            </th>
                            <td>
                                <input type="number" name="trendtoday_search_suggestions_count" id="trendtoday_search_suggestions_count" 
                                       value="<?php echo esc_attr( $search_suggestions_count ); ?>" 
                                       min="1" max="20" class="small-text" />
                                <p class="description">
                                    <?php _e( 'จำนวน Suggestions ที่แสดง (1-20)', 'trendtoday' ); ?>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="trendtoday_search_debounce"><?php _e( 'Debounce Delay (ms)', 'trendtoday' ); ?></label>
                            </th>
                            <td>
                                <input type="number" name="trendtoday_search_debounce" id="trendtoday_search_debounce" 
                                       value="<?php echo esc_attr( $search_debounce ); ?>" 
                                       min="0" max="2000" step="50" class="small-text" />
                                <p class="description">
                                    <?php _e( 'ระยะเวลาที่รอก่อนค้นหา (0-2000ms, แนะนำ 300ms)', 'trendtoday' ); ?>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="trendtoday_search_min_length"><?php _e( 'Minimum Length', 'trendtoday' ); ?></label>
                            </th>
                            <td>
                                <input type="number" name="trendtoday_search_min_length" id="trendtoday_search_min_length" 
                                       value="<?php echo esc_attr( $search_min_length ); ?>" 
                                       min="1" max="10" class="small-text" />
                                <p class="description">
                                    <?php _e( 'ความยาวขั้นต่ำของคำค้นหา (1-10 ตัวอักษร)', 'trendtoday' ); ?>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label><?php _e( 'Post Types to Search', 'trendtoday' ); ?></label>
                            </th>
                            <td>
                                <div class="trendtoday-platforms-grid">
                                    <?php foreach ( $available_post_types as $post_type_key => $post_type_name ) : ?>
                                        <label class="trendtoday-platform-item">
                                            <input type="checkbox" name="trendtoday_search_post_types[]" value="<?php echo esc_attr( $post_type_key ); ?>" 
                                                   <?php checked( in_array( $post_type_key, $search_post_types ), true ); ?> />
                                            <span class="platform-icon">
                                                <i class="fas fa-file-alt"></i>
                                            </span>
                                            <span class="platform-name"><?php echo esc_html( $post_type_name ); ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                                <p class="description">
                                    <?php _e( 'เลือก Post Types ที่ต้องการให้ค้นหา', 'trendtoday' ); ?>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label><?php _e( 'Search Fields', 'trendtoday' ); ?></label>
                            </th>
                            <td>
                                <fieldset>
                                    <label style="display: block; margin-bottom: 8px;">
                                        <input type="checkbox" name="trendtoday_search_field_title" value="1" 
                                               <?php checked( in_array( 'title', $search_fields ), true ); ?> />
                                        <?php _e( 'Title (ชื่อบทความ)', 'trendtoday' ); ?>
                                    </label>
                                    <label style="display: block; margin-bottom: 8px;">
                                        <input type="checkbox" name="trendtoday_search_field_content" value="1" 
                                               <?php checked( in_array( 'content', $search_fields ), true ); ?> />
                                        <?php _e( 'Content (เนื้อหา)', 'trendtoday' ); ?>
                                    </label>
                                    <label style="display: block; margin-bottom: 8px;">
                                        <input type="checkbox" name="trendtoday_search_field_excerpt" value="1" 
                                               <?php checked( in_array( 'excerpt', $search_fields ), true ); ?> />
                                        <?php _e( 'Excerpt (คำอธิบาย)', 'trendtoday' ); ?>
                                    </label>
                                    <label style="display: block; margin-bottom: 8px;">
                                        <input type="checkbox" name="trendtoday_search_field_categories" value="1" 
                                               <?php checked( in_array( 'categories', $search_fields ), true ); ?> />
                                        <?php _e( 'Categories (หมวดหมู่)', 'trendtoday' ); ?>
                                    </label>
                                    <label style="display: block; margin-bottom: 8px;">
                                        <input type="checkbox" name="trendtoday_search_field_tags" value="1" 
                                               <?php checked( in_array( 'tags', $search_fields ), true ); ?> />
                                        <?php _e( 'Tags (ป้ายกำกับ)', 'trendtoday' ); ?>
                                    </label>
                                </fieldset>
                                <p class="description">
                                    <?php _e( 'เลือก Fields ที่ต้องการให้ค้นหา', 'trendtoday' ); ?>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="trendtoday_search_suggestions_style"><?php _e( 'Suggestions Style', 'trendtoday' ); ?></label>
                            </th>
                            <td>
                                <select name="trendtoday_search_suggestions_style" id="trendtoday_search_suggestions_style">
                                    <option value="dropdown" <?php selected( $search_suggestions_style, 'dropdown' ); ?>>
                                        <?php _e( 'Dropdown (ใต้ search box)', 'trendtoday' ); ?>
                                    </option>
                                    <option value="modal" <?php selected( $search_suggestions_style, 'modal' ); ?>>
                                        <?php _e( 'Modal/Popup', 'trendtoday' ); ?>
                                    </option>
                                    <option value="fullpage" <?php selected( $search_suggestions_style, 'fullpage' ); ?>>
                                        <?php _e( 'Full Page', 'trendtoday' ); ?>
                                    </option>
                                </select>
                                <p class="description">
                                    <?php _e( 'เลือกรูปแบบการแสดงผล Suggestions', 'trendtoday' ); ?>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label><?php _e( 'Suggestions Display', 'trendtoday' ); ?></label>
                            </th>
                            <td>
                                <fieldset>
                                    <label style="display: block; margin-bottom: 8px;">
                                        <input type="checkbox" name="trendtoday_search_show_image" value="1" 
                                               <?php checked( in_array( 'image', $search_suggestions_display ), true ); ?> />
                                        <?php _e( 'Featured Image', 'trendtoday' ); ?>
                                    </label>
                                    <label style="display: block; margin-bottom: 8px;">
                                        <input type="checkbox" name="trendtoday_search_show_excerpt" value="1" 
                                               <?php checked( in_array( 'excerpt', $search_suggestions_display ), true ); ?> />
                                        <?php _e( 'Excerpt', 'trendtoday' ); ?>
                                    </label>
                                    <label style="display: block; margin-bottom: 8px;">
                                        <input type="checkbox" name="trendtoday_search_show_date" value="1" 
                                               <?php checked( in_array( 'date', $search_suggestions_display ), true ); ?> />
                                        <?php _e( 'Date', 'trendtoday' ); ?>
                                    </label>
                                    <label style="display: block; margin-bottom: 8px;">
                                        <input type="checkbox" name="trendtoday_search_show_category" value="1" 
                                               <?php checked( in_array( 'category', $search_suggestions_display ), true ); ?> />
                                        <?php _e( 'Category', 'trendtoday' ); ?>
                                    </label>
                                </fieldset>
                                <p class="description">
                                    <?php _e( 'เลือกข้อมูลที่ต้องการแสดงใน Suggestions', 'trendtoday' ); ?>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="trendtoday_search_results_layout"><?php _e( 'Results Layout', 'trendtoday' ); ?></label>
                            </th>
                            <td>
                                <select name="trendtoday_search_results_layout" id="trendtoday_search_results_layout">
                                    <option value="list" <?php selected( $search_results_layout, 'list' ); ?>>
                                        <?php _e( 'List View', 'trendtoday' ); ?>
                                    </option>
                                    <option value="grid" <?php selected( $search_results_layout, 'grid' ); ?>>
                                        <?php _e( 'Grid View', 'trendtoday' ); ?>
                                    </option>
                                    <option value="mixed" <?php selected( $search_results_layout, 'mixed' ); ?>>
                                        <?php _e( 'Mixed View', 'trendtoday' ); ?>
                                    </option>
                                </select>
                                <p class="description">
                                    <?php _e( 'เลือกรูปแบบการแสดงผลในหน้า Search Results', 'trendtoday' ); ?>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="trendtoday_search_results_sort"><?php _e( 'Default Sort', 'trendtoday' ); ?></label>
                            </th>
                            <td>
                                <select name="trendtoday_search_results_sort" id="trendtoday_search_results_sort">
                                    <option value="relevance" <?php selected( $search_results_sort, 'relevance' ); ?>>
                                        <?php _e( 'Relevance (ความเกี่ยวข้อง)', 'trendtoday' ); ?>
                                    </option>
                                    <option value="date_desc" <?php selected( $search_results_sort, 'date_desc' ); ?>>
                                        <?php _e( 'Date (ใหม่สุด)', 'trendtoday' ); ?>
                                    </option>
                                    <option value="date_asc" <?php selected( $search_results_sort, 'date_asc' ); ?>>
                                        <?php _e( 'Date (เก่าสุด)', 'trendtoday' ); ?>
                                    </option>
                                    <option value="title_asc" <?php selected( $search_results_sort, 'title_asc' ); ?>>
                                        <?php _e( 'Title (A-Z)', 'trendtoday' ); ?>
                                    </option>
                                    <option value="title_desc" <?php selected( $search_results_sort, 'title_desc' ); ?>>
                                        <?php _e( 'Title (Z-A)', 'trendtoday' ); ?>
                                    </option>
                                </select>
                                <p class="description">
                                    <?php _e( 'เลือกวิธีการเรียงลำดับผลการค้นหา', 'trendtoday' ); ?>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="trendtoday_search_placeholder"><?php _e( 'Search Placeholder', 'trendtoday' ); ?></label>
                            </th>
                            <td>
                                <input type="text" name="trendtoday_search_placeholder" id="trendtoday_search_placeholder" 
                                       value="<?php echo esc_attr( $search_placeholder ); ?>" 
                                       class="regular-text" />
                                <p class="description">
                                    <?php _e( 'ข้อความที่แสดงใน Search Box', 'trendtoday' ); ?>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label><?php _e( 'Exclude Categories', 'trendtoday' ); ?></label>
                            </th>
                            <td>
                                <div style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; border-radius: 4px;">
                                    <?php if ( ! empty( $all_categories ) ) : ?>
                                        <?php foreach ( $all_categories as $category ) : ?>
                                            <label style="display: block; margin-bottom: 8px;">
                                                <input type="checkbox" name="trendtoday_search_exclude_categories[]" value="<?php echo esc_attr( $category->term_id ); ?>" 
                                                       <?php checked( in_array( $category->term_id, $search_exclude_categories ), true ); ?> />
                                                <?php echo esc_html( $category->name ); ?> (<?php echo $category->count; ?>)
                                            </label>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <p class="description"><?php _e( 'ไม่มีหมวดหมู่', 'trendtoday' ); ?></p>
                                    <?php endif; ?>
                                </div>
                                <p class="description">
                                    <?php _e( 'เลือกหมวดหมู่ที่ต้องการยกเว้นจากการค้นหา', 'trendtoday' ); ?>
                                </p>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <!-- TOC Settings Tab -->
            <div id="toc-tab" class="trendtoday-tab-content <?php echo $active_tab === 'toc' ? 'active' : ''; ?>">
                <div class="trendtoday-settings-section">
                    <h2 class="trendtoday-section-title">
                        <span class="dashicons dashicons-list-view"></span>
                        <?php _e( 'Table of Contents Settings', 'trendtoday' ); ?>
                    </h2>
                    <p class="trendtoday-section-description">
                        <?php _e( 'ตั้งค่า Table of Contents (TOC) สำหรับบทความ', 'trendtoday' ); ?>
                    </p>
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <?php _e( 'Enable TOC', 'trendtoday' ); ?>
                            </th>
                            <td>
                                <label class="trendtoday-toggle">
                                    <input type="checkbox" name="trendtoday_toc_enabled" value="1" <?php checked( $toc_enabled, '1' ); ?> />
                                    <span class="toggle-slider"></span>
                                    <span class="toggle-label"><?php _e( 'เปิดใช้งาน Table of Contents', 'trendtoday' ); ?></span>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="trendtoday_toc_position"><?php _e( 'Position', 'trendtoday' ); ?></label>
                            </th>
                            <td>
                                <select name="trendtoday_toc_position" id="trendtoday_toc_position">
                                    <option value="top" <?php selected( $toc_position, 'top' ); ?>>
                                        <?php _e( 'Top (ด้านบนเนื้อหา)', 'trendtoday' ); ?>
                                    </option>
                                    <option value="sidebar" <?php selected( $toc_position, 'sidebar' ); ?>>
                                        <?php _e( 'Sidebar (ด้านข้าง)', 'trendtoday' ); ?>
                                    </option>
                                    <option value="floating" <?php selected( $toc_position, 'floating' ); ?>>
                                        <?php _e( 'Floating (ลอยด้านข้าง)', 'trendtoday' ); ?>
                                    </option>
                                </select>
                                <p class="description">
                                    <?php _e( 'เลือกตำแหน่งที่ต้องการแสดง TOC', 'trendtoday' ); ?>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <?php _e( 'Show on Mobile', 'trendtoday' ); ?>
                            </th>
                            <td>
                                <label class="trendtoday-toggle">
                                    <input type="checkbox" name="trendtoday_toc_mobile_enabled" value="1" <?php checked( $toc_mobile_enabled, '1' ); ?> />
                                    <span class="toggle-slider"></span>
                                    <span class="toggle-label"><?php _e( 'แสดง TOC บน Mobile', 'trendtoday' ); ?></span>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="trendtoday_toc_mobile_position"><?php _e( 'Mobile Position', 'trendtoday' ); ?></label>
                            </th>
                            <td>
                                <select name="trendtoday_toc_mobile_position" id="trendtoday_toc_mobile_position">
                                    <option value="top" <?php selected( $toc_mobile_position, 'top' ); ?>>
                                        <?php _e( 'Top (ด้านบน)', 'trendtoday' ); ?>
                                    </option>
                                    <option value="bottom" <?php selected( $toc_mobile_position, 'bottom' ); ?>>
                                        <?php _e( 'Bottom (ด้านล่าง)', 'trendtoday' ); ?>
                                    </option>
                                    <option value="floating" <?php selected( $toc_mobile_position, 'floating' ); ?>>
                                        <?php _e( 'Floating Button (ปุ่มลอย)', 'trendtoday' ); ?>
                                    </option>
                                    <option value="collapsible" <?php selected( $toc_mobile_position, 'collapsible' ); ?>>
                                        <?php _e( 'Collapsible Menu (เมนูย่อ/ขยาย)', 'trendtoday' ); ?>
                                    </option>
                                </select>
                                <p class="description">
                                    <?php _e( 'เลือกตำแหน่งที่ต้องการแสดง TOC บน Mobile', 'trendtoday' ); ?>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label><?php _e( 'Heading Levels', 'trendtoday' ); ?></label>
                            </th>
                            <td>
                                <fieldset>
                                    <label style="display: block; margin-bottom: 8px;">
                                        <input type="checkbox" name="trendtoday_toc_heading_h2" value="1" 
                                               <?php checked( in_array( 'h2', $toc_headings ), true ); ?> />
                                        <?php _e( 'H2 (หัวข้อหลัก)', 'trendtoday' ); ?>
                                    </label>
                                    <label style="display: block; margin-bottom: 8px;">
                                        <input type="checkbox" name="trendtoday_toc_heading_h3" value="1" 
                                               <?php checked( in_array( 'h3', $toc_headings ), true ); ?> />
                                        <?php _e( 'H3 (หัวข้อย่อย)', 'trendtoday' ); ?>
                                    </label>
                                    <label style="display: block; margin-bottom: 8px;">
                                        <input type="checkbox" name="trendtoday_toc_heading_h4" value="1" 
                                               <?php checked( in_array( 'h4', $toc_headings ), true ); ?> />
                                        <?php _e( 'H4 (หัวข้อรอง)', 'trendtoday' ); ?>
                                    </label>
                                    <label style="display: block; margin-bottom: 8px;">
                                        <input type="checkbox" name="trendtoday_toc_heading_h5" value="1" 
                                               <?php checked( in_array( 'h5', $toc_headings ), true ); ?> />
                                        <?php _e( 'H5', 'trendtoday' ); ?>
                                    </label>
                                    <label style="display: block; margin-bottom: 8px;">
                                        <input type="checkbox" name="trendtoday_toc_heading_h6" value="1" 
                                               <?php checked( in_array( 'h6', $toc_headings ), true ); ?> />
                                        <?php _e( 'H6', 'trendtoday' ); ?>
                                    </label>
                                </fieldset>
                                <p class="description">
                                    <?php _e( 'เลือกระดับ Heading ที่ต้องการแสดงใน TOC', 'trendtoday' ); ?>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="trendtoday_toc_style"><?php _e( 'Style', 'trendtoday' ); ?></label>
                            </th>
                            <td>
                                <select name="trendtoday_toc_style" id="trendtoday_toc_style">
                                    <option value="simple" <?php selected( $toc_style, 'simple' ); ?>>
                                        <?php _e( 'Simple List (รายการธรรมดา)', 'trendtoday' ); ?>
                                    </option>
                                    <option value="numbered" <?php selected( $toc_style, 'numbered' ); ?>>
                                        <?php _e( 'Numbered List (รายการแบบมีเลข)', 'trendtoday' ); ?>
                                    </option>
                                    <option value="nested" <?php selected( $toc_style, 'nested' ); ?>>
                                        <?php _e( 'Nested/Indented (แบบซ้อน)', 'trendtoday' ); ?>
                                    </option>
                                </select>
                                <p class="description">
                                    <?php _e( 'เลือกรูปแบบการแสดงผล TOC', 'trendtoday' ); ?>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label><?php _e( 'Features', 'trendtoday' ); ?></label>
                            </th>
                            <td>
                                <fieldset>
                                    <label style="display: block; margin-bottom: 8px;">
                                        <input type="checkbox" name="trendtoday_toc_smooth_scroll" value="1" 
                                               <?php checked( $toc_smooth_scroll, '1' ); ?> />
                                        <?php _e( 'Smooth Scroll (เลื่อนแบบนุ่มนวล)', 'trendtoday' ); ?>
                                    </label>
                                    <label style="display: block; margin-bottom: 8px;">
                                        <input type="checkbox" name="trendtoday_toc_scroll_spy" value="1" 
                                               <?php checked( $toc_scroll_spy, '1' ); ?> />
                                        <?php _e( 'Scroll Spy (Highlight section ที่กำลังอ่าน)', 'trendtoday' ); ?>
                                    </label>
                                    <label style="display: block; margin-bottom: 8px;">
                                        <input type="checkbox" name="trendtoday_toc_collapsible" value="1" 
                                               <?php checked( $toc_collapsible, '1' ); ?> />
                                        <?php _e( 'Collapsible (ย่อ/ขยายได้)', 'trendtoday' ); ?>
                                    </label>
                                    <label style="display: block; margin-bottom: 8px;">
                                        <input type="checkbox" name="trendtoday_toc_sticky" value="1" 
                                               <?php checked( $toc_sticky, '1' ); ?> />
                                        <?php _e( 'Sticky (ติดตามเมื่อ scroll)', 'trendtoday' ); ?>
                                    </label>
                                    <label style="display: block; margin-bottom: 8px;">
                                        <input type="checkbox" name="trendtoday_toc_auto_collapse_mobile" value="1" 
                                               <?php checked( $toc_auto_collapse_mobile, '1' ); ?> />
                                        <?php _e( 'Auto-collapse on Mobile (ย่ออัตโนมัติบนมือถือ)', 'trendtoday' ); ?>
                                    </label>
                                </fieldset>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="trendtoday_toc_min_headings"><?php _e( 'Minimum Headings', 'trendtoday' ); ?></label>
                            </th>
                            <td>
                                <input type="number" name="trendtoday_toc_min_headings" id="trendtoday_toc_min_headings" 
                                       value="<?php echo esc_attr( $toc_min_headings ); ?>" 
                                       min="0" max="20" class="small-text" />
                                <p class="description">
                                    <?php _e( 'จำนวน Heading ขั้นต่ำที่จะแสดง TOC (0 = แสดงเสมอ)', 'trendtoday' ); ?>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="trendtoday_toc_title"><?php _e( 'TOC Title', 'trendtoday' ); ?></label>
                            </th>
                            <td>
                                <input type="text" name="trendtoday_toc_title" id="trendtoday_toc_title" 
                                       value="<?php echo esc_attr( $toc_title ); ?>" 
                                       class="regular-text" />
                                <p class="description">
                                    <?php _e( 'ชื่อหัวข้อของ TOC (เช่น: สารบัญ, Table of Contents)', 'trendtoday' ); ?>
                                </p>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <!-- Image Optimization Tab -->
            <div id="image-optimization-tab" class="trendtoday-tab-content <?php echo $active_tab === 'image-optimization' ? 'active' : ''; ?>">
                <div class="trendtoday-settings-section">
                    <h2 class="trendtoday-section-title">
                        <span class="dashicons dashicons-images-alt2"></span>
                        <?php _e( 'Image Optimization Settings', 'trendtoday' ); ?>
                    </h2>
                    <p class="trendtoday-section-description">
                        <?php _e( 'ตั้งค่าการปรับขนาดภาพและแปลงเป็น WebP อัตโนมัติเพื่อเพิ่มประสิทธิภาพ', 'trendtoday' ); ?>
                    </p>
                    
                    <!-- Statistics -->
                    <div class="trendtoday-stats-box" style="background: #f0f6fc; padding: 20px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #2271b1;">
                        <h3 style="margin-top: 0;">
                            <span class="dashicons dashicons-chart-bar"></span>
                            <?php _e( 'Image Statistics', 'trendtoday' ); ?>
                        </h3>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 20px;">
                            <div>
                                <strong><?php _e( 'Total Images:', 'trendtoday' ); ?></strong>
                                <span style="font-size: 24px; color: #2271b1; display: block;"><?php echo number_format( $image_stats['total_images'] ); ?></span>
                            </div>
                            <div>
                                <strong><?php _e( 'Total Size:', 'trendtoday' ); ?></strong>
                                <span style="font-size: 24px; color: #2271b1; display: block;"><?php echo number_format( $image_stats['total_size_mb'], 2 ); ?> MB</span>
                            </div>
                            <div>
                                <strong><?php _e( 'Optimized Images:', 'trendtoday' ); ?></strong>
                                <span style="font-size: 24px; color: #00a32a; display: block;">
                                    <?php echo number_format( $image_stats['optimized_images'] ); ?>
                                    <?php if ( $image_stats['total_images'] > 0 ) : ?>
                                        <small style="font-size: 14px; color: #646970;">
                                            (<?php echo number_format( $image_stats['optimization_percentage'], 1 ); ?>%)
                                        </small>
                                    <?php endif; ?>
                                </span>
                            </div>
                            <div>
                                <strong><?php _e( 'WebP Images:', 'trendtoday' ); ?></strong>
                                <span style="font-size: 24px; color: #2271b1; display: block;">
                                    <?php echo number_format( $image_stats['webp_images'] ); ?>
                                    <?php if ( $image_stats['total_images'] > 0 ) : ?>
                                        <small style="font-size: 14px; color: #646970;">
                                            (<?php echo number_format( $image_stats['webp_percentage'], 1 ); ?>%)
                                        </small>
                                    <?php endif; ?>
                                </span>
                            </div>
                        </div>
                        
                        <!-- Settings Status -->
                        <div style="background: #fff; padding: 15px; border-radius: 6px; margin-top: 15px;">
                            <h4 style="margin-top: 0; margin-bottom: 10px;">
                                <span class="dashicons dashicons-admin-settings"></span>
                                <?php _e( 'Current Settings Status', 'trendtoday' ); ?>
                            </h4>
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 10px; font-size: 13px;">
                                <div>
                                    <strong><?php _e( 'Auto Resize:', 'trendtoday' ); ?></strong>
                                    <?php if ( $image_stats['settings_status']['auto_resize'] ) : ?>
                                        <span style="color: #00a32a;">
                                            <span class="dashicons dashicons-yes-alt"></span> <?php _e( 'Enabled', 'trendtoday' ); ?>
                                        </span>
                                        <br><small style="color: #646970;">
                                            <?php echo esc_html( $image_stats['settings_status']['max_width'] ); ?> × <?php echo esc_html( $image_stats['settings_status']['max_height'] ); ?> px
                                        </small>
                                    <?php else : ?>
                                        <span style="color: #d63638;">
                                            <span class="dashicons dashicons-dismiss"></span> <?php _e( 'Disabled', 'trendtoday' ); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <strong><?php _e( 'WebP Conversion:', 'trendtoday' ); ?></strong>
                                    <?php if ( $image_stats['settings_status']['webp_enabled'] && $webp_supported ) : ?>
                                        <span style="color: #00a32a;">
                                            <span class="dashicons dashicons-yes-alt"></span> <?php _e( 'Enabled', 'trendtoday' ); ?>
                                        </span>
                                        <br><small style="color: #646970;">
                                            <?php _e( 'Quality:', 'trendtoday' ); ?> <?php echo esc_html( $image_stats['settings_status']['webp_quality'] ); ?>%
                                        </small>
                                    <?php else : ?>
                                        <span style="color: #d63638;">
                                            <span class="dashicons dashicons-dismiss"></span> <?php _e( 'Disabled', 'trendtoday' ); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <strong><?php _e( 'JPEG Quality:', 'trendtoday' ); ?></strong>
                                    <span><?php echo esc_html( $image_stats['settings_status']['jpeg_quality'] ); ?>%</span>
                                </div>
                                <div>
                                    <strong><?php _e( 'Strip EXIF:', 'trendtoday' ); ?></strong>
                                    <?php if ( $image_stats['settings_status']['strip_exif'] ) : ?>
                                        <span style="color: #00a32a;">
                                            <span class="dashicons dashicons-yes-alt"></span> <?php _e( 'Enabled', 'trendtoday' ); ?>
                                        </span>
                                    <?php else : ?>
                                        <span style="color: #d63638;">
                                            <span class="dashicons dashicons-dismiss"></span> <?php _e( 'Disabled', 'trendtoday' ); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Recent Optimized Images -->
                        <?php if ( ! empty( $image_stats['recent_optimized'] ) ) : ?>
                            <div style="background: #fff; padding: 15px; border-radius: 6px; margin-top: 15px;">
                                <h4 style="margin-top: 0; margin-bottom: 15px;">
                                    <span class="dashicons dashicons-images-alt2"></span>
                                    <?php _e( 'Recent Optimized Images', 'trendtoday' ); ?>
                                </h4>
                                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 15px;">
                                    <?php foreach ( $image_stats['recent_optimized'] as $img ) : ?>
                                        <div style="border: 1px solid #ddd; border-radius: 6px; overflow: hidden; background: #fff;">
                                            <div style="position: relative; padding-bottom: 75%; background: #f0f0f1;">
                                                <?php if ( $img['url'] ) : ?>
                                                    <img src="<?php echo esc_url( $img['url'] ); ?>" 
                                                         alt="<?php echo esc_attr( $img['title'] ); ?>"
                                                         style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;">
                                                <?php endif; ?>
                                                <?php if ( $img['has_webp'] ) : ?>
                                                    <span style="position: absolute; top: 5px; right: 5px; background: #00a32a; color: #fff; padding: 2px 6px; border-radius: 3px; font-size: 10px; font-weight: bold;">
                                                        WebP
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                            <div style="padding: 8px; font-size: 11px;">
                                                <div style="font-weight: bold; margin-bottom: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?php echo esc_attr( $img['title'] ); ?>">
                                                    <?php echo esc_html( wp_trim_words( $img['title'], 5 ) ); ?>
                                                </div>
                                                <div style="color: #646970; font-size: 10px;">
                                                    <?php if ( $img['width'] > 0 && $img['height'] > 0 ) : ?>
                                                        <?php echo esc_html( $img['width'] ); ?> × <?php echo esc_html( $img['height'] ); ?> px
                                                    <?php endif; ?>
                                                    <br>
                                                    <?php echo esc_html( $img['size'] ); ?> KB
                                                    <?php if ( $img['is_resized'] ) : ?>
                                                        <span style="color: #00a32a;" title="<?php _e( 'Resized', 'trendtoday' ); ?>">
                                                            <span class="dashicons dashicons-yes-alt" style="font-size: 12px; width: 12px; height: 12px;"></span>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <?php _e( 'Auto Resize on Upload', 'trendtoday' ); ?>
                            </th>
                            <td>
                                <label class="trendtoday-toggle">
                                    <input type="checkbox" name="trendtoday_image_auto_resize" value="1" <?php checked( $image_auto_resize, '1' ); ?> />
                                    <span class="toggle-slider"></span>
                                    <span class="toggle-label"><?php _e( 'ปรับขนาดภาพอัตโนมัติเมื่ออัพโหลด', 'trendtoday' ); ?></span>
                                </label>
                                <p class="description">
                                    <?php _e( 'ภาพจะถูกปรับขนาดตาม Max Width/Height ที่ตั้งค่าไว้', 'trendtoday' ); ?>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="trendtoday_image_max_width"><?php _e( 'Max Width (px)', 'trendtoday' ); ?></label>
                            </th>
                            <td>
                                <input type="number" name="trendtoday_image_max_width" id="trendtoday_image_max_width" 
                                       value="<?php echo esc_attr( $image_max_width ); ?>" 
                                       min="100" max="10000" step="10" class="small-text" />
                                <p class="description">
                                    <?php _e( 'ความกว้างสูงสุดของภาพ (แนะนำ: 1920px สำหรับ Full HD)', 'trendtoday' ); ?>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="trendtoday_image_max_height"><?php _e( 'Max Height (px)', 'trendtoday' ); ?></label>
                            </th>
                            <td>
                                <input type="number" name="trendtoday_image_max_height" id="trendtoday_image_max_height" 
                                       value="<?php echo esc_attr( $image_max_height ); ?>" 
                                       min="100" max="10000" step="10" class="small-text" />
                                <p class="description">
                                    <?php _e( 'ความสูงสูงสุดของภาพ (แนะนำ: 1080px สำหรับ Full HD)', 'trendtoday' ); ?>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <?php _e( 'Maintain Aspect Ratio', 'trendtoday' ); ?>
                            </th>
                            <td>
                                <label class="trendtoday-toggle">
                                    <input type="checkbox" name="trendtoday_image_maintain_aspect" value="1" <?php checked( $image_maintain_aspect, '1' ); ?> />
                                    <span class="toggle-slider"></span>
                                    <span class="toggle-label"><?php _e( 'รักษาสัดส่วนภาพ', 'trendtoday' ); ?></span>
                                </label>
                                <p class="description">
                                    <?php _e( 'ภาพจะถูกปรับขนาดโดยรักษาสัดส่วนเดิม', 'trendtoday' ); ?>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="trendtoday_image_jpeg_quality"><?php _e( 'JPEG Quality', 'trendtoday' ); ?></label>
                            </th>
                            <td>
                                <input type="number" name="trendtoday_image_jpeg_quality" id="trendtoday_image_jpeg_quality" 
                                       value="<?php echo esc_attr( $image_jpeg_quality ); ?>" 
                                       min="0" max="100" class="small-text" />
                                <p class="description">
                                    <?php _e( 'คุณภาพของภาพ JPEG (0-100, แนะนำ: 85 สำหรับสมดุลระหว่างคุณภาพและขนาดไฟล์)', 'trendtoday' ); ?>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="trendtoday_image_max_file_size"><?php _e( 'Max File Size Before Resize (MB)', 'trendtoday' ); ?></label>
                            </th>
                            <td>
                                <input type="number" name="trendtoday_image_max_file_size" id="trendtoday_image_max_file_size" 
                                       value="<?php echo esc_attr( $image_max_file_size ); ?>" 
                                       min="0" max="100" step="0.5" class="small-text" />
                                <p class="description">
                                    <?php _e( 'ปรับขนาดเฉพาะภาพที่ใหญ่กว่าขนาดนี้ (0 = ปรับทุกภาพ)', 'trendtoday' ); ?>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row" colspan="2">
                                <h3 style="margin: 20px 0 10px 0; padding-bottom: 10px; border-bottom: 2px solid #ddd;">
                                    <?php _e( 'WebP Conversion', 'trendtoday' ); ?>
                                </h3>
                            </th>
                        </tr>
                        <tr>
                            <th scope="row">
                                <?php _e( 'WebP Support', 'trendtoday' ); ?>
                            </th>
                            <td>
                                <?php if ( $webp_supported ) : ?>
                                    <span style="color: #00a32a; font-weight: bold;">
                                        <span class="dashicons dashicons-yes-alt"></span>
                                        <?php _e( 'WebP is supported on this server', 'trendtoday' ); ?>
                                    </span>
                                <?php else : ?>
                                    <span style="color: #d63638; font-weight: bold;">
                                        <span class="dashicons dashicons-warning"></span>
                                        <?php _e( 'WebP is NOT supported on this server', 'trendtoday' ); ?>
                                    </span>
                                    <p class="description">
                                        <?php _e( 'Please install GD Library with WebP support or Imagick extension', 'trendtoday' ); ?>
                                    </p>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <?php _e( 'Enable WebP Conversion', 'trendtoday' ); ?>
                            </th>
                            <td>
                                <label class="trendtoday-toggle">
                                    <input type="checkbox" name="trendtoday_image_webp_enabled" value="1" 
                                           <?php checked( $image_webp_enabled, '1' ); ?>
                                           <?php echo ! $webp_supported ? 'disabled' : ''; ?> />
                                    <span class="toggle-slider"></span>
                                    <span class="toggle-label"><?php _e( 'แปลงภาพเป็น WebP อัตโนมัติ', 'trendtoday' ); ?></span>
                                </label>
                                <p class="description">
                                    <?php _e( 'สร้างไฟล์ WebP พร้อมกับไฟล์ต้นฉบับ (ลดขนาดไฟล์ 30-50%)', 'trendtoday' ); ?>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="trendtoday_image_webp_quality"><?php _e( 'WebP Quality', 'trendtoday' ); ?></label>
                            </th>
                            <td>
                                <input type="number" name="trendtoday_image_webp_quality" id="trendtoday_image_webp_quality" 
                                       value="<?php echo esc_attr( $image_webp_quality ); ?>" 
                                       min="0" max="100" 
                                       class="small-text"
                                       <?php echo ! $webp_supported ? 'disabled' : ''; ?> />
                                <p class="description">
                                    <?php _e( 'คุณภาพของภาพ WebP (0-100, แนะนำ: 85)', 'trendtoday' ); ?>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row" colspan="2">
                                <h3 style="margin: 20px 0 10px 0; padding-bottom: 10px; border-bottom: 2px solid #ddd;">
                                    <?php _e( 'Additional Options', 'trendtoday' ); ?>
                                </h3>
                            </th>
                        </tr>
                        <tr>
                            <th scope="row">
                                <?php _e( 'Strip EXIF Data', 'trendtoday' ); ?>
                            </th>
                            <td>
                                <label class="trendtoday-toggle">
                                    <input type="checkbox" name="trendtoday_image_strip_exif" value="1" <?php checked( $image_strip_exif, '1' ); ?> />
                                    <span class="toggle-slider"></span>
                                    <span class="toggle-label"><?php _e( 'ลบ EXIF metadata ออกจากภาพ', 'trendtoday' ); ?></span>
                                </label>
                                <p class="description">
                                    <?php _e( 'ลบข้อมูล EXIF (เช่น GPS, Camera info) เพื่อลดขนาดไฟล์และความเป็นส่วนตัว', 'trendtoday' ); ?>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <?php _e( 'Regenerate Images', 'trendtoday' ); ?>
                            </th>
                            <td>
                                <button type="button" id="trendtoday-regenerate-images" class="button button-secondary">
                                    <span class="dashicons dashicons-update" style="vertical-align: middle;"></span>
                                    <?php _e( 'Regenerate All Images', 'trendtoday' ); ?>
                                </button>
                                <p class="description">
                                    <?php _e( 'ปรับขนาดและแปลงเป็น WebP สำหรับภาพที่มีอยู่แล้วทั้งหมด (อาจใช้เวลานาน)', 'trendtoday' ); ?>
                                </p>
                                <div id="trendtoday-regenerate-progress" style="display: none; margin-top: 10px;">
                                    <div style="background: #f0f0f1; height: 20px; border-radius: 10px; overflow: hidden;">
                                        <div id="trendtoday-regenerate-progress-bar" style="background: #2271b1; height: 100%; width: 0%; transition: width 0.3s;"></div>
                                    </div>
                                    <p id="trendtoday-regenerate-status" style="margin-top: 5px; font-size: 12px;"></p>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <!-- Image Cleanup Section -->
                <div class="trendtoday-settings-section" style="margin-top: 30px; padding-top: 30px; border-top: 2px solid #ddd;">
                    <h2 class="trendtoday-section-title">
                        <span class="dashicons dashicons-trash"></span>
                        <?php _e( 'Unused Image Cleaner', 'trendtoday' ); ?>
                    </h2>
                    <p class="trendtoday-section-description">
                        <?php _e( 'แสกนและลบภาพที่ไม่ได้ใช้ รวมถึง Thumbnail ขยะ, WebP ที่ไม่ได้ใช้, และภาพที่ไม่มี attachment record', 'trendtoday' ); ?>
                    </p>
                    
                    <!-- Scan Section -->
                    <div class="trendtoday-image-cleanup-section" style="background: #fff; padding: 20px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #ddd;">
                        <h3 style="margin-top: 0;">
                            <span class="dashicons dashicons-search"></span>
                            <?php _e( 'Scan for Unused Images', 'trendtoday' ); ?>
                        </h3>
                        <p class="description">
                            <?php _e( 'คลิกปุ่มด้านล่างเพื่อเริ่มแสกนหาภาพที่ไม่ได้ใช้ กระบวนการนี้อาจใช้เวลาสักครู่', 'trendtoday' ); ?>
                        </p>
                        <button type="button" id="trendtoday-scan-unused-images" class="button button-primary" style="margin-top: 10px;">
                            <span class="dashicons dashicons-search" style="vertical-align: middle;"></span>
                            <?php _e( 'Scan for Unused Images', 'trendtoday' ); ?>
                        </button>
                        
                        <!-- Progress Indicator -->
                        <div id="trendtoday-scan-progress" style="display: none; margin-top: 20px;">
                            <div style="background: #f0f0f1; height: 24px; border-radius: 12px; overflow: hidden; position: relative;">
                                <div id="trendtoday-scan-progress-bar" style="background: linear-gradient(90deg, #2271b1 0%, #00a32a 100%); height: 100%; width: 0%; transition: width 0.3s; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: bold; font-size: 12px;"></div>
                            </div>
                            <p id="trendtoday-scan-status" style="margin-top: 10px; font-size: 13px; color: #646970;">
                                <span class="dashicons dashicons-update" style="animation: spin 1s linear infinite; display: inline-block;"></span>
                                <?php _e( 'กำลังแสกน...', 'trendtoday' ); ?>
                            </p>
                        </div>
                    </div>
                    
                    <!-- Results Section -->
                    <div id="trendtoday-scan-results" style="display: none;">
                        <!-- Statistics Box -->
                        <div class="trendtoday-stats-box" style="background: #f0f6fc; padding: 20px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #2271b1;">
                            <h3 style="margin-top: 0;">
                                <span class="dashicons dashicons-chart-bar"></span>
                                <?php _e( 'Scan Results', 'trendtoday' ); ?>
                            </h3>
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 15px;">
                                <div>
                                    <strong><?php _e( 'Unused Thumbnails:', 'trendtoday' ); ?></strong>
                                    <span id="stat-thumbnails-count" style="font-size: 24px; color: #2271b1; display: block;">0</span>
                                    <small id="stat-thumbnails-size" style="color: #646970;">0 MB</small>
                                </div>
                                <div>
                                    <strong><?php _e( 'Unused WebP:', 'trendtoday' ); ?></strong>
                                    <span id="stat-webp-count" style="font-size: 24px; color: #2271b1; display: block;">0</span>
                                    <small id="stat-webp-size" style="color: #646970;">0 MB</small>
                                </div>
                                <div>
                                    <strong><?php _e( 'Orphaned Images:', 'trendtoday' ); ?></strong>
                                    <span id="stat-orphaned-count" style="font-size: 24px; color: #2271b1; display: block;">0</span>
                                    <small id="stat-orphaned-size" style="color: #646970;">0 MB</small>
                                </div>
                                <div>
                                    <strong><?php _e( 'Total Reclaimable:', 'trendtoday' ); ?></strong>
                                    <span id="stat-total-size" style="font-size: 24px; color: #00a32a; display: block; font-weight: bold;">0 MB</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- File Lists Tabs -->
                        <div class="trendtoday-file-lists" style="background: #fff; border: 1px solid #ddd; border-radius: 8px; overflow: hidden;">
                            <ul class="nav-tabs" style="margin: 0; padding: 0; list-style: none; display: flex; border-bottom: 1px solid #ddd; background: #f9f9f9;">
                                <li style="margin: 0;">
                                    <a href="#thumbnails-tab" class="nav-tab nav-tab-active" data-tab="thumbnails" style="display: block; padding: 12px 20px; text-decoration: none; color: #2271b1; border-bottom: 3px solid #2271b1; background: #fff; margin-bottom: -1px;">
                                        <span class="dashicons dashicons-images-alt2"></span>
                                        <?php _e( 'Thumbnails', 'trendtoday' ); ?>
                                        <span id="tab-thumbnails-count" class="count-badge" style="background: #2271b1; color: #fff; padding: 2px 8px; border-radius: 10px; font-size: 11px; margin-left: 5px;">0</span>
                                    </a>
                                </li>
                                <li style="margin: 0;">
                                    <a href="#webp-tab" class="nav-tab" data-tab="webp" style="display: block; padding: 12px 20px; text-decoration: none; color: #646970; border-bottom: 3px solid transparent;">
                                        <span class="dashicons dashicons-format-image"></span>
                                        <?php _e( 'Unused WebP', 'trendtoday' ); ?>
                                        <span id="tab-webp-count" class="count-badge" style="background: #646970; color: #fff; padding: 2px 8px; border-radius: 10px; font-size: 11px; margin-left: 5px;">0</span>
                                    </a>
                                </li>
                                <li style="margin: 0;">
                                    <a href="#orphaned-tab" class="nav-tab" data-tab="orphaned" style="display: block; padding: 12px 20px; text-decoration: none; color: #646970; border-bottom: 3px solid transparent;">
                                        <span class="dashicons dashicons-warning"></span>
                                        <?php _e( 'Orphaned Images', 'trendtoday' ); ?>
                                        <span id="tab-orphaned-count" class="count-badge" style="background: #646970; color: #fff; padding: 2px 8px; border-radius: 10px; font-size: 11px; margin-left: 5px;">0</span>
                                    </a>
                                </li>
                                <li style="margin: 0; display: none;" id="failed-files-tab-li">
                                    <a href="#failed-tab" class="nav-tab" data-tab="failed" style="display: block; padding: 12px 20px; text-decoration: none; color: #d63638; border-bottom: 3px solid transparent;">
                                        <span class="dashicons dashicons-dismiss"></span>
                                        <?php _e( 'Failed to Delete', 'trendtoday' ); ?>
                                        <span id="tab-failed-count" class="count-badge" style="background: #d63638; color: #fff; padding: 2px 8px; border-radius: 10px; font-size: 11px; margin-left: 5px;">0</span>
                                    </a>
                                </li>
                            </ul>
                            
                            <!-- Tab Contents -->
                            <div class="tab-content" style="padding: 20px;">
                                <!-- Thumbnails Tab -->
                                <div id="thumbnails-tab" class="tab-pane active" style="display: block;">
                                    <div id="thumbnails-list" class="file-list" style="max-height: 400px; overflow-y: auto;">
                                        <p style="color: #646970; text-align: center; padding: 20px;">
                                            <?php _e( 'ไม่มี thumbnail ที่ไม่ได้ใช้', 'trendtoday' ); ?>
                                        </p>
                                    </div>
                                </div>
                                
                                <!-- WebP Tab -->
                                <div id="webp-tab" class="tab-pane" style="display: none;">
                                    <div id="webp-list" class="file-list" style="max-height: 400px; overflow-y: auto;">
                                        <p style="color: #646970; text-align: center; padding: 20px;">
                                            <?php _e( 'ไม่มี WebP ที่ไม่ได้ใช้', 'trendtoday' ); ?>
                                        </p>
                                    </div>
                                </div>
                                
                                <!-- Orphaned Tab -->
                                <div id="orphaned-tab" class="tab-pane" style="display: none;">
                                    <div id="orphaned-list" class="file-list" style="max-height: 400px; overflow-y: auto;">
                                        <p style="color: #646970; text-align: center; padding: 20px;">
                                            <?php _e( 'ไม่มี orphaned images', 'trendtoday' ); ?>
                                        </p>
                                    </div>
                                </div>
                                
                                <!-- Failed Files Tab -->
                                <div id="failed-tab" class="tab-pane" style="display: none;">
                                    <div id="failed-list" class="file-list" style="max-height: 400px; overflow-y: auto;">
                                        <p style="color: #646970; text-align: center; padding: 20px;">
                                            <?php _e( 'ไม่มีไฟล์ที่ลบไม่สำเร็จ', 'trendtoday' ); ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="trendtoday-cleanup-actions" style="margin-top: 20px; padding: 20px; background: #fff; border: 1px solid #ddd; border-radius: 8px;">
                            <h3 style="margin-top: 0;">
                                <span class="dashicons dashicons-trash"></span>
                                <?php _e( 'Delete Files', 'trendtoday' ); ?>
                            </h3>
                            <p class="description">
                                <?php _e( 'เลือกไฟล์ที่ต้องการลบ หรือลบทั้งหมด ระวัง: การลบไฟล์ไม่สามารถยกเลิกได้', 'trendtoday' ); ?>
                            </p>
                            <div style="margin-top: 15px;">
                                <button type="button" id="trendtoday-delete-selected" class="button button-secondary" disabled>
                                    <span class="dashicons dashicons-trash"></span>
                                    <?php _e( 'Delete Selected', 'trendtoday' ); ?>
                                </button>
                                <button type="button" id="trendtoday-delete-all" class="button button-danger" style="background: #d63638; border-color: #d63638; color: #fff;">
                                    <span class="dashicons dashicons-warning"></span>
                                    <?php _e( 'Delete All', 'trendtoday' ); ?>
                                </button>
                                <button type="button" id="trendtoday-download-report" class="button button-secondary">
                                    <span class="dashicons dashicons-download"></span>
                                    <?php _e( 'Download Report (CSV)', 'trendtoday' ); ?>
                                </button>
                            </div>
                            
                            <!-- Delete Progress -->
                            <div id="trendtoday-delete-progress" style="display: none; margin-top: 20px;">
                                <div style="background: #f0f0f1; height: 24px; border-radius: 12px; overflow: hidden;">
                                    <div id="trendtoday-delete-progress-bar" style="background: #d63638; height: 100%; width: 0%; transition: width 0.3s;"></div>
                                </div>
                                <p id="trendtoday-delete-status" style="margin-top: 10px; font-size: 13px; color: #646970;"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Widgets Tab -->
            <div id="widgets-tab" class="trendtoday-tab-content <?php echo $active_tab === 'widgets' ? 'active' : ''; ?>">
                <div class="trendtoday-settings-section">
                    <h2 class="trendtoday-section-title">
                        <span class="dashicons dashicons-welcome-widgets-menus"></span>
                        <?php _e( 'Widget Visibility Settings', 'trendtoday' ); ?>
                    </h2>
                    <p class="trendtoday-section-description">
                        <?php _e( 'เลือก widgets ที่ต้องการให้แสดงใน WordPress Widgets area (Appearance > Widgets)', 'trendtoday' ); ?>
                    </p>
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <?php _e( 'Available Widgets', 'trendtoday' ); ?>
                            </th>
                            <td>
                                <fieldset>
                                    <legend class="screen-reader-text">
                                        <span><?php _e( 'Select widgets to enable', 'trendtoday' ); ?></span>
                                    </legend>
                                    <?php foreach ( $available_widgets as $widget_key => $widget_name ) : ?>
                                        <label style="display: block; margin-bottom: 12px; padding: 10px; background: #f9f9f9; border-radius: 4px; border-left: 3px solid #2271b1;">
                                            <input type="checkbox" 
                                                   name="trendtoday_enabled_widgets[]" 
                                                   value="<?php echo esc_attr( $widget_key ); ?>"
                                                   <?php checked( in_array( $widget_key, $enabled_widgets, true ) ); ?> />
                                            <strong><?php echo esc_html( $widget_name ); ?></strong>
                                            <p class="description" style="margin: 5px 0 0 25px; color: #646970;">
                                                <?php
                                                switch ( $widget_key ) {
                                                    case 'popular_posts':
                                                        _e( 'แสดงบทความยอดนิยมตามจำนวน views', 'trendtoday' );
                                                        break;
                                                    case 'recent_posts':
                                                        _e( 'แสดงบทความล่าสุด', 'trendtoday' );
                                                        break;
                                                    case 'trending_tags':
                                                        _e( 'แสดง tags ที่มาแรง (Trending tags)', 'trendtoday' );
                                                        break;
                                                }
                                                ?>
                                            </p>
                                        </label>
                                    <?php endforeach; ?>
                                </fieldset>
                                <p class="description" style="margin-top: 15px;">
                                    <strong><?php _e( 'หมายเหตุ:', 'trendtoday' ); ?></strong><br>
                                    <?php _e( '• Widgets ที่ถูกเลือกจะแสดงใน Appearance > Widgets และสามารถเพิ่มไปยัง Widget Areas ต่างๆ ได้', 'trendtoday' ); ?><br>
                                    <?php _e( '• Widgets ที่ไม่ถูกเลือกจะไม่แสดงใน Widgets area แต่โค้ดยังคงทำงานอยู่', 'trendtoday' ); ?><br>
                                    <?php _e( '• การเปลี่ยนแปลงจะมีผลหลังจากบันทึกการตั้งค่า', 'trendtoday' ); ?>
                                </p>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <!-- Submit Button -->
            <div class="trendtoday-settings-footer">
                <?php submit_button( __( 'Save Settings', 'trendtoday' ), 'primary large', 'trendtoday_save_settings', false ); ?>
            </div>
        </form>
    </div>
    
    <style>
    /* Trend Today Settings Styles */
    .trendtoday-settings-wrap {
        max-width: 1200px;
    }
    
    .trendtoday-nav-tabs {
        margin: 20px 0 0;
        border-bottom: 2px solid #c3c4c7;
    }
    
    .trendtoday-nav-tabs .nav-tab {
        padding: 12px 20px;
        font-size: 14px;
        font-weight: 600;
        border: none;
        border-bottom: 3px solid transparent;
        background: transparent;
        margin-right: 5px;
        margin-bottom: -2px;
        transition: all 0.3s ease;
    }
    
    .trendtoday-nav-tabs .nav-tab:hover {
        background: #f0f0f1;
        border-bottom-color: #2271b1;
    }
    
    .trendtoday-nav-tabs .nav-tab-active {
        border-bottom-color: #2271b1;
        color: #2271b1;
    }
    
    .trendtoday-nav-tabs .nav-tab .dashicons {
        margin-right: 5px;
        vertical-align: middle;
    }
    
    .trendtoday-tab-content {
        display: none;
        background: #fff;
        border: 1px solid #c3c4c7;
        border-top: none;
        padding: 20px;
        margin-top: -1px;
    }
    
    .trendtoday-tab-content.active {
        display: block;
    }
    
    .trendtoday-settings-section {
        margin-bottom: 30px;
    }
    
    .trendtoday-section-title {
        font-size: 18px;
        font-weight: 600;
        margin: 0 0 10px;
        padding-bottom: 10px;
        border-bottom: 2px solid #f0f0f1;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .trendtoday-section-title .dashicons {
        color: #2271b1;
    }
    
    .trendtoday-section-description {
        color: #646970;
        margin: 0 0 20px;
        font-size: 14px;
    }
    
    /* Logo Upload */
    .trendtoday-logo-preview {
        margin-bottom: 15px;
        padding: 15px;
        background: #f9f9f9;
        border: 1px solid #ddd;
        border-radius: 4px;
        display: inline-block;
    }
    
    .trendtoday-logo-preview img {
        max-width: 200px;
        height: auto;
        display: block;
    }
    
    .trendtoday-logo-placeholder {
        width: 200px;
        height: 100px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background: #f0f0f1;
        border: 2px dashed #c3c4c7;
        border-radius: 4px;
        color: #646970;
    }
    
    .trendtoday-logo-placeholder .dashicons {
        font-size: 32px;
        width: 32px;
        height: 32px;
        margin-bottom: 5px;
    }
    
    .trendtoday-logo-placeholder p {
        margin: 0;
        font-size: 12px;
    }
    
    .trendtoday-logo-actions {
        display: flex;
        gap: 10px;
    }
    
    .trendtoday-logo-actions .button .dashicons {
        margin-right: 5px;
    }
    
    /* Radio Options */
    .trendtoday-radio-group {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
    }
    
    .trendtoday-radio-option {
        display: flex;
        align-items: center;
        padding: 15px 20px;
        border: 2px solid #ddd;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.3s ease;
        background: #fff;
        min-width: 200px;
    }
    
    .trendtoday-radio-option:hover {
        border-color: #2271b1;
        background: #f0f6fc;
    }
    
    .trendtoday-radio-option input[type="radio"] {
        margin-right: 12px;
    }
    
    .trendtoday-radio-option input[type="radio"]:checked + .radio-label {
        color: #2271b1;
    }
    
    .trendtoday-radio-option:has(input:checked) {
        border-color: #2271b1;
        background: #f0f6fc;
    }
    
    .radio-label {
        display: flex;
        flex-direction: column;
    }
    
    .radio-label strong {
        font-size: 14px;
        margin-bottom: 3px;
    }
    
    .radio-label small {
        font-size: 12px;
        color: #646970;
    }
    
    /* Toggle Switch */
    .trendtoday-toggle {
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
    }
    
    .trendtoday-toggle input[type="checkbox"] {
        display: none;
    }
    
    .toggle-slider {
        position: relative;
        width: 50px;
        height: 26px;
        background: #c3c4c7;
        border-radius: 13px;
        transition: background 0.3s ease;
    }
    
    .toggle-slider::before {
        content: '';
        position: absolute;
        width: 20px;
        height: 20px;
        background: #fff;
        border-radius: 50%;
        top: 3px;
        left: 3px;
        transition: transform 0.3s ease;
    }
    
    .trendtoday-toggle input:checked + .toggle-slider {
        background: #2271b1;
    }
    
    .trendtoday-toggle input:checked + .toggle-slider::before {
        transform: translateX(24px);
    }
    
    /* Platforms Grid */
    .trendtoday-platforms-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 15px;
        margin-bottom: 15px;
    }
    
    .trendtoday-platform-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 20px;
        border: 2px solid #ddd;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        background: #fff;
        text-align: center;
    }
    
    .trendtoday-platform-item:hover {
        border-color: #2271b1;
        background: #f0f6fc;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .trendtoday-platform-item input[type="checkbox"] {
        display: none;
    }
    
    .trendtoday-platform-item:has(input:checked) {
        border-color: #2271b1;
        background: #f0f6fc;
    }
    
    .platform-icon {
        font-size: 32px;
        margin-bottom: 10px;
        color: #646970;
        transition: color 0.3s ease;
    }
    
    .trendtoday-platform-item:has(input:checked) .platform-icon {
        color: #2271b1;
    }
    
    .platform-name {
        font-size: 13px;
        font-weight: 500;
        color: #1d2327;
    }
    
    /* Positions Grid */
    .trendtoday-positions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 15px;
        margin-bottom: 15px;
    }
    
    .trendtoday-position-item {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 15px;
        border: 2px solid #ddd;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        background: #fff;
    }
    
    .trendtoday-position-item:hover {
        border-color: #2271b1;
        background: #f0f6fc;
    }
    
    .trendtoday-position-item input[type="checkbox"] {
        margin: 0;
    }
    
    .trendtoday-position-item:has(input:checked) {
        border-color: #2271b1;
        background: #f0f6fc;
    }
    
    .position-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f0f0f1;
        border-radius: 50%;
        color: #646970;
    }
    
    .trendtoday-position-item:has(input:checked) .position-icon {
        background: #2271b1;
        color: #fff;
    }
    
    .position-label {
        display: flex;
        flex-direction: column;
    }
    
    .position-label strong {
        font-size: 14px;
        margin-bottom: 3px;
    }
    
    .position-label small {
        font-size: 12px;
        color: #646970;
    }
    
    /* Settings Footer */
    .trendtoday-settings-footer {
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #c3c4c7;
    }
    
    @media (max-width: 782px) {
        .trendtoday-platforms-grid,
        .trendtoday-positions-grid {
            grid-template-columns: 1fr;
        }
        
        .trendtoday-radio-group {
            flex-direction: column;
        }
    }
    </style>
    
    <script type="text/javascript">
    (function($) {
        'use strict';
        
        var logoUploader;
        
        // Wait for wp.media to be available
        function initLogoUploader() {
            if (typeof wp === 'undefined' || typeof wp.media === 'undefined') {
                setTimeout(initLogoUploader, 100);
                return;
            }
            
            $('#trendtoday_upload_logo_btn').off('click').on('click', function(e) {
                e.preventDefault();
                
                // If the uploader object has already been created, reopen it
                if (logoUploader) {
                    logoUploader.open();
                    return;
                }
                
                // Create the media uploader
                logoUploader = wp.media({
                    title: '<?php echo esc_js( __( 'Choose Logo', 'trendtoday' ) ); ?>',
                    button: {
                        text: '<?php echo esc_js( __( 'Use this logo', 'trendtoday' ) ); ?>'
                    },
                    library: {
                        type: 'image'
                    },
                    multiple: false
                });
                
                // When an image is selected, run a callback
                logoUploader.on('select', function() {
                    var attachment = logoUploader.state().get('selection').first().toJSON();
                    $('#trendtoday_logo').val(attachment.id);
                    $('#trendtoday_logo_preview').html('<img src="' + attachment.url + '" style="max-width: 200px; height: auto; display: block; margin-bottom: 10px;" />');
                    $('#trendtoday_upload_logo_btn').text('<?php echo esc_js( __( 'Change Logo', 'trendtoday' ) ); ?>');
                    if ($('#trendtoday_remove_logo_btn').length === 0) {
                        $('#trendtoday_upload_logo_btn').after('<button type="button" class="button" id="trendtoday_remove_logo_btn" style="margin-left: 10px;"><?php echo esc_js( __( 'Remove Logo', 'trendtoday' ) ); ?></button>');
                    }
                });
                
                // Open the uploader
                logoUploader.open();
            });
            
            // Remove logo
            $(document).off('click', '#trendtoday_remove_logo_btn').on('click', '#trendtoday_remove_logo_btn', function(e) {
                e.preventDefault();
                $('#trendtoday_logo').val('');
                $('#trendtoday_logo_preview').html('');
                $('#trendtoday_upload_logo_btn').text('<?php echo esc_js( __( 'Upload Logo', 'trendtoday' ) ); ?>');
                $(this).remove();
            });
        }
        
        // Initialize when DOM is ready
        $(document).ready(function() {
            initLogoUploader();
            
            // Tab switching
            $('.trendtoday-nav-tabs .nav-tab').on('click', function(e) {
                e.preventDefault();
                var targetTab = $(this).data('tab');
                
                // Update hidden input
                $('#trendtoday_active_tab').val(targetTab);
                
                // Update URL hash without triggering hashchange
                if (history.pushState) {
                    history.pushState(null, null, '#' + targetTab);
                } else {
                    window.location.hash = '#' + targetTab;
                }
                
                // Update nav tabs
                $('.trendtoday-nav-tabs .nav-tab').removeClass('nav-tab-active');
                $(this).addClass('nav-tab-active');
                
                // Update tab content
                $('.trendtoday-tab-content').removeClass('active');
                $('#' + targetTab + '-tab').addClass('active');
            });
            
            // Restore active tab on page load
            // First check URL hash
            var hash = window.location.hash.replace('#', '');
            var activeTab = hash || $('#trendtoday_active_tab').val() || 'general';
            
            // Update hidden input
            $('#trendtoday_active_tab').val(activeTab);
            
            // Switch to the correct tab
            if (activeTab && activeTab !== 'general') {
                $('.trendtoday-nav-tabs .nav-tab[data-tab="' + activeTab + '"]').trigger('click');
            }
            
            // Handle hash changes (when clicking tabs)
            $(window).on('hashchange', function() {
                var newHash = window.location.hash.replace('#', '');
                if (newHash) {
                    var tabElement = $('.trendtoday-nav-tabs .nav-tab[data-tab="' + newHash + '"]');
                    if (tabElement.length) {
                        tabElement.trigger('click');
                    }
                }
            });
            
            // Regenerate Images Handler
            $('#trendtoday-regenerate-images').on('click', function(e) {
                e.preventDefault();
                
                if (!confirm('<?php echo esc_js( __( 'This will regenerate all images. This may take a long time. Continue?', 'trendtoday' ) ); ?>')) {
                    return;
                }
                
                var $button = $(this);
                var $progress = $('#trendtoday-regenerate-progress');
                var $progressBar = $('#trendtoday-regenerate-progress-bar');
                var $status = $('#trendtoday-regenerate-status');
                
                $button.prop('disabled', true).text('<?php echo esc_js( __( 'Processing...', 'trendtoday' ) ); ?>');
                $progress.show();
                $progressBar.css('width', '0%');
                $status.text('<?php echo esc_js( __( 'Initializing...', 'trendtoday' ) ); ?>');
                
                // Get total images
                $.ajax({
                    url: (typeof trendtodayAdmin !== 'undefined' && trendtodayAdmin.ajaxurl) ? trendtodayAdmin.ajaxurl : ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'regenerate_images',
                        action_type: 'get_total',
                        nonce: (typeof trendtodayAdmin !== 'undefined' && trendtodayAdmin.nonce) ? trendtodayAdmin.nonce : '<?php echo wp_create_nonce( 'trendtoday_settings_nonce' ); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            var total = response.data.total;
                            var processed = 0;
                            var offset = 0;
                            var batchSize = 5;
                            
                            function processBatch() {
                                $.ajax({
                                    url: (typeof trendtodayAdmin !== 'undefined' && trendtodayAdmin.ajaxurl) ? trendtodayAdmin.ajaxurl : ajaxurl,
                                    type: 'POST',
                                    data: {
                                        action: 'regenerate_images',
                                        action_type: 'regenerate',
                                        offset: offset,
                                        batch_size: batchSize,
                                        nonce: (typeof trendtodayAdmin !== 'undefined' && trendtodayAdmin.nonce) ? trendtodayAdmin.nonce : '<?php echo wp_create_nonce( 'trendtoday_settings_nonce' ); ?>'
                                    },
                                    success: function(response) {
                                        if (response.success) {
                                            processed += response.data.processed;
                                            offset = response.data.offset;
                                            
                                            var percent = Math.round((processed / total) * 100);
                                            $progressBar.css('width', percent + '%');
                                            $status.text('<?php echo esc_js( __( 'Processing', 'trendtoday' ) ); ?>: ' + processed + ' / ' + total);
                                            
                                            if (offset < total) {
                                                // Continue processing
                                                setTimeout(processBatch, 500);
                                            } else {
                                                // Done
                                                $progressBar.css('width', '100%');
                                                $status.text('<?php echo esc_js( __( 'Completed! Processed', 'trendtoday' ) ); ?> ' + processed + ' <?php echo esc_js( __( 'images', 'trendtoday' ) ); ?>');
                                                $button.prop('disabled', false).html('<span class="dashicons dashicons-update" style="vertical-align: middle;"></span> <?php echo esc_js( __( 'Regenerate All Images', 'trendtoday' ) ); ?>');
                                                
                                                // Show errors if any
                                                if (response.data.errors && response.data.errors.length > 0) {
                                                    alert('<?php echo esc_js( __( 'Some errors occurred:', 'trendtoday' ) ); ?>\n' + response.data.errors.join('\n'));
                                                }
                                            }
                                        } else {
                                            alert('<?php echo esc_js( __( 'Error:', 'trendtoday' ) ); ?> ' + response.data.message);
                                            $button.prop('disabled', false).html('<span class="dashicons dashicons-update" style="vertical-align: middle;"></span> <?php echo esc_js( __( 'Regenerate All Images', 'trendtoday' ) ); ?>');
                                        }
                                    },
                                    error: function() {
                                        alert('<?php echo esc_js( __( 'An error occurred while processing images.', 'trendtoday' ) ); ?>');
                                        $button.prop('disabled', false).html('<span class="dashicons dashicons-update" style="vertical-align: middle;"></span> <?php echo esc_js( __( 'Regenerate All Images', 'trendtoday' ) ); ?>');
                                    }
                                });
                            }
                            
                            processBatch();
                        } else {
                            alert('<?php echo esc_js( __( 'Error:', 'trendtoday' ) ); ?> ' + response.data.message);
                            $button.prop('disabled', false).html('<span class="dashicons dashicons-update" style="vertical-align: middle;"></span> <?php echo esc_js( __( 'Regenerate All Images', 'trendtoday' ) ); ?>');
                        }
                    },
                    error: function() {
                        alert('<?php echo esc_js( __( 'An error occurred while getting image count.', 'trendtoday' ) ); ?>');
                        $button.prop('disabled', false).html('<span class="dashicons dashicons-update" style="vertical-align: middle;"></span> <?php echo esc_js( __( 'Regenerate All Images', 'trendtoday' ) ); ?>');
                    }
                });
            });
        });
    })(jQuery);
    </script>
    <?php
}

/**
 * Register Custom Post Types
 */
function trendtoday_register_post_types() {
    // Video News Post Type - under Trend Today menu
    register_post_type(
        'video_news',
        array(
            'labels'              => array(
                'name'               => __( 'Video News', 'trendtoday' ),
                'singular_name'      => __( 'Video News', 'trendtoday' ),
                'menu_name'          => __( 'Video News', 'trendtoday' ),
                'add_new'            => __( 'Add New', 'trendtoday' ),
                'add_new_item'       => __( 'Add New Video News', 'trendtoday' ),
                'edit_item'          => __( 'Edit Video News', 'trendtoday' ),
                'new_item'           => __( 'New Video News', 'trendtoday' ),
                'view_item'          => __( 'View Video News', 'trendtoday' ),
                'search_items'       => __( 'Search Video News', 'trendtoday' ),
                'not_found'          => __( 'No video news found', 'trendtoday' ),
                'not_found_in_trash' => __( 'No video news found in Trash', 'trendtoday' ),
                'all_items'          => __( 'All Video News', 'trendtoday' ),
            ),
            'public'              => true,
            'has_archive'         => true,
            'publicly_queryable'  => true,
            'show_ui'             => true,
            'show_in_menu'        => 'trendtoday', // Parent menu slug
            'show_in_nav_menus'   => true,
            'show_in_admin_bar'   => true,
            'menu_icon'           => 'dashicons-video-alt3',
            'capability_type'     => 'post',
            'hierarchical'        => false,
            'supports'            => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments', 'custom-fields', 'revisions' ),
            'rewrite'             => array( 'slug' => 'video-news', 'with_front' => false ),
            'query_var'           => true,
            'can_export'          => true,
            'show_in_rest'        => true,
            'rest_base'           => 'video-news',
        )
    );

    // Gallery Post Type - under Trend Today menu
    register_post_type(
        'gallery',
        array(
            'labels'              => array(
                'name'               => __( 'Photo Galleries', 'trendtoday' ),
                'singular_name'      => __( 'Photo Gallery', 'trendtoday' ),
                'menu_name'          => __( 'Photo Galleries', 'trendtoday' ),
                'add_new'            => __( 'Add New', 'trendtoday' ),
                'add_new_item'       => __( 'Add New Gallery', 'trendtoday' ),
                'edit_item'          => __( 'Edit Gallery', 'trendtoday' ),
                'new_item'           => __( 'New Gallery', 'trendtoday' ),
                'view_item'          => __( 'View Gallery', 'trendtoday' ),
                'search_items'       => __( 'Search Galleries', 'trendtoday' ),
                'not_found'          => __( 'No galleries found', 'trendtoday' ),
                'not_found_in_trash' => __( 'No galleries found in Trash', 'trendtoday' ),
                'all_items'          => __( 'All Galleries', 'trendtoday' ),
            ),
            'public'              => true,
            'has_archive'         => true,
            'publicly_queryable'  => true,
            'show_ui'             => true,
            'show_in_menu'        => 'trendtoday', // Parent menu slug
            'show_in_nav_menus'   => true,
            'show_in_admin_bar'   => true,
            'menu_icon'           => 'dashicons-format-gallery',
            'capability_type'     => 'post',
            'hierarchical'        => false,
            'supports'            => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments', 'custom-fields', 'revisions' ),
            'rewrite'             => array( 'slug' => 'gallery', 'with_front' => false ),
            'query_var'           => true,
            'can_export'          => true,
            'show_in_rest'        => true,
            'rest_base'           => 'galleries',
        )
    );

    // Featured Story Post Type - under Trend Today menu
    register_post_type(
        'featured_story',
        array(
            'labels'              => array(
                'name'               => __( 'Featured Stories', 'trendtoday' ),
                'singular_name'      => __( 'Featured Story', 'trendtoday' ),
                'menu_name'          => __( 'Featured Stories', 'trendtoday' ),
                'add_new'            => __( 'Add New', 'trendtoday' ),
                'add_new_item'       => __( 'Add New Featured Story', 'trendtoday' ),
                'edit_item'          => __( 'Edit Featured Story', 'trendtoday' ),
                'new_item'           => __( 'New Featured Story', 'trendtoday' ),
                'view_item'          => __( 'View Featured Story', 'trendtoday' ),
                'search_items'       => __( 'Search Featured Stories', 'trendtoday' ),
                'not_found'          => __( 'No featured stories found', 'trendtoday' ),
                'not_found_in_trash' => __( 'No featured stories found in Trash', 'trendtoday' ),
                'all_items'          => __( 'All Featured Stories', 'trendtoday' ),
            ),
            'public'              => true,
            'has_archive'         => true,
            'publicly_queryable'  => true,
            'show_ui'             => true,
            'show_in_menu'        => 'trendtoday', // Parent menu slug
            'show_in_nav_menus'   => true,
            'show_in_admin_bar'   => true,
            'menu_icon'           => 'dashicons-star-filled',
            'capability_type'     => 'post',
            'hierarchical'        => false,
            'supports'            => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments', 'custom-fields', 'revisions' ),
            'rewrite'             => array( 'slug' => 'featured', 'with_front' => false ),
            'query_var'           => true,
            'can_export'          => true,
            'show_in_rest'        => true,
            'rest_base'           => 'featured-stories',
        )
    );
}
add_action( 'init', 'trendtoday_register_post_types' );

/**
 * Flush rewrite rules on theme activation
 */
function trendtoday_flush_rewrite_rules() {
    trendtoday_register_post_types();
    flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'trendtoday_flush_rewrite_rules' );

/**
 * Register Custom Taxonomies for Custom Post Types
 */
function trendtoday_register_taxonomies() {
    // Video Category Taxonomy
    register_taxonomy(
        'video_category',
        array( 'video_news' ),
        array(
            'labels'            => array(
                'name'              => __( 'Video Categories', 'trendtoday' ),
                'singular_name'     => __( 'Video Category', 'trendtoday' ),
                'search_items'      => __( 'Search Video Categories', 'trendtoday' ),
                'all_items'         => __( 'All Video Categories', 'trendtoday' ),
                'parent_item'       => __( 'Parent Video Category', 'trendtoday' ),
                'parent_item_colon' => __( 'Parent Video Category:', 'trendtoday' ),
                'edit_item'         => __( 'Edit Video Category', 'trendtoday' ),
                'update_item'       => __( 'Update Video Category', 'trendtoday' ),
                'add_new_item'      => __( 'Add New Video Category', 'trendtoday' ),
                'new_item_name'     => __( 'New Video Category Name', 'trendtoday' ),
                'menu_name'         => __( 'Video Categories', 'trendtoday' ),
            ),
            'hierarchical'      => true,
            'show_ui'            => true,
            'show_admin_column'  => true,
            'query_var'          => true,
            'rewrite'            => array( 'slug' => 'video-category' ),
            'show_in_rest'        => true,
        )
    );

    // Gallery Category Taxonomy
    register_taxonomy(
        'gallery_category',
        array( 'gallery' ),
        array(
            'labels'            => array(
                'name'              => __( 'Gallery Categories', 'trendtoday' ),
                'singular_name'     => __( 'Gallery Category', 'trendtoday' ),
                'search_items'      => __( 'Search Gallery Categories', 'trendtoday' ),
                'all_items'         => __( 'All Gallery Categories', 'trendtoday' ),
                'parent_item'       => __( 'Parent Gallery Category', 'trendtoday' ),
                'parent_item_colon' => __( 'Parent Gallery Category:', 'trendtoday' ),
                'edit_item'         => __( 'Edit Gallery Category', 'trendtoday' ),
                'update_item'       => __( 'Update Gallery Category', 'trendtoday' ),
                'add_new_item'      => __( 'Add New Gallery Category', 'trendtoday' ),
                'new_item_name'     => __( 'New Gallery Category Name', 'trendtoday' ),
                'menu_name'         => __( 'Gallery Categories', 'trendtoday' ),
            ),
            'hierarchical'      => true,
            'show_ui'            => true,
            'show_admin_column'  => true,
            'query_var'          => true,
            'rewrite'            => array( 'slug' => 'gallery-category' ),
            'show_in_rest'        => true,
        )
    );
}
add_action( 'init', 'trendtoday_register_taxonomies' );

/**
 * Add custom columns to post type admin lists
 */
function trendtoday_add_custom_columns( $columns ) {
    // Add thumbnail column at the beginning for all post types
    $new_columns = array();
    
    // Add checkbox column first
    if ( isset( $columns['cb'] ) ) {
        $new_columns['cb'] = $columns['cb'];
        unset( $columns['cb'] );
    }
    
    // Add thumbnail column after checkbox
    $new_columns['featured_image'] = __( 'Image', 'trendtoday' );
    
    // Merge with existing columns
    $columns = array_merge( $new_columns, $columns );
    
    // Add custom columns for posts
    if ( ! isset( $_GET['post_type'] ) || $_GET['post_type'] === 'post' ) {
        $columns['post_views']    = __( 'Views', 'trendtoday' );
    }

    // Add custom columns for video_news
    if ( isset( $_GET['post_type'] ) && $_GET['post_type'] === 'video_news' ) {
        $columns['video_url'] = __( 'Video URL', 'trendtoday' );
        $columns['duration']  = __( 'Duration', 'trendtoday' );
    }

    // Add custom columns for gallery
    if ( isset( $_GET['post_type'] ) && $_GET['post_type'] === 'gallery' ) {
        $columns['image_count'] = __( 'Images', 'trendtoday' );
    }

    return $columns;
}
add_filter( 'manage_posts_columns', 'trendtoday_add_custom_columns' );
add_filter( 'manage_pages_columns', 'trendtoday_add_custom_columns' );

/**
 * Display custom column content
 */
function trendtoday_custom_column_content( $column, $post_id ) {
    switch ( $column ) {
        case 'featured_image':
            if ( has_post_thumbnail( $post_id ) ) {
                $thumbnail = get_the_post_thumbnail( $post_id, array( 60, 60 ), array( 'style' => 'width: 60px; height: 60px; object-fit: cover; border-radius: 4px;' ) );
                $edit_link = get_edit_post_link( $post_id );
                echo '<a href="' . esc_url( $edit_link ) . '">' . $thumbnail . '</a>';
            } else {
                echo '<span style="display: inline-block; width: 60px; height: 60px; background: #f0f0f0; border-radius: 4px; text-align: center; line-height: 60px; color: #999; font-size: 11px;">' . __( 'No image', 'trendtoday' ) . '</span>';
            }
            break;

        case 'post_views':
            $views = get_post_meta( $post_id, 'post_views', true );
            echo $views ? number_format_i18n( $views ) : '0';
            break;

        case 'video_url':
            $video_url = get_post_meta( $post_id, 'video_url', true );
            echo $video_url ? esc_html( $video_url ) : '—';
            break;

        case 'duration':
            $duration = get_post_meta( $post_id, 'video_duration', true );
            echo $duration ? esc_html( $duration ) : '—';
            break;

        case 'image_count':
            $gallery_images = get_post_meta( $post_id, 'gallery_images', true );
            $count = is_array( $gallery_images ) ? count( $gallery_images ) : 0;
            echo $count > 0 ? esc_html( $count ) : '—';
            break;
    }
}
add_action( 'manage_posts_custom_column', 'trendtoday_custom_column_content', 10, 2 );
add_action( 'manage_pages_custom_column', 'trendtoday_custom_column_content', 10, 2 );

/**
 * Make custom columns sortable
 */
function trendtoday_make_columns_sortable( $columns ) {
    if ( isset( $_GET['post_type'] ) && $_GET['post_type'] === 'video_news' ) {
        $columns['duration'] = 'duration';
    }
    if ( isset( $_GET['post_type'] ) && $_GET['post_type'] === 'gallery' ) {
        $columns['image_count'] = 'image_count';
    }
    return $columns;
}
add_filter( 'manage_edit-video_news_sortable_columns', 'trendtoday_make_columns_sortable' );
add_filter( 'manage_edit-gallery_sortable_columns', 'trendtoday_make_columns_sortable' );
