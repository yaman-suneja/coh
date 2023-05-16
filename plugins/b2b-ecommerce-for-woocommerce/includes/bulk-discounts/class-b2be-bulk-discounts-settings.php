<?php
/**
 * WC Ecommerce For Woocommerce Main Class.
 *
 * @package b2b-ecommerce-for-woocomerce
 */

defined( 'ABSPATH' ) || exit;

/**
 * Codup_B2B_Ecommerce_For_Woocommerce class.
 */
class B2BE_Bulk_Discounts_Settings {

	/**
	 * Function Calculate Shipping.
	 */
	public static function init() {

		add_action( 'wp_ajax_save_discount_rules', __CLASS__ . '::save_discount_rules' );
		add_action( 'wp_ajax_nopriv_save_discount_rules', __CLASS__ . '::save_discount_rules' );

	}

	/**
	 * Return Discount Rules setting fields.
	 */
	public static function get_settings() {

		$b2be_discount_rules = get_option( 'b2be_discount_rule' );
		$b2be_is_enable      = get_option( 'b2be_enable_discount' );

		if ( empty( $b2be_discount_rules ) ) {

			$b2be_discount_rules = array(
				array(
					'ruleId'            => 1,
					'priority'          => 10,
					'is_role_based'     => false,
					'is_category_based' => false,
					'is_customer_based' => false,
					'is_quantity_based' => false,
					'roles'             => array(),
					'categories'        => array(),
					'customer'          => array(),
					'innerRule'         => array(
						array(
							'minQuantity' => '',
							'maxQuantity' => '',
							'discount'    => '',
							'type'        => 'percentage',
						),
					),
				),
			);

		}

		include CWRFQ_PLUGIN_DIR . '/includes/admin/bulk-discounts/views/bulk-discounts-fields.php';

	}

	/**
	 * Saving the discount options settings using Ajax.
	 */
	public static function save_discount_rules() {

		if ( ! empty( $_POST['_wpnonce'] ) ) {
			wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ) );
		}

		$b2be_discount_rules = filter_input( INPUT_POST, 'discountRules', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$is_enable           = isset( $_POST['isEnable'] ) ? sanitize_text_field( wp_unslash( $_POST['isEnable'] ) ) : 'false';

		if ( 'false' == $is_enable ) {
			update_option( 'b2be_enable_discount', $is_enable );
			wp_die( 'true' );
		}

		if ( ! empty( $b2be_discount_rules ) ) {

			$is_validated = self::b2be_validate_discount_rules( $b2be_discount_rules );
			if ( $is_validated ) {
				update_option( 'b2be_enable_discount', $is_enable );

				delete_option( 'b2be_discount_rule' );
				update_option( 'b2be_discount_rule', $b2be_discount_rules );

				wp_die( 'true' );
			}
		}

	}

	/**
	 * Validating Rules.
	 *
	 * @param array $b2be_discount_rules Discount Rules Array.
	 */
	public static function b2be_validate_discount_rules( $b2be_discount_rules = array() ) {

		$errors = self::check_for_individual_errors_in_rules( $b2be_discount_rules );

		$b2be_discount_rules_length = count( $b2be_discount_rules );
		for ( $i = 0; $i <= $b2be_discount_rules_length; $i++ ) {
			for ( $j = $i + 1; $j <= $b2be_discount_rules_length; $j++ ) {
				if ( ! empty( array_intersect( $b2be_discount_rules[ $i ], $b2be_discount_rules[ $j ] ) ) ) {
					$common_problems['is_role_based']     = ( $b2be_discount_rules[ $i ]['is_role_based'] == $b2be_discount_rules[ $j ]['is_role_based'] );
					$common_problems['is_product_based']  = ( $b2be_discount_rules[ $i ]['is_product_based'] == $b2be_discount_rules[ $j ]['is_product_based'] );
					$common_problems['is_category_based'] = ( $b2be_discount_rules[ $i ]['is_category_based'] == $b2be_discount_rules[ $j ]['is_category_based'] );
					$common_problems['is_customer_based'] = ( $b2be_discount_rules[ $i ]['is_customer_based'] == $b2be_discount_rules[ $j ]['is_customer_based'] );

					if ( empty( $errors ) ) {
						if ( $common_problems['is_product_based'] && $common_problems['is_category_based'] && $common_problems['is_customer_based'] && ! empty( $roles_errors ) ) {
							$errors = $roles_errors;
						}
						if ( $common_problems['is_role_based'] && $common_problems['is_product_based'] && $common_problems['is_customer_based'] && ! empty( $category_errors ) ) {
							$errors = $category_errors;
						}
						if ( $common_problems['is_role_based'] && $common_problems['is_category_based'] && $common_problems['is_product_based'] && ! empty( $customer_errors ) ) {
							$errors = $customer_errors;
						}
						if ( $common_problems['is_role_based'] && $common_problems['is_category_based'] && $common_problems['is_customer_based'] && ! empty( $product_errors ) ) {
							$errors = $product_errors;
						}
					}
				}
			}
		}

		$priority_found = array();
		$errors_length  = count( $errors );
		for ( $i = 0; $i < $errors_length; $i++ ) {
			foreach ( $b2be_discount_rules as $key => $value ) {
				$index = array_search( $errors[ $i ], $value );
				if ( 'ruleId' == $index ) {
					$priority_found[ $b2be_discount_rules[ $key ]['ruleId'] ] = $b2be_discount_rules[ $key ]['priority'];
				}
			}
		}

		for ( $i = 1; $i <= last_array_key( $priority_found ); $i++ ) {
			if ( isset( $priority_found[ $i ] ) ) {
				for ( $k = $i + 1; $k <= last_array_key( $priority_found ); $k++ ) {
					if ( $priority_found[ $i ] != $priority_found[ $k ] ) {
						$key = array_search( $i, $errors );
						unset( $errors[ $key ] );
						$key = array_search( $k, $errors );
						unset( $errors[ $key ] );
					}
				}
			}
		}
		if ( ! empty( $errors ) ) {
			wp_die( json_encode( array_unique( $errors ) ) );
		} else {
			return true;
		}

	}

	/**
	 * Check For Individual Errors In Rules.
	 *
	 * @param array $b2be_discount_rules All the rules from discount options tab.
	 *
	 * @return array Returns errors array consisting rule id.
	 */
	private static function check_for_individual_errors_in_rules( $b2be_discount_rules = array() ) {

		$roles_errors    = array();
		$category_errors = array();
		$customer_errors = array();
		$product_errors  = array();

		$common_roles      = array_column( $b2be_discount_rules, 'roles', 'ruleId' );
		$common_categories = array_column( $b2be_discount_rules, 'categories', 'ruleId' );
		$common_customer   = array_column( $b2be_discount_rules, 'customer', 'ruleId' );
		$common_product    = array_column( $b2be_discount_rules, 'products', 'ruleId' );

		$roles_length    = count( $common_roles );
		$category_length = count( $common_categories );
		$customer_length = count( $common_customer );
		$product_length  = count( $common_product );

		for ( $i = 1; $i <= $roles_length; $i++ ) {
			for ( $j = $i + 1; $j <= $roles_length; $j++ ) {
				if ( ! empty( array_intersect( $common_roles[ $i ], $common_roles[ $j ] ) ) ) {
					if ( ! in_array( $i, $roles_errors ) ) {
						array_push( $roles_errors, $i );
					}
					if ( ! in_array( $j, $roles_errors ) ) {
						array_push( $roles_errors, $j );
					}
				}
			}
		}

		for ( $i = 1; $i <= $category_length; $i++ ) {
			for ( $j = $i + 1; $j <= $category_length; $j++ ) {
				if ( ! empty( array_intersect( $common_categories[ $i ], $common_categories[ $j ] ) ) ) {
					if ( ! in_array( $i, $category_errors ) ) {
						array_push( $category_errors, $i );
					}
					if ( ! in_array( $j, $category_errors ) ) {
						array_push( $category_errors, $j );
					}
				}
			}
		}

		for ( $i = 1; $i <= $customer_length; $i++ ) {
			for ( $j = $i + 1; $j <= $customer_length; $j++ ) {
				if ( ! empty( array_intersect( $common_customer[ $i ], $common_customer[ $j ] ) ) ) {
					if ( ! in_array( $i, $customer_errors ) ) {
						array_push( $customer_errors, $i );
					}
					if ( ! in_array( $j, $customer_errors ) ) {
						array_push( $customer_errors, $j );
					}
				}
			}
		}

		for ( $i = 1; $i <= $product_length; $i++ ) {
			for ( $j = $i + 1; $j <= $product_length; $j++ ) {
				if ( ! empty( array_intersect( $common_product[ $i ], $common_product[ $j ] ) ) ) {
					if ( ! in_array( $i, $product_errors ) ) {
						array_push( $product_errors, $i );
					}
					if ( ! in_array( $j, $product_errors ) ) {
						array_push( $product_errors, $j );
					}
				}
			}
		}

		return self::check_for_inter_rules_errors( $roles_errors, $category_errors, $customer_errors, $product_errors );

	}

	/**
	 * Check For Errors betwwen inner rules.
	 *
	 * @param array $roles_errors Array of rule ids having individual role errors.
	 * @param array $category_errors Array of rule ids having individual category errors.
	 * @param array $customer_errors Array of rule ids having individual customer errors.
	 * @param array $product_errors Array of rule ids having individual product errors.
	 *
	 * @return array Returns errors array consisting rule id.
	 */
	private static function check_for_inter_rules_errors( $roles_errors = array(), $category_errors = array(), $customer_errors = array(), $product_errors = array() ) {

		$errors = array();
		if ( ! empty( array_intersect_assoc( $roles_errors, $category_errors ) ) ) {
			$errors = array_merge( $errors, array_intersect_assoc( $roles_errors, $category_errors ) );
		}
		if ( ! empty( array_intersect_assoc( $category_errors, $customer_errors ) ) ) {
			$errors = array_merge( $errors, array_intersect_assoc( $category_errors, $customer_errors ) );
		}
		if ( ! empty( array_intersect_assoc( $roles_errors, $product_errors ) ) ) {
			$errors = array_merge( $errors, array_intersect_assoc( $roles_errors, $product_errors ) );
		}
		if ( ! empty( array_intersect_assoc( $category_errors, $product_errors ) ) ) {
			$errors = array_merge( $errors, array_intersect_assoc( $category_errors, $product_errors ) );
		}
		if ( ! empty( array_intersect_assoc( $customer_errors, $product_errors ) ) ) {
			$errors = array_merge( $errors, array_intersect_assoc( $customer_errors, $product_errors ) );
		}
		if ( ! empty( array_intersect_assoc( $roles_errors, $category_errors, $customer_errors ) ) ) {
			$errors = array_merge( $errors, array_intersect_assoc( $roles_errors, $category_errors, $customer_errors ) );
		}
		if ( ! empty( array_intersect_assoc( $roles_errors, $category_errors, $product_errors ) ) ) {
			$errors = array_merge( $errors, array_intersect_assoc( $roles_errors, $category_errors, $product_errors ) );
		}
		if ( ! empty( array_intersect_assoc( $category_errors, $customer_errors, $product_errors ) ) ) {
			$errors = array_merge( $errors, array_intersect_assoc( $category_errors, $customer_errors, $product_errors ) );
		}
		if ( ! empty( array_intersect_assoc( $roles_errors, $category_errors, $customer_errors, $product_errors ) ) ) {
			$errors = array_merge( $errors, array_intersect_assoc( $roles_errors, $category_errors, $customer_errors, $product_errors ) );
		}

		return $errors;

	}


}
