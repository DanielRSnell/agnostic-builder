<?php

add_action('rest_api_init', function () {
    register_rest_route('lc/v1', '/dynamic/', array(
        'methods' => 'GET',
        'callback' => 'get_custom_posts_with_meta',
    ));

    register_rest_route('lc/v1', '/dynamic/(?P<id>\d+)', array(
        'methods' => 'GET',
        'callback' => 'get_post_connections',
    ));
});

function get_custom_posts_with_meta(WP_REST_Request $request)
{
    $args = array(
        'post_type' => 'lc_dynamic_template',
        'posts_per_page' => -1,
    );

    $query = new WP_Query($args);

    if (!$query->have_posts()) {
        return new WP_REST_Response('No posts found', 404);
    }

    $posts = array();

    while ($query->have_posts()) {
        $query->the_post();
        $post_id = get_the_ID();

        $connections = get_connections($post_id);

        $posts[] = array(
            'ID' => $post_id,
            'title' => get_the_title(),
            'custom_meta' => $connections,
        );
    }

    wp_reset_postdata();

    return new WP_REST_Response($posts, 200);
}

function get_connections($post_id)
{
    $custom_meta = get_post_meta($post_id);
    $connected_single = array();
    $connected_archive = array();
    $connected_search = false;
    $connected_404 = false;
    $post_loop = false;

    foreach ($custom_meta as $meta_key => $meta_values) {
        if (strpos($meta_key, 'is_') === 0 && $meta_values[0] == '1') {
            $meta_suffix = substr($meta_key, 3);

            if (strpos($meta_suffix, 'single') === 0) {
                $connected_single[] = substr($meta_suffix, 7);
            } elseif (strpos($meta_suffix, 'archive_for_tax_') === 0) {
                $tax_type = substr($meta_suffix, 16);
                $connected_archive['tax'][] = $tax_type;
            } elseif (strpos($meta_suffix, 'archive_for_post_type_') === 0) {
                $post_type = substr($meta_suffix, 22);
                $connected_archive['post_type'][] = $post_type;
            } elseif (strpos($meta_suffix, 'archive_') === 0) {
                $archive_type = substr($meta_suffix, 8);
                $connected_archive[$archive_type] = true;
            } elseif ($meta_suffix == 'search') {
                $connected_search = true;
            } elseif ($meta_suffix == '404') {
                $connected_404 = true;
            } elseif ($meta_suffix == 'post_loop') {
                $post_loop = true;
            }
        }
    }

    return array(
        'connected_single' => $connected_single,
        'connected_archive' => $connected_archive,
        'connected_search' => $connected_search,
        'connected_404' => $connected_404,
        'post_loop' => $post_loop,
    );
}

function get_post_connections(WP_REST_Request $request)
{
    $post_id = $request['id'];
    if (!get_post($post_id)) {
        return new WP_REST_Response('Post not found', 404);
    }

    $connections = get_connections($post_id);

    return new WP_REST_Response($connections, 200);
}
