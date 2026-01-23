<?php
/**
 * Hero Slider Component
 *
 * Displays Hero Banner CPT items.
 *
 * @package KonDernTang
 * @since 1.0.0
 */

$hero_enabled = konderntang_get_option('hero_slider_enabled', true);
if (!$hero_enabled) {
    return;
}

// Get Settings
$hero_source = konderntang_get_option('hero_slider_source', 'banner');
$hero_height = konderntang_get_option('hero_slider_height', 500);

// Query Args
$limit = konderntang_get_option('hero_slider_posts', 4);
$posts_array = array();

// Get current language for Polylang filtering
$current_lang = function_exists('pll_current_language') ? pll_current_language() : '';

if ($hero_source === 'posts') {
    // 1. Posts Only
    $query_args = array(
        'post_type' => 'post',
        'posts_per_page' => $limit,
        'post_status' => 'publish',
        'ignore_sticky_posts' => 1
    );
    if ($current_lang) {
        $query_args['lang'] = $current_lang;
    }
    $q_posts = new WP_Query($query_args);
    $posts_array = $q_posts->posts;

} elseif ($hero_source === 'mixed') {
    // 2. Mixed: Banners FIRST, then Posts
    // Fetch all banners first (or up to limit)
    $query_args = array(
        'post_type' => 'hero_banner',
        'posts_per_page' => $limit,
        'post_status' => 'publish'
    );
    if ($current_lang) {
        $query_args['lang'] = $current_lang;
    }
    $q_banners = new WP_Query($query_args);

    $banner_count = count($q_banners->posts);
    $posts_array = $q_banners->posts;

    // If we still have room, fetch posts
    if ($banner_count < $limit) {
        $remaining = $limit - $banner_count;
        $query_args = array(
            'post_type' => 'post',
            'posts_per_page' => $remaining,
            'post_status' => 'publish',
            'ignore_sticky_posts' => 1
        );
        if ($current_lang) {
            $query_args['lang'] = $current_lang;
        }
        $q_posts = new WP_Query($query_args);
        // Merge
        $posts_array = array_merge($posts_array, $q_posts->posts);
    }

} else {
    // 3. Banner Only (default)
    $query_args = array(
        'post_type' => 'hero_banner',
        'posts_per_page' => -1,
        'post_status' => 'publish'
    );
    if ($current_lang) {
        $query_args['lang'] = $current_lang;
    }
    $q_banners = new WP_Query($query_args);
    $posts_array = $q_banners->posts;
}

if (empty($posts_array)) {
    return;
}

// Setup global post data for the loop
global $post;
?>

<header class="relative bg-dark overflow-hidden group" style="height: <?php echo esc_attr($hero_height); ?>px;">
    <!-- Slider Container -->
    <div id="hero-slider" class="relative h-full">
        <?php
        $slide_index = 0;
        $total_slides = count($posts_array);

        foreach ($posts_array as $post):
            setup_postdata($post);
            $post_id = get_the_ID();
            $post_type = get_post_type($post_id);

            // Default values
            $subtitle_text = ''; // This maps to the mockups "Highlight" badge or small colored tag
            $btn_text = '';
            $btn_link = '';
            $badge = ''; // This maps to the mockups "Category" badge
            $video_url = '';
            $description = ''; // This maps to the paragraph text below title
            $badge_bg_class = 'bg-secondary'; // Default
        
            // Get Meta Data based on Post Type
            if ($post_type === 'hero_banner') {
                $subtitle_text = get_post_meta($post_id, '_hero_badge', true) ?: get_post_meta($post_id, '_hero_subtitle', true);
                $btn_text = get_post_meta($post_id, '_hero_btn_text', true);
                $btn_link = get_post_meta($post_id, '_hero_link', true);
                $video_url = get_post_meta($post_id, '_hero_video_url', true);

                // Badge Color
                $badge_color = get_post_meta($post_id, '_hero_badge_color', true);
                if ($badge_color) {
                    if ($badge_color === 'primary')
                        $badge_bg_class = 'bg-primary';
                    elseif ($badge_color === 'secondary')
                        $badge_bg_class = 'bg-secondary';
                    elseif ($badge_color === 'gray')
                        $badge_bg_class = 'bg-gray-500';
                    else
                        $badge_bg_class = 'bg-' . $badge_color . '-500';
                }

                // Use subtitle as description if available, otherwise excerpt
                $desc_meta = get_post_meta($post_id, '_hero_subtitle', true);
                if ($desc_meta) {
                    $description = $desc_meta;
                }
            } else {
                // Regular Post logic
                $categories = get_the_category();
                if (!empty($categories)) {
                    $subtitle_text = '';
                    $use_cat = null;

                    // Logic: Prefer child category (sub-category) over parent
                    // First pass: look for child
                    foreach ($categories as $cat) {
                        if ($cat->parent > 0) {
                            $use_cat = $cat;
                            break;
                        }
                    }
                    // Fallback: use first category if no child found
                    if (!$use_cat && isset($categories[0])) {
                        $use_cat = $categories[0];
                    }

                    if ($use_cat) {
                        $subtitle_text = $use_cat->name;
                        $term_id = $use_cat->term_id;
                        $cat_color = get_term_meta($term_id, 'konderntang_accent_color', true);

                        // If Sub-Category has no color, try Parent Category
                        if (!$cat_color && $use_cat->parent > 0) {
                            $parent_id = $use_cat->parent;
                            $cat_color = get_term_meta($parent_id, 'konderntang_accent_color', true);
                        }

                        // Map border-color (from category meta) to bg-color
                        if ($cat_color) {
                            // $cat_color is like 'border-primary', 'border-red-500'
                            $bg_class = str_replace('border-', 'bg-', $cat_color);
                            // Ensure we actually got a change classes, or if it's already bg format (unlikely but safe)
                            if ($bg_class !== $cat_color || strpos($cat_color, 'bg-') === 0) {
                                $badge_bg_class = $bg_class;
                            }
                        }
                    }
                }
                $btn_text = __('Read Article', 'konderntang'); // "อ่านรีวิวฉบับเต็ม"
                $btn_link = get_permalink();
                $description = get_the_excerpt();

                // Use tags as badge if available
                $tags = get_the_tags();
                if ($tags && !empty($tags)) {
                    // Optional: mapped to something else if needed
                }
            }

            // Image
            $thumbnail = get_the_post_thumbnail_url($post_id, 'full');
            if (!$thumbnail && !$video_url) {
                $thumbnail = KONDERN_THEME_URI . '/assets/images/placeholder.webp';
            }

            $slide_class = $slide_index === 0 ? '' : 'opacity-0';
            ?>
            <div class="hero-slide absolute inset-0 flex items-end group <?php echo esc_attr($slide_class); ?>"
                data-slide="<?php echo esc_attr($slide_index); ?>">

                <!-- Background (Video or Image) -->
                <?php if ($video_url): ?>
                    <video autoplay muted loop playsinline class="absolute inset-0 w-full h-full object-cover">
                        <source src="<?php echo esc_url($video_url); ?>" type="video/mp4">
                    </video>
                <?php else: ?>
                    <img src="<?php echo esc_url($thumbnail); ?>" alt="<?php echo esc_attr(get_the_title()); ?>"
                        class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105 opacity-80">
                <?php endif; ?>

                <!-- Overlay: Gradient Bottom-Up -->
                <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent"></div>

                <!-- Content -->
                <div class="container mx-auto px-4 pb-12 relative z-10 text-left">

                    <!-- Badge / Subtitle -->
                    <?php if ($subtitle_text): ?>
                        <span
                            class="inline-block <?php echo esc_attr($badge_bg_class); ?> text-white text-xs font-bold px-3 py-1 rounded-full mb-4 uppercase tracking-wider">
                            <?php echo esc_html($subtitle_text); ?>
                        </span>
                    <?php endif; ?>

                    <!-- Title -->
                    <h1 class="text-3xl md:text-5xl font-heading font-bold text-white mb-4 leading-tight drop-shadow-md">
                        <?php if ($btn_link): ?>
                            <a href="<?php echo esc_url($btn_link); ?>"
                                class="text-white hover:text-primary transition-colors duration-300 decoration-0">
                                <?php echo esc_html(get_the_title()); ?>
                            </a>
                        <?php else: ?>
                            <?php echo esc_html(get_the_title()); ?>
                        <?php endif; ?>
                    </h1>

                    <!-- Description -->
                    <?php if ($description): ?>
                        <p class="text-gray-300 text-lg mb-6 max-w-2xl font-light line-clamp-2">
                            <?php echo esc_html($description); ?>
                        </p>
                    <?php endif; ?>

                    <!-- Button -->
                    <?php if ($btn_text && $btn_link): ?>
                        <a href="<?php echo esc_url($btn_link); ?>"
                            class="inline-flex items-center gap-2 bg-white text-dark font-heading font-semibold px-6 py-3 rounded-lg hover:bg-gray-100 transition">
                            <?php echo esc_html($btn_text); ?> <i class="ph ph-arrow-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php
            $slide_index++;
        endforeach;
        wp_reset_postdata();
        ?>
    </div>

    <!-- Navigation Arrows -->
    <?php if ($total_slides > 1): ?>
        <button id="hero-prev"
            class="absolute left-4 top-1/2 -translate-y-1/2 z-20 bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white p-3 rounded-full transition-all duration-300 hover:scale-110 opacity-0 group-hover:opacity-100"
            aria-label="<?php esc_attr_e('Previous Slide', 'konderntang'); ?>">
            <i class="ph ph-caret-left text-2xl"></i>
        </button>
        <button id="hero-next"
            class="absolute right-4 top-1/2 -translate-y-1/2 z-20 bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white p-3 rounded-full transition-all duration-300 hover:scale-110 opacity-0 group-hover:opacity-100"
            aria-label="<?php esc_attr_e('Next Slide', 'konderntang'); ?>">
            <i class="ph ph-caret-right text-2xl"></i>
        </button>

        <!-- Dots Indicator -->
        <div class="absolute bottom-4 left-1/2 -translate-x-1/2 z-20 flex gap-2">
            <?php
            for ($i = 0; $i < $total_slides; $i++) {
                $dot_class = $i === 0 ? 'bg-white' : 'bg-white/50 hover:bg-white/75';
                ?>
                <button
                    class="hero-dot w-2.5 h-2.5 rounded-full <?php echo esc_attr($dot_class); ?> transition-all duration-300"
                    data-slide="<?php echo esc_attr($i); ?>"
                    aria-label="<?php printf(esc_attr__('Go to slide %d', 'konderntang'), $i + 1); ?>"></button>
                <?php
            }
            ?>
        </div>
    <?php endif; ?>
</header>