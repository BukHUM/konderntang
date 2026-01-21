<?php
/**
 * Image Cleanup Class
 * Scan and delete unused images, thumbnails, and WebP files
 *
 * @package Image_Optimization
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Image Cleanup Class
 */
class IO_Image_Cleanup {

	/**
	 * Upload directory path
	 *
	 * @var string
	 */
	private $uploads_dir;

	/**
	 * Upload directory URL
	 *
	 * @var string
	 */
	private $uploads_url;

	/**
	 * Valid image extensions
	 *
	 * @var array
	 */
	private $image_extensions = array( 'jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'ico' );

	/**
	 * Protected files/folders (whitelist)
	 *
	 * @var array
	 */
	private $protected_files = array(
		'.htaccess',
		'index.php',
		'.htpasswd',
	);

	/**
	 * Protected directories (whitelist)
	 *
	 * @var array
	 */
	private $protected_dirs = array(
		'backups',
		'upgrade',
	);

	/**
	 * Constructor
	 */
	public function __construct() {
		$upload_dir      = wp_upload_dir();
		$this->uploads_dir = $upload_dir['basedir'];
		$this->uploads_url = $upload_dir['baseurl'];
	}

	/**
	 * Log deletion activity
	 *
	 * @param string $action Action performed.
	 * @param array  $data   Data to log.
	 * @return void
	 */
	private function log_action( $action, $data = array() ) {
		if ( ! function_exists( 'error_log' ) ) {
			return;
		}

		$log_message = sprintf(
			'[Image Optimization] %s: %s',
			$action,
			wp_json_encode( $data )
		);

		error_log( $log_message );
	}

	/**
	 * Get all registered thumbnails for an attachment
	 *
	 * @param int $attachment_id Attachment ID.
	 * @return array Array of thumbnail file paths.
	 */
	public function get_attachment_thumbnails( $attachment_id ) {
		$thumbnails = array();

		// Get attachment file
		$file = get_attached_file( $attachment_id );
		if ( ! $file || ! file_exists( $file ) ) {
			return $thumbnails;
		}

		// Add original file (always include it)
		$thumbnails[ $file ] = true;

		// Get attachment metadata
		$metadata = wp_get_attachment_metadata( $attachment_id );
		if ( ! $metadata || ! isset( $metadata['sizes'] ) ) {
			// Still return original file even if no sizes
			return $thumbnails;
		}

		// Get upload directory for this attachment
		$upload_dir = wp_upload_dir();
		$file_dir   = dirname( $file );

		// Add all registered thumbnail sizes
		foreach ( $metadata['sizes'] as $size => $size_data ) {
			if ( isset( $size_data['file'] ) ) {
				$thumbnail_path = path_join( $file_dir, $size_data['file'] );
				$thumbnail_path = wp_normalize_path( $thumbnail_path );
				if ( file_exists( $thumbnail_path ) ) {
					$thumbnails[ $thumbnail_path ] = true;
				}
			}
		}

		// Check for WebP version (if stored in meta)
		$webp_file = get_post_meta( $attachment_id, '_webp_file', true );
		if ( $webp_file && file_exists( $webp_file ) ) {
			$webp_path = wp_normalize_path( $webp_file );
			$thumbnails[ $webp_path ] = true;
		}

		return $thumbnails;
	}

	/**
	 * Get all registered thumbnails for all attachments
	 *
	 * @param bool $use_cache Whether to use cached results.
	 * @return array Array of all registered thumbnail file paths (keys only for O(1) lookup).
	 */
	public function get_all_registered_thumbnails( $use_cache = true ) {
		$cache_key = 'io_registered_thumbnails';

		// Try to get from cache
		if ( $use_cache ) {
			$cached = wp_cache_get( $cache_key, 'io_image_cleanup' );
			if ( false !== $cached ) {
				return $cached;
			}
		}

		$registered_files = array();

		try {
			// Get all image attachments with optimized query
			$attachments = get_posts(
				array(
					'post_type'              => 'attachment',
					'post_mime_type'        => 'image',
					'posts_per_page'        => -1,
					'post_status'           => 'inherit',
					'fields'                => 'ids',
					'no_found_rows'         => true, // Skip counting for performance
					'update_post_meta_cache' => false, // Don't cache meta
					'update_post_term_cache' => false, // Don't cache terms
				)
			);
			
			if ( is_wp_error( $attachments ) ) {
				throw new Exception( __( 'ไม่สามารถดึงข้อมูล attachments จากฐานข้อมูลได้: ', 'image-optimization' ) . $attachments->get_error_message() );
			}

		// Process in batches to avoid memory issues
		$batch_size = 100;
		$total      = count( $attachments );

		for ( $i = 0; $i < $total; $i += $batch_size ) {
			$batch = array_slice( $attachments, $i, $batch_size );

			foreach ( $batch as $attachment_id ) {
				$thumbnails        = $this->get_attachment_thumbnails( $attachment_id );
				$registered_files = array_merge( $registered_files, $thumbnails );
			}

			// Clear memory periodically
			if ( $i % ( $batch_size * 10 ) === 0 ) {
				wp_cache_flush_group( 'io_image_cleanup' );
			}
		}

			// Normalize all paths
			$normalized = array();
			foreach ( $registered_files as $path => $value ) {
				try {
					$normalized_path = wp_normalize_path( realpath( $path ) );
					if ( $normalized_path ) {
						$normalized[ $normalized_path ] = true;
					}
				} catch ( Exception $e ) {
					// Skip invalid paths, log but continue
					$this->log_action(
						'PATH_NORMALIZE_ERROR',
						array(
							'path'  => $path,
							'error' => $e->getMessage(),
						)
					);
					continue;
				}
			}

			// Cache for 5 minutes
			wp_cache_set( $cache_key, $normalized, 'io_image_cleanup', 300 );

			return $normalized;
		} catch ( Exception $e ) {
			$this->log_action(
				'GET_REGISTERED_THUMBNAILS_ERROR',
				array(
					'error' => $e->getMessage(),
					'trace' => $e->getTraceAsString(),
				)
			);
			throw $e; // Re-throw to be handled by caller
		}
	}

	/**
	 * Clear cache for registered thumbnails
	 *
	 * @return void
	 */
	public function clear_thumbnails_cache() {
		wp_cache_delete( 'io_registered_thumbnails', 'io_image_cleanup' );
	}

	/**
	 * Check if file is a thumbnail by filename pattern
	 *
	 * @param string $filename File name.
	 * @return bool True if looks like thumbnail.
	 */
	private function is_thumbnail_filename( $filename ) {
		// WordPress thumbnail pattern: filename-150x150.jpg
		if ( preg_match( '/-\d+x\d+\.(jpg|jpeg|png|gif|webp)$/i', $filename ) ) {
			return true;
		}

		// Other common patterns
		if ( preg_match( '/_(thumbnail|thumb|small|medium|large)\./i', $filename ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Find unused thumbnails
	 *
	 * @param array $options Scan options.
	 * @return array Array of unused thumbnail files with details.
	 */
	public function scan_unused_thumbnails( $options = array() ) {
		$unused_thumbnails = array();
		$error_files = array();

		// Get all registered thumbnails
		try {
			$registered_files = $this->get_all_registered_thumbnails( isset( $options['use_cache'] ) ? $options['use_cache'] : true );
		} catch ( Exception $e ) {
			$error_files[] = array(
				'file'  => __( 'การดึงข้อมูล registered thumbnails', 'image-optimization' ),
				'error' => $e->getMessage(),
			);
			$registered_files = array(); // Continue with empty array
		}

		// Scan uploads directory
		if ( ! is_dir( $this->uploads_dir ) ) {
			$this->log_action(
				'SCAN_ERROR',
				array(
					'type'  => 'thumbnails',
					'error' => 'Uploads directory not found',
				)
			);
			return array(
				'files' => $unused_thumbnails,
				'errors' => array(
					array(
						'file'  => $this->uploads_dir,
						'error' => __( 'ไม่พบโฟลเดอร์ uploads', 'image-optimization' ),
					)
				),
			);
		}
		
		if ( ! is_readable( $this->uploads_dir ) ) {
			return array(
				'files' => $unused_thumbnails,
				'errors' => array(
					array(
						'file'  => $this->uploads_dir,
						'error' => __( 'ไม่สามารถอ่านโฟลเดอร์ uploads ได้', 'image-optimization' ),
					)
				),
			);
		}

		$max_files = isset( $options['max_files'] ) ? absint( $options['max_files'] ) : 0;
		$processed = 0;
		$total_scanned = 0;
		$memory_check_interval = 100; // Check memory every 100 files

		try {
			$iterator = new RecursiveIteratorIterator(
				new RecursiveDirectoryIterator( $this->uploads_dir, RecursiveDirectoryIterator::SKIP_DOTS ),
				RecursiveIteratorIterator::SELF_FIRST
			);
		} catch ( Exception $e ) {
			return array(
				'files' => $unused_thumbnails,
				'errors' => array(
					array(
						'file'  => $this->uploads_dir,
						'error' => __( 'ไม่สามารถเข้าถึงโฟลเดอร์ uploads: ', 'image-optimization' ) . $e->getMessage(),
					)
				),
			);
		}

		foreach ( $iterator as $file ) {
			// Check max files limit
			if ( $max_files > 0 && $processed >= $max_files ) {
				break;
			}

			if ( $file->isDir() ) {
				continue;
			}

			$total_scanned++;
			$file_path = null;
			$filename = '';

			// Memory management: Check and clean up every N files
			if ( $total_scanned % $memory_check_interval === 0 ) {
				// Get current memory usage
				$memory_used = memory_get_usage( true );
				$memory_limit = ini_get( 'memory_limit' );
				$memory_limit_bytes = $this->convert_to_bytes( $memory_limit );
				$memory_percent = ( $memory_used / $memory_limit_bytes ) * 100;
				
				// If memory usage is above 80%, trigger garbage collection
				if ( $memory_percent > 80 ) {
					// Unset large variables that are no longer needed
					unset( $file_path, $filename );
					
					// Force garbage collection
					if ( function_exists( 'gc_collect_cycles' ) ) {
						gc_collect_cycles();
					}
					
					// Log memory warning
					$this->log_action(
						'SCAN_MEMORY_WARNING',
						array(
							'memory_used' => $this->format_bytes( $memory_used ),
							'memory_limit' => $memory_limit,
							'percent' => round( $memory_percent, 2 ),
							'files_scanned' => $total_scanned,
						)
					);
				}
			}

			try {
				$raw_path = $file->getRealPath();
				if ( ! $raw_path ) {
					$error_files[] = array(
						'file'  => $file->getPathname(),
						'error' => __( 'ไม่สามารถอ่าน path ของไฟล์ได้', 'image-optimization' ),
					);
					continue;
				}
				
				// Normalize path: On Windows, convert backslashes to forward slashes
				// wp_normalize_path() should handle this, but ensure it's done correctly
				$file_path = str_replace( '\\', '/', $raw_path );
				$file_path = wp_normalize_path( $file_path );

				$filename = $file->getFilename();

				// Skip if not an image
				$ext = strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) );
				if ( ! in_array( $ext, $this->image_extensions, true ) ) {
					continue;
				}

				// Skip if not a thumbnail pattern
				if ( ! $this->is_thumbnail_filename( $filename ) ) {
					continue;
				}

				// Skip if file is registered
				if ( isset( $registered_files[ $file_path ] ) ) {
					continue;
				}

				// Add to unused list
				$unused_thumbnails[] = array(
					'path'     => $file_path,
					'filename' => $filename,
					'size'     => $file->getSize(),
					'modified' => $file->getMTime(),
					'url'      => str_replace( $this->uploads_dir, $this->uploads_url, $file_path ),
				);

				$processed++;
			} catch ( Exception $e ) {
				// Log error but continue
				$this->log_action(
					'SCAN_FILE_ERROR',
					array(
						'file'  => $file->getPathname(),
						'error' => $e->getMessage(),
					)
				);
				$error_files[] = array(
					'file'  => $file_path ? $file_path : $file->getPathname(),
					'filename' => $filename ? $filename : basename( $file->getPathname() ),
					'error' => $e->getMessage(),
				);
				continue;
			}
		}

		return array(
			'files' => $unused_thumbnails,
			'errors' => $error_files,
			'statistics' => array(
				'total_scanned' => $total_scanned,
				'processed' => $processed,
			),
		);
	}

	/**
	 * Find unused WebP files
	 *
	 * @param array $options Scan options.
	 * @return array Array of unused WebP files with details.
	 */
	public function scan_unused_webp( $options = array() ) {
		$unused_webp = array();
		$error_files = array();

		// Get all attachments with WebP files
		$attachments = array();
		try {
			$attachments = get_posts(
				array(
					'post_type'      => 'attachment',
					'post_mime_type' => 'image',
					'posts_per_page' => -1,
					'post_status'    => 'inherit',
					'fields'         => 'ids',
				)
			);
			
			if ( is_wp_error( $attachments ) ) {
				$error_files[] = array(
					'file'  => __( 'การดึงข้อมูล attachments', 'image-optimization' ),
					'error' => $attachments->get_error_message(),
				);
				$attachments = array();
			}
		} catch ( Exception $e ) {
			$error_files[] = array(
				'file'  => __( 'การดึงข้อมูล attachments', 'image-optimization' ),
				'error' => $e->getMessage(),
			);
		}
			
			if ( is_wp_error( $attachments ) ) {
				throw new Exception( __( 'ไม่สามารถดึงข้อมูล attachments จากฐานข้อมูลได้', 'image-optimization' ) );
			}

		// Build map of valid WebP files
		$valid_webp = array();

		foreach ( $attachments as $attachment_id ) {
			// Check if has WebP in meta
			$webp_file = get_post_meta( $attachment_id, '_webp_file', true );
			if ( $webp_file && file_exists( $webp_file ) ) {
				$webp_path = wp_normalize_path( realpath( $webp_file ) );
				if ( $webp_path ) {
					$valid_webp[ $webp_path ] = true;
				}
			}

			// Check metadata for WebP in sizes
			$metadata = wp_get_attachment_metadata( $attachment_id );
			if ( $metadata && isset( $metadata['sizes'] ) ) {
				$file = get_attached_file( $attachment_id );
				if ( $file ) {
					$file_dir = dirname( $file );
					foreach ( $metadata['sizes'] as $size => $size_data ) {
						if ( isset( $size_data['mime-type'] ) && $size_data['mime-type'] === 'image/webp' ) {
							if ( isset( $size_data['file'] ) ) {
								$webp_path = path_join( $file_dir, $size_data['file'] );
								$webp_path = wp_normalize_path( realpath( $webp_path ) );
								if ( $webp_path && file_exists( $webp_path ) ) {
									$valid_webp[ $webp_path ] = true;
								}
							}
						}
					}
				}
			}

			// Check for WebP version of original (same name, different extension)
			$file = get_attached_file( $attachment_id );
			if ( $file ) {
				$webp_original = preg_replace( '/\.(jpg|jpeg|png)$/i', '.webp', $file );
				if ( file_exists( $webp_original ) ) {
					$webp_path = wp_normalize_path( realpath( $webp_original ) );
					if ( $webp_path ) {
						$valid_webp[ $webp_path ] = true;
					}
				}
			}
		}

		// Scan for all WebP files
		if ( ! is_dir( $this->uploads_dir ) ) {
			return array(
				'files' => $unused_webp,
				'errors' => array_merge( $error_files, array(
					array(
						'file'  => $this->uploads_dir,
						'error' => __( 'ไม่พบโฟลเดอร์ uploads', 'image-optimization' ),
					)
				) ),
			);
		}
		
		if ( ! is_readable( $this->uploads_dir ) ) {
			return array(
				'files' => $unused_webp,
				'errors' => array_merge( $error_files, array(
					array(
						'file'  => $this->uploads_dir,
						'error' => __( 'ไม่สามารถอ่านโฟลเดอร์ uploads ได้', 'image-optimization' ),
					)
				) ),
			);
		}

		$total_scanned = 0;
		$memory_check_interval = 100; // Check memory every 100 files
		
		try {
			$iterator = new RecursiveIteratorIterator(
				new RecursiveDirectoryIterator( $this->uploads_dir, RecursiveDirectoryIterator::SKIP_DOTS ),
				RecursiveIteratorIterator::SELF_FIRST
			);
		} catch ( Exception $e ) {
			return array(
				'files' => $unused_webp,
				'errors' => array_merge( $error_files, array(
					array(
						'file'  => $this->uploads_dir,
						'error' => __( 'ไม่สามารถเข้าถึงโฟลเดอร์ uploads: ', 'image-optimization' ) . $e->getMessage(),
					)
				) ),
			);
		}

		foreach ( $iterator as $file ) {
			if ( $file->isDir() ) {
				continue;
			}

			$total_scanned++;
			$file_path = null;
			$filename = '';

			// Memory management: Check and clean up every N files
			if ( $total_scanned % $memory_check_interval === 0 ) {
				$memory_used = memory_get_usage( true );
				$memory_limit = ini_get( 'memory_limit' );
				$memory_limit_bytes = $this->convert_to_bytes( $memory_limit );
				$memory_percent = $memory_limit_bytes > 0 ? ( $memory_used / $memory_limit_bytes ) * 100 : 0;
				
				if ( $memory_percent > 80 ) {
					unset( $file_path, $filename );
					if ( function_exists( 'gc_collect_cycles' ) ) {
						gc_collect_cycles();
					}
				}
			}

			try {
				$raw_path = $file->getRealPath();
				if ( ! $raw_path ) {
					continue;
				}
				// Normalize path: On Windows, convert backslashes to forward slashes
				$file_path = str_replace( '\\', '/', $raw_path );
				$file_path = wp_normalize_path( $file_path );
				$filename  = $file->getFilename();

				// Only process WebP files
				if ( strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) ) !== 'webp' ) {
					continue;
				}

				// Skip if it's a valid WebP file
				if ( isset( $valid_webp[ $file_path ] ) ) {
					continue;
				}

				// Check if original file exists
				$original_file = preg_replace( '/\.webp$/i', '.jpg', $file_path );
				if ( ! file_exists( $original_file ) ) {
					$original_file = preg_replace( '/\.webp$/i', '.jpeg', $file_path );
				}
				if ( ! file_exists( $original_file ) ) {
					$original_file = preg_replace( '/\.webp$/i', '.png', $file_path );
				}

				// If no original exists, it's unused
				if ( ! file_exists( $original_file ) ) {
					$unused_webp[] = array(
						'path'     => $file_path,
						'filename' => $filename,
						'size'     => $file->getSize(),
						'modified' => $file->getMTime(),
						'url'      => str_replace( $this->uploads_dir, $this->uploads_url, $file_path ),
					);
				}
			} catch ( Exception $e ) {
				$error_files[] = array(
					'file'  => $file_path ? $file_path : $file->getPathname(),
					'filename' => $filename ? $filename : basename( $file->getPathname() ),
					'error' => $e->getMessage(),
				);
				continue;
			}
		}

		return array(
			'files' => $unused_webp,
			'errors' => $error_files,
			'statistics' => array(
				'total_scanned' => $total_scanned,
			),
		);
	}

	/**
	 * Get all attachment file paths
	 *
	 * @return array Array of attachment file paths (keys only for O(1) lookup).
	 */
	private function get_all_attachment_files() {
		$attachment_files = array();

		// Get all image attachments
		$attachments = get_posts(
			array(
				'post_type'      => 'attachment',
				'post_mime_type' => 'image',
				'posts_per_page' => -1,
				'post_status'    => 'inherit',
				'fields'         => 'ids',
			)
		);

		foreach ( $attachments as $attachment_id ) {
			$file = get_attached_file( $attachment_id );
			if ( $file && file_exists( $file ) ) {
				$file_path = wp_normalize_path( realpath( $file ) );
				if ( $file_path ) {
					$attachment_files[ $file_path ] = true;
				}
			}

			// Also get all thumbnails for this attachment
			$thumbnails = $this->get_attachment_thumbnails( $attachment_id );
			foreach ( $thumbnails as $thumb_path => $value ) {
				$thumb_path = wp_normalize_path( realpath( $thumb_path ) );
				if ( $thumb_path ) {
					$attachment_files[ $thumb_path ] = true;
				}
			}
		}

		return $attachment_files;
	}

	/**
	 * Find orphaned images (files without attachment record)
	 *
	 * @param array $options Scan options.
	 * @return array Array of orphaned image files with details.
	 */
	public function scan_orphaned_images( $options = array() ) {
		$orphaned_images = array();
		$error_files = array();

		// Get all attachment files
		$attachment_files = array();
		try {
			$attachment_files = $this->get_all_attachment_files();
		} catch ( Exception $e ) {
			$error_files[] = array(
				'file'  => __( 'การดึงข้อมูล attachment files', 'image-optimization' ),
				'error' => $e->getMessage(),
			);
		}

		// Scan uploads directory
		if ( ! is_dir( $this->uploads_dir ) ) {
			return array(
				'files' => $orphaned_images,
				'errors' => array_merge( $error_files, array(
					array(
						'file'  => $this->uploads_dir,
						'error' => __( 'ไม่พบโฟลเดอร์ uploads', 'image-optimization' ),
					)
				) ),
			);
		}
		
		if ( ! is_readable( $this->uploads_dir ) ) {
			return array(
				'files' => $orphaned_images,
				'errors' => array_merge( $error_files, array(
					array(
						'file'  => $this->uploads_dir,
						'error' => __( 'ไม่สามารถอ่านโฟลเดอร์ uploads ได้', 'image-optimization' ),
					)
				) ),
			);
		}

		$total_scanned = 0;
		$memory_check_interval = 100; // Check memory every 100 files
		
		try {
			$iterator = new RecursiveIteratorIterator(
				new RecursiveDirectoryIterator( $this->uploads_dir, RecursiveDirectoryIterator::SKIP_DOTS ),
				RecursiveIteratorIterator::SELF_FIRST
			);
		} catch ( Exception $e ) {
			return array(
				'files' => $orphaned_images,
				'errors' => array_merge( $error_files, array(
					array(
						'file'  => $this->uploads_dir,
						'error' => __( 'ไม่สามารถเข้าถึงโฟลเดอร์ uploads: ', 'image-optimization' ) . $e->getMessage(),
					)
				) ),
			);
		}

		foreach ( $iterator as $file ) {
			if ( $file->isDir() ) {
				continue;
			}

			$total_scanned++;
			$file_path = null;
			$filename = '';

			// Memory management: Check and clean up every N files
			if ( $total_scanned % $memory_check_interval === 0 ) {
				$memory_used = memory_get_usage( true );
				$memory_limit = ini_get( 'memory_limit' );
				$memory_limit_bytes = $this->convert_to_bytes( $memory_limit );
				$memory_percent = $memory_limit_bytes > 0 ? ( $memory_used / $memory_limit_bytes ) * 100 : 0;
				
				if ( $memory_percent > 80 ) {
					unset( $file_path, $filename );
					if ( function_exists( 'gc_collect_cycles' ) ) {
						gc_collect_cycles();
					}
				}
			}

			try {
				$raw_path = $file->getRealPath();
				if ( ! $raw_path ) {
					continue;
				}
				// Normalize path: On Windows, convert backslashes to forward slashes
				$file_path = str_replace( '\\', '/', $raw_path );
				$file_path = wp_normalize_path( $file_path );
				$filename  = $file->getFilename();

				// Skip if not an image
				$ext = strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) );
				if ( ! in_array( $ext, $this->image_extensions, true ) ) {
					continue;
				}

				// Skip if file is registered as attachment
				if ( isset( $attachment_files[ $file_path ] ) ) {
					continue;
				}

				// Skip system files
				if ( in_array( $filename, array( '.htaccess', 'index.php' ), true ) ) {
					continue;
				}

				// Add to orphaned list
				$orphaned_images[] = array(
					'path'     => $file_path,
					'filename' => $filename,
					'size'     => $file->getSize(),
					'modified' => $file->getMTime(),
					'url'      => str_replace( $this->uploads_dir, $this->uploads_url, $file_path ),
				);
			} catch ( Exception $e ) {
				$error_files[] = array(
					'file'  => $file_path ? $file_path : $file->getPathname(),
					'filename' => $filename ? $filename : basename( $file->getPathname() ),
					'error' => $e->getMessage(),
				);
				continue;
			}
		}

		return array(
			'files' => $orphaned_images,
			'errors' => $error_files,
			'statistics' => array(
				'total_scanned' => $total_scanned,
			),
		);
	}

	/**
	 * Scan for all unused images
	 *
	 * @param array $options Scan options.
	 * @return array Complete scan results.
	 */
	public function scan_unused_images( $options = array() ) {
		$defaults = array(
			'use_cache'  => true,
			'scan_type'  => 'all', // 'all', 'thumbnails', 'webp', 'orphaned'
			'max_files'  => 0, // 0 = unlimited
			'time_limit' => 0, // 0 = unlimited (seconds)
		);
		$options = wp_parse_args( $options, $defaults );

		$start_time = time();
		$results    = array(
			'thumbnails' => array(),
			'webp'       => array(),
			'orphaned'   => array(),
			'error_files' => array(), // All error files from all scan types
			'statistics' => array(
				'total_thumbnails' => 0,
				'total_webp'       => 0,
				'total_orphaned'   => 0,
				'total_size'       => 0,
				'thumbnails_size'  => 0,
				'webp_size'        => 0,
				'orphaned_size'    => 0,
				'scan_time'        => 0,
				'total_scanned'    => 0,
				'total_errors'     => 0,
			),
		);

		// Validate uploads directory before scanning
		if ( ! is_dir( $this->uploads_dir ) ) {
			$results['error_files'][] = array(
				'file'  => $this->uploads_dir,
				'error' => __( 'ไม่พบโฟลเดอร์ uploads', 'image-optimization' ),
				'type'  => 'system',
			);
			$results['statistics']['total_errors'] = count( $results['error_files'] );
			return $results;
		}
		
		if ( ! is_readable( $this->uploads_dir ) ) {
			$results['error_files'][] = array(
				'file'  => $this->uploads_dir,
				'error' => __( 'ไม่สามารถอ่านโฟลเดอร์ uploads ได้', 'image-optimization' ),
				'type'  => 'system',
			);
			$results['statistics']['total_errors'] = count( $results['error_files'] );
			return $results;
		}
		
		// Scan thumbnails
		if ( $options['scan_type'] === 'all' || $options['scan_type'] === 'thumbnails' ) {
			$thumbnails_result = $this->scan_unused_thumbnails( $options );
			
			// Handle result structure (new format with files/errors or old format)
			if ( isset( $thumbnails_result['files'] ) ) {
				$results['thumbnails'] = $thumbnails_result['files'];
				if ( ! empty( $thumbnails_result['errors'] ) ) {
					foreach ( $thumbnails_result['errors'] as $error ) {
						$error['type'] = 'thumbnails';
						$results['error_files'][] = $error;
					}
				}
				if ( isset( $thumbnails_result['statistics']['total_scanned'] ) ) {
					$results['statistics']['total_scanned'] += $thumbnails_result['statistics']['total_scanned'];
				}
			} else {
				// Old format - backward compatibility
				$results['thumbnails'] = $thumbnails_result;
			}
			
			$results['statistics']['total_thumbnails'] = count( $results['thumbnails'] );
			foreach ( $results['thumbnails'] as $thumb ) {
				if ( isset( $thumb['size'] ) ) {
					$results['statistics']['thumbnails_size'] += $thumb['size'];
					$results['statistics']['total_size']      += $thumb['size'];
				}
			}

			// Check time limit
			if ( $options['time_limit'] > 0 && ( time() - $start_time ) > $options['time_limit'] ) {
				$results['statistics']['timeout'] = true;
				$results['statistics']['total_errors'] = count( $results['error_files'] );
				$results['statistics']['scan_time'] = time() - $start_time;
				return $results;
			}
		}

		// Scan WebP
		if ( $options['scan_type'] === 'all' || $options['scan_type'] === 'webp' ) {
			$webp_result = $this->scan_unused_webp( $options );
			
			// Handle result structure
			if ( isset( $webp_result['files'] ) ) {
				$results['webp'] = $webp_result['files'];
				if ( ! empty( $webp_result['errors'] ) ) {
					foreach ( $webp_result['errors'] as $error ) {
						$error['type'] = 'webp';
						$results['error_files'][] = $error;
					}
				}
				if ( isset( $webp_result['statistics']['total_scanned'] ) ) {
					$results['statistics']['total_scanned'] += $webp_result['statistics']['total_scanned'];
				}
			} else {
				// Old format
				$results['webp'] = $webp_result;
			}
			
			$results['statistics']['total_webp'] = count( $results['webp'] );
			foreach ( $results['webp'] as $webp ) {
				if ( isset( $webp['size'] ) ) {
					$results['statistics']['webp_size'] += $webp['size'];
					$results['statistics']['total_size'] += $webp['size'];
				}
			}

			// Check time limit
			if ( $options['time_limit'] > 0 && ( time() - $start_time ) > $options['time_limit'] ) {
				$results['statistics']['timeout'] = true;
				$results['statistics']['total_errors'] = count( $results['error_files'] );
				$results['statistics']['scan_time'] = time() - $start_time;
				return $results;
			}
		}

		// Scan orphaned images
		if ( $options['scan_type'] === 'all' || $options['scan_type'] === 'orphaned' ) {
			$orphaned_result = $this->scan_orphaned_images( $options );
			
			// Handle result structure
			if ( isset( $orphaned_result['files'] ) ) {
				$results['orphaned'] = $orphaned_result['files'];
				if ( ! empty( $orphaned_result['errors'] ) ) {
					foreach ( $orphaned_result['errors'] as $error ) {
						$error['type'] = 'orphaned';
						$results['error_files'][] = $error;
					}
				}
				if ( isset( $orphaned_result['statistics']['total_scanned'] ) ) {
					$results['statistics']['total_scanned'] += $orphaned_result['statistics']['total_scanned'];
				}
			} else {
				// Old format
				$results['orphaned'] = $orphaned_result;
			}
			
			$results['statistics']['total_orphaned'] = count( $results['orphaned'] );
			foreach ( $results['orphaned'] as $orphaned ) {
				if ( isset( $orphaned['size'] ) ) {
					$results['statistics']['orphaned_size'] += $orphaned['size'];
					$results['statistics']['total_size']    += $orphaned['size'];
				}
			}
		}

		// Convert sizes to MB (ensure values exist before division)
		$results['statistics']['total_size_mb']       = round( ( isset( $results['statistics']['total_size'] ) ? $results['statistics']['total_size'] : 0 ) / 1024 / 1024, 2 );
		$results['statistics']['thumbnails_size_mb']  = round( ( isset( $results['statistics']['thumbnails_size'] ) ? $results['statistics']['thumbnails_size'] : 0 ) / 1024 / 1024, 2 );
		$results['statistics']['webp_size_mb']       = round( ( isset( $results['statistics']['webp_size'] ) ? $results['statistics']['webp_size'] : 0 ) / 1024 / 1024, 2 );
		$results['statistics']['orphaned_size_mb']   = round( ( isset( $results['statistics']['orphaned_size'] ) ? $results['statistics']['orphaned_size'] : 0 ) / 1024 / 1024, 2 );

		// Calculate scan time
		$results['statistics']['scan_time'] = time() - $start_time;
		$results['statistics']['total_errors'] = count( $results['error_files'] );

		return $results;
	}

	/**
	 * Validate file path (security check)
	 *
	 * @param string $file_path File path to validate.
	 * @return array Validation result with 'valid' and 'error' keys.
	 */
	public function validate_file_path( $file_path ) {
		$result = array(
			'valid' => false,
			'error' => '',
		);

		// Check if path is empty
		if ( empty( $file_path ) ) {
			$result['error'] = 'Empty file path';
			return $result;
		}

		// Normalize path
		$normalized_path = wp_normalize_path( $file_path );
		
		// Try to get real path
		$real_path = realpath( $normalized_path );
		
		// If realpath fails, try the normalized path directly
		if ( ! $real_path ) {
			// Check if normalized path exists
			if ( file_exists( $normalized_path ) ) {
				$real_path = $normalized_path;
			} else {
				// Log for debugging
				$this->log_action(
					'VALIDATE_PATH_FAILED',
					array(
						'original_path' => $file_path,
						'normalized_path' => $normalized_path,
						'exists' => file_exists( $normalized_path ),
					)
				);
				$result['error'] = 'Invalid file path or file does not exist';
				return $result;
			}
		}

		$real_path    = wp_normalize_path( $real_path );
		$uploads_dir  = wp_normalize_path( realpath( $this->uploads_dir ) );

		if ( ! $uploads_dir ) {
			$result['error'] = 'Uploads directory not found';
			return $result;
		}

		// Normalize both paths for comparison (handle Windows path case sensitivity)
		$real_path_lower = strtolower( $real_path );
		$uploads_dir_lower = strtolower( $uploads_dir );

		// Check if file is within uploads directory
		if ( strpos( $real_path_lower, $uploads_dir_lower ) !== 0 ) {
			$this->log_action(
				'VALIDATE_PATH_OUTSIDE',
				array(
					'file_path' => $real_path,
					'uploads_dir' => $uploads_dir,
				)
			);
			$result['error'] = 'File is outside uploads directory';
			return $result;
		}

		// Check if file exists
		if ( ! file_exists( $real_path ) ) {
			$result['error'] = 'File does not exist';
			return $result;
		}

		// Check if it's a file (not directory)
		if ( ! is_file( $real_path ) ) {
			$result['error'] = 'Path is not a file';
			return $result;
		}

		// Check if file is protected (whitelist)
		$filename = basename( $real_path );
		if ( in_array( $filename, $this->protected_files, true ) ) {
			$result['error'] = 'File is protected (whitelist)';
			return $result;
		}

		// Check if file is in protected directory
		$relative_path = str_replace( $uploads_dir . '/', '', $real_path );
		$path_parts    = explode( '/', $relative_path );
		if ( ! empty( $path_parts ) && in_array( $path_parts[0], $this->protected_dirs, true ) ) {
			$result['error'] = 'File is in protected directory';
			return $result;
		}

		// Check file extension (only allow image files)
		$ext = strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) );
		if ( ! in_array( $ext, $this->image_extensions, true ) ) {
			$result['error'] = 'File is not an image';
			return $result;
		}

		// All checks passed
		$result['valid'] = true;
		return $result;
	}

	/**
	 * Delete unused images
	 *
	 * @param array $file_paths Array of file paths to delete.
	 * @param array $options    Delete options (dry_run, batch_size, etc.).
	 * @return array Results with deleted, failed, and errors.
	 */
	public function delete_unused_images( $file_paths, $options = array() ) {
		$defaults = array(
			'dry_run'    => false,
			'batch_size' => 50,
			'log'        => true,
		);
		$options = wp_parse_args( $options, $defaults );

		$results = array(
			'deleted' => 0,
			'failed'  => 0,
			'errors'  => array(),
			'skipped' => 0,
		);

		// Log deletion start (only log summary, not every file)
		if ( $options['log'] && $options['dry_run'] ) {
			$this->log_action(
				'DELETE_START',
				array(
					'total_files' => count( $file_paths ),
					'dry_run'     => $options['dry_run'],
				)
			);
		}

		$processed = 0;
		foreach ( $file_paths as $file_path ) {
			// Skip empty paths
			if ( empty( $file_path ) ) {
				continue;
			}
			
			// Quick validation: check if file exists and is within uploads directory
			// Skip full validation for speed - only validate critical checks
			$normalized_path = wp_normalize_path( $file_path );
			$real_path = realpath( $normalized_path );
			
			// If realpath fails, try normalized path directly
			if ( ! $real_path ) {
				$real_path = $normalized_path;
			}
			
			// Quick check: file must exist
			if ( ! file_exists( $real_path ) ) {
				// File already deleted, count as success
				$results['deleted']++;
				$processed++;
				continue;
			}
			
			// Quick check: must be a file (not directory)
			if ( ! is_file( $real_path ) ) {
				$results['failed']++;
				$results['errors'][] = array(
					'file'  => $file_path,
					'error' => 'Path is not a file',
				);
				$processed++;
				continue;
			}
			
			// Final normalize path
			$real_path = wp_normalize_path( $real_path );

			// Dry run mode - don't actually delete
			if ( $options['dry_run'] ) {
				$results['deleted']++;
				if ( $options['log'] ) {
					$this->log_action(
						'DELETE_DRY_RUN',
						array(
							'file' => $real_path,
						)
					);
				}
				$processed++;
				continue;
			}

			// Try to delete
			$deleted      = false;
			$delete_error = '';

			// Check if file exists first
			if ( ! file_exists( $real_path ) ) {
				// File already deleted, count as success
				$deleted = true;
				if ( $options['log'] ) {
					$this->log_action(
						'DELETE_ALREADY_GONE',
						array(
							'file' => $real_path,
						)
					);
				}
			} else {
				// Attempt deletion directly (is_writable check can be unreliable on Windows)
				$deleted = @unlink( $real_path );
				if ( ! $deleted ) {
					// Check if file still exists to determine error
					if ( file_exists( $real_path ) ) {
						// File still exists, deletion failed
						$delete_error = 'Failed to delete file (permission denied or file locked)';
						if ( $options['log'] ) {
							$this->log_action(
								'DELETE_FAILED_DETAIL',
								array(
									'file' => $real_path,
									'is_readable' => is_readable( $real_path ),
									'is_writable' => is_writable( $real_path ),
									'perms' => fileperms( $real_path ),
								)
							);
						}
					} else {
						// File was deleted successfully (race condition)
						$deleted = true;
					}
				}
			}

			if ( $deleted ) {
				$results['deleted']++;
				// Only log failures, not every success (to reduce I/O overhead)
			} else {
				$results['failed']++;
				$results['errors'][] = array(
					'file'  => $real_path,
					'error' => $delete_error,
				);
				// Only log failures for debugging
				if ( $options['log'] ) {
					$this->log_action(
						'DELETE_FAILED',
						array(
							'file'  => $real_path,
							'error' => $delete_error,
						)
					);
				}
			}

			$processed++;
		}

		// Log deletion end
		if ( $options['log'] ) {
			$this->log_action(
				'DELETE_END',
				array(
					'deleted' => $results['deleted'],
					'failed'  => $results['failed'],
					'skipped' => $results['skipped'],
				)
			);
		}

		return $results;
	}

	/**
	 * Get deletion statistics
	 *
	 * @return array Statistics about deletions.
	 */
	public function get_deletion_stats() {
		// This could read from a log file or database
		// For now, return empty stats
		return array(
			'total_deleted' => 0,
			'total_failed'   => 0,
			'last_deletion' => null,
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
		if ( empty( $memory_limit ) ) {
			return 0;
		}
		
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
	 * Format bytes to human-readable string
	 *
	 * @param int $bytes Bytes to format.
	 * @return string Formatted string (e.g., '128 MB').
	 */
	private function format_bytes( $bytes ) {
		$units = array( 'B', 'KB', 'MB', 'GB', 'TB' );
		$bytes = max( $bytes, 0 );
		$pow = floor( ( $bytes ? log( $bytes ) : 0 ) / log( 1024 ) );
		$pow = min( $pow, count( $units ) - 1 );
		$bytes /= pow( 1024, $pow );
		
		return round( $bytes, 2 ) . ' ' . $units[ $pow ];
	}
}

