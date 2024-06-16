<?php

add_action('rest_api_init', 'register_menu_names_endpoint');

function register_menu_names_endpoint()
{
    register_rest_route('lc/v1', '/menu-names', array(
        'methods' => 'GET',
        'callback' => 'get_menu_names',
        'permission_callback' => function () {
            return true; // Set to true for public access or use custom permission callback
        },
    ));
}

function get_menu_names()
{
    $menus = wp_get_nav_menus();
    $menu_names = array();

    foreach ($menus as $menu) {
        $menu_names[] = $menu->name;
    }

    return $menu_names;
}
