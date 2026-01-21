<?php
/**
 * Trending Tags Component
 *
 * @package KonDernTang
 * @since 1.0.0
 */

$tags_count = absint( konderntang_get_option( 'trending_tags_count', 10 ) );

$tags = get_tags(
    array(
        'orderby' => 'count',
        'order'   => 'DESC',
        'number'  => $tags_count,
    )
);

if ( empty( $tags ) ) {
    return;
}
?>

<section class="mb-16">
    <h2 class="font-heading font-bold text-2xl text-dark mb-4"><?php esc_html_e( 'แท็กยอดนิยม', 'konderntang' ); ?></h2>
    <div class="flex flex-wrap gap-2">
        <?php
        foreach ( $tags as $tag ) :
            ?>
            <a href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>" class="inline-flex items-center gap-2 bg-gray-100 hover:bg-primary hover:text-white text-gray-700 px-4 py-2 rounded-full transition text-sm font-medium">
                <i class="ph ph-hash"></i> <?php echo esc_html( $tag->name ); ?>
                <span class="text-xs opacity-75">(<?php echo esc_html( $tag->count ); ?>)</span>
            </a>
            <?php
        endforeach;
        ?>
    </div>
</section>
