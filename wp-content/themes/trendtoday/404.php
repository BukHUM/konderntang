<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package TrendToday
 * @since 1.0.0
 */

get_header();
?>

<main id="main-content" class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 w-full">
    <div class="text-center">
        <div class="mb-8">
            <h1 class="text-9xl font-bold text-gray-200 mb-4">404</h1>
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                <?php _e( 'ไม่พบหน้าที่คุณกำลังมองหา', 'trendtoday' ); ?>
            </h2>
            <p class="text-gray-500 text-lg mb-8">
                <?php _e( 'หน้านี้อาจถูกลบหรือย้ายไปที่อื่นแล้ว', 'trendtoday' ); ?>
            </p>
        </div>

        <div class="max-w-md mx-auto mb-12">
            <form method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="relative">
                <input type="search" 
                       name="s" 
                       class="w-full px-6 py-4 rounded-full text-gray-900 focus:outline-none focus:ring-4 focus:ring-accent/50 text-lg shadow-lg pl-14"
                       placeholder="<?php _e( 'ลองค้นหาดู...', 'trendtoday' ); ?>">
                <i class="fas fa-search absolute left-6 top-1/2 -translate-y-1/2 text-gray-400 text-xl"></i>
                <button type="submit"
                        class="absolute right-3 top-1/2 -translate-y-1/2 bg-accent hover:bg-orange-600 text-white px-6 py-2 rounded-full font-bold text-sm transition">
                    <?php _e( 'ค้นหา', 'trendtoday' ); ?>
                </button>
            </form>
        </div>

        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" 
               class="bg-accent text-white px-8 py-3 rounded-full hover:bg-orange-600 transition font-medium">
                <i class="fas fa-home mr-2"></i><?php _e( 'กลับหน้าแรก', 'trendtoday' ); ?>
            </a>
            <a href="javascript:history.back()" 
               class="bg-white border-2 border-gray-300 text-gray-700 px-8 py-3 rounded-full hover:bg-gray-50 transition font-medium">
                <i class="fas fa-arrow-left mr-2"></i><?php _e( 'กลับหน้าก่อนหน้า', 'trendtoday' ); ?>
            </a>
        </div>

        <!-- Popular Posts -->
        <?php
        $popular_posts = get_posts( array(
            'numberposts' => 4,
            'orderby'     => 'date',
            'order'       => 'DESC',
        ) );

        if ( ! empty( $popular_posts ) ) :
            ?>
            <div class="mt-16">
                <h3 class="text-2xl font-bold text-gray-900 mb-6"><?php _e( 'ข่าวล่าสุด', 'trendtoday' ); ?></h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <?php foreach ( $popular_posts as $post ) : setup_postdata( $post ); ?>
                        <a href="<?php echo esc_url( trendtoday_fix_url( get_permalink() ) ); ?>" class="group">
                            <div class="bg-white rounded-xl overflow-hidden shadow-sm border border-gray-100 hover:shadow-md transition">
                                <?php if ( has_post_thumbnail() ) : ?>
                                    <div class="h-40 overflow-hidden">
                                        <?php the_post_thumbnail( 'trendtoday-thumbnail', array(
                                            'class' => 'w-full h-full object-cover group-hover:scale-105 transition duration-500',
                                        ) ); ?>
                                    </div>
                                <?php endif; ?>
                                <div class="p-4">
                                    <h4 class="font-bold text-gray-900 group-hover:text-accent transition line-clamp-2 mb-2">
                                        <?php the_title(); ?>
                                    </h4>
                                    <p class="text-gray-500 text-xs">
                                        <?php echo human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) . ' ที่แล้ว'; ?>
                                    </p>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; wp_reset_postdata(); ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php
get_footer();
?>
