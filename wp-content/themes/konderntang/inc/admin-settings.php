<?php
/**
 * Admin Settings Page
 *
 * @package KonDernTang
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Save settings
 */
function konderntang_save_settings() {
    if ( ! isset( $_POST['konderntang_save_settings'] ) ) {
        return;
    }
    
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'konderntang' ) );
    }
    
    check_admin_referer( 'konderntang_settings_nonce' );
    
    // General Settings
    if ( isset( $_POST['logo_fallback_image'] ) ) {
        set_theme_mod( 'logo_fallback_image', esc_url_raw( $_POST['logo_fallback_image'] ) );
    }
        
    // Header Settings
    $header_show_search = isset( $_POST['header_show_search'] ) ? '1' : '0';
    set_theme_mod( 'header_show_search', $header_show_search );
    
    // Footer Settings
    if ( isset( $_POST['footer_layout'] ) ) {
        $footer_layout = absint( $_POST['footer_layout'] );
        if ( $footer_layout >= 0 && $footer_layout <= 4 ) {
            set_theme_mod( 'footer_layout', $footer_layout );
        }
    }
    
    if ( isset( $_POST['footer_copyright_text'] ) ) {
        set_theme_mod( 'footer_copyright_text', wp_kses_post( $_POST['footer_copyright_text'] ) );
    }
    
    // Cookie Consent Settings
    $cookie_consent_enabled = isset( $_POST['cookie_consent_enabled'] ) ? '1' : '0';
    set_theme_mod( 'cookie_consent_enabled', $cookie_consent_enabled );
    
    if ( isset( $_POST['cookie_consent_message'] ) ) {
        set_theme_mod( 'cookie_consent_message', sanitize_textarea_field( $_POST['cookie_consent_message'] ) );
    }
    
    // Homepage Settings
    $hero_slider_enabled = isset( $_POST['hero_slider_enabled'] ) ? '1' : '0';
    set_theme_mod( 'hero_slider_enabled', $hero_slider_enabled );
    
    if ( isset( $_POST['hero_slider_posts'] ) ) {
        $hero_slider_posts = absint( $_POST['hero_slider_posts'] );
        if ( $hero_slider_posts >= 1 && $hero_slider_posts <= 10 ) {
            set_theme_mod( 'hero_slider_posts', $hero_slider_posts );
        }
    }
    
    $featured_section_enabled = isset( $_POST['featured_section_enabled'] ) ? '1' : '0';
    set_theme_mod( 'featured_section_enabled', $featured_section_enabled );
    
    if ( isset( $_POST['featured_posts_count'] ) ) {
        $featured_posts_count = absint( $_POST['featured_posts_count'] );
        if ( $featured_posts_count >= 1 && $featured_posts_count <= 10 ) {
            set_theme_mod( 'featured_posts_count', $featured_posts_count );
        }
    }
    
    if ( isset( $_POST['recent_posts_count'] ) ) {
        $recent_posts_count = absint( $_POST['recent_posts_count'] );
        if ( $recent_posts_count >= 1 && $recent_posts_count <= 20 ) {
            set_theme_mod( 'recent_posts_count', $recent_posts_count );
        }
    }
    
    $newsletter_enabled = isset( $_POST['newsletter_enabled'] ) ? '1' : '0';
    set_theme_mod( 'newsletter_enabled', $newsletter_enabled );
    
    if ( isset( $_POST['trending_tags_count'] ) ) {
        $trending_tags_count = absint( $_POST['trending_tags_count'] );
        if ( $trending_tags_count >= 1 && $trending_tags_count <= 30 ) {
            set_theme_mod( 'trending_tags_count', $trending_tags_count );
        }
    }
    
    $recently_viewed_enabled = isset( $_POST['recently_viewed_enabled'] ) ? '1' : '0';
    set_theme_mod( 'recently_viewed_enabled', $recently_viewed_enabled );
    
    // Layout Settings
    if ( isset( $_POST['layout_container_width'] ) ) {
        $container_width = absint( $_POST['layout_container_width'] );
        if ( $container_width >= 960 && $container_width <= 1920 ) {
            set_theme_mod( 'layout_container_width', $container_width );
        }
    }
    
    if ( isset( $_POST['layout_archive_sidebar'] ) ) {
        $archive_sidebar = sanitize_text_field( $_POST['layout_archive_sidebar'] );
        if ( in_array( $archive_sidebar, array( 'left', 'right', 'none' ), true ) ) {
            set_theme_mod( 'layout_archive_sidebar', $archive_sidebar );
        }
    }
    
    if ( isset( $_POST['layout_single_sidebar'] ) ) {
        $single_sidebar = sanitize_text_field( $_POST['layout_single_sidebar'] );
        if ( in_array( $single_sidebar, array( 'left', 'right', 'none' ), true ) ) {
            set_theme_mod( 'layout_single_sidebar', $single_sidebar );
        }
    }
    
    if ( isset( $_POST['layout_posts_per_page'] ) ) {
        $posts_per_page = absint( $_POST['layout_posts_per_page'] );
        if ( $posts_per_page >= 1 && $posts_per_page <= 50 ) {
            set_theme_mod( 'layout_posts_per_page', $posts_per_page );
        }
    }
    
    // Color Settings
    if ( isset( $_POST['color_primary'] ) ) {
        set_theme_mod( 'color_primary', sanitize_hex_color( $_POST['color_primary'] ) );
    }
    
    if ( isset( $_POST['color_secondary'] ) ) {
        set_theme_mod( 'color_secondary', sanitize_hex_color( $_POST['color_secondary'] ) );
    }
    
    if ( isset( $_POST['color_text'] ) ) {
        set_theme_mod( 'color_text', sanitize_hex_color( $_POST['color_text'] ) );
    }
    
    if ( isset( $_POST['color_background'] ) ) {
        set_theme_mod( 'color_background', sanitize_hex_color( $_POST['color_background'] ) );
    }
    
    if ( isset( $_POST['color_link'] ) ) {
        set_theme_mod( 'color_link', sanitize_hex_color( $_POST['color_link'] ) );
    }
    
    // Typography Settings
    if ( isset( $_POST['typography_body_font'] ) ) {
        $body_font = sanitize_text_field( $_POST['typography_body_font'] );
        if ( in_array( $body_font, array( 'Sarabun', 'Kanit', 'Prompt', 'Sarabun Sans' ), true ) ) {
            set_theme_mod( 'typography_body_font', $body_font );
        }
    }
    
    if ( isset( $_POST['typography_heading_font'] ) ) {
        $heading_font = sanitize_text_field( $_POST['typography_heading_font'] );
        if ( in_array( $heading_font, array( 'Kanit', 'Sarabun', 'Prompt', 'Sarabun Sans' ), true ) ) {
            set_theme_mod( 'typography_heading_font', $heading_font );
        }
    }
    
    if ( isset( $_POST['typography_body_size'] ) ) {
        $body_size = absint( $_POST['typography_body_size'] );
        if ( $body_size >= 12 && $body_size <= 24 ) {
            set_theme_mod( 'typography_body_size', $body_size );
        }
    }
    
    if ( isset( $_POST['typography_h1_size'] ) ) {
        $h1_size = absint( $_POST['typography_h1_size'] );
        if ( $h1_size >= 24 && $h1_size <= 72 ) {
            set_theme_mod( 'typography_h1_size', $h1_size );
        }
    }
    
    if ( isset( $_POST['typography_line_height'] ) ) {
        $line_height = floatval( $_POST['typography_line_height'] );
        if ( $line_height >= 1.0 && $line_height <= 2.5 ) {
            set_theme_mod( 'typography_line_height', $line_height );
        }
    }
    
    // TOC Settings
    $toc_enabled = isset( $_POST['toc_enabled'] ) ? '1' : '0';
    set_theme_mod( 'toc_enabled', $toc_enabled );
    
    if ( isset( $_POST['toc_min_headings'] ) ) {
        $toc_min_headings = absint( $_POST['toc_min_headings'] );
        if ( $toc_min_headings >= 1 && $toc_min_headings <= 10 ) {
            set_theme_mod( 'toc_min_headings', $toc_min_headings );
        }
    }
    
    if ( isset( $_POST['toc_heading_levels'] ) ) {
        $toc_heading_levels = array_map( 'sanitize_text_field', $_POST['toc_heading_levels'] );
        set_theme_mod( 'toc_heading_levels', $toc_heading_levels );
    }
    
    if ( isset( $_POST['toc_title'] ) ) {
        set_theme_mod( 'toc_title', sanitize_text_field( $_POST['toc_title'] ) );
    }
    
    $toc_collapsible = isset( $_POST['toc_collapsible'] ) ? '1' : '0';
    set_theme_mod( 'toc_collapsible', $toc_collapsible );
    
    $toc_smooth_scroll = isset( $_POST['toc_smooth_scroll'] ) ? '1' : '0';
    set_theme_mod( 'toc_smooth_scroll', $toc_smooth_scroll );
    
    $toc_scroll_spy = isset( $_POST['toc_scroll_spy'] ) ? '1' : '0';
    set_theme_mod( 'toc_scroll_spy', $toc_scroll_spy );
    // Advanced Settings
    if ( isset( $_POST['advanced_custom_css'] ) ) {
        set_theme_mod( 'advanced_custom_css', wp_strip_all_tags( $_POST['advanced_custom_css'] ) );
    }
    
    if ( isset( $_POST['advanced_custom_js'] ) ) {
        set_theme_mod( 'advanced_custom_js', wp_strip_all_tags( $_POST['advanced_custom_js'] ) );
    }
    
    if ( isset( $_POST['advanced_google_analytics'] ) ) {
        set_theme_mod( 'advanced_google_analytics', wp_kses_post( $_POST['advanced_google_analytics'] ) );
    }
    
    if ( isset( $_POST['advanced_facebook_pixel'] ) ) {
        set_theme_mod( 'advanced_facebook_pixel', wp_kses_post( $_POST['advanced_facebook_pixel'] ) );
    }
    
    add_settings_error(
        'konderntang_settings',
        'konderntang_settings_saved',
        esc_html__( 'Settings saved successfully!', 'konderntang' ),
        'success'
    );
}
add_action( 'admin_init', 'konderntang_save_settings' );

/**
 * Settings page render callback
 */
function konderntang_settings_page_render() {
    // Display settings errors
    settings_errors( 'konderntang_settings' );
    
    // Get active tab from POST, GET parameter, or default to general
    $active_tab = 'general';
    if ( isset( $_POST['konderntang_active_tab'] ) ) {
        $active_tab = sanitize_text_field( $_POST['konderntang_active_tab'] );
    } elseif ( isset( $_GET['tab'] ) ) {
        $active_tab = sanitize_text_field( $_GET['tab'] );
    }
    
    $valid_tabs = array( 'general', 'header', 'footer', 'homepage', 'layout', 'colors', 'typography', 'toc', 'cookie', 'advanced' );
    if ( ! in_array( $active_tab, $valid_tabs, true ) ) {
        $active_tab = 'general';
    }
    
    // Define tab groups
    $tab_groups = array(
        'general' => array(
            'label' => esc_html__( 'General', 'konderntang' ),
            'icon' => 'dashicons-admin-generic',
            'tabs' => array( 'general' ),
        ),
        'appearance' => array(
            'label' => esc_html__( 'Appearance', 'konderntang' ),
            'icon' => 'dashicons-admin-appearance',
            'tabs' => array( 'header', 'footer', 'colors', 'typography' ),
        ),
        'content' => array(
            'label' => esc_html__( 'Content & Layout', 'konderntang' ),
            'icon' => 'dashicons-layout',
            'tabs' => array( 'homepage', 'layout', 'toc' ),
        ),
        'features' => array(
            'label' => esc_html__( 'Features', 'konderntang' ),
            'icon' => 'dashicons-admin-plugins',
            'tabs' => array( 'cookie' ),
        ),
        'advanced' => array(
            'label' => esc_html__( 'Advanced', 'konderntang' ),
            'icon' => 'dashicons-admin-tools',
            'tabs' => array( 'advanced' ),
        ),
    );
    
    // Determine which group the active tab belongs to
    $active_group = 'general';
    foreach ( $tab_groups as $group_key => $group_data ) {
        if ( in_array( $active_tab, $group_data['tabs'], true ) ) {
            $active_group = $group_key;
            break;
        }
    }
    
    // Get current settings
    $logo_fallback_image = konderntang_get_option( 'logo_fallback_image', '' );
    $header_show_search = konderntang_get_option( 'header_show_search', true );
    $footer_layout = konderntang_get_option( 'footer_layout', '0' );
    $footer_copyright_text = konderntang_get_option( 'footer_copyright_text', sprintf( esc_html__( '&copy; %1$s %2$s - %3$s', 'konderntang' ), date( 'Y' ), get_bloginfo( 'name' ), esc_html__( 'เพื่อนเดินทางของคุณ', 'konderntang' ) ) );
    $cookie_consent_enabled = konderntang_get_option( 'cookie_consent_enabled', true );
    $cookie_consent_message = konderntang_get_option( 'cookie_consent_message', esc_html__( 'เราใช้คุกกี้เพื่อปรับปรุงประสบการณ์การใช้งานของคุณ', 'konderntang' ) );
    
    // Homepage Settings
    $hero_slider_enabled = konderntang_get_option( 'hero_slider_enabled', true );
    $hero_slider_posts = konderntang_get_option( 'hero_slider_posts', 4 );
    $featured_section_enabled = konderntang_get_option( 'featured_section_enabled', true );
    $featured_posts_count = konderntang_get_option( 'featured_posts_count', 3 );
    $recent_posts_count = konderntang_get_option( 'recent_posts_count', 6 );
    $newsletter_enabled = konderntang_get_option( 'newsletter_enabled', true );
    $trending_tags_count = konderntang_get_option( 'trending_tags_count', 10 );
    $recently_viewed_enabled = konderntang_get_option( 'recently_viewed_enabled', true );
    
    // Layout Settings
    $layout_container_width = konderntang_get_option( 'layout_container_width', 1200 );
    $layout_archive_sidebar = konderntang_get_option( 'layout_archive_sidebar', 'right' );
    $layout_single_sidebar = konderntang_get_option( 'layout_single_sidebar', 'right' );
    $layout_posts_per_page = konderntang_get_option( 'layout_posts_per_page', 10 );
    
    // Color Settings
    $color_primary = konderntang_get_option( 'color_primary', '#0ea5e9' );
    $color_secondary = konderntang_get_option( 'color_secondary', '#64748b' );
    $color_text = konderntang_get_option( 'color_text', '#1e293b' );
    $color_background = konderntang_get_option( 'color_background', '#ffffff' );
    $color_link = konderntang_get_option( 'color_link', '#0ea5e9' );
    
    // Typography Settings
    $typography_body_font = konderntang_get_option( 'typography_body_font', 'Sarabun' );
    $typography_heading_font = konderntang_get_option( 'typography_heading_font', 'Kanit' );
    $typography_body_size = konderntang_get_option( 'typography_body_size', 16 );
    $typography_h1_size = konderntang_get_option( 'typography_h1_size', 36 );
    $typography_line_height = konderntang_get_option( 'typography_line_height', 1.6 );
    
    
    // TOC Settings
    $toc_enabled = konderntang_get_option( 'toc_enabled', true );
    $toc_min_headings = konderntang_get_option( 'toc_min_headings', 2 );
    $toc_heading_levels = konderntang_get_option( 'toc_heading_levels', array( 'h2', 'h3', 'h4' ) );
    if ( ! is_array( $toc_heading_levels ) ) {
        $toc_heading_levels = array( 'h2', 'h3', 'h4' );
    }
    $toc_title = konderntang_get_option( 'toc_title', esc_html__( 'สารบัญ', 'konderntang' ) );
    $toc_collapsible = konderntang_get_option( 'toc_collapsible', true );
    $toc_smooth_scroll = konderntang_get_option( 'toc_smooth_scroll', true );
    $toc_scroll_spy = konderntang_get_option( 'toc_scroll_spy', true );

    // Advanced Settings
    $advanced_custom_css = konderntang_get_option( 'advanced_custom_css', '' );
    $advanced_custom_js = konderntang_get_option( 'advanced_custom_js', '' );
    $advanced_google_analytics = konderntang_get_option( 'advanced_google_analytics', '' );
    $advanced_facebook_pixel = konderntang_get_option( 'advanced_facebook_pixel', '' );
    
    // Enqueue media uploader
    wp_enqueue_media();
    ?>
    <div class="wrap konderntang-settings-wrap">
        <h1 class="konderntang-dashboard-title">
            <span class="dashicons dashicons-admin-settings"></span>
            <?php echo esc_html( get_admin_page_title() ); ?>
        </h1>
        
        <!-- Tab Groups Navigation -->
        <div class="konderntang-settings-groups">
            <?php foreach ( $tab_groups as $group_key => $group_data ) : ?>
                <div class="konderntang-settings-group <?php echo $active_group === $group_key ? 'active' : ''; ?>" data-group="<?php echo esc_attr( $group_key ); ?>">
                    <div class="konderntang-settings-group-header">
                        <span class="dashicons <?php echo esc_attr( $group_data['icon'] ); ?>"></span>
                        <strong><?php echo esc_html( $group_data['label'] ); ?></strong>
                        <span class="konderntang-group-arrow dashicons dashicons-arrow-down-alt2"></span>
                    </div>
                    <div class="konderntang-settings-group-tabs">
                        <?php foreach ( $group_data['tabs'] as $tab_key ) : 
                            $tab_labels = array(
                                'general' => esc_html__( 'General', 'konderntang' ),
                                'header' => esc_html__( 'Header', 'konderntang' ),
                                'footer' => esc_html__( 'Footer', 'konderntang' ),
                                'homepage' => esc_html__( 'Homepage', 'konderntang' ),
                                'layout' => esc_html__( 'Layout', 'konderntang' ),
                                'colors' => esc_html__( 'Colors', 'konderntang' ),
                                'typography' => esc_html__( 'Typography', 'konderntang' ),
                                'toc' => esc_html__( 'Table of Contents', 'konderntang' ),
                                'cookie' => esc_html__( 'Cookie Consent', 'konderntang' ),
                                'advanced' => esc_html__( 'Advanced', 'konderntang' ),
                            );
                            $tab_icons = array(
                                'general' => 'dashicons-admin-generic',
                                'header' => 'dashicons-admin-appearance',
                                'footer' => 'dashicons-arrow-down-alt',
                                'homepage' => 'dashicons-admin-home',
                                'layout' => 'dashicons-layout',
                                'colors' => 'dashicons-art',
                                'typography' => 'dashicons-editor-textcolor',
                                'toc' => 'dashicons-list-view',
                                'cookie' => 'dashicons-privacy',
                                'advanced' => 'dashicons-admin-tools',
                            );
                        ?>
                            <a href="?page=konderntang-settings&tab=<?php echo esc_attr( $tab_key ); ?>" class="konderntang-group-tab <?php echo $active_tab === $tab_key ? 'active' : ''; ?>">
                                <span class="dashicons <?php echo esc_attr( $tab_icons[ $tab_key ] ); ?>"></span>
                                <?php echo esc_html( $tab_labels[ $tab_key ] ); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <form method="post" action="" id="konderntang-settings-form">
            <?php wp_nonce_field( 'konderntang_settings_nonce' ); ?>
            <input type="hidden" name="konderntang_active_tab" value="<?php echo esc_attr( $active_tab ); ?>" />
            
            <?php if ( $active_tab === 'general' ) : ?>
                <div class="konderntang-widget">
                    <div class="konderntang-widget-header">
                        <h3>
                            <span class="dashicons dashicons-admin-generic"></span>
                            <?php esc_html_e( 'General Settings', 'konderntang' ); ?>
                        </h3>
                    </div>
                    <div class="konderntang-widget-content">
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label for="logo_fallback_image">
                                        <span class="dashicons dashicons-format-image" style="vertical-align: middle; margin-right: 4px; color: #0ea5e9;"></span>
                                        <?php esc_html_e( 'Logo Fallback Image', 'konderntang' ); ?>
                                    </label>
                                </th>
                                <td>
                            <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                                <input type="text" id="logo_fallback_image" name="logo_fallback_image" value="<?php echo esc_attr( $logo_fallback_image ); ?>" class="regular-text" style="flex: 1; min-width: 300px;" />
                                <button type="button" class="button media-upload-button" data-target="logo_fallback_image">
                                    <span class="dashicons dashicons-upload" style="vertical-align: middle; margin-right: 4px;"></span>
                                    <?php esc_html_e( 'Upload Image', 'konderntang' ); ?>
                                </button>
                                <?php if ( $logo_fallback_image ) : ?>
                                    <button type="button" class="button konderntang-remove-image" style="color: #ef4444;">
                                        <span class="dashicons dashicons-trash"></span>
                                    </button>
                                <?php endif; ?>
                            </div>
                            <?php if ( $logo_fallback_image ) : ?>
                                <div class="konderntang-image-preview" style="margin-top: 10px;">
                                    <img src="<?php echo esc_url( $logo_fallback_image ); ?>" style="max-width: 200px; height: auto; border-radius: 6px; border: 1px solid #e2e8f0; box-shadow: 0 2px 4px rgba(0,0,0,0.1);" alt="Logo Preview" />
                                </div>
                            <?php endif; ?>
                            <p class="description">
                                <span class="dashicons dashicons-info" style="font-size: 16px; vertical-align: middle; color: #0ea5e9;"></span>
                                <?php esc_html_e( 'Upload a fallback image if no custom logo is set.', 'konderntang' ); ?>
                            </p>
                        </td>
                    </tr>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if ( $active_tab === 'header' ) : ?>
                <div class="konderntang-widget">
                    <div class="konderntang-widget-header">
                        <h3>
                            <span class="dashicons dashicons-admin-appearance"></span>
                            <?php esc_html_e( 'Header Settings', 'konderntang' ); ?>
                        </h3>
                    </div>
                    <div class="konderntang-widget-content">
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <span class="dashicons dashicons-search" style="vertical-align: middle; margin-right: 4px; color: #0ea5e9;"></span>
                                    <?php esc_html_e( 'Search Button', 'konderntang' ); ?>
                                </th>
                                <td>
                                    <label style="display: flex; align-items: center; gap: 8px; padding: 10px; border-radius: 6px; transition: background 0.2s; cursor: pointer;" onmouseover="this.style.background='#f0f9ff'" onmouseout="this.style.background='transparent'">
                                        <input type="checkbox" name="header_show_search" value="1" <?php checked( $header_show_search, true ); ?> style="width: 18px; height: 18px; accent-color: #0ea5e9;" />
                                        <strong><?php esc_html_e( 'Show search button in header', 'konderntang' ); ?></strong>
                                    </label>
                                    <p class="description">
                                        <?php esc_html_e( 'แสดงปุ่มค้นหาในส่วน header navigation', 'konderntang' ); ?>
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if ( $active_tab === 'footer' ) : ?>
                <div class="konderntang-widget">
                    <div class="konderntang-widget-header">
                        <h3>
                            <span class="dashicons dashicons-arrow-down-alt"></span>
                            <?php esc_html_e( 'Footer Settings', 'konderntang' ); ?>
                        </h3>
                    </div>
                    <div class="konderntang-widget-content">
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label for="footer_layout">
                                        <span class="dashicons dashicons-layout" style="vertical-align: middle; margin-right: 4px; color: #0ea5e9;"></span>
                                        <?php esc_html_e( 'Footer Layout', 'konderntang' ); ?>
                                    </label>
                                </th>
                                <td>
                                    <select name="footer_layout" id="footer_layout" style="width: 200px;">
                                        <option value="0" <?php selected( $footer_layout, '0' ); ?>><?php esc_html_e( 'No Widgets', 'konderntang' ); ?></option>
                                        <option value="1" <?php selected( $footer_layout, '1' ); ?>><?php esc_html_e( '1 Column', 'konderntang' ); ?></option>
                                        <option value="2" <?php selected( $footer_layout, '2' ); ?>><?php esc_html_e( '2 Columns', 'konderntang' ); ?></option>
                                        <option value="3" <?php selected( $footer_layout, '3' ); ?>><?php esc_html_e( '3 Columns', 'konderntang' ); ?></option>
                                        <option value="4" <?php selected( $footer_layout, '4' ); ?>><?php esc_html_e( '4 Columns', 'konderntang' ); ?></option>
                                    </select>
                                    <p class="description">
                                        <?php esc_html_e( 'เลือกจำนวนคอลัมน์สำหรับ Footer Widget Areas', 'konderntang' ); ?>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="footer_copyright_text">
                                        <span class="dashicons dashicons-edit" style="vertical-align: middle; margin-right: 4px; color: #0ea5e9;"></span>
                                        <?php esc_html_e( 'Copyright Text', 'konderntang' ); ?>
                                    </label>
                                </th>
                                <td>
                                    <textarea name="footer_copyright_text" id="footer_copyright_text" rows="3" class="large-text" placeholder="<?php esc_attr_e( '&copy; %Y% %SITE_NAME% - เพื่อนเดินทางของคุณ', 'konderntang' ); ?>"><?php echo esc_textarea( $footer_copyright_text ); ?></textarea>
                                    <p class="description">
                                        <span class="dashicons dashicons-info" style="font-size: 16px; vertical-align: middle; color: #0ea5e9;"></span>
                                        <?php esc_html_e( 'ใช้ %Y% สำหรับปี, %SITE_NAME% สำหรับชื่อเว็บไซต์', 'konderntang' ); ?>
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if ( $active_tab === 'homepage' ) : ?>
                <div class="konderntang-widget">
                    <div class="konderntang-widget-header">
                        <h3>
                            <span class="dashicons dashicons-admin-home"></span>
                            <?php esc_html_e( 'Homepage Settings', 'konderntang' ); ?>
                        </h3>
                    </div>
                    <div class="konderntang-widget-content">
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <span class="dashicons dashicons-slides" style="vertical-align: middle; margin-right: 4px; color: #0ea5e9;"></span>
                                    <?php esc_html_e( 'Hero Slider', 'konderntang' ); ?>
                                </th>
                                <td>
                                    <label style="display: flex; align-items: center; gap: 8px; padding: 10px; border-radius: 6px; transition: background 0.2s; cursor: pointer;" onmouseover="this.style.background='#f0f9ff'" onmouseout="this.style.background='transparent'">
                                        <input type="checkbox" name="hero_slider_enabled" value="1" <?php checked( $hero_slider_enabled, true ); ?> style="width: 18px; height: 18px; accent-color: #0ea5e9;" />
                                        <strong><?php esc_html_e( 'Enable hero slider on homepage', 'konderntang' ); ?></strong>
                                    </label>
                                    <p class="description">
                                        <?php esc_html_e( 'แสดง Hero Slider ที่ด้านบนของหน้าแรก', 'konderntang' ); ?>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="hero_slider_posts"><?php esc_html_e( 'Number of Hero Slides', 'konderntang' ); ?></label>
                                </th>
                                <td>
                                    <input type="number" name="hero_slider_posts" id="hero_slider_posts" value="<?php echo esc_attr( $hero_slider_posts ); ?>" min="1" max="10" step="1" />
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <?php esc_html_e( 'Featured Section', 'konderntang' ); ?>
                                </th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="featured_section_enabled" value="1" <?php checked( $featured_section_enabled, true ); ?> />
                                        <?php esc_html_e( 'Enable featured section on homepage', 'konderntang' ); ?>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="featured_posts_count"><?php esc_html_e( 'Number of Featured Posts', 'konderntang' ); ?></label>
                                </th>
                                <td>
                                    <input type="number" name="featured_posts_count" id="featured_posts_count" value="<?php echo esc_attr( $featured_posts_count ); ?>" min="1" max="10" step="1" />
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="recent_posts_count"><?php esc_html_e( 'Number of Recent Posts', 'konderntang' ); ?></label>
                                </th>
                                <td>
                                    <input type="number" name="recent_posts_count" id="recent_posts_count" value="<?php echo esc_attr( $recent_posts_count ); ?>" min="1" max="20" step="1" />
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <?php esc_html_e( 'Newsletter', 'konderntang' ); ?>
                                </th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="newsletter_enabled" value="1" <?php checked( $newsletter_enabled, true ); ?> />
                                        <?php esc_html_e( 'Enable newsletter section on homepage', 'konderntang' ); ?>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="trending_tags_count"><?php esc_html_e( 'Number of Trending Tags', 'konderntang' ); ?></label>
                                </th>
                                <td>
                                    <input type="number" name="trending_tags_count" id="trending_tags_count" value="<?php echo esc_attr( $trending_tags_count ); ?>" min="1" max="30" step="1" />
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <?php esc_html_e( 'Recently Viewed', 'konderntang' ); ?>
                                </th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="recently_viewed_enabled" value="1" <?php checked( $recently_viewed_enabled, true ); ?> />
                                        <?php esc_html_e( 'Enable recently viewed posts section', 'konderntang' ); ?>
                                    </label>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if ( $active_tab === 'layout' ) : ?>
                <div class="konderntang-widget">
                    <div class="konderntang-widget-header">
                        <h3>
                            <span class="dashicons dashicons-layout"></span>
                            <?php esc_html_e( 'Layout Settings', 'konderntang' ); ?>
                        </h3>
                    </div>
                    <div class="konderntang-widget-content">
                        <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="layout_container_width"><?php esc_html_e( 'Container Width (px)', 'konderntang' ); ?></label>
                        </th>
                        <td>
                            <input type="number" name="layout_container_width" id="layout_container_width" value="<?php echo esc_attr( $layout_container_width ); ?>" min="960" max="1920" step="10" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="layout_archive_sidebar"><?php esc_html_e( 'Archive Sidebar Position', 'konderntang' ); ?></label>
                        </th>
                        <td>
                            <select name="layout_archive_sidebar" id="layout_archive_sidebar">
                                <option value="left" <?php selected( $layout_archive_sidebar, 'left' ); ?>><?php esc_html_e( 'Left', 'konderntang' ); ?></option>
                                <option value="right" <?php selected( $layout_archive_sidebar, 'right' ); ?>><?php esc_html_e( 'Right', 'konderntang' ); ?></option>
                                <option value="none" <?php selected( $layout_archive_sidebar, 'none' ); ?>><?php esc_html_e( 'None', 'konderntang' ); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="layout_single_sidebar"><?php esc_html_e( 'Single Post Sidebar Position', 'konderntang' ); ?></label>
                        </th>
                        <td>
                            <select name="layout_single_sidebar" id="layout_single_sidebar">
                                <option value="left" <?php selected( $layout_single_sidebar, 'left' ); ?>><?php esc_html_e( 'Left', 'konderntang' ); ?></option>
                                <option value="right" <?php selected( $layout_single_sidebar, 'right' ); ?>><?php esc_html_e( 'Right', 'konderntang' ); ?></option>
                                <option value="none" <?php selected( $layout_single_sidebar, 'none' ); ?>><?php esc_html_e( 'None', 'konderntang' ); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="layout_posts_per_page"><?php esc_html_e( 'Posts Per Page', 'konderntang' ); ?></label>
                        </th>
                        <td>
                            <input type="number" name="layout_posts_per_page" id="layout_posts_per_page" value="<?php echo esc_attr( $layout_posts_per_page ); ?>" min="1" max="50" step="1" />
                        </td>
                    </tr>
                </table>
            <?php endif; ?>
            
            <?php if ( $active_tab === 'colors' ) : ?>
                <div class="konderntang-widget">
                    <div class="konderntang-widget-header">
                        <h3>
                            <span class="dashicons dashicons-art"></span>
                            <?php esc_html_e( 'Color Settings', 'konderntang' ); ?>
                        </h3>
                    </div>
                    <div class="konderntang-widget-content">
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label for="color_primary">
                                        <span class="dashicons dashicons-admin-appearance" style="vertical-align: middle; margin-right: 4px; color: #0ea5e9;"></span>
                                        <?php esc_html_e( 'Primary Color', 'konderntang' ); ?>
                                    </label>
                                </th>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <input type="color" name="color_primary" id="color_primary" value="<?php echo esc_attr( $color_primary ); ?>" />
                                        <input type="text" value="<?php echo esc_attr( $color_primary ); ?>" readonly style="width: 100px; padding: 6px 10px; border: 1px solid #cbd5e1; border-radius: 6px; background: #f8fafc; font-family: monospace;" class="konderntang-color-value" />
                                        <span class="description"><?php esc_html_e( 'สีหลักของธีม (ปุ่ม, ลิงก์, ไฮไลท์)', 'konderntang' ); ?></span>
                                    </div>
                                </td>
                            </tr>
                    <tr>
                        <th scope="row">
                            <label for="color_secondary">
                                <span class="dashicons dashicons-admin-appearance" style="vertical-align: middle; margin-right: 4px; color: #f97316;"></span>
                                <?php esc_html_e( 'Secondary Color', 'konderntang' ); ?>
                            </label>
                        </th>
                        <td>
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <input type="color" name="color_secondary" id="color_secondary" value="<?php echo esc_attr( $color_secondary ); ?>" />
                                <input type="text" value="<?php echo esc_attr( $color_secondary ); ?>" readonly style="width: 100px; padding: 6px 10px; border: 1px solid #cbd5e1; border-radius: 6px; background: #f8fafc; font-family: monospace;" class="konderntang-color-value" />
                                <span class="description"><?php esc_html_e( 'สีรองของธีม (ปุ่มพิเศษ, badge)', 'konderntang' ); ?></span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="color_text">
                                <span class="dashicons dashicons-editor-textcolor" style="vertical-align: middle; margin-right: 4px; color: #1e293b;"></span>
                                <?php esc_html_e( 'Text Color', 'konderntang' ); ?>
                            </label>
                        </th>
                        <td>
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <input type="color" name="color_text" id="color_text" value="<?php echo esc_attr( $color_text ); ?>" />
                                <input type="text" value="<?php echo esc_attr( $color_text ); ?>" readonly style="width: 100px; padding: 6px 10px; border: 1px solid #cbd5e1; border-radius: 6px; background: #f8fafc; font-family: monospace;" class="konderntang-color-value" />
                                <span class="description"><?php esc_html_e( 'สีข้อความหลัก', 'konderntang' ); ?></span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="color_background">
                                <span class="dashicons dashicons-admin-appearance" style="vertical-align: middle; margin-right: 4px; color: #f8fafc;"></span>
                                <?php esc_html_e( 'Background Color', 'konderntang' ); ?>
                            </label>
                        </th>
                        <td>
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <input type="color" name="color_background" id="color_background" value="<?php echo esc_attr( $color_background ); ?>" />
                                <input type="text" value="<?php echo esc_attr( $color_background ); ?>" readonly style="width: 100px; padding: 6px 10px; border: 1px solid #cbd5e1; border-radius: 6px; background: #f8fafc; font-family: monospace;" class="konderntang-color-value" />
                                <span class="description"><?php esc_html_e( 'สีพื้นหลังหลัก', 'konderntang' ); ?></span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="color_link">
                                <span class="dashicons dashicons-admin-links" style="vertical-align: middle; margin-right: 4px; color: #0ea5e9;"></span>
                                <?php esc_html_e( 'Link Color', 'konderntang' ); ?>
                            </label>
                        </th>
                        <td>
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <input type="color" name="color_link" id="color_link" value="<?php echo esc_attr( $color_link ); ?>" />
                                <input type="text" value="<?php echo esc_attr( $color_link ); ?>" readonly style="width: 100px; padding: 6px 10px; border: 1px solid #cbd5e1; border-radius: 6px; background: #f8fafc; font-family: monospace;" class="konderntang-color-value" />
                                <span class="description"><?php esc_html_e( 'สีลิงก์', 'konderntang' ); ?></span>
                            </div>
                        </td>
                    </tr>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if ( $active_tab === 'typography' ) : ?>
                <div class="konderntang-widget">
                    <div class="konderntang-widget-header">
                        <h3>
                            <span class="dashicons dashicons-editor-textcolor"></span>
                            <?php esc_html_e( 'Typography Settings', 'konderntang' ); ?>
                        </h3>
                    </div>
                    <div class="konderntang-widget-content">
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label for="typography_body_font">
                                        <span class="dashicons dashicons-editor-textcolor" style="vertical-align: middle; margin-right: 4px; color: #0ea5e9;"></span>
                                        <?php esc_html_e( 'Body Font', 'konderntang' ); ?>
                                    </label>
                                </th>
                        <td>
                            <select name="typography_body_font" id="typography_body_font" style="width: 250px;">
                                <option value="Sarabun" <?php selected( $typography_body_font, 'Sarabun' ); ?>>Sarabun</option>
                                <option value="Kanit" <?php selected( $typography_body_font, 'Kanit' ); ?>>Kanit</option>
                                <option value="Prompt" <?php selected( $typography_body_font, 'Prompt' ); ?>>Prompt</option>
                                <option value="Sarabun Sans" <?php selected( $typography_body_font, 'Sarabun Sans' ); ?>>Sarabun Sans</option>
                            </select>
                            <p class="description">
                                <?php esc_html_e( 'ฟอนต์สำหรับข้อความทั่วไป', 'konderntang' ); ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="typography_heading_font">
                                <span class="dashicons dashicons-editor-bold" style="vertical-align: middle; margin-right: 4px; color: #0ea5e9;"></span>
                                <?php esc_html_e( 'Heading Font', 'konderntang' ); ?>
                            </label>
                        </th>
                        <td>
                            <select name="typography_heading_font" id="typography_heading_font" style="width: 250px;">
                                <option value="Kanit" <?php selected( $typography_heading_font, 'Kanit' ); ?>>Kanit</option>
                                <option value="Sarabun" <?php selected( $typography_heading_font, 'Sarabun' ); ?>>Sarabun</option>
                                <option value="Prompt" <?php selected( $typography_heading_font, 'Prompt' ); ?>>Prompt</option>
                                <option value="Sarabun Sans" <?php selected( $typography_heading_font, 'Sarabun Sans' ); ?>>Sarabun Sans</option>
                            </select>
                            <p class="description">
                                <?php esc_html_e( 'ฟอนต์สำหรับหัวข้อ (H1-H6)', 'konderntang' ); ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="typography_body_size">
                                <span class="dashicons dashicons-admin-settings" style="vertical-align: middle; margin-right: 4px; color: #0ea5e9;"></span>
                                <?php esc_html_e( 'Body Font Size (px)', 'konderntang' ); ?>
                            </label>
                        </th>
                        <td>
                            <input type="number" name="typography_body_size" id="typography_body_size" value="<?php echo esc_attr( $typography_body_size ); ?>" min="12" max="24" step="1" style="width: 100px;" />
                            <p class="description">
                                <?php esc_html_e( 'ขนาดฟอนต์สำหรับข้อความ (12-24px)', 'konderntang' ); ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="typography_h1_size">
                                <span class="dashicons dashicons-admin-settings" style="vertical-align: middle; margin-right: 4px; color: #0ea5e9;"></span>
                                <?php esc_html_e( 'H1 Font Size (px)', 'konderntang' ); ?>
                            </label>
                        </th>
                        <td>
                            <input type="number" name="typography_h1_size" id="typography_h1_size" value="<?php echo esc_attr( $typography_h1_size ); ?>" min="24" max="72" step="1" style="width: 100px;" />
                            <p class="description">
                                <?php esc_html_e( 'ขนาดฟอนต์สำหรับ H1 (24-72px)', 'konderntang' ); ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="typography_line_height">
                                <span class="dashicons dashicons-editor-ul" style="vertical-align: middle; margin-right: 4px; color: #0ea5e9;"></span>
                                <?php esc_html_e( 'Line Height', 'konderntang' ); ?>
                            </label>
                        </th>
                        <td>
                            <input type="number" name="typography_line_height" id="typography_line_height" value="<?php echo esc_attr( $typography_line_height ); ?>" min="1.0" max="2.5" step="0.1" style="width: 100px;" />
                            <p class="description">
                                <?php esc_html_e( 'ความสูงของบรรทัด (1.0-2.5)', 'konderntang' ); ?>
                            </p>
                        </td>
                    </tr>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if ( $active_tab === 'advanced' ) : ?>
                <div style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 12px 16px; margin-bottom: 20px; border-radius: 6px;">
                    <p style="margin: 0; color: #92400e; font-weight: 500;">
                        <span class="dashicons dashicons-warning" style="vertical-align: middle; margin-right: 4px;"></span>
                        <?php esc_html_e( 'คำเตือน: การแก้ไขส่วนนี้ควรทำโดยผู้เชี่ยวชาญเท่านั้น', 'konderntang' ); ?>
                    </p>
                </div>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="advanced_custom_css">
                                <span class="dashicons dashicons-editor-code" style="vertical-align: middle; margin-right: 4px; color: #0ea5e9;"></span>
                                <?php esc_html_e( 'Custom CSS', 'konderntang' ); ?>
                            </label>
                        </th>
                        <td>
                            <textarea name="advanced_custom_css" id="advanced_custom_css" rows="12" class="large-text code" placeholder="/* เพิ่ม CSS ของคุณที่นี่ */"><?php echo esc_textarea( $advanced_custom_css ); ?></textarea>
                            <p class="description">
                                <span class="dashicons dashicons-info" style="font-size: 16px; vertical-align: middle; color: #0ea5e9;"></span>
                                <?php esc_html_e( 'เพิ่ม CSS แบบกำหนดเอง อย่ารวม <style> tags', 'konderntang' ); ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="advanced_custom_js">
                                <span class="dashicons dashicons-media-code" style="vertical-align: middle; margin-right: 4px; color: #0ea5e9;"></span>
                                <?php esc_html_e( 'Custom JavaScript', 'konderntang' ); ?>
                            </label>
                        </th>
                        <td>
                            <textarea name="advanced_custom_js" id="advanced_custom_js" rows="12" class="large-text code" placeholder="// เพิ่ม JavaScript ของคุณที่นี่"><?php echo esc_textarea( $advanced_custom_js ); ?></textarea>
                            <p class="description">
                                <span class="dashicons dashicons-info" style="font-size: 16px; vertical-align: middle; color: #0ea5e9;"></span>
                                <?php esc_html_e( 'เพิ่ม JavaScript แบบกำหนดเอง อย่ารวม <script> tags', 'konderntang' ); ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="advanced_google_analytics">
                                <span class="dashicons dashicons-chart-line" style="vertical-align: middle; margin-right: 4px; color: #0ea5e9;"></span>
                                <?php esc_html_e( 'Google Analytics Code', 'konderntang' ); ?>
                            </label>
                        </th>
                        <td>
                            <textarea name="advanced_google_analytics" id="advanced_google_analytics" rows="6" class="large-text code" placeholder="<!-- Google Analytics -->"><?php echo esc_textarea( $advanced_google_analytics ); ?></textarea>
                            <p class="description">
                                <span class="dashicons dashicons-info" style="font-size: 16px; vertical-align: middle; color: #0ea5e9;"></span>
                                <?php esc_html_e( 'วาง Google Analytics tracking code ของคุณที่นี่', 'konderntang' ); ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="advanced_facebook_pixel">
                                <span class="dashicons dashicons-facebook-alt" style="vertical-align: middle; margin-right: 4px; color: #0ea5e9;"></span>
                                <?php esc_html_e( 'Facebook Pixel Code', 'konderntang' ); ?>
                            </label>
                        </th>
                        <td>
                            <textarea name="advanced_facebook_pixel" id="advanced_facebook_pixel" rows="6" class="large-text code" placeholder="<!-- Facebook Pixel -->"><?php echo esc_textarea( $advanced_facebook_pixel ); ?></textarea>
                            <p class="description">
                                <span class="dashicons dashicons-info" style="font-size: 16px; vertical-align: middle; color: #0ea5e9;"></span>
                                <?php esc_html_e( 'วาง Facebook Pixel tracking code ของคุณที่นี่', 'konderntang' ); ?>
                            </p>
                        </td>
                    </tr>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if ( $active_tab === 'toc' ) : ?>
                <div class="konderntang-widget">
                    <div class="konderntang-widget-header">
                        <h3>
                            <span class="dashicons dashicons-list-view"></span>
                            <?php esc_html_e( 'Table of Contents Settings', 'konderntang' ); ?>
                        </h3>
                    </div>
                    <div class="konderntang-widget-content">
                        <table class="form-table">
                    <tr>
                        <th scope="row">
                            <span class="dashicons dashicons-list-view" style="vertical-align: middle; margin-right: 4px; color: #0ea5e9;"></span>
                            <?php esc_html_e( 'Table of Contents', 'konderntang' ); ?>
                        </th>
                        <td>
                            <label style="display: flex; align-items: center; gap: 8px; padding: 10px; border-radius: 6px; transition: background 0.2s; cursor: pointer;" onmouseover="this.style.background='#f0f9ff'" onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="toc_enabled" value="1" <?php checked( $toc_enabled, true ); ?> style="width: 18px; height: 18px; accent-color: #0ea5e9;" />
                                <strong><?php esc_html_e( 'Enable Table of Contents globally', 'konderntang' ); ?></strong>
                            </label>
                            <p class="description">
                                <span class="dashicons dashicons-info" style="font-size: 16px; vertical-align: middle; color: #0ea5e9;"></span>
                                <?php esc_html_e( 'หมายเหตุ: แต่ละบทความสามารถ override การตั้งค่านี้ผ่าน TOC meta box', 'konderntang' ); ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="toc_min_headings"><?php esc_html_e( 'Minimum Headings', 'konderntang' ); ?></label>
                        </th>
                        <td>
                            <input type="number" name="toc_min_headings" id="toc_min_headings" value="<?php echo esc_attr( $toc_min_headings ); ?>" min="1" max="10" step="1" />
                            <p class="description"><?php esc_html_e( 'Minimum number of headings required to show TOC', 'konderntang' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label><?php esc_html_e( 'Heading Levels', 'konderntang' ); ?></label>
                        </th>
                        <td>
                            <label>
                                <input type="checkbox" name="toc_heading_levels[]" value="h2" <?php checked( in_array( 'h2', $toc_heading_levels, true ) ); ?> />
                                <?php esc_html_e( 'H2', 'konderntang' ); ?>
                            </label><br>
                            <label>
                                <input type="checkbox" name="toc_heading_levels[]" value="h3" <?php checked( in_array( 'h3', $toc_heading_levels, true ) ); ?> />
                                <?php esc_html_e( 'H3', 'konderntang' ); ?>
                            </label><br>
                            <label>
                                <input type="checkbox" name="toc_heading_levels[]" value="h4" <?php checked( in_array( 'h4', $toc_heading_levels, true ) ); ?> />
                                <?php esc_html_e( 'H4', 'konderntang' ); ?>
                            </label>
                            <p class="description"><?php esc_html_e( 'Select which heading levels to include in TOC', 'konderntang' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="toc_title"><?php esc_html_e( 'TOC Title', 'konderntang' ); ?></label>
                        </th>
                        <td>
                            <input type="text" name="toc_title" id="toc_title" value="<?php echo esc_attr( $toc_title ); ?>" class="regular-text" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php esc_html_e( 'TOC Options', 'konderntang' ); ?>
                        </th>
                        <td>
                            <label>
                                <input type="checkbox" name="toc_collapsible" value="1" <?php checked( $toc_collapsible, true ); ?> />
                                <?php esc_html_e( 'Collapsible TOC', 'konderntang' ); ?>
                            </label><br>
                            <label>
                                <input type="checkbox" name="toc_smooth_scroll" value="1" <?php checked( $toc_smooth_scroll, true ); ?> />
                                <?php esc_html_e( 'Smooth scroll navigation', 'konderntang' ); ?>
                            </label><br>
                            <label>
                                <input type="checkbox" name="toc_scroll_spy" value="1" <?php checked( $toc_scroll_spy, true ); ?> />
                                <?php esc_html_e( 'Scroll spy (highlight current section)', 'konderntang' ); ?>
                            </label>
                        </td>
                    </tr>
                </table>
            <?php endif; ?>
            
            <?php if ( $active_tab === 'cookie' ) : ?>
                <div class="konderntang-widget">
                    <div class="konderntang-widget-header">
                        <h3>
                            <span class="dashicons dashicons-privacy"></span>
                            <?php esc_html_e( 'Cookie Consent Settings', 'konderntang' ); ?>
                        </h3>
                    </div>
                    <div class="konderntang-widget-content">
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <span class="dashicons dashicons-privacy" style="vertical-align: middle; margin-right: 4px; color: #0ea5e9;"></span>
                                    <?php esc_html_e( 'Cookie Consent', 'konderntang' ); ?>
                                </th>
                                <td>
                                    <label style="display: flex; align-items: center; gap: 8px; padding: 10px; border-radius: 6px; transition: background 0.2s; cursor: pointer;" onmouseover="this.style.background='#f0f9ff'" onmouseout="this.style.background='transparent'">
                                        <input type="checkbox" name="cookie_consent_enabled" value="1" <?php checked( $cookie_consent_enabled, true ); ?> style="width: 18px; height: 18px; accent-color: #0ea5e9;" />
                                        <strong><?php esc_html_e( 'Enable cookie consent banner', 'konderntang' ); ?></strong>
                                    </label>
                                    <p class="description">
                                        <?php esc_html_e( 'แสดง Cookie Consent Banner เพื่อให้สอดคล้องกับ GDPR', 'konderntang' ); ?>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="cookie_consent_message">
                                        <span class="dashicons dashicons-edit" style="vertical-align: middle; margin-right: 4px; color: #0ea5e9;"></span>
                                        <?php esc_html_e( 'Cookie Consent Message', 'konderntang' ); ?>
                                    </label>
                                </th>
                                <td>
                                    <textarea name="cookie_consent_message" id="cookie_consent_message" rows="3" class="large-text" placeholder="<?php esc_attr_e( 'เราใช้คุกกี้เพื่อปรับปรุงประสบการณ์การใช้งานของคุณ', 'konderntang' ); ?>"><?php echo esc_textarea( $cookie_consent_message ); ?></textarea>
                                    <p class="description">
                                        <?php esc_html_e( 'ข้อความที่จะแสดงใน Cookie Consent Banner', 'konderntang' ); ?>
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
            
            <div class="konderntang-widget" style="margin-top: 20px;">
                <div class="konderntang-widget-content">
                    <p class="submit" style="margin: 0;">
                        <input type="submit" name="konderntang_save_settings" class="button button-primary button-large" value="<?php esc_attr_e( '💾 บันทึกการตั้งค่า', 'konderntang' ); ?>" style="padding: 12px 32px; font-size: 14px; font-weight: 600;" />
                        <a href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>" class="button button-secondary" style="margin-left: 10px;">
                            <span class="dashicons dashicons-admin-appearance" style="vertical-align: middle; margin-right: 4px;"></span>
                            <?php esc_html_e( 'Customize Theme', 'konderntang' ); ?>
                        </a>
                    </p>
                </div>
            </div>
        </form>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        // Media uploader
        $('.media-upload-button').on('click', function(e) {
            e.preventDefault();
            var button = $(this);
            var targetInput = $('#' + button.data('target'));
            
            var mediaUploader = wp.media({
                title: '<?php esc_html_e( 'Choose Image', 'konderntang' ); ?>',
                button: {
                    text: '<?php esc_html_e( 'Use Image', 'konderntang' ); ?>'
                },
                multiple: false
            });
            
            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                targetInput.val(attachment.url);
            });
            
            mediaUploader.open();
        });
    });
    </script>
    <?php
}
