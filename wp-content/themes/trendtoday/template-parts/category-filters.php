<?php
/**
 * Template part for displaying category filters
 *
 * @package TrendToday
 * @since 1.0.0
 */

$categories = get_categories( array(
    'orderby' => 'count',
    'order'   => 'DESC',
    'number'  => 6,
) );
?>

<?php if ( ! empty( $categories ) ) : ?>
    <div class="bg-white border-b border-gray-100 py-4 sticky top-16 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-2 overflow-x-auto hide-scroll scroll-smooth">
                <button class="category-filter active bg-accent text-white px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap transition-all duration-200 hover:bg-orange-600"
                        data-category="all">
                    ทั้งหมด
                </button>
                <?php foreach ( $categories as $category ) : ?>
                    <button class="category-filter bg-gray-100 text-gray-700 px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap transition-all duration-200 hover:bg-gray-200"
                            data-category="<?php echo esc_attr( $category->slug ); ?>">
                        <?php echo esc_html( $category->name ); ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endif; ?>
