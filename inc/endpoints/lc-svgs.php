<?php

add_action('rest_api_init', function () {
    register_rest_route('lc/v1', '/svgs', array(
        'methods' => 'GET',
        'callback' => 'get_livecanvas_svgs',
    ));
});

function get_livecanvas_svgs($request)
{
    $child_theme_dir = get_stylesheet_directory();
    $svgs_dir = $child_theme_dir . '/template-livecanvas-svgs';
    $svgs = array();

    // Get SVG files from the svgs directory
    if (is_dir($svgs_dir)) {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($svgs_dir),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            if ($file->isFile() && pathinfo($file, PATHINFO_EXTENSION) === 'svg') {
                $file_path = $file->getPathname();
                $relative_path = substr($file_path, strlen($svgs_dir) + 1);
                $directory = dirname($relative_path);
                $category = basename($directory);
                $file_name = basename($file_path, '.svg');
                $name = ucwords(str_replace(array('_', '-'), ' ', $file_name));
                $slug = $file_name;
                $source = 'theme';
                $type = 'svg';
                $component = '{{ include("@svg/' . $relative_path . '") }}';
                $content = file_get_contents($file_path);

                $svgs[] = array(
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

    return $svgs;
}
