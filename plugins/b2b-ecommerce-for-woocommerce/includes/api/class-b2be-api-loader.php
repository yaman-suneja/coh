<?php
/**
 * B2B Ecommerce API Loader File.
 *
 * @package class-b2be-api-loader.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'B2BE_API_Loader' ) ) {

	/**
	 * Class B2BE_API_Loader.
	 */
	class B2BE_API_Loader {

		/**
		 * Constructor
		 */
		public function __construct() {

			add_action( 'rest_api_init', array( $this, 'b2be_register_api_route' ) );

		}

		/**
		 * Registering Api Routes for B2B Ecommerce for wocommerce.
		 */
		public function b2be_register_api_route() {
			include dirname( __FILE__ ) . '/b2be-api-functions.php';
			include_once dirname( __FILE__ ) . '/request-for-quote/class-b2be-create-quotes-api.php';
			include_once dirname( __FILE__ ) . '/request-for-quote/class-b2be-update-quotes-api.php';
			include_once dirname( __FILE__ ) . '/request-for-quote/class-b2be-retrieve-quotes-api.php';
			include_once dirname( __FILE__ ) . '/request-for-quote/class-b2be-delete-quotes-api.php';
			include_once dirname( __FILE__ ) . '/request-for-quote/class-b2be-delete-quotes-line-items-api.php';
			include_once dirname( __FILE__ ) . '/user-role/class-b2be-create-roles-api.php';
			include_once dirname( __FILE__ ) . '/user-role/class-b2be-update-roles-api.php';
			include_once dirname( __FILE__ ) . '/user-role/class-b2be-delete-roles-api.php';
			include_once dirname( __FILE__ ) . '/user-role/class-b2be-retrieve-roles-api.php';
			include_once dirname( __FILE__ ) . '/required-login-for-catalogue/class-b2be-update-price-visibility-api.php';
			include_once dirname( __FILE__ ) . '/payment-gateways/class-b2be-retrieve-payment-gateways-api.php';

			$api_classes = apply_filters(
				'b2be_api_classes',
				array(
					'B2BE_Retrieve_Quotes_API',
					'B2BE_Create_Quotes_API',
					'B2BE_Update_Quotes_API',
					'B2BE_Delete_Quotes_API',
					'B2BE_Delete_Quotes_Line_Items_API',
					'B2BE_Create_Roles_API',
					'B2BE_Update_Roles_API',
					'B2BE_Retrieve_Roles_API',
					'B2BE_Delete_Roles_API',
					'B2BE_Update_Price_Visibility_API',
					'B2BE_Retrieve_Payment_Gateways_API',
				)
			);

			foreach ( $api_classes as $api_class ) {

				$api_object = new $api_class();
				$api_object->register_route();

			}

		}

	}

}
new B2BE_API_Loader();
