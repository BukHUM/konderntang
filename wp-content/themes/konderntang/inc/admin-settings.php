<?php
/**
 * Admin Settings Page
 *
 * @package KonDernTang
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Save settings
 */
function konderntang_save_settings()
{
    // Debug: Log if function is called
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('KonDernTang save_settings function called');
        error_log('KonDernTang POST keys: ' . print_r(array_keys($_POST), true));
        error_log('KonDernTang POST konderntang_save_settings: ' . (isset($_POST['konderntang_save_settings']) ? 'SET' : 'NOT SET'));
    }

    if (!isset($_POST['konderntang_save_settings'])) {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('KonDernTang save_settings: konderntang_save_settings not in POST - returning early');
        }
        return;
    }

    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'konderntang'));
    }

    check_admin_referer('konderntang_settings_nonce');

    // Debug: Log after nonce check
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('KonDernTang save_settings: Passed nonce check, processing settings...');
    }

    // General Settings - Logo
    // Always check if site_logo is in POST (even if empty string)
    if (array_key_exists('site_logo', $_POST)) {
        $site_logo = trim(esc_url_raw($_POST['site_logo']));

        // Debug: Log POST value
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('KonDernTang POST site_logo: ' . print_r($_POST['site_logo'], true));
            error_log('KonDernTang POST site_logo (trimmed): ' . print_r($site_logo, true));
        }

        if (!empty($site_logo)) {
            set_theme_mod('site_logo', $site_logo);
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('KonDernTang site_logo saved: ' . $site_logo);
            }
        } else {
            // Only clear if explicitly empty string
            remove_theme_mod('site_logo');
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('KonDernTang site_logo removed (empty)');
            }
        }
    } else {
        // Debug: Log if site_logo is not in POST
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('KonDernTang site_logo NOT in POST - keeping existing value');
        }
    }
    // If site_logo is not in POST at all, keep existing value (don't clear it)

    // Logo Fallback Image (optional)
    // Always check if logo_fallback_image is in POST (even if empty string)
    if (array_key_exists('logo_fallback_image', $_POST)) {
        $logo_fallback = trim(esc_url_raw($_POST['logo_fallback_image']));
        if (!empty($logo_fallback)) {
            set_theme_mod('logo_fallback_image', $logo_fallback);
        } else {
            // Only clear if explicitly empty string
            remove_theme_mod('logo_fallback_image');
        }
    }
    // If logo_fallback_image is not in POST at all, keep existing value (don't clear it)

    // Header Settings
    $header_show_search = isset($_POST['header_show_search']) ? '1' : '0';
    set_theme_mod('header_show_search', $header_show_search);

    // Geo-Location Detection Settings
    $geo_location_enabled = isset($_POST['geo_location_enabled']) ? '1' : '0';
    set_theme_mod('geo_location_enabled', $geo_location_enabled);

    if (isset($_POST['geo_location_default_lang'])) {
        $default_lang = sanitize_text_field($_POST['geo_location_default_lang']);
        set_theme_mod('geo_location_default_lang', $default_lang);
    }

    $geo_location_auto_redirect = isset($_POST['geo_location_auto_redirect']) ? '1' : '0';
    set_theme_mod('geo_location_auto_redirect', $geo_location_auto_redirect);

    $geo_location_show_modal = isset($_POST['geo_location_show_modal']) ? '1' : '0';
    set_theme_mod('geo_location_show_modal', $geo_location_show_modal);

    // Language Switcher Settings
    if (isset($_POST['language_switcher_style'])) {
        $switcher_style = sanitize_text_field($_POST['language_switcher_style']);
        if (in_array($switcher_style, array('dropdown', 'modal'), true)) {
            set_theme_mod('language_switcher_style', $switcher_style);
        }
    }

    $language_switcher_show_flags = isset($_POST['language_switcher_show_flags']) ? '1' : '0';
    set_theme_mod('language_switcher_show_flags', $language_switcher_show_flags);

    $language_switcher_show_search = isset($_POST['language_switcher_show_search']) ? '1' : '0';
    set_theme_mod('language_switcher_show_search', $language_switcher_show_search);

    if (isset($_POST['language_switcher_modal_title'])) {
        $modal_title = sanitize_text_field($_POST['language_switcher_modal_title']);
        set_theme_mod('language_switcher_modal_title', $modal_title);
    }

    // Footer Settings
    if (isset($_POST['footer_layout'])) {
        $footer_layout = absint($_POST['footer_layout']);
        if ($footer_layout >= 0 && $footer_layout <= 4) {
            set_theme_mod('footer_layout', $footer_layout);
        }
    }

    if (isset($_POST['footer_copyright_text'])) {
        set_theme_mod('footer_copyright_text', wp_kses_post($_POST['footer_copyright_text']));
    }

    // Cookie Consent Settings
    $cookie_consent_enabled = isset($_POST['cookie_consent_enabled']) ? '1' : '0';
    set_theme_mod('cookie_consent_enabled', $cookie_consent_enabled);

    if (isset($_POST['cookie_consent_message'])) {
        set_theme_mod('cookie_consent_message', sanitize_textarea_field($_POST['cookie_consent_message']));
    }

    if (isset($_POST['cookie_consent_privacy_page'])) {
        set_theme_mod('cookie_consent_privacy_page', absint($_POST['cookie_consent_privacy_page']));
    }

    if (isset($_POST['cookie_consent_position'])) {
        set_theme_mod('cookie_consent_position', sanitize_text_field($_POST['cookie_consent_position']));
    }

    if (isset($_POST['cookie_consent_style'])) {
        set_theme_mod('cookie_consent_style', sanitize_text_field($_POST['cookie_consent_style']));
    }

    if (isset($_POST['cookie_consent_bg_color'])) {
        set_theme_mod('cookie_consent_bg_color', sanitize_hex_color($_POST['cookie_consent_bg_color']));
    }

    if (isset($_POST['cookie_consent_text_color'])) {
        set_theme_mod('cookie_consent_text_color', sanitize_hex_color($_POST['cookie_consent_text_color']));
    }

    if (isset($_POST['cookie_consent_button_bg'])) {
        set_theme_mod('cookie_consent_button_bg', sanitize_hex_color($_POST['cookie_consent_button_bg']));
    }

    if (isset($_POST['cookie_consent_button_text'])) {
        set_theme_mod('cookie_consent_button_text', sanitize_hex_color($_POST['cookie_consent_button_text']));
    }

    if (isset($_POST['cookie_consent_accept_text'])) {
        set_theme_mod('cookie_consent_accept_text', sanitize_text_field($_POST['cookie_consent_accept_text']));
    }

    if (isset($_POST['cookie_consent_decline_text'])) {
        set_theme_mod('cookie_consent_decline_text', sanitize_text_field($_POST['cookie_consent_decline_text']));
    }

    if (isset($_POST['cookie_consent_settings_text'])) {
        set_theme_mod('cookie_consent_settings_text', sanitize_text_field($_POST['cookie_consent_settings_text']));
    }

    $cookie_consent_show_decline = isset($_POST['cookie_consent_show_decline']) ? '1' : '0';
    set_theme_mod('cookie_consent_show_decline', $cookie_consent_show_decline);

    $cookie_consent_auto_hide = isset($_POST['cookie_consent_auto_hide']) ? '1' : '0';
    set_theme_mod('cookie_consent_auto_hide', $cookie_consent_auto_hide);

    if (isset($_POST['cookie_consent_auto_hide_delay'])) {
        $delay = absint($_POST['cookie_consent_auto_hide_delay']);
        if ($delay >= 5 && $delay <= 60) {
            set_theme_mod('cookie_consent_auto_hide_delay', $delay);
        }
    }

    // Cookie Categories
    $cookie_consent_necessary = isset($_POST['cookie_consent_necessary']) ? '1' : '0';
    set_theme_mod('cookie_consent_necessary', $cookie_consent_necessary);

    $cookie_consent_analytics = isset($_POST['cookie_consent_analytics']) ? '1' : '0';
    set_theme_mod('cookie_consent_analytics', $cookie_consent_analytics);

    $cookie_consent_marketing = isset($_POST['cookie_consent_marketing']) ? '1' : '0';
    set_theme_mod('cookie_consent_marketing', $cookie_consent_marketing);

    $cookie_consent_functional = isset($_POST['cookie_consent_functional']) ? '1' : '0';
    set_theme_mod('cookie_consent_functional', $cookie_consent_functional);

    if (isset($_POST['cookie_consent_necessary_desc'])) {
        set_theme_mod('cookie_consent_necessary_desc', sanitize_textarea_field($_POST['cookie_consent_necessary_desc']));
    }

    if (isset($_POST['cookie_consent_analytics_desc'])) {
        set_theme_mod('cookie_consent_analytics_desc', sanitize_textarea_field($_POST['cookie_consent_analytics_desc']));
    }

    if (isset($_POST['cookie_consent_marketing_desc'])) {
        set_theme_mod('cookie_consent_marketing_desc', sanitize_textarea_field($_POST['cookie_consent_marketing_desc']));
    }

    if (isset($_POST['cookie_consent_functional_desc'])) {
        set_theme_mod('cookie_consent_functional_desc', sanitize_textarea_field($_POST['cookie_consent_functional_desc']));
    }

    // Homepage Settings
    $hero_slider_enabled = isset($_POST['hero_slider_enabled']) ? '1' : '0';
    set_theme_mod('hero_slider_enabled', $hero_slider_enabled);

    if (isset($_POST['hero_slider_source'])) {
        $source = sanitize_text_field($_POST['hero_slider_source']);
        if (in_array($source, array('posts', 'banner', 'mixed'), true)) {
            set_theme_mod('hero_slider_source', $source);
        }
    }

    if (isset($_POST['hero_slider_height'])) {
        $height = absint($_POST['hero_slider_height']);
        if ($height >= 300 && $height <= 1000) {
            set_theme_mod('hero_slider_height', $height);
        }
    }

    if (isset($_POST['hero_slider_posts'])) {
        $hero_slider_posts = absint($_POST['hero_slider_posts']);
        if ($hero_slider_posts >= 1 && $hero_slider_posts <= 10) {
            set_theme_mod('hero_slider_posts', $hero_slider_posts);
        }
    }

    $featured_section_enabled = isset($_POST['featured_section_enabled']) ? '1' : '0';
    set_theme_mod('featured_section_enabled', $featured_section_enabled);

    if (isset($_POST['featured_posts_count'])) {
        $featured_posts_count = absint($_POST['featured_posts_count']);
        if ($featured_posts_count >= 1 && $featured_posts_count <= 10) {
            set_theme_mod('featured_posts_count', $featured_posts_count);
        }
    }

    if (isset($_POST['recent_posts_count'])) {
        $recent_posts_count = absint($_POST['recent_posts_count']);
        if ($recent_posts_count >= 1 && $recent_posts_count <= 20) {
            set_theme_mod('recent_posts_count', $recent_posts_count);
        }
    }

    if (isset($_POST['news_section_category'])) {
        set_theme_mod('news_section_category', absint($_POST['news_section_category']));
    }

    if (isset($_POST['news_posts_count'])) {
        $news_posts_count = absint($_POST['news_posts_count']);
        if ($news_posts_count >= 1 && $news_posts_count <= 20) {
            set_theme_mod('news_posts_count', $news_posts_count);
        }
    }

    // Homepage Sections (Multi-Section)
    for ($i = 1; $i <= 3; $i++) {
        $enabled = isset($_POST["homepage_section_{$i}_enabled"]) ? '1' : '0';
        set_theme_mod("homepage_section_{$i}_enabled", $enabled);

        if (isset($_POST["homepage_section_{$i}_category"])) {
            set_theme_mod("homepage_section_{$i}_category", absint($_POST["homepage_section_{$i}_category"]));
        }
        if (isset($_POST["homepage_section_{$i}_count"])) {
            $count = absint($_POST["homepage_section_{$i}_count"]);
            if ($count >= 1 && $count <= 20) {
                set_theme_mod("homepage_section_{$i}_count", $count);
            }
        }
    }

    // CTA Banner
    $cta_banner_enabled = isset($_POST['cta_banner_enabled']) ? '1' : '0';
    set_theme_mod('cta_banner_enabled', $cta_banner_enabled);

    if (isset($_POST['cta_banner_title'])) {
        set_theme_mod('cta_banner_title', sanitize_text_field($_POST['cta_banner_title']));
    }
    if (isset($_POST['cta_banner_subtitle'])) {
        set_theme_mod('cta_banner_subtitle', sanitize_text_field($_POST['cta_banner_subtitle']));
    }
    if (isset($_POST['cta_banner_button_text'])) {
        set_theme_mod('cta_banner_button_text', sanitize_text_field($_POST['cta_banner_button_text']));
    }
    if (isset($_POST['cta_banner_button_url'])) {
        set_theme_mod('cta_banner_button_url', esc_url_raw($_POST['cta_banner_button_url']));
    }

    // Layout Order
    for ($i = 1; $i <= 4; $i++) {
        if (isset($_POST["homepage_layout_slot_{$i}"])) {
            set_theme_mod("homepage_layout_slot_{$i}", sanitize_text_field($_POST["homepage_layout_slot_{$i}"]));
        }
    }

    $newsletter_enabled = isset($_POST['newsletter_enabled']) ? '1' : '0';
    set_theme_mod('newsletter_enabled', $newsletter_enabled);

    if (isset($_POST['trending_tags_count'])) {
        $trending_tags_count = absint($_POST['trending_tags_count']);
        if ($trending_tags_count >= 1 && $trending_tags_count <= 30) {
            set_theme_mod('trending_tags_count', $trending_tags_count);
        }
    }

    $recently_viewed_enabled = isset($_POST['recently_viewed_enabled']) ? '1' : '0';
    set_theme_mod('recently_viewed_enabled', $recently_viewed_enabled);

    // Layout Settings
    if (isset($_POST['layout_container_width'])) {
        $container_width = absint($_POST['layout_container_width']);
        if ($container_width >= 960 && $container_width <= 1920) {
            set_theme_mod('layout_container_width', $container_width);
        }
    }

    if (isset($_POST['layout_archive_sidebar'])) {
        $archive_sidebar = sanitize_text_field($_POST['layout_archive_sidebar']);
        if (in_array($archive_sidebar, array('left', 'right', 'none'), true)) {
            set_theme_mod('layout_archive_sidebar', $archive_sidebar);
        }
    }

    if (isset($_POST['layout_single_sidebar'])) {
        $single_sidebar = sanitize_text_field($_POST['layout_single_sidebar']);
        if (in_array($single_sidebar, array('left', 'right', 'none'), true)) {
            set_theme_mod('layout_single_sidebar', $single_sidebar);
        }
    }

    if (isset($_POST['layout_posts_per_page'])) {
        $posts_per_page = absint($_POST['layout_posts_per_page']);
        if ($posts_per_page >= 1 && $posts_per_page <= 50) {
            set_theme_mod('layout_posts_per_page', $posts_per_page);
        }
    }

    // Breadcrumbs Settings
    $breadcrumbs_enabled = isset($_POST['breadcrumbs_enabled']) ? '1' : '0';
    set_theme_mod('breadcrumbs_enabled', $breadcrumbs_enabled);

    if (isset($_POST['breadcrumbs_home_text'])) {
        set_theme_mod('breadcrumbs_home_text', sanitize_text_field($_POST['breadcrumbs_home_text']));
    }

    if (isset($_POST['breadcrumbs_separator'])) {
        set_theme_mod('breadcrumbs_separator', sanitize_text_field($_POST['breadcrumbs_separator']));
    }

    // Breadcrumbs visibility for different page types
    $breadcrumb_pages = array('single', 'archive', 'search', '404', 'page');
    foreach ($breadcrumb_pages as $page_type) {
        $key = 'breadcrumbs_show_' . $page_type;
        $value = isset($_POST[$key]) ? '1' : '0';
        set_theme_mod($key, $value);
    }

    // Color Settings
    if (isset($_POST['color_primary'])) {
        set_theme_mod('color_primary', sanitize_hex_color($_POST['color_primary']));
    }

    if (isset($_POST['color_secondary'])) {
        set_theme_mod('color_secondary', sanitize_hex_color($_POST['color_secondary']));
    }

    if (isset($_POST['color_text'])) {
        set_theme_mod('color_text', sanitize_hex_color($_POST['color_text']));
    }

    if (isset($_POST['color_background'])) {
        set_theme_mod('color_background', sanitize_hex_color($_POST['color_background']));
    }

    if (isset($_POST['color_link'])) {
        set_theme_mod('color_link', sanitize_hex_color($_POST['color_link']));
    }

    // Typography Settings - Font Families
    if (isset($_POST['typography_body_font'])) {
        $body_font = sanitize_text_field($_POST['typography_body_font']);
        $allowed_fonts = array('system-ui', 'Sarabun', 'Kanit', 'Prompt', 'Noto Sans Thai', 'Arial', 'Helvetica', 'Georgia', 'Times New Roman', 'Sarabun Sans');
        if (in_array($body_font, $allowed_fonts, true)) {
            set_theme_mod('typography_body_font', $body_font);
        }
    }

    if (isset($_POST['typography_heading_font'])) {
        $heading_font = sanitize_text_field($_POST['typography_heading_font']);
        $allowed_fonts = array('system-ui', 'Sarabun', 'Kanit', 'Prompt', 'Noto Sans Thai', 'Arial', 'Helvetica', 'Georgia', 'Times New Roman', 'Sarabun Sans');
        if (in_array($heading_font, $allowed_fonts, true)) {
            set_theme_mod('typography_heading_font', $heading_font);
        }
    }

    if (isset($_POST['typography_menu_font'])) {
        $menu_font = sanitize_text_field($_POST['typography_menu_font']);
        $allowed_fonts = array('system-ui', 'Sarabun', 'Kanit', 'Prompt', 'Noto Sans Thai', 'Arial', 'Helvetica', 'Sarabun Sans');
        if (in_array($menu_font, $allowed_fonts, true)) {
            set_theme_mod('typography_menu_font', $menu_font);
        }
    }

    if (isset($_POST['typography_button_font'])) {
        $button_font = sanitize_text_field($_POST['typography_button_font']);
        $allowed_fonts = array('system-ui', 'inherit', 'Sarabun', 'Kanit', 'Prompt', 'Sarabun Sans');
        if (in_array($button_font, $allowed_fonts, true)) {
            set_theme_mod('typography_button_font', $button_font);
        }
    }

    // Typography Settings - Font Sizes
    if (isset($_POST['typography_body_size'])) {
        $body_size = absint($_POST['typography_body_size']);
        if ($body_size >= 14 && $body_size <= 20) {
            set_theme_mod('typography_body_size', $body_size);
        }
    }

    if (isset($_POST['typography_menu_size'])) {
        $menu_size = absint($_POST['typography_menu_size']);
        if ($menu_size >= 12 && $menu_size <= 24) {
            set_theme_mod('typography_menu_size', $menu_size);
        }
    }

    if (isset($_POST['typography_h1_size'])) {
        $h1_size = absint($_POST['typography_h1_size']);
        if ($h1_size >= 24 && $h1_size <= 48) {
            set_theme_mod('typography_h1_size', $h1_size);
        }
    }

    if (isset($_POST['typography_h2_size'])) {
        $h2_size = absint($_POST['typography_h2_size']);
        if ($h2_size >= 20 && $h2_size <= 36) {
            set_theme_mod('typography_h2_size', $h2_size);
        }
    }

    if (isset($_POST['typography_h3_size'])) {
        $h3_size = absint($_POST['typography_h3_size']);
        if ($h3_size >= 18 && $h3_size <= 30) {
            set_theme_mod('typography_h3_size', $h3_size);
        }
    }

    if (isset($_POST['typography_h4_size'])) {
        $h4_size = absint($_POST['typography_h4_size']);
        if ($h4_size >= 16 && $h4_size <= 24) {
            set_theme_mod('typography_h4_size', $h4_size);
        }
    }

    if (isset($_POST['typography_h5_size'])) {
        $h5_size = absint($_POST['typography_h5_size']);
        if ($h5_size >= 14 && $h5_size <= 20) {
            set_theme_mod('typography_h5_size', $h5_size);
        }
    }

    if (isset($_POST['typography_h6_size'])) {
        $h6_size = absint($_POST['typography_h6_size']);
        if ($h6_size >= 12 && $h6_size <= 18) {
            set_theme_mod('typography_h6_size', $h6_size);
        }
    }

    if (isset($_POST['typography_button_size'])) {
        $button_size = absint($_POST['typography_button_size']);
        if ($button_size >= 12 && $button_size <= 20) {
            set_theme_mod('typography_button_size', $button_size);
        }
    }

    // Typography Settings - Line Heights
    if (isset($_POST['typography_body_line_height'])) {
        $body_line_height = floatval($_POST['typography_body_line_height']);
        if ($body_line_height >= 1.2 && $body_line_height <= 2.0) {
            set_theme_mod('typography_body_line_height', $body_line_height);
        }
    }

    if (isset($_POST['typography_heading_line_height'])) {
        $heading_line_height = floatval($_POST['typography_heading_line_height']);
        if ($heading_line_height >= 1.0 && $heading_line_height <= 1.8) {
            set_theme_mod('typography_heading_line_height', $heading_line_height);
        }
    }

    // Typography Settings - Font Weights
    if (isset($_POST['typography_body_weight'])) {
        $body_weight = absint($_POST['typography_body_weight']);
        $allowed_weights = array(300, 400, 500, 600, 700);
        if (in_array($body_weight, $allowed_weights, true)) {
            set_theme_mod('typography_body_weight', $body_weight);
        }
    }

    if (isset($_POST['typography_heading_weight'])) {
        $heading_weight = absint($_POST['typography_heading_weight']);
        $allowed_weights = array(400, 500, 600, 700, 800, 900);
        if (in_array($heading_weight, $allowed_weights, true)) {
            set_theme_mod('typography_heading_weight', $heading_weight);
        }
    }

    if (isset($_POST['typography_menu_weight'])) {
        $menu_weight = absint($_POST['typography_menu_weight']);
        $allowed_weights = array(400, 500, 600, 700);
        if (in_array($menu_weight, $allowed_weights, true)) {
            set_theme_mod('typography_menu_weight', $menu_weight);
        }
    }

    // TOC Settings
    $toc_enabled = isset($_POST['toc_enabled']) ? '1' : '0';
    set_theme_mod('toc_enabled', $toc_enabled);

    if (isset($_POST['toc_min_headings'])) {
        $toc_min_headings = absint($_POST['toc_min_headings']);
        if ($toc_min_headings >= 1 && $toc_min_headings <= 10) {
            set_theme_mod('toc_min_headings', $toc_min_headings);
        }
    }

    if (isset($_POST['toc_heading_levels'])) {
        $toc_heading_levels = array_map('sanitize_text_field', $_POST['toc_heading_levels']);
        set_theme_mod('toc_heading_levels', $toc_heading_levels);
    }

    if (isset($_POST['toc_title'])) {
        set_theme_mod('toc_title', sanitize_text_field($_POST['toc_title']));
    }

    $toc_collapsible = isset($_POST['toc_collapsible']) ? '1' : '0';
    set_theme_mod('toc_collapsible', $toc_collapsible);

    $toc_smooth_scroll = isset($_POST['toc_smooth_scroll']) ? '1' : '0';
    set_theme_mod('toc_smooth_scroll', $toc_smooth_scroll);

    $toc_scroll_spy = isset($_POST['toc_scroll_spy']) ? '1' : '0';
    set_theme_mod('toc_scroll_spy', $toc_scroll_spy);

    // Social Media Settings - Profiles
    $social_platforms = array('facebook', 'twitter', 'instagram', 'youtube', 'tiktok', 'line', 'pinterest', 'linkedin');
    foreach ($social_platforms as $platform) {
        $key = 'social_' . $platform;
        if (isset($_POST[$key])) {
            $url = esc_url_raw(trim($_POST[$key]));
            set_theme_mod($key, $url);
        }
    }

    // Social Media Settings - Display Options
    $social_show_header = isset($_POST['social_show_header']) ? '1' : '0';
    set_theme_mod('social_show_header', $social_show_header);

    $social_show_footer = isset($_POST['social_show_footer']) ? '1' : '0';
    set_theme_mod('social_show_footer', $social_show_footer);

    if (isset($_POST['social_icon_style'])) {
        $icon_style = sanitize_text_field($_POST['social_icon_style']);
        if (in_array($icon_style, array('default', 'rounded', 'square', 'outline'), true)) {
            set_theme_mod('social_icon_style', $icon_style);
        }
    }

    if (isset($_POST['social_icon_size'])) {
        $icon_size = sanitize_text_field($_POST['social_icon_size']);
        if (in_array($icon_size, array('small', 'medium', 'large'), true)) {
            set_theme_mod('social_icon_size', $icon_size);
        }
    }

    $social_open_new_tab = isset($_POST['social_open_new_tab']) ? '1' : '0';
    set_theme_mod('social_open_new_tab', $social_open_new_tab);

    // Social Sharing Settings
    $share_enabled = isset($_POST['share_enabled']) ? '1' : '0';
    set_theme_mod('share_enabled', $share_enabled);

    if (isset($_POST['share_position'])) {
        $share_position = sanitize_text_field($_POST['share_position']);
        if (in_array($share_position, array('top', 'bottom', 'both', 'floating'), true)) {
            set_theme_mod('share_position', $share_position);
        }
    }

    if (isset($_POST['share_style'])) {
        $share_style = sanitize_text_field($_POST['share_style']);
        if (in_array($share_style, array('icon', 'icon-text', 'button'), true)) {
            set_theme_mod('share_style', $share_style);
        }
    }

    if (isset($_POST['share_platforms']) && is_array($_POST['share_platforms'])) {
        $allowed_platforms = array('facebook', 'twitter', 'line', 'email', 'copy', 'pinterest', 'linkedin', 'whatsapp');
        $share_platforms = array_intersect(array_map('sanitize_text_field', $_POST['share_platforms']), $allowed_platforms);
        set_theme_mod('share_platforms', $share_platforms);
    } else {
        set_theme_mod('share_platforms', array());
    }

    $share_show_count = isset($_POST['share_show_count']) ? '1' : '0';
    set_theme_mod('share_show_count', $share_show_count);

    if (isset($_POST['share_label'])) {
        set_theme_mod('share_label', sanitize_text_field($_POST['share_label']));
    }

    // Advanced Settings
    if (isset($_POST['advanced_custom_css'])) {
        set_theme_mod('advanced_custom_css', wp_strip_all_tags($_POST['advanced_custom_css']));
    }

    if (isset($_POST['advanced_custom_js'])) {
        set_theme_mod('advanced_custom_js', wp_strip_all_tags($_POST['advanced_custom_js']));
    }

    if (isset($_POST['advanced_google_analytics'])) {
        set_theme_mod('advanced_google_analytics', wp_kses_post($_POST['advanced_google_analytics']));
    }

    if (isset($_POST['advanced_facebook_pixel'])) {
        set_theme_mod('advanced_facebook_pixel', wp_kses_post($_POST['advanced_facebook_pixel']));
    }

    // Debug: Log saved values
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('KonDernTang Settings Saved - site_logo: ' . print_r(konderntang_get_option('site_logo', ''), true));
        error_log('KonDernTang Settings Saved - logo_fallback_image: ' . print_r(konderntang_get_option('logo_fallback_image', ''), true));
    }

    add_settings_error(
        'konderntang_settings',
        'konderntang_settings_saved',
        esc_html__('Settings saved successfully!', 'konderntang'),
        'success'
    );

    // Redirect to prevent resubmission
    $section = isset($_POST['active_section']) ? sanitize_text_field($_POST['active_section']) : 'general';
    $redirect_url = add_query_arg(
        array(
            'page' => 'konderntang-settings',
            'section' => $section,
            'settings-updated' => 'true',
        ),
        admin_url('admin.php')
    );
    wp_safe_redirect($redirect_url);
    exit;
}
add_action('admin_init', 'konderntang_save_settings');

/**
 * Settings page render callback
 */
function konderntang_settings_page_render()
{
    // Display settings errors
    settings_errors('konderntang_settings');

    // Debug: Log loaded values
    if (defined('WP_DEBUG') && WP_DEBUG) {
        $site_logo_debug = konderntang_get_option('site_logo', '');
        error_log('KonDernTang Settings Page Render - site_logo loaded: ' . print_r($site_logo_debug, true));
        error_log('KonDernTang Settings Page Render - logo_fallback_image loaded: ' . print_r(konderntang_get_option('logo_fallback_image', ''), true));
    }

    // Get active section from GET parameter or default
    $active_section = isset($_GET['section']) ? sanitize_text_field($_GET['section']) : 'general';

    // Define all sections
    $sections = array(
        'general' => array(
            'label' => esc_html__('General', 'konderntang'),
            'icon' => 'dashicons-admin-generic',
            'description' => esc_html__('Basic theme settings', 'konderntang'),
        ),
        'header-footer' => array(
            'label' => esc_html__('Header & Footer', 'konderntang'),
            'icon' => 'dashicons-admin-appearance',
            'description' => esc_html__('Header and footer settings', 'konderntang'),
        ),
        'homepage' => array(
            'label' => esc_html__('Homepage', 'konderntang'),
            'icon' => 'dashicons-admin-home',
            'description' => esc_html__('Homepage sections and features', 'konderntang'),
        ),
        'layout' => array(
            'label' => esc_html__('Layout', 'konderntang'),
            'icon' => 'dashicons-layout',
            'description' => esc_html__('Page layout and sidebar settings', 'konderntang'),
        ),
        'colors' => array(
            'label' => esc_html__('Colors', 'konderntang'),
            'icon' => 'dashicons-art',
            'description' => esc_html__('Theme color scheme', 'konderntang'),
        ),
        'typography' => array(
            'label' => esc_html__('Typography', 'konderntang'),
            'icon' => 'dashicons-editor-textcolor',
            'description' => esc_html__('Fonts and text settings', 'konderntang'),
        ),
        'toc' => array(
            'label' => esc_html__('Table of Contents', 'konderntang'),
            'icon' => 'dashicons-list-view',
            'description' => esc_html__('TOC display options', 'konderntang'),
        ),
        'social' => array(
            'label' => esc_html__('Social Media', 'konderntang'),
            'icon' => 'dashicons-share',
            'description' => esc_html__('Social profiles and sharing', 'konderntang'),
        ),
        'cookie' => array(
            'label' => esc_html__('Cookie Consent', 'konderntang'),
            'icon' => 'dashicons-privacy',
            'description' => esc_html__('GDPR cookie settings', 'konderntang'),
        ),
        'advanced' => array(
            'label' => esc_html__('Advanced', 'konderntang'),
            'icon' => 'dashicons-admin-tools',
            'description' => esc_html__('Custom code and analytics', 'konderntang'),
        ),
    );

    // Get current settings
    $logo_fallback_image = konderntang_get_option('logo_fallback_image', '');
    $header_show_search = konderntang_get_option('header_show_search', true);

    // Geo-Location Detection Settings
    $geo_location_enabled = konderntang_get_option('geo_location_enabled', false);
    $geo_location_default_lang = konderntang_get_option('geo_location_default_lang', '');
    $geo_location_auto_redirect = konderntang_get_option('geo_location_auto_redirect', false);
    $geo_location_show_modal = konderntang_get_option('geo_location_show_modal', true);

    // Language Switcher Settings
    $language_switcher_style = konderntang_get_option('language_switcher_style', 'dropdown');
    $language_switcher_show_flags = konderntang_get_option('language_switcher_show_flags', true);
    $language_switcher_show_search = konderntang_get_option('language_switcher_show_search', false);
    $language_switcher_modal_title = konderntang_get_option('language_switcher_modal_title', esc_html__('เลือกภาษา', 'konderntang'));

    // Get Polylang languages if available
    $polylang_languages = array();
    if (function_exists('pll_the_languages')) {
        $polylang_languages_raw = pll_the_languages(array('raw' => 1));
        if (is_array($polylang_languages_raw)) {
            foreach ($polylang_languages_raw as $lang) {
                $polylang_languages[$lang['slug']] = $lang['name'];
            }
        }
    }

    $footer_layout = konderntang_get_option('footer_layout', '0');
    $footer_copyright_text = konderntang_get_option('footer_copyright_text', sprintf(esc_html__('&copy; %1$s %2$s - %3$s', 'konderntang'), date('Y'), get_bloginfo('name'), esc_html__('เพื่อนเดินทางของคุณ', 'konderntang')));

    // Cookie Consent Settings
    $cookie_consent_enabled = konderntang_get_option('cookie_consent_enabled', true);
    $cookie_consent_message = konderntang_get_option('cookie_consent_message', esc_html__('เราใช้คุกกี้เพื่อปรับปรุงประสบการณ์การใช้งานของคุณ', 'konderntang'));
    $cookie_consent_privacy_page = konderntang_get_option('cookie_consent_privacy_page', 0);
    $cookie_consent_position = konderntang_get_option('cookie_consent_position', 'bottom');
    $cookie_consent_style = konderntang_get_option('cookie_consent_style', 'bar');
    $cookie_consent_bg_color = konderntang_get_option('cookie_consent_bg_color', '#ffffff');
    $cookie_consent_text_color = konderntang_get_option('cookie_consent_text_color', '#374151');
    $cookie_consent_button_bg = konderntang_get_option('cookie_consent_button_bg', '#3b82f6');
    $cookie_consent_button_text = konderntang_get_option('cookie_consent_button_text', '#ffffff');
    $cookie_consent_accept_text = konderntang_get_option('cookie_consent_accept_text', esc_html__('ยอมรับทั้งหมด', 'konderntang'));
    $cookie_consent_decline_text = konderntang_get_option('cookie_consent_decline_text', esc_html__('ปฏิเสธทั้งหมด', 'konderntang'));
    $cookie_consent_settings_text = konderntang_get_option('cookie_consent_settings_text', esc_html__('ตั้งค่า', 'konderntang'));
    $cookie_consent_show_decline = konderntang_get_option('cookie_consent_show_decline', true);
    $cookie_consent_auto_hide = konderntang_get_option('cookie_consent_auto_hide', false);
    $cookie_consent_auto_hide_delay = konderntang_get_option('cookie_consent_auto_hide_delay', 10);

    // Cookie Categories
    $cookie_consent_necessary = konderntang_get_option('cookie_consent_necessary', true);
    $cookie_consent_analytics = konderntang_get_option('cookie_consent_analytics', true);
    $cookie_consent_marketing = konderntang_get_option('cookie_consent_marketing', true);
    $cookie_consent_functional = konderntang_get_option('cookie_consent_functional', true);
    $cookie_consent_necessary_desc = konderntang_get_option('cookie_consent_necessary_desc', esc_html__('คุกกี้ที่จำเป็นสำหรับการทำงานพื้นฐานของเว็บไซต์', 'konderntang'));
    $cookie_consent_analytics_desc = konderntang_get_option('cookie_consent_analytics_desc', esc_html__('คุกกี้สำหรับวิเคราะห์การใช้งานเว็บไซต์เพื่อปรับปรุงประสบการณ์', 'konderntang'));
    $cookie_consent_marketing_desc = konderntang_get_option('cookie_consent_marketing_desc', esc_html__('คุกกี้สำหรับการตลาดและโฆษณาที่ตรงกับความสนใจของคุณ', 'konderntang'));
    $cookie_consent_functional_desc = konderntang_get_option('cookie_consent_functional_desc', esc_html__('คุกกี้สำหรับฟังก์ชันเสริมเช่นการแชร์โซเชียลมีเดีย', 'konderntang'));

    // Homepage Settings
    $hero_slider_enabled = konderntang_get_option('hero_slider_enabled', true);
    $hero_slider_source = konderntang_get_option('hero_slider_source', 'banner');
    $hero_slider_height = konderntang_get_option('hero_slider_height', 500);
    $hero_slider_posts = konderntang_get_option('hero_slider_posts', 4);
    $featured_section_enabled = konderntang_get_option('featured_section_enabled', true);
    $featured_posts_count = konderntang_get_option('featured_posts_count', 3);
    $featured_posts_count = konderntang_get_option('featured_posts_count', 3);
    $recent_posts_count = konderntang_get_option('recent_posts_count', 6);
    $news_section_category = konderntang_get_option('news_section_category', 0);
    $news_posts_count = konderntang_get_option('news_posts_count', 4);

    // Get Multi-Section Options
    $homepage_sections = array();
    for ($i = 1; $i <= 3; $i++) {
        $homepage_sections[$i] = array(
            'enabled' => konderntang_get_option("homepage_section_{$i}_enabled", false), // Default disabled
            'category' => konderntang_get_option("homepage_section_{$i}_category", 0),
            'count' => konderntang_get_option("homepage_section_{$i}_count", 4)
        );
    }

    // CTA Banner Settings
    $cta_banner_enabled = konderntang_get_option('cta_banner_enabled', false);
    $cta_banner_title = konderntang_get_option('cta_banner_title', esc_html__('Ready to Explore?', 'konderntang'));
    $cta_banner_subtitle = konderntang_get_option('cta_banner_subtitle', esc_html__('Join our community and discover amazing content!', 'konderntang'));
    $cta_banner_button_text = konderntang_get_option('cta_banner_button_text', esc_html__('Get Started', 'konderntang'));
    $cta_banner_button_url = konderntang_get_option('cta_banner_button_url', '#');

    // Layout Order Defaults
    $homepage_layout_slot_1 = konderntang_get_option('homepage_layout_slot_1', 'section_1');
    $homepage_layout_slot_2 = konderntang_get_option('homepage_layout_slot_2', 'section_2');
    $homepage_layout_slot_3 = konderntang_get_option('homepage_layout_slot_3', 'section_3');
    $homepage_layout_slot_4 = konderntang_get_option('homepage_layout_slot_4', 'cta_banner');

    $newsletter_enabled = konderntang_get_option('newsletter_enabled', true);

    // Get all categories for selection
    $all_categories = get_categories(array('hide_empty' => false));
    $newsletter_enabled = konderntang_get_option('newsletter_enabled', true);
    $trending_tags_count = konderntang_get_option('trending_tags_count', 10);
    $recently_viewed_enabled = konderntang_get_option('recently_viewed_enabled', true);

    // Layout Settings
    $layout_container_width = konderntang_get_option('layout_container_width', 1200);
    $layout_archive_sidebar = konderntang_get_option('layout_archive_sidebar', 'right');
    $layout_single_sidebar = konderntang_get_option('layout_single_sidebar', 'right');
    $layout_posts_per_page = konderntang_get_option('layout_posts_per_page', 10);

    // Breadcrumbs Settings
    $breadcrumbs_enabled = konderntang_get_option('breadcrumbs_enabled', true);
    $breadcrumbs_home_text = konderntang_get_option('breadcrumbs_home_text', esc_html__('หน้าแรก', 'konderntang'));
    $breadcrumbs_separator = konderntang_get_option('breadcrumbs_separator', 'caret-right'); // caret-right, slash, arrow-right, chevron-right
    $breadcrumbs_show_single = konderntang_get_option('breadcrumbs_show_single', true);
    $breadcrumbs_show_archive = konderntang_get_option('breadcrumbs_show_archive', false);
    $breadcrumbs_show_search = konderntang_get_option('breadcrumbs_show_search', false);
    $breadcrumbs_show_404 = konderntang_get_option('breadcrumbs_show_404', false);
    $breadcrumbs_show_page = konderntang_get_option('breadcrumbs_show_page', false);

    // Color Settings
    $color_primary = konderntang_get_option('color_primary', '#0ea5e9');
    $color_secondary = konderntang_get_option('color_secondary', '#64748b');
    $color_text = konderntang_get_option('color_text', '#1e293b');
    $color_background = konderntang_get_option('color_background', '#ffffff');
    $color_link = konderntang_get_option('color_link', '#0ea5e9');

    // Typography Settings - Font Families
    $typography_body_font = konderntang_get_option('typography_body_font', 'Sarabun');
    $typography_heading_font = konderntang_get_option('typography_heading_font', 'Kanit');
    $typography_menu_font = konderntang_get_option('typography_menu_font', 'system-ui');
    $typography_button_font = konderntang_get_option('typography_button_font', 'system-ui');

    // Typography Settings - Font Sizes
    $typography_body_size = konderntang_get_option('typography_body_size', 18);
    $typography_menu_size = konderntang_get_option('typography_menu_size', 16);
    $typography_h1_size = konderntang_get_option('typography_h1_size', 30);
    $typography_h2_size = konderntang_get_option('typography_h2_size', 24);
    $typography_h3_size = konderntang_get_option('typography_h3_size', 24);
    $typography_h4_size = konderntang_get_option('typography_h4_size', 18);
    $typography_h5_size = konderntang_get_option('typography_h5_size', 16);
    $typography_h6_size = konderntang_get_option('typography_h6_size', 14);
    $typography_button_size = konderntang_get_option('typography_button_size', 16);

    // Typography Settings - Line Heights
    $typography_body_line_height = konderntang_get_option('typography_body_line_height', 1.75);
    $typography_heading_line_height = konderntang_get_option('typography_heading_line_height', 1.3);

    // Typography Settings - Font Weights
    $typography_body_weight = konderntang_get_option('typography_body_weight', 400);
    $typography_heading_weight = konderntang_get_option('typography_heading_weight', 700);
    $typography_menu_weight = konderntang_get_option('typography_menu_weight', 500);

    // TOC Settings
    $toc_enabled = konderntang_get_option('toc_enabled', true);
    $toc_min_headings = konderntang_get_option('toc_min_headings', 2);
    $toc_heading_levels = konderntang_get_option('toc_heading_levels', array('h2', 'h3', 'h4'));
    if (!is_array($toc_heading_levels)) {
        $toc_heading_levels = array('h2', 'h3', 'h4');
    }
    $toc_title = konderntang_get_option('toc_title', esc_html__('สารบัญ', 'konderntang'));
    $toc_collapsible = konderntang_get_option('toc_collapsible', true);
    $toc_smooth_scroll = konderntang_get_option('toc_smooth_scroll', true);
    $toc_scroll_spy = konderntang_get_option('toc_scroll_spy', true);

    // Social Media Settings
    $social_facebook = konderntang_get_option('social_facebook', '');
    $social_twitter = konderntang_get_option('social_twitter', '');
    $social_instagram = konderntang_get_option('social_instagram', '');
    $social_youtube = konderntang_get_option('social_youtube', '');
    $social_tiktok = konderntang_get_option('social_tiktok', '');
    $social_line = konderntang_get_option('social_line', '');
    $social_pinterest = konderntang_get_option('social_pinterest', '');
    $social_linkedin = konderntang_get_option('social_linkedin', '');
    $social_show_header = konderntang_get_option('social_show_header', false);
    $social_show_footer = konderntang_get_option('social_show_footer', true);
    $social_icon_style = konderntang_get_option('social_icon_style', 'default');
    $social_icon_size = konderntang_get_option('social_icon_size', 'medium');
    $social_open_new_tab = konderntang_get_option('social_open_new_tab', true);

    // Social Sharing Settings
    $share_enabled = konderntang_get_option('share_enabled', true);
    $share_position = konderntang_get_option('share_position', 'bottom');
    $share_style = konderntang_get_option('share_style', 'icon');
    $share_platforms = konderntang_get_option('share_platforms', array('facebook', 'twitter', 'line', 'copy'));
    if (!is_array($share_platforms)) {
        $share_platforms = array('facebook', 'twitter', 'line', 'copy');
    }
    $share_show_count = konderntang_get_option('share_show_count', false);
    $share_label = konderntang_get_option('share_label', esc_html__('แชร์บทความนี้', 'konderntang'));

    // Advanced Settings
    $advanced_custom_css = konderntang_get_option('advanced_custom_css', '');
    $advanced_custom_js = konderntang_get_option('advanced_custom_js', '');
    $advanced_google_analytics = konderntang_get_option('advanced_google_analytics', '');
    $advanced_facebook_pixel = konderntang_get_option('advanced_facebook_pixel', '');

    // Enqueue media uploader
    wp_enqueue_media();
    ?>
    <div class="wrap konderntang-settings-wrap-new">
        <div class="konderntang-settings-header">
            <h1 class="konderntang-dashboard-title">
                <span class="dashicons dashicons-admin-settings"></span>
                <?php echo esc_html(get_admin_page_title()); ?>
            </h1>
            <div class="konderntang-settings-actions">
                <button type="submit" name="konderntang_save_settings" form="konderntang-settings-form"
                    class="button button-primary button-large konderntang-save-btn">
                    <span class="dashicons dashicons-yes-alt"></span>
                    <?php esc_html_e('Save Changes', 'konderntang'); ?>
                </button>
            </div>
        </div>

        <div class="konderntang-settings-layout">
            <!-- Sidebar Navigation -->
            <div class="konderntang-settings-sidebar">
                <div class="konderntang-sidebar-search">
                    <input type="text" id="konderntang-settings-search"
                        placeholder="<?php esc_attr_e('Search settings...', 'konderntang'); ?>" />
                    <span class="dashicons dashicons-search"></span>
                </div>
                <nav class="konderntang-sidebar-nav">
                    <?php foreach ($sections as $section_key => $section_data): ?>
                        <a href="#section-<?php echo esc_attr($section_key); ?>"
                            class="konderntang-nav-item <?php echo $active_section === $section_key ? 'active' : ''; ?>"
                            data-section="<?php echo esc_attr($section_key); ?>">
                            <span class="dashicons <?php echo esc_attr($section_data['icon']); ?>"></span>
                            <div class="nav-item-content">
                                <span class="nav-item-label"><?php echo esc_html($section_data['label']); ?></span>
                                <span class="nav-item-desc"><?php echo esc_html($section_data['description']); ?></span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="konderntang-settings-content">
                <form method="post" action="" id="konderntang-settings-form">
                    <?php wp_nonce_field('konderntang_settings_nonce'); ?>
                    <input type="hidden" name="active_section" id="active_section"
                        value="<?php echo esc_attr($active_section); ?>" />

                    <!-- General Settings -->
                    <div class="konderntang-settings-section" id="section-general" data-section="general">
                        <div class="konderntang-section-header">
                            <div class="section-header-content">
                                <span class="dashicons dashicons-admin-generic"></span>
                                <div>
                                    <h2><?php esc_html_e('General Settings', 'konderntang'); ?></h2>
                                    <p><?php esc_html_e('Basic theme configuration', 'konderntang'); ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="konderntang-section-content">
                            <table class="form-table">
                                <tr>
                                    <th scope="row">
                                        <label for="site_logo">
                                            <span class="dashicons dashicons-format-image"></span>
                                            <?php esc_html_e('Site Logo', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <?php
                                        $site_logo = konderntang_get_option('site_logo', '');
                                        ?>
                                        <div class="konderntang-field-group">
                                            <input type="text" id="site_logo" name="site_logo"
                                                value="<?php echo esc_attr($site_logo); ?>" class="regular-text" />
                                            <button type="button" class="button media-upload-button"
                                                data-target="site_logo">
                                                <span class="dashicons dashicons-upload"></span>
                                                <?php esc_html_e('Upload Logo', 'konderntang'); ?>
                                            </button>
                                            <button type="button" class="button konderntang-remove-image" <?php echo $site_logo ? '' : 'style="display:none;"'; ?>>
                                                <span class="dashicons dashicons-trash"></span>
                                            </button>
                                        </div>
                                        <div class="konderntang-image-preview" <?php echo $site_logo ? '' : 'style="display:none;"'; ?>>
                                            <?php if ($site_logo): ?>
                                                <img src="<?php echo esc_url($site_logo); ?>" alt="Logo Preview" />
                                            <?php endif; ?>
                                        </div>
                                        <p class="description">
                                            <?php esc_html_e('อัปโหลดโลโก้ที่จะแสดงในส่วน header ของเว็บไซต์ หากไม่มีการอัปโหลดโลโก้ ระบบจะแสดงชื่อเว็บไซต์แทน', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="logo_fallback_image">
                                            <span class="dashicons dashicons-format-image"></span>
                                            <?php esc_html_e('Logo Fallback Image', 'konderntang'); ?>
                                            <span
                                                style="color: #94a3b8; font-weight: normal; font-size: 12px;">(<?php esc_html_e('ไม่บังคับ', 'konderntang'); ?>)</span>
                                        </label>
                                    </th>
                                    <td>
                                        <div class="konderntang-field-group">
                                            <input type="text" id="logo_fallback_image" name="logo_fallback_image"
                                                value="<?php echo esc_attr($logo_fallback_image); ?>"
                                                class="regular-text" />
                                            <button type="button" class="button media-upload-button"
                                                data-target="logo_fallback_image">
                                                <span class="dashicons dashicons-upload"></span>
                                                <?php esc_html_e('Upload', 'konderntang'); ?>
                                            </button>
                                            <button type="button" class="button konderntang-remove-image" <?php echo $logo_fallback_image ? '' : 'style="display:none;"'; ?>>
                                                <span class="dashicons dashicons-trash"></span>
                                            </button>
                                        </div>
                                        <div class="konderntang-image-preview" <?php echo $logo_fallback_image ? '' : 'style="display:none;"'; ?>>
                                            <?php if ($logo_fallback_image): ?>
                                                <img src="<?php echo esc_url($logo_fallback_image); ?>"
                                                    alt="Fallback Logo Preview" />
                                            <?php endif; ?>
                                        </div>
                                        <p class="description">
                                            <?php esc_html_e('โลโก้สำรอง (ไม่บังคับ) - จะใช้เมื่อไม่มี Site Logo และไม่มี WordPress Custom Logo', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Header & Footer Settings -->
                    <div class="konderntang-settings-section" id="section-header-footer" data-section="header-footer">
                        <div class="konderntang-section-header">
                            <div class="section-header-content">
                                <span class="dashicons dashicons-admin-appearance"></span>
                                <div>
                                    <h2><?php esc_html_e('Header & Footer Settings', 'konderntang'); ?></h2>
                                    <p><?php esc_html_e('Configure header and footer sections', 'konderntang'); ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="konderntang-section-content">
                            <table class="form-table">
                                <!-- Header Settings -->
                                <tr>
                                    <th scope="row" colspan="2">
                                        <h3
                                            style="margin: 0 0 10px; padding-bottom: 10px; border-bottom: 1px solid #e2e8f0;">
                                            <span class="dashicons dashicons-admin-appearance"></span>
                                            <?php esc_html_e('Header Settings', 'konderntang'); ?>
                                        </h3>
                                    </th>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <span class="dashicons dashicons-search"></span>
                                        <?php esc_html_e('Search Button', 'konderntang'); ?>
                                    </th>
                                    <td>
                                        <label class="konderntang-toggle">
                                            <input type="checkbox" name="header_show_search" value="1" <?php checked($header_show_search, true); ?> />
                                            <span class="toggle-slider"></span>
                                            <span
                                                class="toggle-label"><?php esc_html_e('Show search button in header', 'konderntang'); ?></span>
                                        </label>
                                        <p class="description">
                                            <?php esc_html_e('แสดงปุ่มค้นหาในส่วน header navigation', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>

                                <?php
                                // Check if Polylang is active
                                $polylang_active = function_exists('pll_the_languages');
                                $polylang_has_languages = $polylang_active && !empty($polylang_languages) && count($polylang_languages) >= 2;
                                ?>

                                <!-- Polylang Status Notice -->
                                <?php if (!$polylang_active): ?>
                                    <tr>
                                        <th scope="row">
                                            <span class="dashicons dashicons-warning" style="color: #d63638;"></span>
                                            <?php esc_html_e('Polylang Required', 'konderntang'); ?>
                                        </th>
                                        <td>
                                            <div class="notice notice-error inline" style="margin: 0; padding: 10px 15px;">
                                                <p>
                                                    <strong><?php esc_html_e('Polylang plugin is not installed or activated.', 'konderntang'); ?></strong>
                                                </p>
                                                <p>
                                                    <?php esc_html_e('ฟีเจอร์ Geo-Location Detection และ Language Switcher ต้องการปลั๊กอิน Polylang เพื่อทำงานได้', 'konderntang'); ?>
                                                </p>
                                                <p>
                                                    <a href="<?php echo esc_url(admin_url('plugin-install.php?s=polylang&tab=search&type=term')); ?>"
                                                        class="button button-primary">
                                                        <span class="dashicons dashicons-download"
                                                            style="vertical-align: middle; margin-right: 5px;"></span>
                                                        <?php esc_html_e('Install Polylang', 'konderntang'); ?>
                                                    </a>
                                                </p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php elseif (!$polylang_has_languages): ?>
                                    <tr>
                                        <th scope="row">
                                            <span class="dashicons dashicons-info" style="color: #dba617;"></span>
                                            <?php esc_html_e('Languages Required', 'konderntang'); ?>
                                        </th>
                                        <td>
                                            <div class="notice notice-warning inline" style="margin: 0; padding: 10px 15px;">
                                                <p>
                                                    <strong><?php esc_html_e('Polylang is installed but no languages are configured.', 'konderntang'); ?></strong>
                                                </p>
                                                <p>
                                                    <?php esc_html_e('กรุณาตั้งค่าภาษาอย่างน้อย 2 ภาษาใน Polylang เพื่อใช้งานฟีเจอร์ Language Switcher', 'konderntang'); ?>
                                                </p>
                                                <p>
                                                    <a href="<?php echo esc_url(admin_url('admin.php?page=mlang')); ?>"
                                                        class="button button-primary">
                                                        <span class="dashicons dashicons-translation"
                                                            style="vertical-align: middle; margin-right: 5px;"></span>
                                                        <?php esc_html_e('Configure Languages', 'konderntang'); ?>
                                                    </a>
                                                </p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>

                                <!-- Geo-Location Detection -->
                                <tr>
                                    <th scope="row">
                                        <span class="dashicons dashicons-location"></span>
                                        <?php esc_html_e('Geo-Location Detection', 'konderntang'); ?>
                                    </th>
                                    <td>
                                        <?php if (!$polylang_has_languages): ?>
                                            <label class="konderntang-toggle" style="opacity: 0.5; pointer-events: none;">
                                                <input type="checkbox" name="geo_location_enabled" value="1" disabled />
                                                <span class="toggle-slider"></span>
                                                <span
                                                    class="toggle-label"><?php esc_html_e('Enable Geo-Location Detection', 'konderntang'); ?></span>
                                            </label>
                                            <p class="description" style="color: #d63638;">
                                                <?php esc_html_e('⚠️ ต้องติดตั้ง Polylang และตั้งค่าอย่างน้อย 2 ภาษาก่อนจึงจะใช้งานได้', 'konderntang'); ?>
                                            </p>
                                        <?php else: ?>
                                            <label class="konderntang-toggle">
                                                <input type="checkbox" name="geo_location_enabled" value="1" <?php checked($geo_location_enabled, true); ?> />
                                                <span class="toggle-slider"></span>
                                                <span
                                                    class="toggle-label"><?php esc_html_e('Enable Geo-Location Detection', 'konderntang'); ?></span>
                                            </label>
                                            <p class="description">
                                                <?php esc_html_e('ตรวจสอบประเทศของผู้ใช้และแนะนำภาษาที่เหมาะสม', 'konderntang'); ?>
                                            </p>

                                            <?php if ($geo_location_enabled): ?>
                                                <div
                                                    style="margin-top: 15px; padding-left: 30px; border-left: 3px solid #2271b1; background: #f6f7f7; padding: 15px 15px 15px 30px; border-radius: 0 4px 4px 0;">
                                                    <p>
                                                        <label for="geo_location_default_lang">
                                                            <strong><?php esc_html_e('Default Language:', 'konderntang'); ?></strong>
                                                        </label>
                                                        <select name="geo_location_default_lang" id="geo_location_default_lang"
                                                            style="margin-left: 10px;">
                                                            <option value=""><?php esc_html_e('-- Select --', 'konderntang'); ?>
                                                            </option>
                                                            <?php foreach ($polylang_languages as $lang_code => $lang_name): ?>
                                                                <option value="<?php echo esc_attr($lang_code); ?>" <?php selected($geo_location_default_lang, $lang_code); ?>>
                                                                    <?php echo esc_html($lang_name); ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                        <span class="description" style="display: block; margin-top: 5px;">
                                                            <?php esc_html_e('ภาษาที่จะใช้เมื่อไม่สามารถตรวจจับประเทศได้', 'konderntang'); ?>
                                                        </span>
                                                    </p>
                                                    <p style="margin-top: 15px;">
                                                        <label class="konderntang-toggle">
                                                            <input type="checkbox" name="geo_location_auto_redirect" value="1" <?php checked($geo_location_auto_redirect, true); ?> />
                                                            <span class="toggle-slider"></span>
                                                            <span
                                                                class="toggle-label"><?php esc_html_e('Auto-redirect on first visit', 'konderntang'); ?></span>
                                                        </label>
                                                        <span class="description"
                                                            style="display: block; margin-left: 50px; margin-top: 5px;">
                                                            <?php esc_html_e('เปลี่ยนภาษาอัตโนมัติตามประเทศของผู้ใช้ (ใช้ครั้งแรกเท่านั้น)', 'konderntang'); ?>
                                                        </span>
                                                    </p>
                                                    <p style="margin-top: 15px;">
                                                        <label class="konderntang-toggle">
                                                            <input type="checkbox" name="geo_location_show_modal" value="1" <?php checked($geo_location_show_modal, true); ?> />
                                                            <span class="toggle-slider"></span>
                                                            <span
                                                                class="toggle-label"><?php esc_html_e('Show language selection modal', 'konderntang'); ?></span>
                                                        </label>
                                                        <span class="description"
                                                            style="display: block; margin-left: 50px; margin-top: 5px;">
                                                            <?php esc_html_e('แสดง popup ให้ผู้ใช้เลือกภาษาเมื่อตรวจพบว่ามาจากต่างประเทศ', 'konderntang'); ?>
                                                        </span>
                                                    </p>
                                                </div>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>

                                <!-- Language Switcher Style -->
                                <tr>
                                    <th scope="row">
                                        <span class="dashicons dashicons-translation"></span>
                                        <?php esc_html_e('Language Switcher Style', 'konderntang'); ?>
                                    </th>
                                    <td>
                                        <?php if (!$polylang_has_languages): ?>
                                            <div style="opacity: 0.5; pointer-events: none;">
                                                <label style="margin-right: 20px;">
                                                    <input type="radio" name="language_switcher_style" value="dropdown"
                                                        disabled />
                                                    <?php esc_html_e('Dropdown (default)', 'konderntang'); ?>
                                                </label>
                                                <label>
                                                    <input type="radio" name="language_switcher_style" value="modal" disabled />
                                                    <?php esc_html_e('Modal Popup', 'konderntang'); ?>
                                                </label>
                                            </div>
                                            <p class="description" style="color: #d63638;">
                                                <?php esc_html_e('⚠️ ต้องติดตั้ง Polylang และตั้งค่าอย่างน้อย 2 ภาษาก่อนจึงจะใช้งานได้', 'konderntang'); ?>
                                            </p>
                                        <?php else: ?>
                                            <label style="margin-right: 20px;">
                                                <input type="radio" name="language_switcher_style" value="dropdown" <?php checked($language_switcher_style, 'dropdown'); ?> />
                                                <?php esc_html_e('Dropdown (default)', 'konderntang'); ?>
                                            </label>
                                            <label>
                                                <input type="radio" name="language_switcher_style" value="modal" <?php checked($language_switcher_style, 'modal'); ?> />
                                                <?php esc_html_e('Modal Popup', 'konderntang'); ?>
                                            </label>
                                            <p class="description">
                                                <?php esc_html_e('Dropdown เหมาะกับ 2-3 ภาษา, Modal เหมาะกับ 4+ ภาษา', 'konderntang'); ?>
                                            </p>

                                            <div style="margin-top: 15px;">
                                                <p>
                                                    <label class="konderntang-toggle">
                                                        <input type="checkbox" name="language_switcher_show_flags" value="1"
                                                            <?php checked($language_switcher_show_flags, true); ?> />
                                                        <span class="toggle-slider"></span>
                                                        <span
                                                            class="toggle-label"><?php esc_html_e('Show Flags', 'konderntang'); ?></span>
                                                    </label>
                                                </p>

                                                <?php if ($language_switcher_style === 'modal'): ?>
                                                    <p style="margin-top: 10px;">
                                                        <label class="konderntang-toggle">
                                                            <input type="checkbox" name="language_switcher_show_search" value="1"
                                                                <?php checked($language_switcher_show_search, true); ?> />
                                                            <span class="toggle-slider"></span>
                                                            <span
                                                                class="toggle-label"><?php esc_html_e('Show Search Box (Modal only)', 'konderntang'); ?></span>
                                                        </label>
                                                    </p>
                                                    <p style="margin-top: 10px;">
                                                        <label for="language_switcher_modal_title">
                                                            <?php esc_html_e('Modal Title:', 'konderntang'); ?>
                                                        </label>
                                                        <input type="text" name="language_switcher_modal_title"
                                                            id="language_switcher_modal_title"
                                                            value="<?php echo esc_attr($language_switcher_modal_title); ?>"
                                                            style="margin-left: 10px; width: 300px;" />
                                                    </p>
                                                <?php endif; ?>
                                            </div>

                                            <!-- Available Languages Info -->
                                            <div
                                                style="margin-top: 15px; padding: 10px 15px; background: #f0f6fc; border-left: 3px solid #2271b1; border-radius: 0 4px 4px 0;">
                                                <p style="margin: 0;">
                                                    <strong><?php esc_html_e('Available Languages:', 'konderntang'); ?></strong>
                                                    <?php
                                                    $lang_names = array_values($polylang_languages);
                                                    echo esc_html(implode(', ', $lang_names));
                                                    ?>
                                                    <span style="color: #666;">
                                                        (<?php printf(esc_html__('%d languages', 'konderntang'), count($polylang_languages)); ?>)
                                                    </span>
                                                </p>
                                                <p style="margin: 5px 0 0 0;">
                                                    <a href="<?php echo esc_url(admin_url('admin.php?page=mlang')); ?>">
                                                        <?php esc_html_e('Manage Languages in Polylang →', 'konderntang'); ?>
                                                    </a>
                                                </p>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>

                                <!-- Footer Settings -->
                                <tr>
                                    <th scope="row" colspan="2">
                                        <h3
                                            style="margin: 10px 0 10px; padding-bottom: 10px; border-bottom: 1px solid #e2e8f0;">
                                            <span class="dashicons dashicons-arrow-down-alt"></span>
                                            <?php esc_html_e('Footer Settings', 'konderntang'); ?>
                                        </h3>
                                    </th>
                                </tr>

                                <tr>
                                    <th scope="row">
                                        <label for="footer_layout">
                                            <span class="dashicons dashicons-layout"></span>
                                            <?php esc_html_e('Footer Layout', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <select name="footer_layout" id="footer_layout" class="regular-text">
                                            <option value="0" <?php selected($footer_layout, '0'); ?>>
                                                <?php esc_html_e('No Widgets', 'konderntang'); ?>
                                            </option>
                                            <option value="1" <?php selected($footer_layout, '1'); ?>>
                                                <?php esc_html_e('1 Column', 'konderntang'); ?>
                                            </option>
                                            <option value="2" <?php selected($footer_layout, '2'); ?>>
                                                <?php esc_html_e('2 Columns', 'konderntang'); ?>
                                            </option>
                                            <option value="3" <?php selected($footer_layout, '3'); ?>>
                                                <?php esc_html_e('3 Columns', 'konderntang'); ?>
                                            </option>
                                            <option value="4" <?php selected($footer_layout, '4'); ?>>
                                                <?php esc_html_e('4 Columns', 'konderntang'); ?>
                                            </option>
                                        </select>
                                        <p class="description">
                                            <?php esc_html_e('เลือกจำนวนคอลัมน์สำหรับ Footer Widget Areas', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="footer_copyright_text">
                                            <span class="dashicons dashicons-edit"></span>
                                            <?php esc_html_e('Copyright Text', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <textarea name="footer_copyright_text" id="footer_copyright_text" rows="3"
                                            class="large-text"
                                            placeholder="<?php esc_attr_e('&copy; %Y% %SITE_NAME% - เพื่อนเดินทางของคุณ', 'konderntang'); ?>"><?php echo esc_textarea($footer_copyright_text); ?></textarea>
                                        <p class="description">
                                            <?php esc_html_e('ใช้ %Y% สำหรับปี, %SITE_NAME% สำหรับชื่อเว็บไซต์', 'konderntang'); ?>
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
                                    <h2><?php esc_html_e('Homepage Settings', 'konderntang'); ?></h2>
                                    <p><?php esc_html_e('Configure homepage sections', 'konderntang'); ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="konderntang-section-content">
                            <table class="form-table">
                                <tr>
                                    <th scope="row">
                                        <span class="dashicons dashicons-slides"></span>
                                        <?php esc_html_e('Hero Slider', 'konderntang'); ?>
                                    </th>
                                    <td>
                                        <label class="konderntang-toggle">
                                            <input type="checkbox" name="hero_slider_enabled" value="1" <?php checked($hero_slider_enabled, true); ?> />
                                            <span class="toggle-slider"></span>
                                            <span
                                                class="toggle-label"><?php esc_html_e('Enable hero slider on homepage', 'konderntang'); ?></span>
                                        </label>
                                        <p class="description">
                                            <?php esc_html_e('แสดง Hero Slider ที่ด้านบนของหน้าแรก', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="hero_slider_source">
                                            <span class="dashicons dashicons-database"></span>
                                            <?php esc_html_e('Slider Source', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <select name="hero_slider_source" id="hero_slider_source" class="regular-text">
                                            <option value="banner" <?php selected($hero_slider_source, 'banner'); ?>>
                                                <?php esc_html_e('Custom Banners (Recommended)', 'konderntang'); ?></option>
                                            <option value="posts" <?php selected($hero_slider_source, 'posts'); ?>>
                                                <?php esc_html_e('Recent Posts', 'konderntang'); ?></option>
                                            <option value="mixed" <?php selected($hero_slider_source, 'mixed'); ?>>
                                                <?php esc_html_e('Mixed (Banners + Posts)', 'konderntang'); ?></option>
                                        </select>
                                        <p class="description">
                                            <?php esc_html_e('เลือกแหล่งข้อมูลที่จะแสดงในสไลด์', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="hero_slider_height">
                                            <span class="dashicons dashicons-arrow-up-alt2"></span>
                                            <?php esc_html_e('Slider Height (px)', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <input type="number" name="hero_slider_height" id="hero_slider_height"
                                            value="<?php echo esc_attr($hero_slider_height); ?>" min="300" max="1000"
                                            step="10" class="small-text" />
                                        <p class="description">
                                            <?php esc_html_e('กำหนดความสูงของสไลด์ (ค่าเริ่มต้น 500px)', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="hero_slider_posts">
                                            <span class="dashicons dashicons-images-alt2"></span>
                                            <?php esc_html_e('Number of Hero Slides', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <input type="number" name="hero_slider_posts" id="hero_slider_posts"
                                            value="<?php echo esc_attr($hero_slider_posts); ?>" min="1" max="10" step="1"
                                            class="small-text" />
                                        <p class="description">
                                            <?php esc_html_e('จำนวนสไลด์ที่จะแสดงใน Hero Slider (1-10)', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <span class="dashicons dashicons-star-filled"></span>
                                        <?php esc_html_e('Featured Section', 'konderntang'); ?>
                                    </th>
                                    <td>
                                        <label class="konderntang-toggle">
                                            <input type="checkbox" name="featured_section_enabled" value="1" <?php checked($featured_section_enabled, true); ?> />
                                            <span class="toggle-slider"></span>
                                            <span
                                                class="toggle-label"><?php esc_html_e('Enable featured section on homepage', 'konderntang'); ?></span>
                                        </label>
                                        <p class="description">
                                            <?php esc_html_e('แสดงส่วนบทความแนะนำบนหน้าแรก', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="featured_posts_count">
                                            <span class="dashicons dashicons-admin-post"></span>
                                            <?php esc_html_e('Number of Featured Posts', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <input type="number" name="featured_posts_count" id="featured_posts_count"
                                            value="<?php echo esc_attr($featured_posts_count); ?>" min="1" max="10" step="1"
                                            class="small-text" />
                                        <p class="description">
                                            <?php esc_html_e('จำนวนบทความแนะนำที่จะแสดง (1-10)', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="recent_posts_count">
                                            <span class="dashicons dashicons-clock"></span>
                                            <?php esc_html_e('Number of Recent Posts', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <input type="number" name="recent_posts_count" id="recent_posts_count"
                                            value="<?php echo esc_attr($recent_posts_count); ?>" min="1" max="20" step="1"
                                            class="small-text" />
                                        <p class="description">
                                            <?php esc_html_e('จำนวนบทความล่าสุดที่จะแสดง (1-20)', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>
                                <?php for ($i = 1; $i <= 3; $i++): ?>
                                    <tr class="bg-gray-50">
                                        <th scope="row" colspan="2" class="th-full">
                                            <h3 class="my-2 border-b pb-2">
                                                <?php printf(esc_html__('Homepage Section %d', 'konderntang'), $i); ?>
                                            </h3>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th scope="row">
                                            <label for="homepage_section_<?php echo $i; ?>_enabled">
                                                <?php printf(esc_html__('Enable Section %d', 'konderntang'), $i); ?>
                                            </label>
                                        </th>
                                        <td>
                                            <label class="konderntang-toggle">
                                                <input type="checkbox" name="homepage_section_<?php echo $i; ?>_enabled"
                                                    value="1" <?php checked($homepage_sections[$i]['enabled'], true); ?> />
                                                <span class="toggle-slider"></span>
                                                <span
                                                    class="toggle-label"><?php esc_html_e('Enable this section', 'konderntang'); ?></span>
                                            </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">
                                            <label for="homepage_section_<?php echo $i; ?>_category">
                                                <span class="dashicons dashicons-category"></span>
                                                <?php printf(esc_html__('Section %d Category', 'konderntang'), $i); ?>
                                            </label>
                                        </th>
                                        <td>
                                            <select name="homepage_section_<?php echo $i; ?>_category"
                                                id="homepage_section_<?php echo $i; ?>_category" class="regular-text">
                                                <option value="0" <?php selected($homepage_sections[$i]['category'], 0); ?>>
                                                    <?php esc_html_e('-- Select Category (Use Default Title) --', 'konderntang'); ?>
                                                </option>
                                                <?php foreach ($all_categories as $cat): ?>
                                                    <option value="<?php echo esc_attr($cat->term_id); ?>" <?php selected($homepage_sections[$i]['category'], $cat->term_id); ?>>
                                                        <?php echo esc_html($cat->name); ?> (<?php echo esc_html($cat->count); ?>)
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <p class="description">
                                                <?php printf(esc_html__('Select a category for Section %d.', 'konderntang'), $i); ?>
                                            </p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">
                                            <label for="homepage_section_<?php echo $i; ?>_count">
                                                <span class="dashicons dashicons-grid-view"></span>
                                                <?php printf(esc_html__('Number of Posts', 'konderntang'), $i); ?>
                                            </label>
                                        </th>
                                        <td>
                                            <input type="number" name="homepage_section_<?php echo $i; ?>_count"
                                                id="homepage_section_<?php echo $i; ?>_count"
                                                value="<?php echo esc_attr($homepage_sections[$i]['count']); ?>" min="1"
                                                max="20" step="1" class="small-text" />
                                            <p class="description">
                                                <?php esc_html_e('Number of posts to display in this section.', 'konderntang'); ?>
                                            </p>
                                        </td>
                                    </tr>
                                    <tr class="konderntang-separator">
                                        <td colspan="2">
                                            <hr>
                                        </td>
                                    </tr>
                                <?php endfor; ?>

                                <!-- CTA Banner Settings -->
                                <tr>
                                    <th scope="row" colspan="2">
                                        <h3 class="konderntang-section-title">
                                            <?php esc_html_e('CTA Banner Settings', 'konderntang'); ?>
                                        </h3>
                                    </th>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label
                                            for="cta_banner_enabled"><?php esc_html_e('Enable CTA Banner', 'konderntang'); ?></label>
                                    </th>
                                    <td>
                                        <label class="konderntang-toggle">
                                            <input type="checkbox" name="cta_banner_enabled" id="cta_banner_enabled"
                                                value="1" <?php checked($cta_banner_enabled, true); ?>>
                                            <span class="toggle-slider"></span>
                                        </label>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label
                                            for="cta_banner_title"><?php esc_html_e('Banner Title', 'konderntang'); ?></label>
                                    </th>
                                    <td>
                                        <input type="text" name="cta_banner_title" id="cta_banner_title"
                                            value="<?php echo esc_attr($cta_banner_title); ?>" class="regular-text">
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label
                                            for="cta_banner_subtitle"><?php esc_html_e('Banner Subtitle', 'konderntang'); ?></label>
                                    </th>
                                    <td>
                                        <input type="text" name="cta_banner_subtitle" id="cta_banner_subtitle"
                                            value="<?php echo esc_attr($cta_banner_subtitle); ?>" class="regular-text">
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label
                                            for="cta_banner_button_text"><?php esc_html_e('Button Text', 'konderntang'); ?></label>
                                    </th>
                                    <td>
                                        <input type="text" name="cta_banner_button_text" id="cta_banner_button_text"
                                            value="<?php echo esc_attr($cta_banner_button_text); ?>" class="regular-text">
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label
                                            for="cta_banner_button_url"><?php esc_html_e('Button URL', 'konderntang'); ?></label>
                                    </th>
                                    <td>
                                        <input type="url" name="cta_banner_button_url" id="cta_banner_button_url"
                                            value="<?php echo esc_url($cta_banner_button_url); ?>" class="regular-text">
                                    </td>
                                </tr>
                                <tr class="konderntang-separator">
                                    <td colspan="2">
                                        <hr>
                                    </td>
                                </tr>

                                <!-- Layout Order Settings -->
                                <tr>
                                    <th scope="row" colspan="2">
                                        <h3 class="konderntang-section-title">
                                            <?php esc_html_e('Homepage Layout Order', 'konderntang'); ?>
                                        </h3>
                                    </th>
                                </tr>
                                <?php
                                $layout_options = array(
                                    'none' => esc_html__('-- None --', 'konderntang'),
                                    'section_1' => esc_html__('Section 1', 'konderntang'),
                                    'section_2' => esc_html__('Section 2', 'konderntang'),
                                    'section_3' => esc_html__('Section 3', 'konderntang'),
                                    'cta_banner' => esc_html__('CTA Banner', 'konderntang'),
                                );
                                for ($i = 1; $i <= 4; $i++):
                                    $current_val = ${"homepage_layout_slot_{$i}"};
                                    ?>
                                    <tr>
                                        <th scope="row">
                                            <label for="homepage_layout_slot_<?php echo $i; ?>">
                                                <span class="dashicons dashicons-sort"></span>
                                                <?php printf(esc_html__('Slot %d', 'konderntang'), $i); ?>
                                            </label>
                                        </th>
                                        <td>
                                            <select name="homepage_layout_slot_<?php echo $i; ?>"
                                                id="homepage_layout_slot_<?php echo $i; ?>" class="regular-text">
                                                <?php foreach ($layout_options as $key => $label): ?>
                                                    <option value="<?php echo esc_attr($key); ?>" <?php selected($current_val, $key); ?>>
                                                        <?php echo esc_html($label); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                    </tr>
                                <?php endfor; ?>

                                <!-- Legacy Newsletter (Optional) -->
                                <tr class="hidden">
                                    <th scope="row">
                                        <span class="dashicons dashicons-email-alt"></span>
                                        <?php esc_html_e('Newsletter', 'konderntang'); ?>
                                    </th>
                                    <td>
                                        <label class="konderntang-toggle">
                                            <input type="checkbox" name="newsletter_enabled" value="1" <?php checked($newsletter_enabled, true); ?> />
                                            <span class="toggle-slider"></span>
                                            <span
                                                class="toggle-label"><?php esc_html_e('Enable newsletter section on homepage', 'konderntang'); ?></span>
                                        </label>
                                        <p class="description">
                                            <?php esc_html_e('แสดงส่วนสมัครรับจดหมายข่าวบนหน้าแรก', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="trending_tags_count">
                                            <span class="dashicons dashicons-tag"></span>
                                            <?php esc_html_e('Number of Trending Tags', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <input type="number" name="trending_tags_count" id="trending_tags_count"
                                            value="<?php echo esc_attr($trending_tags_count); ?>" min="1" max="30" step="1"
                                            class="small-text" />
                                        <p class="description">
                                            <?php esc_html_e('จำนวนแท็กยอดนิยมที่จะแสดง (1-30)', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <span class="dashicons dashicons-visibility"></span>
                                        <?php esc_html_e('Recently Viewed', 'konderntang'); ?>
                                    </th>
                                    <td>
                                        <label class="konderntang-toggle">
                                            <input type="checkbox" name="recently_viewed_enabled" value="1" <?php checked($recently_viewed_enabled, true); ?> />
                                            <span class="toggle-slider"></span>
                                            <span
                                                class="toggle-label"><?php esc_html_e('Enable recently viewed posts section', 'konderntang'); ?></span>
                                        </label>
                                        <p class="description">
                                            <?php esc_html_e('แสดงส่วนบทความที่ดูล่าสุด', 'konderntang'); ?>
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
                                    <h2><?php esc_html_e('Layout Settings', 'konderntang'); ?></h2>
                                    <p><?php esc_html_e('Page layout and sidebar configuration', 'konderntang'); ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="konderntang-section-content">
                            <table class="form-table">
                                <tr>
                                    <th scope="row">
                                        <label for="layout_container_width">
                                            <span class="dashicons dashicons-editor-expand"></span>
                                            <?php esc_html_e('Container Width (px)', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <input type="number" name="layout_container_width" id="layout_container_width"
                                            value="<?php echo esc_attr($layout_container_width); ?>" min="960" max="1920"
                                            step="10" class="small-text" />
                                        <p class="description">
                                            <?php esc_html_e('ความกว้างสูงสุดของเนื้อหาเว็บไซต์ (960-1920px)', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="layout_archive_sidebar">
                                            <span class="dashicons dashicons-align-wide"></span>
                                            <?php esc_html_e('Archive Sidebar Position', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <select name="layout_archive_sidebar" id="layout_archive_sidebar"
                                            class="regular-text">
                                            <option value="left" <?php selected($layout_archive_sidebar, 'left'); ?>>
                                                <?php esc_html_e('Left', 'konderntang'); ?>
                                            </option>
                                            <option value="right" <?php selected($layout_archive_sidebar, 'right'); ?>>
                                                <?php esc_html_e('Right', 'konderntang'); ?>
                                            </option>
                                            <option value="none" <?php selected($layout_archive_sidebar, 'none'); ?>>
                                                <?php esc_html_e('None', 'konderntang'); ?>
                                            </option>
                                        </select>
                                        <p class="description">
                                            <?php esc_html_e('ตำแหน่ง Sidebar สำหรับหน้า Archive (หมวดหมู่, แท็ก)', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="layout_single_sidebar">
                                            <span class="dashicons dashicons-align-wide"></span>
                                            <?php esc_html_e('Single Post Sidebar Position', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <select name="layout_single_sidebar" id="layout_single_sidebar"
                                            class="regular-text">
                                            <option value="left" <?php selected($layout_single_sidebar, 'left'); ?>>
                                                <?php esc_html_e('Left', 'konderntang'); ?>
                                            </option>
                                            <option value="right" <?php selected($layout_single_sidebar, 'right'); ?>>
                                                <?php esc_html_e('Right', 'konderntang'); ?>
                                            </option>
                                            <option value="none" <?php selected($layout_single_sidebar, 'none'); ?>>
                                                <?php esc_html_e('None', 'konderntang'); ?>
                                            </option>
                                        </select>
                                        <p class="description">
                                            <?php esc_html_e('ตำแหน่ง Sidebar สำหรับหน้าบทความเดี่ยว', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="layout_posts_per_page">
                                            <span class="dashicons dashicons-media-text"></span>
                                            <?php esc_html_e('Posts Per Page', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <input type="number" name="layout_posts_per_page" id="layout_posts_per_page"
                                            value="<?php echo esc_attr($layout_posts_per_page); ?>" min="1" max="50"
                                            step="1" class="small-text" />
                                        <p class="description">
                                            <?php esc_html_e('จำนวนบทความที่จะแสดงต่อหน้า (1-50)', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>

                                <!-- Breadcrumbs Settings -->
                                <tr>
                                    <th scope="row" colspan="2">
                                        <h3
                                            style="margin: 20px 0 10px; padding-bottom: 10px; border-bottom: 1px solid #e2e8f0;">
                                            <span class="dashicons dashicons-admin-links"></span>
                                            <?php esc_html_e('Breadcrumbs Settings', 'konderntang'); ?>
                                        </h3>
                                    </th>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="breadcrumbs_enabled">
                                            <span class="dashicons dashicons-visibility"></span>
                                            <?php esc_html_e('Enable Breadcrumbs', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <label>
                                            <input type="checkbox" name="breadcrumbs_enabled" id="breadcrumbs_enabled"
                                                value="1" <?php checked($breadcrumbs_enabled, true); ?> />
                                            <?php esc_html_e('แสดง Breadcrumbs ในเว็บไซต์', 'konderntang'); ?>
                                        </label>
                                        <p class="description">
                                            <?php esc_html_e('เปิด/ปิดการแสดง Breadcrumbs ทั้งหมด', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="breadcrumbs_home_text">
                                            <span class="dashicons dashicons-admin-home"></span>
                                            <?php esc_html_e('Home Text', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <input type="text" name="breadcrumbs_home_text" id="breadcrumbs_home_text"
                                            value="<?php echo esc_attr($breadcrumbs_home_text); ?>" class="regular-text" />
                                        <p class="description">
                                            <?php esc_html_e('ข้อความที่แสดงสำหรับลิงก์หน้าแรก', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="breadcrumbs_separator">
                                            <span class="dashicons dashicons-arrow-right-alt"></span>
                                            <?php esc_html_e('Separator', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <select name="breadcrumbs_separator" id="breadcrumbs_separator"
                                            class="regular-text">
                                            <option value="caret-right" <?php selected($breadcrumbs_separator, 'caret-right'); ?>><?php esc_html_e('Caret Right (>)', 'konderntang'); ?>
                                            </option>
                                            <option value="slash" <?php selected($breadcrumbs_separator, 'slash'); ?>>
                                                <?php esc_html_e('Slash (/)', 'konderntang'); ?>
                                            </option>
                                            <option value="arrow-right" <?php selected($breadcrumbs_separator, 'arrow-right'); ?>><?php esc_html_e('Arrow Right (→)', 'konderntang'); ?>
                                            </option>
                                            <option value="chevron-right" <?php selected($breadcrumbs_separator, 'chevron-right'); ?>>
                                                <?php esc_html_e('Chevron Right (»)', 'konderntang'); ?>
                                            </option>
                                        </select>
                                        <p class="description">
                                            <?php esc_html_e('เลือกรูปแบบตัวคั่นระหว่าง Breadcrumb items', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label>
                                            <span class="dashicons dashicons-admin-page"></span>
                                            <?php esc_html_e('Show On', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <fieldset>
                                            <label style="display: block; margin-bottom: 8px;">
                                                <input type="checkbox" name="breadcrumbs_show_single" value="1" <?php checked($breadcrumbs_show_single, true); ?> />
                                                <?php esc_html_e('Single Posts', 'konderntang'); ?>
                                            </label>
                                            <label style="display: block; margin-bottom: 8px;">
                                                <input type="checkbox" name="breadcrumbs_show_archive" value="1" <?php checked($breadcrumbs_show_archive, true); ?> />
                                                <?php esc_html_e('Archive Pages', 'konderntang'); ?>
                                            </label>
                                            <label style="display: block; margin-bottom: 8px;">
                                                <input type="checkbox" name="breadcrumbs_show_page" value="1" <?php checked($breadcrumbs_show_page, true); ?> />
                                                <?php esc_html_e('Pages', 'konderntang'); ?>
                                            </label>
                                            <label style="display: block; margin-bottom: 8px;">
                                                <input type="checkbox" name="breadcrumbs_show_search" value="1" <?php checked($breadcrumbs_show_search, true); ?> />
                                                <?php esc_html_e('Search Results', 'konderntang'); ?>
                                            </label>
                                            <label style="display: block; margin-bottom: 8px;">
                                                <input type="checkbox" name="breadcrumbs_show_404" value="1" <?php checked($breadcrumbs_show_404, true); ?> />
                                                <?php esc_html_e('404 Error Page', 'konderntang'); ?>
                                            </label>
                                        </fieldset>
                                        <p class="description">
                                            <?php esc_html_e('เลือกประเภทหน้าที่ต้องการแสดง Breadcrumbs', 'konderntang'); ?>
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
                                    <h2><?php esc_html_e('Color Settings', 'konderntang'); ?></h2>
                                    <p><?php esc_html_e('Customize theme colors', 'konderntang'); ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="konderntang-section-content">
                            <table class="form-table">
                                <tr>
                                    <th scope="row">
                                        <label for="color_primary">
                                            <span class="dashicons dashicons-admin-appearance"></span>
                                            <?php esc_html_e('Primary Color', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <div class="konderntang-color-picker-group">
                                            <input type="color" name="color_primary" id="color_primary"
                                                value="<?php echo esc_attr($color_primary); ?>" />
                                            <input type="text" value="<?php echo esc_attr($color_primary); ?>" readonly
                                                class="konderntang-color-value" />
                                            <span
                                                class="description"><?php esc_html_e('สีหลักของธีม (ปุ่ม, ลิงก์, ไฮไลท์)', 'konderntang'); ?></span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="color_secondary">
                                            <span class="dashicons dashicons-admin-appearance"></span>
                                            <?php esc_html_e('Secondary Color', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <div class="konderntang-color-picker-group">
                                            <input type="color" name="color_secondary" id="color_secondary"
                                                value="<?php echo esc_attr($color_secondary); ?>" />
                                            <input type="text" value="<?php echo esc_attr($color_secondary); ?>" readonly
                                                class="konderntang-color-value" />
                                            <span
                                                class="description"><?php esc_html_e('สีรองของธีม (ปุ่มพิเศษ, badge)', 'konderntang'); ?></span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="color_text">
                                            <span class="dashicons dashicons-editor-textcolor"></span>
                                            <?php esc_html_e('Text Color', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <div class="konderntang-color-picker-group">
                                            <input type="color" name="color_text" id="color_text"
                                                value="<?php echo esc_attr($color_text); ?>" />
                                            <input type="text" value="<?php echo esc_attr($color_text); ?>" readonly
                                                class="konderntang-color-value" />
                                            <span
                                                class="description"><?php esc_html_e('สีข้อความหลัก', 'konderntang'); ?></span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="color_background">
                                            <span class="dashicons dashicons-admin-appearance"></span>
                                            <?php esc_html_e('Background Color', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <div class="konderntang-color-picker-group">
                                            <input type="color" name="color_background" id="color_background"
                                                value="<?php echo esc_attr($color_background); ?>" />
                                            <input type="text" value="<?php echo esc_attr($color_background); ?>" readonly
                                                class="konderntang-color-value" />
                                            <span
                                                class="description"><?php esc_html_e('สีพื้นหลังหลัก', 'konderntang'); ?></span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="color_link">
                                            <span class="dashicons dashicons-admin-links"></span>
                                            <?php esc_html_e('Link Color', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <div class="konderntang-color-picker-group">
                                            <input type="color" name="color_link" id="color_link"
                                                value="<?php echo esc_attr($color_link); ?>" />
                                            <input type="text" value="<?php echo esc_attr($color_link); ?>" readonly
                                                class="konderntang-color-value" />
                                            <span class="description"><?php esc_html_e('สีลิงก์', 'konderntang'); ?></span>
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
                                    <h2><?php esc_html_e('Typography Settings', 'konderntang'); ?></h2>
                                    <p><?php esc_html_e('Font and text styling', 'konderntang'); ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="konderntang-section-content">

                            <!-- Font Families Sub-section -->
                            <h3 style="border-bottom: 1px solid #ddd; padding-bottom: 10px; margin-bottom: 20px;">
                                <span class="dashicons dashicons-editor-spellcheck"></span>
                                <?php esc_html_e('Font Families', 'konderntang'); ?>
                            </h3>
                            <table class="form-table">
                                <tr>
                                    <th scope="row">
                                        <label for="typography_body_font">
                                            <span class="dashicons dashicons-editor-bold"></span>
                                            <?php esc_html_e('Body Font', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <select name="typography_body_font" id="typography_body_font" class="regular-text">
                                            <option value="system-ui" <?php selected($typography_body_font, 'system-ui'); ?>><?php esc_html_e('System Default', 'konderntang'); ?></option>
                                            <option value="Sarabun" <?php selected($typography_body_font, 'Sarabun'); ?>>
                                                Sarabun (Thai)</option>
                                            <option value="Kanit" <?php selected($typography_body_font, 'Kanit'); ?>>Kanit
                                                (Thai)</option>
                                            <option value="Prompt" <?php selected($typography_body_font, 'Prompt'); ?>>
                                                Prompt (Thai)</option>
                                            <option value="Noto Sans Thai" <?php selected($typography_body_font, 'Noto Sans Thai'); ?>>Noto Sans Thai</option>
                                            <option value="Arial" <?php selected($typography_body_font, 'Arial'); ?>>Arial
                                            </option>
                                            <option value="Helvetica" <?php selected($typography_body_font, 'Helvetica'); ?>>Helvetica</option>
                                            <option value="Georgia" <?php selected($typography_body_font, 'Georgia'); ?>>
                                                Georgia</option>
                                            <option value="Times New Roman" <?php selected($typography_body_font, 'Times New Roman'); ?>>Times New Roman</option>
                                        </select>
                                        <p class="description">
                                            <?php esc_html_e('เลือกฟอนต์สำหรับเนื้อหาหลัก', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="typography_heading_font">
                                            <span class="dashicons dashicons-heading"></span>
                                            <?php esc_html_e('Heading Font', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <select name="typography_heading_font" id="typography_heading_font"
                                            class="regular-text">
                                            <option value="Kanit" <?php selected($typography_heading_font, 'Kanit'); ?>>
                                                Kanit (Thai)</option>
                                            <option value="Sarabun" <?php selected($typography_heading_font, 'Sarabun'); ?>>
                                                Sarabun (Thai)</option>
                                            <option value="Prompt" <?php selected($typography_heading_font, 'Prompt'); ?>>
                                                Prompt (Thai)</option>
                                            <option value="Noto Sans Thai" <?php selected($typography_heading_font, 'Noto Sans Thai'); ?>>Noto Sans Thai</option>
                                            <option value="system-ui" <?php selected($typography_heading_font, 'system-ui'); ?>><?php esc_html_e('System Default', 'konderntang'); ?></option>
                                            <option value="Arial" <?php selected($typography_heading_font, 'Arial'); ?>>
                                                Arial</option>
                                            <option value="Helvetica" <?php selected($typography_heading_font, 'Helvetica'); ?>>Helvetica</option>
                                            <option value="Georgia" <?php selected($typography_heading_font, 'Georgia'); ?>>
                                                Georgia</option>
                                            <option value="Times New Roman" <?php selected($typography_heading_font, 'Times New Roman'); ?>>Times New Roman</option>
                                        </select>
                                        <p class="description"><?php esc_html_e('เลือกฟอนต์สำหรับหัวข้อ', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="typography_menu_font">
                                            <span class="dashicons dashicons-menu"></span>
                                            <?php esc_html_e('Menu Font', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <select name="typography_menu_font" id="typography_menu_font" class="regular-text">
                                            <option value="system-ui" <?php selected($typography_menu_font, 'system-ui'); ?>><?php esc_html_e('System Default', 'konderntang'); ?></option>
                                            <option value="Sarabun" <?php selected($typography_menu_font, 'Sarabun'); ?>>
                                                Sarabun (Thai)</option>
                                            <option value="Kanit" <?php selected($typography_menu_font, 'Kanit'); ?>>Kanit
                                                (Thai)</option>
                                            <option value="Prompt" <?php selected($typography_menu_font, 'Prompt'); ?>>
                                                Prompt (Thai)</option>
                                            <option value="Noto Sans Thai" <?php selected($typography_menu_font, 'Noto Sans Thai'); ?>>Noto Sans Thai</option>
                                            <option value="Arial" <?php selected($typography_menu_font, 'Arial'); ?>>Arial
                                            </option>
                                            <option value="Helvetica" <?php selected($typography_menu_font, 'Helvetica'); ?>>Helvetica</option>
                                        </select>
                                        <p class="description"><?php esc_html_e('เลือกฟอนต์สำหรับเมนู', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="typography_button_font">
                                            <span class="dashicons dashicons-button"></span>
                                            <?php esc_html_e('Button Font', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <select name="typography_button_font" id="typography_button_font"
                                            class="regular-text">
                                            <option value="system-ui" <?php selected($typography_button_font, 'system-ui'); ?>><?php esc_html_e('System Default', 'konderntang'); ?></option>
                                            <option value="inherit" <?php selected($typography_button_font, 'inherit'); ?>>
                                                <?php esc_html_e('Inherit from Body', 'konderntang'); ?>
                                            </option>
                                            <option value="Sarabun" <?php selected($typography_button_font, 'Sarabun'); ?>>
                                                Sarabun (Thai)</option>
                                            <option value="Kanit" <?php selected($typography_button_font, 'Kanit'); ?>>Kanit
                                                (Thai)</option>
                                            <option value="Prompt" <?php selected($typography_button_font, 'Prompt'); ?>>
                                                Prompt (Thai)</option>
                                        </select>
                                        <p class="description"><?php esc_html_e('เลือกฟอนต์สำหรับปุ่ม', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Font Sizes Sub-section -->
                            <h3 style="border-bottom: 1px solid #ddd; padding-bottom: 10px; margin: 30px 0 20px 0;">
                                <span class="dashicons dashicons-editor-expand"></span>
                                <?php esc_html_e('Font Sizes', 'konderntang'); ?>
                            </h3>
                            <table class="form-table">
                                <tr>
                                    <th scope="row">
                                        <label for="typography_body_size">
                                            <span class="dashicons dashicons-editor-paragraph"></span>
                                            <?php esc_html_e('Body Font Size (px)', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <input type="number" name="typography_body_size" id="typography_body_size"
                                            value="<?php echo esc_attr($typography_body_size); ?>" min="14" max="20"
                                            step="1" class="small-text" />
                                        <p class="description">
                                            <?php esc_html_e('ขนาดฟอนต์สำหรับข้อความ (14-20px)', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="typography_menu_size">
                                            <span class="dashicons dashicons-menu"></span>
                                            <?php esc_html_e('Menu Font Size (px)', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <input type="number" name="typography_menu_size" id="typography_menu_size"
                                            value="<?php echo esc_attr($typography_menu_size); ?>" min="12" max="24"
                                            step="1" class="small-text" />
                                        <p class="description">
                                            <?php esc_html_e('ขนาดฟอนต์สำหรับเมนู (12-24px)', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="typography_h1_size">
                                            <span class="dashicons dashicons-heading"></span>
                                            <?php esc_html_e('H1 Font Size (px)', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <input type="number" name="typography_h1_size" id="typography_h1_size"
                                            value="<?php echo esc_attr($typography_h1_size); ?>" min="24" max="48" step="2"
                                            class="small-text" />
                                        <p class="description">
                                            <?php esc_html_e('ขนาดฟอนต์สำหรับ H1 (24-48px)', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="typography_h2_size">
                                            <span class="dashicons dashicons-heading"></span>
                                            <?php esc_html_e('H2 Font Size (px)', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <input type="number" name="typography_h2_size" id="typography_h2_size"
                                            value="<?php echo esc_attr($typography_h2_size); ?>" min="20" max="36" step="1"
                                            class="small-text" />
                                        <p class="description">
                                            <?php esc_html_e('ขนาดฟอนต์สำหรับ H2 (20-36px)', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="typography_h3_size">
                                            <span class="dashicons dashicons-heading"></span>
                                            <?php esc_html_e('H3 Font Size (px)', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <input type="number" name="typography_h3_size" id="typography_h3_size"
                                            value="<?php echo esc_attr($typography_h3_size); ?>" min="18" max="30" step="1"
                                            class="small-text" />
                                        <p class="description">
                                            <?php esc_html_e('ขนาดฟอนต์สำหรับ H3 (18-30px)', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="typography_h4_size">
                                            <span class="dashicons dashicons-heading"></span>
                                            <?php esc_html_e('H4 Font Size (px)', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <input type="number" name="typography_h4_size" id="typography_h4_size"
                                            value="<?php echo esc_attr($typography_h4_size); ?>" min="16" max="24" step="1"
                                            class="small-text" />
                                        <p class="description">
                                            <?php esc_html_e('ขนาดฟอนต์สำหรับ H4 (16-24px)', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="typography_h5_size">
                                            <span class="dashicons dashicons-heading"></span>
                                            <?php esc_html_e('H5 Font Size (px)', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <input type="number" name="typography_h5_size" id="typography_h5_size"
                                            value="<?php echo esc_attr($typography_h5_size); ?>" min="14" max="20" step="1"
                                            class="small-text" />
                                        <p class="description">
                                            <?php esc_html_e('ขนาดฟอนต์สำหรับ H5 (14-20px)', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="typography_h6_size">
                                            <span class="dashicons dashicons-heading"></span>
                                            <?php esc_html_e('H6 Font Size (px)', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <input type="number" name="typography_h6_size" id="typography_h6_size"
                                            value="<?php echo esc_attr($typography_h6_size); ?>" min="12" max="18" step="1"
                                            class="small-text" />
                                        <p class="description">
                                            <?php esc_html_e('ขนาดฟอนต์สำหรับ H6 (12-18px)', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="typography_button_size">
                                            <span class="dashicons dashicons-button"></span>
                                            <?php esc_html_e('Button Font Size (px)', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <input type="number" name="typography_button_size" id="typography_button_size"
                                            value="<?php echo esc_attr($typography_button_size); ?>" min="12" max="20"
                                            step="1" class="small-text" />
                                        <p class="description">
                                            <?php esc_html_e('ขนาดฟอนต์สำหรับปุ่ม (12-20px)', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Line Heights Sub-section -->
                            <h3 style="border-bottom: 1px solid #ddd; padding-bottom: 10px; margin: 30px 0 20px 0;">
                                <span class="dashicons dashicons-editor-ul"></span>
                                <?php esc_html_e('Line Heights', 'konderntang'); ?>
                            </h3>
                            <table class="form-table">
                                <tr>
                                    <th scope="row">
                                        <label for="typography_body_line_height">
                                            <span class="dashicons dashicons-editor-paragraph"></span>
                                            <?php esc_html_e('Body Line Height', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <input type="number" name="typography_body_line_height"
                                            id="typography_body_line_height"
                                            value="<?php echo esc_attr($typography_body_line_height); ?>" min="1.2"
                                            max="2.0" step="0.05" class="small-text" />
                                        <p class="description">
                                            <?php esc_html_e('ความสูงของบรรทัดสำหรับเนื้อหา (1.2-2.0)', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="typography_heading_line_height">
                                            <span class="dashicons dashicons-heading"></span>
                                            <?php esc_html_e('Heading Line Height', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <input type="number" name="typography_heading_line_height"
                                            id="typography_heading_line_height"
                                            value="<?php echo esc_attr($typography_heading_line_height); ?>" min="1.0"
                                            max="1.8" step="0.05" class="small-text" />
                                        <p class="description">
                                            <?php esc_html_e('ความสูงของบรรทัดสำหรับหัวข้อ (1.0-1.8)', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Font Weights Sub-section -->
                            <h3 style="border-bottom: 1px solid #ddd; padding-bottom: 10px; margin: 30px 0 20px 0;">
                                <span class="dashicons dashicons-editor-bold"></span>
                                <?php esc_html_e('Font Weights', 'konderntang'); ?>
                            </h3>
                            <table class="form-table">
                                <tr>
                                    <th scope="row">
                                        <label for="typography_body_weight">
                                            <span class="dashicons dashicons-editor-paragraph"></span>
                                            <?php esc_html_e('Body Font Weight', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <select name="typography_body_weight" id="typography_body_weight"
                                            class="regular-text">
                                            <option value="300" <?php selected($typography_body_weight, 300); ?>>
                                                <?php esc_html_e('Light (300)', 'konderntang'); ?>
                                            </option>
                                            <option value="400" <?php selected($typography_body_weight, 400); ?>>
                                                <?php esc_html_e('Normal (400)', 'konderntang'); ?>
                                            </option>
                                            <option value="500" <?php selected($typography_body_weight, 500); ?>>
                                                <?php esc_html_e('Medium (500)', 'konderntang'); ?>
                                            </option>
                                            <option value="600" <?php selected($typography_body_weight, 600); ?>>
                                                <?php esc_html_e('Semi Bold (600)', 'konderntang'); ?>
                                            </option>
                                            <option value="700" <?php selected($typography_body_weight, 700); ?>>
                                                <?php esc_html_e('Bold (700)', 'konderntang'); ?>
                                            </option>
                                        </select>
                                        <p class="description">
                                            <?php esc_html_e('น้ำหนักฟอนต์สำหรับเนื้อหา', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="typography_heading_weight">
                                            <span class="dashicons dashicons-heading"></span>
                                            <?php esc_html_e('Heading Font Weight', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <select name="typography_heading_weight" id="typography_heading_weight"
                                            class="regular-text">
                                            <option value="400" <?php selected($typography_heading_weight, 400); ?>>
                                                <?php esc_html_e('Normal (400)', 'konderntang'); ?>
                                            </option>
                                            <option value="500" <?php selected($typography_heading_weight, 500); ?>>
                                                <?php esc_html_e('Medium (500)', 'konderntang'); ?>
                                            </option>
                                            <option value="600" <?php selected($typography_heading_weight, 600); ?>>
                                                <?php esc_html_e('Semi Bold (600)', 'konderntang'); ?>
                                            </option>
                                            <option value="700" <?php selected($typography_heading_weight, 700); ?>>
                                                <?php esc_html_e('Bold (700)', 'konderntang'); ?>
                                            </option>
                                            <option value="800" <?php selected($typography_heading_weight, 800); ?>>
                                                <?php esc_html_e('Extra Bold (800)', 'konderntang'); ?>
                                            </option>
                                            <option value="900" <?php selected($typography_heading_weight, 900); ?>>
                                                <?php esc_html_e('Black (900)', 'konderntang'); ?>
                                            </option>
                                        </select>
                                        <p class="description">
                                            <?php esc_html_e('น้ำหนักฟอนต์สำหรับหัวข้อ', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="typography_menu_weight">
                                            <span class="dashicons dashicons-menu"></span>
                                            <?php esc_html_e('Menu Font Weight', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <select name="typography_menu_weight" id="typography_menu_weight"
                                            class="regular-text">
                                            <option value="400" <?php selected($typography_menu_weight, 400); ?>>
                                                <?php esc_html_e('Normal (400)', 'konderntang'); ?>
                                            </option>
                                            <option value="500" <?php selected($typography_menu_weight, 500); ?>>
                                                <?php esc_html_e('Medium (500)', 'konderntang'); ?>
                                            </option>
                                            <option value="600" <?php selected($typography_menu_weight, 600); ?>>
                                                <?php esc_html_e('Semi Bold (600)', 'konderntang'); ?>
                                            </option>
                                            <option value="700" <?php selected($typography_menu_weight, 700); ?>>
                                                <?php esc_html_e('Bold (700)', 'konderntang'); ?>
                                            </option>
                                        </select>
                                        <p class="description"><?php esc_html_e('น้ำหนักฟอนต์สำหรับเมนู', 'konderntang'); ?>
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
                                    <h2><?php esc_html_e('Table of Contents Settings', 'konderntang'); ?></h2>
                                    <p><?php esc_html_e('Configure TOC display options', 'konderntang'); ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="konderntang-section-content">
                            <table class="form-table">
                                <tr>
                                    <th scope="row">
                                        <span class="dashicons dashicons-list-view"></span>
                                        <?php esc_html_e('Table of Contents', 'konderntang'); ?>
                                    </th>
                                    <td>
                                        <label class="konderntang-toggle">
                                            <input type="checkbox" name="toc_enabled" value="1" <?php checked($toc_enabled, true); ?> />
                                            <span class="toggle-slider"></span>
                                            <span
                                                class="toggle-label"><?php esc_html_e('Enable Table of Contents globally', 'konderntang'); ?></span>
                                        </label>
                                        <p class="description">
                                            <?php esc_html_e('Note: Individual posts can override this setting via the TOC meta box.', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="toc_min_headings">
                                            <span class="dashicons dashicons-editor-ol"></span>
                                            <?php esc_html_e('Minimum Headings', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <input type="number" name="toc_min_headings" id="toc_min_headings"
                                            value="<?php echo esc_attr($toc_min_headings); ?>" min="1" max="10" step="1"
                                            class="small-text" />
                                        <p class="description">
                                            <?php esc_html_e('จำนวนหัวข้อขั้นต่ำที่ต้องมีเพื่อแสดงสารบัญ (1-10)', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <span class="dashicons dashicons-editor-ul"></span>
                                        <?php esc_html_e('Heading Levels', 'konderntang'); ?>
                                    </th>
                                    <td>
                                        <div class="konderntang-checkbox-group">
                                            <label class="konderntang-checkbox-label">
                                                <input type="checkbox" name="toc_heading_levels[]" value="h2" <?php checked(in_array('h2', $toc_heading_levels, true)); ?> />
                                                <span><?php esc_html_e('H2', 'konderntang'); ?></span>
                                            </label>
                                            <label class="konderntang-checkbox-label">
                                                <input type="checkbox" name="toc_heading_levels[]" value="h3" <?php checked(in_array('h3', $toc_heading_levels, true)); ?> />
                                                <span><?php esc_html_e('H3', 'konderntang'); ?></span>
                                            </label>
                                            <label class="konderntang-checkbox-label">
                                                <input type="checkbox" name="toc_heading_levels[]" value="h4" <?php checked(in_array('h4', $toc_heading_levels, true)); ?> />
                                                <span><?php esc_html_e('H4', 'konderntang'); ?></span>
                                            </label>
                                        </div>
                                        <p class="description">
                                            <?php esc_html_e('เลือกหัวข้อที่จะรวมในสารบัญ', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="toc_title">
                                            <span class="dashicons dashicons-edit"></span>
                                            <?php esc_html_e('TOC Title', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <input type="text" name="toc_title" id="toc_title"
                                            value="<?php echo esc_attr($toc_title); ?>" class="regular-text"
                                            placeholder="<?php esc_attr_e('สารบัญ', 'konderntang'); ?>" />
                                        <p class="description">
                                            <?php esc_html_e('ข้อความหัวข้อสำหรับสารบัญ', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <span class="dashicons dashicons-admin-settings"></span>
                                        <?php esc_html_e('TOC Options', 'konderntang'); ?>
                                    </th>
                                    <td>
                                        <div class="konderntang-checkbox-group">
                                            <label class="konderntang-checkbox-label">
                                                <input type="checkbox" name="toc_collapsible" value="1" <?php checked($toc_collapsible, true); ?> />
                                                <span><?php esc_html_e('Collapsible TOC', 'konderntang'); ?></span>
                                            </label>
                                            <label class="konderntang-checkbox-label">
                                                <input type="checkbox" name="toc_smooth_scroll" value="1" <?php checked($toc_smooth_scroll, true); ?> />
                                                <span><?php esc_html_e('Smooth scroll navigation', 'konderntang'); ?></span>
                                            </label>
                                            <label class="konderntang-checkbox-label">
                                                <input type="checkbox" name="toc_scroll_spy" value="1" <?php checked($toc_scroll_spy, true); ?> />
                                                <span><?php esc_html_e('Scroll spy (highlight current section)', 'konderntang'); ?></span>
                                            </label>
                                        </div>
                                        <p class="description">
                                            <?php esc_html_e('เปิดใช้งานตัวเลือกเพิ่มเติมสำหรับสารบัญ', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Social Media Settings -->
                    <div class="konderntang-settings-section" id="section-social" data-section="social">
                        <div class="konderntang-section-header">
                            <div class="section-header-content">
                                <span class="dashicons dashicons-share"></span>
                                <div>
                                    <h2><?php esc_html_e('Social Media Settings', 'konderntang'); ?></h2>
                                    <p><?php esc_html_e('Configure social media profiles and display options', 'konderntang'); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="konderntang-section-content">
                            <div class="konderntang-info-box">
                                <span class="dashicons dashicons-info"></span>
                                <p><?php esc_html_e('กรอก URL โปรไฟล์โซเชียลมีเดียของคุณ ข้อมูลเหล่านี้จะถูกนำไปใช้แสดงผลใน Header, Footer และ Widget โดยอัตโนมัติ', 'konderntang'); ?>
                                </p>
                            </div>
                            <table class="form-table">
                                <!-- Social Profiles Section -->
                                <tr>
                                    <th colspan="2">
                                        <h3 style="margin: 0; padding: 15px 0 5px; border-bottom: 1px solid #e2e8f0;">
                                            <span class="dashicons dashicons-admin-links" style="margin-right: 8px;"></span>
                                            <?php esc_html_e('Social Media Profiles', 'konderntang'); ?>
                                        </h3>
                                    </th>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="social_facebook">
                                            <span class="dashicons dashicons-facebook" style="color: #1877f2;"></span>
                                            <?php esc_html_e('Facebook', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <input type="url" id="social_facebook" name="social_facebook"
                                            value="<?php echo esc_attr($social_facebook); ?>" class="regular-text"
                                            placeholder="https://facebook.com/yourpage" />
                                        <p class="description">
                                            <?php esc_html_e('URL หน้า Facebook Page หรือ Profile', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="social_twitter">
                                            <span class="dashicons dashicons-twitter" style="color: #000000;"></span>
                                            <?php esc_html_e('X (Twitter)', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <input type="url" id="social_twitter" name="social_twitter"
                                            value="<?php echo esc_attr($social_twitter); ?>" class="regular-text"
                                            placeholder="https://x.com/yourusername" />
                                        <p class="description">
                                            <?php esc_html_e('URL โปรไฟล์ X (Twitter)', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="social_instagram">
                                            <span class="dashicons dashicons-instagram" style="color: #e4405f;"></span>
                                            <?php esc_html_e('Instagram', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <input type="url" id="social_instagram" name="social_instagram"
                                            value="<?php echo esc_attr($social_instagram); ?>" class="regular-text"
                                            placeholder="https://instagram.com/yourusername" />
                                        <p class="description">
                                            <?php esc_html_e('URL โปรไฟล์ Instagram', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="social_youtube">
                                            <span class="dashicons dashicons-youtube" style="color: #ff0000;"></span>
                                            <?php esc_html_e('YouTube', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <input type="url" id="social_youtube" name="social_youtube"
                                            value="<?php echo esc_attr($social_youtube); ?>" class="regular-text"
                                            placeholder="https://youtube.com/@yourchannel" />
                                        <p class="description"><?php esc_html_e('URL ช่อง YouTube', 'konderntang'); ?></p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="social_tiktok">
                                            <span class="dashicons dashicons-video-alt3" style="color: #000000;"></span>
                                            <?php esc_html_e('TikTok', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <input type="url" id="social_tiktok" name="social_tiktok"
                                            value="<?php echo esc_attr($social_tiktok); ?>" class="regular-text"
                                            placeholder="https://tiktok.com/@yourusername" />
                                        <p class="description"><?php esc_html_e('URL โปรไฟล์ TikTok', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="social_line">
                                            <span class="dashicons dashicons-format-chat" style="color: #00b900;"></span>
                                            <?php esc_html_e('LINE Official Account', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <input type="url" id="social_line" name="social_line"
                                            value="<?php echo esc_attr($social_line); ?>" class="regular-text"
                                            placeholder="https://line.me/R/ti/p/@yourlineid" />
                                        <p class="description">
                                            <?php esc_html_e('URL LINE Official Account หรือ LINE Add Friend Link', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="social_pinterest">
                                            <span class="dashicons dashicons-pinterest" style="color: #bd081c;"></span>
                                            <?php esc_html_e('Pinterest', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <input type="url" id="social_pinterest" name="social_pinterest"
                                            value="<?php echo esc_attr($social_pinterest); ?>" class="regular-text"
                                            placeholder="https://pinterest.com/yourusername" />
                                        <p class="description">
                                            <?php esc_html_e('URL โปรไฟล์ Pinterest', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="social_linkedin">
                                            <span class="dashicons dashicons-linkedin" style="color: #0a66c2;"></span>
                                            <?php esc_html_e('LinkedIn', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <input type="url" id="social_linkedin" name="social_linkedin"
                                            value="<?php echo esc_attr($social_linkedin); ?>" class="regular-text"
                                            placeholder="https://linkedin.com/company/yourcompany" />
                                        <p class="description">
                                            <?php esc_html_e('URL หน้า LinkedIn Company หรือ Profile', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>

                                <!-- Display Options Section -->
                                <tr>
                                    <th colspan="2">
                                        <h3
                                            style="margin: 20px 0 0; padding: 15px 0 5px; border-bottom: 1px solid #e2e8f0;">
                                            <span class="dashicons dashicons-visibility" style="margin-right: 8px;"></span>
                                            <?php esc_html_e('Display Options', 'konderntang'); ?>
                                        </h3>
                                    </th>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <span class="dashicons dashicons-admin-appearance"></span>
                                        <?php esc_html_e('Show in Header', 'konderntang'); ?>
                                    </th>
                                    <td>
                                        <label class="konderntang-toggle">
                                            <input type="checkbox" name="social_show_header" value="1" <?php checked($social_show_header, true); ?> />
                                            <span class="toggle-slider"></span>
                                            <span
                                                class="toggle-label"><?php esc_html_e('แสดงไอคอนโซเชียลมีเดียในส่วน Header', 'konderntang'); ?></span>
                                        </label>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <span class="dashicons dashicons-arrow-down-alt"></span>
                                        <?php esc_html_e('Show in Footer', 'konderntang'); ?>
                                    </th>
                                    <td>
                                        <label class="konderntang-toggle">
                                            <input type="checkbox" name="social_show_footer" value="1" <?php checked($social_show_footer, true); ?> />
                                            <span class="toggle-slider"></span>
                                            <span
                                                class="toggle-label"><?php esc_html_e('แสดงไอคอนโซเชียลมีเดียในส่วน Footer', 'konderntang'); ?></span>
                                        </label>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="social_icon_style">
                                            <span class="dashicons dashicons-art"></span>
                                            <?php esc_html_e('Icon Style', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <select name="social_icon_style" id="social_icon_style">
                                            <option value="default" <?php selected($social_icon_style, 'default'); ?>>
                                                <?php esc_html_e('Default (Flat)', 'konderntang'); ?>
                                            </option>
                                            <option value="rounded" <?php selected($social_icon_style, 'rounded'); ?>>
                                                <?php esc_html_e('Rounded', 'konderntang'); ?>
                                            </option>
                                            <option value="square" <?php selected($social_icon_style, 'square'); ?>>
                                                <?php esc_html_e('Square', 'konderntang'); ?>
                                            </option>
                                            <option value="outline" <?php selected($social_icon_style, 'outline'); ?>>
                                                <?php esc_html_e('Outline', 'konderntang'); ?>
                                            </option>
                                        </select>
                                        <p class="description">
                                            <?php esc_html_e('เลือกรูปแบบการแสดงผลไอคอน', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="social_icon_size">
                                            <span class="dashicons dashicons-editor-expand"></span>
                                            <?php esc_html_e('Icon Size', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <select name="social_icon_size" id="social_icon_size">
                                            <option value="small" <?php selected($social_icon_size, 'small'); ?>>
                                                <?php esc_html_e('Small (20px)', 'konderntang'); ?>
                                            </option>
                                            <option value="medium" <?php selected($social_icon_size, 'medium'); ?>>
                                                <?php esc_html_e('Medium (24px)', 'konderntang'); ?>
                                            </option>
                                            <option value="large" <?php selected($social_icon_size, 'large'); ?>>
                                                <?php esc_html_e('Large (32px)', 'konderntang'); ?>
                                            </option>
                                        </select>
                                        <p class="description"><?php esc_html_e('เลือกขนาดไอคอน', 'konderntang'); ?></p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <span class="dashicons dashicons-external"></span>
                                        <?php esc_html_e('Open in New Tab', 'konderntang'); ?>
                                    </th>
                                    <td>
                                        <label class="konderntang-toggle">
                                            <input type="checkbox" name="social_open_new_tab" value="1" <?php checked($social_open_new_tab, true); ?> />
                                            <span class="toggle-slider"></span>
                                            <span
                                                class="toggle-label"><?php esc_html_e('เปิดลิงก์โซเชียลมีเดียในแท็บใหม่', 'konderntang'); ?></span>
                                        </label>
                                    </td>
                                </tr>

                                <!-- Social Sharing Section -->
                                <tr>
                                    <th colspan="2">
                                        <h3
                                            style="margin: 20px 0 0; padding: 15px 0 5px; border-bottom: 1px solid #e2e8f0;">
                                            <span class="dashicons dashicons-share-alt" style="margin-right: 8px;"></span>
                                            <?php esc_html_e('Social Sharing (Share Buttons)', 'konderntang'); ?>
                                        </h3>
                                    </th>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <span class="dashicons dashicons-share"></span>
                                        <?php esc_html_e('Enable Share Buttons', 'konderntang'); ?>
                                    </th>
                                    <td>
                                        <label class="konderntang-toggle">
                                            <input type="checkbox" name="share_enabled" value="1" <?php checked($share_enabled, true); ?> />
                                            <span class="toggle-slider"></span>
                                            <span
                                                class="toggle-label"><?php esc_html_e('แสดงปุ่มแชร์ในหน้าบทความ', 'konderntang'); ?></span>
                                        </label>
                                        <p class="description">
                                            <?php esc_html_e('เปิดใช้งานปุ่มแชร์บทความไปยังโซเชียลมีเดีย', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="share_position">
                                            <span class="dashicons dashicons-editor-alignleft"></span>
                                            <?php esc_html_e('Share Buttons Position', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <select name="share_position" id="share_position">
                                            <option value="top" <?php selected($share_position, 'top'); ?>>
                                                <?php esc_html_e('Above Content (ด้านบนเนื้อหา)', 'konderntang'); ?>
                                            </option>
                                            <option value="bottom" <?php selected($share_position, 'bottom'); ?>>
                                                <?php esc_html_e('Below Content (ด้านล่างเนื้อหา)', 'konderntang'); ?>
                                            </option>
                                            <option value="both" <?php selected($share_position, 'both'); ?>>
                                                <?php esc_html_e('Both (ทั้งด้านบนและด้านล่าง)', 'konderntang'); ?>
                                            </option>
                                            <option value="floating" <?php selected($share_position, 'floating'); ?>>
                                                <?php esc_html_e('Floating Sidebar (แถบลอยด้านข้าง)', 'konderntang'); ?>
                                            </option>
                                        </select>
                                        <p class="description">
                                            <?php esc_html_e('เลือกตำแหน่งแสดงปุ่มแชร์', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="share_style">
                                            <span class="dashicons dashicons-art"></span>
                                            <?php esc_html_e('Share Button Style', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <select name="share_style" id="share_style">
                                            <option value="icon" <?php selected($share_style, 'icon'); ?>>
                                                <?php esc_html_e('Icon Only (ไอคอนอย่างเดียว)', 'konderntang'); ?>
                                            </option>
                                            <option value="icon-text" <?php selected($share_style, 'icon-text'); ?>>
                                                <?php esc_html_e('Icon with Text (ไอคอนพร้อมข้อความ)', 'konderntang'); ?>
                                            </option>
                                            <option value="button" <?php selected($share_style, 'button'); ?>>
                                                <?php esc_html_e('Full Button (ปุ่มเต็ม)', 'konderntang'); ?>
                                            </option>
                                        </select>
                                        <p class="description"><?php esc_html_e('เลือกรูปแบบปุ่มแชร์', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <span class="dashicons dashicons-admin-generic"></span>
                                        <?php esc_html_e('Share Platforms', 'konderntang'); ?>
                                    </th>
                                    <td>
                                        <fieldset>
                                            <label style="display: block; margin-bottom: 8px;">
                                                <input type="checkbox" name="share_platforms[]" value="facebook" <?php checked(in_array('facebook', $share_platforms, true)); ?> />
                                                <span class="dashicons dashicons-facebook" style="color: #1877f2;"></span>
                                                <?php esc_html_e('Facebook', 'konderntang'); ?>
                                            </label>
                                            <label style="display: block; margin-bottom: 8px;">
                                                <input type="checkbox" name="share_platforms[]" value="twitter" <?php checked(in_array('twitter', $share_platforms, true)); ?> />
                                                <span class="dashicons dashicons-twitter" style="color: #000000;"></span>
                                                <?php esc_html_e('X (Twitter)', 'konderntang'); ?>
                                            </label>
                                            <label style="display: block; margin-bottom: 8px;">
                                                <input type="checkbox" name="share_platforms[]" value="line" <?php checked(in_array('line', $share_platforms, true)); ?> />
                                                <span class="dashicons dashicons-format-chat"
                                                    style="color: #00b900;"></span>
                                                <?php esc_html_e('LINE', 'konderntang'); ?>
                                            </label>
                                            <label style="display: block; margin-bottom: 8px;">
                                                <input type="checkbox" name="share_platforms[]" value="email" <?php checked(in_array('email', $share_platforms, true)); ?> />
                                                <span class="dashicons dashicons-email" style="color: #666666;"></span>
                                                <?php esc_html_e('Email', 'konderntang'); ?>
                                            </label>
                                            <label style="display: block; margin-bottom: 8px;">
                                                <input type="checkbox" name="share_platforms[]" value="copy" <?php checked(in_array('copy', $share_platforms, true)); ?> />
                                                <span class="dashicons dashicons-admin-links"
                                                    style="color: #666666;"></span>
                                                <?php esc_html_e('Copy Link', 'konderntang'); ?>
                                            </label>
                                            <label style="display: block; margin-bottom: 8px;">
                                                <input type="checkbox" name="share_platforms[]" value="pinterest" <?php checked(in_array('pinterest', $share_platforms, true)); ?> />
                                                <span class="dashicons dashicons-pinterest" style="color: #bd081c;"></span>
                                                <?php esc_html_e('Pinterest', 'konderntang'); ?>
                                            </label>
                                            <label style="display: block; margin-bottom: 8px;">
                                                <input type="checkbox" name="share_platforms[]" value="linkedin" <?php checked(in_array('linkedin', $share_platforms, true)); ?> />
                                                <span class="dashicons dashicons-linkedin" style="color: #0a66c2;"></span>
                                                <?php esc_html_e('LinkedIn', 'konderntang'); ?>
                                            </label>
                                            <label style="display: block; margin-bottom: 8px;">
                                                <input type="checkbox" name="share_platforms[]" value="whatsapp" <?php checked(in_array('whatsapp', $share_platforms, true)); ?> />
                                                <span class="dashicons dashicons-whatsapp" style="color: #25d366;"></span>
                                                <?php esc_html_e('WhatsApp', 'konderntang'); ?>
                                            </label>
                                        </fieldset>
                                        <p class="description">
                                            <?php esc_html_e('เลือกแพลตฟอร์มที่ต้องการให้แสดงปุ่มแชร์', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <span class="dashicons dashicons-visibility"></span>
                                        <?php esc_html_e('Show Share Count', 'konderntang'); ?>
                                    </th>
                                    <td>
                                        <label class="konderntang-toggle">
                                            <input type="checkbox" name="share_show_count" value="1" <?php checked($share_show_count, true); ?> />
                                            <span class="toggle-slider"></span>
                                            <span
                                                class="toggle-label"><?php esc_html_e('แสดงจำนวนการแชร์ (ถ้ามี)', 'konderntang'); ?></span>
                                        </label>
                                        <p class="description">
                                            <?php esc_html_e('แสดงจำนวนครั้งที่บทความถูกแชร์ (บางแพลตฟอร์มอาจไม่รองรับ)', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="share_label">
                                            <span class="dashicons dashicons-editor-textcolor"></span>
                                            <?php esc_html_e('Share Label', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <input type="text" id="share_label" name="share_label"
                                            value="<?php echo esc_attr($share_label); ?>" class="regular-text"
                                            placeholder="<?php esc_attr_e('แชร์บทความนี้', 'konderntang'); ?>" />
                                        <p class="description">
                                            <?php esc_html_e('ข้อความที่แสดงก่อนปุ่มแชร์ (เว้นว่างไว้หากไม่ต้องการแสดง)', 'konderntang'); ?>
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
                                    <h2><?php esc_html_e('Cookie Consent Settings', 'konderntang'); ?></h2>
                                    <p><?php esc_html_e('GDPR cookie banner configuration', 'konderntang'); ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="konderntang-section-content">
                            <table class="form-table">
                                <!-- Enable Cookie Consent -->
                                <tr>
                                    <th scope="row">
                                        <span class="dashicons dashicons-privacy"></span>
                                        <?php esc_html_e('Cookie Consent', 'konderntang'); ?>
                                    </th>
                                    <td>
                                        <label class="konderntang-toggle">
                                            <input type="checkbox" name="cookie_consent_enabled" value="1" <?php checked($cookie_consent_enabled, true); ?> />
                                            <span class="toggle-slider"></span>
                                            <span
                                                class="toggle-label"><?php esc_html_e('Enable cookie consent banner', 'konderntang'); ?></span>
                                        </label>
                                        <p class="description">
                                            <?php esc_html_e('แสดง Cookie Consent Banner เพื่อให้สอดคล้องกับ GDPR', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>

                                <!-- Cookie Message -->
                                <tr>
                                    <th scope="row">
                                        <label for="cookie_consent_message">
                                            <span class="dashicons dashicons-edit"></span>
                                            <?php esc_html_e('Cookie Consent Message', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <textarea name="cookie_consent_message" id="cookie_consent_message" rows="3"
                                            class="large-text"
                                            placeholder="<?php esc_attr_e('เราใช้คุกกี้เพื่อปรับปรุงประสบการณ์การใช้งานของคุณ', 'konderntang'); ?>"><?php echo esc_textarea($cookie_consent_message); ?></textarea>
                                        <p class="description">
                                            <?php esc_html_e('ข้อความที่จะแสดงใน Cookie Consent Banner', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>

                                <!-- Privacy Policy Page -->
                                <tr>
                                    <th scope="row">
                                        <label for="cookie_consent_privacy_page">
                                            <span class="dashicons dashicons-media-document"></span>
                                            <?php esc_html_e('Privacy Policy Page', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <?php
                                        wp_dropdown_pages(array(
                                            'name' => 'cookie_consent_privacy_page',
                                            'id' => 'cookie_consent_privacy_page',
                                            'selected' => $cookie_consent_privacy_page,
                                            'show_option_none' => esc_html__('— Select —', 'konderntang'),
                                            'option_none_value' => 0,
                                            'class' => 'regular-text',
                                        ));
                                        ?>
                                        <p class="description">
                                            <?php esc_html_e('หน้านโยบายความเป็นส่วนตัวที่จะลิงก์จาก Cookie Banner', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>

                                <!-- Position -->
                                <tr>
                                    <th scope="row">
                                        <label for="cookie_consent_position">
                                            <span class="dashicons dashicons-move"></span>
                                            <?php esc_html_e('Banner Position', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <select name="cookie_consent_position" id="cookie_consent_position"
                                            class="regular-text">
                                            <option value="bottom" <?php selected($cookie_consent_position, 'bottom'); ?>>
                                                <?php esc_html_e('Bottom', 'konderntang'); ?>
                                            </option>
                                            <option value="top" <?php selected($cookie_consent_position, 'top'); ?>>
                                                <?php esc_html_e('Top', 'konderntang'); ?>
                                            </option>
                                        </select>
                                        <p class="description">
                                            <?php esc_html_e('ตำแหน่งการแสดง Banner', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>

                                <!-- Style -->
                                <tr>
                                    <th scope="row">
                                        <label for="cookie_consent_style">
                                            <span class="dashicons dashicons-art"></span>
                                            <?php esc_html_e('Banner Style', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <select name="cookie_consent_style" id="cookie_consent_style" class="regular-text">
                                            <option value="bar" <?php selected($cookie_consent_style, 'bar'); ?>>
                                                <?php esc_html_e('Bar (แบบแถบ)', 'konderntang'); ?>
                                            </option>
                                            <option value="box" <?php selected($cookie_consent_style, 'box'); ?>>
                                                <?php esc_html_e('Box (แบบกล่อง)', 'konderntang'); ?>
                                            </option>
                                        </select>
                                        <p class="description">
                                            <?php esc_html_e('รูปแบบการแสดงผล Banner', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>

                                <!-- Background Color -->
                                <tr>
                                    <th scope="row">
                                        <label for="cookie_consent_bg_color">
                                            <span class="dashicons dashicons-art"></span>
                                            <?php esc_html_e('Background Color', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <input type="text" name="cookie_consent_bg_color" id="cookie_consent_bg_color"
                                            value="<?php echo esc_attr($cookie_consent_bg_color); ?>"
                                            class="konderntang-color-picker" data-default-color="#ffffff" />
                                        <p class="description">
                                            <?php esc_html_e('สีพื้นหลังของ Banner', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>

                                <!-- Text Color -->
                                <tr>
                                    <th scope="row">
                                        <label for="cookie_consent_text_color">
                                            <span class="dashicons dashicons-editor-textcolor"></span>
                                            <?php esc_html_e('Text Color', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <input type="text" name="cookie_consent_text_color" id="cookie_consent_text_color"
                                            value="<?php echo esc_attr($cookie_consent_text_color); ?>"
                                            class="konderntang-color-picker" data-default-color="#374151" />
                                        <p class="description">
                                            <?php esc_html_e('สีข้อความ', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>

                                <!-- Button Background Color -->
                                <tr>
                                    <th scope="row">
                                        <label for="cookie_consent_button_bg">
                                            <span class="dashicons dashicons-art"></span>
                                            <?php esc_html_e('Button Background Color', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <input type="text" name="cookie_consent_button_bg" id="cookie_consent_button_bg"
                                            value="<?php echo esc_attr($cookie_consent_button_bg); ?>"
                                            class="konderntang-color-picker" data-default-color="#3b82f6" />
                                        <p class="description">
                                            <?php esc_html_e('สีพื้นหลังของปุ่ม', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>

                                <!-- Button Text Color -->
                                <tr>
                                    <th scope="row">
                                        <label for="cookie_consent_button_text">
                                            <span class="dashicons dashicons-editor-textcolor"></span>
                                            <?php esc_html_e('Button Text Color', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <input type="text" name="cookie_consent_button_text" id="cookie_consent_button_text"
                                            value="<?php echo esc_attr($cookie_consent_button_text); ?>"
                                            class="konderntang-color-picker" data-default-color="#ffffff" />
                                        <p class="description">
                                            <?php esc_html_e('สีข้อความบนปุ่ม', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>

                                <!-- Button Texts -->
                                <tr>
                                    <th scope="row">
                                        <label for="cookie_consent_accept_text">
                                            <span class="dashicons dashicons-yes"></span>
                                            <?php esc_html_e('Accept Button Text', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <input type="text" name="cookie_consent_accept_text" id="cookie_consent_accept_text"
                                            value="<?php echo esc_attr($cookie_consent_accept_text); ?>"
                                            class="regular-text"
                                            placeholder="<?php esc_attr_e('ยอมรับทั้งหมด', 'konderntang'); ?>" />
                                    </td>
                                </tr>

                                <tr>
                                    <th scope="row">
                                        <label for="cookie_consent_decline_text">
                                            <span class="dashicons dashicons-no"></span>
                                            <?php esc_html_e('Decline Button Text', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <input type="text" name="cookie_consent_decline_text"
                                            id="cookie_consent_decline_text"
                                            value="<?php echo esc_attr($cookie_consent_decline_text); ?>"
                                            class="regular-text"
                                            placeholder="<?php esc_attr_e('ปฏิเสธทั้งหมด', 'konderntang'); ?>" />
                                    </td>
                                </tr>

                                <tr>
                                    <th scope="row">
                                        <label for="cookie_consent_settings_text">
                                            <span class="dashicons dashicons-admin-settings"></span>
                                            <?php esc_html_e('Settings Button Text', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <input type="text" name="cookie_consent_settings_text"
                                            id="cookie_consent_settings_text"
                                            value="<?php echo esc_attr($cookie_consent_settings_text); ?>"
                                            class="regular-text"
                                            placeholder="<?php esc_attr_e('ตั้งค่า', 'konderntang'); ?>" />
                                    </td>
                                </tr>

                                <!-- Show Decline Button -->
                                <tr>
                                    <th scope="row">
                                        <span class="dashicons dashicons-no"></span>
                                        <?php esc_html_e('Show Decline Button', 'konderntang'); ?>
                                    </th>
                                    <td>
                                        <label class="konderntang-toggle">
                                            <input type="checkbox" name="cookie_consent_show_decline" value="1" <?php checked($cookie_consent_show_decline, true); ?> />
                                            <span class="toggle-slider"></span>
                                            <span
                                                class="toggle-label"><?php esc_html_e('Show decline button in banner', 'konderntang'); ?></span>
                                        </label>
                                        <p class="description">
                                            <?php esc_html_e('แสดงปุ่มปฏิเสธใน Banner (บางประเทศ GDPR กำหนดให้ต้องมี)', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>

                                <!-- Auto Hide -->
                                <tr>
                                    <th scope="row">
                                        <span class="dashicons dashicons-hidden"></span>
                                        <?php esc_html_e('Auto Hide Banner', 'konderntang'); ?>
                                    </th>
                                    <td>
                                        <label class="konderntang-toggle">
                                            <input type="checkbox" name="cookie_consent_auto_hide" value="1" <?php checked($cookie_consent_auto_hide, true); ?> />
                                            <span class="toggle-slider"></span>
                                            <span
                                                class="toggle-label"><?php esc_html_e('Auto-hide banner after delay', 'konderntang'); ?></span>
                                        </label>
                                        <p class="description">
                                            <?php esc_html_e('ซ่อน Banner อัตโนมัติหลังจากเวลาที่กำหนด (ไม่แนะนำสำหรับ GDPR compliance)', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>

                                <tr>
                                    <th scope="row">
                                        <label for="cookie_consent_auto_hide_delay">
                                            <span class="dashicons dashicons-clock"></span>
                                            <?php esc_html_e('Auto Hide Delay (seconds)', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <input type="number" name="cookie_consent_auto_hide_delay"
                                            id="cookie_consent_auto_hide_delay"
                                            value="<?php echo esc_attr($cookie_consent_auto_hide_delay); ?>" min="5"
                                            max="60" step="1" class="small-text" />
                                        <p class="description">
                                            <?php esc_html_e('เวลาก่อนซ่อน Banner อัตโนมัติ (5-60 วินาที)', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>

                                <!-- Cookie Categories Header -->
                                <tr>
                                    <th scope="row" colspan="2">
                                        <h3 class="konderntang-section-title">
                                            <span class="dashicons dashicons-category"></span>
                                            <?php esc_html_e('Cookie Categories', 'konderntang'); ?>
                                        </h3>
                                        <p class="description">
                                            <?php esc_html_e('กำหนดประเภทคุกกี้ที่จะให้ผู้ใช้เลือกได้ในหน้าตั้งค่า', 'konderntang'); ?>
                                        </p>
                                    </th>
                                </tr>

                                <!-- Necessary Cookies -->
                                <tr>
                                    <th scope="row">
                                        <span class="dashicons dashicons-shield-alt"></span>
                                        <?php esc_html_e('Necessary Cookies', 'konderntang'); ?>
                                    </th>
                                    <td>
                                        <label class="konderntang-toggle">
                                            <input type="checkbox" name="cookie_consent_necessary" value="1" <?php checked($cookie_consent_necessary, true); ?> disabled />
                                            <span class="toggle-slider"></span>
                                            <span
                                                class="toggle-label"><?php esc_html_e('Always enabled (required)', 'konderntang'); ?></span>
                                        </label>
                                        <p class="description">
                                            <?php esc_html_e('คุกกี้ที่จำเป็นไม่สามารถปิดได้', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>

                                <tr>
                                    <th scope="row">
                                        <label for="cookie_consent_necessary_desc">
                                            <span class="dashicons dashicons-editor-alignleft"></span>
                                            <?php esc_html_e('Description', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <textarea name="cookie_consent_necessary_desc" id="cookie_consent_necessary_desc"
                                            rows="2"
                                            class="large-text"><?php echo esc_textarea($cookie_consent_necessary_desc); ?></textarea>
                                    </td>
                                </tr>

                                <!-- Analytics Cookies -->
                                <tr>
                                    <th scope="row">
                                        <span class="dashicons dashicons-chart-line"></span>
                                        <?php esc_html_e('Analytics Cookies', 'konderntang'); ?>
                                    </th>
                                    <td>
                                        <label class="konderntang-toggle">
                                            <input type="checkbox" name="cookie_consent_analytics" value="1" <?php checked($cookie_consent_analytics, true); ?> />
                                            <span class="toggle-slider"></span>
                                            <span
                                                class="toggle-label"><?php esc_html_e('Enable analytics category', 'konderntang'); ?></span>
                                        </label>
                                        <p class="description">
                                            <?php esc_html_e('แสดงตัวเลือกคุกกี้วิเคราะห์การใช้งาน (Google Analytics, etc.)', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>

                                <tr>
                                    <th scope="row">
                                        <label for="cookie_consent_analytics_desc">
                                            <span class="dashicons dashicons-editor-alignleft"></span>
                                            <?php esc_html_e('Description', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <textarea name="cookie_consent_analytics_desc" id="cookie_consent_analytics_desc"
                                            rows="2"
                                            class="large-text"><?php echo esc_textarea($cookie_consent_analytics_desc); ?></textarea>
                                    </td>
                                </tr>

                                <!-- Marketing Cookies -->
                                <tr>
                                    <th scope="row">
                                        <span class="dashicons dashicons-megaphone"></span>
                                        <?php esc_html_e('Marketing Cookies', 'konderntang'); ?>
                                    </th>
                                    <td>
                                        <label class="konderntang-toggle">
                                            <input type="checkbox" name="cookie_consent_marketing" value="1" <?php checked($cookie_consent_marketing, true); ?> />
                                            <span class="toggle-slider"></span>
                                            <span
                                                class="toggle-label"><?php esc_html_e('Enable marketing category', 'konderntang'); ?></span>
                                        </label>
                                        <p class="description">
                                            <?php esc_html_e('แสดงตัวเลือกคุกกี้การตลาด (Facebook Pixel, Google Ads, etc.)', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>

                                <tr>
                                    <th scope="row">
                                        <label for="cookie_consent_marketing_desc">
                                            <span class="dashicons dashicons-editor-alignleft"></span>
                                            <?php esc_html_e('Description', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <textarea name="cookie_consent_marketing_desc" id="cookie_consent_marketing_desc"
                                            rows="2"
                                            class="large-text"><?php echo esc_textarea($cookie_consent_marketing_desc); ?></textarea>
                                    </td>
                                </tr>

                                <!-- Functional Cookies -->
                                <tr>
                                    <th scope="row">
                                        <span class="dashicons dashicons-admin-tools"></span>
                                        <?php esc_html_e('Functional Cookies', 'konderntang'); ?>
                                    </th>
                                    <td>
                                        <label class="konderntang-toggle">
                                            <input type="checkbox" name="cookie_consent_functional" value="1" <?php checked($cookie_consent_functional, true); ?> />
                                            <span class="toggle-slider"></span>
                                            <span
                                                class="toggle-label"><?php esc_html_e('Enable functional category', 'konderntang'); ?></span>
                                        </label>
                                        <p class="description">
                                            <?php esc_html_e('แสดงตัวเลือกคุกกี้เสริมฟังก์ชัน (Social Share, Live Chat, etc.)', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>

                                <tr>
                                    <th scope="row">
                                        <label for="cookie_consent_functional_desc">
                                            <span class="dashicons dashicons-editor-alignleft"></span>
                                            <?php esc_html_e('Description', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <textarea name="cookie_consent_functional_desc" id="cookie_consent_functional_desc"
                                            rows="2"
                                            class="large-text"><?php echo esc_textarea($cookie_consent_functional_desc); ?></textarea>
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
                                    <h2><?php esc_html_e('Advanced Settings', 'konderntang'); ?></h2>
                                    <p><?php esc_html_e('Custom code and analytics', 'konderntang'); ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="konderntang-section-content">
                            <div class="konderntang-warning-box">
                                <span class="dashicons dashicons-warning"></span>
                                <p><?php esc_html_e('ข้อควรระวัง: การแก้ไขการตั้งค่าขั้นสูงอาจส่งผลต่อการทำงานของเว็บไซต์ หากไม่แน่ใจ ควรปรึกษาผู้เชี่ยวชาญ', 'konderntang'); ?>
                                </p>
                            </div>
                            <table class="form-table">
                                <tr>
                                    <th scope="row">
                                        <label for="advanced_custom_css">
                                            <span class="dashicons dashicons-editor-code"></span>
                                            <?php esc_html_e('Custom CSS', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <textarea name="advanced_custom_css" id="advanced_custom_css" rows="10"
                                            class="large-text code"
                                            placeholder="/* Custom CSS */"><?php echo esc_textarea($advanced_custom_css); ?></textarea>
                                        <p class="description">
                                            <?php esc_html_e('เพิ่มโค้ด CSS ที่กำหนดเอง ไม่ต้องใส่แท็ก <style>', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="advanced_custom_js">
                                            <span class="dashicons dashicons-media-code"></span>
                                            <?php esc_html_e('Custom JavaScript', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <textarea name="advanced_custom_js" id="advanced_custom_js" rows="10"
                                            class="large-text code"
                                            placeholder="// Custom JavaScript"><?php echo esc_textarea($advanced_custom_js); ?></textarea>
                                        <p class="description">
                                            <?php esc_html_e('เพิ่มโค้ด JavaScript ที่กำหนดเอง ไม่ต้องใส่แท็ก <script>', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="advanced_google_analytics">
                                            <span class="dashicons dashicons-chart-area"></span>
                                            <?php esc_html_e('Google Analytics Code', 'konderntang'); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <textarea name="advanced_google_analytics" id="advanced_google_analytics" rows="6"
                                            class="large-text code"
                                            placeholder="<!-- Google Analytics -->"><?php echo esc_textarea($advanced_google_analytics); ?></textarea>
                                        <p class="description">
                                            <?php esc_html_e('วาง Google Analytics tracking code ของคุณที่นี่', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="advanced_facebook_pixel">
                                            <span class="dashicons dashicons-facebook-alt"></span>
                                            <?php esc_html_e('Facebook Pixel Code', 'konderntang'); ?></label>
                                    </th>
                                    <td>
                                        <textarea name="advanced_facebook_pixel" id="advanced_facebook_pixel" rows="6"
                                            class="large-text code"
                                            placeholder="<!-- Facebook Pixel -->"><?php echo esc_textarea($advanced_facebook_pixel); ?></textarea>
                                        <p class="description">
                                            <?php esc_html_e('วาง Facebook Pixel tracking code ของคุณที่นี่', 'konderntang'); ?>
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Save Button -->
                    <div class="konderntang-settings-footer">
                        <button type="submit" name="konderntang_save_settings"
                            class="button button-primary button-large konderntang-save-btn">
                            <span class="dashicons dashicons-yes-alt"></span>
                            <?php esc_html_e('Save Changes', 'konderntang'); ?>
                        </button>
                        <a href="<?php echo esc_url(admin_url('customize.php')); ?>" class="button button-secondary">
                            <span class="dashicons dashicons-admin-appearance"></span>
                            <?php esc_html_e('Customize Theme', 'konderntang'); ?>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php
}
