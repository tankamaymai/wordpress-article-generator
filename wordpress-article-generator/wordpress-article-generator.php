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
define('WPAG_VERSION', '1.0.0');
define('WPAG_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WPAG_PLUGIN_URL', plugin_dir_url(__FILE__));
define('WPAG_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Load required files
require_once WPAG_PLUGIN_DIR . 'includes/class-article-generator.php';
require_once WPAG_PLUGIN_DIR . 'includes/admin/class-admin-menu.php';
require_once WPAG_PLUGIN_DIR . 'includes/admin/class-generator-page.php';

// Initialize the plugin
function wpag_init() {
    // Load text domain for translations
    load_plugin_textdomain('wp-article-generator', false, dirname(WPAG_PLUGIN_BASENAME) . '/languages');
    
    // Initialize main plugin class
    $article_generator = new WPAG_Article_Generator();
    $article_generator->init();
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