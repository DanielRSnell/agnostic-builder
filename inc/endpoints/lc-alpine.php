<?php

add_action('rest_api_init', function () {
    register_rest_route('lc/v1', '/alpine', array(
        'methods' => 'GET',
        'callback' => 'get_livecanvas_alpine',
    ));
});

function get_livecanvas_alpine($request)
{
    $child_theme_dir = get_stylesheet_directory();
    $blocks_dir = $child_theme_dir . '/template-livecanvas-alpine';
    $blocks = array();

    // Get blocks from the template-livecanvas-blocks directory
    if (is_dir($blocks_dir)) {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($blocks_dir),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            if ($file->isFile() && pathinfo($file, PATHINFO_EXTENSION) === 'html') {
                $file_path = $file->getPathname();
                $relative_path = substr($file_path, strlen($blocks_dir) + 1);
                $directory = dirname($relative_path);
                $category = basename($directory);
                $file_name = basename($file_path, '.html');
                $name = ucwords(str_replace(array('_', '-'), ' ', $file_name));
                $slug = $file_name;
                $source = 'theme';
                $type = 'block';
                $component = '{{ include("' . $relative_path . '") }}';
                $content = file_get_contents($file_path);

                $blocks[] = array(
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

    // Get blocks from the lc_block custom post type
    $args = array(
        'post_type' => 'lc_block',
        'posts_per_page' => -1,
    );
    $lc_blocks = get_posts($args);

    foreach ($lc_blocks as $post) {
        $name = $post->post_title;
        $slug = $post->post_name;
        $source = 'lc';
        $type = 'block';
        $category = 'lc';
        $component = '[lc_get_post post_type="lc_block" slug="' . $slug . '"]';
        $content = $post->post_content;

        $blocks[] = array(
            'name' => $name,
            'slug' => $slug,
            'source' => $source,
            'type' => $type,
            'category' => $category,
            'component' => $component,
            'content' => $content,
        );
    }

    return $blocks;
}
