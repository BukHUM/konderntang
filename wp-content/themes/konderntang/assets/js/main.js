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
    });

})();
