<?php
/*
*
* beshop woocommerce related functions
*
*
*/
require get_template_directory() . '/inc/woo-items/customizer-woo.php';
require get_template_directory() . '/inc/woo-items/woo-inline-style.php';


function beshop_woocommerce_body_classes($classes)
{
	// Adds a class of hfeed to non-singular pages.
	if (! is_singular()) {
		$classes[] = 'woocommerce';
	}

	return $classes;
}
add_filter('body_class', 'beshop_woocommerce_body_classes');

if (!function_exists('beshop_woocommerce_setup')) {
	function beshop_woocommerce_setup()
	{
		add_theme_support('woocommerce');
		add_theme_support('wc-product-gallery-zoom');
		add_theme_support('wc-product-gallery-lightbox');
		add_theme_support('wc-product-gallery-slider');
	}
	add_action('after_setup_theme', 'beshop_woocommerce_setup',999);
}

if (!function_exists('beshop_woocommerce_scripts')) {
	function beshop_woocommerce_scripts()
	{
		wp_enqueue_style('beshop-woocommerce-style', get_template_directory_uri() . '/assets/css/beshop-woocommerce.css', array(), BESHOP_VERSION, 'all');
		wp_enqueue_script('beshop-number', get_template_directory_uri() . '/assets/js/number.js', array('jquery'), BESHOP_VERSION, false);
	}
	add_action('wp_enqueue_scripts', 'beshop_woocommerce_scripts');
}

if (!function_exists('beshop_woocommerce_cart_link_fragment')) {
	/**
	 * Cart Fragments.
	 *
	 * Ensure cart contents update when products are added to the cart via AJAX.
	 *
	 * @param array $fragments Fragments to refresh via AJAX.
	 * @return array Fragments to refresh via AJAX.
	 */
	function beshop_woocommerce_cart_link_fragment($fragments)
	{
		ob_start();
		beshop_woocommerce_cart_link();
		$fragments['.beshoping-bag'] = ob_get_clean();

		return $fragments;
	}
}
add_filter('woocommerce_add_to_cart_fragments', 'beshop_woocommerce_cart_link_fragment');

if (!function_exists('beshop_woocommerce_cart_link')) {
	/**
	 * Cart Link.
	 *
	 * Displayed a link to the cart including the number of items present and the cart total.
	 *
	 * @return void
	 */
	function beshop_woocommerce_cart_link()
	{
		$item_count_text = sprintf(
			/* translators: number of items in the mini cart. */
			_n('(%d)', '(%d)', WC()->cart->get_cart_contents_count(), 'beshop'),
			WC()->cart->get_cart_contents_count()
		);
?>
		<div class="beshoping-bag" data-bs-toggle="modal" data-bs-target="#cartModal">
			<div class="beshoping-inner-bag">
				<i class="fas fa-shopping-basket"></i>
				<span class="count cart-contents"><?php echo esc_html($item_count_text); ?></span>
			</div>
		</div>


	<?php
	}
}

if (!function_exists('beshop_woocommerce_header_cart')) {
	/**
	 * Display Header Cart.
	 *
	 * @return void
	 */
	function beshop_woocommerce_header_cart()
	{
		$beshop_basket_position = get_theme_mod('beshop_basket_position', 'right');
		if (is_cart()) {
			$class = 'current-menu-item xcart-page';
		} else {
			$class = 'not-cart-page';
		}

	?>
		<div class="beshoping-cart bbasket-<?php echo esc_attr($beshop_basket_position); ?> <?php echo esc_attr($class); ?>">
			<?php beshop_woocommerce_cart_link(); ?>
			<!-- Modal -->
			<div class="modal fade" id="cartModal" tabindex="-1" role="dialog" aria-labelledby="becartTitle" aria-hidden="true">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title" id="becartTitle"><?php echo esc_html__('Shopping Cart ', 'beshop'); ?></h5>
						</div>
						<div class="modal-body">
							<?php
							$instance = array(
								'title' => '',
							);

							the_widget('WC_Widget_Cart', $instance);
							?>

						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php esc_html_e('Close', 'beshop'); ?>
							</button>
						</div>
					</div>
				</div>
			</div>

		</div>
	<?php
	}
}

if (!function_exists('beshop_woowidgets_init')) {
	function beshop_woowidgets_init()
	{
		register_sidebar(array(
			'name'          => esc_html__('Shop Sidebar', 'beshop'),
			'id'            => 'shop-sidebar',
			'description'   => esc_html__('Add shop widgets here.', 'beshop'),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		));
		register_sidebar(array(
			'name'          => esc_html__('Shop Page Top Widget.', 'beshop'),
			'id'            => 'top-filter',
			'description'   => esc_html__('Shop Page products fileter top widget.', 'beshop'),
			'before_widget' => '<div id="%1$s" class="beshop-top-filter %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="top-widget-title d-none">',
			'after_title'   => '</h3>',
		));
	}
	add_action('widgets_init', 'beshop_woowidgets_init');
}

if (!function_exists('beshop_body_wooclasses')) {
	function beshop_body_wooclasses($classes)
	{

		if (!is_active_sidebar('shop-sidebar') && is_shop()) {
			$classes[] = 'no-shop-widget';
		}
		if (is_front_page() && is_shop()) {
			$classes[] = 'befront-shop';
		}

		return $classes;
	}
	add_filter('body_class', 'beshop_body_wooclasses');
}


/**
 * Change number or products per row 
 */
add_filter('loop_shop_columns', 'beshop_loop_columns', 999);
if (!function_exists('beshop_loop_columns')) {
	function beshop_loop_columns()
	{
		$beshop_shop_column = get_theme_mod('beshop_shop_column', '4');

		return $beshop_shop_column; // 4 products per row
	}
}

//add new div for product

function beshop_before_shop_loop_div()
{
	$beshop_shop_style = get_theme_mod('beshop_shop_style', '1');

	echo '<div class="beshop-poroduct style' . esc_attr($beshop_shop_style) . '">';
}
add_action('woocommerce_before_shop_loop_item', 'beshop_before_shop_loop_div', 5);

function beshop_after_shop_loop_div()
{
	echo '</div>';
}
add_action('woocommerce_after_shop_loop_item', 'beshop_after_shop_loop_div', 15);
// End div for product

function beshop_woobody_classes($classes)
{
	// Adds a class of hfeed to non-singular pages.
	if (is_shop()) {
		$classes[] = 'be-shop';
	}

	return $classes;
}
add_filter('body_class', 'beshop_woobody_classes');

function beshop_woocommerce_page_title($page_title)
{
	$beshop_shop_title = get_theme_mod('beshop_shop_title', esc_html__('Shop', 'beshop'));
	if (is_shop()) {
		return $beshop_shop_title;
	} else {
		return $page_title;
	}
}
add_filter('woocommerce_page_title', 'beshop_woocommerce_page_title');


// add filter widget in shop page top 

function beshop_woocommerce_before_shop_loop()
{
	if (is_active_sidebar('top-filter')) {
		$beshop_ftwidget_position = get_theme_mod('beshop_ftwidget_position', 'center');
	?>
		<div class="beshop-products-filter bestopwid-<?php echo esc_attr($beshop_ftwidget_position); ?>">
			<?php dynamic_sidebar('top-filter'); ?>
		</div>

<?php
	}
}
add_action('woocommerce_before_shop_loop', 'beshop_woocommerce_before_shop_loop', 15);



/*Checkout page edit*/

/*
 Remove all possible fields
 */
function beshop__remove_checkout_fields($fields)
{

	$beshop_checkout_lastname = get_theme_mod('beshop_checkout_lastname', 1);
	$beshop_checkout_email = get_theme_mod('beshop_checkout_email', 'required');
	$beshop_checkout_postcode = get_theme_mod('beshop_checkout_postcode', '1');

	if (empty($beshop_checkout_lastname)) {
		unset($fields['billing']['billing_last_name']);
		$fields['billing']['billing_first_name']['label'] = esc_html__('Name', 'beshop');
	}


	if ($beshop_checkout_email == 'hide') {
		unset($fields['billing']['billing_email']);
	}
	if (empty($beshop_checkout_postcode)) {
		unset($fields['billing']['billing_postcode']);
	}


	return $fields;
}
add_filter('woocommerce_checkout_fields', 'beshop__remove_checkout_fields');

function beshop__required_checkout_fields($fields)
{
	$beshop_checkout_email = get_theme_mod('beshop_checkout_email', 'required');

	if ($beshop_checkout_email == 'optional') {
		$fields['billing_email']['required'] = false;
	}



	return $fields;
}
add_filter('woocommerce_billing_fields', 'beshop__required_checkout_fields');



function beshop_products_pagination($max_num_pages)
{
	$big = 999999999; // need an unlikely integer

	$pages = paginate_links(array(
		'base'    => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
		'format'  => '?paged=%#%',
		'current' => max(1, get_query_var('paged')),
		'total'   => $max_num_pages,
		'type'    => 'array',
		'prev_text' => __('&laquo;', 'beshop'),
		'next_text' => __('&raquo;', 'beshop'),
	));

	if (is_array($pages)) {
		$pagination = '<ul class="page-numbers">';
		foreach ($pages as $page) {
			$pagination .= '<li>' . $page . '</li>';
		}
		$pagination .= '</ul>';
		echo $pagination;
	}
}


/**
 * Check if there are any products in the WooCommerce store.
 *
 * @return bool
 */
function beshop_has_woocommerce_products()
{
	// Define the query arguments
	$args = array(
		'post_type'      => 'product',
		'posts_per_page' => 1,
		'post_status'    => 'publish'
	);

	// Get the products
	$products = new WP_Query($args);

	// Check if there are products
	$has_products = $products->have_posts();

	// Reset the post data
	wp_reset_postdata();

	// Check product
	if ($has_products) {
		return true;
	}
	return false;
}
