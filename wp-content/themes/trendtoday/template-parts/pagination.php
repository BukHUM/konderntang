<?php
/**
 * Template part for displaying pagination
 *
 * @package TrendToday
 * @since 1.0.0
 */

if ( ! function_exists( 'trendtoday_pagination' ) ) {
    function trendtoday_pagination() {
        global $wp_query;

        if ( $wp_query->max_num_pages <= 1 ) {
            return;
        }

        $paged = get_query_var( 'paged' ) ? absint( get_query_var( 'paged' ) ) : 1;
        $max   = intval( $wp_query->max_num_pages );

        // Add current page to the array
        if ( $paged >= 1 ) {
            $links[] = $paged;
        }

        // Add the pages around the current page to the array
        if ( $paged >= 3 ) {
            $links[] = $paged - 1;
            $links[] = $paged - 2;
        }

        if ( ( $paged + 2 ) <= $max ) {
            $links[] = $paged + 2;
            $links[] = $paged + 1;
        }

        echo '<nav class="pagination flex justify-center items-center gap-2 mt-10" aria-label="' . esc_attr__( 'Pagination', 'trendtoday' ) . '">';

        // Previous Page Link
        if ( get_previous_posts_link() ) {
            printf( '<a href="%s" class="px-4 py-2 rounded-lg bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 transition">%s</a>',
                esc_url( get_previous_posts_page_link() ),
                '<i class="fas fa-chevron-left mr-1"></i>' . __( 'ก่อนหน้า', 'trendtoday' )
            );
        }

        // Page Links
        foreach ( range( 1, $max ) as $link ) {
            if ( in_array( $link, $links ) ) {
                if ( $link == $paged ) {
                    printf( '<span class="px-4 py-2 rounded-lg bg-accent text-white font-bold">%s</span>', $link );
                } else {
                    printf( '<a href="%s" class="px-4 py-2 rounded-lg bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 transition">%s</a>',
                        esc_url( get_pagenum_link( $link ) ),
                        $link
                    );
                }
            }
        }

        // Next Page Link
        if ( get_next_posts_link() ) {
            printf( '<a href="%s" class="px-4 py-2 rounded-lg bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 transition">%s</a>',
                esc_url( get_next_posts_page_link() ),
                __( 'ถัดไป', 'trendtoday' ) . ' <i class="fas fa-chevron-right ml-1"></i>'
            );
        }

        echo '</nav>';
    }
}

// Use WordPress default pagination if available, otherwise use custom
if ( function_exists( 'the_posts_pagination' ) ) {
    the_posts_pagination( array(
        'mid_size'  => 2,
        'prev_text' => '<i class="fas fa-chevron-left"></i> ' . __( 'ก่อนหน้า', 'trendtoday' ),
        'next_text' => __( 'ถัดไป', 'trendtoday' ) . ' <i class="fas fa-chevron-right"></i>',
    ) );
} else {
    trendtoday_pagination();
}
