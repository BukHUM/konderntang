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

		try {
			// Get all registered thumbnails
			$registered_files = $this->get_all_registered_thumbnails( isset( $options['use_cache'] ) ? $options['use_cache'] : true );

			// Scan uploads directory
			if ( ! is_dir( $this->uploads_dir ) ) {
				$this->log_action(
					'SCAN_ERROR',
					array(
						'type'  => 'thumbnails',
						'error' => 'Uploads directory not found',
					)
				);
				throw new Exception( __( 'ไม่พบโฟลเดอร์ uploads', 'image-optimization' ) );
			}
			
			if ( ! is_readable( $this->uploads_dir ) ) {
				throw new Exception( __( 'ไม่สามารถอ่านโฟลเดอร์ uploads ได้', 'image-optimization' ) );
			}

			$max_files = isset( $options['max_files'] ) ? absint( $options['max_files'] ) : 0;
			$processed = 0;

			try {
				$iterator = new RecursiveIteratorIterator(
					new RecursiveDirectoryIterator( $this->uploads_dir, RecursiveDirectoryIterator::SKIP_DOTS ),
					RecursiveIteratorIterator::SELF_FIRST
				);
			} catch ( Exception $e ) {
				throw new Exception( __( 'ไม่สามารถเข้าถึงโฟลเดอร์ uploads: ', 'image-optimization' ) . $e->getMessage() );
			}

			foreach ( $iterator as $file ) {
				// Check max files limit
				if ( $max_files > 0 && $processed >= $max_files ) {
					break;
				}

				if ( $file->isDir() ) {
					continue;
				}

				try {
					$file_path = wp_normalize_path( $file->getRealPath() );
					if ( ! $file_path ) {
						continue;
					}

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
					continue;
				}
			}
		} catch ( Exception $e ) {
			$this->log_action(
				'SCAN_ERROR',
				array(
					'type'  => 'thumbnails',
					'error' => $e->getMessage(),
					'trace' => $e->getTraceAsString(),
				)
			);
		}

		return $unused_thumbnails;
	}

	/**
	 * Find unused WebP files
	 *
	 * @param array $options Scan options.
	 * @return array Array of unused WebP files with details.
	 */
	public function scan_unused_webp( $options = array() ) {
		$unused_webp = array();

		try {
			// Get all attachments with WebP files
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
			throw new Exception( __( 'ไม่พบโฟลเดอร์ uploads', 'image-optimization' ) );
		}
		
		if ( ! is_readable( $this->uploads_dir ) ) {
			throw new Exception( __( 'ไม่สามารถอ่านโฟลเดอร์ uploads ได้', 'image-optimization' ) );
		}

		try {
			$iterator = new RecursiveIteratorIterator(
				new RecursiveDirectoryIterator( $this->uploads_dir, RecursiveDirectoryIterator::SKIP_DOTS ),
				RecursiveIteratorIterator::SELF_FIRST
			);
		} catch ( Exception $e ) {
			throw new Exception( __( 'ไม่สามารถเข้าถึงโฟลเดอร์ uploads: ', 'image-optimization' ) . $e->getMessage() );
		}

		foreach ( $iterator as $file ) {
			if ( $file->isDir() ) {
				continue;
			}

			$file_path = wp_normalize_path( $file->getRealPath() );
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
		}
		} catch ( Exception $e ) {
			$this->log_action(
				'SCAN_WEBP_ERROR',
				array(
					'error' => $e->getMessage(),
					'trace' => $e->getTraceAsString(),
				)
			);
			throw $e; // Re-throw to be handled by caller
		}

		return $unused_webp;
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

		try {
			// Get all attachment files
			$attachment_files = $this->get_all_attachment_files();

		// Scan uploads directory
		if ( ! is_dir( $this->uploads_dir ) ) {
			throw new Exception( __( 'ไม่พบโฟลเดอร์ uploads', 'image-optimization' ) );
		}
		
		if ( ! is_readable( $this->uploads_dir ) ) {
			throw new Exception( __( 'ไม่สามารถอ่านโฟลเดอร์ uploads ได้', 'image-optimization' ) );
		}

		try {
			$iterator = new RecursiveIteratorIterator(
				new RecursiveDirectoryIterator( $this->uploads_dir, RecursiveDirectoryIterator::SKIP_DOTS ),
				RecursiveIteratorIterator::SELF_FIRST
			);
		} catch ( Exception $e ) {
			throw new Exception( __( 'ไม่สามารถเข้าถึงโฟลเดอร์ uploads: ', 'image-optimization' ) . $e->getMessage() );
		}

		foreach ( $iterator as $file ) {
			if ( $file->isDir() ) {
				continue;
			}

			$file_path = wp_normalize_path( $file->getRealPath() );
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
		}
		} catch ( Exception $e ) {
			$this->log_action(
				'SCAN_ORPHANED_ERROR',
				array(
					'error' => $e->getMessage(),
					'trace' => $e->getTraceAsString(),
				)
			);
			throw $e; // Re-throw to be handled by caller
		}

		return $orphaned_images;
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
			'statistics' => array(
				'total_thumbnails' => 0,
				'total_webp'       => 0,
				'total_orphaned'   => 0,
				'total_size'       => 0,
				'thumbnails_size'  => 0,
				'webp_size'        => 0,
				'orphaned_size'    => 0,
				'scan_time'        => 0,
			),
		);

		try {
			// Validate uploads directory before scanning
			if ( ! is_dir( $this->uploads_dir ) ) {
				throw new Exception( __( 'ไม่พบโฟลเดอร์ uploads', 'image-optimization' ) );
			}
			
			if ( ! is_readable( $this->uploads_dir ) ) {
				throw new Exception( __( 'ไม่สามารถอ่านโฟลเดอร์ uploads ได้ กรุณาตรวจสอบสิทธิ์การเข้าถึง', 'image-optimization' ) );
			}
			
			// Scan thumbnails
			if ( $options['scan_type'] === 'all' || $options['scan_type'] === 'thumbnails' ) {
				try {
					$results['thumbnails']                    = $this->scan_unused_thumbnails( $options );
					$results['statistics']['total_thumbnails'] = count( $results['thumbnails'] );
					foreach ( $results['thumbnails'] as $thumb ) {
						if ( isset( $thumb['size'] ) ) {
							$results['statistics']['thumbnails_size'] += $thumb['size'];
							$results['statistics']['total_size']      += $thumb['size'];
						}
					}
				} catch ( Exception $e ) {
					$this->log_action(
						'SCAN_THUMBNAILS_ERROR',
						array(
							'error' => $e->getMessage(),
							'trace' => $e->getTraceAsString(),
						)
					);
					throw new Exception( __( 'เกิดข้อผิดพลาดในการแสกน thumbnails: ', 'image-optimization' ) . $e->getMessage() );
				}

				// Check time limit
				if ( $options['time_limit'] > 0 && ( time() - $start_time ) > $options['time_limit'] ) {
					$results['statistics']['timeout'] = true;
					$results['error'] = __( 'การแสกนใช้เวลานานเกินกำหนด กรุณาลองใหม่อีกครั้ง', 'image-optimization' );
					return $results;
				}
			}

			// Scan WebP
			if ( $options['scan_type'] === 'all' || $options['scan_type'] === 'webp' ) {
				try {
					$results['webp']                    = $this->scan_unused_webp( $options );
					$results['statistics']['total_webp'] = count( $results['webp'] );
					foreach ( $results['webp'] as $webp ) {
						if ( isset( $webp['size'] ) ) {
							$results['statistics']['webp_size'] += $webp['size'];
							$results['statistics']['total_size'] += $webp['size'];
						}
					}
				} catch ( Exception $e ) {
					$this->log_action(
						'SCAN_WEBP_ERROR',
						array(
							'error' => $e->getMessage(),
							'trace' => $e->getTraceAsString(),
						)
					);
					throw new Exception( __( 'เกิดข้อผิดพลาดในการแสกน WebP: ', 'image-optimization' ) . $e->getMessage() );
				}

				// Check time limit
				if ( $options['time_limit'] > 0 && ( time() - $start_time ) > $options['time_limit'] ) {
					$results['statistics']['timeout'] = true;
					$results['error'] = __( 'การแสกนใช้เวลานานเกินกำหนด กรุณาลองใหม่อีกครั้ง', 'image-optimization' );
					return $results;
				}
			}

			// Scan orphaned images
			if ( $options['scan_type'] === 'all' || $options['scan_type'] === 'orphaned' ) {
				try {
					$results['orphaned']                    = $this->scan_orphaned_images( $options );
					$results['statistics']['total_orphaned'] = count( $results['orphaned'] );
					foreach ( $results['orphaned'] as $orphaned ) {
						if ( isset( $orphaned['size'] ) ) {
							$results['statistics']['orphaned_size'] += $orphaned['size'];
							$results['statistics']['total_size']    += $orphaned['size'];
						}
					}
				} catch ( Exception $e ) {
					$this->log_action(
						'SCAN_ORPHANED_ERROR',
						array(
							'error' => $e->getMessage(),
							'trace' => $e->getTraceAsString(),
						)
					);
					throw new Exception( __( 'เกิดข้อผิดพลาดในการแสกน orphaned images: ', 'image-optimization' ) . $e->getMessage() );
				}
			}

			// Convert sizes to MB
			$results['statistics']['total_size_mb']       = round( $results['statistics']['total_size'] / 1024 / 1024, 2 );
			$results['statistics']['thumbnails_size_mb']  = round( $results['statistics']['thumbnails_size'] / 1024 / 1024, 2 );
			$results['statistics']['webp_size_mb']       = round( $results['statistics']['webp_size'] / 1024 / 1024, 2 );
			$results['statistics']['orphaned_size_mb']   = round( $results['statistics']['orphaned_size'] / 1024 / 1024, 2 );

			// Calculate scan time
			$results['statistics']['scan_time'] = time() - $start_time;

		} catch ( Exception $e ) {
			// Log error with full details
			$this->log_action(
				'SCAN_ERROR',
				array(
					'error' => $e->getMessage(),
					'trace' => $e->getTraceAsString(),
					'uploads_dir' => $this->uploads_dir,
					'is_dir' => is_dir( $this->uploads_dir ),
					'is_readable' => is_readable( $this->uploads_dir ),
				)
			);

			// Set error message for user
			$results['error'] = $e->getMessage();
		} catch ( Error $e ) {
			// Log fatal error
			$this->log_action(
				'SCAN_FATAL_ERROR',
				array(
					'error' => $e->getMessage(),
					'trace' => $e->getTraceAsString(),
				)
			);
			
			$results['error'] = __( 'เกิดข้อผิดพลาดร้ายแรงในการแสกน กรุณาตรวจสอบ log', 'image-optimization' );
		}

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
		$real_path       = realpath( $normalized_path );

		if ( ! $real_path ) {
			$result['error'] = 'Invalid file path';
			return $result;
		}

		$real_path    = wp_normalize_path( $real_path );
		$uploads_dir  = wp_normalize_path( realpath( $this->uploads_dir ) );

		if ( ! $uploads_dir ) {
			$result['error'] = 'Uploads directory not found';
			return $result;
		}

		// Check if file is within uploads directory
		if ( strpos( $real_path, $uploads_dir ) !== 0 ) {
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

		// Log deletion start
		if ( $options['log'] ) {
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
			// Check batch size limit
			if ( $processed >= $options['batch_size'] && ! $options['dry_run'] ) {
				// In real deletion, we might want to stop here and continue in next request
				// For now, we'll continue but log it
				if ( $options['log'] ) {
					$this->log_action(
						'BATCH_LIMIT_REACHED',
						array(
							'processed' => $processed,
							'batch_size' => $options['batch_size'],
						)
					);
				}
			}

			// Validate path
			$validation = $this->validate_file_path( $file_path );
			if ( ! $validation['valid'] ) {
				$results['failed']++;
				$results['errors'][] = array(
					'file'  => $file_path,
					'error' => $validation['error'],
				);

				if ( $options['log'] ) {
					$this->log_action(
						'DELETE_FAILED',
						array(
							'file'  => $file_path,
							'error' => $validation['error'],
						)
					);
				}
				continue;
			}

			// Get real path after validation
			$real_path = wp_normalize_path( realpath( $file_path ) );

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

			// Check if file is writable
			if ( ! is_writable( $real_path ) ) {
				$delete_error = 'File is not writable';
			} else {
				// Attempt deletion
				$deleted = @unlink( $real_path );
				if ( ! $deleted ) {
					$delete_error = 'Failed to delete file (permission denied or file locked)';
				}
			}

			if ( $deleted ) {
				$results['deleted']++;
				if ( $options['log'] ) {
					$this->log_action(
						'DELETE_SUCCESS',
						array(
							'file' => $real_path,
						)
					);
				}
			} else {
				$results['failed']++;
				$results['errors'][] = array(
					'file'  => $real_path,
					'error' => $delete_error,
				);
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
}

