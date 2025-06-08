/**
 * WordPress Article Generator Admin JavaScript
 */

jQuery(document).ready(function($) {
    'use strict';
    
    // Generate button click handler
    $('#wpag-generate-btn').on('click', function() {
        var $button = $(this);
        var $spinner = $button.next('.spinner');
        var $form = $('#wpag-generator-form');
        
        // Validate form
        if (!$form[0].checkValidity()) {
            $form[0].reportValidity();
            return;
        }
        
        // Show spinner and disable button
        $spinner.addClass('is-active');
        $button.prop('disabled', true).text(wpag_ajax.generating_text);
        
        // Collect form data
        var data = {
            action: 'wpag_generate_article',
            nonce: wpag_ajax.generate_nonce,
            title: $('#wpag-title').val(),
            keywords: $('#wpag-keywords').val(),
            length: $('#wpag-length').val(),
            tone: $('#wpag-tone').val()
        };
        
        // Send AJAX request
        $.post(wpag_ajax.ajax_url, data, function(response) {
            $spinner.removeClass('is-active');
            $button.prop('disabled', false).text($button.data('original-text') || 'Generate Article');
            
            if (response.success) {
                // Display generated article
                displayGeneratedArticle(response.data);
            } else {
                // Show more detailed error message
                var errorMsg = 'Error generating article: ';
                if (typeof response.data === 'string') {
                    errorMsg += response.data;
                } else if (response.data && response.data.message) {
                    errorMsg += response.data.message;
                } else {
                    errorMsg += wpag_ajax.error_text;
                }
                alert(errorMsg);
            }
        }).fail(function() {
            $spinner.removeClass('is-active');
            $button.prop('disabled', false).text($button.data('original-text') || 'Generate Article');
            alert(wpag_ajax.error_text);
        });
    });
    
    // Display generated article
    function displayGeneratedArticle(article) {
        $('#wpag-preview-title').val(article.title);
        $('#wpag-preview-excerpt').val(article.excerpt);
        $('#wpag-preview-content').html(article.content);
        $('#wpag-preview-tags').val(article.tags ? article.tags.join(', ') : '');
        
        // Show preview section
        $('#wpag-preview').slideDown();
        
        // Scroll to preview
        $('html, body').animate({
            scrollTop: $('#wpag-preview').offset().top - 50
        }, 500);
    }
    
    // Save button click handler
    $('#wpag-save-btn').on('click', function() {
        var $button = $(this);
        var $spinner = $button.nextAll('.spinner').first();
        
        // Show spinner and disable button
        $spinner.addClass('is-active');
        $button.prop('disabled', true).text(wpag_ajax.saving_text);
        
        // Collect data
        var data = {
            action: 'wpag_save_article',
            nonce: wpag_ajax.save_nonce,
            title: $('#wpag-preview-title').val(),
            content: $('#wpag-preview-content').html(),
            excerpt: $('#wpag-preview-excerpt').val(),
            tags: $('#wpag-preview-tags').val().split(',').map(function(tag) {
                return tag.trim();
            }).filter(function(tag) {
                return tag.length > 0;
            }),
            post_status: $('input[name="post_status"]:checked').val()
        };
        
        // Send AJAX request
        $.post(wpag_ajax.ajax_url, data, function(response) {
            $spinner.removeClass('is-active');
            $button.prop('disabled', false).text('Save Article');
            
            if (response.success) {
                // Show success message
                var $result = $('#wpag-save-result');
                $result.removeClass('notice-error').addClass('notice notice-success');
                $result.find('p').html(
                    'Article saved successfully! ' +
                    '<a href="' + response.data.edit_link + '">Edit</a> | ' +
                    '<a href="' + response.data.view_link + '" target="_blank">View</a>'
                );
                $result.slideDown();
                
                // Scroll to result
                $('html, body').animate({
                    scrollTop: $result.offset().top - 50
                }, 500);
            } else {
                // Show error message
                var $result = $('#wpag-save-result');
                $result.removeClass('notice-success').addClass('notice notice-error');
                $result.find('p').text(response.data || wpag_ajax.error_text);
                $result.slideDown();
            }
        }).fail(function() {
            $spinner.removeClass('is-active');
            $button.prop('disabled', false).text('Save Article');
            
            var $result = $('#wpag-save-result');
            $result.removeClass('notice-success').addClass('notice notice-error');
            $result.find('p').text(wpag_ajax.error_text);
            $result.slideDown();
        });
    });
    
    // Regenerate button click handler
    $('#wpag-regenerate-btn').on('click', function() {
        // Hide preview and result
        $('#wpag-preview').slideUp();
        $('#wpag-save-result').slideUp();
        
        // Clear form
        $('#wpag-generator-form')[0].reset();
        
        // Scroll to form
        $('html, body').animate({
            scrollTop: $('.wpag-generator-form').offset().top - 50
        }, 500);
    });
    
    // Store original button text
    $('#wpag-generate-btn').data('original-text', $('#wpag-generate-btn').text());
});