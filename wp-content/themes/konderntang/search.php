<?php
/**
 * The template for displaying search results
 *
 * @package KonDernTang
 * @since 1.0.0
 */

get_header();
?>

<main id="main" class="site-main">
    <div class="container mx-auto px-4 py-12">
        <header class="page-header mb-8">
            <h1 class="page-title text-4xl font-heading font-bold text-dark mb-4">
                <?php
                printf(
                    /* translators: %s: search query */
                    esc_html__( 'ผลการค้นหา: %s', 'konderntang' ),
                    '<span class="text-primary">' . get_search_query() . '</span>'
                );
                ?>
            </h1>
            <?php if ( have_posts() ) : ?>
                <p class="text-gray-600">
                    <?php
                    printf(
                        /* translators: %d: number of results */
                        esc_html__( 'พบ %d ผลลัพธ์', 'konderntang' ),
                        absint( $GLOBALS['wp_query']->found_posts )
                    );
                    ?>
                </p>
            <?php endif; ?>
        </header>

        <?php
        if ( have_posts() ) :
            ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <?php
                while ( have_posts() ) :
                    the_post();
                    konderntang_get_component( 'post-card', array( 'post' => $post ) );
                endwhile;
                ?>
            </div>

            <?php
            get_template_part( 'template-parts/sections/pagination' );
        else :
            get_template_part( 'template-parts/content/content', 'none' );
        endif;
        ?>
    </div>
</main>

<?php
get_sidebar();
get_footer();
