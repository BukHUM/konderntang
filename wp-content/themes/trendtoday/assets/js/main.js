/**
 * Main JavaScript for Trend Today Theme
 * 
 * @package TrendToday
 * @since 1.0.0
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        // Initialize theme features
        initLazyLoading();
        initSmoothScroll();
        initAccessibility();
        initPerformanceOptimizations();
    });

    /**
     * Initialize lazy loading for images
     */
    function initLazyLoading() {
        if ('loading' in HTMLImageElement.prototype) {
            const images = document.querySelectorAll('img[loading="lazy"]');
            images.forEach(img => {
                if (img.dataset.src) {
                    img.src = img.dataset.src;
                }
            });
        } else {
            // Fallback for browsers that don't support native lazy loading
            const script = document.createElement('script');
            script.src = 'https://cdnjs.cloudflare.com/ajax/libs/lazysizes/5.3.2/lazysizes.min.js';
            document.body.appendChild(script);
        }
    }

    /**
     * Initialize smooth scroll
     */
    function initSmoothScroll() {
        // Smooth scroll for anchor links
        $('a[href^="#"]').on('click', function(e) {
            const target = $(this.getAttribute('href'));
            if (target.length) {
                e.preventDefault();
                $('html, body').animate({
                    scrollTop: target.offset().top - 80
                }, 600);
            }
        });
    }

    /**
     * Initialize accessibility features
     */
    function initAccessibility() {
        // Skip to content link
        if (!$('#skip-to-content').length) {
            $('body').prepend('<a href="#main-content" id="skip-to-content" class="sr-only focus:not-sr-only focus:absolute focus:top-0 focus:left-0 focus:z-50 focus:px-4 focus:py-2 focus:bg-accent focus:text-white">Skip to content</a>');
        }

        // Keyboard navigation for mobile menu
        $('#mobile-menu-button').on('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                toggleMobileMenu();
            }
        });

        // Focus trap for mobile menu
        $('#mobile-menu').on('keydown', function(e) {
            if (e.key === 'Escape') {
                const menu = document.getElementById('mobile-menu');
                const button = document.getElementById('mobile-menu-button');
                if (menu && !menu.classList.contains('hidden')) {
                    toggleMobileMenu();
                    button.focus();
                }
            }
        });
    }

    /**
     * Initialize performance optimizations
     */
    function initPerformanceOptimizations() {
        // Preload critical resources
        const criticalImages = document.querySelectorAll('img[data-critical]');
        criticalImages.forEach(img => {
            const link = document.createElement('link');
            link.rel = 'preload';
            link.as = 'image';
            link.href = img.src;
            document.head.appendChild(link);
        });

        // Defer non-critical CSS
        if (window.requestIdleCallback) {
            requestIdleCallback(function() {
                const nonCriticalCSS = document.querySelectorAll('link[rel="stylesheet"][data-non-critical]');
                nonCriticalCSS.forEach(link => {
                    link.media = 'print';
                    link.onload = function() {
                        this.media = 'all';
                    };
                });
            });
        }
    }

    // Expose global functions
    window.trendtodayInit = {
        lazyLoading: initLazyLoading,
        smoothScroll: initSmoothScroll,
        accessibility: initAccessibility
    };

})(jQuery);
