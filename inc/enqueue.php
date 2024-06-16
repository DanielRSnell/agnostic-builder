<?php

function enqueue_swup_js_files()
{
    // Define the path to the JS directory
    $js_dir = get_stylesheet_directory() . '/js/swup/';
    $js_url = get_stylesheet_directory_uri() . '/js/swup/';

    // Get all .js files in the directory
    $files = glob($js_dir . '*.js');

    // Enqueue core.js first if it exists
    if (in_array($js_dir . 'core.js', $files)) {
        wp_enqueue_script('swup', $js_url . 'core.js', array(), null, true);
        // Remove core.js from the files array
        $files = array_diff($files, array($js_dir . 'swup.js'));
    }

    // Enqueue the remaining files, excluding init.js
    foreach ($files as $file) {
        $filename = basename($file);
        if ($filename !== 'init.js') {
            wp_enqueue_script($filename, $js_url . $filename, array(), null, true);
        }
    }

    // Enqueue init.js last if it exists
    if (in_array($js_dir . 'init.js', $files)) {
        wp_enqueue_script('init-js', $js_url . 'init.js', array(), null, true);
    }
}
add_action('wp_enqueue_scripts', 'enqueue_swup_js_files');

function enqueue_alpine_js_files()
{
    // Define the path to the JS directory
    $js_dir = get_stylesheet_directory() . '/js/alpine/';
    $js_url = get_stylesheet_directory_uri() . '/js/alpine/';

    // Get all .js files in the directory
    $files = glob($js_dir . '*.js');

    // Enqueue each file, except core.js
    foreach ($files as $file) {
        $filename = basename($file);

        // Skip core.js for now
        if ($filename === 'core.js') {
            continue;
        }

        // Enqueue the file
        wp_enqueue_script($filename, $js_url . $filename, array(), null, true);
    }

    // Enqueue core.js last
    if (file_exists($js_dir . 'core.js')) {
        wp_enqueue_script('core-js', $js_url . 'core.js', array(), null, true);
    }
}
add_action('wp_enqueue_scripts', 'enqueue_alpine_js_files');

function editor_tweaks()
{
    $context = Timber::context();

    // Add any additional data to the context if needed
    // $context['variable_name'] = $variable_value;
    echo '<script id="twig-editor" src="' . get_stylesheet_directory_uri() . '/views/scripts/twig.js"></script>';
    echo '<script id="observers-editor" src="' . get_stylesheet_directory_uri() . '/views/scripts/observers.js"></script>';
    echo '<script id="modifications-editor" src="' . get_stylesheet_directory_uri() . '/views/scripts/modifications.js"></script>';

    Timber::render('editor_tweaks.html', $context);
}
add_action('lc_editor_before_body_closing', 'editor_tweaks', 9999);

class LiveCanvasSiulIntegration
{
    private $additionalPostTypes;

    public function __construct()
    {
        $this->additionalPostTypes = [
            'lc_block',
            'lc_partial',
            'lc_section',
            'lc_dynamic_template',
        ];

        add_filter('f!yabe/siul/integration/livecanvas/compile:get_contents.post_types', [$this, 'addAdditionalPostTypes']);
        add_filter('f!yabe/siul/integration/gutenberg/compile:get_contents.post_types', [$this, 'addAdditionalPostTypes']);
        add_filter('f!yabe/siul/integration/gutenberg/compile:get_contents.render', [$this, 'overrideDoBlocks'], 9, 2);
        add_filter('f!yabe/siul/integration/livecanvas/compile:get_contents.render', [$this, 'overrideDoBlocks'], 9, 2);
    }

    public function addAdditionalPostTypes($postTypes)
    {
        return array_merge($postTypes, $this->additionalPostTypes);
    }

    public function overrideDoBlocks($continue, $post)
    {
        if ($continue) {
            // Manually apply the other transformations to $post->post_content
            // Note: Since we can't modify the original $post_content directly in this filter,
            // you'd need to apply these modifications in a separate step or filter if possible.
            // Skipping do_blocks manually by not including it here
            // $post_content = do_blocks($post->post_content); // This line is omitted intentionally
            // Apply the rest of the functions directly if possible, or set up another filter to modify $post_content accordingly
            // $post_content = wptexturize($post->post_content);
            // $post_content = convert_smilies($post_content);
            // $post_content = shortcode_unautop($post_content);
            // $post_content = wp_filter_content_tags($post_content);
            $postContent = do_shortcode($post->post_content);
            // Since we can't directly modify $post_content here, consider storing the modified content temporarily or using another approach to update the post content.
        }
        // Return false to prevent the original code block from running since we've manually handled the content manipulation
        return false;
    }
}

new LiveCanvasSiulIntegration();
