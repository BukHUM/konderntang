<?php
/**
 * Template part for displaying social share buttons
 *
 * @package TrendToday
 * @since 1.0.0
 */

// Check if social sharing is enabled
$social_sharing_enabled = get_option( 'trendtoday_social_sharing_enabled', '1' );
if ( $social_sharing_enabled !== '1' ) {
    return;
}

$selected_platforms = get_option( 'trendtoday_social_platforms', array( 'facebook', 'twitter', 'line' ) );
if ( empty( $selected_platforms ) ) {
    return;
}

$button_style = get_option( 'trendtoday_social_button_style', 'icon_only' );
$button_size = get_option( 'trendtoday_social_button_size', 'medium' );

// Size classes
$size_classes = array(
    'small' => 'text-sm px-3 py-2',
    'medium' => 'text-base px-4 py-3',
    'large' => 'text-lg px-5 py-4',
);

$size_class = isset( $size_classes[ $button_size ] ) ? $size_classes[ $button_size ] : $size_classes['medium'];

// Style classes
$style_classes = array(
    'icon_only' => 'trendtoday-share-icon-only',
    'icon_text' => 'trendtoday-share-icon-text',
    'button' => 'trendtoday-share-button',
);

$style_class = isset( $style_classes[ $button_style ] ) ? $style_classes[ $button_style ] : $style_classes['icon_only'];

$post_id = get_the_ID();
$post_url = trendtoday_fix_url( get_permalink( $post_id ) );
$post_title = get_the_title( $post_id );
?>

<div class="trendtoday-social-share <?php echo esc_attr( $style_class ); ?> <?php echo esc_attr( $size_class ); ?>" data-post-id="<?php echo esc_attr( $post_id ); ?>" data-post-url="<?php echo esc_url( $post_url ); ?>" data-post-title="<?php echo esc_attr( $post_title ); ?>">
    <?php if ( $button_style === 'icon_text' || $button_style === 'button' ) : ?>
        <span class="trendtoday-share-label"><?php _e( 'แชร์', 'trendtoday' ); ?></span>
    <?php endif; ?>
    
    <div class="trendtoday-share-buttons">
        <?php foreach ( $selected_platforms as $platform ) : 
            $share_url = trendtoday_get_share_url( $platform, $post_id );
            $label = trendtoday_get_share_label( $platform );
            $icon = trendtoday_get_share_icon( $platform );
            $color = trendtoday_get_share_color( $platform );
        ?>
            <?php if ( $platform === 'copy_link' ) : ?>
                <a href="#" 
                   class="trendtoday-share-btn trendtoday-share-<?php echo esc_attr( $platform ); ?> trendtoday-share-copy_link" 
                   data-platform="<?php echo esc_attr( $platform ); ?>"
                   data-post-url="<?php echo esc_url( $post_url ); ?>"
                   aria-label="<?php echo esc_attr( sprintf( __( 'Copy link', 'trendtoday' ), $label ) ); ?>"
                   style="background-color: <?php echo esc_attr( $color ); ?>;">
                    <i class="<?php echo esc_attr( $icon ); ?>"></i>
                    <?php if ( $button_style === 'icon_text' || $button_style === 'button' ) : ?>
                        <span class="trendtoday-share-btn-label"><?php echo esc_html( $label ); ?></span>
                    <?php endif; ?>
                </a>
            <?php else : ?>
                <a href="<?php echo esc_url( $share_url ); ?>" 
                   class="trendtoday-share-btn trendtoday-share-<?php echo esc_attr( $platform ); ?>" 
                   data-platform="<?php echo esc_attr( $platform ); ?>"
                   target="_blank" 
                   rel="noopener noreferrer"
                   aria-label="<?php echo esc_attr( sprintf( __( 'Share on %s', 'trendtoday' ), $label ) ); ?>"
                   style="background-color: <?php echo esc_attr( $color ); ?>;">
                    <i class="<?php echo esc_attr( $icon ); ?>"></i>
                    <?php if ( $button_style === 'icon_text' || $button_style === 'button' ) : ?>
                        <span class="trendtoday-share-btn-label"><?php echo esc_html( $label ); ?></span>
                    <?php endif; ?>
                </a>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</div>
