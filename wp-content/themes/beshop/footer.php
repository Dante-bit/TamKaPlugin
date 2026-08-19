<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package BeShop
 */
$beshop_topfooter_show = get_theme_mod( 'beshop_topfooter_show', 1 );
$beshop_basket_visibility = get_theme_mod( 'beshop_basket_visibility', 'all' );

?>
<?php if( is_active_sidebar( 'footer-widget' ) && $beshop_topfooter_show ): ?>
	<div class="footer-top mt-5 pb-5 pt-5 bg-dark">
		<div class="footer-widget">
			<div class="container">
				<div class="row">
					<?php dynamic_sidebar( 'footer-widget' ) ?>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>

	<footer id="colophon" class="site-footer text-center">
		<div class="site-info finfo">
			<a href="<?php echo esc_url( __( 'https://wordpress.org/', 'beshop' ) ); ?>">
				<?php _e( 'Powered by WordPress', 'beshop' ); ?>
			</a>
			
				<?php
				/* translators: 1: Theme name, 2: Theme author. */
				printf( __( 'Theme: %1$s by %2$s.', 'beshop' ), 'beshop', '<a href="https://wpthemespace.com/">wp theme space</a>' );
				?>
		</div><!-- .site-info -->
	</footer><!-- #colophon -->
</div><!-- #page -->
<?php
	if ( function_exists( 'beshop_woocommerce_header_cart' ) ) {
		if( ($beshop_basket_visibility == 'all') || ($beshop_basket_visibility == 'shop' && is_shop()) ){
		beshop_woocommerce_header_cart();
		}
	}
?>
<?php wp_footer(); ?>

</body>
</html>