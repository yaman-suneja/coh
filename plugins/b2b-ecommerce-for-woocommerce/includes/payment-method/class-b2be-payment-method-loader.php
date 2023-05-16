<?php
/**
 * WC RFQ.
 *
 * @package b2b-ecommerce-for-woocomerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'B2BE_Payment_Method_Loader' ) ) {
	/**
	 * Class B2BE_Payment_Method.
	 */
	class B2BE_Payment_Method_Loader {
		/**
		 * Cart Variable.
		 */
		public function __construct() {

			$this->includes();
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_payment_method_backend_scripts' ) );

		}

		/**
		 *  Function Includes.
		 */
		public function includes() {

			include_once 'class-b2be-payment-method-settings.php';
			include_once 'class-b2be-payment-method.php';

		}

		/**
		 * Enqueue backend script.
		 *
		 * @param int $page_id Page Id.
		 */
		public function enqueue_payment_method_backend_scripts( $page_id ) {
			if ( 'woocommerce_page_wc-settings' == $page_id ) {

				if ( ( isset( $_GET['tab'] ) && 'checkout' == $_GET['tab'] ) || ( isset( $_GET['section'] ) && 'codup-payment-method' == $_GET['section'] ) ) {

					wp_enqueue_script( 'payment_method_settings_script', CWRFQ_ASSETS_DIR_URL . '/js/payment-method/admin/payment-method-settings.js', array( 'jquery' ), true );
					wp_localize_script(
						'payment_method_settings_script',
						'payment_method_settings',
						array(
							'ajaxurl' => admin_url( 'admin-ajax.php' ),
						)
					);
				}
			}
		}

	}

}
new B2BE_Payment_Method_Loader();
