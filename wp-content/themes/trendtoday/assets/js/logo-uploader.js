/**
 * Logo Uploader Script for Trend Today Theme
 * 
 * @package TrendToday
 * @since 1.0.0
 */

(function($) {
    'use strict';
    
    var logoUploader;
    
    // Wait for wp.media to be available
    function initLogoUploader() {
        if (typeof wp === 'undefined' || typeof wp.media === 'undefined') {
            setTimeout(initLogoUploader, 100);
            return;
        }
        
        $('#trendtoday_upload_logo_btn').off('click').on('click', function(e) {
            e.preventDefault();
            
            // If the uploader object has already been created, reopen it
            if (logoUploader) {
                logoUploader.open();
                return;
            }
            
            // Create the media uploader
            logoUploader = wp.media({
                title: trendtodayLogo.chooseLogo,
                button: {
                    text: trendtodayLogo.useLogo
                },
                library: {
                    type: 'image'
                },
                multiple: false
            });
            
            // When an image is selected, run a callback
            logoUploader.on('select', function() {
                var attachment = logoUploader.state().get('selection').first().toJSON();
                $('#trendtoday_logo').val(attachment.id);
                $('#trendtoday_logo_preview').html('<img src="' + attachment.url + '" style="max-width: 200px; height: auto; display: block; margin-bottom: 10px;" />');
                $('#trendtoday_upload_logo_btn').text(trendtodayLogo.changeLogo);
                if ($('#trendtoday_remove_logo_btn').length === 0) {
                    $('#trendtoday_upload_logo_btn').after('<button type="button" class="button" id="trendtoday_remove_logo_btn" style="margin-left: 10px;">' + trendtodayLogo.removeLogo + '</button>');
                }
            });
            
            // Open the uploader
            logoUploader.open();
        });
        
        // Remove logo
        $(document).off('click', '#trendtoday_remove_logo_btn').on('click', '#trendtoday_remove_logo_btn', function(e) {
            e.preventDefault();
            $('#trendtoday_logo').val('');
            $('#trendtoday_logo_preview').html('');
            $('#trendtoday_upload_logo_btn').text(trendtodayLogo.uploadLogo);
            $(this).remove();
        });
    }
    
    // Initialize when DOM is ready
    $(document).ready(function() {
        initLogoUploader();
    });
    
})(jQuery);
