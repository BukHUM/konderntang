/**
 * Cookie Consent Management
 *
 * @package KonDernTang
 * @since 1.0.0
 */

(function() {
    'use strict';

    let autoHideTimer = null;

    function checkCookieConsent() {
        const consent = localStorage.getItem('cookieConsent');
        if (!consent) {
            // Show cookie banner after 1 second
            setTimeout(() => {
                const banner = document.getElementById('cookie-consent');
                if (banner) {
                    // Remove both possible translate classes
                    banner.classList.remove('translate-y-full');
                    banner.classList.remove('-translate-y-full');
                    
                    // Start auto-hide timer if enabled
                    const autoHide = banner.dataset.autoHide === '1';
                    const autoHideDelay = parseInt(banner.dataset.autoHideDelay) || 10;
                    
                    if (autoHide) {
                        autoHideTimer = setTimeout(() => {
                            // Auto-accept if user doesn't interact
                            acceptCookies();
                        }, autoHideDelay * 1000);
                    }
                }
            }, 1000);
        }
    }

    function acceptCookies() {
        clearAutoHideTimer();
        localStorage.setItem('cookieConsent', 'accepted');
        localStorage.setItem('cookieNecessary', 'true');
        localStorage.setItem('cookieAnalytics', 'true');
        localStorage.setItem('cookieMarketing', 'true');
        localStorage.setItem('cookieFunctional', 'true');
        hideCookieBanner();
        
        // Trigger custom event for other scripts to listen to
        triggerCookieEvent('accepted', {
            necessary: true,
            analytics: true,
            marketing: true,
            functional: true
        });
    }

    function declineCookies() {
        clearAutoHideTimer();
        localStorage.setItem('cookieConsent', 'declined');
        localStorage.setItem('cookieNecessary', 'true');
        localStorage.setItem('cookieAnalytics', 'false');
        localStorage.setItem('cookieMarketing', 'false');
        localStorage.setItem('cookieFunctional', 'false');
        hideCookieBanner();
        
        // Trigger custom event
        triggerCookieEvent('declined', {
            necessary: true,
            analytics: false,
            marketing: false,
            functional: false
        });
    }

    function hideCookieBanner() {
        const banner = document.getElementById('cookie-consent');
        if (banner) {
            // Check if it's top or bottom positioned
            const isTopPosition = banner.classList.contains('top-0');
            
            if (isTopPosition) {
                banner.classList.add('-translate-y-full');
                banner.classList.remove('translate-y-full');
            } else {
                banner.classList.add('translate-y-full');
                banner.classList.remove('-translate-y-full');
            }
        }
        closeCookiePreferences();
    }

    function openCookiePreferences() {
        clearAutoHideTimer();
        const modal = document.getElementById('cookie-preferences-modal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            
            // Load saved preferences
            const analytics = localStorage.getItem('cookieAnalytics') === 'true';
            const marketing = localStorage.getItem('cookieMarketing') === 'true';
            const functional = localStorage.getItem('cookieFunctional') === 'true';
            
            const analyticsEl = document.getElementById('cookie-analytics');
            const marketingEl = document.getElementById('cookie-marketing');
            const functionalEl = document.getElementById('cookie-functional');
            
            if (analyticsEl) analyticsEl.checked = analytics;
            if (marketingEl) marketingEl.checked = marketing;
            if (functionalEl) functionalEl.checked = functional;
        }
    }

    function closeCookiePreferences() {
        const modal = document.getElementById('cookie-preferences-modal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }

    function saveCookiePreferences() {
        clearAutoHideTimer();
        const analyticsEl = document.getElementById('cookie-analytics');
        const marketingEl = document.getElementById('cookie-marketing');
        const functionalEl = document.getElementById('cookie-functional');
        
        const analytics = analyticsEl ? analyticsEl.checked : false;
        const marketing = marketingEl ? marketingEl.checked : false;
        const functional = functionalEl ? functionalEl.checked : false;
        
        localStorage.setItem('cookieConsent', 'custom');
        localStorage.setItem('cookieNecessary', 'true');
        localStorage.setItem('cookieAnalytics', analytics ? 'true' : 'false');
        localStorage.setItem('cookieMarketing', marketing ? 'true' : 'false');
        localStorage.setItem('cookieFunctional', functional ? 'true' : 'false');
        
        hideCookieBanner();
        
        // Trigger custom event
        triggerCookieEvent('custom', {
            necessary: true,
            analytics: analytics,
            marketing: marketing,
            functional: functional
        });
        
        // Show success message
        showNotification('บันทึกการตั้งค่าคุกกี้เรียบร้อยแล้ว');
    }

    function clearAutoHideTimer() {
        if (autoHideTimer) {
            clearTimeout(autoHideTimer);
            autoHideTimer = null;
        }
    }

    function triggerCookieEvent(action, preferences) {
        const event = new CustomEvent('cookieConsentChange', {
            detail: {
                action: action,
                preferences: preferences,
                timestamp: Date.now()
            }
        });
        document.dispatchEvent(event);
        
        // For debugging
        if (typeof console !== 'undefined' && console.log) {
            console.log('[Cookie Consent] Action:', action, 'Preferences:', preferences);
        }
    }

    function showNotification(message) {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = 'fixed top-4 right-4 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg z-[60] transition-opacity duration-300';
        notification.innerHTML = `
            <div class="flex items-center gap-2">
                <i class="ph ph-check-circle"></i>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Fade out and remove after 3 seconds
        setTimeout(() => {
            notification.style.opacity = '0';
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }

    // Public API for checking cookie consent
    window.KonderntangCookieConsent = {
        hasConsent: function(category) {
            const consent = localStorage.getItem('cookieConsent');
            if (!consent) return false;
            
            if (category === 'necessary') return true;
            
            const categoryValue = localStorage.getItem('cookie' + category.charAt(0).toUpperCase() + category.slice(1));
            return categoryValue === 'true';
        },
        
        getPreferences: function() {
            return {
                necessary: true,
                analytics: localStorage.getItem('cookieAnalytics') === 'true',
                marketing: localStorage.getItem('cookieMarketing') === 'true',
                functional: localStorage.getItem('cookieFunctional') === 'true'
            };
        },
        
        resetConsent: function() {
            localStorage.removeItem('cookieConsent');
            localStorage.removeItem('cookieNecessary');
            localStorage.removeItem('cookieAnalytics');
            localStorage.removeItem('cookieMarketing');
            localStorage.removeItem('cookieFunctional');
            location.reload();
        },
        
        showBanner: function() {
            const banner = document.getElementById('cookie-consent');
            if (banner) {
                banner.classList.remove('translate-y-full');
                banner.classList.remove('-translate-y-full');
            }
        },
        
        showPreferences: function() {
            openCookiePreferences();
        }
    };

    // Expose functions globally for inline onclick handlers
    window.acceptCookies = acceptCookies;
    window.declineCookies = declineCookies;
    window.openCookiePreferences = openCookiePreferences;
    window.closeCookiePreferences = closeCookiePreferences;
    window.saveCookiePreferences = saveCookiePreferences;

    // Close modal when clicking outside
    document.addEventListener('click', function(event) {
        const modal = document.getElementById('cookie-preferences-modal');
        if (modal && event.target === modal) {
            closeCookiePreferences();
        }
    });

    // Initialize cookie consent check on page load
    document.addEventListener('DOMContentLoaded', function() {
        checkCookieConsent();
    });
})();
