<?php
/**
 * Trend Today Theme Functions
 *
 * @package TrendToday
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Include theme files
 */
require_once get_template_directory() . '/inc/theme-setup.php';
require_once get_template_directory() . '/inc/theme-helpers.php';
require_once get_template_directory() . '/inc/enqueue-scripts.php';
require_once get_template_directory() . '/inc/custom-post-types.php';
require_once get_template_directory() . '/inc/custom-fields.php';
require_once get_template_directory() . '/inc/category-fields.php';
require_once get_template_directory() . '/inc/cpt-helpers.php';
require_once get_template_directory() . '/inc/dynamic-content.php';
require_once get_template_directory() . '/inc/ajax-handlers.php';
require_once get_template_directory() . '/inc/menu-walker.php';
require_once get_template_directory() . '/inc/navigation-functions.php';
require_once get_template_directory() . '/inc/menu-icons.php';
require_once get_template_directory() . '/inc/menu-active-states.php';
require_once get_template_directory() . '/inc/widget-helpers.php';
require_once get_template_directory() . '/inc/widget-styling.php';
require_once get_template_directory() . '/inc/search-functions.php';
require_once get_template_directory() . '/inc/security.php';
require_once get_template_directory() . '/inc/image-optimization.php';
require_once get_template_directory() . '/inc/image-cleanup.php';

// Load widgets (must be loaded before register-widgets.php)
require_once get_template_directory() . '/widgets/class-popular-posts-widget.php';
require_once get_template_directory() . '/widgets/class-recent-posts-widget.php';
require_once get_template_directory() . '/widgets/class-newsletter-widget.php';
require_once get_template_directory() . '/widgets/class-trending-tags-widget.php';

// Register widgets (after widget classes are loaded)
require_once get_template_directory() . '/inc/register-widgets.php';

/**
 * Multisite compatibility: Ensure theme is available in network
 * This function helps theme appear correctly in Multisite network admin
 */
if ( is_multisite() ) {
    /**
     * Allow theme to be network-enabled
     * This ensures theme appears in Network Admin > Themes
     * Note: WP_Theme::get_allowed_on_network() uses this filter
     * 
     * Special handling: On Edit Site > Themes page, we temporarily remove theme
     * from network allowed list so WordPress doesn't filter it out
     */
    add_filter( 'allowed_themes', 'trendtoday_allow_network_theme', 10, 1 );
    function trendtoday_allow_network_theme( $allowed_themes ) {
        // Ensure $allowed_themes is an array
        if ( ! is_array( $allowed_themes ) ) {
            $allowed_themes = array();
        }
        
        $theme_slug = 'trendtoday';
        
        // Check if we're in Edit Site > Themes page
        $is_site_themes_page = false;
        if ( is_admin() ) {
            // Check URL parameter
            if ( isset( $_GET['page'] ) && 'site-themes' === $_GET['page'] ) {
                $is_site_themes_page = true;
            }
            // Also check screen ID as fallback
            $screen = get_current_screen();
            if ( $screen && 'site-themes-network' === $screen->id ) {
                $is_site_themes_page = true;
            }
        }
        
        // Check if theme exists
        $theme = wp_get_theme( $theme_slug );
        if ( $theme->exists() ) {
            if ( $is_site_themes_page ) {
                // On Edit Site > Themes page: Don't add to network allowed
                // This makes WordPress think it's NOT network-enabled
                // So it won't be filtered out by prepare_items() at line 142
                // The theme will still be available via all_themes filter
            } else {
                // On other pages (Network Admin > Themes): Add to network allowed
                $allowed_themes[ $theme_slug ] = true;
            }
        }
        
        return $allowed_themes;
    }
    
    /**
     * Make theme appear in Edit Site > Themes tab
     * WordPress filters out network-enabled themes in prepare_items() at line 142
     * The issue: WordPress unset themes AFTER all_themes filter runs
     * Solution: We need to hook into the list table object directly
     */
    add_action( 'admin_init', 'trendtoday_init_site_themes_fix', 1 );
    function trendtoday_init_site_themes_fix() {
        // Only on site themes page
        if ( ! isset( $_GET['page'] ) || 'site-themes' !== $_GET['page'] ) {
            return;
        }
        
        // Hook into the themes list table after it's created
        add_action( 'load-network-admin_page_site-themes', 'trendtoday_fix_site_themes_list', 999 );
    }
    
    /**
     * Fix the themes list after prepare_items() runs
     * We access the list table object and add our theme back
     */
    function trendtoday_fix_site_themes_list() {
        global $wp_list_table;
        
        if ( ! $wp_list_table || ! is_a( $wp_list_table, 'WP_MS_Themes_List_Table' ) ) {
            return;
        }
        
        // Get the themes array from the list table
        // We need to access the private property, so we use reflection
        $reflection = new ReflectionClass( $wp_list_table );
        
        // Hook into display_rows to add theme before display
        add_filter( 'all_themes', 'trendtoday_add_to_site_themes', 999, 1 );
    }
    
    /**
     * Add theme back to the list
     * This runs with very high priority to ensure it happens after WordPress filters
     */
    function trendtoday_add_to_site_themes( $themes ) {
        // Only on site themes page
        if ( ! isset( $_GET['page'] ) || 'site-themes' !== $_GET['page'] ) {
            return $themes;
        }
        
        $theme_slug = 'trendtoday';
        
        // Always ensure theme is in the list
        // WordPress removes it if network-enabled, so we add it back
        if ( ! isset( $themes[ $theme_slug ] ) ) {
            $theme = wp_get_theme( $theme_slug );
            if ( $theme->exists() ) {
                $themes[ $theme_slug ] = $theme;
            }
        }
        
        return $themes;
    }
    
    /**
     * Override theme's is_allowed() method result for network check
     * This makes WordPress think the theme is NOT network-enabled in site themes context
     * We use a filter on the WP_Theme object's method
     */
    add_filter( 'theme_is_allowed', 'trendtoday_override_network_check', 10, 3 );
    function trendtoday_override_network_check( $allowed, $theme_slug, $context ) {
        // Only for our theme and only when checking for network in site themes context
        if ( 'trendtoday' === $theme_slug && 'network' === $context ) {
            // Check if we're in the site themes admin page
            if ( isset( $_GET['page'] ) && 'site-themes' === $_GET['page'] ) {
                // Return false to make WordPress think it's NOT network-enabled
                // This prevents it from being filtered out
                return false;
            }
        }
        
        return $allowed;
    }
    
    /**
     * Make theme allowed for individual sites
     * This ensures the theme passes the is_allowed('site') check
     */
    add_filter( 'site_allowed_themes', 'trendtoday_allow_for_site', 10, 2 );
    function trendtoday_allow_for_site( $allowed_themes, $blog_id ) {
        $theme_slug = 'trendtoday';
        
        // Ensure $allowed_themes is an array
        if ( ! is_array( $allowed_themes ) ) {
            $allowed_themes = array();
        }
        
        // Check if theme exists
        $theme = wp_get_theme( $theme_slug );
        if ( $theme->exists() ) {
            // Add theme to allowed list for this site
            $allowed_themes[ $theme_slug ] = true;
        }
        
        return $allowed_themes;
    }
}

/**
 * Note: Widget areas are registered in inc/theme-setup.php
 * This file only includes necessary theme files.
 */
