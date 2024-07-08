<?php

// ACF Form REST API
function agnostic_register_acf_form_endpoint()
{
    register_rest_route('acf/v1', '/form/(?P<post_id>\d+)', [
        'methods' => 'GET',
        'callback' => 'agnostic_get_acf_form',
        'args' => [
            'post_id' => [
                'validate_callback' => function ($param) {
                    return is_numeric($param);
                },
            ],
        ],
        'permission_callback' => '__return_true',
    ]);
}
add_action('rest_api_init', 'agnostic_register_acf_form_endpoint');

function agnostic_get_acf_form($request)
{
    $post_id = $request->get_param('post_id');
    $post = get_post($post_id);

    if (!$post) {
        return new WP_Error('invalid_post_id', 'Invalid post ID', ['status' => 404]);
    }

    $atts = [
        'post_id' => $post_id,
        'post_title' => false,
        'post_content' => false,
        'submit_value' => 'Update',
        'updated_message' => 'Fields updated successfully',
        'html_updated_message' => '<div class="acf-notice -success acf-success-message -dismiss"><p class="success-msg">%s</p><a href="#" class="acf-notice-dismiss acf-icon -cancel"></a></div>',
        'return' => add_query_arg('updated', 'true', get_permalink($post_id)),
    ];

    ob_start();
    acf_form($atts);
    $form = ob_get_clean();

    return new WP_REST_Response(['form' => $form], 200);
}
