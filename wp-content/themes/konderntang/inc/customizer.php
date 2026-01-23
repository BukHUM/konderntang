<?php
/**
 * Theme Customizer
 *
 * @package KonDernTang
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add customizer settings
 */
function konderntang_customize_register($wp_customize)
{
    // General Settings Section
    $wp_customize->add_section(
        'konderntang_general',
        array(
            'title' => esc_html__('General Settings', 'konderntang'),
            'priority' => 10,
        )
    );

    // Logo Fallback Image
    $wp_customize->add_setting(
        'logo_fallback_image',
        array(
            'default' => '',
            'sanitize_callback' => 'esc_url_raw',
        )
    );

    $wp_customize->add_control(
        new WP_Customize_Image_Control(
            $wp_customize,
            'logo_fallback_image',
            array(
                'label' => esc_html__('Logo Fallback Image', 'konderntang'),
                'description' => esc_html__('Image to display when no custom logo is set', 'konderntang'),
                'section' => 'konderntang_general',
                'settings' => 'logo_fallback_image',
            )
        )
    );

    // Header Settings Section
    $wp_customize->add_section(
        'konderntang_header',
        array(
            'title' => esc_html__('Header Settings', 'konderntang'),
            'priority' => 20,
        )
    );

    // Show Search Button
    $wp_customize->add_setting(
        'header_show_search',
        array(
            'default' => true,
            'sanitize_callback' => 'konderntang_sanitize_checkbox',
        )
    );

    $wp_customize->add_control(
        'header_show_search',
        array(
            'label' => esc_html__('Show Search Button', 'konderntang'),
            'section' => 'konderntang_header',
            'type' => 'checkbox',
        )
    );

    // Footer Settings Section
    $wp_customize->add_section(
        'konderntang_footer',
        array(
            'title' => esc_html__('Footer Settings', 'konderntang'),
            'priority' => 30,
        )
    );

    // Footer Layout (Number of columns)
    $wp_customize->add_setting(
        'footer_layout',
        array(
            'default' => '0',
            'sanitize_callback' => 'absint',
        )
    );

    $wp_customize->add_control(
        'footer_layout',
        array(
            'label' => esc_html__('Footer Widget Columns', 'konderntang'),
            'description' => esc_html__('Number of footer widget columns (0 = no widgets, 1-4 = columns)', 'konderntang'),
            'section' => 'konderntang_footer',
            'type' => 'select',
            'choices' => array(
                '0' => esc_html__('No Widgets', 'konderntang'),
                '1' => esc_html__('1 Column', 'konderntang'),
                '2' => esc_html__('2 Columns', 'konderntang'),
                '3' => esc_html__('3 Columns', 'konderntang'),
                '4' => esc_html__('4 Columns', 'konderntang'),
            ),
        )
    );

    // Footer Copyright Text
    $wp_customize->add_setting(
        'footer_copyright_text',
        array(
            'default' => sprintf(
                /* translators: 1: Year, 2: Site Name, 3: Tagline */
                esc_html__('&copy; %1$s %2$s - %3$s', 'konderntang'),
                date('Y'),
                get_bloginfo('name'),
                esc_html__('เพื่อนเดินทางของคุณ', 'konderntang')
            ),
            'sanitize_callback' => 'wp_kses_post',
        )
    );

    $wp_customize->add_control(
        'footer_copyright_text',
        array(
            'label' => esc_html__('Copyright Text', 'konderntang'),
            'description' => esc_html__('Use %year% for current year, %site% for site name', 'konderntang'),
            'section' => 'konderntang_footer',
            'type' => 'textarea',
        )
    );

    // Cookie Consent Settings Section
    $wp_customize->add_section(
        'konderntang_cookie_consent',
        array(
            'title' => esc_html__('Cookie Consent', 'konderntang'),
            'priority' => 40,
        )
    );

    // Enable Cookie Consent
    $wp_customize->add_setting(
        'cookie_consent_enabled',
        array(
            'default' => true,
            'sanitize_callback' => 'konderntang_sanitize_checkbox',
        )
    );

    $wp_customize->add_control(
        'cookie_consent_enabled',
        array(
            'label' => esc_html__('Enable Cookie Consent Banner', 'konderntang'),
            'section' => 'konderntang_cookie_consent',
            'type' => 'checkbox',
        )
    );

    // Cookie Consent Message
    $wp_customize->add_setting(
        'cookie_consent_message',
        array(
            'default' => esc_html__('เราใช้คุกกี้เพื่อปรับปรุงประสบการณ์การใช้งานของคุณ', 'konderntang'),
            'sanitize_callback' => 'sanitize_textarea_field',
        )
    );

    $wp_customize->add_control(
        'cookie_consent_message',
        array(
            'label' => esc_html__('Cookie Consent Message', 'konderntang'),
            'section' => 'konderntang_cookie_consent',
            'type' => 'textarea',
        )
    );

    // Homepage Settings Section
    $wp_customize->add_section(
        'konderntang_homepage',
        array(
            'title' => esc_html__('Homepage Settings', 'konderntang'),
            'priority' => 50,
        )
    );

    // Hero Slider Enabled
    $wp_customize->add_setting(
        'hero_slider_enabled',
        array(
            'default' => true,
            'sanitize_callback' => 'konderntang_sanitize_checkbox',
        )
    );

    $wp_customize->add_control(
        'hero_slider_enabled',
        array(
            'label' => esc_html__('Enable Hero Slider', 'konderntang'),
            'section' => 'konderntang_homepage',
            'type' => 'checkbox',
        )
    );

    // Hero Slider Posts Count
    $wp_customize->add_setting(
        'hero_slider_posts',
        array(
            'default' => 4,
            'sanitize_callback' => 'absint',
        )
    );

    $wp_customize->add_control(
        'hero_slider_posts',
        array(
            'label' => esc_html__('Number of Hero Slides', 'konderntang'),
            'section' => 'konderntang_homepage',
            'type' => 'number',
            'input_attrs' => array(
                'min' => 1,
                'max' => 10,
                'step' => 1,
            ),
        )
    );

    // Featured Section Enabled
    $wp_customize->add_setting(
        'featured_section_enabled',
        array(
            'default' => true,
            'sanitize_callback' => 'konderntang_sanitize_checkbox',
        )
    );

    $wp_customize->add_control(
        'featured_section_enabled',
        array(
            'label' => esc_html__('Enable Featured Section', 'konderntang'),
            'section' => 'konderntang_homepage',
            'type' => 'checkbox',
        )
    );

    // Featured Posts Count
    $wp_customize->add_setting(
        'featured_posts_count',
        array(
            'default' => 3,
            'sanitize_callback' => 'absint',
        )
    );

    $wp_customize->add_control(
        'featured_posts_count',
        array(
            'label' => esc_html__('Number of Featured Posts', 'konderntang'),
            'section' => 'konderntang_homepage',
            'type' => 'number',
            'input_attrs' => array(
                'min' => 1,
                'max' => 10,
                'step' => 1,
            ),
        )
    );

    // Recent Posts Count
    $wp_customize->add_setting(
        'recent_posts_count',
        array(
            'default' => 6,
            'sanitize_callback' => 'absint',
        )
    );

    $wp_customize->add_control(
        'recent_posts_count',
        array(
            'label' => esc_html__('Number of Recent Posts', 'konderntang'),
            'section' => 'konderntang_homepage',
            'type' => 'number',
            'input_attrs' => array(
                'min' => 1,
                'max' => 20,
                'step' => 1,
            ),
        )
    );

    // Newsletter Enabled
    $wp_customize->add_setting(
        'newsletter_enabled',
        array(
            'default' => true,
            'sanitize_callback' => 'konderntang_sanitize_checkbox',
        )
    );

    $wp_customize->add_control(
        'newsletter_enabled',
        array(
            'label' => esc_html__('Enable Newsletter Section', 'konderntang'),
            'section' => 'konderntang_homepage',
            'type' => 'checkbox',
        )
    );

    // Trending Tags Count
    $wp_customize->add_setting(
        'trending_tags_count',
        array(
            'default' => 10,
            'sanitize_callback' => 'absint',
        )
    );

    $wp_customize->add_control(
        'trending_tags_count',
        array(
            'label' => esc_html__('Number of Trending Tags', 'konderntang'),
            'section' => 'konderntang_homepage',
            'type' => 'number',
            'input_attrs' => array(
                'min' => 1,
                'max' => 30,
                'step' => 1,
            ),
        )
    );

    // Recently Viewed Enabled
    $wp_customize->add_setting(
        'recently_viewed_enabled',
        array(
            'default' => true,
            'sanitize_callback' => 'konderntang_sanitize_checkbox',
        )
    );

    $wp_customize->add_control(
        'recently_viewed_enabled',
        array(
            'label' => esc_html__('Enable Recently Viewed', 'konderntang'),
            'section' => 'konderntang_homepage',
            'type' => 'checkbox',
        )
    );

    // Color Settings Section
    $wp_customize->add_section(
        'konderntang_colors',
        array(
            'title' => esc_html__('Color Settings', 'konderntang'),
            'priority' => 60,
        )
    );

    // Primary Color
    // Enable live preview for color settings
    $wp_customize->get_setting('blogname')->transport = 'postMessage';
    $wp_customize->get_setting('blogdescription')->transport = 'postMessage';

    // Primary Color
    $wp_customize->add_setting(
        'color_primary',
        array(
            'default' => '#0ea5e9',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport' => 'postMessage',
        )
    );

    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize,
            'color_primary',
            array(
                'label' => esc_html__('Primary Color', 'konderntang'),
                'section' => 'konderntang_colors',
            )
        )
    );

    // Secondary Color
    $wp_customize->add_setting(
        'color_secondary',
        array(
            'default' => '#f97316',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport' => 'postMessage',
        )
    );

    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize,
            'color_secondary',
            array(
                'label' => esc_html__('Secondary Color', 'konderntang'),
                'section' => 'konderntang_colors',
            )
        )
    );

    // Text Color
    $wp_customize->add_setting(
        'color_text',
        array(
            'default' => '#1e293b',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport' => 'postMessage',
        )
    );

    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize,
            'color_text',
            array(
                'label' => esc_html__('Text Color', 'konderntang'),
                'section' => 'konderntang_colors',
            )
        )
    );

    // Background Color
    $wp_customize->add_setting(
        'color_background',
        array(
            'default' => '#f8fafc',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport' => 'postMessage',
        )
    );

    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize,
            'color_background',
            array(
                'label' => esc_html__('Background Color', 'konderntang'),
                'section' => 'konderntang_colors',
            )
        )
    );

    // Link Color
    $wp_customize->add_setting(
        'color_link',
        array(
            'default' => '#0ea5e9',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport' => 'postMessage',
        )
    );

    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize,
            'color_link',
            array(
                'label' => esc_html__('Link Color', 'konderntang'),
                'section' => 'konderntang_colors',
            )
        )
    );

    // Typography Settings Section
    $wp_customize->add_section(
        'konderntang_typography',
        array(
            'title' => esc_html__('Typography Settings', 'konderntang'),
            'priority' => 70,
        )
    );

    // === Font Families ===

    // Body Font Family
    $wp_customize->add_setting(
        'typography_body_font',
        array(
            'default' => 'system-ui',
            'sanitize_callback' => 'sanitize_text_field',
            'transport' => 'postMessage',
        )
    );

    $wp_customize->add_control(
        'typography_body_font',
        array(
            'label' => esc_html__('Body Font Family', 'konderntang'),
            'section' => 'konderntang_typography',
            'type' => 'select',
            'choices' => array(
                'system-ui' => 'System Default',
                'Sarabun' => 'Sarabun (Thai)',
                'Kanit' => 'Kanit (Thai)',
                'Prompt' => 'Prompt (Thai)',
                'Noto Sans Thai' => 'Noto Sans Thai',
                'Arial' => 'Arial',
                'Helvetica' => 'Helvetica',
                'Georgia' => 'Georgia',
                'Times New Roman' => 'Times New Roman',
            ),
        )
    );

    // Heading Font Family
    $wp_customize->add_setting(
        'typography_heading_font',
        array(
            'default' => 'Kanit',
            'sanitize_callback' => 'sanitize_text_field',
            'transport' => 'postMessage',
        )
    );

    $wp_customize->add_control(
        'typography_heading_font',
        array(
            'label' => esc_html__('Heading Font Family', 'konderntang'),
            'section' => 'konderntang_typography',
            'type' => 'select',
            'choices' => array(
                'Kanit' => 'Kanit (Thai)',
                'Sarabun' => 'Sarabun (Thai)',
                'Prompt' => 'Prompt (Thai)',
                'Noto Sans Thai' => 'Noto Sans Thai',
                'system-ui' => 'System Default',
                'Arial' => 'Arial',
                'Helvetica' => 'Helvetica',
                'Georgia' => 'Georgia',
                'Times New Roman' => 'Times New Roman',
            ),
        )
    );

    // Menu Font Family
    $wp_customize->add_setting(
        'typography_menu_font',
        array(
            'default' => 'system-ui',
            'sanitize_callback' => 'sanitize_text_field',
            'transport' => 'postMessage',
        )
    );

    $wp_customize->add_control(
        'typography_menu_font',
        array(
            'label' => esc_html__('Menu Font Family', 'konderntang'),
            'section' => 'konderntang_typography',
            'type' => 'select',
            'choices' => array(
                'system-ui' => 'System Default',
                'Sarabun' => 'Sarabun (Thai)',
                'Kanit' => 'Kanit (Thai)',
                'Prompt' => 'Prompt (Thai)',
                'Noto Sans Thai' => 'Noto Sans Thai',
                'Arial' => 'Arial',
                'Helvetica' => 'Helvetica',
            ),
        )
    );

    // === Font Sizes ===

    // Menu Font Size
    $wp_customize->add_setting(
        'typography_menu_size',
        array(
            'default' => 16,
            'sanitize_callback' => 'absint',
            'transport' => 'postMessage',
        )
    );

    $wp_customize->add_control(
        'typography_menu_size',
        array(
            'label' => esc_html__('Menu Font Size (px)', 'konderntang'),
            'section' => 'konderntang_typography',
            'type' => 'number',
            'input_attrs' => array(
                'min' => 12,
                'max' => 24,
                'step' => 1,
            ),
        )
    );

    // Body Font Size
    $wp_customize->add_setting(
        'typography_body_size',
        array(
            'default' => 18,
            'sanitize_callback' => 'absint',
            'transport' => 'postMessage',
        )
    );

    $wp_customize->add_control(
        'typography_body_size',
        array(
            'label' => esc_html__('Body Font Size (px)', 'konderntang'),
            'section' => 'konderntang_typography',
            'type' => 'number',
            'input_attrs' => array(
                'min' => 14,
                'max' => 20,
                'step' => 1,
            ),
        )
    );

    // H1 Font Size
    $wp_customize->add_setting(
        'typography_h1_size',
        array(
            'default' => 30,
            'sanitize_callback' => 'absint',
            'transport' => 'postMessage',
        )
    );

    $wp_customize->add_control(
        'typography_h1_size',
        array(
            'label' => esc_html__('H1 Font Size (px)', 'konderntang'),
            'section' => 'konderntang_typography',
            'type' => 'number',
            'input_attrs' => array(
                'min' => 24,
                'max' => 48,
                'step' => 2,
            ),
        )
    );

    // H2 Font Size
    $wp_customize->add_setting(
        'typography_h2_size',
        array(
            'default' => 24,
            'sanitize_callback' => 'absint',
            'transport' => 'postMessage',
        )
    );

    $wp_customize->add_control(
        'typography_h2_size',
        array(
            'label' => esc_html__('H2 Font Size (px)', 'konderntang'),
            'section' => 'konderntang_typography',
            'type' => 'number',
            'input_attrs' => array(
                'min' => 20,
                'max' => 36,
                'step' => 1,
            ),
        )
    );

    // H3 Font Size
    $wp_customize->add_setting(
        'typography_h3_size',
        array(
            'default' => 24,
            'sanitize_callback' => 'absint',
            'transport' => 'postMessage',
        )
    );

    $wp_customize->add_control(
        'typography_h3_size',
        array(
            'label' => esc_html__('H3 Font Size (px)', 'konderntang'),
            'section' => 'konderntang_typography',
            'type' => 'number',
            'input_attrs' => array(
                'min' => 18,
                'max' => 30,
                'step' => 1,
            ),
        )
    );

    // H4 Font Size
    $wp_customize->add_setting(
        'typography_h4_size',
        array(
            'default' => 18,
            'sanitize_callback' => 'absint',
            'transport' => 'postMessage',
        )
    );

    $wp_customize->add_control(
        'typography_h4_size',
        array(
            'label' => esc_html__('H4 Font Size (px)', 'konderntang'),
            'section' => 'konderntang_typography',
            'type' => 'number',
            'input_attrs' => array(
                'min' => 16,
                'max' => 24,
                'step' => 1,
            ),
        )
    );

    // H5 Font Size
    $wp_customize->add_setting(
        'typography_h5_size',
        array(
            'default' => 16,
            'sanitize_callback' => 'absint',
            'transport' => 'postMessage',
        )
    );

    $wp_customize->add_control(
        'typography_h5_size',
        array(
            'label' => esc_html__('H5 Font Size (px)', 'konderntang'),
            'section' => 'konderntang_typography',
            'type' => 'number',
            'input_attrs' => array(
                'min' => 14,
                'max' => 20,
                'step' => 1,
            ),
        )
    );

    // H6 Font Size
    $wp_customize->add_setting(
        'typography_h6_size',
        array(
            'default' => 14,
            'sanitize_callback' => 'absint',
            'transport' => 'postMessage',
        )
    );

    $wp_customize->add_control(
        'typography_h6_size',
        array(
            'label' => esc_html__('H6 Font Size (px)', 'konderntang'),
            'section' => 'konderntang_typography',
            'type' => 'number',
            'input_attrs' => array(
                'min' => 12,
                'max' => 18,
                'step' => 1,
            ),
        )
    );

    // === Line Heights ===

    // Body Line Height
    $wp_customize->add_setting(
        'typography_body_line_height',
        array(
            'default' => 1.75,
            'sanitize_callback' => 'konderntang_sanitize_float',
            'transport' => 'postMessage',
        )
    );

    $wp_customize->add_control(
        'typography_body_line_height',
        array(
            'label' => esc_html__('Body Line Height', 'konderntang'),
            'section' => 'konderntang_typography',
            'type' => 'number',
            'input_attrs' => array(
                'min' => 1.2,
                'max' => 2.0,
                'step' => 0.05,
            ),
        )
    );

    // Heading Line Height
    $wp_customize->add_setting(
        'typography_heading_line_height',
        array(
            'default' => 1.3,
            'sanitize_callback' => 'konderntang_sanitize_float',
            'transport' => 'postMessage',
        )
    );

    $wp_customize->add_control(
        'typography_heading_line_height',
        array(
            'label' => esc_html__('Heading Line Height', 'konderntang'),
            'section' => 'konderntang_typography',
            'type' => 'number',
            'input_attrs' => array(
                'min' => 1.0,
                'max' => 1.8,
                'step' => 0.05,
            ),
        )
    );

    // === Font Weights ===

    // Body Font Weight
    $wp_customize->add_setting(
        'typography_body_weight',
        array(
            'default' => 400,
            'sanitize_callback' => 'absint',
            'transport' => 'postMessage',
        )
    );

    $wp_customize->add_control(
        'typography_body_weight',
        array(
            'label' => esc_html__('Body Font Weight', 'konderntang'),
            'section' => 'konderntang_typography',
            'type' => 'select',
            'choices' => array(
                '300' => esc_html__('Light (300)', 'konderntang'),
                '400' => esc_html__('Normal (400)', 'konderntang'),
                '500' => esc_html__('Medium (500)', 'konderntang'),
                '600' => esc_html__('Semi Bold (600)', 'konderntang'),
                '700' => esc_html__('Bold (700)', 'konderntang'),
            ),
        )
    );

    // Heading Font Weight
    $wp_customize->add_setting(
        'typography_heading_weight',
        array(
            'default' => 700,
            'sanitize_callback' => 'absint',
            'transport' => 'postMessage',
        )
    );

    $wp_customize->add_control(
        'typography_heading_weight',
        array(
            'label' => esc_html__('Heading Font Weight', 'konderntang'),
            'section' => 'konderntang_typography',
            'type' => 'select',
            'choices' => array(
                '400' => esc_html__('Normal (400)', 'konderntang'),
                '500' => esc_html__('Medium (500)', 'konderntang'),
                '600' => esc_html__('Semi Bold (600)', 'konderntang'),
                '700' => esc_html__('Bold (700)', 'konderntang'),
                '800' => esc_html__('Extra Bold (800)', 'konderntang'),
                '900' => esc_html__('Black (900)', 'konderntang'),
            ),
        )
    );

    // Menu Font Weight
    $wp_customize->add_setting(
        'typography_menu_weight',
        array(
            'default' => 500,
            'sanitize_callback' => 'absint',
            'transport' => 'postMessage',
        )
    );

    $wp_customize->add_control(
        'typography_menu_weight',
        array(
            'label' => esc_html__('Menu Font Weight', 'konderntang'),
            'section' => 'konderntang_typography',
            'type' => 'select',
            'choices' => array(
                '400' => esc_html__('Normal (400)', 'konderntang'),
                '500' => esc_html__('Medium (500)', 'konderntang'),
                '600' => esc_html__('Semi Bold (600)', 'konderntang'),
                '700' => esc_html__('Bold (700)', 'konderntang'),
            ),
        )
    );

    // === Button Typography ===

    // Button Font Family
    $wp_customize->add_setting(
        'typography_button_font',
        array(
            'default' => 'system-ui',
            'sanitize_callback' => 'sanitize_text_field',
            'transport' => 'postMessage',
        )
    );

    $wp_customize->add_control(
        'typography_button_font',
        array(
            'label' => esc_html__('Button Font Family', 'konderntang'),
            'section' => 'konderntang_typography',
            'type' => 'select',
            'choices' => array(
                'system-ui' => 'System Default',
                'inherit' => 'Inherit from Body',
                'Sarabun' => 'Sarabun (Thai)',
                'Kanit' => 'Kanit (Thai)',
                'Prompt' => 'Prompt (Thai)',
            ),
        )
    );

    // Button Font Size
    $wp_customize->add_setting(
        'typography_button_size',
        array(
            'default' => 16,
            'sanitize_callback' => 'absint',
            'transport' => 'postMessage',
        )
    );

    $wp_customize->add_control(
        'typography_button_size',
        array(
            'label' => esc_html__('Button Font Size (px)', 'konderntang'),
            'section' => 'konderntang_typography',
            'type' => 'number',
            'input_attrs' => array(
                'min' => 12,
                'max' => 24,
                'step' => 1,
            ),
        )
    );

    // Button Font Weight
    $wp_customize->add_setting(
        'typography_button_weight',
        array(
            'default' => 500,
            'sanitize_callback' => 'absint',
            'transport' => 'postMessage',
        )
    );

    $wp_customize->add_control(
        'typography_button_weight',
        array(
            'label' => esc_html__('Button Font Weight', 'konderntang'),
            'section' => 'konderntang_typography',
            'type' => 'select',
            'choices' => array(
                '400' => esc_html__('Normal (400)', 'konderntang'),
                '500' => esc_html__('Medium (500)', 'konderntang'),
                '600' => esc_html__('Semi Bold (600)', 'konderntang'),
                '700' => esc_html__('Bold (700)', 'konderntang'),
            ),
        )
    );

    // === Meta Info Typography ===

    // Meta Font Size
    $wp_customize->add_setting(
        'typography_meta_size',
        array(
            'default' => 14,
            'sanitize_callback' => 'absint',
            'transport' => 'postMessage',
        )
    );

    $wp_customize->add_control(
        'typography_meta_size',
        array(
            'label' => esc_html__('Meta Info Font Size (px)', 'konderntang'),
            'description' => esc_html__('For dates, authors, categories', 'konderntang'),
            'section' => 'konderntang_typography',
            'type' => 'number',
            'input_attrs' => array(
                'min' => 10,
                'max' => 18,
                'step' => 1,
            ),
        )
    );

    // === Additional Settings ===

    // Letter Spacing
    $wp_customize->add_setting(
        'typography_letter_spacing',
        array(
            'default' => 0,
            'sanitize_callback' => 'konderntang_sanitize_float',
            'transport' => 'postMessage',
        )
    );

    $wp_customize->add_control(
        'typography_letter_spacing',
        array(
            'label' => esc_html__('Letter Spacing (px)', 'konderntang'),
            'description' => esc_html__('Applies to headings', 'konderntang'),
            'section' => 'konderntang_typography',
            'type' => 'number',
            'input_attrs' => array(
                'min' => -2,
                'max' => 5,
                'step' => 0.1,
            ),
        )
    );

    // Text Transform
    $wp_customize->add_setting(
        'typography_text_transform',
        array(
            'default' => 'none',
            'sanitize_callback' => 'sanitize_text_field',
            'transport' => 'postMessage',
        )
    );

    $wp_customize->add_control(
        'typography_text_transform',
        array(
            'label' => esc_html__('Heading Text Transform', 'konderntang'),
            'section' => 'konderntang_typography',
            'type' => 'select',
            'choices' => array(
                'none' => esc_html__('None', 'konderntang'),
                'uppercase' => esc_html__('Uppercase', 'konderntang'),
                'lowercase' => esc_html__('Lowercase', 'konderntang'),
                'capitalize' => esc_html__('Capitalize', 'konderntang'),
            ),
        )
    );

    // Layout Settings Section
    $wp_customize->add_section(
        'konderntang_layout',
        array(
            'title' => esc_html__('Layout Settings', 'konderntang'),
            'priority' => 80,
        )
    );

    // Container Width
    $wp_customize->add_setting(
        'layout_container_width',
        array(
            'default' => 1200,
            'sanitize_callback' => 'absint',
            'transport' => 'postMessage',
        )
    );

    $wp_customize->add_control(
        'layout_container_width',
        array(
            'label' => esc_html__('Container Width (px)', 'konderntang'),
            'description' => esc_html__('Maximum width of the main container', 'konderntang'),
            'section' => 'konderntang_layout',
            'type' => 'number',
            'input_attrs' => array(
                'min' => 960,
                'max' => 1920,
                'step' => 20,
            ),
        )
    );

    // Sidebar Position (Archive)
    $wp_customize->add_setting(
        'layout_archive_sidebar',
        array(
            'default' => 'right',
            'sanitize_callback' => 'sanitize_text_field',
        )
    );

    $wp_customize->add_control(
        'layout_archive_sidebar',
        array(
            'label' => esc_html__('Archive Sidebar Position', 'konderntang'),
            'section' => 'konderntang_layout',
            'type' => 'select',
            'choices' => array(
                'left' => esc_html__('Left', 'konderntang'),
                'right' => esc_html__('Right', 'konderntang'),
                'none' => esc_html__('No Sidebar', 'konderntang'),
            ),
        )
    );

    // Sidebar Position (Single Post)
    $wp_customize->add_setting(
        'layout_single_sidebar',
        array(
            'default' => 'right',
            'sanitize_callback' => 'sanitize_text_field',
        )
    );

    $wp_customize->add_control(
        'layout_single_sidebar',
        array(
            'label' => esc_html__('Single Post Sidebar Position', 'konderntang'),
            'section' => 'konderntang_layout',
            'type' => 'select',
            'choices' => array(
                'left' => esc_html__('Left', 'konderntang'),
                'right' => esc_html__('Right', 'konderntang'),
                'none' => esc_html__('No Sidebar', 'konderntang'),
            ),
        )
    );

    // Posts Per Page (Archive)
    $wp_customize->add_setting(
        'layout_posts_per_page',
        array(
            'default' => 12,
            'sanitize_callback' => 'absint',
        )
    );

    $wp_customize->add_control(
        'layout_posts_per_page',
        array(
            'label' => esc_html__('Posts Per Page (Archive)', 'konderntang'),
            'section' => 'konderntang_layout',
            'type' => 'number',
            'input_attrs' => array(
                'min' => 1,
                'max' => 50,
                'step' => 1,
            ),
        )
    );

    // Advanced Settings Section
    $wp_customize->add_section(
        'konderntang_advanced',
        array(
            'title' => esc_html__('Advanced Settings', 'konderntang'),
            'priority' => 90,
        )
    );

    // Custom CSS
    $wp_customize->add_setting(
        'advanced_custom_css',
        array(
            'default' => '',
            'sanitize_callback' => 'wp_strip_all_tags',
        )
    );

    $wp_customize->add_control(
        'advanced_custom_css',
        array(
            'label' => esc_html__('Custom CSS', 'konderntang'),
            'description' => esc_html__('Add custom CSS code here', 'konderntang'),
            'section' => 'konderntang_advanced',
            'type' => 'textarea',
        )
    );

    // Custom JavaScript
    $wp_customize->add_setting(
        'advanced_custom_js',
        array(
            'default' => '',
            'sanitize_callback' => 'wp_strip_all_tags',
        )
    );

    $wp_customize->add_control(
        'advanced_custom_js',
        array(
            'label' => esc_html__('Custom JavaScript', 'konderntang'),
            'description' => esc_html__('Add custom JavaScript code here (without script tags)', 'konderntang'),
            'section' => 'konderntang_advanced',
            'type' => 'textarea',
        )
    );

    // Google Analytics Code
    $wp_customize->add_setting(
        'advanced_google_analytics',
        array(
            'default' => '',
            'sanitize_callback' => 'wp_kses_post',
        )
    );

    $wp_customize->add_control(
        'advanced_google_analytics',
        array(
            'label' => esc_html__('Google Analytics Code', 'konderntang'),
            'description' => esc_html__('Paste your Google Analytics tracking code here', 'konderntang'),
            'section' => 'konderntang_advanced',
            'type' => 'textarea',
        )
    );

    // Facebook Pixel Code
    $wp_customize->add_setting(
        'advanced_facebook_pixel',
        array(
            'default' => '',
            'sanitize_callback' => 'wp_kses_post',
        )
    );

    $wp_customize->add_control(
        'advanced_facebook_pixel',
        array(
            'label' => esc_html__('Facebook Pixel Code', 'konderntang'),
            'description' => esc_html__('Paste your Facebook Pixel code here', 'konderntang'),
            'section' => 'konderntang_advanced',
            'type' => 'textarea',
        )
    );
}
add_action('customize_register', 'konderntang_customize_register');


/**
 * Output custom CSS from Customizer
 */
function konderntang_customizer_css()
{
    $custom_css = konderntang_get_option('advanced_custom_css', '');
    if (!empty($custom_css)) {
        echo '<style type="text/css" id="konderntang-custom-css">' . wp_strip_all_tags($custom_css) . '</style>';
    }
}
add_action('wp_head', 'konderntang_customizer_css', 100);

/**
 * Output custom JavaScript from Customizer
 */
function konderntang_customizer_js()
{
    $custom_js = konderntang_get_option('advanced_custom_js', '');
    if (!empty($custom_js)) {
        echo '<script type="text/javascript" id="konderntang-custom-js">' . wp_strip_all_tags($custom_js) . '</script>';
    }
}
add_action('wp_footer', 'konderntang_customizer_js', 100);

/**
 * Output Google Analytics code
 */
function konderntang_google_analytics()
{
    $ga_code = konderntang_get_option('advanced_google_analytics', '');
    if (!empty($ga_code)) {
        echo wp_kses_post($ga_code);
    }
}
add_action('wp_head', 'konderntang_google_analytics', 10);

/**
 * Output Facebook Pixel code
 */
function konderntang_facebook_pixel()
{
    $fb_pixel = konderntang_get_option('advanced_facebook_pixel', '');
    if (!empty($fb_pixel)) {
        echo wp_kses_post($fb_pixel);
    }
}
add_action('wp_head', 'konderntang_facebook_pixel', 10);

/**
 * Output dynamic CSS from Customizer colors and typography
 */
function konderntang_dynamic_css()
{
    // Colors
    $primary_color = konderntang_get_option('color_primary', '#0ea5e9');
    $secondary_color = konderntang_get_option('color_secondary', '#f97316');
    $text_color = konderntang_get_option('color_text', '#1e293b');
    $background_color = konderntang_get_option('color_background', '#f8fafc');
    $link_color = konderntang_get_option('color_link', '#0ea5e9');

    // Typography - Font Families
    $body_font = konderntang_get_option('typography_body_font', 'system-ui');
    $heading_font = konderntang_get_option('typography_heading_font', 'Kanit');
    $menu_font = konderntang_get_option('typography_menu_font', 'system-ui');
    $button_font_setting = konderntang_get_option('typography_button_font', 'system-ui');
    $button_font = ($button_font_setting === 'inherit') ? $body_font : $button_font_setting;

    // Typography - Font Sizes
    $menu_size = absint(konderntang_get_option('typography_menu_size', 16));
    $body_size = absint(konderntang_get_option('typography_body_size', 18));
    $h1_size = absint(konderntang_get_option('typography_h1_size', 30));
    $h2_size = absint(konderntang_get_option('typography_h2_size', 24));
    $h3_size = absint(konderntang_get_option('typography_h3_size', 24));
    $h4_size = absint(konderntang_get_option('typography_h4_size', 18));
    $h5_size = absint(konderntang_get_option('typography_h5_size', 16));
    $h6_size = absint(konderntang_get_option('typography_h6_size', 14));

    $button_size = absint(konderntang_get_option('typography_button_size', 16));
    $meta_size = absint(konderntang_get_option('typography_meta_size', 14));

    // Typography - Line Heights
    $body_line_height = floatval(konderntang_get_option('typography_body_line_height', 1.75));
    $heading_line_height = floatval(konderntang_get_option('typography_heading_line_height', 1.3));

    // Typography - Font Weights
    $body_weight = absint(konderntang_get_option('typography_body_weight', 400));
    $heading_weight = absint(konderntang_get_option('typography_heading_weight', 700));
    $menu_weight = absint(konderntang_get_option('typography_menu_weight', 500));
    $button_weight = absint(konderntang_get_option('typography_button_weight', 500));

    // Typography - Additional
    $letter_spacing = floatval(konderntang_get_option('typography_letter_spacing', 0));
    $text_transform = sanitize_text_field(konderntang_get_option('typography_text_transform', 'none'));

    // Layout
    $container_width = absint(konderntang_get_option('layout_container_width', 1200));

    ?>
    <style type="text/css" id="konderntang-dynamic-css">
        :root {
            /* Colors */
            --konderntang-primary: <?php echo esc_attr($primary_color); ?>;
            --konderntang-secondary: <?php echo esc_attr($secondary_color); ?>;
            --konderntang-text: <?php echo esc_attr($text_color); ?>;
            --konderntang-background: <?php echo esc_attr($background_color); ?>;
            --konderntang-link: <?php echo esc_attr($link_color); ?>;

            /* Typography - Fonts */
            --font-body: '<?php echo esc_attr($body_font); ?>', sans-serif;
            --font-heading: '<?php echo esc_attr($heading_font); ?>', sans-serif;
            --font-menu: '<?php echo esc_attr($menu_font); ?>', sans-serif;
            --font-button: '<?php echo esc_attr($button_font); ?>', sans-serif;

            /* Typography - Sizes (Responsive with clamp) */
            --size-menu: <?php echo esc_attr($menu_size); ?>px;

            /* Body: Min 16px, Max user setting */
            --size-body: clamp(16px, 2vw + 1rem, <?php echo esc_attr($body_size); ?>px);

            /* Headings: Min 70% of max, slope 4vw + 1rem */
            --size-h1: clamp(<?php echo max(24, $h1_size * 0.7); ?>px, 4vw + 1rem, <?php echo esc_attr($h1_size); ?>px);
            --size-h2: clamp(<?php echo max(20, $h2_size * 0.75); ?>px, 3.5vw + 1rem, <?php echo esc_attr($h2_size); ?>px);
            --size-h3: clamp(<?php echo max(18, $h3_size * 0.8); ?>px, 3vw + 1rem, <?php echo esc_attr($h3_size); ?>px);
            --size-h4: clamp(<?php echo max(16, $h4_size * 0.9); ?>px, 2vw + 1rem, <?php echo esc_attr($h4_size); ?>px);
            --size-h5: clamp(<?php echo max(14, $h5_size * 0.9); ?>px, 2vw + 1rem, <?php echo esc_attr($h5_size); ?>px);
            --size-h6: clamp(14px, 2vw + 1rem, <?php echo esc_attr($h6_size); ?>px);

            --size-button: <?php echo esc_attr($button_size); ?>px;
            --size-meta: <?php echo esc_attr($meta_size); ?>px;

            /* Typography - Line Heights */
            --line-height-body: <?php echo esc_attr($body_line_height); ?>;
            --line-height-heading: <?php echo esc_attr($heading_line_height); ?>;

            /* Typography - Weights */
            --weight-body: <?php echo esc_attr($body_weight); ?>;
            --weight-heading: <?php echo esc_attr($heading_weight); ?>;
            --weight-menu: <?php echo esc_attr($menu_weight); ?>;
            --weight-button: <?php echo esc_attr($button_weight); ?>;
        }

        /* Body */
        body {
            font-family: var(--font-body);
            font-size: var(--size-body);
            line-height: var(--line-height-body);
            font-weight: var(--weight-body);
            color: <?php echo esc_attr($text_color); ?>;
            background-color: <?php echo esc_attr($background_color); ?>;
        }

        /* Headings */
        h1, h2, h3, h4, h5, h6, .font-heading {
            font-family: var(--font-heading);
            font-weight: var(--weight-heading);
            line-height: var(--line-height-heading);
            letter-spacing: <?php echo esc_attr($letter_spacing); ?>px;
            text-transform: <?php echo esc_attr($text_transform); ?>;
        }

        h1 { font-size: var(--size-h1); }
        h2 { font-size: var(--size-h2); }
        h3 { font-size: var(--size-h3); }
        h4 { font-size: var(--size-h4); }
        h5 { font-size: var(--size-h5); }
        h6 { font-size: var(--size-h6); }

        /* Menu */
        .main-navigation a, .primary-menu a, nav a, nav button, #primary-menu a, #primary-menu button, .menu a, .menu button {
            font-family: var(--font-menu);
            font-size: var(--size-menu);
            font-weight: var(--weight-menu);
        }

        /* Buttons */
        button, input[type="button"], input[type="reset"], input[type="submit"], .button, .btn, .wp-block-button__link {
            font-family: var(--font-button);
            font-size: var(--size-button);
            font-weight: var(--weight-button);
        }

        /* Meta Info (Date, Categories, etc) */
        .entry-meta, .post-meta, .cat-links, .tags-links, .byline, .posted-on {
            font-size: var(--size-meta);
        }

        /* Links */
        a { color: <?php echo esc_attr($link_color); ?>; }

        /* Container */
        .container { max-width: <?php echo esc_attr($container_width); ?>px; }

        /* Utility Classes */
        .bg-primary { background-color: <?php echo esc_attr($primary_color); ?> !important; }
        .text-primary { color: <?php echo esc_attr($primary_color); ?> !important; }
        .bg-secondary { background-color: <?php echo esc_attr($secondary_color); ?> !important; }
        .text-secondary { color: <?php echo esc_attr($secondary_color); ?> !important; }
    </style>
    <?php
}
add_action('wp_head', 'konderntang_dynamic_css', 20);
