<?php
/**
 * Menu Icons Support
 *
 * @package TrendToday
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Add icon field to menu items
 */
function trendtoday_add_menu_icon_field( $item_id, $item ) {
    $menu_icon = get_post_meta( $item_id, '_menu_item_icon', true );
    ?>
    <p class="field-icon description description-wide">
        <label for="edit-menu-item-icon-<?php echo esc_attr( $item_id ); ?>">
            <?php _e( 'Icon Class (Font Awesome)', 'trendtoday' ); ?><br />
            <input type="text" 
                   id="edit-menu-item-icon-<?php echo esc_attr( $item_id ); ?>" 
                   class="widefat code edit-menu-item-icon" 
                   name="menu-item-icon[<?php echo esc_attr( $item_id ); ?>]" 
                   value="<?php echo esc_attr( $menu_icon ); ?>" 
                   placeholder="fas fa-home" />
            <span class="description"><?php _e( 'Enter Font Awesome icon class (e.g., fas fa-home)', 'trendtoday' ); ?></span>
        </label>
    </p>
    <?php
}
add_action( 'wp_nav_menu_item_custom_fields', 'trendtoday_add_menu_icon_field', 10, 2 );

/**
 * Save menu icon field
 */
function trendtoday_save_menu_icon_field( $menu_id, $menu_item_db_id ) {
    if ( isset( $_POST['menu-item-icon'][ $menu_item_db_id ] ) ) {
        $icon = sanitize_text_field( $_POST['menu-item-icon'][ $menu_item_db_id ] );
        update_post_meta( $menu_item_db_id, '_menu_item_icon', $icon );
    } else {
        delete_post_meta( $menu_item_db_id, '_menu_item_icon' );
    }
}
add_action( 'wp_update_nav_menu_item', 'trendtoday_save_menu_icon_field', 10, 2 );

/**
 * Get menu item icon
 *
 * @param int $item_id Menu item ID.
 * @return string Icon class.
 */
function trendtoday_get_menu_item_icon( $item_id ) {
    return get_post_meta( $item_id, '_menu_item_icon', true );
}
