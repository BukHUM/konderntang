/**
 * Error Pages Functions
 *
 * @package KonDernTang
 * @since 1.0.0
 */

(function() {
    'use strict';

    function initErrorPages() {
        // Initialize 500 error page details
        const errorTimestamp = document.getElementById('error-timestamp');
        const errorRequestId = document.getElementById('error-request-id');
        
        if (errorTimestamp) {
            errorTimestamp.textContent = new Date().toLocaleString('th-TH', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
        }
        
        if (errorRequestId) {
            // Generate a random request ID for demo purposes
            errorRequestId.textContent = 'REQ-' + Math.random().toString(36).substr(2, 9).toUpperCase();
        }
    }

    // Initialize error pages when 500 page is loaded
    document.addEventListener('pageLoaded', function(event) {
        if (event.detail.pageName === '500') {
            setTimeout(initErrorPages, 100);
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        if (document.body.classList.contains('error500') || document.getElementById('error-timestamp')) {
            initErrorPages();
        }
    });
})();
