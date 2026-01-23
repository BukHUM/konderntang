<?php
/**
 * KonDernTang Theme Functions
 *
 * @package KonDernTang
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Theme Version
 */
define('KONDERN_THEME_VERSION', '1.0.1');
define('KONDERN_THEME_DIR', get_template_directory());
define('KONDERN_THEME_URI', get_template_directory_uri());

/**
 * Include theme files
 */
require_once KONDERN_THEME_DIR . '/inc/theme-setup.php';
require_once KONDERN_THEME_DIR . '/inc/component-loader.php';
require_once KONDERN_THEME_DIR . '/inc/theme-helpers.php';
require_once KONDERN_THEME_DIR . '/inc/menu-walker.php';
require_once KONDERN_THEME_DIR . '/inc/menu-custom-fields.php';
require_once KONDERN_THEME_DIR . '/inc/geo-location.php';
require_once KONDERN_THEME_DIR . '/inc/widget-areas.php';
require_once KONDERN_THEME_DIR . '/inc/customizer.php';
require_once KONDERN_THEME_DIR . '/inc/admin-settings.php';
require_once KONDERN_THEME_DIR . '/inc/admin-menu.php';
require_once KONDERN_THEME_DIR . '/inc/admin-columns.php';
require_once KONDERN_THEME_DIR . '/inc/enqueue-scripts.php';
require_once KONDERN_THEME_DIR . '/inc/google-fonts.php';

require_once KONDERN_THEME_DIR . '/inc/widget-styling.php';
require_once KONDERN_THEME_DIR . '/inc/table-of-contents.php';
require_once KONDERN_THEME_DIR . '/inc/custom-post-types.php';
require_once KONDERN_THEME_DIR . '/inc/category-metadata.php';
require_once KONDERN_THEME_DIR . '/inc/cpt-hero-banner.php';
require_once KONDERN_THEME_DIR . '/inc/custom-fields.php';
require_once KONDERN_THEME_DIR . '/inc/ajax-handlers.php';
require_once KONDERN_THEME_DIR . '/inc/user-behavior-tracker.php';
require_once KONDERN_THEME_DIR . '/inc/recommendation-engine.php';
require_once KONDERN_THEME_DIR . '/inc/search-analytics.php';
require_once KONDERN_THEME_DIR . '/inc/google-analytics.php';
require_once KONDERN_THEME_DIR . '/inc/performance.php';
require_once KONDERN_THEME_DIR . '/inc/seo.php';
require_once KONDERN_THEME_DIR . '/inc/security.php';

// Admin Tools (Seeder, Language Tools, etc.)
if (is_admin()) {
    require_once KONDERN_THEME_DIR . '/inc/admin-tools.php';
}

// Load widgets (must be loaded before register-widgets.php)
require_once KONDERN_THEME_DIR . '/widgets/class-recent-posts-widget.php';
require_once KONDERN_THEME_DIR . '/widgets/class-popular-posts-widget.php';
require_once KONDERN_THEME_DIR . '/widgets/class-related-posts-widget.php';
require_once KONDERN_THEME_DIR . '/widgets/class-newsletter-widget.php';
require_once KONDERN_THEME_DIR . '/widgets/class-trending-tags-widget.php';
require_once KONDERN_THEME_DIR . '/widgets/class-recently-viewed-widget.php';
require_once KONDERN_THEME_DIR . '/widgets/class-social-links-widget.php';
require_once KONDERN_THEME_DIR . '/widgets/class-personalized-recommendations-widget.php';

// Register widgets (after widget classes are loaded)
require_once KONDERN_THEME_DIR . '/inc/register-widgets.php';