<?php
/**
 * Category Section Component
 *
 * @package KonDernTang
 * @since 1.0.0
 */

$category_slug = isset($category) ? $category : '';
$section_title = isset($title) ? $title : '';
$posts_count = isset($posts_count) ? absint($posts_count) : 6;

if (empty($category_slug)) {
    return;
}

$category_obj = get_category_by_slug($category_slug);
if (!$category_obj) {
    return;
}

$args = array(
    'posts_per_page' => $posts_count,
    'category__in' => array($category_obj->term_id),
    'post_status' => 'publish',
);

$category_posts = get_posts($args);

if (empty($category_posts)) {
    return;
}

$section_subtitle = isset($subtitle) ? $subtitle : '';
$section_link_text = isset($link_text) ? $link_text : esc_html__('ดูทั้งหมด', 'konderntang');

if (empty($section_title)) {
    $section_title = $category_obj->name;
}
?>

<?php
// Determine accent color
$accent_color_class = isset($accent_color) ? $accent_color : 'border-primary';
$text_color_class = str_replace('border-', 'text-', $accent_color_class);
// Handle secondary color specifically if passed as text-secondary (sometimes mapped differently)
// But for simplicity assume border-X matches text-X logic in Tailwind config or custom classes
?>

<section class="mb-16">
    <div
        class="flex flex-col md:flex-row md:items-end justify-between mb-8 border-l-4 <?php echo esc_attr($accent_color_class); ?> pl-4">
        <div>
            <h2 class="text-3xl font-heading font-bold text-dark"><?php echo esc_html($section_title); ?></h2>
            <?php if (!empty($section_subtitle)): ?>
                <p class="text-gray-500 mt-1"><?php echo esc_html($section_subtitle); ?></p>
            <?php endif; ?>
        </div>
        <a href="<?php echo esc_url(get_category_link($category_obj->term_id)); ?>"
            class="<?php echo esc_attr($text_color_class); ?> hover:opacity-80 font-medium flex items-center gap-2 mt-4 md:mt-0">
            <?php echo esc_html($section_link_text); ?> <i class="ph ph-arrow-right inline-block"></i>
        </a>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <?php
        foreach ($category_posts as $post):
            setup_postdata($post);
            konderntang_get_component('post-card', array('post' => $post));
        endforeach;
        wp_reset_postdata();
        ?>
    </div>
</section>