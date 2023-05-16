<?php
/**
 * WC RFQ.
 *
 * @package b2b-ecommerce-for-woocomerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'B2BE_Payment_Method' ) ) {
	/**
	 * Class B2BE_Payment_Method.
	 */
	class B2BE_Payment_Method {

		/**
		 * Cart Variable.
		 */
		public function __construct() {

			B2BE_Payment_Method_Settings::init();
		}

	}

}
new B2BE_Payment_Method();
