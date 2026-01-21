<?php
/**
 * Admin Menu
 *
 * @package KonDernTang
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Add admin menu
 */
function konderntang_add_admin_menu() {
    // Main menu page
    add_menu_page(
        esc_html__( 'KonDernTang', 'konderntang' ),
        esc_html__( 'KonDernTang', 'konderntang' ),
        'edit_posts',
        'konderntang',
        'konderntang_admin_page',
        'dashicons-admin-site-alt3',
        5
    );
    
    // Dashboard submenu (default page)
    add_submenu_page(
        'konderntang',
        esc_html__( 'Dashboard', 'konderntang' ),
        esc_html__( 'Dashboard', 'konderntang' ),
        'edit_posts',
        'konderntang',
        'konderntang_admin_page'
    );
    
    // Theme Settings submenu
    add_submenu_page(
        'konderntang',
        esc_html__( 'Theme Settings', 'konderntang' ),
        esc_html__( 'Theme Settings', 'konderntang' ),
        'manage_options',
        'konderntang-settings',
        'konderntang_settings_page'
    );
    
    // Documentation submenu
    add_submenu_page(
        'konderntang',
        esc_html__( 'Documentation', 'konderntang' ),
        esc_html__( 'Documentation', 'konderntang' ),
        'edit_posts',
        'konderntang-docs',
        'konderntang_docs_page'
    );
}
add_action( 'admin_menu', 'konderntang_add_admin_menu', 9 );

/**
 * Admin page callback for KonDernTang menu
 */
function konderntang_admin_page() {
    if ( ! current_user_can( 'edit_posts' ) ) {
        wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'konderntang' ) );
    }
    
    // Get statistics
    $post_count = wp_count_posts();
    $page_count = wp_count_posts( 'page' );
    
    $category_count = wp_count_terms(
        array(
            'taxonomy'   => 'category',
            'hide_empty' => false,
        )
    );
    if ( is_wp_error( $category_count ) ) {
        $category_count = 0;
    }
    
    $tag_count = wp_count_terms(
        array(
            'taxonomy'   => 'post_tag',
            'hide_empty' => false,
        )
    );
    if ( is_wp_error( $tag_count ) ) {
        $tag_count = 0;
    }
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
    ?>
    <div class="wrap konderntang-dashboard-wrap">
        <h1 class="konderntang-dashboard-title">
            <span class="dashicons dashicons-admin-site-alt3"></span>
            <?php echo esc_html( get_admin_page_title() ); ?>
        </h1>
        
        <div class="konderntang-dashboard-widgets">
            <!-- Content Statistics -->
            <div class="konderntang-widget">
                <div class="konderntang-widget-header">
                    <h3>
                        <span class="dashicons dashicons-chart-bar"></span>
                        <?php esc_html_e( 'Content Statistics', 'konderntang' ); ?>
                    </h3>
                </div>
                <div class="konderntang-widget-content">
                    <div class="konderntang-stats-grid">
                        <div class="konderntang-stat-item">
                            <div class="stat-icon" style="background: #0ea5e9;">
                                <span class="dashicons dashicons-admin-post"></span>
                            </div>
                            <div class="stat-info">
                                <div class="stat-number"><?php echo number_format_i18n( $post_count->publish ); ?></div>
                                <div class="stat-label"><?php esc_html_e( 'บทความ', 'konderntang' ); ?></div>
                            </div>
                        </div>
                        <div class="konderntang-stat-item">
                            <div class="stat-icon" style="background: #f97316;">
                                <span class="dashicons dashicons-admin-page"></span>
                            </div>
                            <div class="stat-info">
                                <div class="stat-number"><?php echo number_format_i18n( $page_count->publish ); ?></div>
                                <div class="stat-label"><?php esc_html_e( 'หน้า', 'konderntang' ); ?></div>
                            </div>
                        </div>
                        <div class="konderntang-stat-item">
                            <div class="stat-icon" style="background: #10b981;">
                                <span class="dashicons dashicons-category"></span>
                            </div>
                            <div class="stat-info">
                                <div class="stat-number"><?php echo number_format_i18n( $category_count ); ?></div>
                                <div class="stat-label"><?php esc_html_e( 'หมวดหมู่', 'konderntang' ); ?></div>
                            </div>
                        </div>
                        <div class="konderntang-stat-item">
                            <div class="stat-icon" style="background: #8b5cf6;">
                                <span class="dashicons dashicons-tag"></span>
                            </div>
                            <div class="stat-info">
                                <div class="stat-number"><?php echo number_format_i18n( $tag_count ); ?></div>
                                <div class="stat-label"><?php esc_html_e( 'แท็ก', 'konderntang' ); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recent Posts -->
            <div class="konderntang-widget">
                <div class="konderntang-widget-header">
                    <h3>
                        <span class="dashicons dashicons-clock"></span>
                        <?php esc_html_e( 'Recent Posts', 'konderntang' ); ?>
                    </h3>
                </div>
                <div class="konderntang-widget-content">
                    <?php if ( ! empty( $recent_posts ) ) : ?>
                        <ul class="konderntang-recent-posts">
                            <?php foreach ( $recent_posts as $post ) : setup_postdata( $post ); ?>
                                <li>
                                    <a href="<?php echo get_edit_post_link( $post->ID ); ?>">
                                        <strong><?php echo esc_html( get_the_title( $post->ID ) ); ?></strong>
                                        <span class="post-date">
                                            <?php echo human_time_diff( get_the_time( 'U', $post->ID ), current_time( 'timestamp' ) ) . ' ' . esc_html__( 'ago', 'konderntang' ); ?>
                                        </span>
                                    </a>
                                </li>
                            <?php endforeach; wp_reset_postdata(); ?>
                        </ul>
                    <?php else : ?>
                        <p><?php esc_html_e( 'ยังไม่มีบทความ', 'konderntang' ); ?></p>
                    <?php endif; ?>
                    <p style="margin-top: 15px;">
                        <a href="<?php echo esc_url( admin_url( 'edit.php' ) ); ?>" class="button button-small">
                            <?php esc_html_e( 'ดูบทความทั้งหมด', 'konderntang' ); ?>
                        </a>
                        <a href="<?php echo esc_url( admin_url( 'post-new.php' ) ); ?>" class="button button-small">
                            <?php esc_html_e( 'เขียนบทความใหม่', 'konderntang' ); ?>
                        </a>
                    </p>
                </div>
            </div>
            
            <!-- System Information -->
            <div class="konderntang-widget">
                <div class="konderntang-widget-header">
                    <h3>
                        <span class="dashicons dashicons-info"></span>
                        <?php esc_html_e( 'System Information', 'konderntang' ); ?>
                    </h3>
                </div>
                <div class="konderntang-widget-content">
                    <ul class="konderntang-system-info">
                        <li>
                            <strong><?php esc_html_e( 'Theme Version:', 'konderntang' ); ?></strong>
                            <span><?php echo esc_html( $theme_version ); ?></span>
                        </li>
                        <li>
                            <strong><?php esc_html_e( 'WordPress Version:', 'konderntang' ); ?></strong>
                            <span><?php echo esc_html( $wp_version ); ?></span>
                        </li>
                        <li>
                            <strong><?php esc_html_e( 'PHP Version:', 'konderntang' ); ?></strong>
                            <span><?php echo esc_html( $php_version ); ?></span>
                        </li>
                        <li>
                            <strong><?php esc_html_e( 'Memory Limit:', 'konderntang' ); ?></strong>
                            <span><?php echo esc_html( $memory_limit ); ?></span>
                        </li>
                        <li>
                            <strong><?php esc_html_e( 'Active Plugins:', 'konderntang' ); ?></strong>
                            <span><?php echo number_format_i18n( $active_plugins_count ); ?></span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Quick Links -->
            <div class="konderntang-widget">
                <div class="konderntang-widget-header">
                    <h3>
                        <span class="dashicons dashicons-admin-links"></span>
                        <?php esc_html_e( 'Quick Links', 'konderntang' ); ?>
                    </h3>
                </div>
                <div class="konderntang-widget-content">
                    <ul class="konderntang-stats-list">
                        <li>
                            <a href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>">
                                <span class="dashicons dashicons-admin-appearance"></span>
                                <strong><?php esc_html_e( 'Customize Theme', 'konderntang' ); ?></strong>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo esc_url( admin_url( 'admin.php?page=konderntang-settings' ) ); ?>">
                                <span class="dashicons dashicons-admin-settings"></span>
                                <strong><?php esc_html_e( 'Theme Settings', 'konderntang' ); ?></strong>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo esc_url( admin_url( 'widgets.php' ) ); ?>">
                                <span class="dashicons dashicons-welcome-widgets-menus"></span>
                                <strong><?php esc_html_e( 'Manage Widgets', 'konderntang' ); ?></strong>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo esc_url( admin_url( 'nav-menus.php' ) ); ?>">
                                <span class="dashicons dashicons-menu"></span>
                                <strong><?php esc_html_e( 'Manage Menus', 'konderntang' ); ?></strong>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank">
                                <span class="dashicons dashicons-external"></span>
                                <strong><?php esc_html_e( 'View Site', 'konderntang' ); ?></strong>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Settings page callback
 */
function konderntang_settings_page() {
    require_once KONDERN_THEME_DIR . '/inc/admin-settings.php';
    konderntang_settings_page_render();
}

/**
 * Documentation page callback
 */
function konderntang_docs_page() {
    if ( ! current_user_can( 'edit_posts' ) ) {
        wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'konderntang' ) );
    }
    ?>
    <div class="wrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
        
        <div class="konderntang-docs" style="background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 8px; margin-top: 20px;">
            <h2><?php esc_html_e( 'Theme Documentation', 'konderntang' ); ?></h2>
            
            <h3><?php esc_html_e( 'Getting Started', 'konderntang' ); ?></h3>
            <ol>
                <li><?php esc_html_e( 'Go to Appearance → Customize to configure theme settings', 'konderntang' ); ?></li>
                <li><?php esc_html_e( 'Set up your menus at Appearance → Menus', 'konderntang' ); ?></li>
                <li><?php esc_html_e( 'Configure widgets at Appearance → Widgets', 'konderntang' ); ?></li>
                <li><?php esc_html_e( 'Create your first post or page', 'konderntang' ); ?></li>
            </ol>
            
            <h3><?php esc_html_e( 'Theme Features', 'konderntang' ); ?></h3>
            <ul>
                <li><?php esc_html_e( 'Component-based architecture', 'konderntang' ); ?></li>
                <li><?php esc_html_e( 'Customizable homepage sections', 'konderntang' ); ?></li>
                <li><?php esc_html_e( 'Hero slider with auto-play', 'konderntang' ); ?></li>
                <li><?php esc_html_e( 'Multiple widget areas', 'konderntang' ); ?></li>
                <li><?php esc_html_e( 'Cookie consent banner', 'konderntang' ); ?></li>
                <li><?php esc_html_e( 'Responsive design', 'konderntang' ); ?></li>
            </ul>
            
            <h3><?php esc_html_e( 'Support', 'konderntang' ); ?></h3>
            <p><?php esc_html_e( 'For more information, please refer to the theme documentation or contact support.', 'konderntang' ); ?></p>
        </div>
    </div>
    <?php
}

