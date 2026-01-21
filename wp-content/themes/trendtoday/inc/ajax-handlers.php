<?php
/**
 * AJAX Handlers
 *
 * @package TrendToday
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Load more posts via AJAX
 */
function trendtoday_load_more_posts() {
    check_ajax_referer( 'trendtoday-nonce', 'nonce' );

    $page = isset( $_POST['page'] ) ? absint( $_POST['page'] ) : 1;
    $posts_per_page = isset( $_POST['posts_per_page'] ) ? absint( $_POST['posts_per_page'] ) : get_option( 'posts_per_page' );
    $category = isset( $_POST['category'] ) ? sanitize_text_field( $_POST['category'] ) : '';
    $search_query = isset( $_POST['search'] ) ? sanitize_text_field( $_POST['search'] ) : '';
    $tag_id = isset( $_POST['tag_id'] ) ? absint( $_POST['tag_id'] ) : 0;
    $cat_id = isset( $_POST['cat_id'] ) ? absint( $_POST['cat_id'] ) : 0;

    $args = array(
        'post_type'      => 'post',
        'posts_per_page' => $posts_per_page,
        'paged'          => $page,
        'post_status'    => 'publish',
    );

    // Handle category filter (from category filters)
    if ( ! empty( $category ) && $category !== 'all' ) {
        $args['cat'] = absint( $category );
    }
    
    // Handle archive category
    if ( $cat_id > 0 ) {
        $args['cat'] = $cat_id;
    }
    
    // Handle archive tag
    if ( $tag_id > 0 ) {
        $args['tag_id'] = $tag_id;
    }
    
    // Handle search query
    if ( ! empty( $search_query ) ) {
        $args['s'] = $search_query;
    }

    $query = new WP_Query( $args );

    ob_start();

    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            get_template_part( 'template-parts/news-card' );
        }
        wp_reset_postdata();
    }

    $html = ob_get_clean();

    wp_send_json_success( array(
        'html' => $html,
        'has_more' => $query->max_num_pages > $page,
        'next_page' => $page + 1,
    ) );
}
add_action( 'wp_ajax_load_more_posts', 'trendtoday_load_more_posts' );
add_action( 'wp_ajax_nopriv_load_more_posts', 'trendtoday_load_more_posts' );

/**
 * Filter posts by category via AJAX
 */
function trendtoday_filter_posts() {
    check_ajax_referer( 'trendtoday-nonce', 'nonce' );

    $category = isset( $_POST['category'] ) ? sanitize_text_field( $_POST['category'] ) : 'all';
    $posts_per_page = isset( $_POST['posts_per_page'] ) ? absint( $_POST['posts_per_page'] ) : get_option( 'posts_per_page' );

    $args = array(
        'post_type'      => 'post',
        'posts_per_page' => $posts_per_page,
        'post_status'    => 'publish',
    );

    if ( ! empty( $category ) && $category !== 'all' ) {
        $args['cat'] = absint( $category );
    }

    $query = new WP_Query( $args );

    ob_start();

    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            get_template_part( 'template-parts/news-card' );
        }
        wp_reset_postdata();
    } else {
        get_template_part( 'template-parts/content', 'none' );
    }

    $html = ob_get_clean();

    wp_send_json_success( array(
        'html' => $html,
        'found_posts' => $query->found_posts,
        'max_pages' => $query->max_num_pages,
    ) );
}
add_action( 'wp_ajax_filter_posts', 'trendtoday_filter_posts' );
add_action( 'wp_ajax_nopriv_filter_posts', 'trendtoday_filter_posts' );

/**
 * Search suggestions via AJAX
 */
function trendtoday_search_suggestions() {
    check_ajax_referer( 'trendtoday-nonce', 'nonce' );
    
    // Rate limiting
    if ( ! trendtoday_check_rate_limit( 'search_suggestions', 30 ) ) {
        wp_send_json_error( array( 'message' => __( 'Too many requests. Please try again later.', 'trendtoday' ) ) );
    }

    // Get search settings
    $search_enabled = get_option( 'trendtoday_search_enabled', '1' );
    $search_suggestions_enabled = get_option( 'trendtoday_search_suggestions_enabled', '1' );
    $search_min_length = get_option( 'trendtoday_search_min_length', 2 );
    $search_suggestions_count = get_option( 'trendtoday_search_suggestions_count', 5 );
    $search_post_types = get_option( 'trendtoday_search_post_types', array( 'post' ) );
    $search_fields = get_option( 'trendtoday_search_fields', array( 'title', 'content' ) );
    $search_suggestions_display = get_option( 'trendtoday_search_suggestions_display', array( 'image', 'excerpt' ) );
    $search_exclude_categories = get_option( 'trendtoday_search_exclude_categories', array() );

    if ( $search_enabled !== '1' || $search_suggestions_enabled !== '1' ) {
        wp_send_json_success( array( 'suggestions' => array() ) );
    }

    $search_term = isset( $_POST['search'] ) ? trendtoday_sanitize_search_query( $_POST['search'] ) : '';

    if ( strlen( $search_term ) < $search_min_length ) {
        wp_send_json_success( array( 'suggestions' => array() ) );
    }

    // Build search query
    $args = array(
        'post_type'      => $search_post_types,
        'posts_per_page' => $search_suggestions_count,
        's'              => $search_term,
        'post_status'    => 'publish',
    );

    // Exclude categories
    if ( ! empty( $search_exclude_categories ) ) {
        $args['category__not_in'] = $search_exclude_categories;
    }

    $query = new WP_Query( $args );

    $suggestions = array();

    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $suggestion = array(
                'title' => get_the_title(),
                'url'   => trendtoday_fix_url( get_permalink() ),
                'type'  => get_post_type(),
            );

            // Add optional fields based on settings
            if ( in_array( 'image', $search_suggestions_display ) && has_post_thumbnail() ) {
                $suggestion['image'] = get_the_post_thumbnail_url( get_the_ID(), 'thumbnail' );
            }

            if ( in_array( 'excerpt', $search_suggestions_display ) ) {
                $suggestion['excerpt'] = wp_trim_words( get_the_excerpt(), 15, '...' );
            }

            if ( in_array( 'date', $search_suggestions_display ) ) {
                $suggestion['date'] = human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) . ' ที่แล้ว';
            }

            if ( in_array( 'category', $search_suggestions_display ) ) {
                $categories = get_the_category();
                if ( ! empty( $categories ) ) {
                    $suggestion['category'] = $categories[0]->name;
                    $suggestion['category_color'] = get_term_meta( $categories[0]->term_id, 'category_color', true ) ?: '#3B82F6';
                }
            }

            $suggestions[] = $suggestion;
        }
        wp_reset_postdata();
    }

    wp_send_json_success( array( 'suggestions' => $suggestions ) );
}
add_action( 'wp_ajax_search_suggestions', 'trendtoday_search_suggestions' );
add_action( 'wp_ajax_nopriv_search_suggestions', 'trendtoday_search_suggestions' );

/**
 * Increment post views via AJAX
 */
function trendtoday_increment_views() {
    check_ajax_referer( 'trendtoday-nonce', 'nonce' );

    $post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;

    if ( ! $post_id ) {
        wp_send_json_error( array( 'message' => __( 'Invalid post ID', 'trendtoday' ) ) );
    }

    trendtoday_increment_post_views( $post_id );
    $views = trendtoday_get_post_views( $post_id );

    wp_send_json_success( array( 'views' => $views ) );
}
add_action( 'wp_ajax_increment_views', 'trendtoday_increment_views' );
add_action( 'wp_ajax_nopriv_increment_views', 'trendtoday_increment_views' );

/**
 * Regenerate images via AJAX
 */
function trendtoday_regenerate_images_ajax() {
    check_ajax_referer( 'trendtoday_settings_nonce', 'nonce' );
    
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( array( 'message' => __( 'Insufficient permissions', 'trendtoday' ) ) );
    }
    
    $action = isset( $_POST['action_type'] ) ? sanitize_text_field( $_POST['action_type'] ) : 'get_total';
    $offset = isset( $_POST['offset'] ) ? absint( $_POST['offset'] ) : 0;
    $batch_size = isset( $_POST['batch_size'] ) ? absint( $_POST['batch_size'] ) : 5;
    
    if ( $action === 'get_total' ) {
        // Get total images count
        $attachments = get_posts( array(
            'post_type' => 'attachment',
            'post_mime_type' => 'image',
            'posts_per_page' => -1,
            'post_status' => 'inherit',
            'fields' => 'ids',
        ) );
        
        wp_send_json_success( array(
            'total' => count( $attachments ),
            'message' => sprintf( __( 'Found %d images to process', 'trendtoday' ), count( $attachments ) ),
        ) );
    } elseif ( $action === 'regenerate' ) {
        // Get batch of images
        $attachments = get_posts( array(
            'post_type' => 'attachment',
            'post_mime_type' => 'image',
            'posts_per_page' => $batch_size,
            'offset' => $offset,
            'post_status' => 'inherit',
            'fields' => 'ids',
            'orderby' => 'ID',
            'order' => 'ASC',
        ) );
        
        $processed = 0;
        $errors = array();
        
        foreach ( $attachments as $attachment_id ) {
            if ( function_exists( 'trendtoday_regenerate_image' ) ) {
                $result = trendtoday_regenerate_image( $attachment_id );
                if ( is_wp_error( $result ) ) {
                    $errors[] = sprintf( __( 'Error processing image ID %d: %s', 'trendtoday' ), $attachment_id, $result->get_error_message() );
                } else {
                    $processed++;
                }
            }
        }
        
        wp_send_json_success( array(
            'processed' => $processed,
            'errors' => $errors,
            'offset' => $offset + count( $attachments ),
            'message' => sprintf( __( 'Processed %d images', 'trendtoday' ), $processed ),
        ) );
    }
    
    wp_send_json_error( array( 'message' => __( 'Invalid action', 'trendtoday' ) ) );
}
add_action( 'wp_ajax_regenerate_images', 'trendtoday_regenerate_images_ajax' );

/**
 * Scan for unused images via AJAX
 */
function trendtoday_scan_unused_images_ajax() {
    check_ajax_referer( 'trendtoday_settings_nonce', 'nonce' );
    
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( array( 'message' => __( 'Insufficient permissions', 'trendtoday' ) ) );
    }
    
    // Check if function exists
    if ( ! function_exists( 'trendtoday_scan_unused_images' ) ) {
        wp_send_json_error( array( 'message' => __( 'Image cleanup functions not available', 'trendtoday' ) ) );
    }
    
    // Get scan options
    $scan_type = isset( $_POST['scan_type'] ) ? sanitize_text_field( $_POST['scan_type'] ) : 'all';
    $use_cache = isset( $_POST['use_cache'] ) ? ( $_POST['use_cache'] === 'true' ) : true;
    $max_files = isset( $_POST['max_files'] ) ? absint( $_POST['max_files'] ) : 0;
    $time_limit = isset( $_POST['time_limit'] ) ? absint( $_POST['time_limit'] ) : 0;
    
    $options = array(
        'scan_type'  => $scan_type,
        'use_cache'  => $use_cache,
        'max_files'  => $max_files,
        'time_limit' => $time_limit,
    );
    
    // Increase time limit if needed
    if ( $time_limit > 0 ) {
        @set_time_limit( $time_limit + 10 );
    } else {
        @set_time_limit( 300 ); // 5 minutes default
    }
    
    // Increase memory limit if possible
    $current_memory = ini_get( 'memory_limit' );
    if ( $current_memory !== '-1' ) {
        $memory_bytes = wp_convert_hr_to_bytes( $current_memory );
        if ( $memory_bytes < 256 * 1024 * 1024 ) { // Less than 256MB
            @ini_set( 'memory_limit', '256M' );
        }
    }
    
    try {
        // Perform scan
        $results = trendtoday_scan_unused_images( $options );
        
        // Store results in transient for later use
        set_transient( 'trendtoday_scan_results', $results, HOUR_IN_SECONDS );
        
        wp_send_json_success( $results );
    } catch ( Exception $e ) {
        wp_send_json_error( array(
            'message' => __( 'Scan failed: ', 'trendtoday' ) . $e->getMessage(),
            'error'   => $e->getMessage(),
        ) );
    }
}
add_action( 'wp_ajax_scan_unused_images', 'trendtoday_scan_unused_images_ajax' );

/**
 * Delete unused images via AJAX
 */
function trendtoday_delete_unused_images_ajax() {
    check_ajax_referer( 'trendtoday_settings_nonce', 'nonce' );
    
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( array( 'message' => __( 'Insufficient permissions', 'trendtoday' ) ) );
    }
    
    // Check if function exists
    if ( ! function_exists( 'trendtoday_delete_unused_images' ) ) {
        wp_send_json_error( array( 'message' => __( 'Image cleanup functions not available', 'trendtoday' ) ) );
    }
    
    $action = isset( $_POST['action_type'] ) ? sanitize_text_field( $_POST['action_type'] ) : 'selected';
    $file_paths = array();
    
    if ( $action === 'selected' ) {
        // Get selected file paths
        if ( isset( $_POST['file_paths'] ) && is_array( $_POST['file_paths'] ) ) {
            $file_paths = array_map( 'sanitize_text_field', $_POST['file_paths'] );
        }
    } elseif ( $action === 'all' ) {
        // Get all file paths from scan results
        $scan_results = get_transient( 'trendtoday_scan_results' );
        if ( $scan_results ) {
            $all_files = array();
            if ( isset( $scan_results['thumbnails'] ) ) {
                foreach ( $scan_results['thumbnails'] as $file ) {
                    if ( isset( $file['path'] ) ) {
                        $all_files[] = $file['path'];
                    }
                }
            }
            if ( isset( $scan_results['webp'] ) ) {
                foreach ( $scan_results['webp'] as $file ) {
                    if ( isset( $file['path'] ) ) {
                        $all_files[] = $file['path'];
                    }
                }
            }
            if ( isset( $scan_results['orphaned'] ) ) {
                foreach ( $scan_results['orphaned'] as $file ) {
                    if ( isset( $file['path'] ) ) {
                        $all_files[] = $file['path'];
                    }
                }
            }
            $file_paths = $all_files;
        }
    }
    
    if ( empty( $file_paths ) ) {
        wp_send_json_error( array( 'message' => __( 'No files selected for deletion', 'trendtoday' ) ) );
    }
    
    // Get delete options
    $dry_run = isset( $_POST['dry_run'] ) && $_POST['dry_run'] === 'true';
    $batch_size = isset( $_POST['batch_size'] ) ? absint( $_POST['batch_size'] ) : 50;
    
    $delete_options = array(
        'dry_run'    => $dry_run,
        'batch_size' => $batch_size,
        'log'        => true,
    );
    
    // Delete files
    $results = trendtoday_delete_unused_images( $file_paths, $delete_options );
    
    // Prepare failed files list for display
    $failed_files = array();
    if ( isset( $results['errors'] ) && is_array( $results['errors'] ) ) {
        foreach ( $results['errors'] as $error ) {
            if ( isset( $error['file'] ) ) {
                // Get file info from scan results
                $scan_results = get_transient( 'trendtoday_scan_results' );
                $file_info = null;
                
                if ( $scan_results ) {
                    // Search in all file types
                    $all_files = array_merge(
                        isset( $scan_results['thumbnails'] ) ? $scan_results['thumbnails'] : array(),
                        isset( $scan_results['webp'] ) ? $scan_results['webp'] : array(),
                        isset( $scan_results['orphaned'] ) ? $scan_results['orphaned'] : array()
                    );
                    
                    foreach ( $all_files as $file ) {
                        if ( isset( $file['path'] ) && $file['path'] === $error['file'] ) {
                            $file_info = $file;
                            break;
                        }
                    }
                }
                
                // If file info not found, create basic info
                if ( ! $file_info ) {
                    $file_info = array(
                        'path'     => $error['file'],
                        'filename' => basename( $error['file'] ),
                        'size'     => file_exists( $error['file'] ) ? filesize( $error['file'] ) : 0,
                        'modified' => file_exists( $error['file'] ) ? filemtime( $error['file'] ) : time(),
                    );
                }
                
                $file_info['error'] = isset( $error['error'] ) ? $error['error'] : 'Unknown error';
                $failed_files[] = $file_info;
            }
        }
    }
    
    // Add failed files to results
    $results['failed_files'] = $failed_files;
    
    // Update scan results after deletion
    $scan_results = get_transient( 'trendtoday_scan_results' );
    if ( $scan_results ) {
        // Get successfully deleted paths
        $deleted_paths = array();
        foreach ( $file_paths as $index => $path ) {
            // Check if this file was successfully deleted (not in errors)
            $is_deleted = true;
            if ( isset( $results['errors'] ) && is_array( $results['errors'] ) ) {
                foreach ( $results['errors'] as $error ) {
                    if ( isset( $error['file'] ) && $error['file'] === $path ) {
                        $is_deleted = false;
                        break;
                    }
                }
            }
            if ( $is_deleted ) {
                $deleted_paths[] = $path;
            }
        }
        
        // Filter out deleted files
        if ( isset( $scan_results['thumbnails'] ) ) {
            $scan_results['thumbnails'] = array_values( array_filter( $scan_results['thumbnails'], function( $file ) use ( $deleted_paths ) {
                return ! in_array( $file['path'], $deleted_paths, true );
            } ) );
        }
        if ( isset( $scan_results['webp'] ) ) {
            $scan_results['webp'] = array_values( array_filter( $scan_results['webp'], function( $file ) use ( $deleted_paths ) {
                return ! in_array( $file['path'], $deleted_paths, true );
            } ) );
        }
        if ( isset( $scan_results['orphaned'] ) ) {
            $scan_results['orphaned'] = array_values( array_filter( $scan_results['orphaned'], function( $file ) use ( $deleted_paths ) {
                return ! in_array( $file['path'], $deleted_paths, true );
            } ) );
        }
        
        // Recalculate statistics
        if ( isset( $scan_results['statistics'] ) ) {
            $stats = array(
                'total_thumbnails'  => count( $scan_results['thumbnails'] ),
                'total_webp'        => count( $scan_results['webp'] ),
                'total_orphaned'    => count( $scan_results['orphaned'] ),
                'total_size'        => 0,
                'thumbnails_size'   => 0,
                'webp_size'         => 0,
                'orphaned_size'     => 0,
            );
            
            foreach ( $scan_results['thumbnails'] as $thumb ) {
                $stats['thumbnails_size'] += $thumb['size'];
                $stats['total_size'] += $thumb['size'];
            }
            foreach ( $scan_results['webp'] as $webp ) {
                $stats['webp_size'] += $webp['size'];
                $stats['total_size'] += $webp['size'];
            }
            foreach ( $scan_results['orphaned'] as $orphaned ) {
                $stats['orphaned_size'] += $orphaned['size'];
                $stats['total_size'] += $orphaned['size'];
            }
            
            $stats['total_size_mb'] = round( $stats['total_size'] / 1024 / 1024, 2 );
            $stats['thumbnails_size_mb'] = round( $stats['thumbnails_size'] / 1024 / 1024, 2 );
            $stats['webp_size_mb'] = round( $stats['webp_size'] / 1024 / 1024, 2 );
            $stats['orphaned_size_mb'] = round( $stats['orphaned_size'] / 1024 / 1024, 2 );
            
            $scan_results['statistics'] = $stats;
        }
        
        set_transient( 'trendtoday_scan_results', $scan_results, HOUR_IN_SECONDS );
    }
    
    wp_send_json_success( $results );
}
add_action( 'wp_ajax_delete_unused_images', 'trendtoday_delete_unused_images_ajax' );
