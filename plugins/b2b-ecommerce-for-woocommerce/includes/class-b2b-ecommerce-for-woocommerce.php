<?php
/**
 * Class B2BE_RFQ_Loader file.
 *
 * @package b2b-ecommerce-for-woocomerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Gett All the loader files here...

if ( ! class_exists( 'B2B_Ecommerce_For_WooCommerce' ) ) {

	/**
	 *  Class B2B_Ecommerce_For_WooCommerce.
	 */
	class B2B_Ecommerce_For_WooCommerce {

		/**
		 *  Constructor.
		 */
		public function __construct() {

			$this->include_b2b_ecommerce_main_files();

			add_action( 'plugins_loaded', array( $this, 'b2be_load_plugin_textdomain' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_b2b_scripts' ) );
		}

		/**
		 * Include style and scripts in all over pluign.
		 */
		public function enqueue_b2b_scripts() {
			wp_enqueue_style( 'b2b_scripts_style', CWRFQ_ASSETS_DIR_URL . '/css/style.css', array(), rand() );
		}

		/**
		 * Include all the main files of this plugin.
		 */
		public function include_b2b_ecommerce_main_files() {

			include_once 'class-b2b-ecommerce-section.php';
			require_once 'request-for-quote/class-b2be-rfq-loader.php';
			require_once 'signup-form/class-b2be-sign-up-form-loader.php';
			require_once 'payment-method/class-b2be-payment-method-loader.php';
			require_once 'bulk-discounts/class-b2be-bulk-discounts-loader.php';
			require_once 'moq/class-b2be-moq-loader.php';
			require_once 'mov/class-b2be-mov-loader.php';
			require_once 'user-role/class-b2be-user-role-loader.php';
			require_once 're-order/class-b2be-reorder-loader.php';
			require_once 'credit-payment/class-b2be-credit-payment-loader.php';
			require_once 'catalogue-visibility/class-b2be-catalogue-visibility-loader.php';
			require_once 'api/class-b2be-api-loader.php';
			if ( 'yes' == get_option( 'b2be_integrate_balance' ) ) {
				require_once 'balance-integration/class-woocommerce-balance-gateway-loader.php';
			}
		}

		/**
		 * Languages loaded.
		 */
		public function b2be_load_plugin_textdomain() {
			load_plugin_textdomain( 'b2b-ecommerce', false, basename( CWRFQ_BASENAME ) . '/languages/' );
		}

	}
}
