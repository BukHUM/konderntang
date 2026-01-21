<?php
/**
 * Template for displaying single gallery posts
 *
 * @package TrendToday
 * @since 1.0.0
 */

get_header();
?>

<main id="main-content" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <?php
    while ( have_posts() ) :
        the_post();
        ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class( 'bg-white rounded-xl shadow-sm overflow-hidden' ); ?>>
            <!-- Breadcrumb -->
            <?php get_template_part( 'template-parts/breadcrumb' ); ?>

            <!-- Gallery Header -->
            <header class="mb-8">
                <?php
                $categories = get_the_terms( get_the_ID(), 'gallery_category' );
                if ( $categories && ! is_wp_error( $categories ) ) :
                    ?>
                    <div class="flex flex-wrap gap-2 mb-4">
                        <?php foreach ( $categories as $category ) : ?>
                            <a href="<?php echo esc_url( get_term_link( $category ) ); ?>" 
                               class="inline-block px-3 py-1 bg-accent text-white text-sm font-medium rounded-full hover:bg-orange-600 transition-colors">
                                <?php echo esc_html( $category->name ); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <h1 class="text-4xl font-bold text-gray-900 mb-4"><?php the_title(); ?></h1>

                <?php get_template_part( 'template-parts/post-meta' ); ?>
            </header>

            <!-- Gallery Images -->
            <div class="mb-8">
                <?php
                $gallery_images = get_post_meta( get_the_ID(), 'gallery_images', true );
                $gallery_images = is_array( $gallery_images ) ? $gallery_images : array();

                if ( ! empty( $gallery_images ) ) :
                    ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="gallery-grid">
                        <?php foreach ( $gallery_images as $image_id ) : ?>
                            <?php
                            $image = trendtoday_get_attachment_image_src( $image_id, 'large' );
                            $full_image = trendtoday_get_attachment_image_src( $image_id, 'full' );
                            if ( $image ) :
                                ?>
                                <a href="<?php echo esc_url( $full_image[0] ); ?>" 
                                   class="gallery-item block overflow-hidden rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300"
                                   data-lightbox="gallery">
                                    <img src="<?php echo esc_url( $image[0] ); ?>" 
                                         alt="<?php echo esc_attr( get_the_title( $image_id ) ); ?>"
                                         class="w-full h-64 object-cover hover:scale-105 transition-transform duration-300">
                                </a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php elseif ( has_post_thumbnail() ) : ?>
                    <div class="rounded-lg overflow-hidden">
                        <?php the_post_thumbnail( 'large', array( 'class' => 'w-full h-auto' ) ); ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Content -->
            <?php if ( get_the_content() ) : ?>
                <div class="prose prose-lg max-w-none mb-8">
                    <?php the_content(); ?>
                </div>
            <?php endif; ?>

            <!-- Tags -->
            <?php
            $tags = get_the_tags();
            if ( $tags ) :
                ?>
                <div class="mb-8">
                    <h3 class="text-lg font-semibold mb-3"><?php _e( 'Tags:', 'trendtoday' ); ?></h3>
                    <div class="flex flex-wrap gap-2">
                        <?php foreach ( $tags as $tag ) : ?>
                            <a href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>" 
                               class="inline-block px-3 py-1 bg-gray-100 text-gray-700 text-sm rounded-full hover:bg-gray-200 transition-colors">
                                #<?php echo esc_html( $tag->name ); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Related Galleries -->
            <?php
            $related_galleries = get_posts( array(
                'post_type'      => 'gallery',
                'posts_per_page' => 3,
                'post__not_in'   => array( get_the_ID() ),
                'orderby'         => 'rand',
            ) );

            if ( $related_galleries ) :
                ?>
                <div class="mt-12">
                    <h2 class="text-2xl font-bold mb-6"><?php _e( 'Related Galleries', 'trendtoday' ); ?></h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <?php foreach ( $related_galleries as $related_gallery ) : ?>
                            <?php
                            setup_postdata( $related_gallery );
                            get_template_part( 'template-parts/news-card' );
                            ?>
                        <?php endforeach; ?>
                        <?php wp_reset_postdata(); ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Comments -->
            <?php
            if ( comments_open() || get_comments_number() ) :
                comments_template();
            endif;
            ?>
        </article>
        <?php
    endwhile;
    ?>
</main>

<?php
get_sidebar();
get_footer();
