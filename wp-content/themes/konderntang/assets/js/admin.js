/**
 * Admin JavaScript for KonDernTang Theme
 *
 * @package KonDernTang
 * @since 1.0.0
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        
        // Settings Groups Accordion
        $('.konderntang-settings-group-header').on('click', function() {
            const $group = $(this).closest('.konderntang-settings-group');
            const $allGroups = $('.konderntang-settings-group');
            
            // Toggle current group
            if ($group.hasClass('active')) {
                $group.removeClass('active');
            } else {
                // Close all groups first
                $allGroups.removeClass('active');
                // Open clicked group
                $group.addClass('active');
            }
        });
        
        // Auto-expand group containing active tab
        $('.konderntang-settings-group').each(function() {
            if ($(this).find('.konderntang-group-tab.active').length > 0) {
                $(this).addClass('active');
            }
        });
        
        // Tab persistence with smooth scrolling
        $('.konderntang-group-tab, .nav-tab').on('click', function() {
            const href = $(this).attr('href');
            if (href && href.indexOf('tab=') !== -1) {
                const tab = href.split('tab=')[1].split('&')[0];
                if (tab) {
                    localStorage.setItem('konderntang_active_tab', tab);
                }
            }
        });

        // Restore active tab
        const savedTab = localStorage.getItem('konderntang_active_tab');
        if (savedTab) {
            const tabLink = $('.konderntang-group-tab[href*="tab=' + savedTab + '"], .nav-tab[href*="tab=' + savedTab + '"]');
            if (tabLink.length) {
                // Expand the group containing this tab
                tabLink.closest('.konderntang-settings-group').addClass('active');
                $('html, body').animate({
                    scrollTop: tabLink.offset().top - 100
                }, 300);
            }
        }

        // Color picker preview and sync with text input
        $('input[type="color"]').on('change', function() {
            const color = $(this).val();
            const $textInput = $(this).siblings('.konderntang-color-value');
            if ($textInput.length) {
                $textInput.val(color);
            }
        });

        // Sync text input to color picker
        $('.konderntang-color-value').on('input', function() {
            const color = $(this).val();
            const $colorInput = $(this).siblings('input[type="color"]');
            if ($colorInput.length && /^#[0-9A-F]{6}$/i.test(color)) {
                $colorInput.val(color);
            }
        });

        // Form validation feedback
        $('#konderntang-settings-form').on('submit', function(e) {
            let hasErrors = false;
            
            // Validate number inputs
            $(this).find('input[type="number"]').each(function() {
                const $input = $(this);
                const min = parseFloat($input.attr('min'));
                const max = parseFloat($input.attr('max'));
                const value = parseFloat($input.val());
                
                if (value < min || value > max) {
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
            const $submitBtn = $(this).find('.button-primary');
            const originalText = $submitBtn.val();
            $submitBtn.val('กำลังบันทึก...').prop('disabled', true);
            
            // Re-enable after a delay (in case of error)
            setTimeout(function() {
                $submitBtn.val(originalText).prop('disabled', false);
            }, 5000);
        });

        // Auto-save indicator
        let saveTimeout;
        $('#konderntang-settings-form input, #konderntang-settings-form select, #konderntang-settings-form textarea').on('change', function() {
            clearTimeout(saveTimeout);
            
            const $indicator = $('<span class="konderntang-auto-save-indicator" style="color: #10b981; margin-left: 10px; font-size: 12px;">⚡ มีการเปลี่ยนแปลง</span>');
            const $submitBtn = $('#konderntang-settings-form .button-primary');
            
            $submitBtn.siblings('.konderntang-auto-save-indicator').remove();
            $submitBtn.after($indicator);
            
            setTimeout(function() {
                $indicator.fadeOut(function() {
                    $(this).remove();
                });
            }, 3000);
        });

        // Collapsible sections (for better organization)
        $('.konderntang-section-header').on('click', function() {
            const $section = $(this).next('.konderntang-section-content');
            $section.slideToggle();
            $(this).find('.dashicons').toggleClass('dashicons-arrow-down-alt2 dashicons-arrow-up-alt2');
        });

        // Tooltip initialization
        $('.konderntang-tooltip').each(function() {
            const tooltip = $(this).attr('data-tooltip');
            if (tooltip) {
                $(this).attr('title', tooltip);
            }
        });

        // Enhanced media uploader with preview
        $('.media-upload-button').on('click', function(e) {
            e.preventDefault();
            const button = $(this);
            const targetInput = $('#' + button.data('target'));
            const previewContainer = targetInput.siblings('.konderntang-image-preview');
            
            const mediaUploader = wp.media({
                title: 'เลือกภาพ',
                button: {
                    text: 'ใช้ภาพนี้'
                },
                multiple: false
            });
            
            mediaUploader.on('select', function() {
                const attachment = mediaUploader.state().get('selection').first().toJSON();
                targetInput.val(attachment.url);
                
                // Show preview
                if (previewContainer.length === 0) {
                    const preview = $('<div class="konderntang-image-preview" style="margin-top: 10px;"><img src="" style="max-width: 200px; height: auto; border-radius: 6px; border: 1px solid #e2e8f0;" /></div>');
                    targetInput.after(preview);
                    preview.find('img').attr('src', attachment.url);
                } else {
                    previewContainer.find('img').attr('src', attachment.url);
                }
            });
            
            mediaUploader.open();
        });

        // Remove image preview
        $(document).on('click', '.konderntang-remove-image', function() {
            const $preview = $(this).closest('.konderntang-image-preview');
            const $input = $preview.siblings('input[type="text"]');
            $input.val('');
            $preview.fadeOut(function() {
                $(this).remove();
            });
        });

        // Number input steppers
        $('input[type="number"]').each(function() {
            const $input = $(this);
            const wrapper = $('<div class="konderntang-number-wrapper" style="display: inline-flex; align-items: center; gap: 5px;"></div>');
            
            $input.wrap(wrapper);
            
            const $decrease = $('<button type="button" class="button kecil" style="padding: 4px 8px;">−</button>');
            const $increase = $('<button type="button" class="button kecil" style="padding: 4px 8px;">+</button>');
            
            $input.before($decrease);
            $input.after($increase);
            
            $decrease.on('click', function() {
                const min = parseFloat($input.attr('min')) || 0;
                const current = parseFloat($input.val()) || 0;
                const step = parseFloat($input.attr('step')) || 1;
                const newValue = Math.max(min, current - step);
                $input.val(newValue).trigger('change');
            });
            
            $increase.on('click', function() {
                const max = parseFloat($input.attr('max')) || 100;
                const current = parseFloat($input.val()) || 0;
                const step = parseFloat($input.attr('step')) || 1;
                const newValue = Math.min(max, current + step);
                $input.val(newValue).trigger('change');
            });
        });

        // Copy to clipboard functionality
        $('.konderntang-copy-button').on('click', function(e) {
            e.preventDefault();
            const text = $(this).data('copy');
            const $temp = $('<textarea>');
            $('body').append($temp);
            $temp.val(text).select();
            document.execCommand('copy');
            $temp.remove();
            
            const originalText = $(this).text();
            $(this).text('คัดลอกแล้ว!').css('color', '#10b981');
            
            setTimeout(function() {
                $(this).text(originalText).css('color', '');
            }.bind(this), 2000);
        });
    });
})(jQuery);
