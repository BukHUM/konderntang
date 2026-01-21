<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package KonDernTang
 * @since 1.0.0
 */

get_header();
?>

<main id="main" class="site-main">
    <div class="container mx-auto px-4 py-20">
        <div class="text-center max-w-2xl mx-auto">
            <div class="mb-8">
                <h1 class="text-9xl font-heading font-bold text-primary mb-4">404</h1>
                <h2 class="text-4xl font-heading font-bold text-dark mb-4">
                    <?php esc_html_e( 'ไม่พบหน้าที่คุณกำลังมองหา', 'konderntang' ); ?>
                </h2>
                <p class="text-gray-600 text-lg mb-8">
                    <?php esc_html_e( 'ขออภัย หน้าที่คุณกำลังมองหาไม่มีอยู่หรือถูกย้ายไปแล้ว', 'konderntang' ); ?>
                </p>
            </div>

            <!-- Search Form -->
            <div class="mb-12">
                <h3 class="font-heading font-bold text-xl text-dark mb-4">
                    <?php esc_html_e( 'ลองค้นหาดู', 'konderntang' ); ?>
                </h3>
                <?php get_search_form(); ?>
            </div>

            <!-- Popular Links -->
            <div class="mb-8">
                <h3 class="font-heading font-bold text-xl text-dark mb-4">
                    <?php esc_html_e( 'หรือลองดูหน้าเหล่านี้', 'konderntang' ); ?>
                </h3>
                <div class="flex flex-wrap justify-center gap-4">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="px-6 py-3 bg-primary text-white rounded-lg hover:bg-blue-600 transition font-medium">
                        <i class="ph ph-house"></i> <?php esc_html_e( 'หน้าแรก', 'konderntang' ); ?>
                    </a>
                    <?php
                    $popular_pages = get_pages(
                        array(
                            'sort_column' => 'post_date',
                            'sort_order'  => 'desc',
                            'number'      => 3,
                        )
                    );
                    foreach ( $popular_pages as $page ) {
                        ?>
                        <a href="<?php echo esc_url( get_permalink( $page->ID ) ); ?>" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium">
                            <?php echo esc_html( $page->post_title ); ?>
                        </a>
                        <?php
                    }
                    ?>
                </div>
            </div>

            <!-- Recent Posts -->
            <?php
            $recent_posts = get_posts(
                array(
                    'posts_per_page' => 3,
                    'post_status'    => 'publish',
                )
            );
            if ( ! empty( $recent_posts ) ) {
                ?>
                <div>
                    <h3 class="font-heading font-bold text-xl text-dark mb-4">
                        <?php esc_html_e( 'บทความล่าสุด', 'konderntang' ); ?>
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <?php
                        foreach ( $recent_posts as $post ) {
                            setup_postdata( $post );
                            konderntang_get_component( 'post-card', array( 'post' => $post, 'show_excerpt' => false ) );
                        }
                        wp_reset_postdata();
                        ?>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
</main>

<?php
get_footer();
