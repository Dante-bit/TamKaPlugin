<?php
/**
 * Plugin Name: WC Flash Promotion (Khuyến mãi theo thời gian)
 * Plugin URI:  https://example.com
 * Description: Tạo chương trình khuyến mãi WooCommerce có thiết lập thời gian bắt đầu/kết thúc và % giảm giá, quản lý ngay trong Dashboard WordPress.
 * Version:     1.0.0
 * Author:      Your Store
 * Text Domain: wc-promotion
 * Requires Plugins: woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Không cho truy cập trực tiếp.
}

// ==== Hằng số dùng chung trong plugin ====
define( 'WCP_VERSION', '1.0.0' );
define( 'WCP_PLUGIN_FILE', __FILE__ );
define( 'WCP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'WCP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Kiểm tra WooCommerce đã được kích hoạt chưa.
 */
function wcp_is_woocommerce_active() {
	return in_array(
		'woocommerce/woocommerce.php',
		apply_filters( 'active_plugins', get_option( 'active_plugins' ) ),
		true
	) || ( is_multisite() && array_key_exists( 'woocommerce/woocommerce.php', get_site_option( 'active_sitewide_plugins', array() ) ) );
}

/**
 * Nạp các file của plugin.
 * File "menu chính" (class-wcp-admin-menu.php) sẽ include 2 file phụ:
 *  - class-wcp-time-settings.php     (thiết lập thời gian)
 *  - class-wcp-discount-settings.php (thiết lập % khuyến mãi)
 */
function wcp_load_plugin_files() {
	require_once WCP_PLUGIN_DIR . 'includes/class-wcp-time-settings.php';
	require_once WCP_PLUGIN_DIR . 'includes/class-wcp-discount-settings.php';
	require_once WCP_PLUGIN_DIR . 'includes/class-wcp-admin-menu.php';
	require_once WCP_PLUGIN_DIR . 'includes/class-wcp-promotion-engine.php';
}

/**
 * Khởi chạy plugin.
 */
function wcp_init_plugin() {
	if ( ! wcp_is_woocommerce_active() ) {
		add_action( 'admin_notices', 'wcp_woocommerce_missing_notice' );
		return;
	}

	wcp_load_plugin_files();

	// Khởi tạo menu dashboard (file chính, chia ra 2 file phụ bên trong).
	new WCP_Admin_Menu();

	// Khởi tạo bộ máy áp dụng khuyến mãi lên giá sản phẩm WooCommerce.
	new WCP_Promotion_Engine();
}
add_action( 'plugins_loaded', 'wcp_init_plugin' );

/**
 * Thông báo khi chưa cài WooCommerce.
 */
function wcp_woocommerce_missing_notice() {
	echo '<div class="notice notice-error"><p>';
	esc_html_e( 'Plugin "WC Flash Promotion" cần WooCommerce được cài đặt và kích hoạt để hoạt động.', 'wc-promotion' );
	echo '</p></div>';
}

/**
 * Khi kích hoạt plugin: thiết lập giá trị mặc định cho các option nếu chưa có.
 */
function wcp_activate_plugin() {
	add_option( 'wcp_promo_enabled', 'no' );
	add_option( 'wcp_promo_start', '' );
	add_option( 'wcp_promo_end', '' );
	add_option( 'wcp_promo_percent', 0 );
	add_option( 'wcp_promo_scope', 'all' );
	add_option( 'wcp_promo_categories', array() );
	add_option( 'wcp_promo_products', array() );
}
register_activation_hook( __FILE__, 'wcp_activate_plugin' );

/**
 * Khi gỡ kích hoạt: xoá cache giá tạm của WooCommerce cho chắc ăn.
 */
function wcp_deactivate_plugin() {
	if ( function_exists( 'wc_delete_product_transients' ) ) {
		wc_delete_product_transients();
	}
}
register_deactivation_hook( __FILE__, 'wcp_deactivate_plugin' );
