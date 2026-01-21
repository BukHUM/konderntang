/**
 * Main Navigation and Core Functions
 *
 * @package KonDernTang
 * @since 1.0.0
 */

(function() {
    'use strict';

    // Breadcrumb Configuration (from PHP)
    const breadcrumbConfig = window.konderntangData?.breadcrumbConfig || {};

    // Breadcrumb Management
    function updateBreadcrumb(pageName) {
        const breadcrumb = document.getElementById('breadcrumb');
        const breadcrumbCurrent = document.getElementById('breadcrumb-current');
        const breadcrumbSeparator = document.getElementById('breadcrumb-separator');
        
        if (!breadcrumb || !breadcrumbCurrent) return;
        
        const config = breadcrumbConfig[pageName];
        
        if (config && config.show) {
            breadcrumb.classList.remove('hidden');
            if (breadcrumbCurrent) {
                breadcrumbCurrent.textContent = config.text;
            }
            if (breadcrumbSeparator) {
                breadcrumbSeparator.classList.remove('hidden');
            }
        } else {
            breadcrumb.classList.add('hidden');
        }
    }

    // Mobile Submenu Toggle
    function toggleMobileSubmenu(submenuId) {
        const submenu = document.getElementById(submenuId);
        const icon = document.getElementById(submenuId + '-icon');
        
        if (submenu && icon) {
            submenu.classList.toggle('hidden');
            icon.classList.toggle('rotate-180');
        }
    }

    // Expose functions globally
    window.updateBreadcrumb = updateBreadcrumb;
    window.toggleMobileSubmenu = toggleMobileSubmenu;

    // Social Share - Copy Link Handler
    function initShareCopyLink() {
        const copyButtons = document.querySelectorAll('.konderntang-share-copy');
        
        copyButtons.forEach(function(button) {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                const url = this.getAttribute('data-copy-url');
                if (!url) return;
                
                // Copy to clipboard
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(url).then(function() {
                        showCopySuccess(button);
                    }).catch(function() {
                        fallbackCopy(url, button);
                    });
                } else {
                    fallbackCopy(url, button);
                }
            });
        });
    }

    // Fallback copy for older browsers
    function fallbackCopy(text, button) {
        const textArea = document.createElement('textarea');
        textArea.value = text;
        textArea.style.position = 'fixed';
        textArea.style.left = '-9999px';
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        
        try {
            document.execCommand('copy');
            showCopySuccess(button);
        } catch (err) {
            console.error('Copy failed:', err);
        }
        
        document.body.removeChild(textArea);
    }

    // Show copy success feedback
    function showCopySuccess(button) {
        button.classList.add('copied');
        
        // Store original text if exists
        const textEl = button.querySelector('.share-text');
        const originalText = textEl ? textEl.textContent : '';
        
        if (textEl) {
            textEl.textContent = window.konderntangData?.copiedText || 'Copied!';
        }
        
        // Reset after 2 seconds
        setTimeout(function() {
            button.classList.remove('copied');
            if (textEl && originalText) {
                textEl.textContent = originalText;
            }
        }, 2000);
    }

    // Social Share - Open popup window for share links
    function initSharePopup() {
        const shareButtons = document.querySelectorAll('.konderntang-share-button[target="_blank"]');
        
        shareButtons.forEach(function(button) {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                const url = this.getAttribute('href');
                const width = 600;
                const height = 400;
                const left = (screen.width - width) / 2;
                const top = (screen.height - height) / 2;
                
                window.open(
                    url,
                    'share',
                    'width=' + width + ',height=' + height + ',left=' + left + ',top=' + top + ',toolbar=0,menubar=0,location=0,status=0'
                );
            });
        });
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Determine current page
        let currentPage = 'home';
        if (document.body.classList.contains('single')) {
            currentPage = 'single';
        } else if (document.body.classList.contains('archive')) {
            currentPage = 'archive';
        } else if (document.body.classList.contains('search')) {
            currentPage = 'search';
        } else if (document.body.classList.contains('error404')) {
            currentPage = '404';
        }
        
        updateBreadcrumb(currentPage);

        // Initialize social share features
        initShareCopyLink();
        initSharePopup();
    });

})();
