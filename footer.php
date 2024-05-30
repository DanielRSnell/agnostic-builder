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

        $footer = '{{ partial("footer", state, "lc_partial") }}';

        // Compile the content as a Twig template
        echo do_shortcode('[twig]' . $footer . '[/twig]');
    }

endif;

?>
<?php wp_footer();?>
</body>
</html>
