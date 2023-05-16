<?php
/**
 * File for dependencies functions.
 *
 * @package catalog-visibility-for-woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return about woocommerce is active or not.
 */
function cwcv_woocommerce_active() {

	register_activation_hook( __FILE__, 'cwcv_activate' );
	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'cwcv_plugin_settings_link' );

	$active_plugins = (array) get_option( 'active_plugins', array() );

	if ( is_multisite() ) {
		$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
	}

	if ( in_array( 'woocommerce/woocommerce.php', $active_plugins ) || array_key_exists( 'woocommerce/woocommerce.php', $active_plugins ) ) {

		return true;

	} else {

		add_action( 'admin_notices', 'cwcv_admin_notice_plugin_dependencies' );
		return false;
	}

}

/**
 * Show admin notice if WooCommerce plugin is not active
 */
function cwcv_admin_notice_plugin_dependencies() {
	?>
<div id="message" class="error">
	<p>
		<?php

		$install_url = wp_nonce_url(
			add_query_arg(
				array(
					'action' => 'install-plugin',
					'plugin' => 'woocommerce',
				),
				admin_url( 'update.php' )
			),
			'install-plugin_woocommerce'
		);

			/* translators: %s: is activated */
					printf( esc_html__( 'The %3$sWooCommerce plugin%4$s must be active for %1$s Catalog Visibility for WooCommerce %2$s to work. Please %5$sinstall & activate WooCommerce &raquo;%6$s', 'codup-wc-referral-system' ), '<strong>', '</strong>', '<a href="http://wordpress.org/extend/plugins/woocommerce/">', '</a>', '<a href="' . esc_url( $install_url ) . '">', '</a>' );
		?>


	</p>
</div>
	<?php
}

/**
 * Add settings hyper link on plugin activation page
 *
 * @param array $links Links.
 */
function wcrs_plugin_settings_link( $links ) {
	$links[] = '<a href="' .
		admin_url( 'admin.php?page=wc-settings&tab=catalog_visibility' ) .
		'">' . __( 'Settings' ) . '</a>';
	return $links;
}
