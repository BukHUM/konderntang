<?php
/**
 * Template part for displaying news article card
 *
 * @package TrendToday
 * @since 1.0.0
 *
 * @var WP_Post $post Current post object
 */

if ( ! isset( $post ) ) {
    global $post;
}

$categories = get_the_category();
$category   = ! empty( $categories ) ? $categories[0] : null;
$cat_color  = $category ? ( get_term_meta( $category->term_id, 'category_color', true ) ?: '#3B82F6' ) : '#3B82F6';
?>

<article class="article-card bg-white rounded-xl overflow-hidden shadow-sm border border-gray-100 hover:shadow-md transition duration-300 flex flex-col cursor-pointer"
         onclick="window.location.href='<?php echo esc_url( trendtoday_fix_url( get_permalink() ) ); ?>'"
         role="article"
         aria-label="<?php echo esc_attr( get_the_title() ); ?>">
    <div class="relative overflow-hidden h-48">
        <?php if ( has_post_thumbnail() ) : ?>
            <a href="<?php echo esc_url( trendtoday_fix_url( get_permalink() ) ); ?>">
                <?php 
                $thumbnail_id = get_post_thumbnail_id();
                if ( $thumbnail_id ) {
                    echo wp_get_attachment_image( 
                        $thumbnail_id, 
                        'trendtoday-card', 
                        false, 
                        array(
                            'class' => 'article-img w-full h-full object-cover transition duration-500',
                            'alt'   => esc_attr( get_the_title() ),
                            'loading' => 'lazy',
                            'srcset' => wp_get_attachment_image_srcset( $thumbnail_id, 'trendtoday-card' ),
                            'sizes' => '(max-width: 768px) 100vw, (max-width: 1024px) 50vw, 33vw',
                        ) 
                    );
                }
                ?>
            </a>
        <?php endif; ?>
        <?php if ( $category ) : ?>
            <span class="category-badge absolute top-4 left-4 text-white text-[10px] font-bold px-3 py-1.5 rounded-full uppercase tracking-wide shadow-lg"
                  style="background-color: <?php echo esc_attr( $cat_color ); ?>">
                <?php echo esc_html( $category->name ); ?>
            </span>
        <?php endif; ?>
    </div>
    <div class="p-5 flex flex-col flex-grow">
        <h3 class="text-lg font-bold text-gray-900 mb-2 leading-snug line-clamp-2 hover:text-accent cursor-pointer transition-colors">
            <a href="<?php echo esc_url( trendtoday_fix_url( get_permalink() ) ); ?>">
                <?php the_title(); ?>
            </a>
        </h3>
        <?php if ( has_excerpt() || get_the_excerpt() ) : ?>
            <p class="text-gray-500 text-sm mb-4 line-clamp-2 flex-grow font-light leading-relaxed">
                <?php echo wp_trim_words( get_the_excerpt(), 15, '...' ); ?>
            </p>
        <?php endif; ?>
        <div class="flex justify-between items-center text-xs text-gray-400 border-t border-gray-50 pt-3 mt-auto">
            <span class="font-medium text-gray-500 flex items-center">
                <i class="far fa-user mr-1"></i>
                <?php echo esc_html( trendtoday_get_author_name() ); ?>
            </span>
            <span class="flex items-center">
                <i class="far fa-clock mr-1"></i>
                <?php echo human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) . ' ที่แล้ว'; ?>
            </span>
        </div>
    </div>
</article>
