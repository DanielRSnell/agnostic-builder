<?php

add_action('rest_api_init', 'create_acf_fields_endpoint');

function create_acf_fields_endpoint()
{
    register_rest_route('acf/v1', '/fields', array(
        'methods' => 'GET',
        'callback' => 'get_acf_fields',
    ));
}

function get_acf_fields()
{
    $fields = get_fields_with_types();
    return rest_ensure_response($fields);
}

function get_fields_with_types()
{
    $field_groups = acf_get_field_groups();
    $fields = array();

    foreach ($field_groups as $field_group) {
        $group_fields = acf_get_fields($field_group['ID']);

        foreach ($group_fields as $field) {
            $fields[] = $field;
        }
    }

    return $fields;
}

add_action('rest_api_init', function () {
    register_rest_route('snippets/v1', '/twig', array(
        'methods' => 'GET',
        'callback' => 'get_twig_snippets',
    ));
});

function get_twig_snippets()
{
    // Directory containing the JSON snippet files
    $dir = get_stylesheet_directory() . '/inc/timber/snippets';
    $files = glob($dir . '/*.json');
    $snippets = array();

    foreach ($files as $file) {
        $json_data = file_get_contents($file);
        $data = json_decode($json_data, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            foreach ($data as $key => $value) {
                $snippets[] = $value;
            }
        }
    }

    return new WP_REST_Response($snippets, 200);
}

add_action('wp_ajax_lc_process_dynamic_templating_twig', 'lc_process_dynamic_templating_twig_func');

function lc_process_dynamic_templating_twig_func()
{
    if (!current_user_can("edit_pages")) {
        return;
    }
    //Only for editors
    define("LC_DOING_DYNAMIC_TEMPLATE_TWIG_RENDERING", 1);
    $input = stripslashes($_POST['shortcode']);
    global $post;
    $post = get_post($_POST['post_id']);
    //determine which kind of template we're in
    foreach (get_post_meta($_POST['post_id']) as $meta_key => $meta_value) {
        if (substr($meta_key, 0, 3) == 'is_' && $meta_value[0] == 1) {
            break;
        }
    }
    // Process Twig shortcode
    $output = do_shortcode('[twig]' . $input . '<script
  type="application/json"
  id="state-data"
>
  {{ function("json_encode", state, constant("JSON_PRETTY_PRINT")) | raw }}
</script>[/twig]');
    echo $output;
    wp_die();
}

// Function to unregister the existing shortcode and register the new one
function replace_lc_get_posts_shortcode()
{
    // Unregister the existing shortcode
    remove_shortcode('lc_get_posts');

    // Register the new shortcode function
    add_shortcode('lc_get_posts', 'lc_get_posts_timber');
}

// Hook into init to replace the shortcode after all shortcodes are registered
add_action('init', 'replace_lc_get_posts_shortcode');

// Global variable to hold additional context data
global $additional_context_data;
$additional_context_data = [];

function add_to_twig_context($key, $value)
{
    global $additional_context_data;
    $additional_context_data[$key] = $value;
}

function lc_get_posts_timber($atts)
{
    global $additional_context_data;

    // Extract values from shortcode call
    $get_posts_shortcode_atts = shortcode_atts(array(
        'posts_per_page' => 10,
        'offset' => 0,
        'category' => '',
        'category_name' => '',
        'orderby' => 'date',
        'order' => 'DESC',
        'include' => '',
        'exclude' => '',
        'meta_key' => '',
        'meta_value' => '',
        'post_type' => 'post',
        'post_mime_type' => '',
        'post_parent' => '',
        'author' => '',
        'post_status' => 'publish',
        'suppress_filters' => true,
        'tax_query' => '',
        'lang' => apply_filters('wpml_current_language', null),
        'output_view' => 'lc_get_posts_default_view',
        'output_dynamic_view_id' => '',
    ), $atts, 'lc_get_posts');

    // Handle custom tax query case
    if (!empty($get_posts_shortcode_atts['tax_query'])) {
        $array_tax_query_par = explode("=", $get_posts_shortcode_atts['tax_query']);

        // Handle related posts case
        if ($array_tax_query_par[1] === 'related') {
            global $post;

            $terms = wp_get_post_terms($post->ID, $array_tax_query_par[0]);

            if (!empty($terms)) {
                $the_main_term = $terms[0];
                $array_tax_query_par[1] = $the_main_term->term_id; // main category/term ID of the current post
                $get_posts_shortcode_atts = array_merge($get_posts_shortcode_atts, array('exclude' => $post->ID));
            }
        }

        // Add the tax query
        $get_posts_shortcode_atts['tax_query'] = array(
            array(
                'taxonomy' => $array_tax_query_par[0], // taxonomy name
                'field' => 'id',
                'terms' => $array_tax_query_par[1], // term_id
                'include_children' => false,
            ),
        );
    }

    // Get the posts
    $the_posts = Timber::get_posts($get_posts_shortcode_atts);

    // Add to Timber context
    $context_key = $atts['output_dynamic_view_id'] ?: $atts['output_view'];
    add_to_twig_context($context_key, $the_posts);

    // Return empty because we are not directly outputting anything
    return '';
}

add_shortcode('lc_get_posts', 'lc_get_posts_timber');

function twig_shortcode($atts, $content = null)
{
    global $post;
    global $additional_context_data;

    // Process the shortcodes within the content first
    $content = do_shortcode($content);

    // Get the base Timber context
    $context = Timber::context();
    $context['is_editor'] = is_admin() && current_user_can('edit_others_posts');
    $context['attributes'] = $atts;
    $context['singular'] = is_singular();
    $context['archive'] = is_archive();
    $context['tax'] = is_tax() || is_category() || is_tag();
    $context['editor'] = false;

    // Check if it's a single post
    if ($context['singular']) {
        $context['post'] = Timber::get_post($post->ID);
    }

    // Check if it's a term archive
    if ($context['tax']) {
        $term = get_queried_object();
        $context['term'] = Timber::get_term($term);
    }

    // Check if it's a general archive (e.g., date archive, author archive)
    if ($context['archive']) {
        $context['archive'] = array(
            'title' => get_the_archive_title(),
            'description' => get_the_archive_description(),
            'posts' => Timber::get_posts(),
        );
    }

    // if singular, tax, and archive = false, then default to single
    if (!$context['singular'] && !$context['tax'] && !$context['archive']) {
        $context['editor'] = true;
        $context['post'] = Timber::get_post($post->ID);
    }

    // Add the additional context data
    $context = array_merge($context, $additional_context_data);

    $context['state'] = $context;

    // Compile the content as a Twig template
    $compiled_content = Timber::compile_string($content, $context);

    // Clear the additional context data to avoid data leakage
    $additional_context_data = [];

    return $compiled_content;
}

add_shortcode('twig', 'twig_shortcode');
