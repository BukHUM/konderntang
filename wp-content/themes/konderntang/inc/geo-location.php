<?php
/**
 * Geo-Location Detection
 *
 * @package KonDernTang
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Get user's country code from IP address
 *
 * @param string $ip_address IP address (optional, will use current user's IP if not provided).
 * @return string|false Country code (e.g., 'TH', 'US') or false on failure.
 */
function konderntang_get_country_from_ip( $ip_address = null ) {
    // Check if geo-location detection is enabled
    if ( ! konderntang_get_option( 'geo_location_enabled', false ) ) {
        return false;
    }

    // Get IP address if not provided
    if ( empty( $ip_address ) ) {
        $ip_address = konderntang_get_user_ip();
    }

    // Skip localhost/private IPs (but allow debug mode for testing)
    $is_localhost = ! filter_var( $ip_address, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE );
    
    if ( $is_localhost ) {
        // For localhost testing, use default language or a test country
        // Check if debug/test mode is enabled via query parameter or constant
        if ( isset( $_GET['geo_test'] ) && current_user_can( 'manage_options' ) ) {
            // Allow admins to test with ?geo_test=TH (or any country code)
            $test_country = strtoupper( sanitize_text_field( $_GET['geo_test'] ) );
            if ( preg_match( '/^[A-Z]{2}$/', $test_country ) ) {
                return $test_country;
            }
        }
        
        // For localhost, return false (no detection)
        return false;
    }

    // Check cache first
    $cache_key = 'konderntang_geo_' . md5( $ip_address );
    $cached_country = get_transient( $cache_key );
    
    if ( false !== $cached_country ) {
        return $cached_country;
    }

    // Try to get country from API
    $country_code = konderntang_fetch_country_from_api( $ip_address );

    // Cache result for 1 hour
    if ( false !== $country_code ) {
        set_transient( $cache_key, $country_code, HOUR_IN_SECONDS );
    }

    return $country_code;
}

/**
 * Fetch country code from GeoIP API
 *
 * @param string $ip_address IP address.
 * @return string|false Country code or false on failure.
 */
function konderntang_fetch_country_from_api( $ip_address ) {
    // Use ip-api.com (free tier: 45 requests/minute)
    $api_url = 'http://ip-api.com/json/' . urlencode( $ip_address ) . '?fields=status,countryCode';
    
    $response = wp_remote_get(
        $api_url,
        array(
            'timeout' => 3,
            'sslverify' => false,
        )
    );

    if ( is_wp_error( $response ) ) {
        return false;
    }

    $body = wp_remote_retrieve_body( $response );
    $data = json_decode( $body, true );

    if ( isset( $data['status'] ) && 'success' === $data['status'] && isset( $data['countryCode'] ) ) {
        return $data['countryCode'];
    }

    return false;
}

/**
 * Get user's IP address
 *
 * @return string IP address.
 */
function konderntang_get_user_ip() {
    $ip_keys = array(
        'HTTP_CLIENT_IP',
        'HTTP_X_FORWARDED_FOR',
        'HTTP_X_FORWARDED',
        'HTTP_X_CLUSTER_CLIENT_IP',
        'HTTP_FORWARDED_FOR',
        'HTTP_FORWARDED',
        'REMOTE_ADDR',
    );

    foreach ( $ip_keys as $key ) {
        if ( array_key_exists( $key, $_SERVER ) === true ) {
            foreach ( explode( ',', $_SERVER[ $key ] ) as $ip ) {
                $ip = trim( $ip );
                if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) !== false ) {
                    return $ip;
                }
            }
        }
    }

    return isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
}

/**
 * Map country code to language code
 *
 * @param string $country_code Country code (e.g., 'TH', 'US').
 * @return string|false Language code (e.g., 'th', 'en') or false if not mapped.
 */
function konderntang_country_to_language( $country_code ) {
    // Extended country to language mapping
    $country_language_map = array(
        // Southeast Asia
        'TH' => 'th', // Thailand → Thai
        'LA' => 'lo', // Laos → Lao
        'VN' => 'vi', // Vietnam → Vietnamese
        'MM' => 'my', // Myanmar → Burmese
        'KH' => 'km', // Cambodia → Khmer
        'MY' => 'ms', // Malaysia → Malay
        'ID' => 'id', // Indonesia → Indonesian
        'SG' => 'en', // Singapore → English
        'PH' => 'en', // Philippines → English
        
        // East Asia
        'JP' => 'ja', // Japan → Japanese
        'CN' => 'zh', // China → Chinese
        'TW' => 'zh', // Taiwan → Chinese
        'HK' => 'zh', // Hong Kong → Chinese
        'KR' => 'ko', // South Korea → Korean
        
        // South Asia
        'IN' => 'hi', // India → Hindi
        'BD' => 'bn', // Bangladesh → Bengali
        
        // Middle East
        'SA' => 'ar', // Saudi Arabia → Arabic
        'AE' => 'ar', // UAE → Arabic
        'EG' => 'ar', // Egypt → Arabic
        'IL' => 'he', // Israel → Hebrew
        'TR' => 'tr', // Turkey → Turkish
        
        // Europe
        'GB' => 'en', // United Kingdom → English
        'US' => 'en', // United States → English
        'AU' => 'en', // Australia → English
        'CA' => 'en', // Canada → English (or French)
        'NZ' => 'en', // New Zealand → English
        'IE' => 'en', // Ireland → English
        'FR' => 'fr', // France → French
        'DE' => 'de', // Germany → German
        'AT' => 'de', // Austria → German
        'CH' => 'de', // Switzerland → German
        'ES' => 'es', // Spain → Spanish
        'MX' => 'es', // Mexico → Spanish
        'AR' => 'es', // Argentina → Spanish
        'IT' => 'it', // Italy → Italian
        'PT' => 'pt', // Portugal → Portuguese
        'BR' => 'pt', // Brazil → Portuguese
        'RU' => 'ru', // Russia → Russian
        'UA' => 'uk', // Ukraine → Ukrainian
        'PL' => 'pl', // Poland → Polish
        'NL' => 'nl', // Netherlands → Dutch
        'BE' => 'nl', // Belgium → Dutch (or French)
        'SE' => 'sv', // Sweden → Swedish
        'NO' => 'no', // Norway → Norwegian
        'DK' => 'da', // Denmark → Danish
        'FI' => 'fi', // Finland → Finnish
        'GR' => 'el', // Greece → Greek
        'CZ' => 'cs', // Czech Republic → Czech
    );

    // Allow filtering the map
    $country_language_map = apply_filters( 'konderntang_country_language_map', $country_language_map );

    return isset( $country_language_map[ $country_code ] ) ? $country_language_map[ $country_code ] : false;
}

/**
 * Check if geo-location detection should run
 *
 * @return bool True if should run, false otherwise.
 */
function konderntang_should_run_geo_detection() {
    // Check if enabled
    if ( ! konderntang_get_option( 'geo_location_enabled', false ) ) {
        return false;
    }

    // Check if already has language preference cookie
    if ( isset( $_COOKIE['konderntang_lang_preference'] ) ) {
        return false;
    }

    // Check if Polylang is active
    if ( ! function_exists( 'pll_current_language' ) ) {
        return false;
    }

    return true;
}

/**
 * Get suggested language from geo-location
 *
 * @return string|false Language code or false.
 */
function konderntang_get_suggested_language() {
    if ( ! konderntang_should_run_geo_detection() ) {
        return false;
    }

    $country_code = konderntang_get_country_from_ip();
    
    if ( false === $country_code ) {
        // Use default language if API fails
        $default_lang = konderntang_get_option( 'geo_location_default_lang', '' );
        return ! empty( $default_lang ) ? $default_lang : false;
    }

    $language_code = konderntang_country_to_language( $country_code );
    
    if ( false === $language_code ) {
        // Use default language if country not mapped
        $default_lang = konderntang_get_option( 'geo_location_default_lang', '' );
        return ! empty( $default_lang ) ? $default_lang : false;
    }

    // Check if language is available in Polylang
    if ( function_exists( 'pll_the_languages' ) ) {
        $languages = pll_the_languages( array( 'raw' => 1 ) );
        foreach ( $languages as $lang ) {
            if ( $lang['slug'] === $language_code ) {
                return $language_code;
            }
        }
    }

    // Fallback to default language
    $default_lang = konderntang_get_option( 'geo_location_default_lang', '' );
    return ! empty( $default_lang ) ? $default_lang : false;
}

/**
 * Set language preference cookie
 *
 * @param string $lang_code Language code.
 * @param int    $expiry    Cookie expiry in days (default: 30).
 */
function konderntang_set_language_preference( $lang_code, $expiry = 30 ) {
    $expiry_time = time() + ( $expiry * DAY_IN_SECONDS );
    setcookie( 'konderntang_lang_preference', $lang_code, $expiry_time, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true );
    $_COOKIE['konderntang_lang_preference'] = $lang_code;
}

/**
 * Get language preference from cookie
 *
 * @return string|false Language code or false if not set.
 */
function konderntang_get_language_preference() {
    return isset( $_COOKIE['konderntang_lang_preference'] ) ? sanitize_text_field( $_COOKIE['konderntang_lang_preference'] ) : false;
}

/**
 * Clear language preference cookie
 */
function konderntang_clear_language_preference() {
    setcookie( 'konderntang_lang_preference', '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true );
    unset( $_COOKIE['konderntang_lang_preference'] );
}

/**
 * Get URL for a specific language
 *
 * @param string $lang_code Language code.
 * @return string|false URL or false if not available.
 */
function konderntang_get_language_url( $lang_code ) {
    if ( ! function_exists( 'pll_the_languages' ) ) {
        return false;
    }

    $languages = pll_the_languages( array( 'raw' => 1 ) );
    
    foreach ( $languages as $lang ) {
        if ( $lang['slug'] === $lang_code ) {
            return $lang['url'];
        }
    }

    return false;
}

/**
 * Handle geo-location redirect on init
 */
function konderntang_handle_geo_redirect() {
    // Only run on frontend, not admin
    if ( is_admin() ) {
        return;
    }

    // Check if auto-redirect is enabled
    if ( ! konderntang_get_option( 'geo_location_auto_redirect', false ) ) {
        return;
    }

    // Check if should run geo detection
    if ( ! konderntang_should_run_geo_detection() ) {
        return;
    }

    // Get suggested language
    $suggested_lang = konderntang_get_suggested_language();
    
    if ( false === $suggested_lang ) {
        return;
    }

    // Get current language
    if ( ! function_exists( 'pll_current_language' ) ) {
        return;
    }
    
    $current_lang = pll_current_language();
    
    // If suggested language is different from current, redirect
    if ( $suggested_lang !== $current_lang ) {
        $redirect_url = konderntang_get_language_url( $suggested_lang );
        
        if ( $redirect_url ) {
            // Set preference cookie before redirect
            konderntang_set_language_preference( $suggested_lang );
            
            // Redirect to suggested language
            wp_safe_redirect( $redirect_url );
            exit;
        }
    }
}
add_action( 'template_redirect', 'konderntang_handle_geo_redirect', 1 );

/**
 * Output geo-location data for JavaScript
 */
function konderntang_geo_location_js_data() {
    // Only run on frontend
    if ( is_admin() ) {
        return;
    }

    // Check if geo-location is enabled
    if ( ! konderntang_get_option( 'geo_location_enabled', false ) ) {
        return;
    }

    // Check if Polylang is active
    if ( ! function_exists( 'pll_the_languages' ) ) {
        return;
    }

    $show_modal = konderntang_get_option( 'geo_location_show_modal', true );
    $auto_redirect = konderntang_get_option( 'geo_location_auto_redirect', false );
    $has_preference = konderntang_get_language_preference() !== false;
    $should_detect = konderntang_should_run_geo_detection();
    
    // Get available languages
    $languages = array();
    $pll_languages = pll_the_languages( array( 'raw' => 1 ) );
    foreach ( $pll_languages as $lang ) {
        $languages[ $lang['slug'] ] = array(
            'name' => $lang['name'],
            'url'  => $lang['url'],
            'flag' => isset( $lang['flag'] ) ? $lang['flag'] : '',
        );
    }

    $current_lang = function_exists( 'pll_current_language' ) ? pll_current_language() : '';
    
    // Check if localhost
    $user_ip = konderntang_get_user_ip();
    $is_localhost = ! filter_var( $user_ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE );
    
    // Debug mode for admins
    $debug_mode = current_user_can( 'manage_options' ) && ( defined( 'WP_DEBUG' ) && WP_DEBUG );
    
    // Output JavaScript data
    ?>
    <script type="text/javascript">
    var konderntangGeoData = {
        enabled: true,
        showModal: <?php echo $show_modal ? 'true' : 'false'; ?>,
        autoRedirect: <?php echo $auto_redirect ? 'true' : 'false'; ?>,
        hasPreference: <?php echo $has_preference ? 'true' : 'false'; ?>,
        shouldDetect: <?php echo $should_detect ? 'true' : 'false'; ?>,
        currentLang: '<?php echo esc_js( $current_lang ); ?>',
        languages: <?php echo wp_json_encode( $languages ); ?>,
        cookieName: 'konderntang_lang_preference',
        cookieExpiry: 30,
        isLocalhost: <?php echo $is_localhost ? 'true' : 'false'; ?>,
        debug: <?php echo $debug_mode ? 'true' : 'false'; ?>
    };
    <?php if ( $debug_mode && $is_localhost ) : ?>
    console.log('%c[KonDernTang Geo]%c Localhost detected - Geo detection disabled', 'background: #ff9800; color: white; padding: 2px 6px; border-radius: 3px;', 'color: #ff9800;');
    console.log('%c[KonDernTang Geo]%c To test, add ?geo_test=TH (or any country code) to URL', 'background: #2196f3; color: white; padding: 2px 6px; border-radius: 3px;', 'color: #2196f3;');
    <?php endif; ?>
    </script>
    <?php
}
add_action( 'wp_head', 'konderntang_geo_location_js_data', 5 );

/**
 * AJAX handler for geo-location detection
 */
function konderntang_ajax_detect_location() {
    // Verify nonce
    if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'konderntang_geo_nonce' ) ) {
        wp_send_json_error( array( 'message' => 'Invalid nonce' ) );
    }

    $suggested_lang = konderntang_get_suggested_language();
    
    if ( false === $suggested_lang ) {
        wp_send_json_error( array( 'message' => 'Could not detect location' ) );
    }

    $redirect_url = konderntang_get_language_url( $suggested_lang );

    wp_send_json_success( array(
        'suggested_lang' => $suggested_lang,
        'redirect_url'   => $redirect_url,
    ) );
}
add_action( 'wp_ajax_konderntang_detect_location', 'konderntang_ajax_detect_location' );
add_action( 'wp_ajax_nopriv_konderntang_detect_location', 'konderntang_ajax_detect_location' );

/**
 * AJAX handler for setting language preference
 */
function konderntang_ajax_set_language_preference() {
    // Verify nonce
    if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'konderntang_geo_nonce' ) ) {
        wp_send_json_error( array( 'message' => 'Invalid nonce' ) );
    }

    $lang_code = isset( $_POST['lang_code'] ) ? sanitize_text_field( $_POST['lang_code'] ) : '';
    
    if ( empty( $lang_code ) ) {
        wp_send_json_error( array( 'message' => 'Invalid language code' ) );
    }

    konderntang_set_language_preference( $lang_code );

    wp_send_json_success( array(
        'message' => 'Language preference saved',
        'lang_code' => $lang_code,
    ) );
}
add_action( 'wp_ajax_konderntang_set_lang_preference', 'konderntang_ajax_set_language_preference' );
add_action( 'wp_ajax_nopriv_konderntang_set_lang_preference', 'konderntang_ajax_set_language_preference' );

/**
 * Get flag emoji for language
 *
 * @param string $lang_slug Language slug (e.g., 'th', 'en').
 * @param string $fallback  Fallback value if not found.
 * @return string Flag emoji or fallback.
 */
function konderntang_get_flag_emoji( $lang_slug, $fallback = '🌐' ) {
    $flag_emojis = array(
        'th' => '🇹🇭',
        'en' => '🇺🇸',
        'ja' => '🇯🇵',
        'zh' => '🇨🇳',
        'ko' => '🇰🇷',
        'fr' => '🇫🇷',
        'de' => '🇩🇪',
        'es' => '🇪🇸',
        'it' => '🇮🇹',
        'ru' => '🇷🇺',
        'pt' => '🇵🇹',
        'ar' => '🇸🇦',
        'hi' => '🇮🇳',
        'vi' => '🇻🇳',
        'lo' => '🇱🇦',
        'my' => '🇲🇲',
        'km' => '🇰🇭',
        'ms' => '🇲🇾',
        'id' => '🇮🇩',
        'nl' => '🇳🇱',
        'pl' => '🇵🇱',
        'tr' => '🇹🇷',
        'uk' => '🇺🇦',
        'cs' => '🇨🇿',
        'sv' => '🇸🇪',
        'da' => '🇩🇰',
        'fi' => '🇫🇮',
        'no' => '🇳🇴',
        'el' => '🇬🇷',
        'he' => '🇮🇱',
        'bn' => '🇧🇩',
        'ta' => '🇮🇳',
        'te' => '🇮🇳',
    );

    return isset( $flag_emojis[ $lang_slug ] ) ? $flag_emojis[ $lang_slug ] : $fallback;
}

/**
 * Check API rate limit
 *
 * @return bool True if within rate limit, false if exceeded.
 */
function konderntang_check_geo_rate_limit() {
    $rate_key = 'konderntang_geo_rate_' . date( 'YmdHi' ); // Per minute
    $current_count = (int) get_transient( $rate_key );
    
    // ip-api.com allows 45 requests per minute
    if ( $current_count >= 40 ) {
        return false;
    }
    
    set_transient( $rate_key, $current_count + 1, MINUTE_IN_SECONDS );
    return true;
}

/**
 * Fetch country code with fallback APIs
 *
 * @param string $ip_address IP address.
 * @return string|false Country code or false on failure.
 */
function konderntang_fetch_country_with_fallback( $ip_address ) {
    // Check rate limit
    if ( ! konderntang_check_geo_rate_limit() ) {
        // Log rate limit exceeded
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            error_log( 'KonDernTang Geo: Rate limit exceeded' );
        }
        return false;
    }

    // Primary API: ip-api.com (free, 45 req/min)
    $country = konderntang_fetch_country_from_api( $ip_address );
    
    if ( false !== $country ) {
        return $country;
    }

    // Fallback API: ipapi.co (free, 1000 req/day)
    $country = konderntang_fetch_country_from_ipapi( $ip_address );
    
    return $country;
}

/**
 * Fetch country from ipapi.co (fallback API)
 *
 * @param string $ip_address IP address.
 * @return string|false Country code or false on failure.
 */
function konderntang_fetch_country_from_ipapi( $ip_address ) {
    $api_url = 'https://ipapi.co/' . urlencode( $ip_address ) . '/country/';
    
    $response = wp_remote_get(
        $api_url,
        array(
            'timeout'   => 3,
            'sslverify' => true,
            'headers'   => array(
                'User-Agent' => 'KonDernTang/' . KONDERN_THEME_VERSION,
            ),
        )
    );

    if ( is_wp_error( $response ) ) {
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            error_log( 'KonDernTang Geo ipapi.co error: ' . $response->get_error_message() );
        }
        return false;
    }

    $status_code = wp_remote_retrieve_response_code( $response );
    
    if ( 200 !== $status_code ) {
        return false;
    }

    $country_code = trim( wp_remote_retrieve_body( $response ) );
    
    // Validate country code (should be 2 uppercase letters)
    if ( preg_match( '/^[A-Z]{2}$/', $country_code ) ) {
        return $country_code;
    }

    return false;
}

/**
 * Get all available languages with flags
 *
 * @return array Array of languages with slug, name, url, flag.
 */
function konderntang_get_available_languages() {
    if ( ! function_exists( 'pll_the_languages' ) ) {
        return array();
    }

    $languages = array();
    $pll_languages = pll_the_languages( array( 'raw' => 1 ) );
    
    if ( ! is_array( $pll_languages ) ) {
        return array();
    }

    foreach ( $pll_languages as $lang ) {
        $languages[] = array(
            'slug'       => $lang['slug'],
            'name'       => $lang['name'],
            'url'        => $lang['url'],
            'flag'       => konderntang_get_flag_emoji( $lang['slug'] ),
            'is_current' => ! empty( $lang['current_lang'] ),
        );
    }

    return $languages;
}

/**
 * Debug helper - log geo detection info
 *
 * @param string $message Message to log.
 * @param mixed  $data    Additional data to log.
 */
function konderntang_geo_debug_log( $message, $data = null ) {
    if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
        return;
    }

    $log_message = 'KonDernTang Geo: ' . $message;
    
    if ( null !== $data ) {
        $log_message .= ' - ' . print_r( $data, true );
    }

    error_log( $log_message );
}
