<?php
/**
 * Category Filter Component
 *
 * @package KonDernTang
 * @since 1.0.0
 */

if ( ! is_category() && ! is_archive() ) {
    return;
}

$current_category = is_category() ? get_queried_object() : null;
$parent_categories = get_categories(
    array(
        'parent' => 0,
        'hide_empty' => false,
    )
);
?>

<div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
    <h3 class="font-heading font-bold text-lg mb-4 flex items-center gap-2">
        <i class="ph ph-map-pin text-primary"></i> <?php esc_html_e( 'หมวดหมู่', 'konderntang' ); ?>
    </h3>
    <ul class="space-y-2 text-sm text-gray-600">
        <?php
        foreach ( $parent_categories as $category ) {
            $is_active = $current_category && $current_category->term_id === $category->term_id;
            $link_class = $is_active ? 'text-primary font-semibold' : 'hover:text-primary transition';
            ?>
            <li>
                <a href="<?php echo esc_url( get_category_link( $category->term_id ) ); ?>" class="flex items-center gap-2 <?php echo esc_attr( $link_class ); ?>">
                    <?php echo esc_html( $category->name ); ?>
                    <span class="text-xs opacity-75">(<?php echo esc_html( $category->count ); ?>)</span>
                </a>
            </li>
            <?php
        }
        ?>
    </ul>
</div>
