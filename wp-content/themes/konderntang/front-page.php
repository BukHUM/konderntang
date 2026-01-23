<?php
/**
 * The front page template
 *
 * @package KonDernTang
 * @since 1.0.0
 */

get_header();
?>

<main id="main" class="site-main">
    <?php
    // Hero Slider
    if (konderntang_get_option('hero_slider_enabled', true)) {
        konderntang_get_component('hero-slider');
    }

    echo '<div class="container mx-auto px-4 py-12">';

    // AI Recommendations (Featured Section)
    if (konderntang_get_option('featured_section_enabled', true)) {
        konderntang_get_component('featured-section', array(
            'title' => esc_html__('แนะนำสำหรับคุณ', 'konderntang'),
            'subtitle' => esc_html__('เนื้อหาที่คัดสรรมาเป็นพิเศษตามความสนใจของคุณ', 'konderntang')
        ));
    }

    // Category Section: Thailand
    konderntang_get_component('category-section', array(
        'category' => 'travel-thailand',
        'title' => esc_html__('เที่ยวทั่วไทย', 'konderntang'),
        'subtitle' => esc_html__('หลงรักเมืองไทย ไปกี่ครั้งก็ไม่เบื่อ', 'konderntang'),
        'link_text' => esc_html__('ดูทั้งหมด', 'konderntang'),
        'accent_color' => 'border-primary'
    ));

    // Affiliate Banner (Placeholder: Future Component or Widget)
    // <div class="bg-gradient-to-r from-blue-600 to-blue-400 rounded-2xl p-8 mb-16 text-center text-white shadow-xl">...</div>
    
    // Category Section: International
    konderntang_get_component('category-section', array(
        'category' => 'travel-international',
        'title' => esc_html__('เที่ยวต่างประเทศ', 'konderntang'),
        'subtitle' => esc_html__('เปิดประสบการณ์ใหม่ในต่างแดน สัมผัสวัฒนธรรมจากทั่วทุกมุมโลก', 'konderntang'),
        'link_text' => esc_html__('ดูทั้งหมด', 'konderntang'),
        'accent_color' => 'border-secondary'
    ));

    // Dynamic Homepage Layout (Slots 1-4)
    for ($i = 1; $i <= 4; $i++) {
        $slot_component = konderntang_get_option("homepage_layout_slot_{$i}", 'none');

        // Defaults for first 4 slots if not set (backward compatibility)
        if ($i === 1 && $slot_component === 'none')
            $slot_component = 'section_1';
        if ($i === 2 && $slot_component === 'none')
            $slot_component = 'section_2';
        if ($i === 3 && $slot_component === 'none')
            $slot_component = 'section_3';
        if ($i === 4 && $slot_component === 'none')
            $slot_component = 'cta_banner';

        switch ($slot_component) {
            case 'section_1':
            case 'section_2':
            case 'section_3':
                // Extract number from string 'section_X'
                $sec_num = intval(str_replace('section_', '', $slot_component));

                $sec_enabled = konderntang_get_option("homepage_section_{$sec_num}_enabled", false);
                $sec_taxonomy_type = konderntang_get_option("homepage_section_{$sec_num}_taxonomy_type", 'category');
                $sec_category_id = konderntang_get_option("homepage_section_{$sec_num}_category", 0);
                $sec_posts_count = konderntang_get_option("homepage_section_{$sec_num}_count", 4);

                if ($sec_enabled && !empty($sec_category_id)) {
                    $section_args = array(
                        'category' => $sec_category_id,
                        'taxonomy_type' => $sec_taxonomy_type,
                        'posts_count' => $sec_posts_count,
                        'title' => '',
                        'subtitle' => '',
                        'accent_color' => 'border-primary'
                    );

                    // Get term based on taxonomy type
                    $term = null;
                    if ($sec_taxonomy_type === 'category') {
                        $term = get_term($sec_category_id, 'category');
                    } elseif ($sec_taxonomy_type === 'destination') {
                        $term = get_term($sec_category_id, 'destination');
                    } elseif ($sec_taxonomy_type === 'travel_type') {
                        $term = get_term($sec_category_id, 'travel_type');
                    }

                    if (!is_wp_error($term) && $term) {
                        $section_args['title'] = $term->name;
                        if (!empty($term->description)) {
                            $section_args['subtitle'] = $term->description;
                        }
                        $cat_accent = get_term_meta($sec_category_id, 'konderntang_accent_color', true);
                        if (!empty($cat_accent)) {
                            $section_args['accent_color'] = $cat_accent;
                        }
                    }

                    if (!empty($section_args['title'])) {
                        konderntang_get_component('recent-posts-grid', $section_args);
                    }
                }
                break;

            case 'cta_banner':
                $cta_banner_enabled = konderntang_get_option('cta_banner_enabled', false);
                if ($cta_banner_enabled) {
                    konderntang_get_component('cta-banner', array(
                        'title' => konderntang_get_option('cta_banner_title', ''),
                        'subtitle' => konderntang_get_option('cta_banner_subtitle', ''),
                        'button_text' => konderntang_get_option('cta_banner_button_text', ''),
                        'button_url' => konderntang_get_option('cta_banner_button_url', '#'),
                        'enabled' => true
                    ));
                }
                break;
        }
    }


    // Social Feed
    // Reuse 'social_show_footer' or just always show if no specific setting
    if (konderntang_get_option('social_show_footer', true)) {
        konderntang_get_component('social-feed');
    }
    ?>
</main>

<?php
get_footer();
