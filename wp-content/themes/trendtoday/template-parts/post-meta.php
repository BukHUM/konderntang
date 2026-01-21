<?php
/**
 * Template part for displaying post meta information
 *
 * @package TrendToday
 * @since 1.0.0
 *
 * @param array $args {
 *     Optional. Array of arguments.
 *     @type string $show_date     Whether to show date. Default true.
 *     @type string $show_author   Whether to show author. Default true.
 *     @type string $show_category Whether to show category. Default true.
 *     @type string $show_comments Whether to show comments. Default false.
 * }
 */

$args = wp_parse_args( $args, array(
    'show_date'     => true,
    'show_author'   => true,
    'show_category' => true,
    'show_comments' => false,
) );
?>

<div class="post-meta flex flex-wrap items-center gap-4 text-sm text-gray-500">
    <?php if ( $args['show_date'] ) : ?>
        <span class="flex items-center">
            <i class="far fa-clock mr-1.5"></i>
            <?php echo human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) . ' ' . __( 'ที่แล้ว', 'trendtoday' ); ?>
        </span>
    <?php endif; ?>

    <?php if ( $args['show_author'] ) : ?>
        <span class="flex items-center">
            <i class="far fa-user mr-1.5"></i>
            <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" class="hover:text-accent">
                <?php the_author(); ?>
            </a>
        </span>
    <?php endif; ?>

    <?php if ( $args['show_category'] ) : ?>
        <?php
        $categories = get_the_category();
        if ( ! empty( $categories ) ) :
            $category = $categories[0];
            $cat_color = get_term_meta( $category->term_id, 'category_color', true ) ?: '#3B82F6';
            ?>
            <span class="flex items-center">
                <a href="<?php echo esc_url( get_category_link( $category->term_id ) ); ?>" 
                   class="text-white text-xs font-bold px-2 py-1 rounded uppercase hover:opacity-80 transition"
                   style="background-color: <?php echo esc_attr( $cat_color ); ?>">
                    <?php echo esc_html( $category->name ); ?>
                </a>
            </span>
        <?php endif; ?>
    <?php endif; ?>

    <?php
    $reading_time = get_post_meta( get_the_ID(), 'reading_time', true );
    if ( $reading_time ) :
        ?>
        <span class="flex items-center">
            <i class="far fa-clock mr-1.5"></i>
            <?php printf( __( 'อ่าน %d นาที', 'trendtoday' ), absint( $reading_time ) ); ?>
        </span>
    <?php endif; ?>

    <?php if ( $args['show_comments'] && comments_open() ) : ?>
        <span class="flex items-center">
            <i class="far fa-comment mr-1.5"></i>
            <a href="<?php comments_link(); ?>" class="hover:text-accent">
                <?php comments_number( __( '0 ความคิดเห็น', 'trendtoday' ), __( '1 ความคิดเห็น', 'trendtoday' ), __( '% ความคิดเห็น', 'trendtoday' ) ); ?>
            </a>
        </span>
    <?php endif; ?>
</div>
