<?php
/**
 * Featured Section Component
 *
 * @package KonDernTang
 * @since 1.0.0
 */

$featured_enabled = konderntang_get_option('featured_section_enabled', true);
if (!$featured_enabled) {
    return;
}

$featured_posts_count = absint(konderntang_get_option('featured_posts_count', 3));

// Get effective user ID (logged-in user or visitor cookie)
$user_id = konderntang_get_effective_user_id();

// Get AI-powered recommended posts
$featured_posts = konderntang_get_recommended_posts($user_id, $featured_posts_count);

if (empty($featured_posts)) {
    return;
}
?>

<section class="mb-16 relative group/section">
    <div
        class="bg-gradient-to-br from-purple-50 to-blue-50 rounded-2xl p-8 border border-purple-100 relative overflow-hidden">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <div class="bg-gradient-to-br from-purple-500 to-blue-500 p-3 rounded-xl shadow-lg shadow-purple-200">
                    <i class="ph ph-sparkle text-white text-2xl"></i>
                </div>
                <div>
                    <h2 class="font-heading font-bold text-2xl text-dark">
                        <?php echo isset($title) ? esc_html($title) : esc_html__('แนะนำสำหรับคุณ', 'konderntang'); ?>
                    </h2>
                    <p class="text-sm text-gray-600">
                        <?php echo isset($subtitle) ? esc_html($subtitle) : esc_html__('เนื้อหาที่คัดสรรมาเป็นพิเศษตามความสนใจของคุณ', 'konderntang'); ?>
                    </p>
                </div>
            </div>

            <!-- Navigation Buttons (only if > 3 items) -->
            <?php if (count($featured_posts) > 3): ?>
                <div class="hidden md:flex gap-2">
                    <button
                        class="featured-prev w-10 h-10 rounded-full bg-white border border-purple-100 flex items-center justify-center text-purple-600 hover:bg-purple-50 transition shadow-sm">
                        <i class="ph ph-caret-left text-lg"></i>
                    </button>
                    <button
                        class="featured-next w-10 h-10 rounded-full bg-white border border-purple-100 flex items-center justify-center text-purple-600 hover:bg-purple-50 transition shadow-sm">
                        <i class="ph ph-caret-right text-lg"></i>
                    </button>
                </div>
            <?php endif; ?>
        </div>

        <!-- Slider Container -->
        <div
            class="featured-slider-container flex gap-6 overflow-x-auto snap-x snap-mandatory scrollbar-hide pb-4 -mx-4 px-4 md:mx-0 md:px-0">
            <?php
            foreach ($featured_posts as $featured_post):
                // Card Wrapper for Slider
                ?>
                <div class="min-w-[85%] md:min-w-[calc(33.333%-16px)] snap-start">
                    <?php
                    konderntang_get_component('post-card', array(
                        'post' => $featured_post,
                        'show_badge' => true,
                        'badge_text' => esc_html__('AI แนะนำ', 'konderntang'),
                        'card_size' => 'normal' // Featured size forced to normal for slider
                    ));
                    ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<style>
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }

    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>