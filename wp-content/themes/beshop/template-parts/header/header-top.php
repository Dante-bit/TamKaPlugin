<?php

/**
 * Template part for displaying header top bar
 *
 * @link https://wpthemespace.com/product/beshop
 *
 * @package BeShop
 */
$beshop_topbar_mtext = get_theme_mod('beshop_topbar_mtext', esc_html__('Welcome to Our Website', 'beshop'));
$beshop_topbar_menushow = get_theme_mod('beshop_topbar_menushow', 1);
//$beshop_topbar_search = get_theme_mod( 'beshop_topbar_search','1' );

$beshop_topbar_search_item = get_theme_mod('beshop_topbar_search_item', 'popup');


?>

<div class="beshop-tophead bg-dark text-light <?php if ($beshop_topbar_search_item == 'simple') : ?>has-search pt-1 pb-1<?php else : ?>pt-2 pb-2<?php endif; ?>">
	<div class="container">
		<div class="row">
			<?php if ($beshop_topbar_mtext) : ?>
				<div class="col-lg-auto">
					<span class="bhtop-text pt-2"><?php echo esc_html($beshop_topbar_mtext); ?></span>
				</div>
			<?php endif; ?>
			<?php if ($beshop_topbar_search_item != 'hide' || ($beshop_topbar_menushow && has_nav_menu('btop-menu'))) : ?>
				<div class="col-lg-auto ms-auto">
					<div class="topmenu-serch bsearch-<?php echo esc_attr($beshop_topbar_search_item); ?>">
						<?php if ($beshop_topbar_menushow && has_nav_menu('btop-menu')) : ?>
							<div class="top-menu list-hide text-white">
								<?php
								wp_nav_menu(
									array(
										'theme_location' => 'btop-menu',
										'menu_id'        => 'btop-menu',
										'menu_class'     => 'btop-menu',
										'depth'          => 1,
										'fallback_cb'    => false,
									)
								);
								?>
							</div>
						<?php endif; ?>
						<?php if ($beshop_topbar_search_item == 'simple') : ?>
							<div class="header-top-search">
								<?php get_search_form(); ?>
							</div>
						<?php endif; ?>
						<?php if ($beshop_topbar_search_item == 'popup') : ?>
							<div class="besearch-icon">
								<a href="#" id="besearch"><i class="fas fa-search"></i></a>
							</div>
						<?php endif; ?>
					</div>
				</div>
			<?php endif; ?>

		</div>
	</div>
</div>

<?php if ($beshop_topbar_search_item == 'popup') : ?>
	<div id="bspopup" class="off">
		<div id="bessearch" class="open">
			<button data-widget="remove" id="removeClass" class="close" type="button">×</button>
			<?php get_search_form(); ?>
			<small class="beshop-cradit"><?php esc_html_e('Beshop Theme By', 'beshop') ?> <a target="_blank" title="<?php esc_attr_e('Beshop Theme', 'beshop') ?>" href="<?php echo esc_url('https://wpthemespace.com/product/beshop/'); ?>"><?php esc_html_e('Wp Theme Space', 'beshop') ?></a></small>
		</div>
	</div>
<?php endif; ?>