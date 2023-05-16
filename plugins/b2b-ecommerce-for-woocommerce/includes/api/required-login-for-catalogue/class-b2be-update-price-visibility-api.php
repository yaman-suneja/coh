<?php
/**
 * API File For Update Roles.
 *
 * @package class-b2be-update-price-visibility-api.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class B2BE_Update_Price_Visibility_API.
 */
class B2BE_Update_Price_Visibility_API {

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
	protected $rest_base = 'price-visibility';

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

		if ( empty( $request->get_params() ) ) {
			$response['status'] = 412;
			$errors             = __( 'Missing required values ' );
			return new WP_Error( __( 'Something went wrong.' ), $errors, $response );
		}

		$errors = $this->validating_provided_params( $parameters );

		if ( $errors ) {
			$message                 = __( 'Missing or Invalid values ' );
			$response['errors']      = $errors;
			$response['status']      = 404;
			$response['error_count'] = count( $errors );
			return new WP_Error( __( 'Something went wrong.' ), $message, $response );
		} else {
			$response['status'] = 200;
		}

		if ( ! $errors || 6 != count( $errors ) ) {
			$response['message'] = 'Settings Updated Successfully.';
		}

		return new WP_REST_Response( $response );

	}

	/**
	 * Validateing params provided.
	 *
	 * @param array $parameters parameters provided.
	 */
	public function validating_provided_params( $parameters = array() ) {

		if ( empty( $parameters ) ) {
			return false;
		}

		$category_query = get_terms(
			array(
				'taxonomy'   => 'product_cat',
				'hide_empty' => true,
			)
		);

		$product_query = get_posts(
			array(
				'post_type'   => 'product',
				'numberposts' => -1,
				'post_status' => 'publish',
				'fields'      => 'ids',
			)
		);

		$pages_query = get_pages();

		foreach ( $product_query as $product_id ) {

			$product                     = wc_get_product( $product_id );
			$all_products[ $product_id ] = $product->get_name();

		}

		foreach ( $category_query as $key => $value ) {

			$product_categories[ $value->term_taxonomy_id ] = $value->name;

		}

		foreach ( $pages_query as $key => $page_object ) {

			if ( 'my-account' == $page_object->post_name ) {
				continue;
			}
			$all_pages[ $page_object->ID ] = $page_object->post_title;

		}

		if ( isset( $parameters['codup_enable_hide_catalogue'] ) ) {
			if ( empty( $parameters['codup_enable_hide_catalogue'] ) ) {
				$errors['codup_enable_hide_catalogue'] = 'No value Provided. Please enter a valid value.';
			} else {
				if ( 'yes' == $parameters['codup_enable_hide_catalogue'] ) {
					update_option( 'codup_enable_hide_catalogue', $parameters['codup_enable_hide_catalogue'] );
				} elseif ( 'no' == $parameters['codup_enable_hide_catalogue'] ) {
					update_option( 'codup_enable_hide_catalogue', $parameters['codup_enable_hide_catalogue'] );
				} else {
					$errors['codup_enable_hide_catalogue'] = 'Value provide for this setting is not valid';
				}
			}
		}
		if ( isset( $parameters['codup_restrict_store'] ) ) {
			if ( empty( $parameters['codup_restrict_store'] ) ) {
				$errors['codup_restrict_store'] = 'No value Provided. Please enter a valid value.';
			} else {
				if ( 'yes' == $parameters['codup_restrict_store'] ) {
					update_option( 'codup_restrict_store', $parameters['codup_restrict_store'] );
				} elseif ( 'no' == $parameters['codup_restrict_store'] ) {
					update_option( 'codup_restrict_store', $parameters['codup_restrict_store'] );
				} else {
					$errors['codup_restrict_store'] = 'Value provide for this setting is not valid';
				}
			}
		}
		if ( isset( $parameters['codup_hide_for_all'] ) ) {
			if ( empty( $parameters['codup_hide_for_all'] ) ) {
				$errors['codup_hide_for_all'] = 'No value Provided. Please enter a valid value.';
			} else {
				if ( 'yes' == $parameters['codup_hide_for_all'] ) {
					update_option( 'codup_hide_for_all', $parameters['codup_hide_for_all'] );
				} elseif ( 'no' == $parameters['codup_hide_for_all'] ) {
					update_option( 'codup_hide_for_all', $parameters['codup_hide_for_all'] );
				} else {
					$errors['codup_hide_for_all'] = 'Value provide for this setting is not valid';
				}
			}
		}
		if ( isset( $parameters['codup_hide_by_category'] ) ) {
			if ( empty( $parameters['codup_hide_by_category'] ) ) {
				$errors['codup_hide_by_category'] = 'No value Provided. Please enter a valid value.';
			} elseif ( ! is_array( json_decode( $parameters['codup_hide_by_category'] ) ) ) {
				$errors['codup_hide_by_category'] = 'Please enter a valid value.The given format is wrong, it should be an array.';
			} else {
				$category_ids  = json_decode( $parameters['codup_hide_by_category'] );
				$formatted_ids = array();
				foreach ( $category_ids as $key => $category_id ) {
					if ( in_array( $category_id, array_keys( $product_categories ) ) ) {
						$formatted_ids[ $key ] = (string) $category_id;
					} else {
						$errors['codup_hide_by_category'][ $category_id ] = 'No category with this id exist.';
					}
				}
				if ( ! empty( $formatted_ids ) ) {
					update_option( 'codup_hide_by_category', $formatted_ids );
				}
			}
		}
		if ( isset( $parameters['codup_hide_by_product'] ) ) {
			if ( empty( $parameters['codup_hide_by_product'] ) ) {
				$errors['codup_hide_by_product'] = 'No value Provided. Please enter a valid value.';
			} elseif ( ! is_array( json_decode( $parameters['codup_hide_by_product'] ) ) ) {
				$errors['codup_hide_by_product'] = 'Please enter a valid value.The given format is wrong, it should be an array.';
			} else {
				$product_ids   = json_decode( $parameters['codup_hide_by_product'] );
				$formatted_ids = array();
				foreach ( $product_ids as $key => $product_id ) {
					if ( in_array( $product_id, array_keys( $all_products ) ) ) {
						$formatted_ids[ $key ] = (string) $product_id;
					} else {
						$errors['codup_hide_by_product'][ $product_id ] = 'No product with this id exist.';
					}
				}
				if ( ! empty( $formatted_ids ) ) {
					update_option( 'codup_hide_by_product', $formatted_ids );
				}
			}
		}
		if ( isset( $parameters['codup_hide_by_pages'] ) ) {
			if ( empty( $parameters['codup_hide_by_pages'] ) ) {
				$errors['codup_hide_by_pages'] = 'No value Provided. Please enter a valid value.';
			} elseif ( ! is_array( json_decode( $parameters['codup_hide_by_pages'] ) ) ) {
				$errors['codup_hide_by_pages'] = 'Please enter a valid value.The given format is wrong, it should be an array.';
			} else {
				$page_ids      = json_decode( $parameters['codup_hide_by_pages'] );
				$formatted_ids = array();
				foreach ( $page_ids as $key => $page_id ) {
					if ( in_array( $page_id, array_keys( $all_pages ) ) ) {
						$formatted_ids[ $key ] = (string) $page_id;
					} else {
						$errors['codup_hide_by_pages'][ $page_id ] = 'No page with this id exist.';
					}
				}
				if ( ! empty( $formatted_ids ) ) {
					update_option( 'codup_hide_by_pages', $formatted_ids );
				}
			}
		}

		return $errors;
	}

}
