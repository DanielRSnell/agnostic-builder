<?php

add_action('rest_api_init', function () {
    register_rest_route('lc/v1', '/sections', array(
        'methods' => 'GET',
        'callback' => 'get_livecanvas_sections',
    ));
});

function get_livecanvas_sections($request)
{
    $child_theme_dir = get_stylesheet_directory();
    $sections_dir = $child_theme_dir . '/template-livecanvas-sections';
    $sections = array();

    // Get sections from the template-livecanvas-sections directory
    if (is_dir($sections_dir)) {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($sections_dir),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            if ($file->isFile() && pathinfo($file, PATHINFO_EXTENSION) === 'html') {
                $file_path = $file->getPathname();
                $relative_path = substr($file_path, strlen($sections_dir) + 1);
                $directory = dirname($relative_path);
                $category = basename($directory);
                $file_name = basename($file_path, '.html');
                $name = ucwords(str_replace(array('_', '-'), ' ', $file_name));
                $slug = $file_name;
                $source = 'theme';
                $type = 'block';
                $component = '{{ include("' . $relative_path . '") }}';
                $content = file_get_contents($file_path);

                $sections[] = array(
                    'name' => $name,
                    'slug' => $slug,
                    'source' => $source,
                    'type' => $type,
                    'category' => $category,
                    'component' => $component,
                    'content' => $content,
                );
            }
        }
    }

    // Get sections from the lc_block custom post type
    $args = array(
        'post_type' => 'lc_section',
        'posts_per_page' => -1,
    );
    $lc_sections = get_posts($args);

    foreach ($lc_sections as $post) {
        $name = $post->post_title;
        $slug = $post->post_name;
        $source = 'lc';
        $type = 'block';
        $category = 'lc';
        $component = '[lc_get_post post_type="lc_block" slug="' . $slug . '"]';
        $content = $post->post_content;

        $sections[] = array(
            'name' => $name,
            'slug' => $slug,
            'source' => $source,
            'type' => $type,
            'category' => $category,
            'component' => $component,
            'content' => $content,
        );
    }

    return $sections;
}
