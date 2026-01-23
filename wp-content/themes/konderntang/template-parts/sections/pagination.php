<?php
/**
 * Template part for displaying pagination
 *
 * @package KonDernTang
 * @since 1.0.0
 */

the_posts_pagination(
    array(
        'mid_size' => 2,
        'prev_text' => '<i class="ph ph-arrow-left"></i> ' . esc_html__('Prev', 'konderntang'),
        'next_text' => esc_html__('Next', 'konderntang') . ' <i class="ph ph-arrow-right"></i>',
        'screen_reader_text' => ' ', // Hide the H2 "Posts navigation"
        'class' => 'konderntang-pagination',
    )
);
