<?php
/**
 * Bootstrap file for better error handling
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Check if WordPress functions are available
 */
function wpag_check_wordpress_functions() {
    $required_functions = array(
        'add_action',
        'is_admin',
        'wp_remote_post',
        'sanitize_text_field',
        'wp_send_json_success',
        'wp_insert_post'
    );
    
    $missing = array();
    foreach ($required_functions as $func) {
        if (!function_exists($func)) {
            $missing[] = $func;
        }
    }
    
    return $missing;
}

/**
 * Safe plugin initialization
 */
function wpag_safe_init() {
    $missing = wpag_check_wordpress_functions();
    
    if (!empty($missing)) {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('WordPress Article Generator: Missing functions - ' . implode(', ', $missing));
        }
        return false;
    }
    
    return true;
}