<?php

function inline_themify_icons()
{
    echo '<style id="icons-themeify">';
    echo '@font-face {
            font-family: "themify";
            src: url("' . get_template_directory_uri() . '/fonts/themify-icons/fonts/themify.eot?-fvbane");
            src: url("' . get_template_directory_uri() . '/fonts/themify-icons/fonts/themify.eot?#iefix-fvbane") format("embedded-opentype"),
                 url("' . get_template_directory_uri() . '/fonts/themify-icons/fonts/themify.woff?-fvbane") format("woff"),
                 url("' . get_template_directory_uri() . '/fonts/themify-icons/fonts/themify.ttf?-fvbane") format("truetype"),
                 url("' . get_template_directory_uri() . '/fonts/themify-icons/fonts/themify.svg?-fvbane#themify") format("svg");
            font-weight: normal;
            font-style: normal;
          }';
    include get_template_directory() . '/fonts/themify-icons/themify-icons.css';
    echo '</style>';
}

add_action('lc_editor_header', function () {
    echo '<link rel="stylesheet" id="tweaks-editor" href="' . get_template_directory_uri() . '/views/styles/tweaks.css">';
    inline_themify_icons();
});

add_action('agnostic_header', function () {
    inline_themify_icons();
});
