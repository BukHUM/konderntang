<?php
/**
 * The footer template file
 *
 * @package TrendToday
 * @since 1.0.0
 */
?>

    <!-- Footer -->
    <footer class="bg-gradient-to-b from-gray-50 to-white border-t-2 border-gray-200 mt-16 pt-6 pb-6 relative overflow-hidden">
        <!-- Decorative background elements -->
        <div class="absolute top-0 left-0 w-full h-full opacity-5 pointer-events-none">
            <div class="absolute top-20 right-10 w-64 h-64 bg-accent rounded-full blur-3xl"></div>
            <div class="absolute bottom-20 left-10 w-48 h-48 bg-blue-500 rounded-full blur-3xl"></div>
        </div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <!-- Newsletter Section - Full Width -->
            <div class="mb-10 mt-0">
                <div class="bg-gradient-to-r from-accent/10 via-orange-50 to-accent/5 border border-accent/20 rounded-2xl p-6 md:p-8 shadow-sm">
                    <div class="flex flex-col md:flex-row items-start md:items-center gap-4 md:gap-6">
                        <div class="flex-shrink-0">
                            <div class="w-16 h-16 bg-gradient-to-br from-accent to-orange-600 rounded-xl flex items-center justify-center shadow-lg">
                                <i class="fas fa-envelope text-white text-2xl"></i>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-xl text-gray-900 mb-1">
                                <?php _e( 'ไม่พลาดทุกเทรนด์', 'trendtoday' ); ?>
                            </h3>
                            <p class="text-gray-600 text-sm mb-0 md:mb-0">
                                <?php _e( 'สมัครรับข่าวสารสรุปประจำวันส่งตรงถึงอีเมลของคุณ', 'trendtoday' ); ?>
                            </p>
                        </div>
                        <div class="w-full md:w-auto md:flex-shrink-0">
                            <form class="flex flex-col sm:flex-row gap-2" onsubmit="event.preventDefault(); handleNewsletterSubmit(event);" aria-label="<?php _e( 'Newsletter subscription', 'trendtoday' ); ?>">
                                <input type="email" 
                                       placeholder="<?php _e( 'ใส่อีเมลของคุณ', 'trendtoday' ); ?>" 
                                       required
                                       aria-label="<?php _e( 'Email address', 'trendtoday' ); ?>"
                                       class="newsletter-input flex-1 px-4 py-3 rounded-lg bg-white border border-gray-200 text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition-all duration-200 shadow-sm"
                                       id="footer-newsletter-email">
                                <button type="submit"
                                        class="bg-gradient-to-r from-accent to-orange-600 hover:from-orange-600 hover:to-accent text-white font-semibold px-6 py-3 rounded-lg transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105 whitespace-nowrap">
                                    <i class="fas fa-paper-plane mr-2"></i><?php _e( 'สมัครรับข่าวสาร', 'trendtoday' ); ?>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Main Footer Content -->
            <div class="hidden md:grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 md:gap-6 lg:gap-8 mb-10">
                <!-- Footer Widget 1 - Brand -->
                <div>
                    <?php if ( is_active_sidebar( 'footer-1' ) ) : ?>
                        <?php dynamic_sidebar( 'footer-1' ); ?>
                    <?php else : ?>
                        <!-- Default: Brand Column -->
                        <div class="space-y-4">
                            <div class="flex items-center gap-3 mb-4">
                                <?php 
                                $theme_logo_id = get_option( 'trendtoday_logo', '' );
                                $theme_logo_url = $theme_logo_id ? wp_get_attachment_image_url( $theme_logo_id, 'full' ) : '';
                                
                                if ( $theme_logo_url ) : 
                                    // Use theme settings logo
                                    ?>
                                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php bloginfo( 'name' ); ?>" class="hover:opacity-80 transition-opacity">
                                        <img src="<?php echo esc_url( $theme_logo_url ); ?>" alt="<?php bloginfo( 'name' ); ?>" class="h-10 w-auto" />
                                    </a>
                                <?php elseif ( has_custom_logo() ) : ?>
                                    <?php the_custom_logo(); ?>
                                <?php else : ?>
                                    <div class="w-10 h-10 bg-gradient-to-br from-accent to-orange-600 text-white flex items-center justify-center rounded-lg font-bold text-lg shadow-md">
                                        T
                                    </div>
                                    <span class="font-bold text-2xl tracking-tight text-gray-900"><?php bloginfo( 'name' ); ?></span>
                                <?php endif; ?>
                            </div>
                            <p class="text-gray-600 text-base leading-relaxed">
                                <?php bloginfo( 'description' ); ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Footer Widget 2 - Categories -->
                <div>
                    <?php if ( is_active_sidebar( 'footer-2' ) ) : ?>
                        <?php dynamic_sidebar( 'footer-2' ); ?>
                    <?php else : ?>
                        <!-- Default: Category Column -->
                        <button class="flex justify-between items-center w-full py-3 md:py-0 text-left md:cursor-default group"
                                onclick="toggleFooter(this)"
                                aria-expanded="false"
                                aria-controls="footer-categories">
                            <h4 class="font-bold text-gray-900 text-base md:mb-5 group-hover:text-accent transition-colors">
                                <i class="fas fa-folder-open text-accent mr-2"></i><?php _e( 'หมวดหมู่', 'trendtoday' ); ?>
                            </h4>
                            <i class="fas fa-chevron-down text-gray-400 text-sm md:hidden transition-transform duration-300 transform"></i>
                        </button>
                        <ul id="footer-categories" class="footer-links space-y-2.5 text-sm text-gray-600 hidden md:block" role="list">
                            <?php
                            wp_list_categories( array(
                                'title_li' => '',
                                'number'   => 6,
                                'walker'   => new Walker_Category_Footer(),
                            ) );
                            ?>
                        </ul>
                    <?php endif; ?>
                </div>

                <!-- Footer Widget 3 - About -->
                <div>
                    <?php if ( is_active_sidebar( 'footer-3' ) ) : ?>
                        <?php dynamic_sidebar( 'footer-3' ); ?>
                    <?php else : ?>
                        <!-- Default: About Column -->
                        <button class="flex justify-between items-center w-full py-3 md:py-0 text-left md:cursor-default group"
                                onclick="toggleFooter(this)"
                                aria-expanded="false"
                                aria-controls="footer-about">
                            <h4 class="font-bold text-gray-900 text-base md:mb-5 group-hover:text-accent transition-colors">
                                <i class="fas fa-info-circle text-accent mr-2"></i><?php _e( 'เกี่ยวกับเรา', 'trendtoday' ); ?>
                            </h4>
                            <i class="fas fa-chevron-down text-gray-400 text-sm md:hidden transition-transform duration-300 transform"></i>
                        </button>
                        <?php
                        if ( has_nav_menu( 'footer' ) ) {
                            wp_nav_menu( array(
                                'theme_location' => 'footer',
                                'menu_class'     => 'footer-links space-y-2.5 text-sm text-gray-600 hidden md:block',
                                'container'      => false,
                                'fallback_cb'    => false,
                                'depth'          => 1,
                                'walker'         => new Walker_Nav_Menu_Footer(),
                            ) );
                        } else {
                            // Fallback menu
                            ?>
                            <ul id="footer-about" class="footer-links space-y-2.5 text-sm text-gray-600 hidden md:block" role="list">
                                <li>
                                    <a href="#" class="footer-link hover:text-accent transition-colors duration-200 inline-flex items-center gap-2">
                                        <i class="fas fa-chevron-right text-xs text-gray-400"></i>
                                        <?php _e( 'ติดต่อโฆษณา', 'trendtoday' ); ?>
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="footer-link hover:text-accent transition-colors duration-200 inline-flex items-center gap-2">
                                        <i class="fas fa-chevron-right text-xs text-gray-400"></i>
                                        <?php _e( 'ร่วมงานกับเรา', 'trendtoday' ); ?>
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="footer-link hover:text-accent transition-colors duration-200 inline-flex items-center gap-2">
                                        <i class="fas fa-chevron-right text-xs text-gray-400"></i>
                                        <?php _e( 'นโยบายความเป็นส่วนตัว', 'trendtoday' ); ?>
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="footer-link hover:text-accent transition-colors duration-200 inline-flex items-center gap-2">
                                        <i class="fas fa-chevron-right text-xs text-gray-400"></i>
                                        <?php _e( 'เงื่อนไขการใช้งาน', 'trendtoday' ); ?>
                                    </a>
                                </li>
                            </ul>
                            <?php
                        }
                        ?>
                    <?php endif; ?>
                </div>

                <!-- Footer Widget 4 - Social Media -->
                <div>
                    <?php if ( is_active_sidebar( 'footer-4' ) ) : ?>
                        <?php dynamic_sidebar( 'footer-4' ); ?>
                    <?php else : ?>
                        <!-- Default: Follow Column -->
                        <button class="flex justify-between items-center w-full py-3 md:py-0 text-left md:cursor-default group"
                                onclick="toggleFooter(this)"
                                aria-expanded="false"
                                aria-controls="footer-social">
                            <h4 class="font-bold text-gray-900 text-base md:mb-5 group-hover:text-accent transition-colors">
                                <i class="fas fa-share-alt text-accent mr-2"></i><?php _e( 'ติดตามเรา', 'trendtoday' ); ?>
                            </h4>
                            <i class="fas fa-chevron-down text-gray-400 text-sm md:hidden transition-transform duration-300 transform"></i>
                        </button>
                        <div id="footer-social" class="footer-links hidden md:block">
                            <p class="text-gray-600 text-sm mb-4"><?php _e( 'ติดตามข่าวสารและอัปเดตล่าสุด', 'trendtoday' ); ?></p>
                            <div class="flex flex-wrap gap-3">
                                <a href="#" 
                                   class="footer-social-icon w-12 h-12 rounded-full bg-gradient-to-br from-blue-600 to-blue-700 flex items-center justify-center text-white hover:from-blue-700 hover:to-blue-800 transition-all duration-300 shadow-md hover:shadow-lg transform hover:scale-110"
                                   aria-label="<?php _e( 'Facebook', 'trendtoday' ); ?>"
                                   target="_blank"
                                   rel="noopener noreferrer">
                                    <i class="fab fa-facebook-f text-base"></i>
                                </a>
                                <a href="#" 
                                   class="footer-social-icon w-12 h-12 rounded-full bg-gradient-to-br from-sky-500 to-sky-600 flex items-center justify-center text-white hover:from-sky-600 hover:to-sky-700 transition-all duration-300 shadow-md hover:shadow-lg transform hover:scale-110"
                                   aria-label="<?php _e( 'Twitter', 'trendtoday' ); ?>"
                                   target="_blank"
                                   rel="noopener noreferrer">
                                    <i class="fab fa-twitter text-base"></i>
                                </a>
                                <a href="#" 
                                   class="footer-social-icon w-12 h-12 rounded-full bg-gradient-to-br from-pink-600 to-pink-700 flex items-center justify-center text-white hover:from-pink-700 hover:to-pink-800 transition-all duration-300 shadow-md hover:shadow-lg transform hover:scale-110"
                                   aria-label="<?php _e( 'Instagram', 'trendtoday' ); ?>"
                                   target="_blank"
                                   rel="noopener noreferrer">
                                    <i class="fab fa-instagram text-base"></i>
                                </a>
                                <a href="#" 
                                   class="footer-social-icon w-12 h-12 rounded-full bg-gradient-to-br from-red-600 to-red-700 flex items-center justify-center text-white hover:from-red-700 hover:to-red-800 transition-all duration-300 shadow-md hover:shadow-lg transform hover:scale-110"
                                   aria-label="<?php _e( 'YouTube', 'trendtoday' ); ?>"
                                   target="_blank"
                                   rel="noopener noreferrer">
                                    <i class="fab fa-youtube text-base"></i>
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Trending Tags Section -->
            <?php get_template_part( 'template-parts/trending-tags' ); ?>
            
            <!-- Copyright Section -->
            <div class="border-t border-gray-200 pt-6 mt-8">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4 text-sm text-gray-500">
                    <div class="text-center md:text-left">
                        &copy; <?php echo date( 'Y' ); ?> <span class="font-semibold text-gray-700"><?php bloginfo( 'name' ); ?></span>. <?php _e( 'All rights reserved.', 'trendtoday' ); ?>
                    </div>
                    <div class="flex items-center gap-6 text-xs">
                        <a href="#" class="hover:text-accent transition-colors"><?php _e( 'Privacy Policy', 'trendtoday' ); ?></a>
                        <span class="text-gray-300">|</span>
                        <a href="#" class="hover:text-accent transition-colors"><?php _e( 'Terms of Service', 'trendtoday' ); ?></a>
                        <span class="text-gray-300">|</span>
                        <a href="#" class="hover:text-accent transition-colors"><?php _e( 'Contact', 'trendtoday' ); ?></a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    
    <?php
    // Custom Walker for Footer Categories
    class Walker_Category_Footer extends Walker_Category {
        function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
            $cat_name = esc_attr( $category->name );
            $cat_link = esc_url( get_category_link( $category->term_id ) );
            
            $output .= '<li class="footer-link-item">';
            $output .= '<a href="' . $cat_link . '" class="footer-link hover:text-accent transition-colors duration-200 inline-flex items-center gap-2">';
            $output .= '<i class="fas fa-chevron-right text-xs text-gray-400"></i>';
            $output .= $cat_name;
            $output .= '</a></li>';
        }
    }
    
    // Custom Walker for Footer Menu
    class Walker_Nav_Menu_Footer extends Walker_Nav_Menu {
        function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
            $output .= '<li class="footer-link-item">';
            $output .= '<a href="' . esc_url( $item->url ) . '" class="footer-link hover:text-accent transition-colors duration-200 inline-flex items-center gap-2">';
            $output .= '<i class="fas fa-chevron-right text-xs text-gray-400"></i>';
            $output .= esc_html( $item->title );
            $output .= '</a></li>';
        }
    }
    ?>

    <?php
    // Floating Social Share Buttons
    if ( is_single() ) {
        $display_positions = get_option( 'trendtoday_social_display_positions', array( 'single_bottom' ) );
        if ( in_array( 'floating', $display_positions ) ) {
            get_template_part( 'template-parts/social-share-floating' );
        }
        
        // Table of Contents - Floating
        $toc_enabled = get_option( 'trendtoday_toc_enabled', '1' );
        $toc_position = get_option( 'trendtoday_toc_position', 'top' );
        if ( $toc_enabled === '1' && $toc_position === 'floating' ) {
            get_template_part( 'template-parts/table-of-contents' );
        }
    }
    
    // Search Modal
    get_template_part( 'template-parts/search-modal' );
    ?>

    <!-- Back to Top Button -->
    <button id="back-to-top" 
            class="fixed bottom-8 right-8 bg-accent text-white p-4 rounded-full shadow-lg hover:bg-orange-600 transition-all duration-300 opacity-0 invisible z-50 hover:scale-110"
            aria-label="<?php _e( 'กลับขึ้นด้านบน', 'trendtoday' ); ?>"
            onclick="window.scrollTo({top: 0, behavior: 'smooth'})">
        <i class="fas fa-arrow-up text-lg"></i>
    </button>

<?php wp_footer(); ?>
</body>
</html>
