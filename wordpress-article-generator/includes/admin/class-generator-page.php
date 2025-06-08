<?php
/**
 * Generator Page Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class WPAG_Generator_Page {
    
    /**
     * Render the generator page
     */
    public function render() {
        ?>
        <div class="wrap">
            <h1><?php _e('Article Generator', 'wp-article-generator'); ?></h1>
            
            <div class="wpag-container">
                <div class="wpag-generator-form">
                    <h2><?php _e('Generate New Article', 'wp-article-generator'); ?></h2>
                    
                    <form id="wpag-generator-form">
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label for="wpag-title"><?php _e('Article Title', 'wp-article-generator'); ?></label>
                                </th>
                                <td>
                                    <input type="text" id="wpag-title" name="title" class="regular-text" required />
                                    <p class="description"><?php _e('Enter the main topic or title for your article', 'wp-article-generator'); ?></p>
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row">
                                    <label for="wpag-keywords"><?php _e('Keywords', 'wp-article-generator'); ?></label>
                                </th>
                                <td>
                                    <input type="text" id="wpag-keywords" name="keywords" class="regular-text" />
                                    <p class="description"><?php _e('Comma-separated keywords to include in the article', 'wp-article-generator'); ?></p>
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row">
                                    <label for="wpag-length"><?php _e('Article Length', 'wp-article-generator'); ?></label>
                                </th>
                                <td>
                                    <select id="wpag-length" name="length">
                                        <option value="300"><?php _e('Short (300 words)', 'wp-article-generator'); ?></option>
                                        <option value="500" selected><?php _e('Medium (500 words)', 'wp-article-generator'); ?></option>
                                        <option value="1000"><?php _e('Long (1000 words)', 'wp-article-generator'); ?></option>
                                        <option value="1500"><?php _e('Very Long (1500+ words)', 'wp-article-generator'); ?></option>
                                    </select>
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row">
                                    <label for="wpag-tone"><?php _e('Writing Tone', 'wp-article-generator'); ?></label>
                                </th>
                                <td>
                                    <select id="wpag-tone" name="tone">
                                        <option value="professional"><?php _e('Professional', 'wp-article-generator'); ?></option>
                                        <option value="casual"><?php _e('Casual', 'wp-article-generator'); ?></option>
                                        <option value="informative"><?php _e('Informative', 'wp-article-generator'); ?></option>
                                        <option value="persuasive"><?php _e('Persuasive', 'wp-article-generator'); ?></option>
                                    </select>
                                </td>
                            </tr>
                        </table>
                        
                        <p class="submit">
                            <button type="button" id="wpag-generate-btn" class="button button-primary">
                                <?php _e('Generate Article', 'wp-article-generator'); ?>
                            </button>
                            <span class="spinner"></span>
                        </p>
                    </form>
                </div>
                
                <div id="wpag-preview" class="wpag-preview" style="display: none;">
                    <h2><?php _e('Generated Article Preview', 'wp-article-generator'); ?></h2>
                    
                    <div class="wpag-preview-content">
                        <div class="wpag-preview-title">
                            <h3><?php _e('Title', 'wp-article-generator'); ?></h3>
                            <input type="text" id="wpag-preview-title" class="large-text" />
                        </div>
                        
                        <div class="wpag-preview-excerpt">
                            <h3><?php _e('Excerpt', 'wp-article-generator'); ?></h3>
                            <textarea id="wpag-preview-excerpt" class="large-text" rows="3"></textarea>
                        </div>
                        
                        <div class="wpag-preview-body">
                            <h3><?php _e('Content', 'wp-article-generator'); ?></h3>
                            <div id="wpag-preview-content" class="wpag-content-editor" contenteditable="true"></div>
                        </div>
                        
                        <div class="wpag-preview-tags">
                            <h3><?php _e('Tags', 'wp-article-generator'); ?></h3>
                            <input type="text" id="wpag-preview-tags" class="large-text" />
                        </div>
                        
                        <div class="wpag-save-options">
                            <h3><?php _e('Save Options', 'wp-article-generator'); ?></h3>
                            <label>
                                <input type="radio" name="post_status" value="draft" checked />
                                <?php _e('Save as Draft', 'wp-article-generator'); ?>
                            </label>
                            <label>
                                <input type="radio" name="post_status" value="publish" />
                                <?php _e('Publish Immediately', 'wp-article-generator'); ?>
                            </label>
                        </div>
                        
                        <p class="submit">
                            <button type="button" id="wpag-save-btn" class="button button-primary">
                                <?php _e('Save Article', 'wp-article-generator'); ?>
                            </button>
                            <button type="button" id="wpag-regenerate-btn" class="button">
                                <?php _e('Generate New', 'wp-article-generator'); ?>
                            </button>
                            <span class="spinner"></span>
                        </p>
                        
                        <div id="wpag-save-result" class="notice" style="display: none;">
                            <p></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}