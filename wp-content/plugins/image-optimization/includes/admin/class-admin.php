<?php
/**
 * Admin
 *
 * @package Image_Optimization
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Admin Class
 */
class IO_Admin {
    
    /**
     * Instance
     *
     * @var IO_Admin
     */
    private static $instance = null;
    
    /**
     * Get instance
     *
     * @return IO_Admin
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
        // Load admin menu
        require_once IO_PLUGIN_DIR . 'includes/admin/class-admin-menu.php';
        IO_Admin_Menu::get_instance();
        
        // Enqueue admin assets
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
        
        // Show migration notice if needed
        add_action( 'admin_notices', array( $this, 'show_migration_notice' ) );
    }
    
    /**
     * Show migration notice if theme options exist
     */
    public function show_migration_notice() {
        // Only show on our settings page
        $screen = get_current_screen();
        if ( ! $screen || 'toplevel_page_image-optimization' !== $screen->id ) {
            return;
        }
        
        require_once IO_PLUGIN_DIR . 'includes/helpers/class-settings-migration.php';
        $migration = IO_Settings_Migration::get_instance();
        $status = $migration->get_migration_status();
        
        // Show notice if theme options exist but not migrated
        if ( ! $status['migrated'] && $status['theme_options_exist'] > 0 ) {
            $migrate_url = wp_nonce_url(
                add_query_arg( 'migrate', '1', admin_url( 'admin.php?page=image-optimization' ) ),
                'io_settings_nonce',
                '_wpnonce'
            );
            ?>
            <div class="notice notice-info is-dismissible">
                <p>
                    <strong><?php _e( 'Image Optimization:', 'image-optimization' ); ?></strong>
                    <?php
                    printf(
                        /* translators: %d: Number of theme options found */
                        esc_html__( 'Found %d settings from theme. Would you like to migrate them?', 'image-optimization' ),
                        esc_html( $status['theme_options_exist'] )
                    );
                    ?>
                    <a href="<?php echo esc_url( $migrate_url ); ?>" class="button button-primary" style="margin-left: 10px;">
                        <?php _e( 'Migrate Settings', 'image-optimization' ); ?>
                    </a>
                </p>
            </div>
            <?php
        }
    }
    
    /**
     * Enqueue admin assets
     *
     * @param string $hook Current admin page hook.
     */
    public function enqueue_admin_assets( $hook ) {
        // Only load on our settings page
        if ( 'toplevel_page_image-optimization' !== $hook ) {
            return;
        }
        
        // Enqueue styles
        wp_enqueue_style(
            'io-admin',
            IO_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            IO_VERSION
        );
        
        // Enqueue scripts
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script(
            'io-admin',
            IO_PLUGIN_URL . 'assets/js/admin.js',
            array( 'jquery' ),
            IO_VERSION,
            true
        );
        
        // Localize script
        wp_localize_script( 'io-admin', 'ioAdmin', array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'io_settings_nonce' ),
            'i18n'    => array(
                'scanning'              => __( 'กำลังแสกน...', 'image-optimization' ),
                'scanComplete'          => __( 'แสกนเสร็จสิ้น', 'image-optimization' ),
                'scanError'             => __( 'เกิดข้อผิดพลาดในการแสกน', 'image-optimization' ),
                'noFiles'               => __( 'ไม่มีไฟล์', 'image-optimization' ),
                'noFilesSelected'       => __( 'กรุณาเลือกไฟล์ที่ต้องการลบ', 'image-optimization' ),
                'confirmDeleteSelected' => __( 'คุณแน่ใจหรือไม่ว่าต้องการลบไฟล์ที่เลือก? การลบไม่สามารถยกเลิกได้', 'image-optimization' ),
                'confirmDeleteAll'      => __( 'คุณแน่ใจหรือไม่ว่าต้องการลบไฟล์ทั้งหมด? การลบไม่สามารถยกเลิกได้', 'image-optimization' ),
                'deleting'              => __( 'กำลังลบ...', 'image-optimization' ),
                'deleteComplete'        => __( 'ลบเสร็จสิ้น', 'image-optimization' ),
                'deleteError'           => __( 'เกิดข้อผิดพลาดในการลบ', 'image-optimization' ),
                'reportFeature'         => __( 'ฟีเจอร์นี้จะเพิ่มในอนาคต', 'image-optimization' ),
                'scanTimeout'           => __( 'การแสกนใช้เวลานานเกินไป กรุณาลองใหม่อีกครั้ง', 'image-optimization' ),
                'noFailedFiles'        => __( 'ไม่มีไฟล์ที่ลบไม่สำเร็จ', 'image-optimization' ),
                'regenerating'          => __( 'กำลัง Regenerate...', 'image-optimization' ),
                'regenerateComplete'   => __( 'Regenerate เสร็จสิ้น', 'image-optimization' ),
                'regenerateError'      => __( 'เกิดข้อผิดพลาดในการ Regenerate', 'image-optimization' ),
                'regenerateCancelled'  => __( 'Regenerate ถูกยกเลิก', 'image-optimization' ),
            ),
        ) );
    }
}
