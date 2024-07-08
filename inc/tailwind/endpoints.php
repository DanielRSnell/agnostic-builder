<?php

// Get Tailwind Config
add_action('wp_ajax_get_tailwind_config', 'get_tailwind_config');
// Remove this line if you don't need non-logged in users to access this
// add_action('wp_ajax_nopriv_get_tailwind_config', 'get_tailwind_config');

function get_tailwind_config()
{
    // check_ajax_referer('tailwind_nonce', 'nonce');

    $upload_dir = wp_upload_dir();
    $config_file = $upload_dir['basedir'] . '/agnostic/tailwind.config.js';

    if (file_exists($config_file) && is_readable($config_file)) {
        wp_send_json_success(file_get_contents($config_file));
    } else {
        wp_send_json_error('Tailwind config file not found or not readable.');
    }
}

// Update Tailwind Config
add_action('wp_ajax_update_tailwind_config', 'update_tailwind_config');

function update_tailwind_config()
{
    // check_ajax_referer('tailwind_nonce', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error('You do not have permission to perform this action.');
    }

    $upload_dir = wp_upload_dir();
    $config_file = $upload_dir['basedir'] . '/agnostic/tailwind.config.js';
    $content = isset($_POST['content']) ? stripslashes($_POST['content']) : '';

    // Additional content validation could be added here

    if (file_put_contents($config_file, $content) !== false) {
        wp_send_json_success('Tailwind config updated successfully.');
    } else {
        wp_send_json_error('Failed to update Tailwind config. Check file permissions.');
    }
}

// Get Tailwind CSS
add_action('wp_ajax_get_tailwind_css', 'get_tailwind_css');
// Remove this line if you don't need non-logged in users to access this
// add_action('wp_ajax_nopriv_get_tailwind_css', 'get_tailwind_css');

function get_tailwind_css()
{
    // check_ajax_referer('tailwind_nonce', 'nonce');

    $upload_dir = wp_upload_dir();
    $css_file = $upload_dir['basedir'] . '/agnostic/app.css';

    if (file_exists($css_file) && is_readable($css_file)) {
        wp_send_json_success(file_get_contents($css_file));
    } else {
        wp_send_json_error('Tailwind CSS file not found or not readable.');
    }
}

// Update Tailwind CSS
add_action('wp_ajax_update_tailwind_css', 'update_tailwind_css');

function update_tailwind_css()
{
    // check_ajax_referer('tailwind_nonce', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error('You do not have permission to perform this action.');
    }

    $upload_dir = wp_upload_dir();
    $css_file = $upload_dir['basedir'] . '/agnostic/app.css';
    $content = isset($_POST['content']) ? stripslashes($_POST['content']) : '';

    // Additional content validation could be added here

    if (file_put_contents($css_file, $content) !== false) {
        wp_send_json_success('Tailwind CSS updated successfully.');
    } else {
        wp_send_json_error('Failed to update Tailwind CSS. Check file permissions.');
    }
}
