<?php

// function register_custom_block_categories($categories, $post)
// {
//     return array_merge(
//         $categories,
//         array(
//             array(
//                 'slug' => 'sections',
//                 'title' => __('Sections', 'your-text-domain'),
//             ),
//             array(
//                 'slug' => 'blocks',
//                 'title' => __('Blocks', 'your-text-domain'),
//             ),
//             array(
//                 'slug' => 'partials',
//                 'title' => __('Partials', 'your-text-domain'),
//             ),
//         )
//     );
// }
// add_filter('block_categories_all', 'register_custom_block_categories', 10, 2);

// function register_lc_section_blocks()
// {
//     $args = array(
//         'post_type' => 'lc_section',
//         'posts_per_page' => -1,
//     );
//     $query = new WP_Query($args);

//     if ($query->have_posts()) {
//         while ($query->have_posts()) {
//             $query->the_post();
//             $title = get_the_title();
//             $slug = sanitize_title($title);

//             acf_register_block_type(array(
//                 'name' => $slug,
//                 'title' => $title,
//                 'description' => '',
//                 'render_callback' => function ($block, $content = '', $is_preview = false, $post_id = 0, $title) use ($slug) {
//                     return render_lc_section_block($block, $content, $is_preview, $post_id, $slug, $title);
//                 },
//                 'category' => 'sections',
//                 'icon' => 'admin-comments',
//                 'keywords' => array($slug),
//             ));
//         }
//         wp_reset_postdata();
//     }
// }
// add_action('acf/init', 'register_lc_section_blocks');

// function render_lc_section_block($block, $content = '', $is_preview = false, $post_id = 0, $slug = '', $title = '')
// {
//     $section_data = get_post($post_id);

//     // Manipulate the section data or perform any necessary operations
//     $section_content = $section_data->post_content;

//     // Create id attribute allowing for custom "anchor" value.
//     $id = 'lc-section-' . $block['id'];
//     if (!empty($block['anchor'])) {
//         $id = $block['anchor'];
//     }

//     // Create class attribute allowing for custom "className" and "align" values.
//     $class_name = 'lc-section';
//     if (!empty($block['className'])) {
//         $class_name .= ' ' . $block['className'];
//     }
//     if (!empty($block['align'])) {
//         $class_name .= ' align' . $block['align'];
//     }

//     // Render the block HTML
//     $output = '<div id="' . esc_attr($id) . '" class="' . esc_attr($class_name) . '">';
//     $output .= $section_content;
//     $output .= '</div>';

//     echo $title;
// }
