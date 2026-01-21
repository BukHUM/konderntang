<?php
/**
 * Enqueue scripts and styles
 *
 * @package TrendToday
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Enqueue theme scripts and styles
 */
function trendtoday_enqueue_assets() {
    $version = trendtoday_get_theme_version();

    // Google Fonts (preconnect for performance)
    wp_enqueue_style(
        'trendtoday-google-fonts',
        'https://fonts.googleapis.com/css2?family=Prompt:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300&display=swap',
        array(),
        null
    );

    // Add preconnect for Google Fonts
    add_filter( 'style_loader_tag', 'trendtoday_add_preconnect_for_fonts', 10, 2 );

    // Font Awesome (with preload)
    wp_enqueue_style(
        'font-awesome',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
        array(),
        '6.4.0'
    );
    
    // Add preload for Font Awesome
    add_filter( 'style_loader_tag', function( $tag, $handle ) {
        if ( 'font-awesome' === $handle ) {
            $preload = '<link rel="preload" as="style" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">' . "\n";
            return $preload . $tag;
        }
        return $tag;
    }, 10, 2 );

    // Tailwind CSS is now loaded directly in header.php before wp_head()
    // This ensures it loads before all other styles

    // Theme stylesheet (style.css in theme root)
    $style_uri = get_stylesheet_uri();
    wp_enqueue_style(
        'trendtoday-style',
        $style_uri,
        array( 'trendtoday-google-fonts', 'font-awesome' ),
        $version
    );

    // Custom CSS
    $custom_css_file = get_template_directory() . '/assets/css/custom.css';
    if ( file_exists( $custom_css_file ) ) {
        wp_enqueue_style(
            'trendtoday-custom',
            get_template_directory_uri() . '/assets/css/custom.css',
            array( 'trendtoday-style' ),
            filemtime( $custom_css_file )
        );
    }

    // Print styles
    $print_css_file = get_template_directory() . '/assets/css/print.css';
    if ( file_exists( $print_css_file ) ) {
        wp_enqueue_style(
            'trendtoday-print',
            get_template_directory_uri() . '/assets/css/print.css',
            array( 'trendtoday-style' ),
            filemtime( $print_css_file ),
            'print'
        );
    }

    // Main JavaScript
    $main_js_file = get_template_directory() . '/assets/js/main.js';
    if ( file_exists( $main_js_file ) ) {
        wp_enqueue_script(
            'trendtoday-main',
            get_template_directory_uri() . '/assets/js/main.js',
            array( 'jquery' ),
            filemtime( $main_js_file ),
            true
        );

        // Custom JavaScript
        $custom_js_file = get_template_directory() . '/assets/js/custom.js';
        if ( file_exists( $custom_js_file ) ) {
            wp_enqueue_script(
                'trendtoday-custom',
                get_template_directory_uri() . '/assets/js/custom.js',
                array( 'trendtoday-main' ),
                filemtime( $custom_js_file ),
                true
            );
        }
    }
    
    // Add defer attribute for non-critical scripts
    add_filter( 'script_loader_tag', 'trendtoday_add_defer_to_scripts', 10, 2 );

    // Localize script for AJAX
    $search_enabled = get_option( 'trendtoday_search_enabled', '1' );
    $search_suggestions_enabled = get_option( 'trendtoday_search_suggestions_enabled', '1' );
    $search_live_enabled = get_option( 'trendtoday_search_live_enabled', '1' );
    $search_debounce = get_option( 'trendtoday_search_debounce', 300 );
    $search_min_length = get_option( 'trendtoday_search_min_length', 2 );
    $search_suggestions_style = get_option( 'trendtoday_search_suggestions_style', 'dropdown' );
    $search_placeholder = get_option( 'trendtoday_search_placeholder', __( 'พิมพ์คำค้นหา...', 'trendtoday' ) );
    
    wp_localize_script( 'trendtoday-main', 'trendtodayAjax', array(
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
        'nonce'   => wp_create_nonce( 'trendtoday-nonce' ),
        'searchUrl' => home_url( '/?s=' ),
        'search' => array(
            'enabled' => $search_enabled === '1',
            'suggestions_enabled' => $search_suggestions_enabled === '1',
            'live_enabled' => $search_live_enabled === '1',
            'debounce' => absint( $search_debounce ),
            'min_length' => absint( $search_min_length ),
            'style' => $search_suggestions_style,
            'placeholder' => $search_placeholder,
        ),
    ) );

    // Comment reply script (only on single posts with comments)
    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }
    
    // Conditional script loading: Only load custom.js on pages that need it
    $load_custom_js = true; // Default: load on all pages
    
    // Check if we're on a page that needs custom.js
    if ( is_front_page() || is_home() || is_archive() || is_search() || is_single() ) {
        $load_custom_js = true;
    } else {
        // Don't load on pages that don't need it
        $load_custom_js = false;
    }
    
    // Store flag for later use
    if ( ! $load_custom_js ) {
        // Remove custom.js if it was enqueued
        add_action( 'wp_print_scripts', function() {
            wp_dequeue_script( 'trendtoday-custom' );
        }, 100 );
    }

    // Lazy loading support
    wp_add_inline_script( 'trendtoday-main', '
        if ("loading" in HTMLImageElement.prototype) {
            const images = document.querySelectorAll("img[loading=\'lazy\']");
            images.forEach(img => {
                img.src = img.dataset.src || img.src;
            });
        }
    ', 'before' );
}
add_action( 'wp_enqueue_scripts', 'trendtoday_enqueue_assets' );

/**
 * Add defer attribute to non-critical scripts
 *
 * @param string $tag Script tag.
 * @param string $handle Script handle.
 * @return string Modified script tag.
 */
function trendtoday_add_defer_to_scripts( $tag, $handle ) {
    $defer_scripts = array( 'trendtoday-main', 'trendtoday-custom' );
    
    if ( in_array( $handle, $defer_scripts, true ) ) {
        // Add defer if not already present
        if ( strpos( $tag, ' defer' ) === false && strpos( $tag, 'defer' ) === false ) {
            $tag = str_replace( ' src', ' defer src', $tag );
        }
    }
    
    return $tag;
}

/**
 * Add preconnect for Google Fonts
 *
 * @param string $tag Link tag.
 * @param string $handle Style handle.
 * @return string Modified link tag.
 */
function trendtoday_add_preconnect_for_fonts( $tag, $handle ) {
    if ( 'trendtoday-google-fonts' === $handle ) {
        $preconnect = '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
        $preconnect .= '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
        return $preconnect . $tag;
    }
    return $tag;
}

/**
 * Enqueue admin styles and scripts
 */
function trendtoday_enqueue_admin_styles( $hook ) {
    // Load on post list and edit screens
    if ( in_array( $hook, array( 'post.php', 'post-new.php', 'edit.php' ) ) ) {
        wp_enqueue_style(
            'trendtoday-admin',
            get_template_directory_uri() . '/assets/css/admin.css',
            array(),
            trendtoday_get_theme_version()
        );
        
        // Add inline CSS for post list table column widths
        if ( 'edit.php' === $hook ) {
            $custom_css = '
            #posts-filter .wp-list-table,
            #posts-filter .widefat {
                table-layout: auto !important;
            }
            #posts-filter .wp-list-table th.column-title,
            #posts-filter .wp-list-table td.column-title {
                width: 50% !important;
                min-width: 400px !important;
                max-width: none !important;
            }
            .wp-list-table th.column-title,
            .wp-list-table td.column-title {
                width: 50% !important;
                min-width: 400px !important;
                max-width: none !important;
            }
            ';
            wp_add_inline_style( 'trendtoday-admin', $custom_css );
        }
    }
    
    // Load on theme settings page
    if ( strpos( $hook, 'trendtoday-settings' ) !== false ) {
        wp_enqueue_style(
            'trendtoday-admin',
            get_template_directory_uri() . '/assets/css/admin.css',
            array(),
            trendtoday_get_theme_version()
        );
        
        // Enqueue WordPress media uploader with proper dependencies
        wp_enqueue_media();
        
        // Enqueue jQuery for admin scripts
        wp_enqueue_script( 'jquery' );
        
        // Localize script for AJAX (must be array)
        wp_localize_script( 'jquery', 'trendtodayAdmin', array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'trendtoday_settings_nonce' ),
            'i18n' => array(
                'scanning' => __( 'กำลังแสกน...', 'trendtoday' ),
                'scanComplete' => __( 'แสกนเสร็จสิ้น', 'trendtoday' ),
                'scanError' => __( 'เกิดข้อผิดพลาดในการแสกน', 'trendtoday' ),
                'noFiles' => __( 'ไม่มีไฟล์', 'trendtoday' ),
                'noFilesSelected' => __( 'กรุณาเลือกไฟล์ที่ต้องการลบ', 'trendtoday' ),
                'confirmDeleteSelected' => __( 'คุณแน่ใจหรือไม่ว่าต้องการลบไฟล์ที่เลือก? การลบไม่สามารถยกเลิกได้', 'trendtoday' ),
                'confirmDeleteAll' => __( 'คุณแน่ใจหรือไม่ว่าต้องการลบไฟล์ทั้งหมด? การลบไม่สามารถยกเลิกได้', 'trendtoday' ),
                'deleting' => __( 'กำลังลบ...', 'trendtoday' ),
                'deleteComplete' => __( 'ลบเสร็จสิ้น', 'trendtoday' ),
                'deleteError' => __( 'เกิดข้อผิดพลาดในการลบ', 'trendtoday' ),
                'reportFeature' => __( 'ฟีเจอร์นี้จะเพิ่มในอนาคต', 'trendtoday' ),
                'scanTimeout' => __( 'การแสกนใช้เวลานานเกินไป กรุณาลองใหม่อีกครั้ง', 'trendtoday' ),
                'noFailedFiles' => __( 'ไม่มีไฟล์ที่ลบไม่สำเร็จ', 'trendtoday' ),
            ),
        ) );
        
        // Add inline JavaScript for Image Cleanup
        $image_cleanup_js = "
        (function() {
            'use strict';
            
            // Wait for jQuery and DOM to be ready
            if (typeof jQuery === 'undefined') {
                console.error('jQuery is not loaded!');
                return;
            }
            
            jQuery(document).ready(function($) {
                console.log('Image Cleanup script loaded');
                
                // Tab switching
            $(document).on('click', '.trendtoday-file-lists .nav-tab', function(e) {
                e.preventDefault();
                var tab = $(this).data('tab');
                
                // Update active tab
                $('.trendtoday-file-lists .nav-tab').removeClass('nav-tab-active').css({
                    'color': '#646970',
                    'border-bottom-color': 'transparent',
                    'background': '#f9f9f9'
                });
                $(this).addClass('nav-tab-active').css({
                    'color': '#2271b1',
                    'border-bottom-color': '#2271b1',
                    'background': '#fff'
                });
                
                // Show/hide tab content
                $('.tab-pane').hide();
                $('#' + tab + '-tab').show();
            });
            
            // Scan for unused images
            $(document).on('click', '#trendtoday-scan-unused-images', function(e) {
                e.preventDefault();
                console.log('Scan button clicked');
                
                var \$btn = $(this);
                var \$progress = $('#trendtoday-scan-progress');
                var \$progressBar = $('#trendtoday-scan-progress-bar');
                var \$status = $('#trendtoday-scan-status');
                var \$results = $('#trendtoday-scan-results');
                
                console.log('Button:', \$btn.length, 'Progress:', \$progress.length, 'Status:', \$status.length);
                
                if (\$btn.length === 0) {
                    console.error('Scan button not found!');
                    alert('Error: Scan button not found. Please refresh the page.');
                    return;
                }
                
                \$btn.prop('disabled', true);
                \$progress.show();
                \$progressBar.css('width', '0%');
                \$status.html('<span class=\"dashicons dashicons-update\" style=\"animation: spin 1s linear infinite; display: inline-block;\"></span> ' + (trendtodayAdmin.i18n.scanning || 'กำลังแสกน...'));
                \$results.hide();
                
                console.log('Starting scan...');
                
                // Simulate progress (since we can't get real-time progress from AJAX)
                var progressInterval = setInterval(function() {
                    var currentWidth = parseInt(\$progressBar.css('width')) || 0;
                    var containerWidth = \$progressBar.parent().width();
                    var percent = (currentWidth / containerWidth) * 100;
                    if (percent < 90) {
                        percent += Math.random() * 5;
                        \$progressBar.css('width', Math.min(percent, 90) + '%');
                    }
                }, 500);
                
                console.log('AJAX URL:', trendtodayAdmin.ajaxurl);
                console.log('Nonce:', trendtodayAdmin.nonce);
                
                $.ajax({
                    url: trendtodayAdmin.ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'scan_unused_images',
                        nonce: trendtodayAdmin.nonce,
                        scan_type: 'all',
                        use_cache: true,
                        max_files: 0,
                        time_limit: 0
                    },
                    timeout: 300000, // 5 minutes timeout
                    beforeSend: function() {
                        console.log('AJAX request sent');
                    },
                    success: function(response) {
                        console.log('AJAX success:', response);
                        clearInterval(progressInterval);
                        if (response.success) {
                            \$progressBar.css('width', '100%');
                            var scanTime = response.data.statistics && response.data.statistics.scan_time ? 
                                ' (' + response.data.statistics.scan_time + 's)' : '';
                            \$status.html('<span class=\"dashicons dashicons-yes-alt\" style=\"color: #00a32a;\"></span> ' + 
                                (trendtodayAdmin.i18n.scanComplete || 'แสกนเสร็จสิ้น') + scanTime);
                            
                            // Display results
                            displayScanResults(response.data);
                            \$results.show();
                        } else {
                            \$progressBar.css('width', '0%');
                            \$status.html('<span class=\"dashicons dashicons-dismiss\" style=\"color: #d63638;\"></span> ' + 
                                (response.data.message || 'เกิดข้อผิดพลาด'));
                        }
                        \$btn.prop('disabled', false);
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX error:', status, error, xhr);
                        clearInterval(progressInterval);
                        \$progressBar.css('width', '0%');
                        var errorMsg = trendtodayAdmin.i18n.scanError || 'เกิดข้อผิดพลาดในการแสกน';
                        if (status === 'timeout') {
                            errorMsg = trendtodayAdmin.i18n.scanTimeout || 'การแสกนใช้เวลานานเกินไป กรุณาลองใหม่อีกครั้ง';
                        } else if (xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message) {
                            errorMsg = xhr.responseJSON.data.message;
                        }
                        \$status.html('<span class=\"dashicons dashicons-dismiss\" style=\"color: #d63638;\"></span> ' + errorMsg);
                        \$btn.prop('disabled', false);
                        alert('Error: ' + errorMsg);
                    }
                });
            });
            
            // Display scan results
            function displayScanResults(data) {
                var stats = data.statistics || {};
                
                // Update statistics
                $('#stat-thumbnails-count').text(stats.total_thumbnails || 0);
                $('#stat-thumbnails-size').text((stats.thumbnails_size_mb || 0).toFixed(2) + ' MB');
                $('#stat-webp-count').text(stats.total_webp || 0);
                $('#stat-webp-size').text((stats.webp_size_mb || 0).toFixed(2) + ' MB');
                $('#stat-orphaned-count').text(stats.total_orphaned || 0);
                $('#stat-orphaned-size').text((stats.orphaned_size_mb || 0).toFixed(2) + ' MB');
                $('#stat-total-size').text((stats.total_size_mb || 0).toFixed(2) + ' MB');
                
                // Update tab counts
                $('#tab-thumbnails-count').text(stats.total_thumbnails || 0);
                $('#tab-webp-count').text(stats.total_webp || 0);
                $('#tab-orphaned-count').text(stats.total_orphaned || 0);
                
                // Display file lists
                displayFileList('thumbnails', data.thumbnails || []);
                displayFileList('webp', data.webp || []);
                displayFileList('orphaned', data.orphaned || []);
                
                // Display failed files if any
                if (data.failed_files && data.failed_files.length > 0) {
                    displayFailedFiles(data.failed_files);
                } else {
                    $('#failed-files-tab-li').hide();
                    $('#failed-list').html('<p style=\"color: #646970; text-align: center; padding: 20px;\">' + 
                        (trendtodayAdmin.i18n.noFailedFiles || 'ไม่มีไฟล์ที่ลบไม่สำเร็จ') + '</p>');
                }
                
                // Enable/disable delete buttons
                var totalFiles = (stats.total_thumbnails || 0) + (stats.total_webp || 0) + (stats.total_orphaned || 0);
                $('#trendtoday-delete-selected').prop('disabled', totalFiles === 0);
                $('#trendtoday-delete-all').prop('disabled', totalFiles === 0);
            }
            
            // Display failed files
            function displayFailedFiles(failedFiles) {
                var \$list = $('#failed-list');
                \$list.empty();
                
                if (failedFiles.length === 0) {
                    \$list.html('<p style=\"color: #646970; text-align: center; padding: 20px;\">' + 
                        (trendtodayAdmin.i18n.noFailedFiles || 'ไม่มีไฟล์ที่ลบไม่สำเร็จ') + '</p>');
                    $('#failed-files-tab-li').hide();
                    return;
                }
                
                // Show failed files tab
                $('#failed-files-tab-li').show();
                $('#tab-failed-count').text(failedFiles.length);
                
                var html = '<table class=\"wp-list-table widefat fixed striped\" style=\"margin: 0;\"><thead><tr><th>File</th><th style=\"width: 100px;\">Size</th><th style=\"width: 200px;\">Error</th><th style=\"width: 150px;\">Date</th></tr></thead><tbody>';
                
                failedFiles.forEach(function(file) {
                    var size = formatFileSize(file.size || 0);
                    var date = 'N/A';
                    if (file.modified) {
                        var dateObj = new Date(file.modified * 1000);
                        date = dateObj.toLocaleDateString() + ' ' + dateObj.toLocaleTimeString();
                    }
                    var filename = file.filename || (file.path ? file.path.split(/[\\/]/).pop() : '');
                    var errorMsg = file.error || 'Unknown error';
                    
                    html += '<tr style=\"background: #fff5f5;\">';
                    html += '<td><code style=\"font-size: 11px; word-break: break-all; color: #d63638;\">' + escapeHtml(filename) + '</code></td>';
                    html += '<td>' + size + '</td>';
                    html += '<td><span style=\"color: #d63638; font-size: 11px;\" title=\"' + escapeHtml(errorMsg) + '\">' + escapeHtml(errorMsg) + '</span></td>';
                    html += '<td style=\"font-size: 11px;\">' + date + '</td>';
                    html += '</tr>';
                });
                
                html += '</tbody></table>';
                \$list.html(html);
            }
            
            // Display file list
            function displayFileList(type, files) {
                var \$list = $('#' + type + '-list');
                \$list.empty();
                
                if (files.length === 0) {
                    \$list.html('<p style=\"color: #646970; text-align: center; padding: 20px;\">' + (trendtodayAdmin.i18n.noFiles || 'ไม่มีไฟล์') + '</p>');
                    return;
                }
                
                var html = '<table class=\"wp-list-table widefat fixed striped\" style=\"margin: 0;\"><thead><tr><th style=\"width: 30px;\"><input type=\"checkbox\" class=\"select-all-' + type + '\"></th><th>File</th><th style=\"width: 100px;\">Size</th><th style=\"width: 150px;\">Date</th></tr></thead><tbody>';
                
                files.forEach(function(file) {
                    var size = formatFileSize(file.size || 0);
                    var date = 'N/A';
                    if (file.modified) {
                        var dateObj = new Date(file.modified * 1000);
                        date = dateObj.toLocaleDateString() + ' ' + dateObj.toLocaleTimeString();
                    }
                    var filename = file.filename || (file.path ? file.path.split(/[\\/]/).pop() : '');
                    html += '<tr>';
                    html += '<td><input type=\"checkbox\" class=\"file-checkbox\" data-path=\"' + escapeHtml(file.path) + '\" data-type=\"' + type + '\"></td>';
                    html += '<td><code style=\"font-size: 11px; word-break: break-all;\">' + escapeHtml(filename) + '</code></td>';
                    html += '<td>' + size + '</td>';
                    html += '<td style=\"font-size: 11px;\">' + date + '</td>';
                    html += '</tr>';
                });
                
                html += '</tbody></table>';
                \$list.html(html);
            }
            
            // Format file size
            function formatFileSize(bytes) {
                if (bytes === 0) return '0 B';
                var k = 1024;
                var sizes = ['B', 'KB', 'MB', 'GB'];
                var i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }
            
            // Escape HTML
            function escapeHtml(text) {
                var map = {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '\"': '&quot;',
                    \"'\": '&#039;'
                };
                return text.replace(/[&<>\"']/g, function(m) { return map[m]; });
            }
            
            // Select all checkbox
            $(document).on('change', '.select-all-thumbnails, .select-all-webp, .select-all-orphaned', function() {
                var type = $(this).hasClass('select-all-thumbnails') ? 'thumbnails' : 
                          $(this).hasClass('select-all-webp') ? 'webp' : 'orphaned';
                var checked = $(this).is(':checked');
                $('#' + type + '-list .file-checkbox[data-type=\"' + type + '\"]').prop('checked', checked);
                updateDeleteButton();
            });
            
            // File checkbox change
            $(document).on('change', '.file-checkbox', function() {
                updateDeleteButton();
            });
            
            // Update delete button state
            function updateDeleteButton() {
                var checked = $('.file-checkbox:checked').length;
                $('#trendtoday-delete-selected').prop('disabled', checked === 0);
            }
            
            // Delete selected files
            $('#trendtoday-delete-selected').on('click', function() {
                var selectedPaths = [];
                $('.file-checkbox:checked').each(function() {
                    selectedPaths.push($(this).data('path'));
                });
                
                if (selectedPaths.length === 0) {
                    alert(trendtodayAdmin.i18n.noFilesSelected || 'กรุณาเลือกไฟล์ที่ต้องการลบ');
                    return;
                }
                
                if (!confirm(trendtodayAdmin.i18n.confirmDeleteSelected || 'คุณแน่ใจหรือไม่ว่าต้องการลบไฟล์ที่เลือก ' + selectedPaths.length + ' ไฟล์? การลบไม่สามารถยกเลิกได้')) {
                    return;
                }
                
                deleteFiles(selectedPaths, 'selected');
            });
            
            // Delete all files
            $('#trendtoday-delete-all').on('click', function() {
                if (!confirm(trendtodayAdmin.i18n.confirmDeleteAll || 'คุณแน่ใจหรือไม่ว่าต้องการลบไฟล์ทั้งหมด? การลบไม่สามารถยกเลิกได้')) {
                    return;
                }
                
                deleteFiles([], 'all');
            });
            
            // Delete files
            function deleteFiles(filePaths, actionType) {
                var \$progress = $('#trendtoday-delete-progress');
                var \$progressBar = $('#trendtoday-delete-progress-bar');
                var \$status = $('#trendtoday-delete-status');
                
                \$progress.show();
                \$progressBar.css('width', '0%');
                \$status.html('<span class=\"dashicons dashicons-update\" style=\"animation: spin 1s linear infinite; display: inline-block;\"></span> ' + (trendtodayAdmin.i18n.deleting || 'กำลังลบ...'));
                
                $.ajax({
                    url: trendtodayAdmin.ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'delete_unused_images',
                        nonce: trendtodayAdmin.nonce,
                        action_type: actionType,
                        file_paths: filePaths
                    },
                    success: function(response) {
                        if (response.success) {
                            \$progressBar.css('width', '100%');
                            var deleted = response.data.deleted || 0;
                            var failed = response.data.failed || 0;
                            var failedFiles = response.data.failed_files || [];
                            
                            var statusMsg = (trendtodayAdmin.i18n.deleteComplete || 'ลบเสร็จสิ้น') + ': ' + deleted + ' ไฟล์';
                            if (failed > 0) {
                                statusMsg += ', <span style=\"color: #d63638;\">ลบไม่สำเร็จ: ' + failed + ' ไฟล์</span>';
                            }
                            \$status.html('<span class=\"dashicons dashicons-yes-alt\" style=\"color: #00a32a;\"></span> ' + statusMsg);
                            
                            // Display failed files if any
                            if (failedFiles.length > 0) {
                                displayFailedFiles(failedFiles);
                                // Switch to failed tab after a short delay
                                setTimeout(function() {
                                    if ($('#failed-files-tab-li').is(':visible')) {
                                        $('#failed-files-tab-li a').trigger('click');
                                    }
                                }, 500);
                            }
                            
                            // Refresh scan results
                            $('#trendtoday-scan-unused-images').trigger('click');
                        } else {
                            \$status.html('<span class=\"dashicons dashicons-dismiss\" style=\"color: #d63638;\"></span> ' + (response.data.message || 'เกิดข้อผิดพลาด'));
                        }
                    },
                    error: function() {
                        \$status.html('<span class=\"dashicons dashicons-dismiss\" style=\"color: #d63638;\"></span> ' + (trendtodayAdmin.i18n.deleteError || 'เกิดข้อผิดพลาดในการลบ'));
                    }
                });
            }
            
            // Download report
            $('#trendtoday-download-report').on('click', function() {
                // This would generate a CSV file - implementation can be added later
                alert(trendtodayAdmin.i18n.reportFeature || 'ฟีเจอร์นี้จะเพิ่มในอนาคต');
            });
            
                // Add spin animation
                if (!$('#trendtoday-spin-animation').length) {
                    $('head').append('<style id=\"trendtoday-spin-animation\">@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }</style>');
                }
            });
        })();
        ";
        
        wp_add_inline_script( 'jquery', $image_cleanup_js );
    }
}
add_action( 'admin_enqueue_scripts', 'trendtoday_enqueue_admin_styles' );

/**
 * Add Tailwind CSS CDN to head
 * Must be added early in head before other styles
 */
function trendtoday_add_tailwind_cdn() {
    // Only add once
    static $added = false;
    if ( $added ) {
        return;
    }
    $added = true;
    ?>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: {
                    sans: ["Prompt", "sans-serif"],
                },
                colors: {
                    primary: "#1a1a1a",
                    accent: "#FF4500",
                    "news-tech": "#3B82F6",
                    "news-ent": "#EC4899",
                    "news-fin": "#10B981",
                    "news-sport": "#F59E0B",
                }
            }
        }
    }
    </script>
    <?php
}
