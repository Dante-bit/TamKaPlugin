<?php
/**
 * Template part for displaying header middle
 *
 * @link https://wpthemespace.com/product/beshop
 *
 * @package BeShop
 */
$beshop_logo_position = get_theme_mod( 'beshop_logo_position','center');
$beshop_himg_height = get_theme_mod( 'beshop_himg_height','fixed');

?> 
<?php if(has_header_image()): ?>
		<div class="site-branding has-himg text-<?php echo esc_attr($beshop_logo_position); ?> <?php if(display_header_text() == true && has_custom_logo()): ?>bshop-two-logo<?php endif; ?>behimg-<?php echo esc_attr($beshop_himg_height); ?>">
			<div class="beshop-header-img">
				<?php the_header_image_tag(); ?>
			</div>
	<?php else: ?>
		<div class="site-branding text-<?php echo esc_attr($beshop_logo_position); ?>">
	<?php endif; ?>
			<?php beshop_header_logo(5); ?>
			
		</div><!-- .site-branding -->	

