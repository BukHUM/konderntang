<?php
/**
 * Template part for displaying navigation bar
 *
 * @package TrendToday
 * @since 1.0.0
 */
?>

<nav class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm" role="navigation" aria-label="Main navigation">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20">
            <!-- Logo -->
            <div class="flex items-center">
                <?php 
                $theme_logo_id = get_option( 'trendtoday_logo', '' );
                $theme_logo_url = $theme_logo_id ? wp_get_attachment_image_url( $theme_logo_id, 'full' ) : '';
                
                if ( $theme_logo_url ) : 
                    // Use theme settings logo
                    ?>
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="flex-shrink-0 group" aria-label="<?php bloginfo( 'name' ); ?>">
                        <img src="<?php echo esc_url( $theme_logo_url ); ?>" alt="<?php bloginfo( 'name' ); ?>" class="h-10 w-auto" />
                    </a>
                <?php elseif ( has_custom_logo() ) : ?>
                    <?php the_custom_logo(); ?>
                <?php else : ?>
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="flex-shrink-0 flex items-center gap-3 group" aria-label="<?php bloginfo( 'name' ); ?>">
                        <!-- Icon G -->
                        <div class="w-10 h-10 bg-black group-hover:bg-accent transition duration-300 text-white flex items-center justify-center rounded-lg font-bold text-2xl shadow-sm">
                            <?php echo substr( get_bloginfo( 'name' ), 0, 1 ); ?>
                        </div>
                        <!-- Text Block -->
                        <div class="flex flex-col justify-center -space-y-1">
                            <span class="font-bold text-2xl text-gray-900 tracking-tight leading-none group-hover:text-gray-700 transition">
                                <?php bloginfo( 'name' ); ?>
                                <?php if ( strpos( get_bloginfo( 'name' ), '.' ) === false ) : ?>
                                    <span class="text-gray-400 text-sm font-normal">.com</span>
                                <?php endif; ?>
                            </span>
                            <span class="text-[10px] font-bold text-accent uppercase tracking-[0.2em] leading-tight"><?php _e( 'Trend Today', 'trendtoday' ); ?></span>
                        </div>
                    </a>
                <?php endif; ?>
            </div>

            <!-- Desktop Menu -->
            <div class="hidden md:flex items-center space-x-8">
                <?php
                // Always use WordPress menu (no fallback)
                wp_nav_menu( array(
                    'theme_location'  => 'primary',
                    'menu_class'      => 'flex items-center space-x-8',
                    'container'       => false,
                    'fallback_cb'     => false,
                    'walker'          => new TrendToday_Walker_Nav_Menu(),
                    'depth'           => 1,
                ) );
                ?>
                <?php
                $search_enabled = get_option( 'trendtoday_search_enabled', '1' );
                $search_suggestions_style = get_option( 'trendtoday_search_suggestions_style', 'dropdown' );
                $search_placeholder = get_option( 'trendtoday_search_placeholder', __( 'พิมพ์คำค้นหา...', 'trendtoday' ) );
                
                if ( $search_enabled === '1' ) :
                    if ( $search_suggestions_style === 'modal' || $search_suggestions_style === 'fullpage' ) :
                ?>
                    <button type="button" 
                            class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 hover:bg-accent hover:text-white transition trendtoday-search-toggle"
                            aria-label="<?php _e( 'ค้นหาข่าว', 'trendtoday' ); ?>">
                        <i class="fas fa-search text-sm"></i>
                    </button>
                <?php else : ?>
                    <div class="relative trendtoday-search-container">
                        <input type="search" 
                               class="trendtoday-search-input w-48 px-4 py-2 pl-10 pr-10 rounded-full border border-gray-200 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent text-sm"
                               placeholder="<?php echo esc_attr( $search_placeholder ); ?>"
                               autocomplete="off" />
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <div class="trendtoday-search-suggestions absolute top-full left-0 right-0 mt-2 bg-white border border-gray-200 rounded-lg shadow-lg z-50 hidden max-h-96 overflow-y-auto"></div>
                    </div>
                <?php
                    endif;
                else :
                ?>
                    <a href="<?php echo esc_url( home_url( '/?s=' ) ); ?>" 
                       class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 hover:bg-accent hover:text-white transition"
                       aria-label="<?php _e( 'ค้นหาข่าว', 'trendtoday' ); ?>">
                        <i class="fas fa-search text-sm"></i>
                    </a>
                <?php endif; ?>
            </div>

            <!-- Mobile menu button -->
            <div class="flex items-center md:hidden">
                <button onclick="toggleMobileMenu()" 
                        aria-label="Toggle menu" 
                        aria-expanded="false" 
                        id="mobile-menu-button"
                        class="text-gray-500 hover:text-gray-900 focus:outline-none p-2">
                    <i class="fas fa-bars text-2xl" id="menu-icon"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu Panel -->
    <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-gray-100 absolute w-full shadow-lg" role="menu">
        <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
            <?php
            // Always use WordPress menu (no fallback)
            wp_nav_menu( array(
                'theme_location' => 'primary',
                'menu_class'     => 'space-y-1',
                'container'      => false,
                'fallback_cb'    => false,
                'walker'         => new TrendToday_Walker_Nav_Menu_Mobile(),
                'depth'          => 1,
            ) );
            ?>
        </div>
    </div>
</nav>
