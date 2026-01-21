<?php
/**
 * Admin Cache Buster Helper
 * 
 * Use this to force reload admin assets after updates
 * 
 * @package KonDernTang
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add cache busting query string to admin assets
 */
function konderntang_admin_cache_buster() {
    // Force reload by adding timestamp
    $cache_buster = time();
    
    // You can also use this in your enqueue functions:
    // wp_enqueue_style('konderntang-admin', ..., array(), $cache_buster);
    
    return $cache_buster;
}
