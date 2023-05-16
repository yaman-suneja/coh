<?php
/**
 * WC RFQ.
 *
 * @package b2b-ecommerce-for-woocomerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'B2BE_User_Role_Loader' ) ) {
	/**
	 * Class B2BE_User_Role.
	 */
	class B2BE_User_Role_Loader {
		/**
		 * Cart Variable.
		 */
		public function __construct() {
			$this->includes();
			add_action( 'user_register', array( $this, 'enable_payment_method_on_user_creation' ), 99, 2 );
		}

		/**
		 *  Function Includes.
		 */
		public function includes() {
			include_once 'class-b2be-user-role.php';
			include_once 'b2be-user-role-function.php';
			include_once 'class-b2be-custom-roles-cpt.php';
			include_once 'class-b2be-custom-roles-order-filter.php';
		}

		public function enable_payment_method_on_user_creation( $user_id, $user_data ) {

			$gateways = b2be_get_formatted_payment_methods( 'woocommerce' );
			if ( ! empty( $gateways ) ) {

				$user      = new WP_User( $user_id );
				$post_name = $user->roles;

				foreach ( $gateways as $id => $payment_method_name ) {
					update_user_meta( $user_id, $id, 'no' );
					if ( $post_name ) {
						$role_id = b2be_custom_role_exists( $post_name[0] );
						if ( 0 != $role_id && 'yes' == get_post_meta( $role_id, $id, true ) ) {
							update_user_meta( $user_id, $id, 'yes' );
						} else {
							update_user_meta( $user_id, $id, 'no' );
						}
					}
				}
			}

		}

	}

}
new B2BE_User_Role_Loader();
