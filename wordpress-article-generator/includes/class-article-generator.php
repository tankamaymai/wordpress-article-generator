<?php
/**
 * Main Article Generator Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class WPAG_Article_Generator {
    
    /**
     * Initialize the plugin
     */
    public function init() {
        // Initialize admin functionality
        if (is_admin()) {
            $admin_menu = new WPAG_Admin_Menu();
            $admin_menu->init();
        }
        
        // Add AJAX handlers
        add_action('wp_ajax_wpag_generate_article', array($this, 'ajax_generate_article'));
        add_action('wp_ajax_wpag_save_article', array($this, 'ajax_save_article'));
    }
    
    /**
     * Generate article content
     */
    public function generate_article($params) {
        $title = sanitize_text_field($params['title']);
        $keywords = sanitize_text_field($params['keywords']);
        $length = intval($params['length']);
        $tone = sanitize_text_field($params['tone']);
        
        // Here you would integrate with an AI API (OpenAI, Claude, etc.)
        // For now, we'll return a sample response
        $article = array(
            'title' => $title,
            'content' => $this->generate_sample_content($title, $keywords, $length),
            'excerpt' => $this->generate_sample_excerpt($title),
            'tags' => explode(',', $keywords),
        );
        
        return $article;
    }
    
    /**
     * Generate sample content (placeholder)
     */
    private function generate_sample_content($title, $keywords, $length) {
        $content = "<p>This is a sample article about {$title}.</p>\n\n";
        $content .= "<h2>Introduction</h2>\n";
        $content .= "<p>Welcome to this comprehensive guide about {$title}. ";
        $content .= "This article covers important aspects related to {$keywords}.</p>\n\n";
        
        if ($length >= 500) {
            $content .= "<h2>Main Content</h2>\n";
            $content .= "<p>Here we dive deeper into the subject. ";
            $content .= "The main topics we'll cover include various aspects of {$keywords}.</p>\n\n";
        }
        
        if ($length >= 1000) {
            $content .= "<h2>Advanced Topics</h2>\n";
            $content .= "<p>For those looking to go beyond the basics, ";
            $content .= "this section explores advanced concepts related to {$title}.</p>\n\n";
        }
        
        $content .= "<h2>Conclusion</h2>\n";
        $content .= "<p>In conclusion, {$title} is an important topic that deserves attention. ";
        $content .= "We hope this article has provided valuable insights.</p>";
        
        return $content;
    }
    
    /**
     * Generate sample excerpt
     */
    private function generate_sample_excerpt($title) {
        return "This article provides a comprehensive overview of {$title}, covering key concepts and practical applications.";
    }
    
    /**
     * AJAX handler for article generation
     */
    public function ajax_generate_article() {
        check_ajax_referer('wpag_generate_nonce', 'nonce');
        
        if (!current_user_can('edit_posts')) {
            wp_die('Unauthorized');
        }
        
        $params = array(
            'title' => $_POST['title'],
            'keywords' => $_POST['keywords'],
            'length' => $_POST['length'],
            'tone' => $_POST['tone'],
        );
        
        $article = $this->generate_article($params);
        
        wp_send_json_success($article);
    }
    
    /**
     * AJAX handler for saving article
     */
    public function ajax_save_article() {
        check_ajax_referer('wpag_save_nonce', 'nonce');
        
        if (!current_user_can('edit_posts')) {
            wp_die('Unauthorized');
        }
        
        $post_data = array(
            'post_title' => sanitize_text_field($_POST['title']),
            'post_content' => wp_kses_post($_POST['content']),
            'post_excerpt' => sanitize_text_field($_POST['excerpt']),
            'post_status' => sanitize_text_field($_POST['post_status']),
            'post_type' => 'post',
        );
        
        $post_id = wp_insert_post($post_data);
        
        if ($post_id && !is_wp_error($post_id)) {
            // Add tags
            if (!empty($_POST['tags'])) {
                $tags = array_map('sanitize_text_field', $_POST['tags']);
                wp_set_post_tags($post_id, $tags);
            }
            
            wp_send_json_success(array(
                'post_id' => $post_id,
                'edit_link' => get_edit_post_link($post_id),
                'view_link' => get_permalink($post_id),
            ));
        } else {
            wp_send_json_error('Failed to create post');
        }
    }
}