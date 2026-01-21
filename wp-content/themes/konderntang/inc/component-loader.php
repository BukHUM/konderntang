<?php
/**
 * Component Loader
 *
 * Loads reusable theme components
 *
 * @package KonDernTang
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Get component
 *
 * Loads a component from template-parts/components/
 *
 * @param string $component_name Component name (without .php extension)
 * @param array  $args           Arguments to pass to component
 * @return void
 */
function konderntang_get_component( $component_name, $args = array() ) {
    $component_path = KONDERN_THEME_DIR . '/template-parts/components/' . $component_name . '.php';
    
    if ( ! file_exists( $component_path ) ) {
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            error_log( sprintf( 'Component not found: %s', $component_name ) );
        }
        return;
    }
    
    // Extract args for use in component
    extract( $args, EXTR_SKIP );
    
    // Include component
    include $component_path;
}

/**
 * Check if component exists
 *
 * @param string $component_name Component name
 * @return bool
 */
function konderntang_component_exists( $component_name ) {
    $component_path = KONDERN_THEME_DIR . '/template-parts/components/' . $component_name . '.php';
    return file_exists( $component_path );
}
