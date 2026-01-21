<?php
/**
 * Recent Posts Widget with Thumbnails
 *
 * @package TrendToday
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Recent Posts Widget Class
 */
class TrendToday_Recent_Posts_Widget extends WP_Widget {

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct(
            'trendtoday_recent_posts',
            __( 'Trend Today: Recent Posts', 'trendtoday' ),
            array(
                'description' => __( 'Display recent posts with thumbnails', 'trendtoday' ),
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
        $title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Recent Posts', 'trendtoday' );
        $number = ! empty( $instance['number'] ) ? absint( $instance['number'] ) : 5;
        $show_date = ! empty( $instance['show_date'] );

        echo $args['before_widget'];

        if ( $title ) {
            echo $args['before_title'] . esc_html( $title ) . $args['after_title'];
        }

        $query = new WP_Query( array(
            'post_type'      => 'post',
            'posts_per_page' => $number,
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
        ) );

        if ( $query->have_posts() ) {
            echo '<div class="space-y-4" role="list">';
            while ( $query->have_posts() ) {
                $query->the_post();
                ?>
                <a href="<?php echo esc_url( trendtoday_fix_url( get_permalink() ) ); ?>" 
                   class="popular-item flex gap-3 items-start group p-2 rounded-lg hover:bg-gray-50 transition-colors duration-200"
                   role="listitem">
                    <?php if ( has_post_thumbnail() ) : ?>
                        <div class="flex-shrink-0 w-20 h-20 rounded-lg overflow-hidden bg-gray-100">
                            <?php the_post_thumbnail( 'trendtoday-thumbnail', array(
                                'class' => 'w-full h-full object-cover group-hover:scale-105 transition-transform duration-300',
                                'alt'   => get_the_title(),
                            ) ); ?>
                        </div>
                    <?php else : ?>
                        <div class="flex-shrink-0 w-20 h-20 rounded-lg overflow-hidden bg-gray-200 flex items-center justify-center">
                            <i class="fas fa-image text-gray-400 text-2xl"></i>
                        </div>
                    <?php endif; ?>
                    <div class="flex-grow min-w-0">
                        <h4 class="text-sm font-medium text-gray-700 group-hover:text-accent transition-colors line-clamp-2 leading-snug">
                            <?php the_title(); ?>
                        </h4>
                        <?php if ( $show_date ) : ?>
                            <span class="text-xs text-gray-500 mt-1 block">
                                <?php echo human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) . ' ที่แล้ว'; ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </a>
                <?php
            }
            echo '</div>';
            wp_reset_postdata();
        } else {
            echo '<p>' . __( 'No recent posts found.', 'trendtoday' ) . '</p>';
        }

        echo $args['after_widget'];
    }

    /**
     * Widget form
     *
     * @param array $instance Widget instance.
     */
    public function form( $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Recent Posts', 'trendtoday' );
        $number = ! empty( $instance['number'] ) ? absint( $instance['number'] ) : 5;
        $show_date = ! empty( $instance['show_date'] ) ? 1 : 0;
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
            <input class="checkbox" 
                   type="checkbox" 
                   <?php checked( $show_date ); ?> 
                   id="<?php echo esc_attr( $this->get_field_id( 'show_date' ) ); ?>" 
                   name="<?php echo esc_attr( $this->get_field_name( 'show_date' ) ); ?>">
            <label for="<?php echo esc_attr( $this->get_field_id( 'show_date' ) ); ?>">
                <?php _e( 'Display post date', 'trendtoday' ); ?>
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
        $instance['show_date'] = ! empty( $new_instance['show_date'] ) ? 1 : 0;
        return $instance;
    }
}
