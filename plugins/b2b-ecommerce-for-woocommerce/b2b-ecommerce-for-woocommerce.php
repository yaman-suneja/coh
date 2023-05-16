<?php
/**
 * Plugin Name: B2B E-commerce For WooCommerce
 * Description: The ultimate wholesale extension for WooCommerce stores.
 * Author: Codup
 * Author URI: http://codup.co/
 * Version: 1.3.20.4
 * Domain Path: /languages
 * Text Domain: b2b-ecommerce
 * Woo: 5237459:c1e9d6d4f0129a8063339e3020221aa8
 * WC requires at least: 3.8.0
 * WC tested up to: 6.3.1
 *
 * @package b2b-ecommerce-for-woocommerce
 */

// prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if( ! class_exists( 'WC_AM_Client_2_7' ) )
{
	require_once plugin_dir_path( __FILE__ ) .'wc-am-client.php';
}

if( class_exists( 'WC_AM_Client_2_7' ) ) {
	new WC_AM_Client_2_7( __FILE__, '1680', '1.3.20.4', 'plugin', 'https://b2bwoo.com/', 'B2B E-commerce For WooCommerce' );
}

define( 'CWRFQ_PLUGIN_DIR', __DIR__ );
define( 'CWRFQ_PLUGIN_NAME', 'B2B E-commerce For WooCommerce' );
define( 'CWRFQ_PLUGIN_DIR_URL', plugin_dir_url( __FILE__ ) );
define( 'CWRFQ_TEMPLATE_DIR', CWRFQ_PLUGIN_DIR . '/templates/request-for-quote/' );
define( 'CWSFG_TEMPLATE_DIR', CWRFQ_PLUGIN_DIR . '/templates/signup-form/' );
define( 'CWRFQ_ASSETS_DIR_URL', CWRFQ_PLUGIN_DIR_URL . 'assets/' );
define( 'B2BE_CATALOGUE_VISIBILITY_PREFIX', 'b2be_catalogue' );

require_once CWRFQ_PLUGIN_DIR . '/includes/functions.php';
require_once CWRFQ_PLUGIN_DIR . '/constants.php';
require_once CWRFQ_PLUGIN_DIR . '/includes/class-b2b-ecommerce-for-woocommerce.php';
require_once CWRFQ_PLUGIN_DIR . '/contact_us/class-b2be-contact-us.php';

/**
 * Check if WooCommerce is activated
 */
if ( b2be_is_woocommerce_activated() ) {
	b2be_create_tables();
	new B2B_Ecommerce_For_WooCommerce();
	register_activation_hook( __FILE__, 'b2be_register_default_settings_for_rfq' );
}
