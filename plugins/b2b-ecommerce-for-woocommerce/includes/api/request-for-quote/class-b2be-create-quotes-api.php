<?php
/**
 * API File For Creating Quotes.
 *
 * @package class-b2be-create-quotes-api.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class B2BE_Create_Quotes_API.
 */
class B2BE_Create_Quotes_API {

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
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'create_quote' ),
					'permission_callback' => function() {
						return b2be_auth_verification();
					},
				),
			)
		);
	}

	/**
	 * Create Quote according to params provided.
	 *
	 * @param array $request parameters provided.
	 */
	public function create_quote( WP_REST_Request $request ) {

		$parameters = $request->get_params();

		if ( empty( $request->get_params() ) ) {
			$response['status'] = 412;
			$errors             = __( 'Missing required values ' );
			return new WP_Error( __( 'Something went wrong.' ), $errors, $response );
		}

		$items          = array();
		$customer_email = $parameters['customer_email'];
		$customer       = get_user_by( 'email', $customer_email );

		if ( empty( $customer ) ) {
			$response['status'] = 404;
			$errors             = __( 'Invalid Email. No user found against this email.' );
			return new WP_Error( __( 'Something went wrong.' ), $errors, $response );
		}

		if ( ! isset( $parameters['items'] ) ) {
			$response['status'] = 404;
			$errors             = __( 'No Items Found.' );
			return new WP_Error( __( 'Something went wrong.' ), $errors, $response );
		}

		$line_items = json_decode( $parameters['items'] );
		if ( empty( $parameters['items'] ) && ! is_array( $line_items ) ) {
			$response['status'] = 404;
			$errors             = __( 'No or Invalid data passed in items' );
			return new WP_Error( __( 'Something went wrong.' ), $errors, $response );
		}

		$errors = array();

		foreach ( $line_items as $key => $item ) {

			$item = (array) $item;

			if ( ! isset( $item['product_id'] ) || empty( $item['product_id'] ) ) {
				$errors[ $key ][0] = 'No Product id found at index ' . $key;
			}
			if ( ! isset( $item['qty'] ) || empty( $item['qty'] ) ) {
				$errors[ $key ][1] = 'No quantity found at index ' . $key;
			}

			$variation = '';
			$product   = wc_get_product( $item['product_id'] );

			if ( empty( $product ) ) {
				$errors[ $key ][0] = 'No Product found matching product id = ' . $item['product_id'];
			}

			if ( $errors[ $key ] ) {
				continue;
			}

			$price        = $product->get_price();
			$variation_id = $item['variation_id'] ? $item['variation_id'] : 0;
			if ( 0 != $item['variation_id'] ) {
				$variation = wc_get_product( $variation_id );
			}
			$items[ $key ] = array(
				'name'            => $product->is_type( 'variation' ) ? $variation->get_name() : $product->get_name(),
				'tax_class'       => $product->get_tax_class(),
				'product_id'      => $product->is_type( 'variation' ) ? $product->get_parent_id() : $product->get_id(),
				'variation_id'    => $variation_id,
				'item_product_id' => $item['product_id'],
				'qty'             => $item['qty'],
				'variation'       => $variation,
				'subtotal'        => $item['qty'] * $price,
				'total'           => $item['qty'] * $price,
			);
		}

		if ( empty( $items ) ) {
			$response['status'] = 404;
			$errors             = __( 'There is no Items to add.' );
			return new WP_Error( __( 'Something went wrong.' ), $errors, $response );
		}

		$quote_data = array(
			'post_title'  => sprintf( 'Quote from %s', $customer->first_name ),
			'post_status' => 'requested',
			'post_type'   => 'quote',
			'post_author' => 1,
			'meta_input'  => array(
				'first_name'     => $customer->first_name,
				'last_name'      => ( isset( $customer->last_name ) ) ? $customer->last_name : '',
				'email'          => ( isset( $customer->user_email ) ) ? $customer->user_email : '',
				'message'        => ( isset( $parameters['message'] ) ) ? $parameters['message'] : '',
				'_customer_user' => $customer_id,
				'items'          => $items,
			),
		);

		$quote_id = wp_insert_post( $quote_data );
		if ( ! is_wp_error( $quote_id ) ) {
			$post_name = sprintf( '#%s %s %s', $quote_id, $customer->first_name, $customer->last_name );
			wp_update_post(
				array(
					'ID'         => $quote_id,
					'post_title' => $post_name,
				)
			);
			$response['status']  = 200;
			$response['message'] = $errors ? 'Quote created successfully with ' . count( $errors ) . ' error(s)' : 'Quote created successfully.';
		}

		if ( $errors ) {
			$response['errors']      = $errors;
			$response['error_count'] = count( $errors );
		}

		return new WP_REST_Response( $response );

	}

}
