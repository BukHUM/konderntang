<?php
/**
 * Recently Viewed Widget
 *
 * @package KonDernTang
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Recently Viewed Widget Class
 */
class KonDernTang_Recently_Viewed_Widget extends WP_Widget {

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct(
            'konderntang_recently_viewed',
            esc_html__( 'KonDernTang: Recently Viewed', 'konderntang' ),
            array(
                'description' => esc_html__( 'Display recently viewed posts (using localStorage/cookies)', 'konderntang' ),
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
        $title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Recently Viewed', 'konderntang' );
        $number = ! empty( $instance['number'] ) ? absint( $instance['number'] ) : 5;
        $show_thumbnail = ! empty( $instance['show_thumbnail'] );

        echo $args['before_widget'];

        if ( $title ) {
            echo $args['before_title'] . esc_html( $title ) . $args['after_title'];
        }

        // Get recently viewed posts from cookie/localStorage (handled by JavaScript)
        // For server-side fallback, we'll use a cookie
        $recently_viewed_ids = array();
        if ( isset( $_COOKIE['konderntang_recently_viewed'] ) ) {
            $recently_viewed_ids = json_decode( stripslashes( $_COOKIE['konderntang_recently_viewed'] ), true );
            if ( ! is_array( $recently_viewed_ids ) ) {
                $recently_viewed_ids = array();
            }
            $recently_viewed_ids = array_slice( array_reverse( $recently_viewed_ids ), 0, $number );
        }

        if ( ! empty( $recently_viewed_ids ) ) {
            $query = new WP_Query( array(
                'post_type'      => 'post',
                'post__in'       => $recently_viewed_ids,
                'posts_per_page' => $number,
                'orderby'        => 'post__in',
                'post_status'    => 'publish',
            ) );

            if ( $query->have_posts() ) {
                echo '<ul class="konderntang-recently-viewed">';
                while ( $query->have_posts() ) {
                    $query->the_post();
                    ?>
                    <li class="konderntang-recently-viewed-item">
                        <a href="<?php echo esc_url( get_permalink() ); ?>" class="konderntang-recently-viewed-link">
                            <?php if ( $show_thumbnail && has_post_thumbnail() ) : ?>
                                <div class="konderntang-recently-viewed-thumbnail">
                                    <?php the_post_thumbnail( 'konderntang-card', array(
                                        'class' => 'konderntang-recently-viewed-image',
                                        'alt'   => get_the_title(),
                                    ) ); ?>
                                </div>
                            <?php endif; ?>
                            <div class="konderntang-recently-viewed-content">
                                <h4 class="konderntang-recently-viewed-title"><?php the_title(); ?></h4>
                            </div>
                        </a>
                    </li>
                    <?php
                }
                echo '</ul>';
                wp_reset_postdata();
            } else {
                echo '<p class="konderntang-recently-viewed-empty">' . esc_html__( 'No recently viewed posts.', 'konderntang' ) . '</p>';
            }
        } else {
            echo '<p class="konderntang-recently-viewed-empty">' . esc_html__( 'No recently viewed posts.', 'konderntang' ) . '</p>';
        }

        // Add JavaScript to handle localStorage
        ?>
        <script>
        (function() {
            // This will be handled by the main JavaScript file
            // The widget container has class 'konderntang-recently-viewed-widget'
            // JavaScript will populate it from localStorage
        })();
        </script>
        <?php

        echo $args['after_widget'];
    }

    /**
     * Widget form
     *
     * @param array $instance Widget instance.
     */
    public function form( $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Recently Viewed', 'konderntang' );
        $number = ! empty( $instance['number'] ) ? absint( $instance['number'] ) : 5;
        $show_thumbnail = ! empty( $instance['show_thumbnail'] );
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
            <label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>">
                <?php esc_html_e( 'Number of items:', 'konderntang' ); ?>
            </label>
            <input class="tiny-text" 
                   id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" 
                   name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" 
                   type="number" 
                   step="1" 
                   min="1" 
                   max="20"
                   value="<?php echo esc_attr( $number ); ?>">
        </p>
        <p>
            <input class="checkbox" 
                   type="checkbox" 
                   <?php checked( $show_thumbnail ); ?> 
                   id="<?php echo esc_attr( $this->get_field_id( 'show_thumbnail' ) ); ?>" 
                   name="<?php echo esc_attr( $this->get_field_name( 'show_thumbnail' ) ); ?>">
            <label for="<?php echo esc_attr( $this->get_field_id( 'show_thumbnail' ) ); ?>">
                <?php esc_html_e( 'Show thumbnail', 'konderntang' ); ?>
            </label>
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
        $instance['number'] = ! empty( $new_instance['number'] ) ? absint( $new_instance['number'] ) : 5;
        $instance['show_thumbnail'] = ! empty( $new_instance['show_thumbnail'] ) ? 1 : 0;
        return $instance;
    }
}
