/**
 * News Archive Functions
 *
 * @package KonDernTang
 * @since 1.0.0
 */

(function() {
    'use strict';

    function handleNewsSearch(event) {
        event.preventDefault();
        const queryInput = document.getElementById('news-search-input');
        const query = queryInput ? queryInput.value.trim() : '';
        if (query) {
            // In real app, this would filter/search news
            window.location.href = '?s=' + encodeURIComponent(query);
        }
    }

    function clearNewsFilters() {
        // Reset all filters
        const checkboxes = document.querySelectorAll('input[type="checkbox"][name="category"]');
        checkboxes.forEach(cb => cb.checked = false);
        
        const radios = document.querySelectorAll('input[type="radio"][name="date-filter"]');
        radios.forEach(radio => {
            if (radio.value === 'all') radio.checked = true;
            else radio.checked = false;
        });
        
        const searchInput = document.getElementById('news-search-input');
        if (searchInput) searchInput.value = '';
        
        updateActiveFilters();
    }

    function updateActiveFilters() {
        const activeFilters = document.getElementById('active-filters');
        if (!activeFilters) return;
        
        activeFilters.innerHTML = '';
        const checkedCategories = document.querySelectorAll('input[name="category"]:checked');
        const selectedDate = document.querySelector('input[name="date-filter"]:checked');
        
        checkedCategories.forEach(cb => {
            const label = cb.nextElementSibling;
            if (label) {
                const labelText = label.textContent.trim();
                const span = document.createElement('span');
                span.className = 'bg-primary/10 text-primary px-3 py-1 rounded-full text-sm flex items-center gap-2';
                span.innerHTML = labelText + ' <button onclick="removeFilter(\'' + cb.value + '\')" class="hover:bg-primary/20 rounded-full p-0.5"><i class="ph ph-x text-xs"></i></button>';
                activeFilters.appendChild(span);
            }
        });
        
        if (selectedDate && selectedDate.value !== 'all') {
            const label = selectedDate.nextElementSibling;
            if (label) {
                const labelText = label.textContent.trim();
                const span = document.createElement('span');
                span.className = 'bg-secondary/10 text-secondary px-3 py-1 rounded-full text-sm flex items-center gap-2';
                span.innerHTML = labelText + ' <button onclick="removeDateFilter()" class="hover:bg-secondary/20 rounded-full p-0.5"><i class="ph ph-x text-xs"></i></button>';
                activeFilters.appendChild(span);
            }
        }
    }

    function removeFilter(value) {
        const checkbox = document.querySelector('input[name="category"][value="' + value + '"]');
        if (checkbox) checkbox.checked = false;
        updateActiveFilters();
    }

    function removeDateFilter() {
        const allRadio = document.querySelector('input[name="date-filter"][value="all"]');
        if (allRadio) allRadio.checked = true;
        updateActiveFilters();
    }

    // Initialize news archive filters
    function initNewsArchiveFilters() {
        const filterInputs = document.querySelectorAll('input[type="checkbox"][name="category"], input[type="radio"][name="date-filter"]');
        filterInputs.forEach(input => {
            input.addEventListener('change', function() {
                updateActiveFilters();
            });
        });
    }

    // Expose functions globally
    window.handleNewsSearch = handleNewsSearch;
    window.clearNewsFilters = clearNewsFilters;
    window.removeFilter = removeFilter;
    window.removeDateFilter = removeDateFilter;

    document.addEventListener('pageLoaded', function(event) {
        if (event.detail.pageName === 'news-archive') {
            setTimeout(initNewsArchiveFilters, 100);
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('news-search-input')) {
            initNewsArchiveFilters();
        }
    });
})();
