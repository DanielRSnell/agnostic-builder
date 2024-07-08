<?php

// Custom function to modify $lc_main_html before it is echoed
function modify_lc_main_html_content()
{
    global $lc_main_html;

    // Check if the global variable is set and not empty
    if (isset($lc_main_html) && !empty($lc_main_html)) {
        // Process the content with lct_do_shortcode
        $processed_content = lct_do_shortcode(lc_strip_lc_attributes($lc_main_html));

        // Wrap the processed content with [twig] and [/twig]
        $lc_main_html = do_shortcode('[twig]' . $processed_content . '[/twig]');
    }
}

// Hook the custom function into wp_head to ensure it runs before the content is echoed
add_action('wp_head', 'modify_lc_main_html_content');

add_filter('content_save_pre', 'agnostic_do_not_sanitize_alpine_attr_in_content');

function my_timber_content_filter($content)
{

    $content = do_shortcode('[twig]' . $content . '[/twig]');

    return $content;

}

add_filter('the_content', 'my_timber_content_filter', 10);
