<?php
/**
 * BeShop Theme Customizer
 *
 * @package BeShop
 */

if ( ! function_exists( 'beshop_sanitize_image' ) ) :
function beshop_sanitize_image( $input ){
    /* default output */
    $output = '';
    /* check file type */
    $filetype = wp_check_filetype( $input );
    $mime_type = $filetype['type'];
    /* only mime type "image" allowed */
    if ( strpos( $mime_type, 'image' ) !== false ){
        $output = $input;
    }
    return $output;
}
endif;

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function beshopwoo_customize_register( $wp_customize ) {

	//select sanitization function
        function beshopwoo_sanitize_select( $input, $setting ){
            $input = sanitize_key($input);
            $choices = $setting->manager->get_control( $setting->id )->choices;
            return ( array_key_exists( $input, $choices ) ? $input : $setting->default );                
              
        }

    $wp_customize->add_section(
            'beshop_general',
            array(
                'title'    => __( 'BeShop General Settings', 'beshop' ),
                'priority' => 5,
                'panel'    => 'woocommerce',
            )
    );
    $wp_customize->add_setting('beshop_basket_visibility', array(
        'default'        => 'all',
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
        'sanitize_callback' => 'beshopwoo_sanitize_select',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('beshop_basket_visibility', array(
        'label'      => __('Shoping Basket Visibility', 'beshop'),
        'section'    => 'beshop_general',
        'settings'   => 'beshop_basket_visibility',
        'type'       => 'select',
        'choices'    => array(
            'shop' => __('Show Only Shop Page', 'beshop'),
            'all' => __('Show All Page', 'beshop'),
            'hide' => __('Hide Shoping Basket', 'beshop'),
        ),
    ));
    $wp_customize->add_setting('beshop_basket_position', array(
        'default'        => 'right',
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
        'sanitize_callback' => 'beshopwoo_sanitize_select',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('beshop_basket_position', array(
        'label'      => __('Shoping Basket Position', 'beshop'),
        'section'    => 'beshop_general',
        'settings'   => 'beshop_basket_position',
        'type'       => 'select',
        'choices'    => array(
            'left' => __('Left Side', 'beshop'),
            'right' => __('Right Side', 'beshop'),
        ),
    ));
    $wp_customize->add_setting('beshop_breadcrump_show', array(
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
        'default'       =>  1,
        'sanitize_callback' => 'absint',
         'transport' => 'refresh',

    ));
    $wp_customize->add_control('beshop_breadcrump_show', array(
        'label'      => __('Display Shop Breadcrumb', 'beshop'),
        'description'     => __('You can show or hide shop breadcrumb.', 'beshop'),
        'section'    => 'beshop_general',
        'settings'   => 'beshop_breadcrump_show',
        'type'       => 'checkbox',
    ));
    $wp_customize->add_setting('beshop_breadcrump_position', array(
        'default'        => 'left',
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
        'sanitize_callback' => 'beshopwoo_sanitize_select',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('beshop_breadcrump_position', array(
        'label'      => __('Products Breadcrumb Position', 'beshop'),
        'section'    => 'beshop_general',
        'settings'   => 'beshop_breadcrump_position',
        'type'       => 'select',
        'choices'    => array(
            'left' => __('Left', 'beshop'),
            'center' => __('Center', 'beshop'),
            'right' => __('Right', 'beshop'),
        ),
    ));
    $wp_customize->add_setting('beshop_breadcrump_color', array(
        'default' => '#222',
        'type' =>'theme_mod',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'refresh',
    ));
    // Add color control 
    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize, 'beshop_breadcrump_color', array(
                'label' => __('Breadcrump Text Color','beshop'),
                'section' => 'beshop_general'
            )
        )
    );
    $wp_customize->add_setting('beshop_breadcrump_bgcolor', array(
        'default' => '#ededed',
        'type' =>'theme_mod',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'refresh',
    ));
    // Add color control 
    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize, 'beshop_breadcrump_bgcolor', array(
                'label' => __('Breadcrump Background Color','beshop'),
                'section' => 'beshop_general'
            )
        )
    );


    $wp_customize->add_setting('beshop_products_pagination', array(
        'default'        => 'center',
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
        'sanitize_callback' => 'beshopwoo_sanitize_select',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('beshop_products_pagination', array(
        'label'      => __('Products Pagination Position', 'beshop'),
        'section'    => 'beshop_general',
        'settings'   => 'beshop_products_pagination',
        'type'       => 'select',
        'choices'    => array(
            'left' => __('Left', 'beshop'),
            'center' => __('Center', 'beshop'),
            'right' => __('Right', 'beshop'),
        ),
    ));
        $wp_customize->add_setting('beshop_pagitext_color', array(
        'default' => '',
        'type' =>'theme_mod',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'refresh',
    ));
    // Add color control 
    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize, 'beshop_pagitext_color', array(
                'label' => __('Pagination Text Color','beshop'),
                'section' => 'beshop_general'
            )
        )
    );
    $wp_customize->add_setting('beshop_pagibg_color', array(
        'default' => '',
        'type' =>'theme_mod',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'refresh',
    ));
    // Add color control 
    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize, 'beshop_pagibg_color', array(
                'label' => __('Pagination Background Color','beshop'),
                'section' => 'beshop_general'
            )
        )
    );
    $wp_customize->add_section(
            'beshop_shop_banner',
            array(
                'title'    => __( 'Shop Page Banner', 'beshop' ),
                'priority' => 6,
                'panel'    => 'woocommerce',
            )
    );
    $wp_customize->add_setting('beshop_shopbanner_show', array(
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
        'default'       => '',
        'sanitize_callback' => 'absint',
         'transport' => 'refresh',

    ));
    $wp_customize->add_control('beshop_shopbanner_show', array(
        'label'      => __('Display Shop Page banner', 'beshop'),
        'description'     => __('You can show or hide shop page banner.', 'beshop'),
        'section'    => 'beshop_shop_banner',
        'settings'   => 'beshop_shopbanner_show',
        'type'       => 'checkbox',
        'priority'       => 5,
    ));
         // Side menu profile image
    $wp_customize->add_setting('beshop_shopb_img', array(
        'default'        => '',
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
        'sanitize_callback' => beshop_sanitize_image( 'beshop_shopb_img' ),
        'transport' => 'refresh',
    ));

    $wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, 'beshop_shopb_img', array(
        'label' => __( 'Upload Shop Image', 'beshop' ),
        'description' => __( 'You can upload shop image by this option', 'beshop' ),
        'section'    => 'beshop_shop_banner',
        'settings'   => 'beshop_shopb_img',
        'mime_type' => 'image',

    ) ) );
    
     $wp_customize->add_setting('beshop_banner_subtext', array(
        'default' =>  '',
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('beshop_banner_subtext', array(
        'label'      => __('Shop Banner Subtitle', 'beshop'),
        'description'     => __('Enter your home image title here. The title only show in home page', 'beshop'),
        'section'    => 'beshop_shop_banner',
        'settings'   => 'beshop_banner_subtext',
        'type'       => 'text',

    ));

     $wp_customize->add_setting('beshop_banner_title', array(
        'default' =>  '',
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('beshop_banner_title', array(
        'label'      => __('Shop Banner Title', 'beshop'),
        'description'     => __('Enter your shop banner title. Leave empty if don\'t show the title.' , 'beshop'),
        'section'    => 'beshop_shop_banner',
        'settings'   => 'beshop_banner_title',
        'type'       => 'text',
    ));
     $wp_customize->add_setting('beshop_banner_desc', array(
        'default' =>  '',
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('beshop_banner_desc', array(
        'label'      => __('Shop Banner Description', 'beshop'),
        'description'     => __('Enter your shop banner description. Leave empty if don\'t show the title.', 'beshop'),
        'section'    => 'beshop_shop_banner',
        'settings'   => 'beshop_banner_desc',
        'type'       => 'textarea',
    ));
     $wp_customize->add_setting('beshop_banner_btn', array(
        'default' =>  esc_html__('Shop Now','beshop'),
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('beshop_banner_btn', array(
        'label'      => __('Shop Banner Button text', 'beshop'),
        'description'     => __('Enter your shop banner button text.', 'beshop'),
        'section'    => 'beshop_shop_banner',
        'settings'   => 'beshop_banner_btn',
        'type'       => 'text',
    ));
     $wp_customize->add_setting('beshop_banner_url', array(
        'default' =>  '#',
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
        'sanitize_callback' => 'sanitize_url',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('beshop_banner_url', array(
        'label'      => __('Shop Banner Button url', 'beshop'),
        'description'     => __('Enter your shop banner button url. Leave empty if you don\'t want to use the button.' , 'beshop'),
        'section'    => 'beshop_shop_banner',
        'settings'   => 'beshop_banner_url',
        'type'       => 'url',
    ));
    $wp_customize->add_setting('beshop_bannerbtn_color', array(
        'default' => '#fff',
        'type' =>'theme_mod',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'refresh',
    ));
    // Add color control 
    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize, 'beshop_bannerbtn_color', array(
                'label' => __('Button Color','beshop'),
                'section' => 'beshop_shop_banner'
            )
        )
    );
    $wp_customize->add_setting('beshop_bannerbtn_bgcolor', array(
        'default' => '#77A464',
        'type' =>'theme_mod',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'refresh',
    ));
    // Add color control 
    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize, 'beshop_bannerbtn_bgcolor', array(
                'label' => __('Button Background Color','beshop'),
                'section' => 'beshop_shop_banner'
            )
        )
    );
    $wp_customize->add_setting('beshop_text_position', array(
        'default'        => 'left',
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
        'sanitize_callback' => 'beshopwoo_sanitize_select',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('beshop_text_position', array(
        'label'      => __('Text Position', 'beshop'),
        'section'    => 'beshop_shop_banner',
        'settings'   => 'beshop_text_position',
        'type'       => 'select',
        'choices'    => array(
            'left' => __('Left', 'beshop'),
            'center' => __('Center', 'beshop'),
            'right' => __('Right', 'beshop'),
        ),
    ));
    $wp_customize->add_setting('beshop_bannertext_color', array(
        'default' => '#fff',
        'type' =>'theme_mod',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'refresh',
    ));
    // Add color control 
    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize, 'beshop_bannertext_color', array(
                'label' => __('Banner Text Color','beshop'),
                'section' => 'beshop_shop_banner'
            )
        )
    );
    $wp_customize->add_setting( 'beshop_banner_overlay' , array(
    'capability'     => 'edit_theme_options',
    'type'           => 'theme_mod',
    'default'       =>  '',
    'sanitize_callback' => 'absint',
    'transport'     => 'refresh',
    ) );
    $wp_customize->add_control( 'beshop_banner_overlay', array(
        'label'      => __('Show banner overlay? ', 'beshop'),
        'description'=> __('You can show or hide banner overlay.', 'beshop'),
        'section'    => 'beshop_shop_banner',
        'settings'   => 'beshop_banner_overlay',
        'type'       => 'checkbox',
        
    ) );
    
    //End shop page banner

    $wp_customize->add_section(
            'beshop_shop',
            array(
                'title'    => __( 'BeShop Settings', 'beshop' ),
                'priority' => 6,
                'panel'    => 'woocommerce',
            )
    );


     $wp_customize->add_setting('beshop_shop_container', array(
        'default'        => 'container',
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
        'sanitize_callback' => 'beshopwoo_sanitize_select',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('beshop_shop_container', array(
        'label'      => __('Shop Container type', 'beshop'),
        'description'=> __('You can set standard container or full width container for shop. ', 'beshop'),
        'section'    => 'beshop_shop',
        'settings'   => 'beshop_shop_container',
        'type'       => 'select',
        'choices'    => array(
            'container' => __('Standard Container', 'beshop'),
            'container-fluid' => __('Full width Container', 'beshop'),
        ),
    ));
    $wp_customize->add_setting('beshop_shop_layout', array(
        'default'        => 'rightside',
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
        'sanitize_callback' => 'beshopwoo_sanitize_select',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('beshop_shop_layout', array(
        'label'      => __('Select Shop Layout', 'beshop'),
        'description'=> __('Right and Left sidebar only show when shop sidebar widget is available. ', 'beshop'),
        'section'    => 'beshop_shop',
        'settings'   => 'beshop_shop_layout',
        'type'       => 'select',
        'choices'    => array(
            'rightside' => __('Right Sidebar', 'beshop'),
            'leftside' => __('Left Sidebar', 'beshop'),
            'fullwidth' => __('Full Width', 'beshop'),
        ),
    ));
        
    
   $wp_customize->add_setting('beshop_shop_title', array(
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
        'default'       =>  esc_html__('Shop','beshop'),
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('beshop_shop_title', array(
        'label'      => __('Shop Page Title', 'beshop'),
        'description'     => esc_html__('Enter your shop page title. Leave empty if you don\'t want the title.','beshop'),
        'section'    => 'beshop_shop',
        'settings'   => 'beshop_shop_title',
        'type'       => 'text',
    ));
    $wp_customize->add_setting('beshop_title_position', array(
        'default'        => 'center',
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
        'sanitize_callback' => 'beshopwoo_sanitize_select',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('beshop_title_position', array(
        'label'      => __('Title Position', 'beshop'),
        'section'    => 'beshop_shop',
        'settings'   => 'beshop_title_position',
        'type'       => 'select',
        'choices'    => array(
            'left' => __('Left', 'beshop'),
            'center' => __('Center', 'beshop'),
            'right' => __('Right', 'beshop'),
        ),
    ));
    $wp_customize->add_setting('beshop_titlecolor', array(
        'default' => '',
        'type' =>'theme_mod',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'refresh',
    ));
    // Add color control 
    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize, 'beshop_titlecolor', array(
                'label' => __('Shop Title Color','beshop'),
                'section' => 'beshop_shop'
            )
        )
    );
    $wp_customize->add_setting('beshop_ftwidget_position', array(
        'default'        => 'center',
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
        'sanitize_callback' => 'beshopwoo_sanitize_select',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('beshop_ftwidget_position', array(
        'label'      => __('Shop Page Top Widget Position', 'beshop'),
        'description'      => __('Set filter widget from widget section for fiilter shop page products. You can set posotion filiter items by this opiton.', 'beshop'),
        'section'    => 'beshop_shop',
        'settings'   => 'beshop_ftwidget_position',
        'type'       => 'select',
        'choices'    => array(
            'left' => __('Left', 'beshop'),
            'center' => __('Center', 'beshop'),
            'right' => __('Right', 'beshop'),
        ),
    ));
    $wp_customize->add_setting('beshop_ftwidget_color', array(
        'default' => '',
        'type' =>'theme_mod',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'refresh',
    ));
    // Add color control 
    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize, 'beshop_ftwidget_color', array(
                'label' => __('Shop Top Widget Text Color','beshop'),
                'section' => 'beshop_shop'
            )
        )
    );
    $wp_customize->add_setting('beshop_ftwidget_hvcolor', array(
        'default' => '',
        'type' =>'theme_mod',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'refresh',
    ));
    // Add color control 
    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize, 'beshop_ftwidget_hvcolor', array(
                'label' => __('Shop Top Widget Text Hover Color','beshop'),
                'section' => 'beshop_shop'
            )
        )
    );
    $wp_customize->add_setting('beshop_ftwidget_bgcolor', array(
        'default' => '',
        'type' =>'theme_mod',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'refresh',
    ));
    // Add color control 
    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize, 'beshop_ftwidget_bgcolor', array(
                'label' => __('Shop Top Widget Background Color','beshop'),
                'section' => 'beshop_shop'
            )
        )
    );
    $wp_customize->add_setting( 'beshop_resultcount' , array(
    'capability'     => 'edit_theme_options',
    'type'           => 'theme_mod',
    'default'       =>  '1',
    'sanitize_callback' => 'absint',
    'transport'     => 'refresh',
    ) );
    $wp_customize->add_control( 'beshop_resultcount', array(
        'label'      => __('Show Result Count? ', 'beshop'),
        'section'    => 'beshop_shop',
        'settings'   => 'beshop_resultcount',
        'type'       => 'checkbox',
    ) );
    $wp_customize->add_setting( 'beshop_porder' , array(
    'capability'     => 'edit_theme_options',
    'type'           => 'theme_mod',
    'default'       =>  '1',
    'sanitize_callback' => 'absint',
    'transport'     => 'refresh',
    ) );
    $wp_customize->add_control( 'beshop_porder', array(
        'label'      => __('Show Products Order? ', 'beshop'),
        'section'    => 'beshop_shop',
        'settings'   => 'beshop_porder',
        'type'       => 'checkbox',
    ) );
    $wp_customize->add_setting('beshop_shop_column', array(
        'default'        => '4',
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
        'sanitize_callback' => 'beshopwoo_sanitize_select',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('beshop_shop_column', array(
        'label'      => __('Set Product Per row', 'beshop'),
        'section'    => 'beshop_shop',
        'settings'   => 'beshop_shop_column',
        'type'       => 'select',
        'choices'    => array(
            '5' => __('Five Products', 'beshop'),
            '4' => __('Four Products', 'beshop'),
            '3' => __('Three Products', 'beshop'),
            '2' => __('Two Products', 'beshop'),
        ),
    ));
    $wp_customize->add_setting('beshop_shop_style', array(
        'default'        => '1',
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
        'sanitize_callback' => 'beshopwoo_sanitize_select',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('beshop_shop_style', array(
        'label'      => __('Select Products Style', 'beshop'),
        'section'    => 'beshop_shop',
        'settings'   => 'beshop_shop_style',
        'type'       => 'select',
        'choices'    => array(
            '1' => __('Style One', 'beshop'),
            '2' => __('Style Two', 'beshop'),
        ),
    ));
    $wp_customize->add_setting('beshop_product_bgcolor', array(
        'default' => '',
        'type' =>'theme_mod',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'refresh',
    ));
    // Add color control 
    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize, 'beshop_product_bgcolor', array(
                'label' => __('Products Background Color','beshop'),
                'section' => 'beshop_shop'
            )
        )
    );
    $wp_customize->add_setting('beshop_ptitle_color', array(
        'default' => '',
        'type' =>'theme_mod',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'refresh',
    ));
    // Add color control 
    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize, 'beshop_ptitle_color', array(
                'label' => __('Products Title Color','beshop'),
                'section' => 'beshop_shop'
            )
        )
    );
    $wp_customize->add_setting('beshop_prating_color', array(
        'default' => '',
        'type' =>'theme_mod',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'refresh',
    ));
    // Add color control 
    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize, 'beshop_prating_color', array(
                'label' => __('Products Rating Color','beshop'),
                'section' => 'beshop_shop'
            )
        )
    );
    $wp_customize->add_setting('beshop_pprice_color', array(
        'default' => '',
        'type' =>'theme_mod',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'refresh',
    ));
    // Add color control 
    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize, 'beshop_pprice_color', array(
                'label' => __('Products Price Color','beshop'),
                'section' => 'beshop_shop'
            )
        )
    );
    $wp_customize->add_setting('beshop_pbtn_bgcolor', array(
        'default' => '',
        'type' =>'theme_mod',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'refresh',
    ));
    // Add color control 
    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize, 'beshop_pbtn_bgcolor', array(
                'label' => __('Products button background color','beshop'),
                'section' => 'beshop_shop'
            )
        )
    );
    $wp_customize->add_setting('beshop_pbtn_hvbgcolor', array(
        'default' => '',
        'type' =>'theme_mod',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'refresh',
    ));
    // Add color control 
    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize, 'beshop_pbtn_hvbgcolor', array(
                'label' => __('Products button hover background color','beshop'),
                'section' => 'beshop_shop'
            )
        )
    );
    $wp_customize->add_setting('beshop_pbtn_color', array(
        'default' => '',
        'type' =>'theme_mod',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'refresh',
    ));
    // Add color control 
    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize, 'beshop_pbtn_color', array(
                'label' => __('Products button color','beshop'),
                'section' => 'beshop_shop'
            )
        )
    );
    $wp_customize->add_setting('beshop_pbtn_hvcolor', array(
        'default' => '',
        'type' =>'theme_mod',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'refresh',
    ));
    // Add color control 
    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize, 'beshop_pbtn_hvcolor', array(
                'label' => __('Products button hover color','beshop'),
                'section' => 'beshop_shop'
            )
        )
    );
    /*Single product page options*/
    $wp_customize->add_section(
            'beshop_single_product',
            array(
                'title'    => __( 'Single Product Settings', 'beshop' ),
                'priority' => 10,
                'panel'    => 'woocommerce',
            )
    );
    $wp_customize->add_setting('beshop_ptitle_fsize', array(
        'default'        => '',
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
        'sanitize_callback' => 'beshop_sanitize_css_numeric',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('beshop_ptitle_fsize', array(
        'label'      => __('Font Size', 'beshop'),
        'description'     => __('Set single product title font size.', 'beshop'),
        'section'    => 'beshop_single_product',
        'settings'   => 'beshop_ptitle_fsize',
        'type'       => 'number',

    ));
    $wp_customize->add_setting('beshop_sptitle_color', array(
        'default' => '',
        'type' =>'theme_mod',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'refresh',
    ));
    // Add color control 
    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize, 'beshop_sptitle_color', array(
                'label' => __('Title Color','beshop'),
                'section' => 'beshop_single_product'
            )
        )
    );
    $wp_customize->add_setting( 'beshop_srating_show' , array(
    'capability'     => 'edit_theme_options',
    'type'           => 'theme_mod',
    'default'       =>  '1',
    'sanitize_callback' => 'absint',
    'transport'     => 'refresh',
    ) );
    $wp_customize->add_control( 'beshop_srating_show', array(
        'label'      => __('Show Rating ', 'beshop'),
        'description'=> __('You can show or hide single product rating. Rating only show when rating available.', 'beshop'),
        'section'    => 'beshop_single_product',
        'settings'   => 'beshop_srating_show',
        'type'       => 'checkbox',
    ) );
    $wp_customize->add_setting( 'beshop_sdesc_show' , array(
    'capability'     => 'edit_theme_options',
    'type'           => 'theme_mod',
    'default'       =>  '1',
    'sanitize_callback' => 'absint',
    'transport'     => 'refresh',
    ) );
    $wp_customize->add_control( 'beshop_sdesc_show', array(
        'label'      => __('Show Short description ', 'beshop'),
        'description'=> __('You can show or hide single product short description.', 'beshop'),
        'section'    => 'beshop_single_product',
        'settings'   => 'beshop_sdesc_show',
        'type'       => 'checkbox',
    ) );
    $wp_customize->add_setting( 'beshop_sku_show' , array(
    'capability'     => 'edit_theme_options',
    'type'           => 'theme_mod',
    'default'       =>  '1',
    'sanitize_callback' => 'absint',
    'transport'     => 'refresh',
    ) );
    $wp_customize->add_control( 'beshop_sku_show', array(
        'label'      => __('Show SKU', 'beshop'),
        'section'    => 'beshop_single_product',
        'settings'   => 'beshop_sku_show',
        'type'       => 'checkbox',
    ) );
    $wp_customize->add_setting( 'beshop_spcat_show' , array(
    'capability'     => 'edit_theme_options',
    'type'           => 'theme_mod',
    'default'       =>  '1',
    'sanitize_callback' => 'absint',
    'transport'     => 'refresh',
    ) );
    $wp_customize->add_control( 'beshop_spcat_show', array(
        'label'      => __('Show Categories ', 'beshop'),
        'section'    => 'beshop_single_product',
        'settings'   => 'beshop_spcat_show',
        'type'       => 'checkbox',
    ) );
    $wp_customize->add_setting( 'beshop_sptag_show' , array(
    'capability'     => 'edit_theme_options',
    'type'           => 'theme_mod',
    'default'       =>  '1',
    'sanitize_callback' => 'absint',
    'transport'     => 'refresh',
    ) );
    $wp_customize->add_control( 'beshop_sptag_show', array(
        'label'      => __('Show Tags', 'beshop'),
        'section'    => 'beshop_single_product',
        'settings'   => 'beshop_sptag_show',
        'type'       => 'checkbox',
    ) );
    $wp_customize->add_setting( 'beshop_sptab_show' , array(
    'capability'     => 'edit_theme_options',
    'type'           => 'theme_mod',
    'default'       =>  '1',
    'sanitize_callback' => 'absint',
    'transport'     => 'refresh',
    ) );
    $wp_customize->add_control( 'beshop_sptab_show', array(
        'label'      => __('Show Tab', 'beshop'),
        'section'    => 'beshop_single_product',
        'settings'   => 'beshop_sptab_show',
        'type'       => 'checkbox',
    ) );
    $wp_customize->add_setting( 'beshop_sprelated_show' , array(
    'capability'     => 'edit_theme_options',
    'type'           => 'theme_mod',
    'default'       =>  '1',
    'sanitize_callback' => 'absint',
    'transport'     => 'refresh',
    ) );
    $wp_customize->add_control( 'beshop_sprelated_show', array(
        'label'      => __('Show Related Products', 'beshop'),
        'section'    => 'beshop_single_product',
        'settings'   => 'beshop_sprelated_show',
        'type'       => 'checkbox',
    ) );
    /*Woocommerce checkout options*/
    $wp_customize->add_setting( 'beshop_checkout_lastname' , array(
    'capability'     => 'edit_theme_options',
    'type'           => 'theme_mod',
    'default'       =>  '1',
    'sanitize_callback' => 'absint',
    'transport'     => 'refresh',
    ) );
    $wp_customize->add_control( 'beshop_checkout_lastname', array(
        'label'      => __('Show Last Nama Field', 'beshop'),
        'section'    => 'woocommerce_checkout',
        'settings'   => 'beshop_checkout_lastname',
        'type'       => 'checkbox',
        'priority' => 5,
    ) );
    $wp_customize->add_setting('beshop_checkout_email', array(
        'default'        => 'required',
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
        'sanitize_callback' => 'beshop_sanitize_select',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('beshop_checkout_email', array(
        'label'      => __('Email field', 'beshop'),
        'section'    => 'woocommerce_checkout',
        'settings'   => 'beshop_checkout_email',
        'type'       => 'select',
        'choices'    => array(
            'required' => __('Required', 'beshop'),
            'optional' => __('Optional', 'beshop'),
            'hide' => __('Hidden', 'beshop'),
        ),
        'priority'       => 7,
    ));
    $wp_customize->add_setting( 'beshop_checkout_postcode' , array(
    'capability'     => 'edit_theme_options',
    'type'           => 'theme_mod',
    'default'       =>  '1',
    'sanitize_callback' => 'absint',
    'transport'     => 'refresh',
    ) );
    $wp_customize->add_control( 'beshop_checkout_postcode', array(
        'label'      => __('Show Postcode / ZIP', 'beshop'),
        'section'    => 'woocommerce_checkout',
        'settings'   => 'beshop_checkout_postcode',
        'type'       => 'checkbox',
        'priority' => 7,
    ) );
    
       


}
add_action( 'customize_register', 'beshopwoo_customize_register' );

