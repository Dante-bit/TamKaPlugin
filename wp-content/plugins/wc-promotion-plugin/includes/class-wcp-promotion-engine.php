<?php
/**
 * Bộ máy áp dụng khuyến mãi thực tế lên giá sản phẩm WooCommerce,
 * dựa trên dữ liệu đã lưu từ 2 file phụ (thời gian + %).
 * File này không có giao diện, chỉ xử lý logic hook vào WooCommerce.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WCP_Promotion_Engine {

	public function __construct() {
		// Sản phẩm đơn giản.
		add_filter( 'woocommerce_product_get_price', array( $this, 'filter_price' ), 99, 2 );
		add_filter( 'woocommerce_product_get_sale_price', array( $this, 'filter_sale_price' ), 99, 2 );

		// Biến thể sản phẩm (variation).
		add_filter( 'woocommerce_product_variation_get_price', array( $this, 'filter_price' ), 99, 2 );
		add_filter( 'woocommerce_product_variation_get_sale_price', array( $this, 'filter_sale_price' ), 99, 2 );

		// Badge "Sale!" -> đổi thành "-X%" và đổi màu đỏ.
		add_filter( 'woocommerce_sale_flash', array( $this, 'custom_sale_badge' ), 10, 3 );

		// Nạp CSS cho badge ở phía ngoài trang (shop, chi tiết sản phẩm...).
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_style' ) );
	}

	/**
	 * Nạp CSS đổi màu badge khuyến mãi sang màu đỏ.
	 */
	public function enqueue_frontend_style() {
		wp_register_style( 'wcp-frontend', false, array(), WCP_VERSION );
		wp_enqueue_style( 'wcp-frontend' );
		wp_add_inline_style(
			'wcp-frontend',
			'.onsale.wcp-onsale{background-color:#e2001a;color:#fff;}'
		);
	}

	/**
	 * Thay nội dung badge "Sale!" mặc định của WooCommerce bằng % giảm giá
	 * (ví dụ: -20%), chỉ áp dụng cho sản phẩm đang nằm trong chương trình
	 * khuyến mãi do plugin quản lý. Các trường hợp giảm giá khác (đặt sale
	 * price thủ công trong WooCommerce) vẫn giữ nguyên badge "Sale!" mặc định.
	 *
	 * @param string     $html    HTML badge mặc định.
	 * @param WP_Post    $post    Post của sản phẩm.
	 * @param WC_Product $product Sản phẩm.
	 */
	public function custom_sale_badge( $html, $post, $product ) {
		if ( ! $product instanceof WC_Product ) {
			return $html;
		}

		if ( ! self::is_active() || ! self::is_product_included( $product ) ) {
			return $html;
		}

		$percent = floatval( get_option( 'wcp_promo_percent', 0 ) );
		if ( $percent <= 0 ) {
			return $html;
		}

		// Làm tròn để hiển thị gọn (VD: 20.0 -> 20, 12.5 -> 12.5).
		$percent_label = ( floor( $percent ) === $percent ) ? (string) intval( $percent ) : rtrim( rtrim( number_format( $percent, 1 ), '0' ), '.' );

		return sprintf(
			'<span class="onsale wcp-onsale">-%s%%</span>',
			esc_html( $percent_label )
		);
	}

	/**
	 * Kiểm tra chương trình khuyến mãi có đang trong khoảng thời gian hiệu lực không.
	 */
	public static function is_active() {
		if ( 'yes' !== get_option( 'wcp_promo_enabled', 'no' ) ) {
			return false;
		}

		$start = get_option( 'wcp_promo_start', '' );
		$end   = get_option( 'wcp_promo_end', '' );

		if ( empty( $start ) || empty( $end ) ) {
			return false;
		}

		$now = current_time( 'timestamp' ); // Giờ theo cấu hình WordPress.

		return ( $now >= strtotime( $start ) && $now <= strtotime( $end ) );
	}

	/**
	 * Đếm số sản phẩm đang nằm trong phạm vi áp dụng khuyến mãi hiện tại
	 * (dùng để hiển thị ở stat card trên trang thiết lập).
	 */
	public static function count_included_products() {
		$scope = get_option( 'wcp_promo_scope', 'all' );

		if ( 'all' === $scope ) {
			$counts = wp_count_posts( 'product' );
			return isset( $counts->publish ) ? (int) $counts->publish : 0;
		}

		if ( 'category' === $scope ) {
			$categories = (array) get_option( 'wcp_promo_categories', array() );
			if ( empty( $categories ) ) {
				return 0;
			}
			$query = new WP_Query(
				array(
					'post_type'      => 'product',
					'post_status'    => 'publish',
					'fields'         => 'ids',
					'posts_per_page' => -1,
					'no_found_rows'  => false,
					'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
						array(
							'taxonomy' => 'product_cat',
							'field'    => 'term_id',
							'terms'    => $categories,
						),
					),
				)
			);
			return (int) $query->found_posts;
		}

		if ( 'product' === $scope ) {
			return count( (array) get_option( 'wcp_promo_products', array() ) );
		}

		return 0;
	}

	/**
	 * Kiểm tra 1 sản phẩm có nằm trong phạm vi áp dụng khuyến mãi không.
	 *
	 * @param WC_Product $product
	 */
	public static function is_product_included( $product ) {
		$scope = get_option( 'wcp_promo_scope', 'all' );

		if ( 'all' === $scope ) {
			return true;
		}

		// Với biến thể, lấy id sản phẩm cha để so khớp danh mục/danh sách.
		$product_id = $product->is_type( 'variation' ) ? $product->get_parent_id() : $product->get_id();

		if ( 'category' === $scope ) {
			$categories = (array) get_option( 'wcp_promo_categories', array() );
			if ( empty( $categories ) ) {
				return false;
			}
			return has_term( $categories, 'product_cat', $product_id );
		}

		if ( 'product' === $scope ) {
			$products = (array) get_option( 'wcp_promo_products', array() );
			return in_array( $product_id, $products, true );
		}

		return false;
	}

	/**
	 * Tính giá sau khi giảm % dựa trên giá gốc (regular price).
	 */
	private function calculate_sale_price( $product ) {
		$regular = $product->get_regular_price();
		if ( '' === $regular || null === $regular ) {
			return null;
		}

		$percent = floatval( get_option( 'wcp_promo_percent', 0 ) );
		if ( $percent <= 0 ) {
			return null;
		}

		$sale = (float) $regular - ( (float) $regular * $percent / 100 );
		return wc_format_decimal( max( 0, $sale ), wc_get_price_decimals() );
	}

	/**
	 * Hook: woocommerce_product_get_price / woocommerce_product_variation_get_price
	 */
	public function filter_price( $price, $product ) {
		if ( ! self::is_active() || ! self::is_product_included( $product ) ) {
			return $price;
		}

		$sale = $this->calculate_sale_price( $product );
		return null !== $sale ? $sale : $price;
	}

	/**
	 * Hook: woocommerce_product_get_sale_price / woocommerce_product_variation_get_sale_price
	 * (giúp WooCommerce tự nhận biết sản phẩm "đang giảm giá" và hiện gạch giá cũ + nhãn "Sale!")
	 */
	public function filter_sale_price( $price, $product ) {
		if ( ! self::is_active() || ! self::is_product_included( $product ) ) {
			return $price;
		}

		$sale = $this->calculate_sale_price( $product );
		return null !== $sale ? $sale : $price;
	}
}
