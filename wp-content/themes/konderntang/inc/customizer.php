<?php
/**
 * Theme Customizer
 *
 * @package KonDernTang
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Add customizer settings
 */
function konderntang_customize_register( $wp_customize ) {
    // General Settings Section
    $wp_customize->add_section(
        'konderntang_general',
        array(
            'title'    => esc_html__( 'General Settings', 'konderntang' ),
            'priority' => 10,
        )
    );

    // Logo Fallback Image
    $wp_customize->add_setting(
        'logo_fallback_image',
        array(
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
        )
    );

    $wp_customize->add_control(
        new WP_Customize_Image_Control(
            $wp_customize,
            'logo_fallback_image',
            array(
                'label'       => esc_html__( 'Logo Fallback Image', 'konderntang' ),
                'description' => esc_html__( 'Image to display when no custom logo is set', 'konderntang' ),
                'section'     => 'konderntang_general',
                'settings'    => 'logo_fallback_image',
            )
        )
    );

    // Header Settings Section
    $wp_customize->add_section(
        'konderntang_header',
        array(
            'title'    => esc_html__( 'Header Settings', 'konderntang' ),
            'priority' => 20,
        )
    );

    // Show Search Button
    $wp_customize->add_setting(
        'header_show_search',
        array(
            'default'           => true,
            'sanitize_callback' => 'konderntang_sanitize_checkbox',
        )
    );

    $wp_customize->add_control(
        'header_show_search',
        array(
            'label'   => esc_html__( 'Show Search Button', 'konderntang' ),
            'section' => 'konderntang_header',
            'type'    => 'checkbox',
        )
    );

    // Footer Settings Section
    $wp_customize->add_section(
        'konderntang_footer',
        array(
            'title'    => esc_html__( 'Footer Settings', 'konderntang' ),
            'priority' => 30,
        )
    );

    // Footer Layout (Number of columns)
    $wp_customize->add_setting(
        'footer_layout',
        array(
            'default'           => '0',
            'sanitize_callback' => 'absint',
        )
    );

    $wp_customize->add_control(
        'footer_layout',
        array(
            'label'       => esc_html__( 'Footer Widget Columns', 'konderntang' ),
            'description' => esc_html__( 'Number of footer widget columns (0 = no widgets, 1-4 = columns)', 'konderntang' ),
            'section'     => 'konderntang_footer',
            'type'        => 'select',
            'choices'     => array(
                '0' => esc_html__( 'No Widgets', 'konderntang' ),
                '1' => esc_html__( '1 Column', 'konderntang' ),
                '2' => esc_html__( '2 Columns', 'konderntang' ),
                '3' => esc_html__( '3 Columns', 'konderntang' ),
                '4' => esc_html__( '4 Columns', 'konderntang' ),
            ),
        )
    );

    // Footer Copyright Text
    $wp_customize->add_setting(
        'footer_copyright_text',
        array(
            'default'           => sprintf(
                /* translators: 1: Year, 2: Site Name, 3: Tagline */
                esc_html__( '&copy; %1$s %2$s - %3$s', 'konderntang' ),
                date( 'Y' ),
                get_bloginfo( 'name' ),
                esc_html__( 'เพื่อนเดินทางของคุณ', 'konderntang' )
            ),
            'sanitize_callback' => 'wp_kses_post',
        )
    );

    $wp_customize->add_control(
        'footer_copyright_text',
        array(
            'label'       => esc_html__( 'Copyright Text', 'konderntang' ),
            'description' => esc_html__( 'Use %year% for current year, %site% for site name', 'konderntang' ),
            'section'     => 'konderntang_footer',
            'type'        => 'textarea',
        )
    );

    // Cookie Consent Settings Section
    $wp_customize->add_section(
        'konderntang_cookie_consent',
        array(
            'title'    => esc_html__( 'Cookie Consent', 'konderntang' ),
            'priority' => 40,
        )
    );

    // Enable Cookie Consent
    $wp_customize->add_setting(
        'cookie_consent_enabled',
        array(
            'default'           => true,
            'sanitize_callback' => 'konderntang_sanitize_checkbox',
        )
    );

    $wp_customize->add_control(
        'cookie_consent_enabled',
        array(
            'label'   => esc_html__( 'Enable Cookie Consent Banner', 'konderntang' ),
            'section' => 'konderntang_cookie_consent',
            'type'    => 'checkbox',
        )
    );

    // Cookie Consent Message
    $wp_customize->add_setting(
        'cookie_consent_message',
        array(
            'default'           => esc_html__( 'เราใช้คุกกี้เพื่อปรับปรุงประสบการณ์การใช้งานของคุณ', 'konderntang' ),
            'sanitize_callback' => 'sanitize_textarea_field',
        )
    );

    $wp_customize->add_control(
        'cookie_consent_message',
        array(
            'label'   => esc_html__( 'Cookie Consent Message', 'konderntang' ),
            'section' => 'konderntang_cookie_consent',
            'type'    => 'textarea',
        )
    );

    // Homepage Settings Section
    $wp_customize->add_section(
        'konderntang_homepage',
        array(
            'title'    => esc_html__( 'Homepage Settings', 'konderntang' ),
            'priority' => 50,
        )
    );

    // Hero Slider Enabled
    $wp_customize->add_setting(
        'hero_slider_enabled',
        array(
            'default'           => true,
            'sanitize_callback' => 'konderntang_sanitize_checkbox',
        )
    );

    $wp_customize->add_control(
        'hero_slider_enabled',
        array(
            'label'   => esc_html__( 'Enable Hero Slider', 'konderntang' ),
            'section' => 'konderntang_homepage',
            'type'    => 'checkbox',
        )
    );

    // Hero Slider Posts Count
    $wp_customize->add_setting(
        'hero_slider_posts',
        array(
            'default'           => 4,
            'sanitize_callback' => 'absint',
        )
    );

    $wp_customize->add_control(
        'hero_slider_posts',
        array(
            'label'       => esc_html__( 'Number of Hero Slides', 'konderntang' ),
            'section'     => 'konderntang_homepage',
            'type'        => 'number',
            'input_attrs' => array(
                'min'  => 1,
                'max'  => 10,
                'step' => 1,
            ),
        )
    );

    // Featured Section Enabled
    $wp_customize->add_setting(
        'featured_section_enabled',
        array(
            'default'           => true,
            'sanitize_callback' => 'konderntang_sanitize_checkbox',
        )
    );

    $wp_customize->add_control(
        'featured_section_enabled',
        array(
            'label'   => esc_html__( 'Enable Featured Section', 'konderntang' ),
            'section' => 'konderntang_homepage',
            'type'    => 'checkbox',
        )
    );

    // Featured Posts Count
    $wp_customize->add_setting(
        'featured_posts_count',
        array(
            'default'           => 3,
            'sanitize_callback' => 'absint',
        )
    );

    $wp_customize->add_control(
        'featured_posts_count',
        array(
            'label'       => esc_html__( 'Number of Featured Posts', 'konderntang' ),
            'section'     => 'konderntang_homepage',
            'type'        => 'number',
            'input_attrs' => array(
                'min'  => 1,
                'max'  => 10,
                'step' => 1,
            ),
        )
    );

    // Recent Posts Count
    $wp_customize->add_setting(
        'recent_posts_count',
        array(
            'default'           => 6,
            'sanitize_callback' => 'absint',
        )
    );

    $wp_customize->add_control(
        'recent_posts_count',
        array(
            'label'       => esc_html__( 'Number of Recent Posts', 'konderntang' ),
            'section'     => 'konderntang_homepage',
            'type'        => 'number',
            'input_attrs' => array(
                'min'  => 1,
                'max'  => 20,
                'step' => 1,
            ),
        )
    );

    // Newsletter Enabled
    $wp_customize->add_setting(
        'newsletter_enabled',
        array(
            'default'           => true,
            'sanitize_callback' => 'konderntang_sanitize_checkbox',
        )
    );

    $wp_customize->add_control(
        'newsletter_enabled',
        array(
            'label'   => esc_html__( 'Enable Newsletter Section', 'konderntang' ),
            'section' => 'konderntang_homepage',
            'type'    => 'checkbox',
        )
    );

    // Trending Tags Count
    $wp_customize->add_setting(
        'trending_tags_count',
        array(
            'default'           => 10,
            'sanitize_callback' => 'absint',
        )
    );

    $wp_customize->add_control(
        'trending_tags_count',
        array(
            'label'       => esc_html__( 'Number of Trending Tags', 'konderntang' ),
            'section'     => 'konderntang_homepage',
            'type'        => 'number',
            'input_attrs' => array(
                'min'  => 1,
                'max'  => 30,
                'step' => 1,
            ),
        )
    );

    // Recently Viewed Enabled
    $wp_customize->add_setting(
        'recently_viewed_enabled',
        array(
            'default'           => true,
            'sanitize_callback' => 'konderntang_sanitize_checkbox',
        )
    );

    $wp_customize->add_control(
        'recently_viewed_enabled',
        array(
            'label'   => esc_html__( 'Enable Recently Viewed', 'konderntang' ),
            'section' => 'konderntang_homepage',
            'type'    => 'checkbox',
        )
    );

    // Color Settings Section
    $wp_customize->add_section(
        'konderntang_colors',
        array(
            'title'    => esc_html__( 'Color Settings', 'konderntang' ),
            'priority' => 60,
        )
    );

    // Primary Color
    // Enable live preview for color settings
    $wp_customize->get_setting( 'blogname' )->transport = 'postMessage';
    $wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';

    // Primary Color
    $wp_customize->add_setting(
        'color_primary',
        array(
            'default'           => '#0ea5e9',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'postMessage',
        )
    );

    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize,
            'color_primary',
            array(
                'label'   => esc_html__( 'Primary Color', 'konderntang' ),
                'section' => 'konderntang_colors',
            )
        )
    );

    // Secondary Color
    $wp_customize->add_setting(
        'color_secondary',
        array(
            'default'           => '#f97316',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'postMessage',
        )
    );

    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize,
            'color_secondary',
            array(
                'label'   => esc_html__( 'Secondary Color', 'konderntang' ),
                'section' => 'konderntang_colors',
            )
        )
    );

    // Text Color
    $wp_customize->add_setting(
        'color_text',
        array(
            'default'           => '#1e293b',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'postMessage',
        )
    );

    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize,
            'color_text',
            array(
                'label'   => esc_html__( 'Text Color', 'konderntang' ),
                'section' => 'konderntang_colors',
            )
        )
    );

    // Background Color
    $wp_customize->add_setting(
        'color_background',
        array(
            'default'           => '#f8fafc',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'postMessage',
        )
    );

    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize,
            'color_background',
            array(
                'label'   => esc_html__( 'Background Color', 'konderntang' ),
                'section' => 'konderntang_colors',
            )
        )
    );

    // Link Color
    $wp_customize->add_setting(
        'color_link',
        array(
            'default'           => '#0ea5e9',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'postMessage',
        )
    );

    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize,
            'color_link',
            array(
                'label'   => esc_html__( 'Link Color', 'konderntang' ),
                'section' => 'konderntang_colors',
            )
        )
    );

    // Typography Settings Section
    $wp_customize->add_section(
        'konderntang_typography',
        array(
            'title'    => esc_html__( 'Typography Settings', 'konderntang' ),
            'priority' => 70,
        )
    );

    // Body Font Family
    $wp_customize->add_setting(
        'typography_body_font',
        array(
            'default'           => 'Sarabun',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'postMessage',
        )
    );

    $wp_customize->add_control(
        'typography_body_font',
        array(
            'label'   => esc_html__( 'Body Font Family', 'konderntang' ),
            'section' => 'konderntang_typography',
            'type'    => 'select',
            'choices' => array(
                'Sarabun' => 'Sarabun',
                'Kanit'   => 'Kanit',
                'Prompt'  => 'Prompt',
                'Sarabun Sans' => 'Sarabun Sans',
            ),
        )
    );

    // Heading Font Family
    $wp_customize->add_setting(
        'typography_heading_font',
        array(
            'default'           => 'Kanit',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'postMessage',
        )
    );

    $wp_customize->add_control(
        'typography_heading_font',
        array(
            'label'   => esc_html__( 'Heading Font Family', 'konderntang' ),
            'section' => 'konderntang_typography',
            'type'    => 'select',
            'choices' => array(
                'Kanit'   => 'Kanit',
                'Sarabun' => 'Sarabun',
                'Prompt'  => 'Prompt',
                'Sarabun Sans' => 'Sarabun Sans',
            ),
        )
    );

    // Body Font Size
    $wp_customize->add_setting(
        'typography_body_size',
        array(
            'default'           => 16,
            'sanitize_callback' => 'absint',
            'transport'         => 'postMessage',
        )
    );

    $wp_customize->add_control(
        'typography_body_size',
        array(
            'label'       => esc_html__( 'Body Font Size (px)', 'konderntang' ),
            'section'     => 'konderntang_typography',
            'type'        => 'number',
            'input_attrs' => array(
                'min'  => 12,
                'max'  => 24,
                'step' => 1,
            ),
        )
    );

    // Heading Font Size (H1)
    $wp_customize->add_setting(
        'typography_h1_size',
        array(
            'default'           => 48,
            'sanitize_callback' => 'absint',
            'transport'         => 'postMessage',
        )
    );

    $wp_customize->add_control(
        'typography_h1_size',
        array(
            'label'       => esc_html__( 'H1 Font Size (px)', 'konderntang' ),
            'section'     => 'konderntang_typography',
            'type'        => 'number',
            'input_attrs' => array(
                'min'  => 24,
                'max'  => 72,
                'step' => 2,
            ),
        )
    );

    // Line Height
    $wp_customize->add_setting(
        'typography_line_height',
        array(
            'default'           => 1.6,
            'sanitize_callback' => 'floatval',
            'transport'         => 'postMessage',
        )
    );

    $wp_customize->add_control(
        'typography_line_height',
        array(
            'label'       => esc_html__( 'Line Height', 'konderntang' ),
            'section'     => 'konderntang_typography',
            'type'        => 'number',
            'input_attrs' => array(
                'min'  => 1.0,
                'max'  => 2.5,
                'step' => 0.1,
            ),
        )
    );

    // Layout Settings Section
    $wp_customize->add_section(
        'konderntang_layout',
        array(
            'title'    => esc_html__( 'Layout Settings', 'konderntang' ),
            'priority' => 80,
        )
    );

    // Container Width
    $wp_customize->add_setting(
        'layout_container_width',
        array(
            'default'           => 1200,
            'sanitize_callback' => 'absint',
            'transport'         => 'postMessage',
        )
    );

    $wp_customize->add_control(
        'layout_container_width',
        array(
            'label'       => esc_html__( 'Container Width (px)', 'konderntang' ),
            'description' => esc_html__( 'Maximum width of the main container', 'konderntang' ),
            'section'     => 'konderntang_layout',
            'type'        => 'number',
            'input_attrs' => array(
                'min'  => 960,
                'max'  => 1920,
                'step' => 20,
            ),
        )
    );

    // Sidebar Position (Archive)
    $wp_customize->add_setting(
        'layout_archive_sidebar',
        array(
            'default'           => 'right',
            'sanitize_callback' => 'sanitize_text_field',
        )
    );

    $wp_customize->add_control(
        'layout_archive_sidebar',
        array(
            'label'   => esc_html__( 'Archive Sidebar Position', 'konderntang' ),
            'section' => 'konderntang_layout',
            'type'    => 'select',
            'choices' => array(
                'left'  => esc_html__( 'Left', 'konderntang' ),
                'right' => esc_html__( 'Right', 'konderntang' ),
                'none'  => esc_html__( 'No Sidebar', 'konderntang' ),
            ),
        )
    );

    // Sidebar Position (Single Post)
    $wp_customize->add_setting(
        'layout_single_sidebar',
        array(
            'default'           => 'right',
            'sanitize_callback' => 'sanitize_text_field',
        )
    );

    $wp_customize->add_control(
        'layout_single_sidebar',
        array(
            'label'   => esc_html__( 'Single Post Sidebar Position', 'konderntang' ),
            'section' => 'konderntang_layout',
            'type'    => 'select',
            'choices' => array(
                'left'  => esc_html__( 'Left', 'konderntang' ),
                'right' => esc_html__( 'Right', 'konderntang' ),
                'none'  => esc_html__( 'No Sidebar', 'konderntang' ),
            ),
        )
    );

    // Posts Per Page (Archive)
    $wp_customize->add_setting(
        'layout_posts_per_page',
        array(
            'default'           => 12,
            'sanitize_callback' => 'absint',
        )
    );

    $wp_customize->add_control(
        'layout_posts_per_page',
        array(
            'label'       => esc_html__( 'Posts Per Page (Archive)', 'konderntang' ),
            'section'     => 'konderntang_layout',
            'type'        => 'number',
            'input_attrs' => array(
                'min'  => 1,
                'max'  => 50,
                'step' => 1,
            ),
        )
    );

    // Advanced Settings Section
    $wp_customize->add_section(
        'konderntang_advanced',
        array(
            'title'    => esc_html__( 'Advanced Settings', 'konderntang' ),
            'priority' => 90,
        )
    );

    // Custom CSS
    $wp_customize->add_setting(
        'advanced_custom_css',
        array(
            'default'           => '',
            'sanitize_callback' => 'wp_strip_all_tags',
        )
    );

    $wp_customize->add_control(
        'advanced_custom_css',
        array(
            'label'       => esc_html__( 'Custom CSS', 'konderntang' ),
            'description' => esc_html__( 'Add custom CSS code here', 'konderntang' ),
            'section'     => 'konderntang_advanced',
            'type'        => 'textarea',
        )
    );

    // Custom JavaScript
    $wp_customize->add_setting(
        'advanced_custom_js',
        array(
            'default'           => '',
            'sanitize_callback' => 'wp_strip_all_tags',
        )
    );

    $wp_customize->add_control(
        'advanced_custom_js',
        array(
            'label'       => esc_html__( 'Custom JavaScript', 'konderntang' ),
            'description' => esc_html__( 'Add custom JavaScript code here (without script tags)', 'konderntang' ),
            'section'     => 'konderntang_advanced',
            'type'        => 'textarea',
        )
    );

    // Google Analytics Code
    $wp_customize->add_setting(
        'advanced_google_analytics',
        array(
            'default'           => '',
            'sanitize_callback' => 'wp_kses_post',
        )
    );

    $wp_customize->add_control(
        'advanced_google_analytics',
        array(
            'label'       => esc_html__( 'Google Analytics Code', 'konderntang' ),
            'description' => esc_html__( 'Paste your Google Analytics tracking code here', 'konderntang' ),
            'section'     => 'konderntang_advanced',
            'type'        => 'textarea',
        )
    );

    // Facebook Pixel Code
    $wp_customize->add_setting(
        'advanced_facebook_pixel',
        array(
            'default'           => '',
            'sanitize_callback' => 'wp_kses_post',
        )
    );

    $wp_customize->add_control(
        'advanced_facebook_pixel',
        array(
            'label'       => esc_html__( 'Facebook Pixel Code', 'konderntang' ),
            'description' => esc_html__( 'Paste your Facebook Pixel code here', 'konderntang' ),
            'section'     => 'konderntang_advanced',
            'type'        => 'textarea',
        )
    );
}
add_action( 'customize_register', 'konderntang_customize_register' );

/**
 * Output custom CSS from Customizer
 */
function konderntang_customizer_css() {
    $custom_css = konderntang_get_option( 'advanced_custom_css', '' );
    if ( ! empty( $custom_css ) ) {
        echo '<style type="text/css" id="konderntang-custom-css">' . wp_strip_all_tags( $custom_css ) . '</style>';
    }
}
add_action( 'wp_head', 'konderntang_customizer_css', 100 );

/**
 * Output custom JavaScript from Customizer
 */
function konderntang_customizer_js() {
    $custom_js = konderntang_get_option( 'advanced_custom_js', '' );
    if ( ! empty( $custom_js ) ) {
        echo '<script type="text/javascript" id="konderntang-custom-js">' . wp_strip_all_tags( $custom_js ) . '</script>';
    }
}
add_action( 'wp_footer', 'konderntang_customizer_js', 100 );

/**
 * Output Google Analytics code
 */
function konderntang_google_analytics() {
    $ga_code = konderntang_get_option( 'advanced_google_analytics', '' );
    if ( ! empty( $ga_code ) ) {
        echo wp_kses_post( $ga_code );
    }
}
add_action( 'wp_head', 'konderntang_google_analytics', 10 );

/**
 * Output Facebook Pixel code
 */
function konderntang_facebook_pixel() {
    $fb_pixel = konderntang_get_option( 'advanced_facebook_pixel', '' );
    if ( ! empty( $fb_pixel ) ) {
        echo wp_kses_post( $fb_pixel );
    }
}
add_action( 'wp_head', 'konderntang_facebook_pixel', 10 );

/**
 * Output dynamic CSS from Customizer colors and typography
 */
function konderntang_dynamic_css() {
    $primary_color = konderntang_get_option( 'color_primary', '#0ea5e9' );
    $secondary_color = konderntang_get_option( 'color_secondary', '#f97316' );
    $text_color = konderntang_get_option( 'color_text', '#1e293b' );
    $background_color = konderntang_get_option( 'color_background', '#f8fafc' );
    $link_color = konderntang_get_option( 'color_link', '#0ea5e9' );
    
    $body_font = konderntang_get_option( 'typography_body_font', 'Sarabun' );
    $heading_font = konderntang_get_option( 'typography_heading_font', 'Kanit' );
    $body_size = absint( konderntang_get_option( 'typography_body_size', 16 ) );
    $h1_size = absint( konderntang_get_option( 'typography_h1_size', 48 ) );
    $line_height = floatval( konderntang_get_option( 'typography_line_height', 1.6 ) );
    
    $container_width = absint( konderntang_get_option( 'layout_container_width', 1200 ) );
    
    ?>
    <style type="text/css" id="konderntang-dynamic-css">
        :root {
            --konderntang-primary: <?php echo esc_attr( $primary_color ); ?>;
            --konderntang-secondary: <?php echo esc_attr( $secondary_color ); ?>;
            --konderntang-text: <?php echo esc_attr( $text_color ); ?>;
            --konderntang-background: <?php echo esc_attr( $background_color ); ?>;
            --konderntang-link: <?php echo esc_attr( $link_color ); ?>;
        }
        
        body {
            font-family: '<?php echo esc_attr( $body_font ); ?>', sans-serif;
            font-size: <?php echo esc_attr( $body_size ); ?>px;
            line-height: <?php echo esc_attr( $line_height ); ?>;
            color: <?php echo esc_attr( $text_color ); ?>;
            background-color: <?php echo esc_attr( $background_color ); ?>;
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: '<?php echo esc_attr( $heading_font ); ?>', sans-serif;
        }
        
        h1 {
            font-size: <?php echo esc_attr( $h1_size ); ?>px;
        }
        
        a {
            color: <?php echo esc_attr( $link_color ); ?>;
        }
        
        .container {
            max-width: <?php echo esc_attr( $container_width ); ?>px;
        }
        
        .bg-primary {
            background-color: <?php echo esc_attr( $primary_color ); ?> !important;
        }
        
        .text-primary {
            color: <?php echo esc_attr( $primary_color ); ?> !important;
        }
        
        .bg-secondary {
            background-color: <?php echo esc_attr( $secondary_color ); ?> !important;
        }
        
        .text-secondary {
            color: <?php echo esc_attr( $secondary_color ); ?> !important;
        }
    </style>
    <?php
}
add_action( 'wp_head', 'konderntang_dynamic_css', 20 );

