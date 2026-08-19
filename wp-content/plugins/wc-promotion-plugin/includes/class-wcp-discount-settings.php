<?php
/**
 * FILE PHỤ #2: Xử lý thiết lập MỨC KHUYẾN MÃI (%).
 * - % giảm giá
 * - Phạm vi áp dụng: Tất cả sản phẩm / Theo danh mục / Theo sản phẩm cụ thể
 * - Bảng chọn sản phẩm trực quan (ảnh, tên, SKU, giá, tìm kiếm, lọc, phân trang)
 *
 * Toàn bộ logic liên quan tới "% và phạm vi áp dụng" nằm gọn trong file
 * này, tách biệt với phần thời gian ở file class-wcp-time-settings.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WCP_Discount_Settings {

	const NONCE_ACTION = 'wcp_save_discount_settings';

	public function __construct() {
		add_action( 'admin_init', array( $this, 'maybe_save' ) );
	}

	public function maybe_save() {
		if ( ! isset( $_POST['wcp_discount_nonce'] ) ) {
			return;
		}
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wcp_discount_nonce'] ) ), self::NONCE_ACTION ) ) {
			return;
		}
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		$percent = isset( $_POST['wcp_promo_percent'] ) ? floatval( $_POST['wcp_promo_percent'] ) : 0;
		$percent = max( 0, min( 100, $percent ) ); // giới hạn 0-100.

		$scope = isset( $_POST['wcp_promo_scope'] ) ? sanitize_text_field( wp_unslash( $_POST['wcp_promo_scope'] ) ) : 'all';
		if ( ! in_array( $scope, array( 'all', 'category', 'product' ), true ) ) {
			$scope = 'all';
		}

		$categories = isset( $_POST['wcp_promo_categories'] ) ? array_map( 'absint', (array) $_POST['wcp_promo_categories'] ) : array();
		$products   = isset( $_POST['wcp_promo_products'] ) ? array_map( 'absint', (array) $_POST['wcp_promo_products'] ) : array();

		update_option( 'wcp_promo_percent', $percent );
		update_option( 'wcp_promo_scope', $scope );
		update_option( 'wcp_promo_categories', $categories );
		update_option( 'wcp_promo_products', $products );

		if ( function_exists( 'wc_delete_product_transients' ) ) {
			wc_delete_product_transients();
		}

		add_action( 'admin_notices', array( $this, 'saved_notice' ) );
	}

	public function saved_notice() {
		echo '<div class="notice notice-success is-dismissible"><p>' .
			esc_html__( '✅ Đã lưu mức khuyến mãi.', 'wc-promotion' ) .
			'</p></div>';
	}

	/**
	 * In ra field % giảm giá + phạm vi áp dụng (scope cards + chip danh mục
	 * + bảng chọn sản phẩm). Không kèm <form>/nonce/nút lưu vì trang gộp
	 * chung sẽ tự bọc các phần này — xem class-wcp-admin-menu.php.
	 */
	public function render_fields() {
		$percent    = get_option( 'wcp_promo_percent', 0 );
		$scope      = get_option( 'wcp_promo_scope', 'all' );
		$categories = (array) get_option( 'wcp_promo_categories', array() );
		$products   = (array) get_option( 'wcp_promo_products', array() );

		$all_categories = get_terms(
			array(
				'taxonomy'   => 'product_cat',
				'hide_empty' => false,
			)
		);
		?>
		<div class="wcp-field-row">
			<div class="wcp-field-row__label"><?php esc_html_e( '% giảm giá', 'wc-promotion' ); ?></div>
			<div>
				<div class="wcp-percent-input">
					<input type="number" id="wcp_promo_percent" name="wcp_promo_percent"
						value="<?php echo esc_attr( $percent ); ?>" min="0" max="100" step="0.1" />
					<span class="wcp-percent-sign">%</span>
				</div>
				<p class="description"><?php esc_html_e( 'Áp dụng đồng loạt lên giá gốc của sản phẩm nằm trong phạm vi bên dưới.', 'wc-promotion' ); ?></p>
			</div>
		</div>

		<div class="wcp-field-row">
			<div class="wcp-field-row__label"><?php esc_html_e( 'Áp dụng cho', 'wc-promotion' ); ?></div>
			<div>
				<div class="wcp-scope-cards">
					<label class="wcp-scope-card">
						<input type="radio" name="wcp_promo_scope" value="all" <?php checked( $scope, 'all' ); ?> />
						<span class="wcp-scope-card__icon">🌐</span>
						<span class="wcp-scope-card__title"><?php esc_html_e( 'Tất cả sản phẩm', 'wc-promotion' ); ?></span>
						<span class="wcp-scope-card__desc"><?php esc_html_e( 'Áp dụng cho toàn bộ shop', 'wc-promotion' ); ?></span>
					</label>
					<label class="wcp-scope-card">
						<input type="radio" name="wcp_promo_scope" value="category" <?php checked( $scope, 'category' ); ?> />
						<span class="wcp-scope-card__icon">🗂️</span>
						<span class="wcp-scope-card__title"><?php esc_html_e( 'Theo danh mục', 'wc-promotion' ); ?></span>
						<span class="wcp-scope-card__desc"><?php esc_html_e( 'Chọn 1 hoặc nhiều danh mục', 'wc-promotion' ); ?></span>
					</label>
					<label class="wcp-scope-card">
						<input type="radio" name="wcp_promo_scope" value="product" <?php checked( $scope, 'product' ); ?> />
						<span class="wcp-scope-card__icon">🎯</span>
						<span class="wcp-scope-card__title"><?php esc_html_e( 'Sản phẩm cụ thể', 'wc-promotion' ); ?></span>
						<span class="wcp-scope-card__desc"><?php esc_html_e( 'Chọn tay từng sản phẩm', 'wc-promotion' ); ?></span>
					</label>
				</div>

				<div class="wcp-scope-panel" data-scope="category" style="<?php echo 'category' === $scope ? 'margin-top:16px;' : 'display:none;margin-top:16px;'; ?>">
					<div class="wcp-chip-grid">
						<?php if ( ! is_wp_error( $all_categories ) && $all_categories ) : ?>
							<?php foreach ( $all_categories as $cat ) : ?>
								<label class="wcp-chip">
									<input type="checkbox" name="wcp_promo_categories[]" value="<?php echo esc_attr( $cat->term_id ); ?>" <?php checked( in_array( $cat->term_id, $categories, true ) ); ?> />
									<span><?php echo esc_html( $cat->name ); ?> (<?php echo esc_html( $cat->count ); ?>)</span>
								</label>
							<?php endforeach; ?>
						<?php else : ?>
							<p class="description"><?php esc_html_e( 'Chưa có danh mục sản phẩm nào.', 'wc-promotion' ); ?></p>
						<?php endif; ?>
					</div>
				</div>

				<div class="wcp-scope-panel" data-scope="product" style="<?php echo 'product' === $scope ? 'margin-top:16px;' : 'display:none;margin-top:16px;'; ?>">
					<?php $this->render_product_picker( $products ); ?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Bảng chọn sản phẩm trực quan: ảnh đại diện, tên, SKU, danh mục, giá gốc
	 * kèm preview giá sau giảm (JS tính theo % đang nhập), ô tìm kiếm, lọc
	 * theo danh mục và phân trang phía client (assets/admin.js).
	 */
	private function render_product_picker( $selected_ids ) {
		$products = wc_get_products(
			array(
				'limit'   => 500,
				'status'  => 'publish',
				'orderby' => 'title',
				'order'   => 'ASC',
				'return'  => 'objects',
			)
		);

		$all_categories = get_terms(
			array(
				'taxonomy'   => 'product_cat',
				'hide_empty' => true,
			)
		);
		?>
		<div id="wcp-product-picker">
			<div class="wcp-picker-toolbar">
				<input type="search" class="wcp-picker-search" placeholder="<?php esc_attr_e( '🔎 Tìm theo tên hoặc SKU…', 'wc-promotion' ); ?>" />
				<select class="wcp-picker-cat-filter">
					<option value=""><?php esc_html_e( 'Tất cả danh mục', 'wc-promotion' ); ?></option>
					<?php if ( ! is_wp_error( $all_categories ) ) : ?>
						<?php foreach ( $all_categories as $cat ) : ?>
							<option value="<?php echo esc_attr( $cat->term_id ); ?>"><?php echo esc_html( $cat->name ); ?></option>
						<?php endforeach; ?>
					<?php endif; ?>
				</select>
				<span class="wcp-picker-count"></span>
			</div>

			<div class="wcp-product-table-wrap">
				<table class="wcp-product-table">
					<thead>
						<tr>
							<th style="width:34px;"><input type="checkbox" class="wcp-picker-check-all" /></th>
							<th style="width:44px;"></th>
							<th><?php esc_html_e( 'Sản phẩm', 'wc-promotion' ); ?></th>
							<th><?php esc_html_e( 'Danh mục', 'wc-promotion' ); ?></th>
							<th><?php esc_html_e( 'Giá', 'wc-promotion' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php if ( empty( $products ) ) : ?>
							<tr class="wcp-empty-row"><td colspan="5"><?php esc_html_e( 'Chưa có sản phẩm nào.', 'wc-promotion' ); ?></td></tr>
						<?php else : ?>
							<?php foreach ( $products as $product ) : ?>
								<?php
								$cat_ids   = wc_get_product_term_ids( $product->get_id(), 'product_cat' );
								$cat_names = array();
								foreach ( $cat_ids as $cat_id ) {
									$term = get_term( $cat_id, 'product_cat' );
									if ( $term && ! is_wp_error( $term ) ) {
										$cat_names[] = $term->name;
									}
								}
								$regular = $product->get_regular_price();
								$image   = wp_get_attachment_image_url( $product->get_image_id(), 'thumbnail' );
								?>
								<tr
									data-name="<?php echo esc_attr( function_exists( 'mb_strtolower' ) ? mb_strtolower( $product->get_name() ) : strtolower( $product->get_name() ) ); ?>"
									data-sku="<?php echo esc_attr( strtolower( $product->get_sku() ) ); ?>"
									data-cat="<?php echo esc_attr( implode( ',', $cat_ids ) ); ?>"
									data-regular-price="<?php echo esc_attr( $regular ); ?>"
								>
									<td>
										<input type="checkbox" name="wcp_promo_products[]" value="<?php echo esc_attr( $product->get_id() ); ?>" <?php checked( in_array( $product->get_id(), $selected_ids, true ) ); ?> />
									</td>
									<td>
										<?php if ( $image ) : ?>
											<img class="wcp-product-thumb" src="<?php echo esc_url( $image ); ?>" alt="" />
										<?php else : ?>
											<span class="wcp-product-thumb" aria-hidden="true"></span>
										<?php endif; ?>
									</td>
									<td>
										<span class="wcp-product-name"><?php echo esc_html( $product->get_name() ); ?></span><br />
										<span class="wcp-product-sku"><?php echo esc_html( $product->get_sku() ? 'SKU: ' . $product->get_sku() : '' ); ?></span>
									</td>
									<td><?php echo esc_html( $cat_names ? implode( ', ', $cat_names ) : '—' ); ?></td>
									<td class="wcp-product-price">
										<?php echo $regular ? wp_kses_post( wc_price( $regular ) ) : '—'; ?>
										<span class="wcp-new-price" style="display:none;"></span>
									</td>
								</tr>
							<?php endforeach; ?>
						<?php endif; ?>
					</tbody>
				</table>
				<div class="wcp-pagination"></div>
			</div>
		</div>
		<?php
	}
}
