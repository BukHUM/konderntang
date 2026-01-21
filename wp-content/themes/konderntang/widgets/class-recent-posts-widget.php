<?php
/**
 * Recent Posts Widget
 *
 * @package KonDernTang
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Recent Posts Widget Class
 */
class KonDernTang_Recent_Posts_Widget extends WP_Widget
{

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct(
            'konderntang_recent_posts',
            esc_html__('KonDernTang: Recent Posts', 'konderntang'),
            array(
                'description' => esc_html__('Display recent posts with thumbnails, excerpt, date, and category', 'konderntang'),
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
        $title = !empty($instance['title']) ? $instance['title'] : esc_html__('Recent Posts', 'konderntang');
        $number = !empty($instance['number']) ? absint($instance['number']) : 5;
        $show_image = !empty($instance['show_image']);
        $show_excerpt = !empty($instance['show_excerpt']);
        $show_date = !empty($instance['show_date']);
        $show_category = !empty($instance['show_category']);

        echo $args['before_widget'];

        if ($title) {
            echo $args['before_title'] . esc_html($title) . $args['after_title'];
        }

        $query = new WP_Query(array(
            'post_type' => 'post',
            'posts_per_page' => $number,
            'post_status' => 'publish',
            'orderby' => 'date',
            'order' => 'DESC',
            'no_found_rows' => true,
        ));

        if ($query->have_posts()) {
            echo '<ul class="konderntang-recent-posts">';
            while ($query->have_posts()) {
                $query->the_post();
                ?>
                <li class="konderntang-recent-post-item">
                    <a href="<?php echo esc_url(get_permalink()); ?>" class="konderntang-recent-post-link">
                        <?php if ($show_image && has_post_thumbnail()): ?>
                            <div class="konderntang-recent-post-thumbnail">
                                <?php the_post_thumbnail('konderntang-card', array(
                                    'class' => 'konderntang-recent-post-image',
                                    'alt' => get_the_title(),
                                )); ?>
                            </div>
                        <?php endif; ?>
                        <div class="konderntang-recent-post-content">
                            <h4 class="konderntang-recent-post-title"><?php the_title(); ?></h4>
                            <?php if ($show_excerpt): ?>
                                <div class="konderntang-recent-post-excerpt">
                                    <?php echo wp_trim_words(get_the_excerpt(), 15, '...'); ?>
                                </div>
                            <?php endif; ?>
                            <div class="konderntang-recent-post-meta">
                                <?php if ($show_date): ?>
                                    <span class="konderntang-recent-post-date">
                                        <?php echo get_the_date(); ?>
                                    </span>
                                <?php endif; ?>
                                <?php if ($show_category): ?>
                                    <?php
                                    $categories = get_the_category();
                                    if (!empty($categories)):
                                        ?>
                                        <span class="konderntang-recent-post-category">
                                            <?php echo esc_html($categories[0]->name); ?>
                                        </span>
                                    <?php endif; ?>
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
            echo '<p>' . esc_html__('No recent posts found.', 'konderntang') . '</p>';
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
        $title = !empty($instance['title']) ? $instance['title'] : esc_html__('Recent Posts', 'konderntang');
        $number = !empty($instance['number']) ? absint($instance['number']) : 5;
        $show_image = !empty($instance['show_image']);
        $show_excerpt = !empty($instance['show_excerpt']);
        $show_date = !empty($instance['show_date']);
        $show_category = !empty($instance['show_category']);
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
            <input class="checkbox" type="checkbox" <?php checked($show_image); ?>
                id="<?php echo esc_attr($this->get_field_id('show_image')); ?>"
                name="<?php echo esc_attr($this->get_field_name('show_image')); ?>">
            <label for="<?php echo esc_attr($this->get_field_id('show_image')); ?>">
                <?php esc_html_e('Show featured image', 'konderntang'); ?>
            </label>
        </p>
        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_excerpt); ?>
                id="<?php echo esc_attr($this->get_field_id('show_excerpt')); ?>"
                name="<?php echo esc_attr($this->get_field_name('show_excerpt')); ?>">
            <label for="<?php echo esc_attr($this->get_field_id('show_excerpt')); ?>">
                <?php esc_html_e('Show excerpt', 'konderntang'); ?>
            </label>
        </p>
        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_date); ?>
                id="<?php echo esc_attr($this->get_field_id('show_date')); ?>"
                name="<?php echo esc_attr($this->get_field_name('show_date')); ?>">
            <label for="<?php echo esc_attr($this->get_field_id('show_date')); ?>">
                <?php esc_html_e('Show date', 'konderntang'); ?>
            </label>
        </p>
        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_category); ?>
                id="<?php echo esc_attr($this->get_field_id('show_category')); ?>"
                name="<?php echo esc_attr($this->get_field_name('show_category')); ?>">
            <label for="<?php echo esc_attr($this->get_field_id('show_category')); ?>">
                <?php esc_html_e('Show category', 'konderntang'); ?>
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
        $instance['show_image'] = !empty($new_instance['show_image']) ? 1 : 0;
        $instance['show_excerpt'] = !empty($new_instance['show_excerpt']) ? 1 : 0;
        $instance['show_date'] = !empty($new_instance['show_date']) ? 1 : 0;
        $instance['show_category'] = !empty($new_instance['show_category']) ? 1 : 0;
        return $instance;
    }
}
