<?php
/**
 * Cookie Consent Component
 *
 * @package KonDernTang
 * @since 1.0.0
 */

$cookie_consent_enabled = konderntang_get_option( 'cookie_consent_enabled', true );
if ( ! $cookie_consent_enabled ) {
    return;
}

// Get all cookie settings
$cookie_message         = konderntang_get_option( 'cookie_consent_message', esc_html__( 'เราใช้คุกกี้เพื่อปรับปรุงประสบการณ์การใช้งานของคุณ', 'konderntang' ) );
$privacy_page_id        = konderntang_get_option( 'cookie_consent_privacy_page', 0 );
$position               = konderntang_get_option( 'cookie_consent_position', 'bottom' );
$style                  = konderntang_get_option( 'cookie_consent_style', 'bar' );
$bg_color               = konderntang_get_option( 'cookie_consent_bg_color', '#ffffff' );
$text_color             = konderntang_get_option( 'cookie_consent_text_color', '#374151' );
$button_bg              = konderntang_get_option( 'cookie_consent_button_bg', '#3b82f6' );
$button_text            = konderntang_get_option( 'cookie_consent_button_text', '#ffffff' );
$accept_text            = konderntang_get_option( 'cookie_consent_accept_text', esc_html__( 'ยอมรับทั้งหมด', 'konderntang' ) );
$decline_text           = konderntang_get_option( 'cookie_consent_decline_text', esc_html__( 'ปฏิเสธทั้งหมด', 'konderntang' ) );
$settings_text          = konderntang_get_option( 'cookie_consent_settings_text', esc_html__( 'ตั้งค่า', 'konderntang' ) );
$show_decline           = konderntang_get_option( 'cookie_consent_show_decline', true );
$auto_hide              = konderntang_get_option( 'cookie_consent_auto_hide', false );
$auto_hide_delay        = konderntang_get_option( 'cookie_consent_auto_hide_delay', 10 );

// Cookie categories
$show_necessary         = true; // Always true
$show_analytics         = konderntang_get_option( 'cookie_consent_analytics', true );
$show_marketing         = konderntang_get_option( 'cookie_consent_marketing', true );
$show_functional        = konderntang_get_option( 'cookie_consent_functional', true );
$necessary_desc         = konderntang_get_option( 'cookie_consent_necessary_desc', esc_html__( 'คุกกี้ที่จำเป็นสำหรับการทำงานพื้นฐานของเว็บไซต์', 'konderntang' ) );
$analytics_desc         = konderntang_get_option( 'cookie_consent_analytics_desc', esc_html__( 'คุกกี้สำหรับวิเคราะห์การใช้งานเว็บไซต์เพื่อปรับปรุงประสบการณ์', 'konderntang' ) );
$marketing_desc         = konderntang_get_option( 'cookie_consent_marketing_desc', esc_html__( 'คุกกี้สำหรับการตลาดและโฆษณาที่ตรงกับความสนใจของคุณ', 'konderntang' ) );
$functional_desc        = konderntang_get_option( 'cookie_consent_functional_desc', esc_html__( 'คุกกี้สำหรับฟังก์ชันเสริมเช่นการแชร์โซเชียลมีเดีย', 'konderntang' ) );

// Privacy page URL
$privacy_url = $privacy_page_id ? get_permalink( $privacy_page_id ) : get_privacy_policy_url();
if ( ! $privacy_url ) {
    $privacy_url = '#';
}

// Position classes
$position_class = ( $position === 'top' ) ? 'top-0' : 'bottom-0';
$translate_class = ( $position === 'top' ) ? '-translate-y-full' : 'translate-y-full';

// Style classes
$style_class = ( $style === 'box' ) ? 'cookie-consent-box' : 'cookie-consent-bar';
?>

<!-- Cookie Consent Banner -->
<div id="cookie-consent" 
     class="fixed left-0 right-0 <?php echo esc_attr( $position_class ); ?> <?php echo esc_attr( $translate_class ); ?> <?php echo esc_attr( $style_class ); ?> border shadow-2xl z-50 transition-transform duration-300"
     style="background-color: <?php echo esc_attr( $bg_color ); ?>; color: <?php echo esc_attr( $text_color ); ?>; border-color: <?php echo esc_attr( $text_color ); ?>33;"
     data-auto-hide="<?php echo esc_attr( $auto_hide ? '1' : '0' ); ?>"
     data-auto-hide-delay="<?php echo esc_attr( $auto_hide_delay ); ?>">
    <div class="container mx-auto px-4 py-4">
        <div class="flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex-1">
                <p class="text-sm">
                    <?php echo esc_html( $cookie_message ); ?>
                    <?php if ( $privacy_url && $privacy_url !== '#' ) : ?>
                        <a href="<?php echo esc_url( $privacy_url ); ?>" class="hover:underline font-medium" style="color: <?php echo esc_attr( $button_bg ); ?>;">
                            <?php esc_html_e( 'อ่านเพิ่มเติม', 'konderntang' ); ?>
                        </a>
                    <?php endif; ?>
                </p>
            </div>
            <div class="flex gap-3 flex-wrap">
                <?php if ( $show_decline ) : ?>
                    <button onclick="declineCookies()" class="px-4 py-2 text-sm transition border rounded-lg hover:opacity-80" style="color: <?php echo esc_attr( $text_color ); ?>; border-color: <?php echo esc_attr( $text_color ); ?>;">
                        <?php echo esc_html( $decline_text ); ?>
                    </button>
                <?php endif; ?>
                <button onclick="openCookiePreferences()" class="px-4 py-2 text-sm transition hover:opacity-80" style="color: <?php echo esc_attr( $text_color ); ?>;">
                    <?php echo esc_html( $settings_text ); ?>
                </button>
                <button onclick="acceptCookies()" class="px-6 py-2 rounded-lg transition font-semibold text-sm hover:opacity-90" style="background-color: <?php echo esc_attr( $button_bg ); ?>; color: <?php echo esc_attr( $button_text ); ?>;">
                    <?php echo esc_html( $accept_text ); ?>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Cookie Preferences Modal -->
<div id="cookie-preferences-modal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="font-heading font-bold text-2xl text-dark flex items-center gap-2">
                    <i class="ph ph-sliders text-primary"></i>
                    <?php esc_html_e( 'ตั้งค่าคุกกี้', 'konderntang' ); ?>
                </h2>
                <button onclick="closeCookiePreferences()" class="text-gray-400 hover:text-gray-600 transition">
                    <i class="ph ph-x text-2xl"></i>
                </button>
            </div>
        </div>
        
        <div class="p-6 space-y-6">
            <p class="text-gray-600 text-sm leading-relaxed">
                <?php esc_html_e( 'คุณสามารถเลือกประเภทคุกกี้ที่ต้องการอนุญาตได้ คุกกี้ที่จำเป็นจะต้องเปิดใช้งานเสมอเพื่อให้เว็บไซต์ทำงานได้', 'konderntang' ); ?>
            </p>
            
            <!-- Necessary Cookies (Always ON) -->
            <?php if ( $show_necessary ) : ?>
            <div class="cookie-category">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i class="ph ph-shield-check text-green-600"></i>
                        <?php esc_html_e( 'คุกกี้ที่จำเป็น', 'konderntang' ); ?>
                    </h3>
                    <label class="relative inline-block w-12 h-6">
                        <input type="checkbox" id="cookie-necessary" checked disabled class="sr-only" />
                        <span class="block w-full h-full bg-green-600 rounded-full cursor-not-allowed opacity-50"></span>
                        <span class="absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform transform translate-x-6"></span>
                    </label>
                </div>
                <p class="text-sm text-gray-600"><?php echo esc_html( $necessary_desc ); ?></p>
            </div>
            <?php endif; ?>
            
            <!-- Analytics Cookies -->
            <?php if ( $show_analytics ) : ?>
            <div class="cookie-category">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i class="ph ph-chart-line text-blue-600"></i>
                        <?php esc_html_e( 'คุกกี้วิเคราะห์', 'konderntang' ); ?>
                    </h3>
                    <label class="relative inline-block w-12 h-6 cursor-pointer">
                        <input type="checkbox" id="cookie-analytics" class="sr-only cookie-toggle" />
                        <span class="block w-full h-full bg-gray-300 rounded-full transition-colors toggle-bg"></span>
                        <span class="absolute bg-white w-4 h-4 rounded-full toggle-dot" style="left: 4px; top: 50%; transform: translateY(-50%);"></span>
                    </label>
                </div>
                <p class="text-sm text-gray-600"><?php echo esc_html( $analytics_desc ); ?></p>
            </div>
            <?php endif; ?>
            
            <!-- Marketing Cookies -->
            <?php if ( $show_marketing ) : ?>
            <div class="cookie-category">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i class="ph ph-megaphone text-orange-600"></i>
                        <?php esc_html_e( 'คุกกี้การตลาด', 'konderntang' ); ?>
                    </h3>
                    <label class="relative inline-block w-12 h-6 cursor-pointer">
                        <input type="checkbox" id="cookie-marketing" class="sr-only cookie-toggle" />
                        <span class="block w-full h-full bg-gray-300 rounded-full transition-colors toggle-bg"></span>
                        <span class="absolute bg-white w-4 h-4 rounded-full toggle-dot" style="left: 4px; top: 50%; transform: translateY(-50%);"></span>
                    </label>
                </div>
                <p class="text-sm text-gray-600"><?php echo esc_html( $marketing_desc ); ?></p>
            </div>
            <?php endif; ?>
            
            <!-- Functional Cookies -->
            <?php if ( $show_functional ) : ?>
            <div class="cookie-category">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i class="ph ph-plug text-purple-600"></i>
                        <?php esc_html_e( 'คุกกี้ฟังก์ชันเสริม', 'konderntang' ); ?>
                    </h3>
                    <label class="relative inline-block w-12 h-6 cursor-pointer">
                        <input type="checkbox" id="cookie-functional" class="sr-only cookie-toggle" />
                        <span class="block w-full h-full bg-gray-300 rounded-full transition-colors toggle-bg"></span>
                        <span class="absolute bg-white w-4 h-4 rounded-full toggle-dot" style="left: 4px; top: 50%; transform: translateY(-50%);"></span>
                    </label>
                </div>
                <p class="text-sm text-gray-600"><?php echo esc_html( $functional_desc ); ?></p>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="p-6 border-t border-gray-200 flex flex-col sm:flex-row gap-3 justify-end">
            <button onclick="declineCookies()" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                <?php echo esc_html( $decline_text ); ?>
            </button>
            <button onclick="saveCookiePreferences()" class="px-6 py-2 rounded-lg transition font-semibold hover:opacity-90" style="background-color: <?php echo esc_attr( $button_bg ); ?>; color: <?php echo esc_attr( $button_text ); ?>;">
                <?php esc_html_e( 'บันทึกการตั้งค่า', 'konderntang' ); ?>
            </button>
        </div>
    </div>
</div>

<style>
    .cookie-consent-box {
        max-width: 500px;
        margin: 0 auto;
        left: 50%;
        transform: translateX(-50%);
        border-radius: 1rem;
    }
    
    .cookie-consent-box.translate-y-full {
        transform: translateX(-50%) translateY(100%);
    }
    
    .cookie-consent-box:not(.translate-y-full) {
        transform: translateX(-50%) translateY(0);
    }
    
    .cookie-consent-box.-translate-y-full {
        transform: translateX(-50%) translateY(-100%);
    }
    
    .cookie-toggle:checked ~ .toggle-bg {
        background-color: <?php echo esc_attr( $button_bg ); ?> !important;
    }
    
    .cookie-toggle:checked ~ .toggle-dot {
        transform: translate(1.5rem, -50%) !important;
    }
    
    .cookie-category {
        padding: 1rem;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        transition: border-color 0.2s;
    }
    
    .cookie-category:hover {
        border-color: #d1d5db;
    }
</style>
