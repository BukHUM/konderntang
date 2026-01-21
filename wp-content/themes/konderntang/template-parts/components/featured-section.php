<?php
/**
 * Featured Section Component
 *
 * @package KonDernTang
 * @since 1.0.0
 */

$featured_enabled = konderntang_get_option( 'featured_section_enabled', true );
if ( ! $featured_enabled ) {
    return;
}

$featured_posts_count = absint( konderntang_get_option( 'featured_posts_count', 3 ) );
$featured_category = konderntang_get_option( 'featured_section_category', '' );

$args = array(
    'posts_per_page' => $featured_posts_count,
    'post_status'    => 'publish',
);

if ( ! empty( $featured_category ) ) {
    $args['category__in'] = array( absint( $featured_category ) );
}

$featured_posts = get_posts( $args );

if ( empty( $featured_posts ) ) {
    return;
}
?>

<section class="mb-16">
    <div class="bg-gradient-to-br from-purple-50 to-blue-50 rounded-2xl p-8 border border-purple-100">
        <div class="flex items-center gap-3 mb-6">
            <div class="bg-gradient-to-br from-purple-500 to-blue-500 p-3 rounded-xl">
                <i class="ph ph-sparkle text-white text-2xl"></i>
            </div>
            <div>
                <h2 class="font-heading font-bold text-2xl text-dark"><?php esc_html_e( 'แนะนำสำหรับคุณ', 'konderntang' ); ?></h2>
                <p class="text-sm text-gray-600"><?php esc_html_e( 'เนื้อหาที่คัดสรรมาเป็นพิเศษตามความสนใจของคุณ', 'konderntang' ); ?></p>
            </div>
        </div>
        <div class="grid md:grid-cols-3 gap-6">
            <?php
            foreach ( $featured_posts as $post ) :
                setup_postdata( $post );
                konderntang_get_component( 'post-card', array( 'post' => $post, 'show_badge' => true, 'badge_text' => esc_html__( 'AI แนะนำ', 'konderntang' ) ) );
            endforeach;
            wp_reset_postdata();
            ?>
        </div>
    </div>
</section>
