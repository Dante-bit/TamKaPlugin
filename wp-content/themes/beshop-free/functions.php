<?php
/*This file is part of BeShop child theme.

All functions of this file will be loaded before of parent theme functions.
Learn more at https://codex.wordpress.org/Child_Themes.

Note: this function loads the parent stylesheet before, then child theme stylesheet
(leave it in place unless you know what you are doing.)
*/

if (!defined('BESHOP_FREE_VERSION')) {
	// Replace the version number of the theme on each release.
	define('BESHOP_FREE_VERSION', '1.0.2');
}



function beshop_free_fonts_url()
{
	$fonts_url = '';

	$font_families = array();

	$font_families[] = 'Oxygen:400,500,700';
	$font_families[] = 'Rubik:400,500,500i,700,700i';

	$query_args = array(
		'family' => urlencode(implode('|', $font_families)),
		'subset' => urlencode('latin,latin-ext'),
	);

	$fonts_url = add_query_arg($query_args, 'https://fonts.googleapis.com/css');


	return esc_url_raw($fonts_url);
}


function beshop_free_enqueue_child_styles()
{
	wp_enqueue_style('beshop-free-google-font', beshop_free_fonts_url(), array(), null);
	wp_enqueue_style('beshop-free-parent-style', get_template_directory_uri() . '/style.css', array('beshop-main', 'beshop-google-font', 'beshop-default'), '', 'all');
	wp_enqueue_style('beshop-free-main', get_stylesheet_directory_uri() . '/assets/css/main.css', array(), BESHOP_FREE_VERSION, 'all');

	wp_enqueue_script('masonry');
	wp_enqueue_script('beshop-free-main-js', get_stylesheet_directory_uri() . '/assets/js/main.js', array('jquery'), BESHOP_FREE_VERSION, true);
}
add_action('wp_enqueue_scripts', 'beshop_free_enqueue_child_styles');




/**
 * Customizer additions.
 */
require get_stylesheet_directory() . '/inc/customizer.php';
// // Nav walker for menu


/**
 * Filter the except length to 20 words.
 *
 * @param int $length Excerpt length.
 * @return int (Maybe) modified excerpt length.
 */
add_filter('excerpt_more', '__return_empty_string', 999);
function newsx_paper_excerpt_length($length)
{
	return 30;
}
add_filter('excerpt_length', 'newsx_paper_excerpt_length', 999);
