<?php
// Exit if accessed directly.
defined('ABSPATH') || exit;

?>
<!doctype html>
<html <?php language_attributes();?>>

<head>

	<meta charset="<?php bloginfo('charset');?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- wp_head -->
	<?php wp_head();?>
	<!-- /wp_head -->
</head>

<body <?php body_class();?>>
	<?php wp_body_open();?>

	<?php
// Custom filter to check if header elements should be displayed. To disable, use: add_filter('picostrap_enable_header_elements', '__return_false');
if (apply_filters('picostrap_enable_header_elements', true)):

    //check if LC option is set to "Handle Header"
    if (!function_exists('lc_custom_header')) {
        //use the built-in theme header elements
        get_template_part('partials/header', 'optional-topbar');
        get_template_part('partials/header', 'navbar');
    } else {
        $header = '[lc_get_post post_type="lc_partial" slug="header"]';
        $header = do_shortcode($header);

        // Get the current post type
        $current_post_type = get_post_type();

        // Check if the current post type is not lc_partial, lc_block, or lc_section
        if (!in_array($current_post_type, array('lc_partial', 'lc_block', 'lc_section'))) {

            // Compile the content as a Twig template only if the post type is not excluded
            echo do_shortcode('[twig]' . $header . '[/twig]');
        } else {
            // Don't display header if the post type is lc_partial, lc_block, or lc_section
        }
    }

endif;

?>

	<main id='theme-main'>