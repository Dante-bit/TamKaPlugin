<?php
/**
 * Sample implementation of the Custom Header feature
 *
 * You can add an optional custom header image to header.php like so ...
 *
	<?php the_header_image_tag(); ?>
 *
 * @link https://developer.wordpress.org/themes/functionality/custom-headers/
 *
 * @package BeShop
 */

/**
 * Set up the WordPress core custom header feature.
 *
 * @uses beshop_header_style()
 */
function beshop_custom_header_setup() {
	add_theme_support(
		'custom-header',
		apply_filters(
			'beshop_custom_header_args',
			array(
				'default-image'      => '',
				'default-text-color' => '000000',
				'width'              => 1800,
				'height'             => 250,
				'flex-height'        => true,
				'wp-head-callback'   => '',
			)
		)
	);
}
add_action( 'after_setup_theme', 'beshop_custom_header_setup' );


/**
 * Mobile Menu Output
 * 
 * Displays responsive mobile navigation with:
 * - Topbar with site logo and hamburger menu button
 * - Full-screen slide-in menu panel with focus trap
 * - ARIA labels and keyboard accessibility support
 * - Proper focus management and screen reader compatibility
 * - Semantic HTML structure for accessibility
 * - Keyboard focus trapped inside menu when open
 * 
 * @since 1.0.0
 */
function beshop_mobile_menu_output()
{
?>
	<div id="wsm-menu" class="mobile-menu-bar wsm-menu">
		<div class="container">
			<div class="mobile-topbar">
				<div class="mobile-topbar-logo">
					<?php
					if (has_custom_logo()) {
						the_custom_logo();
					} else {
					?>
						<a href="<?php echo esc_url(home_url('/')); ?>" class="mobile-site-title" rel="home">
							<?php bloginfo('name'); ?>
						</a>
					<?php } ?>
				</div>
				<button id="mmenu-btn" class="menu-btn" aria-expanded="false" aria-controls="mobile-menu-panel" aria-label="<?php esc_attr_e('Open mobile menu', 'beshop'); ?>">
					<span class="mopen" aria-hidden="true">
						<svg class="hamburger-icon" width="26" height="26" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" focusable="false">
							<path d="M3 12h18M3 6h18M3 18h18" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
						</svg>
					</span>
					<span class="mclose" aria-hidden="true">
						<svg class="close-icon" width="26" height="26" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" focusable="false">
							<path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
						</svg>
					</span>
					<span class="sr-only"><?php esc_html_e('Menu', 'beshop'); ?></span>
				</button>
			</div>
		</div>

		<!-- Overlay backdrop -->
		<div id="mobile-menu-overlay" class="mobile-menu-overlay" aria-hidden="true"></div>

		<!-- Slide-in menu panel -->
		<nav id="mobile-menu-panel" class="mobile-menu-panel" role="navigation" aria-label="<?php esc_attr_e('Mobile Menu', 'beshop'); ?>" aria-hidden="true">
			<div class="mobile-panel-header">
				<div class="mobile-panel-logo">
					<?php
					if (has_custom_logo()) {
						the_custom_logo();
					} else {
					?>
						<a href="<?php echo esc_url(home_url('/')); ?>" class="mobile-site-title" rel="home">
							<?php bloginfo('name'); ?>
						</a>
					<?php } ?>
				</div>
				<button id="mmenu-close-btn" class="menu-close-btn" aria-label="<?php esc_attr_e('Close mobile menu', 'beshop'); ?>">
					<svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" focusable="false">
						<path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" />
					</svg>
				</button>
			</div>
			<div id="mobile-navigation" class="mobile-navigation">
				<?php
				wp_nav_menu(array(
					'theme_location' => 'menu-1',
					'menu_id'        => 'wsm-menu-ul',
					'menu_class'     => 'wsm-menu-has',
					'container'      => false,
				));
				?>
			</div>
		</nav>
	</div>

<?php
}
add_action('beshop_mobile_menu', 'beshop_mobile_menu_output');


