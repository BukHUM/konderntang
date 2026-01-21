<?php
/**
 * Template part for displaying breadcrumb navigation
 *
 * @package TrendToday
 * @since 1.0.0
 */

if ( is_front_page() ) {
    return;
}
?>

<nav class="hidden md:flex mb-6 text-sm text-gray-500" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-3">
        <li class="inline-flex items-center">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="inline-flex items-center hover:text-accent">
                <i class="fas fa-home mr-2"></i> <?php _e( 'หน้าแรก', 'trendtoday' ); ?>
            </a>
        </li>
        
        <?php if ( is_category() ) : ?>
            <?php
            $cat_title = single_cat_title( '', false );
            $cat_title = strip_tags( $cat_title );
            $cat_title = preg_replace( '/^(หมวดหมู่|Category):\s*/i', '', $cat_title );
            $cat_title = trim( $cat_title );
            ?>
            <li aria-current="page">
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-xs mx-1"></i>
                    <span class="text-gray-400"><?php echo esc_html( $cat_title ); ?></span>
                </div>
            </li>
        <?php elseif ( is_tag() ) : ?>
            <li aria-current="page">
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-xs mx-1"></i>
                    <span class="text-gray-400"><?php echo esc_html( strip_tags( single_tag_title( '', false ) ) ); ?></span>
                </div>
            </li>
        <?php elseif ( is_single() ) : ?>
            <?php
            $categories = get_the_category();
            if ( ! empty( $categories ) ) :
                $category = $categories[0];
                // Strip all HTML tags and remove any prefix text like "หมวดหมู่:" or "Category:"
                $category_name = strip_tags( $category->name );
                $category_name = preg_replace( '/^(หมวดหมู่|Category):\s*/i', '', $category_name );
                $category_name = trim( $category_name );
                ?>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-xs mx-1"></i>
                        <a href="<?php echo esc_url( get_category_link( $category->term_id ) ); ?>" class="hover:text-accent">
                            <?php echo esc_html( $category_name ); ?>
                        </a>
                    </div>
                </li>
            <?php endif; ?>
            <li aria-current="page">
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-xs mx-1"></i>
                    <span class="text-gray-400"><?php echo esc_html( strip_tags( get_the_title() ) ); ?></span>
                </div>
            </li>
        <?php elseif ( is_page() ) : ?>
            <li aria-current="page">
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-xs mx-1"></i>
                    <span class="text-gray-400"><?php echo esc_html( strip_tags( get_the_title() ) ); ?></span>
                </div>
            </li>
        <?php elseif ( is_search() ) : ?>
            <li aria-current="page">
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-xs mx-1"></i>
                    <span class="text-gray-400"><?php _e( 'ผลการค้นหา', 'trendtoday' ); ?></span>
                </div>
            </li>
        <?php elseif ( is_404() ) : ?>
            <li aria-current="page">
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-xs mx-1"></i>
                    <span class="text-gray-400"><?php _e( 'ไม่พบหน้า', 'trendtoday' ); ?></span>
                </div>
            </li>
        <?php endif; ?>
    </ol>
</nav>
