<?php
/**
 * Related Posts Component
 *
 * @package KonDernTang
 * @since 1.0.0
 */

$post_id = isset( $post_id ) ? absint( $post_id ) : get_the_ID();
$posts_count = isset( $posts_count ) ? absint( $posts_count ) : 3;

$categories = wp_get_post_categories( $post_id );
if ( empty( $categories ) ) {
    return;
}

$args = array(
    'posts_per_page' => $posts_count,
    'post__not_in'   => array( $post_id ),
    'category__in'   => $categories,
    'post_status'    => 'publish',
);

$related_posts = get_posts( $args );

if ( empty( $related_posts ) ) {
    return;
}
?>

<div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm">
    <h4 class="font-heading font-bold text-lg mb-4 border-l-4 border-secondary pl-3">
        <?php esc_html_e( 'บทความแนะนำ', 'konderntang' ); ?>
    </h4>
    <div class="space-y-4">
        <?php
        foreach ( $related_posts as $post ) :
            setup_postdata( $post );
            $thumbnail = get_the_post_thumbnail_url( $post->ID, 'thumbnail' );
            if ( ! $thumbnail ) {
                $thumbnail = KONDERN_THEME_URI . '/assets/images/placeholder-thumb.jpg';
            }
            ?>
            <a href="<?php echo esc_url( get_permalink() ); ?>" class="flex gap-3 cursor-pointer group">
                <img src="<?php echo esc_url( $thumbnail ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" class="w-20 h-20 object-cover rounded-lg flex-shrink-0">
                <div class="flex-1 min-w-0">
                    <h5 class="font-bold text-sm text-dark group-hover:text-primary leading-tight line-clamp-2">
                        <?php echo esc_html( get_the_title() ); ?>
                    </h5>
                    <span class="text-xs text-gray-400 mt-1 block"><?php echo esc_html( get_the_date() ); ?></span>
                </div>
            </a>
            <?php
        endforeach;
        wp_reset_postdata();
        ?>
    </div>
</div>
