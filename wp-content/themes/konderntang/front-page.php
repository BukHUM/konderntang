<?php
/**
 * The front page template
 *
 * @package KonDernTang
 * @since 1.0.0
 */

get_header();
?>

<main id="main" class="site-main">
    <?php
    // Hero Slider
    konderntang_get_component( 'hero-slider' );
    ?>

    <div class="container mx-auto px-4 py-12">
        <?php
        // Featured Section
        konderntang_get_component( 'featured-section' );

        // Category Sections
        konderntang_get_component( 'category-section', array( 'category' => 'travel-thailand', 'title' => esc_html__( 'เที่ยวทั่วไทย', 'konderntang' ) ) );
        konderntang_get_component( 'category-section', array( 'category' => 'travel-international', 'title' => esc_html__( 'เที่ยวต่างประเทศ', 'konderntang' ) ) );
        konderntang_get_component( 'category-section', array( 'category' => 'travel-seasonal', 'title' => esc_html__( 'เที่ยวตามฤดูกาล', 'konderntang' ) ) );

        // Recent Posts Grid
        konderntang_get_component( 'recent-posts-grid' );

        // Newsletter Section
        konderntang_get_component( 'newsletter-form' );

        // Trending Tags
        konderntang_get_component( 'trending-tags' );
        ?>
    </div>
</main>

<?php
get_footer();
