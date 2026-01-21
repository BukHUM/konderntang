<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 *
 * @package TrendToday
 * @since 1.0.0
 */

get_header();
?>

<main id="main-content" class="site-main flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">
    <?php
    if ( have_posts() ) :
        ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <?php
            while ( have_posts() ) :
                the_post();
                get_template_part( 'template-parts/news-card' );
            endwhile;
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
    else :
        get_template_part( 'template-parts/content', 'none' );
    endif;
    ?>
</main>

<?php
get_footer();
