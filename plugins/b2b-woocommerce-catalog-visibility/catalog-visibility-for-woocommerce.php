<?php
/**
 * Plugin Name: Catalog Visibility for WooCommerce
 * Plugin URI: http://codup.co/
 * Description: Let WooCommerce store admin to control the visibility of products based on specific user, user role, geo location, price tier & user group.
 * Version: 1.1.8.6
 * Author: Codup
 * Author URI: http://codup.co/
 * Text Domain: codup-woocommerce-catalog-visibility
 * Domain Path: /languages
 * WC requires at least: 3.8.0
 * WC tested up to: 5.5.2
 *
 * @package catalog-visibility-for-woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if( ! class_exists( 'WC_AM_Client_2_7' ) )
{
	require_once plugin_dir_path( __FILE__ ) .'wc-am-client.php';
}

if( class_exists( 'WC_AM_Client_2_7' ) ) {
	new WC_AM_Client_2_7( __FILE__, '1677', '1.1.8.6', 'plugin', 'https://b2bwoo.com/', 'Catalog Visibility for WooCommerce' );
}

if ( ! defined( 'CWCCV_PLUGIN_PREFIX' ) ) {
	define( 'CWCCV_PLUGIN_PREFIX', 'cwccv' );
}
if ( ! defined( 'CWCCV_PLUGIN_ASSETS_URL' ) ) {
	define( 'CWCCV_PLUGIN_ASSETS_URL', plugin_dir_url( __FILE__ ) . 'includes/assets' );
}

if ( ! defined( 'CWCCV_ABSPATH' ) ) {
	define( 'CWCCV_ABSPATH', dirname( __FILE__ ) );
}

require_once CWCCV_ABSPATH . '/contact_us/class-cwccv-contact-us.php';

/**
 * Check if WooCommerce is active or not
 */
require_once CWCCV_ABSPATH . '/includes/catalog-visibility-dependencies.php';

if ( true != cwcv_woocommerce_active() ) {
	return;
}


// Include here the main loader file.
require_once CWCCV_ABSPATH . '/includes/class-cwccv-helper.php';
require_once CWCCV_ABSPATH . '/includes/classes/class-cwccv-loader.php';

