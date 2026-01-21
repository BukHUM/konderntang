<?php
/**
 * Hero Slider Component
 *
 * @package KonDernTang
 * @since 1.0.0
 */

$hero_enabled = konderntang_get_option( 'hero_slider_enabled', true );
if ( ! $hero_enabled ) {
    return;
}

$hero_posts = konderntang_get_option( 'hero_slider_posts', 4 );
$hero_category = konderntang_get_option( 'hero_slider_category', '' );

// Get featured posts
$args = array(
    'posts_per_page' => absint( $hero_posts ),
    'post_status'    => 'publish',
    'meta_query'     => array(
        array(
            'key'   => '_konderntang_featured_post',
            'value' => '1',
        ),
    ),
);

if ( ! empty( $hero_category ) ) {
    $args['category__in'] = array( absint( $hero_category ) );
}

// If no featured posts, get latest posts
$featured_posts = get_posts( $args );
if ( empty( $featured_posts ) ) {
    $args = array(
        'posts_per_page' => absint( $hero_posts ),
        'post_status'    => 'publish',
    );
    if ( ! empty( $hero_category ) ) {
        $args['category__in'] = array( absint( $hero_category ) );
    }
    $featured_posts = get_posts( $args );
}

if ( empty( $featured_posts ) ) {
    return;
}
?>

<header class="relative bg-dark h-[500px] overflow-hidden group">
    <!-- Slider Container -->
    <div id="hero-slider" class="relative h-full">
        <?php
        $slide_index = 0;
        foreach ( $featured_posts as $post ) :
            setup_postdata( $post );
            $slide_class = $slide_index === 0 ? '' : 'opacity-0';
            $thumbnail = get_the_post_thumbnail_url( $post->ID, 'konderntang-hero' );
            if ( ! $thumbnail ) {
                $thumbnail = KONDERN_THEME_URI . '/assets/images/placeholder-hero.jpg';
            }
            ?>
            <div class="hero-slide absolute inset-0 flex items-end group <?php echo esc_attr( $slide_class ); ?>" data-slide="<?php echo esc_attr( $slide_index ); ?>">
                <img src="<?php echo esc_url( $thumbnail ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105 opacity-80">
                <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent"></div>
                <div class="container mx-auto px-4 pb-12 relative z-10">
                    <?php
                    $categories = get_the_category( $post->ID );
                    if ( ! empty( $categories ) ) {
                        $category = $categories[0];
                        ?>
                        <span class="inline-block bg-secondary text-white text-xs font-bold px-3 py-1 rounded-full mb-4 uppercase tracking-wider">
                            <?php echo esc_html( $category->name ); ?>
                        </span>
                        <?php
                    }
                    ?>
                    <h1 class="text-3xl md:text-5xl font-heading font-bold text-white mb-4 leading-tight drop-shadow-md">
                        <a href="<?php echo esc_url( get_permalink() ); ?>" class="hover:underline">
                            <?php echo esc_html( get_the_title() ); ?>
                        </a>
                    </h1>
                    <p class="text-gray-300 text-lg mb-6 max-w-2xl font-light line-clamp-2">
                        <?php echo esc_html( wp_trim_words( get_the_excerpt(), 20 ) ); ?>
                    </p>
                    <a href="<?php echo esc_url( get_permalink() ); ?>" class="inline-flex items-center gap-2 bg-white text-dark font-heading font-semibold px-6 py-3 rounded-lg hover:bg-gray-100 transition">
                        <?php esc_html_e( 'อ่านรีวิวฉบับเต็ม', 'konderntang' ); ?> <i class="ph ph-arrow-right"></i>
                    </a>
                </div>
            </div>
            <?php
            $slide_index++;
        endforeach;
        wp_reset_postdata();
        ?>
    </div>
    
    <!-- Navigation Arrows -->
    <?php if ( count( $featured_posts ) > 1 ) : ?>
        <button id="hero-prev" class="absolute left-4 top-1/2 -translate-y-1/2 z-20 bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white p-3 rounded-full transition-all duration-300 hover:scale-110 opacity-0 group-hover:opacity-100" aria-label="<?php esc_attr_e( 'Previous Slide', 'konderntang' ); ?>">
            <i class="ph ph-caret-left text-2xl"></i>
        </button>
        <button id="hero-next" class="absolute right-4 top-1/2 -translate-y-1/2 z-20 bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white p-3 rounded-full transition-all duration-300 hover:scale-110 opacity-0 group-hover:opacity-100" aria-label="<?php esc_attr_e( 'Next Slide', 'konderntang' ); ?>">
            <i class="ph ph-caret-right text-2xl"></i>
        </button>
        
        <!-- Dots Indicator -->
        <div class="absolute bottom-4 left-1/2 -translate-x-1/2 z-20 flex gap-2">
            <?php
            for ( $i = 0; $i < count( $featured_posts ); $i++ ) {
                $dot_class = $i === 0 ? 'bg-white' : 'bg-white/50 hover:bg-white/75';
                ?>
                <button class="hero-dot w-2.5 h-2.5 rounded-full <?php echo esc_attr( $dot_class ); ?> transition-all duration-300" data-slide="<?php echo esc_attr( $i ); ?>" aria-label="<?php printf( esc_attr__( 'Go to slide %d', 'konderntang' ), $i + 1 ); ?>"></button>
                <?php
            }
            ?>
        </div>
    <?php endif; ?>
</header>
