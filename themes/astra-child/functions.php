<?php
/**
 * Astra Child Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Astra Child
 * @since 1.0.0
 */

/**
 * Define Constants
 */
define( 'CHILD_THEME_ASTRA_CHILD_VERSION', '1.0.0' );

/**
 * Enqueue styles
 */
function child_enqueue_styles() {

	wp_enqueue_style( 'astra-child-theme-css', get_stylesheet_directory_uri() . '/style.css', array('astra-theme-css'), CHILD_THEME_ASTRA_CHILD_VERSION, 'all' );

}

add_action( 'wp_enqueue_scripts', 'child_enqueue_styles', 15 );


/*function custom_enqueue(){
    wp_enqueue_style('customstyle-custom', get_stylesheet_directory_uri() . '/assets/css/custom.css', array(), '1.0.0', 'all' );
    wp_enqueue_script('front-script-custom', get_stylesheet_directory_uri() . '/assets/js/custom.js', array(), '1.0.0', true );
}
add_action('wp_enqueue_scripts', 'custom_enqueue');*/


require_once get_stylesheet_directory() . '/includes/common-function.php';