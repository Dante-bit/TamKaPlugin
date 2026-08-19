<?php
/**
 * Plugin Name: Site Color Changer
 * Plugin URI:  https://example.com
 * Description: Cho phép quản trị viên đổi màu sắc cho toàn bộ website qua trang Dashboard riêng, có xem trước trực tiếp (live preview) responsive theo Desktop/Tablet/Mobile và các bảng màu mẫu dựng sẵn.
 * Version:     2.0.0
 * Author:      Your Name
 * License:     GPL v2 or later
 * Text Domain: site-color-changer
 */

// Chặn truy cập trực tiếp file
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SCC_Site_Color_Changer {

    const OPTION_KEY   = 'scc_color_settings';
    const NONCE_ACTION = 'scc_save_colors';

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'register_admin_menu' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
        add_action( 'wp_head', array( $this, 'output_custom_css' ) );
        add_action( 'admin_post_scc_save_colors', array( $this, 'handle_save' ) );
        add_action( 'admin_post_scc_reset_colors', array( $this, 'handle_reset' ) );
    }

    /* ==========================================================
     *  DỮ LIỆU: TRƯỜNG MÀU, MAP CSS VAR, NHÓM HIỂN THỊ, PRESET
     * ========================================================== */

    /** Danh sách các trường màu + nhãn + giá trị mặc định */
    public function get_fields() {
        return array(
            'primary_color'    => array( 'label' => 'Màu chủ đạo (Primary)', 'default' => '#2271b1' ),
            'secondary_color'  => array( 'label' => 'Màu phụ (Secondary)',   'default' => '#135e96' ),
            'background_color' => array( 'label' => 'Màu nền trang',        'default' => '#ffffff' ),
            'text_color'       => array( 'label' => 'Màu chữ',              'default' => '#1e1e1e' ),
            'link_color'       => array( 'label' => 'Màu liên kết (Link)',  'default' => '#2271b1' ),
            'header_bg'        => array( 'label' => 'Màu nền Header',       'default' => '#ffffff' ),
            'footer_bg'        => array( 'label' => 'Màu nền Footer',       'default' => '#1e1e1e' ),
            'footer_text'      => array( 'label' => 'Màu chữ Footer',       'default' => '#ffffff' ),
            'button_bg'        => array( 'label' => 'Màu nền nút (Button)', 'default' => '#2271b1' ),
            'button_text'      => array( 'label' => 'Màu chữ nút (Button)', 'default' => '#ffffff' ),
        );
    }

    /** Nhóm các trường theo card để bố cục dạng cột cho dễ nhìn */
    public function get_field_groups() {
        return array(
            'general' => array(
                'title'  => 'Màu chữ & nền chính',
                'fields' => array( 'primary_color', 'secondary_color', 'background_color', 'text_color', 'link_color' ),
            ),
            'header_footer' => array(
                'title'  => 'Header & Footer',
                'fields' => array( 'header_bg', 'footer_bg', 'footer_text' ),
            ),
            'button' => array(
                'title'  => 'Nút bấm (Button)',
                'fields' => array( 'button_bg', 'button_text' ),
            ),
        );
    }

    /** Map field-key => tên biến CSS dùng ở cả admin preview lẫn frontend */
    public function get_css_var_map() {
        return array(
            'primary_color'    => 'primary',
            'secondary_color'  => 'secondary',
            'background_color' => 'background',
            'text_color'       => 'text',
            'link_color'       => 'link',
            'header_bg'        => 'header-bg',
            'footer_bg'        => 'footer-bg',
            'footer_text'      => 'footer-text',
            'button_bg'        => 'button-bg',
            'button_text'      => 'button-text',
        );
    }

    /** Các bảng màu mẫu dựng sẵn để chọn nhanh */
    public function get_presets() {
        return array(
            'blue_pro' => array(
                'name'   => 'Xanh dương chuyên nghiệp',
                'colors' => array(
                    'primary_color' => '#2271b1', 'secondary_color' => '#135e96', 'background_color' => '#ffffff',
                    'text_color' => '#1e1e1e', 'link_color' => '#2271b1', 'header_bg' => '#ffffff',
                    'footer_bg' => '#1e293b', 'footer_text' => '#ffffff', 'button_bg' => '#2271b1', 'button_text' => '#ffffff',
                ),
            ),
            'green_nature' => array(
                'name'   => 'Xanh lá thiên nhiên',
                'colors' => array(
                    'primary_color' => '#2f9e44', 'secondary_color' => '#2b8a3e', 'background_color' => '#f4fbf6',
                    'text_color' => '#1b4332', 'link_color' => '#2f9e44', 'header_bg' => '#ffffff',
                    'footer_bg' => '#1b4332', 'footer_text' => '#ffffff', 'button_bg' => '#37b24d', 'button_text' => '#ffffff',
                ),
            ),
            'orange_energy' => array(
                'name'   => 'Cam năng động',
                'colors' => array(
                    'primary_color' => '#f76707', 'secondary_color' => '#d9480f', 'background_color' => '#fff8f1',
                    'text_color' => '#402c1f', 'link_color' => '#f76707', 'header_bg' => '#ffffff',
                    'footer_bg' => '#402c1f', 'footer_text' => '#ffe8d6', 'button_bg' => '#f76707', 'button_text' => '#ffffff',
                ),
            ),
            'purple_luxury' => array(
                'name'   => 'Tím sang trọng',
                'colors' => array(
                    'primary_color' => '#7048e8', 'secondary_color' => '#5f3dc4', 'background_color' => '#f8f6ff',
                    'text_color' => '#2b2140', 'link_color' => '#7048e8', 'header_bg' => '#ffffff',
                    'footer_bg' => '#2b2140', 'footer_text' => '#e9e4ff', 'button_bg' => '#7048e8', 'button_text' => '#ffffff',
                ),
            ),
            'bw_minimal' => array(
                'name'   => 'Đen trắng tối giản',
                'colors' => array(
                    'primary_color' => '#111111', 'secondary_color' => '#444444', 'background_color' => '#ffffff',
                    'text_color' => '#111111', 'link_color' => '#111111', 'header_bg' => '#ffffff',
                    'footer_bg' => '#111111', 'footer_text' => '#ffffff', 'button_bg' => '#111111', 'button_text' => '#ffffff',
                ),
            ),
            'pink_soft' => array(
                'name'   => 'Hồng pastel nhẹ nhàng',
                'colors' => array(
                    'primary_color' => '#e64980', 'secondary_color' => '#d6336c', 'background_color' => '#fff0f6',
                    'text_color' => '#3a1f28', 'link_color' => '#e64980', 'header_bg' => '#ffffff',
                    'footer_bg' => '#3a1f28', 'footer_text' => '#ffd6e6', 'button_bg' => '#e64980', 'button_text' => '#ffffff',
                ),
            ),
            'red_bold' => array(
                'name'   => 'Đỏ mạnh mẽ',
                'colors' => array(
                    'primary_color' => '#e03131', 'secondary_color' => '#c92a2a', 'background_color' => '#fff5f5',
                    'text_color' => '#2b1414', 'link_color' => '#e03131', 'header_bg' => '#ffffff',
                    'footer_bg' => '#2b1414', 'footer_text' => '#ffffff', 'button_bg' => '#e03131', 'button_text' => '#ffffff',
                ),
            ),
            'teal_modern' => array(
                'name'   => 'Xanh ngọc hiện đại',
                'colors' => array(
                    'primary_color' => '#0c8599', 'secondary_color' => '#0b7285', 'background_color' => '#f0fbfc',
                    'text_color' => '#0b2e33', 'link_color' => '#0c8599', 'header_bg' => '#ffffff',
                    'footer_bg' => '#0b2e33', 'footer_text' => '#ffffff', 'button_bg' => '#0c8599', 'button_text' => '#ffffff',
                ),
            ),
        );
    }

    public function get_settings() {
        $saved  = get_option( self::OPTION_KEY, array() );
        $fields = $this->get_fields();
        $out    = array();
        foreach ( $fields as $key => $data ) {
            $out[ $key ] = isset( $saved[ $key ] ) ? $saved[ $key ] : $data['default'];
        }
        return $out;
    }

    /* ==========================================================
     *  MENU / SETTINGS API
     * ========================================================== */

    public function register_admin_menu() {
        add_menu_page(
            'Site Color Changer', 'Đổi màu Website', 'manage_options',
            'scc-dashboard', array( $this, 'render_dashboard_page' ), 'dashicons-admin-appearance', 61
        );
        add_submenu_page(
            'scc-dashboard', 'Dashboard', 'Dashboard', 'manage_options',
            'scc-dashboard', array( $this, 'render_dashboard_page' )
        );
        add_submenu_page(
            'scc-dashboard', 'Cài đặt màu sắc', 'Cài đặt màu sắc', 'manage_options',
            'scc-settings', array( $this, 'render_settings_page' )
        );
    }

    public function register_settings() {
        register_setting( 'scc_settings_group', self::OPTION_KEY );
    }

    public function enqueue_admin_assets( $hook ) {
        if ( strpos( $hook, 'scc-' ) === false ) {
            return;
        }
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_style( 'dashicons' );
        wp_enqueue_script( 'wp-color-picker' );
        wp_enqueue_script( 'jquery' );

        // Script "rỗng" hợp lệ để gắn JS tùy biến vào (an toàn hơn wp_add_inline_script trên handle false)
        wp_register_script( 'scc-admin-js', '', array( 'jquery', 'wp-color-picker' ), '2.0.0', true );
        wp_enqueue_script( 'scc-admin-js' );

        $var_map = $this->get_css_var_map();
        $js = "
        jQuery(document).ready(function($){
            var sccVarMap = " . wp_json_encode( $var_map ) . ";

            function sccUpdatePreview(){
                var cssVars = '';
                $('.scc-color-field').each(function(){
                    var field = $(this).data('field');
                    var varName = sccVarMap[field];
                    var val = $(this).val();
                    if (varName && val) {
                        cssVars += '--scc-' + varName + ':' + val + ';';
                    }
                });
                $('#scc-preview-frame').attr('style', cssVars);
            }

            $('.scc-color-field').wpColorPicker({
                change: function(){ setTimeout(sccUpdatePreview, 30); },
                clear:  function(){ setTimeout(sccUpdatePreview, 30); }
            });

            setTimeout(sccUpdatePreview, 200);

            // Áp dụng preset màu mẫu
            $('.scc-preset-card').on('click', function(){
                var preset = $(this).data('preset');
                $.each(preset, function(key, val){
                    var \$input = $('#' + key);
                    \$input.val(val);
                    \$input.wpColorPicker('color', val);
                });
                $('.scc-preset-card').removeClass('is-active');
                $(this).addClass('is-active');
                setTimeout(sccUpdatePreview, 80);
            });

            // Chuyển đổi kích thước xem trước theo thiết bị
            $('.scc-device-btn').on('click', function(){
                $('.scc-device-btn').removeClass('is-active');
                $(this).addClass('is-active');
                var w = $(this).data('width');
                $('#scc-preview-wrapper').css('max-width', w);
            });
        });
        ";
        wp_add_inline_script( 'scc-admin-js', $js );
    }

    /* ==========================================================
     *  TRANG DASHBOARD TỔNG QUAN
     * ========================================================== */

    public function render_dashboard_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        $settings = $this->get_settings();
        ?>
        <div class="wrap">
            <h1>Site Color Changer — Dashboard</h1>
            <p>Chào mừng bạn đến trang quản lý màu sắc website. Tại đây bạn có thể xem nhanh bảng màu hiện tại và truy cập trang cài đặt để thay đổi.</p>

            <h2 class="title">Bảng màu hiện tại</h2>
            <div style="display:flex; flex-wrap:wrap; gap:16px; margin-top:16px;">
                <?php foreach ( $this->get_fields() as $key => $data ) : ?>
                    <div style="width:160px; border:1px solid #ccd0d4; border-radius:8px; overflow:hidden; background:#fff;">
                        <div style="height:70px; background:<?php echo esc_attr( $settings[ $key ] ); ?>;"></div>
                        <div style="padding:8px; font-size:12px;">
                            <strong><?php echo esc_html( $data['label'] ); ?></strong><br>
                            <code><?php echo esc_html( $settings[ $key ] ); ?></code>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <p style="margin-top:24px;">
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=scc-settings' ) ); ?>" class="button button-primary">
                    Đi đến trang Cài đặt màu sắc
                </a>
            </p>
        </div>
        <?php
    }

    /* ==========================================================
     *  TRANG CÀI ĐẶT: PRESET + FORM 2 CỘT + LIVE PREVIEW RESPONSIVE
     * ========================================================== */

    public function render_settings_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        $settings     = $this->get_settings();
        $groups       = $this->get_field_groups();
        $fields       = $this->get_fields();
        $presets      = $this->get_presets();
        $preview_vars = '';
        foreach ( $this->get_css_var_map() as $key => $var ) {
            $preview_vars .= '--scc-' . $var . ':' . esc_attr( $settings[ $key ] ) . ';';
        }

        if ( isset( $_GET['scc_updated'] ) ) {
            echo '<div class="notice notice-success is-dismissible"><p>Đã lưu màu sắc thành công!</p></div>';
        }
        if ( isset( $_GET['scc_reset'] ) ) {
            echo '<div class="notice notice-success is-dismissible"><p>Đã khôi phục màu mặc định!</p></div>';
        }
        ?>
        <style>
            .scc-wrap * { box-sizing: border-box; }
            .scc-wrap { max-width: 1400px; }
            .scc-layout {
                display: grid;
                grid-template-columns: 1fr 420px;
                gap: 28px;
                margin-top: 20px;
                align-items: start;
            }
            @media (max-width: 1100px) {
                .scc-layout { grid-template-columns: 1fr; }
            }

            /* --- Preset gallery --- */
            .scc-presets-title { margin: 28px 0 10px; font-size: 15px; }
            .scc-preset-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(190px, 1fr));
                gap: 14px;
                margin-bottom: 10px;
            }
            .scc-preset-card {
                background: #fff;
                border: 2px solid #dcdcde;
                border-radius: 10px;
                padding: 12px;
                cursor: pointer;
                transition: border-color .15s ease, box-shadow .15s ease, transform .1s ease;
            }
            .scc-preset-card:hover { border-color: #8c8f94; transform: translateY(-1px); }
            .scc-preset-card.is-active { border-color: #2271b1; box-shadow: 0 0 0 1px #2271b1; }
            .scc-preset-name { font-size: 12.5px; font-weight: 600; margin-bottom: 8px; color: #1e1e1e; }
            .scc-preset-chips { display: flex; gap: 4px; margin-bottom: 10px; }
            .scc-preset-chips span { width: 18px; height: 18px; border-radius: 50%; border: 1px solid rgba(0,0,0,.08); }

            /* Mini sample bên trong mỗi preset */
            .scc-mini-preview { border-radius: 6px; overflow: hidden; border: 1px solid rgba(0,0,0,.08); }
            .scc-mini-header { height: 14px; }
            .scc-mini-body { padding: 8px 8px 10px; }
            .scc-mini-title { width: 60%; height: 6px; border-radius: 3px; margin-bottom: 6px; }
            .scc-mini-line { width: 90%; height: 4px; border-radius: 2px; margin-bottom: 4px; opacity: .55; }
            .scc-mini-line.short { width: 55%; }
            .scc-mini-btn { width: 44px; height: 10px; border-radius: 3px; margin-top: 6px; }
            .scc-mini-footer { height: 12px; }

            /* --- Form cards / 2 cột --- */
            .scc-card {
                background: #fff; border: 1px solid #dcdcde; border-radius: 10px;
                padding: 18px 20px; margin-bottom: 18px;
            }
            .scc-card h2 { margin-top: 0; font-size: 14px; text-transform: uppercase; letter-spacing: .04em; color: #50575e; }
            .scc-field-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 16px 20px;
            }
            @media (max-width: 640px) {
                .scc-field-grid { grid-template-columns: 1fr; }
            }
            .scc-field label { display: block; font-weight: 600; margin-bottom: 6px; font-size: 13px; }

            /* --- Live preview panel --- */
            .scc-preview-panel { position: sticky; top: 40px; }
            .scc-device-switch { display: flex; gap: 6px; margin-bottom: 12px; }
            .scc-device-btn {
                display: flex; align-items: center; gap: 5px;
                background: #fff; border: 1px solid #dcdcde; border-radius: 6px;
                padding: 6px 10px; cursor: pointer; font-size: 12.5px; color: #1e1e1e;
            }
            .scc-device-btn.is-active { background: #2271b1; border-color: #2271b1; color: #fff; }
            .scc-preview-outer {
                background: #eef0f2; border-radius: 12px; padding: 18px;
                display: flex; justify-content: center;
            }
            #scc-preview-wrapper {
                width: 100%; max-width: 100%; transition: max-width .25s ease;
                border-radius: 10px; overflow: hidden; box-shadow: 0 3px 14px rgba(0,0,0,.12);
            }
            #scc-preview-frame {
                background: var(--scc-background); color: var(--scc-text);
                font-family: -apple-system, sans-serif; min-height: 420px;
            }
            .scc-pv-header {
                background: var(--scc-header-bg); padding: 14px 18px;
                display: flex; justify-content: space-between; align-items: center;
                border-bottom: 1px solid rgba(0,0,0,.06);
            }
            .scc-pv-logo { font-weight: 700; color: var(--scc-primary); font-size: 15px; }
            .scc-pv-nav a { color: var(--scc-link); text-decoration: none; font-size: 12px; margin-left: 12px; }
            .scc-pv-hero { padding: 26px 20px; }
            .scc-pv-hero h1 { color: var(--scc-primary); font-size: 20px; margin: 0 0 8px; }
            .scc-pv-hero p { font-size: 13px; line-height: 1.6; margin: 0 0 14px; }
            .scc-pv-hero a.scc-pv-link { color: var(--scc-link); font-size: 13px; }
            .scc-pv-btn {
                display: inline-block; margin-top: 14px; padding: 9px 18px; border-radius: 6px;
                background: var(--scc-button-bg); color: var(--scc-button-text);
                font-size: 12.5px; font-weight: 600; text-decoration: none;
            }
            .scc-pv-cards { display: flex; gap: 10px; padding: 0 20px 20px; flex-wrap: wrap; }
            .scc-pv-card { flex: 1; min-width: 110px; border: 1px solid rgba(0,0,0,.08); border-radius: 8px; padding: 10px; }
            .scc-pv-card .dot { width: 20px; height: 20px; border-radius: 50%; background: var(--scc-secondary); margin-bottom: 8px; }
            .scc-pv-card p { font-size: 11px; margin: 0; opacity: .75; }
            .scc-pv-footer {
                background: var(--scc-footer-bg); color: var(--scc-footer-text);
                padding: 16px 20px; font-size: 11.5px; text-align: center;
            }
        </style>

        <div class="wrap scc-wrap">
            <h1>Cài đặt màu sắc Website</h1>
            <p>Chọn nhanh một bảng màu mẫu bên dưới, hoặc tự tùy chỉnh từng màu — mọi thay đổi sẽ hiện ngay ở khung xem trước bên phải, xem được theo cả Desktop / Tablet / Mobile.</p>

            <h2 class="scc-presets-title">🎨 Bảng màu mẫu dựng sẵn</h2>
            <div class="scc-preset-grid">
                <?php foreach ( $presets as $preset ) :
                    $c = $preset['colors'];
                    ?>
                    <div class="scc-preset-card" data-preset='<?php echo esc_attr( wp_json_encode( $c ) ); ?>'>
                        <div class="scc-preset-name"><?php echo esc_html( $preset['name'] ); ?></div>
                        <div class="scc-mini-preview">
                            <div class="scc-mini-header" style="background:<?php echo esc_attr( $c['header_bg'] ); ?>"></div>
                            <div class="scc-mini-body" style="background:<?php echo esc_attr( $c['background_color'] ); ?>">
                                <div class="scc-mini-title" style="background:<?php echo esc_attr( $c['primary_color'] ); ?>"></div>
                                <div class="scc-mini-line" style="background:<?php echo esc_attr( $c['text_color'] ); ?>"></div>
                                <div class="scc-mini-line short" style="background:<?php echo esc_attr( $c['text_color'] ); ?>"></div>
                                <div class="scc-mini-btn" style="background:<?php echo esc_attr( $c['button_bg'] ); ?>"></div>
                            </div>
                            <div class="scc-mini-footer" style="background:<?php echo esc_attr( $c['footer_bg'] ); ?>"></div>
                        </div>
                        <div class="scc-preset-chips">
                            <span style="background:<?php echo esc_attr( $c['primary_color'] ); ?>"></span>
                            <span style="background:<?php echo esc_attr( $c['secondary_color'] ); ?>"></span>
                            <span style="background:<?php echo esc_attr( $c['button_bg'] ); ?>"></span>
                            <span style="background:<?php echo esc_attr( $c['footer_bg'] ); ?>"></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                <input type="hidden" name="action" value="scc_save_colors">
                <?php wp_nonce_field( self::NONCE_ACTION ); ?>

                <div class="scc-layout">
                    <!-- CỘT TRÁI: form cài đặt theo nhóm -->
                    <div class="scc-settings-column">
                        <?php foreach ( $groups as $group ) : ?>
                            <div class="scc-card">
                                <h2><?php echo esc_html( $group['title'] ); ?></h2>
                                <div class="scc-field-grid">
                                    <?php foreach ( $group['fields'] as $key ) :
                                        $data = $fields[ $key ];
                                        ?>
                                        <div class="scc-field">
                                            <label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $data['label'] ); ?></label>
                                            <input
                                                type="text"
                                                id="<?php echo esc_attr( $key ); ?>"
                                                name="<?php echo esc_attr( self::OPTION_KEY ); ?>[<?php echo esc_attr( $key ); ?>]"
                                                value="<?php echo esc_attr( $settings[ $key ] ); ?>"
                                                class="scc-color-field"
                                                data-field="<?php echo esc_attr( $key ); ?>"
                                                data-default-color="<?php echo esc_attr( $data['default'] ); ?>"
                                            />
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <?php submit_button( 'Lưu thay đổi' ); ?>
                    </div>

                    <!-- CỘT PHẢI: xem trước trực tiếp, responsive -->
                    <div class="scc-preview-column">
                        <div class="scc-preview-panel">
                            <div class="scc-device-switch">
                                <button type="button" class="scc-device-btn is-active" data-width="100%">
                                    <span class="dashicons dashicons-desktop"></span> Desktop
                                </button>
                                <button type="button" class="scc-device-btn" data-width="768px">
                                    <span class="dashicons dashicons-tablet"></span> Tablet
                                </button>
                                <button type="button" class="scc-device-btn" data-width="375px">
                                    <span class="dashicons dashicons-smartphone"></span> Mobile
                                </button>
                            </div>

                            <div class="scc-preview-outer">
                                <div id="scc-preview-wrapper">
                                    <div id="scc-preview-frame" style="<?php echo esc_attr( $preview_vars ); ?>">
                                        <div class="scc-pv-header">
                                            <div class="scc-pv-logo">MyWebsite</div>
                                            <div class="scc-pv-nav">
                                                <a href="#">Trang chủ</a>
                                                <a href="#">Dịch vụ</a>
                                                <a href="#">Liên hệ</a>
                                            </div>
                                        </div>
                                        <div class="scc-pv-hero">
                                            <h1>Chào mừng đến với website của bạn</h1>
                                            <p>Đây là đoạn văn bản mẫu để bạn xem thử màu chữ, màu nền và bố cục sẽ trông như thế nào sau khi áp dụng. Hãy thử đổi màu và xem hiệu ứng ngay lập tức.</p>
                                            <a href="#" class="scc-pv-link">Xem thêm liên kết mẫu →</a><br>
                                            <a href="#" class="scc-pv-btn">Nút bấm mẫu</a>
                                        </div>
                                        <div class="scc-pv-cards">
                                            <div class="scc-pv-card"><div class="dot"></div><p>Tính năng nổi bật một</p></div>
                                            <div class="scc-pv-card"><div class="dot"></div><p>Tính năng nổi bật hai</p></div>
                                        </div>
                                        <div class="scc-pv-footer">© 2026 MyWebsite — Bản xem trước màu sắc</div>
                                    </div>
                                </div>
                            </div>
                            <p class="description" style="text-align:center; margin-top:10px;">
                                Bản xem trước minh họa — giao diện thật trên website có thể khác tùy theme.
                            </p>
                        </div>
                    </div>
                </div>
            </form>

            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" onsubmit="return confirm('Khôi phục toàn bộ về màu mặc định?');" style="margin-top: 6px;">
                <input type="hidden" name="action" value="scc_reset_colors">
                <?php wp_nonce_field( self::NONCE_ACTION ); ?>
                <?php submit_button( 'Khôi phục màu mặc định', 'secondary' ); ?>
            </form>
        </div>
        <?php
    }

    /* ==========================================================
     *  XỬ LÝ LƯU / RESET
     * ========================================================== */

    public function handle_save() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'Không có quyền truy cập.' );
        }
        check_admin_referer( self::NONCE_ACTION );

        $fields = $this->get_fields();
        $input  = isset( $_POST[ self::OPTION_KEY ] ) ? wp_unslash( $_POST[ self::OPTION_KEY ] ) : array();
        $clean  = array();

        foreach ( $fields as $key => $data ) {
            $value         = isset( $input[ $key ] ) ? sanitize_hex_color( $input[ $key ] ) : $data['default'];
            $clean[ $key ] = $value ? $value : $data['default'];
        }

        update_option( self::OPTION_KEY, $clean );
        wp_safe_redirect( admin_url( 'admin.php?page=scc-settings&scc_updated=1' ) );
        exit;
    }

    public function handle_reset() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'Không có quyền truy cập.' );
        }
        check_admin_referer( self::NONCE_ACTION );

        delete_option( self::OPTION_KEY );
        wp_safe_redirect( admin_url( 'admin.php?page=scc-settings&scc_reset=1' ) );
        exit;
    }

    /* ==========================================================
     *  CSS ÁP DỤNG CHO FRONTEND (TOÀN WEBSITE)
     * ========================================================== */

    public function output_custom_css() {
        $s = $this->get_settings();
        ?>
        <style id="scc-custom-colors">
            :root {
                --scc-primary: <?php echo esc_attr( $s['primary_color'] ); ?>;
                --scc-secondary: <?php echo esc_attr( $s['secondary_color'] ); ?>;
                --scc-background: <?php echo esc_attr( $s['background_color'] ); ?>;
                --scc-text: <?php echo esc_attr( $s['text_color'] ); ?>;
                --scc-link: <?php echo esc_attr( $s['link_color'] ); ?>;
                --scc-header-bg: <?php echo esc_attr( $s['header_bg'] ); ?>;
                --scc-footer-bg: <?php echo esc_attr( $s['footer_bg'] ); ?>;
                --scc-footer-text: <?php echo esc_attr( $s['footer_text'] ); ?>;
                --scc-button-bg: <?php echo esc_attr( $s['button_bg'] ); ?>;
                --scc-button-text: <?php echo esc_attr( $s['button_text'] ); ?>;
            }
            body { background-color: var(--scc-background) !important; color: var(--scc-text) !important; }
            a { color: var(--scc-link) !important; }
            header, .site-header, #masthead { background-color: var(--scc-header-bg) !important; }
            footer, .site-footer, #colophon { background-color: var(--scc-footer-bg) !important; color: var(--scc-footer-text) !important; }
            footer a, .site-footer a, #colophon a { color: var(--scc-footer-text) !important; }
            .button, button, input[type="submit"], input[type="button"], .wp-block-button__link {
                background-color: var(--scc-button-bg) !important;
                color: var(--scc-button-text) !important;
                border-color: var(--scc-button-bg) !important;
            }
            .site-title a, .site-title, h1, h2, h3 { color: var(--scc-primary) !important; }
        </style>
        <?php
    }
}

new SCC_Site_Color_Changer();