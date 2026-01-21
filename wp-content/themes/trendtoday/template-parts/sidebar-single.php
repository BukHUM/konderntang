<?php
/**
 * Template part for displaying sidebar on single post pages
 *
 * @package TrendToday
 * @since 1.0.0
 */
?>

<aside class="lg:w-1/3 space-y-8">
    <?php
    // Table of Contents - Sidebar
    $toc_enabled = get_option( 'trendtoday_toc_enabled', '1' );
    $toc_position = get_option( 'trendtoday_toc_position', 'top' );
    if ( $toc_enabled === '1' && $toc_position === 'sidebar' ) :
    ?>
        <div class="mb-8">
            <?php get_template_part( 'template-parts/table-of-contents' ); ?>
        </div>
    <?php endif; ?>
    
    <?php 
    // Display widgets from sidebar-1 if any are active
    if ( is_active_sidebar( 'sidebar-1' ) ) {
        dynamic_sidebar( 'sidebar-1' );
    } else {
        // Only show default sidebar content if no widgets are active
        // Check if Recent Posts widget is enabled in settings
        if ( trendtoday_is_widget_enabled( 'recent_posts' ) ) :
            ?>
            
            <!-- Latest News Sidebar (for single post pages) -->
        <?php
        // Get latest posts excluding current post
        $current_post_id = get_the_ID();
        $latest_query = new WP_Query( array(
            'post_type'      => 'post',
            'posts_per_page' => 3,
            'post_status'    => 'publish',
            'post__not_in'   => array( $current_post_id ),
            'orderby'        => 'date',
            'order'          => 'DESC',
            'ignore_sticky_posts' => true,
        ) );
        
        if ( $latest_query->have_posts() ) :
        ?>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 sticky top-24">
            <h3 class="font-bold text-xl mb-4 border-l-4 border-accent pl-3">
                <?php _e( 'ข่าวน่าสนใจ', 'trendtoday' ); ?>
            </h3>
            <div class="space-y-6">
                <?php
                while ( $latest_query->have_posts() ) :
                    $latest_query->the_post();
                    $post_obj = $latest_query->post;
                    $post_title = $post_obj->post_title;
                    $post_permalink = trendtoday_fix_url( get_permalink( $post_obj->ID ) );
                    $post_date = get_post_time( 'U', false, $post_obj->ID );
                    $thumbnail_id = get_post_thumbnail_id( $post_obj->ID );
                    
                    // Get first category
                    $categories = get_the_category( $post_obj->ID );
                    $category_name = ! empty( $categories ) ? $categories[0]->name : '';
                    
                    // Skip if no title
                    if ( empty( $post_title ) ) {
                        continue;
                    }
                    ?>
                    <a href="<?php echo esc_url( $post_permalink ); ?>" 
                       class="flex gap-4 group cursor-pointer">
                        <?php if ( $thumbnail_id ) : ?>
                            <div class="w-20 h-20 flex-shrink-0 rounded-lg overflow-hidden">
                                <?php echo get_the_post_thumbnail( $post_obj->ID, 'trendtoday-thumbnail', array(
                                    'class' => 'w-full h-full object-cover group-hover:scale-110 transition',
                                    'alt'   => esc_attr( $post_title ),
                                    'loading' => 'lazy',
                                ) ); ?>
                            </div>
                        <?php else : ?>
                            <div class="w-20 h-20 flex-shrink-0 rounded-lg overflow-hidden bg-gray-200 flex items-center justify-center">
                                <i class="fas fa-image text-gray-400 text-2xl"></i>
                            </div>
                        <?php endif; ?>
                        <div>
                            <h4 class="text-sm font-bold text-gray-900 group-hover:text-accent transition line-clamp-2 mb-1">
                                <?php echo esc_html( $post_title ); ?>
                            </h4>
                            <span class="text-xs text-gray-400">
                                <?php if ( $category_name ) : ?>
                                    <?php echo esc_html( $category_name ); ?> • 
                                <?php endif; ?>
                                <?php 
                                $time_diff = human_time_diff( $post_date, current_time( 'timestamp' ) );
                                // Convert to Thai format if needed
                                echo esc_html( $time_diff ); ?> <?php _e( 'ที่แล้ว', 'trendtoday' ); ?>
                            </span>
                        </div>
                    </a>
                <?php
                endwhile;
                wp_reset_postdata();
                ?>
            </div>
        </div>
        <?php 
        endif; // End check for have_posts
        endif; // End check for recent_posts_enabled
    } // End check for is_active_sidebar
    ?>
</aside>
