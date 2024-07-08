<?php

add_action('rest_api_init', function () {
    register_rest_route('lc/v1', '/partials', array(
        'methods' => 'GET',
        'callback' => 'get_livecanvas_partials',
    ));
});

function get_livecanvas_partials($request)
{
    $child_theme_dir = get_template_directory();
    $partials_dir = $child_theme_dir . '/template-livecanvas-partials';
    $partials = array();

    // Get partials from the template-livecanvas-partials directory
    if (is_dir($partials_dir)) {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($partials_dir),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            if ($file->isFile() && pathinfo($file, PATHINFO_EXTENSION) === 'html') {
                $file_path = $file->getPathname();
                $relative_path = substr($file_path, strlen($partials_dir) + 1);
                $directory = dirname($relative_path);
                $category = basename($directory);
                $file_name = basename($file_path, '.html');
                $name = ucwords(str_replace(array('_', '-'), ' ', $file_name));
                $slug = $file_name;
                $source = 'theme';
                $type = 'block';
                $component = '{{ include("' . $relative_path . '") }}';
                $content = file_get_contents($file_path);

                $partials[] = array(
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

    // Get partials from the lc_block custom post type
    $args = array(
        'post_type' => 'lc_partial',
        'posts_per_page' => -1,
    );
    $lc_partials = get_posts($args);

    foreach ($lc_partials as $post) {
        $name = $post->post_title;
        $slug = $post->post_name;
        $source = 'lc';
        $type = 'block';
        $category = 'lc';
        $component = '[lc_get_post post_type="lc_block" slug="' . $slug . '"]';
        $content = $post->post_content;

        $partials[] = array(
            'name' => $name,
            'slug' => $slug,
            'source' => $source,
            'type' => $type,
            'category' => $category,
            'component' => $component,
            'content' => $content,
        );
    }

    return $partials;
}
