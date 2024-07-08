<?php

use Carbon_Fields\Block;
use Carbon_Fields\Container;
use Carbon_Fields\Field;

// Add checkbox and select field to lc_block post type
function add_block_settings_fields()
{
    $fields = array(
        Field::make('checkbox', 'is_gutenberg_block', 'Generate as Gutenberg Block'),
    );

    if (class_exists('ACF')) {
        $fields[] = Field::make('select', 'block_registration_method', 'Block Registration Method')
            ->set_options(array(
                'carbon_fields' => 'Carbon Fields',
                'acf' => 'Advanced Custom Fields',
            ))
            ->set_default_value('carbon_fields')
            ->set_conditional_logic(array(
                array(
                    'field' => 'is_gutenberg_block',
                    'value' => true,
                ),
            ));
    }

    Container::make('post_meta', 'Block Settings')
        ->where('post_type', '=', 'lc_block')
        ->add_fields($fields)
        ->set_context('side');
}
add_action('carbon_fields_register_fields', 'add_block_settings_fields');

// Register the block for each post in the lc_block post type
function register_lc_block_blocks()
{
    $posts = get_posts(array(
        'post_type' => 'lc_block',
        'posts_per_page' => -1,
    ));

    foreach ($posts as $post) {
        $is_gutenberg_block = carbon_get_post_meta($post->ID, 'is_gutenberg_block');

        if (!$is_gutenberg_block) {
            continue;
        }

        $block_registration_method = carbon_get_post_meta($post->ID, 'block_registration_method');

        if ($block_registration_method === 'acf' && function_exists('acf_register_block_type')) {
            // Register block using ACF
            acf_register_block_type(array(
                'name' => 'lc-' . $post->post_name,
                'title' => $post->post_title,
                'description' => $post->post_excerpt,
                'render_callback' => function ($block, $content = '', $is_preview = false, $post_id = 0) use ($post) {
                    render_acf_block($block, $content, $is_preview, $post_id, $post->ID);
                },
                'category' => 'formatting',
                'icon' => 'admin-comments',
                'keywords' => array('lc', 'block'),
            ));
        } else {
            // Register block using Carbon Fields
            $fields = array();
            $fields_json = extract_fields_json($post->post_content);

            if (!empty($fields_json)) {
                $parsed_json = json_decode($fields_json, true);

                if (is_array($parsed_json)) {
                    $fields = generateFieldsFromSchema($parsed_json);
                }
            }

            Block::make($post->post_title)
                ->add_fields($fields)
                ->set_render_callback(function ($fields, $attributes, $inner_blocks) use ($post) {
                    $context = Timber::context();
                    $context['title'] = $post->post_title;
                    $context['attributes'] = $attributes;
                    $context['fields'] = $fields;

                    $compile = Timber::compile_string($post->post_content, $context);
                    $compile = do_shortcode('[twig]' . $compile . '[/twig]');
                    echo $compile;
                });
        }
    }
}
add_action('carbon_fields_register_fields', 'register_lc_block_blocks');

// Render callback for ACF blocks
function render_acf_block($block, $content = '', $is_preview = false, $post_id = 0, $lc_block_id = 0)
{
    $lc_block = get_post($lc_block_id);
    $context = Timber::context();
    $context['block'] = $block;
    $context['fields'] = get_fields();
    $context['is_preview'] = $is_preview;

    $compile = Timber::compile_string($lc_block->post_content, $context);

    $compile = do_shortcode('[twig]' . $compile . '[/twig]');

    echo $compile;
}

// Helper function to extract the JSON from the post content
function extract_fields_json($content)
{
    preg_match('/<script id="JSON-FIELDS">(.*?)<\/script>/s', $content, $matches);

    if (isset($matches[1])) {
        return trim($matches[1]);
    }

    return '';
}

// Helper function to generate fields from the JSON schema
function generateFieldsFromSchema($schema)
{
    $fields = array();

    foreach ($schema as $field) {
        if ($field['type'] === 'complex') {
            $complex_fields = generateFieldsFromSchema($field['fields']);
            $fields[] = Field::make('complex', $field['name'], $field['label'])
                ->set_layout('tabbed-horizontal')
                ->set_collapsed(true)
                ->add_fields($complex_fields);
        } else {
            $field_instance = Field::make($field['type'], $field['name'], $field['label']);

            if ($field['type'] === 'select' && isset($field['options'])) {
                $options = $field['options'];
                $field_instance->set_options($options);
            }

            $fields[] = $field_instance;
        }
    }

    return $fields;
}

// Register fields for connected taxonomies and post types
function register_lc_dynamic_template_fields()
{
    $posts = get_posts(array(
        'post_type' => 'lc_dynamic_template',
        'posts_per_page' => -1,
    ));

    foreach ($posts as $post) {
        $connections = get_connections($post->ID);
        $fields_json = extract_fields_json($post->post_content);

        // Skip if there are no JSON-FIELDS
        if (empty($fields_json)) {
            continue;
        }

        $parsed_json = json_decode($fields_json, true);

        if (!is_array($parsed_json)) {
            continue;
        }

        $fields = generateFieldsFromSchema($parsed_json);

        // Skip if no fields were generated
        if (empty($fields)) {
            continue;
        }

        // Register fields for connected taxonomies
        if (isset($connections['connected_archive']['tax']) && is_array($connections['connected_archive']['tax'])) {
            foreach ($connections['connected_archive']['tax'] as $taxonomy) {
                Container::make('term_meta', ucwords(str_replace('_', ' ', $taxonomy)))
                    ->where('term_taxonomy', '=', $taxonomy)
                    ->add_fields($fields);
            }
        }

        // Register fields for connected post types
        if (isset($connections['connected_archive']['post_type']) && is_array($connections['connected_archive']['post_type'])) {
            foreach ($connections['connected_archive']['post_type'] as $post_type) {
                Container::make('post_meta', ucwords(str_replace('_', ' ', $post_type)))
                    ->where('post_type', '=', $post_type)
                    ->add_fields($fields);
            }
        }
    }
}
add_action('carbon_fields_register_fields', 'register_lc_dynamic_template_fields');
