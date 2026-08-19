<?php
/**
 * BeShop free Theme Customizer
 *
 * @package BeShop free
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */


function beshop_freecustomize_register( $wp_customize ) {

    $wp_customize->remove_control('beshop_blog_style');
    $wp_customize->remove_control('beshop_shop_style');
    
    $wp_customize->add_setting('beshopfree_blog_style', array(
        'default'        => 'style4',
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
        'sanitize_callback' => 'beshop_sanitize_select',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('beshopfree_blog_style', array(
        'label'      => __('Select Blog Style', 'beshop-free'),
        'section'    => 'beshop_blog',
        'settings'   => 'beshopfree_blog_style',
        'type'       => 'select',
        'choices'    => array(
            'style1' => __('List over Image', 'beshop-free'),
            'style2' => __('List Style', 'beshop-free'),
            'style3' => __('Classic Style', 'beshop-free'),
            'style4' => __('Grid Style', 'beshop-free'),
        ),
    ));
    $wp_customize->add_setting('beshop_shop_style', array(
        'default'        => '2',
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
        'sanitize_callback' => 'beshopwoo_sanitize_select',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('beshop_shop_style', array(
        'label'      => __('Select Products Style', 'beshop-free'),
        'section'    => 'beshop_shop',
        'settings'   => 'beshop_shop_style',
        'type'       => 'select',
        'choices'    => array(
            '1' => __('Style One', 'beshop-free'),
            '2' => __('Style Two', 'beshop-free'),
        ),
    ));
    


}
add_action( 'customize_register', 'beshop_freecustomize_register',99 );

