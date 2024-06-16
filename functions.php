<?php
/*
_               _                  _____        _     _ _     _   _   _
(_)             | |                | ____|      | |   (_) |   | | | | | |
_ __  _  ___ ___  ___| |_ _ __ __ _ _ __| |__     ___| |__  _| | __| | | |_| |__   ___ _ __ ___   ___
| '_ \| |/ __/ _ \/ __| __| '__/ _` | '_ \___ \   / __| '_ \| | |/ _` | | __| '_ \ / _ \ '_ ` _ \ / _ \
| |_) | | (_| (_) \__ \ |_| | | (_| | |_) |__) | | (__| | | | | | (_| | | |_| | | |  __/ | | | | |  __/
| .__/|_|\___\___/|___/\__|_|  \__,_| .__/____/   \___|_| |_|_|_|\__,_|  \__|_| |_|\___|_| |_| |_|\___|
| |                                 | |
|_|                                 |_|

 *************************************** WELCOME TO PICOSTRAP ***************************************

 ********************* THE BEST WAY TO EXPERIENCE SASS, BOOTSTRAP AND WORDPRESS *********************

PLEASE WATCH THE VIDEOS FOR BEST RESULTS:
https://www.youtube.com/playlist?list=PLtyHhWhkgYU8i11wu-5KJDBfA9C-D4Bfl

 */

// Vendor
require_once __DIR__ . '/vendor/autoload.php';

Timber\Timber::init();
Timber::$dirname = ['views'];
Timber::$autoescape = false;

require get_stylesheet_directory() . '/inc/timber/controller.php';
require get_stylesheet_directory() . '/inc/enqueue.php';

// DE-ENQUEUE PARENT THEME BOOTSTRAP JS BUNDLE
add_action('wp_print_scripts', function () {
    wp_dequeue_script('bootstrap5');
    //wp_dequeue_script( 'dark-mode-switch' );  //optionally
}, 100);

// ENQUEUE THE BOOTSTRAP JS BUNDLE (AND EVENTUALLY MORE LIBS) FROM THE CHILD THEME DIRECTORY
add_action('wp_enqueue_scripts', function () {
    //enqueue js in footer, defer
    // wp_enqueue_script('bootstrap5-childtheme', get_stylesheet_directory_uri() . "/js/bootstrap.bundle.min.js", array(), null, array('strategy' => 'defer', 'in_footer' => true));

    //optional: example of how to globally load js files eg  lottie player
    //wp_enqueue_script( 'lottie-player', 'https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js', array(), null, array('strategy' => 'defer', 'in_footer' => true)  );
}, 101);

// HACK HERE: ENQUEUE YOUR CUSTOM JS FILES, IF NEEDED
add_action('wp_enqueue_scripts', function () {

    //UNCOMMENT next row to include the js/custom.js file globally
    wp_enqueue_script('custom', get_stylesheet_directory_uri() . '/js/custom.js', array( /* 'jquery' */), null, array('strategy' => 'defer', 'in_footer' => true));

    //UNCOMMENT next 3 rows to load the js file only on one page
    //if (is_page('mypageslug')) {
    //    wp_enqueue_script('custom', get_stylesheet_directory_uri() . '/js/custom.js', array(/* 'jquery' */), null, array('strategy' => 'defer', 'in_footer' => true) );
    //}

}, 102);

// OPTIONAL: ADD MORE NAV MENUS
//register_nav_menus( array( 'third' => __( 'Third Menu', 'picostrap' ), 'fourth' => __( 'Fourth Menu', 'picostrap' ), 'fifth' => __( 'Fifth Menu', 'picostrap' ), ) );
// THEN USE SHORTCODE:  [lc_nav_menu theme_location="third" container_class="" container_id="" menu_class="navbar-nav"]

// CHECK PARENT THEME VERSION
add_action('admin_notices', function () {
    if ((pico_get_parent_theme_version()) >= 3.0) {
        return;
    }

    $message = __('This Child Theme requires at least Picostrap Version 3.0.0  in order to work properly. Please update the parent theme.', 'picostrap');
    printf('<div class="%1$s"><h1>%2$s</h1></div>', esc_attr('notice notice-error'), esc_html($message));
});

// FOR SECURITY: DISABLE APPLICATION PASSWORDS. Remove if needed (unlikely!)
add_filter('wp_is_application_passwords_available', '__return_false');

// ADD YOUR CUSTOM PHP CODE DOWN BELOW /////////////////////////

// TWIG EDITOR SUPPORT
// Enqueue twig.js from child theme

function inline_themify_icons()
{
    echo '<style id="icons-themeify">';
    echo '@font-face {
            font-family: "themify";
            src: url("' . get_stylesheet_directory_uri() . '/fonts/themify-icons/fonts/themify.eot?-fvbane");
            src: url("' . get_stylesheet_directory_uri() . '/fonts/themify-icons/fonts/themify.eot?#iefix-fvbane") format("embedded-opentype"),
                 url("' . get_stylesheet_directory_uri() . '/fonts/themify-icons/fonts/themify.woff?-fvbane") format("woff"),
                 url("' . get_stylesheet_directory_uri() . '/fonts/themify-icons/fonts/themify.ttf?-fvbane") format("truetype"),
                 url("' . get_stylesheet_directory_uri() . '/fonts/themify-icons/fonts/themify.svg?-fvbane#themify") format("svg");
            font-weight: normal;
            font-style: normal;
          }';
    include get_stylesheet_directory() . '/fonts/themify-icons/themify-icons.css';
    echo '</style>';
}

add_action('lc_editor_header', function () {
    echo '<link rel="stylesheet" id="tweaks-editor" href="' . get_stylesheet_directory_uri() . '/views/styles/tweaks.css">';
    inline_themify_icons();
});

function my_timber_content_filter($content)
{

    $content = do_shortcode('[twig]' . $content . '[/twig]');

    return $content;

}

add_filter('the_content', 'my_timber_content_filter', 10);

// Register REST endpoint for ACF form
function register_acf_form_endpoint()
{
    register_rest_route('acf/v1', '/form/(?P<post_id>\d+)', [
        'methods' => 'GET',
        'callback' => 'get_acf_form',
        'args' => [
            'post_id' => [
                'validate_callback' => function ($param, $request, $key) {
                    return is_numeric($param);
                },
            ],
        ],
        'permission_callback' => function () {
            // allow access to anyone
            return true;
        },
    ]);
}
add_action('rest_api_init', 'register_acf_form_endpoint');

// Callback function to get ACF form
function get_acf_form($request)
{
    $post_id = $request->get_param('post_id');
    $post = get_post($post_id);

    if (!$post) {
        return new WP_Error('invalid_post_id', 'Invalid post ID', ['status' => 404]);
    }

    $atts = [
        'post_id' => $post_id,
        'post_title' => false,
        'post_content' => false,
        'submit_value' => 'Update',
        'updated_message' => 'Fields updated successfully',
        'html_updated_message' => '<div class="acf-notice -success acf-success-message -dismiss"><p class="success-msg">%s</p><a href="#" class="acf-notice-dismiss acf-icon -cancel"></a></div>',
        'return' => add_query_arg('updated', 'true', get_permalink($post_id)),
    ];

    ob_start();
    acf_form($atts);
    $form = ob_get_clean();

    return new WP_REST_Response(['form' => $form], 200);
}

add_action('rest_api_init', function () {
    register_rest_route('lc/v1', '/dynamic-templates', array(
        'methods' => 'GET',
        'callback' => 'get_dynamic_templates',
        'permission_callback' => '__return_true',
    ));
});

function get_dynamic_templates($request)
{
    $args = array(
        'post_type' => 'lc_dynamic_template',
        'posts_per_page' => -1,
    );

    $templates = Timber::get_posts($args);

    $data = array();

    foreach ($templates as $template) {
        $template_data = array(
            'id' => $template->ID,
            'title' => $template->post_title,
            'content' => $template->post_content,
            'meta' => get_post_meta($template->ID),
        );

        $data[] = $template_data;
    }

    return $data;
}

$endpoints_dir = get_stylesheet_directory() . '/inc/endpoints';

if (is_dir($endpoints_dir)) {
    $endpoint_files = glob($endpoints_dir . '/*.php');

    foreach ($endpoint_files as $file) {
        if (is_file($file)) {
            require_once $file;
        }
    }
}

add_action('rest_api_init', function () {
    register_rest_route('lc/v1', '/shortcodes', array(
        'methods' => 'GET',
        'callback' => 'get_all_shortcodes',
    ));
});

function get_all_shortcodes()
{
    global $shortcode_tags;

    // Return the list of all registered shortcodes
    return new WP_REST_Response($shortcode_tags, 200);
}

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
