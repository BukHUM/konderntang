<?php
/**
 * Template part for displaying single post content
 *
 * @package KonDernTang
 * @since 1.0.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <header class="entry-header">
        <?php
        if ( has_post_thumbnail() ) {
            ?>
            <div class="post-thumbnail">
                <?php the_post_thumbnail( 'konderntang-hero' ); ?>
            </div>
            <?php
        }
        ?>

        <h1 class="entry-title"><?php the_title(); ?></h1>

        <div class="entry-meta">
            <span class="posted-on">
                <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
                    <?php echo esc_html( get_the_date() ); ?>
                </time>
            </span>
            <span class="byline">
                <?php esc_html_e( 'โดย', 'konderntang' ); ?> 
                <span class="author vcard">
                    <?php the_author(); ?>
                </span>
            </span>
        </div>
    </header>

    <div class="entry-content">
        <?php
        the_content();

        wp_link_pages(
            array(
                'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'konderntang' ),
                'after'  => '</div>',
            )
        );
        ?>
    </div>

    <footer class="entry-footer">
        <?php
        $categories_list = get_the_category_list( ', ' );
        if ( $categories_list ) {
            printf( '<span class="cat-links">%s</span>', $categories_list );
        }

        $tags_list = get_the_tag_list( '', ', ' );
        if ( $tags_list ) {
            printf( '<span class="tags-links">%s</span>', $tags_list );
        }
        ?>
    </footer>
</article>

<?php
// Comments
if ( comments_open() || get_comments_number() ) {
    comments_template();
}
