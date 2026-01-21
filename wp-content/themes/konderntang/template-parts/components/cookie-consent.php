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
?>

<!-- Cookie Consent Banner -->
<div id="cookie-consent" class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-2xl z-50 translate-y-full transition-transform duration-300">
    <div class="container mx-auto px-4 py-4">
        <div class="flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex-1">
                <p class="text-sm text-gray-700">
                    <?php
                    $cookie_message = konderntang_get_option( 'cookie_consent_message', esc_html__( 'เราใช้คุกกี้เพื่อปรับปรุงประสบการณ์การใช้งานของคุณ', 'konderntang' ) );
                    echo esc_html( $cookie_message );
                    ?>
                    <a href="<?php echo esc_url( get_privacy_policy_url() ); ?>" class="text-primary hover:underline">
                        <?php esc_html_e( 'อ่านเพิ่มเติม', 'konderntang' ); ?>
                    </a>
                </p>
            </div>
            <div class="flex gap-3">
                <button onclick="openCookiePreferences()" class="px-4 py-2 text-sm text-gray-700 hover:text-gray-900 transition">
                    <?php esc_html_e( 'ตั้งค่า', 'konderntang' ); ?>
                </button>
                <button onclick="acceptCookies()" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-blue-600 transition font-semibold text-sm">
                    <?php esc_html_e( 'ยอมรับทั้งหมด', 'konderntang' ); ?>
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
            
            <!-- Cookie categories will be handled by JavaScript -->
        </div>
        
        <div class="p-6 border-t border-gray-200 flex flex-col sm:flex-row gap-3 justify-end">
            <button onclick="declineCookies()" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                <?php esc_html_e( 'ปฏิเสธทั้งหมด', 'konderntang' ); ?>
            </button>
            <button onclick="saveCookiePreferences()" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-blue-600 transition font-semibold">
                <?php esc_html_e( 'บันทึกการตั้งค่า', 'konderntang' ); ?>
            </button>
        </div>
    </div>
</div>
