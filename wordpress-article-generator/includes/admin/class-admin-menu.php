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
            
            <div class="notice notice-info">
                <p><strong><?php _e('API Configuration', 'wp-article-generator'); ?></strong></p>
                <p><?php _e('This plugin uses OpenAI API for article generation. The API key is configured in the .env file.', 'wp-article-generator'); ?></p>
                <p><?php _e('Model: gpt-4o-latest', 'wp-article-generator'); ?></p>
            </div>
            
            <form method="post" action="options.php">
                <?php
                settings_fields('wpag_settings_group');
                do_settings_sections('wpag_settings');
                submit_button();
                ?>
            </form>
            
            <div class="wpag-settings-info">
                <h2><?php _e('Usage Instructions', 'wp-article-generator'); ?></h2>
                <ol>
                    <li><?php _e('Navigate to "Article Generator" in the admin menu', 'wp-article-generator'); ?></li>
                    <li><?php _e('Enter a title and optional keywords for your article', 'wp-article-generator'); ?></li>
                    <li><?php _e('Select the desired article length and tone', 'wp-article-generator'); ?></li>
                    <li><?php _e('Click "Generate Article" to create content using AI', 'wp-article-generator'); ?></li>
                    <li><?php _e('Review and edit the generated content as needed', 'wp-article-generator'); ?></li>
                    <li><?php _e('Save as draft or publish immediately', 'wp-article-generator'); ?></li>
                </ol>
            </div>
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