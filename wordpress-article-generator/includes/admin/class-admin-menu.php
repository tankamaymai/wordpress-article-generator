<?php
/**
 * Admin Menu Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class WPAG_Admin_Menu {
    
    /**
     * Initialize admin menu
     */
    public function init() {
        add_action('admin_menu', array($this, 'add_menu_pages'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
    }
    
    /**
     * Add menu pages
     */
    public function add_menu_pages() {
        // Main menu
        add_menu_page(
            __('Article Generator', 'wp-article-generator'),
            __('Article Generator', 'wp-article-generator'),
            'edit_posts',
            'wpag-generator',
            array($this, 'render_generator_page'),
            'dashicons-edit-page',
            30
        );
        
        // Settings submenu
        add_submenu_page(
            'wpag-generator',
            __('Settings', 'wp-article-generator'),
            __('Settings', 'wp-article-generator'),
            'manage_options',
            'wpag-settings',
            array($this, 'render_settings_page')
        );
    }
    
    /**
     * Render generator page
     */
    public function render_generator_page() {
        $generator_page = new WPAG_Generator_Page();
        $generator_page->render();
    }
    
    /**
     * Render settings page
     */
    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('wpag_settings_group');
                do_settings_sections('wpag_settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
    
    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook) {
        if (strpos($hook, 'wpag-generator') === false) {
            return;
        }
        
        // Enqueue CSS
        wp_enqueue_style(
            'wpag-admin',
            WPAG_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            WPAG_VERSION
        );
        
        // Enqueue JavaScript
        wp_enqueue_script(
            'wpag-admin',
            WPAG_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery'),
            WPAG_VERSION,
            true
        );
        
        // Localize script
        wp_localize_script('wpag-admin', 'wpag_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'generate_nonce' => wp_create_nonce('wpag_generate_nonce'),
            'save_nonce' => wp_create_nonce('wpag_save_nonce'),
            'generating_text' => __('Generating article...', 'wp-article-generator'),
            'saving_text' => __('Saving article...', 'wp-article-generator'),
            'error_text' => __('An error occurred. Please try again.', 'wp-article-generator'),
        ));
    }
}