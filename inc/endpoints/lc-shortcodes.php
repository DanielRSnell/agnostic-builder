<?php

// Dynamic Templates REST API
function agnostic_register_dynamic_templates_endpoint()
{
    register_rest_route('agnostic/v1', '/dynamic-templates', [
        'methods' => 'GET',
        'callback' => 'agnostic_get_dynamic_templates',
        'permission_callback' => '__return_true',
    ]);
}
add_action('rest_api_init', 'agnostic_register_dynamic_templates_endpoint');

function agnostic_get_dynamic_templates()
{
    $args = [
        'post_type' => 'agnostic_dynamic_template',
        'posts_per_page' => -1,
    ];

    $templates = Timber::get_posts($args);
    $data = array_map(function ($template) {
        return [
            'id' => $template->ID,
            'title' => $template->post_title,
            'content' => $template->post_content,
            'meta' => get_post_meta($template->ID),
        ];
    }, $templates);

    return $data;
}

// Include additional endpoint files
$endpoints_dir = get_template_directory() . '/inc/endpoints';
if (is_dir($endpoints_dir)) {
    array_map(function ($file) {
        if (is_file($file)) {
            require_once $file;
        }
    }, glob($endpoints_dir . '/*.php'));
}

// Shortcodes REST API
function agnostic_register_shortcodes_endpoint()
{
    register_rest_route('agnostic/v1', '/shortcodes', [
        'methods' => 'GET',
        'callback' => 'agnostic_get_all_shortcodes',
    ]);
}
add_action('rest_api_init', 'agnostic_register_shortcodes_endpoint');

function agnostic_get_all_shortcodes()
{
    global $shortcode_tags;
    return new WP_REST_Response($shortcode_tags, 200);
}
