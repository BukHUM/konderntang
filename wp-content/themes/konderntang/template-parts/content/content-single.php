<?php
/**
 * Template part for displaying single post content
 *
 * @package KonDernTang
 * @since 1.0.0
 */

// Load social share component
require_once KONDERN_THEME_DIR . '/template-parts/components/social-share.php';
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <header class="entry-header">
        <?php
        if (has_post_thumbnail()) {
            ?>
            <div class="post-thumbnail">
                <?php the_post_thumbnail('konderntang-hero'); ?>
            </div>
            <?php
        }
        ?>

        <h1 class="entry-title"><?php the_title(); ?></h1>

        <div class="entry-meta">
            <span class="posted-on">
                <time datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                    <?php echo esc_html(get_the_date()); ?>
                </time>
            </span>
            <span class="byline">
                <?php esc_html_e('โดย', 'konderntang'); ?>
                <span class="author vcard">
                    <?php the_author(); ?>
                </span>
            </span>
        </div>
    </header>

    <?php
    // Social Share Buttons - Top Position
    konderntang_render_share_buttons('top');
    ?>

    <div class="entry-content">
        <?php
        // Get the content
        $content = get_the_content();

        // Check if content has paragraphs
        if (preg_match('/<p>(.*?)<\/p>/s', $content, $matches)) {
            // Extract first paragraph
            $first_paragraph = $matches[0];
            $remaining_content = str_replace($first_paragraph, '', $content);

            // Add lead class to first paragraph
            $first_paragraph = str_replace('<p>', '<p class="lead text-xl text-gray-600 font-light italic border-l-4 border-secondary pl-4">', $first_paragraph);

            // Output lead paragraph
            echo $first_paragraph;

            // Output remaining content
            echo apply_filters('the_content', $remaining_content);
        } else {
            // No paragraphs found, output content normally
            the_content();
        }

        wp_link_pages(
            array(
                'before' => '<div class="page-links">' . esc_html__('Pages:', 'konderntang'),
                'after' => '</div>',
            )
        );
        ?>
    </div>

    <?php
    // Social Share Buttons - Bottom Position
    konderntang_render_share_buttons('bottom');
    ?>

    <footer class="entry-footer">
        <?php
        $categories_list = get_the_category_list(', ');
        if ($categories_list) {
            printf('<span class="cat-links">%s</span>', $categories_list);
        }

        $tags_list = get_the_tag_list('', ', ');
        if ($tags_list) {
            printf('<span class="tags-links">%s</span>', $tags_list);
        }
        ?>
    </footer>
</article>

<?php
// Social Share Buttons - Floating Position
konderntang_render_floating_share();

// Comments
if (comments_open() || get_comments_number()) {
    comments_template();
}
