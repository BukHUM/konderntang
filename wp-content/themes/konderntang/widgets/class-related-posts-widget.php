<?php
/**
 * Related Posts Widget
 *
 * @package KonDernTang
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Related Posts Widget Class
 */
class KonDernTang_Related_Posts_Widget extends WP_Widget
{

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct(
            'konderntang_related_posts',
            esc_html__('KonDernTang: Related Posts', 'konderntang'),
            array(
                'description' => esc_html__('Display related posts by category or tag', 'konderntang'),
            )
        );
    }

    /**
     * Widget output
     *
     * @param array $args Widget arguments.
     * @param array $instance Widget instance.
     */
    public function widget($args, $instance)
    {
        // Only show on single post pages
        if (!is_single()) {
            return;
        }

        $title = !empty($instance['title']) ? $instance['title'] : esc_html__('Related Posts', 'konderntang');
        $number = !empty($instance['number']) ? absint($instance['number']) : 5;
        $relation_type = !empty($instance['relation_type']) ? $instance['relation_type'] : 'category';
        $show_image = !empty($instance['show_image']);

        echo $args['before_widget'];

        if ($title) {
            echo $args['before_title'] . esc_html($title) . $args['after_title'];
        }

        global $post;
        $post_id = $post->ID;

        // Build query args
        $query_args = array(
            'post_type' => 'post',
            'posts_per_page' => $number,
            'post_status' => 'publish',
            'post__not_in' => array($post_id),
            'no_found_rows' => true,
        );

        if ('category' === $relation_type) {
            $categories = wp_get_post_categories($post_id);
            if (!empty($categories)) {
                $query_args['category__in'] = $categories;
            }
        } else {
            $tags = wp_get_post_tags($post_id, array('fields' => 'ids'));
            if (!empty($tags)) {
                $query_args['tag__in'] = $tags;
            }
        }

        $query = new WP_Query($query_args);

        if ($query->have_posts()) {
            echo '<ul class="konderntang-related-posts">';
            while ($query->have_posts()) {
                $query->the_post();
                ?>
                <li class="konderntang-related-post-item">
                    <a href="<?php echo esc_url(get_permalink()); ?>" class="konderntang-related-post-link">
                        <?php if ($show_image && has_post_thumbnail()): ?>
                            <div class="konderntang-related-post-thumbnail">
                                <?php the_post_thumbnail('konderntang-card', array(
                                    'class' => 'konderntang-related-post-image',
                                    'alt' => get_the_title(),
                                )); ?>
                            </div>
                        <?php endif; ?>
                        <div class="konderntang-related-post-content">
                            <h4 class="konderntang-related-post-title"><?php the_title(); ?></h4>
                            <span class="konderntang-related-post-date"><?php echo get_the_date(); ?></span>
                        </div>
                    </a>
                </li>
                <?php
            }
            echo '</ul>';
            wp_reset_postdata();
        } else {
            echo '<p>' . esc_html__('No related posts found.', 'konderntang') . '</p>';
        }

        echo $args['after_widget'];
    }

    /**
     * Widget form
     *
     * @param array $instance Widget instance.
     */
    public function form($instance)
    {
        $title = !empty($instance['title']) ? $instance['title'] : esc_html__('Related Posts', 'konderntang');
        $number = !empty($instance['number']) ? absint($instance['number']) : 5;
        $relation_type = !empty($instance['relation_type']) ? $instance['relation_type'] : 'category';
        $show_image = !empty($instance['show_image']);
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">
                <?php esc_html_e('Title:', 'konderntang'); ?>
            </label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>"
                name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text"
                value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('number')); ?>">
                <?php esc_html_e('Number of posts:', 'konderntang'); ?>
            </label>
            <input class="tiny-text" id="<?php echo esc_attr($this->get_field_id('number')); ?>"
                name="<?php echo esc_attr($this->get_field_name('number')); ?>" type="number" step="1" min="1" max="20"
                value="<?php echo esc_attr($number); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('relation_type')); ?>">
                <?php esc_html_e('Show related by:', 'konderntang'); ?>
            </label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('relation_type')); ?>"
                name="<?php echo esc_attr($this->get_field_name('relation_type')); ?>">
                <option value="category" <?php selected($relation_type, 'category'); ?>>
                    <?php esc_html_e('Category', 'konderntang'); ?></option>
                <option value="tag" <?php selected($relation_type, 'tag'); ?>><?php esc_html_e('Tag', 'konderntang'); ?>
                </option>
            </select>
        </p>
        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_image); ?>
                id="<?php echo esc_attr($this->get_field_id('show_image')); ?>"
                name="<?php echo esc_attr($this->get_field_name('show_image')); ?>">
            <label for="<?php echo esc_attr($this->get_field_id('show_image')); ?>">
                <?php esc_html_e('Show featured image', 'konderntang'); ?>
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
    public function update($new_instance, $old_instance)
    {
        $instance = array();
        $instance['title'] = !empty($new_instance['title']) ? sanitize_text_field($new_instance['title']) : '';
        $instance['number'] = !empty($new_instance['number']) ? absint($new_instance['number']) : 5;
        $instance['relation_type'] = !empty($new_instance['relation_type']) ? sanitize_text_field($new_instance['relation_type']) : 'category';
        $instance['show_image'] = !empty($new_instance['show_image']) ? 1 : 0;
        return $instance;
    }
}
