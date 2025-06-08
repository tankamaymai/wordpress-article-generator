<?php
/**
 * OpenAI API Client Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class WPAG_OpenAI_Client {
    
    private $api_key;
    private $api_url = 'https://api.openai.com/v1/chat/completions';
    private $model = 'gpt-4o-latest';
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->api_key = isset($_ENV['OPENAI_API_KEY']) ? $_ENV['OPENAI_API_KEY'] : '';
    }
    
    /**
     * Generate article using OpenAI API
     */
    public function generate_article($title, $keywords, $length, $tone) {
        if (empty($this->api_key)) {
            if (class_exists('WP_Error')) {
                return new WP_Error('no_api_key', 'OpenAI API key is not configured');
            }
            return array(
                'error' => true,
                'message' => 'OpenAI API key is not configured'
            );
        }
        
        // Create the prompt
        $prompt = $this->create_prompt($title, $keywords, $length, $tone);
        
        // Prepare API request
        $request_data = array(
            'model' => $this->model,
            'messages' => array(
                array(
                    'role' => 'system',
                    'content' => 'You are a professional content writer. Create engaging, SEO-optimized articles in HTML format. Use proper heading tags (h2, h3), paragraph tags, and lists where appropriate.'
                ),
                array(
                    'role' => 'user',
                    'content' => $prompt
                )
            ),
            'temperature' => 0.7,
            'max_tokens' => $this->get_max_tokens($length)
        );
        
        // Make API request
        if (!function_exists('wp_remote_post')) {
            return array(
                'error' => true,
                'message' => 'WordPress HTTP API not available'
            );
        }
        
        $response = wp_remote_post($this->api_url, array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $this->api_key,
                'Content-Type' => 'application/json'
            ),
            'body' => json_encode($request_data),
            'timeout' => 60
        ));
        
        // Handle response
        if (function_exists('is_wp_error') && is_wp_error($response)) {
            return $response;
        }
        
        $response_code = function_exists('wp_remote_retrieve_response_code') ? wp_remote_retrieve_response_code($response) : 200;
        $body = function_exists('wp_remote_retrieve_body') ? wp_remote_retrieve_body($response) : '';
        $data = json_decode($body, true);
        
        // Handle rate limiting
        if ($response_code === 429) {
            if (class_exists('WP_Error')) {
                return new WP_Error('rate_limit', 'API rate limit exceeded. Please try again later.');
            }
            return array('error' => true, 'message' => 'API rate limit exceeded. Please try again later.');
        }
        
        if (isset($data['error'])) {
            $error_message = isset($data['error']['message']) ? $data['error']['message'] : 'Unknown API error';
            if (class_exists('WP_Error')) {
                return new WP_Error('api_error', $error_message);
            }
            return array('error' => true, 'message' => $error_message);
        }
        
        if (!isset($data['choices'][0]['message']['content'])) {
            if (class_exists('WP_Error')) {
                return new WP_Error('invalid_response', 'Invalid response from OpenAI API');
            }
            return array('error' => true, 'message' => 'Invalid response from OpenAI API');
        }
        
        $content = $data['choices'][0]['message']['content'];
        
        // Generate excerpt
        $excerpt = $this->generate_excerpt($content);
        
        // Extract keywords as tags
        $tags = $this->extract_tags($keywords, $content);
        
        return array(
            'title' => $title,
            'content' => $content,
            'excerpt' => $excerpt,
            'tags' => $tags
        );
    }
    
    /**
     * Create prompt for OpenAI
     */
    private function create_prompt($title, $keywords, $length, $tone) {
        $word_count = $this->get_word_count($length);
        
        $tone_descriptions = array(
            'professional' => 'professional and authoritative',
            'casual' => 'casual and conversational',
            'informative' => 'informative and educational',
            'persuasive' => 'persuasive and compelling'
        );
        
        $tone_desc = isset($tone_descriptions[$tone]) ? $tone_descriptions[$tone] : 'professional';
        
        $prompt = "Write a {$word_count}-word article about '{$title}' in a {$tone_desc} tone.\n\n";
        
        if (!empty($keywords)) {
            $prompt .= "Include these keywords naturally throughout the article: {$keywords}\n\n";
        }
        
        $prompt .= "Structure the article with:\n";
        $prompt .= "- An engaging introduction\n";
        $prompt .= "- Clear sections with H2 headings\n";
        $prompt .= "- Informative body paragraphs\n";
        $prompt .= "- A compelling conclusion\n\n";
        $prompt .= "Format the output in clean HTML using only <h2>, <h3>, <p>, <ul>, <ol>, and <li> tags.";
        
        return $prompt;
    }
    
    /**
     * Get word count based on length setting
     */
    private function get_word_count($length) {
        $word_counts = array(
            300 => '300',
            500 => '500',
            1000 => '1000',
            1500 => '1500-2000'
        );
        
        return isset($word_counts[$length]) ? $word_counts[$length] : '500';
    }
    
    /**
     * Get max tokens based on length
     */
    private function get_max_tokens($length) {
        // Rough estimate: 1 token ≈ 0.75 words
        $token_counts = array(
            300 => 500,
            500 => 800,
            1000 => 1500,
            1500 => 2500
        );
        
        return isset($token_counts[$length]) ? $token_counts[$length] : 800;
    }
    
    /**
     * Generate excerpt from content
     */
    private function generate_excerpt($content) {
        // Strip HTML tags
        $text = strip_tags($content);
        
        // Get first 150 characters
        $excerpt = substr($text, 0, 150);
        
        // Find last complete word
        $last_space = strrpos($excerpt, ' ');
        if ($last_space !== false) {
            $excerpt = substr($excerpt, 0, $last_space);
        }
        
        return $excerpt . '...';
    }
    
    /**
     * Extract tags from keywords and content
     */
    private function extract_tags($keywords, $content) {
        $tags = array();
        
        // Add provided keywords as tags
        if (!empty($keywords)) {
            $keyword_array = explode(',', $keywords);
            foreach ($keyword_array as $keyword) {
                $tag = trim($keyword);
                if (!empty($tag)) {
                    $tags[] = $tag;
                }
            }
        }
        
        // Limit to 10 tags
        return array_slice($tags, 0, 10);
    }
}