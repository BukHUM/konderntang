<?php
/**
 * Optimization Tab View
 *
 * @package Image_Optimization
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Variables are passed from IO_Settings_Page::render_optimization_tab()
?>

<div class="io-settings-section">
    <h2 class="io-section-title">
        <span class="dashicons dashicons-images-alt2"></span>
        <?php _e( 'Image Optimization Settings', 'image-optimization' ); ?>
    </h2>
    <p class="io-section-description">
        <?php _e( 'ตั้งค่าการปรับขนาดภาพและแปลงเป็น WebP อัตโนมัติเพื่อเพิ่มประสิทธิภาพ', 'image-optimization' ); ?>
    </p>
    
    <!-- Statistics -->
    <div class="io-stats-box">
        <h3>
            <span class="dashicons dashicons-chart-bar"></span>
            <?php _e( 'Image Statistics', 'image-optimization' ); ?>
        </h3>
        <div class="io-stats-grid">
            <div class="io-stat-item">
                <strong><?php _e( 'Total Images:', 'image-optimization' ); ?></strong>
                <span class="io-stat-value"><?php echo number_format( $image_stats['total_images'] ); ?></span>
            </div>
            <div class="io-stat-item">
                <strong><?php _e( 'Total Size:', 'image-optimization' ); ?></strong>
                <span class="io-stat-value"><?php echo number_format( $image_stats['total_size_mb'], 2 ); ?> MB</span>
            </div>
            <div class="io-stat-item">
                <strong><?php _e( 'Optimized Images:', 'image-optimization' ); ?></strong>
                <span class="io-stat-value success">
                    <?php echo number_format( $image_stats['optimized_images'] ); ?>
                    <?php if ( $image_stats['total_images'] > 0 ) : ?>
                        <small>(<?php echo number_format( $image_stats['optimization_percentage'], 1 ); ?>%)</small>
                    <?php endif; ?>
                </span>
            </div>
            <div class="io-stat-item">
                <strong><?php _e( 'WebP Images:', 'image-optimization' ); ?></strong>
                <span class="io-stat-value">
                    <?php echo number_format( $image_stats['webp_images'] ); ?>
                    <?php if ( $image_stats['total_images'] > 0 ) : ?>
                        <small>(<?php echo number_format( $image_stats['webp_percentage'], 1 ); ?>%)</small>
                    <?php endif; ?>
                </span>
            </div>
        </div>
        
        <!-- Settings Status -->
        <div class="io-settings-status-box">
            <h4>
                <span class="dashicons dashicons-admin-settings"></span>
                <?php _e( 'Current Settings Status', 'image-optimization' ); ?>
            </h4>
            <div class="io-settings-status-grid">
                <div class="io-status-item <?php echo $image_stats['settings_status']['auto_resize'] ? 'enabled' : 'disabled'; ?>">
                    <strong><?php _e( 'Auto Resize:', 'image-optimization' ); ?></strong>
                    <?php if ( $image_stats['settings_status']['auto_resize'] ) : ?>
                        <span class="status-enabled">
                            <span class="dashicons dashicons-yes-alt"></span> <?php _e( 'Enabled', 'image-optimization' ); ?>
                        </span>
                        <br><small class="io-stat-subtext">
                            <?php echo esc_html( $image_stats['settings_status']['max_width'] ); ?> × <?php echo esc_html( $image_stats['settings_status']['max_height'] ); ?> px
                        </small>
                    <?php else : ?>
                        <span class="status-disabled">
                            <span class="dashicons dashicons-dismiss"></span> <?php _e( 'Disabled', 'image-optimization' ); ?>
                        </span>
                    <?php endif; ?>
                </div>
                <div class="io-status-item <?php echo ( $image_stats['settings_status']['webp_enabled'] && $webp_supported ) ? 'enabled' : 'disabled'; ?>">
                    <strong><?php _e( 'WebP Conversion:', 'image-optimization' ); ?></strong>
                    <?php if ( $image_stats['settings_status']['webp_enabled'] && $webp_supported ) : ?>
                        <span class="status-enabled">
                            <span class="dashicons dashicons-yes-alt"></span> <?php _e( 'Enabled', 'image-optimization' ); ?>
                        </span>
                        <br><small class="io-stat-subtext">
                            <?php _e( 'Quality:', 'image-optimization' ); ?> <?php echo esc_html( $image_stats['settings_status']['webp_quality'] ); ?>%
                        </small>
                    <?php else : ?>
                        <span class="status-disabled">
                            <span class="dashicons dashicons-dismiss"></span> <?php _e( 'Disabled', 'image-optimization' ); ?>
                        </span>
                    <?php endif; ?>
                </div>
                <div class="io-status-item">
                    <strong><?php _e( 'JPEG Quality:', 'image-optimization' ); ?></strong>
                    <span><?php echo esc_html( $image_stats['settings_status']['jpeg_quality'] ); ?>%</span>
                </div>
                <div class="io-status-item <?php echo $image_stats['settings_status']['strip_exif'] ? 'enabled' : 'disabled'; ?>">
                    <strong><?php _e( 'Strip EXIF:', 'image-optimization' ); ?></strong>
                    <?php if ( $image_stats['settings_status']['strip_exif'] ) : ?>
                        <span class="status-enabled">
                            <span class="dashicons dashicons-yes-alt"></span> <?php _e( 'Enabled', 'image-optimization' ); ?>
                        </span>
                    <?php else : ?>
                        <span class="status-disabled">
                            <span class="dashicons dashicons-dismiss"></span> <?php _e( 'Disabled', 'image-optimization' ); ?>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Recent Optimized Images -->
        <?php if ( ! empty( $image_stats['recent_optimized'] ) ) : ?>
            <div class="io-settings-status-box">
                <h4>
                    <span class="dashicons dashicons-images-alt2"></span>
                    <?php _e( 'Recent Optimized Images', 'image-optimization' ); ?>
                </h4>
                <div class="io-recent-images-grid">
                    <?php foreach ( $image_stats['recent_optimized'] as $img ) : ?>
                        <div class="io-image-card">
                            <div class="io-image-card-thumb">
                                <?php if ( $img['url'] ) : ?>
                                    <img src="<?php echo esc_url( $img['url'] ); ?>" 
                                         alt="<?php echo esc_attr( $img['title'] ); ?>">
                                <?php endif; ?>
                                <?php if ( $img['has_webp'] ) : ?>
                                    <span class="io-image-card-badge">WebP</span>
                                <?php endif; ?>
                            </div>
                            <div class="io-image-card-body">
                                <div class="io-image-card-title" title="<?php echo esc_attr( $img['title'] ); ?>">
                                    <?php echo esc_html( wp_trim_words( $img['title'], 5 ) ); ?>
                                </div>
                                <div class="io-image-card-meta">
                                    <?php if ( $img['width'] > 0 && $img['height'] > 0 ) : ?>
                                        <?php echo esc_html( $img['width'] ); ?> × <?php echo esc_html( $img['height'] ); ?> px
                                    <?php endif; ?>
                                    <br>
                                    <?php echo esc_html( $img['size'] ); ?> KB
                                    <?php if ( $img['is_resized'] ) : ?>
                                        <span class="dashicons dashicons-yes-alt" title="<?php _e( 'Resized', 'image-optimization' ); ?>"></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <table class="form-table">
        <tr>
            <th scope="row">
                <?php _e( 'Auto Resize on Upload', 'image-optimization' ); ?>
            </th>
            <td>
                <label class="io-toggle">
                    <input type="checkbox" name="io_image_auto_resize" value="1" <?php checked( $auto_resize, '1' ); ?> />
                    <span class="toggle-slider"></span>
                    <span class="toggle-label"><?php _e( 'ปรับขนาดภาพอัตโนมัติเมื่ออัพโหลด', 'image-optimization' ); ?></span>
                </label>
                <p class="description">
                    <?php _e( 'ภาพจะถูกปรับขนาดตาม Max Width/Height ที่ตั้งค่าไว้', 'image-optimization' ); ?>
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="io_image_max_width"><?php _e( 'Max Width (px)', 'image-optimization' ); ?></label>
            </th>
            <td>
                <input type="number" name="io_image_max_width" id="io_image_max_width" 
                       value="<?php echo esc_attr( $max_width ); ?>" 
                       min="100" max="10000" step="10" class="small-text" />
                <p class="description">
                    <?php _e( 'ความกว้างสูงสุดของภาพ (แนะนำ: 1920px สำหรับ Full HD)', 'image-optimization' ); ?>
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="io_image_max_height"><?php _e( 'Max Height (px)', 'image-optimization' ); ?></label>
            </th>
            <td>
                <input type="number" name="io_image_max_height" id="io_image_max_height" 
                       value="<?php echo esc_attr( $max_height ); ?>" 
                       min="100" max="10000" step="10" class="small-text" />
                <p class="description">
                    <?php _e( 'ความสูงสูงสุดของภาพ (แนะนำ: 1080px สำหรับ Full HD)', 'image-optimization' ); ?>
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <?php _e( 'Maintain Aspect Ratio', 'image-optimization' ); ?>
            </th>
            <td>
                <label class="io-toggle">
                    <input type="checkbox" name="io_image_maintain_aspect" value="1" <?php checked( $maintain_aspect, '1' ); ?> />
                    <span class="toggle-slider"></span>
                    <span class="toggle-label"><?php _e( 'รักษาสัดส่วนภาพ', 'image-optimization' ); ?></span>
                </label>
                <p class="description">
                    <?php _e( 'ภาพจะถูกปรับขนาดโดยรักษาสัดส่วนเดิม', 'image-optimization' ); ?>
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="io_image_jpeg_quality"><?php _e( 'JPEG Quality', 'image-optimization' ); ?></label>
            </th>
            <td>
                <input type="number" name="io_image_jpeg_quality" id="io_image_jpeg_quality" 
                       value="<?php echo esc_attr( $jpeg_quality ); ?>" 
                       min="0" max="100" class="small-text" />
                <p class="description">
                    <?php _e( 'คุณภาพของภาพ JPEG (0-100, แนะนำ: 85 สำหรับสมดุลระหว่างคุณภาพและขนาดไฟล์)', 'image-optimization' ); ?>
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="io_image_max_file_size"><?php _e( 'Max File Size Before Resize (MB)', 'image-optimization' ); ?></label>
            </th>
            <td>
                <input type="number" name="io_image_max_file_size" id="io_image_max_file_size" 
                       value="<?php echo esc_attr( $max_file_size ); ?>" 
                       min="0" max="100" step="0.5" class="small-text" />
                <p class="description">
                    <?php _e( 'ปรับขนาดเฉพาะภาพที่ใหญ่กว่าขนาดนี้ (0 = ปรับทุกภาพ)', 'image-optimization' ); ?>
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" colspan="2">
                <h3>
                    <?php _e( 'WebP Conversion', 'image-optimization' ); ?>
                </h3>
            </th>
        </tr>
        <tr>
            <th scope="row">
                <?php _e( 'WebP Support', 'image-optimization' ); ?>
            </th>
            <td>
                <?php if ( $webp_supported ) : ?>
                    <span class="io-alert io-alert-success" style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 12px; margin-bottom: 10px;">
                        <span class="dashicons dashicons-yes-alt"></span>
                        <strong><?php _e( 'WebP is supported on this server', 'image-optimization' ); ?></strong>
                    </span>
                <?php else : ?>
                    <span class="io-alert io-alert-error" style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 12px; margin-bottom: 10px;">
                        <span class="dashicons dashicons-warning"></span>
                        <strong><?php _e( 'WebP is NOT supported on this server', 'image-optimization' ); ?></strong>
                    </span>
                    <p class="description">
                        <?php _e( 'Please install GD Library with WebP support or Imagick extension', 'image-optimization' ); ?>
                    </p>
                <?php endif; ?>
                
                <?php if ( $webp_supported && $webp_enabled === '1' ) : ?>
                    <div class="io-alert io-alert-info" style="margin-top: 15px; padding: 15px; border-left: 4px solid #2271b1; background: #f0f6fc;">
                        <h4 style="margin-top: 0; margin-bottom: 10px; font-size: 14px; font-weight: 600; color: #1d2327;">
                            <span class="dashicons dashicons-info" style="vertical-align: middle;"></span>
                            <?php _e( 'รูปแบบภาพที่ Theme ใช้', 'image-optimization' ); ?>
                        </h4>
                        <p style="margin: 0; font-size: 13px; line-height: 1.6; color: #1d2327;">
                            <?php _e( 'เมื่อเปิด WebP Conversion แล้ว:', 'image-optimization' ); ?>
                        </p>
                        <ul style="margin: 10px 0 0 20px; font-size: 13px; line-height: 1.8; color: #1d2327;">
                            <li>
                                <strong><?php _e( 'wp_get_attachment_image_url()', 'image-optimization' ); ?></strong> 
                                <?php _e( '→ จะได้ WebP URL', 'image-optimization' ); ?>
                            </li>
                            <li>
                                <strong><?php _e( 'wp_get_attachment_image_src()', 'image-optimization' ); ?></strong> 
                                <?php _e( '→ จะได้ WebP URL', 'image-optimization' ); ?>
                            </li>
                            <li>
                                <strong><?php _e( 'wp_get_attachment_image()', 'image-optimization' ); ?></strong> 
                                <?php _e( '→ จะใช้ &lt;picture&gt; tag (Browser จะเลือก WebP ถ้ารองรับ)', 'image-optimization' ); ?>
                            </li>
                        </ul>
                        <p style="margin: 10px 0 0; font-size: 12px; color: #646970; font-style: italic;">
                            <?php _e( 'หมายเหตุ: ภาพที่อัพโหลดก่อนเปิด WebP Conversion จะยังไม่มี WebP (ต้อง regenerate)', 'image-optimization' ); ?>
                        </p>
                    </div>
                <?php elseif ( $webp_enabled !== '1' ) : ?>
                    <div class="io-alert io-alert-warning" style="margin-top: 15px; padding: 15px; border-left: 4px solid #dba617; background: #fcf9e8;">
                        <p style="margin: 0; font-size: 13px; line-height: 1.6; color: #1d2327;">
                            <strong><?php _e( 'WebP Conversion ปิดอยู่', 'image-optimization' ); ?></strong><br>
                            <?php _e( 'Theme จะใช้ภาพรูปแบบ original (JPEG/PNG) ตามปกติ', 'image-optimization' ); ?>
                        </p>
                    </div>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <?php _e( 'Enable WebP Conversion', 'image-optimization' ); ?>
            </th>
            <td>
                <label class="io-toggle">
                    <input type="checkbox" name="io_image_webp_enabled" value="1" 
                           <?php checked( $webp_enabled, '1' ); ?>
                           <?php echo ! $webp_supported ? 'disabled' : ''; ?> />
                    <span class="toggle-slider"></span>
                    <span class="toggle-label"><?php _e( 'แปลงภาพเป็น WebP อัตโนมัติ', 'image-optimization' ); ?></span>
                </label>
                <p class="description">
                    <?php _e( 'สร้างไฟล์ WebP พร้อมกับไฟล์ต้นฉบับ (ลดขนาดไฟล์ 30-50%)', 'image-optimization' ); ?>
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="io_image_webp_quality"><?php _e( 'WebP Quality', 'image-optimization' ); ?></label>
            </th>
            <td>
                <input type="number" name="io_image_webp_quality" id="io_image_webp_quality" 
                       value="<?php echo esc_attr( $webp_quality ); ?>" 
                       min="0" max="100" 
                       class="small-text"
                       <?php echo ! $webp_supported ? 'disabled' : ''; ?> />
                <p class="description">
                    <?php _e( 'คุณภาพของภาพ WebP (0-100, แนะนำ: 85)', 'image-optimization' ); ?>
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row" colspan="2">
                <h3>
                    <?php _e( 'Additional Options', 'image-optimization' ); ?>
                </h3>
            </th>
        </tr>
        <tr>
            <th scope="row">
                <?php _e( 'Strip EXIF Data', 'image-optimization' ); ?>
            </th>
            <td>
                <label class="io-toggle">
                    <input type="checkbox" name="io_image_strip_exif" value="1" <?php checked( $strip_exif, '1' ); ?> />
                    <span class="toggle-slider"></span>
                    <span class="toggle-label"><?php _e( 'ลบ EXIF metadata ออกจากภาพ', 'image-optimization' ); ?></span>
                </label>
                <p class="description">
                    <?php _e( 'ลบข้อมูล EXIF (เช่น GPS, Camera info) เพื่อลดขนาดไฟล์และความเป็นส่วนตัว', 'image-optimization' ); ?>
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <?php _e( 'Regenerate Images', 'image-optimization' ); ?>
            </th>
            <td>
                <div id="io-regenerate-controls">
                    <button type="button" id="io-regenerate-images" class="button button-secondary">
                        <span class="dashicons dashicons-update"></span>
                        <?php _e( 'Regenerate All Images', 'image-optimization' ); ?>
                    </button>
                    <p class="description">
                        <?php _e( 'ปรับขนาดและแปลงเป็น WebP สำหรับภาพที่มีอยู่แล้วทั้งหมด', 'image-optimization' ); ?>
                    </p>
                    <div id="io-regenerate-preview" style="display: none; margin-top: 10px; padding: 15px; background: #f0f6fc; border-left: 4px solid #2271b1; border-radius: 4px;">
                        <p style="margin: 0 0 10px; font-weight: 600; color: #1d2327;">
                            <span class="dashicons dashicons-info"></span>
                            <?php _e( 'พบภาพทั้งหมด:', 'image-optimization' ); ?>
                            <span id="io-regenerate-total-count" style="color: #2271b1; font-size: 18px;">0</span>
                            <?php _e( 'ไฟล์', 'image-optimization' ); ?>
                        </p>
                        <p style="margin: 0; font-size: 13px; color: #646970;">
                            <?php _e( 'กระบวนการนี้อาจใช้เวลานาน คุณต้องการดำเนินการต่อหรือไม่?', 'image-optimization' ); ?>
                        </p>
                        <div style="margin-top: 10px;">
                            <button type="button" id="io-regenerate-confirm" class="button button-primary">
                                <?php _e( 'เริ่ม Regenerate', 'image-optimization' ); ?>
                            </button>
                            <button type="button" id="io-regenerate-cancel-preview" class="button">
                                <?php _e( 'ยกเลิก', 'image-optimization' ); ?>
                            </button>
                        </div>
                    </div>
                </div>
                <div id="io-regenerate-progress" style="display: none; margin-top: 15px;">
                    <div style="background: #fff; border: 1px solid #ddd; border-radius: 8px; padding: 20px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                            <h4 style="margin: 0; font-size: 14px; font-weight: 600; color: #1d2327;">
                                <span class="dashicons dashicons-update" style="animation: spin 1s linear infinite;"></span>
                                <?php _e( 'กำลัง Regenerate Images...', 'image-optimization' ); ?>
                            </h4>
                            <button type="button" id="io-regenerate-cancel" class="button button-small">
                                <span class="dashicons dashicons-dismiss"></span>
                                <?php _e( 'ยกเลิก', 'image-optimization' ); ?>
                            </button>
                        </div>
                        <div class="io-progress-container" style="margin-bottom: 15px;">
                            <div id="io-regenerate-progress-bar" class="io-progress-bar"></div>
                        </div>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; font-size: 13px;">
                            <div>
                                <strong style="color: #646970; display: block; margin-bottom: 5px;"><?php _e( 'ทำเสร็จแล้ว:', 'image-optimization' ); ?></strong>
                                <span id="io-regenerate-processed" style="font-size: 20px; font-weight: 700; color: #00a32a;">0</span>
                                <span style="color: #646970;">/</span>
                                <span id="io-regenerate-total" style="color: #646970;">0</span>
                            </div>
                            <div>
                                <strong style="color: #646970; display: block; margin-bottom: 5px;"><?php _e( 'สำเร็จ:', 'image-optimization' ); ?></strong>
                                <span id="io-regenerate-success" style="font-size: 20px; font-weight: 700; color: #00a32a;">0</span>
                            </div>
                            <div>
                                <strong style="color: #646970; display: block; margin-bottom: 5px;"><?php _e( 'ล้มเหลว:', 'image-optimization' ); ?></strong>
                                <span id="io-regenerate-failed" style="font-size: 20px; font-weight: 700; color: #d63638;">0</span>
                            </div>
                            <div>
                                <strong style="color: #646970; display: block; margin-bottom: 5px;"><?php _e( 'เวลาที่ใช้:', 'image-optimization' ); ?></strong>
                                <span id="io-regenerate-time" style="font-size: 20px; font-weight: 700; color: #2271b1;">0:00</span>
                            </div>
                            <div>
                                <strong style="color: #646970; display: block; margin-bottom: 5px;"><?php _e( 'เวลาที่เหลือ (ประมาณ):', 'image-optimization' ); ?></strong>
                                <span id="io-regenerate-eta" style="font-size: 20px; font-weight: 700; color: #646970;">-</span>
                            </div>
                        </div>
                        <p id="io-regenerate-status" class="io-progress-status" style="margin-top: 15px;"></p>
                    </div>
                </div>
            </td>
        </tr>
    </table>
</div>
