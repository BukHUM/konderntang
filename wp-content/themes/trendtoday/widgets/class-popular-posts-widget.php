<?php
/**
 * Popular Posts Widget
 *
 * @package TrendToday
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Popular Posts Widget Class
 */
class TrendToday_Popular_Posts_Widget extends WP_Widget {

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct(
            'trendtoday_popular_posts',
            __( 'Trend Today: Popular Posts', 'trendtoday' ),
            array(
                'description' => __( 'Display popular posts based on views or comments', 'trendtoday' ),
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
        $title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'ยอดนิยม', 'trendtoday' );
        $number = ! empty( $instance['number'] ) ? absint( $instance['number'] ) : 4;
        $orderby = ! empty( $instance['orderby'] ) ? $instance['orderby'] : 'views';

        echo $args['before_widget'];

        if ( $title ) {
            echo $args['before_title'];
            echo '<i class="fas fa-fire text-accent"></i> ';
            echo esc_html( $title );
            echo $args['after_title'];
        }

        $query = trendtoday_get_popular_posts( $number, $orderby );

        if ( $query->have_posts() ) {
            echo '<div class="space-y-4" role="list" aria-label="' . esc_attr__( 'Popular articles', 'trendtoday' ) . '">';
            $index = 0;
            while ( $query->have_posts() ) {
                $query->the_post();
                $index++;
                $post_title = get_the_title();
                
                // Skip if no title
                if ( empty( $post_title ) ) {
                    continue;
                }
                ?>
                <a href="<?php echo esc_url( trendtoday_fix_url( get_permalink() ) ); ?>" 
                   class="popular-item flex gap-4 items-start group p-2 rounded-lg hover:bg-gray-50 transition-colors duration-200"
                   role="listitem"
                   aria-label="<?php echo esc_attr( sprintf( __( 'Popular article %d: %s', 'trendtoday' ), $index, $post_title ) ); ?>">
                    <span class="popular-number text-2xl font-bold text-gray-200 group-hover:text-accent transition-all duration-300 flex-shrink-0">
                        <?php echo str_pad( $index, 2, '0', STR_PAD_LEFT ); ?>
                    </span>
                    <div class="flex-grow">
                        <h4 class="text-sm font-medium text-gray-700 group-hover:text-accent transition-colors line-clamp-2 leading-snug">
                            <?php echo esc_html( $post_title ); ?>
                        </h4>
                    </div>
                </a>
                <?php
            }
            echo '</div>';
            wp_reset_postdata();
        } else {
            echo '<p>' . __( 'No popular posts found.', 'trendtoday' ) . '</p>';
        }

        echo $args['after_widget'];
    }

    /**
     * Widget form
     *
     * @param array $instance Widget instance.
     */
    public function form( $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'ยอดนิยม', 'trendtoday' );
        $number = ! empty( $instance['number'] ) ? absint( $instance['number'] ) : 4;
        $orderby = ! empty( $instance['orderby'] ) ? $instance['orderby'] : 'views';
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
            <label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>">
                <?php _e( 'Number of posts:', 'trendtoday' ); ?>
            </label>
            <input class="tiny-text" 
                   id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" 
                   name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" 
                   type="number" 
                   step="1" 
                   min="1" 
                   value="<?php echo esc_attr( $number ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'orderby' ) ); ?>">
                <?php _e( 'Order by:', 'trendtoday' ); ?>
            </label>
            <select class="widefat" 
                    id="<?php echo esc_attr( $this->get_field_id( 'orderby' ) ); ?>" 
                    name="<?php echo esc_attr( $this->get_field_name( 'orderby' ) ); ?>">
                <option value="views" <?php selected( $orderby, 'views' ); ?>><?php _e( 'Views', 'trendtoday' ); ?></option>
                <option value="comments" <?php selected( $orderby, 'comments' ); ?>><?php _e( 'Comments', 'trendtoday' ); ?></option>
                <option value="date" <?php selected( $orderby, 'date' ); ?>><?php _e( 'Date', 'trendtoday' ); ?></option>
            </select>
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
        $instance['number'] = ! empty( $new_instance['number'] ) ? absint( $new_instance['number'] ) : 4;
        $instance['orderby'] = ! empty( $new_instance['orderby'] ) ? sanitize_text_field( $new_instance['orderby'] ) : 'views';
        return $instance;
    }
}
