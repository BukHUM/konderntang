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
    // Debug: Log if function is called
    if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
        error_log( 'KonDernTang save_settings function called' );
        error_log( 'KonDernTang POST keys: ' . print_r( array_keys( $_POST ), true ) );
        error_log( 'KonDernTang POST konderntang_save_settings: ' . ( isset( $_POST['konderntang_save_settings'] ) ? 'SET' : 'NOT SET' ) );
    }
    
    if ( ! isset( $_POST['konderntang_save_settings'] ) ) {
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            error_log( 'KonDernTang save_settings: konderntang_save_settings not in POST - returning early' );
        }
        return;
    }
    
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'konderntang' ) );
    }
    
    check_admin_referer( 'konderntang_settings_nonce' );
    
    // Debug: Log after nonce check
    if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
        error_log( 'KonDernTang save_settings: Passed nonce check, processing settings...' );
    }
    
    // General Settings - Logo
    // Always check if site_logo is in POST (even if empty string)
    if ( array_key_exists( 'site_logo', $_POST ) ) {
        $site_logo = trim( esc_url_raw( $_POST['site_logo'] ) );
        
        // Debug: Log POST value
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            error_log( 'KonDernTang POST site_logo: ' . print_r( $_POST['site_logo'], true ) );
            error_log( 'KonDernTang POST site_logo (trimmed): ' . print_r( $site_logo, true ) );
        }
        
        if ( ! empty( $site_logo ) ) {
            set_theme_mod( 'site_logo', $site_logo );
            if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                error_log( 'KonDernTang site_logo saved: ' . $site_logo );
            }
        } else {
            // Only clear if explicitly empty string
            remove_theme_mod( 'site_logo' );
            if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                error_log( 'KonDernTang site_logo removed (empty)' );
            }
        }
    } else {
        // Debug: Log if site_logo is not in POST
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            error_log( 'KonDernTang site_logo NOT in POST - keeping existing value' );
        }
    }
    // If site_logo is not in POST at all, keep existing value (don't clear it)
    
    // Logo Fallback Image (optional)
    // Always check if logo_fallback_image is in POST (even if empty string)
    if ( array_key_exists( 'logo_fallback_image', $_POST ) ) {
        $logo_fallback = trim( esc_url_raw( $_POST['logo_fallback_image'] ) );
        if ( ! empty( $logo_fallback ) ) {
            set_theme_mod( 'logo_fallback_image', $logo_fallback );
        } else {
            // Only clear if explicitly empty string
            remove_theme_mod( 'logo_fallback_image' );
        }
    }
    // If logo_fallback_image is not in POST at all, keep existing value (don't clear it)
        
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
    
    // Breadcrumbs Settings
    $breadcrumbs_enabled = isset( $_POST['breadcrumbs_enabled'] ) ? '1' : '0';
    set_theme_mod( 'breadcrumbs_enabled', $breadcrumbs_enabled );
    
    if ( isset( $_POST['breadcrumbs_home_text'] ) ) {
        set_theme_mod( 'breadcrumbs_home_text', sanitize_text_field( $_POST['breadcrumbs_home_text'] ) );
    }
    
    if ( isset( $_POST['breadcrumbs_separator'] ) ) {
        set_theme_mod( 'breadcrumbs_separator', sanitize_text_field( $_POST['breadcrumbs_separator'] ) );
    }
    
    // Breadcrumbs visibility for different page types
    $breadcrumb_pages = array( 'single', 'archive', 'search', '404', 'page' );
    foreach ( $breadcrumb_pages as $page_type ) {
        $key = 'breadcrumbs_show_' . $page_type;
        $value = isset( $_POST[ $key ] ) ? '1' : '0';
        set_theme_mod( $key, $value );
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
    
    // Debug: Log saved values
    if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
        error_log( 'KonDernTang Settings Saved - site_logo: ' . print_r( konderntang_get_option( 'site_logo', '' ), true ) );
        error_log( 'KonDernTang Settings Saved - logo_fallback_image: ' . print_r( konderntang_get_option( 'logo_fallback_image', '' ), true ) );
    }
    
    add_settings_error(
        'konderntang_settings',
        'konderntang_settings_saved',
        esc_html__( 'Settings saved successfully!', 'konderntang' ),
        'success'
    );
    
    // Redirect to prevent resubmission
    $section = isset( $_POST['active_section'] ) ? sanitize_text_field( $_POST['active_section'] ) : 'general';
    $redirect_url = add_query_arg(
        array(
            'page' => 'konderntang-settings',
            'section' => $section,
            'settings-updated' => 'true',
        ),
        admin_url( 'admin.php' )
    );
    wp_safe_redirect( $redirect_url );
    exit;
}
add_action( 'admin_init', 'konderntang_save_settings' );

/**
 * Settings page render callback
 */
function konderntang_settings_page_render() {
    // Display settings errors
    settings_errors( 'konderntang_settings' );
    
    // Debug: Log loaded values
    if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
        $site_logo_debug = konderntang_get_option( 'site_logo', '' );
        error_log( 'KonDernTang Settings Page Render - site_logo loaded: ' . print_r( $site_logo_debug, true ) );
        error_log( 'KonDernTang Settings Page Render - logo_fallback_image loaded: ' . print_r( konderntang_get_option( 'logo_fallback_image', '' ), true ) );
    }
    
    // Get active section from GET parameter or default
    $active_section = isset( $_GET['section'] ) ? sanitize_text_field( $_GET['section'] ) : 'general';
    
    // Define all sections
    $sections = array(
        'general' => array(
            'label' => esc_html__( 'General', 'konderntang' ),
            'icon' => 'dashicons-admin-generic',
            'description' => esc_html__( 'Basic theme settings', 'konderntang' ),
        ),
        'header' => array(
            'label' => esc_html__( 'Header', 'konderntang' ),
            'icon' => 'dashicons-admin-appearance',
            'description' => esc_html__( 'Header navigation settings', 'konderntang' ),
        ),
        'footer' => array(
            'label' => esc_html__( 'Footer', 'konderntang' ),
            'icon' => 'dashicons-arrow-down-alt',
            'description' => esc_html__( 'Footer layout and content', 'konderntang' ),
        ),
        'homepage' => array(
            'label' => esc_html__( 'Homepage', 'konderntang' ),
            'icon' => 'dashicons-admin-home',
            'description' => esc_html__( 'Homepage sections and features', 'konderntang' ),
        ),
        'layout' => array(
            'label' => esc_html__( 'Layout', 'konderntang' ),
            'icon' => 'dashicons-layout',
            'description' => esc_html__( 'Page layout and sidebar settings', 'konderntang' ),
        ),
        'colors' => array(
            'label' => esc_html__( 'Colors', 'konderntang' ),
            'icon' => 'dashicons-art',
            'description' => esc_html__( 'Theme color scheme', 'konderntang' ),
        ),
        'typography' => array(
            'label' => esc_html__( 'Typography', 'konderntang' ),
            'icon' => 'dashicons-editor-textcolor',
            'description' => esc_html__( 'Fonts and text settings', 'konderntang' ),
        ),
        'toc' => array(
            'label' => esc_html__( 'Table of Contents', 'konderntang' ),
            'icon' => 'dashicons-list-view',
            'description' => esc_html__( 'TOC display options', 'konderntang' ),
        ),
        'cookie' => array(
            'label' => esc_html__( 'Cookie Consent', 'konderntang' ),
            'icon' => 'dashicons-privacy',
            'description' => esc_html__( 'GDPR cookie settings', 'konderntang' ),
        ),
        'advanced' => array(
            'label' => esc_html__( 'Advanced', 'konderntang' ),
            'icon' => 'dashicons-admin-tools',
            'description' => esc_html__( 'Custom code and analytics', 'konderntang' ),
        ),
    );
    
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
    
    // Breadcrumbs Settings
    $breadcrumbs_enabled = konderntang_get_option( 'breadcrumbs_enabled', true );
    $breadcrumbs_home_text = konderntang_get_option( 'breadcrumbs_home_text', esc_html__( 'หน้าแรก', 'konderntang' ) );
    $breadcrumbs_separator = konderntang_get_option( 'breadcrumbs_separator', 'caret-right' ); // caret-right, slash, arrow-right, chevron-right
    $breadcrumbs_show_single = konderntang_get_option( 'breadcrumbs_show_single', true );
    $breadcrumbs_show_archive = konderntang_get_option( 'breadcrumbs_show_archive', false );
    $breadcrumbs_show_search = konderntang_get_option( 'breadcrumbs_show_search', false );
    $breadcrumbs_show_404 = konderntang_get_option( 'breadcrumbs_show_404', false );
    $breadcrumbs_show_page = konderntang_get_option( 'breadcrumbs_show_page', false );
    
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
    <div class="wrap konderntang-settings-wrap-new">
        <div class="konderntang-settings-header">
            <h1 class="konderntang-dashboard-title">
                <span class="dashicons dashicons-admin-settings"></span>
                <?php echo esc_html( get_admin_page_title() ); ?>
            </h1>
            <div class="konderntang-settings-actions">
                <button type="submit" name="konderntang_save_settings" form="konderntang-settings-form" class="button button-primary button-large konderntang-save-btn">
                    <span class="dashicons dashicons-yes-alt"></span>
                    <?php esc_html_e( 'Save Changes', 'konderntang' ); ?>
                </button>
            </div>
        </div>
        
        <div class="konderntang-settings-layout">
            <!-- Sidebar Navigation -->
            <div class="konderntang-settings-sidebar">
                <div class="konderntang-sidebar-search">
                    <input type="text" id="konderntang-settings-search" placeholder="<?php esc_attr_e( 'Search settings...', 'konderntang' ); ?>" />
                    <span class="dashicons dashicons-search"></span>
                </div>
                <nav class="konderntang-sidebar-nav">
                    <?php foreach ( $sections as $section_key => $section_data ) : ?>
                        <a href="#section-<?php echo esc_attr( $section_key ); ?>" 
                           class="konderntang-nav-item <?php echo $active_section === $section_key ? 'active' : ''; ?>" 
                           data-section="<?php echo esc_attr( $section_key ); ?>">
                            <span class="dashicons <?php echo esc_attr( $section_data['icon'] ); ?>"></span>
                            <div class="nav-item-content">
                                <span class="nav-item-label"><?php echo esc_html( $section_data['label'] ); ?></span>
                                <span class="nav-item-desc"><?php echo esc_html( $section_data['description'] ); ?></span>
                            </div>
                        </a>
                    <?php endforeach; ?>
        </nav>
            </div>
        
            <!-- Main Content -->
            <div class="konderntang-settings-content">
        <form method="post" action="" id="konderntang-settings-form">
            <?php wp_nonce_field( 'konderntang_settings_nonce' ); ?>
            <input type="hidden" name="active_section" id="active_section" value="<?php echo esc_attr( $active_section ); ?>" />
                    
                    <!-- General Settings -->
                    <div class="konderntang-settings-section" id="section-general" data-section="general">
                        <div class="konderntang-section-header">
                            <div class="section-header-content">
                                <span class="dashicons dashicons-admin-generic"></span>
                                <div>
                                    <h2><?php esc_html_e( 'General Settings', 'konderntang' ); ?></h2>
                                    <p><?php esc_html_e( 'Basic theme configuration', 'konderntang' ); ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="konderntang-section-content">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                                        <label for="site_logo">
                                            <span class="dashicons dashicons-format-image"></span>
                                            <?php esc_html_e( 'Site Logo', 'konderntang' ); ?>
                                        </label>
                        </th>
                        <td>
                                        <?php
                                        $site_logo = konderntang_get_option( 'site_logo', '' );
                                        ?>
                                        <div class="konderntang-field-group">
                                            <input type="text" id="site_logo" name="site_logo" value="<?php echo esc_attr( $site_logo ); ?>" class="regular-text" />
                                            <button type="button" class="button media-upload-button" data-target="site_logo">
                                                <span class="dashicons dashicons-upload"></span>
                                                <?php esc_html_e( 'Upload Logo', 'konderntang' ); ?>
                                            </button>
                                            <button type="button" class="button konderntang-remove-image" <?php echo $site_logo ? '' : 'style="display:none;"'; ?>>
                                                <span class="dashicons dashicons-trash"></span>
                                            </button>
                                        </div>
                                        <div class="konderntang-image-preview" <?php echo $site_logo ? '' : 'style="display:none;"'; ?>>
                                            <?php if ( $site_logo ) : ?>
                                                <img src="<?php echo esc_url( $site_logo ); ?>" alt="Logo Preview" />
                                            <?php endif; ?>
                                        </div>
                                        <p class="description">
                                            <?php esc_html_e( 'อัปโหลดโลโก้ที่จะแสดงในส่วน header ของเว็บไซต์ หากไม่มีการอัปโหลดโลโก้ ระบบจะแสดงชื่อเว็บไซต์แทน', 'konderntang' ); ?>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="logo_fallback_image">
                                            <span class="dashicons dashicons-format-image"></span>
                                            <?php esc_html_e( 'Logo Fallback Image', 'konderntang' ); ?>
                                            <span style="color: #94a3b8; font-weight: normal; font-size: 12px;">(<?php esc_html_e( 'ไม่บังคับ', 'konderntang' ); ?>)</span>
                                        </label>
                                    </th>
                                    <td>
                                        <div class="konderntang-field-group">
                            <input type="text" id="logo_fallback_image" name="logo_fallback_image" value="<?php echo esc_attr( $logo_fallback_image ); ?>" class="regular-text" />
                            <button type="button" class="button media-upload-button" data-target="logo_fallback_image">
                                                <span class="dashicons dashicons-upload"></span>
                                                <?php esc_html_e( 'Upload', 'konderntang' ); ?>
                            </button>
                                            <button type="button" class="button konderntang-remove-image" <?php echo $logo_fallback_image ? '' : 'style="display:none;"'; ?>>
                                                <span class="dashicons dashicons-trash"></span>
                                            </button>
                                        </div>
                                        <div class="konderntang-image-preview" <?php echo $logo_fallback_image ? '' : 'style="display:none;"'; ?>>
                                            <?php if ( $logo_fallback_image ) : ?>
                                                <img src="<?php echo esc_url( $logo_fallback_image ); ?>" alt="Fallback Logo Preview" />
                                            <?php endif; ?>
                                        </div>
                                        <p class="description">
                                            <?php esc_html_e( 'โลโก้สำรอง (ไม่บังคับ) - จะใช้เมื่อไม่มี Site Logo และไม่มี WordPress Custom Logo', 'konderntang' ); ?>
                                        </p>
                        </td>
                    </tr>
                </table>
                        </div>
                    </div>
                    
                    <!-- Header Settings -->
                    <div class="konderntang-settings-section" id="section-header" data-section="header">
                        <div class="konderntang-section-header">
                            <div class="section-header-content">
                                <span class="dashicons dashicons-admin-appearance"></span>
                                <div>
                                    <h2><?php esc_html_e( 'Header Settings', 'konderntang' ); ?></h2>
                                    <p><?php esc_html_e( 'Configure header navigation', 'konderntang' ); ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="konderntang-section-content">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                                        <span class="dashicons dashicons-search"></span>
                            <?php esc_html_e( 'Search Button', 'konderntang' ); ?>
                        </th>
                        <td>
                                        <label class="konderntang-toggle">
                                <input type="checkbox" name="header_show_search" value="1" <?php checked( $header_show_search, true ); ?> />
                                            <span class="toggle-slider"></span>
                                            <span class="toggle-label"><?php esc_html_e( 'Show search button in header', 'konderntang' ); ?></span>
                            </label>
                                        <p class="description">
                                            <?php esc_html_e( 'แสดงปุ่มค้นหาในส่วน header navigation', 'konderntang' ); ?>
                                        </p>
                        </td>
                    </tr>
                </table>
                        </div>
                    </div>
                    
                    <!-- Footer Settings -->
                    <div class="konderntang-settings-section" id="section-footer" data-section="footer">
                        <div class="konderntang-section-header">
                            <div class="section-header-content">
                                <span class="dashicons dashicons-arrow-down-alt"></span>
                                <div>
                                    <h2><?php esc_html_e( 'Footer Settings', 'konderntang' ); ?></h2>
                                    <p><?php esc_html_e( 'Footer layout and content', 'konderntang' ); ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="konderntang-section-content">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                                        <label for="footer_layout">
                                            <span class="dashicons dashicons-layout"></span>
                                            <?php esc_html_e( 'Footer Layout', 'konderntang' ); ?>
                                        </label>
                        </th>
                        <td>
                                        <select name="footer_layout" id="footer_layout" class="regular-text">
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
                                            <span class="dashicons dashicons-edit"></span>
                                            <?php esc_html_e( 'Copyright Text', 'konderntang' ); ?>
                                        </label>
                        </th>
                        <td>
                                        <textarea name="footer_copyright_text" id="footer_copyright_text" rows="3" class="large-text" placeholder="<?php esc_attr_e( '&copy; %Y% %SITE_NAME% - เพื่อนเดินทางของคุณ', 'konderntang' ); ?>"><?php echo esc_textarea( $footer_copyright_text ); ?></textarea>
                                        <p class="description">
                                            <?php esc_html_e( 'ใช้ %Y% สำหรับปี, %SITE_NAME% สำหรับชื่อเว็บไซต์', 'konderntang' ); ?>
                                        </p>
                        </td>
                    </tr>
                </table>
                        </div>
                    </div>
                    
                    <!-- Homepage Settings -->
                    <div class="konderntang-settings-section" id="section-homepage" data-section="homepage">
                        <div class="konderntang-section-header">
                            <div class="section-header-content">
                                <span class="dashicons dashicons-admin-home"></span>
                                <div>
                                    <h2><?php esc_html_e( 'Homepage Settings', 'konderntang' ); ?></h2>
                                    <p><?php esc_html_e( 'Configure homepage sections', 'konderntang' ); ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="konderntang-section-content">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                                        <span class="dashicons dashicons-slides"></span>
                            <?php esc_html_e( 'Hero Slider', 'konderntang' ); ?>
                        </th>
                        <td>
                                        <label class="konderntang-toggle">
                                <input type="checkbox" name="hero_slider_enabled" value="1" <?php checked( $hero_slider_enabled, true ); ?> />
                                            <span class="toggle-slider"></span>
                                            <span class="toggle-label"><?php esc_html_e( 'Enable hero slider on homepage', 'konderntang' ); ?></span>
                            </label>
                                        <p class="description">
                                            <?php esc_html_e( 'แสดง Hero Slider ที่ด้านบนของหน้าแรก', 'konderntang' ); ?>
                                        </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                                        <label for="hero_slider_posts">
                                            <span class="dashicons dashicons-images-alt2"></span>
                                            <?php esc_html_e( 'Number of Hero Slides', 'konderntang' ); ?>
                                        </label>
                        </th>
                        <td>
                                        <input type="number" name="hero_slider_posts" id="hero_slider_posts" value="<?php echo esc_attr( $hero_slider_posts ); ?>" min="1" max="10" step="1" class="small-text" />
                                        <p class="description">
                                            <?php esc_html_e( 'จำนวนสไลด์ที่จะแสดงใน Hero Slider (1-10)', 'konderntang' ); ?>
                                        </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                                        <span class="dashicons dashicons-star-filled"></span>
                            <?php esc_html_e( 'Featured Section', 'konderntang' ); ?>
                        </th>
                        <td>
                                        <label class="konderntang-toggle">
                                <input type="checkbox" name="featured_section_enabled" value="1" <?php checked( $featured_section_enabled, true ); ?> />
                                            <span class="toggle-slider"></span>
                                            <span class="toggle-label"><?php esc_html_e( 'Enable featured section on homepage', 'konderntang' ); ?></span>
                            </label>
                                        <p class="description">
                                            <?php esc_html_e( 'แสดงส่วนบทความแนะนำบนหน้าแรก', 'konderntang' ); ?>
                                        </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                                        <label for="featured_posts_count">
                                            <span class="dashicons dashicons-admin-post"></span>
                                            <?php esc_html_e( 'Number of Featured Posts', 'konderntang' ); ?>
                                        </label>
                        </th>
                        <td>
                                        <input type="number" name="featured_posts_count" id="featured_posts_count" value="<?php echo esc_attr( $featured_posts_count ); ?>" min="1" max="10" step="1" class="small-text" />
                                        <p class="description">
                                            <?php esc_html_e( 'จำนวนบทความแนะนำที่จะแสดง (1-10)', 'konderntang' ); ?>
                                        </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                                        <label for="recent_posts_count">
                                            <span class="dashicons dashicons-clock"></span>
                                            <?php esc_html_e( 'Number of Recent Posts', 'konderntang' ); ?>
                                        </label>
                        </th>
                        <td>
                                        <input type="number" name="recent_posts_count" id="recent_posts_count" value="<?php echo esc_attr( $recent_posts_count ); ?>" min="1" max="20" step="1" class="small-text" />
                                        <p class="description">
                                            <?php esc_html_e( 'จำนวนบทความล่าสุดที่จะแสดง (1-20)', 'konderntang' ); ?>
                                        </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                                        <span class="dashicons dashicons-email-alt"></span>
                            <?php esc_html_e( 'Newsletter', 'konderntang' ); ?>
                        </th>
                        <td>
                                        <label class="konderntang-toggle">
                                <input type="checkbox" name="newsletter_enabled" value="1" <?php checked( $newsletter_enabled, true ); ?> />
                                            <span class="toggle-slider"></span>
                                            <span class="toggle-label"><?php esc_html_e( 'Enable newsletter section on homepage', 'konderntang' ); ?></span>
                            </label>
                                        <p class="description">
                                            <?php esc_html_e( 'แสดงส่วนสมัครรับจดหมายข่าวบนหน้าแรก', 'konderntang' ); ?>
                                        </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                                        <label for="trending_tags_count">
                                            <span class="dashicons dashicons-tag"></span>
                                            <?php esc_html_e( 'Number of Trending Tags', 'konderntang' ); ?>
                                        </label>
                        </th>
                        <td>
                                        <input type="number" name="trending_tags_count" id="trending_tags_count" value="<?php echo esc_attr( $trending_tags_count ); ?>" min="1" max="30" step="1" class="small-text" />
                                        <p class="description">
                                            <?php esc_html_e( 'จำนวนแท็กยอดนิยมที่จะแสดง (1-30)', 'konderntang' ); ?>
                                        </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                                        <span class="dashicons dashicons-visibility"></span>
                            <?php esc_html_e( 'Recently Viewed', 'konderntang' ); ?>
                        </th>
                        <td>
                                        <label class="konderntang-toggle">
                                <input type="checkbox" name="recently_viewed_enabled" value="1" <?php checked( $recently_viewed_enabled, true ); ?> />
                                            <span class="toggle-slider"></span>
                                            <span class="toggle-label"><?php esc_html_e( 'Enable recently viewed posts section', 'konderntang' ); ?></span>
                            </label>
                                        <p class="description">
                                            <?php esc_html_e( 'แสดงส่วนบทความที่ดูล่าสุด', 'konderntang' ); ?>
                                        </p>
                        </td>
                    </tr>
                </table>
                        </div>
                    </div>
                    
                    <!-- Layout Settings -->
                    <div class="konderntang-settings-section" id="section-layout" data-section="layout">
                        <div class="konderntang-section-header">
                            <div class="section-header-content">
                                <span class="dashicons dashicons-layout"></span>
                                <div>
                                    <h2><?php esc_html_e( 'Layout Settings', 'konderntang' ); ?></h2>
                                    <p><?php esc_html_e( 'Page layout and sidebar configuration', 'konderntang' ); ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="konderntang-section-content">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                                        <label for="layout_container_width">
                                            <span class="dashicons dashicons-editor-expand"></span>
                                            <?php esc_html_e( 'Container Width (px)', 'konderntang' ); ?>
                                        </label>
                        </th>
                        <td>
                                        <input type="number" name="layout_container_width" id="layout_container_width" value="<?php echo esc_attr( $layout_container_width ); ?>" min="960" max="1920" step="10" class="small-text" />
                                        <p class="description">
                                            <?php esc_html_e( 'ความกว้างสูงสุดของเนื้อหาเว็บไซต์ (960-1920px)', 'konderntang' ); ?>
                                        </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                                        <label for="layout_archive_sidebar">
                                            <span class="dashicons dashicons-align-wide"></span>
                                            <?php esc_html_e( 'Archive Sidebar Position', 'konderntang' ); ?>
                                        </label>
                        </th>
                        <td>
                                        <select name="layout_archive_sidebar" id="layout_archive_sidebar" class="regular-text">
                                <option value="left" <?php selected( $layout_archive_sidebar, 'left' ); ?>><?php esc_html_e( 'Left', 'konderntang' ); ?></option>
                                <option value="right" <?php selected( $layout_archive_sidebar, 'right' ); ?>><?php esc_html_e( 'Right', 'konderntang' ); ?></option>
                                <option value="none" <?php selected( $layout_archive_sidebar, 'none' ); ?>><?php esc_html_e( 'None', 'konderntang' ); ?></option>
                            </select>
                                        <p class="description">
                                            <?php esc_html_e( 'ตำแหน่ง Sidebar สำหรับหน้า Archive (หมวดหมู่, แท็ก)', 'konderntang' ); ?>
                                        </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                                        <label for="layout_single_sidebar">
                                            <span class="dashicons dashicons-align-wide"></span>
                                            <?php esc_html_e( 'Single Post Sidebar Position', 'konderntang' ); ?>
                                        </label>
                        </th>
                        <td>
                                        <select name="layout_single_sidebar" id="layout_single_sidebar" class="regular-text">
                                <option value="left" <?php selected( $layout_single_sidebar, 'left' ); ?>><?php esc_html_e( 'Left', 'konderntang' ); ?></option>
                                <option value="right" <?php selected( $layout_single_sidebar, 'right' ); ?>><?php esc_html_e( 'Right', 'konderntang' ); ?></option>
                                <option value="none" <?php selected( $layout_single_sidebar, 'none' ); ?>><?php esc_html_e( 'None', 'konderntang' ); ?></option>
                            </select>
                                        <p class="description">
                                            <?php esc_html_e( 'ตำแหน่ง Sidebar สำหรับหน้าบทความเดี่ยว', 'konderntang' ); ?>
                                        </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                                        <label for="layout_posts_per_page">
                                            <span class="dashicons dashicons-media-text"></span>
                                            <?php esc_html_e( 'Posts Per Page', 'konderntang' ); ?>
                                        </label>
                        </th>
                        <td>
                                        <input type="number" name="layout_posts_per_page" id="layout_posts_per_page" value="<?php echo esc_attr( $layout_posts_per_page ); ?>" min="1" max="50" step="1" class="small-text" />
                                        <p class="description">
                                            <?php esc_html_e( 'จำนวนบทความที่จะแสดงต่อหน้า (1-50)', 'konderntang' ); ?>
                                        </p>
                        </td>
                    </tr>
                    
                    <!-- Breadcrumbs Settings -->
                    <tr>
                        <th scope="row" colspan="2">
                            <h3 style="margin: 20px 0 10px; padding-bottom: 10px; border-bottom: 1px solid #e2e8f0;">
                                <span class="dashicons dashicons-admin-links"></span>
                                <?php esc_html_e( 'Breadcrumbs Settings', 'konderntang' ); ?>
                            </h3>
                        </th>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="breadcrumbs_enabled">
                                <span class="dashicons dashicons-visibility"></span>
                                <?php esc_html_e( 'Enable Breadcrumbs', 'konderntang' ); ?>
                            </label>
                        </th>
                        <td>
                            <label>
                                <input type="checkbox" name="breadcrumbs_enabled" id="breadcrumbs_enabled" value="1" <?php checked( $breadcrumbs_enabled, true ); ?> />
                                <?php esc_html_e( 'แสดง Breadcrumbs ในเว็บไซต์', 'konderntang' ); ?>
                            </label>
                            <p class="description">
                                <?php esc_html_e( 'เปิด/ปิดการแสดง Breadcrumbs ทั้งหมด', 'konderntang' ); ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="breadcrumbs_home_text">
                                <span class="dashicons dashicons-admin-home"></span>
                                <?php esc_html_e( 'Home Text', 'konderntang' ); ?>
                            </label>
                        </th>
                        <td>
                            <input type="text" name="breadcrumbs_home_text" id="breadcrumbs_home_text" value="<?php echo esc_attr( $breadcrumbs_home_text ); ?>" class="regular-text" />
                            <p class="description">
                                <?php esc_html_e( 'ข้อความที่แสดงสำหรับลิงก์หน้าแรก', 'konderntang' ); ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="breadcrumbs_separator">
                                <span class="dashicons dashicons-arrow-right-alt"></span>
                                <?php esc_html_e( 'Separator', 'konderntang' ); ?>
                            </label>
                        </th>
                        <td>
                            <select name="breadcrumbs_separator" id="breadcrumbs_separator" class="regular-text">
                                <option value="caret-right" <?php selected( $breadcrumbs_separator, 'caret-right' ); ?>><?php esc_html_e( 'Caret Right (>)', 'konderntang' ); ?></option>
                                <option value="slash" <?php selected( $breadcrumbs_separator, 'slash' ); ?>><?php esc_html_e( 'Slash (/)', 'konderntang' ); ?></option>
                                <option value="arrow-right" <?php selected( $breadcrumbs_separator, 'arrow-right' ); ?>><?php esc_html_e( 'Arrow Right (→)', 'konderntang' ); ?></option>
                                <option value="chevron-right" <?php selected( $breadcrumbs_separator, 'chevron-right' ); ?>><?php esc_html_e( 'Chevron Right (»)', 'konderntang' ); ?></option>
                            </select>
                            <p class="description">
                                <?php esc_html_e( 'เลือกรูปแบบตัวคั่นระหว่าง Breadcrumb items', 'konderntang' ); ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label>
                                <span class="dashicons dashicons-admin-page"></span>
                                <?php esc_html_e( 'Show On', 'konderntang' ); ?>
                            </label>
                        </th>
                        <td>
                            <fieldset>
                                <label style="display: block; margin-bottom: 8px;">
                                    <input type="checkbox" name="breadcrumbs_show_single" value="1" <?php checked( $breadcrumbs_show_single, true ); ?> />
                                    <?php esc_html_e( 'Single Posts', 'konderntang' ); ?>
                                </label>
                                <label style="display: block; margin-bottom: 8px;">
                                    <input type="checkbox" name="breadcrumbs_show_archive" value="1" <?php checked( $breadcrumbs_show_archive, true ); ?> />
                                    <?php esc_html_e( 'Archive Pages', 'konderntang' ); ?>
                                </label>
                                <label style="display: block; margin-bottom: 8px;">
                                    <input type="checkbox" name="breadcrumbs_show_page" value="1" <?php checked( $breadcrumbs_show_page, true ); ?> />
                                    <?php esc_html_e( 'Pages', 'konderntang' ); ?>
                                </label>
                                <label style="display: block; margin-bottom: 8px;">
                                    <input type="checkbox" name="breadcrumbs_show_search" value="1" <?php checked( $breadcrumbs_show_search, true ); ?> />
                                    <?php esc_html_e( 'Search Results', 'konderntang' ); ?>
                                </label>
                                <label style="display: block; margin-bottom: 8px;">
                                    <input type="checkbox" name="breadcrumbs_show_404" value="1" <?php checked( $breadcrumbs_show_404, true ); ?> />
                                    <?php esc_html_e( '404 Error Page', 'konderntang' ); ?>
                                </label>
                            </fieldset>
                            <p class="description">
                                <?php esc_html_e( 'เลือกประเภทหน้าที่ต้องการแสดง Breadcrumbs', 'konderntang' ); ?>
                            </p>
                        </td>
                    </tr>
                </table>
                        </div>
                    </div>
                    
                    <!-- Color Settings -->
                    <div class="konderntang-settings-section" id="section-colors" data-section="colors">
                        <div class="konderntang-section-header">
                            <div class="section-header-content">
                                <span class="dashicons dashicons-art"></span>
                                <div>
                                    <h2><?php esc_html_e( 'Color Settings', 'konderntang' ); ?></h2>
                                    <p><?php esc_html_e( 'Customize theme colors', 'konderntang' ); ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="konderntang-section-content">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                                        <label for="color_primary">
                                            <span class="dashicons dashicons-admin-appearance"></span>
                                            <?php esc_html_e( 'Primary Color', 'konderntang' ); ?>
                                        </label>
                        </th>
                        <td>
                                        <div class="konderntang-color-picker-group">
                            <input type="color" name="color_primary" id="color_primary" value="<?php echo esc_attr( $color_primary ); ?>" />
                                            <input type="text" value="<?php echo esc_attr( $color_primary ); ?>" readonly class="konderntang-color-value" />
                                            <span class="description"><?php esc_html_e( 'สีหลักของธีม (ปุ่ม, ลิงก์, ไฮไลท์)', 'konderntang' ); ?></span>
                                        </div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                                        <label for="color_secondary">
                                            <span class="dashicons dashicons-admin-appearance"></span>
                                            <?php esc_html_e( 'Secondary Color', 'konderntang' ); ?>
                                        </label>
                        </th>
                        <td>
                                        <div class="konderntang-color-picker-group">
                            <input type="color" name="color_secondary" id="color_secondary" value="<?php echo esc_attr( $color_secondary ); ?>" />
                                            <input type="text" value="<?php echo esc_attr( $color_secondary ); ?>" readonly class="konderntang-color-value" />
                                            <span class="description"><?php esc_html_e( 'สีรองของธีม (ปุ่มพิเศษ, badge)', 'konderntang' ); ?></span>
                                        </div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                                        <label for="color_text">
                                            <span class="dashicons dashicons-editor-textcolor"></span>
                                            <?php esc_html_e( 'Text Color', 'konderntang' ); ?>
                                        </label>
                        </th>
                        <td>
                                        <div class="konderntang-color-picker-group">
                            <input type="color" name="color_text" id="color_text" value="<?php echo esc_attr( $color_text ); ?>" />
                                            <input type="text" value="<?php echo esc_attr( $color_text ); ?>" readonly class="konderntang-color-value" />
                                            <span class="description"><?php esc_html_e( 'สีข้อความหลัก', 'konderntang' ); ?></span>
                                        </div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                                        <label for="color_background">
                                            <span class="dashicons dashicons-admin-appearance"></span>
                                            <?php esc_html_e( 'Background Color', 'konderntang' ); ?>
                                        </label>
                        </th>
                        <td>
                                        <div class="konderntang-color-picker-group">
                            <input type="color" name="color_background" id="color_background" value="<?php echo esc_attr( $color_background ); ?>" />
                                            <input type="text" value="<?php echo esc_attr( $color_background ); ?>" readonly class="konderntang-color-value" />
                                            <span class="description"><?php esc_html_e( 'สีพื้นหลังหลัก', 'konderntang' ); ?></span>
                                        </div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                                        <label for="color_link">
                                            <span class="dashicons dashicons-admin-links"></span>
                                            <?php esc_html_e( 'Link Color', 'konderntang' ); ?>
                                        </label>
                        </th>
                        <td>
                                        <div class="konderntang-color-picker-group">
                            <input type="color" name="color_link" id="color_link" value="<?php echo esc_attr( $color_link ); ?>" />
                                            <input type="text" value="<?php echo esc_attr( $color_link ); ?>" readonly class="konderntang-color-value" />
                                            <span class="description"><?php esc_html_e( 'สีลิงก์', 'konderntang' ); ?></span>
                                        </div>
                        </td>
                    </tr>
                </table>
                        </div>
                    </div>
                    
                    <!-- Typography Settings -->
                    <div class="konderntang-settings-section" id="section-typography" data-section="typography">
                        <div class="konderntang-section-header">
                            <div class="section-header-content">
                                <span class="dashicons dashicons-editor-textcolor"></span>
                                <div>
                                    <h2><?php esc_html_e( 'Typography Settings', 'konderntang' ); ?></h2>
                                    <p><?php esc_html_e( 'Font and text styling', 'konderntang' ); ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="konderntang-section-content">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                                        <label for="typography_body_font">
                                            <span class="dashicons dashicons-editor-bold"></span>
                                            <?php esc_html_e( 'Body Font', 'konderntang' ); ?>
                                        </label>
                        </th>
                        <td>
                                        <select name="typography_body_font" id="typography_body_font" class="regular-text">
                                <option value="Sarabun" <?php selected( $typography_body_font, 'Sarabun' ); ?>>Sarabun</option>
                                <option value="Kanit" <?php selected( $typography_body_font, 'Kanit' ); ?>>Kanit</option>
                                <option value="Prompt" <?php selected( $typography_body_font, 'Prompt' ); ?>>Prompt</option>
                                <option value="Sarabun Sans" <?php selected( $typography_body_font, 'Sarabun Sans' ); ?>>Sarabun Sans</option>
                            </select>
                                        <p class="description">
                                            <?php esc_html_e( 'เลือกฟอนต์สำหรับเนื้อหาหลัก', 'konderntang' ); ?>
                                        </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                                        <label for="typography_heading_font">
                                            <span class="dashicons dashicons-editor-bold"></span>
                                            <?php esc_html_e( 'Heading Font', 'konderntang' ); ?>
                                        </label>
                        </th>
                        <td>
                                        <select name="typography_heading_font" id="typography_heading_font" class="regular-text">
                                <option value="Kanit" <?php selected( $typography_heading_font, 'Kanit' ); ?>>Kanit</option>
                                <option value="Sarabun" <?php selected( $typography_heading_font, 'Sarabun' ); ?>>Sarabun</option>
                                <option value="Prompt" <?php selected( $typography_heading_font, 'Prompt' ); ?>>Prompt</option>
                                <option value="Sarabun Sans" <?php selected( $typography_heading_font, 'Sarabun Sans' ); ?>>Sarabun Sans</option>
                            </select>
                                        <p class="description">
                                            <?php esc_html_e( 'เลือกฟอนต์สำหรับหัวข้อ', 'konderntang' ); ?>
                                        </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                                        <label for="typography_body_size">
                                            <span class="dashicons dashicons-editor-contract"></span>
                                            <?php esc_html_e( 'Body Font Size (px)', 'konderntang' ); ?>
                                        </label>
                        </th>
                        <td>
                                        <input type="number" name="typography_body_size" id="typography_body_size" value="<?php echo esc_attr( $typography_body_size ); ?>" min="12" max="24" step="1" class="small-text" />
                                        <p class="description">
                                            <?php esc_html_e( 'ขนาดฟอนต์สำหรับข้อความ (12-24px)', 'konderntang' ); ?>
                                        </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                                        <label for="typography_h1_size">
                                            <span class="dashicons dashicons-admin-settings"></span>
                                            <?php esc_html_e( 'H1 Font Size (px)', 'konderntang' ); ?>
                                        </label>
                        </th>
                        <td>
                                        <input type="number" name="typography_h1_size" id="typography_h1_size" value="<?php echo esc_attr( $typography_h1_size ); ?>" min="24" max="72" step="1" class="small-text" />
                                        <p class="description">
                                            <?php esc_html_e( 'ขนาดฟอนต์สำหรับ H1 (24-72px)', 'konderntang' ); ?>
                                        </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                                        <label for="typography_line_height">
                                            <span class="dashicons dashicons-editor-ul"></span>
                                            <?php esc_html_e( 'Line Height', 'konderntang' ); ?>
                                        </label>
                        </th>
                        <td>
                                        <input type="number" name="typography_line_height" id="typography_line_height" value="<?php echo esc_attr( $typography_line_height ); ?>" min="1.0" max="2.5" step="0.1" class="small-text" />
                                        <p class="description">
                                            <?php esc_html_e( 'ความสูงของบรรทัด (1.0-2.5)', 'konderntang' ); ?>
                                        </p>
                        </td>
                    </tr>
                </table>
                        </div>
                    </div>
                    
                    <!-- Table of Contents Settings -->
                    <div class="konderntang-settings-section" id="section-toc" data-section="toc">
                        <div class="konderntang-section-header">
                            <div class="section-header-content">
                                <span class="dashicons dashicons-list-view"></span>
                                <div>
                                    <h2><?php esc_html_e( 'Table of Contents Settings', 'konderntang' ); ?></h2>
                                    <p><?php esc_html_e( 'Configure TOC display options', 'konderntang' ); ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="konderntang-section-content">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                                        <span class="dashicons dashicons-list-view"></span>
                                        <?php esc_html_e( 'Table of Contents', 'konderntang' ); ?>
                        </th>
                        <td>
                                        <label class="konderntang-toggle">
                                            <input type="checkbox" name="toc_enabled" value="1" <?php checked( $toc_enabled, true ); ?> />
                                            <span class="toggle-slider"></span>
                                            <span class="toggle-label"><?php esc_html_e( 'Enable Table of Contents globally', 'konderntang' ); ?></span>
                                        </label>
                                        <p class="description">
                                            <?php esc_html_e( 'Note: Individual posts can override this setting via the TOC meta box.', 'konderntang' ); ?>
                                        </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                                        <label for="toc_min_headings">
                                            <span class="dashicons dashicons-editor-ol"></span>
                                            <?php esc_html_e( 'Minimum Headings', 'konderntang' ); ?>
                                        </label>
                        </th>
                        <td>
                                        <input type="number" name="toc_min_headings" id="toc_min_headings" value="<?php echo esc_attr( $toc_min_headings ); ?>" min="1" max="10" step="1" class="small-text" />
                                        <p class="description">
                                            <?php esc_html_e( 'จำนวนหัวข้อขั้นต่ำที่ต้องมีเพื่อแสดงสารบัญ (1-10)', 'konderntang' ); ?>
                                        </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                                        <span class="dashicons dashicons-editor-ul"></span>
                                        <?php esc_html_e( 'Heading Levels', 'konderntang' ); ?>
                        </th>
                        <td>
                                        <div class="konderntang-checkbox-group">
                                            <label class="konderntang-checkbox-label">
                                                <input type="checkbox" name="toc_heading_levels[]" value="h2" <?php checked( in_array( 'h2', $toc_heading_levels, true ) ); ?> />
                                                <span><?php esc_html_e( 'H2', 'konderntang' ); ?></span>
                                            </label>
                                            <label class="konderntang-checkbox-label">
                                                <input type="checkbox" name="toc_heading_levels[]" value="h3" <?php checked( in_array( 'h3', $toc_heading_levels, true ) ); ?> />
                                                <span><?php esc_html_e( 'H3', 'konderntang' ); ?></span>
                                            </label>
                                            <label class="konderntang-checkbox-label">
                                                <input type="checkbox" name="toc_heading_levels[]" value="h4" <?php checked( in_array( 'h4', $toc_heading_levels, true ) ); ?> />
                                                <span><?php esc_html_e( 'H4', 'konderntang' ); ?></span>
                                            </label>
                                        </div>
                                        <p class="description">
                                            <?php esc_html_e( 'เลือกหัวข้อที่จะรวมในสารบัญ', 'konderntang' ); ?>
                                        </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                                        <label for="toc_title">
                                            <span class="dashicons dashicons-edit"></span>
                                            <?php esc_html_e( 'TOC Title', 'konderntang' ); ?>
                                        </label>
                        </th>
                        <td>
                                        <input type="text" name="toc_title" id="toc_title" value="<?php echo esc_attr( $toc_title ); ?>" class="regular-text" placeholder="<?php esc_attr_e( 'สารบัญ', 'konderntang' ); ?>" />
                                        <p class="description">
                                            <?php esc_html_e( 'ข้อความหัวข้อสำหรับสารบัญ', 'konderntang' ); ?>
                                        </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                                        <span class="dashicons dashicons-admin-settings"></span>
                                        <?php esc_html_e( 'TOC Options', 'konderntang' ); ?>
                        </th>
                        <td>
                                        <div class="konderntang-checkbox-group">
                                            <label class="konderntang-checkbox-label">
                                                <input type="checkbox" name="toc_collapsible" value="1" <?php checked( $toc_collapsible, true ); ?> />
                                                <span><?php esc_html_e( 'Collapsible TOC', 'konderntang' ); ?></span>
                            </label>
                                            <label class="konderntang-checkbox-label">
                                                <input type="checkbox" name="toc_smooth_scroll" value="1" <?php checked( $toc_smooth_scroll, true ); ?> />
                                                <span><?php esc_html_e( 'Smooth scroll navigation', 'konderntang' ); ?></span>
                                            </label>
                                            <label class="konderntang-checkbox-label">
                                                <input type="checkbox" name="toc_scroll_spy" value="1" <?php checked( $toc_scroll_spy, true ); ?> />
                                                <span><?php esc_html_e( 'Scroll spy (highlight current section)', 'konderntang' ); ?></span>
                                            </label>
                                        </div>
                                        <p class="description">
                                            <?php esc_html_e( 'เปิดใช้งานตัวเลือกเพิ่มเติมสำหรับสารบัญ', 'konderntang' ); ?>
                                        </p>
                        </td>
                    </tr>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Cookie Consent Settings -->
                    <div class="konderntang-settings-section" id="section-cookie" data-section="cookie">
                        <div class="konderntang-section-header">
                            <div class="section-header-content">
                                <span class="dashicons dashicons-privacy"></span>
                                <div>
                                    <h2><?php esc_html_e( 'Cookie Consent Settings', 'konderntang' ); ?></h2>
                                    <p><?php esc_html_e( 'GDPR cookie banner configuration', 'konderntang' ); ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="konderntang-section-content">
                            <table class="form-table">
                    <tr>
                        <th scope="row">
                                        <span class="dashicons dashicons-privacy"></span>
                                        <?php esc_html_e( 'Cookie Consent', 'konderntang' ); ?>
                        </th>
                        <td>
                                        <label class="konderntang-toggle">
                                            <input type="checkbox" name="cookie_consent_enabled" value="1" <?php checked( $cookie_consent_enabled, true ); ?> />
                                            <span class="toggle-slider"></span>
                                            <span class="toggle-label"><?php esc_html_e( 'Enable cookie consent banner', 'konderntang' ); ?></span>
                                        </label>
                                        <p class="description">
                                            <?php esc_html_e( 'แสดง Cookie Consent Banner เพื่อให้สอดคล้องกับ GDPR', 'konderntang' ); ?>
                                        </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                                        <label for="cookie_consent_message">
                                            <span class="dashicons dashicons-edit"></span>
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
                    
                    <!-- Advanced Settings -->
                    <div class="konderntang-settings-section" id="section-advanced" data-section="advanced">
                        <div class="konderntang-section-header">
                            <div class="section-header-content">
                                <span class="dashicons dashicons-admin-tools"></span>
                                <div>
                                    <h2><?php esc_html_e( 'Advanced Settings', 'konderntang' ); ?></h2>
                                    <p><?php esc_html_e( 'Custom code and analytics', 'konderntang' ); ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="konderntang-section-content">
                            <div class="konderntang-warning-box">
                                <span class="dashicons dashicons-warning"></span>
                                <p><?php esc_html_e( 'ข้อควรระวัง: การแก้ไขการตั้งค่าขั้นสูงอาจส่งผลต่อการทำงานของเว็บไซต์ หากไม่แน่ใจ ควรปรึกษาผู้เชี่ยวชาญ', 'konderntang' ); ?></p>
                            </div>
                            <table class="form-table">
                    <tr>
                        <th scope="row">
                                        <label for="advanced_custom_css">
                                            <span class="dashicons dashicons-editor-code"></span>
                                            <?php esc_html_e( 'Custom CSS', 'konderntang' ); ?>
                                        </label>
                        </th>
                        <td>
                                        <textarea name="advanced_custom_css" id="advanced_custom_css" rows="10" class="large-text code" placeholder="/* Custom CSS */"><?php echo esc_textarea( $advanced_custom_css ); ?></textarea>
                                        <p class="description">
                                            <?php esc_html_e( 'เพิ่มโค้ด CSS ที่กำหนดเอง ไม่ต้องใส่แท็ก <style>', 'konderntang' ); ?>
                                        </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                                        <label for="advanced_custom_js">
                                            <span class="dashicons dashicons-media-code"></span>
                                            <?php esc_html_e( 'Custom JavaScript', 'konderntang' ); ?>
                                        </label>
                        </th>
                        <td>
                                        <textarea name="advanced_custom_js" id="advanced_custom_js" rows="10" class="large-text code" placeholder="// Custom JavaScript"><?php echo esc_textarea( $advanced_custom_js ); ?></textarea>
                                        <p class="description">
                                            <?php esc_html_e( 'เพิ่มโค้ด JavaScript ที่กำหนดเอง ไม่ต้องใส่แท็ก <script>', 'konderntang' ); ?>
                                        </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                                        <label for="advanced_google_analytics">
                                            <span class="dashicons dashicons-chart-area"></span>
                                            <?php esc_html_e( 'Google Analytics Code', 'konderntang' ); ?>
                                        </label>
                        </th>
                        <td>
                                        <textarea name="advanced_google_analytics" id="advanced_google_analytics" rows="6" class="large-text code" placeholder="<!-- Google Analytics -->"><?php echo esc_textarea( $advanced_google_analytics ); ?></textarea>
                                        <p class="description">
                                            <?php esc_html_e( 'วาง Google Analytics tracking code ของคุณที่นี่', 'konderntang' ); ?>
                                        </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                                        <label for="advanced_facebook_pixel">
                                            <span class="dashicons dashicons-facebook-alt"></span>
                                            <?php esc_html_e( 'Facebook Pixel Code', 'konderntang' ); ?></label>
                        </th>
                        <td>
                                        <textarea name="advanced_facebook_pixel" id="advanced_facebook_pixel" rows="6" class="large-text code" placeholder="<!-- Facebook Pixel -->"><?php echo esc_textarea( $advanced_facebook_pixel ); ?></textarea>
                                        <p class="description">
                                            <?php esc_html_e( 'วาง Facebook Pixel tracking code ของคุณที่นี่', 'konderntang' ); ?>
                                        </p>
                        </td>
                    </tr>
                </table>
                        </div>
                    </div>
                    
                    <!-- Save Button -->
                    <div class="konderntang-settings-footer">
                        <button type="submit" name="konderntang_save_settings" class="button button-primary button-large konderntang-save-btn">
                            <span class="dashicons dashicons-yes-alt"></span>
                            <?php esc_html_e( 'Save Changes', 'konderntang' ); ?>
                        </button>
                        <a href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>" class="button button-secondary">
                            <span class="dashicons dashicons-admin-appearance"></span>
                            <?php esc_html_e( 'Customize Theme', 'konderntang' ); ?>
                        </a>
                    </div>
        </form>
    </div>
        </div>
    </div>
    <?php
}
