<?php
/**
 * Template part for displaying a message that posts cannot be found
 *
 * @package TrendToday
 * @since 1.0.0
 */
?>

<section class="no-results not-found text-center py-16">
    <div class="max-w-md mx-auto">
        <i class="fas fa-newspaper text-gray-300 text-6xl mb-6"></i>
        <h2 class="text-2xl font-bold text-gray-900 mb-4">
            <?php _e( 'ไม่พบข่าว', 'trendtoday' ); ?>
        </h2>
        <p class="text-gray-500 mb-8">
            <?php _e( 'ขออภัย ไม่พบเนื้อหาที่คุณกำลังมองหา ลองค้นหาด้วยคำอื่น', 'trendtoday' ); ?>
        </p>
        
        <?php if ( is_home() && current_user_can( 'publish_posts' ) ) : ?>
            <p class="mb-4">
                <?php
                printf(
                    __( 'พร้อมที่จะเผยแพร่โพสต์แรกของคุณแล้ว? <a href="%1$s">เริ่มที่นี่</a>.', 'trendtoday' ),
                    esc_url( admin_url( 'post-new.php' ) )
                );
                ?>
            </p>
        <?php elseif ( is_search() ) : ?>
            <div class="mb-8">
                <?php get_search_form(); ?>
            </div>
        <?php else : ?>
            <p class="mb-4">
                <?php _e( 'ดูเหมือนว่าเราไม่พบสิ่งที่คุณกำลังมองหา ลองค้นหาดู', 'trendtoday' ); ?>
            </p>
            <div class="mb-8">
                <?php get_search_form(); ?>
            </div>
        <?php endif; ?>
    </div>
</section>
