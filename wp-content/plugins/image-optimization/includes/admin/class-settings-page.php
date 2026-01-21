<?php
/**
 * Settings Page
 *
 * @package Image_Optimization
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Settings Page Class
 */
class IO_Settings_Page {
    
    /**
     * Instance
     *
     * @var IO_Settings_Page
     */
    private static $instance = null;
    
    /**
     * Get instance
     *
     * @return IO_Settings_Page
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        add_action( 'admin_init', array( $this, 'save_settings' ) );
        add_action( 'admin_init', array( $this, 'handle_migration' ) );
        add_action( 'admin_init', array( $this, 'handle_clear_cache' ) );
    }
    
    /**
     * Handle cache clear request
     */
    public function handle_clear_cache() {
        if ( ! isset( $_GET['clear_update_cache'] ) || '1' !== $_GET['clear_update_cache'] ) {
            return;
        }
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( 'You do not have sufficient permissions to access this page.', 'image-optimization' ) );
        }
        
        check_admin_referer( 'io_clear_cache_nonce', '_wpnonce' );
        
        // Clear update cache
        io_clear_update_cache();
        
        add_settings_error(
            'io_settings',
            'io_cache_cleared',
            __( 'Update cache has been cleared successfully.', 'image-optimization' ),
            'success'
        );
        
        // Redirect to remove query parameter
        wp_safe_redirect( admin_url( 'admin.php?page=image-optimization' ) );
        exit;
    }
    
    /**
     * Handle settings migration
     */
    public function handle_migration() {
        if ( ! isset( $_GET['migrate'] ) || '1' !== $_GET['migrate'] ) {
            return;
        }
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( 'You do not have sufficient permissions to access this page.', 'image-optimization' ) );
        }
        
        check_admin_referer( 'io_settings_nonce', '_wpnonce' );
        
        require_once IO_PLUGIN_DIR . 'includes/helpers/class-settings-migration.php';
        $migration = IO_Settings_Migration::get_instance();
        $result = $migration->migrate_from_theme();
        
        if ( $result['success'] ) {
            add_settings_error(
                'io_settings',
                'io_settings_migrated',
                $result['message'],
                'success'
            );
        } else {
            add_settings_error(
                'io_settings',
                'io_settings_migration_failed',
                $result['message'],
                'error'
            );
        }
        
        // Redirect to remove query parameter
        wp_safe_redirect( admin_url( 'admin.php?page=image-optimization' ) );
        exit;
    }
    
    /**
     * Save settings
     */
    public function save_settings() {
        if ( ! isset( $_POST['io_save_settings'] ) ) {
            return;
        }
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( 'You do not have sufficient permissions to access this page.', 'image-optimization' ) );
        }
        
        check_admin_referer( 'io_settings_nonce' );
        
        // Auto resize
        $auto_resize = isset( $_POST['io_image_auto_resize'] ) ? '1' : '0';
        update_option( 'io_image_auto_resize', $auto_resize );
        
        // Max dimensions
        if ( isset( $_POST['io_image_max_width'] ) ) {
            $max_width = absint( $_POST['io_image_max_width'] );
            if ( $max_width > 0 && $max_width <= 10000 ) {
                update_option( 'io_image_max_width', $max_width );
            }
        }
        
        if ( isset( $_POST['io_image_max_height'] ) ) {
            $max_height = absint( $_POST['io_image_max_height'] );
            if ( $max_height > 0 && $max_height <= 10000 ) {
                update_option( 'io_image_max_height', $max_height );
            }
        }
        
        // Maintain aspect ratio
        $maintain_aspect = isset( $_POST['io_image_maintain_aspect'] ) ? '1' : '0';
        update_option( 'io_image_maintain_aspect', $maintain_aspect );
        
        // JPEG Quality
        if ( isset( $_POST['io_image_jpeg_quality'] ) ) {
            $jpeg_quality = absint( $_POST['io_image_jpeg_quality'] );
            if ( $jpeg_quality >= 0 && $jpeg_quality <= 100 ) {
                update_option( 'io_image_jpeg_quality', $jpeg_quality );
            }
        }
        
        // Max file size before resize
        if ( isset( $_POST['io_image_max_file_size'] ) ) {
            $max_file_size = absint( $_POST['io_image_max_file_size'] );
            if ( $max_file_size >= 0 && $max_file_size <= 100 ) {
                update_option( 'io_image_max_file_size', $max_file_size );
            }
        }
        
        // WebP settings
        $webp_enabled = isset( $_POST['io_image_webp_enabled'] ) ? '1' : '0';
        update_option( 'io_image_webp_enabled', $webp_enabled );
        
        if ( isset( $_POST['io_image_webp_quality'] ) ) {
            $webp_quality = absint( $_POST['io_image_webp_quality'] );
            if ( $webp_quality >= 0 && $webp_quality <= 100 ) {
                update_option( 'io_image_webp_quality', $webp_quality );
            }
        }
        
        // Strip EXIF
        $strip_exif = isset( $_POST['io_image_strip_exif'] ) ? '1' : '0';
        update_option( 'io_image_strip_exif', $strip_exif );
        
        add_settings_error(
            'io_settings',
            'io_settings_saved',
            __( 'Settings saved successfully!', 'image-optimization' ),
            'success'
        );
    }
    
    /**
     * Render settings page
     */
    public function render() {
        // Get active tab
        $active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'optimization';
        if ( ! in_array( $active_tab, array( 'optimization', 'cleanup', 'regenerate' ), true ) ) {
            $active_tab = 'optimization';
        }
        
        // Get settings
        $auto_resize = get_option( 'io_image_auto_resize', '1' );
        $max_width = get_option( 'io_image_max_width', 1920 );
        $max_height = get_option( 'io_image_max_height', 1080 );
        $maintain_aspect = get_option( 'io_image_maintain_aspect', '1' );
        $jpeg_quality = get_option( 'io_image_jpeg_quality', 85 );
        $max_file_size = get_option( 'io_image_max_file_size', 0 );
        $webp_enabled = get_option( 'io_image_webp_enabled', '1' );
        $webp_quality = get_option( 'io_image_webp_quality', 85 );
        $strip_exif = get_option( 'io_image_strip_exif', '1' );
        
        // Get image statistics
        $image_optimizer = IO_Image_Optimizer::get_instance();
        $image_stats = $image_optimizer->get_image_stats();
        
        // Check WebP support
        $webp_converter = IO_WebP_Converter::get_instance();
        $webp_supported = $webp_converter->webp_supported();
        
        // Display settings errors
        settings_errors( 'io_settings' );
        
        // Check for false update notifications
        $this->check_false_update_notification();
        
        ?>
        <div class="wrap io-settings-wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            
            <nav class="nav-tab-wrapper">
                <a href="#" 
                   class="io-main-tab nav-tab <?php echo $active_tab === 'optimization' ? 'nav-tab-active' : ''; ?>" 
                   data-tab="optimization">
                    <span class="dashicons dashicons-images-alt2"></span>
                    <?php _e( 'Image Optimization', 'image-optimization' ); ?>
                </a>
                <a href="#" 
                   class="io-main-tab nav-tab <?php echo $active_tab === 'regenerate' ? 'nav-tab-active' : ''; ?>" 
                   data-tab="regenerate">
                    <span class="dashicons dashicons-update"></span>
                    <?php _e( 'Regenerate Images', 'image-optimization' ); ?>
                </a>
                <a href="#" 
                   class="io-main-tab nav-tab <?php echo $active_tab === 'cleanup' ? 'nav-tab-active' : ''; ?>" 
                   data-tab="cleanup">
                    <span class="dashicons dashicons-trash"></span>
                    <?php _e( 'Image Cleanup', 'image-optimization' ); ?>
                </a>
            </nav>
            
            <!-- Optimization Tab Content -->
            <div id="io-tab-optimization" class="io-main-tab-content" style="<?php echo $active_tab === 'optimization' ? '' : 'display: none;'; ?>">
                <form method="post" action="" id="io-settings-form">
                    <?php wp_nonce_field( 'io_settings_nonce' ); ?>
                    <?php $this->render_optimization_tab( $auto_resize, $max_width, $max_height, $maintain_aspect, $jpeg_quality, $max_file_size, $webp_enabled, $webp_quality, $strip_exif, $image_stats, $webp_supported ); ?>
                    <?php submit_button( __( 'Save Settings', 'image-optimization' ), 'primary large', 'io_save_settings', false ); ?>
                </form>
            </div>
            
            <!-- Regenerate Tab Content -->
            <div id="io-tab-regenerate" class="io-main-tab-content" style="<?php echo $active_tab === 'regenerate' ? '' : 'display: none;'; ?>">
                <?php $this->render_regenerate_tab(); ?>
            </div>
            
            <!-- Cleanup Tab Content -->
            <div id="io-tab-cleanup" class="io-main-tab-content" style="<?php echo $active_tab === 'cleanup' ? '' : 'display: none;'; ?>">
                <?php $this->render_cleanup_tab(); ?>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render optimization tab
     */
    private function render_optimization_tab( $auto_resize, $max_width, $max_height, $maintain_aspect, $jpeg_quality, $max_file_size, $webp_enabled, $webp_quality, $strip_exif, $image_stats, $webp_supported ) {
        require_once IO_PLUGIN_DIR . 'includes/admin/views/optimization-tab.php';
    }
    
    /**
     * Render regenerate tab
     */
    private function render_regenerate_tab() {
        require_once IO_PLUGIN_DIR . 'includes/admin/views/regenerate-tab.php';
    }
    
    /**
     * Render cleanup tab
     */
    private function render_cleanup_tab() {
        require_once IO_PLUGIN_DIR . 'includes/admin/views/cleanup-tab.php';
    }
    
    /**
     * Check for false update notifications and display warning
     */
    private function check_false_update_notification() {
        $update_plugins = get_site_transient( 'update_plugins' );
        $plugin_file = IO_PLUGIN_BASENAME;
        
        if ( ! is_object( $update_plugins ) || ! isset( $update_plugins->response ) ) {
            return;
        }
        
        // Check if our plugin is in the update list
        if ( isset( $update_plugins->response[ $plugin_file ] ) ) {
            $update_data = $update_plugins->response[ $plugin_file ];
            
            // Check if it's from WordPress.org (not our plugin)
            if ( isset( $update_data->slug ) && $update_data->slug === 'image-optimization' ) {
                if ( ! isset( $update_data->package ) || strpos( $update_data->package, 'wordpress.org' ) !== false ) {
                    // This is a false positive from WordPress.org
                    $clear_cache_url = wp_nonce_url(
                        add_query_arg( 'clear_update_cache', '1', admin_url( 'admin.php?page=image-optimization' ) ),
                        'io_clear_cache_nonce',
                        '_wpnonce'
                    );
                    ?>
                    <div class="notice notice-warning is-dismissible" style="margin-top: 20px;">
                        <p>
                            <strong><?php _e( 'False Update Notification Detected', 'image-optimization' ); ?></strong>
                            <?php _e( 'WordPress is showing an update notification for a different plugin with the same name. This is a false positive. Click the button below to clear the update cache.', 'image-optimization' ); ?>
                        </p>
                        <p>
                            <a href="<?php echo esc_url( $clear_cache_url ); ?>" class="button button-secondary">
                                <span class="dashicons dashicons-update" style="vertical-align: middle;"></span>
                                <?php _e( 'Clear Update Cache', 'image-optimization' ); ?>
                            </a>
                        </p>
                    </div>
                    <?php
                }
            }
        }
    }
}
