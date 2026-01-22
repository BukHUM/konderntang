<?php
/**
 * Custom Post Types
 *
 * @package KonDernTang
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register Custom Post Types
 * 
 * NOTE: If MU-Plugin 'konderntang-custom-types.php' exists, 
 * this function will be skipped to avoid duplicate registration.
 */
function konderntang_register_post_types() {
    // Check if MU-Plugin is handling registration
    if ( function_exists( 'konderntang_mu_register_post_types' ) ) {
        return; // MU-Plugin is handling this, skip theme registration
    }
    // Travel Guides Post Type
    register_post_type(
        'travel_guide',
        array(
            'labels'              => array(
                'name'               => esc_html__( 'Travel Guides', 'konderntang' ),
                'singular_name'      => esc_html__( 'Travel Guide', 'konderntang' ),
                'menu_name'          => esc_html__( 'Travel Guides', 'konderntang' ),
                'add_new'            => esc_html__( 'Add New', 'konderntang' ),
                'add_new_item'       => esc_html__( 'Add New Travel Guide', 'konderntang' ),
                'edit_item'          => esc_html__( 'Edit Travel Guide', 'konderntang' ),
                'new_item'           => esc_html__( 'New Travel Guide', 'konderntang' ),
                'view_item'          => esc_html__( 'View Travel Guide', 'konderntang' ),
                'search_items'       => esc_html__( 'Search Travel Guides', 'konderntang' ),
                'not_found'          => esc_html__( 'No travel guides found', 'konderntang' ),
                'not_found_in_trash' => esc_html__( 'No travel guides found in Trash', 'konderntang' ),
                'all_items'          => esc_html__( 'All Travel Guides', 'konderntang' ),
            ),
            'public'              => true,
            'has_archive'         => true,
            'publicly_queryable'  => true,
            'show_ui'             => true,
            'show_in_menu'        => 'konderntang',
            'show_in_nav_menus'   => true,
            'show_in_admin_bar'   => true,
            'menu_icon'           => 'dashicons-location-alt',
            'capability_type'     => 'post',
            'hierarchical'        => false,
            'supports'            => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments', 'custom-fields', 'revisions' ),
            'rewrite'             => array( 'slug' => 'travel-guide', 'with_front' => false ),
            'query_var'           => true,
            'can_export'          => true,
            'show_in_rest'        => true,
        )
    );

    // Hotels Post Type
    register_post_type(
        'hotel',
        array(
            'labels'              => array(
                'name'               => esc_html__( 'Hotels', 'konderntang' ),
                'singular_name'      => esc_html__( 'Hotel', 'konderntang' ),
                'menu_name'          => esc_html__( 'Hotels', 'konderntang' ),
                'add_new'            => esc_html__( 'Add New', 'konderntang' ),
                'add_new_item'       => esc_html__( 'Add New Hotel', 'konderntang' ),
                'edit_item'          => esc_html__( 'Edit Hotel', 'konderntang' ),
                'new_item'           => esc_html__( 'New Hotel', 'konderntang' ),
                'view_item'          => esc_html__( 'View Hotel', 'konderntang' ),
                'search_items'       => esc_html__( 'Search Hotels', 'konderntang' ),
                'not_found'          => esc_html__( 'No hotels found', 'konderntang' ),
                'not_found_in_trash' => esc_html__( 'No hotels found in Trash', 'konderntang' ),
                'all_items'          => esc_html__( 'All Hotels', 'konderntang' ),
            ),
            'public'              => true,
            'has_archive'         => true,
            'publicly_queryable'  => true,
            'show_ui'             => true,
            'show_in_menu'        => 'konderntang',
            'show_in_nav_menus'   => true,
            'show_in_admin_bar'   => true,
            'menu_icon'           => 'dashicons-building',
            'capability_type'     => 'post',
            'hierarchical'        => false,
            'supports'            => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments', 'custom-fields', 'revisions' ),
            'rewrite'             => array( 'slug' => 'hotel', 'with_front' => false ),
            'query_var'           => true,
            'can_export'          => true,
            'show_in_rest'        => true,
        )
    );

    // Promotions Post Type
    register_post_type(
        'promotion',
        array(
            'labels'              => array(
                'name'               => esc_html__( 'Promotions', 'konderntang' ),
                'singular_name'      => esc_html__( 'Promotion', 'konderntang' ),
                'menu_name'          => esc_html__( 'Promotions', 'konderntang' ),
                'add_new'            => esc_html__( 'Add New', 'konderntang' ),
                'add_new_item'       => esc_html__( 'Add New Promotion', 'konderntang' ),
                'edit_item'          => esc_html__( 'Edit Promotion', 'konderntang' ),
                'new_item'           => esc_html__( 'New Promotion', 'konderntang' ),
                'view_item'          => esc_html__( 'View Promotion', 'konderntang' ),
                'search_items'       => esc_html__( 'Search Promotions', 'konderntang' ),
                'not_found'          => esc_html__( 'No promotions found', 'konderntang' ),
                'not_found_in_trash' => esc_html__( 'No promotions found in Trash', 'konderntang' ),
                'all_items'          => esc_html__( 'All Promotions', 'konderntang' ),
            ),
            'public'              => true,
            'has_archive'         => true,
            'publicly_queryable'  => true,
            'show_ui'             => true,
            'show_in_menu'        => 'konderntang',
            'show_in_nav_menus'   => true,
            'show_in_admin_bar'   => true,
            'menu_icon'           => 'dashicons-tag',
            'capability_type'     => 'post',
            'hierarchical'        => false,
            'supports'            => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments', 'custom-fields', 'revisions' ),
            'rewrite'             => array( 'slug' => 'promotion', 'with_front' => false ),
            'query_var'           => true,
            'can_export'          => true,
            'show_in_rest'        => true,
        )
    );
}
add_action( 'init', 'konderntang_register_post_types' );

/**
 * Register Custom Taxonomies
 * 
 * NOTE: If MU-Plugin 'konderntang-custom-types.php' exists, 
 * this function will be skipped to avoid duplicate registration.
 */
function konderntang_register_taxonomies() {
    // Check if MU-Plugin is handling registration
    if ( function_exists( 'konderntang_mu_register_taxonomies' ) ) {
        return; // MU-Plugin is handling this, skip theme registration
    }
    // Destinations Taxonomy (for Travel Guides)
    register_taxonomy(
        'destination',
        array( 'travel_guide', 'post' ),
        array(
            'labels'            => array(
                'name'              => esc_html__( 'Destinations', 'konderntang' ),
                'singular_name'     => esc_html__( 'Destination', 'konderntang' ),
                'search_items'      => esc_html__( 'Search Destinations', 'konderntang' ),
                'all_items'         => esc_html__( 'All Destinations', 'konderntang' ),
                'parent_item'       => esc_html__( 'Parent Destination', 'konderntang' ),
                'parent_item_colon' => esc_html__( 'Parent Destination:', 'konderntang' ),
                'edit_item'         => esc_html__( 'Edit Destination', 'konderntang' ),
                'update_item'       => esc_html__( 'Update Destination', 'konderntang' ),
                'add_new_item'      => esc_html__( 'Add New Destination', 'konderntang' ),
                'new_item_name'     => esc_html__( 'New Destination Name', 'konderntang' ),
                'menu_name'         => esc_html__( 'Destinations', 'konderntang' ),
            ),
            'hierarchical'      => true,
            'public'            => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array( 'slug' => 'destination' ),
            'show_in_rest'      => true,
        )
    );

    // Travel Types Taxonomy (for Travel Guides)
    register_taxonomy(
        'travel_type',
        array( 'travel_guide', 'post' ),
        array(
            'labels'            => array(
                'name'              => esc_html__( 'Travel Types', 'konderntang' ),
                'singular_name'     => esc_html__( 'Travel Type', 'konderntang' ),
                'search_items'      => esc_html__( 'Search Travel Types', 'konderntang' ),
                'all_items'         => esc_html__( 'All Travel Types', 'konderntang' ),
                'edit_item'         => esc_html__( 'Edit Travel Type', 'konderntang' ),
                'update_item'       => esc_html__( 'Update Travel Type', 'konderntang' ),
                'add_new_item'      => esc_html__( 'Add New Travel Type', 'konderntang' ),
                'new_item_name'     => esc_html__( 'New Travel Type Name', 'konderntang' ),
                'menu_name'         => esc_html__( 'Travel Types', 'konderntang' ),
            ),
            'hierarchical'      => false,
            'public'            => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array( 'slug' => 'travel-type' ),
            'show_in_rest'      => true,
        )
    );

    // Hotel Types Taxonomy
    register_taxonomy(
        'hotel_type',
        'hotel',
        array(
            'labels'            => array(
                'name'              => esc_html__( 'Hotel Types', 'konderntang' ),
                'singular_name'     => esc_html__( 'Hotel Type', 'konderntang' ),
                'search_items'      => esc_html__( 'Search Hotel Types', 'konderntang' ),
                'all_items'         => esc_html__( 'All Hotel Types', 'konderntang' ),
                'edit_item'         => esc_html__( 'Edit Hotel Type', 'konderntang' ),
                'update_item'       => esc_html__( 'Update Hotel Type', 'konderntang' ),
                'add_new_item'      => esc_html__( 'Add New Hotel Type', 'konderntang' ),
                'new_item_name'     => esc_html__( 'New Hotel Type Name', 'konderntang' ),
                'menu_name'         => esc_html__( 'Hotel Types', 'konderntang' ),
            ),
            'hierarchical'      => true,
            'public'            => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array( 'slug' => 'hotel-type' ),
            'show_in_rest'      => true,
        )
    );
}
add_action( 'init', 'konderntang_register_taxonomies' );

/**
 * Force Destinations and Travel Types to show only in Posts menu
 * This ensures they appear under Posts menu instead of Travel Guides menu
 * 
 * NOTE: If MU-Plugin is active, this will be handled by MU-Plugin instead.
 */
function konderntang_taxonomy_menu_placement( $parent_file ) {
    // Check if MU-Plugin is handling this
    if ( function_exists( 'konderntang_mu_taxonomy_menu_placement' ) ) {
        return $parent_file; // Let MU-Plugin handle it
    }
    global $current_screen;
    
    // Force Destinations and Travel Types to show under Posts menu
    if ( isset( $current_screen->taxonomy ) ) {
        if ( in_array( $current_screen->taxonomy, array( 'destination', 'travel_type' ), true ) ) {
            return 'edit.php';
        }
    }
    
    return $parent_file;
}
add_filter( 'parent_file', 'konderntang_taxonomy_menu_placement' );

/**
 * Remove taxonomy submenus from Travel Guides menu
 * 
 * NOTE: If MU-Plugin is active, this will be handled by MU-Plugin instead.
 */
function konderntang_remove_taxonomy_from_cpt_menu() {
    // Check if MU-Plugin is handling this
    if ( function_exists( 'konderntang_mu_remove_taxonomy_from_cpt_menu' ) ) {
        return; // Let MU-Plugin handle it
    }
    global $submenu;
    
    // Remove Destinations and Travel Types from Travel Guides menu
    if ( isset( $submenu['edit.php?post_type=travel_guide'] ) ) {
        foreach ( $submenu['edit.php?post_type=travel_guide'] as $key => $item ) {
            if ( isset( $item[2] ) ) {
                if ( strpos( $item[2], 'taxonomy=destination' ) !== false || 
                     strpos( $item[2], 'taxonomy=travel_type' ) !== false ) {
                    unset( $submenu['edit.php?post_type=travel_guide'][ $key ] );
                }
            }
        }
    }
}
add_action( 'admin_menu', 'konderntang_remove_taxonomy_from_cpt_menu', 999 );