</main>
<?php

// Custom filter to check if footer elements should be displayed. To disable, use: add_filter('picostrap_enable_footer_elements', '__return_false');
if (apply_filters('picostrap_enable_footer_elements', true)):

    // Check if LC option is set to "Handle Footer"
    if (!function_exists("lc_custom_footer")) {

        echo 'built-in footer';
        // Use the built-in theme footer elements
        get_template_part('partials/footer', 'elements');

    } else {
        // Use the LiveCanvas Custom Footer

        // Get the html from lc_custom_footer() function
        $footer = '[lc_get_post post_type="lc_partial" slug="footer"]';
        $footer = do_shortcode($footer);

        // Get the current post type
        $current_post_type = get_post_type();

        // Check if the current post type is not lc_partial, lc_block, or lc_section
        if (!in_array($current_post_type, array('lc_partial', 'lc_block', 'lc_section'))) {

            // Compile the content as a Twig template only if the post type is not excluded
            echo do_shortcode('[twig]' . $footer . '[/twig]');
        } else {
            // Don't display footer if the post type is lc_partial, lc_block, or lc_section
        }
    }

endif;

?>
<?php wp_footer();?>

</body>
</html>