<?php
/**
 * Social Links Widget
 *
 * @package KonDernTang
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Social Links Widget Class
 */
class KonDernTang_Social_Links_Widget extends WP_Widget {

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct(
            'konderntang_social_links',
            esc_html__( 'KonDernTang: Social Links', 'konderntang' ),
            array(
                'description' => esc_html__( 'Display social media links with icons', 'konderntang' ),
            )
        );
    }

    /**
     * Widget output
     *
     * @param array $args Widget arguments.
     * @param array $instance Widget instance.
     */
    public function widget( $args, $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Follow Us', 'konderntang' );
        $icon_style = ! empty( $instance['icon_style'] ) ? $instance['icon_style'] : 'default';

        echo $args['before_widget'];

        if ( $title ) {
            echo $args['before_title'] . esc_html( $title ) . $args['after_title'];
        }

        $social_links = array(
            'facebook'  => ! empty( $instance['facebook'] ) ? esc_url( $instance['facebook'] ) : '',
            'twitter'   => ! empty( $instance['twitter'] ) ? esc_url( $instance['twitter'] ) : '',
            'instagram' => ! empty( $instance['instagram'] ) ? esc_url( $instance['instagram'] ) : '',
            'youtube'   => ! empty( $instance['youtube'] ) ? esc_url( $instance['youtube'] ) : '',
            'line'      => ! empty( $instance['line'] ) ? esc_url( $instance['line'] ) : '',
            'tiktok'    => ! empty( $instance['tiktok'] ) ? esc_url( $instance['tiktok'] ) : '',
        );

        $social_links = array_filter( $social_links );

        if ( ! empty( $social_links ) ) {
            echo '<div class="konderntang-social-links konderntang-social-links-' . esc_attr( $icon_style ) . '">';
            foreach ( $social_links as $platform => $url ) {
                if ( ! empty( $url ) ) {
                    $icon_class = 'konderntang-social-icon-' . $platform;
                    $platform_name = ucfirst( $platform );
                    ?>
                    <a href="<?php echo esc_url( $url ); ?>" 
                       class="konderntang-social-link konderntang-social-link-<?php echo esc_attr( $platform ); ?>" 
                       target="_blank" 
                       rel="noopener noreferrer"
                       aria-label="<?php echo esc_attr( sprintf( __( 'Follow us on %s', 'konderntang' ), $platform_name ) ); ?>">
                        <span class="<?php echo esc_attr( $icon_class ); ?>"></span>
                        <?php if ( 'text' === $icon_style ) : ?>
                            <span class="konderntang-social-text"><?php echo esc_html( $platform_name ); ?></span>
                        <?php endif; ?>
                    </a>
                    <?php
                }
            }
            echo '</div>';
        } else {
            echo '<p>' . esc_html__( 'No social links configured.', 'konderntang' ) . '</p>';
        }

        echo $args['after_widget'];
    }

    /**
     * Widget form
     *
     * @param array $instance Widget instance.
     */
    public function form( $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Follow Us', 'konderntang' );
        $icon_style = ! empty( $instance['icon_style'] ) ? $instance['icon_style'] : 'default';
        
        $social_fields = array(
            'facebook'  => ! empty( $instance['facebook'] ) ? $instance['facebook'] : '',
            'twitter'   => ! empty( $instance['twitter'] ) ? $instance['twitter'] : '',
            'instagram' => ! empty( $instance['instagram'] ) ? $instance['instagram'] : '',
            'youtube'   => ! empty( $instance['youtube'] ) ? $instance['youtube'] : '',
            'line'      => ! empty( $instance['line'] ) ? $instance['line'] : '',
            'tiktok'    => ! empty( $instance['tiktok'] ) ? $instance['tiktok'] : '',
        );
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
                <?php esc_html_e( 'Title:', 'konderntang' ); ?>
            </label>
            <input class="widefat" 
                   id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" 
                   name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" 
                   type="text" 
                   value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'icon_style' ) ); ?>">
                <?php esc_html_e( 'Icon style:', 'konderntang' ); ?>
            </label>
            <select class="widefat" 
                    id="<?php echo esc_attr( $this->get_field_id( 'icon_style' ) ); ?>" 
                    name="<?php echo esc_attr( $this->get_field_name( 'icon_style' ) ); ?>">
                <option value="default" <?php selected( $icon_style, 'default' ); ?>><?php esc_html_e( 'Icons only', 'konderntang' ); ?></option>
                <option value="text" <?php selected( $icon_style, 'text' ); ?>><?php esc_html_e( 'Icons with text', 'konderntang' ); ?></option>
                <option value="rounded" <?php selected( $icon_style, 'rounded' ); ?>><?php esc_html_e( 'Rounded icons', 'konderntang' ); ?></option>
            </select>
        </p>
        <?php
        foreach ( $social_fields as $platform => $value ) {
            $platform_label = ucfirst( $platform );
            ?>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( $platform ) ); ?>">
                    <?php echo esc_html( $platform_label ); ?> <?php esc_html_e( 'URL:', 'konderntang' ); ?>
                </label>
                <input class="widefat" 
                       id="<?php echo esc_attr( $this->get_field_id( $platform ) ); ?>" 
                       name="<?php echo esc_attr( $this->get_field_name( $platform ) ); ?>" 
                       type="url" 
                       value="<?php echo esc_attr( $value ); ?>">
            </p>
            <?php
        }
    }

    /**
     * Update widget
     *
     * @param array $new_instance New instance.
     * @param array $old_instance Old instance.
     * @return array Updated instance.
     */
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ! empty( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) : '';
        $instance['icon_style'] = ! empty( $new_instance['icon_style'] ) ? sanitize_text_field( $new_instance['icon_style'] ) : 'default';
        $instance['facebook'] = ! empty( $new_instance['facebook'] ) ? esc_url_raw( $new_instance['facebook'] ) : '';
        $instance['twitter'] = ! empty( $new_instance['twitter'] ) ? esc_url_raw( $new_instance['twitter'] ) : '';
        $instance['instagram'] = ! empty( $new_instance['instagram'] ) ? esc_url_raw( $new_instance['instagram'] ) : '';
        $instance['youtube'] = ! empty( $new_instance['youtube'] ) ? esc_url_raw( $new_instance['youtube'] ) : '';
        $instance['line'] = ! empty( $new_instance['line'] ) ? esc_url_raw( $new_instance['line'] ) : '';
        $instance['tiktok'] = ! empty( $new_instance['tiktok'] ) ? esc_url_raw( $new_instance['tiktok'] ) : '';
        return $instance;
    }
}
