<?php

require_once __DIR__ . '/vendor/autoload.php';

// Initialize Timber
Timber\Timber::init();
Timber::$dirname = ['views'];
Timber::$autoescape = false;

// Initialize Carbon Fields
\Carbon_Fields\Carbon_Fields::boot();

function lc_theme_is_livecanvas_friendly()
{
    // Livecanvas compatibility
}

// Include required files
require get_template_directory() . '/inc/timber/controller.php';
require get_template_directory() . '/inc/enqueue.php';
require get_template_directory() . '/inc/controller.php';
// Theme setup
function agnostic_theme_setup()
{
    add_action('wp_print_scripts', 'agnostic_dequeue_parent_scripts', 100);
    // add_action('wp_enqueue_scripts', 'agnostic_enqueue_child_scripts', 101);
    add_action('wp_enqueue_scripts', 'agnostic_enqueue_custom_scripts', 102);
    add_filter('wp_is_application_passwords_available', '__return_false');
}
add_action('after_setup_theme', 'agnostic_theme_setup');

// Dequeue parent theme scripts
function agnostic_dequeue_parent_scripts()
{
    wp_dequeue_script('bootstrap5');
}

// Enqueue custom scripts
function agnostic_enqueue_custom_scripts()
{
    wp_enqueue_script('custom', get_template_directory_uri() . '/js/custom.js', array(), null, array('strategy' => 'defer', 'in_footer' => true));
}
