<?php
/**
 * Custom Fields and Meta Boxes
 *
 * @package KonDernTang
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Add custom meta boxes
 */
function konderntang_add_meta_boxes() {
    // Post Options for standard posts
    add_meta_box(
        'konderntang_post_options',
        esc_html__( 'Post Options', 'konderntang' ),
        'konderntang_post_options_callback',
        'post',
        'side',
        'default'
    );

    // TOC Options
    add_meta_box(
        'konderntang_toc_options',
        esc_html__( 'Table of Contents', 'konderntang' ),
        'konderntang_toc_options_callback',
        'post',
        'side',
        'default'
    );

    // Travel Guide Options
    add_meta_box(
        'konderntang_travel_guide_options',
        esc_html__( 'Travel Guide Information', 'konderntang' ),
        'konderntang_travel_guide_options_callback',
        'travel_guide',
        'normal',
        'high'
    );

    // Hotel Options
    add_meta_box(
        'konderntang_hotel_options',
        esc_html__( 'Hotel Information', 'konderntang' ),
        'konderntang_hotel_options_callback',
        'hotel',
        'normal',
        'high'
    );

    // Promotion Options
    add_meta_box(
        'konderntang_promotion_options',
        esc_html__( 'Promotion Information', 'konderntang' ),
        'konderntang_promotion_options_callback',
        'promotion',
        'normal',
        'high'
    );

    // SEO Options (for all post types)
    $post_types = array( 'post', 'travel_guide', 'hotel', 'promotion' );
    foreach ( $post_types as $post_type ) {
        add_meta_box(
            'konderntang_seo_options',
            esc_html__( 'SEO Options', 'konderntang' ),
            'konderntang_seo_options_callback',
            $post_type,
            'normal',
            'low'
        );
    }
}
add_action( 'add_meta_boxes', 'konderntang_add_meta_boxes' );

/**
 * Post Options Meta Box Callback
 */
function konderntang_post_options_callback( $post ) {
    wp_nonce_field( 'konderntang_save_meta', 'konderntang_meta_nonce' );

    $breaking_news = get_post_meta( $post->ID, '_konderntang_breaking_news', true );
    $reading_time = get_post_meta( $post->ID, '_konderntang_reading_time', true );
    $featured_post = get_post_meta( $post->ID, '_konderntang_featured_post', true );
    ?>
    <div style="padding: 8px 0;">
        <label style="display: flex; align-items: center; gap: 8px; padding: 10px; border-radius: 6px; transition: background 0.2s; cursor: pointer;" onmouseover="this.style.background='#f0f9ff'" onmouseout="this.style.background='transparent'">
            <input type="checkbox" name="breaking_news" value="1" <?php checked( $breaking_news, '1' ); ?> style="width: 18px; height: 18px; accent-color: #ef4444;" />
            <span class="dashicons dashicons-megaphone" style="color: #ef4444; font-size: 18px;"></span>
            <strong><?php esc_html_e( 'Breaking News', 'konderntang' ); ?></strong>
        </label>
        <p class="description" style="margin: 5px 0 0 35px; color: #64748b; font-size: 12px;">
            <?php esc_html_e( 'แสดงเป็นข่าวด่วน', 'konderntang' ); ?>
        </p>
    </div>
    <div style="padding: 8px 0; border-top: 1px solid #e2e8f0; margin-top: 12px;">
        <label style="display: flex; align-items: center; gap: 8px; padding: 10px; border-radius: 6px; transition: background 0.2s; cursor: pointer;" onmouseover="this.style.background='#f0f9ff'" onmouseout="this.style.background='transparent'">
            <input type="checkbox" name="featured_post" value="1" <?php checked( $featured_post, '1' ); ?> style="width: 18px; height: 18px; accent-color: #0ea5e9;" />
            <span class="dashicons dashicons-star-filled" style="color: #f97316; font-size: 18px;"></span>
            <strong><?php esc_html_e( 'Featured Post', 'konderntang' ); ?></strong>
        </label>
        <p class="description" style="margin: 5px 0 0 35px; color: #64748b; font-size: 12px;">
            <?php esc_html_e( 'แสดงใน Hero Slider และ Featured Section', 'konderntang' ); ?>
        </p>
    </div>
    <div style="padding: 8px 0; border-top: 1px solid #e2e8f0; margin-top: 12px;">
        <label for="reading_time" style="display: block; margin-bottom: 8px; font-weight: 600; color: #1e293b;">
            <span class="dashicons dashicons-clock" style="vertical-align: middle; margin-right: 4px; color: #0ea5e9;"></span>
            <?php esc_html_e( 'Reading Time (minutes)', 'konderntang' ); ?>
        </label>
        <input type="number" id="reading_time" name="reading_time" value="<?php echo esc_attr( $reading_time ); ?>" min="1" step="1" class="small-text" style="width: 80px; padding: 6px 10px; border: 1px solid #cbd5e1; border-radius: 6px;" />
        <p class="description" style="margin: 5px 0 0; color: #64748b; font-size: 12px;">
            <?php esc_html_e( 'เวลาที่ใช้ในการอ่านบทความ (นาที)', 'konderntang' ); ?>
        </p>
    </div>
    <?php
}

/**
 * TOC Options Meta Box Callback
 */
function konderntang_toc_options_callback( $post ) {
    wp_nonce_field( 'konderntang_save_meta', 'konderntang_meta_nonce' );

    $toc_enabled = get_post_meta( $post->ID, '_konderntang_toc_enabled', true );
    $toc_position = get_post_meta( $post->ID, '_konderntang_toc_position', true );
    if ( empty( $toc_position ) ) {
        $toc_position = 'before_content';
    }
    ?>
    <div style="padding: 8px 0;">
        <label style="display: flex; align-items: center; gap: 8px; padding: 10px; border-radius: 6px; transition: background 0.2s; cursor: pointer;" onmouseover="this.style.background='#f0f9ff'" onmouseout="this.style.background='transparent'">
            <input type="checkbox" name="toc_enabled" value="yes" <?php checked( $toc_enabled, 'yes' ); ?> style="width: 18px; height: 18px; accent-color: #0ea5e9;" />
            <span class="dashicons dashicons-list-view" style="color: #0ea5e9; font-size: 18px;"></span>
            <strong><?php esc_html_e( 'Enable Table of Contents', 'konderntang' ); ?></strong>
        </label>
        <p class="description" style="margin: 5px 0 0 35px; color: #64748b; font-size: 12px;">
            <?php esc_html_e( 'สร้างสารบัญอัตโนมัติจากหัวข้อในบทความ', 'konderntang' ); ?>
        </p>
    </div>
    <div style="padding: 8px 0; border-top: 1px solid #e2e8f0; margin-top: 12px;">
        <label for="toc_position" style="display: block; margin-bottom: 8px; font-weight: 600; color: #1e293b;">
            <span class="dashicons dashicons-admin-settings" style="vertical-align: middle; margin-right: 4px; color: #0ea5e9;"></span>
            <?php esc_html_e( 'TOC Position:', 'konderntang' ); ?>
        </label>
        <select id="toc_position" name="toc_position" class="widefat">
            <option value="before_content" <?php selected( $toc_position, 'before_content' ); ?>><?php esc_html_e( 'Before Content', 'konderntang' ); ?></option>
            <option value="after_first_heading" <?php selected( $toc_position, 'after_first_heading' ); ?>><?php esc_html_e( 'After First Heading', 'konderntang' ); ?></option>
            <option value="sidebar" <?php selected( $toc_position, 'sidebar' ); ?>><?php esc_html_e( 'Sidebar', 'konderntang' ); ?></option>
        </select>
    </p>
    <?php
}

/**
 * Travel Guide Options Meta Box Callback
 */
function konderntang_travel_guide_options_callback( $post ) {
    wp_nonce_field( 'konderntang_save_meta', 'konderntang_meta_nonce' );

    $location = get_post_meta( $post->ID, '_konderntang_location', true );
    $duration = get_post_meta( $post->ID, '_konderntang_duration', true );
    $season = get_post_meta( $post->ID, '_konderntang_season', true );
    $difficulty = get_post_meta( $post->ID, '_konderntang_difficulty', true );
    $price_range = get_post_meta( $post->ID, '_konderntang_price_range', true );
    ?>
    <table class="form-table">
        <tr>
            <th scope="row">
                <label for="location"><?php esc_html_e( 'Location', 'konderntang' ); ?></label>
            </th>
            <td>
                <input type="text" id="location" name="location" value="<?php echo esc_attr( $location ); ?>" class="regular-text" />
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="duration"><?php esc_html_e( 'Duration', 'konderntang' ); ?></label>
            </th>
            <td>
                <input type="text" id="duration" name="duration" value="<?php echo esc_attr( $duration ); ?>" class="regular-text" placeholder="<?php esc_attr_e( 'e.g., 3 days 2 nights', 'konderntang' ); ?>" />
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="season"><?php esc_html_e( 'Best Season', 'konderntang' ); ?></label>
            </th>
            <td>
                <select id="season" name="season" class="regular-text">
                    <option value=""><?php esc_html_e( 'Select Season', 'konderntang' ); ?></option>
                    <option value="all" <?php selected( $season, 'all' ); ?>><?php esc_html_e( 'All Year', 'konderntang' ); ?></option>
                    <option value="summer" <?php selected( $season, 'summer' ); ?>><?php esc_html_e( 'Summer', 'konderntang' ); ?></option>
                    <option value="winter" <?php selected( $season, 'winter' ); ?>><?php esc_html_e( 'Winter', 'konderntang' ); ?></option>
                    <option value="rainy" <?php selected( $season, 'rainy' ); ?>><?php esc_html_e( 'Rainy Season', 'konderntang' ); ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="difficulty"><?php esc_html_e( 'Difficulty Level', 'konderntang' ); ?></label>
            </th>
            <td>
                <select id="difficulty" name="difficulty" class="regular-text">
                    <option value=""><?php esc_html_e( 'Select Difficulty', 'konderntang' ); ?></option>
                    <option value="easy" <?php selected( $difficulty, 'easy' ); ?>><?php esc_html_e( 'Easy', 'konderntang' ); ?></option>
                    <option value="medium" <?php selected( $difficulty, 'medium' ); ?>><?php esc_html_e( 'Medium', 'konderntang' ); ?></option>
                    <option value="hard" <?php selected( $difficulty, 'hard' ); ?>><?php esc_html_e( 'Hard', 'konderntang' ); ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="price_range"><?php esc_html_e( 'Price Range', 'konderntang' ); ?></label>
            </th>
            <td>
                <input type="text" id="price_range" name="price_range" value="<?php echo esc_attr( $price_range ); ?>" class="regular-text" placeholder="<?php esc_attr_e( 'e.g., 5,000 - 10,000 THB', 'konderntang' ); ?>" />
            </td>
        </tr>
    </table>
    <?php
}

/**
 * Hotel Options Meta Box Callback
 */
function konderntang_hotel_options_callback( $post ) {
    wp_nonce_field( 'konderntang_save_meta', 'konderntang_meta_nonce' );

    $hotel_price = get_post_meta( $post->ID, '_konderntang_hotel_price', true );
    $hotel_rating = get_post_meta( $post->ID, '_konderntang_hotel_rating', true );
    $hotel_amenities = get_post_meta( $post->ID, '_konderntang_hotel_amenities', true );
    $hotel_address = get_post_meta( $post->ID, '_konderntang_hotel_address', true );
    $hotel_phone = get_post_meta( $post->ID, '_konderntang_hotel_phone', true );
    $hotel_website = get_post_meta( $post->ID, '_konderntang_hotel_website', true );
    ?>
    <table class="form-table">
        <tr>
            <th scope="row">
                <label for="hotel_price"><?php esc_html_e( 'Price per Night', 'konderntang' ); ?></label>
            </th>
            <td>
                <input type="text" id="hotel_price" name="hotel_price" value="<?php echo esc_attr( $hotel_price ); ?>" class="regular-text" placeholder="<?php esc_attr_e( 'e.g., 2,500 THB', 'konderntang' ); ?>" />
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="hotel_rating"><?php esc_html_e( 'Rating', 'konderntang' ); ?></label>
            </th>
            <td>
                <select id="hotel_rating" name="hotel_rating" class="regular-text">
                    <option value=""><?php esc_html_e( 'Select Rating', 'konderntang' ); ?></option>
                    <option value="1" <?php selected( $hotel_rating, '1' ); ?>>1 Star</option>
                    <option value="2" <?php selected( $hotel_rating, '2' ); ?>>2 Stars</option>
                    <option value="3" <?php selected( $hotel_rating, '3' ); ?>>3 Stars</option>
                    <option value="4" <?php selected( $hotel_rating, '4' ); ?>>4 Stars</option>
                    <option value="5" <?php selected( $hotel_rating, '5' ); ?>>5 Stars</option>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="hotel_amenities"><?php esc_html_e( 'Amenities', 'konderntang' ); ?></label>
            </th>
            <td>
                <textarea id="hotel_amenities" name="hotel_amenities" rows="5" class="large-text"><?php echo esc_textarea( $hotel_amenities ); ?></textarea>
                <p class="description"><?php esc_html_e( 'Enter amenities separated by commas or line breaks', 'konderntang' ); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="hotel_address"><?php esc_html_e( 'Address', 'konderntang' ); ?></label>
            </th>
            <td>
                <textarea id="hotel_address" name="hotel_address" rows="3" class="large-text"><?php echo esc_textarea( $hotel_address ); ?></textarea>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="hotel_phone"><?php esc_html_e( 'Phone', 'konderntang' ); ?></label>
            </th>
            <td>
                <input type="text" id="hotel_phone" name="hotel_phone" value="<?php echo esc_attr( $hotel_phone ); ?>" class="regular-text" />
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="hotel_website"><?php esc_html_e( 'Website', 'konderntang' ); ?></label>
            </th>
            <td>
                <input type="url" id="hotel_website" name="hotel_website" value="<?php echo esc_attr( $hotel_website ); ?>" class="regular-text" />
            </td>
        </tr>
    </table>
    <?php
}

/**
 * Promotion Options Meta Box Callback
 */
function konderntang_promotion_options_callback( $post ) {
    wp_nonce_field( 'konderntang_save_meta', 'konderntang_meta_nonce' );

    $promotion_price = get_post_meta( $post->ID, '_konderntang_promotion_price', true );
    $promotion_discount = get_post_meta( $post->ID, '_konderntang_promotion_discount', true );
    $promotion_start_date = get_post_meta( $post->ID, '_konderntang_promotion_start_date', true );
    $promotion_end_date = get_post_meta( $post->ID, '_konderntang_promotion_end_date', true );
    $promotion_code = get_post_meta( $post->ID, '_konderntang_promotion_code', true );
    ?>
    <table class="form-table">
        <tr>
            <th scope="row">
                <label for="promotion_price"><?php esc_html_e( 'Price', 'konderntang' ); ?></label>
            </th>
            <td>
                <input type="text" id="promotion_price" name="promotion_price" value="<?php echo esc_attr( $promotion_price ); ?>" class="regular-text" />
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="promotion_discount"><?php esc_html_e( 'Discount (%)', 'konderntang' ); ?></label>
            </th>
            <td>
                <input type="number" id="promotion_discount" name="promotion_discount" value="<?php echo esc_attr( $promotion_discount ); ?>" min="0" max="100" step="1" class="small-text" />
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="promotion_start_date"><?php esc_html_e( 'Start Date', 'konderntang' ); ?></label>
            </th>
            <td>
                <input type="date" id="promotion_start_date" name="promotion_start_date" value="<?php echo esc_attr( $promotion_start_date ); ?>" class="regular-text" />
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="promotion_end_date"><?php esc_html_e( 'End Date', 'konderntang' ); ?></label>
            </th>
            <td>
                <input type="date" id="promotion_end_date" name="promotion_end_date" value="<?php echo esc_attr( $promotion_end_date ); ?>" class="regular-text" />
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="promotion_code"><?php esc_html_e( 'Promotion Code', 'konderntang' ); ?></label>
            </th>
            <td>
                <input type="text" id="promotion_code" name="promotion_code" value="<?php echo esc_attr( $promotion_code ); ?>" class="regular-text" />
            </td>
        </tr>
    </table>
    <?php
}

/**
 * SEO Options Meta Box Callback
 */
function konderntang_seo_options_callback( $post ) {
    wp_nonce_field( 'konderntang_save_meta', 'konderntang_meta_nonce' );

    $meta_description = get_post_meta( $post->ID, '_konderntang_meta_description', true );
    $meta_keywords = get_post_meta( $post->ID, '_konderntang_meta_keywords', true );
    $og_title = get_post_meta( $post->ID, '_konderntang_og_title', true );
    $og_description = get_post_meta( $post->ID, '_konderntang_og_description', true );
    $og_image = get_post_meta( $post->ID, '_konderntang_og_image', true );
    ?>
    <table class="form-table">
        <tr>
            <th scope="row">
                <label for="meta_description"><?php esc_html_e( 'Meta Description', 'konderntang' ); ?></label>
            </th>
            <td>
                <textarea id="meta_description" name="meta_description" rows="3" class="large-text"><?php echo esc_textarea( $meta_description ); ?></textarea>
                <p class="description"><?php esc_html_e( 'Recommended: 150-160 characters', 'konderntang' ); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="meta_keywords"><?php esc_html_e( 'Meta Keywords', 'konderntang' ); ?></label>
            </th>
            <td>
                <input type="text" id="meta_keywords" name="meta_keywords" value="<?php echo esc_attr( $meta_keywords ); ?>" class="large-text" />
                <p class="description"><?php esc_html_e( 'Separate keywords with commas', 'konderntang' ); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="og_title"><?php esc_html_e( 'Open Graph Title', 'konderntang' ); ?></label>
            </th>
            <td>
                <input type="text" id="og_title" name="og_title" value="<?php echo esc_attr( $og_title ); ?>" class="large-text" />
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="og_description"><?php esc_html_e( 'Open Graph Description', 'konderntang' ); ?></label>
            </th>
            <td>
                <textarea id="og_description" name="og_description" rows="3" class="large-text"><?php echo esc_textarea( $og_description ); ?></textarea>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="og_image"><?php esc_html_e( 'Open Graph Image URL', 'konderntang' ); ?></label>
            </th>
            <td>
                <input type="url" id="og_image" name="og_image" value="<?php echo esc_attr( $og_image ); ?>" class="large-text" />
                <button type="button" class="button media-upload-button" data-target="og_image"><?php esc_html_e( 'Upload Image', 'konderntang' ); ?></button>
            </td>
        </tr>
    </table>
    <?php
}

/**
 * Save meta box data
 */
function konderntang_save_meta_box( $post_id ) {
    // Security checks
    if ( ! isset( $_POST['konderntang_meta_nonce'] ) || ! wp_verify_nonce( $_POST['konderntang_meta_nonce'], 'konderntang_save_meta' ) ) {
        return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    // Post Options
    if ( isset( $_POST['breaking_news'] ) ) {
        update_post_meta( $post_id, '_konderntang_breaking_news', '1' );
    } else {
        delete_post_meta( $post_id, '_konderntang_breaking_news' );
    }

    if ( isset( $_POST['featured_post'] ) ) {
        update_post_meta( $post_id, '_konderntang_featured_post', '1' );
    } else {
        delete_post_meta( $post_id, '_konderntang_featured_post' );
    }

    if ( isset( $_POST['reading_time'] ) ) {
        update_post_meta( $post_id, '_konderntang_reading_time', absint( $_POST['reading_time'] ) );
    }

    // TOC Options
    if ( isset( $_POST['toc_enabled'] ) ) {
        update_post_meta( $post_id, '_konderntang_toc_enabled', sanitize_text_field( $_POST['toc_enabled'] ) );
    } else {
        delete_post_meta( $post_id, '_konderntang_toc_enabled' );
    }

    if ( isset( $_POST['toc_position'] ) ) {
        update_post_meta( $post_id, '_konderntang_toc_position', sanitize_text_field( $_POST['toc_position'] ) );
    }

    // Travel Guide Options
    if ( isset( $_POST['location'] ) ) {
        update_post_meta( $post_id, '_konderntang_location', sanitize_text_field( $_POST['location'] ) );
    }
    if ( isset( $_POST['duration'] ) ) {
        update_post_meta( $post_id, '_konderntang_duration', sanitize_text_field( $_POST['duration'] ) );
    }
    if ( isset( $_POST['season'] ) ) {
        update_post_meta( $post_id, '_konderntang_season', sanitize_text_field( $_POST['season'] ) );
    }
    if ( isset( $_POST['difficulty'] ) ) {
        update_post_meta( $post_id, '_konderntang_difficulty', sanitize_text_field( $_POST['difficulty'] ) );
    }
    if ( isset( $_POST['price_range'] ) ) {
        update_post_meta( $post_id, '_konderntang_price_range', sanitize_text_field( $_POST['price_range'] ) );
    }

    // Hotel Options
    if ( isset( $_POST['hotel_price'] ) ) {
        update_post_meta( $post_id, '_konderntang_hotel_price', sanitize_text_field( $_POST['hotel_price'] ) );
    }
    if ( isset( $_POST['hotel_rating'] ) ) {
        update_post_meta( $post_id, '_konderntang_hotel_rating', sanitize_text_field( $_POST['hotel_rating'] ) );
    }
    if ( isset( $_POST['hotel_amenities'] ) ) {
        update_post_meta( $post_id, '_konderntang_hotel_amenities', sanitize_textarea_field( $_POST['hotel_amenities'] ) );
    }
    if ( isset( $_POST['hotel_address'] ) ) {
        update_post_meta( $post_id, '_konderntang_hotel_address', sanitize_textarea_field( $_POST['hotel_address'] ) );
    }
    if ( isset( $_POST['hotel_phone'] ) ) {
        update_post_meta( $post_id, '_konderntang_hotel_phone', sanitize_text_field( $_POST['hotel_phone'] ) );
    }
    if ( isset( $_POST['hotel_website'] ) ) {
        update_post_meta( $post_id, '_konderntang_hotel_website', esc_url_raw( $_POST['hotel_website'] ) );
    }

    // Promotion Options
    if ( isset( $_POST['promotion_price'] ) ) {
        update_post_meta( $post_id, '_konderntang_promotion_price', sanitize_text_field( $_POST['promotion_price'] ) );
    }
    if ( isset( $_POST['promotion_discount'] ) ) {
        update_post_meta( $post_id, '_konderntang_promotion_discount', absint( $_POST['promotion_discount'] ) );
    }
    if ( isset( $_POST['promotion_start_date'] ) ) {
        update_post_meta( $post_id, '_konderntang_promotion_start_date', sanitize_text_field( $_POST['promotion_start_date'] ) );
    }
    if ( isset( $_POST['promotion_end_date'] ) ) {
        update_post_meta( $post_id, '_konderntang_promotion_end_date', sanitize_text_field( $_POST['promotion_end_date'] ) );
    }
    if ( isset( $_POST['promotion_code'] ) ) {
        update_post_meta( $post_id, '_konderntang_promotion_code', sanitize_text_field( $_POST['promotion_code'] ) );
    }

    // SEO Options
    if ( isset( $_POST['meta_description'] ) ) {
        update_post_meta( $post_id, '_konderntang_meta_description', sanitize_textarea_field( $_POST['meta_description'] ) );
    }
    if ( isset( $_POST['meta_keywords'] ) ) {
        update_post_meta( $post_id, '_konderntang_meta_keywords', sanitize_text_field( $_POST['meta_keywords'] ) );
    }
    if ( isset( $_POST['og_title'] ) ) {
        update_post_meta( $post_id, '_konderntang_og_title', sanitize_text_field( $_POST['og_title'] ) );
    }
    if ( isset( $_POST['og_description'] ) ) {
        update_post_meta( $post_id, '_konderntang_og_description', sanitize_textarea_field( $_POST['og_description'] ) );
    }
    if ( isset( $_POST['og_image'] ) ) {
        update_post_meta( $post_id, '_konderntang_og_image', esc_url_raw( $_POST['og_image'] ) );
    }
}
add_action( 'save_post', 'konderntang_save_meta_box' );
