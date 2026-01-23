<?php
/**
 * Recent Posts Grid Component
 *
 * @package KonDernTang
 * @since 1.0.0
 */

// Handle arguments
$posts_count = isset($posts_count) ? absint($posts_count) : absint(konderntang_get_option('recent_posts_count', 6));
$category_id = isset($category) ? absint($category) : 0;
$title = isset($title) ? $title : esc_html__('บทความล่าสุด', 'konderntang');
$subtitle = isset($subtitle) ? $subtitle : '';
$show_link = isset($show_link) ? $show_link : true;
$link_text = isset($link_text) ? $link_text : esc_html__('ดูทั้งหมด', 'konderntang');

// Determine accent color
$accent_color_class = isset($accent_color) ? $accent_color : 'border-primary';
$text_color_class = str_replace('border-', 'text-', $accent_color_class);

$args = array(
    'posts_per_page' => $posts_count,
    'post_status' => 'publish',
);

$category_link = '';
if (!empty($category_id)) {
    $args['cat'] = $category_id;
    $category_link = get_category_link($category_id);
}

$recent_posts = get_posts($args);

if (empty($recent_posts)) {
    return;
}
?>

<section class="mb-16">
    <div
        class="flex flex-col md:flex-row md:items-end justify-between mb-8 border-l-4 <?php echo esc_attr($accent_color_class); ?> pl-4">
        <div>
            <h2 class="text-3xl font-heading font-bold text-dark"><?php echo esc_html($title); ?></h2>
            <?php if (!empty($subtitle)): ?>
                <p class="text-gray-500 mt-1"><?php echo esc_html($subtitle); ?></p>
            <?php endif; ?>
        </div>
        <?php if ($show_link && !empty($category_link)): ?>
            <a href="<?php echo esc_url($category_link); ?>"
                class="<?php echo esc_attr($text_color_class); ?> hover:opacity-80 font-medium flex items-center gap-2 mt-4 md:mt-0">
                <?php echo esc_html($link_text); ?> <i class="ph ph-arrow-right inline-block"></i>
            </a>
        <?php endif; ?>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <?php
        foreach ($recent_posts as $post):
            setup_postdata($post);
            konderntang_get_component('post-card', array('post' => $post));
        endforeach;
        wp_reset_postdata();
        ?>
    </div>
</section>