<?php
/**
 * API File For Retrieving Roles.
 *
 * @package class-b2be-retrieve-roles-api.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class B2BE_Retrieve_Roles_API.
 */
class B2BE_Retrieve_Roles_API {

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
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_roles' ),
					'permission_callback' => function() {
						return b2be_auth_verification();
					},
				),
			)
		);
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/count',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_roles_count' ),
					'permission_callback' => function() {
						return b2be_auth_verification();
					},
				),
			)
		);

	}

	/**
	 * Get Role according to params provided.
	 *
	 * @param array $request parameters provided.
	 */
	public function get_roles( WP_REST_Request $request ) {

		if ( ! isset( $request->get_params()['name'] ) ) {
			$response['status'] = 404;
			$errors             = __( 'Missing Role Name.' );
			return new WP_Error( __( 'Something went wrong.' ), $errors, $response );
		}

		$args      = array();
		$role_name = strtolower( sanitize_text_field( wp_unslash( $request->get_params()['name'] ) ) );

		if ( isset( $role_name ) && ! empty( $role_name ) ) { // Getting roles...
			$args = array(
				'post_type'      => 'codup-custom-roles',
				'name'           => $role_name,
				'post_status'    => 'publish',
				'posts_per_page' => -1,
			);
		} else {
			$args = array(
				'post_type'      => 'codup-custom-roles',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
			);
		}

		$roles = get_posts( $args );
		if ( empty( $roles ) ) {
			$response['status'] = 404;
			$errors             = 'No role was found matching the given name.';
			return new WP_Error( __( 'Something went wrong.' ), $errors, $response );
		} else {
			$response['status']  = 200;
			$response['message'] = 'Success';
			$response['data']    = $roles;
		}

		return new WP_REST_Response( $response );

	}

	/**
	 * Get Roles Count according to params provided.
	 *
	 * @param array $request parameters provided.
	 */
	public function get_roles_count( WP_REST_Request $request ) {

		$count['count'] = 0;
		$args           = array(
			'post_type'      => 'codup-custom-roles',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
		);

		$roles = get_posts( $args );
		if ( empty( $roles ) ) {
			$response['status']  = 404;
			$response['message'] = 'No roles were found';
			$response['data']    = $roles;
		} else {
			$response['status']  = 200;
			$response['message'] = 'Success';
			$response['data']    = array(
				'count' => count( $roles ),
			);
		}

		return new WP_REST_Response( $response );

	}

}
