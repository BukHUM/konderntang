<?php
/**
 * Template part for displaying pagination
 *
 * @package KonDernTang
 * @since 1.0.0
 */

the_posts_pagination(
    array(
        'mid_size'  => 2,
        'prev_text' => esc_html__( '&laquo; Previous', 'konderntang' ),
        'next_text' => esc_html__( 'Next &raquo;', 'konderntang' ),
    )
);
