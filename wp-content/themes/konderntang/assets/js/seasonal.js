/**
 * Seasonal Travel Functions
 *
 * @package KonDernTang
 * @since 1.0.0
 */

(function() {
    'use strict';

    function switchSeason(season) {
        // Hide all season content
        document.querySelectorAll('.season-content').forEach(content => {
            content.classList.add('hidden');
        });
        
        // Remove active class from all tabs
        document.querySelectorAll('.season-tab').forEach(tab => {
            tab.classList.remove('active', 'bg-orange-50', 'bg-blue-50');
            if (tab.id === 'tab-summer') {
                tab.classList.remove('border-orange-500', 'text-orange-500');
                tab.classList.add('border-gray-300', 'text-gray-600');
            } else if (tab.id === 'tab-rainy') {
                tab.classList.remove('border-blue-500', 'text-blue-500');
                tab.classList.add('border-gray-300', 'text-gray-600');
            } else if (tab.id === 'tab-winter') {
                tab.classList.remove('border-blue-300', 'text-blue-600');
                tab.classList.add('border-gray-300', 'text-gray-600');
            }
        });
        
        // Show selected season content
        const selectedContent = document.getElementById('season-' + season);
        if (selectedContent) {
            selectedContent.classList.remove('hidden');
        }
        
        // Add active class to selected tab
        const selectedTab = document.getElementById('tab-' + season);
        if (selectedTab) {
            selectedTab.classList.add('active');
            if (season === 'summer') {
                selectedTab.classList.add('bg-orange-50', 'border-orange-500', 'text-orange-500');
                selectedTab.classList.remove('border-gray-300', 'text-gray-600');
            } else if (season === 'rainy') {
                selectedTab.classList.add('bg-blue-50', 'border-blue-500', 'text-blue-500');
                selectedTab.classList.remove('border-gray-300', 'text-gray-600');
            } else if (season === 'winter') {
                selectedTab.classList.add('bg-blue-50', 'border-blue-300', 'text-blue-600');
                selectedTab.classList.remove('border-gray-300', 'text-gray-600');
            }
        }
    }

    // Expose function globally
    window.switchSeason = switchSeason;
})();
