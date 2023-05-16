<?php
/**
 * API File For Update Roles.
 *
 * @package class-b2be-update-roles-api.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class B2BE_Update_Roles_API.
 */
class B2BE_Update_Roles_API {

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
	protected $rest_base = 'roles';

	/**
	 * Register Route for this api.
	 */
	public function register_route() {

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/update',
			array(
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_role' ),
					'permission_callback' => function() {
						return b2be_auth_verification();
					},
				),
			)
		);

	}

	/**
	 * Update Role according to params provided.
	 *
	 * @param array $request parameters provided.
	 */
	public function update_role( WP_REST_Request $request ) {

		$parameters = $request->get_params();

		if ( ! isset( $parameters['name'] ) ) {
			$response['status'] = 404;
			$errors             = __( 'Missing Name key. It is required.' );
			return new WP_Error( __( 'Something went wrong.' ), $errors, $response );
		} elseif ( isset( $parameters['name'] ) && empty( $parameters['name'] ) ) {
			$response['status'] = 404;
			$errors             = __( 'Missing Role Name.' );
			return new WP_Error( __( 'Something went wrong.' ), $errors, $response );
		}

		if ( empty( $request->get_params() ) ) {
			$response['status'] = 412;
			$errors             = __( 'Missing required values ' );
			return new WP_Error( __( 'Something went wrong.' ), $errors, $response );
		}
		$role_name = str_replace( ' ', '-', strtolower( sanitize_text_field( wp_unslash( $parameters['name'] ) ) ) );

		if ( ! role_exists( $role_name ) ) {
			$response['status'] = 409;
			$errors             = __( 'No Role exist with this name.' );
			return new WP_Error( __( 'Something went wrong.' ), $errors, $response );
		}

		$b2be_role_id = b2be_custom_role_exists( $role_name );

		$errors = $this->validating_provided_params( $parameters, $b2be_role_id );

		if ( $errors ) {
			$response['errors']      = $errors;
			$response['error_count'] = count( $errors );
		}

		$response['status']  = 200;
		$response['message'] = 'Role Updated Successfully.';

		return new WP_REST_Response( $response );

	}

	/**
	 * Validating params provided.
	 *
	 * @param array $parameters parameters provided.
	 * @param int   $b2be_role_id Role Id for which the params are being validating.
	 */
	public function validating_provided_params( $parameters = array(), $b2be_role_id ) {

		if ( empty( $parameters ) ) {
			return false;
		}

		if ( isset( $parameters['enable_rfq'] ) && ! empty( $parameters['enable_rfq'] ) ) {
			if ( 'yes' == $parameters['enable_rfq'] ) {
				update_post_meta( $b2be_role_id, 'enable_rfq', $parameters['enable_rfq'] );
			} elseif ( 'no' == $parameters['enable_rfq'] ) {
				update_post_meta( $b2be_role_id, 'enable_rfq', $parameters['enable_rfq'] );
			} else {
				$errors['enable_rfq'] = 'Value provide for this setting is not valid';
			}
		}
		if ( isset( $parameters['disable_add_to_cart'] ) && ! empty( $parameters['disable_add_to_cart'] ) ) {
			if ( 'yes' == $parameters['disable_add_to_cart'] ) {
				update_post_meta( $b2be_role_id, 'disable_add_to_cart', $parameters['disable_add_to_cart'] );
			} elseif ( 'no' == $parameters['disable_add_to_cart'] ) {
				update_post_meta( $b2be_role_id, 'disable_add_to_cart', $parameters['disable_add_to_cart'] );
			} else {
				$errors['disable_add_to_cart'] = 'Value provide for this setting is not valid';
			}
		}
		if ( isset( $parameters['tax_exempt'] ) && ! empty( $parameters['tax_exempt'] ) ) {
			$role_tax_classes = array();
			$tax_classes      = json_decode( $parameters['tax_exempt'] );
			if ( is_array( $tax_classes ) ) {

				foreach ( $tax_classes as $key => $tax_class ) {
					foreach ( $tax_class as $class => $value ) {
						if ( 'yes' == $value ) {
							$role_tax_classes[ $class ] = 'on';
						} elseif ( 'no' == $value ) {
							$role_tax_classes[ $class ] = '';
						} else {
							$errors[ $class ] = 'Value provide for this setting is not valid';
						}
					}
				}
				update_post_meta( $b2be_role_id, 'tax_exempt', $role_tax_classes );
			} else {
				$errors['tax_exempt'] = 'Value provide for this setting is not valid';
			}
		}
		if ( isset( $parameters['shipping_exempt'] ) && ! empty( $parameters['shipping_exempt'] ) ) {
			$role_shipping_classes = array();
			$shipping_classes      = json_decode( $parameters['shipping_exempt'] );
			if ( is_array( $shipping_classes ) ) {
				foreach ( $shipping_classes as $key => $shipping_class ) {
					foreach ( $shipping_class as $class => $value ) {
						if ( 'yes' == $value ) {
							$role_shipping_classes[ $class ] = 'on';
						} elseif ( 'no' == $value ) {
							$role_shipping_classes[ $class ] = '';
						} else {
							$errors[ $class ] = 'Value provide for this setting is not valid';
						}
					}
				}
				update_post_meta( $b2be_role_id, 'shipping_exempt', $role_shipping_classes );
			} else {
				$errors['shipping_exempt'] = 'Value provide for this setting is not valid';
			}
		}
		if ( isset( $parameters['enable_b2b_credit_payment'] ) && ! empty( $parameters['enable_b2b_credit_payment'] ) ) {
			if ( 'yes' == $parameters['enable_b2b_credit_payment'] ) {
				update_post_meta( $b2be_role_id, 'enable_b2b_credit_payment', 'on' );
			} elseif ( 'no' == $parameters['enable_b2b_credit_payment'] ) {
				update_post_meta( $b2be_role_id, 'enable_b2b_credit_payment', '' );
			} else {
				$errors['enable_b2b_credit_payment'] = 'Value provide for this setting is not valid';
			}
		}
		if ( isset( $parameters['ccr_credit_value'] ) && ! empty( $parameters['ccr_credit_value'] ) ) {
			if ( is_numeric( $parameters['ccr_credit_value'] ) ) {

				update_post_meta( $b2be_role_id, 'ccr_credit_value', $parameters['ccr_credit_value'] );
				b2be_maintain_credit_payments( $b2be_role_id, b2be_user_id_for_api(), sanitize_text_field( wp_unslash( $parameters['ccr_credit_value'] ) ), 'Add Credit Balance' );

				$all_users = get_users( array( 'role__in' => array( $post->post_name ) ) );
				foreach ( $all_users as $_user ) {
					$credit_balance = get_user_meta( $_user->ID, 'credit_payment_bal', true, 0 ) + sanitize_text_field( wp_unslash( $parameters['ccr_credit_value'] ) );
					update_user_meta( $_user->ID, 'credit_payment_bal', $credit_balance );
				}
			} else {
				$errors['ccr_credit_value'] = 'Value provide for this setting is not valid';
			}
		}

		if ( isset( $parameters['b2be_role_woocomerce_payment_method'] ) && ! empty( $parameters['b2be_role_woocomerce_payment_method'] ) ) {
			$gateways_by_user = json_decode( $parameters['b2be_role_woocomerce_payment_method'] );
			$gateways         = b2be_get_formatted_payment_methods( 'woocommerce' );
			if ( is_array( $gateways_by_user ) ) {
				foreach ( $gateways_by_user as $key => $gateway ) {
					foreach ( $gateway as $name => $value ) {
						if ( in_array( $name, array_keys( $gateways ) ) ) {
							if ( 'yes' == $value ) {
								update_post_meta( $b2be_role_id, $name, $value );
							} elseif ( 'no' == $value ) {
								update_post_meta( $b2be_role_id, $name, $value );
							} else {
								$errors[ $name ] = 'Value provide for this setting is not valid';
							}
						} else {
							$errors[ $name ] = 'The payment gateway' . $name . 'is either not enable from payment tab or it doesn\'t exist';
						}
					}
				}
			} else {
				$errors['b2be_role_woocomerce_payment_method'] = 'Value provide for this setting is not valid';
			}
		}
		if ( isset( $parameters['b2be_role_based_payment_method'] ) && ! empty( $parameters['b2be_role_based_payment_method'] ) ) {

			if ( in_array( $parameters['b2be_role_based_payment_method'], array_keys( get_b2be_custom_payment_methods() ) ) ) {
				update_post_meta( $b2be_role_id, 'b2be_role_based_payment_method', $parameters['b2be_role_based_payment_method'] );
			} else {
				$errors['b2be_role_based_payment_method'] = 'Value provide for this setting is not valid';
			}
		}

		return $errors;
	}

}
