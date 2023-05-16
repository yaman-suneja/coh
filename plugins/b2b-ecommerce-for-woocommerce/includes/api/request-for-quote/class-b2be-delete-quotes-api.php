<?php
/**
 * API File For Delete Quotes.
 *
 * @package class-b2be-delete-quotes-api.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class B2BE_Delete_Quotes_API.
 */
class B2BE_Delete_Quotes_API {

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
	protected $rest_base = 'quotes';

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
					'callback'            => array( $this, 'delete_quote' ),
					'permission_callback' => function() {
						return b2be_auth_verification();
					},
				),
			)
		);

	}

	/**
	 * Delete Quote according to params provided.
	 *
	 * @param array $request parameters provided.
	 */
	public function delete_quote( WP_REST_Request $request ) {

		$parameters = $request->get_params();

		if ( ! isset( $parameters['quote_id'] ) || empty( $parameters['quote_id'] ) ) {
			$response['status'] = 412;
			$errors             = __( 'No quote id provided.' );
			return new WP_Error( __( 'Something went wrong.' ), $errors, $response );
		}

		$quote = wc_get_quote( intval( $parameters['quote_id'] ) );
		if ( empty( $quote->get_prop( 'status' ) ) ) {
			$response['status'] = 404;
			$errors             = __( 'No Quote found against this quote id.' );
			return new WP_Error( __( 'Something went wrong.' ), $errors, $response );
		}

		wp_trash_post( intval( $parameters['quote_id'] ) );

		$response['status']  = 200;
		$response['message'] = 'Quote Deleted successfully.';

		return new WP_REST_Response( $response );

	}

}
