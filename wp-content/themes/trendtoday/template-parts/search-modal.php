<?php
/**
 * Template part for displaying search modal
 *
 * @package TrendToday
 * @since 1.0.0
 */

$search_enabled = get_option( 'trendtoday_search_enabled', '1' );
if ( $search_enabled !== '1' ) {
    return;
}

$search_placeholder = get_option( 'trendtoday_search_placeholder', __( 'พิมพ์คำค้นหา...', 'trendtoday' ) );
$search_suggestions_style = get_option( 'trendtoday_search_suggestions_style', 'dropdown' );

if ( $search_suggestions_style !== 'modal' && $search_suggestions_style !== 'fullpage' ) {
    return;
}
?>

<div id="trendtoday-search-modal" class="trendtoday-search-modal <?php echo $search_suggestions_style === 'fullpage' ? 'trendtoday-search-fullpage' : ''; ?> hidden fixed inset-0 z-50 bg-black/50 backdrop-blur-sm">
    <div class="trendtoday-search-modal-content <?php echo $search_suggestions_style === 'fullpage' ? 'h-full' : 'max-w-2xl mx-auto mt-20'; ?> bg-white rounded-lg shadow-2xl">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-900"><?php _e( 'ค้นหา', 'trendtoday' ); ?></h2>
                <button type="button" class="trendtoday-search-close w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 hover:bg-gray-200 transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="relative">
                <input type="search" 
                       class="trendtoday-search-input w-full px-6 py-4 pl-14 pr-14 rounded-full border-2 border-gray-200 focus:outline-none focus:ring-4 focus:ring-accent/50 focus:border-accent text-lg"
                       placeholder="<?php echo esc_attr( $search_placeholder ); ?>"
                       autocomplete="off"
                       autofocus />
                <i class="fas fa-search absolute left-6 top-1/2 -translate-y-1/2 text-gray-400 text-xl"></i>
                <button type="button" class="trendtoday-search-submit absolute right-3 top-1/2 -translate-y-1/2 bg-accent hover:bg-orange-600 text-white px-6 py-2 rounded-full font-bold text-sm transition">
                    <?php _e( 'ค้นหา', 'trendtoday' ); ?>
                </button>
            </div>
            
            <div class="trendtoday-search-suggestions mt-4 max-h-96 overflow-y-auto"></div>
        </div>
    </div>
</div>
