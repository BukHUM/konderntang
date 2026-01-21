<?php
/**
 * The template for displaying single posts
 *
 * @package TrendToday
 * @since 1.0.0
 */

get_header();
?>

<!-- Progress Bar (Reading Progress) -->
<div class="h-1 bg-gray-200 w-full fixed top-16 z-40">
    <div class="h-full bg-accent w-0 transition-all duration-300" id="reading-progress"></div>
</div>

<main id="main-content" class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">

    <!-- Breadcrumb -->
    <?php get_template_part( 'template-parts/breadcrumb' ); ?>

    <div class="flex flex-col lg:flex-row gap-10">

        <!-- Article Content -->
        <article class="lg:w-2/3 overflow-hidden">

            <!-- Article Header -->
            <header class="mb-8">
                <?php
                $categories = get_the_category();
                if ( ! empty( $categories ) ) :
                    $category = $categories[0];
                    $cat_color = get_term_meta( $category->term_id, 'category_color', true ) ?: '#3B82F6';
                    ?>
                    <div class="flex items-center gap-2 mb-4">
                        <span class="text-white text-xs font-bold px-2 py-1 rounded uppercase"
                              style="background-color: <?php echo esc_attr( $cat_color ); ?>">
                            <?php echo esc_html( $category->name ); ?>
                        </span>
                        <span class="text-gray-500 text-sm">
                            <i class="far fa-clock mr-1"></i>
                            <?php echo human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) . ' ที่แล้ว'; ?>
                        </span>
                    </div>
                <?php endif; ?>

                <h1 class="text-xl md:text-3xl lg:text-4xl font-bold text-gray-900 leading-tight mb-6">
                    <?php the_title(); ?>
                </h1>

                <?php
                // Social Share Buttons - Top
                $display_positions = get_option( 'trendtoday_social_display_positions', array( 'single_bottom' ) );
                if ( in_array( 'single_top', $display_positions ) ) :
                ?>
                    <div class="mb-6">
                        <?php get_template_part( 'template-parts/social-share' ); ?>
                    </div>
                <?php endif; ?>

                <?php if ( has_excerpt() ) : ?>
                    <p class="text-xl text-gray-600 leading-relaxed font-light mb-6 border-l-4 border-accent pl-4">
                        <?php the_excerpt(); ?>
                    </p>
                <?php endif; ?>
            </header>

            <!-- Featured Image -->
            <?php if ( has_post_thumbnail() ) : ?>
                <figure class="mb-8 rounded-xl overflow-hidden shadow-lg">
                    <?php the_post_thumbnail( 'trendtoday-hero', array( 'class' => 'w-full h-auto object-cover' ) ); ?>
                    <?php if ( get_the_post_thumbnail_caption() ) : ?>
                        <figcaption class="text-center text-sm text-gray-500 mt-2 italic">
                            <?php the_post_thumbnail_caption(); ?>
                        </figcaption>
                    <?php endif; ?>
                </figure>
            <?php endif; ?>

            <?php
            // Table of Contents - Top
            $toc_enabled = get_option( 'trendtoday_toc_enabled', '1' );
            $toc_position = get_option( 'trendtoday_toc_position', 'top' );
            if ( $toc_enabled === '1' && $toc_position === 'top' ) :
            ?>
                <div class="mb-8">
                    <?php get_template_part( 'template-parts/table-of-contents' ); ?>
                </div>
            <?php endif; ?>

            <!-- Article Body -->
            <div class="prose prose-lg max-w-none text-gray-800 leading-relaxed overflow-hidden" id="article-content" data-toc-content="true">
                <div class="trendtoday-article-content">
                <?php
                the_content();

                wp_link_pages( array(
                    'before' => '<div class="page-links mt-8 pt-6 border-t border-gray-200">' . __( 'Pages:', 'trendtoday' ),
                    'after'  => '</div>',
                ) );
                ?>
                </div>
            </div>

            <?php
            // Social Share Buttons - Bottom
            $display_positions = get_option( 'trendtoday_social_display_positions', array( 'single_bottom' ) );
            if ( in_array( 'single_bottom', $display_positions ) ) :
            ?>
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <?php get_template_part( 'template-parts/social-share' ); ?>
                </div>
            <?php endif; ?>

            <!-- Tags -->
            <?php
            $tags = get_the_tags();
            if ( $tags ) :
                ?>
                <div class="mt-10 flex flex-wrap gap-2">
                    <?php foreach ( $tags as $tag ) : ?>
                        <a href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>"
                           class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-3 py-1 rounded-full text-sm transition">
                            #<?php echo esc_html( $tag->name ); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Related News -->
            <?php
            $related_posts = get_posts( array(
                'category__in'   => wp_get_post_categories( get_the_ID() ),
                'numberposts'    => 2,
                'post__not_in'   => array( get_the_ID() ),
            ) );

            if ( ! empty( $related_posts ) ) :
                ?>
                <div class="mt-16 border-t border-gray-100 pt-10">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6"><?php _e( 'ข่าวที่เกี่ยวข้อง', 'trendtoday' ); ?></h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <?php foreach ( $related_posts as $related_post ) : ?>
                            <a href="<?php echo esc_url( trendtoday_fix_url( get_permalink( $related_post->ID ) ) ); ?>" class="group">
                                <div class="overflow-hidden rounded-xl mb-3 h-48">
                                    <?php
                                    if ( has_post_thumbnail( $related_post->ID ) ) {
                                        echo get_the_post_thumbnail( $related_post->ID, 'trendtoday-card', array(
                                            'class' => 'w-full h-full object-cover transition duration-500 group-hover:scale-105',
                                        ) );
                                    }
                                    ?>
                                </div>
                                <h4 class="font-bold text-lg text-gray-900 group-hover:text-accent transition leading-snug">
                                    <?php echo esc_html( $related_post->post_title ); ?>
                                </h4>
                                <p class="text-gray-500 text-xs mt-2">
                                    <?php echo human_time_diff( get_the_time( 'U', $related_post->ID ), current_time( 'timestamp' ) ) . ' ที่แล้ว'; ?>
                                </p>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- After Content Widget Area -->
            <?php if ( is_active_sidebar( 'after-content' ) ) : ?>
                <div class="mt-12">
                    <?php dynamic_sidebar( 'after-content' ); ?>
                </div>
            <?php endif; ?>

            <!-- Comments -->
            <?php
            if ( comments_open() || get_comments_number() ) :
                comments_template();
            endif;
            ?>

        </article>

        <!-- Sidebar (different for single post) -->
        <?php get_template_part( 'template-parts/sidebar-single' ); ?>

    </div>
</main>

<script>
// Reading Progress Bar
(function() {
    const progressBar = document.getElementById('reading-progress');
    if (!progressBar) return;
    
    window.addEventListener('scroll', function() {
        const windowHeight = window.innerHeight;
        const documentHeight = document.documentElement.scrollHeight;
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        const scrollPercent = (scrollTop / (documentHeight - windowHeight)) * 100;
        progressBar.style.width = scrollPercent + '%';
    });
})();
</script>

<?php
get_footer();
?>
