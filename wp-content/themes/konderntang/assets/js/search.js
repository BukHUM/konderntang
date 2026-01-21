/**
 * Search Functionality
 *
 * @package KonDernTang
 * @since 1.0.0
 */

(function() {
    'use strict';

    function handleSearch(event) {
        event.preventDefault();
        const searchInput = document.getElementById('search-input');
        const query = searchInput ? searchInput.value.trim() : '';
        
        if (query) {
            // Hide suggestions
            const suggestions = document.getElementById('search-suggestions');
            if (suggestions) {
                suggestions.classList.add('hidden');
            }
            
            // Redirect to search results page
            window.location.href = '?s=' + encodeURIComponent(query);
        }
    }

    function setSearchQuery(query) {
        const searchInput = document.getElementById('search-input');
        if (searchInput) {
            searchInput.value = query;
            const suggestions = document.getElementById('search-suggestions');
            if (suggestions) {
                suggestions.classList.add('hidden');
            }
            handleSearch(new Event('submit'));
        }
    }

    // Show/hide search suggestions
    function initSearchSuggestions() {
        const searchInput = document.getElementById('search-input');
        const suggestions = document.getElementById('search-suggestions');
        
        if (searchInput && suggestions) {
            searchInput.addEventListener('focus', function() {
                if (this.value.length === 0) {
                    suggestions.classList.remove('hidden');
                }
            });
            
            searchInput.addEventListener('blur', function() {
                // Delay to allow click on suggestion
                setTimeout(() => {
                    suggestions.classList.add('hidden');
                }, 200);
            });
            
            searchInput.addEventListener('input', function() {
                if (this.value.length > 0) {
                    suggestions.classList.add('hidden');
                } else {
                    suggestions.classList.remove('hidden');
                }
            });
        }
    }

    // Expose functions globally
    window.handleSearch = handleSearch;
    window.setSearchQuery = setSearchQuery;

    // Initialize search suggestions when search page is loaded
    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('search-input')) {
            initSearchSuggestions();
        }
    });

    document.addEventListener('pageLoaded', function(event) {
        if (event.detail.pageName === 'search') {
            setTimeout(initSearchSuggestions, 100);
        }
    });
})();
