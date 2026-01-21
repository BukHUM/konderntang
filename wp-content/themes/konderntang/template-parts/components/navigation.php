<?php
/**
 * Navigation Component
 *
 * @package KonDernTang
 * @since 1.0.0
 */

$nav_config = require KONDERN_THEME_DIR . '/config/navigation-config.php';
$menu_location = isset( $menu_location ) ? $menu_location : 'primary';
?>

<nav class="bg-dark shadow-lg sticky top-0 z-50 border-b border-gray-700" role="navigation" aria-label="<?php esc_attr_e( 'Primary Navigation', 'konderntang' ); ?>">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center h-20">
            <!-- Logo -->
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="flex-shrink-0 flex items-center gap-2" rel="home">
                <?php
                if ( has_custom_logo() ) {
                    the_custom_logo();
                } else {
                    $fallback_logo = konderntang_get_option( 'logo_fallback_image', '' );
                    if ( $fallback_logo ) {
                        ?>
                        <img src="<?php echo esc_url( $fallback_logo ); ?>" alt="<?php bloginfo( 'name' ); ?>" class="h-16 w-auto object-contain">
                        <?php
                    } else {
                        // Default fallback
                        ?>
                        <img src="<?php echo esc_url( KONDERN_THEME_URI . '/assets/images/konderntang-no-border.png' ); ?>" alt="<?php bloginfo( 'name' ); ?>" class="h-16 w-auto object-contain" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                        <span class="text-white font-heading font-bold text-xl" style="display:none;"><?php bloginfo( 'name' ); ?></span>
                        <?php
                    }
                }
                ?>
            </a>

            <!-- Desktop Menu -->
            <div class="hidden xl:flex space-x-1 items-center font-heading font-medium text-gray-300">
                <!-- Home Button -->
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="px-3 py-2 text-white hover:bg-gray-800 rounded-md transition flex items-center gap-1.5">
                    <i class="ph ph-house text-lg"></i> <?php esc_html_e( 'หน้าแรก', 'konderntang' ); ?>
                </a>
                
                <!-- Travel Dropdown -->
                <div class="relative group">
                    <button class="px-3 py-2 hover:text-white hover:bg-gray-800 rounded-md transition flex items-center gap-1.5">
                        <i class="ph ph-map-trifold text-lg"></i> <?php esc_html_e( 'เที่ยว', 'konderntang' ); ?>
                        <i class="ph ph-caret-down text-xs ml-1"></i>
                    </button>
                    <!-- Dropdown Menu -->
                    <div class="absolute top-full left-0 mt-1 w-64 bg-white rounded-lg shadow-xl border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                        <div class="p-2">
                            <?php
                            $travel_menu = $nav_config['dropdown_menus']['travel'];
                            foreach ( $travel_menu as $key => $item ) :
                                $icon_class = 'ph ph-' . str_replace( 'ph-', '', $item['icon'] );
                                $color_class = '';
                                if ( $key === 'archive' ) {
                                    $color_class = 'text-primary';
                                } elseif ( $key === 'international' ) {
                                    $color_class = 'text-secondary';
                                } elseif ( $key === 'seasonal' ) {
                                    $color_class = 'text-orange-500';
                                } elseif ( $key === 'guide' ) {
                                    $color_class = 'text-green-600';
                                }
                                ?>
                                <a href="<?php echo esc_url( $item['url'] ); ?>" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-50 transition group/item">
                                    <i class="ph <?php echo esc_attr( $icon_class ); ?> <?php echo esc_attr( $color_class ); ?> text-xl"></i>
                                    <div>
                                        <div class="font-semibold text-dark group-hover/item:<?php echo esc_attr( $color_class ); ?>"><?php echo esc_html( $item['label'] ); ?></div>
                                        <?php if ( $key === 'archive' ) : ?>
                                            <div class="text-xs text-gray-500">77 จังหวัด</div>
                                        <?php elseif ( $key === 'international' ) : ?>
                                            <div class="text-xs text-gray-500">ทั่วโลก</div>
                                        <?php elseif ( $key === 'seasonal' ) : ?>
                                            <div class="text-xs text-gray-500">ร้อน ฝน หนาว</div>
                                        <?php elseif ( $key === 'guide' ) : ?>
                                            <div class="text-xs text-gray-500">เทคนิคและคำแนะนำ</div>
                                        <?php endif; ?>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Hotels Button with Badge -->
                <a href="<?php echo esc_url( home_url( '/hotels/' ) ); ?>" class="px-3 py-2 hover:text-white hover:bg-gray-800 rounded-md transition flex items-center gap-1.5 relative group">
                    <i class="ph ph-bed text-lg"></i> <?php esc_html_e( 'จองที่พัก', 'konderntang' ); ?>
                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">Hot</span>
                </a>
                
                <!-- Flights Button with Badge -->
                <a href="<?php echo esc_url( home_url( '/flights/' ) ); ?>" class="px-3 py-2 hover:text-white hover:bg-gray-800 rounded-md transition flex items-center gap-1.5 relative group">
                    <i class="ph ph-airplane-tilt text-lg"></i> <?php esc_html_e( 'จองตั๋วเครื่องบิน', 'konderntang' ); ?>
                    <span class="absolute -top-1 -right-1 bg-green-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full"><?php esc_html_e( 'ใหม่', 'konderntang' ); ?></span>
                </a>
                
                <!-- Promotion Button -->
                <a href="<?php echo esc_url( home_url( '/promotion/' ) ); ?>" class="ml-2 px-4 py-2 bg-secondary text-white rounded-full hover:bg-orange-600 transition shadow-sm flex items-center gap-1.5">
                    <i class="ph ph-tag text-lg"></i> <?php esc_html_e( 'โปรโมชั่น', 'konderntang' ); ?>
                </a>
                
                <!-- Search Button -->
                <?php if ( konderntang_get_option( 'header_show_search', true ) ) : ?>
                    <a href="<?php echo esc_url( home_url( '/?s=' ) ); ?>" class="ml-2 px-3 py-2 hover:text-white hover:bg-gray-800 rounded-md transition flex items-center gap-1.5" aria-label="<?php esc_attr_e( 'Search', 'konderntang' ); ?>">
                        <i class="ph ph-magnifying-glass text-lg"></i>
                    </a>
                <?php endif; ?>
                
                <!-- Language Selector -->
                <div class="ml-3 relative group">
                    <button class="px-3 py-2 hover:bg-gray-800 rounded-md transition flex items-center gap-1.5 text-gray-300 hover:text-white">
                        <i class="ph ph-globe text-lg"></i>
                        <span class="text-sm"><?php esc_html_e( 'ไทย', 'konderntang' ); ?></span>
                        <i class="ph ph-caret-down text-xs"></i>
                    </button>
                    <div class="absolute right-0 mt-2 w-40 bg-gray-800 rounded-lg shadow-xl border border-gray-700 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                        <div class="py-2">
                            <?php
                            $languages = array(
                                'th' => array( 'flag' => '🇹🇭', 'name' => esc_html__( 'ไทย', 'konderntang' ) ),
                                'en' => array( 'flag' => '🇬🇧', 'name' => esc_html__( 'อังกฤษ', 'konderntang' ) ),
                                'fr' => array( 'flag' => '🇫🇷', 'name' => esc_html__( 'ฝรั่งเศส', 'konderntang' ) ),
                                'ja' => array( 'flag' => '🇯🇵', 'name' => esc_html__( 'ญี่ปุ่น', 'konderntang' ) ),
                                'lo' => array( 'flag' => '🇱🇦', 'name' => esc_html__( 'ลาว', 'konderntang' ) ),
                                'de' => array( 'flag' => '🇩🇪', 'name' => esc_html__( 'เยอรมัน', 'konderntang' ) ),
                                'ru' => array( 'flag' => '🇷🇺', 'name' => esc_html__( 'รัสเซีย', 'konderntang' ) ),
                            );
                            foreach ( $languages as $code => $lang ) :
                                ?>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white transition flex items-center gap-2" data-lang="<?php echo esc_attr( $code ); ?>">
                                    <span class="text-base"><?php echo esc_html( $lang['flag'] ); ?></span> <?php echo esc_html( $lang['name'] ); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile Menu Button -->
            <button onclick="document.getElementById('mobile-menu').classList.toggle('hidden')" class="xl:hidden text-gray-300 hover:text-white p-2" aria-label="<?php esc_attr_e( 'Toggle Menu', 'konderntang' ); ?>">
                <i class="ph ph-list text-3xl"></i>
            </button>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="hidden xl:hidden bg-gray-900 border-t border-gray-700 px-4 pt-3 pb-4 shadow-lg font-heading">
        <!-- Navigation Items -->
        <div class="space-y-1.5 mb-4">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="block w-full text-left px-3 py-2.5 text-white font-medium bg-gray-800 rounded-lg flex items-center gap-2.5 hover:bg-gray-750 transition">
                <i class="ph ph-house text-lg"></i> <?php esc_html_e( 'หน้าแรก', 'konderntang' ); ?>
            </a>
            
            <!-- Travel Section with Collapsible -->
            <div class="space-y-1.5">
                <button onclick="toggleMobileSubmenu('travel-submenu')" class="block w-full text-left px-3 py-2.5 text-gray-300 hover:bg-gray-800 hover:text-white rounded-lg flex items-center justify-between transition">
                    <div class="flex items-center gap-2.5">
                        <i class="ph ph-map-trifold text-lg"></i>
                        <span><?php esc_html_e( 'เที่ยว', 'konderntang' ); ?></span>
                    </div>
                    <i class="ph ph-caret-down text-sm transition" id="travel-submenu-icon"></i>
                </button>
                <div id="travel-submenu" class="hidden pl-6 space-y-1">
                    <?php
                    $travel_menu = $nav_config['dropdown_menus']['travel'];
                    foreach ( $travel_menu as $key => $item ) :
                        $icon_class = 'ph ph-' . str_replace( 'ph-', '', $item['icon'] );
                        ?>
                        <a href="<?php echo esc_url( $item['url'] ); ?>" class="block w-full text-left px-3 py-2 text-gray-400 hover:bg-gray-800 hover:text-white rounded-lg flex items-center gap-2.5 transition text-sm">
                            <i class="ph <?php echo esc_attr( $icon_class ); ?> text-base"></i> <?php echo esc_html( $item['label'] ); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <a href="<?php echo esc_url( home_url( '/hotels/' ) ); ?>" class="block w-full text-left px-3 py-2.5 text-gray-300 hover:bg-gray-800 hover:text-white rounded-lg flex items-center gap-2.5 relative transition">
                <i class="ph ph-bed text-lg"></i> <?php esc_html_e( 'จองที่พัก', 'konderntang' ); ?>
                <span class="ml-auto bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">Hot</span>
            </a>
            
            <a href="<?php echo esc_url( home_url( '/flights/' ) ); ?>" class="block w-full text-left px-3 py-2.5 text-gray-300 hover:bg-gray-800 hover:text-white rounded-lg flex items-center gap-2.5 relative transition">
                <i class="ph ph-airplane-tilt text-lg"></i> <?php esc_html_e( 'จองตั๋ว', 'konderntang' ); ?>
                <span class="ml-auto bg-green-500 text-white text-xs font-bold px-2 py-0.5 rounded-full"><?php esc_html_e( 'ใหม่', 'konderntang' ); ?></span>
            </a>
            
            <a href="<?php echo esc_url( home_url( '/promotion/' ) ); ?>" class="block w-full text-left px-3 py-2.5 bg-secondary text-white rounded-lg flex items-center gap-2.5 hover:bg-orange-600 transition">
                <i class="ph ph-tag text-lg"></i> <?php esc_html_e( 'โปรโมชั่น', 'konderntang' ); ?>
            </a>
            
            <a href="<?php echo esc_url( home_url( '/?s=' ) ); ?>" class="block w-full text-left px-3 py-2.5 text-gray-300 hover:bg-gray-800 hover:text-white rounded-lg flex items-center gap-2.5 transition">
                <i class="ph ph-magnifying-glass text-lg"></i> <?php esc_html_e( 'ค้นหา', 'konderntang' ); ?>
            </a>
        </div>
        
        <!-- Language Selector Mobile - Compact Grid -->
        <div class="border-t border-gray-700 pt-3">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <i class="ph ph-globe text-gray-400"></i>
                    <span class="text-gray-400 text-sm font-semibold"><?php esc_html_e( 'เปลี่ยนภาษา', 'konderntang' ); ?></span>
                </div>
                <span class="text-xs text-gray-500 bg-gray-800 px-2 py-1 rounded"><?php esc_html_e( 'ไทย', 'konderntang' ); ?></span>
            </div>
            <div class="grid grid-cols-2 gap-2">
                <?php
                $languages = array(
                    'th' => array( 'flag' => '🇹🇭', 'name' => esc_html__( 'ไทย', 'konderntang' ) ),
                    'en' => array( 'flag' => '🇬🇧', 'name' => esc_html__( 'อังกฤษ', 'konderntang' ) ),
                    'fr' => array( 'flag' => '🇫🇷', 'name' => esc_html__( 'ฝรั่งเศส', 'konderntang' ) ),
                    'ja' => array( 'flag' => '🇯🇵', 'name' => esc_html__( 'ญี่ปุ่น', 'konderntang' ) ),
                    'lo' => array( 'flag' => '🇱🇦', 'name' => esc_html__( 'ลาว', 'konderntang' ) ),
                    'de' => array( 'flag' => '🇩🇪', 'name' => esc_html__( 'เยอรมัน', 'konderntang' ) ),
                    'ru' => array( 'flag' => '🇷🇺', 'name' => esc_html__( 'รัสเซีย', 'konderntang' ) ),
                );
                $lang_index = 0;
                foreach ( $languages as $code => $lang ) :
                    $lang_index++;
                    $active_class = ( $code === 'th' ) ? 'bg-gray-800 text-white' : 'bg-gray-800/50 text-gray-300';
                    $col_span = ( $lang_index === count( $languages ) ) ? 'col-span-2' : '';
                    ?>
                    <button class="px-3 py-2 <?php echo esc_attr( $active_class ); ?> rounded-lg flex items-center justify-center gap-2 text-sm hover:bg-gray-800 hover:text-white transition <?php echo esc_attr( $col_span ); ?>" data-lang="<?php echo esc_attr( $code ); ?>">
                        <span class="text-base"><?php echo esc_html( $lang['flag'] ); ?></span> <?php echo esc_html( $lang['name'] ); ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</nav>

<?php
/**
 * Default menu fallback
 */
function konderntang_default_menu() {
    ?>
    <ul class="flex space-x-1 items-center">
        <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="px-3 py-2 text-white hover:bg-gray-800 rounded-md transition flex items-center gap-1.5">
            <i class="ph ph-house text-lg"></i> <?php esc_html_e( 'หน้าแรก', 'konderntang' ); ?>
        </a></li>
    </ul>
    <?php
}
