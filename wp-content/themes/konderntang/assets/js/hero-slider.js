/**
 * Hero Slider Functionality
 *
 * @package KonDernTang
 * @since 1.0.0
 */

(function() {
    'use strict';

    let currentSlide = 0;
    let totalSlides = 0;
    const slides = document.querySelectorAll('.hero-slide');
    const dots = document.querySelectorAll('.hero-dot');
    const prevButton = document.getElementById('hero-prev');
    const nextButton = document.getElementById('hero-next');

    if (slides.length === 0) {
        return;
    }

    totalSlides = slides.length;

    function showSlide(index) {
        // Hide all slides
        slides.forEach((slide, i) => {
            if (i === index) {
                slide.classList.remove('opacity-0');
                slide.classList.add('opacity-100');
            } else {
                slide.classList.remove('opacity-100');
                slide.classList.add('opacity-0');
            }
        });

        // Update dots
        dots.forEach((dot, i) => {
            if (i === index) {
                dot.classList.remove('bg-white/50', 'hover:bg-white/75');
                dot.classList.add('bg-white');
            } else {
                dot.classList.remove('bg-white');
                dot.classList.add('bg-white/50', 'hover:bg-white/75');
            }
        });

        currentSlide = index;
    }

    function nextSlide() {
        const next = (currentSlide + 1) % totalSlides;
        showSlide(next);
    }

    function prevSlide() {
        const prev = (currentSlide - 1 + totalSlides) % totalSlides;
        showSlide(prev);
    }

    // Event listeners
    if (nextButton) {
        nextButton.addEventListener('click', nextSlide);
    }

    if (prevButton) {
        prevButton.addEventListener('click', prevSlide);
    }

    // Dot navigation
    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => {
            showSlide(index);
        });
    });

    // Auto-play (optional)
    let autoPlayInterval = null;
    const autoPlayEnabled = true; // Can be made configurable

    function startAutoPlay() {
        if (autoPlayEnabled && totalSlides > 1) {
            autoPlayInterval = setInterval(nextSlide, 5000); // Change slide every 5 seconds
        }
    }

    function stopAutoPlay() {
        if (autoPlayInterval) {
            clearInterval(autoPlayInterval);
            autoPlayInterval = null;
        }
    }

    // Start auto-play
    startAutoPlay();

    // Pause on hover
    const heroSlider = document.getElementById('hero-slider');
    if (heroSlider) {
        heroSlider.addEventListener('mouseenter', stopAutoPlay);
        heroSlider.addEventListener('mouseleave', startAutoPlay);
    }

})();
