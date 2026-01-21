<?php
/**
 * Back to Top Component
 *
 * @package KonDernTang
 * @since 1.0.0
 */
?>

<button 
    id="back-to-top" 
    onclick="scrollToTop()" 
    class="fixed bottom-8 right-8 bg-primary text-white p-4 rounded-full shadow-lg hover:bg-blue-600 transition-all duration-300 opacity-0 pointer-events-none z-50 group"
    aria-label="<?php esc_attr_e( 'กลับขึ้นด้านบน', 'konderntang' ); ?>"
>
    <i class="ph ph-arrow-up text-2xl"></i>
</button>
