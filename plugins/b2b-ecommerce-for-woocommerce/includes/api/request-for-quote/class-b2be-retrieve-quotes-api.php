<?php
/**
 * API File For Retrieving Quotes.
 *
 * @package class-b2be-retrieve-quotes-api.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class B2BE_Retrieve_Quotes_API.
 */
class B2BE_Retrieve_Quotes_API {

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
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_quotes' ),
					'permission_callback' => function() {
						return b2be_auth_verification();
					},
					'args'                => array(
						'id'     => array(
							'validate_callback' => function( $param, $request, $key ) {
								return is_numeric( $param );
							},
						),
						'status' => array(
							'validate_callback' => function( $param, $request, $key ) {
								return in_array( $param, array_keys( wcrfq_get_quotes_statuses() ) );
							},
						),
					),
				),
			)
		);
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/count',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_quotes_count' ),
					'permission_callback' => function() {
						return b2be_auth_verification();
					},
				),
			)
		);

	}

	/**
	 * Get Quotes according to params provided.
	 *
	 * @param array $request parameters provided.
	 */
	public function get_quotes( WP_REST_Request $request ) {

		$args            = array();
		$quote_id        = $request->get_params()['id'];
		$quote_status    = $request->get_params()['status'];
		$quotes_per_page = get_option( 'posts_per_page' );

		if ( isset( $quote_id ) && ! empty( $quote_id ) ) { // Getting quotes by id..
			return get_post( $quote_id );
		} elseif ( isset( $quote_status ) && ! empty( $quote_status ) ) { // Getting quotes by status..
			$args = array(
				'post_type'      => 'quote',
				'post_status'    => $quote_status,
				'posts_per_page' => $quotes_per_page,
			);
		} else {
			$args = array(
				'post_type'      => 'quote',
				'post_status'    => 'any',
				'posts_per_page' => $quotes_per_page,
			);
		}

		$quotes = get_posts( $args );
		if ( empty( $quotes ) ) {
			$response['status']  = 200;
			$response['message'] = 'No Quotes were found';
			$response['data']    = $quotes;
		} else {
			$response['status']  = 200;
			$response['message'] = 'Success';
			$response['data']    = $quotes;
		}

		return new WP_REST_Response( $response );

	}

	/**
	 * Get Count of Quotes according to params provided.
	 *
	 * @param array $request parameters provided.
	 */
	public function get_quotes_count( WP_REST_Request $request ) {

		$count['count'] = 0;
		$args           = array(
			'post_type'      => 'quote',
			'post_status'    => 'any',
			'posts_per_page' => -1,
		);

		$quotes = get_posts( $args );
		if ( empty( $quotes ) ) {
			$response['status']  = 200;
			$response['message'] = 'No Quotes were found';
			$response['data']    = $quotes;
		} else {
			$response['status']  = 200;
			$response['message'] = 'Success';
			$response['data']    = array(
				'count' => count( $quotes ),
			);
		}

		return new WP_REST_Response( $response );

	}

}
