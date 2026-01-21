<?php
/**
 * Newsletter Widget
 *
 * @package TrendToday
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Newsletter Widget Class
 */
class TrendToday_Newsletter_Widget extends WP_Widget {

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct(
            'trendtoday_newsletter',
            __( 'Trend Today: Newsletter', 'trendtoday' ),
            array(
                'description' => __( 'Newsletter subscription form', 'trendtoday' ),
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
        $title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'ไม่พลาดทุกเทรนด์', 'trendtoday' );
        $description = ! empty( $instance['description'] ) ? $instance['description'] : __( 'สมัครรับข่าวสารสรุปประจำวันส่งตรงถึงอีเมลของคุณ', 'trendtoday' );
        $button_text = ! empty( $instance['button_text'] ) ? $instance['button_text'] : __( 'ติดตาม', 'trendtoday' );

        echo $args['before_widget'];

        ?>
        <div class="bg-gradient-to-br from-gray-900 to-gray-800 text-white p-6 rounded-xl relative overflow-hidden shadow-lg">
            <div class="relative z-10">
                <?php if ( $title ) : ?>
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fas fa-envelope text-accent text-xl"></i>
                        <h3 class="font-bold text-xl"><?php echo esc_html( $title ); ?></h3>
                    </div>
                <?php endif; ?>
                
                <?php if ( $description ) : ?>
                    <p class="text-gray-300 text-sm mb-4 leading-relaxed">
                        <?php echo esc_html( $description ); ?>
                    </p>
                <?php endif; ?>
                
                <form class="newsletter-form space-y-3" 
                      action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" 
                      method="post"
                      aria-label="<?php _e( 'Newsletter subscription', 'trendtoday' ); ?>">
                    <input type="hidden" name="action" value="trendtoday_newsletter_subscribe">
                    <?php wp_nonce_field( 'trendtoday_newsletter', 'newsletter_nonce' ); ?>
                    
                    <input type="email" 
                           name="email" 
                           placeholder="<?php _e( 'ใส่อีเมลของคุณ', 'trendtoday' ); ?>" 
                           required
                           aria-label="<?php _e( 'Email address', 'trendtoday' ); ?>"
                           class="newsletter-input w-full px-4 py-3 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-accent transition-all duration-200">
                    
                    <button type="submit"
                            class="w-full bg-accent hover:bg-orange-600 text-white font-bold py-3 rounded-lg transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        <i class="fas fa-paper-plane mr-2"></i><?php echo esc_html( $button_text ); ?>
                    </button>
                </form>
            </div>
            <!-- Decorative elements -->
            <div class="absolute -bottom-10 -right-10 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
            <div class="absolute -top-10 -left-10 w-24 h-24 bg-accent/20 rounded-full blur-xl"></div>
        </div>
        <?php

        echo $args['after_widget'];
    }

    /**
     * Widget form
     *
     * @param array $instance Widget instance.
     */
    public function form( $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'ไม่พลาดทุกเทรนด์', 'trendtoday' );
        $description = ! empty( $instance['description'] ) ? $instance['description'] : __( 'สมัครรับข่าวสารสรุปประจำวันส่งตรงถึงอีเมลของคุณ', 'trendtoday' );
        $button_text = ! empty( $instance['button_text'] ) ? $instance['button_text'] : __( 'ติดตาม', 'trendtoday' );
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
                <?php _e( 'Title:', 'trendtoday' ); ?>
            </label>
            <input class="widefat" 
                   id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" 
                   name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" 
                   type="text" 
                   value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'description' ) ); ?>">
                <?php _e( 'Description:', 'trendtoday' ); ?>
            </label>
            <textarea class="widefat" 
                      id="<?php echo esc_attr( $this->get_field_id( 'description' ) ); ?>" 
                      name="<?php echo esc_attr( $this->get_field_name( 'description' ) ); ?>" 
                      rows="3"><?php echo esc_textarea( $description ); ?></textarea>
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'button_text' ) ); ?>">
                <?php _e( 'Button Text:', 'trendtoday' ); ?>
            </label>
            <input class="widefat" 
                   id="<?php echo esc_attr( $this->get_field_id( 'button_text' ) ); ?>" 
                   name="<?php echo esc_attr( $this->get_field_name( 'button_text' ) ); ?>" 
                   type="text" 
                   value="<?php echo esc_attr( $button_text ); ?>">
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
        return $instance;
    }
}
