<?php
/**
 * Language Switcher Component
 *
 * @package KonDernTang
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Check if Polylang is active
if ( ! function_exists( 'pll_the_languages' ) ) {
    return;
}

// Get settings
$switcher_style = konderntang_get_option( 'language_switcher_style', 'dropdown' );
$show_flags = konderntang_get_option( 'language_switcher_show_flags', true );
$show_search = konderntang_get_option( 'language_switcher_show_search', false );
$modal_title = konderntang_get_option( 'language_switcher_modal_title', esc_html__( 'เลือกภาษา', 'konderntang' ) );

// Check if this is mobile context (passed from navigation.php)
$is_mobile = isset( $args['is_mobile'] ) ? $args['is_mobile'] : false;

// Get languages - only show languages that have content
$languages = pll_the_languages( array( 
    'raw'                    => 1,
    'hide_if_no_translation' => 1,  // Hide languages without translation for current content (single post/page)
    'hide_if_empty'          => 1,  // Hide languages that have no posts at all
    'hide_current'           => 0,  // Show current language too
) );
$current_lang = function_exists( 'pll_current_language' ) ? pll_current_language() : '';

// Filter out languages with no published posts
if ( ! empty( $languages ) ) {
    $languages = array_filter( $languages, function( $lang ) {
        // Check if language has at least 1 published post
        if ( isset( $lang['no_translation'] ) && $lang['no_translation'] ) {
            return false; // No translation for current content
        }
        
        // For homepage/archive, check if language has any posts
        if ( function_exists( 'pll_count_posts' ) ) {
            $post_count = pll_count_posts( $lang['slug'] );
            return $post_count > 0;
        }
        
        return true;
    });
}

if ( empty( $languages ) || count( $languages ) < 2 ) {
    return; // Don't show if less than 2 languages available
}

// Get current language data
$current_lang_data = null;
foreach ( $languages as $lang ) {
    if ( ! empty( $lang['current_lang'] ) ) {
        $current_lang_data = $lang;
        break;
    }
}

/**
 * Get flag for language (wrapper function)
 */
if ( ! function_exists( 'konderntang_get_lang_flag' ) ) {
    function konderntang_get_lang_flag( $lang ) {
        if ( function_exists( 'konderntang_get_flag_emoji' ) ) {
            return konderntang_get_flag_emoji( $lang['slug'], '🌐' );
        }
        return ! empty( $lang['flag'] ) ? $lang['flag'] : '🌐';
    }
}

// Unique ID for mobile/desktop
$unique_id = $is_mobile ? 'mobile' : 'desktop';

if ( 'modal' === $switcher_style ) :
    ?>
    <!-- Language Switcher Modal -->
    <div class="konderntang-language-modal" id="konderntang-language-modal-<?php echo esc_attr( $unique_id ); ?>" style="display: none;" role="dialog" aria-modal="true" aria-labelledby="modal-title-<?php echo esc_attr( $unique_id ); ?>">
        <div class="konderntang-language-modal-overlay" data-close-modal></div>
        <div class="konderntang-language-modal-container">
            <!-- Modal Header -->
            <div class="konderntang-language-modal-header">
                <div class="konderntang-language-modal-icon">
                    <i class="ph ph-globe"></i>
                </div>
                <h3 id="modal-title-<?php echo esc_attr( $unique_id ); ?>"><?php echo esc_html( $modal_title ); ?></h3>
                <button type="button" class="konderntang-language-modal-close" data-close-modal aria-label="<?php esc_attr_e( 'ปิด', 'konderntang' ); ?>">
                    <i class="ph ph-x"></i>
                </button>
            </div>
            
            <!-- Geo-location suggestion banner (hidden by default, shown via JS) -->
            <div class="konderntang-language-suggestion" id="language-suggestion-<?php echo esc_attr( $unique_id ); ?>" style="display: none;">
                <div class="konderntang-language-suggestion-content">
                    <i class="ph ph-map-pin"></i>
                    <span class="konderntang-language-suggestion-text">
                        <?php esc_html_e( 'เราตรวจพบว่าคุณอยู่ใน', 'konderntang' ); ?>
                        <strong id="detected-country-<?php echo esc_attr( $unique_id ); ?>"></strong>
                    </span>
                </div>
                <a href="#" class="konderntang-language-suggestion-btn" id="use-suggested-lang-<?php echo esc_attr( $unique_id ); ?>">
                    <?php esc_html_e( 'ใช้ภาษาที่แนะนำ', 'konderntang' ); ?>
                </a>
            </div>
            
            <?php if ( $show_search && count( $languages ) > 4 ) : ?>
                <!-- Search Box -->
                <div class="konderntang-language-modal-search">
                    <i class="ph ph-magnifying-glass"></i>
                    <input type="text" 
                           id="konderntang-language-search-<?php echo esc_attr( $unique_id ); ?>" 
                           placeholder="<?php esc_attr_e( 'ค้นหาภาษา...', 'konderntang' ); ?>" 
                           class="konderntang-language-search-input"
                           autocomplete="off" />
                </div>
            <?php endif; ?>
            
            <!-- Language Grid -->
            <div class="konderntang-language-modal-grid" id="konderntang-language-list-<?php echo esc_attr( $unique_id ); ?>">
                <?php foreach ( $languages as $lang ) : 
                    $flag = konderntang_get_lang_flag( $lang );
                    $is_current = ! empty( $lang['current_lang'] );
                ?>
                    <a href="<?php echo esc_url( $lang['url'] ); ?>" 
                       class="konderntang-language-card <?php echo $is_current ? 'current' : ''; ?>" 
                       data-lang="<?php echo esc_attr( $lang['slug'] ); ?>"
                       data-name="<?php echo esc_attr( strtolower( $lang['name'] ) ); ?>">
                        <?php if ( $is_current ) : ?>
                            <span class="konderntang-language-check">
                                <i class="ph-fill ph-check-circle"></i>
                            </span>
                        <?php endif; ?>
                        <?php if ( $show_flags ) : ?>
                            <span class="konderntang-language-flag"><?php echo esc_html( $flag ); ?></span>
                        <?php endif; ?>
                        <span class="konderntang-language-name"><?php echo esc_html( $lang['name'] ); ?></span>
                        <span class="konderntang-language-code"><?php echo esc_html( strtoupper( $lang['slug'] ) ); ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    
    <!-- Language Switcher Button (Modal Trigger) -->
    <div class="konderntang-language-switcher <?php echo $is_mobile ? 'is-mobile' : ''; ?>">
        <button type="button" class="konderntang-language-button" id="konderntang-language-button-<?php echo esc_attr( $unique_id ); ?>" aria-haspopup="dialog">
            <?php if ( $current_lang_data && $show_flags ) : ?>
                <span class="konderntang-language-flag"><?php echo esc_html( konderntang_get_lang_flag( $current_lang_data ) ); ?></span>
            <?php endif; ?>
            <span class="konderntang-language-name"><?php echo $current_lang_data ? esc_html( $current_lang_data['name'] ) : esc_html__( 'ภาษา', 'konderntang' ); ?></span>
            <i class="ph ph-globe-simple"></i>
        </button>
    </div>
    
<?php else : ?>
    <!-- Language Switcher Dropdown -->
    <div class="konderntang-language-switcher konderntang-language-switcher-dropdown <?php echo $is_mobile ? 'is-mobile' : ''; ?>">
        <button type="button" class="konderntang-language-button" id="konderntang-language-button-<?php echo esc_attr( $unique_id ); ?>" aria-haspopup="listbox" aria-expanded="false">
            <?php if ( $current_lang_data && $show_flags ) : ?>
                <span class="konderntang-language-flag"><?php echo esc_html( konderntang_get_lang_flag( $current_lang_data ) ); ?></span>
            <?php endif; ?>
            <span class="konderntang-language-name"><?php echo $current_lang_data ? esc_html( $current_lang_data['name'] ) : esc_html__( 'ภาษา', 'konderntang' ); ?></span>
            <i class="ph ph-caret-down konderntang-language-caret"></i>
        </button>
        <div class="konderntang-language-dropdown" id="konderntang-language-dropdown-<?php echo esc_attr( $unique_id ); ?>" role="listbox">
            <?php foreach ( $languages as $lang ) : 
                $flag = konderntang_get_lang_flag( $lang );
                $is_current = ! empty( $lang['current_lang'] );
            ?>
                <a href="<?php echo esc_url( $lang['url'] ); ?>" 
                   class="konderntang-language-item <?php echo $is_current ? 'current' : ''; ?>"
                   role="option"
                   aria-selected="<?php echo $is_current ? 'true' : 'false'; ?>">
                    <?php if ( $show_flags ) : ?>
                        <span class="konderntang-language-flag"><?php echo esc_html( $flag ); ?></span>
                    <?php endif; ?>
                    <span class="konderntang-language-name"><?php echo esc_html( $lang['name'] ); ?></span>
                    <?php if ( $is_current ) : ?>
                        <span class="konderntang-language-check">
                            <i class="ph-fill ph-check"></i>
                        </span>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>
