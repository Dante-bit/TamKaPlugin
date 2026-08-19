<?php
/*
Plugin Name: Quản lý khách hàng Toàn diện
Description: Phiên bản thu thập đầy đủ thông tin khách hàng (Ngày sinh, Giới tính, Địa chỉ...). Form chia 2 cột chuyên nghiệp, chuẩn Responsive.
Version: 4.0
Author: Your Name
*/

if (!defined('ABSPATH')) {
    exit;
}

// ========================================================================
// 1. TẠO & CẬP NHẬT BẢNG CƠ SỞ DỮ LIỆU (Thêm các trường mới)
// ========================================================================
register_activation_hook(__FILE__, 'qlkh_create_table_v4');
add_action('plugins_loaded', 'qlkh_create_table_v4'); // Đảm bảo bảng được cập nhật khi dán code mới
function qlkh_create_table_v4() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'customers_list';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name varchar(100) NOT NULL,
        email varchar(100) NOT NULL,
        phone varchar(20) NOT NULL,
        dob date DEFAULT NULL,
        gender varchar(10) DEFAULT '',
        address text DEFAULT '',
        notes text DEFAULT '',
        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// ========================================================================
// 2. MENU VÀ GIAO DIỆN QUẢN TRỊ (ADMIN)
// ========================================================================
add_action('admin_menu', 'qlkh_add_admin_menu_v4');
function qlkh_add_admin_menu_v4() {
    add_menu_page('Quản lý khách hàng', 'Khách hàng', 'manage_options', 'quan-ly-khach-hang', 'qlkh_admin_page_handler_v4', 'dashicons-groups', 20);
}

function qlkh_admin_page_handler_v4() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'customers_list';
    $action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : '';
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $page_url = admin_url('admin.php?page=quan-ly-khach-hang');

    echo '<div class="wrap">';

    // --- Xử lý Xóa ---
    if ($action === 'delete' && $id > 0 && isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'qlkh_delete')) {
        $wpdb->delete($table_name, array('id' => $id));
        echo '<div class="notice notice-success is-dismissible"><p>Đã xóa hồ sơ khách hàng thành công.</p></div>';
        $action = '';
    }

    // --- Xử lý Cập nhật ---
    if (isset($_POST['qlkh_update_admin']) && wp_verify_nonce($_POST['qlkh_nonce_admin'], 'qlkh_update')) {
        $update_id = intval($_POST['customer_id']);
        $data = array(
            'name'    => sanitize_text_field($_POST['qlkh_name']),
            'email'   => sanitize_email($_POST['qlkh_email']),
            'phone'   => sanitize_text_field($_POST['qlkh_phone']),
            'dob'     => sanitize_text_field($_POST['qlkh_dob']),
            'gender'  => sanitize_text_field($_POST['qlkh_gender']),
            'address' => sanitize_textarea_field($_POST['qlkh_address']),
            'notes'   => sanitize_textarea_field($_POST['qlkh_notes'])
        );
        $wpdb->update($table_name, $data, array('id' => $update_id));
        echo '<div class="notice notice-success is-dismissible"><p>Cập nhật thông tin thành công.</p></div>';
        $action = '';
    }

    // --- Giao diện Sửa ---
    if ($action === 'edit' && $id > 0) {
        $customer = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));
        if ($customer) {
            ?>
            <h1 class="wp-heading-inline">Chỉnh sửa hồ sơ khách hàng</h1>
            <a href="<?php echo esc_url($page_url); ?>" class="page-title-action">Quay lại danh sách</a>
            <hr class="wp-header-end">
            <div style="background:#fff; padding:20px; border:1px solid #ccd0d4; max-width:800px; margin-top:20px; box-shadow:0 1px 1px rgba(0,0,0,.04);">
                <form method="POST" action="">
                    <?php wp_nonce_field('qlkh_update', 'qlkh_nonce_admin'); ?>
                    <input type="hidden" name="customer_id" value="<?php echo esc_attr($customer->id); ?>">
                    <table class="form-table">
                        <tr>
                            <th>Họ và Tên (*)</th>
                            <td><input type="text" name="qlkh_name" value="<?php echo esc_attr($customer->name); ?>" class="regular-text" required></td>
                        </tr>
                        <tr>
                            <th>Email (*)</th>
                            <td><input type="email" name="qlkh_email" value="<?php echo esc_attr($customer->email); ?>" class="regular-text" required></td>
                        </tr>
                        <tr>
                            <th>Số điện thoại (*)</th>
                            <td><input type="text" name="qlkh_phone" value="<?php echo esc_attr($customer->phone); ?>" class="regular-text" required></td>
                        </tr>
                        <tr>
                            <th>Ngày sinh</th>
                            <td><input type="date" name="qlkh_dob" value="<?php echo esc_attr($customer->dob); ?>" class="regular-text"></td>
                        </tr>
                        <tr>
                            <th>Giới tính</th>
                            <td>
                                <select name="qlkh_gender" class="regular-text">
                                    <option value="" <?php selected($customer->gender, ''); ?>>Chưa xác định</option>
                                    <option value="Nam" <?php selected($customer->gender, 'Nam'); ?>>Nam</option>
                                    <option value="Nữ" <?php selected($customer->gender, 'Nữ'); ?>>Nữ</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>Địa chỉ</th>
                            <td><textarea name="qlkh_address" rows="3" class="large-text"><?php echo esc_textarea($customer->address); ?></textarea></td>
                        </tr>
                        <tr>
                            <th>Nhu cầu / Ghi chú</th>
                            <td><textarea name="qlkh_notes" rows="4" class="large-text"><?php echo esc_textarea($customer->notes); ?></textarea></td>
                        </tr>
                    </table>
                    <p class="submit"><button type="submit" name="qlkh_update_admin" class="button button-primary button-large">Lưu thay đổi</button></p>
                </form>
            </div>
            <?php
        }
    } 
    // --- Giao diện Danh sách ---
    else {
        $customers = $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC");
        ?>
        <h1 class="wp-heading-inline">Danh sách hồ sơ khách hàng</h1>
        <hr class="wp-header-end">
        <table class="wp-list-table widefat fixed striped" style="margin-top: 20px;">
            <thead>
                <tr>
                    <th style="width: 50px;">ID</th>
                    <th>Khách hàng</th>
                    <th>Liên hệ</th>
                    <th>Địa chỉ</th>
                    <th style="width: 140px;">Ngày đăng ký</th>
                    <th style="width: 120px;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if($customers): foreach($customers as $c): 
                    $edit_url = wp_nonce_url(add_query_arg(['action' => 'edit', 'id' => $c->id], $page_url), 'qlkh_edit');
                    $delete_url = wp_nonce_url(add_query_arg(['action' => 'delete', 'id' => $c->id], $page_url), 'qlkh_delete');
                ?>
                <tr>
                    <td><?php echo esc_html($c->id); ?></td>
                    <td>
                        <strong><?php echo esc_html($c->name); ?></strong><br>
                        <small style="color:#666;"><?php echo esc_html($c->gender); ?> <?php echo $c->dob ? '| NS: '.date('d/m/Y', strtotime($c->dob)) : ''; ?></small>
                    </td>
                    <td>
                        📞 <?php echo esc_html($c->phone); ?><br>
                        ✉️ <a href="mailto:<?php echo esc_html($c->email); ?>"><?php echo esc_html($c->email); ?></a>
                    </td>
                    <td><?php echo esc_html($c->address); ?></td>
                    <td><?php echo esc_html(date('d/m/Y H:i', strtotime($c->created_at))); ?></td>
                    <td>
                        <a href="<?php echo esc_url($edit_url); ?>" class="button button-small">Chi tiết & Sửa</a>
                        <a href="<?php echo esc_url($delete_url); ?>" class="button button-small" style="color: #d63638; margin-top: 5px;" onclick="return confirm('Xác nhận xóa hồ sơ này?');">Xóa</a>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr><td colspan="6">Chưa có hồ sơ khách hàng nào.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        <?php
    }
    echo '</div>';
}

// ========================================================================
// 3. FRONTEND - FORM ĐĂNG KÝ GRID LAYOUT CHUYÊN NGHIỆP
// ========================================================================
add_shortcode('form_dang_ky_khach', 'qlkh_frontend_form_v4');
function qlkh_frontend_form_v4() {
    ob_start();

    $errors = [];
    $success = false;
    
    // Khởi tạo biến giữ dữ liệu
    $data = array('name' => '', 'email' => '', 'phone' => '', 'dob' => '', 'gender' => '', 'address' => '', 'notes' => '');

    if (isset($_POST['qlkh_submit_frontend']) && isset($_POST['qlkh_nonce_frontend']) && wp_verify_nonce($_POST['qlkh_nonce_frontend'], 'qlkh_submit_action')) {
        
        $data['name']    = sanitize_text_field($_POST['qlkh_name']);
        $data['email']   = sanitize_email($_POST['qlkh_email']);
        $data['phone']   = sanitize_text_field($_POST['qlkh_phone']);
        $data['dob']     = sanitize_text_field($_POST['qlkh_dob']);
        $data['gender']  = sanitize_text_field($_POST['qlkh_gender']);
        $data['address'] = sanitize_textarea_field($_POST['qlkh_address']);
        $data['notes']   = sanitize_textarea_field($_POST['qlkh_notes']);

        // Bắt lỗi dữ liệu bắt buộc
        if (empty($data['name'])) { $errors[] = "Vui lòng nhập họ và tên."; }
        if (empty($data['email']) || !is_email($data['email'])) { $errors[] = "Email không hợp lệ."; }
        if (empty($data['phone']) || !preg_match('/^[0-9]{9,11}$/', $data['phone'])) { $errors[] = "Số điện thoại phải từ 9-11 số."; }

        // Nếu hợp lệ
        if (empty($errors)) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'customers_list';
            $wpdb->insert($table_name, $data);
            $success = true;
            // Xóa trắng data
            $data = array_fill_keys(array_keys($data), '');
        }
    }
    
    ?>
    <style>
        .qlkh-wrapper {
            max-width: 750px; /* Mở rộng chiều ngang cho Form 2 cột */
            margin: 40px auto;
            padding: 40px;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.06);
            border: 1px solid #f1f1f1;
            font-family: inherit;
        }
        .qlkh-wrapper h3 {
            text-align: center; margin-top: 0; margin-bottom: 30px;
            color: #111; font-size: 26px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;
        }
        
        /* CSS Lưới 2 Cột */
        .qlkh-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .qlkh-full {
            grid-column: 1 / -1; /* Chiếm cả 2 cột */
        }
        
        .qlkh-group label {
            display: block; font-weight: 600; margin-bottom: 8px; color: #333; font-size: 14px;
        }
        .qlkh-req { color: #d63638; margin-left: 2px; }
        .qlkh-group input, .qlkh-group select, .qlkh-group textarea {
            width: 100%; padding: 14px 16px; border: 1.5px solid #e0e0e0;
            border-radius: 8px; box-sizing: border-box; font-size: 15px; background-color: #fafafa;
            transition: all 0.25s ease;
        }
        .qlkh-group input:focus, .qlkh-group select:focus, .qlkh-group textarea:focus {
            background-color: #fff; border-color: #000; outline: none; box-shadow: 0 0 0 3px rgba(0,0,0,0.05);
        }
        .qlkh-group textarea { resize: vertical; min-height: 100px; }
        
        .qlkh-btn {
            width: 100%; padding: 18px; background-color: #000; color: #fff;
            border: none; border-radius: 8px; font-size: 18px; font-weight: 700;
            cursor: pointer; transition: all 0.2s ease; text-transform: uppercase; margin-top: 15px;
        }
        .qlkh-btn:hover { background-color: #333; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .qlkh-btn:active { transform: translateY(0); }
        
        .qlkh-msg { padding: 16px; border-radius: 8px; font-size: 15px; margin-bottom: 25px; line-height: 1.5; }
        .qlkh-msg-success { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; text-align: center; font-weight: 600; }
        .qlkh-msg-error { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
        .qlkh-msg-error ul { margin: 10px 0 0 20px; padding: 0; }
        .qlkh-msg-error li { margin-bottom: 5px; }

        /* Reponsive cho Mobile */
        @media (max-width: 650px) {
            .qlkh-grid { grid-template-columns: 1fr; }
            .qlkh-wrapper { padding: 25px 20px; margin: 20px auto; }
        }
    </style>

    <div class="qlkh-wrapper">
        <h3>Đăng ký hồ sơ khách hàng</h3>

        <?php if ($success): ?>
            <div class="qlkh-msg qlkh-msg-success">🎉 Cảm ơn bạn! Thông tin hồ sơ đã được gửi thành công.</div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="qlkh-msg qlkh-msg-error">
                <strong>⚠️ Vui lòng kiểm tra lại:</strong>
                <ul><?php foreach ($errors as $err) { echo "<li>" . esc_html($err) . "</li>"; } ?></ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <?php wp_nonce_field('qlkh_submit_action', 'qlkh_nonce_frontend'); ?>
            
            <div class="qlkh-grid">
                <!-- Cột 1 & 2: Thông tin cơ bản -->
                <div class="qlkh-group">
                    <label for="qlkh_name">Họ và tên <span class="qlkh-req">*</span></label>
                    <input type="text" id="qlkh_name" name="qlkh_name" value="<?php echo esc_attr($data['name']); ?>" placeholder="Vd: Nguyễn Văn A" required>
                </div>
                
                <div class="qlkh-group">
                    <label for="qlkh_email">Email liên hệ <span class="qlkh-req">*</span></label>
                    <input type="email" id="qlkh_email" name="qlkh_email" value="<?php echo esc_attr($data['email']); ?>" placeholder="Vd: email@domain.com" required>
                </div>
                
                <div class="qlkh-group">
                    <label for="qlkh_phone">Số điện thoại <span class="qlkh-req">*</span></label>
                    <input type="tel" id="qlkh_phone" name="qlkh_phone" value="<?php echo esc_attr($data['phone']); ?>" placeholder="Vd: 0912345678" required pattern="[0-9]{9,11}">
                </div>

                <div class="qlkh-group">
                    <label for="qlkh_dob">Ngày sinh</label>
                    <input type="date" id="qlkh_dob" name="qlkh_dob" value="<?php echo esc_attr($data['dob']); ?>">
                </div>

                <!-- Cột dài: Giới tính & Địa chỉ -->
                <div class="qlkh-group">
                    <label for="qlkh_gender">Giới tính</label>
                    <select id="qlkh_gender" name="qlkh_gender">
                        <option value="">-- Chọn giới tính --</option>
                        <option value="Nam" <?php selected($data['gender'], 'Nam'); ?>>Nam</option>
                        <option value="Nữ" <?php selected($data['gender'], 'Nữ'); ?>>Nữ</option>
                    </select>
                </div>

                <div class="qlkh-group">
                    <label for="qlkh_address">Địa chỉ hiện tại</label>
                    <input type="text" id="qlkh_address" name="qlkh_address" value="<?php echo esc_attr($data['address']); ?>" placeholder="Số nhà, đường, quận, thành phố...">
                </div>

                <!-- Dòng ghi chú chiếm trọn 2 cột -->
                <div class="qlkh-group qlkh-full">
                    <label for="qlkh_notes">Nhu cầu tư vấn / Ghi chú thêm</label>
                    <textarea id="qlkh_notes" name="qlkh_notes" placeholder="Bạn cần chúng tôi hỗ trợ thêm thông tin gì không?"><?php echo esc_textarea($data['notes']); ?></textarea>
                </div>

                <div class="qlkh-group qlkh-full" style="margin-bottom: 0;">
                    <button type="submit" name="qlkh_submit_frontend" class="qlkh-btn">Gửi thông tin đăng ký</button>
                </div>
            </div>
        </form>
    </div>
    <?php
    return ob_get_clean();
}