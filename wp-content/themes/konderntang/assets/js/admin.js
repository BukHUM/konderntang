/**
 * Admin JavaScript for KonDernTang Theme
 *
 * @package KonDernTang
 * @since 1.0.0
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        
        // ============================================
        // SIDEBAR NAVIGATION
        // ============================================
        
        // Handle sidebar navigation clicks
        $('.konderntang-nav-item').on('click', function(e) {
            e.preventDefault();
            const sectionId = $(this).data('section');
            const $targetSection = $('#section-' + sectionId);
            
            if ($targetSection.length) {
                // Update active state
                $('.konderntang-nav-item').removeClass('active');
                $(this).addClass('active');
                
                // Scroll to section (fast scroll with shorter duration)
                const offset = $('.konderntang-settings-header').outerHeight() + 40;
                const targetPosition = $targetSection.offset().top - offset;
                
                // Use faster animation (200ms instead of 500ms)
                $('html, body').animate({
                    scrollTop: targetPosition
                }, 200, 'linear', function() {
                    // Expand section if collapsed
                    $targetSection.addClass('active');
                });
                
                // Update URL without reload
                const newUrl = window.location.pathname + '?page=konderntang-settings&section=' + sectionId;
                window.history.pushState({}, '', newUrl);
                
                // Save to localStorage
                localStorage.setItem('konderntang_active_section', sectionId);
            }
        });
        
        // Restore active section from URL or localStorage
        const urlParams = new URLSearchParams(window.location.search);
        const urlSection = urlParams.get('section');
        const savedSection = localStorage.getItem('konderntang_active_section');
        const activeSection = urlSection || savedSection || 'general';
        
        if (activeSection) {
            const $navItem = $('.konderntang-nav-item[data-section="' + activeSection + '"]');
            if ($navItem.length) {
                $navItem.addClass('active');
                setTimeout(function() {
                    const $targetSection = $('#section-' + activeSection);
                    if ($targetSection.length) {
                        const offset = $('.konderntang-settings-header').outerHeight() + 40;
                        $('html, body').scrollTop($targetSection.offset().top - offset);
                    }
                }, 100);
            }
        }
        
        // Highlight section on scroll
        let scrollTimeout;
        $(window).on('scroll', function() {
            clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(function() {
                const scrollTop = $(window).scrollTop();
                const offset = $('.konderntang-settings-header').outerHeight() + 100;
                
                $('.konderntang-settings-section').each(function() {
                    const $section = $(this);
                    const sectionTop = $section.offset().top - offset;
                    const sectionBottom = sectionTop + $section.outerHeight();
                    
                    if (scrollTop >= sectionTop && scrollTop < sectionBottom) {
                        const sectionId = $section.data('section');
                        $('.konderntang-nav-item').removeClass('active');
                        $('.konderntang-nav-item[data-section="' + sectionId + '"]').addClass('active');
                    }
                });
            }, 100);
        });
        
        // ============================================
        // SEARCH FUNCTIONALITY
        // ============================================
        
        $('#konderntang-settings-search').on('input', function() {
            const searchTerm = $(this).val().toLowerCase();
            
            if (searchTerm === '') {
                $('.konderntang-nav-item').removeClass('hidden');
                $('.konderntang-settings-section').show();
            } else {
                // Filter navigation items
                $('.konderntang-nav-item').each(function() {
                    const $item = $(this);
                    const label = $item.find('.nav-item-label').text().toLowerCase();
                    const desc = $item.find('.nav-item-desc').text().toLowerCase();
                    
                    if (label.includes(searchTerm) || desc.includes(searchTerm)) {
                        $item.removeClass('hidden');
                    } else {
                        $item.addClass('hidden');
                    }
                });
                
                // Filter sections
                $('.konderntang-settings-section').each(function() {
                    const $section = $(this);
                    const sectionId = $section.data('section');
                    const $navItem = $('.konderntang-nav-item[data-section="' + sectionId + '"]');
                    
                    if ($navItem.hasClass('hidden')) {
                        $section.hide();
                    } else {
                        $section.show();
                    }
                });
            }
        });
        
        // ============================================
        // COLOR PICKER SYNC
        // ============================================
        
        // Color picker to text input
        $('input[type="color"]').on('change', function() {
            const color = $(this).val();
            const $textInput = $(this).siblings('.konderntang-color-value');
            if ($textInput.length) {
                $textInput.val(color);
            }
        });
        
        // Text input to color picker
        $('.konderntang-color-value').on('input', function() {
            const color = $(this).val();
            const $colorInput = $(this).siblings('input[type="color"]');
            if ($colorInput.length && /^#[0-9A-F]{6}$/i.test(color)) {
                $colorInput.val(color);
            }
        });
        
        // ============================================
        // MEDIA UPLOADER
        // ============================================
        
        $('.media-upload-button').on('click', function(e) {
            e.preventDefault();
            const button = $(this);
            const targetInput = $('#' + button.data('target'));
            const imagePreview = button.closest('td').find('.konderntang-image-preview');
            
            const mediaUploader = wp.media({
                title: konderntangAdmin.i18n.chooseImage || 'Choose Image',
                button: {
                    text: konderntangAdmin.i18n.useImage || 'Use Image'
                },
                multiple: false
            });
            
            mediaUploader.on('select', function() {
                const attachment = mediaUploader.state().get('selection').first().toJSON();
                targetInput.val(attachment.url);
                
                if (imagePreview.length) {
                    if (imagePreview.find('img').length === 0) {
                        imagePreview.html('<img src="' + attachment.url + '" alt="Preview" />');
                    } else {
                        imagePreview.find('img').attr('src', attachment.url);
                    }
                    imagePreview.show();
                }
                
                // Show remove button
                button.siblings('.konderntang-remove-image').show();
            });
            
            mediaUploader.open();
        });
        
        // Remove image button
        $('.konderntang-remove-image').on('click', function() {
            const $this = $(this);
            const targetInput = $('#' + $this.siblings('.media-upload-button').data('target'));
            const imagePreview = $this.closest('td').find('.konderntang-image-preview');
            
            targetInput.val('');
            imagePreview.hide().find('img').attr('src', '');
            $this.hide();
        });
        
        // Show preview on page load if image exists
        function initImagePreviews() {
            $('.konderntang-field-group input[type="text"]').each(function() {
                const $input = $(this);
                const imageUrl = $input.val().trim();
                if (imageUrl) {
                    const imagePreview = $input.closest('td').find('.konderntang-image-preview');
                    if (imagePreview.length) {
                        if (imagePreview.find('img').length === 0) {
                            imagePreview.html('<img src="' + imageUrl + '" alt="Preview" />');
                        } else {
                            imagePreview.find('img').attr('src', imageUrl);
                        }
                        imagePreview.show();
                        // Show remove button
                        $input.siblings('.konderntang-remove-image').show();
                    }
                } else {
                    // Hide preview and remove button if no image
                    const imagePreview = $input.closest('td').find('.konderntang-image-preview');
                    imagePreview.hide();
                    $input.siblings('.konderntang-remove-image').hide();
                }
            });
        }
        
        // Initialize on page load
        initImagePreviews();
        
        // Also initialize after form submit (in case of redirect)
        $(document).ready(function() {
            initImagePreviews();
        });
        
        // ============================================
        // FORM VALIDATION
        // ============================================
        
        $('#konderntang-settings-form').on('submit', function(e) {
            // Debug: Log form submit
            console.log('KonDernTang Form Submit triggered');
            console.log('Form data:', $(this).serialize());
            
            // Update active section before submit
            const activeSection = $('.konderntang-nav-item.active').data('section') || 'general';
            $('#active_section').val(activeSection);
            
            let hasErrors = false;
            
            // Validate number inputs
            $(this).find('input[type="number"]').each(function() {
                const $input = $(this);
                const min = parseFloat($input.attr('min'));
                const max = parseFloat($input.attr('max'));
                const value = parseFloat($input.val());
                
                if (!isNaN(min) && !isNaN(max) && (value < min || value > max)) {
                    $input.css('border-color', '#ef4444');
                    hasErrors = true;
                    
                    setTimeout(function() {
                        $input.css('border-color', '');
                    }, 3000);
                }
            });
            
            if (hasErrors) {
                e.preventDefault();
                alert('กรุณาตรวจสอบค่าที่กรอกให้ถูกต้อง');
                return false;
            }
            
            // Show loading state
            const $submitBtn = $(this).find('.konderntang-save-btn');
            const originalText = $submitBtn.html();
            $submitBtn.html('<span class="dashicons dashicons-update"></span> ' + 'กำลังบันทึก...').prop('disabled', true);
            
            // Re-enable after a delay (in case of error)
            setTimeout(function() {
                $submitBtn.html(originalText).prop('disabled', false);
            }, 5000);
        });
        
        // ============================================
        // AUTO-SAVE INDICATOR
        // ============================================
        
        let saveTimeout;
        $('#konderntang-settings-form input, #konderntang-settings-form select, #konderntang-settings-form textarea').on('change', function() {
            clearTimeout(saveTimeout);
            
            const $indicator = $('<span class="konderntang-auto-save-indicator" style="color: #10b981; margin-left: 10px; font-size: 12px; display: inline-flex; align-items: center; gap: 4px;"><span class="dashicons dashicons-edit" style="font-size: 14px;"></span> มีการเปลี่ยนแปลง</span>');
            const $submitBtn = $('#konderntang-settings-form .konderntang-save-btn');
            
            $submitBtn.siblings('.konderntang-auto-save-indicator').remove();
            $submitBtn.after($indicator);
            
            setTimeout(function() {
                $indicator.fadeOut(function() {
                    $(this).remove();
                });
            }, 3000);
        });
        
        // ============================================
        // SMOOTH SCROLL FOR ANCHOR LINKS
        // ============================================
        
        $('a[href^="#section-"]').on('click', function(e) {
            e.preventDefault();
            const target = $(this.getAttribute('href'));
            if (target.length) {
                const offset = $('.konderntang-settings-header').outerHeight() + 40;
                const targetPosition = target.offset().top - offset;
                
                // Use faster animation (200ms)
                $('html, body').animate({
                    scrollTop: targetPosition
                }, 200, 'linear');
            }
        });
        
        // ============================================
        // KEYBOARD SHORTCUTS
        // ============================================
        
        $(document).on('keydown', function(e) {
            // Ctrl/Cmd + S to save
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                e.preventDefault();
                $('#konderntang-settings-form').submit();
            }
            
            // Ctrl/Cmd + K to focus search
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                $('#konderntang-settings-search').focus();
            }
        });
        
        // ============================================
        // SECTION COLLAPSE/EXPAND (Optional)
        // ============================================
        
        $('.konderntang-section-header').on('click', function() {
            const $section = $(this).closest('.konderntang-settings-section');
            const $content = $section.find('.konderntang-section-content');
            
            if ($section.hasClass('collapsed')) {
                $section.removeClass('collapsed');
                $content.slideDown(300);
            } else {
                $section.addClass('collapsed');
                $content.slideUp(300);
            }
        });
        
    });
    
})(jQuery);
