<?php
/**
 * Plugin Recommendations for WordPress Themes
 * Dynamic theme name support using wp_get_theme()
 *
 * @package beshop_Pro
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Include plugin data configuration
require_once get_template_directory() . '/libs/activation/plugin-data.php';



/**
 * Silent Upgrader Skin for automatic plugin installation
 * Only define when needed to avoid early loading issues
 */
if (!class_exists('beshop_Pro_Silent_Upgrader_Skin')) {
    
    /**
     * Load the Silent Upgrader Skin class when needed
     */
    function beshop_load_silent_upgrader_skin() {
        if (!class_exists('WP_Upgrader_Skin')) {
            require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        }
        
        if (!class_exists('beshop_Pro_Silent_Upgrader_Skin')) {
            class beshop_Pro_Silent_Upgrader_Skin extends WP_Upgrader_Skin {
                
                /**
                 * Override feedback method to suppress output
                 */
                public function feedback($string, ...$args) {
                    // Suppress all output during installation
                }
                
                /**
                 * Override header method to suppress output
                 */
                public function header() {
                    // Suppress header output
                }
                
                /**
                 * Override footer method to suppress output
                 */
                public function footer() {
                    // Suppress footer output
                }
                
                /**
                 * Override error method to suppress output
                 */
                public function error($errors) {
                    // Suppress error output but still return the error
                    return $errors;
                }
            }
        }
    }
}

/**
 * Plugin Recommendations Class
 */
class beshop_Pro_Plugin_Recommendations {
    
    /**
     * Plugin directory path
     */
    private $plugin_dir;
    
    /**
     * Plugin directory URL
     */
    private $plugin_url;
    
    /**
     * Theme name
     */
    private $theme_name;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->plugin_dir = get_template_directory() . '/libs/activation';
        $this->plugin_url = get_template_directory_uri() . '/libs/activation';
        $this->theme_name = wp_get_theme()->get('Name');
        
        // Reset old notice dismissals only once (for migration to new system)
        $this->migrate_notice_dismissal();
        
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('wp_ajax_beshop_install_plugin', array($this, 'ajax_install_plugin'));
        add_action('wp_ajax_beshop_activate_plugin', array($this, 'ajax_activate_plugin'));
        add_action('wp_ajax_beshop_update_plugin', array($this, 'ajax_update_plugin'));
        add_action('wp_ajax_beshop_dismiss_plugin_notice', array($this, 'ajax_dismiss_plugin_notice'));
        add_action('wp_ajax_beshop_dismiss_update_notice', array($this, 'ajax_dismiss_update_notice'));
        add_action('wp_ajax_beshop_dismiss_install_notice', array($this, 'ajax_dismiss_install_notice'));
        add_action('wp_ajax_beshop_update_all_plugins', array($this, 'ajax_update_all_plugins'));
        add_action('wp_ajax_beshop_install_required_plugins', array($this, 'ajax_install_required_plugins'));
        add_action('wp_ajax_beshop_install_recommended_plugins', array($this, 'ajax_install_recommended_plugins'));
        add_action('wp_ajax_beshop_check_recommended_plugins_status', array($this, 'ajax_check_recommended_plugins_status'));
        add_action('admin_notices', array($this, 'show_recommendation_notice'));
        add_action('admin_notices', array($this, 'show_update_notice'));
        
        // Auto-install required plugins
        add_action('after_switch_theme', array($this, 'auto_install_required_plugins'));
        add_action('admin_init', array($this, 'check_required_plugins'));
    }
    
    /**
     * Get theme name for display
     */
    public function get_theme_name() {
        return $this->theme_name;
    }
    
    /**
     * Add admin menu page
     */
    public function add_admin_menu() {
        add_theme_page(
            __('Recommended Plugins', 'beshop'),
            __('Recommended Plugins', 'beshop'),
            'manage_options',
            'beshop-plugins',
            array($this, 'render_admin_page')
        );
    }
    
    /**
     * Get recommended plugins (with filter hooks for extensibility)
     * 
     * @return array Array of plugin configurations
     */
    public function get_recommended_plugins() {
        // Get default plugin data from the configuration file
        $default_plugins = beshop_get_default_plugin_data();
        
        /**
         * Filter to modify the recommended plugins list
         * 
         * This filter allows themes, child themes, and plugins to add, remove, 
         * or modify the recommended plugins list.
         * 
         * @since 1.0.0
         * 
         * @param array $default_plugins Default plugin configuration array
         */
        $plugins = apply_filters('beshop_recommended_plugins', $default_plugins);
        
        /**
         * Filter to add additional plugins to the recommendations
         * 
         * This is a convenient filter specifically for adding new plugins
         * without having to handle the entire array.
         * 
         * @since 1.0.0
         * 
         * @param array $plugins Current plugin configuration array
         */
        $plugins = apply_filters('beshop_add_recommended_plugins', $plugins);
        
        /**
         * Filter to modify specific plugin categories
         * 
         * @since 1.0.0
         * 
         * @param array $plugins Current plugin configuration array
         */
        $plugins = apply_filters('beshop_filter_plugins_by_category', $plugins);
        
        return $plugins;
    }
    
    /**
     * Check plugin status including version checking for updates
     */
    public function get_plugin_status($plugin) {
        $this->check_plugin_functions();
        
        $plugin_file = $plugin['file'];
        
        if (is_plugin_active($plugin_file)) {
            // Check if plugin needs update (for plugins with version specified)
            if (isset($plugin['version']) && !empty($plugin['version'])) {
                $current_version = $this->get_plugin_version($plugin_file);
                if ($current_version && version_compare($current_version, $plugin['version'], '<')) {
                    return 'needs-update';
                }
            }
            return 'active';
        } elseif (file_exists(WP_PLUGIN_DIR . '/' . $plugin_file)) {
            // Check if inactive plugin needs update
            if (isset($plugin['version']) && !empty($plugin['version'])) {
                $current_version = $this->get_plugin_version($plugin_file);
                if ($current_version && version_compare($current_version, $plugin['version'], '<')) {
                    return 'inactive-needs-update';
                }
            }
            return 'inactive';
        } else {
            return 'not-installed';
        }
    }
    
    /**
     * Get plugin version from plugin header
     */
    private function get_plugin_version($plugin_file) {
        $plugin_path = WP_PLUGIN_DIR . '/' . $plugin_file;
        
        if (!file_exists($plugin_path)) {
            return false;
        }
        
        // Get plugin data
        if (!function_exists('get_plugin_data')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        
        $plugin_data = get_plugin_data($plugin_path);
        
        return isset($plugin_data['Version']) ? $plugin_data['Version'] : false;
    }
    
    /**
     * Check if plugin is a local plugin
     */
    private function is_local_plugin($plugin) {
        $result = isset($plugin['is_local']) && $plugin['is_local'] === true;
        return $result;
    }
    
    /**
     * Render admin page
     */
    public function render_admin_page() {
        $plugins = $this->get_recommended_plugins();
        ?>
        <div class="wrap beshop-plugins">
            <h1><?php printf(esc_html__('Recommended Plugins for %s', 'beshop'), $this->theme_name); ?></h1>
            <p class="description">
                <?php printf(esc_html__('These plugins are recommended to enhance your %s theme experience. They work perfectly with the theme\'s features and animations.', 'beshop'), $this->theme_name); ?>
            </p>
            
            <?php
            // Check if there are missing required plugins
            $missing_required = array();
            $required_plugins = $this->get_required_plugins();
            
            foreach ($required_plugins as $slug => $plugin) {
                if ($this->get_plugin_status($plugin) !== 'active') {
                    $missing_required[$slug] = $plugin;
                }
            }
            
            if (!empty($missing_required)) :
            ?>
            <div class="notice notice-warning">
                <p>
                    <strong><?php esc_html_e('Missing Required Plugins:', 'beshop'); ?></strong>
                    <?php printf(esc_html__('%d required plugins are missing or inactive.', 'beshop'), count($missing_required)); ?>
                    <button id="install-required-plugins" class="button button-primary" style="margin-left: 10px;">
                        <?php esc_html_e('Install Required Plugins', 'beshop'); ?>
                    </button>
                </p>
            </div>
            <?php endif; ?>
            
            <?php
            // Count plugins in each category
            $plugin_counts = array(
                'all' => count($plugins),
                'featured' => 0,
                'required' => 0,
                'active' => 0,
                'inactive' => 0,
                'not-installed' => 0
            );
            
            foreach ($plugins as $slug => $plugin) {
                $status = $this->get_plugin_status($plugin);
                
                if ($plugin['featured']) {
                    $plugin_counts['featured']++;
                }
                
                if ($plugin['required']) {
                    $plugin_counts['required']++;
                }
                
                switch ($status) {
                    case 'active':
                        $plugin_counts['active']++;
                        break;
                    case 'inactive':
                    case 'inactive-needs-update':
                        $plugin_counts['inactive']++;
                        break;
                    case 'not-installed':
                        $plugin_counts['not-installed']++;
                        break;
                }
            }
            
            // Determine which tab should be active (first available tab)
            $available_tabs = array();
            $first_active_tab = '';
            
            if ($plugin_counts['all'] > 0) {
                $available_tabs[] = 'all';
                if (empty($first_active_tab)) $first_active_tab = 'all';
            }
            if ($plugin_counts['featured'] > 0) {
                $available_tabs[] = 'featured';
                if (empty($first_active_tab)) $first_active_tab = 'featured';
            }
            if ($plugin_counts['required'] > 0) {
                $available_tabs[] = 'required';
                if (empty($first_active_tab)) $first_active_tab = 'required';
            }
            if ($plugin_counts['active'] > 0) {
                $available_tabs[] = 'active';
                if (empty($first_active_tab)) $first_active_tab = 'active';
            }
            if ($plugin_counts['inactive'] > 0) {
                $available_tabs[] = 'inactive';
                if (empty($first_active_tab)) $first_active_tab = 'inactive';
            }
            if ($plugin_counts['not-installed'] > 0) {
                $available_tabs[] = 'not-installed';
                if (empty($first_active_tab)) $first_active_tab = 'not-installed';
            }
            ?>
            
            <div class="plugin-filter-tabs">
                <?php if ($plugin_counts['all'] > 0) : ?>
                    <button class="filter-tab <?php echo ($first_active_tab === 'all') ? 'active' : ''; ?>" data-filter="all"><?php printf(esc_html__('All Plugins (%d)', 'beshop'), $plugin_counts['all']); ?></button>
                <?php endif; ?>
                
                <?php if ($plugin_counts['featured'] > 0) : ?>
                    <button class="filter-tab <?php echo ($first_active_tab === 'featured') ? 'active' : ''; ?>" data-filter="featured"><?php printf(esc_html__('Featured (%d)', 'beshop'), $plugin_counts['featured']); ?></button>
                <?php endif; ?>
                
                <?php if ($plugin_counts['required'] > 0) : ?>
                    <button class="filter-tab <?php echo ($first_active_tab === 'required') ? 'active' : ''; ?>" data-filter="required"><?php printf(esc_html__('Required (%d)', 'beshop'), $plugin_counts['required']); ?></button>
                <?php endif; ?>
                
                <?php if ($plugin_counts['active'] > 0) : ?>
                    <button class="filter-tab <?php echo ($first_active_tab === 'active') ? 'active' : ''; ?>" data-filter="active"><?php printf(esc_html__('Active (%d)', 'beshop'), $plugin_counts['active']); ?></button>
                <?php endif; ?>
                
                <?php if ($plugin_counts['inactive'] > 0) : ?>
                    <button class="filter-tab <?php echo ($first_active_tab === 'inactive') ? 'active' : ''; ?>" data-filter="inactive"><?php printf(esc_html__('Inactive (%d)', 'beshop'), $plugin_counts['inactive']); ?></button>
                <?php endif; ?>
                
                <?php if ($plugin_counts['not-installed'] > 0) : ?>
                    <button class="filter-tab <?php echo ($first_active_tab === 'not-installed') ? 'active' : ''; ?>" data-filter="not-installed"><?php printf(esc_html__('Not Installed (%d)', 'beshop'), $plugin_counts['not-installed']); ?></button>
                <?php endif; ?>
            </div>
            
            <div class="plugins-grid">
                <?php 
                foreach ($plugins as $slug => $plugin) : 
                    $status = $this->get_plugin_status($plugin);
                    $featured_class = $plugin['featured'] ? 'featured' : '';
                    $is_local = $this->is_local_plugin($plugin);
                ?>
                    <div class="plugin-card <?php echo esc_attr($featured_class); ?>" data-status="<?php echo esc_attr($status); ?>" data-featured="<?php echo $plugin['featured'] ? '1' : '0'; ?>" data-required="<?php echo $plugin['required'] ? '1' : '0'; ?>" data-local="<?php echo $is_local ? '1' : '0'; ?>" data-slug="<?php echo esc_attr($slug); ?>">
                        <?php if ($plugin['featured']) : ?>
                            <div class="featured-badge"><?php esc_html_e('Featured', 'beshop'); ?></div>
                        <?php endif; ?>
                        
                        <?php if ($plugin['required']) : ?>
                            <div class="required-badge"><?php esc_html_e('Required', 'beshop'); ?></div>
                        <?php endif; ?>
                        
                        <?php if ($is_local) : ?>
                            <div class="local-plugin-badge"><?php esc_html_e('Local', 'beshop'); ?></div>
                        <?php endif; ?>
                        
                        <div class="plugin-header">
                            <h3><?php echo esc_html($plugin['name']); ?></h3>
                            <span class="plugin-category"><?php echo esc_html($plugin['category']); ?></span>
                        </div>
                        
                        <div class="plugin-description">
                            <p><?php echo esc_html($plugin['description']); ?></p>
                        </div>
                        
                        <div class="plugin-actions">
                            <?php $this->render_plugin_button($slug, $plugin, $status); ?>
                            <span class="plugin-status status-<?php echo esc_attr($status); ?>">
                                <?php echo esc_html($this->get_status_text($status)); ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render plugin action button
     */
    private function render_plugin_button($slug, $plugin, $status) {
        $is_local = $this->is_local_plugin($plugin);
        $current_version = '';
        $required_version = isset($plugin['version']) ? $plugin['version'] : '';
        
        if ($status === 'active' || $status === 'inactive' || $status === 'needs-update' || $status === 'inactive-needs-update') {
            $current_version = $this->get_plugin_version($plugin['file']);
        }
        
        switch ($status) {
            case 'active':
                echo '<span class="button disabled">' . esc_html__('Active', 'beshop') . '</span>';
                break;
            case 'needs-update':
                echo '<button class="button button-secondary update-plugin" data-slug="' . esc_attr($slug) . '" data-file="' . esc_attr($plugin['file']) . '" data-is-local="' . ($is_local ? '1' : '0') . '">' . esc_html__('Update', 'beshop') . '</button>';
                break;
            case 'inactive':
                echo '<button class="button button-primary activate-plugin" data-slug="' . esc_attr($slug) . '" data-file="' . esc_attr($plugin['file']) . '">' . esc_html__('Activate', 'beshop') . '</button>';
                break;
            case 'inactive-needs-update':
                echo '<button class="button button-secondary update-plugin" data-slug="' . esc_attr($slug) . '" data-file="' . esc_attr($plugin['file']) . '" data-is-local="' . ($is_local ? '1' : '0') . '">' . esc_html__('Update', 'beshop') . '</button>';
                break;
            case 'not-installed':
                if ($is_local) {
                    echo '<button class="button button-primary install-plugin" data-slug="' . esc_attr($slug) . '" data-is-local="1">' . esc_html__('Install', 'beshop') . '</button>';
                } else {
                    echo '<button class="button button-primary install-plugin" data-slug="' . esc_attr($plugin['slug']) . '" data-is-local="0">' . esc_html__('Install', 'beshop') . '</button>';
                }
                break;
        }
        
        // Show version info if available
        if ($current_version && $required_version) {
            echo '<small class="version-info">';
            printf(esc_html__('Current: %s | Required: %s', 'beshop'), $current_version, $required_version);
            echo '</small>';
        } elseif ($current_version) {
            echo '<small class="version-info">';
            printf(esc_html__('Version: %s', 'beshop'), $current_version);
            echo '</small>';
        } elseif ($required_version) {
            echo '<small class="version-info">';
            printf(esc_html__('Required Version: %s', 'beshop'), $required_version);
            echo '</small>';
        }
    }
    
    /**
     * Get status text
     */
    private function get_status_text($status) {
        switch ($status) {
            case 'active':
                return __('Active', 'beshop');
            case 'needs-update':
                return __('Update Available', 'beshop');
            case 'inactive':
                return __('Installed but not active', 'beshop');
            case 'inactive-needs-update':
                return __('Update Available (Inactive)', 'beshop');
            case 'not-installed':
                return __('Not installed', 'beshop');
            default:
                return __('Unknown', 'beshop');
        }
    }
    
    /**
     * Show update notice for plugins that need updates
     */
    public function show_update_notice() {
        if ( ! get_user_meta( get_current_user_id(), 'aime_notice_dismissed', true ) ) {
            return;
        }
        $screen = get_current_screen();
        
        // Only show on dashboard, themes, and plugins pages
        if (!in_array($screen->id, array('dashboard', 'themes', 'plugins', 'appearance_page_beshop-plugins'))) {
            return;
        }
        
        // Check if user dismissed the update notice
        $dismissed_time = get_user_meta(get_current_user_id(), 'beshop_hide_update_notice_time', true);
        $show_update_notice = !$dismissed_time || (time() - $dismissed_time) >= (7 * 24 * 60 * 60); // 7 days
        
        if (!$show_update_notice) {
            return;
        }
        
        // Get plugins that need updates
        $plugins_need_update = $this->get_plugins_needing_updates();
        
        if (empty($plugins_need_update)) {
            return;
        }
        
        ?>
        <div class="notice notice-warning is-dismissible beshop-pro-update-notice">
            <div class="plugin-update-content">
                <div style="flex: 1;">
                    <h3><?php esc_html_e('Plugin Updates Available', 'beshop'); ?></h3>
                    <p><?php printf(esc_html__('%d plugins have updates available. Keep your plugins up to date for better security and performance.', 'beshop'), count($plugins_need_update)); ?></p>
                    <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap; margin-top: 15px;">
                        <button id="update-all-plugins" class="button button-primary"><?php esc_html_e('Update All Plugins', 'beshop'); ?></button>
                        <a href="<?php echo esc_url(admin_url('themes.php?page=beshop-plugins')); ?>" class="button button-secondary"><?php esc_html_e('Manage Plugins', 'beshop'); ?></a>
                    </div>
                </div>
                <div class="plugin-icon-recomend">🔄</div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Get plugins that need updates
     */
    private function get_plugins_needing_updates() {
        $all_plugins = $this->get_recommended_plugins();
        $plugins_need_update = array();
        
        foreach ($all_plugins as $slug => $plugin) {
            $status = $this->get_plugin_status($plugin);
            if (in_array($status, array('needs-update', 'inactive-needs-update'))) {
                $plugins_need_update[$slug] = $plugin;
            }
        }
        
        return $plugins_need_update;
    }
    
    /**
     * AJAX handler for dismissing update notice
     */
    public function ajax_dismiss_update_notice() {
        check_ajax_referer('beshop_plugins_nonce', 'nonce');
        
        // Save the current timestamp when update notice is dismissed
        update_user_meta(get_current_user_id(), 'beshop_hide_update_notice_time', time());
        wp_send_json_success();
    }
    
    /**
     * AJAX handler for dismissing install notice
     */
    public function ajax_dismiss_install_notice() {
        check_ajax_referer('beshop_plugins_nonce', 'nonce');
        
        // Save the current timestamp when install notice is dismissed
        update_user_meta(get_current_user_id(), 'beshop_hide_install_notice_time', time());
        wp_send_json_success();
    }
    
    /**
     * AJAX handler for dismissing plugin notice
     */
    public function ajax_dismiss_plugin_notice() {
        check_ajax_referer('beshop_plugins_nonce', 'nonce');
        
        $notice_type = sanitize_text_field($_POST['notice_type']);
        
        if ($notice_type === 'recommended') {
            // Save the current timestamp when recommended notice is dismissed
            update_user_meta(get_current_user_id(), 'beshop_info_recomend_plugins', time());
        }
        
        wp_send_json_success();
    }
    
    /**
     * AJAX handler for updating all plugins
     */
    public function ajax_update_all_plugins() {
        check_ajax_referer('beshop_plugins_nonce', 'nonce');
        
        if (!current_user_can('update_plugins')) {
            wp_die(__('You do not have permission to update plugins.', 'beshop'));
        }
        
        $plugins_need_update = $this->get_plugins_needing_updates();
        $results = array();
        $success_count = 0;
        $total_count = count($plugins_need_update);
        
        foreach ($plugins_need_update as $slug => $plugin) {
            $is_local = $this->is_local_plugin($plugin);
            
            if ($is_local) {
                $result = $this->update_local_plugin($plugin);
            } else {
                $result = $this->update_wordpress_org_plugin($plugin['file']);
            }
            
            if ($result) {
                $results[$slug] = 'updated';
                $success_count++;
            } else {
                $results[$slug] = 'failed';
            }
        }
        
        if ($success_count === $total_count) {
            wp_send_json_success(array(
                'message' => sprintf(__('%d plugins updated successfully.', 'beshop'), $success_count),
                'results' => $results
            ));
        } else {
            wp_send_json_error(array(
                'message' => sprintf(__('%d of %d plugins updated successfully.', 'beshop'), $success_count, $total_count),
                'results' => $results
            ));
        }
    }
    
    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_admin_scripts($hook) {
        // Load scripts on plugin page, dashboard, themes page, and plugins page
        if (!in_array($hook, array('appearance_page_beshop-plugins', 'index.php', 'themes.php', 'plugins.php'))) {
            return;
        }
        
        wp_enqueue_script(
            'beshop-plugins-admin',
            $this->plugin_url . '/assets/js/admin-plugins.js',
            array('jquery'),
            BESHOP_VERSION,
            true
        );
        
        wp_localize_script('beshop-plugins-admin', 'blogBuildProPlugins', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('beshop_plugins_nonce'),
            'strings' => array(
                'installing' => __('Installing...', 'beshop'),
                'activating' => __('Activating...', 'beshop'),
                'updating' => __('Updating...', 'beshop'),
                'installed' => __('Installed', 'beshop'),
                'activated' => __('Activated', 'beshop'),
                'updated' => __('Updated', 'beshop'),
                'active' => __('Active', 'beshop'),
                'inactive' => __('Inactive', 'beshop'),
                'notInstalled' => __('Not Installed', 'beshop'),
                'needsUpdate' => __('Update Available', 'beshop'),
                'inactiveNeedsUpdate' => __('Update Available (Inactive)', 'beshop'),
                'install' => __('Install', 'beshop'),
                'activate' => __('Activate', 'beshop'),
                'update' => __('Update', 'beshop'),
                'error' => __('Error occurred', 'beshop'),
                'requiredWarning' => __('You must install and activate all required plugins before dismissing this notice. Click Dismiss again to hide this warning.', 'beshop'),
            )
        ));
        
        wp_enqueue_style(
            'beshop-plugins-admin',
            $this->plugin_url . '/assets/css/admin-plugins.css',
            array(),
            BESHOP_VERSION
        );
    }
    
    /**
     * AJAX handler for plugin installation
     */
    public function ajax_install_plugin() {
        check_ajax_referer('beshop_plugins_nonce', 'nonce');
        
        if (!current_user_can('install_plugins')) {
            wp_die(__('You do not have permission to install plugins.', 'beshop'));
        }
        
        $slug = sanitize_text_field($_POST['slug']);
        $is_local = isset($_POST['is_local']) && $_POST['is_local'] === '1';
        
        if ($is_local) {
            // Handle local plugin installation
            $plugins = $this->get_recommended_plugins();
            
            if (!isset($plugins[$slug])) {
                wp_send_json_error(__('Plugin not found in recommendations.', 'beshop'));
            }
            
            $plugin = $plugins[$slug];
            
            // Check if source file exists
            if (!isset($plugin['source']) || !file_exists($plugin['source'])) {
                // Try to construct the path manually if not set correctly
                if (!isset($plugin['source'])) {
                    $manual_source = get_template_directory() . '/libs/plugins/' . $slug . '.zip';
                    if (file_exists($manual_source)) {
                        $plugin['source'] = $manual_source;
                    }
                }
                
                if (!isset($plugin['source']) || !file_exists($plugin['source'])) {
                    wp_send_json_error(__('Local plugin zip file not found. Please ensure the plugin zip file exists in the theme directory.', 'beshop'));
                }
            }
            
            // Check if ZipArchive is available - removed strict requirement
            // We now have fallback methods that work without ZipArchive
            if (!class_exists('ZipArchive')) {
                // Continue anyway - we have fallback extraction methods
            }
            
            // Check if plugins directory is writable
            if (!is_writable(WP_PLUGIN_DIR)) {
                wp_send_json_error(__('Plugins directory is not writable. Please check directory permissions.', 'beshop'));
            }
            
            $result = $this->install_local_plugin($plugin);
            
            if ($result) {
                wp_send_json_success(__('Local plugin installed successfully.', 'beshop'));
            } else {
                wp_send_json_error(__('Local plugin installation failed. Please check error logs for details.', 'beshop'));
            }
        } else {
            // Handle WordPress.org plugin installation
            include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
            include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
            
            $api = plugins_api('plugin_information', array('slug' => $slug));
            
            if (is_wp_error($api)) {
                wp_send_json_error(__('Plugin information could not be retrieved.', 'beshop'));
            }
            
            $upgrader = new Plugin_Upgrader(new WP_Ajax_Upgrader_Skin());
            $install = $upgrader->install($api->download_link);
            
            if ($install) {
                wp_send_json_success(__('Plugin installed successfully.', 'beshop'));
            } else {
                wp_send_json_error(__('Plugin installation failed.', 'beshop'));
            }
        }
    }
    
    /**
     * AJAX handler for plugin activation
     */
    public function ajax_activate_plugin() {
        check_ajax_referer('beshop_plugins_nonce', 'nonce');
        
        if (!current_user_can('activate_plugins')) {
            wp_die(__('You do not have permission to activate plugins.', 'beshop'));
        }
        
        $plugin_file = sanitize_text_field($_POST['file']);
        
        $result = activate_plugin($plugin_file);
        
        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        } else {
            wp_send_json_success(__('Plugin activated successfully.', 'beshop'));
        }
    }
    
    /**
     * AJAX handler for plugin updates
     */
    public function ajax_update_plugin() {
        check_ajax_referer('beshop_plugins_nonce', 'nonce');
        
        if (!current_user_can('update_plugins')) {
            wp_die(__('You do not have permission to update plugins.', 'beshop'));
        }
        
        $slug = sanitize_text_field($_POST['slug']);
        $plugin_file = sanitize_text_field($_POST['file']);
        $is_local = isset($_POST['is_local']) && $_POST['is_local'] === '1';
        
        $plugins = $this->get_recommended_plugins();
        
        if (!isset($plugins[$slug])) {
            wp_send_json_error(__('Plugin not found.', 'beshop'));
        }
        
        $plugin = $plugins[$slug];
        
        // Handle local plugin update
        if ($is_local) {
            $result = $this->update_local_plugin($plugin);
            
            if ($result) {
                wp_send_json_success(__('Local plugin updated successfully.', 'beshop'));
            } else {
                wp_send_json_error(__('Failed to update local plugin.', 'beshop'));
            }
        } else {
            // Handle WordPress.org plugin update
            $result = $this->update_wordpress_org_plugin($plugin_file);
            
            if ($result) {
                wp_send_json_success(__('Plugin updated successfully.', 'beshop'));
            } else {
                wp_send_json_error(__('Failed to update plugin.', 'beshop'));
            }
        }
    }
    
    /**
     * AJAX handler for bulk installing required plugins
     */
    public function ajax_install_required_plugins() {
        check_ajax_referer('beshop_plugins_nonce', 'nonce');
        
        if (!current_user_can('install_plugins')) {
            wp_die(__('You do not have permission to install plugins.', 'beshop'));
        }
        
        $required_plugins = $this->get_required_plugins();
        $results = array();
        $success_count = 0;
        $total_count = count($required_plugins);
        
        foreach ($required_plugins as $slug => $plugin) {
            $status = $this->get_plugin_status($plugin);
            
            if ($status === 'active') {
                $results[$slug] = 'already-active';
                $success_count++;
            } else {
                $installed = $this->install_and_activate_plugin($plugin);
                
                if ($installed) {
                    $results[$slug] = 'installed-and-activated';
                    $success_count++;
                } else {
                    $results[$slug] = 'failed';
                }
            }
        }
        
        if ($success_count === $total_count) {
            wp_send_json_success(array(
                'message' => sprintf(__('%d required plugins installed and activated successfully.', 'beshop'), $success_count),
                'results' => $results
            ));
        } else {
            wp_send_json_error(array(
                'message' => sprintf(__('%d of %d required plugins installed successfully.', 'beshop'), $success_count, $total_count),
                'results' => $results
            ));
        }
    }
    
    /**
     * AJAX handler for bulk installing recommended plugins
     */
    public function ajax_install_recommended_plugins() {
        check_ajax_referer('beshop_plugins_nonce', 'nonce');
        
        if (!current_user_can('install_plugins')) {
            wp_die(__('You do not have permission to install plugins.', 'beshop'));
        }
        
        $recommended_plugins = $this->get_recommended_plugins();
        $results = array();
        $success_count = 0;
        $total_count = count($recommended_plugins);
        
        foreach ($recommended_plugins as $slug => $plugin) {
            $status = $this->get_plugin_status($plugin);
            
            if ($status === 'active') {
                $results[$slug] = 'already-active';
                $success_count++;
            } else {
                $installed = $this->install_and_activate_plugin($plugin);
                
                if ($installed) {
                    $results[$slug] = 'installed-and-activated';
                    $success_count++;
                } else {
                    $results[$slug] = 'failed';
                }
            }
        }
        
        if ($success_count === $total_count) {
            wp_send_json_success(array(
                'message' => sprintf(__('%d recommended plugins installed and activated successfully.', 'beshop'), $success_count),
                'results' => $results
            ));
        } else {
            wp_send_json_error(array(
                'message' => sprintf(__('%d of %d recommended plugins installed successfully.', 'beshop'), $success_count, $total_count),
                'results' => $results
            ));
        }
    }
    
    /**
     * AJAX handler to check if all recommended plugins are active
     */
    public function ajax_check_recommended_plugins_status() {
        check_ajax_referer('beshop_plugins_nonce', 'nonce');
        
        $all_active = $this->are_all_recommended_plugins_active();
        
        wp_send_json_success(array(
            'all_active' => $all_active,
            'message' => $all_active ? __('All recommended plugins are active!', 'beshop') : __('Some recommended plugins are not active.', 'beshop')
        ));
    }
    
    /**
     * Show recommendation notice in admin
     */
    public function show_recommendation_notice() {
        if ( ! get_user_meta( get_current_user_id(), 'aime_notice_dismissed', true ) ) {
            return;
        }
        $screen = get_current_screen();
        
        // Only show on dashboard, themes, and plugins pages
        if (!in_array($screen->id, array('dashboard', 'themes', 'plugins', 'appearance_page_beshop-plugins'))) {
            return;
        }
        
        // Check if there are any missing required plugins
        $missing_required = array();
        $required_plugins = $this->get_required_plugins();
        
        foreach ($required_plugins as $slug => $plugin) {
            if ($this->get_plugin_status($plugin) !== 'active') {
                $missing_required[$slug] = $plugin;
            }
        }
        
        // Show REQUIRED plugins notice only if there are missing plugins and not dismissed
        if (!empty($missing_required)) {
            // Check if user dismissed the required plugins notice
            $dismissed_time = get_user_meta(get_current_user_id(), 'beshop_hide_install_notice_time', true);
            $show_required_notice = !$dismissed_time || (time() - $dismissed_time) >= (7 * 24 * 60 * 60); // 7 days
            
            if ($show_required_notice) {
                ?>
            <div class="notice notice-warning beshop-pro-required-notice">
                <p>
                    <strong><?php echo esc_html($this->theme_name); ?></strong> - 
                    <?php printf(esc_html__('%d required plugins are missing. Please install them for optimal theme functionality.', 'beshop'), count($missing_required)); ?>
                    <br><br>
                    <button id="install-required-plugins" class="button button-primary" style="margin-right: 10px;">
                        <?php esc_html_e('Install Required Plugins', 'beshop'); ?>
                    </button>
                    <a href="<?php echo esc_url(admin_url('themes.php?page=beshop-plugins')); ?>" class="button button-secondary">
                        <?php esc_html_e('View All Plugins', 'beshop'); ?>
                    </a>
                     <button id="install-dismiss" class="button install-dismiss" style="margin-left: 10px;">
                        <?php esc_html_e('Dismiss', 'beshop'); ?>
                    </button>
                    
                </p>
            </div>
            <?php
            }
        }
        
        // Show recommended plugins notice (dismissible) only on dashboard and themes pages
        if (in_array($screen->id, array('dashboard', 'themes', 'plugins'))) {
            $this->show_recommended_plugins_notice();
        }
    }
    
    /**
     * Show recommended plugins notice (dismissible)
     */
    private function show_recommended_plugins_notice() {
        // Check if user dismissed the recommended plugins notice
        $dismissed_time = get_user_meta(get_current_user_id(), 'beshop_info_recomend_plugins', true);
        $show_recommended_notice = !$dismissed_time || (time() - $dismissed_time) >= (30 * 24 * 60 * 60); // 30 days
        
        if (!$show_recommended_notice) {
            return;
        }
        
        // Check if all recommended plugins are active
        if ($this->are_all_recommended_plugins_active()) {
            return;
        }
        
        // Get recommended plugins that are not active
        $recommended_plugins = $this->get_recommended_plugins();
        $inactive_recommended = array();
        
        foreach ($recommended_plugins as $slug => $plugin) {
            if (!$plugin['required'] && $this->get_plugin_status($plugin) !== 'active') {
                $inactive_recommended[$slug] = $plugin;
            }
        }
        
        if (empty($inactive_recommended)) {
            return;
        }
        
        ?>
        <div class="notice notice-info is-dismissible beshop-pro-recommended-notice">
            <div class="plugin-recommended-content">
                <div style="flex: 1;">
                    <h3><?php printf(esc_html__('Recommended Plugins for %s', 'beshop'), $this->theme_name); ?></h3>
                    <p><?php printf(esc_html__('We recommend %d additional plugins to enhance your %s theme experience.', 'beshop'), count($inactive_recommended), $this->theme_name); ?></p>
                    <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap; margin-top: 15px;">
                        <button id="install-recommended-plugins" class="button button-primary"><?php esc_html_e('Install Recommended Plugins', 'beshop'); ?></button>
                        <a href="<?php echo esc_url(admin_url('themes.php?page=beshop-plugins')); ?>" class="button button-secondary"><?php esc_html_e('View All Plugins', 'beshop'); ?></a>
                        <button class="install-dismiss" class="button" style="margin-left: 10px;">
                        <?php esc_html_e('Dismiss', 'beshop'); ?>
                        </button>
                    </div>
                </div>
                <div class="plugin-icon-recomend">🔌</div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Auto-install required plugins on theme activation
     */
    public function auto_install_required_plugins() {
        // Check if we have permission to install plugins
        if (!current_user_can('install_plugins')) {
            return;
        }
        
        // Get all required plugins
        $required_plugins = $this->get_required_plugins();
        
        if (empty($required_plugins)) {
            return;
        }
        
        // Set a flag to indicate we've processed theme activation
        set_transient('beshop_theme_activated', true, 30);
        
        // Install and activate required plugins
        foreach ($required_plugins as $plugin) {
            $this->install_and_activate_plugin($plugin);
        }
        
        // Set a notice that plugins were installed
        set_transient('beshop_plugins_installed', true, 300);
    }
    
    /**
     * Check for required plugins on admin init
     */
    public function check_required_plugins() {
        // Only check if user can install plugins
        if (!current_user_can('install_plugins')) {
            return;
        }
        
        // Only check once per session to avoid performance issues
        if (get_transient('beshop_plugins_checked')) {
            return;
        }
        
        // Set transient to avoid repeated checks
        set_transient('beshop_plugins_checked', true, 3600); // 1 hour
        
        // Get required plugins
        $required_plugins = $this->get_required_plugins();
        
        if (empty($required_plugins)) {
            return;
        }
        
        // Check each required plugin
        foreach ($required_plugins as $plugin) {
            $status = $this->get_plugin_status($plugin);
            
            if ($status === 'not-installed') {
                $this->install_and_activate_plugin($plugin);
            } elseif ($status === 'inactive') {
                $this->activate_plugin_silent($plugin);
            }
        }
    }
    
    /**
     * Get only required plugins
     */
    private function get_required_plugins() {
        $all_plugins = $this->get_recommended_plugins();
        $required_plugins = array();
        
        foreach ($all_plugins as $slug => $plugin) {
            if (isset($plugin['required']) && $plugin['required'] === true) {
                $required_plugins[$slug] = $plugin;
            }
        }
        
        return $required_plugins;
    }
    
    /**
     * Install and activate a plugin silently
     */
    private function install_and_activate_plugin($plugin) {
        // Check if plugin is already active
        if ($this->get_plugin_status($plugin) === 'active') {
            return true;
        }
        
        // Install plugin if not installed
        if ($this->get_plugin_status($plugin) === 'not-installed') {
            $installed = $this->install_plugin_silent($plugin);
            if (!$installed) {
                return false;
            }
        }
        
        // Activate plugin
        return $this->activate_plugin_silent($plugin);
    }
    
    /**
     * Install plugin silently
     */
    private function install_plugin_silent($plugin) {
        try {
            // Load the silent upgrader skin class
            beshop_load_silent_upgrader_skin();
            
            // Handle local plugin installation
            if ($this->is_local_plugin($plugin)) {
                return $this->install_local_plugin($plugin);
            }
            
            // Handle WordPress.org plugin installation
            return $this->install_wordpress_org_plugin($plugin);
            
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Install local plugin from zip file
     */
    private function install_local_plugin($plugin) {
        // Basic checks
        if (!isset($plugin['source']) || !file_exists($plugin['source'])) {
            return false;
        }

        if (!class_exists('ZipArchive')) {
            // Try WordPress built-in unzip_file first
            if (!function_exists('unzip_file')) {
                require_once ABSPATH . 'wp-admin/includes/file.php';
            }
            
            if (function_exists('unzip_file')) {
                // Use WordPress plugins directory
                $wp_plugins_dir = WP_CONTENT_DIR . '/plugins';
                
                // Try direct filesystem method - bypass credentials
                global $wp_filesystem;
                if (!$wp_filesystem) {
                    require_once ABSPATH . 'wp-admin/includes/file.php';
                    // Force direct filesystem method
                    add_filter('filesystem_method', function() { return 'direct'; });
                    WP_Filesystem();
                }
                
                $unzip_result = unzip_file($plugin['source'], $wp_plugins_dir);
                
                if (!is_wp_error($unzip_result)) {
                    // Check if the plugin file exists
                    $plugin_file_path = $wp_plugins_dir . '/' . $plugin['file'];
                    if (file_exists($plugin_file_path)) {
                        return true;
                    } else {
                        return $this->locate_extracted_plugin($plugin, $wp_plugins_dir);
                    }
                }
            }
            
            // Fallback: Try PclZip (WordPress included)
            if (!class_exists('PclZip')) {
                $pclzip_path = ABSPATH . 'wp-admin/includes/class-pclzip.php';
                if (file_exists($pclzip_path)) {
                    require_once $pclzip_path;
                }
            }
            
            if (class_exists('PclZip')) {
                $wp_plugins_dir = WP_CONTENT_DIR . '/plugins';
                
                $archive = new PclZip($plugin['source']);
                $result = $archive->extract(PCLZIP_OPT_PATH, $wp_plugins_dir);
                
                if ($result !== 0) {
                    // Check if plugin file exists
                    $plugin_file_path = $wp_plugins_dir . '/' . $plugin['file'];
                    if (file_exists($plugin_file_path)) {
                        return true;
                    } else {
                        return $this->locate_extracted_plugin($plugin, $wp_plugins_dir);
                    }
                }
            }
            
            // If all extraction methods fail, return false
            return false;
        }
        
        if (!is_writable(WP_PLUGIN_DIR)) {
            return false;
        }

        $plugin_file_path = WP_PLUGIN_DIR . '/' . $plugin['file'];
        if (file_exists($plugin_file_path)) {
            return true;
        }
        
        // Use a simple direct extraction approach
        $zip = new ZipArchive();
        $open_result = $zip->open($plugin['source']);
        
        if ($open_result !== TRUE) {
            $zip_errors = array(
                ZipArchive::ER_OK => 'No error',
                ZipArchive::ER_MULTIDISK => 'Multi-disk zip archives not supported',
                ZipArchive::ER_RENAME => 'Renaming temporary file failed',
                ZipArchive::ER_CLOSE => 'Closing zip archive failed',
                ZipArchive::ER_SEEK => 'Seek error',
                ZipArchive::ER_READ => 'Read error',
                ZipArchive::ER_WRITE => 'Write error',
                ZipArchive::ER_CRC => 'CRC error',
                ZipArchive::ER_ZIPCLOSED => 'Containing zip archive was closed',
                ZipArchive::ER_NOENT => 'No such file',
                ZipArchive::ER_EXISTS => 'File already exists',
                ZipArchive::ER_OPEN => 'Can\'t open file',
                ZipArchive::ER_TMPOPEN => 'Failure to create temporary file',
                ZipArchive::ER_ZLIB => 'Zlib error',
                ZipArchive::ER_MEMORY => 'Memory allocation failure',
                ZipArchive::ER_CHANGED => 'Entry has been changed',
                ZipArchive::ER_COMPNOTSUPP => 'Compression method not supported',
                ZipArchive::ER_EOF => 'Premature EOF',
                ZipArchive::ER_INVAL => 'Invalid argument',
                ZipArchive::ER_NOZIP => 'Not a zip archive',
                ZipArchive::ER_INTERNAL => 'Internal error',
                ZipArchive::ER_INCONS => 'Zip archive inconsistent',
                ZipArchive::ER_REMOVE => 'Can\'t remove file',
                ZipArchive::ER_DELETED => 'Entry has been deleted',
            );
            $error_message = isset($zip_errors[$open_result]) ? $zip_errors[$open_result] : 'Unknown error';
            return false;
        }
        
        // Extract directly to plugins directory
        $extract_result = $zip->extractTo(WP_PLUGIN_DIR);
        $zip->close();
        
        if (!$extract_result) {
            return false;
        }
        
        // Check if the expected plugin file now exists
        if (file_exists($plugin_file_path)) {
            return true;
        }
        
        // Look for the plugin directory based on slug
        $plugin_slug = $plugin['slug'];
        $possible_dirs = array(
            WP_PLUGIN_DIR . '/' . $plugin_slug,
            WP_PLUGIN_DIR . '/' . $plugin_slug . '-master',
            WP_PLUGIN_DIR . '/' . strtolower($plugin_slug),
        );
        
        foreach ($possible_dirs as $dir) {
            if (is_dir($dir)) {
                $php_files = glob($dir . '/*.php');
                foreach ($php_files as $file) {
                    // Check if this looks like a main plugin file using WordPress function
                    if (!function_exists('get_plugin_data')) {
                        require_once ABSPATH . 'wp-admin/includes/plugin.php';
                    }
                    
                    // Use WordPress function to safely check plugin headers
                    $plugin_data = get_plugin_data($file, false, false);
                    if (!empty($plugin_data['Name'])) {
                        return true;
                    }
                }
            }
        }
        
        return false;
    }
    
    /**
     * Try to locate extracted plugin in plugins directory
     */
    private function locate_extracted_plugin($plugin, $plugin_dir) {
        $plugin_slug = $plugin['slug'];
        
        // Common directory patterns for extracted plugins
        $possible_dirs = array(
            $plugin_dir . '/' . $plugin_slug,
            $plugin_dir . '/' . $plugin_slug . '-master',
            $plugin_dir . '/' . strtolower($plugin_slug),
            $plugin_dir . '/' . ucfirst($plugin_slug),
            $plugin_dir . '/' . str_replace('-', '_', $plugin_slug),
        );
        
        foreach ($possible_dirs as $dir) {
            $expected_file = $dir . '/' . basename($plugin['file']);
            
            if (file_exists($expected_file)) {
                return true;
            }
            
            // Also check if directory exists and scan for PHP files
            if (is_dir($dir)) {
                $php_files = glob($dir . '/*.php');
                foreach ($php_files as $file) {
                    // Check if this looks like a main plugin file using WordPress function
                    if (!function_exists('get_plugin_data')) {
                        require_once ABSPATH . 'wp-admin/includes/plugin.php';
                    }
                    
                    // Use WordPress function to safely check plugin headers
                    $plugin_data = get_plugin_data($file, false, false);
                    if (!empty($plugin_data['Name'])) {
                        return true;
                    }
                }
            }
        }
        
        return false;
    }

    /**
     * Fallback manual extraction when WordPress methods fail
    }

    /**
     * Install WordPress.org plugin
     */
    private function install_wordpress_org_plugin($plugin) {
        try {
            include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
            
            // Get plugin info from WordPress.org
            $api = plugins_api('plugin_information', array(
                'slug' => $plugin['slug'],
                'fields' => array(
                    'short_description' => false,
                    'sections' => false,
                    'requires' => false,
                    'rating' => false,
                    'ratings' => false,
                    'downloaded' => false,
                    'last_updated' => false,
                    'added' => false,
                    'tags' => false,
                    'compatibility' => false,
                    'homepage' => false,
                    'donate_link' => false,
                )
            ));
            
            if (is_wp_error($api)) {
                return false;
            }
            
            // Use a silent upgrader
            $upgrader = new Plugin_Upgrader(new beshop_Pro_Silent_Upgrader_Skin());
            $install = $upgrader->install($api->download_link);
            
            return !is_wp_error($install) && $install;
            
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Update local plugin from zip file
     */
    private function update_local_plugin($plugin) {
        try {
            // Check if source file exists
            if (!isset($plugin['source']) || !file_exists($plugin['source'])) {
                return false;
            }
            
            // Deactivate plugin before update
            if (is_plugin_active($plugin['file'])) {
                deactivate_plugins($plugin['file'], true);
            }
            
            // Get plugin directory
            $plugin_dir = WP_PLUGIN_DIR . '/' . dirname($plugin['file']);
            
            // Remove existing plugin directory
            if (is_dir($plugin_dir)) {
                $this->delete_directory($plugin_dir);
            }
            
            // Extract new plugin
            $result = $this->extract_plugin_zip($plugin['source'], WP_PLUGIN_DIR);
            
            if ($result) {
                // Reactivate plugin
                activate_plugin($plugin['file'], '', false, true);
                return true;
            }
            
            return false;
            
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Update WordPress.org plugin
     */
    private function update_wordpress_org_plugin($plugin_file) {
        try {
            include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
            
            // Load the silent upgrader skin class
            beshop_load_silent_upgrader_skin();
            
            $upgrader = new Plugin_Upgrader(new beshop_Pro_Silent_Upgrader_Skin());
            $result = $upgrader->upgrade($plugin_file);
            
            return !is_wp_error($result) && $result;
            
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Activate plugin silently
     */
    private function activate_plugin_silent($plugin) {
        try {
            $result = activate_plugin($plugin['file'], '', false, true);
            return !is_wp_error($result);
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Extract plugin zip file
     */
    private function extract_plugin_zip($zip_file, $destination) {
        if (!class_exists('ZipArchive')) {
            return false;
        }
        
        if (!file_exists($zip_file) || !is_readable($zip_file)) {
            return false;
        }
        
        $zip = new ZipArchive();
        $result = $zip->open($zip_file);
        
        if ($result !== TRUE) {
            return false;
        }
        
        $extract_result = $zip->extractTo($destination);
        $zip->close();
        
        return $extract_result;
    }
    
    /**
     * Delete directory recursively
     */
    private function delete_directory($dir) {
        if (!is_dir($dir)) {
            return false;
        }
        
        $files = array_diff(scandir($dir), array('.', '..'));
        
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            if (is_dir($path)) {
                $this->delete_directory($path);
            } else {
                unlink($path);
            }
        }
        
        return rmdir($dir);
    }
    
    /**
     * Check if plugin installation/activation functions are available
     */
    private function check_plugin_functions() {
        if (!function_exists('is_plugin_active')) {
            include_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
    }
    
    /**
     * Migrate old notice dismissal to new system
     */
    private function migrate_notice_dismissal() {
        // Check if migration has already been done
        if (get_option('beshop_notice_migration_done')) {
            return;
        }
        
        // Reset old dismissals for migration
        delete_user_meta(get_current_user_id(), 'beshop_hide_required_plugins_notice');
        delete_user_meta(get_current_user_id(), 'beshop_hide_recommended_plugins_notice');
        delete_user_meta(get_current_user_id(), 'beshop_hide_update_notice_time');
        
        // Mark migration as done
        update_option('beshop_notice_migration_done', true);
    }
    
    /**
     * Check if all recommended plugins are active
     */
    private function are_all_recommended_plugins_active() {
        $all_plugins = $this->get_recommended_plugins();
        
        foreach ($all_plugins as $plugin) {
            if ($this->get_plugin_status($plugin) !== 'active') {
                return false;
            }
        }
        
        return true;
    }
    
}

// Initialize the plugin recommendations
new beshop_Pro_Plugin_Recommendations();

