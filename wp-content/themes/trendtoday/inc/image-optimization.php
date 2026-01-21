<?php
/**
 * Image Optimization Functions
 * Auto resize and WebP conversion
 *
 * @package TrendToday
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Check if WebP is supported by server
 *
 * @return bool True if WebP is supported.
 */
function trendtoday_webp_supported() {
    // Check if GD library supports WebP
    if ( function_exists( 'imagewebp' ) ) {
        return true;
    }
    
    // Check if Imagick supports WebP
    if ( extension_loaded( 'imagick' ) ) {
        $imagick = new Imagick();
        $formats = $imagick->queryFormats();
        if ( in_array( 'WEBP', $formats, true ) ) {
            return true;
        }
    }
    
    return false;
}

/**
 * Resize image on upload
 *
 * @param array $file Uploaded file array.
 * @return array Modified file array.
 */
function trendtoday_resize_uploaded_image( $file ) {
    // Check if auto resize is enabled
    $auto_resize = get_option( 'trendtoday_image_auto_resize', '1' );
    if ( $auto_resize !== '1' ) {
        return $file;
    }
    
    // Check if it's an image
    $image_types = array( 'image/jpeg', 'image/jpg', 'image/png', 'image/gif' );
    if ( ! in_array( $file['type'], $image_types, true ) ) {
        return $file;
    }
    
    // Get settings
    $max_width = absint( get_option( 'trendtoday_image_max_width', 1920 ) );
    $max_height = absint( get_option( 'trendtoday_image_max_height', 1080 ) );
    $maintain_aspect = get_option( 'trendtoday_image_maintain_aspect', '1' ) === '1';
    $max_file_size = absint( get_option( 'trendtoday_image_max_file_size', 0 ) ); // 0 = resize all
    
    // Check file size threshold
    if ( $max_file_size > 0 && $file['size'] < ( $max_file_size * 1024 * 1024 ) ) {
        return $file; // Skip small images
    }
    
    // Load image
    $image = wp_get_image_editor( $file['tmp_name'] );
    if ( is_wp_error( $image ) ) {
        return $file;
    }
    
    // Get original dimensions
    $size = $image->get_size();
    $width = $size['width'];
    $height = $size['height'];
    
    // Check if resize is needed
    if ( $width <= $max_width && $height <= $max_height ) {
        return $file; // No resize needed
    }
    
    // Calculate new dimensions
    if ( $maintain_aspect ) {
        $ratio = min( $max_width / $width, $max_height / $height );
        $new_width = round( $width * $ratio );
        $new_height = round( $height * $ratio );
    } else {
        $new_width = $max_width;
        $new_height = $max_height;
    }
    
    // Resize image
    $resized = $image->resize( $new_width, $new_height, false );
    if ( is_wp_error( $resized ) ) {
        return $file;
    }
    
    // Save resized image
    $saved = $image->save( $file['tmp_name'] );
    if ( is_wp_error( $saved ) ) {
        return $file;
    }
    
    // Update file size
    $file['size'] = filesize( $file['tmp_name'] );
    
    return $file;
}
add_filter( 'wp_handle_upload_prefilter', 'trendtoday_resize_uploaded_image' );

/**
 * Convert image to WebP after upload
 *
 * @param array $metadata Attachment metadata.
 * @param int $attachment_id Attachment ID.
 * @return array Modified metadata.
 */
function trendtoday_convert_to_webp( $metadata, $attachment_id ) {
    // Check if WebP conversion is enabled
    $webp_enabled = get_option( 'trendtoday_image_webp_enabled', '1' );
    if ( $webp_enabled !== '1' ) {
        return $metadata;
    }
    
    // Check if WebP is supported
    if ( ! trendtoday_webp_supported() ) {
        return $metadata;
    }
    
    // Get attachment file
    $file = get_attached_file( $attachment_id );
    if ( ! $file || ! file_exists( $file ) ) {
        return $metadata;
    }
    
    // Check if it's an image
    $image_types = array( 'image/jpeg', 'image/jpg', 'image/png' );
    $mime_type = get_post_mime_type( $attachment_id );
    if ( ! in_array( $mime_type, $image_types, true ) ) {
        return $metadata;
    }
    
    // Skip if already WebP
    if ( $mime_type === 'image/webp' ) {
        return $metadata;
    }
    
    // Get WebP quality
    $webp_quality = absint( get_option( 'trendtoday_image_webp_quality', 85 ) );
    $webp_quality = max( 0, min( 100, $webp_quality ) );
    
    // Create WebP version
    $webp_file = preg_replace( '/\.(jpg|jpeg|png)$/i', '.webp', $file );
    
    // Load image
    $image = wp_get_image_editor( $file );
    if ( is_wp_error( $image ) ) {
        return $metadata;
    }
    
    // Convert to WebP
    $converted = $image->save( $webp_file, 'image/webp' );
    if ( is_wp_error( $converted ) ) {
        return $metadata;
    }
    
    // Store WebP file path in metadata
    if ( ! isset( $metadata['sizes'] ) ) {
        $metadata['sizes'] = array();
    }
    
    // Add WebP version to sizes
    $metadata['sizes']['webp'] = array(
        'file' => basename( $webp_file ),
        'width' => $converted['width'],
        'height' => $converted['height'],
        'mime-type' => 'image/webp',
    );
    
    // Update attachment meta
    update_post_meta( $attachment_id, '_webp_file', $webp_file );
    
    return $metadata;
}
add_filter( 'wp_generate_attachment_metadata', 'trendtoday_convert_to_webp', 10, 2 );

/**
 * Strip EXIF data from images
 *
 * @param array $file Uploaded file array.
 * @return array Modified file array.
 */
function trendtoday_strip_exif_data( $file ) {
    // Check if strip EXIF is enabled
    $strip_exif = get_option( 'trendtoday_image_strip_exif', '1' );
    if ( $strip_exif !== '1' ) {
        return $file;
    }
    
    // Only for JPEG images
    if ( $file['type'] !== 'image/jpeg' && $file['type'] !== 'image/jpg' ) {
        return $file;
    }
    
    // Load image
    $image = wp_get_image_editor( $file['tmp_name'] );
    if ( is_wp_error( $image ) ) {
        return $file;
    }
    
    // Save without EXIF (this strips EXIF automatically)
    $saved = $image->save( $file['tmp_name'] );
    if ( is_wp_error( $saved ) ) {
        return $file;
    }
    
    // Update file size
    $file['size'] = filesize( $file['tmp_name'] );
    
    return $file;
}
add_filter( 'wp_handle_upload_prefilter', 'trendtoday_strip_exif_data', 20 );

/**
 * Get WebP URL for attachment
 *
 * @param string $url Original image URL.
 * @param int $attachment_id Attachment ID.
 * @return string WebP URL or original URL.
 */
function trendtoday_get_webp_url( $url, $attachment_id ) {
    // Check if WebP is enabled and supported
    $webp_enabled = get_option( 'trendtoday_image_webp_enabled', '1' );
    if ( $webp_enabled !== '1' || ! trendtoday_webp_supported() ) {
        return $url;
    }
    
    // Check if WebP version exists
    $webp_file = get_post_meta( $attachment_id, '_webp_file', true );
    if ( ! $webp_file || ! file_exists( $webp_file ) ) {
        return $url;
    }
    
    // Get upload directory
    $upload_dir = wp_upload_dir();
    $webp_path = str_replace( $upload_dir['basedir'], $upload_dir['baseurl'], $webp_file );
    
    return $webp_path;
}

/**
 * Add WebP support to image srcset
 *
 * @param array $sources Image sources array.
 * @param array $size_array Array of width and height values.
 * @param string $image_src Image source URL.
 * @param array $image_meta Image metadata.
 * @param int $attachment_id Attachment ID.
 * @return array Modified sources array.
 */
function trendtoday_add_webp_to_srcset( $sources, $size_array, $image_src, $image_meta, $attachment_id ) {
    $webp_enabled = get_option( 'trendtoday_image_webp_enabled', '1' );
    if ( $webp_enabled !== '1' || ! trendtoday_webp_supported() ) {
        return $sources;
    }
    
    // Check if WebP version exists
    $webp_file = get_post_meta( $attachment_id, '_webp_file', true );
    if ( ! $webp_file || ! file_exists( $webp_file ) ) {
        return $sources;
    }
    
    // Add WebP versions to srcset
    $upload_dir = wp_upload_dir();
    $webp_base_url = str_replace( $upload_dir['basedir'], $upload_dir['baseurl'], dirname( $webp_file ) );
    $webp_filename = basename( $webp_file );
    
    foreach ( $sources as $width => $source ) {
        // Create WebP URL for this size
        $sources[ $width ]['url'] = $webp_base_url . '/' . $webp_filename;
        $sources[ $width ]['descriptor'] = 'w';
    }
    
    return $sources;
}
add_filter( 'wp_calculate_image_srcset', 'trendtoday_add_webp_to_srcset', 10, 5 );

/**
 * Add WebP picture element support
 * This adds a <picture> element with WebP source and fallback
 *
 * @param string $html Image HTML.
 * @param int $attachment_id Attachment ID.
 * @param string $size Image size.
 * @param bool $icon Whether this is an icon.
 * @return string Modified HTML.
 */
function trendtoday_add_webp_picture_element( $html, $attachment_id, $size, $icon ) {
    $webp_enabled = get_option( 'trendtoday_image_webp_enabled', '1' );
    if ( $webp_enabled !== '1' || ! trendtoday_webp_supported() || $icon ) {
        return $html;
    }
    
    // Check if WebP version exists
    $webp_file = get_post_meta( $attachment_id, '_webp_file', true );
    if ( ! $webp_file || ! file_exists( $webp_file ) ) {
        return $html;
    }
    
    // Get original image URL
    $original_url = wp_get_attachment_image_url( $attachment_id, $size );
    if ( ! $original_url ) {
        return $html;
    }
    
    // Get WebP URL
    $webp_url = trendtoday_get_webp_url( $original_url, $attachment_id );
    if ( $webp_url === $original_url ) {
        return $html;
    }
    
    // Wrap in picture element with WebP source
    $picture_html = '<picture>';
    $picture_html .= '<source srcset="' . esc_url( $webp_url ) . '" type="image/webp">';
    $picture_html .= $html;
    $picture_html .= '</picture>';
    
    return $picture_html;
}
// Note: This filter is commented out because it may conflict with WordPress's built-in responsive images
// Uncomment if you want to use <picture> elements instead of srcset
// add_filter( 'wp_get_attachment_image', 'trendtoday_add_webp_picture_element', 10, 4 );

/**
 * Regenerate image sizes for a single attachment
 *
 * @param int $attachment_id Attachment ID.
 * @return bool|WP_Error True on success, WP_Error on failure.
 */
function trendtoday_regenerate_image( $attachment_id ) {
    $file = get_attached_file( $attachment_id );
    if ( ! $file || ! file_exists( $file ) ) {
        return new WP_Error( 'no_file', __( 'File not found.', 'trendtoday' ) );
    }
    
    // Regenerate metadata and sizes
    $metadata = wp_generate_attachment_metadata( $attachment_id, $file );
    if ( is_wp_error( $metadata ) ) {
        return $metadata;
    }
    
    wp_update_attachment_metadata( $attachment_id, $metadata );
    
    // Convert to WebP if enabled
    if ( get_option( 'trendtoday_image_webp_enabled', '1' ) === '1' ) {
        trendtoday_convert_to_webp( $metadata, $attachment_id );
    }
    
    return true;
}

/**
 * Get image optimization statistics
 *
 * @return array Statistics array.
 */
function trendtoday_get_image_stats() {
    $stats = array(
        'total_images' => 0,
        'total_size' => 0,
        'optimized_images' => 0,
        'webp_images' => 0,
        'recent_optimized' => array(),
        'settings_status' => array(),
    );
    
    // Get settings status
    $stats['settings_status'] = array(
        'auto_resize' => get_option( 'trendtoday_image_auto_resize', '1' ) === '1',
        'webp_enabled' => get_option( 'trendtoday_image_webp_enabled', '1' ) === '1',
        'strip_exif' => get_option( 'trendtoday_image_strip_exif', '1' ) === '1',
        'max_width' => get_option( 'trendtoday_image_max_width', 1920 ),
        'max_height' => get_option( 'trendtoday_image_max_height', 1080 ),
        'jpeg_quality' => get_option( 'trendtoday_image_jpeg_quality', 85 ),
        'webp_quality' => get_option( 'trendtoday_image_webp_quality', 85 ),
    );
    
    $attachments = get_posts( array(
        'post_type' => 'attachment',
        'post_mime_type' => 'image',
        'posts_per_page' => -1,
        'post_status' => 'inherit',
        'fields' => 'ids',
        'orderby' => 'date',
        'order' => 'DESC',
    ) );
    
    $stats['total_images'] = count( $attachments );
    $recent_count = 0;
    $max_recent = 5;
    
    foreach ( $attachments as $attachment_id ) {
        $file = get_attached_file( $attachment_id );
        if ( $file && file_exists( $file ) ) {
            $file_size = filesize( $file );
            $stats['total_size'] += $file_size;
            
            // Get image metadata
            $metadata = wp_get_attachment_metadata( $attachment_id );
            $width = isset( $metadata['width'] ) ? $metadata['width'] : 0;
            $height = isset( $metadata['height'] ) ? $metadata['height'] : 0;
            
            // Check if image is optimized (resized)
            $max_width = $stats['settings_status']['max_width'];
            $max_height = $stats['settings_status']['max_height'];
            $is_resized = ( $width > 0 && $height > 0 ) && ( $width <= $max_width && $height <= $max_height );
            
            if ( $is_resized ) {
                $stats['optimized_images']++;
            }
            
            // Check if has WebP version
            $webp_file = get_post_meta( $attachment_id, '_webp_file', true );
            $has_webp = $webp_file && file_exists( $webp_file );
            
            if ( $has_webp ) {
                $stats['webp_images']++;
            }
            
            // Get recent optimized images (last 5)
            if ( $recent_count < $max_recent && ( $is_resized || $has_webp ) ) {
                $image_url = wp_get_attachment_image_url( $attachment_id, 'thumbnail' );
                $webp_url = $has_webp ? trendtoday_get_webp_url( $image_url, $attachment_id ) : '';
                
                $stats['recent_optimized'][] = array(
                    'id' => $attachment_id,
                    'title' => get_the_title( $attachment_id ),
                    'url' => $image_url,
                    'webp_url' => $webp_url,
                    'has_webp' => $has_webp,
                    'is_resized' => $is_resized,
                    'width' => $width,
                    'height' => $height,
                    'size' => round( $file_size / 1024, 2 ), // KB
                    'date' => get_the_date( 'Y-m-d H:i', $attachment_id ),
                );
                $recent_count++;
            }
        }
    }
    
    // Convert bytes to MB
    $stats['total_size_mb'] = round( $stats['total_size'] / 1024 / 1024, 2 );
    
    // Calculate optimization percentage
    if ( $stats['total_images'] > 0 ) {
        $stats['optimization_percentage'] = round( ( $stats['optimized_images'] / $stats['total_images'] ) * 100, 1 );
        $stats['webp_percentage'] = round( ( $stats['webp_images'] / $stats['total_images'] ) * 100, 1 );
    } else {
        $stats['optimization_percentage'] = 0;
        $stats['webp_percentage'] = 0;
    }
    
    return $stats;
}
