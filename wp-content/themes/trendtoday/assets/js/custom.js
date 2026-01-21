/**
 * Custom JavaScript for Trend Today Theme
 * 
 * @package TrendToday
 * @since 1.0.0
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        // Mobile Menu Toggle
        window.toggleMobileMenu = function() {
            const menu = document.getElementById('mobile-menu');
            const button = document.getElementById('mobile-menu-button');
            const icon = document.getElementById('menu-icon');
            
            if (!menu || !button || !icon) return;
            
            const isHidden = menu.classList.contains('hidden');
            
            if (isHidden) {
                menu.classList.remove('hidden');
                button.setAttribute('aria-expanded', 'true');
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            } else {
                menu.classList.add('hidden');
                button.setAttribute('aria-expanded', 'false');
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        };

        // Category Filtering
        $('.category-filter').on('click', function() {
            const category = $(this).data('category');
            
            // Remove active class from all filters
            $('.category-filter').removeClass('active bg-accent text-white').addClass('bg-gray-100 text-gray-700');
            
            // Add active class to clicked filter
            $(this).addClass('active bg-accent text-white').removeClass('bg-gray-100 text-gray-700');
            
            // Filter posts via AJAX
            if (typeof trendtodayAjax !== 'undefined') {
                $.ajax({
                    url: trendtodayAjax.ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'filter_posts',
                        category: category,
                        nonce: trendtodayAjax.nonce
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#news-grid').html(response.data.html);
                        }
                    }
                });
            }
        });

        // Load More Posts
        $('#load-more-btn').on('click', function() {
            const button = $(this);
            const currentPage = parseInt(button.data('page')) || 1;
            const nextPage = currentPage + 1;
            
            button.prop('disabled', true);
            button.html('<i class="fas fa-spinner fa-spin mr-2"></i>กำลังโหลด...');
            
            if (typeof trendtodayAjax !== 'undefined') {
                // Get current query parameters
                const ajaxData = {
                    action: 'load_more_posts',
                    page: nextPage,
                    nonce: trendtodayAjax.nonce
                };
                
                // Check if we're on archive page (category or tag)
                const urlParams = new URLSearchParams(window.location.search);
                const catId = button.data('cat-id') || urlParams.get('cat') || '';
                const tagId = button.data('tag-id') || urlParams.get('tag_id') || '';
                const searchQuery = button.data('search') || urlParams.get('s') || '';
                
                if (catId) {
                    ajaxData.cat_id = catId;
                }
                if (tagId) {
                    ajaxData.tag_id = tagId;
                }
                if (searchQuery) {
                    ajaxData.search = searchQuery;
                }
                
                // Get category from active filter (if exists)
                const activeFilter = $('.category-filter.active');
                if (activeFilter.length && activeFilter.data('category') && activeFilter.data('category') !== 'all') {
                    ajaxData.category = activeFilter.data('category');
                }
                
                $.ajax({
                    url: trendtodayAjax.ajaxurl,
                    type: 'POST',
                    data: ajaxData,
                    success: function(response) {
                        if (response.success) {
                            // Find the grid container (works for both home and archive)
                            let gridContainer = $('#news-grid');
                            if (!gridContainer.length) {
                                // Try to find grid container in archive/search pages
                                gridContainer = $('.grid.grid-cols-1.md\\:grid-cols-2').first();
                            }
                            if (!gridContainer.length) {
                                // For search page, use space-y-6 container
                                gridContainer = $('.space-y-6').first();
                            }
                            
                            if (gridContainer.length) {
                                gridContainer.append(response.data.html);
                            } else {
                                // Fallback: append before the button
                                button.before(response.data.html);
                            }
                            
                            button.data('page', nextPage);
                            
                            if (!response.data.has_more) {
                                button.hide();
                            } else {
                                button.prop('disabled', false);
                                button.html('<span class="relative z-10">โหลดข่าวเพิ่มเติม</span><i class="fas fa-arrow-down ml-2 relative z-10"></i>');
                            }
                        }
                    },
                    error: function() {
                        button.prop('disabled', false);
                        button.html('<span class="relative z-10">โหลดข่าวเพิ่มเติม</span><i class="fas fa-arrow-down ml-2 relative z-10"></i>');
                    }
                });
            }
        });

        // Social Share - Copy Link
        $(document).on('click', '.trendtoday-share-copy_link', function(e) {
            e.preventDefault();
            const button = $(this);
            const postUrl = button.closest('.trendtoday-social-share').data('post-url') || button.data('post-url');
            const url = postUrl || window.location.href;
            
            // Copy to clipboard
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(url).then(function() {
                    showCopyToast();
                }).catch(function() {
                    fallbackCopyTextToClipboard(url);
                });
            } else {
                fallbackCopyTextToClipboard(url);
            }
        });
        
        // Fallback copy function
        function fallbackCopyTextToClipboard(text) {
            const textArea = document.createElement('textarea');
            textArea.value = text;
            textArea.style.position = 'fixed';
            textArea.style.left = '-999999px';
            textArea.style.top = '-999999px';
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            
            try {
                const successful = document.execCommand('copy');
                if (successful) {
                    showCopyToast();
                }
            } catch (err) {
                console.error('Fallback: Could not copy text', err);
            }
            
            document.body.removeChild(textArea);
        }
        
        // Show copy toast notification
        function showCopyToast() {
            // Remove existing toast
            $('.trendtoday-copy-toast').remove();
            
            // Create toast
            const toast = $('<div class="trendtoday-copy-toast"><i class="fas fa-check-circle"></i><span>คัดลอกลิงก์เรียบร้อยแล้ว</span></div>');
            $('body').append(toast);
            
            // Show toast
            setTimeout(function() {
                toast.addClass('show');
            }, 100);
            
            // Hide toast after 3 seconds
            setTimeout(function() {
                toast.removeClass('show');
                setTimeout(function() {
                    toast.remove();
                }, 300);
            }, 3000);
        }
        
        // Floating Share Buttons Toggle
        $(document).on('click', '.trendtoday-floating-share-toggle', function(e) {
            e.preventDefault();
            const floatingShare = $(this).closest('.trendtoday-floating-share');
            floatingShare.toggleClass('active');
        });
        
        // Close floating share when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.trendtoday-floating-share').length) {
                $('.trendtoday-floating-share').removeClass('active');
            }
        });
        
        // Handle copy link for floating buttons
        $(document).on('click', '.trendtoday-floating-share-btn.trendtoday-share-copy_link', function(e) {
            e.preventDefault();
            const button = $(this);
            const postUrl = button.data('post-url') || window.location.href;
            const url = postUrl || window.location.href;
            
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(url).then(function() {
                    showCopyToast();
                }).catch(function() {
                    fallbackCopyTextToClipboard(url);
                });
            } else {
                fallbackCopyTextToClipboard(url);
            }
        });

        // Back to Top Button
        const backToTopBtn = $('#back-to-top');
        if (backToTopBtn.length) {
            $(window).scroll(function() {
                if ($(this).scrollTop() > 300) {
                    backToTopBtn.removeClass('opacity-0 invisible').addClass('opacity-100 visible');
                } else {
                    backToTopBtn.addClass('opacity-0 invisible').removeClass('opacity-100 visible');
                }
            });
        }

        // Newsletter Form Handler (jQuery fallback for older forms)
        $('form[onsubmit*="handleNewsletterSubmit"]').on('submit', function(e) {
            e.preventDefault();
            const form = $(this);
            const emailInput = form.find('input[type="email"]');
            const email = emailInput.val().trim();
            const button = form.find('button[type="submit"]');
            const originalText = button.html();
            
            if (!email) {
                emailInput.focus();
                return;
            }
            
            button.html('<i class="fas fa-spinner fa-spin"></i>');
            button.prop('disabled', true);
            
            // Simulate API call (replace with actual API endpoint)
            setTimeout(function() {
                // Success state
                button.html('<i class="fas fa-check text-green-500"></i>');
                button.addClass('bg-green-500 hover:bg-green-600');
                form[0].reset();
                
                // Show success message
                const successMsg = $('<div class="mt-2 text-sm text-green-500">ขอบคุณที่สมัครรับข่าวสาร!</div>');
                form.append(successMsg);
                
                setTimeout(function() {
                    button.html(originalText);
                    button.prop('disabled', false);
                    button.removeClass('bg-green-500 hover:bg-green-600');
                    successMsg.fadeOut(300, function() {
                        $(this).remove();
                    });
                }, 3000);
            }, 1500);
        });
    });

    // Footer Toggle Function (for mobile accordion) - Enhanced
    window.toggleFooter = function(button) {
        if (!button) return;
        
        const $button = $(button);
        const $icon = $button.find('i.fa-chevron-down');
        const targetId = $button.attr('aria-controls');
        const $links = targetId ? $('#' + targetId) : $button.next('.footer-links');
        
        if ($links.length) {
            const isHidden = $links.hasClass('hidden') || $button.attr('aria-expanded') === 'false';
            
            if (isHidden) {
                $links.removeClass('hidden').slideDown(300, function() {
                    $(this).attr('aria-hidden', 'false');
                });
                $icon.addClass('rotate-180');
                $button.attr('aria-expanded', 'true');
            } else {
                $links.slideUp(300, function() {
                    $(this).addClass('hidden').attr('aria-hidden', 'true');
                });
                $icon.removeClass('rotate-180');
                $button.attr('aria-expanded', 'false');
            }
        }
    };
    
    // Enhanced Newsletter Form Handler
    window.handleNewsletterSubmit = function(event) {
        event.preventDefault();
        const form = event.target.closest('form');
        if (!form) return;
        
        const emailInput = form.querySelector('input[type="email"]');
        const button = form.querySelector('button[type="submit"]');
        
        if (!emailInput || !button) return;
        
        const email = emailInput.value.trim();
        const originalButtonHTML = button.innerHTML;
        
        if (!email) {
            emailInput.focus();
            return;
        }
        
        // Disable button and show loading state
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        
        // Simulate API call (replace with actual API endpoint)
        setTimeout(function() {
            // Success state
            button.innerHTML = '<i class="fas fa-check text-green-500"></i>';
            button.classList.add('bg-green-500', 'hover:bg-green-600');
            emailInput.value = '';
            
            // Show success message (you can replace with toast notification)
            const successMsg = document.createElement('div');
            successMsg.className = 'mt-2 text-sm text-green-500';
            successMsg.textContent = 'ขอบคุณที่สมัครรับข่าวสาร!';
            form.appendChild(successMsg);
            
            setTimeout(function() {
                button.disabled = false;
                button.innerHTML = originalButtonHTML;
                button.classList.remove('bg-green-500', 'hover:bg-green-600');
                if (successMsg.parentNode) {
                    successMsg.parentNode.removeChild(successMsg);
                }
            }, 3000);
        }, 1500);
    };

    // Hero Slider Functionality
    function initHeroSlider() {
        const sliderContainer = document.querySelector('.hero-slider-container');
        if (!sliderContainer) return;

        const slides = sliderContainer.querySelectorAll('.hero-slide');
        const indicators = sliderContainer.querySelectorAll('.hero-slider-indicator');
        const prevBtn = sliderContainer.querySelector('.hero-slider-prev');
        const nextBtn = sliderContainer.querySelector('.hero-slider-next');
        
        if (slides.length === 0) return;

        let currentSlide = 0;
        let autoplayInterval = null;
        const autoplayDelay = 5000; // 5 seconds

        function showSlide(index) {
            // Remove active class from all slides and indicators
            slides.forEach(slide => slide.classList.remove('active'));
            indicators.forEach(indicator => {
                indicator.classList.remove('active', 'bg-white');
                indicator.classList.add('bg-white/50');
            });

            // Add active class to current slide and indicator
            if (slides[index]) {
                slides[index].classList.add('active');
            }
            if (indicators[index]) {
                indicators[index].classList.add('active', 'bg-white');
                indicators[index].classList.remove('bg-white/50');
            }

            currentSlide = index;
        }

        function nextSlide() {
            const next = (currentSlide + 1) % slides.length;
            showSlide(next);
        }

        function prevSlide() {
            const prev = (currentSlide - 1 + slides.length) % slides.length;
            showSlide(prev);
        }

        function startAutoplay() {
            stopAutoplay();
            autoplayInterval = setInterval(nextSlide, autoplayDelay);
        }

        function stopAutoplay() {
            if (autoplayInterval) {
                clearInterval(autoplayInterval);
                autoplayInterval = null;
            }
        }

        // Navigation button events
        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                nextSlide();
                startAutoplay();
            });
        }

        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                prevSlide();
                startAutoplay();
            });
        }

        // Indicator events
        indicators.forEach((indicator, index) => {
            indicator.addEventListener('click', () => {
                showSlide(index);
                startAutoplay();
            });
        });

        // Pause autoplay on hover
        sliderContainer.addEventListener('mouseenter', stopAutoplay);
        sliderContainer.addEventListener('mouseleave', startAutoplay);

        // Keyboard navigation
        sliderContainer.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft') {
                prevSlide();
                startAutoplay();
            } else if (e.key === 'ArrowRight') {
                nextSlide();
                startAutoplay();
            }
        });

        // Touch/swipe support for mobile
        let touchStartX = 0;
        let touchEndX = 0;

        sliderContainer.addEventListener('touchstart', (e) => {
            touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });

        sliderContainer.addEventListener('touchend', (e) => {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipe();
        }, { passive: true });

        function handleSwipe() {
            const swipeThreshold = 50;
            const diff = touchStartX - touchEndX;

            if (Math.abs(diff) > swipeThreshold) {
                if (diff > 0) {
                    nextSlide();
                } else {
                    prevSlide();
                }
                startAutoplay();
            }
        }

        // Start autoplay
        startAutoplay();
    }

    // Initialize hero slider when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initHeroSlider);
    } else {
        initHeroSlider();
    }
    
    // Initialize Search Functionality
    if (typeof trendtodayAjax !== 'undefined' && trendtodayAjax.search && trendtodayAjax.search.enabled) {
        initSearchFunctionality();
    }
    
    // Search Functionality
    function initSearchFunctionality() {
        if (typeof trendtodayAjax === 'undefined' || !trendtodayAjax.search) {
            return;
        }
        
        const searchConfig = trendtodayAjax.search;
        let searchTimeout = null;
        let currentSearchTerm = '';
        
        // Search Modal Toggle
        $('.trendtoday-search-toggle').on('click', function() {
            $('#trendtoday-search-modal').removeClass('hidden');
            $('.trendtoday-search-input').first().focus();
        });
        
        $(document).on('click', '.trendtoday-search-close, #trendtoday-search-modal', function(e) {
            if (e.target === this || $(e.target).hasClass('trendtoday-search-close')) {
                $('#trendtoday-search-modal').addClass('hidden');
            }
        });
        
        // Prevent modal close when clicking inside
        $(document).on('click', '.trendtoday-search-modal-content', function(e) {
            e.stopPropagation();
        });
        
        // Search Input Handler
        function handleSearchInput(input) {
            const searchTerm = $(input).val().trim();
            
            if (searchTerm === currentSearchTerm) {
                return;
            }
            
            currentSearchTerm = searchTerm;
            
            // Clear previous timeout
            if (searchTimeout) {
                clearTimeout(searchTimeout);
            }
            
            const suggestionsContainer = $(input).closest('.trendtoday-search-container, .trendtoday-search-modal-content').find('.trendtoday-search-suggestions');
            
            if (searchTerm.length < searchConfig.min_length) {
                suggestionsContainer.addClass('hidden').empty();
                return;
            }
            
            // Debounce search
            searchTimeout = setTimeout(function() {
                if (searchConfig.suggestions_enabled) {
                    performSearch(searchTerm, suggestionsContainer);
                }
            }, searchConfig.debounce);
        }
        
        // Perform Search
        function performSearch(term, container) {
            if (typeof trendtodayAjax === 'undefined') {
                return;
            }
            
            container.html('<div class="p-4 text-center text-gray-500"><i class="fas fa-spinner fa-spin"></i> กำลังค้นหา...</div>');
            container.removeClass('hidden');
            
            $.ajax({
                url: trendtodayAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'search_suggestions',
                    search: term,
                    nonce: trendtodayAjax.nonce
                },
                success: function(response) {
                    if (response.success && response.data.suggestions) {
                        renderSuggestions(response.data.suggestions, container, searchConfig);
                    } else {
                        container.html('<div class="p-4 text-center text-gray-500">ไม่พบผลการค้นหา</div>');
                    }
                },
                error: function() {
                    container.html('<div class="p-4 text-center text-red-500">เกิดข้อผิดพลาดในการค้นหา</div>');
                }
            });
        }
        
        // Render Suggestions
        function renderSuggestions(suggestions, container, config) {
            if (suggestions.length === 0) {
                container.html('<div class="p-4 text-center text-gray-500">ไม่พบผลการค้นหา</div>');
                return;
            }
            
            let html = '<div class="divide-y divide-gray-100">';
            
            suggestions.forEach(function(item) {
                html += '<a href="' + item.url + '" class="block p-4 hover:bg-gray-50 transition group">';
                html += '<div class="flex items-start gap-4">';
                
                if (item.image) {
                    html += '<img src="' + item.image + '" alt="' + item.title + '" class="w-16 h-16 object-cover rounded flex-shrink-0" />';
                }
                
                html += '<div class="flex-1 min-w-0">';
                
                if (item.category) {
                    html += '<span class="inline-block text-xs font-bold text-white px-2 py-1 rounded mb-2" style="background-color: ' + (item.category_color || '#3B82F6') + '">' + item.category + '</span>';
                }
                
                html += '<h3 class="font-bold text-gray-900 group-hover:text-accent transition line-clamp-2 mb-1">' + item.title + '</h3>';
                
                if (item.excerpt) {
                    html += '<p class="text-sm text-gray-500 line-clamp-2">' + item.excerpt + '</p>';
                }
                
                if (item.date) {
                    html += '<p class="text-xs text-gray-400 mt-2"><i class="far fa-clock mr-1"></i>' + item.date + '</p>';
                }
                
                html += '</div>';
                html += '</div>';
                html += '</a>';
            });
            
            html += '</div>';
            container.html(html);
        }
        
        // Bind search input events
        $(document).on('input', '.trendtoday-search-input', function() {
            if (searchConfig.live_enabled) {
                handleSearchInput(this);
            }
        });
        
        $(document).on('keydown', '.trendtoday-search-input', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const searchTerm = $(this).val().trim();
                if (searchTerm.length >= searchConfig.min_length) {
                    window.location.href = trendtodayAjax.searchUrl || (window.location.origin + '/?s=') + encodeURIComponent(searchTerm);
                }
            }
        });
        
        // Search Submit Button
        $(document).on('click', '.trendtoday-search-submit', function() {
            const searchInput = $(this).closest('.trendtoday-search-modal-content, .trendtoday-search-container').find('.trendtoday-search-input');
            const searchTerm = searchInput.val().trim();
            if (searchTerm.length >= searchConfig.min_length) {
                window.location.href = trendtodayAjax.searchUrl || (window.location.origin + '/?s=') + encodeURIComponent(searchTerm);
            }
        });
        
        // Close suggestions when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.trendtoday-search-container, .trendtoday-search-modal').length) {
                $('.trendtoday-search-suggestions').addClass('hidden');
            }
        });
    }
    
    // Initialize Table of Contents
    initTableOfContents();

})(jQuery);

// Table of Contents Functionality
function initTableOfContents() {
    const tocContainer = document.querySelector('.trendtoday-toc');
    if (!tocContainer) {
        return;
    }
    
    try {
        const config = JSON.parse(tocContainer.getAttribute('data-toc-config'));
        const headingsSelector = config.headings.split(',').map(h => h.trim()).join(', ');
        
        // Find the main article content area - prioritize .trendtoday-article-content
        let contentArea = document.querySelector('.trendtoday-article-content');
        if (!contentArea) {
            contentArea = document.querySelector('#article-content .prose, .prose[data-toc-content="true"], #article-content');
        }
        if (!contentArea) {
            contentArea = document.querySelector('.prose, .entry-content');
        }
        
        if (!contentArea) {
            return;
        }
        
        // Find all headings ONLY within the main article content
        const allHeadings = Array.from(contentArea.querySelectorAll(headingsSelector));
        
        // Filter out headings from excluded sections
        const headings = allHeadings.filter(heading => {
            // Exclude headings that are inside elements with data-toc-exclude attribute
            const excludedParent = heading.closest('[data-toc-exclude="true"]');
            if (excludedParent) {
                return false;
            }
            
            // Exclude headings from related posts section
            const relatedSection = heading.closest('[class*="mt-16"], [class*="related"], [id*="related"]');
            if (relatedSection && (relatedSection.querySelector('a[href*="post"]') || relatedSection.textContent.includes('เกี่ยวข้อง'))) {
                return false;
            }
            
            // Exclude headings from comments section
            const commentsSection = heading.closest('#comments, .comments-area, .comment-form, [class*="comment"], [id*="comment"]');
            if (commentsSection) {
                return false;
            }
            
            // Exclude headings from widget areas
            const widgetArea = heading.closest('.widget, [class*="widget"], [id*="sidebar"]');
            if (widgetArea) {
                return false;
            }
            
            // Exclude headings from tags section
            const tagsSection = heading.closest('[class*="mt-10"]');
            if (tagsSection && tagsSection.querySelector('a[href*="tag"]')) {
                return false;
            }
            
            // Exclude headings from after-content widget area
            const afterContent = heading.closest('[id*="after-content"], [class*="after-content"]');
            if (afterContent) {
                return false;
            }
            
            // Exclude headings that are outside the main article content
            if (!contentArea.contains(heading)) {
                return false;
            }
            
            // Exclude specific headings by text content
            const headingText = heading.textContent.trim().toLowerCase();
            const excludedTexts = [
                'ข่าวที่เกี่ยวข้อง',
                'related',
                'leave a reply',
                'ความคิดเห็น',
                'comments',
                'tags',
                'tag',
                'related posts',
                'related news',
                'related videos',
                'related galleries',
                'reply',
                'ความคิดเห็น',
                'comment',
                'reply to',
                'cancel reply'
            ];
            
            if (excludedTexts.some(text => headingText.includes(text))) {
                return false;
            }
            
            return true;
        });
        
        // Check minimum headings count
        if (headings.length < config.minHeadings) {
            tocContainer.style.display = 'none';
            const mobileToggle = document.querySelector('.trendtoday-toc-mobile-toggle');
            if (mobileToggle) {
                mobileToggle.style.display = 'none';
            }
            return;
        }
        
        // Generate anchor IDs for headings
        headings.forEach((heading, index) => {
            if (!heading.id) {
                const text = heading.textContent.trim();
                const id = 'toc-' + text.toLowerCase()
                    .replace(/[^\w\s-]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-')
                    .substring(0, 50) + '-' + index;
                heading.id = id;
            }
        });
        
        // Generate TOC structure
        const tocNav = tocContainer.querySelector('.trendtoday-toc-nav');
        const mobileTocNav = document.querySelector('.trendtoday-toc-mobile-nav');
        
        if (tocNav) {
            tocNav.innerHTML = generateTOC(headings, config.style);
        }
        
        if (mobileTocNav) {
            mobileTocNav.innerHTML = generateTOC(headings, config.style);
        }
        
        // Smooth scroll
        if (config.smoothScroll) {
            if (tocNav) {
                tocNav.addEventListener('click', function(e) {
                    const link = e.target.closest('a[href^="#"]');
                    if (link) {
                        e.preventDefault();
                        const targetId = link.getAttribute('href').substring(1);
                        const targetElement = document.getElementById(targetId);
                        if (targetElement) {
                            const offset = 100;
                            const targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset - offset;
                            window.scrollTo({
                                top: targetPosition,
                                behavior: 'smooth'
                            });
                        }
                    }
                });
            }
            
            if (mobileTocNav) {
                mobileTocNav.addEventListener('click', function(e) {
                    const link = e.target.closest('a[href^="#"]');
                    if (link) {
                        e.preventDefault();
                        const targetId = link.getAttribute('href').substring(1);
                        const targetElement = document.getElementById(targetId);
                        if (targetElement) {
                            const offset = 100;
                            const targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset - offset;
                            window.scrollTo({
                                top: targetPosition,
                                behavior: 'smooth'
                            });
                            // Close mobile drawer
                            const drawer = document.querySelector('.trendtoday-toc-mobile-drawer');
                            if (drawer) {
                                const content = drawer.querySelector('.trendtoday-toc-mobile-content');
                                if (content) {
                                    content.classList.remove('trendtoday-toc-mobile-content-open');
                                    setTimeout(() => drawer.classList.add('hidden'), 300);
                                }
                            }
                        }
                    }
                });
            }
        }
        
        // Scroll Spy
        if (config.scrollSpy) {
            let scrollTimeout;
            window.addEventListener('scroll', function() {
                clearTimeout(scrollTimeout);
                scrollTimeout = setTimeout(function() {
                    updateActiveSection(headings, tocNav, mobileTocNav);
                }, 100);
            });
            updateActiveSection(headings, tocNav, mobileTocNav);
        }
        
        // Collapsible toggle
        const toggleBtn = tocContainer.querySelector('.trendtoday-toc-toggle');
        if (toggleBtn) {
            toggleBtn.addEventListener('click', function() {
                const content = tocContainer.querySelector('.trendtoday-toc-content');
                const icon = toggleBtn.querySelector('.trendtoday-toc-icon');
                if (content) {
                    content.classList.toggle('trendtoday-toc-content-collapsed');
                    if (icon) {
                        icon.classList.toggle('fa-chevron-down');
                        icon.classList.toggle('fa-chevron-up');
                    }
                }
            });
        }
        
        // Mobile toggle
        const mobileToggle = document.querySelector('.trendtoday-toc-mobile-toggle');
        const mobileDrawer = document.querySelector('.trendtoday-toc-mobile-drawer');
        const mobileClose = document.querySelector('.trendtoday-toc-mobile-close');
        
        if (mobileToggle && mobileDrawer) {
            mobileToggle.addEventListener('click', function() {
                mobileDrawer.classList.remove('hidden');
                const content = mobileDrawer.querySelector('.trendtoday-toc-mobile-content');
                if (content) {
                    setTimeout(() => content.classList.add('trendtoday-toc-mobile-content-open'), 10);
                }
            });
        }
        
        if (mobileClose && mobileDrawer) {
            mobileClose.addEventListener('click', function() {
                const content = mobileDrawer.querySelector('.trendtoday-toc-mobile-content');
                if (content) {
                    content.classList.remove('trendtoday-toc-mobile-content-open');
                    setTimeout(() => mobileDrawer.classList.add('hidden'), 300);
                }
            });
        }
        
        // Close mobile drawer when clicking outside
        if (mobileDrawer) {
            mobileDrawer.addEventListener('click', function(e) {
                if (e.target === mobileDrawer) {
                    const content = mobileDrawer.querySelector('.trendtoday-toc-mobile-content');
                    if (content) {
                        content.classList.remove('trendtoday-toc-mobile-content-open');
                        setTimeout(() => mobileDrawer.classList.add('hidden'), 300);
                    }
                }
            });
        }
        
        // Auto-collapse on mobile
        if (config.autoCollapseMobile && window.innerWidth < 768) {
            const content = tocContainer.querySelector('.trendtoday-toc-content');
            if (content) {
                content.classList.add('trendtoday-toc-content-collapsed');
            }
        }
        
    } catch (error) {
        console.error('TOC initialization error:', error);
    }
}

// Generate TOC HTML
function generateTOC(headings, style) {
    if (headings.length === 0) {
        return '<p class="text-gray-500 text-sm">ไม่พบหัวข้อ</p>';
    }
    
    let html = '';
    let currentLevel = 0;
    let itemNumber = 0;
    
    headings.forEach((heading, index) => {
        const level = parseInt(heading.tagName.substring(1));
        const id = heading.id || 'toc-' + index;
        const text = heading.textContent.trim();
        
        if (level > currentLevel) {
            // Open new nested list
            for (let i = currentLevel; i < level - 1; i++) {
                html += '<ul class="trendtoday-toc-nested">';
            }
            html += '<ul class="trendtoday-toc-list">';
            currentLevel = level;
        } else if (level < currentLevel) {
            // Close nested lists
            for (let i = currentLevel; i > level; i--) {
                html += '</ul>';
            }
            currentLevel = level;
        }
        
        itemNumber++;
        const number = style === 'numbered' ? itemNumber + '. ' : '';
        const indent = style === 'nested' ? 'trendtoday-toc-item-level-' + level : '';
        
        html += '<li class="trendtoday-toc-item ' + indent + '">';
        html += '<a href="#' + id + '" class="trendtoday-toc-link" data-toc-id="' + id + '">';
        html += number + text;
        html += '</a>';
        html += '</li>';
    });
    
    // Close remaining lists
    for (let i = currentLevel; i > 0; i--) {
        html += '</ul>';
    }
    
    return html;
}

// Update active section in TOC
function updateActiveSection(headings, tocNav, mobileTocNav) {
    if (!tocNav && !mobileTocNav) {
        return;
    }
    
    const scrollPosition = window.pageYOffset + 150;
    let activeId = null;
    
    // Find the current active heading
    for (let i = headings.length - 1; i >= 0; i--) {
        const heading = headings[i];
        const rect = heading.getBoundingClientRect();
        if (rect.top <= 150) {
            activeId = heading.id;
            break;
        }
    }
    
    // Update active state
    [tocNav, mobileTocNav].forEach(nav => {
        if (!nav) return;
        
        nav.querySelectorAll('.trendtoday-toc-link').forEach(link => {
            link.classList.remove('trendtoday-toc-link-active');
        });
        
        if (activeId) {
            const activeLink = nav.querySelector('a[href="#' + activeId + '"]');
            if (activeLink) {
                activeLink.classList.add('trendtoday-toc-link-active');
                // Scroll into view if needed (only for sidebar)
                if (nav.closest('.trendtoday-toc-sidebar')) {
                    activeLink.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
            }
        }
    });
}
