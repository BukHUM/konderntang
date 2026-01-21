<?php
/**
 * The main template file for blog/news archive
 *
 * @package TrendToday
 * @since 1.0.0
 */

get_header();
?>

<main id="main-content" class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">

    <!-- Hero Section (Breaking News) -->
    <?php get_template_part( 'template-parts/hero-section' ); ?>

    <!-- Latest News Grid -->
    <div class="flex flex-col lg:flex-row gap-10">

        <!-- Main Feed -->
        <div class="lg:w-2/3">
            <div class="flex justify-between items-end mb-6">
                <h2 class="text-2xl font-bold text-gray-900 border-l-4 border-accent pl-3">
                    <?php _e( 'ข่าวล่าสุด', 'trendtoday' ); ?>
                </h2>
                <a href="<?php echo esc_url( get_permalink( get_option( 'page_for_posts' ) ) ); ?>" 
                   class="text-sm text-gray-500 hover:text-accent">
                    <?php _e( 'ดูทั้งหมด', 'trendtoday' ); ?> <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6" id="news-grid">
                <?php
                if ( have_posts() ) :
                    while ( have_posts() ) :
                        the_post();
                        get_template_part( 'template-parts/news-card' );
                    endwhile;
                else :
                    get_template_part( 'template-parts/content', 'none' );
                endif;
                ?>
            </div>

            <?php
            global $wp_query;
            $pagination_type = get_option( 'trendtoday_pagination_type', 'load_more' );
            
            if ( $wp_query->max_num_pages > 1 ) :
                if ( $pagination_type === 'pagination' ) :
                    // Show Pagination
                    get_template_part( 'template-parts/pagination' );
                else :
                    // Show Load More Button
                    ?>
                    <div class="mt-10 text-center">
                        <button
                            class="bg-white border-2 border-gray-300 text-gray-700 font-medium py-3 px-8 rounded-full hover:bg-gray-50 hover:text-black hover:border-accent transition-all duration-200 shadow-sm hover:shadow-md w-full md:w-auto btn-primary"
                            id="load-more-btn"
                            data-page="1"
                            aria-label="<?php _e( 'โหลดข่าวเพิ่มเติม', 'trendtoday' ); ?>">
                            <span class="relative z-10"><?php _e( 'โหลดข่าวเพิ่มเติม', 'trendtoday' ); ?></span>
                            <i class="fas fa-arrow-down ml-2 relative z-10"></i>
                        </button>
                    </div>
                    <?php
                endif;
            endif;
            ?>
        </div>

        <!-- Sidebar -->
        <?php get_template_part( 'template-parts/sidebar' ); ?>

    </div>

</main>

<?php
get_footer();
?>
