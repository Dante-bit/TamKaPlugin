<?php
/**
 * File MENU CHÍNH.
 * Nhiệm vụ duy nhất của file này là đăng ký menu trên Dashboard, dựng khung
 * giao diện (banner + stat card + panel) và gọi render_fields() từ 2 file phụ:
 *   - WCP_Time_Settings::render_fields()      -> class-wcp-time-settings.php
 *   - WCP_Discount_Settings::render_fields()  -> class-wcp-discount-settings.php
 *
 * Nhờ tách riêng như vậy, sau này muốn sửa phần "thời gian" hay phần
 * "% khuyến mãi" chỉ cần mở đúng 1 file nhỏ, không đụng vào các phần khác.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WCP_Admin_Menu {

	/** @var WCP_Time_Settings */
	private $time_settings;

	/** @var WCP_Discount_Settings */
	private $discount_settings;

	public function __construct() {
		$this->time_settings     = new WCP_Time_Settings();
		$this->discount_settings = new WCP_Discount_Settings();

		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	/**
	 * Đăng ký DUY NHẤT 1 trang menu (không có trang phụ / không cần chuyển trang).
	 */
	public function register_menu() {
		$capability = 'manage_woocommerce';

		add_menu_page(
			__( 'Khuyến mãi WC', 'wc-promotion' ),
			__( 'Khuyến mãi WC', 'wc-promotion' ),
			$capability,
			'wcp-promotion',
			array( $this, 'render_settings_page' ),
			'dashicons-tag',
			56
		);
	}

	/**
	 * Trang thiết lập gộp chung: banner + 4 stat card + 2 panel (thời gian,
	 * % khuyến mãi) trong cùng 1 form. Mỗi bên vẫn tự lo phần lưu dữ liệu
	 * của mình (hook admin_init trong chính file phụ của nó), nên giữ
	 * nguyên 2 file phụ tách biệt, chỉ gộp giao diện.
	 */
	public function render_settings_page() {
		?>
		<div class="wrap wcp-wrap">

			<div class="wcp-hero">
				<span class="wcp-hero__badge">✦ Ultra VIP</span>
				<h1>👑 <?php esc_html_e( 'Khuyến Mãi WooCommerce', 'wc-promotion' ); ?></h1>
				<p><?php esc_html_e( 'Thiết lập chương trình khuyến mãi theo thời gian và % giảm giá, áp dụng tự động cho toàn shop, theo danh mục hoặc chọn tay từng sản phẩm.', 'wc-promotion' ); ?></p>
			</div>

			<?php $this->time_settings->render_stat_cards(); ?>

			<form method="post">
				<?php
				wp_nonce_field( WCP_Time_Settings::NONCE_ACTION, 'wcp_time_nonce' );
				wp_nonce_field( WCP_Discount_Settings::NONCE_ACTION, 'wcp_discount_nonce' );
				?>

				<div class="wcp-panel">
					<div class="wcp-panel__header">
						<span class="wcp-panel__icon">⏰</span>
						<div>
							<h2 class="wcp-panel__title"><?php esc_html_e( 'Thời gian khuyến mãi', 'wc-promotion' ); ?></h2>
							<p class="wcp-panel__desc"><?php esc_html_e( 'Bật/tắt và chọn khoảng thời gian chương trình có hiệu lực.', 'wc-promotion' ); ?></p>
						</div>
					</div>
					<?php $this->time_settings->render_fields(); ?>
				</div>

				<div class="wcp-panel">
					<div class="wcp-panel__header">
						<span class="wcp-panel__icon">🏷️</span>
						<div>
							<h2 class="wcp-panel__title"><?php esc_html_e( 'Mức khuyến mãi & phạm vi áp dụng', 'wc-promotion' ); ?></h2>
							<p class="wcp-panel__desc"><?php esc_html_e( 'Chọn % giảm giá và phạm vi: toàn shop, theo danh mục, hoặc chọn tay từng sản phẩm.', 'wc-promotion' ); ?></p>
						</div>
					</div>
					<?php $this->discount_settings->render_fields(); ?>
				</div>

				<div class="wcp-submit-bar">
					<?php submit_button( '💾 ' . __( 'Lưu thiết lập khuyến mãi', 'wc-promotion' ), 'primary', 'submit', false ); ?>
				</div>
			</form>
		</div>
		<?php
	}

	/**
	 * Nạp CSS/JS riêng cho các trang của plugin.
	 */
	public function enqueue_assets( $hook ) {
		if ( strpos( $hook, 'wcp-promotion' ) === false ) {
			return;
		}
		wp_enqueue_style(
			'wcp-admin-style',
			WCP_PLUGIN_URL . 'assets/admin.css',
			array(),
			WCP_VERSION
		);
		wp_enqueue_script(
			'wcp-admin-script',
			WCP_PLUGIN_URL . 'assets/admin.js',
			array(),
			WCP_VERSION,
			true
		);
	}
}
