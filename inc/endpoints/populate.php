<?php

add_action('rest_api_init', 'register_content_endpoints');

function register_content_endpoints()
{
    // Endpoint for retrieving selection types based on content type
    register_rest_route('lc/v1', '/selection-types', array(
        'methods' => 'GET',
        'callback' => 'get_selection_types',
    ));

    // Endpoint for retrieving singles based on selection type
    register_rest_route('lc/v1', '/singles', array(
        'methods' => 'GET',
        'callback' => 'get_singles',
    ));
}

function get_selection_types($request)
{
    $content_type = $request->get_param('content_type');

    // Logic to retrieve selection types based on the content type
    $selection_types = array();

    switch ($content_type) {
        case 'archive':
            $post_types = get_post_types(array('public' => true), 'names');
            $selection_types = array_values($post_types);
            break;
        case 'single':
            $post_types = get_post_types(array('public' => true), 'names');
            $selection_types = array_values($post_types);
            break;
        case 'tax':
            $taxonomies = get_taxonomies(array('public' => true), 'names');
            $selection_types = array_values($taxonomies);
            break;
        case 'search':
            // No selection types for search
            break;
    }

    return $selection_types;
}

function get_singles($request)
{
    $selection_type = $request->get_param('selection_type');

    // Logic to retrieve singles based on the selection type
    $singles = array();

    if (post_type_exists($selection_type)) {
        $posts = get_posts(array(
            'post_type' => $selection_type,
            'posts_per_page' => -1,
        ));

        foreach ($posts as $post) {
            $singles[] = array(
                'id' => $post->ID,
                'title' => $post->post_title,
            );
        }
    } elseif (taxonomy_exists($selection_type)) {
        $terms = get_terms(array(
            'taxonomy' => $selection_type,
            'hide_empty' => false,
        ));

        foreach ($terms as $term) {
            $singles[] = array(
                'id' => $term->term_id,
                'title' => $term->name,
            );
        }
    }

    return $singles;
}
