<?php
/**
 * Category Section Component
 *
 * @package KonDernTang
 * @since 1.0.0
 */

$category_slug = isset( $category ) ? $category : '';
$section_title = isset( $title ) ? $title : '';
$posts_count = isset( $posts_count ) ? absint( $posts_count ) : 6;

if ( empty( $category_slug ) ) {
    return;
}

$category_obj = get_category_by_slug( $category_slug );
if ( ! $category_obj ) {
    return;
}

$args = array(
    'posts_per_page' => $posts_count,
    'category__in'   => array( $category_obj->term_id ),
    'post_status'    => 'publish',
);

$category_posts = get_posts( $args );

if ( empty( $category_posts ) ) {
    return;
}

if ( empty( $section_title ) ) {
    $section_title = $category_obj->name;
}
?>

<section class="mb-16">
    <div class="flex items-center justify-between mb-6">
        <h2 class="font-heading font-bold text-3xl text-dark"><?php echo esc_html( $section_title ); ?></h2>
        <a href="<?php echo esc_url( get_category_link( $category_obj->term_id ) ); ?>" class="text-primary hover:text-blue-600 font-medium flex items-center gap-2">
            <?php esc_html_e( 'ดูทั้งหมด', 'konderntang' ); ?> <i class="ph ph-arrow-right"></i>
        </a>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php
        foreach ( $category_posts as $post ) :
            setup_postdata( $post );
            konderntang_get_component( 'post-card', array( 'post' => $post ) );
        endforeach;
        wp_reset_postdata();
        ?>
    </div>
</section>
