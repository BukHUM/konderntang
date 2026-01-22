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
    
    // Custom Fields Cleaner submenu
    add_submenu_page(
        'konderntang',
        esc_html__( 'Custom Fields Cleaner', 'konderntang' ),
        esc_html__( 'Custom Fields Cleaner', 'konderntang' ),
        'manage_options',
        'konderntang-custom-fields-cleaner',
        'konderntang_custom_fields_cleaner_page'
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
    
    // Custom Post Types
    $travel_guide_count = wp_count_posts( 'travel_guide' );
    $hotel_count = wp_count_posts( 'hotel' );
    $promotion_count = wp_count_posts( 'promotion' );
    
    // Taxonomies
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
    
    $destination_count = wp_count_terms(
        array(
            'taxonomy'   => 'destination',
            'hide_empty' => false,
        )
    );
    if ( is_wp_error( $destination_count ) ) {
        $destination_count = 0;
    }
    
    $travel_type_count = wp_count_terms(
        array(
            'taxonomy'   => 'travel_type',
            'hide_empty' => false,
        )
    );
    if ( is_wp_error( $travel_type_count ) ) {
        $travel_type_count = 0;
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
                        <!-- Standard Content -->
                        <a href="<?php echo esc_url( admin_url( 'edit.php' ) ); ?>" class="konderntang-stat-item">
                            <div class="stat-icon" style="background: #0ea5e9;">
                                <span class="dashicons dashicons-admin-post"></span>
                            </div>
                            <div class="stat-info">
                                <div class="stat-number"><?php echo number_format_i18n( $post_count->publish ); ?></div>
                                <div class="stat-label"><?php esc_html_e( 'บทความ', 'konderntang' ); ?></div>
                            </div>
                        </a>
                        <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=page' ) ); ?>" class="konderntang-stat-item">
                            <div class="stat-icon" style="background: #f97316;">
                                <span class="dashicons dashicons-admin-page"></span>
                            </div>
                            <div class="stat-info">
                                <div class="stat-number"><?php echo number_format_i18n( $page_count->publish ); ?></div>
                                <div class="stat-label"><?php esc_html_e( 'หน้า', 'konderntang' ); ?></div>
                            </div>
                        </a>
                        
                        <!-- Custom Post Types -->
                        <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=travel_guide' ) ); ?>" class="konderntang-stat-item">
                            <div class="stat-icon" style="background: #14b8a6;">
                                <span class="dashicons dashicons-location-alt"></span>
                            </div>
                            <div class="stat-info">
                                <div class="stat-number"><?php echo number_format_i18n( $travel_guide_count->publish ); ?></div>
                                <div class="stat-label"><?php esc_html_e( 'Travel Guides', 'konderntang' ); ?></div>
                            </div>
                        </a>
                        <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=hotel' ) ); ?>" class="konderntang-stat-item">
                            <div class="stat-icon" style="background: #ec4899;">
                                <span class="dashicons dashicons-building"></span>
                            </div>
                            <div class="stat-info">
                                <div class="stat-number"><?php echo number_format_i18n( $hotel_count->publish ); ?></div>
                                <div class="stat-label"><?php esc_html_e( 'Hotels', 'konderntang' ); ?></div>
                            </div>
                        </a>
                        <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=promotion' ) ); ?>" class="konderntang-stat-item">
                            <div class="stat-icon" style="background: #f59e0b;">
                                <span class="dashicons dashicons-tag"></span>
                            </div>
                            <div class="stat-info">
                                <div class="stat-number"><?php echo number_format_i18n( $promotion_count->publish ); ?></div>
                                <div class="stat-label"><?php esc_html_e( 'Promotions', 'konderntang' ); ?></div>
                            </div>
                        </a>
                        
                        <!-- Taxonomies -->
                        <a href="<?php echo esc_url( admin_url( 'edit-tags.php?taxonomy=category' ) ); ?>" class="konderntang-stat-item">
                            <div class="stat-icon" style="background: #10b981;">
                                <span class="dashicons dashicons-category"></span>
                            </div>
                            <div class="stat-info">
                                <div class="stat-number"><?php echo number_format_i18n( $category_count ); ?></div>
                                <div class="stat-label"><?php esc_html_e( 'หมวดหมู่', 'konderntang' ); ?></div>
                            </div>
                        </a>
                        <a href="<?php echo esc_url( admin_url( 'edit-tags.php?taxonomy=post_tag' ) ); ?>" class="konderntang-stat-item">
                            <div class="stat-icon" style="background: #8b5cf6;">
                                <span class="dashicons dashicons-tag"></span>
                            </div>
                            <div class="stat-info">
                                <div class="stat-number"><?php echo number_format_i18n( $tag_count ); ?></div>
                                <div class="stat-label"><?php esc_html_e( 'แท็ก', 'konderntang' ); ?></div>
                            </div>
                        </a>
                        <a href="<?php echo esc_url( admin_url( 'edit-tags.php?taxonomy=destination&post_type=post' ) ); ?>" class="konderntang-stat-item">
                            <div class="stat-icon" style="background: #06b6d4;">
                                <span class="dashicons dashicons-location"></span>
                            </div>
                            <div class="stat-info">
                                <div class="stat-number"><?php echo number_format_i18n( $destination_count ); ?></div>
                                <div class="stat-label"><?php esc_html_e( 'Destinations', 'konderntang' ); ?></div>
                            </div>
                        </a>
                        <a href="<?php echo esc_url( admin_url( 'edit-tags.php?taxonomy=travel_type&post_type=post' ) ); ?>" class="konderntang-stat-item">
                            <div class="stat-icon" style="background: #a855f7;">
                                <span class="dashicons dashicons-airplane"></span>
                            </div>
                            <div class="stat-info">
                                <div class="stat-number"><?php echo number_format_i18n( $travel_type_count ); ?></div>
                                <div class="stat-label"><?php esc_html_e( 'Travel Types', 'konderntang' ); ?></div>
                            </div>
                        </a>
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

/**
 * Custom Fields Cleaner page callback
 */
function konderntang_custom_fields_cleaner_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'konderntang' ) );
    }
    
    global $wpdb;
    
    // Handle deletion
    if ( isset( $_POST['konderntang_delete_meta_keys'] ) && check_admin_referer( 'konderntang_delete_meta_keys' ) ) {
        $meta_keys_to_delete = isset( $_POST['meta_keys'] ) ? array_map( 'sanitize_text_field', $_POST['meta_keys'] ) : array();
        $deleted_count = 0;
        
        foreach ( $meta_keys_to_delete as $meta_key ) {
            // Safety check: Don't delete _konderntang_* fields that are still in use
            if ( strpos( $meta_key, '_konderntang_' ) === 0 ) {
                // Check if this meta key is still used in the theme
                $is_used = konderntang_is_meta_key_used( $meta_key );
                if ( $is_used ) {
                    continue; // Skip if still in use
                }
            }
            
            // Delete all post meta with this key
            $result = $wpdb->delete(
                $wpdb->postmeta,
                array( 'meta_key' => $meta_key ),
                array( '%s' )
            );
            
            if ( $result !== false ) {
                $deleted_count += $result;
            }
        }
        
        echo '<div class="notice notice-success"><p>';
        printf( esc_html__( 'ลบ Custom Fields สำเร็จ: %d records', 'konderntang' ), $deleted_count );
        echo '</p></div>';
    }
    
    // Get all unique meta keys (excluding WordPress core protected keys)
    $meta_keys = $wpdb->get_col(
        "SELECT DISTINCT meta_key 
        FROM {$wpdb->postmeta} 
        WHERE meta_key NOT LIKE '\_wp\_%'
        AND meta_key NOT LIKE '\_edit\_%'
        AND meta_key != ''
        ORDER BY meta_key ASC"
    );
    
    // Get count for each meta key
    $meta_keys_with_count = array();
    foreach ( $meta_keys as $meta_key ) {
        $count = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->postmeta} WHERE meta_key = %s",
            $meta_key
        ) );
        
        $is_used = konderntang_is_meta_key_used( $meta_key );
        $is_protected = konderntang_is_meta_key_protected( $meta_key );
        
        $meta_keys_with_count[] = array(
            'key' => $meta_key,
            'count' => $count,
            'is_used' => $is_used,
            'is_protected' => $is_protected,
        );
    }
    
    // Sort by count (descending)
    usort( $meta_keys_with_count, function( $a, $b ) {
        return $b['count'] - $a['count'];
    } );
    
    // Get theme meta keys (for reference)
    $theme_meta_keys = konderntang_get_theme_meta_keys();
    ?>
    <div class="wrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
        
        <div class="notice notice-warning">
            <p><strong><?php esc_html_e( 'คำเตือน:', 'konderntang' ); ?></strong> <?php esc_html_e( 'การลบ Custom Fields เป็นการกระทำที่ไม่สามารถย้อนกลับได้ กรุณาตรวจสอบให้แน่ใจก่อนลบ', 'konderntang' ); ?></p>
        </div>
        
        <form method="post" action="">
            <?php wp_nonce_field( 'konderntang_delete_meta_keys' ); ?>
            
            <div style="margin: 20px 0;">
                <button type="submit" name="konderntang_delete_meta_keys" class="button button-primary" onclick="return confirm('<?php esc_attr_e( 'คุณแน่ใจหรือไม่ว่าต้องการลบ Custom Fields ที่เลือก? การกระทำนี้ไม่สามารถย้อนกลับได้!', 'konderntang' ); ?>');">
                    <?php esc_html_e( 'ลบ Custom Fields ที่เลือก', 'konderntang' ); ?>
                </button>
            </div>
            
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th style="width: 50px;">
                            <input type="checkbox" id="select-all" />
                        </th>
                        <th><?php esc_html_e( 'Meta Key', 'konderntang' ); ?></th>
                        <th style="width: 150px;"><?php esc_html_e( 'จำนวน Records', 'konderntang' ); ?></th>
                        <th style="width: 150px;"><?php esc_html_e( 'สถานะ', 'konderntang' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ( ! empty( $meta_keys_with_count ) ) : ?>
                        <?php foreach ( $meta_keys_with_count as $item ) : 
                            $can_delete = ! $item['is_used'] && ! $item['is_protected'];
                            $status_class = $item['is_used'] ? 'status-used' : ( $item['is_protected'] ? 'status-protected' : 'status-unused' );
                            $status_text = $item['is_used'] ? esc_html__( 'ใช้อยู่ (Theme)', 'konderntang' ) : ( $item['is_protected'] ? esc_html__( 'ป้องกัน (WordPress)', 'konderntang' ) : esc_html__( 'ไม่ได้ใช้', 'konderntang' ) );
                        ?>
                            <tr class="<?php echo esc_attr( $status_class ); ?>">
                                <td>
                                    <?php if ( $can_delete ) : ?>
                                        <input type="checkbox" name="meta_keys[]" value="<?php echo esc_attr( $item['key'] ); ?>" />
                                    <?php else : ?>
                                        <span class="dashicons dashicons-lock" style="color: #d63638;"></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <code><?php echo esc_html( $item['key'] ); ?></code>
                                    <?php if ( in_array( $item['key'], $theme_meta_keys ) ) : ?>
                                        <span class="dashicons dashicons-yes-alt" style="color: #00a32a;" title="<?php esc_attr_e( 'ใช้ใน Theme', 'konderntang' ); ?>"></span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo number_format_i18n( $item['count'] ); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo esc_attr( $status_class ); ?>">
                                        <?php echo $status_text; ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="4"><?php esc_html_e( 'ไม่พบ Custom Fields', 'konderntang' ); ?></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </form>
        
        <style>
            .status-used { background-color: #fff3cd !important; }
            .status-protected { background-color: #f8d7da !important; }
            .status-unused { background-color: #d1ecf1 !important; }
            .status-badge {
                padding: 4px 8px;
                border-radius: 4px;
                font-size: 12px;
                font-weight: 600;
            }
            .status-status-used { background: #fff3cd; color: #856404; }
            .status-status-protected { background: #f8d7da; color: #721c24; }
            .status-status-unused { background: #d1ecf1; color: #0c5460; }
        </style>
        
        <script>
            document.getElementById('select-all').addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('input[name="meta_keys[]"]');
                checkboxes.forEach(function(checkbox) {
                    checkbox.checked = this.checked;
                }, this);
            });
        </script>
    </div>
    <?php
}

/**
 * Check if meta key is still used in theme
 */
function konderntang_is_meta_key_used( $meta_key ) {
    // List of meta keys used in theme
    $theme_meta_keys = konderntang_get_theme_meta_keys();
    return in_array( $meta_key, $theme_meta_keys, true );
}

/**
 * Get list of meta keys used in theme
 */
function konderntang_get_theme_meta_keys() {
    return array(
        // Post Options
        '_konderntang_breaking_news',
        '_konderntang_reading_time',
        '_konderntang_featured_post',
        
        // TOC Options
        '_konderntang_toc_enabled',
        '_konderntang_toc_position',
        
        // Travel Guide Options
        '_konderntang_location',
        '_konderntang_duration',
        '_konderntang_season',
        '_konderntang_difficulty',
        '_konderntang_price_range',
        
        // Hotel Options
        '_konderntang_hotel_price',
        '_konderntang_hotel_rating',
        '_konderntang_hotel_amenities',
        '_konderntang_hotel_address',
        '_konderntang_hotel_phone',
        '_konderntang_hotel_website',
        
        // Promotion Options
        '_konderntang_promotion_price',
        '_konderntang_promotion_discount',
        '_konderntang_promotion_start_date',
        '_konderntang_promotion_end_date',
        '_konderntang_promotion_code',
        
        // SEO Options
        '_konderntang_meta_description',
        '_konderntang_meta_keywords',
        '_konderntang_og_title',
        '_konderntang_og_description',
        '_konderntang_og_image',
        
        // Other theme meta keys
        'post_views_count',
    );
}

/**
 * Check if meta key is protected (WordPress core)
 */
function konderntang_is_meta_key_protected( $meta_key ) {
    // WordPress core protected meta keys
    $protected_keys = array(
        '_edit_lock',
        '_edit_last',
        '_wp_old_slug',
        '_wp_page_template',
        '_thumbnail_id',
        '_wp_attachment_metadata',
        '_wp_attached_file',
    );
    
    // Check if starts with protected prefixes
    $protected_prefixes = array( '_wp_', '_edit_' );
    foreach ( $protected_prefixes as $prefix ) {
        if ( strpos( $meta_key, $prefix ) === 0 ) {
            return true;
        }
    }
    
    return in_array( $meta_key, $protected_keys, true );
}
