/**
 * Utility Functions
 *
 * @package KonDernTang
 * @since 1.0.0
 */

(function() {
    'use strict';

    // Back to Top Button
    const backToTopButton = document.getElementById('back-to-top');

    function scrollToTop() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }

    function toggleBackToTop() {
        if (!backToTopButton) return;
        
        if (window.scrollY > 300) {
            backToTopButton.classList.remove('opacity-0', 'pointer-events-none');
            backToTopButton.classList.add('opacity-100', 'pointer-events-auto');
        } else {
            backToTopButton.classList.add('opacity-0', 'pointer-events-none');
            backToTopButton.classList.remove('opacity-100', 'pointer-events-auto');
        }
    }

    // Expose function globally
    window.scrollToTop = scrollToTop;

    // Show/hide button on scroll
    if (backToTopButton) {
        window.addEventListener('scroll', toggleBackToTop);
        toggleBackToTop();
    }

})();
