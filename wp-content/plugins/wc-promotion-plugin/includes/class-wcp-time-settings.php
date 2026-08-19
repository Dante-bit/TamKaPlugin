<?php
/**
 * FILE PHỤ #1: Xử lý thiết lập THỜI GIAN khuyến mãi.
 * - Bật/tắt chương trình khuyến mãi
 * - Ngày giờ bắt đầu / kết thúc
 *
 * Toàn bộ logic liên quan tới "thời gian" nằm gọn trong file này để
 * sau này cần sửa (ví dụ đổi timezone, thêm nhiều đợt khuyến mãi...) chỉ
 * cần mở đúng file này.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WCP_Time_Settings {

	const NONCE_ACTION = 'wcp_save_time_settings';

	public function __construct() {
		add_action( 'admin_init', array( $this, 'maybe_save' ) );
	}

	/**
	 * Lưu dữ liệu khi form được submit.
	 */
	public function maybe_save() {
		if ( ! isset( $_POST['wcp_time_nonce'] ) ) {
			return;
		}
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wcp_time_nonce'] ) ), self::NONCE_ACTION ) ) {
			return;
		}
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		$enabled    = isset( $_POST['wcp_promo_enabled'] ) ? 'yes' : 'no';
		$start_date = isset( $_POST['wcp_promo_start_date'] ) ? sanitize_text_field( wp_unslash( $_POST['wcp_promo_start_date'] ) ) : '';
		$start_time = isset( $_POST['wcp_promo_start_time'] ) ? sanitize_text_field( wp_unslash( $_POST['wcp_promo_start_time'] ) ) : '00:00';
		$end_date   = isset( $_POST['wcp_promo_end_date'] ) ? sanitize_text_field( wp_unslash( $_POST['wcp_promo_end_date'] ) ) : '';
		$end_time   = isset( $_POST['wcp_promo_end_time'] ) ? sanitize_text_field( wp_unslash( $_POST['wcp_promo_end_time'] ) ) : '23:59';

		$start = $start_date ? $start_date . ' ' . $start_time . ':00' : '';
		$end   = $end_date ? $end_date . ' ' . $end_time . ':59' : '';

		// Cảnh báo nhẹ nếu kết thúc trước bắt đầu — vẫn lưu nhưng thông báo cho admin biết.
		if ( $start && $end && strtotime( $end ) < strtotime( $start ) ) {
			add_action( 'admin_notices', array( $this, 'invalid_range_notice' ) );
		}

		update_option( 'wcp_promo_enabled', $enabled );
		update_option( 'wcp_promo_start', $start );
		update_option( 'wcp_promo_end', $end );

		// Xoá cache giá để giá mới áp dụng ngay lập tức.
		if ( function_exists( 'wc_delete_product_transients' ) ) {
			wc_delete_product_transients();
		}

		add_action( 'admin_notices', array( $this, 'saved_notice' ) );
	}

	public function saved_notice() {
		echo '<div class="notice notice-success is-dismissible"><p>' .
			esc_html__( '✅ Đã lưu thiết lập khuyến mãi.', 'wc-promotion' ) .
			'</p></div>';
	}

	public function invalid_range_notice() {
		echo '<div class="notice notice-warning is-dismissible"><p>' .
			esc_html__( '⚠️ Thời gian kết thúc đang nhỏ hơn thời gian bắt đầu, vui lòng kiểm tra lại.', 'wc-promotion' ) .
			'</p></div>';
	}

	/**
	 * Trả về 4 stat-card hiển thị ở đầu trang gộp chung: Trạng thái, Đếm ngược,
	 * Số sản phẩm áp dụng, Mức giảm. Được gọi từ class-wcp-admin-menu.php.
	 */
	public function render_stat_cards() {
		$enabled   = get_option( 'wcp_promo_enabled', 'no' );
		$start     = get_option( 'wcp_promo_start', '' );
		$end       = get_option( 'wcp_promo_end', '' );
		$is_active = class_exists( 'WCP_Promotion_Engine' ) && WCP_Promotion_Engine::is_active();
		$percent   = floatval( get_option( 'wcp_promo_percent', 0 ) );
		$count     = class_exists( 'WCP_Promotion_Engine' ) ? WCP_Promotion_Engine::count_included_products() : 0;

		$status_class = 'wcp-stat-card--off';
		$status_text  = __( 'Chưa bật', 'wc-promotion' );
		if ( 'yes' === $enabled ) {
			$status_class = $is_active ? 'wcp-stat-card--active' : 'wcp-stat-card';
			$status_text  = $is_active ? __( 'Đang diễn ra', 'wc-promotion' ) : __( 'Đã bật (chưa tới/hết hạn)', 'wc-promotion' );
		}

		$end_ms   = $end ? strtotime( $end ) * 1000 : 0;
		$start_ms = $start ? strtotime( $start ) * 1000 : 0;
		?>
		<div class="wcp-stats">
			<div class="wcp-stat-card <?php echo esc_attr( $status_class ); ?>">
				<div class="wcp-stat-card__label">🟢 <?php esc_html_e( 'Trạng thái', 'wc-promotion' ); ?></div>
				<div class="wcp-stat-card__value <?php echo $is_active ? 'wcp-stat-card__value--green' : ''; ?>">
					<?php echo esc_html( $status_text ); ?>
				</div>
			</div>

			<div class="wcp-stat-card">
				<div class="wcp-stat-card__label">⏳ <?php esc_html_e( 'Thời gian còn lại', 'wc-promotion' ); ?></div>
				<div class="wcp-countdown" id="wcp-countdown" data-start="<?php echo esc_attr( $start_ms ); ?>" data-end="<?php echo esc_attr( $end_ms ); ?>">
					<?php esc_html_e( '—', 'wc-promotion' ); ?>
				</div>
			</div>

			<div class="wcp-stat-card">
				<div class="wcp-stat-card__label">🛍️ <?php esc_html_e( 'Sản phẩm áp dụng', 'wc-promotion' ); ?></div>
				<div class="wcp-stat-card__value"><?php echo esc_html( number_format_i18n( $count ) ); ?></div>
				<div class="wcp-stat-card__sub"><?php esc_html_e( 'sản phẩm nằm trong phạm vi', 'wc-promotion' ); ?></div>
			</div>

			<div class="wcp-stat-card">
				<div class="wcp-stat-card__label">🔥 <?php esc_html_e( 'Mức giảm hiện tại', 'wc-promotion' ); ?></div>
				<div class="wcp-stat-card__value wcp-stat-card__value--red">-<?php echo esc_html( $percent ); ?>%</div>
			</div>
		</div>
		<?php
	}

	/**
	 * In ra các field thiết lập thời gian với layout "field row", kèm nút
	 * preset nhanh (24h / cuối tuần / 7 ngày / 30 ngày).
	 */
	public function render_fields() {
		$enabled = get_option( 'wcp_promo_enabled', 'no' );
		$start   = get_option( 'wcp_promo_start', '' );
		$end     = get_option( 'wcp_promo_end', '' );

		list( $start_date, $start_time ) = $this->split_datetime( $start, '00:00' );
		list( $end_date, $end_time )     = $this->split_datetime( $end, '23:59' );
		?>
		<div class="wcp-field-row">
			<div class="wcp-field-row__label"><?php esc_html_e( 'Kích hoạt', 'wc-promotion' ); ?></div>
			<div>
				<label class="wcp-toggle">
					<input type="checkbox" name="wcp_promo_enabled" value="1" <?php checked( $enabled, 'yes' ); ?> />
					<span><?php esc_html_e( 'Bật chương trình khuyến mãi', 'wc-promotion' ); ?></span>
				</label>
			</div>
		</div>

		<div class="wcp-field-row">
			<div class="wcp-field-row__label"><?php esc_html_e( 'Thời gian áp dụng', 'wc-promotion' ); ?></div>
			<div>
				<div class="wcp-datetime-group">
					<span><?php esc_html_e( 'Từ', 'wc-promotion' ); ?></span>
					<input type="date" name="wcp_promo_start_date" value="<?php echo esc_attr( $start_date ); ?>" required />
					<input type="time" name="wcp_promo_start_time" value="<?php echo esc_attr( $start_time ); ?>" />
					<span><?php esc_html_e( 'đến', 'wc-promotion' ); ?></span>
					<input type="date" name="wcp_promo_end_date" value="<?php echo esc_attr( $end_date ); ?>" required />
					<input type="time" name="wcp_promo_end_time" value="<?php echo esc_attr( $end_time ); ?>" />
				</div>

				<div class="wcp-presets">
					<button type="button" class="wcp-pill-btn" data-preset="24h">⚡ <?php esc_html_e( 'Flash sale 24 giờ', 'wc-promotion' ); ?></button>
					<button type="button" class="wcp-pill-btn" data-preset="weekend">🎉 <?php esc_html_e( 'Cuối tuần này', 'wc-promotion' ); ?></button>
					<button type="button" class="wcp-pill-btn" data-preset="7d">📅 <?php esc_html_e( '7 ngày tới', 'wc-promotion' ); ?></button>
					<button type="button" class="wcp-pill-btn" data-preset="30d">🗓️ <?php esc_html_e( '30 ngày tới', 'wc-promotion' ); ?></button>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Tách chuỗi "Y-m-d H:i:s" thành [ngày, giờ].
	 */
	private function split_datetime( $value, $default_time ) {
		if ( empty( $value ) ) {
			return array( '', $default_time );
		}
		$parts = explode( ' ', $value );
		$date  = isset( $parts[0] ) ? $parts[0] : '';
		$time  = isset( $parts[1] ) ? substr( $parts[1], 0, 5 ) : $default_time;
		return array( $date, $time );
	}
}
