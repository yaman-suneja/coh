<?php
/**
 * API File For Update Quotes.
 *
 * @package class-b2be-update-quotes-api.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class B2BE_Update_Quotes_API.
 */
class B2BE_Update_Quotes_API {

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
			'/' . $this->rest_base . '/update',
			array(
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_quote' ),
					'permission_callback' => function() {
						return b2be_auth_verification();
					},
				),
			)
		);

	}

	/**
	 * Update Quote according to params provided.
	 *
	 * @param array $request parameters provided.
	 */
	public function update_quote( WP_REST_Request $request ) {

		$parameters = $request->get_params();

		if ( empty( $parameters ) ) {
			$response['status'] = 412;
			$errors             = __( 'Missing required values ' );
			return new WP_Error( __( 'Something went wrong.' ), $errors, $response );
		}

		if ( ! isset( $parameters['quote_id'] ) || empty( $parameters['quote_id'] ) ) {
			$response['status'] = 412;
			$errors             = __( 'No quote id provided.' );
			return new WP_Error( __( 'Something went wrong.' ), $errors, $response );
		}

		$quote = wc_get_quote( $parameters['quote_id'] );
		if ( empty( $quote->get_prop( 'status' ) ) ) {
			$response['status'] = 404;
			$errors             = __( 'No Quote found against this quote id.' );
			return new WP_Error( __( 'Something went wrong.' ), $errors, $response );
		}

		foreach ( $parameters as $key => $parameter ) {

			if ( 'quote_id' == $key || 'items' == $key ) {
				continue;
			}

			if ( 'status' == $key ) {
				$quote->set_status( $parameter );
				$quote->save();
			} elseif ( array_key_exists( $key, get_post_meta( $quote->get_id() ) ) ) {
				if ( 'email' == $key && ! email_exists( $parameter ) ) {
					$errors[ $key ] = 'Invalid Email. User not found.';
					continue;
				}
				update_post_meta( $quote->get_id(), $key, $parameter );
			} else {
				$errors[ $key ] = 'Invalid parameter passed';
			}
		}

		$items               = json_decode( $parameters['items'] );
		$quote_items_ids     = get_quote_items_id( $quote );
		$updated_items       = array();
		$updated_product_ids = array();
		$i                   = 0;

		if ( isset( $parameters['items'] ) ) {
			if ( ! empty( $items ) ) {
				foreach ( $items as $key => $item ) {
					$item = (array) $item;
					if ( in_array( $item['product_id'], $quote_items_ids ) ) {
						$products = get_post_meta( $quote->get_id(), 'items', true ); // Products already in quote...
						foreach ( $products as $key => $product ) {
							if ( $item['product_id'] == $product['product_id'] ) {
								$item_product = wc_get_product( $product['product_id'] );
								if ( isset( $item['variation_id'] ) && $item_product->is_type( 'variable' ) ) {
									if ( ! in_array( $item['variation_id'], $item_product->get_children() ) ) {
										$errors[ $i ] = "The Product '" . $item_product->get_name() . "' doesnot have any variation with id " . $item['variation_id'];
										continue;
									}
								}
								$product = array_merge( $product, $item );
								array_push( $updated_items, $product );
								array_push( $updated_product_ids, $product['product_id'] );
							}
						}
					} else {
						$errors[ $i ] = 'Product Id at index ' . $i . ' does not match any line items in quote';
					}
					$i++;
				}
				if ( ! $errors ) {
					foreach ( $items as $key => $item ) {
						$item = (array) $item;
						if ( ! in_array( $item['product_id'], $updated_product_ids ) ) {
							array_push( $updated_items, $product );
						}
					}
					update_post_meta( $quote->get_id(), 'items', $updated_items );
				}
			} else {
				$errors['items'] = 'No items found to update.';
			}
		}

		if ( $errors ) {
			$response['status']      = 404;
			$response['errors']      = $errors;
			$response['error_count'] = count( $errors );
			return new WP_Error( __( 'Something went wrong.' ), 'Quote can\'t be updated.', $response );
		} else {
			$response['status']  = 200;
			$response['message'] = 'Quote updated successfully.';
		}

		return new WP_REST_Response( $response );

	}

}
