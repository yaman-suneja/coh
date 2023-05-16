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
class B2BE_MOV_Settings {

	/**
	 * Function Calculate Shipping.
	 */
	public static function init() {

		add_action( 'wp_ajax_save_mov_rules', __CLASS__ . '::save_mov_rules' );
		add_action( 'wp_ajax_nopriv_save_mov_rules', __CLASS__ . '::save_mov_rules' );

	}

	/**
	 * Return RFQ setting fields.
	 */
	public static function get_settings() {

		$b2be_mov_rules     = get_option( 'b2be_mov_rule' );
		$is_b2be_mov_enable = get_option( 'b2be_enable_mov' );

		if ( empty( $b2be_mov_rules ) ) {

			$b2be_mov_rules = array(
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
							'minValue' => '',
							'maxValue' => '',
						),
					),
				),
			);

		}

		include CWRFQ_PLUGIN_DIR . '/includes/admin/mov/views/mov-fields.php';

	}

	/**
	 * Saving the discount options settings using Ajax.
	 */
	public static function save_mov_rules() {

		if ( ! empty( $_POST['_wpnonce'] ) ) {
			wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ) );
		}

		$b2be_mov_rules = filter_input( INPUT_POST, 'movRules', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$is_enable      = isset( $_POST['isEnable'] ) ? sanitize_text_field( wp_unslash( $_POST['isEnable'] ) ) : '';

		if ( 'false' == $is_enable ) {
			update_option( 'b2be_enable_mov', $is_enable );
			wp_die( 'true' );
		}

		if ( ! empty( $b2be_mov_rules ) ) {

			$is_validated = self::b2be_validate_mov_rules( $b2be_mov_rules );
			if ( $is_validated ) {

				delete_option( 'b2be_mov_rule' );
				update_option( 'b2be_mov_rule', $b2be_mov_rules );

				update_option( 'b2be_enable_mov', $is_enable );
				wp_die( 'true' );
			}
		}

	}

	/**
	 * Validating Rules.
	 *
	 * @param array $b2be_mov_rules Mov rules array.
	 */
	public static function b2be_validate_mov_rules( $b2be_mov_rules = array() ) {

		$roles_errors    = array();
		$category_errors = array();
		$customer_errors = array();
		$product_errors  = array();
		$quantity_errors = array();

		$common_roles      = array_column( $b2be_mov_rules, 'roles', 'ruleId' );
		$common_categories = array_column( $b2be_mov_rules, 'categories', 'ruleId' );
		$common_customer   = array_column( $b2be_mov_rules, 'customer', 'ruleId' );
		$common_product    = array_column( $b2be_mov_rules, 'products', 'ruleId' );
		$common_quantity   = array_column( $b2be_mov_rules, 'innerRule', 'ruleId' );

		$roles_length    = count( $common_roles );
		$category_length = count( $common_categories );
		$customer_length = count( $common_customer );
		$product_length  = count( $common_product );
		$quantity_length = count( $common_quantity );

		for ( $i = 1; $i <= $quantity_length; $i++ ) {
			for ( $j = $i + 1; $j <= $quantity_length; $j++ ) {
				if ( ! empty( array_intersect( $common_quantity[ $i ][0], $common_quantity[ $j ][0] ) ) ) {
					if ( ! in_array( $i, $quantity_errors ) ) {
						array_push( $quantity_errors, $i );
					}
					if ( ! in_array( $j, $quantity_errors ) ) {
						array_push( $quantity_errors, $j );
					}
				}
			}
		}

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

		$errors = array();
		if ( ! empty( array_intersect_assoc( $roles_errors, $category_errors ) ) ) {
			$errors = array_merge( $errors, array_intersect_assoc( $roles_errors, $category_errors ) );
		}
		if ( ! empty( array_intersect_assoc( $category_errors, $customer_errors ) ) ) {
			$errors = array_merge( $errors, array_intersect_assoc( $category_errors, $customer_errors ) );
		}
		if ( ! empty( array_intersect_assoc( $roles_errors, $category_errors, $customer_errors ) ) ) {
			$errors = array_merge( $errors, array_intersect_assoc( $roles_errors, $category_errors, $customer_errors ) );
		}

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

		$b2be_mov_rules_length = count( $b2be_mov_rules );
		for ( $i = 0; $i <= $b2be_mov_rules_length; $i++ ) {
			for ( $j = $i + 1; $j <= $b2be_mov_rules_length; $j++ ) {
				if ( ! empty( array_intersect( $b2be_mov_rules[ $i ], $b2be_mov_rules[ $j ] ) ) ) {
					$common_problems['is_role_based']     = ( $b2be_mov_rules[ $i ]['is_role_based'] == $b2be_mov_rules[ $j ]['is_role_based'] );
					$common_problems['is_product_based']  = ( $b2be_mov_rules[ $i ]['is_product_based'] == $b2be_mov_rules[ $j ]['is_product_based'] );
					$common_problems['is_category_based'] = ( $b2be_mov_rules[ $i ]['is_category_based'] == $b2be_mov_rules[ $j ]['is_category_based'] );
					$common_problems['is_customer_based'] = ( $b2be_mov_rules[ $i ]['is_customer_based'] == $b2be_mov_rules[ $j ]['is_customer_based'] );

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
		$error_length   = count( $errors );
		$priority_found = array();
		for ( $i = 0; $i < $error_length; $i++ ) {
			foreach ( $b2be_mov_rules as $key => $value ) {
				$index = array_search( $errors[ $i ], $value );
				if ( 'ruleId' == $index ) {
					$priority_found[ $b2be_mov_rules[ $key ]['ruleId'] ] = $b2be_mov_rules[ $key ]['priority'];
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


}
