<?php
/**
 * Contact Us Box on settings page.
 *
 * @package codup/templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'CWCCV_Contact_Us' ) ) {

	/**
	 * Class Codup_Contact_Us
	 */
	class CWCCV_Contact_Us {

		/**
		 * Function Construct
		 */
		public function __construct() {

			if ( isset( $_GET['page'] ) && isset( $_GET['tab'] ) ) {
				if ( 'wc-settings' == $_GET['page'] && 'catalog_visibility' == $_GET['tab'] ) {

					add_action( 'admin_notices', array( $this, 'cwccv_contact_us' ), 9999 );

				}
			}
			add_filter( 'plugin_action_links_catalog-visibility-for-woocommerce/catalog-visibility-for-woocommerce.php', array( $this, 'cwccv_settings_link' ) );
			add_filter( 'plugin_row_meta', array( $this, 'cwccv_support_and_faq_links' ), 10, 4 );
		}

		/**
		 * Function cwccv support and faq links
		 *
		 * @param array  $links_array Links Array.
		 * @param string $plugin_file_name Plugin File.
		 * @param array  $plugin_data Plugin Data.
		 * @param string $status Status.
		 */
		public function cwccv_support_and_faq_links( $links_array, $plugin_file_name, $plugin_data, $status ) {

			if ( 'catalog-visibility-for-woocommerce/catalog-visibility-for-woocommerce.php' == $plugin_file_name ) {
				$url = '<a href="http://ecommerce.codup.io/support/tickets/new" target="_blank">' . esc_attr__( 'Get Support', 'codup-woocommerce-catalog-visibility' ) . '</a>';
				/* translators: 1: support link */
				$links_array[] = sprintf( esc_attr__( 'Having trouble in configuration? %s', 'codup-woocommerce-catalog-visibility' ), wp_kses_post( $url ) );

			}
			return $links_array;
		}

		/**
		 * Function generate settings link.
		 *
		 * @param array $links_array Links Array.
		 */
		public function cwccv_settings_link( $links_array ) {

			array_unshift( $links_array, '<a href="' . site_url() . '/wp-admin/admin.php?page=wc-settings&tab=catalog_visibility">Settings</a>' );
			return $links_array;

		}

		/**
		 * Function Rfq contact us form.
		 */
		public function cwccv_contact_us() {

			?>
			<div class="notice notice-success codup-contact-us" style="position: absolute;right: 20px;">
				<p>
					<?php
					$url  = '<a href="http://ecommerce.codup.io/support/tickets/new">' . esc_attr__( ' Contact us', 'codup-woocommerce-catalog-visibility' ) . '</a> ';
					$url2 = '<a href="mailto:woosupport@codup.io"> woosupport@codup.io </a> ';
					/* translators: 1: support url */
					echo sprintf( esc_attr__( 'Having trouble in configuration? %s for support', 'codup-woocommerce-catalog-visibility' ), wp_kses_post( $url ) );
					?>
				</p>
				<p>
					<?php /* translators: 1: support email */ echo sprintf( esc_attr__( 'Or email your query at %s ', 'codup-woocommerce-catalog-visibility' ), wp_kses_post( $url2 ) ); ?>
				</p>    
			</div>
			<div class="clear"></div>
			<?php
		}
	}

}
new CWCCV_Contact_Us();
