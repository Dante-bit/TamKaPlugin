<?php

/**
 * Template part for displaying header top bar
 *
 * @link https://wpthemespace.com/product/beshop
 *
 * @package BeShop
 */

$beshop_menu_logo = get_theme_mod('beshop_menu_logo');
$beshop_logo_position = get_theme_mod('beshop_logo_position', 'center');

?>

<div class="beshop-main-nav bg-dark text-white menulogo-<?php echo esc_attr($beshop_logo_position); ?>">
	<div class="container">
		<div class="<?php if ($beshop_menu_logo): ?>d-flex has-logo-menu<?php else: ?>logo-hide<?php endif; ?>">
			<?php if ($beshop_logo_position == 'left' && $beshop_menu_logo): ?>
				<div class="menu-logo">
					<?php beshop_header_logo(1); ?>
				</div>
			<?php endif; ?>
			<div class="beshop-main-menu flex-grow-1">
				<nav id="site-navigation" class="main-navigation">
					<button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false"><span class="mshow"><?php esc_html_e('Menu', 'beshop'); ?></span><span class="mhide"><?php esc_html_e('Close Menu', 'beshop'); ?></span></button>
					<?php
						wp_nav_menu(
							array(
								'theme_location' => 'menu-1',
								'menu_id'        => 'primary-menu',
								'menu_class'     => 'beshop-main-menu-container',
								'fallback_cb'     => 'beshop_menu_fallback',	
								'walker'        => new Beshop_Walker_Nav_Menu(),
							)
						);
					?>
				</nav><!-- #site-navigation -->
			</div>
			<?php if ($beshop_logo_position == 'right' && $beshop_menu_logo): ?>
				<div class="menu-logo">
					<?php beshop_header_logo(); ?>
				</div>
			<?php endif; ?>
		</div>
		<?php if ($beshop_logo_position == 'center' && $beshop_menu_logo): ?>
			<div class="menu-logo mt-3">
				<?php beshop_header_logo(); ?>
			</div>
		<?php endif; ?>
	</div>
</div>