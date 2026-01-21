/**
 * AJAX Functionality
 *
 * @package KonDernTang
 * @since 1.0.0
 */

(function() {
    'use strict';

    // Load More Posts
    function initLoadMore() {
        const loadMoreBtn = document.querySelector('.konderntang-load-more');
        if (!loadMoreBtn) {
            return;
        }

        let currentPage = 1;
        let isLoading = false;

        loadMoreBtn.addEventListener('click', function(e) {
            e.preventDefault();

            if (isLoading) {
                return;
            }

            isLoading = true;
            loadMoreBtn.textContent = konderntangData?.loadingText || 'Loading...';
            loadMoreBtn.disabled = true;

            currentPage++;

            const formData = new FormData();
            formData.append('action', 'konderntang_load_more');
            formData.append('nonce', konderntangData.nonce);
            formData.append('page', currentPage);
            formData.append('posts_per_page', loadMoreBtn.dataset.postsPerPage || 10);

            fetch(konderntangData.ajaxUrl, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const container = document.querySelector('.konderntang-posts-container');
                    if (container) {
                        container.insertAdjacentHTML('beforeend', data.data.html);
                    }

                    if (!data.data.has_more) {
                        loadMoreBtn.style.display = 'none';
                    } else {
                        loadMoreBtn.textContent = konderntangData?.loadMoreText || 'Load More';
                        loadMoreBtn.disabled = false;
                    }
                } else {
                    console.error('Error:', data.data.message);
                    loadMoreBtn.textContent = konderntangData?.loadMoreText || 'Load More';
                    loadMoreBtn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                loadMoreBtn.textContent = konderntangData?.loadMoreText || 'Load More';
                loadMoreBtn.disabled = false;
            })
            .finally(() => {
                isLoading = false;
            });
        });
    }

    // Search Autocomplete
    function initSearchAutocomplete() {
        const searchInput = document.querySelector('.konderntang-search-input');
        if (!searchInput) {
            return;
        }

        let timeout;
        const resultsContainer = document.querySelector('.konderntang-search-results');

        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.trim();

            clearTimeout(timeout);

            if (searchTerm.length < 2) {
                if (resultsContainer) {
                    resultsContainer.innerHTML = '';
                    resultsContainer.style.display = 'none';
                }
                return;
            }

            timeout = setTimeout(() => {
                const formData = new FormData();
                formData.append('action', 'konderntang_search_autocomplete');
                formData.append('nonce', konderntangData.nonce);
                formData.append('search', searchTerm);

                fetch(konderntangData.ajaxUrl, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success && resultsContainer) {
                        let html = '';
                        if (data.data.results.length > 0) {
                            data.data.results.forEach(result => {
                                html += `
                                    <a href="${result.url}" class="konderntang-search-result-item">
                                        ${result.image ? `<img src="${result.image}" alt="${result.title}" />` : ''}
                                        <span>${result.title}</span>
                                    </a>
                                `;
                            });
                        } else {
                            html = '<div class="konderntang-search-no-results">No results found</div>';
                        }
                        resultsContainer.innerHTML = html;
                        resultsContainer.style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }, 300);
        });

        // Hide results on click outside
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && resultsContainer && !resultsContainer.contains(e.target)) {
                resultsContainer.style.display = 'none';
            }
        });
    }

    // Category Filter
    function initCategoryFilter() {
        const filterButtons = document.querySelectorAll('.konderntang-category-filter');
        if (filterButtons.length === 0) {
            return;
        }

        filterButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();

                const category = this.dataset.category || 0;
                const container = document.querySelector('.konderntang-posts-container');
                if (!container) {
                    return;
                }

                // Update active state
                filterButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');

                // Show loading
                container.innerHTML = '<div class="konderntang-loading">Loading...</div>';

                const formData = new FormData();
                formData.append('action', 'konderntang_filter_category');
                formData.append('nonce', konderntangData.nonce);
                formData.append('category', category);
                formData.append('page', 1);
                formData.append('posts_per_page', 10);

                fetch(konderntangData.ajaxUrl, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        container.innerHTML = data.data.html;
                    } else {
                        container.innerHTML = '<div class="konderntang-no-posts">No posts found.</div>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    container.innerHTML = '<div class="konderntang-error">Error loading posts.</div>';
                });
            });
        });
    }

    // Track Post Views
    function trackPostViews() {
        if (!document.body.classList.contains('single-post')) {
            return;
        }

        const postId = document.querySelector('[data-post-id]')?.dataset.postId;
        if (!postId) {
            return;
        }

        // Only track once per session
        const viewedKey = `viewed_${postId}`;
        if (sessionStorage.getItem(viewedKey)) {
            return;
        }

        const formData = new FormData();
        formData.append('action', 'konderntang_get_views');
        formData.append('nonce', konderntangData.nonce);
        formData.append('post_id', postId);

        fetch(konderntangData.ajaxUrl, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                sessionStorage.setItem(viewedKey, '1');
            }
        })
        .catch(error => {
            console.error('Error tracking views:', error);
        });
    }

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            initLoadMore();
            initSearchAutocomplete();
            initCategoryFilter();
            trackPostViews();
        });
    } else {
        initLoadMore();
        initSearchAutocomplete();
        initCategoryFilter();
        trackPostViews();
    }
})();
