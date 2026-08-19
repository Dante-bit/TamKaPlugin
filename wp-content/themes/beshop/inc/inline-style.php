<?php

/**
 * Add inline css 
 *
 * 
 */
if (!function_exists('beshop_inline_css')) :
  function beshop_inline_css()
  {

    $style = '';

    // font settings
    $beshop_theme_fonts = get_theme_mod('beshop_theme_fonts', 'Poppins');
    $beshop_font_size = get_theme_mod('beshop_font_size', '14');
    $beshop_font_line_height = get_theme_mod('beshop_font_line_height', '24');
    $beshop_theme_font_head = get_theme_mod('beshop_theme_font_head', 'Noto Serif');
    $beshop_font_weight_head = get_theme_mod('beshop_font_weight_head', '700');

    if ($beshop_theme_fonts != 'Poppins') {
      $style .= 'body, p{font-family:\'' . esc_attr(beshop_sanitize_css_text($beshop_theme_fonts)) . '\', sans-serif;}';
    }
    if ($beshop_font_size != '14') {
      $style .= 'body, p{font-size:' . esc_attr(beshop_sanitize_css_numeric($beshop_font_size)) . 'px;}';
    }
    if ($beshop_font_line_height != '24') {
      $style .= 'body, p{line-height:' . esc_attr(beshop_sanitize_css_numeric($beshop_font_line_height)) . 'px;}';
    }

    if ($beshop_theme_font_head != 'Noto Serif') {
      $style .= 'h1,h1 a, h2,h2 a, h3,h3 a, h4,h4 a, h5, h6{font-family:\'' . esc_attr(beshop_sanitize_css_text($beshop_theme_font_head)) . '\', sans-serif;}';
    }
    if ($beshop_font_weight_head != '700') {
      $style .= 'h1, h2, h3, h4, h5, h6{font-weight:' . esc_attr(beshop_sanitize_css_numeric($beshop_font_weight_head)) . ';}';
    }




    $style .= 'h1.site-title a,p.site-description{color:#' . get_header_textcolor() . ';}';

    $beshop_titletag_bgcolor = get_theme_mod('beshop_titletag_bgcolor');
    if ($beshop_titletag_bgcolor) {
      $style .= 'p.site-description:before{background:' . esc_attr(beshop_sanitize_css_color($beshop_titletag_bgcolor)) . ' !important;}';
    }

    $beshop_tagline_bgshow = get_theme_mod('beshop_tagline_bgshow');
    if (empty($beshop_tagline_bgshow)) {
      $style .= 'p.site-description:before{display:none !important;}';
    }

    //top bar settings  
    $beshop_topbar_bg = get_theme_mod('beshop_topbar_bg', '#343a40');
    $beshop_topbar_color = get_theme_mod('beshop_topbar_color', '#fff');
    $beshop_topbar_hcolor = get_theme_mod('beshop_topbar_hcolor', '#dedede');

    if ($beshop_topbar_bg != '#343a40') {
      $style .= '.beshop-tophead.bg-dark,#bessearch .search-submit{background-color:' . esc_attr(beshop_sanitize_css_color($beshop_topbar_bg)) . '!important;}';
    }
    if ($beshop_topbar_color != '#fff') {
      $style .= '.beshop-tophead, .beshop-tophead a, .beshop-tophead span, .beshop-tophead input,.besearch-icon i, .besearch-icon a,#bessearch .search-submit{color:' . esc_attr(beshop_sanitize_css_color($beshop_topbar_color)) . ';}';
    }
    if ($beshop_topbar_hcolor != '#dedede') {
      $style .= '.beshop-tophead a:hover,.besearch-icon a:hover i{color:' . esc_attr(beshop_sanitize_css_color($beshop_topbar_hcolor)) . ';}';
    }

    // image logo width height
    $beshop_logo_width = get_theme_mod('beshop_logo_width');
    $beshop_logo_height = get_theme_mod('beshop_logo_height');
    if ($beshop_logo_width) {
      $style .= '.site-branding.has-himg a img{width:' . esc_attr(beshop_sanitize_css_numeric($beshop_logo_width)) . 'px;}';
    }
    if ($beshop_logo_height) {
      $style .= '.site-branding.has-himg a img{height:' . esc_attr(beshop_sanitize_css_numeric($beshop_logo_height)) . 'px;}';
    }

    // title and tagline font size
    $beshop_hmiddle_height = get_theme_mod('beshop_hmiddle_height');
    $beshop_logo_fontsize = get_theme_mod('beshop_logo_fontsize');
    $beshop_desc_fontsize = get_theme_mod('beshop_desc_fontsize');
    if ($beshop_hmiddle_height) {
      $style .= '.site-branding, .beshop-header-img img{height:' . esc_attr(beshop_sanitize_css_numeric($beshop_hmiddle_height)) . 'px !important;}';
    }
    if ($beshop_logo_fontsize) {
      $style .= 'h1.site-title a{font-size:' . esc_attr(beshop_sanitize_css_numeric($beshop_logo_fontsize)) . 'px;}';
    }
    if ($beshop_desc_fontsize) {
      $style .= 'p.site-description{font-size:' . esc_attr(beshop_sanitize_css_numeric($beshop_desc_fontsize)) . 'px;}';
    }
    $beshop_menu_position = get_theme_mod('beshop_menu_position', 'center');
    if ($beshop_menu_position) {
      $style .= '.main-navigation ul{justify-content:' . esc_attr(beshop_sanitize_css_text($beshop_menu_position)) . ';}';
    }
    $beshop_menu_tbpadding = get_theme_mod('beshop_menu_tbpadding');
    if ($beshop_menu_tbpadding) {
      $style .= '.beshop-main-nav{padding:' . esc_attr(beshop_sanitize_css_numeric($beshop_menu_tbpadding)) . 'px 0;}';
    }
    $beshop_menu_fontsize = get_theme_mod('beshop_menu_fontsize');
    if ($beshop_menu_fontsize) {
      $style .= '.beshop-main-nav ul li a{font-size:' . esc_attr(beshop_sanitize_css_numeric($beshop_menu_fontsize)) . 'px;}';
    }
    $beshop_menubg_color = get_theme_mod('beshop_menubg_color');
    if ($beshop_menubg_color) {
      $style .= '.beshop-main-nav.bg-dark,.menu-main-menu-container{background:' . esc_attr(beshop_sanitize_css_color($beshop_menubg_color)) . '  !important;}';
      $style .= '.beshop-main-nav ul li a{border-color:' . esc_attr(beshop_sanitize_css_color($beshop_menubg_color)) . '  !important;}';
      $style .= '.mobile-menu-bar,.mobile-panel-header{background:' . esc_attr(beshop_sanitize_css_color($beshop_menubg_color)) . '  !important;}';
    }
    $beshop_menudropbg_color = get_theme_mod('beshop_menudropbg_color');
    if ($beshop_menudropbg_color) {
      $style .= '.main-navigation ul ul{background:' . esc_attr(beshop_sanitize_css_color($beshop_menudropbg_color)) . '  !important;}';
    }
    $beshop_menu_color = get_theme_mod('beshop_menu_color');
    if ($beshop_menu_color) {
      $style .= '.beshop-main-nav ul li a,.mini-toggle, .main-navigation .page_item_has_children:before, .main-navigation .menu-item-has-children:before{color:' . esc_attr(beshop_sanitize_css_color($beshop_menu_color)) . '  !important;}';
    }

    //color section style
    $beshop_header_color = get_theme_mod('beshop_header_color');
    if ($beshop_header_color) {
      $style .= 'h1,h2,h3,h4,h5,h6, h2.entry-title a, h2.entry-title{color:' . esc_attr(beshop_sanitize_css_color($beshop_header_color)) . ';}';
    }
    $beshop_bodyfont_color = get_theme_mod('beshop_bodyfont_color');
    if ($beshop_bodyfont_color) {
      $style .= 'body,body p{color:' . esc_attr(beshop_sanitize_css_color($beshop_bodyfont_color)) . ';}';
    }
    $beshop_contentbg_color = get_theme_mod('beshop_contentbg_color');
    if ($beshop_contentbg_color) {
      $style .= '.beshop-btext, .widget-area .widget, .site-footer, .archive-header, .search-header, .beshop-page, .site-main .comment-navigation, .site-main .posts-navigation, .site-main .post-navigation, .site-footer, .bshop-blog-list, .bshop-single-list, .comments-area,.bshop-simple-list.hasimg{background-color:' . esc_attr(beshop_sanitize_css_color($beshop_contentbg_color)) . ';}';
    }
    $beshop_basic_color = get_theme_mod('beshop_basic_color');
    if ($beshop_basic_color) {
      $style .= '.entry-footer,h3.widget-title, h2.widget-title,.site-footer, .site-main .comment-navigation, .site-main .posts-navigation, .site-main .post-navigation,span.count.cart-contents,.widget .search-form input.search-submit:hover{background-color:' . esc_attr(beshop_sanitize_css_color($beshop_basic_color)) . ';}';
      $style .= '.entry-meta, .entry-meta a,widget .search-form input.search-submit{color:' . esc_attr(beshop_sanitize_css_color($beshop_basic_color)) . ';}';
      $style .= '.widget .search-form input.search-submit{border-color:' . esc_attr(beshop_sanitize_css_color($beshop_basic_color)) . ';}';
    }
    $beshop_link_color = get_theme_mod('beshop_link_color');
    if ($beshop_link_color) {
      $style .= 'a{color:' . esc_attr(beshop_sanitize_css_color($beshop_link_color)) . ';}';
    }
    $beshop_linkhvr_color = get_theme_mod('beshop_linkhvr_color');
    if ($beshop_linkhvr_color) {
      $style .= 'a:hover,a:visited{color:' . esc_attr(beshop_sanitize_css_color($beshop_linkhvr_color)) . ';}';
    }
    $beshop_topfooter_bgcolor = get_theme_mod('beshop_topfooter_bgcolor');
    if ($beshop_topfooter_bgcolor) {
      $style .= '.footer-top.bg-dark{background:' . esc_attr(beshop_sanitize_css_color($beshop_topfooter_bgcolor)) . '  !important;}';
    }
    $beshop_tftitle_color = get_theme_mod('beshop_tftitle_color');
    if ($beshop_tftitle_color) {
      $style .= '.footer-widget .widget-title{color:' . esc_attr(beshop_sanitize_css_color($beshop_tftitle_color)) . '  !important;}';
    }
    $beshop_tftext_color = get_theme_mod('beshop_tftext_color');
    if ($beshop_tftext_color) {
      $style .= '.footer-widget, .footer-widget p, .footer-widget a, .footer-widget #wp-calendar caption, .footer-widget .search-form input.search-submit{color:' . esc_attr(beshop_sanitize_css_color($beshop_tftext_color)) . '  !important;}';
    }
    $beshop_tflink_hovercolor = get_theme_mod('beshop_tflink_hovercolor');
    if ($beshop_tflink_hovercolor) {
      $style .= '.footer-widget a:hover{color:' . esc_attr(beshop_sanitize_css_color($beshop_tflink_hovercolor)) . '  !important;}';
    }
    $beshop_footer_bgcolor = get_theme_mod('beshop_footer_bgcolor');
    if ($beshop_footer_bgcolor) {
      $style .= 'footer.site-footer{background:' . esc_attr(beshop_sanitize_css_color($beshop_footer_bgcolor)) . '  !important;}';
    }
    $beshop_footer_color = get_theme_mod('beshop_footer_color');
    if ($beshop_footer_color) {
      $style .= 'footer.site-footer,footer.site-footer a,footer.site-footer p{color:' . esc_attr(beshop_sanitize_css_color($beshop_footer_color)) . '  !important;}';
    }
    $beshop_footerlink_hcolor = get_theme_mod('beshop_footerlink_hcolor');
    if ($beshop_footerlink_hcolor) {
      $style .= 'footer.site-footer a:hover,footer.site-footer a:focus{color:' . esc_attr(beshop_sanitize_css_color($beshop_footerlink_hcolor)) . '  !important;}';
    }

    $beshop_shopb_img = get_theme_mod('beshop_shopb_img');

    if ($beshop_shopb_img) {
      $beshop_shopb_img_url = wp_get_attachment_image_src($beshop_shopb_img, 'full');
      if ($beshop_shopb_img_url) {
        $style .= '.beshop-banner.bg-overlay{background:url(' . esc_url($beshop_shopb_img_url[0]) . ')}';
      }
    }
    $beshop_bannertext_color = get_theme_mod('beshop_bannertext_color', '#fff');
    if ($beshop_bannertext_color != '#fff') {
      $style .= '.beshop-banner .bbanner-text h1,.beshop-banner .bbanner-text h4,.beshop-banner .bbanner-text p{color:' . esc_attr(beshop_sanitize_css_color($beshop_bannertext_color)) . ' !important}';
    }
    $beshop_bannerbtn_color = get_theme_mod('beshop_bannerbtn_color', '#fff');
    if ($beshop_bannerbtn_color != '#fff') {
      $style .= 'a.btn.bshop-btn{color:' . esc_attr(beshop_sanitize_css_color($beshop_bannerbtn_color)) . ' !important}';
    }
    $beshop_bannerbtn_bgcolor = get_theme_mod('beshop_bannerbtn_bgcolor', '#77A464');
    if ($beshop_bannerbtn_bgcolor != '#77A464') {
      $style .= 'a.btn.bshop-btn{background:' . esc_attr(beshop_sanitize_css_color($beshop_bannerbtn_bgcolor)) . ' !important}';
    }



    wp_add_inline_style('beshop-main', $style);
  }
  add_action('wp_enqueue_scripts', 'beshop_inline_css');
endif;
