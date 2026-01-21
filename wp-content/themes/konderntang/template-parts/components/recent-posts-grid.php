<?php
/**
 * Recent Posts Grid Component
 *
 * @package KonDernTang
 * @since 1.0.0
 */

$recent_posts_count = absint( konderntang_get_option( 'recent_posts_count', 6 ) );

$args = array(
    'posts_per_page' => $recent_posts_count,
    'post_status'    => 'publish',
);

$recent_posts = get_posts( $args );

if ( empty( $recent_posts ) ) {
    return;
}
?>

<section class="mb-16">
    <h2 class="font-heading font-bold text-3xl text-dark mb-6"><?php esc_html_e( 'บทความล่าสุด', 'konderntang' ); ?></h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php
        foreach ( $recent_posts as $post ) :
            setup_postdata( $post );
            konderntang_get_component( 'post-card', array( 'post' => $post ) );
        endforeach;
        wp_reset_postdata();
        ?>
    </div>
</section>
