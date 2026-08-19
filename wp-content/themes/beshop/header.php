<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package BeShop
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'beshop' ); ?></a>
	<?php 
		$beshop_topbar_show = get_theme_mod( 'beshop_topbar_show', 1 );
		$beshop_hmiddle_show = get_theme_mod( 'beshop_hmiddle_show', 1 );
		$beshop_main_menu_show = get_theme_mod( 'beshop_main_menu_show', 1 );
		
	?>
	<header id="masthead" class="beshop-header site-header">
		<?php if(has_header_image() && empty($beshop_hmiddle_show)): ?>
		<div class="beshop-headerimg-top">
				<?php the_header_image_tag(); ?>
		</div>
		<?php endif; ?>
	<?php 
	do_action('beshop_mobile_menu');

	if($beshop_topbar_show){
		get_template_part( 'template-parts/header/header-top' );
	}
	if($beshop_hmiddle_show){
		get_template_part( 'template-parts/header/header-middle' );
	}
	if($beshop_main_menu_show){
		get_template_part( 'template-parts/header/header-main-menu' );
	}


	 ?>
		

	</header><!-- #masthead -->
	