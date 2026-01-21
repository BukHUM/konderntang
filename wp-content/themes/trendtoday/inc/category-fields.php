<?php
/**
 * Category Custom Fields
 *
 * @package TrendToday
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Add color field to category add form
 */
function trendtoday_category_add_color_field() {
    ?>
    <div class="form-field term-color-wrap">
        <label for="category-color"><?php _e( 'Category Color', 'trendtoday' ); ?></label>
        <input type="text" name="category_color" id="category-color" value="" class="category-color-picker" />
        <p class="description"><?php _e( 'Color used for category badges and styling', 'trendtoday' ); ?></p>
    </div>
    <?php
}
add_action( 'category_add_form_fields', 'trendtoday_category_add_color_field' );

/**
 * Add color field to category edit form
 */
function trendtoday_category_edit_color_field( $term ) {
    $color = get_term_meta( $term->term_id, 'category_color', true );
    ?>
    <tr class="form-field term-color-wrap">
        <th scope="row">
            <label for="category-color"><?php _e( 'Category Color', 'trendtoday' ); ?></label>
        </th>
        <td>
            <input type="text" name="category_color" id="category-color" value="<?php echo esc_attr( $color ); ?>" class="category-color-picker" />
            <p class="description"><?php _e( 'Color used for category badges and styling', 'trendtoday' ); ?></p>
        </td>
    </tr>
    <?php
}
add_action( 'category_edit_form_fields', 'trendtoday_category_edit_color_field' );

/**
 * Save category color
 */
function trendtoday_save_category_color( $term_id ) {
    if ( isset( $_POST['category_color'] ) ) {
        $color = sanitize_hex_color( $_POST['category_color'] );
        if ( $color ) {
            update_term_meta( $term_id, 'category_color', $color );
        } else {
            delete_term_meta( $term_id, 'category_color' );
        }
    }
}
add_action( 'created_category', 'trendtoday_save_category_color' );
add_action( 'edited_category', 'trendtoday_save_category_color' );

/**
 * Enqueue color picker script
 */
function trendtoday_category_color_picker_enqueue( $hook ) {
    if ( 'edit-tags.php' !== $hook && 'term.php' !== $hook ) {
        return;
    }

    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'wp-color-picker' );

    wp_add_inline_script( 'wp-color-picker', '
        jQuery(document).ready(function($) {
            $(".category-color-picker").wpColorPicker();
        });
    ' );
}
add_action( 'admin_enqueue_scripts', 'trendtoday_category_color_picker_enqueue' );

/**
 * Add color column to category list
 */
function trendtoday_category_add_color_column( $columns ) {
    $columns['category_color'] = __( 'Color', 'trendtoday' );
    return $columns;
}
add_filter( 'manage_edit-category_columns', 'trendtoday_category_add_color_column' );

/**
 * Display color in category column
 */
function trendtoday_category_color_column_content( $content, $column_name, $term_id ) {
    if ( 'category_color' === $column_name ) {
        $color = get_term_meta( $term_id, 'category_color', true );
        if ( $color ) {
            $content = '<span style="display: inline-block; width: 20px; height: 20px; background-color: ' . esc_attr( $color ) . '; border: 1px solid #ddd; border-radius: 3px;"></span> ' . esc_html( $color );
        } else {
            $content = '—';
        }
    }
    return $content;
}
add_filter( 'manage_category_custom_column', 'trendtoday_category_color_column_content', 10, 3 );
