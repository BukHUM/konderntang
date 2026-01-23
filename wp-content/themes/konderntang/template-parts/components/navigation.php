<?php
/**
 * Navigation Component
 *
 * @package KonDernTang
 * @since 1.0.0
 */

$nav_config = require KONDERN_THEME_DIR . '/config/navigation-config.php';
$menu_location = isset($menu_location) ? $menu_location : 'primary';
?>

<nav class="bg-dark shadow-lg sticky top-0 z-50 border-b border-gray-700" role="navigation"
    aria-label="<?php esc_attr_e('Primary Navigation', 'konderntang'); ?>">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center h-20">
            <!-- Logo -->
            <a href="<?php echo esc_url(home_url('/')); ?>" class="flex-shrink-0 flex items-center gap-2" rel="home">
                <?php
                // Priority: 1. WordPress Custom Logo, 2. Site Logo (Theme Settings), 3. Logo Fallback Image, 4. Default image, 5. Site name
                if (has_custom_logo()) {
                    the_custom_logo();
                } else {
                    $site_logo = konderntang_get_option('site_logo', '');
                    if ($site_logo) {
                        ?>
                        <img src="<?php echo esc_url($site_logo); ?>" alt="<?php bloginfo('name'); ?>"
                            class="h-16 w-auto object-contain">
                        <?php
                    } else {
                        $fallback_logo = konderntang_get_option('logo_fallback_image', '');
                        if ($fallback_logo) {
                            ?>
                            <img src="<?php echo esc_url($fallback_logo); ?>" alt="<?php bloginfo('name'); ?>"
                                class="h-16 w-auto object-contain">
                            <?php
                        } else {
                            // Default fallback image
                            ?>
                            <img src="<?php echo esc_url(KONDERN_THEME_URI . '/assets/images/konderntang-no-border.png'); ?>"
                                alt="<?php bloginfo('name'); ?>" class="h-16 w-auto object-contain"
                                onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                            <span class="text-white font-heading font-bold text-xl"
                                style="display:none;"><?php bloginfo('name'); ?></span>
                            <?php
                        }
                    }
                }
                ?>
            </a>

            <!-- Desktop Menu -->
            <div class="hidden xl:flex space-x-1 items-center font-heading font-medium text-gray-300">
                <?php
                // Use WordPress menu if available, otherwise fallback to config
                if (has_nav_menu('primary')) {
                    wp_nav_menu(
                        array(
                            'theme_location' => 'primary',
                            'container' => false,
                            'menu_class' => 'flex space-x-1 items-center',
                            'menu_id' => 'primary-menu',
                            'fallback_cb' => false,
                            'walker' => new KonDernTang_Walker_Nav_Menu(),
                        )
                    );
                } else {
                    // Fallback to default menu from config
                    ?>
                    <!-- Home Button -->
                    <a href="<?php echo esc_url(home_url('/')); ?>"
                        class="px-3 py-2 text-white hover:bg-gray-800 rounded-md transition flex items-center gap-1.5">
                        <i class="ph ph-house text-lg"></i> <?php esc_html_e('หน้าแรก', 'konderntang'); ?>
                    </a>

                    <!-- Travel Dropdown -->
                    <div class="relative group">
                        <button
                            class="px-3 py-2 hover:text-white hover:bg-gray-800 rounded-md transition flex items-center gap-1.5">
                            <i class="ph ph-map-trifold text-lg"></i> <?php esc_html_e('เที่ยว', 'konderntang'); ?>
                            <i class="ph ph-caret-down text-xs ml-1"></i>
                        </button>
                        <!-- Dropdown Menu -->
                        <div
                            class="absolute top-full left-0 mt-1 w-64 bg-white rounded-lg shadow-xl border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                            <div class="p-2">
                                <?php
                                $travel_menu = $nav_config['dropdown_menus']['travel'];
                                foreach ($travel_menu as $key => $item):
                                    $icon_class = 'ph ph-' . str_replace('ph-', '', $item['icon']);
                                    $color_class = '';
                                    if ($key === 'archive') {
                                        $color_class = 'text-primary';
                                    } elseif ($key === 'international') {
                                        $color_class = 'text-secondary';
                                    } elseif ($key === 'seasonal') {
                                        $color_class = 'text-orange-500';
                                    } elseif ($key === 'guide') {
                                        $color_class = 'text-green-600';
                                    }
                                    ?>
                                    <a href="<?php echo esc_url($item['url']); ?>"
                                        class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-50 transition group/item">
                                        <i
                                            class="ph <?php echo esc_attr($icon_class); ?> <?php echo esc_attr($color_class); ?> text-xl"></i>
                                        <div>
                                            <div
                                                class="font-semibold text-dark group-hover/item:<?php echo esc_attr($color_class); ?>">
                                                <?php echo esc_html($item['label']); ?>
                                            </div>
                                            <?php if ($key === 'archive'): ?>
                                                <div class="text-xs text-gray-500">77 จังหวัด</div>
                                            <?php elseif ($key === 'international'): ?>
                                                <div class="text-xs text-gray-500">ทั่วโลก</div>
                                            <?php elseif ($key === 'seasonal'): ?>
                                                <div class="text-xs text-gray-500">ร้อน ฝน หนาว</div>
                                            <?php elseif ($key === 'guide'): ?>
                                                <div class="text-xs text-gray-500">เทคนิคและคำแนะนำ</div>
                                            <?php endif; ?>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Hotels Button with Badge -->
                    <a href="<?php echo esc_url(home_url('/hotels/')); ?>"
                        class="px-3 py-2 hover:text-white hover:bg-gray-800 rounded-md transition flex items-center gap-1.5 relative group">
                        <i class="ph ph-bed text-lg"></i> <?php esc_html_e('จองที่พัก', 'konderntang'); ?>
                        <span
                            class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">Hot</span>
                    </a>

                    <!-- Flights Button with Badge -->
                    <a href="<?php echo esc_url(home_url('/flights/')); ?>"
                        class="px-3 py-2 hover:text-white hover:bg-gray-800 rounded-md transition flex items-center gap-1.5 relative group">
                        <i class="ph ph-airplane-tilt text-lg"></i>
                        <?php esc_html_e('จองตั๋วเครื่องบิน', 'konderntang'); ?>
                        <span
                            class="absolute -top-1 -right-1 bg-green-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full"><?php esc_html_e('ใหม่', 'konderntang'); ?></span>
                    </a>

                    <!-- Promotion Button -->
                    <a href="<?php echo esc_url(home_url('/promotion/')); ?>"
                        class="ml-2 px-4 py-2 bg-secondary text-white rounded-full hover:bg-orange-600 transition shadow-sm flex items-center gap-1.5">
                        <i class="ph ph-tag text-lg"></i> <?php esc_html_e('โปรโมชั่น', 'konderntang'); ?>
                    </a>
                    <?php
                }
                ?>

                <!-- Search Button -->
                <?php if (konderntang_get_option('header_show_search', true)): ?>
                    <button onclick="openSearchModal()"
                        class="ml-2 px-3 py-2 hover:text-white hover:bg-gray-800 rounded-md transition flex items-center gap-1.5 text-gray-300"
                        aria-label="<?php esc_attr_e('Search', 'konderntang'); ?>">
                        <i class="ph ph-magnifying-glass text-lg"></i>
                    </button>
                <?php endif; ?>

                <!-- Language Selector -->
                <div class="ml-3">
                    <?php get_template_part('template-parts/components/language-switcher'); ?>
                </div>
            </div>

            <!-- Mobile Menu Button -->
            <button onclick="document.getElementById('mobile-menu').classList.toggle('hidden')"
                class="xl:hidden text-gray-300 hover:text-white p-2"
                aria-label="<?php esc_attr_e('Toggle Menu', 'konderntang'); ?>">
                <i class="ph ph-list text-3xl"></i>
            </button>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobile-menu"
        class="hidden xl:hidden bg-gray-900 border-t border-gray-700 px-4 pt-3 pb-4 shadow-lg font-heading">
        <!-- Navigation Items -->
        <div class="space-y-1.5 mb-4">
            <?php
            if (has_nav_menu('primary')) {
                wp_nav_menu(
                    array(
                        'theme_location' => 'primary',
                        'container' => false,
                        'items_wrap' => '%3$s', // No UL wrapper, walker handles structure
                        'walker' => new KonDernTang_Mobile_Walker(),
                        'fallback_cb' => false,
                    )
                );
            } else {
                // Fallback for no menu assigned (Simplified Default)
                ?>
                <a href="<?php echo esc_url(home_url('/')); ?>"
                    class="block w-full text-left px-3 py-2.5 text-white font-medium bg-gray-800 rounded-lg flex items-center gap-2.5 hover:bg-gray-750 transition">
                    <i class="ph ph-house text-lg"></i> <?php esc_html_e('หน้าแรก', 'konderntang'); ?>
                </a>
                <?php
            }
            ?>

            <!-- Language Selector Mobile & Search -->
            <div class="border-t border-gray-700 pt-3 flex justify-between items-center">
                <?php get_template_part('template-parts/components/language-switcher'); ?>

                <!-- Mobile Search Button -->
                <?php if (konderntang_get_option('header_show_search', true)): ?>
                    <button onclick="openSearchModal()"
                        class="text-gray-300 hover:text-white p-2 rounded-lg hover:bg-gray-800 transition"
                        aria-label="<?php esc_attr_e('Search', 'konderntang'); ?>">
                        <div class="flex items-center gap-2">
                            <span><?php esc_html_e('ค้นหา', 'konderntang'); ?></span>
                            <i class="ph ph-magnifying-glass text-xl"></i>
                        </div>
                    </button>
                <?php endif; ?>
            </div>
        </div>
</nav>

<?php
/**
 * Default menu fallback
 */
function konderntang_default_menu()
{
    ?>
    <ul class="flex space-x-1 items-center">
        <li><a href="<?php echo esc_url(home_url('/')); ?>"
                class="px-3 py-2 text-white hover:bg-gray-800 rounded-md transition flex items-center gap-1.5">
                <i class="ph ph-house text-lg"></i> <?php esc_html_e('หน้าแรก', 'konderntang'); ?>
            </a></li>
    </ul>
    <?php
}
