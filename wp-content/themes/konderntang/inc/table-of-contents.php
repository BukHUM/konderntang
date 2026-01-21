<?php
/**
 * Table of Contents Function
 *
 * @package KonDernTang
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Generate table of contents from post content
 *
 * @param string $content Post content.
 * @param array  $args    Optional arguments.
 * @return string Content with TOC.
 */
function konderntang_generate_toc( $content, $args = array() ) {
    $defaults = array(
        'min_headings' => 2,
        'heading_levels' => array( 'h2', 'h3', 'h4' ),
        'toc_title' => esc_html__( 'สารบัญ', 'konderntang' ),
        'toc_position' => 'before_content',
        'auto_insert' => false,
    );

    $args = wp_parse_args( $args, $defaults );

    // Extract headings from content
    $headings = konderntang_extract_headings( $content, $args['heading_levels'] );

    if ( count( $headings ) < $args['min_headings'] ) {
        return $content;
    }

    // Generate TOC HTML
    $toc_html = konderntang_build_toc_html( $headings, $args );

    // Insert TOC into content if auto_insert is enabled
    if ( $args['auto_insert'] && 'before_content' === $args['toc_position'] ) {
        $content = $toc_html . $content;
    } elseif ( $args['auto_insert'] && 'after_first_heading' === $args['toc_position'] ) {
        // Insert after first heading
        $first_heading_pos = strpos( $content, '<' . $args['heading_levels'][0] );
        if ( false !== $first_heading_pos ) {
            $content = substr_replace( $content, $toc_html, $first_heading_pos, 0 );
        }
    }

    return $content;
}

/**
 * Extract headings from content
 *
 * @param string $content Post content.
 * @param array  $levels  Heading levels to extract.
 * @return array Array of headings with id, text, level.
 */
function konderntang_extract_headings( $content, $levels = array( 'h2', 'h3', 'h4' ) ) {
    $headings = array();
    $pattern = '/<(' . implode( '|', $levels ) . ')[^>]*>(.*?)<\/\1>/i';

    if ( preg_match_all( $pattern, $content, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE ) ) {
        $counter = 1;
        foreach ( $matches as $match ) {
            $tag = strtolower( $match[1][0] );
            $text = strip_tags( $match[2][0] );
            $level = (int) substr( $tag, 1 );

            // Generate unique ID
            $id = 'toc-' . sanitize_title( $text ) . '-' . $counter;
            $counter++;

            $headings[] = array(
                'id' => $id,
                'text' => $text,
                'level' => $level,
            );
        }
    }

    return $headings;
}

/**
 * Build TOC HTML
 *
 * @param array $headings Array of headings.
 * @param array $args     Arguments.
 * @return string TOC HTML.
 */
function konderntang_build_toc_html( $headings, $args = array() ) {
    if ( empty( $headings ) ) {
        return '';
    }

    $toc_title = ! empty( $args['toc_title'] ) ? $args['toc_title'] : esc_html__( 'สารบัญ', 'konderntang' );
    $toc_class = 'konderntang-toc';

    $html = '<div class="' . esc_attr( $toc_class ) . '">';
    $html .= '<div class="konderntang-toc-header">';
    $html .= '<h3 class="konderntang-toc-title">' . esc_html( $toc_title ) . '</h3>';
    $html .= '<button class="konderntang-toc-toggle" aria-label="' . esc_attr__( 'Toggle table of contents', 'konderntang' ) . '">';
    $html .= '<span class="konderntang-toc-toggle-icon"></span>';
    $html .= '</button>';
    $html .= '</div>';
    $html .= '<nav class="konderntang-toc-nav" role="navigation" aria-label="' . esc_attr__( 'Table of contents', 'konderntang' ) . '">';
    $html .= '<ul class="konderntang-toc-list">';

    $current_level = 0;
    foreach ( $headings as $heading ) {
        $level = $heading['level'];
        $id = $heading['id'];
        $text = $heading['text'];

        // Close previous levels if needed
        if ( $current_level > 0 && $level > $current_level ) {
            // Opening new nested level
            $html .= '<ul class="konderntang-toc-list konderntang-toc-list-level-' . esc_attr( $level ) . '">';
        } elseif ( $current_level > 0 && $level < $current_level ) {
            // Closing previous levels
            for ( $i = $current_level; $i > $level; $i-- ) {
                $html .= '</ul>';
            }
        }

        $html .= '<li class="konderntang-toc-item konderntang-toc-item-level-' . esc_attr( $level ) . '">';
        $html .= '<a href="#' . esc_attr( $id ) . '" class="konderntang-toc-link">' . esc_html( $text ) . '</a>';
        $html .= '</li>';

        $current_level = $level;
    }

    // Close remaining levels
    for ( $i = $current_level; $i > 0; $i-- ) {
        $html .= '</ul>';
    }

    $html .= '</nav>';
    $html .= '</div>';

    return $html;
}

/**
 * Display table of contents
 *
 * @param array $args Optional arguments.
 * @return string TOC HTML.
 */
function konderntang_display_toc( $args = array() ) {
    global $post;

    if ( ! is_singular() || ! isset( $post->post_content ) ) {
        return '';
    }

    $defaults = array(
        'min_headings' => 2,
        'heading_levels' => array( 'h2', 'h3', 'h4' ),
        'toc_title' => esc_html__( 'สารบัญ', 'konderntang' ),
    );

    $args = wp_parse_args( $args, $defaults );

    // Extract headings
    $headings = konderntang_extract_headings( $post->post_content, $args['heading_levels'] );

    if ( count( $headings ) < $args['min_headings'] ) {
        return '';
    }

    // Build and return TOC HTML
    return konderntang_build_toc_html( $headings, $args );
}

/**
 * Filter post content to auto-insert TOC
 *
 * @param string $content Post content.
 * @return string Modified content.
 */
function konderntang_auto_insert_toc( $content ) {
    if ( ! is_singular( 'post' ) ) {
        return $content;
    }

    // Check if TOC is enabled for this post
    $toc_enabled = get_post_meta( get_the_ID(), '_konderntang_toc_enabled', true );
    if ( 'yes' !== $toc_enabled ) {
        return $content;
    }

    // Get TOC settings
    $toc_position = get_post_meta( get_the_ID(), '_konderntang_toc_position', true );
    if ( empty( $toc_position ) ) {
        $toc_position = 'before_content';
    }

    $args = array(
        'min_headings' => 2,
        'heading_levels' => array( 'h2', 'h3', 'h4' ),
        'toc_title' => esc_html__( 'สารบัญ', 'konderntang' ),
        'toc_position' => $toc_position,
        'auto_insert' => true,
    );

    return konderntang_generate_toc( $content, $args );
}
add_filter( 'the_content', 'konderntang_auto_insert_toc', 10 );
