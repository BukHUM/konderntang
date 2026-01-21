<?php
/**
 * Navigation Configuration
 *
 * @package KonDernTang
 * @since 1.0.0
 */

return array(
    'menu_locations' => array(
        'primary' => esc_html__( 'Primary Menu', 'konderntang' ),
        'footer'  => esc_html__( 'Footer Menu', 'konderntang' ),
    ),
    'dropdown_menus' => array(
        'travel' => array(
            'archive'       => array(
                'label' => esc_html__( 'เที่ยวทั่วไทย', 'konderntang' ),
                'icon'  => 'ph-map-pin',
                'url'   => home_url( '/category/travel-thailand/' ),
            ),
            'international' => array(
                'label' => esc_html__( 'เที่ยวต่างประเทศ', 'konderntang' ),
                'icon'  => 'ph-globe-hemisphere-east',
                'url'   => home_url( '/category/travel-international/' ),
            ),
            'seasonal'      => array(
                'label' => esc_html__( 'เที่ยวตามฤดูกาล', 'konderntang' ),
                'icon'  => 'ph-sun',
                'url'   => home_url( '/category/travel-seasonal/' ),
            ),
            'guide'         => array(
                'label' => esc_html__( 'คู่มือเดินทาง', 'konderntang' ),
                'icon'  => 'ph-book-open',
                'url'   => home_url( '/category/travel-guide/' ),
            ),
        ),
    ),
);
