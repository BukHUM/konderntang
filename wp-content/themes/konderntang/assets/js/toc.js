/**
 * Table of Contents Functionality
 *
 * @package KonDernTang
 * @since 1.0.0
 */

(function() {
    'use strict';

    function initTableOfContents() {
        const tocContainer = document.querySelector('.konderntang-toc');
        if (!tocContainer) {
            return;
        }

        const contentArea = document.querySelector('.entry-content, .post-content, article .content');
        if (!contentArea) {
            return;
        }

        // Extract headings
        const headings = Array.from(contentArea.querySelectorAll('h2, h3, h4'));
        if (headings.length < 2) {
            tocContainer.style.display = 'none';
            return;
        }

        // Add IDs to headings if not present
        headings.forEach((heading, index) => {
            if (!heading.id) {
                const text = heading.textContent.trim();
                heading.id = 'toc-' + text.toLowerCase().replace(/\s+/g, '-').replace(/[^\w-]/g, '') + '-' + index;
            }
        });

        // Build TOC
        const tocNav = tocContainer.querySelector('.konderntang-toc-nav');
        if (!tocNav) {
            return;
        }

        const tocList = document.createElement('ul');
        tocList.className = 'konderntang-toc-list';

        let currentLevel = 0;
        headings.forEach((heading, index) => {
            const level = parseInt(heading.tagName.substring(1));
            const id = heading.id;
            const text = heading.textContent.trim();

            // Close previous levels if needed
            if (currentLevel > 0 && level > currentLevel) {
                const lastItem = tocList.querySelector('li:last-child');
                if (lastItem) {
                    const nestedList = document.createElement('ul');
                    nestedList.className = 'konderntang-toc-list konderntang-toc-list-level-' + level;
                    lastItem.appendChild(nestedList);
                }
            }

            const listItem = document.createElement('li');
            listItem.className = 'konderntang-toc-item konderntang-toc-item-level-' + level;

            const link = document.createElement('a');
            link.href = '#' + id;
            link.className = 'konderntang-toc-link';
            link.textContent = text;

            link.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.getElementById(id);
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    // Update URL without jumping
                    history.pushState(null, null, '#' + id);
                }
            });

            listItem.appendChild(link);

            // Append to appropriate list
            if (level > currentLevel && currentLevel > 0) {
                const nestedList = tocList.querySelector('ul:last-child');
                if (nestedList) {
                    nestedList.appendChild(listItem);
                } else {
                    tocList.appendChild(listItem);
                }
            } else {
                tocList.appendChild(listItem);
            }

            currentLevel = level;
        });

        tocNav.innerHTML = '';
        tocNav.appendChild(tocList);

        // Toggle functionality
        const toggleBtn = tocContainer.querySelector('.konderntang-toc-toggle');
        const tocContent = tocContainer.querySelector('.konderntang-toc-nav');
        if (toggleBtn && tocContent) {
            toggleBtn.addEventListener('click', function() {
                tocContainer.classList.toggle('konderntang-toc-collapsed');
            });
        }

        // Scroll spy
        initScrollSpy(headings, tocContainer);
    }

    function initScrollSpy(headings, tocContainer) {
        const tocLinks = tocContainer.querySelectorAll('.konderntang-toc-link');
        if (tocLinks.length === 0) {
            return;
        }

        function updateActiveLink() {
            let current = '';
            headings.forEach(heading => {
                const rect = heading.getBoundingClientRect();
                if (rect.top <= 100) {
                    current = heading.id;
                }
            });

            tocLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === '#' + current) {
                    link.classList.add('active');
                }
            });
        }

        window.addEventListener('scroll', updateActiveLink);
        updateActiveLink();
    }

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initTableOfContents);
    } else {
        initTableOfContents();
    }
})();
