<?php
/**
 * The sidebar template file
 *
 * @package KonDernTang
 * @since 1.0.0
 */

// Determine which sidebar to use
$sidebar_id = 'sidebar-1';

if ( is_front_page() && is_active_sidebar( 'homepage-sidebar' ) ) {
    $sidebar_id = 'homepage-sidebar';
} elseif ( is_single() && is_active_sidebar( 'single-sidebar' ) ) {
    $sidebar_id = 'single-sidebar';
} elseif ( ( is_archive() || is_home() ) && is_active_sidebar( 'archive-sidebar' ) ) {
    $sidebar_id = 'archive-sidebar';
}

if ( ! is_active_sidebar( $sidebar_id ) ) {
    // Fallback to primary sidebar
    if ( ! is_active_sidebar( 'sidebar-1' ) ) {
        return;
    }
    $sidebar_id = 'sidebar-1';
}
?>

<aside id="secondary" class="widget-area">
    <?php dynamic_sidebar( $sidebar_id ); ?>
</aside><!-- #secondary -->
