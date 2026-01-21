/**
 * Admin JavaScript for Image Optimization Plugin
 *
 * @package Image_Optimization
 * @since 1.0.0
 */

(function() {
    'use strict';
    
    // Wait for jQuery and DOM to be ready
    if (typeof jQuery === 'undefined') {
        console.error('jQuery is not loaded!');
        return;
    }
    
    jQuery(document).ready(function($) {
        console.log('Image Optimization admin script loaded');
        
        // Check if tab elements exist
        var $mainTabs = $('.io-main-tab');
        var $tabContents = $('.io-main-tab-content');
        console.log('Found main tabs:', $mainTabs.length);
        console.log('Found tab contents:', $tabContents.length);
        console.log('Tab contents IDs:', $tabContents.map(function() { return this.id; }).get());
        
        // Main tab switching function
        function switchMainTab(tab) {
            console.log('Switching to tab:', tab);
            
            // Update active tab
            $('.io-main-tab').removeClass('nav-tab-active');
            $('.io-main-tab[data-tab="' + tab + '"]').addClass('nav-tab-active');
            
            // Show/hide tab content
            $('.io-main-tab-content').hide();
            var $targetTab = $('#io-tab-' + tab);
            
            if ($targetTab.length === 0) {
                console.error('Tab content not found: #io-tab-' + tab);
                return false;
            }
            
            $targetTab.fadeIn(200);
            console.log('Tab switched to:', tab);
            
            // Update URL without page reload (for bookmarking)
            var baseUrl = window.location.pathname;
            var search = window.location.search;
            var newSearch = search.replace(/[&?]tab=[^&]*/, '').replace(/^&/, '?');
            if (newSearch === '?') newSearch = '';
            var separator = newSearch ? '&' : '?';
            var newUrl = baseUrl + newSearch + separator + (newSearch ? '' : 'page=image-optimization&') + 'tab=' + tab;
            
            if (window.history && window.history.pushState) {
                window.history.pushState({tab: tab}, '', newUrl);
            }
            
            return true;
        }
        
        // Main tab switching (Optimization <-> Cleanup) - Client-side navigation
        // Use both direct binding and delegated event
        $('.io-main-tab').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            var $tab = $(this);
            var tab = $tab.data('tab');
            
            console.log('Tab clicked (direct):', tab, 'Element:', $tab);
            
            if (!tab) {
                console.error('Tab data not found on element:', $tab);
                return false;
            }
            
            return switchMainTab(tab);
        });
        
        // Also use delegated event as fallback
        $(document).on('click', '.io-main-tab', function(e) {
            // This is a fallback - the direct binding above should handle it
            // But we'll prevent default just in case
            if ($(this).hasClass('io-main-tab')) {
                e.preventDefault();
                var tab = $(this).data('tab');
                console.log('Tab clicked (delegated):', tab);
                if (tab) {
                    switchMainTab(tab);
                }
                return false;
            }
        });
        
        // Tab switching for file lists (within cleanup tab)
        $(document).on('click', '.io-file-lists .nav-tab', function(e) {
            e.preventDefault();
            var tab = $(this).data('tab');
            
            // Update active tab
            $('.io-file-lists .nav-tab').removeClass('nav-tab-active');
            $(this).addClass('nav-tab-active');
            
            // Show/hide tab content
            $('.io-file-lists .tab-pane').removeClass('active').hide();
            $('#' + tab + '-tab').addClass('active').fadeIn(200);
        });
        
        // Store scan progress interval to clear it properly
        var scanProgressInterval = null;
        
        // Function to reset scan state
        function resetScanState() {
            var $btn = $('#io-scan-unused-images');
            var $progress = $('#io-scan-progress');
            var $progressBar = $('#io-scan-progress-bar');
            var $status = $('#io-scan-status');
            
            // Clear any running intervals
            if (scanProgressInterval !== null) {
                clearInterval(scanProgressInterval);
                scanProgressInterval = null;
            }
            
            // Reset UI elements
            $btn.prop('disabled', false);
            $progressBar.css('width', '0%');
            $status.html(''); // Clear status message
            $progress.hide(); // Hide progress section
        }
        
        // Scan for unused images
        $(document).on('click', '#io-scan-unused-images', function(e) {
            e.preventDefault();
            console.log('Scan button clicked');
            
            var $btn = $(this);
            var $progress = $('#io-scan-progress');
            var $progressBar = $('#io-scan-progress-bar');
            var $status = $('#io-scan-status');
            var $results = $('#io-scan-results');
            
            console.log('Button:', $btn.length, 'Progress:', $progress.length, 'Status:', $status.length);
            
            if ($btn.length === 0) {
                console.error('Scan button not found!');
                alert('Error: Scan button not found. Please refresh the page.');
                return;
            }
            
            // Reset any previous state first
            resetScanState();
            
            $btn.prop('disabled', true);
            $progress.show();
            $progressBar.css('width', '0%');
            $status.html('<span class="dashicons dashicons-update" style="animation: spin 1s linear infinite; display: inline-block;"></span> ' + (ioAdmin.i18n.scanning || 'กำลังแสกน...'));
            $results.hide();
            
            console.log('Starting scan...');
            
            // Simulate progress (since we can't get real-time progress from AJAX)
            scanProgressInterval = setInterval(function() {
                var currentWidth = parseInt($progressBar.css('width')) || 0;
                var containerWidth = $progressBar.parent().width();
                var percent = (currentWidth / containerWidth) * 100;
                if (percent < 90) {
                    percent += Math.random() * 5;
                    $progressBar.css('width', Math.min(percent, 90) + '%');
                }
            }, 500);
            
            console.log('AJAX URL:', ioAdmin.ajaxurl);
            console.log('Nonce:', ioAdmin.nonce);
            
            var ajaxRequest = $.ajax({
                url: ioAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'io_scan_unused_images',
                    nonce: ioAdmin.nonce,
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
                    
                    // Clear progress interval
                    if (scanProgressInterval !== null) {
                        clearInterval(scanProgressInterval);
                        scanProgressInterval = null;
                    }
                    
                    if (response.success) {
                        $progressBar.css('width', '100%');
                        var scanTime = response.data.statistics && response.data.statistics.scan_time ? 
                            ' (' + response.data.statistics.scan_time + 's)' : '';
                        $status.html('<span class="dashicons dashicons-yes-alt" style="color: #00a32a;"></span> ' + 
                            (ioAdmin.i18n.scanComplete || 'แสกนเสร็จสิ้น') + scanTime);
                        
                        // Display results
                        displayScanResults(response.data);
                        $results.show();
                        
                        // Hide progress after a short delay
                        setTimeout(function() {
                            $progress.fadeOut(500);
                        }, 2000);
                    } else {
                        // Reset state on error
                        resetScanState();
                        
                        // Show error message briefly
                        $progress.show();
                        $status.html('<span class="dashicons dashicons-dismiss" style="color: #d63638;"></span> ' + 
                            '<span style="color: #d63638; font-weight: 500;">' + (response.data.message || 'เกิดข้อผิดพลาด') + '</span>');
                        
                        // Auto-hide error after 5 seconds
                        setTimeout(function() {
                            resetScanState();
                        }, 5000);
                    }
                    $btn.prop('disabled', false);
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', status, error, xhr);
                    
                    // Clear progress interval
                    if (scanProgressInterval !== null) {
                        clearInterval(scanProgressInterval);
                        scanProgressInterval = null;
                    }
                    
                    var errorMsg = ioAdmin.i18n.scanError || 'เกิดข้อผิดพลาดในการแสกน';
                    
                    // Handle different error types
                    if (status === 'timeout') {
                        errorMsg = ioAdmin.i18n.scanTimeout || 'การแสกนใช้เวลานานเกินไป กรุณาลองใหม่อีกครั้ง';
                    } else if (status === 'abort') {
                        errorMsg = 'การแสกนถูกยกเลิก';
                        resetScanState();
                        return; // Don't show alert for abort
                    } else if (xhr.status === 0) {
                        errorMsg = 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้ กรุณาตรวจสอบการเชื่อมต่ออินเทอร์เน็ต';
                    } else if (xhr.status === 403) {
                        errorMsg = 'คุณไม่มีสิทธิ์ในการดำเนินการนี้';
                    } else if (xhr.status === 500) {
                        errorMsg = 'เกิดข้อผิดพลาดที่เซิร์ฟเวอร์ กรุณาตรวจสอบ log หรือติดต่อผู้ดูแลระบบ';
                    } else if (xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message) {
                        // Use the user-friendly error message from server
                        errorMsg = xhr.responseJSON.data.message;
                    } else if (xhr.responseText) {
                        // Try to parse error from response
                        try {
                            var response = JSON.parse(xhr.responseText);
                            if (response.data && response.data.message) {
                                errorMsg = response.data.message;
                            }
                        } catch (e) {
                            // If parsing fails, use generic message
                            errorMsg = 'เกิดข้อผิดพลาดที่ไม่ทราบสาเหตุ กรุณาลองใหม่อีกครั้ง';
                        }
                    }
                    
                    // Reset state first
                    resetScanState();
                    
                    // Show error message briefly
                    $progress.show();
                    $progressBar.css('width', '0%');
                    $status.html(
                        '<span class="dashicons dashicons-dismiss" style="color: #d63638;"></span> ' + 
                        '<span style="color: #d63638; font-weight: 500;">' + errorMsg + '</span>'
                    );
                    $btn.prop('disabled', false);
                    
                    // Auto-hide error after 5 seconds
                    setTimeout(function() {
                        resetScanState();
                    }, 5000);
                    
                    // Show alert with helpful message (only once)
                    if (!window.ioScanErrorShown) {
                        window.ioScanErrorShown = true;
                        alert('เกิดข้อผิดพลาดในการแสกน\n\n' + errorMsg + '\n\nหากปัญหายังคงอยู่ กรุณาตรวจสอบ:\n' +
                              '1. สิทธิ์การเข้าถึงโฟลเดอร์ uploads\n' +
                              '2. การตั้งค่า memory_limit ใน php.ini\n' +
                              '3. Log ไฟล์สำหรับรายละเอียดเพิ่มเติม');
                        // Reset flag after 2 seconds
                        setTimeout(function() {
                            window.ioScanErrorShown = false;
                        }, 2000);
                    }
                }
            });
            
            // Store ajax request for potential cancellation
            window.ioScanAjaxRequest = ajaxRequest;
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
                $('#failed-list').html('<p style="color: #646970; text-align: center; padding: 20px;">' + 
                    (ioAdmin.i18n.noFailedFiles || 'ไม่มีไฟล์ที่ลบไม่สำเร็จ') + '</p>');
            }
            
            // Enable/disable delete buttons
            var totalFiles = (stats.total_thumbnails || 0) + (stats.total_webp || 0) + (stats.total_orphaned || 0);
            $('#io-delete-selected').prop('disabled', totalFiles === 0);
            $('#io-delete-all').prop('disabled', totalFiles === 0);
        }
        
        // Display failed files
        function displayFailedFiles(failedFiles) {
            var $list = $('#failed-list');
            $list.empty();
            
            if (failedFiles.length === 0) {
                $list.html('<p style="color: #646970; text-align: center; padding: 20px;">' + 
                    (ioAdmin.i18n.noFailedFiles || 'ไม่มีไฟล์ที่ลบไม่สำเร็จ') + '</p>');
                $('#failed-files-tab-li').hide();
                return;
            }
            
            // Show failed files tab
            $('#failed-files-tab-li').show();
            $('#tab-failed-count').text(failedFiles.length);
            
            var html = '<table class="wp-list-table widefat fixed striped" style="margin: 0;"><thead><tr><th>File</th><th style="width: 100px;">Size</th><th style="width: 200px;">Error</th><th style="width: 150px;">Date</th></tr></thead><tbody>';
            
            failedFiles.forEach(function(file) {
                var size = formatFileSize(file.size || 0);
                var date = 'N/A';
                if (file.modified) {
                    var dateObj = new Date(file.modified * 1000);
                    date = dateObj.toLocaleDateString() + ' ' + dateObj.toLocaleTimeString();
                }
                var filename = file.filename || (file.path ? file.path.split(/[\\/]/).pop() : '');
                var errorMsg = file.error || 'Unknown error';
                
                html += '<tr style="background: #fff5f5;">';
                html += '<td><code style="font-size: 11px; word-break: break-all; color: #d63638;">' + escapeHtml(filename) + '</code></td>';
                html += '<td>' + size + '</td>';
                html += '<td><span style="color: #d63638; font-size: 11px;" title="' + escapeHtml(errorMsg) + '">' + escapeHtml(errorMsg) + '</span></td>';
                html += '<td style="font-size: 11px;">' + date + '</td>';
                html += '</tr>';
            });
            
            html += '</tbody></table>';
            $list.html(html);
        }
        
        // Display file list
        function displayFileList(type, files) {
            var $list = $('#' + type + '-list');
            $list.empty();
            
            if (files.length === 0) {
                $list.html('<p style="color: #646970; text-align: center; padding: 20px;">' + (ioAdmin.i18n.noFiles || 'ไม่มีไฟล์') + '</p>');
                return;
            }
            
            var html = '<table class="wp-list-table widefat fixed striped" style="margin: 0;"><thead><tr><th style="width: 30px;"><input type="checkbox" class="select-all-' + type + '"></th><th>File</th><th style="width: 100px;">Size</th><th style="width: 150px;">Date</th></tr></thead><tbody>';
            
            files.forEach(function(file) {
                var size = formatFileSize(file.size || 0);
                var date = 'N/A';
                if (file.modified) {
                    var dateObj = new Date(file.modified * 1000);
                    date = dateObj.toLocaleDateString() + ' ' + dateObj.toLocaleTimeString();
                }
                var filename = file.filename || (file.path ? file.path.split(/[\\/]/).pop() : '');
                html += '<tr>';
                html += '<td><input type="checkbox" class="file-checkbox" data-path="' + escapeHtml(file.path) + '" data-type="' + type + '"></td>';
                html += '<td><code style="font-size: 11px; word-break: break-all;">' + escapeHtml(filename) + '</code></td>';
                html += '<td>' + size + '</td>';
                html += '<td style="font-size: 11px;">' + date + '</td>';
                html += '</tr>';
            });
            
            html += '</tbody></table>';
            $list.html(html);
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
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }
        
        // Select all checkbox
        $(document).on('change', '.select-all-thumbnails, .select-all-webp, .select-all-orphaned', function() {
            var type = $(this).hasClass('select-all-thumbnails') ? 'thumbnails' : 
                      $(this).hasClass('select-all-webp') ? 'webp' : 'orphaned';
            var checked = $(this).is(':checked');
            $('#' + type + '-list .file-checkbox[data-type="' + type + '"]').prop('checked', checked);
            updateDeleteButton();
        });
        
        // File checkbox change
        $(document).on('change', '.file-checkbox', function() {
            updateDeleteButton();
        });
        
        // Update delete button state
        function updateDeleteButton() {
            var checked = $('.file-checkbox:checked').length;
            $('#io-delete-selected').prop('disabled', checked === 0);
        }
        
        // Delete selected files
        $(document).on('click', '#io-delete-selected', function() {
            var selectedPaths = [];
            $('.file-checkbox:checked').each(function() {
                selectedPaths.push($(this).data('path'));
            });
            
            if (selectedPaths.length === 0) {
                alert(ioAdmin.i18n.noFilesSelected || 'กรุณาเลือกไฟล์ที่ต้องการลบ');
                return;
            }
            
            if (!confirm(ioAdmin.i18n.confirmDeleteSelected || 'คุณแน่ใจหรือไม่ว่าต้องการลบไฟล์ที่เลือก ' + selectedPaths.length + ' ไฟล์? การลบไม่สามารถยกเลิกได้')) {
                return;
            }
            
            deleteFiles(selectedPaths, false);
        });
        
        // Delete all files
        $(document).on('click', '#io-delete-all', function() {
            if (!confirm(ioAdmin.i18n.confirmDeleteAll || 'คุณแน่ใจหรือไม่ว่าต้องการลบไฟล์ทั้งหมด? การลบไม่สามารถยกเลิกได้')) {
                return;
            }
            
            deleteFiles([], true);
        });
        
        // Delete files
        function deleteFiles(filePaths, deleteAll) {
            var $progress = $('#io-delete-progress');
            var $progressBar = $('#io-delete-progress-bar');
            var $status = $('#io-delete-status');
            
            $progress.show();
            $progressBar.css('width', '0%');
            $status.html('<span class="dashicons dashicons-update" style="animation: spin 1s linear infinite; display: inline-block;"></span> ' + (ioAdmin.i18n.deleting || 'กำลังลบ...'));
            
            var ajaxData = {
                action: 'io_delete_unused_images',
                nonce: ioAdmin.nonce,
                dry_run: false
            };
            
            if (deleteAll) {
                ajaxData.delete_all = true;
            } else {
                ajaxData.files = filePaths;
            }
            
            $.ajax({
                url: ioAdmin.ajaxurl,
                type: 'POST',
                data: ajaxData,
                success: function(response) {
                    if (response.success) {
                        $progressBar.css('width', '100%');
                        var deleted = response.data.deleted || 0;
                        var failed = response.data.failed || 0;
                        var failedFiles = response.data.failed_files || [];
                        
                        var statusMsg = (ioAdmin.i18n.deleteComplete || 'ลบเสร็จสิ้น') + ': ' + deleted + ' ไฟล์';
                        if (failed > 0) {
                            statusMsg += ', <span style="color: #d63638;">ลบไม่สำเร็จ: ' + failed + ' ไฟล์</span>';
                        }
                        $status.html('<span class="dashicons dashicons-yes-alt" style="color: #00a32a;"></span> ' + statusMsg);
                        
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
                        setTimeout(function() {
                            $('#io-scan-unused-images').trigger('click');
                        }, 1000);
                    } else {
                        $status.html('<span class="dashicons dashicons-dismiss" style="color: #d63638;"></span> ' + (response.data.message || 'เกิดข้อผิดพลาด'));
                    }
                },
                error: function() {
                    $status.html('<span class="dashicons dashicons-dismiss" style="color: #d63638;"></span> ' + (ioAdmin.i18n.deleteError || 'เกิดข้อผิดพลาดในการลบ'));
                }
            });
        }
        
        // Download report
        $(document).on('click', '#io-download-report', function() {
            // This would generate a CSV file - implementation can be added later
            alert(ioAdmin.i18n.reportFeature || 'ฟีเจอร์นี้จะเพิ่มในอนาคต');
        });
        
        // Add spin animation
        if (!$('#io-spin-animation').length) {
            $('head').append('<style id="io-spin-animation">@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }</style>');
        }
        
        // Regenerate Images
        var regenerateState = {
            total: 0,
            processed: 0,
            success: 0,
            failed: 0,
            startTime: null,
            cancelled: false
        };
        
        // Count images before regenerate
        $(document).on('click', '#io-regenerate-images', function() {
            var $btn = $(this);
            $btn.prop('disabled', true);
            
            $.ajax({
                url: ioAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'io_count_images_for_regenerate',
                    nonce: ioAdmin.nonce
                },
                success: function(response) {
                    if (response.success && response.data.count > 0) {
                        regenerateState.total = response.data.count;
                        $('#io-regenerate-total-count').text(response.data.count);
                        $('#io-regenerate-preview').fadeIn(200);
                    } else {
                        alert('ไม่พบภาพที่ต้อง Regenerate');
                    }
                },
                error: function() {
                    alert('เกิดข้อผิดพลาดในการนับจำนวนภาพ');
                },
                complete: function() {
                    $btn.prop('disabled', false);
                }
            });
        });
        
        // Cancel preview
        $(document).on('click', '#io-regenerate-cancel-preview', function() {
            $('#io-regenerate-preview').fadeOut(200);
        });
        
        // Confirm regenerate
        $(document).on('click', '#io-regenerate-confirm', function() {
            $('#io-regenerate-preview').fadeOut(200);
            $('#io-regenerate-controls').hide();
            $('#io-regenerate-progress').fadeIn(200);
            
            // Reset state
            regenerateState.processed = 0;
            regenerateState.success = 0;
            regenerateState.failed = 0;
            regenerateState.startTime = Date.now();
            regenerateState.cancelled = false;
            
            // Clear cancel flag
            // Clear cancel flag by setting it to false
            $.ajax({
                url: ioAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'io_cancel_regenerate',
                    nonce: ioAdmin.nonce,
                    cancel: 0
                }
            });
            
            // Start regeneration
            processRegenerateBatch(0);
        });
        
        // Cancel regenerate
        $(document).on('click', '#io-regenerate-cancel', function() {
            if (!confirm('คุณแน่ใจหรือไม่ว่าต้องการยกเลิกการ Regenerate?')) {
                return;
            }
            
            // Set cancelled flag immediately
            regenerateState.cancelled = true;
            
            // Abort current AJAX request
            if (currentRegenerateRequest) {
                currentRegenerateRequest.abort();
                currentRegenerateRequest = null;
            }
            
            // Set cancel flag on server
            $.ajax({
                url: ioAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'io_cancel_regenerate',
                    nonce: ioAdmin.nonce
                },
                success: function() {
                    handleRegenerateCancelled();
                },
                error: function() {
                    // Even if server request fails, show cancelled state
                    handleRegenerateCancelled();
                }
            });
        });
        
        // Store current AJAX request for cancellation
        var currentRegenerateRequest = null;
        
        // Process regenerate batch
        function processRegenerateBatch(offset) {
            // Check if cancelled before starting
            if (regenerateState.cancelled) {
                handleRegenerateCancelled();
                return;
            }
            
            var batchSize = 10; // Process 10 images per batch
            
            // Cancel any pending request
            if (currentRegenerateRequest) {
                currentRegenerateRequest.abort();
            }
            
            currentRegenerateRequest = $.ajax({
                url: ioAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'io_regenerate_images',
                    nonce: ioAdmin.nonce,
                    batch_size: batchSize,
                    offset: offset,
                    total: regenerateState.total
                },
                success: function(response) {
                    currentRegenerateRequest = null;
                    
                    // Check if cancelled again after response
                    if (regenerateState.cancelled) {
                        handleRegenerateCancelled();
                        return;
                    }
                    
                    if (response.success) {
                        var data = response.data;
                        
                        regenerateState.processed = data.processed || 0;
                        regenerateState.success += data.success || 0;
                        regenerateState.failed += data.failed || 0;
                        
                        // Update UI
                        updateRegenerateProgress();
                        
                        if (data.done || data.cancelled) {
                            // Finished or cancelled
                            if (data.cancelled) {
                                handleRegenerateCancelled();
                            } else {
                                handleRegenerateComplete();
                            }
                        } else {
                            // Continue with next batch (only if not cancelled)
                            if (!regenerateState.cancelled) {
                                setTimeout(function() {
                                    processRegenerateBatch(data.processed);
                                }, 500); // Small delay to prevent overwhelming the server
                            } else {
                                handleRegenerateCancelled();
                            }
                        }
                    } else {
                        $('#io-regenerate-status').html(
                            '<span style="color: #d63638;">' + 
                            (response.data.message || ioAdmin.i18n.regenerateError || 'เกิดข้อผิดพลาด') + 
                            '</span>'
                        );
                    }
                },
                error: function(xhr, status, error) {
                    currentRegenerateRequest = null;
                    
                    // Don't show error if it was cancelled
                    if (status === 'abort' || regenerateState.cancelled) {
                        handleRegenerateCancelled();
                        return;
                    }
                    
                    $('#io-regenerate-status').html(
                        '<span style="color: #d63638;">' + 
                        (ioAdmin.i18n.regenerateError || 'เกิดข้อผิดพลาดในการ Regenerate') + 
                        '</span>'
                    );
                }
            });
        }
        
        // Handle regenerate cancelled
        function handleRegenerateCancelled() {
            // Stop any pending requests
            if (currentRegenerateRequest) {
                currentRegenerateRequest.abort();
                currentRegenerateRequest = null;
            }
            
            // Update UI immediately
            $('#io-regenerate-status').html(
                '<span style="color: #d63638; font-weight: 600; display: block; padding: 10px; background: #fcf0f1; border-left: 4px solid #d63638; border-radius: 4px;">' + 
                '<span class="dashicons dashicons-dismiss" style="vertical-align: middle;"></span> ' +
                (ioAdmin.i18n.regenerateCancelled || 'Regenerate ถูกยกเลิก') + 
                '</span>'
            );
            $('#io-regenerate-cancel').hide();
            
            // Stop progress animation
            $('#io-regenerate-progress .dashicons-update').css('animation', 'none');
            
            // Update ETA to show cancelled
            $('#io-regenerate-eta').text('-');
            
            // Show restart button after 2 seconds
            setTimeout(function() {
                $('#io-regenerate-controls').fadeIn(200);
                $('#io-regenerate-progress').fadeOut(200);
            }, 2000);
        }
        
        // Handle regenerate complete
        function handleRegenerateComplete() {
            $('#io-regenerate-status').html(
                '<span style="color: #00a32a; font-weight: 600;">' + 
                (ioAdmin.i18n.regenerateComplete || 'Regenerate เสร็จสิ้น') + 
                '</span>'
            );
            $('#io-regenerate-cancel').hide();
            
            // Show restart button after 3 seconds
            setTimeout(function() {
                $('#io-regenerate-controls').fadeIn(200);
                $('#io-regenerate-progress').fadeOut(200);
            }, 3000);
        }
        
        // Update regenerate progress
        function updateRegenerateProgress() {
            // Don't update if cancelled
            if (regenerateState.cancelled) {
                return;
            }
            
            var processed = regenerateState.processed;
            var total = regenerateState.total;
            var success = regenerateState.success;
            var failed = regenerateState.failed;
            var percentage = total > 0 ? Math.round((processed / total) * 100) : 0;
            
            // Update progress bar
            $('#io-regenerate-progress-bar').css('width', percentage + '%').text(percentage + '%');
            
            // Update stats
            $('#io-regenerate-processed').text(processed);
            $('#io-regenerate-total').text(total);
            $('#io-regenerate-success').text(success);
            $('#io-regenerate-failed').text(failed);
            
            // Calculate time
            if (regenerateState.startTime) {
                var elapsed = Math.floor((Date.now() - regenerateState.startTime) / 1000);
                var minutes = Math.floor(elapsed / 60);
                var seconds = elapsed % 60;
                $('#io-regenerate-time').text(minutes + ':' + (seconds < 10 ? '0' : '') + seconds);
                
                // Calculate ETA (only if not cancelled)
                if (!regenerateState.cancelled && processed > 0 && processed < total) {
                    var avgTimePerImage = elapsed / processed;
                    var remaining = total - processed;
                    var etaSeconds = Math.floor(avgTimePerImage * remaining);
                    var etaMinutes = Math.floor(etaSeconds / 60);
                    var etaSecs = etaSeconds % 60;
                    $('#io-regenerate-eta').text(etaMinutes + ':' + (etaSecs < 10 ? '0' : '') + etaSecs);
                } else if (processed >= total || regenerateState.cancelled) {
                    $('#io-regenerate-eta').text('-');
                }
            }
            
            // Update status (only if not cancelled)
            if (!regenerateState.cancelled) {
                var remaining = total - processed;
                $('#io-regenerate-status').html(
                    '<span class="dashicons dashicons-update" style="animation: spin 1s linear infinite;"></span> ' +
                    'กำลังประมวลผล... เหลืออีก ' + remaining + ' ไฟล์'
                );
            }
        }
    });
})();
