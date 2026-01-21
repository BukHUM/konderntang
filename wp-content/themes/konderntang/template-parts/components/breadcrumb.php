<?php
/**
 * Breadcrumb Component
 *
 * @package KonDernTang
 * @since 1.0.0
 */

$breadcrumb_config = require KONDERN_THEME_DIR . '/config/breadcrumb-config.php';

// Determine current page
$current_page = 'home';
if ( is_single() ) {
    $current_page = 'single';
} elseif ( is_archive() ) {
    $current_page = 'archive';
} elseif ( is_search() ) {
    $current_page = 'search';
} elseif ( is_404() ) {
    $current_page = '404';
}

$config = isset( $breadcrumb_config[ $current_page ] ) ? $breadcrumb_config[ $current_page ] : array( 'show' => false );
?>

<div id="breadcrumb" class="bg-white border-b border-gray-200 shadow-sm sticky top-20 z-40 <?php echo $config['show'] ? '' : 'hidden'; ?>">
    <div class="container mx-auto px-4 py-3">
        <nav class="flex items-center gap-2 text-sm" aria-label="<?php esc_attr_e( 'Breadcrumb', 'konderntang' ); ?>">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="text-gray-500 hover:text-primary transition flex items-center gap-1">
                <i class="ph ph-house text-base"></i>
                <span><?php esc_html_e( 'หน้าแรก', 'konderntang' ); ?></span>
            </a>
            <?php if ( $config['show'] ) : ?>
                <span id="breadcrumb-separator">
                    <i class="ph ph-caret-right text-gray-400"></i>
                </span>
                <span id="breadcrumb-current" class="text-gray-700 font-medium">
                    <?php
                    if ( is_single() ) {
                        echo esc_html( get_the_title() );
                    } else {
                        echo esc_html( $config['text'] );
                    }
                    ?>
                </span>
            <?php endif; ?>
        </nav>
    </div>
</div>
