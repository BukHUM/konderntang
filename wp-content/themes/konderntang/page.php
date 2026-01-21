<?php
/**
 * The template for displaying all pages
 *
 * @package KonDernTang
 * @since 1.0.0
 */

get_header();
?>

<main id="main" class="site-main">
    <?php
    while ( have_posts() ) :
        the_post();
        ?>
        <div class="container mx-auto px-4 py-12">
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <header class="entry-header mb-8">
                    <h1 class="entry-title text-4xl font-heading font-bold text-dark mb-4">
                        <?php the_title(); ?>
                    </h1>
                </header>

                <div class="entry-content prose prose-lg max-w-none">
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

                <?php if ( comments_open() || get_comments_number() ) : ?>
                    <footer class="entry-footer mt-8">
                        <?php comments_template(); ?>
                    </footer>
                <?php endif; ?>
            </article>
        </div>
        <?php
    endwhile;
    ?>
</main>

<?php
get_sidebar();
get_footer();
