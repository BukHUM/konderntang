<?php
/**
 * Footer Component
 *
 * @package KonDernTang
 * @since 1.0.0
 */
?>

<footer class="bg-dark text-white pt-12 pb-8 border-t border-gray-700 mt-auto">
    <div class="container mx-auto px-4">
        <?php
        // Footer Widget Areas
        $footer_layout = absint( konderntang_get_option( 'footer_layout', 0 ) );
        if ( $footer_layout > 0 ) {
            $footer_widgets_active = false;
            for ( $i = 1; $i <= $footer_layout; $i++ ) {
                if ( is_active_sidebar( 'footer-' . $i ) ) {
                    $footer_widgets_active = true;
                    break;
                }
            }

            if ( $footer_widgets_active ) {
                ?>
                <div class="grid grid-cols-1 md:grid-cols-<?php echo esc_attr( $footer_layout ); ?> gap-8 mb-8">
                    <?php
                    for ( $i = 1; $i <= $footer_layout; $i++ ) {
                        if ( is_active_sidebar( 'footer-' . $i ) ) {
                            ?>
                            <div class="footer-widget-area footer-widget-<?php echo esc_attr( $i ); ?>">
                                <?php dynamic_sidebar( 'footer-' . $i ); ?>
                            </div>
                            <?php
                        }
                    }
                    ?>
                </div>
                <?php
            }
        }
        ?>

        <div class="text-center">
            <h2 class="font-heading font-bold text-2xl mb-4">
                <?php bloginfo( 'name' ); ?><span class="text-secondary">.com</span>
            </h2>
            
            <?php
            if ( has_nav_menu( 'footer' ) ) {
                wp_nav_menu(
                    array(
                        'theme_location' => 'footer',
                        'container'      => false,
                        'menu_class'     => 'flex justify-center gap-6 mb-6 text-gray-400',
                        'fallback_cb'    => false,
                    )
                );
            }
            ?>
            
            <p class="text-gray-500 text-sm">
                <?php
                $copyright_text = konderntang_get_option( 'footer_copyright_text', '' );
                if ( $copyright_text ) {
                    $copyright_text = str_replace( '%year%', date( 'Y' ), $copyright_text );
                    $copyright_text = str_replace( '%site%', get_bloginfo( 'name' ), $copyright_text );
                    echo wp_kses_post( $copyright_text );
                } else {
                    printf(
                        /* translators: 1: Year, 2: Site Name */
                        esc_html__( '&copy; %1$s %2$s - เพื่อนเดินทางของคุณ', 'konderntang' ),
                        esc_html( date( 'Y' ) ),
                        esc_html( get_bloginfo( 'name' ) )
                    );
                }
                ?>
            </p>
        </div>
    </div>
</footer>
