<?php
/**
 * Popular Posts Widget
 *
 * @package KonDernTang
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Popular Posts Widget Class
 */
class KonDernTang_Popular_Posts_Widget extends WP_Widget {

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct(
            'konderntang_popular_posts',
            esc_html__( 'KonDernTang: Popular Posts', 'konderntang' ),
            array(
                'description' => esc_html__( 'Display popular posts based on views or comments', 'konderntang' ),
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
        $title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Popular Posts', 'konderntang' );
        $number = ! empty( $instance['number'] ) ? absint( $instance['number'] ) : 5;
        $orderby = ! empty( $instance['orderby'] ) ? $instance['orderby'] : 'comments';
        $time_period = ! empty( $instance['time_period'] ) ? $instance['time_period'] : 'all';

        echo $args['before_widget'];

        if ( $title ) {
            echo $args['before_title'] . esc_html( $title ) . $args['after_title'];
        }

        // Build query args based on orderby
        $query_args = array(
            'post_type'      => 'post',
            'posts_per_page' => $number,
            'post_status'    => 'publish',
        );

        // Time period filter
        if ( 'all' !== $time_period ) {
            $date_query = array();
            switch ( $time_period ) {
                case 'today':
                    $date_query = array(
                        'after' => '1 day ago',
                    );
                    break;
                case 'week':
                    $date_query = array(
                        'after' => '1 week ago',
                    );
                    break;
                case 'month':
                    $date_query = array(
                        'after' => '1 month ago',
                    );
                    break;
            }
            if ( ! empty( $date_query ) ) {
                $query_args['date_query'] = array( $date_query );
            }
        }

        // Order by
        switch ( $orderby ) {
            case 'comments':
                $query_args['orderby'] = 'comment_count';
                $query_args['order'] = 'DESC';
                break;
            case 'views':
                // If using post views plugin, use meta_key
                $query_args['meta_key'] = 'post_views_count';
                $query_args['orderby'] = 'meta_value_num';
                $query_args['order'] = 'DESC';
                break;
            case 'date':
                $query_args['orderby'] = 'date';
                $query_args['order'] = 'DESC';
                break;
        }

        $query = new WP_Query( $query_args );

        if ( $query->have_posts() ) {
            echo '<ul class="konderntang-popular-posts">';
            $index = 0;
            while ( $query->have_posts() ) {
                $query->the_post();
                $index++;
                ?>
                <li class="konderntang-popular-post-item">
                    <a href="<?php echo esc_url( get_permalink() ); ?>" class="konderntang-popular-post-link">
                        <span class="konderntang-popular-post-number"><?php echo esc_html( $index ); ?></span>
                        <div class="konderntang-popular-post-content">
                            <h4 class="konderntang-popular-post-title"><?php the_title(); ?></h4>
                            <div class="konderntang-popular-post-meta">
                                <?php if ( 'comments' === $orderby ) : ?>
                                    <span class="konderntang-popular-post-count">
                                        <?php echo esc_html( get_comments_number() ); ?> <?php esc_html_e( 'comments', 'konderntang' ); ?>
                                    </span>
                                <?php elseif ( 'views' === $orderby ) : ?>
                                    <span class="konderntang-popular-post-count">
                                        <?php
                                        $views = get_post_meta( get_the_ID(), 'post_views_count', true );
                                        echo esc_html( $views ? $views : 0 );
                                        ?> <?php esc_html_e( 'views', 'konderntang' ); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </a>
                </li>
                <?php
            }
            echo '</ul>';
            wp_reset_postdata();
        } else {
            echo '<p>' . esc_html__( 'No popular posts found.', 'konderntang' ) . '</p>';
        }

        echo $args['after_widget'];
    }

    /**
     * Widget form
     *
     * @param array $instance Widget instance.
     */
    public function form( $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Popular Posts', 'konderntang' );
        $number = ! empty( $instance['number'] ) ? absint( $instance['number'] ) : 5;
        $orderby = ! empty( $instance['orderby'] ) ? $instance['orderby'] : 'comments';
        $time_period = ! empty( $instance['time_period'] ) ? $instance['time_period'] : 'all';
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
                <?php esc_html_e( 'Number of posts:', 'konderntang' ); ?>
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
            <label for="<?php echo esc_attr( $this->get_field_id( 'orderby' ) ); ?>">
                <?php esc_html_e( 'Order by:', 'konderntang' ); ?>
            </label>
            <select class="widefat" 
                    id="<?php echo esc_attr( $this->get_field_id( 'orderby' ) ); ?>" 
                    name="<?php echo esc_attr( $this->get_field_name( 'orderby' ) ); ?>">
                <option value="comments" <?php selected( $orderby, 'comments' ); ?>><?php esc_html_e( 'Comments', 'konderntang' ); ?></option>
                <option value="views" <?php selected( $orderby, 'views' ); ?>><?php esc_html_e( 'Views', 'konderntang' ); ?></option>
                <option value="date" <?php selected( $orderby, 'date' ); ?>><?php esc_html_e( 'Date', 'konderntang' ); ?></option>
            </select>
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'time_period' ) ); ?>">
                <?php esc_html_e( 'Time period:', 'konderntang' ); ?>
            </label>
            <select class="widefat" 
                    id="<?php echo esc_attr( $this->get_field_id( 'time_period' ) ); ?>" 
                    name="<?php echo esc_attr( $this->get_field_name( 'time_period' ) ); ?>">
                <option value="all" <?php selected( $time_period, 'all' ); ?>><?php esc_html_e( 'All time', 'konderntang' ); ?></option>
                <option value="today" <?php selected( $time_period, 'today' ); ?>><?php esc_html_e( 'Today', 'konderntang' ); ?></option>
                <option value="week" <?php selected( $time_period, 'week' ); ?>><?php esc_html_e( 'This week', 'konderntang' ); ?></option>
                <option value="month" <?php selected( $time_period, 'month' ); ?>><?php esc_html_e( 'This month', 'konderntang' ); ?></option>
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
        $instance['number'] = ! empty( $new_instance['number'] ) ? absint( $new_instance['number'] ) : 5;
        $instance['orderby'] = ! empty( $new_instance['orderby'] ) ? sanitize_text_field( $new_instance['orderby'] ) : 'comments';
        $instance['time_period'] = ! empty( $new_instance['time_period'] ) ? sanitize_text_field( $new_instance['time_period'] ) : 'all';
        return $instance;
    }
}
