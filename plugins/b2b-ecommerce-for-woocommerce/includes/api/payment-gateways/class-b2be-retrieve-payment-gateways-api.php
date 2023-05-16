<?php
/**
 * API File For Retrieve Payment Gateways.
 *
 * @package class-b2be-retrieve-payment-gateways-api.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/**
 * Class B2BE_Retrieve_Payment_Gateways_API.
 */
class B2BE_Retrieve_Payment_Gateways_API {

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'b2be';

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'payment-gateways';

	/**
	 * Register Route for this api.
	 */
	public function register_route() {

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_payment_gateways' ),
					'permission_callback' => function() {
						return b2be_auth_verification();
					},
				),
			)
		);

	}

	/**
	 * Retrieve Payment gateways according to params provided.
	 *
	 * @param array $request parameters provided.
	 */
	public function get_payment_gateways( WP_REST_Request $request ) {

		$parameters      = $request->get_params();
		$payment_gatways = array();
		$format          = $parameters['format'];

		if ( isset( $parameters['user_id'] ) ) {
			$user_id = $parameters['user_id'];
			if ( empty( $user_id ) ) {
				$response['status'] = 404;
				$errors             = __( 'Missing user id.' );
				return new WP_Error( __( 'Something went wrong.' ), $errors, $response );
			} elseif ( ! get_userdata( $user_id ) ) {
				$response['status'] = 412;
				$errors             = __( 'No user found.' );
				return new WP_Error( __( 'Something went wrong.' ), $errors, $response );
			}
		}

		if ( isset( $parameters['role_name'] ) ) {
			$role_name = sanitize_text_field( wp_unslash( strtolower( str_replace( ' ', '-', $parameters['role_name'] ) ) ) );
			if ( empty( $role_name ) ) {
				$response['status'] = 404;
				$errors             = __( 'Missing role name.' );
				return new WP_Error( __( 'Something went wrong.' ), $errors, $response );
			} elseif ( ! role_exists( $role_name ) ) {
				$response['status'] = 412;
				$errors             = __( 'No Role found.' );
				return new WP_Error( __( 'Something went wrong.' ), $errors, $response );
			}
		}

		if ( isset( $parameters['format'] ) && ! empty( $format ) ) {

			switch ( $format ) {
				case 'woocommerce':
					$payment_gatways = b2be_get_formatted_payment_methods( $format );
					break;

				case 'b2be_ecommerce':
					$payment_gatways = b2be_get_formatted_payment_methods( $format );
					break;
				default:
					$response['status'] = 412;
					$errors             = __( 'Wrong format given.' );
					return new WP_Error( __( 'Something went wrong.' ), $errors, $response );
					break;
			}
		} elseif ( isset( $parameters['user_id'] ) && ! empty( $user_id ) ) {
			$payment_gatways = b2be_get_payment_methods_by_user_id( $user_id );
		} elseif ( isset( $parameters['role_name'] ) && ! empty( $role_name ) ) {
			$payment_gatways = b2be_get_payment_methods_by_role( $role_name );
		} else {
			$payment_gatways = b2be_get_formatted_payment_methods();
		}

		if ( empty( $payment_gatways ) ) {
			$response['status'] = 412;
			$errors             = __( 'No Payment Gateways found.' );
			return new WP_Error( __( 'Something went wrong.' ), $errors, $response );
		}

		$response['status']  = 200;
		$response['message'] = 'Success.';
		$response['data']    = $payment_gatways;

		return new WP_REST_Response( $response );

	}

}
