<?php

add_action('rest_api_init', function () {
    register_rest_route('agnostic/v1', '/the_head', array(
        'methods' => 'GET',
        'callback' => 'yabe_siul_get_wp_head',
        'permission_callback' => '__return_true', // Adjust this based on your security needs
    ));
});

function yabe_siul_get_wp_head($request)
{
    ob_start(); // Start output buffering
    wp_head(); // Call wp_head() to get all enqueued scripts and styles
    $content = ob_get_clean(); // Get the content and clear the buffer

    // Set the content type header to HTML
    header('Content-Type: text/html; charset=UTF-8');

    // Output the raw HTML content
    echo $content;

    // End PHP execution
    exit();
}
