<?php

/**
 * BeShop Theme Customizer
 *
 * @package BeShop
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer. 
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function beshop_customize_register($wp_customize)
{
    $wp_customize->get_setting('blogname')->transport         = 'postMessage';
    $wp_customize->get_setting('blogdescription')->transport  = 'postMessage';
    $wp_customize->get_setting('header_textcolor')->transport = 'postMessage';

    //select sanitization function
    function beshop_sanitize_select($input, $setting)
    {
        $input = sanitize_key($input);
        $choices = $setting->manager->get_control($setting->id)->choices;
        return (array_key_exists($input, $choices) ? $input : $setting->default);
    }

    // Typography section
    $wp_customize->add_section('beshop_typography', array(
        'title' => __('Beshop Theme typography', 'beshop'),
        'capability'     => 'edit_theme_options',
        'description'     => __('You can setup BeShop theme typography by these options.', 'beshop'),
        'priority'       => 4,

    ));
    $wp_customize->add_setting('beshop_theme_fonts', array(
        'default'       => 'Poppins',
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
        'sanitize_callback' => 'beshop_sanitize_theme_font',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('beshop_theme_fonts_control', array(
        'label'      => __('Select theme body Font', 'beshop'),
        'section'    => 'beshop_typography',
        'settings'   => 'beshop_theme_fonts',
        'type'       => 'select',
        'choices'    => array(
            'Poppins' => __('Poppins', 'beshop'),
            'Noto Serif' => __('Noto Serif', 'beshop'),
            'Roboto' => __('Roboto', 'beshop'),
            'Open Sans' => __('Open Sans', 'beshop'),
            'Lato' => __('Lato', 'beshop'),
            'Montserrat' => __('Montserrat', 'beshop'),
            'Crimson Text' => __('Crimson Text', 'beshop'),
        ),
    ));
    $wp_customize->add_setting('beshop_font_size', array(
        'default' =>  '14',
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
        'sanitize_callback' => 'beshop_sanitize_css_numeric',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('beshop_font_size_control', array(
        'label'      => __('Body font size', 'beshop'),
        'description'     => __('Default body font size is 14px', 'beshop'),
        'section'    => 'beshop_typography',
        'settings'   => 'beshop_font_size',
        'type'       => 'text',

    ));
    $wp_customize->add_setting('beshop_font_line_height', array(
        'default' =>  '24',
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
        'sanitize_callback' => 'beshop_sanitize_css_numeric',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('beshop_font_line_height_control', array(
        'label'      => __('Body font line height', 'beshop'),
        'description'     => __('Default body line height is 24px', 'beshop'),
        'section'    => 'beshop_typography',
        'settings'   => 'beshop_font_line_height',
        'type'       => 'text',

    ));
    $wp_customize->add_setting('beshop_theme_font_head', array(
        'default'       => 'Noto Serif',
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
        'sanitize_callback' => 'beshop_sanitize_theme_head_font',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('beshop_theme_font_head_control', array(
        'label'      => __('Select theme header Font', 'beshop'),
        'section'    => 'beshop_typography',
        'settings'   => 'beshop_theme_font_head',
        'type'       => 'select',
        'choices'    => array(
            'Poppins' => __('Poppins', 'beshop'),
            'Noto Serif' => __('Noto Serif', 'beshop'),
            'Roboto' => __('Roboto', 'beshop'),
            'Open Sans' => __('Open Sans', 'beshop'),
            'Lato' => __('Lato', 'beshop'),
            'Montserrat' => __('Montserrat', 'beshop'),
            'Crimson Text' => __('Crimson Text', 'beshop'),
        ),
    ));
    $wp_customize->add_setting('beshop_font_weight_head', array(
        'default'       => '700',
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
        'sanitize_callback' => 'beshop_sanitize_select',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('beshop_font_weight_head_control', array(
        'label'      => __('Site header font weight', 'beshop'),
        'section'    => 'beshop_typography',
        'settings'   => 'beshop_font_weight_head',
        'type'       => 'select',
        'choices'    => array(
            '400' => __('Normal', 'beshop'),
            '500' => __('Semi Bold', 'beshop'),
            '700' => __('Bold', 'beshop'),
            '900' => __('Extra Bold', 'beshop'),
        ),
    ));
    /*End typography section*/

    // Add beshop top header section
    $wp_customize->add_section('beshop_topbar', array(
        'title' => __('Beshop Top bar', 'beshop'),
        'capability'     => 'edit_theme_options',
        'description'     => __('The beshop topbar options ', 'beshop'),
        'priority'       => 5,

    ));
    $wp_customize->add_setting('beshop_topbar_show', array(
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
        'default'       =>  '1',
        'sanitize_callback' => 'absint',
        'transport'     => 'refresh',
    ));
    $wp_customize->add_control('beshop_topbar_show', array(
        'label'      => __('Show header topbar? ', 'beshop'),
        'description' => __('You can show or hide header topbar.', 'beshop'),
        'section'    => 'beshop_topbar',
        'settings'   => 'beshop_topbar_show',
        'type'       => 'checkbox',

    ));
    $wp_customize->add_setting('beshop_topbar_mtext', array(
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
        'default'       =>  esc_html__('Welcome to Our Website', 'beshop'),
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('beshop_topbar_mtext', array(
        'label'      => __('Welcome text', 'beshop'),
        'description'     => esc_html__('Enter your website welcome text. Leave empty if you don\'t want the text.', 'beshop'),
        'section'    => 'beshop_topbar',
        'settings'   => 'beshop_topbar_mtext',
        'type'       => 'text',
    ));
    $wp_customize->add_setting('beshop_topbar_menushow', array(
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
        'default'       =>  '1',
        'sanitize_callback' => 'absint',
        'transport'     => 'refresh',
    ));
    $wp_customize->add_control('beshop_topbar_menushow', array(
        'label'      => __('Show header topbar menu? ', 'beshop'),
        'description' => __('You can show or hide topbar menu. You need to add menu from menu section for display menu.', 'beshop'),
        'section'    => 'beshop_topbar',
        'settings'   => 'beshop_topbar_menushow',
        'type'       => 'checkbox',

    ));
    /*
    $wp_customize->add_setting( 'beshop_topbar_search' , array(
    'capability'     => 'edit_theme_options',
    'type'           => 'theme_mod',
    'default'       =>  '1',
    'sanitize_callback' => 'absint',
    'transport'     => 'refresh',
    ) );
    $wp_customize->add_control( 'beshop_topbar_search', array(
        'label'      => __('Show header topbar search? ', 'beshop'),
        'description'=> __('You can show or hide topbar search.', 'beshop'),
        'section'    => 'beshop_topbar',
        'settings'   => 'beshop_topbar_search',
        'type'       => 'checkbox',
    ) );*/

    $wp_customize->add_setting('beshop_topbar_search_item', array(
        'default'        => 'popup',
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
        'sanitize_callback' => 'beshop_sanitize_select',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('beshop_topbar_search_item', array(
        'label'      => __('Select header topbar search type', 'beshop'),
        'description' => __('You can show two different way or hide topbar search. ', 'beshop'),
        'section'    => 'beshop_topbar',
        'settings'   => 'beshop_topbar_search_item',
        'type'       => 'select',
        'choices'    => array(
            'simple' => __('Simple Search', 'beshop'),
            'popup' => __('Popup Search', 'beshop'),
            'hide' => __('Hide Search', 'beshop'),
        ),
    ));
    // Add setting
    $wp_customize->add_setting('beshop_topbar_bg', array(
        'default' => '#343a40',
        'type' => 'theme_mod',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'refresh',
    ));
    // Add color control 
    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize,
            'beshop_topbar_bg',
            array(
                'label' => __('Topbar Background Color', 'beshop'),
                'section' => 'beshop_topbar'
            )
        )
    );
    // Add setting
    $wp_customize->add_setting('beshop_topbar_color', array(
        'default' => '#fff',
        'type' => 'theme_mod',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'refresh',
    ));
    // Add color control 
    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize,
            'beshop_topbar_color',
            array(
                'label' => __('Topbar text Color', 'beshop'),
                'section' => 'beshop_topbar'
            )
        )
    );
    // Add setting
    $wp_customize->add_setting('beshop_topbar_hcolor', array(
        'default' => '#dedede',
        'type' => 'theme_mod',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'refresh',
    ));
    // Add color control 
    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize,
            'beshop_topbar_hcolor',
            array(
                'label' => __('Topbar link hover Color', 'beshop'),
                'section' => 'beshop_topbar'
            )
        )
    ); // topbar section end
    /*header image height*/

    /*
      * Logo position 
       */
    $wp_customize->add_setting('beshop_himg_height', array(
        'default'        => 'fixed',
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
        'sanitize_callback' => 'beshop_sanitize_select',
        'transport' => 'refresh',
        //  'priority'       => 20,
    ));
    $wp_customize->add_control('beshop_himg_height', array(
        'label'      => __('Header image height', 'beshop'),
        'section'    => 'header_image',
        'settings'   => 'beshop_himg_height',
        'type'       => 'select',
        'choices'    => array(
            'fixed' => __('Fixed Height', 'beshop'),
            'auto' => __('Auto Height', 'beshop'),
            'amobile' => __('Auto height only small devices', 'beshop'),
        ),
    ));

    //logo width
    $wp_customize->add_setting('beshop_logo_width', array(
        'default'        => '',
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
        'sanitize_callback' => 'beshop_sanitize_css_numeric',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('beshop_logo_width_control', array(
        'label'      => __('Set Image Logo Width', 'beshop'),
        'description'     => __('Set your site logo Width.', 'beshop'),
        'section'    => 'title_tagline',
        'settings'   => 'beshop_logo_width',
        'type'       => 'number',
        'priority'       => 6,

    ));
    $wp_customize->add_setting('beshop_logo_height', array(
        'default'        => '',
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
        'sanitize_callback' => 'beshop_sanitize_css_numeric',
        'transport' => 'refresh',

    ));
    $wp_customize->add_control('beshop_logo_height_control', array(
        'label'      => __('Set Image Logo height', 'beshop'),
        'description'     => __('Set your site logo height. Leave empty for auto height.', 'beshop'),
        'section'    => 'title_tagline',
        'settings'   => 'beshop_logo_height',
        'type'       => 'number',
        'priority'       => 7,
    ));
    $wp_customize->add_setting('beshop_tagline_bgshow', array(
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
        'default'       =>  '1',
        'sanitize_callback' => 'absint',
        'transport'     => 'refresh',
    ));
    $wp_customize->add_control('beshop_tagline_bgshow', array(
        'label'      => __('Show tagline background? ', 'beshop'),
        'description' => __('You can show or hide header tagline background.', 'beshop'),
        'section'    => 'title_tagline',
        'settings'   => 'beshop_tagline_bgshow',
        'type'       => 'checkbox',

    ));
    $wp_customize->add_setting('beshop_logo_fontsize', array(
        'default'        => '',
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
        'sanitize_callback' => 'beshop_sanitize_css_numeric',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('beshop_logo_fontsize', array(
        'label'      => __('Site Title Font Size', 'beshop'),
        'description'     => __('Set your site title font size.', 'beshop'),
        'section'    => 'title_tagline',
        'settings'   => 'beshop_logo_fontsize',
        'type'       => 'number',

    ));
    $wp_customize->add_setting('beshop_desc_fontsize', array(
        'default'        => '',
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
        'sanitize_callback' => 'beshop_sanitize_css_numeric',
        'transport' => 'refresh',

    ));
    $wp_customize->add_control('beshop_desc_fontsize', array(
        'label'      => __('Set Site Tagline Font Size', 'beshop'),
        'description'     => __('Set your site tabline font size.', 'beshop'),
        'section'    => 'title_tagline',
        'settings'   => 'beshop_desc_fontsize',
        'type'       => 'number',
    ));

    /*
      * Logo position 
       */
    $wp_customize->add_setting('beshop_logo_position', array(
        'default'        => 'center',
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
        'sanitize_callback' => 'beshop_sanitize_select',
        'transport' => 'refresh',
        //  'priority'       => 20,
    ));
    $wp_customize->add_control('beshop_logo_position', array(
        'label'      => __('Select Logo Position', 'beshop'),
        'section'    => 'title_tagline',
        'settings'   => 'beshop_logo_position',
        'type'       => 'select',
        'choices'    => array(
            'left' => __('Logo left', 'beshop'),
            'center' => __('Logo center', 'beshop'),
            'right' => __('Logo right', 'beshop'),
        ),
    ));

    // Header middle section
    $wp_customize->add_section('beshop_middle', array(
        'title' => __('Beshop Header Middle', 'beshop'),
        'capability'     => 'edit_theme_options',
        'description'     => __('The beshop header middle section ', 'beshop'),
        'priority'       => 6,

    ));
    $wp_customize->add_setting('beshop_hmiddle_show', array(
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
        'default'       =>  '1',
        'sanitize_callback' => 'absint',
        'transport'     => 'refresh',
    ));
    $wp_customize->add_control('beshop_hmiddle_show', array(
        'label'      => __('Show header Middle? ', 'beshop'),
        'description' => __('You can show or hide header middle section. And can show logo in menu bar', 'beshop'),
        'section'    => 'beshop_middle',
        'settings'   => 'beshop_hmiddle_show',
        'type'       => 'checkbox',

    ));
    $wp_customize->add_setting('beshop_hmiddle_height', array(
        'default'        => '',
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
        'sanitize_callback' => 'beshop_sanitize_css_numeric',
        'transport' => 'refresh',

    ));
    $wp_customize->add_control('beshop_hmiddle_height', array(
        'label'      => __('Set header middle section height', 'beshop'),
        'description'     => __('Set your header middle section height. Leave empty for auto height.', 'beshop'),
        'section'    => 'beshop_middle',
        'settings'   => 'beshop_hmiddle_height',
        'type'       => 'number',
    ));


    // Header middle section
    $wp_customize->add_section('beshop_main_menu', array(
        'title' => __('Beshop Main Menu', 'beshop'),
        'capability'     => 'edit_theme_options',
        'description'     => __('The beshop main menu section. You need to add menu from WordPress menu setup for display the menu manu ', 'beshop'),
        'priority'       => 6,

    ));
    $wp_customize->add_setting('beshop_main_menu_show', array(
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
        'default'       =>  '1',
        'sanitize_callback' => 'absint',
        'transport'     => 'refresh',
    ));
    $wp_customize->add_control('beshop_main_menu_show', array(
        'label'      => __('Show Main Menu section? ', 'beshop'),
        'description' => __('You can show or hide header main menu section.', 'beshop'),
        'section'    => 'beshop_main_menu',
        'settings'   => 'beshop_main_menu_show',
        'type'       => 'checkbox',

    ));
    $wp_customize->add_setting('beshop_menu_logo', array(
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
        'default'       =>  '',
        'sanitize_callback' => 'absint',
        'transport'     => 'refresh',
    ));
    $wp_customize->add_control('beshop_menu_logo', array(
        'label'      => __('Show Logo in the Main Menu section? ', 'beshop'),
        'description' => __('You can show logo in the header main menu section.', 'beshop'),
        'section'    => 'beshop_main_menu',
        'settings'   => 'beshop_menu_logo',
        'type'       => 'checkbox',

    ));
    /*
      * menu position 
       */
    $wp_customize->add_setting('beshop_menu_position', array(
        'default'        => 'center',
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
        'sanitize_callback' => 'beshop_sanitize_select',
        'transport' => 'refresh',
        //  'priority'       => 20,
    ));
    $wp_customize->add_control('beshop_menu_position', array(
        'label'      => __('Select Menu Position', 'beshop'),
        'section'    => 'beshop_main_menu',
        'settings'   => 'beshop_menu_position',
        'type'       => 'select',
        'choices'    => array(
            'flex-start' => __('Menu left', 'beshop'),
            'center' => __('Menu center', 'beshop'),
            'flex-end' => __('Menu right', 'beshop'),
        ),
    ));
    $wp_customize->add_setting('beshop_menu_tbpadding', array(
        'default'        => '',
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
        'sanitize_callback' => 'beshop_sanitize_css_numeric',
        'transport' => 'refresh',

    ));
    $wp_customize->add_control('beshop_menu_tbpadding', array(
        'label'      => __('Menu top bottom padding', 'beshop'),
        'description'     => __('Add main menu top bottom padding.', 'beshop'),
        'section'    => 'beshop_main_menu',
        'settings'   => 'beshop_menu_tbpadding',
        'type'       => 'number',
    ));
    $wp_customize->add_setting('beshop_menu_fontsize', array(
        'default'        => '',
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
        'sanitize_callback' => 'beshop_sanitize_css_numeric',
        'transport' => 'refresh',

    ));
    $wp_customize->add_control('beshop_menu_fontsize', array(
        'label'      => __('Menu item font size', 'beshop'),
        'description'     => __('Set menu item font size. Font size set by px', 'beshop'),
        'section'    => 'beshop_main_menu',
        'settings'   => 'beshop_menu_fontsize',
        'type'       => 'number',
    ));
    // Add setting
    $wp_customize->add_setting('beshop_menu_color', array(
        'default' => '',
        'type' => 'theme_mod',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'refresh',
    ));
    // Add color control 
    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize,
            'beshop_menu_color',
            array(
                'label' => __('Menu font Color', 'beshop'),
                'section' => 'beshop_main_menu'
            )
        )
    );
    // Add setting
    $wp_customize->add_setting('beshop_menubg_color', array(
        'default' => '',
        'type' => 'theme_mod',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'refresh',
    ));
    // Add color control 
    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize,
            'beshop_menubg_color',
            array(
                'label' => __('Menu Background Color', 'beshop'),
                'section' => 'beshop_main_menu'
            )
        )
    );
    // Add setting
    $wp_customize->add_setting('beshop_menudropbg_color', array(
        'default' => '',
        'type' => 'theme_mod',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'refresh',
    ));
    // Add color control 
    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize,
            'beshop_menudropbg_color',
            array(
                'label' => __('Menu dropdown Background Color', 'beshop'),
                'section' => 'beshop_main_menu'
            )
        )
    );

    //color section custom options    

    // Add setting
    $wp_customize->add_setting('beshop_titletag_bgcolor', array(
        'default' => '',
        'type' => 'theme_mod',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'refresh',
    ));
    // Add color control 
    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize,
            'beshop_titletag_bgcolor',
            array(
                'label' => __('Title tag background Color', 'beshop'),
                'section' => 'colors'
            )
        )
    );
    $wp_customize->add_setting('beshop_header_color', array(
        'default' => '',
        'type' => 'theme_mod',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'refresh',
        'priority'       => 2,

    ));
    // Add color control 
    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize,
            'beshop_header_color',
            array(
                'label' => __('Header tag Font Color', 'beshop'),
                'section' => 'colors'
            )
        )
    );
    $wp_customize->add_setting('beshop_bodyfont_color', array(
        'default' => '',
        'type' => 'theme_mod',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'refresh',
    ));
    // Add color control 
    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize,
            'beshop_bodyfont_color',
            array(
                'label' => __('Body Font Color', 'beshop'),
                'section' => 'colors'
            )
        )
    );
    $wp_customize->add_setting('beshop_contentbg_color', array(
        'default' => '',
        'type' => 'theme_mod',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'refresh',
    ));
    // Add color control 
    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize,
            'beshop_contentbg_color',
            array(
                'label' => __('Content Background Color', 'beshop'),
                'section' => 'colors'
            )
        )
    );
    $wp_customize->add_setting('beshop_basic_color', array(
        'default' => '',
        'type' => 'theme_mod',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'refresh',
    ));
    // Add color control 
    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize,
            'beshop_basic_color',
            array(
                'label' => __('Theme Basic Color', 'beshop'),
                'section' => 'colors'
            )
        )
    );
    //link color
    $wp_customize->add_setting('beshop_link_color', array(
        'default' => '',
        'type' => 'theme_mod',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'refresh',
    ));
    // Add color control 
    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize,
            'beshop_link_color',
            array(
                'label' => __('Theme Link Color', 'beshop'),
                'section' => 'colors'
            )
        )
    );
    //link hover color
    $wp_customize->add_setting('beshop_linkhvr_color', array(
        'default' => '',
        'type' => 'theme_mod',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'refresh',
    ));
    // Add color control 
    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize,
            'beshop_linkhvr_color',
            array(
                'label' => __('Theme Link Hover Color', 'beshop'),
                'section' => 'colors'
            )
        )
    );
    // Add beshop blog section
    $wp_customize->add_section('beshop_blog', array(
        'title' => __('Beshop Blog', 'beshop'),
        'capability'     => 'edit_theme_options',
        'description'     => __('The beshop theme blog options ', 'beshop'),
        'priority'       => 60,

    ));
    $wp_customize->add_setting('beshop_blog_container', array(
        'default'        => 'container',
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
        'sanitize_callback' => 'beshop_sanitize_select',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('beshop_blog_container', array(
        'label'      => __('Container type', 'beshop'),
        'description' => __('You can set standard container or full width container. ', 'beshop'),
        'section'    => 'beshop_blog',
        'settings'   => 'beshop_blog_container',
        'type'       => 'select',
        'choices'    => array(
            'container' => __('Standard Container', 'beshop'),
            'container-fluid' => __('Full width Container', 'beshop'),
        ),
    ));
    $wp_customize->add_setting('beshop_blog_layout', array(
        'default'        => 'rightside',
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
        'sanitize_callback' => 'beshop_sanitize_select',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('beshop_blog_layout', array(
        'label'      => __('Select Blog Layout', 'beshop'),
        'description' => __('Right and Left sidebar only show when sidebar widget is available. ', 'beshop'),
        'section'    => 'beshop_blog',
        'settings'   => 'beshop_blog_layout',
        'type'       => 'select',
        'choices'    => array(
            'rightside' => __('Right Sidebar', 'beshop'),
            'leftside' => __('Left Sidebar', 'beshop'),
            'fullwidth' => __('Full Width', 'beshop'),
        ),
    ));

    $wp_customize->add_setting('beshop_blog_style', array(
        'default'        => 'style2',
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
        'sanitize_callback' => 'beshop_sanitize_select',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('beshop_blog_style', array(
        'label'      => __('Select Blog Style', 'beshop'),
        'section'    => 'beshop_blog',
        'settings'   => 'beshop_blog_style',
        'type'       => 'select',
        'choices'    => array(
            'style1' => __('List over Image', 'beshop'),
            'style2' => __('List Style', 'beshop'),
            'style3' => __('Classic Style', 'beshop'),
        ),
    ));
    $wp_customize->add_setting('beshop_blog_readmore', array(
        'default' =>  esc_html__('Read More', 'beshop'),
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('beshop_blog_readmore', array(
        'label'      => __('Posts Read More Text', 'beshop'),
        'description'     => __('You can change read more text by this settings', 'beshop'),
        'section'    => 'beshop_blog',
        'settings'   => 'beshop_blog_readmore',
        'type'       => 'text',

    ));

    $wp_customize->add_setting('beshop_blogdate', array(
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
        'default'       =>  '1',
        'sanitize_callback' => 'absint',
        'transport'     => 'refresh',
    ));
    $wp_customize->add_control('beshop_blogdate', array(
        'label'      => __('Show Posts Date? ', 'beshop'),
        'section'    => 'beshop_blog',
        'settings'   => 'beshop_blogdate',
        'type'       => 'checkbox',
    ));
    $wp_customize->add_setting('beshop_blogauthor', array(
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
        'default'       =>  '1',
        'sanitize_callback' => 'absint',
        'transport'     => 'refresh',
    ));
    $wp_customize->add_control('beshop_blogauthor', array(
        'label'      => __('Show Posts Author? ', 'beshop'),
        'section'    => 'beshop_blog',
        'settings'   => 'beshop_blogauthor',
        'type'       => 'checkbox',
    ));
    $wp_customize->add_setting('beshop_postcat', array(
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
        'default'       =>  '1',
        'sanitize_callback' => 'absint',
        'transport'     => 'refresh',
    ));
    $wp_customize->add_control('beshop_postcat', array(
        'label'      => __('Show Single Posts Categories? ', 'beshop'),
        'section'    => 'beshop_blog',
        'settings'   => 'beshop_postcat',
        'type'       => 'checkbox',
    ));
    $wp_customize->add_setting('beshop_posttag', array(
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
        'default'       =>  '1',
        'sanitize_callback' => 'absint',
        'transport'     => 'refresh',
    ));
    $wp_customize->add_control('beshop_posttag', array(
        'label'      => __('Show Single Posts tags? ', 'beshop'),
        'section'    => 'beshop_blog',
        'settings'   => 'beshop_posttag',
        'type'       => 'checkbox',
    ));
    $wp_customize->add_setting('beshop_post_comment', array(
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
        'default'       =>  '1',
        'sanitize_callback' => 'absint',
        'transport'     => 'refresh',
    ));
    $wp_customize->add_control('beshop_post_comment', array(
        'label'      => __('Show Single Posts comment? ', 'beshop'),
        'section'    => 'beshop_blog',
        'settings'   => 'beshop_post_comment',
        'type'       => 'checkbox',
    ));

    // Add beshop page section
    $wp_customize->add_section('beshop_page', array(
        'title' => __('Beshop Page', 'beshop'),
        'capability'     => 'edit_theme_options',
        'description'     => __('The beshop theme Page options ', 'beshop'),
        'priority'       => 70,

    ));
    $wp_customize->add_setting('beshop_page_container', array(
        'default'        => 'container',
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
        'sanitize_callback' => 'beshop_sanitize_select',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('beshop_page_container', array(
        'label'      => __('Page Container type', 'beshop'),
        'description' => __('You can set standard container or full width container for page. ', 'beshop'),
        'section'    => 'beshop_page',
        'settings'   => 'beshop_page_container',
        'type'       => 'select',
        'choices'    => array(
            'container' => __('Standard Page Container', 'beshop'),
            'container-fluid' => __('Full width Page Container', 'beshop'),
        ),
    ));
    $wp_customize->add_setting('beshop_page_layout', array(
        'default'        => 'rightside',
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
        'sanitize_callback' => 'beshop_sanitize_select',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('beshop_page_layout', array(
        'label'      => __('Select Page Layout', 'beshop'),
        'description' => __('Right and Left sidebar only show when sidebar widget is available. ', 'beshop'),
        'section'    => 'beshop_page',
        'settings'   => 'beshop_page_layout',
        'type'       => 'select',
        'choices'    => array(
            'rightside' => __('Right Sidebar', 'beshop'),
            'leftside' => __('Left Sidebar', 'beshop'),
            'fullwidth' => __('Full Width', 'beshop'),
        ),
    ));




    /*
* Footer setting section
*
*/
    // Add beshop top header section
    $wp_customize->add_panel('beshop_footer_panel', array(
        //  'priority'       => 75,
        'title'          => __('Beshop footer settings', 'beshop'),
        'description'    => __('All Beshop theme footer settings in the panel', 'beshop'),
    ));
    $wp_customize->add_section('beshop_footer_top', array(
        'title' => __('Beshop Footer Top Settings', 'beshop'),
        'capability'     => 'edit_theme_options',
        'description'     => __('The beshop footer settings options ', 'beshop'),
        'panel'    => 'beshop_footer_panel',

    ));
    $wp_customize->add_setting('beshop_topfooter_show', array(
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
        'default'       =>  '1',
        'sanitize_callback' => 'absint',
        'transport'     => 'refresh',
    ));
    $wp_customize->add_control('beshop_topfooter_show', array(
        'label'      => __('Show Top Footer? ', 'beshop'),
        'description' => __('You can show or hide footer top section.The section only visible when you will set footer widget. ', 'beshop'),
        'section'    => 'beshop_footer_top',
        'settings'   => 'beshop_topfooter_show',
        'type'       => 'checkbox',

    ));
    //link hover color
    $wp_customize->add_setting('beshop_topfooter_bgcolor', array(
        'default' => '',
        'type' => 'theme_mod',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'refresh',
    ));
    // Add color control 
    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize,
            'beshop_topfooter_bgcolor',
            array(
                'label' => __('Footer top background color.', 'beshop'),
                'section' => 'beshop_footer_top'
            )
        )
    );
    //link hover color
    $wp_customize->add_setting('beshop_tftitle_color', array(
        'default' => '',
        'type' => 'theme_mod',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'refresh',
    ));
    // Add color control 
    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize,
            'beshop_tftitle_color',
            array(
                'label' => __('Footer Top Widget Title Color.', 'beshop'),
                'section' => 'beshop_footer_top'
            )
        )
    );
    //link hover color
    $wp_customize->add_setting('beshop_tftext_color', array(
        'default' => '',
        'type' => 'theme_mod',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'refresh',
    ));
    // Add color control 
    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize,
            'beshop_tftext_color',
            array(
                'label' => __('Footer Top Widget Text Color.', 'beshop'),
                'section' => 'beshop_footer_top'
            )
        )
    );
    //link hover color
    $wp_customize->add_setting('beshop_tflink_hovercolor', array(
        'default' => '',
        'type' => 'theme_mod',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'refresh',
    ));
    // Add color control 
    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize,
            'beshop_tflink_hovercolor',
            array(
                'label' => __('Footer Top Widget Link hover Color.', 'beshop'),
                'section' => 'beshop_footer_top'
            )
        )
    );
    // Footer section
    $wp_customize->add_section('beshop_footer', array(
        'title' => __('Beshop Footer Settings', 'beshop'),
        'capability'     => 'edit_theme_options',
        'description'     => __('The beshop footer settings options ', 'beshop'),
        'panel'    => 'beshop_footer_panel',

    ));

    $wp_customize->add_setting('beshop_footer_bgcolor', array(
        'default' => '',
        'type' => 'theme_mod',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'refresh',
    ));
    // Add color control 
    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize,
            'beshop_footer_bgcolor',
            array(
                'label' => __('Footer background color.', 'beshop'),
                'section' => 'beshop_footer'
            )
        )
    );
    $wp_customize->add_setting('beshop_footer_color', array(
        'default' => '',
        'type' => 'theme_mod',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'refresh',
    ));
    // Add color control 
    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize,
            'beshop_footer_color',
            array(
                'label' => __('Footer text color.', 'beshop'),
                'section' => 'beshop_footer'
            )
        )
    );
    $wp_customize->add_setting('beshop_footerlink_hcolor', array(
        'default' => '',
        'type' => 'theme_mod',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'refresh',
    ));
    // Add color control 
    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize,
            'beshop_footerlink_hcolor',
            array(
                'label' => __('Footer Link Hover color.', 'beshop'),
                'section' => 'beshop_footer'
            )
        )
    );




    if (isset($wp_customize->selective_refresh)) {
        $wp_customize->selective_refresh->add_partial(
            'blogname',
            array(
                'selector'        => '.site-title a',
                'render_callback' => 'beshop_customize_partial_blogname',
            )
        );
        $wp_customize->selective_refresh->add_partial(
            'blogdescription',
            array(
                'selector'        => '.site-description',
                'render_callback' => 'beshop_customize_partial_blogdescription',
            )
        );
    }
}
add_action('customize_register', 'beshop_customize_register');

/**
 * Render the site title for the selective refresh partial.
 *
 * @return void
 */
function beshop_customize_partial_blogname()
{
    bloginfo('name');
}

/**
 * Render the site tagline for the selective refresh partial.
 *
 * @return void
 */
function beshop_customize_partial_blogdescription()
{
    bloginfo('description');
}

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function beshop_customize_preview_js()
{
    wp_enqueue_script('beshop-customizer', get_template_directory_uri() . '/assets/js/customizer.js', array('customize-preview'), '20151215', true);
}
add_action('customize_preview_init', 'beshop_customize_preview_js');
