<?php
/**
 * Template part for displaying hero/breaking news section
 *
 * @package TrendToday
 * @since 1.0.0
 */

// Get category ID if on category archive page
$category_id = null;
if ( is_category() ) {
    $category_id = get_queried_object_id();
}

// Get breaking news posts (4 slides)
$breaking_query = trendtoday_get_breaking_news( 4, $category_id );

// Fallback to latest posts if no breaking news
if ( ! $breaking_query->have_posts() ) {
    $args = array(
        'posts_per_page' => 4,
        'post_type'      => 'post',
        'orderby'        => 'date',
        'order'          => 'DESC',
    );
    
    // Filter by category if on category archive
    if ( $category_id ) {
        $args['cat'] = $category_id;
    }
    
    $breaking_query = new WP_Query( $args );
}

if ( $breaking_query->have_posts() ) :
    $slides = array();
    while ( $breaking_query->have_posts() ) :
        $breaking_query->the_post();
        $slides[] = array(
            'id'         => get_the_ID(),
            'permalink'  => trendtoday_fix_url( get_permalink() ),
            'title'      => get_the_title(),
            'excerpt'    => has_excerpt() ? get_the_excerpt() : '',
            'thumbnail'  => has_post_thumbnail() ? esc_url( get_the_post_thumbnail_url( get_the_ID(), 'trendtoday-hero' ) ) : '',
            'time'       => human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) . ' ที่แล้ว',
            'author'     => trendtoday_get_author_name(),
        );
    endwhile;
    wp_reset_postdata();
    ?>
    <section class="mb-12 hero-slider-section" aria-label="Breaking news slider">
        <div class="relative hero-slider-container rounded-2xl overflow-hidden shadow-xl h-96 md:h-[500px]">
            <!-- Slides -->
            <div class="hero-slider-wrapper relative w-full h-full">
                <?php foreach ( $slides as $index => $slide ) : ?>
                    <div class="hero-slide <?php echo $index === 0 ? 'active' : ''; ?>" data-slide="<?php echo esc_attr( $index ); ?>">
                        <article class="relative w-full h-full cursor-pointer group"
                                 onclick="window.location.href='<?php echo esc_url( $slide['permalink'] ); ?>'"
                                 onkeypress="if(event.key === 'Enter') window.location.href='<?php echo esc_url( $slide['permalink'] ); ?>'"
                                 role="article"
                                 tabindex="0"
                                 aria-label="<?php echo esc_attr( $slide['title'] ); ?>">
                            <?php if ( $slide['thumbnail'] ) : ?>
                                <img src="<?php echo esc_url( $slide['thumbnail'] ); ?>" 
                                     alt="<?php echo esc_attr( $slide['title'] ); ?>"
                                     class="w-full h-full object-cover transition duration-700 group-hover:scale-105">
                            <?php endif; ?>
                            <div class="absolute inset-0 hero-overlay"></div>
                            <div class="absolute bottom-0 left-0 p-6 md:p-10 w-full md:w-3/4">
                                <span class="category-badge bg-accent text-white text-xs font-bold px-3 py-1.5 rounded-full uppercase tracking-wider mb-3 inline-block shadow-lg">
                                    <i class="fas fa-fire mr-1"></i>Breaking News
                                </span>
                                <h1 class="text-xl md:text-3xl lg:text-4xl font-bold text-white leading-tight mb-3 drop-shadow-lg">
                                    <a href="<?php echo esc_url( $slide['permalink'] ); ?>" class="text-white hover:text-gray-200">
                                        <?php echo esc_html( $slide['title'] ); ?>
                                    </a>
                                </h1>
                                <?php if ( ! empty( $slide['excerpt'] ) ) : ?>
                                    <p class="text-gray-200 text-sm md:text-base mb-4 line-clamp-2 drop-shadow-md">
                                        <?php echo wp_trim_words( $slide['excerpt'], 20, '...' ); ?>
                                    </p>
                                <?php endif; ?>
                                <div class="flex flex-wrap items-center text-gray-300 text-xs md:text-sm gap-4">
                                    <span class="flex items-center">
                                        <i class="far fa-clock mr-1.5"></i>
                                        <?php echo esc_html( $slide['time'] ); ?>
                                    </span>
                                    <span class="flex items-center">
                                        <i class="far fa-user mr-1.5"></i>
                                        <?php echo esc_html( $slide['author'] ); ?>
                                    </span>
                                </div>
                            </div>
                        </article>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Navigation Buttons -->
            <button class="hero-slider-prev absolute left-4 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white text-gray-800 p-3 rounded-full shadow-lg transition-all duration-200 z-10 focus:outline-none focus:ring-2 focus:ring-accent" 
                    aria-label="Previous slide">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button class="hero-slider-next absolute right-4 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white text-gray-800 p-3 rounded-full shadow-lg transition-all duration-200 z-10 focus:outline-none focus:ring-2 focus:ring-accent" 
                    aria-label="Next slide">
                <i class="fas fa-chevron-right"></i>
            </button>

            <!-- Indicators -->
            <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2 z-10">
                <?php foreach ( $slides as $index => $slide ) : ?>
                    <button class="hero-slider-indicator w-2 h-2 rounded-full <?php echo $index === 0 ? 'bg-white' : 'bg-white/50'; ?> transition-all duration-200 hover:bg-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2" 
                            data-slide="<?php echo esc_attr( $index ); ?>"
                            aria-label="Go to slide <?php echo esc_attr( $index + 1 ); ?>"></button>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php
endif;
?>
