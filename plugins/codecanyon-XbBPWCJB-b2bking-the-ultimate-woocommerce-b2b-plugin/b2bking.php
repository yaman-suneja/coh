<?php
/*
/**
 * Plugin Name:       B2BKing Pro
 * Plugin URI:        woocommerce-b2b-plugin.com
 * Description:       B2BKing is the complete solution for turning WooCommerce into an enterprise-level B2B e-commerce platform.
 * Version:           4.1.83
 * Author:            WebWizards
 * Author URI:        webwizards.dev
 * Text Domain:       b2bking
 * Domain Path:       /languages
 * WC requires at least: 3.0.0
 * WC tested up to: 6.4.0
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! defined( 'B2BKING_VERSION' ) ) {
	define(	'B2BKING_VERSION', 'v4.1.83');
}

if ( ! defined( 'B2BKING_DIR' ) ) {
	define( 'B2BKING_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'B2BKINGMAIN_DIR' ) ) {
	define( 'B2BKINGMAIN_DIR', plugin_dir_path( __FILE__ ) );
}

require_once ( B2BKING_DIR . 'includes/class-b2bking-global-helper.php' );
function b2bking() {
    return B2bking_Globalhelper::init();
}

function b2bking_activate() {
	require_once B2BKING_DIR . 'includes/class-b2bking-activator.php';
	B2bking_Activator::activate();

}
register_activation_hook( __FILE__, 'b2bking_activate' );


require B2BKING_DIR . 'includes/class-b2bking.php';

// Load plugin language
add_action( 'plugins_loaded', 'b2bking_load_language');
function b2bking_load_language() {
	load_plugin_textdomain( 'b2bking', FALSE, basename( dirname( __FILE__ ) ) . '/languages');
}

// Begins execution of the plugin.
function b2bking_run() {
	global $b2bking_plugin;
	$b2bking_plugin = new B2bking();
}

b2bking_run();

