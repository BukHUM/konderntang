<?php
/**
 * The main template file
 *
 * @package KonDernTang
 * @since 1.0.0
 */

get_header();
?>

<main id="main" class="site-main">
    <?php
    if ( have_posts() ) :
        while ( have_posts() ) :
            the_post();
            get_template_part( 'template-parts/content/content', get_post_format() );
        endwhile;
        
        get_template_part( 'template-parts/sections/pagination' );
    else :
        get_template_part( 'template-parts/content/content', 'none' );
    endif;
    ?>
</main>

<?php
get_sidebar();
get_footer();
