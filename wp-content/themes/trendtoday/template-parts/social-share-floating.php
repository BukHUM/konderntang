<?php
/**
 * Template part for displaying floating social share buttons
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

$post_id = get_the_ID();
$post_url = trendtoday_fix_url( get_permalink( $post_id ) );
$post_title = get_the_title( $post_id );
?>

<div class="trendtoday-floating-share" id="trendtoday-floating-share">
    <button class="trendtoday-floating-share-toggle" aria-label="<?php _e( 'แชร์', 'trendtoday' ); ?>">
        <i class="fas fa-share-alt"></i>
    </button>
    <div class="trendtoday-floating-share-buttons">
        <?php foreach ( $selected_platforms as $platform ) : 
            $share_url = trendtoday_get_share_url( $platform, $post_id );
            $label = trendtoday_get_share_label( $platform );
            $icon = trendtoday_get_share_icon( $platform );
            $color = trendtoday_get_share_color( $platform );
        ?>
            <?php if ( $platform === 'copy_link' ) : ?>
                <a href="#" 
                   class="trendtoday-floating-share-btn trendtoday-share-<?php echo esc_attr( $platform ); ?> trendtoday-share-copy_link" 
                   data-platform="<?php echo esc_attr( $platform ); ?>"
                   data-post-url="<?php echo esc_url( $post_url ); ?>"
                   aria-label="<?php echo esc_attr( sprintf( __( 'Copy link', 'trendtoday' ), $label ) ); ?>"
                   style="background-color: <?php echo esc_attr( $color ); ?>;"
                   title="<?php echo esc_attr( $label ); ?>">
                    <i class="<?php echo esc_attr( $icon ); ?>"></i>
                </a>
            <?php else : ?>
                <a href="<?php echo esc_url( $share_url ); ?>" 
                   class="trendtoday-floating-share-btn trendtoday-share-<?php echo esc_attr( $platform ); ?>" 
                   data-platform="<?php echo esc_attr( $platform ); ?>"
                   target="_blank" 
                   rel="noopener noreferrer"
                   aria-label="<?php echo esc_attr( sprintf( __( 'Share on %s', 'trendtoday' ), $label ) ); ?>"
                   style="background-color: <?php echo esc_attr( $color ); ?>;"
                   title="<?php echo esc_attr( $label ); ?>">
                    <i class="<?php echo esc_attr( $icon ); ?>"></i>
                </a>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</div>
