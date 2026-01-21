/**
 * Geo-Location Detection & Language Switcher
 *
 * @package KonDernTang
 * @since 1.0.0
 */

(function() {
    'use strict';

    // Check if geo data is available
    const geoData = typeof konderntangGeoData !== 'undefined' ? konderntangGeoData : null;
    const COOKIE_NAME = geoData?.cookieName || 'konderntang_lang_preference';
    const COOKIE_EXPIRY = geoData?.cookieExpiry || 30;

    /**
     * Cookie utilities
     */
    const Cookie = {
        set: function(name, value, days) {
            const expires = new Date();
            expires.setTime(expires.getTime() + (days * 24 * 60 * 60 * 1000));
            document.cookie = name + '=' + encodeURIComponent(value) + ';expires=' + expires.toUTCString() + ';path=/;SameSite=Lax';
        },
        get: function(name) {
            const nameEQ = name + '=';
            const ca = document.cookie.split(';');
            for (let i = 0; i < ca.length; i++) {
                let c = ca[i].trim();
                if (c.indexOf(nameEQ) === 0) {
                    return decodeURIComponent(c.substring(nameEQ.length));
                }
            }
            return null;
        },
        delete: function(name) {
            document.cookie = name + '=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/';
        }
    };

    /**
     * Language preference utilities
     */
    const LangPreference = {
        has: function() {
            return Cookie.get(COOKIE_NAME) !== null || localStorage.getItem(COOKIE_NAME) !== null;
        },
        get: function() {
            return Cookie.get(COOKIE_NAME) || localStorage.getItem(COOKIE_NAME);
        },
        set: function(langCode) {
            Cookie.set(COOKIE_NAME, langCode, COOKIE_EXPIRY);
            localStorage.setItem(COOKIE_NAME, langCode);
        },
        clear: function() {
            Cookie.delete(COOKIE_NAME);
            localStorage.removeItem(COOKIE_NAME);
        }
    };

    /**
     * Modal Manager
     */
    const ModalManager = {
        activeModal: null,

        open: function(modalId) {
            const modal = document.getElementById(modalId);
            if (!modal) return;

            this.activeModal = modal;
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';

            // Focus trap
            const focusableElements = modal.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
            if (focusableElements.length > 0) {
                setTimeout(() => focusableElements[0].focus(), 100);
            }

            // Add event listeners
            this.bindCloseEvents(modal);
        },

        close: function() {
            if (!this.activeModal) return;

            this.activeModal.style.display = 'none';
            document.body.style.overflow = '';
            this.unbindCloseEvents();
            this.activeModal = null;
        },

        bindCloseEvents: function(modal) {
            // Close buttons
            modal.querySelectorAll('[data-close-modal]').forEach(el => {
                el.addEventListener('click', this.handleClose);
            });

            // ESC key
            document.addEventListener('keydown', this.handleEsc);
        },

        unbindCloseEvents: function() {
            if (!this.activeModal) return;

            this.activeModal.querySelectorAll('[data-close-modal]').forEach(el => {
                el.removeEventListener('click', this.handleClose);
            });
            document.removeEventListener('keydown', this.handleEsc);
        },

        handleClose: function(e) {
            e.preventDefault();
            ModalManager.close();
        },

        handleEsc: function(e) {
            if (e.key === 'Escape') {
                ModalManager.close();
            }
        }
    };

    /**
     * Dropdown Manager
     */
    const DropdownManager = {
        activeDropdown: null,

        toggle: function(buttonId, dropdownId) {
            const button = document.getElementById(buttonId);
            const dropdown = document.getElementById(dropdownId);
            if (!button || !dropdown) return;

            const isOpen = dropdown.classList.contains('show');

            // Close any open dropdowns first
            this.closeAll();

            if (!isOpen) {
                dropdown.classList.add('show');
                button.setAttribute('aria-expanded', 'true');
                this.activeDropdown = { button, dropdown };

                // Close on outside click
                setTimeout(() => {
                    document.addEventListener('click', this.handleOutsideClick);
                }, 0);
            }
        },

        closeAll: function() {
            document.querySelectorAll('.konderntang-language-dropdown.show').forEach(dd => {
                dd.classList.remove('show');
            });
            document.querySelectorAll('.konderntang-language-button[aria-expanded="true"]').forEach(btn => {
                btn.setAttribute('aria-expanded', 'false');
            });
            document.removeEventListener('click', this.handleOutsideClick);
            this.activeDropdown = null;
        },

        handleOutsideClick: function(e) {
            if (DropdownManager.activeDropdown) {
                const { button, dropdown } = DropdownManager.activeDropdown;
                if (!button.contains(e.target) && !dropdown.contains(e.target)) {
                    DropdownManager.closeAll();
                }
            }
        }
    };

    /**
     * Language Search
     */
    const LanguageSearch = {
        init: function(inputId, listId) {
            const input = document.getElementById(inputId);
            const list = document.getElementById(listId);
            if (!input || !list) return;

            input.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();
                const items = list.querySelectorAll('.konderntang-language-item');

                items.forEach(item => {
                    const name = (item.dataset.name || item.textContent || '').toLowerCase();
                    const lang = (item.dataset.lang || '').toLowerCase();

                    if (name.includes(searchTerm) || lang.includes(searchTerm)) {
                        item.style.display = '';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });

            // Clear search on ESC
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && this.value) {
                    this.value = '';
                    this.dispatchEvent(new Event('input'));
                    e.stopPropagation();
                }
            });
        }
    };

    /**
     * Geo Detection
     */
    const GeoDetection = {
        suggestedLang: null,
        suggestedUrl: null,

        detect: function(callback) {
            if (!geoData || !geoData.enabled) {
                callback(null);
                return;
            }

            const ajaxUrl = typeof konderntangAjax !== 'undefined' ? konderntangAjax.ajaxUrl : (typeof ajaxurl !== 'undefined' ? ajaxurl : null);
            const nonce = typeof konderntangAjax !== 'undefined' ? konderntangAjax.nonce : '';

            if (!ajaxUrl) {
                callback(null);
                return;
            }

            const formData = new FormData();
            formData.append('action', 'konderntang_detect_location');
            formData.append('nonce', nonce);

            fetch(ajaxUrl, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data) {
                    this.suggestedLang = data.data.suggested_lang;
                    this.suggestedUrl = data.data.redirect_url;
                    callback(data.data);
                } else {
                    callback(null);
                }
            })
            .catch(error => {
                console.log('Geo detection error:', error);
                callback(null);
            });
        },

        showSuggestion: function(modalId, data) {
            if (!data || !data.suggested_lang) return;

            const uniqueId = modalId.replace('konderntang-language-modal-', '');
            const suggestionEl = document.getElementById('language-suggestion-' + uniqueId);
            const countryEl = document.getElementById('detected-country-' + uniqueId);
            const useSuggestedBtn = document.getElementById('use-suggested-lang-' + uniqueId);

            if (!suggestionEl) return;

            // Get language name
            const langName = geoData.languages && geoData.languages[data.suggested_lang] 
                ? geoData.languages[data.suggested_lang].name 
                : data.suggested_lang;

            if (countryEl) {
                countryEl.textContent = langName;
            }

            // Set up use suggested button
            if (useSuggestedBtn && data.redirect_url) {
                useSuggestedBtn.href = data.redirect_url;
                useSuggestedBtn.addEventListener('click', function() {
                    LangPreference.set(data.suggested_lang);
                });
            }

            // Highlight suggested language in list
            const listId = 'konderntang-language-list-' + uniqueId;
            const list = document.getElementById(listId);
            if (list) {
                const suggestedItem = list.querySelector('[data-lang="' + data.suggested_lang + '"]');
                if (suggestedItem && !suggestedItem.classList.contains('current')) {
                    suggestedItem.classList.add('suggested');
                }
            }

            // Show suggestion banner
            suggestionEl.style.display = 'flex';
        }
    };

    /**
     * Initialize Language Switcher
     */
    function initLanguageSwitcher() {
        // Find all language buttons
        document.querySelectorAll('[id^="konderntang-language-button-"]').forEach(button => {
            const uniqueId = button.id.replace('konderntang-language-button-', '');
            const modalId = 'konderntang-language-modal-' + uniqueId;
            const dropdownId = 'konderntang-language-dropdown-' + uniqueId;

            const modal = document.getElementById(modalId);
            const dropdown = document.getElementById(dropdownId);

            if (modal) {
                // Modal mode
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    ModalManager.open(modalId);

                    // Detect geo-location if enabled and no preference
                    if (geoData && geoData.enabled && !LangPreference.has()) {
                        GeoDetection.detect(function(data) {
                            if (data) {
                                GeoDetection.showSuggestion(modalId, data);
                            }
                        });
                    }
                });

                // Initialize search if exists
                const searchId = 'konderntang-language-search-' + uniqueId;
                const listId = 'konderntang-language-list-' + uniqueId;
                LanguageSearch.init(searchId, listId);

                // Set language preference on item click
                const list = document.getElementById(listId);
                if (list) {
                    list.querySelectorAll('.konderntang-language-item').forEach(item => {
                        item.addEventListener('click', function() {
                            const langCode = this.dataset.lang;
                            if (langCode) {
                                LangPreference.set(langCode);
                            }
                        });
                    });
                }

            } else if (dropdown) {
                // Dropdown mode
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    DropdownManager.toggle(button.id, dropdownId);
                });

                // Set language preference on item click
                dropdown.querySelectorAll('.konderntang-language-item').forEach(item => {
                    item.addEventListener('click', function() {
                        const langCode = this.dataset.lang;
                        if (langCode) {
                            LangPreference.set(langCode);
                        }
                    });
                });
            }
        });
    }

    /**
     * Handle auto geo-detection modal on page load
     */
    function handleAutoGeoDetection() {
        if (!geoData || !geoData.enabled || !geoData.showModal) return;
        if (geoData.autoRedirect) return; // Server handles redirect
        if (LangPreference.has()) return;
        if (!geoData.shouldDetect) return;

        // Delay to let page load
        setTimeout(function() {
            // Find first modal (desktop preferred)
            const modal = document.getElementById('konderntang-language-modal-desktop') 
                || document.querySelector('[id^="konderntang-language-modal-"]');

            if (modal) {
                GeoDetection.detect(function(data) {
                    if (data && data.suggested_lang && data.suggested_lang !== geoData.currentLang) {
                        ModalManager.open(modal.id);
                        GeoDetection.showSuggestion(modal.id, data);
                    }
                });
            }
        }, 1500);
    }

    /**
     * Initialize on DOM ready
     */
    function init() {
        initLanguageSwitcher();
        handleAutoGeoDetection();
    }

    // Run on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Expose for external use
    window.KonderntangLangSwitcher = {
        ModalManager,
        DropdownManager,
        LangPreference,
        GeoDetection,
        Cookie,
        // Debug helpers
        reset: function() {
            LangPreference.clear();
            console.log('%c[KonDernTang Geo]%c Language preference cleared! Refresh the page to test.', 'background: #4caf50; color: white; padding: 2px 6px; border-radius: 3px;', 'color: #4caf50;');
        },
        testModal: function() {
            const modal = document.getElementById('konderntang-language-modal-desktop') 
                || document.querySelector('[id^="konderntang-language-modal-"]');
            if (modal) {
                ModalManager.open(modal.id);
                console.log('%c[KonDernTang Geo]%c Modal opened for testing', 'background: #2196f3; color: white; padding: 2px 6px; border-radius: 3px;', 'color: #2196f3;');
            } else {
                console.log('%c[KonDernTang Geo]%c Modal not found. Make sure Language Switcher Style is set to "Modal"', 'background: #f44336; color: white; padding: 2px 6px; border-radius: 3px;', 'color: #f44336;');
            }
        },
        status: function() {
            console.log('%c[KonDernTang Geo] Status:', 'background: #9c27b0; color: white; padding: 2px 6px; border-radius: 3px;');
            console.table({
                'Enabled': geoData?.enabled || false,
                'Show Modal': geoData?.showModal || false,
                'Auto Redirect': geoData?.autoRedirect || false,
                'Has Preference': LangPreference.has(),
                'Current Preference': LangPreference.get() || 'none',
                'Current Language': geoData?.currentLang || 'unknown',
                'Is Localhost': geoData?.isLocalhost || false,
                'Should Detect': geoData?.shouldDetect || false
            });
        }
    };

    // Debug mode
    if (typeof konderntangGeoData !== 'undefined' && konderntangGeoData.debug) {
        console.log('%c[KonDernTang Geo]%c Language Switcher initialized', 'background: #673ab7; color: white; padding: 2px 6px; border-radius: 3px;', 'color: #673ab7;');
        console.log('%c[KonDernTang Geo]%c Debug commands available:', 'background: #009688; color: white; padding: 2px 6px; border-radius: 3px;', 'color: #009688;');
        console.log('  • KonderntangLangSwitcher.reset() - Clear language preference');
        console.log('  • KonderntangLangSwitcher.testModal() - Open modal for testing');
        console.log('  • KonderntangLangSwitcher.status() - Show current status');
        
        if (LangPreference.has()) {
            console.log('%c[KonDernTang Geo]%c Has existing preference: ' + LangPreference.get() + ' (use .reset() to clear)', 'background: #ff9800; color: white; padding: 2px 6px; border-radius: 3px;', 'color: #ff9800;');
        }
    }

})();
