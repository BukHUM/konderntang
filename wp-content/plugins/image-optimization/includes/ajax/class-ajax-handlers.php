<?php
/**
 * AJAX Handlers
 *
 * @package Image_Optimization
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * AJAX Handlers Class
 */
class IO_Ajax_Handlers {
    
    /**
     * Instance
     *
     * @var IO_Ajax_Handlers
     */
    private static $instance = null;
    
    /**
     * Get instance
     *
     * @return IO_Ajax_Handlers
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        // Scan unused images
        add_action( 'wp_ajax_io_scan_unused_images', array( $this, 'scan_unused_images' ) );
        
        // Delete unused images
        add_action( 'wp_ajax_io_delete_unused_images', array( $this, 'delete_unused_images' ) );
        
        // Regenerate images
        add_action( 'wp_ajax_io_count_images_for_regenerate', array( $this, 'count_images_for_regenerate' ) );
        add_action( 'wp_ajax_io_regenerate_images', array( $this, 'regenerate_images' ) );
        add_action( 'wp_ajax_io_cancel_regenerate', array( $this, 'cancel_regenerate' ) );
    }
    
    /**
     * Scan unused images AJAX handler
     */
    public function scan_unused_images() {
        check_ajax_referer( 'io_settings_nonce', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array(
                'message' => __( 'คุณไม่มีสิทธิ์ในการดำเนินการนี้', 'image-optimization' ),
            ) );
        }
        
        // Increase time limit and memory limit
        @set_time_limit( 300 );
        @ini_set( 'memory_limit', '256M' );
        
        $scan_type = isset( $_POST['scan_type'] ) ? sanitize_text_field( $_POST['scan_type'] ) : 'all';
        $use_cache = isset( $_POST['use_cache'] ) ? (bool) $_POST['use_cache'] : true;
        $max_files = isset( $_POST['max_files'] ) ? absint( $_POST['max_files'] ) : 0;
        $time_limit = isset( $_POST['time_limit'] ) ? absint( $_POST['time_limit'] ) : 0;
        
        try {
            // Validate prerequisites before starting
            $validation = $this->validate_scan_prerequisites();
            if ( ! $validation['valid'] ) {
                wp_send_json_error( array(
                    'message' => $validation['message'],
                ) );
            }
            
            // Validate scan_type
            $valid_scan_types = array( 'all', 'thumbnails', 'webp', 'orphaned', 'statistics_only' );
            if ( ! in_array( $scan_type, $valid_scan_types, true ) ) {
                $scan_type = 'all';
            }
            
            $cleanup = new IO_Image_Cleanup();
            if ( ! $cleanup ) {
                throw new Exception( __( 'ไม่สามารถเริ่มต้นระบบทำความสะอาดภาพได้ กรุณาลองใหม่อีกครั้ง', 'image-optimization' ) );
            }
            
            $results = $cleanup->scan_unused_images( array(
                'scan_type' => $scan_type,
                'use_cache' => $use_cache,
                'max_files' => $max_files,
                'time_limit' => $time_limit,
            ) );
            
            if ( ! is_array( $results ) ) {
                throw new Exception( __( 'ผลการแสกนไม่ถูกต้อง กรุณาลองใหม่อีกครั้ง', 'image-optimization' ) );
            }
            
            // Check if there was an error in the scan results
            if ( isset( $results['error'] ) && ! empty( $results['error'] ) ) {
                $error_message = $this->translate_error_message( $results['error'] );
                wp_send_json_error( array(
                    'message' => $error_message,
                ) );
            }
            
            // Store results in transient for later use
            set_transient( 'io_scan_results', $results, HOUR_IN_SECONDS );
            
            wp_send_json_success( $results );
        } catch ( Exception $e ) {
            $error_message = $this->translate_error_message( $e->getMessage() );
            error_log( '[Image Optimization] Scan error: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString() );
            wp_send_json_error( array(
                'message' => $error_message,
            ) );
        } catch ( Error $e ) {
            error_log( '[Image Optimization] Scan fatal error: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString() );
            wp_send_json_error( array(
                'message' => __( 'เกิดข้อผิดพลาดร้ายแรงในการแสกน กรุณาตรวจสอบ log หรือติดต่อผู้ดูแลระบบ', 'image-optimization' ),
            ) );
        }
    }
    
    /**
     * Validate prerequisites before scanning
     *
     * @return array Validation result with 'valid' and 'message' keys.
     */
    private function validate_scan_prerequisites() {
        // Check if uploads directory exists
        $upload_dir = wp_upload_dir();
        if ( empty( $upload_dir['basedir'] ) ) {
            return array(
                'valid' => false,
                'message' => __( 'ไม่พบโฟลเดอร์ uploads กรุณาตรวจสอบการตั้งค่า WordPress', 'image-optimization' ),
            );
        }
        
        $uploads_dir = $upload_dir['basedir'];
        
        // Check if directory exists
        if ( ! is_dir( $uploads_dir ) ) {
            return array(
                'valid' => false,
                'message' => __( 'โฟลเดอร์ uploads ไม่มีอยู่จริง กรุณาตรวจสอบสิทธิ์การเข้าถึง', 'image-optimization' ),
            );
        }
        
        // Check if directory is readable
        if ( ! is_readable( $uploads_dir ) ) {
            return array(
                'valid' => false,
                'message' => __( 'ไม่สามารถอ่านโฟลเดอร์ uploads ได้ กรุณาตรวจสอบสิทธิ์การเข้าถึง (ควรเป็น 755 หรือ 775)', 'image-optimization' ),
            );
        }
        
        // Check database connection
        global $wpdb;
        if ( ! $wpdb ) {
            return array(
                'valid' => false,
                'message' => __( 'ไม่สามารถเชื่อมต่อฐานข้อมูลได้ กรุณาตรวจสอบการตั้งค่า', 'image-optimization' ),
            );
        }
        
        // Check memory limit
        $memory_limit = ini_get( 'memory_limit' );
        $memory_limit_bytes = $this->convert_to_bytes( $memory_limit );
        if ( $memory_limit_bytes < 64 * 1024 * 1024 ) { // Less than 64MB
            return array(
                'valid' => false,
                'message' => sprintf( 
                    __( 'หน่วยความจำไม่เพียงพอ (ปัจจุบัน: %s) กรุณาเพิ่ม memory_limit ใน php.ini เป็นอย่างน้อย 128M', 'image-optimization' ),
                    $memory_limit
                ),
            );
        }
        
        // Check if required PHP functions exist
        $required_functions = array( 'get_posts', 'wp_upload_dir', 'get_attached_file' );
        foreach ( $required_functions as $func ) {
            if ( ! function_exists( $func ) ) {
                return array(
                    'valid' => false,
                    'message' => sprintf( 
                        __( 'ฟังก์ชัน PHP ที่จำเป็นไม่พร้อมใช้งาน: %s', 'image-optimization' ),
                        $func
                    ),
                );
            }
        }
        
        return array(
            'valid' => true,
            'message' => '',
        );
    }
    
    /**
     * Convert memory limit string to bytes
     *
     * @param string $memory_limit Memory limit string (e.g., '128M', '256M').
     * @return int Memory limit in bytes.
     */
    private function convert_to_bytes( $memory_limit ) {
        $memory_limit = trim( $memory_limit );
        $last = strtolower( $memory_limit[ strlen( $memory_limit ) - 1 ] );
        $value = (int) $memory_limit;
        
        switch ( $last ) {
            case 'g':
                $value *= 1024;
                // Fall through
            case 'm':
                $value *= 1024;
                // Fall through
            case 'k':
                $value *= 1024;
        }
        
        return $value;
    }
    
    /**
     * Translate technical error messages to user-friendly Thai messages
     *
     * @param string $error_message Original error message.
     * @return string Translated error message.
     */
    private function translate_error_message( $error_message ) {
        $error_message = trim( $error_message );
        
        // Common error patterns and their translations
        $error_patterns = array(
            // File system errors
            '/permission denied/i' => 'ไม่มีสิทธิ์เข้าถึงไฟล์ กรุณาตรวจสอบสิทธิ์การเข้าถึงโฟลเดอร์ uploads',
            '/failed to open stream/i' => 'ไม่สามารถเปิดไฟล์ได้ กรุณาตรวจสอบสิทธิ์การเข้าถึง',
            '/no such file or directory/i' => 'ไม่พบไฟล์หรือโฟลเดอร์ที่ระบุ',
            '/directory not found/i' => 'ไม่พบโฟลเดอร์ที่ระบุ',
            '/uploads directory not found/i' => 'ไม่พบโฟลเดอร์ uploads',
            
            // Database errors
            '/database error/i' => 'เกิดข้อผิดพลาดจากฐานข้อมูล กรุณาตรวจสอบการเชื่อมต่อ',
            '/mysql/i' => 'เกิดข้อผิดพลาดจากฐานข้อมูล กรุณาตรวจสอบการเชื่อมต่อ',
            '/query failed/i' => 'การค้นหาข้อมูลล้มเหลว กรุณาลองใหม่อีกครั้ง',
            
            // Memory errors
            '/memory/i' => 'หน่วยความจำไม่เพียงพอ กรุณาเพิ่ม memory_limit ใน php.ini',
            '/allowed memory size/i' => 'หน่วยความจำไม่เพียงพอ กรุณาเพิ่ม memory_limit ใน php.ini',
            
            // Timeout errors
            '/timeout/i' => 'การแสกนใช้เวลานานเกินไป กรุณาลองใหม่อีกครั้งหรือลดจำนวนไฟล์',
            '/maximum execution time/i' => 'การแสกนใช้เวลานานเกินไป กรุณาเพิ่ม max_execution_time ใน php.ini',
            
            // Class/function errors
            '/class.*not found/i' => 'ไม่พบคลาสที่จำเป็น กรุณาตรวจสอบการติดตั้งปลั๊กอิน',
            '/function.*not found/i' => 'ไม่พบฟังก์ชันที่จำเป็น กรุณาตรวจสอบการติดตั้งปลั๊กอิน',
            '/call to undefined/i' => 'เรียกใช้ฟังก์ชันที่ไม่ถูกต้อง กรุณาตรวจสอบการติดตั้งปลั๊กอิน',
            
            // Generic errors
            '/invalid/i' => 'ข้อมูลไม่ถูกต้อง กรุณาลองใหม่อีกครั้ง',
            '/failed/i' => 'การดำเนินการล้มเหลว กรุณาลองใหม่อีกครั้ง',
        );
        
        // Check if error matches any pattern
        foreach ( $error_patterns as $pattern => $translation ) {
            if ( preg_match( $pattern, $error_message ) ) {
                return $translation;
            }
        }
        
        // If no pattern matches, return a generic message with the original error for debugging
        // But only show a simple message to the user
        return __( 'เกิดข้อผิดพลาดในการแสกน: ', 'image-optimization' ) . 
               __( 'กรุณาลองใหม่อีกครั้ง หรือตรวจสอบ log สำหรับรายละเอียดเพิ่มเติม', 'image-optimization' );
    }
    
    /**
     * Delete unused images AJAX handler
     */
    public function delete_unused_images() {
        check_ajax_referer( 'io_settings_nonce', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array(
                'message' => __( 'You do not have permission to perform this action.', 'image-optimization' ),
            ) );
        }
        
        // Increase time limit and memory limit
        @set_time_limit( 300 );
        @ini_set( 'memory_limit', '256M' );
        
        $files = isset( $_POST['files'] ) ? $_POST['files'] : array();
        $dry_run = isset( $_POST['dry_run'] ) ? (bool) $_POST['dry_run'] : false;
        
        if ( empty( $files ) && ! isset( $_POST['delete_all'] ) ) {
            wp_send_json_error( array(
                'message' => __( 'No files specified for deletion.', 'image-optimization' ),
            ) );
        }
        
        try {
            $cleanup = new IO_Image_Cleanup();
            if ( ! $cleanup ) {
                throw new Exception( __( 'Failed to initialize Image Cleanup class.', 'image-optimization' ) );
            }
            
            // If delete_all, get all files from scan results
            if ( isset( $_POST['delete_all'] ) && $_POST['delete_all'] ) {
                $scan_results = get_transient( 'io_scan_results' );
                if ( ! $scan_results || ! is_array( $scan_results ) ) {
                    wp_send_json_error( array(
                        'message' => __( 'No scan results found. Please scan first.', 'image-optimization' ),
                    ) );
                }
                
                // Collect all files from scan results
                $files = array();
                if ( ! empty( $scan_results['thumbnails'] ) && is_array( $scan_results['thumbnails'] ) ) {
                    foreach ( $scan_results['thumbnails'] as $file ) {
                        if ( isset( $file['path'] ) && is_string( $file['path'] ) ) {
                            $files[] = $file['path'];
                        }
                    }
                }
                if ( ! empty( $scan_results['webp'] ) && is_array( $scan_results['webp'] ) ) {
                    foreach ( $scan_results['webp'] as $file ) {
                        if ( isset( $file['path'] ) && is_string( $file['path'] ) ) {
                            $files[] = $file['path'];
                        }
                    }
                }
                if ( ! empty( $scan_results['orphaned'] ) && is_array( $scan_results['orphaned'] ) ) {
                    foreach ( $scan_results['orphaned'] as $file ) {
                        if ( isset( $file['path'] ) && is_string( $file['path'] ) ) {
                            $files[] = $file['path'];
                        }
                    }
                }
                
                if ( empty( $files ) ) {
                    wp_send_json_error( array(
                        'message' => __( 'No files found in scan results to delete.', 'image-optimization' ),
                    ) );
                }
            } else {
                // Validate and sanitize file paths
                if ( ! is_array( $files ) ) {
                    $files = array();
                }
                $files = array_map( 'sanitize_text_field', $files );
                $files = array_filter( $files, 'strlen' ); // Remove empty strings
            }
            
            if ( empty( $files ) ) {
                wp_send_json_error( array(
                    'message' => __( 'No files specified for deletion.', 'image-optimization' ),
                ) );
            }
            
            $results = $cleanup->delete_unused_images( $files, array( 'dry_run' => $dry_run ) );
            
            if ( ! is_array( $results ) ) {
                throw new Exception( __( 'Invalid deletion results returned.', 'image-optimization' ) );
            }
            
            // Format response for JavaScript
            $response_data = array(
                'deleted'      => $results['deleted'],
                'failed'       => $results['failed'],
                'failed_files' => array(),
            );
            
            // Format failed files for display
            if ( ! empty( $results['errors'] ) ) {
                foreach ( $results['errors'] as $error ) {
                    $file_info = array(
                        'path'     => $error['file'],
                        'filename' => basename( $error['file'] ),
                        'error'    => $error['error'],
                        'size'     => file_exists( $error['file'] ) ? filesize( $error['file'] ) : 0,
                        'modified' => file_exists( $error['file'] ) ? filemtime( $error['file'] ) : 0,
                    );
                    $response_data['failed_files'][] = $file_info;
                }
            }
            
            // Update scan results if not dry run
            if ( ! $dry_run && $results['deleted'] > 0 ) {
                $scan_results = get_transient( 'io_scan_results' );
                if ( $scan_results ) {
                    // Get deleted file paths from errors (we need to track which files were actually deleted)
                    // Since delete_unused_images doesn't return deleted paths, we'll refresh the scan
                    // For now, just refresh the scan results
                    set_transient( 'io_scan_results', null, 0 );
                }
            }
            
            wp_send_json_success( $response_data );
        } catch ( Exception $e ) {
            error_log( '[Image Optimization] Delete error: ' . $e->getMessage() );
            wp_send_json_error( array(
                'message' => $e->getMessage(),
            ) );
        } catch ( Error $e ) {
            error_log( '[Image Optimization] Delete fatal error: ' . $e->getMessage() );
            wp_send_json_error( array(
                'message' => __( 'A fatal error occurred during deletion.', 'image-optimization' ),
            ) );
        }
    }
    
    /**
     * Count images for regenerate
     */
    public function count_images_for_regenerate() {
        check_ajax_referer( 'io_settings_nonce', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array(
                'message' => __( 'You do not have permission to perform this action.', 'image-optimization' ),
            ) );
        }
        
        try {
            // Get all image attachments
            $attachments = get_posts( array(
                'post_type'      => 'attachment',
                'post_mime_type' => 'image',
                'posts_per_page' => -1,
                'post_status'    => 'inherit',
                'fields'         => 'ids',
                'no_found_rows'  => true,
            ) );
            
            $count = count( $attachments );
            
            wp_send_json_success( array(
                'count' => $count,
            ) );
        } catch ( Exception $e ) {
            error_log( '[Image Optimization] Count error: ' . $e->getMessage() );
            wp_send_json_error( array(
                'message' => $e->getMessage(),
            ) );
        }
    }
    
    /**
     * Regenerate images AJAX handler (batch processing)
     */
    public function regenerate_images() {
        check_ajax_referer( 'io_settings_nonce', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array(
                'message' => __( 'You do not have permission to perform this action.', 'image-optimization' ),
            ) );
        }
        
        // Increase time limit and memory limit
        @set_time_limit( 60 );
        @ini_set( 'memory_limit', '256M' );
        
        $batch_size = isset( $_POST['batch_size'] ) ? absint( $_POST['batch_size'] ) : 10;
        $offset = isset( $_POST['offset'] ) ? absint( $_POST['offset'] ) : 0;
        $total = isset( $_POST['total'] ) ? absint( $_POST['total'] ) : 0;
        
        // Check if cancelled
        $cancelled = get_transient( 'io_regenerate_cancelled' );
        if ( $cancelled ) {
            wp_send_json_error( array(
                'message' => __( 'Regeneration was cancelled.', 'image-optimization' ),
                'cancelled' => true,
            ) );
        }
        
        try {
            require_once IO_PLUGIN_DIR . 'includes/class-image-optimizer.php';
            $optimizer = IO_Image_Optimizer::get_instance();
            
            // Get batch of attachments
            $attachments = get_posts( array(
                'post_type'      => 'attachment',
                'post_mime_type' => 'image',
                'posts_per_page' => $batch_size,
                'post_status'    => 'inherit',
                'fields'         => 'ids',
                'offset'         => $offset,
                'orderby'        => 'ID',
                'order'          => 'ASC',
                'no_found_rows'  => true,
            ) );
            
            if ( empty( $attachments ) ) {
                // All done
                delete_transient( 'io_regenerate_cancelled' );
                wp_send_json_success( array(
                    'done' => true,
                    'processed' => $offset,
                    'total' => $total,
                ) );
            }
            
            $processed = 0;
            $success = 0;
            $failed = 0;
            
            foreach ( $attachments as $attachment_id ) {
                // Check if cancelled
                $cancelled = get_transient( 'io_regenerate_cancelled' );
                if ( $cancelled ) {
                    break;
                }
                
                try {
                    $result = $optimizer->regenerate_image( $attachment_id );
                    if ( $result && ! is_wp_error( $result ) ) {
                        $success++;
                    } else {
                        $failed++;
                        if ( is_wp_error( $result ) ) {
                            error_log( '[Image Optimization] Regenerate error for attachment ' . $attachment_id . ': ' . $result->get_error_message() );
                        }
                    }
                } catch ( Exception $e ) {
                    error_log( '[Image Optimization] Regenerate error for attachment ' . $attachment_id . ': ' . $e->getMessage() );
                    $failed++;
                }
                
                $processed++;
            }
            
            // Check cancelled again after processing
            $cancelled = get_transient( 'io_regenerate_cancelled' );
            
            $new_offset = $offset + $processed;
            $remaining = max( 0, $total - $new_offset );
            
            // If cancelled, clear the flag and return cancelled status
            if ( $cancelled ) {
                wp_send_json_success( array(
                    'processed' => $new_offset,
                    'total' => $total,
                    'success' => $success,
                    'failed' => $failed,
                    'remaining' => $remaining,
                    'done' => true,
                    'cancelled' => true,
                    'message' => __( 'Regeneration was cancelled.', 'image-optimization' ),
                ) );
            }
            
            wp_send_json_success( array(
                'processed' => $new_offset,
                'total' => $total,
                'success' => $success,
                'failed' => $failed,
                'remaining' => $remaining,
                'done' => $remaining === 0,
                'cancelled' => false,
            ) );
        } catch ( Exception $e ) {
            error_log( '[Image Optimization] Regenerate error: ' . $e->getMessage() );
            wp_send_json_error( array(
                'message' => $e->getMessage(),
            ) );
        }
    }
    
    /**
     * Cancel regenerate
     */
    public function cancel_regenerate() {
        check_ajax_referer( 'io_settings_nonce', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array(
                'message' => __( 'You do not have permission to perform this action.', 'image-optimization' ),
            ) );
        }
        
        // Check if we're clearing the cancel flag
        if ( isset( $_POST['cancel'] ) && ( $_POST['cancel'] === '0' || $_POST['cancel'] === false ) ) {
            delete_transient( 'io_regenerate_cancelled' );
            wp_send_json_success( array(
                'message' => __( 'Regeneration flag cleared.', 'image-optimization' ),
            ) );
        } else {
            // Set cancel flag
            set_transient( 'io_regenerate_cancelled', true, 300 );
            wp_send_json_success( array(
                'message' => __( 'Regeneration cancelled.', 'image-optimization' ),
            ) );
        }
    }
}
