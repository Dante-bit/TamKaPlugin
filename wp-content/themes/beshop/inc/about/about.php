<?php

/**
 * About setup
 *
 * @package shop toolkit
 */

if (!function_exists('beshop_about_setup')) :

	/**
	 * About setup.
	 *
	 * @since 1.0.0
	 */
	function beshop_about_setup()
	{
		$theme = wp_get_theme();
		$theme_name = $theme->get('Name');
		$theme_domain = $theme->get('TextDomain');
		$theme_slug = $theme_domain;




		$config = array(
			// Menu name under Appearance.
			'menu_name'               => sprintf(esc_html__('%s Info', 'beshop'), $theme_name),
			// Page title.
			'page_name'               => sprintf(esc_html__('%s Info', 'beshop'), $theme_name),
			/* translators: Main welcome title */
			'welcome_title'         => sprintf(esc_html__('Welcome to %s! - Version ', 'beshop'), $theme['Name']),
			// Main welcome content
			// Welcome content.
			'welcome_content' => sprintf(esc_html__('%1$s is now installed and ready to use. We want to make sure you have the best experience using the theme and that is why we gathered here all the necessary information for you. Thanks for using our theme!', 'beshop'), $theme['Name']),

			// Tabs.
			'tabs' => array(
				'getting_started' => esc_html__('Getting Started', 'beshop'),
				'recommended_actions' => esc_html__('Recommended Actions', 'beshop'),
				'useful_plugins'  => esc_html__('Useful Plugins', 'beshop'),
				'free_pro'  => esc_html__('Free Vs Pro', 'beshop'),
			),

			// Quick links.
			'quick_links' => array(
				'upgrade' => array(
					'text'   => esc_html__('Upgrade BeShop Pro', 'beshop'),
					'url'    => esc_url('https://wpthemespace.com/product/beshop-pro/?add-to-cart=7351'),
					'button' => 'danger',
				),
				'install_plugin' => array(
					'text'   => esc_html__('BeShop Video Tutorial', 'beshop'),
					'url'    => esc_url('https://wpthemespace.com/beshop-theme-video/'),
					'button' => 'danger',
				),

				'demo_live_url' => array(
					'text'   => esc_html__('View Details & Demo', 'beshop'),
					'url'    => 'https://wpthemespace.com/product/beshop-pro/',
					'button' => 'primary',
				),
			),

			// Getting started.
			'getting_started' => array(
				'insplugin' => array(
					'title'       => esc_html__('Install & Active Recommended Plugin', 'beshop'),
					'icon'        => 'dashicons dashicons-plugins-checked',
					'description' => sprintf(esc_html__('You need to instsll and active all recomended plugin. If you want to run your site like demo then you need to install all recomended plugin. BeShop theme work fine without any plugin but if you want to use extra features and benefits then need to active all recommended plugin. ', 'beshop'), esc_html__('One Click Demo Import', 'beshop')),
					'button_text' => esc_html__('INSTALL & ACTIVE PLUGIN', 'beshop'),
					'button_url'  => esc_url(admin_url('themes.php?page=tgmpa-install-plugins')),
					'button_type' => 'primary',
					'is_new_tab'  => true,
				),
				'one' => array(
					'title'       => esc_html__('One Click Demo Import', 'beshop'),
					'icon'        => 'dashicons dashicons-smiley',
					'description' => sprintf(esc_html__('One Click Demo import is an awesome feature. Bishop Theme gives you these awesome features for free!! %1$s should be installed and activated. After plugin is activated, visit Import Demo Data menu under Appearance. Now choose and hit the import demo button. Wow!! your site is ready now with awesome style.', 'beshop'), esc_html__('All recomended plugins', 'beshop')),
					'button_text' => esc_html__('Select Demo', 'beshop'),
					'button_url'  => esc_url(admin_url('themes.php?page=pt-one-click-demo-import')),
					'button_type' => 'primary',
					'is_new_tab'  => false,
				),

				'two' => array(
					'title'       => esc_html__('Theme Options', 'beshop'),
					'icon'        => 'dashicons dashicons-admin-customizer',
					'description' => esc_html__('BeShop Theme uses Customizer API for theme options. The theme has huge options for customize your site. You can change theme typography, unlimited color, header style, blog style, products style and many more.You will be surprised to see so many options for free.  Using the Customizer you can easily customize different aspects of the theme.', 'beshop'),
					'button_text' => esc_html__('Customize', 'beshop'),
					'button_url'  => wp_customize_url(),
					'button_type' => 'primary',
				),
				'widget' => array(
					'title'       => esc_html__('Set Widgets', 'beshop'),
					'icon'        => 'dashicons dashicons-tagcloud',
					'description' => esc_html__('The BeShop theme support three different widget position. There are Sidebar, Footer Widget and Shop Sidebar. Sidebar will show blog and page, Footer Widget will show all posts and pages footer and Shop sidebar will show shop page only.', 'beshop'),
					'button_text' => esc_html__('Add Widgets', 'beshop'),
					'button_url'  => admin_url() . '/widgets.php',
					'button_type' => 'primary',
					'is_new_tab'  => true,
				),
				'three' => array(
					'title'       => esc_html__('Show Video tutorial', 'beshop'),
					'icon'        => 'dashicons dashicons-layout',
					'description' => esc_html__('Please check our full video documentation for detailed information on how to setup and customize the theme. You may see all videos for shop toolkit theme better understanding', 'beshop'),
					'button_text' => esc_html__('Show video', 'beshop'),
					'button_url'  => 'https://wpthemespace.com/beshop-theme-video/',
					'button_type' => 'primary',
					'is_new_tab'  => true,
				),

				'six' => array(
					'title'       => esc_html__('Theme Preview', 'beshop'),
					'icon'        => 'dashicons dashicons-welcome-view-site',
					'description' => esc_html__('You can check out the theme demos for reference to find out what you can achieve using the theme and how it can be customized. Theme demo only work in pro theme', 'beshop'),
					'button_text' => esc_html__('View Demo', 'beshop'),
					'button_url'  => 'https://wpthemespace.com/product/beshop-pro/',
					'button_type' => 'primary',
					'is_new_tab'  => true,
				),
				'seven' => array(
					'title'       => esc_html__('Contact Support', 'beshop'),
					'icon'        => 'dashicons dashicons-sos',
					'description' => esc_html__('Got theme support question or found bug or got some feedbacks? Best place to ask your query is the dedicated Support forum for the theme.', 'beshop'),
					'button_text' => esc_html__('Contact Support', 'beshop'),
					'button_url'  => 'https://wpthemespace.com/support/',
					'button_type' => 'link',
					'is_new_tab'  => true,
				),
			),

			'useful_plugins'        => array(
				'description' => esc_html__('Theme supports some helpful WordPress plugins to enhance your site. But, please enable only those plugins which you need in your site. For example, enable WooCommerce only if you are using e-commerce.', 'beshop'),
				'already_activated_message' => esc_html__('Already activated', 'beshop'),
				'version_label' => esc_html__('Version: ', 'beshop'),
				'install_label' => esc_html__('Install and Activate', 'beshop'),
				'activate_label' => esc_html__('Activate', 'beshop'),
				'deactivate_label' => esc_html__('Deactivate', 'beshop'),
				'content'                   => array(
					array(
						'slug' => 'be-boost'
					),
					array(
						'slug' => 'magical-posts-display'
					),
					array(
						'slug' => 'magical-products-display'
					),
					array(
						'slug' => 'magical-addons-for-elementor'
					),
					array(
						'slug' => 'woocommerce'
					),
					array(
						'slug' => 'one-click-demo-import'
					),
					array(
						'slug' => 'click-to-top'
					),
					array(
						'slug' => 'gallery-box',
						'icon' => 'svg',
					),
					array(
						'slug' => 'easy-share-solution',
						'icon' => 'svg',
					),
					array(
						'slug' => 'wp-edit-password-protected',
						'icon' => 'svg',
					),
				),
			),
			// Required actions array.
			'recommended_actions'        => array(
				'install_label' => esc_html__('Install and Activate', 'beshop'),
				'activate_label' => esc_html__('Active Now', 'beshop'),
				'deactivate_label' => esc_html__('Good Job Plugin Actiated', 'beshop'),
				'content'            => array(
					'one-click' => array(
						'title'       => __('BeBoost Demo Improt', 'beshop'),
						'description' => __('Need to active the plugin with BeBoost plugin for demo import.', 'beshop'),
						'plugin_slug' => 'one-click-demo-import',
						'id' => 'magical-blocks'
					),
					'go-pro' => array(
						'title'       => '<a target="_blank" class="activate-now button button-danger" href="https://wpthemespace.com/product/beshop-pro/?add-to-cart=7351">' . __('UPGRADE SHOP TOOLKITPRO', 'beshop') . '</a>',
						'description' => __('You will get more frequent updates and quicker support with the Pro version.', 'beshop'),
						//'plugin_slug' => 'x-instafeed',
						'id' => 'go-pro'
					),

				),
			),
			// Free vs pro array.
			'free_pro'                => array(
				'free_theme_name'     => $theme_name,
				'pro_theme_name'      => $theme_name . __(' Pro', 'beshop'),
				'pro_theme_link'      => 'https://wpthemespace.com/product/' . $theme_slug,
				'get_pro_theme_label' => sprintf(__('Get %s', 'beshop'), 'BeShop Pro'),
				'features'            => array(
					array(
						'title'       => esc_html__('Fully Responsive Layout', 'beshop'),
						/*'description' => esc_html__( 'BeShop\'s design helps you stand out from the crowd and create an experience that your readers will love and talk about. With a flexible home page you have the chance to easily showcase appealing content with ease.', 'beshop' ),*/
						'is_in_lite'  => 'true',
						'is_in_pro'   => 'true',
					),
					array(
						'title'       => esc_html__('Google Font Support', 'beshop'),

						'is_in_lite'  => 'true',
						'is_in_pro'   => 'true',
					),
					array(
						'title'       => esc_html__('SEO & SMM Friendly', 'beshop'),

						'is_in_lite'  => 'true',
						'is_in_pro'   => 'true',
					),
					array(
						'title'       => esc_html__('Fully WooCommerce Ready', 'beshop'),

						'is_in_lite'  => 'true',
						'is_in_pro'   => 'true',
					),
					array(
						'title'       => esc_html__('Gutenberg Support', 'beshop'),

						'is_in_lite'  => 'true',
						'is_in_pro'   => 'true',
					),
					array(
						'title'       => esc_html__('One Click Demo Installation', 'beshop'),

						'is_in_lite'  => 'true',
						'is_in_pro'   => 'true',
					),
					array(
						'title'       => esc_html__('Advance Shop options', 'beshop'),

						'is_in_lite'  => 'false',
						'is_in_pro'   => 'true',
					),
					array(
						'title'       => esc_html__('One Click Checkout', 'beshop'),

						'is_in_lite'  => 'false',
						'is_in_pro'   => 'true',
					),
					array(
						'title'       => esc_html__('Youtube Video Support for products', 'beshop'),

						'is_in_lite'  => 'false',
						'is_in_pro'   => 'true',
					),
					array(
						'title'       => esc_html__('QR Code Feature Support', 'beshop'),

						'is_in_lite'  => 'false',
						'is_in_pro'   => 'true',
					),
					array(
						'title'       => esc_html__('Custom Elementor Products Addons', 'beshop'),

						'is_in_lite'  => 'false',
						'is_in_pro'   => 'true',
					),
					array(
						'title'       => esc_html__('Custom Elementor Blog Addons', 'beshop'),

						'is_in_lite'  => 'false',
						'is_in_pro'   => 'true',
					),
					array(
						'title'       => esc_html__('Advanced Widgets', 'beshop'),

						'is_in_lite'  => 'false',
						'is_in_pro'   => 'true',
					),
					array(
						'title'       => esc_html__('Remove or edit Footer Cradit link', 'beshop'),

						'is_in_lite'  => 'false',
						'is_in_pro'   => 'true',
					),

				),
			), // free pro tab

		);

		beshop_About::init($config);
	}

endif;

add_action('after_setup_theme', 'beshop_about_setup');

/**
 * Pro notice text
 *
 */
function beshop_pnotice_output()
{
?>
	<div class="mgadin-hero">
		<div class="mge-info-content">
			<div class="mge-info-hello">
				<?php
				$current_theme = wp_get_theme();
				$current_theme_name = $current_theme->get('Name');
				$current_user = wp_get_current_user();
				$demo_link = esc_url('https://wpthemespace.com/product/beshop-pro/');
				$pro_link = esc_url('https://wpthemespace.com/product/beshop-pro/?add-to-cart=7351');

				esc_html_e('Hello, ', 'beshop');
				echo esc_html($current_user->display_name);
				?>
				<?php esc_html_e('👋🏻', 'beshop'); ?>
			</div>
			<div class="mge-info-desc">
				<div class="mge-upgrade-text">
					<?php printf(esc_html__('🎁 Buy %s Pro & Get Magical Products Display Pro Version FREE!', 'beshop'), $current_theme_name); ?>
				</div>
				<div class="mge-features-list">
					<div class="mge-feature">🎁 <?php esc_html_e('FREE Magical Products Display Pro ($49 Value)', 'beshop'); ?></div>
					<div class="mge-feature">✓ <?php esc_html_e('Premium WooCommerce layouts & templates', 'beshop'); ?></div>
					<div class="mge-feature">✓ <?php esc_html_e('Advanced product showcase options', 'beshop'); ?></div>
					<div class="mge-feature">✓ <?php esc_html_e('Smart filtering & sorting features', 'beshop'); ?></div>
					<div class="mge-feature">✓ <?php esc_html_e('One Click Demo Import', 'beshop'); ?></div>
					<div class="mge-feature">✓ <?php esc_html_e('Quick view & compare products', 'beshop'); ?></div>
					<div class="mge-feature">✓ <?php esc_html_e('Enhanced cart & checkout experience', 'beshop'); ?></div>
					<div class="mge-feature">✓ <?php esc_html_e('Priority support & lifetime updates', 'beshop'); ?></div>
				</div>
				<div class="mge-offer">
					<?php esc_html_e('🔥 LIMITED TIME OFFER: Buy BeShop Pro & Get Magical Products Display Pro Version FREE! Don\'t miss out! ⏰', 'beshop'); ?>
				</div>
			</div>
			<div class="mge-info-actions">
				<a href="<?php echo esc_url($pro_link); ?>" target="_blank" class="button button-primary upgrade-btn">
					<?php esc_html_e('Get BeShop Pro + FREE Pro Plugin', 'beshop'); ?>
				</a>
				<a href="<?php echo esc_url($demo_link); ?>" target="_blank" class="button button-primary demo-btn">
					<?php esc_html_e('View Live Demo', 'beshop'); ?>
				</a>
				<button class="button button-info btnend"><?php esc_html_e('Dismiss this notice', 'beshop') ?></button>
			</div>
		</div>
	</div>
<?php
}

//Admin notice 
function beshop_new_optins_texts()
{
	if ( ! get_user_meta( get_current_user_id(), 'aime_notice_dismissed', true ) ) {
		return;
	}
	$screen = get_current_screen();

	if ($screen && $screen->base == 'themes') {
		return;
	}

	$hide_date = get_option('beshop_info_text_details99');
	if (!empty($hide_date)) {
		$clickhide = round((time() - strtotime($hide_date)) / 24 / 60 / 60);
		if ($clickhide < 25) {
			return;
		}
	}

?>
	<div class="mgadin-notice notice notice-success mgadin-theme-dashboard mgadin-theme-dashboard-notice mge is-dismissible meis-dismissible">
		<?php beshop_pnotice_output(); ?>
	</div>
<?php

}
add_action('admin_notices', 'beshop_new_optins_texts');

function beshop_new_optins_texts_init()
{
	if (isset($_GET['besnotice']) && $_GET['besnotice'] == 1) {
		update_option('beshop_info_text_details99', current_time('mysql'));
	}
	$beshop_install_date = get_option('beshop_install_date');
	if (empty($beshop_install_date)) {
		update_option('beshop_install_date', current_time('mysql'));
	}
}
add_action('init', 'beshop_new_optins_texts_init');
