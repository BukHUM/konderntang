<?php
/**
 * Template part for displaying trending tags
 *
 * @package TrendToday
 * @since 1.0.0
 */

// Get trending tags
$tags = trendtoday_get_trending_tags( 10 );
?>

<?php if ( ! empty( $tags ) ) : ?>
    <div class="bg-white/60 backdrop-blur-sm rounded-xl p-5 shadow-sm border-2 border-gray-200 mb-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
            <span class="text-accent font-bold whitespace-nowrap text-base flex items-center gap-2">
                <i class="fas fa-bolt text-accent animate-pulse"></i>
                <?php _e( 'มาแรง:', 'trendtoday' ); ?>
            </span>
            <div class="flex flex-wrap gap-2.5 flex-1" role="list" aria-label="<?php _e( 'Trending topics', 'trendtoday' ); ?>">
                <?php foreach ( $tags as $tag ) : ?>
                    <a href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>"
                       class="trending-tag inline-block bg-gradient-to-r from-gray-100 to-gray-50 hover:from-accent hover:to-orange-600 hover:text-white text-gray-700 text-sm px-4 py-2 rounded-full whitespace-nowrap transition-all duration-300 font-medium shadow-sm hover:shadow-md transform hover:scale-105 border border-gray-200 hover:border-transparent"
                       role="listitem"
                       aria-label="<?php echo esc_attr( sprintf( __( 'Trending topic: %s', 'trendtoday' ), $tag->name ) ); ?>">
                        #<?php echo esc_html( $tag->name ); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endif; ?>
