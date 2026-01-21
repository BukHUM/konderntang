<?php
/**
 * Template for displaying single video news posts
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

            <!-- Video Header -->
            <header class="mb-8">
                <?php
                $categories = get_the_terms( get_the_ID(), 'video_category' );
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

            <!-- Video Player -->
            <div class="mb-8">
                <?php
                $video_url = get_post_meta( get_the_ID(), 'video_url', true );
                $video_embed_code = get_post_meta( get_the_ID(), 'video_embed_code', true );

                if ( $video_embed_code ) {
                    echo '<div class="aspect-video bg-gray-900 rounded-lg overflow-hidden">';
                    echo wp_kses_post( $video_embed_code );
                    echo '</div>';
                } elseif ( $video_url ) {
                    // Try to embed video from URL
                    echo '<div class="aspect-video bg-gray-900 rounded-lg overflow-hidden">';
                    echo wp_oembed_get( $video_url );
                    echo '</div>';
                } elseif ( has_post_thumbnail() ) {
                    the_post_thumbnail( 'large', array( 'class' => 'w-full h-auto rounded-lg' ) );
                }
                ?>
            </div>

            <!-- Content -->
            <div class="prose prose-lg max-w-none mb-8">
                <?php the_content(); ?>
            </div>

            <!-- Video Meta -->
            <?php
            $video_duration = get_post_meta( get_the_ID(), 'video_duration', true );
            if ( $video_duration ) :
                ?>
                <div class="flex items-center gap-4 text-sm text-gray-500 mb-8">
                    <span>
                        <i class="fas fa-clock mr-2"></i>
                        <?php echo esc_html( $video_duration ); ?>
                    </span>
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

            <!-- Related Videos -->
            <?php
            $related_videos = get_posts( array(
                'post_type'      => 'video_news',
                'posts_per_page' => 3,
                'post__not_in'   => array( get_the_ID() ),
                'orderby'         => 'rand',
            ) );

            if ( $related_videos ) :
                ?>
                <div class="mt-12">
                    <h2 class="text-2xl font-bold mb-6"><?php _e( 'Related Videos', 'trendtoday' ); ?></h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <?php foreach ( $related_videos as $related_video ) : ?>
                            <?php
                            setup_postdata( $related_video );
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
