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
 * Class B2BE_Delete_Quotes_Line_Items_API.
 */
class B2BE_Delete_Quotes_Line_Items_API {

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
	protected $rest_base = 'quotes/line-item';

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
					'callback'            => array( $this, 'delete_quote_line_items' ),
					'permission_callback' => function() {
						return b2be_auth_verification();
					},
				),
			)
		);

	}

	/**
	 * Delete Quote Line Items according to params provided.
	 *
	 * @param array $request parameters provided.
	 */
	public function delete_quote_line_items( WP_REST_Request $request ) {

		$parameters = $request->get_params();

		if ( ! isset( $parameters['quote_id'] ) || empty( $parameters['quote_id'] ) ) {
			$response['status'] = 412;
			$errors             = __( 'No quote id provided.' );
			return new WP_Error( __( 'Something went wrong.' ), $errors, $response );
		}

		if ( ! isset( $parameters['product_id'] ) || empty( $parameters['product_id'] ) ) {
			$response['status'] = 412;
			$errors             = __( 'No product id provided.' );
			return new WP_Error( __( 'Something went wrong.' ), $errors, $response );
		}

		$quote = wc_get_quote( intval( $parameters['quote_id'] ) );
		if ( empty( $quote->get_prop( 'status' ) ) ) {
			$response['status'] = 404;
			$errors             = __( 'No Quote found against this quote id.' );
			return new WP_Error( __( 'Something went wrong.' ), $errors, $response );
		}

		$quote_items_ids       = get_quote_items_id( $quote );
		$updated_items         = array();
		$un_matched_variations = 0;
		$total_variations      = 0;
		if ( ! in_array( $parameters['product_id'], $quote_items_ids ) ) {
			$response['status'] = 404;
			$errors             = __( 'Product Id does not match any line items in quote' );
			return new WP_Error( __( 'Something went wrong.' ), $errors, $response );
		}
		$products = get_post_meta( $quote->get_id(), 'items', true ); // Products already in quote...
		foreach ( $products as $key => $product ) {
			$item_product = wc_get_product( $parameters['product_id'] );
			if ( isset( $parameters['variation_id'] ) && $item_product->is_type( 'variable' ) && ! empty( $product['variation'] ) ) {
				if ( $parameters['variation_id'] == $product['variation']->get_id() ) {
					unset( $products[ $key ] );
				} else {
					$un_matched_variations++;
				}
				$total_variations++;
			} elseif ( ( isset( $parameters['variation_id'] ) || empty( $parameters['variation_id'] ) ) && $item_product->is_type( 'variable' ) ) {
				$response['status'] = 412;
				$errors             = __( 'No Variation id provided.' );
				return new WP_Error( __( 'Something went wrong.' ), $errors, $response );
			} elseif ( ( isset( $parameters['variation_id'] ) && $item_product->is_type( 'simple' ) ) ) {
				$response['status'] = 412;
				$errors             = __( 'Variation id is not required. It is a simple product.' );
				return new WP_Error( __( 'Something went wrong.' ), $errors, $response );
			} elseif ( $parameters['product_id'] == $product['product_id'] ) {
				unset( $products[ $key ] );
			}
		}
		if ( $item_product->is_type( 'variable' ) ) {
			if ( $un_matched_variations == $total_variations ) {
				$response['status'] = 404;
				$errors             = __( 'Variation Id didn\'t matched with any of the product in quote.' );
				return new WP_Error( __( 'Something went wrong.' ), $errors, $response );
			}
		}

		// if iniatial count of products in quote remain same after checking it show error.
		if ( count( $product ) == get_post_meta( $quote->get_id(), 'items', true ) ) {
			$response['status'] = 404;
			$errors             = __( 'Product Id didn\'t matched with any of the product in quote.' );
			return new WP_Error( __( 'Something went wrong.' ), $errors, $response );
		}

		update_post_meta( $quote->get_id(), 'items', $products );

		$response['status']  = 200;
		$response['message'] = 'Quote Line Item Deleted successfully.';

		return new WP_REST_Response( $response );

	}

}
