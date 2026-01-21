<?php
/**
 * Template part for displaying sidebar
 *
 * @package TrendToday
 * @since 1.0.0
 */
?>

<aside class="lg:w-1/3 space-y-8 sticky-sidebar" aria-label="Sidebar content">
    <?php 
    // Popular Posts Widget (only if enabled)
    if ( trendtoday_is_widget_enabled( 'popular_posts' ) ) :
        // Try views first, fallback to date if no results
        $popular_query = trendtoday_get_popular_posts( 4, 'views' );
        
        // If no posts with views, fallback to latest posts
        if ( ! $popular_query->have_posts() ) {
            $popular_query = trendtoday_get_popular_posts( 4, 'date' );
        }
        
        if ( $popular_query->have_posts() ) :
        ?>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-300">
            <h3 class="font-bold text-xl mb-5 flex items-center gap-2">
                <i class="fas fa-fire text-accent"></i>
                <?php _e( 'ยอดนิยม', 'trendtoday' ); ?>
            </h3>
            <div class="space-y-4" role="list" aria-label="<?php _e( 'Popular articles', 'trendtoday' ); ?>">
                <?php
                // Store current post to restore later
                global $post;
                $current_post = $post;
                
                $index = 0;
                while ( $popular_query->have_posts() ) :
                    $popular_query->the_post();
                    $index++;
                    
                    // Get post data directly from query post
                    $post_obj = $popular_query->post;
                    $post_title = $post_obj->post_title;
                    $post_permalink = trendtoday_fix_url( get_permalink( $post_obj->ID ) );
                    $post_date = get_post_time( 'U', false, $post_obj->ID );
                    $thumbnail_id = get_post_thumbnail_id( $post_obj->ID );
                    
                    // Skip if no title
                    if ( empty( $post_title ) ) {
                        continue;
                    }
                    ?>
                    <a href="<?php echo esc_url( $post_permalink ); ?>" 
                       class="popular-item flex gap-4 items-start group p-2 rounded-lg hover:bg-gray-50 transition-colors duration-200"
                       role="listitem"
                       aria-label="<?php echo esc_attr( sprintf( __( 'Popular article %d: %s', 'trendtoday' ), $index, $post_title ) ); ?>">
                        <span class="popular-number text-2xl font-bold text-gray-200 group-hover:text-accent transition-all duration-300 flex-shrink-0">
                            <?php echo str_pad( $index, 2, '0', STR_PAD_LEFT ); ?>
                        </span>
                        <div class="flex-grow">
                            <h4 class="text-sm font-medium text-gray-700 group-hover:text-accent transition-colors line-clamp-2 leading-snug">
                                <?php echo esc_html( $post_title ); ?>
                            </h4>
                        </div>
                    </a>
                <?php
                endwhile;
                
                // Restore original post
                $post = $current_post;
                wp_reset_postdata();
                ?>
            </div>
        </div>
        <?php endif; // End check for have_posts ?>
    <?php endif; // End check for popular_posts_enabled ?>
    
    <?php 
    // Display additional widgets from sidebar-1 if any are active
    if ( is_active_sidebar( 'sidebar-1' ) ) {
        dynamic_sidebar( 'sidebar-1' );
    }
    ?>

</aside>
