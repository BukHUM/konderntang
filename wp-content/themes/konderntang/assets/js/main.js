/**
 * Main Navigation and Core Functions
 *
 * @package KonDernTang
 * @since 1.0.0
 */

(function () {
    'use strict';

    // Breadcrumb Configuration (from PHP)
    const breadcrumbConfig = window.konderntangData?.breadcrumbConfig || {};

    // Breadcrumb Management
    function updateBreadcrumb(pageName) {
        const breadcrumb = document.getElementById('breadcrumb');
        const breadcrumbCurrent = document.getElementById('breadcrumb-current');
        const breadcrumbSeparator = document.getElementById('breadcrumb-separator');

        if (!breadcrumb || !breadcrumbCurrent) return;

        const config = breadcrumbConfig[pageName];

        if (config && config.show) {
            breadcrumb.classList.remove('hidden');
            // Removed: breadcrumbCurrent.textContent = config.text;
            // Let PHP handle the breadcrumb text (it's already correct)
            if (breadcrumbSeparator) {
                breadcrumbSeparator.classList.remove('hidden');
            }
        } else {
            breadcrumb.classList.add('hidden');
        }
    }

    // Mobile Submenu Toggle
    function toggleMobileSubmenu(submenuId) {
        const submenu = document.getElementById(submenuId);
        const icon = document.getElementById(submenuId + '-icon');

        if (submenu && icon) {
            submenu.classList.toggle('hidden');
            icon.classList.toggle('rotate-180');
        }
    }

    // Expose functions globally
    window.updateBreadcrumb = updateBreadcrumb;
    window.toggleMobileSubmenu = toggleMobileSubmenu;

    // Social Share - Copy Link Handler
    function initShareCopyLink() {
        const copyButtons = document.querySelectorAll('.konderntang-share-copy');

        copyButtons.forEach(function (button) {
            button.addEventListener('click', function (e) {
                e.preventDefault();

                const url = this.getAttribute('data-copy-url');
                if (!url) return;

                // Copy to clipboard
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(url).then(function () {
                        showCopySuccess(button);
                    }).catch(function () {
                        fallbackCopy(url, button);
                    });
                } else {
                    fallbackCopy(url, button);
                }
            });
        });
    }

    // Fallback copy for older browsers
    function fallbackCopy(text, button) {
        const textArea = document.createElement('textarea');
        textArea.value = text;
        textArea.style.position = 'fixed';
        textArea.style.left = '-9999px';
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();

        try {
            document.execCommand('copy');
            showCopySuccess(button);
        } catch (err) {
            console.error('Copy failed:', err);
        }

        document.body.removeChild(textArea);
    }

    // Show copy success feedback
    function showCopySuccess(button) {
        button.classList.add('copied');

        // Store original text if exists
        const textEl = button.querySelector('.share-text');
        const originalText = textEl ? textEl.textContent : '';

        if (textEl) {
            textEl.textContent = window.konderntangData?.copiedText || 'Copied!';
        }

        // Reset after 2 seconds
        setTimeout(function () {
            button.classList.remove('copied');
            if (textEl && originalText) {
                textEl.textContent = originalText;
            }
        }, 2000);
    }

    // Social Share - Open popup window for share links
    function initSharePopup() {
        const shareButtons = document.querySelectorAll('.konderntang-share-button[target="_blank"]');

        shareButtons.forEach(function (button) {
            button.addEventListener('click', function (e) {
                e.preventDefault();

                const url = this.getAttribute('href');
                const width = 600;
                const height = 400;
                const left = (screen.width - width) / 2;
                const top = (screen.height - height) / 2;

                window.open(
                    url,
                    'share',
                    'width=' + width + ',height=' + height + ',left=' + left + ',top=' + top + ',toolbar=0,menubar=0,location=0,status=0'
                );
            });
        });
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function () {
        // Determine current page
        let currentPage = 'home';
        if (document.body.classList.contains('single')) {
            currentPage = 'single';
        } else if (document.body.classList.contains('archive')) {
            currentPage = 'archive';
        } else if (document.body.classList.contains('search')) {
            currentPage = 'search';
        } else if (document.body.classList.contains('error404')) {
            currentPage = '404';
        }

        updateBreadcrumb(currentPage);

        // Initialize social share features
        // Initialize social share features
        initShareCopyLink();
        initSharePopup();

        // Initialize Language Switcher
        initLanguageSwitcher();
    });

    // Language Switcher Logic
    function initLanguageSwitcher() {
        const switcherButtons = document.querySelectorAll('.konderntang-language-button');

        switcherButtons.forEach(button => {
            button.addEventListener('click', function (e) {
                e.stopPropagation();
                const uniqueId = this.id.replace('konderntang-language-button-', '');
                const modalId = 'konderntang-language-modal-' + uniqueId;
                const dropdownId = 'konderntang-language-dropdown-' + uniqueId;

                const modal = document.getElementById(modalId);
                const dropdown = document.getElementById(dropdownId);

                // Handle Modal
                if (modal) {
                    modal.style.display = 'flex';
                    document.body.style.overflow = 'hidden'; // Prevent scrolling
                }

                // Handle Dropdown (Desktop fallback or alternative style)
                if (dropdown) {
                    const isExpanded = this.getAttribute('aria-expanded') === 'true';
                    this.setAttribute('aria-expanded', !isExpanded);
                    dropdown.classList.toggle('show');
                }
            });
        });

        // Close handlers for Modals
        const closeButtons = document.querySelectorAll('[data-close-modal]');
        closeButtons.forEach(button => {
            button.addEventListener('click', function () {
                const modal = this.closest('.konderntang-language-modal');
                if (modal) {
                    modal.style.display = 'none';
                    document.body.style.overflow = '';
                }
            });
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function (e) {
            if (!e.target.closest('.konderntang-language-switcher')) {
                const dropdowns = document.querySelectorAll('.konderntang-language-dropdown.show');
                dropdowns.forEach(d => d.classList.remove('show'));

                const buttons = document.querySelectorAll('.konderntang-language-button[aria-expanded="true"]');
                buttons.forEach(b => b.setAttribute('aria-expanded', 'false'));
            }
        });
    }

})();
