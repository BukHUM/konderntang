<?php
/**
 * Template for displaying video news archives
 *
 * @package TrendToday
 * @since 1.0.0
 */

get_header();
?>

<main id="main-content" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <header class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">
            <?php
            if ( is_tax( 'video_category' ) ) {
                single_term_title();
            } else {
                _e( 'Video News', 'trendtoday' );
            }
            ?>
        </h1>
        <?php
        $description = get_the_archive_description();
        if ( $description ) :
            ?>
            <div class="text-gray-600">
                <?php echo wp_kses_post( $description ); ?>
            </div>
        <?php endif; ?>
    </header>

    <?php if ( have_posts() ) : ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <?php
            while ( have_posts() ) :
                the_post();
                get_template_part( 'template-parts/news-card' );
            endwhile;
            ?>
        </div>

        <?php get_template_part( 'template-parts/pagination' ); ?>
    <?php else : ?>
        <?php get_template_part( 'template-parts/content-none' ); ?>
    <?php endif; ?>
</main>

<?php
get_sidebar();
get_footer();
