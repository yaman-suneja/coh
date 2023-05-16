<?php
/**
 * API File For Delete Role.
 *
 * @package class-b2be-delete-role-api.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class B2BE_Delete_Roles_API.
 */
class B2BE_Delete_Roles_API {

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
			'/' . $this->rest_base . '/delete',
			array(
				array(
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'delete_role' ),
					'permission_callback' => function() {
						return b2be_auth_verification();
					},
				),
			)
		);

	}

	/**
	 * Delete Role according to params provided.
	 *
	 * @param array $request parameters provided.
	 */
	public function delete_role( WP_REST_Request $request ) {

		$role_name = strtolower( str_replace( ' ', '_', sanitize_text_field( wp_unslash( $request->get_params()['name'] ) ) ) );

		if ( ! isset( $request->get_params()['name'] ) || empty( $role_name ) ) {
			$response['status'] = 412;
			$errors             = __( 'No role name provided.' );
			return new WP_Error( __( 'Something went wrong.' ), $errors, $response );
		}

		if ( ! role_exists( $role_name ) ) {
			$response['status'] = 404;
			$errors             = __( 'No role exist with this name.' );
			return new WP_Error( __( 'Something went wrong.' ), $errors, $response );
		}

		$role_id = b2be_custom_role_exists( $role_name );
		if ( ! $role_id ) {
			$response['status'] = 404;
			$errors             = __( 'No role found against this role id.' );
			return new WP_Error( __( 'Something went wrong.' ), $errors, $response );
		}

		remove_role( $role_name );
		wp_trash_post( intval( $role_id ) );

		$response['status']  = 200;
		$response['message'] = 'Role Deleted successfully.';

		return new WP_REST_Response( $response );

	}

}
