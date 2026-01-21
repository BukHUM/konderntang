/**
 * Countdown Timer for Promotion Page
 *
 * @package KonDernTang
 * @since 1.0.0
 */

(function() {
    'use strict';

    let countdownInterval = null;

    function updateCountdown() {
        // Clear existing interval if any
        if (countdownInterval) {
            clearInterval(countdownInterval);
        }
        
        const targetDate = new Date();
        targetDate.setDate(targetDate.getDate() + 2); // 2 days from now
        targetDate.setHours(23, 59, 59, 999);
        
        function update() {
            const now = new Date().getTime();
            const distance = targetDate - now;
            
            if (distance < 0) {
                // Reset countdown
                targetDate.setDate(targetDate.getDate() + 2);
                return;
            }
            
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            const daysEl = document.getElementById('countdown-days');
            const hoursEl = document.getElementById('countdown-hours');
            const minutesEl = document.getElementById('countdown-minutes');
            const secondsEl = document.getElementById('countdown-seconds');
            
            if (daysEl) daysEl.textContent = String(days).padStart(2, '0');
            if (hoursEl) hoursEl.textContent = String(hours).padStart(2, '0');
            if (minutesEl) minutesEl.textContent = String(minutes).padStart(2, '0');
            if (secondsEl) secondsEl.textContent = String(seconds).padStart(2, '0');
        }
        
        update();
        countdownInterval = setInterval(update, 1000);
    }

    // Initialize countdown when promotion page is loaded
    document.addEventListener('pageLoaded', function(event) {
        if (event.detail.pageName === 'promotion') {
            setTimeout(updateCountdown, 100);
        }
    });

    // Also check on DOMContentLoaded
    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('countdown-days')) {
            updateCountdown();
        }
    });
})();
