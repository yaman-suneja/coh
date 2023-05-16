<?php
/**
 * Class to register main files of this plugin...
 *
 * @package woocommerce-getbalance-gateway/includes
 */

if ( ! class_exists( 'Woocommerce_Balance_Gateway_Loader' ) ) {

	/**
	 * Class Woocommerce_Balance_Gateway_Loader
	 */
	class Woocommerce_Balance_Gateway_Loader {

		/**
		 * Constructor.
		 */
		public function __construct() {

			$this->include_main_files();
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_backend_scripts' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_scripts' ) );

		}

		/**
		 * Enqueue Backend scripts.
		 */
		public function enqueue_backend_scripts() {

			wp_enqueue_script( 'woocommerce-balance-gateway', CWRFQ_ASSETS_DIR_URL . 'js/admin/woocommerce-balance-gateway.js', array( 'jquery' ), rand() );

		}

		/**
		 * Enqueue Frontend scripts.
		 */
		public function enqueue_frontend_scripts() {

			wp_enqueue_script( 'woocommerce-balance-gateway-checkout', CWRFQ_ASSETS_DIR_URL . 'js/balance-gateway/balance-gateway-checkout.js', array( 'jquery' ), rand() );
			wp_localize_script(
				'woocommerce-balance-gateway-checkout',
				'wcgb_checkout',
				array(
					'order_key'      => isset( $_GET['key'] ) ? sanitize_text_field( wp_unslash( $_GET['key'] ) ) : '',
					'order_token'    => isset( $_GET['token'] ) ? sanitize_text_field( wp_unslash( $_GET['token'] ) ) : '',
					'order_id'       => isset( $_GET['order_id'] ) ? sanitize_text_field( wp_unslash( $_GET['order_id'] ) ) : '',
					'transaction_id' => isset( $_GET['transaction_id'] ) ? sanitize_text_field( wp_unslash( $_GET['transaction_id'] ) ) : '',
					'url'            => wc_get_checkout_url(),
					'wcgb_ajax_url'  => admin_url( 'admin-ajax.php' ),
				)
			);
		}

		/**
		 * Enqueue main files.
		 */
		public function include_main_files() {

			if ( is_admin() ) {
				include_once 'class-woocommerce-balance-gateway-settings.php';
			}
			include_once 'class-woocommerce-balance-integration.php';
			include_once 'functions.php';

		}

	}
}
new Woocommerce_Balance_Gateway_Loader();
