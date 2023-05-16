<?php
/*
Plugin Name: iThemeland WooCommerce Bulk Orders Editing Lite
Plugin URI: https://ithemelandco.com/plugins/wordpress-bulk-orders-editing
Description: Editing Date in WordPress is very painful. Be professionals with managing data in the reliable and flexible way by WooCommerce Bulk Order Editor.
Author: iThemelandco
Tested up to: WP 5.3
Requires PHP: 5.4
Tags: woocommerce,woocommerce bulk edit,bulk edit,bulk,orders bulk editor
Text Domain: ithemeland-woocommerce-bulk-orders-editing-lite
Domain Path: /languages
WC requires at least: 3.3.1
WC tested up to: 3.8
Version: 2.1.0
Author URI: https://www.ithemelandco.com
*/

defined('ABSPATH') || exit();

if (defined('WOBEF_NAME')) {
    return false;
}

require_once __DIR__ . '/vendor/autoload.php';

define('WOBEF_NAME', 'ithemeland-woocommerce-bulk-orders-editing-lite');
define('WOBEF_LABEL', 'Ithemeland Woocommerce Bulk Orders Editing Lite');
define('WOBEF_DIR', trailingslashit(plugin_dir_path(__FILE__)));
define('WOBEF_PLUGIN_MAIN_PAGE', admin_url('admin.php?page=wobef'));
define('WOBEF_URL', trailingslashit(plugin_dir_url(__FILE__)));
define('WOBEF_LIB_DIR', trailingslashit(WOBEF_DIR . 'classes/lib'));
define('WOBEF_VIEWS_DIR', trailingslashit(WOBEF_DIR . 'views'));
define('WOBEF_LANGUAGES_DIR', dirname(plugin_basename(__FILE__)) . '/languages/');
define('WOBEF_ASSETS_DIR', trailingslashit(WOBEF_DIR . 'assets'));
define('WOBEF_ASSETS_URL', trailingslashit(WOBEF_URL . 'assets'));
define('WOBEF_CSS_URL', trailingslashit(WOBEF_ASSETS_URL . 'css'));
define('WOBEF_IMAGES_URL', trailingslashit(WOBEF_ASSETS_URL . 'images'));
define('WOBEF_JS_URL', trailingslashit(WOBEF_ASSETS_URL . 'js'));
define('WOBEF_VERSION', '2.1.0');
define('WOBEF_UPGRADE_URL', 'https://ithemelandco.com/plugins/woocommerce-bulk-orders-editing?utm_source=free_plugins&utm_medium=plugin_links&utm_campaign=user-lite-buy');
define('WOBEF_UPGRADE_TEXT', 'Download Pro Version');

register_activation_hook(__FILE__, ['wobef\classes\bootstrap\WOBEF', 'activate']);
register_deactivation_hook(__FILE__, ['wobef\classes\bootstrap\WOBEF', 'deactivate']);

add_action('init', ['wobef\classes\bootstrap\WOBEF', 'wobef_wp_init']);

add_action('plugins_loaded', function () {
    if (!class_exists('WooCommerce')) {
        wobef\classes\bootstrap\WOBEF::wobef_woocommerce_required();
    } else {
        \wobef\classes\bootstrap\WOBEF::init();
    }
}, PHP_INT_MAX);
