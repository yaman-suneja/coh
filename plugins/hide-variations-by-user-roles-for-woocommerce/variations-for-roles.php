<?php
/*
	* Plugin Name:  Hide Variations by User Roles
	* Plugin URI: https://woocommerce.com/products/hide-variations-by-user-roles
	* Description: Hide specific variations based on user roles.
	* Author: Addify
	* Author URI: http://www.addify.co
	* Version: 1.0.0
	* Domain Path:       /languages
	* Text Domain:       addf_vf_roles_dl
	* Woo: 7937663:cf8317c7908f4ba171e3f131cc50cdeb
	* WC requires at least: 3.0.9
	* WC tested up to: 5.*.*
*/

// Check the installation of WooCommerce module if it is not a multi site.
if ( ! is_multisite() ) {
	if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) ) {
		function addf_gr_admin_notice() {
			// Deactivate the plugin.
			deactivate_plugins( __FILE__ );
			$afpvu_woo_check = '<div id="message" class="error">
				<p><strong>' . esc_html__( 'KoalaApps - Variations for user roles is inactive.', 'addf_vf_roles_dl' ) . '</strong> The <a href="http://wordpress.org/extend/plugins/woocommerce/">WooCommerce plugin</a> ' . esc_html__( 'must be active for this plugin to work. Please install &amp; activate WooCommerce.', 'addf_vf_roles_dl' ) . ' »</p></div>';
			echo wp_kses_post( $afpvu_woo_check );
		}
		add_action( 'admin_notices', 'addf_gr_admin_notice' );
	}
}
class ADDF_VF_Roles_Main_Class {
	
	public function __construct() {
		$this->addfl_global_constents_vars();
		add_action( 'wp_loaded', array( $this, 'addf_vf_roles_load_text_domain' ) );
		if ( is_admin() ) {
			include_once ADDF_VF_ROLES . '/admin/class-vf-roles-admin.php';
		} else {
			include_once ADDF_VF_ROLES . '/front/class-vf-roles-front.php';
		}
	}
	public function addf_vf_roles_load_text_domain() {
		if ( function_exists( 'load_plugin_textdomain' ) ) {
			load_plugin_textdomain( 'addf_vf_roles', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		}
	}
	public function addfl_global_constents_vars() {
		if ( ! defined( 'ADDF_VF_ROLES_URL' ) ) {
			define( 'ADDF_VF_ROLES_URL', plugin_dir_url( __FILE__ ) );
		}

		if ( ! defined( 'ADDF_VF_ROLES_BASENAME' ) ) {
			define( 'ADDF_VF_ROLES_BASENAME', plugin_basename( __FILE__ ) );
		}
		if ( ! defined( 'ADDF_VF_ROLES' ) ) {
			define( 'ADDF_VF_ROLES', plugin_dir_path( __FILE__ ) );
		}
	}
}
new ADDF_VF_Roles_Main_Class();
