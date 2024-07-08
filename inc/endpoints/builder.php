<?php
/**
 * Ajax functions for post content management.
 *
 * @package AgnosticTheme
 */

/**
 * Retrieve post content by ID via Ajax.
 *
 * @return void
 */
function agnostic_get_post_content()
{
    // Check for valid post ID.
    if (!isset($_POST['post_id']) || !is_numeric($_POST['post_id'])) {
        wp_send_json_error('Invalid post ID');
    }

    $post_id = intval($_POST['post_id']);
    $post = get_post($post_id);

    // Check if post exists.
    if (!$post) {
        wp_send_json_error('Post not found');
    }

    // Apply content filters and return.
    // $content = apply_filters('the_content', $post->post_content);
    $content = $post->post_content;
    wp_send_json_success(array('content' => $content));
}
add_action('wp_ajax_agnostic_get_post_content', 'agnostic_get_post_content');
add_action('wp_ajax_nopriv_agnostic_get_post_content', 'agnostic_get_post_content');

/**
 * Save post content via Ajax.
 *
 * @return void
 */
function agnostic_save_post_content()
{
    // Check for valid input data.
    if (!isset($_POST['post_id']) || !is_numeric($_POST['post_id']) || !isset($_POST['content'])) {
        wp_send_json_error('Invalid data');
    }

    $post_id = intval($_POST['post_id']);
    $content = $_POST['content'];

    $updated_post = array(
        'ID' => $post_id,
        'post_content' => $content,
    );

    // Attempt to update the post.
    $result = wp_update_post($updated_post);

    if (is_wp_error($result)) {
        wp_send_json_error('Failed to update post');
    }

    wp_send_json_success(array('message' => 'Post updated successfully'));
}
add_action('wp_ajax_agnostic_save_post_content', 'agnostic_save_post_content');

/**
 * Process HTML content with Twig templating.
 *
 * This function handles AJAX requests to process HTML content using Twig templating.
 * It can optionally render a full HTML page structure.
 */
function agnostic_process_html()
{
    // Check user capabilities
    if (!current_user_can('edit_pages')) {
        wp_send_json_error('You don\'t have permission to perform this action.');
    }

    // Define constant for dynamic template rendering
    define('LC_DOING_DYNAMIC_TEMPLATE_TWIG_RENDERING', 1);

    try {
        // Sanitize and retrieve POST data
        $input = isset($_POST['html']) ? wp_kses_post(stripslashes($_POST['html'])) : '';
        $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
        $settings = isset($_POST['settings']) ? json_decode(stripslashes($_POST['settings']), true) : array();
        $full_render = isset($_POST['FULL_RENDER']) && 'true' === $_POST['FULL_RENDER'];

        // Set up global post data
        global $post;
        $post = get_post($post_id);

        // Determine template type
        $post_meta = get_post_meta($post_id);
        foreach ($post_meta as $meta_key => $meta_value) {
            if ('is_' === substr($meta_key, 0, 3) && 1 == $meta_value[0]) {
                break;
            }
        }

        // Build shortcode attributes
        $shortcode_attributes = array();
        $attribute_keys = array('content_type', 'selection_type', 'single_id', 'search');
        foreach ($attribute_keys as $key) {
            if (isset($settings[$key]) && null !== $settings[$key]) {
                $shortcode_attributes[$key] = sanitize_text_field($settings[$key]);
            }
        }

        // Construct Twig shortcode
        $twig_shortcode = '[twig trigger="process"';
        foreach ($shortcode_attributes as $attribute => $value) {
            $twig_shortcode .= ' ' . $attribute . '="' . esc_attr($value) . '"';
        }
        $twig_shortcode .= ']' . $input . '<script
          type="application/json"
          id="state-data"
        >
          {{ function("json_encode", state, constant("JSON_PRETTY_PRINT")) | raw }}
        </script>[/twig]';

        // Process the Twig shortcode
        $output = do_shortcode($twig_shortcode);

        $context = Timber::context();
        $context['template'] = $output;

        Timber::render('render/base.twig', $context);

    } catch (Exception $e) {
        error_log('Error in agnostic_process_html: ' . $e->getMessage());
        wp_send_json_error('Error processing dynamic template');
    }
}
add_action('wp_ajax_agnostic_process_html', 'agnostic_process_html');
add_action('wp_ajax_nopriv_agnostic_process_html', 'agnostic_process_html');

// Document this TODO
function agnostic_preview_template_redirect()
{
    if (isset($_GET['agnostic']) && $_GET['agnostic'] === 'preview') {
        // Check user capabilities
        if (!current_user_can('edit_pages')) {
            wp_die('You don\'t have permission to perform this action.');
        }

        // Define constant for dynamic template rendering
        define('LC_DOING_DYNAMIC_TEMPLATE_TWIG_RENDERING', 1);

        try {
            global $post;

            if (isset($_GET['template_id'])) {
                $template_id = intval($_GET['template_id']);
                $template_post = get_post($template_id);

                if (!$template_post) {
                    wp_die('Invalid template ID');
                }

                $post = $template_post;
            } else {
                // Use the current page if no template_id is provided
                if (!$post) {
                    wp_die('No valid post found');
                }
                $template_id = $post->ID;
            }

            // Determine template type
            $post_meta = get_post_meta($template_id);
            foreach ($post_meta as $meta_key => $meta_value) {
                if ('is_' === substr($meta_key, 0, 3) && 1 == $meta_value[0]) {
                    break;
                }
            }

            // Get settings from post meta if available
            $settings = get_post_meta($template_id, 'agnostic_settings', true);
            if (!$settings) {
                $settings = array();
            }

            // Build shortcode attributes
            $shortcode_attributes = array();
            $attribute_keys = array('content_type', 'selection_type', 'single_id', 'search');
            foreach ($attribute_keys as $key) {
                if (isset($settings[$key]) && null !== $settings[$key]) {
                    $shortcode_attributes[$key] = sanitize_text_field($settings[$key]);
                }
            }

            // Construct Twig shortcode
            $twig_shortcode = '[twig trigger="process"';
            foreach ($shortcode_attributes as $attribute => $value) {
                $twig_shortcode .= ' ' . $attribute . '="' . esc_attr($value) . '"';
            }
            $twig_shortcode .= ']' . $post->post_content . '<script
              type="application/json"
              id="state-data"
            >
              {{ function("json_encode", state, constant("JSON_PRETTY_PRINT")) | raw }}
            </script>[/twig]';

            // Process the Twig shortcode
            $output = do_shortcode($twig_shortcode);

            $context = Timber::context();
            $context['template'] = $output;
            $context['post'] = $post;

            Timber::render('render/base.twig', $context);
            exit;

        } catch (Exception $e) {
            error_log('Error in agnostic_preview_template_redirect: ' . $e->getMessage());
            wp_die('Error processing dynamic template');
        }
    }
}
add_action('template_redirect', 'agnostic_preview_template_redirect');

// Create agnostic="builder" template redirect
function agnostic_builder_template_redirect()
{
    // Check if $_GET['agnostic'] is set to 'builder'
    if (isset($_GET['agnostic']) && $_GET['agnostic'] === 'builder') {
        // Clear any output that might have already been generated
        ob_clean();

        // Prevent WordPress from sending default headers
        status_header(200);

        // Send a content-type header to ensure proper rendering
        header('Content-Type: text/html; charset=utf-8');

        $context = Timber::context();

        Timber::render('editor_tweaks.html', $context);
        exit();
    }
}

add_action('template_redirect', 'agnostic_builder_template_redirect', 1);

// Ace Editor for Agnostic Builder
function include_ace_editor_files()
{
    $parent_theme_uri = get_template_directory_uri();
    $ace_path = $parent_theme_uri . '/js/builder/ace/src-min-noconflict/';
    $files = [
        'ace.js',
        'ext-emmet.js',
        'ext-language_tools.js',
    ];

    $output = '';
    foreach ($files as $file) {
        $file_url = $ace_path . $file;
        $file_path = get_template_directory() . '/js/builder/ace/src-min-noconflict/' . $file;

        if (file_exists($file_path)) {
            $output .= sprintf('<script src="%s"></script>' . PHP_EOL, esc_url($file_url));
        } else {
            $output .= sprintf('<!-- File not found: %s -->' . PHP_EOL, esc_html($file_path));
        }
    }

    echo $output;
}
