<?php
/**
 * Security Hardening Functions
 *
 * @package KonDernTang
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Sanitize all user inputs
 * This is a helper function to ensure all inputs are sanitized
 */
function konderntang_sanitize_input($input, $type = 'text')
{
    switch ($type) {
        case 'email':
            return sanitize_email($input);
        case 'url':
            return esc_url_raw($input);
        case 'int':
            return absint($input);
        case 'float':
            return floatval($input);
        case 'textarea':
            return sanitize_textarea_field($input);
        case 'html':
            return wp_kses_post($input);
        default:
            return sanitize_text_field($input);
    }
}

/**
 * Escape all outputs
 * This is a helper function to ensure all outputs are escaped
 */
function konderntang_escape_output($output, $type = 'html')
{
    switch ($type) {
        case 'url':
            return esc_url($output);
        case 'attr':
            return esc_attr($output);
        case 'js':
            return esc_js($output);
        case 'textarea':
            return esc_textarea($output);
        default:
            return esc_html($output);
    }
}

/**
 * Verify nonce for AJAX requests
 */
function konderntang_verify_ajax_nonce()
{
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'konderntang-nonce')) {
        wp_send_json_error(array('message' => esc_html__('Security check failed', 'konderntang')));
        wp_die();
    }
}

/**
 * Check user capabilities
 */
function konderntang_check_capability($capability = 'edit_posts')
{
    if (!current_user_can($capability)) {
        wp_die(esc_html__('You do not have permission to perform this action.', 'konderntang'));
    }
}

/**
 * Remove WordPress version from head
 */
function konderntang_remove_version()
{
    return '';
}
add_filter('the_generator', 'konderntang_remove_version');

/**
 * Disable XML-RPC
 */
add_filter('xmlrpc_enabled', '__return_false');

/**
 * Remove RSD link
 */
remove_action('wp_head', 'rsd_link');

/**
 * Remove WLW manifest link
 */
remove_action('wp_head', 'wlwmanifest_link');

/**
 * Remove shortlink
 */
remove_action('wp_head', 'wp_shortlink_wp_head');

/**
 * Disable file editing in admin
 */
if (!defined('DISALLOW_FILE_EDIT')) {
    define('DISALLOW_FILE_EDIT', true);
}

/**
 * Sanitize file uploads
 */
function konderntang_sanitize_file_upload($file)
{
    // Check file type
    $allowed_types = array('image/jpeg', 'image/png', 'image/gif', 'image/webp');
    if (isset($file['type']) && !in_array($file['type'], $allowed_types)) {
        return false;
    }
    
    // Check file extension
    $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif', 'webp');
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($file_extension, $allowed_extensions)) {
        return false;
    }
    
    return $file;
}
add_filter('wp_handle_upload_prefilter', 'konderntang_sanitize_file_upload');

/**
 * Add security headers
 */
function konderntang_security_headers()
{
    if (!headers_sent()) {
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('X-XSS-Protection: 1; mode=block');
        header('Referrer-Policy: strict-origin-when-cross-origin');
    }
}
add_action('send_headers', 'konderntang_security_headers');

/**
 * Sanitize widget data
 */
function konderntang_sanitize_widget_data($instance, $widget)
{
    // Sanitize all widget fields
    if (is_array($instance)) {
        foreach ($instance as $key => $value) {
            if (is_string($value)) {
                $instance[$key] = sanitize_text_field($value);
            } elseif (is_array($value)) {
                $instance[$key] = array_map('sanitize_text_field', $value);
            }
        }
    }
    
    return $instance;
}
add_filter('widget_update_callback', 'konderntang_sanitize_widget_data', 10, 2);
