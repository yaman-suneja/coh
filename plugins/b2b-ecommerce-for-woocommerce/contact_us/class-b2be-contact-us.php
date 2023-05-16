<?php
/**
 * Contact Us Box on settings page.
 *
 * @package codup/templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'B2BE_Contact_Us' ) ) {

	/**
	 * Class B2BE_Contact_Us
	 */
	class B2BE_Contact_Us {

		/**
		 * Function Construct
		 */
		public function __construct() {

			if ( isset( $_GET['page'] ) && isset( $_GET['tab'] ) ) {
				if ( 'wc-settings' == $_GET['page'] && 'codup-b2b-ecommerce' == $_GET['tab'] ) {

					add_action( 'admin_notices', array( $this, 'rfq_contact_us' ), 9999 );

				}
			}
			add_filter( 'plugin_action_links_b2b-ecommerce-for-woocommerce/b2b-ecommerce-for-woocommerce.php', array( $this, 'rfq_settings_link' ) );
			add_filter( 'plugin_row_meta', array( $this, 'rfq_support_and_faq_links' ), 10, 4 );
		}

		/**
		 * Function RFQ support and faq links
		 *
		 * @param array  $links_array Links Array.
		 * @param string $plugin_file_name Plugin File.
		 * @param array  $plugin_data Plugin Data.
		 * @param string $status Status.
		 */
		public function rfq_support_and_faq_links( $links_array, $plugin_file_name, $plugin_data, $status ) {

			if ( 'b2b-ecommerce-for-woocommerce/b2b-ecommerce-for-woocommerce.php' == $plugin_file_name ) {

				$url = '<a href="http://ecommerce.codup.io/support/tickets/new" target="_blank">' . esc_attr__( 'Get Support', 'codup-wcrfq' ) . '</a>';
				/* translators: 1: support link */
				$links_array[] = sprintf( esc_attr__( 'Having trouble in configuration? %s', 'codup-wcrfq' ), wp_kses_post( $url ) );
				$links_array[] = '<a href="mailto:woosupport@codup.io" target="_blank">' . esc_attr__( 'Email Us', 'codup-wcrfq' ) . '</a>';

			}
			return $links_array;
		}

		/**
		 * Function generate settings link.
		 *
		 * @param array $links_array Links Array.
		 */
		public function rfq_settings_link( $links_array ) {

			array_unshift( $links_array, '<a href="' . site_url() . '/wp-admin/admin.php?page=wc-settings&tab=codup-b2b-ecommerce">Settings</a>' );
			return $links_array;

		}

		/**
		 * Function Rfq contact us form.
		 */
		public function rfq_contact_us() {

			?>
			<div class="notice notice-success codup-contact-us" style="position: absolute;right: 40px;">
				<p>
					<?php echo esc_html__( 'Having trouble in configuration?', 'b2b-ecommerce' ); ?> <a href="http://ecommerce.codup.io/support/tickets/new"><?php echo esc_html__( 'Contact us', 'b2b-ecommerce' ); ?></a><?php echo esc_html__( ' for support.', 'b2b-ecommerce' ); ?>
				</p>
				<p>
				<?php echo esc_html__( 'Or email your query at ', 'b2b-ecommerce' ); ?><a href="mailto:woosupport@codup.io"><?php echo esc_html__( ' woosupport@codup.io ', 'b2b-ecommerce' ); ?></a>
				</p>        
			</div>
			<div class="clear"></div>
			<?php
		}
	}

}
new B2BE_Contact_Us();
