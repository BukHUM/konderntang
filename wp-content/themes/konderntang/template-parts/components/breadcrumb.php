<?php
/**
 * Breadcrumb Component
 *
 * @package KonDernTang
 * @since 1.0.0
 */

// Get breadcrumbs settings
$breadcrumbs_enabled = konderntang_get_option( 'breadcrumbs_enabled', true );
$breadcrumbs_home_text = konderntang_get_option( 'breadcrumbs_home_text', esc_html__( 'หน้าแรก', 'konderntang' ) );
$breadcrumbs_separator = konderntang_get_option( 'breadcrumbs_separator', 'caret-right' );

// Determine if breadcrumbs should show for current page
$show_breadcrumb = false;
if ( $breadcrumbs_enabled ) {
    if ( is_single() && konderntang_get_option( 'breadcrumbs_show_single', true ) ) {
        $show_breadcrumb = true;
    } elseif ( is_archive() && konderntang_get_option( 'breadcrumbs_show_archive', false ) ) {
        $show_breadcrumb = true;
    } elseif ( is_page() && konderntang_get_option( 'breadcrumbs_show_page', false ) ) {
        $show_breadcrumb = true;
    } elseif ( is_search() && konderntang_get_option( 'breadcrumbs_show_search', false ) ) {
        $show_breadcrumb = true;
    } elseif ( is_404() && konderntang_get_option( 'breadcrumbs_show_404', false ) ) {
        $show_breadcrumb = true;
    }
}

// Get separator icon class
$separator_icons = array(
    'caret-right' => 'ph-caret-right',
    'slash' => 'ph-slash',
    'arrow-right' => 'ph-arrow-right',
    'chevron-right' => 'ph-chevron-right',
);
$separator_icon = isset( $separator_icons[ $breadcrumbs_separator ] ) ? $separator_icons[ $breadcrumbs_separator ] : 'ph-caret-right';

// Get current page text
$current_text = '';
if ( is_single() ) {
    $current_text = get_the_title();
} elseif ( is_archive() ) {
    $current_text = get_the_archive_title();
} elseif ( is_page() ) {
    $current_text = get_the_title();
} elseif ( is_search() ) {
    $current_text = sprintf( esc_html__( 'ค้นหา: %s', 'konderntang' ), get_search_query() );
} elseif ( is_404() ) {
    $current_text = esc_html__( 'ไม่พบหน้า', 'konderntang' );
}
?>

<?php if ( $show_breadcrumb ) : ?>
<div id="breadcrumb" class="bg-white border-b border-gray-200 shadow-sm sticky top-20 z-40">
    <div class="container mx-auto px-4 py-3">
        <nav class="flex items-center gap-2 text-sm" aria-label="<?php esc_attr_e( 'Breadcrumb', 'konderntang' ); ?>">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="text-gray-500 hover:text-primary transition flex items-center gap-1">
                <i class="ph ph-house text-base"></i>
                <span><?php echo esc_html( $breadcrumbs_home_text ); ?></span>
            </a>
            <?php if ( ! empty( $current_text ) ) : ?>
                <span id="breadcrumb-separator">
                    <i class="ph <?php echo esc_attr( $separator_icon ); ?> text-gray-400"></i>
                </span>
                <span id="breadcrumb-current" class="text-gray-700 font-medium">
                    <?php echo esc_html( $current_text ); ?>
                </span>
            <?php endif; ?>
        </nav>
    </div>
</div>
<?php endif; ?>
