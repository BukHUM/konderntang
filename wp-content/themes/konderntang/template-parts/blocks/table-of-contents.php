<?php
/**
 * Template part for displaying Table of Contents
 *
 * @package KonDernTang
 * @since 1.0.0
 */

// Check if TOC is enabled for this post
$toc_enabled = get_post_meta( get_the_ID(), '_konderntang_toc_enabled', true );
if ( 'yes' !== $toc_enabled ) {
    return;
}

// Get TOC settings
$toc_position = get_post_meta( get_the_ID(), '_konderntang_toc_position', true );
if ( empty( $toc_position ) ) {
    $toc_position = 'before_content';
}

// Only display if position matches
$current_position = isset( $args['position'] ) ? $args['position'] : 'before_content';
if ( $current_position !== $toc_position ) {
    return;
}

// Display TOC
echo konderntang_display_toc( array(
    'toc_title' => esc_html__( 'สารบัญ', 'konderntang' ),
) );
