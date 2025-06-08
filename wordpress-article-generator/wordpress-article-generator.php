<?php
/**
 * Plugin Name: WordPress Article Generator
 * Plugin URI: https://example.com/wordpress-article-generator
 * Description: AI-powered article generator for WordPress
 * Version: 1.0.0
 * Author: Your Name
 * License: GPL v2 or later
 * Text Domain: wp-article-generator
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
if (!defined('WPAG_VERSION')) {
    define('WPAG_VERSION', '1.0.0');
}
if (!defined('WPAG_PLUGIN_DIR')) {
    define('WPAG_PLUGIN_DIR', plugin_dir_path(__FILE__));
}
if (!defined('WPAG_PLUGIN_URL')) {
    define('WPAG_PLUGIN_URL', plugin_dir_url(__FILE__));
}
if (!defined('WPAG_PLUGIN_BASENAME')) {
    define('WPAG_PLUGIN_BASENAME', plugin_basename(__FILE__));
}

// Initialize the plugin
class WPAG_Plugin {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->load_dependencies();
        $this->init_hooks();
    }
    
    private function load_dependencies() {
        // Load bootstrap for error checking
        require_once WPAG_PLUGIN_DIR . 'includes/bootstrap.php';
        
        // Check if we can safely load
        if (!wpag_safe_init()) {
            return;
        }
        
        // Load environment variables
        $this->load_env_file();
        
        // Load required files
        require_once WPAG_PLUGIN_DIR . 'includes/class-openai-client.php';
        require_once WPAG_PLUGIN_DIR . 'includes/class-article-generator.php';
        require_once WPAG_PLUGIN_DIR . 'includes/admin/class-admin-menu.php';
        require_once WPAG_PLUGIN_DIR . 'includes/admin/class-generator-page.php';
    }
    
    private function load_env_file() {
        $env_file = WPAG_PLUGIN_DIR . '.env';
        if (file_exists($env_file)) {
            $env_content = file_get_contents($env_file);
            $lines = explode("\n", $env_content);
            foreach ($lines as $line) {
                $line = trim($line);
                if (!empty($line) && strpos($line, '=') !== false) {
                    $parts = explode('=', $line, 2);
                    if (count($parts) == 2) {
                        $_ENV[trim($parts[0])] = trim($parts[1]);
                    }
                }
            }
        }
    }
    
    private function init_hooks() {
        add_action('init', array($this, 'load_textdomain'));
        add_action('init', array($this, 'init_plugin'));
    }
    
    public function load_textdomain() {
        load_plugin_textdomain('wp-article-generator', false, dirname(WPAG_PLUGIN_BASENAME) . '/languages');
    }
    
    public function init_plugin() {
        // Initialize main plugin class
        if (class_exists('WPAG_Article_Generator')) {
            $article_generator = new WPAG_Article_Generator();
            $article_generator->init();
        }
    }
}

// Initialize plugin
function wpag_init() {
    return WPAG_Plugin::get_instance();
}
add_action('plugins_loaded', 'wpag_init');

// Activation hook
register_activation_hook(__FILE__, 'wpag_activate');
function wpag_activate() {
    // Create database tables if needed
    // Set default options
    add_option('wpag_settings', array(
        'default_post_status' => 'draft',
        'default_post_type' => 'post',
        'enable_featured_image' => true,
    ));
}

// Deactivation hook
register_deactivation_hook(__FILE__, 'wpag_deactivate');
function wpag_deactivate() {
    // Clean up temporary data
}