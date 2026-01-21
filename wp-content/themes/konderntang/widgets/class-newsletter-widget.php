<?php
/**
 * Newsletter Widget
 *
 * @package KonDernTang
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Newsletter Widget Class
 */
class KonDernTang_Newsletter_Widget extends WP_Widget {

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct(
            'konderntang_newsletter',
            esc_html__( 'KonDernTang: Newsletter', 'konderntang' ),
            array(
                'description' => esc_html__( 'Display newsletter subscription form', 'konderntang' ),
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
        $title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Newsletter', 'konderntang' );
        $description = ! empty( $instance['description'] ) ? $instance['description'] : '';
        $button_text = ! empty( $instance['button_text'] ) ? $instance['button_text'] : esc_html__( 'Subscribe', 'konderntang' );
        $service = ! empty( $instance['service'] ) ? $instance['service'] : 'default';
        $api_key = ! empty( $instance['api_key'] ) ? $instance['api_key'] : '';
        $list_id = ! empty( $instance['list_id'] ) ? $instance['list_id'] : '';

        echo $args['before_widget'];

        if ( $title ) {
            echo $args['before_title'] . esc_html( $title ) . $args['after_title'];
        }

        if ( $description ) {
            echo '<p class="konderntang-newsletter-description">' . esc_html( $description ) . '</p>';
        }

        // Show success/error messages
        if ( isset( $_GET['newsletter'] ) ) {
            if ( 'success' === $_GET['newsletter'] ) {
                echo '<div class="konderntang-newsletter-message konderntang-newsletter-success">';
                echo esc_html__( 'Thank you for subscribing!', 'konderntang' );
                echo '</div>';
            } elseif ( 'error' === $_GET['newsletter'] ) {
                echo '<div class="konderntang-newsletter-message konderntang-newsletter-error">';
                echo esc_html__( 'There was an error. Please try again.', 'konderntang' );
                echo '</div>';
            }
        }
        ?>
        <form class="konderntang-newsletter-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
            <?php wp_nonce_field( 'konderntang_newsletter_subscribe', 'newsletter_nonce' ); ?>
            <input type="hidden" name="action" value="konderntang_newsletter_subscribe" />
            <input type="hidden" name="service" value="<?php echo esc_attr( $service ); ?>" />
            <input type="hidden" name="api_key" value="<?php echo esc_attr( $api_key ); ?>" />
            <input type="hidden" name="list_id" value="<?php echo esc_attr( $list_id ); ?>" />
            
            <div class="konderntang-newsletter-input-group">
                <input type="email" 
                       name="email" 
                       class="konderntang-newsletter-email" 
                       placeholder="<?php esc_attr_e( 'Enter your email', 'konderntang' ); ?>" 
                       required />
                <button type="submit" class="konderntang-newsletter-submit">
                    <?php echo esc_html( $button_text ); ?>
                </button>
            </div>
        </form>
        <?php

        echo $args['after_widget'];
    }

    /**
     * Widget form
     *
     * @param array $instance Widget instance.
     */
    public function form( $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Newsletter', 'konderntang' );
        $description = ! empty( $instance['description'] ) ? $instance['description'] : '';
        $button_text = ! empty( $instance['button_text'] ) ? $instance['button_text'] : esc_html__( 'Subscribe', 'konderntang' );
        $service = ! empty( $instance['service'] ) ? $instance['service'] : 'default';
        $api_key = ! empty( $instance['api_key'] ) ? $instance['api_key'] : '';
        $list_id = ! empty( $instance['list_id'] ) ? $instance['list_id'] : '';
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
            <label for="<?php echo esc_attr( $this->get_field_id( 'description' ) ); ?>">
                <?php esc_html_e( 'Description:', 'konderntang' ); ?>
            </label>
            <textarea class="widefat" 
                      id="<?php echo esc_attr( $this->get_field_id( 'description' ) ); ?>" 
                      name="<?php echo esc_attr( $this->get_field_name( 'description' ) ); ?>" 
                      rows="3"><?php echo esc_textarea( $description ); ?></textarea>
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'button_text' ) ); ?>">
                <?php esc_html_e( 'Button text:', 'konderntang' ); ?>
            </label>
            <input class="widefat" 
                   id="<?php echo esc_attr( $this->get_field_id( 'button_text' ) ); ?>" 
                   name="<?php echo esc_attr( $this->get_field_name( 'button_text' ) ); ?>" 
                   type="text" 
                   value="<?php echo esc_attr( $button_text ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'service' ) ); ?>">
                <?php esc_html_e( 'Email service:', 'konderntang' ); ?>
            </label>
            <select class="widefat" 
                    id="<?php echo esc_attr( $this->get_field_id( 'service' ) ); ?>" 
                    name="<?php echo esc_attr( $this->get_field_name( 'service' ) ); ?>">
                <option value="default" <?php selected( $service, 'default' ); ?>><?php esc_html_e( 'Default (Store in WordPress)', 'konderntang' ); ?></option>
                <option value="mailchimp" <?php selected( $service, 'mailchimp' ); ?>><?php esc_html_e( 'Mailchimp', 'konderntang' ); ?></option>
            </select>
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'api_key' ) ); ?>">
                <?php esc_html_e( 'API Key (optional):', 'konderntang' ); ?>
            </label>
            <input class="widefat" 
                   id="<?php echo esc_attr( $this->get_field_id( 'api_key' ) ); ?>" 
                   name="<?php echo esc_attr( $this->get_field_name( 'api_key' ) ); ?>" 
                   type="text" 
                   value="<?php echo esc_attr( $api_key ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'list_id' ) ); ?>">
                <?php esc_html_e( 'List ID (optional):', 'konderntang' ); ?>
            </label>
            <input class="widefat" 
                   id="<?php echo esc_attr( $this->get_field_id( 'list_id' ) ); ?>" 
                   name="<?php echo esc_attr( $this->get_field_name( 'list_id' ) ); ?>" 
                   type="text" 
                   value="<?php echo esc_attr( $list_id ); ?>">
        </p>
        <?php
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
        $instance['description'] = ! empty( $new_instance['description'] ) ? sanitize_textarea_field( $new_instance['description'] ) : '';
        $instance['button_text'] = ! empty( $new_instance['button_text'] ) ? sanitize_text_field( $new_instance['button_text'] ) : '';
        $instance['service'] = ! empty( $new_instance['service'] ) ? sanitize_text_field( $new_instance['service'] ) : 'default';
        $instance['api_key'] = ! empty( $new_instance['api_key'] ) ? sanitize_text_field( $new_instance['api_key'] ) : '';
        $instance['list_id'] = ! empty( $new_instance['list_id'] ) ? sanitize_text_field( $new_instance['list_id'] ) : '';
        return $instance;
    }
}
