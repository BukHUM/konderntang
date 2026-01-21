<?php
/**
 * The template for displaying search results
 *
 * @package TrendToday
 * @since 1.0.0
 */

get_header();
?>

<!-- Search Header -->
<header class="bg-gray-900 text-white py-12">
    <div class="max-w-3xl mx-auto px-4 text-center">
        <h1 class="text-3xl font-bold mb-6"><?php _e( 'ค้นหาข่าวและเทรนด์ล่าสุด', 'trendtoday' ); ?></h1>
        <form method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="relative">
            <input type="search" 
                   name="s" 
                   value="<?php echo get_search_query(); ?>"
                   class="w-full px-6 py-4 rounded-full text-gray-900 focus:outline-none focus:ring-4 focus:ring-accent/50 text-lg shadow-lg pl-14"
                   placeholder="<?php echo esc_attr( get_option( 'trendtoday_search_placeholder', __( 'พิมพ์คำค้นหา...', 'trendtoday' ) ) ); ?>">
            <i class="fas fa-search absolute left-6 top-1/2 -translate-y-1/2 text-gray-400 text-xl"></i>
            <button type="submit"
                    class="absolute right-3 top-1/2 -translate-y-1/2 bg-accent hover:bg-orange-600 text-white px-6 py-2 rounded-full font-bold text-sm transition">
                <?php _e( 'ค้นหา', 'trendtoday' ); ?>
            </button>
        </form>
        <div class="mt-4 flex justify-center gap-2 text-sm text-gray-400">
            <span><?php _e( 'คำค้นยอดฮิต:', 'trendtoday' ); ?></span>
            <?php
            $popular_tags = get_tags( array(
                'orderby' => 'count',
                'order'   => 'DESC',
                'number'  => 3,
            ) );
            if ( ! empty( $popular_tags ) ) :
                foreach ( $popular_tags as $tag ) :
                    ?>
                    <a href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>" 
                       class="text-white hover:text-accent underline decoration-dotted">
                        <?php echo esc_html( $tag->name ); ?>
                    </a>
                    <?php
                    if ( $tag !== end( $popular_tags ) ) {
                        echo ',';
                    }
                endforeach;
            endif;
            ?>
        </div>
    </div>
</header>

<main id="main-content" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">

    <div class="flex flex-col lg:flex-row gap-10">

        <!-- Search Results -->
        <div class="lg:w-3/4">

            <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-800">
                    <?php
                    printf(
                        __( 'ผลการค้นหาสำหรับ %s', 'trendtoday' ),
                        '<span class="text-accent">"' . get_search_query() . '"</span>'
                    );
                    ?>
                    <span class="text-sm font-normal text-gray-500 ml-2">
                        (<?php echo $wp_query->found_posts; ?> <?php _e( 'รายการ', 'trendtoday' ); ?>)
                    </span>
                </h2>
            </div>

            <?php if ( have_posts() ) : 
                $search_results_layout = get_option( 'trendtoday_search_results_layout', 'list' );
                $layout_class = $search_results_layout === 'grid' ? 'grid grid-cols-1 md:grid-cols-2 gap-6' : 'space-y-6';
            ?>
                <div class="<?php echo esc_attr( $layout_class ); ?>">
                    <?php
                    while ( have_posts() ) :
                        the_post();
                        $post_permalink = trendtoday_fix_url( get_permalink() );
                        
                        if ( $search_results_layout === 'grid' ) :
                            // Grid Layout
                            get_template_part( 'template-parts/news-card' );
                        else :
                            // List Layout
                        ?>
                            <article class="flex flex-col sm:flex-row bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md transition border border-gray-100 mb-6 group cursor-pointer"
                                     onclick="window.location.href='<?php echo esc_url( $post_permalink ); ?>'">
                                <div class="sm:w-1/3 relative overflow-hidden h-48 sm:h-auto">
                                    <?php if ( has_post_thumbnail() ) : ?>
                                        <?php the_post_thumbnail( 'trendtoday-card', array(
                                            'class' => 'w-full h-full object-cover group-hover:scale-105 transition duration-500',
                                        ) ); ?>
                                    <?php endif; ?>
                                </div>
                                <div class="sm:w-2/3 p-6 flex flex-col justify-center">
                                    <div class="flex items-center gap-2 mb-2">
                                        <?php
                                        $categories = get_the_category();
                                        if ( ! empty( $categories ) ) :
                                            $category = $categories[0];
                                            $cat_color = get_term_meta( $category->term_id, 'category_color', true ) ?: '#3B82F6';
                                            ?>
                                            <span class="text-xs font-bold text-white px-2 py-1 rounded"
                                                  style="background-color: <?php echo esc_attr( $cat_color ); ?>">
                                                <?php echo esc_html( $category->name ); ?>
                                            </span>
                                        <?php endif; ?>
                                        <span class="text-xs text-gray-400">
                                            <?php echo human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) . ' ที่แล้ว'; ?>
                                        </span>
                                    </div>
                                    <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-accent transition leading-snug">
                                        <a href="<?php echo esc_url( $post_permalink ); ?>"><?php the_title(); ?></a>
                                    </h3>
                                    <?php if ( has_excerpt() ) : ?>
                                        <p class="text-gray-500 text-sm line-clamp-2">
                                            <?php echo wp_trim_words( get_the_excerpt(), 20, '...' ); ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </article>
                        <?php
                        endif;
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
                                data-search="<?php echo esc_attr( get_search_query() ); ?>"
                                aria-label="<?php _e( 'โหลดข่าวเพิ่มเติม', 'trendtoday' ); ?>">
                                <span class="relative z-10"><?php _e( 'โหลดข่าวเพิ่มเติม', 'trendtoday' ); ?></span>
                                <i class="fas fa-arrow-down ml-2 relative z-10"></i>
                            </button>
                        </div>
                        <?php
                    endif;
                endif;
                ?>
            <?php else : ?>
                <div class="text-center py-12">
                    <i class="fas fa-search text-gray-300 text-5xl mb-4"></i>
                    <p class="text-gray-500 text-lg mb-2"><?php _e( 'ไม่พบผลการค้นหา', 'trendtoday' ); ?></p>
                    <p class="text-gray-400 text-sm mb-6">
                        <?php _e( 'ลองใช้คำค้นหาอื่น หรือตรวจสอบการสะกด', 'trendtoday' ); ?>
                    </p>
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" 
                       class="inline-block bg-accent text-white px-6 py-3 rounded-full hover:bg-orange-600 transition">
                        <?php _e( 'กลับหน้าแรก', 'trendtoday' ); ?>
                    </a>
                </div>
            <?php endif; ?>

        </div>

        <!-- Sidebar -->
        <?php get_template_part( 'template-parts/sidebar' ); ?>

    </div>
</main>

<?php
get_footer();
?>
