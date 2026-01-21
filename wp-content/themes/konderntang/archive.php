<?php
/**
 * The template for displaying archive pages
 *
 * @package KonDernTang
 * @since 1.0.0
 */

get_header();
?>

<main id="main" class="site-main">
    <?php
    if ( have_posts() ) :
        ?>
        <!-- Category Header -->
        <div class="relative bg-dark text-white py-16 mb-8 overflow-hidden">
            <?php
            $category_image = '';
            if ( is_category() ) {
                $category = get_queried_object();
                $category_image_id = get_term_meta( $category->term_id, 'category_image', true );
                if ( $category_image_id ) {
                    $category_image = wp_get_attachment_image_url( $category_image_id, 'full' );
                }
            }
            if ( $category_image ) {
                ?>
                <img src="<?php echo esc_url( $category_image ); ?>" alt="<?php echo esc_attr( get_the_archive_title() ); ?>" class="absolute inset-0 w-full h-full object-cover opacity-30">
                <?php
            }
            ?>
            <div class="container mx-auto px-4 text-center relative z-10">
                <span class="text-secondary font-bold tracking-wider uppercase text-sm"><?php esc_html_e( 'Category', 'konderntang' ); ?></span>
                <?php the_archive_title( '<h1 class="text-4xl font-heading font-bold mt-2">', '</h1>' ); ?>
                <?php the_archive_description( '<p class="text-gray-300 mt-2 max-w-2xl mx-auto">', '</p>' ); ?>
            </div>
        </div>

        <?php
        $sidebar_position = konderntang_get_option( 'layout_archive_sidebar', 'right' );
        $sidebar_class = $sidebar_position === 'left' ? 'order-first' : '';
        $main_class = $sidebar_position === 'none' ? 'lg:col-span-4' : 'lg:col-span-3';
        ?>
        <div class="container mx-auto px-4 grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Sidebar -->
            <?php if ( $sidebar_position !== 'none' ) : ?>
            <aside class="hidden lg:block lg:col-span-1 space-y-6 <?php echo esc_attr( $sidebar_class ); ?>">
                <?php
                // Category Filter
                konderntang_get_component( 'category-filter' );

                // Newsletter Widget
                if ( is_active_sidebar( 'archive-sidebar' ) ) {
                    dynamic_sidebar( 'archive-sidebar' );
                }
                ?>
            </aside>
            <?php endif; ?>

            <!-- Main Grid -->
            <div class="<?php echo esc_attr( $main_class ); ?>">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <?php
                    while ( have_posts() ) :
                        the_post();
                        konderntang_get_component( 'post-card', array( 'post' => $post, 'show_excerpt' => false ) );
                    endwhile;
                    ?>
                </div>

                <?php get_template_part( 'template-parts/sections/pagination' ); ?>
            </div>
        </div>
        <?php
    else :
        get_template_part( 'template-parts/content/content', 'none' );
    endif;
    ?>
</main>

<?php
get_footer();
