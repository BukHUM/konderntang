<?php
/**
 * Trending Tags Widget
 *
 * @package KonDernTang
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Trending Tags Widget Class
 */
class KonDernTang_Trending_Tags_Widget extends WP_Widget {

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct(
            'konderntang_trending_tags',
            esc_html__( 'KonDernTang: Trending Tags', 'konderntang' ),
            array(
                'description' => esc_html__( 'Display trending tags/hashtags', 'konderntang' ),
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
        $title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Trending Tags', 'konderntang' );
        $number = ! empty( $instance['number'] ) ? absint( $instance['number'] ) : 10;
        $show_count = ! empty( $instance['show_count'] );

        echo $args['before_widget'];

        if ( $title ) {
            echo $args['before_title'] . esc_html( $title ) . $args['after_title'];
        }

        $tags = get_tags( array(
            'orderby' => 'count',
            'order'   => 'DESC',
            'number'  => $number,
            'hide_empty' => true,
        ) );

        if ( ! empty( $tags ) && ! is_wp_error( $tags ) ) {
            echo '<div class="konderntang-trending-tags">';
            foreach ( $tags as $tag ) {
                ?>
                <a href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>" 
                   class="konderntang-trending-tag">
                    #<?php echo esc_html( $tag->name ); ?>
                    <?php if ( $show_count ) : ?>
                        <span class="konderntang-trending-tag-count">(<?php echo esc_html( $tag->count ); ?>)</span>
                    <?php endif; ?>
                </a>
                <?php
            }
            echo '</div>';
        } else {
            echo '<p>' . esc_html__( 'No tags found.', 'konderntang' ) . '</p>';
        }

        echo $args['after_widget'];
    }

    /**
     * Widget form
     *
     * @param array $instance Widget instance.
     */
    public function form( $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Trending Tags', 'konderntang' );
        $number = ! empty( $instance['number'] ) ? absint( $instance['number'] ) : 10;
        $show_count = ! empty( $instance['show_count'] );
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
                <?php esc_html_e( 'Number of tags:', 'konderntang' ); ?>
            </label>
            <input class="tiny-text" 
                   id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" 
                   name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" 
                   type="number" 
                   step="1" 
                   min="1" 
                   max="30"
                   value="<?php echo esc_attr( $number ); ?>">
        </p>
        <p>
            <input class="checkbox" 
                   type="checkbox" 
                   <?php checked( $show_count ); ?> 
                   id="<?php echo esc_attr( $this->get_field_id( 'show_count' ) ); ?>" 
                   name="<?php echo esc_attr( $this->get_field_name( 'show_count' ) ); ?>">
            <label for="<?php echo esc_attr( $this->get_field_id( 'show_count' ) ); ?>">
                <?php esc_html_e( 'Show tag count', 'konderntang' ); ?>
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
        $instance['number'] = ! empty( $new_instance['number'] ) ? absint( $new_instance['number'] ) : 10;
        $instance['show_count'] = ! empty( $new_instance['show_count'] ) ? 1 : 0;
        return $instance;
    }
}
