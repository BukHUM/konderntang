<?php
/**
 * Recently Viewed Component
 *
 * @package KonDernTang
 * @since 1.0.0
 */

$recently_viewed_enabled = konderntang_get_option( 'recently_viewed_enabled', true );
if ( ! $recently_viewed_enabled ) {
    return;
}
?>

<div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm">
    <h4 class="font-heading font-bold text-lg mb-4 border-l-4 border-purple-500 pl-3 flex items-center gap-2">
        <i class="ph ph-clock-clockwise text-purple-500"></i>
        <?php esc_html_e( 'ดูล่าสุด', 'konderntang' ); ?>
    </h4>
    <div id="recently-viewed-list-single" class="space-y-4">
        <p class="text-sm text-gray-500 text-center py-4"><?php esc_html_e( 'ยังไม่มีประวัติการดู', 'konderntang' ); ?></p>
    </div>
</div>
