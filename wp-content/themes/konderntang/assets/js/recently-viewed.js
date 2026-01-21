/**
 * Recently Viewed Posts Tracking
 *
 * @package KonDernTang
 * @since 1.0.0
 */

(function() {
    'use strict';

    function addToRecentlyViewed(title, url) {
        let viewed = JSON.parse(localStorage.getItem('recentlyViewed') || '[]');
        
        // Remove if already exists
        viewed = viewed.filter(item => item.title !== title);
        
        // Add to beginning
        viewed.unshift({
            title: title,
            url: url || '#',
            timestamp: new Date().toISOString(),
            date: new Date().toLocaleDateString('th-TH', { day: 'numeric', month: 'short', year: 'numeric' })
        });
        
        // Keep only last 5 items
        viewed = viewed.slice(0, 5);
        
        localStorage.setItem('recentlyViewed', JSON.stringify(viewed));
        updateRecentlyViewed();
    }

    function updateRecentlyViewed() {
        const viewed = JSON.parse(localStorage.getItem('recentlyViewed') || '[]');
        
        // Update in widget/list
        const newsList = document.getElementById('recently-viewed-list');
        if (newsList) {
            if (viewed.length === 0) {
                newsList.innerHTML = '<p class="text-sm text-gray-500 text-center py-4">ยังไม่มีประวัติการดู</p>';
            } else {
                newsList.innerHTML = viewed.map(item => `
                    <a href="${item.url}" class="flex gap-3 cursor-pointer group">
                        <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="ph ph-clock-clockwise text-gray-400 text-xl"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h5 class="font-semibold text-sm text-dark group-hover:text-primary leading-tight line-clamp-2">${item.title}</h5>
                            <span class="text-xs text-gray-400 mt-1 block">${item.date}</span>
                        </div>
                    </a>
                `).join('');
            }
        }
    }

    // Track page views
    function trackPageView(pageType, title) {
        let pageViews = JSON.parse(localStorage.getItem('pageViews') || '{}');
        if (!pageViews[pageType]) {
            pageViews[pageType] = [];
        }
        pageViews[pageType].push({
            title: title,
            timestamp: new Date().toISOString()
        });
        localStorage.setItem('pageViews', JSON.stringify(pageViews));
    }

    // Expose functions globally
    window.addToRecentlyViewed = addToRecentlyViewed;
    window.trackPageView = trackPageView;

    // Load recently viewed on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateRecentlyViewed();
        
        // Track current page if single post
        if (document.body.classList.contains('single-post') || document.body.classList.contains('single')) {
            const title = document.querySelector('h1')?.textContent || document.title;
            const url = window.location.href;
            addToRecentlyViewed(title, url);
        }
    });

    // Update when pages are loaded
    document.addEventListener('pageLoaded', function() {
        setTimeout(updateRecentlyViewed, 100);
    });
})();
