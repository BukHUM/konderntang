<?php
/**
 * The template for displaying all single posts
 *
 * @package KonDernTang
 * @since 1.0.0
 */

get_header();
?>

<main id="main" class="site-main">
    <?php
    while (have_posts()):
        the_post();
        ?>
        <!-- Post Hero -->
        <?php if (has_post_thumbnail()): ?>
            <div class="relative h-[60vh] w-full">
                <?php the_post_thumbnail('konderntang-hero', array('class' => 'w-full h-full object-cover')); ?>
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent"></div>
                <div class="absolute bottom-0 left-0 w-full p-8 md:p-16 text-white container mx-auto">
                    <div class="flex items-center gap-3 mb-4 text-sm font-medium flex-wrap">
                        <?php
                        $categories = get_the_category();
                        if (!empty($categories)) {
                            ?>
                            <span class="bg-secondary px-3 py-1 rounded-full"><?php echo esc_html($categories[0]->name); ?></span>
                            <?php
                        }
                        ?>
                        <span><i class="ph ph-calendar"></i> <?php echo esc_html(get_the_date()); ?></span>
                        <span><i class="ph ph-user"></i> <?php esc_html_e('โดย', 'konderntang'); ?>
                            <?php the_author(); ?></span>
                    </div>
                    <h1 class="text-3xl md:text-5xl font-heading font-bold leading-tight drop-shadow-lg">
                        <?php the_title(); ?>
                    </h1>
                </div>
            </div>
        <?php else: ?>
            <div class="container mx-auto px-4 py-8">
                <h1 class="text-3xl md:text-5xl font-heading font-bold text-dark mb-4"><?php the_title(); ?></h1>
                <div class="flex items-center gap-3 text-sm text-gray-600 mb-6">
                    <span><i class="ph ph-calendar"></i> <?php echo esc_html(get_the_date()); ?></span>
                    <span><i class="ph ph-user"></i> <?php esc_html_e('โดย', 'konderntang'); ?>
                 <?php the_author(); ?></span>
                </div>
            </div>
        <?php endif; ?>

        <?php
        $sidebar_position = konderntang_get_option('layout_single_sidebar', 'right');
        $sidebar_class = $sidebar_position === 'left' ? 'order-first' : '';
        $main_class = $sidebar_position === 'none' ? 'lg:col-span-3' : 'lg:col-span-2';
        ?>
        <div class="container mx-auto px-4 py-12 grid grid-cols-1 lg:grid-cols-3 gap-12">
            <!-- Article Content -->
            <article
                class="<?php echo esc_attr($main_class); ?> prose prose-lg prose-blue max-w-none font-sans text-gray-700">
                <?php
                get_template_part('template-parts/content/content', 'single');
                ?>
            </article>

            <!-- Sidebar -->
            <?php if ($sidebar_position !== 'none'): ?>
                <aside class="space-y-8 <?php echo esc_attr($sidebar_class); ?>">
                            <?php
                            // Author Box
                            konderntang_get_component('author-box');

                            // Recently Viewed
                            konderntang_get_component('recently-viewed');

                            // Personalized Recommendations Widget
                            if (is_active_sidebar('sidebar-single')) {
                                dynamic_sidebar('sidebar-single');
                            } else {
                                // Fallback: show related posts if widget area is not active
                                konderntang_get_component('related-posts', array('post_id' => get_the_ID()));
                            }

                            // Sticky Ad/Promo
                            konderntang_get_component('sticky-ad');
                            ?>
                        </aside>
                <?php endif; ?>
            </div>

            <?php
            // You May Also Like Section (AI-powered recommendations)
            konderntang_get_component('you-may-also-like');
            ?>
            <?php
    endwhile;
    ?>
</main>

<?php
get_footer();
