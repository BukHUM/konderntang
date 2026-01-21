<?php
/**
 * The template for displaying archive pages
 *
 * @package TrendToday
 * @since 1.0.0
 */

get_header();
?>

<!-- Category Header -->
<header class="bg-white border-b border-gray-200 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="text-sm text-gray-500 mb-2">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="hover:text-accent"><?php _e( 'หน้าแรก', 'trendtoday' ); ?></a>
                    <i class="fas fa-chevron-right text-xs mx-1"></i>
                    <?php
                    if ( is_category() ) {
                        $cat_title = single_cat_title( '', false );
                        $cat_title = strip_tags( $cat_title );
                        $cat_title = preg_replace( '/^(หมวดหมู่|Category):\s*/i', '', $cat_title );
                        $cat_title = trim( $cat_title );
                        echo '<span>' . esc_html( $cat_title ) . '</span>';
                    } elseif ( is_tag() ) {
                        $tag_title = single_tag_title( '', false );
                        $tag_title = strip_tags( $tag_title );
                        $tag_title = preg_replace( '/^(แท็ก|Tag):\s*/i', '', $tag_title );
                        $tag_title = trim( $tag_title );
                        echo '<span>' . esc_html( $tag_title ) . '</span>';
                    } else {
                        $archive_title = get_the_archive_title();
                        $archive_title = strip_tags( $archive_title );
                        $archive_title = preg_replace( '/^(หมวดหมู่|Category|แท็ก|Tag|Archive):\s*/i', '', $archive_title );
                        $archive_title = trim( $archive_title );
                        echo '<span>' . esc_html( $archive_title ) . '</span>';
                    }
                    ?>
                </div>
                <h1 class="text-4xl font-bold text-gray-900">
                    <?php
                    if ( is_category() ) {
                        echo esc_html( single_cat_title( '', false ) );
                    } elseif ( is_tag() ) {
                        echo esc_html( single_tag_title( '', false ) );
                    } elseif ( is_author() ) {
                        the_author();
                    } elseif ( is_date() ) {
                        echo get_the_date();
                    } else {
                        _e( 'Archive', 'trendtoday' );
                    }
                    ?>
                    <span class="text-accent"><?php _e( 'Update', 'trendtoday' ); ?></span>
                </h1>
                <?php
                $description = get_the_archive_description();
                if ( $description ) :
                    ?>
                    <p class="text-gray-500 mt-2 font-light"><?php echo wp_kses_post( $description ); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>

<main id="main-content" class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">

    <!-- Hero Section (Slider) -->
    <?php get_template_part( 'template-parts/hero-section' ); ?>

    <div class="flex flex-col lg:flex-row gap-10">

        <!-- Main Feed -->
        <div class="lg:w-2/3">
            <div class="flex justify-between items-end mb-6">
                <h2 class="text-2xl font-bold text-gray-900 border-l-4 border-accent pl-3">
                    <?php _e( 'ข่าวล่าสุด', 'trendtoday' ); ?>
                </h2>
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
                            <?php if ( is_category() ) : ?>
                                data-cat-id="<?php echo esc_attr( get_queried_object_id() ); ?>"
                            <?php elseif ( is_tag() ) : ?>
                                data-tag-id="<?php echo esc_attr( get_queried_object_id() ); ?>"
                            <?php endif; ?>
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
