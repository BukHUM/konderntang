<?php
/**
 * Post Card Component
 *
 * @package KonDernTang
 * @since 1.0.0
 */

// Get post object from passed variable or global
if (!isset($post)) {
    global $post;
}

if (!$post || !($post instanceof WP_Post)) {
    return;
}

// Extract other variables
$show_badge = isset($show_badge) ? $show_badge : false;
$badge_text = isset($badge_text) ? $badge_text : '';
$card_size = isset($card_size) ? $card_size : 'normal'; // normal, large, small
$show_excerpt = isset($show_excerpt) ? $show_excerpt : true;
$show_meta = isset($show_meta) ? $show_meta : true;

// Get post data using the $post object directly
$post_id = $post->ID;
$post_title = get_the_title($post);
$post_permalink = get_permalink($post);
$post_date = get_the_date('', $post);
$post_excerpt = has_excerpt($post) ? get_the_excerpt($post) : $post->post_content;

$thumbnail = get_the_post_thumbnail_url($post_id, 'konderntang-card');
if (!$thumbnail) {
    $thumbnail = KONDERN_THEME_URI . '/assets/images/placeholder.webp';
}

$card_classes = 'bg-white rounded-xl shadow-sm hover:shadow-lg transition overflow-hidden border border-gray-100 cursor-pointer group';
if ($card_size === 'large') {
    $card_classes .= ' md:col-span-2';
}
?>

<article class="<?php echo esc_attr($card_classes); ?> h-full flex flex-col"
    onclick="window.location.href='<?php echo esc_url($post_permalink); ?>'">
    <div class="relative h-48 overflow-hidden bg-gray-200 shrink-0">
        <img src="<?php echo esc_url($thumbnail); ?>" alt="<?php echo esc_attr($post_title); ?>"
            class="w-full h-full object-cover transition duration-500 group-hover:scale-110">
        <?php if ($show_badge && $badge_text): ?>
            <span
                class="absolute top-3 left-3 bg-white/90 text-dark text-xs font-bold px-2 py-1 rounded backdrop-blur-sm flex items-center gap-1">
                <?php if ($show_badge): ?><i class="ph ph-sparkle text-xs"></i><?php endif; ?>
                <?php echo esc_html($badge_text); ?>
            </span>
        <?php endif; ?>
    </div>
    <div class="p-5 flex flex-col flex-1">
        <h3
            class="font-heading font-semibold text-lg text-dark mb-2 leading-snug group-hover:text-primary transition line-clamp-2">
            <a href="<?php echo esc_url($post_permalink); ?>"><?php echo esc_html($post_title); ?></a>
        </h3>
        <?php if ($show_excerpt): ?>
            <p class="text-gray-500 text-sm mb-3 line-clamp-2">
                <?php echo esc_html(wp_trim_words($post_excerpt, 15)); ?>
            </p>
        <?php endif; ?>
        <?php if ($show_meta): ?>
            <div class="flex items-center gap-2 text-gray-400 text-xs mt-auto">
                <i class="ph ph-calendar"></i> <?php echo esc_html($post_date); ?>
                <span class="mx-1">•</span>
                <i class="ph ph-eye"></i> <?php echo esc_html(konderntang_get_post_view_count($post_id)); ?>
                <?php esc_html_e('views', 'konderntang'); ?>
            </div>
        <?php endif; ?>
    </div>
</article>