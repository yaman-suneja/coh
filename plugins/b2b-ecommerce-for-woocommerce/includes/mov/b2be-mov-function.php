<?php
/**
 * B2B MOV Fucntions File.
 *
 * @package b2b-ecommerce-for-woocomerce
 */

 /**
  * Function to retrieve Minimum Order Quantity applying on product.
  *
  * @param object $product Product Object.
  *
  * @return array
  */
function b2be_get_mov_limit( $product ) {

	$is_b2be_mov_enable = get_option( 'b2be_enable_mov' );

	if ( ( ! $product ) || ( 'false' == $is_b2be_mov_enable ) ) {
		return array();
	}

	$product_id   = $product->get_id();
	$product_type = $product->get_type();

	// variations..
	$product_variation = $product->get_children(); // get variations..

	// simple..
	$regular_price      = $product->get_regular_price();
	$product_categories = $product->get_category_ids();

	// user data..
	$user      = wp_get_current_user();
	$user_id   = $user->ID;
	$user_role = $user->roles;

	$b2be_mov_rules = get_option( 'b2be_mov_rule', array() );
	$mov_rule_limit = array();
	$discount       = array();
	$priority       = 11;

	if ( 0 === count( $b2be_mov_rules ) ) {
		return;
	}

	usort(
		$b2be_mov_rules,
		function ( $item1, $item2 ) {
			if ( $item1['priority'] == $item2['priority'] ) {
				return 0;
			}
			return ( $item1['priority'] < $item2['priority'] ) ? -1 : 1;
		}
	);
	if ( $b2be_mov_rules ) {
		foreach ( $b2be_mov_rules as $key => $main_rule ) {

			$is_role     = false;
			$is_category = false;
			$is_customer = false;
			$is_quantity = false;
			$is_product  = false;

			if ( 'true' == $main_rule['is_role_based'] ) {
				if ( array_intersect( $user_role, $main_rule['roles'] ) ) {
					$is_role = true;
				}
			}
			if ( 'true' == $main_rule['is_category_based'] ) {
				if ( array_intersect( $product_categories, $main_rule['categories'] ) ) {
					$is_category = true;
				}
			}
			if ( 'true' == $main_rule['is_customer_based'] ) {
				if ( isset( $main_rule['customer'] ) && $main_rule['customer'] ) {
					if ( in_array( $user_id, $main_rule['customer'] ) ) {
						$is_customer = true;
					}
				}
			}
			if ( isset( $main_rule['is_product_based'] ) && 'true' == $main_rule['is_product_based'] ) {

				if ( $product->is_type( 'simple' ) ) {
					if ( isset( $main_rule['products'] ) && $main_rule['products'] ) {
						if ( in_array( $product_id, $main_rule['products'] ) ) {
							$is_product = true;
						}
					}
				} elseif ( $product->is_type( 'variable' ) ) {
					$is_product = true;
				}
			}

			foreach ( $main_rule['innerRule'] as $index => $value ) {
				$main_rule['innerRule'][ $index ]['priority'] = $main_rule['priority'];
				$main_rule['innerRule'][ $index ]['ruleId']   = $main_rule['ruleId'];
				if ( ( ( isset( $main_rule['is_product_based'] ) && 'false' == $main_rule['is_product_based'] ) || empty( $main_rule['innerRule'][ $index ]['variation_ids'] ) ) && ! empty( $product_variation ) ) {
					$main_rule['innerRule'][ $index ]['variation_ids'] = $product_variation;
				}
			}
			if ( ( ( 'false' == $main_rule['is_category_based'] && 'false' == $main_rule['is_product_based'] ) && empty( $main_rule['products'] ) ) && empty( $main_rule['innerRule'][ $index ]['variation_ids'] ) ) {
				$main_rule['products'] = get_b2be_products_id();
				$is_product            = true;
			}

			if ( ( $is_role || $is_customer ) && ( $is_category || $is_product ) ) {
				$mov_rule_limit = array_merge( $mov_rule_limit, $main_rule['innerRule'] );
			} elseif ( ( isset( $main_rule['is_role_based'] ) && 'true' != $main_rule['is_role_based'] ) && ( isset( $main_rule['is_customer_based'] ) && 'true' != $main_rule['is_customer_based'] ) && ( isset( $main_rule['is_product_based'] ) && 'true' != $main_rule['is_product_based'] ) && $is_category ) {
				$mov_rule_limit = array_merge( $mov_rule_limit, $main_rule['innerRule'] );
			} elseif ( ( isset( $main_rule['is_role_based'] ) && 'true' != $main_rule['is_role_based'] ) && ( isset( $main_rule['is_customer_based'] ) && 'true' != $main_rule['is_customer_based'] ) && ( isset( $main_rule['is_category_based'] ) && 'true' != $main_rule['is_category_based'] ) && $is_product ) {
				$mov_rule_limit = array_merge( $mov_rule_limit, $main_rule['innerRule'] );
			} elseif ( ( isset( $main_rule['is_category_based'] ) && 'true' != $main_rule['is_category_based'] ) && ( isset( $main_rule['is_product_based'] ) && 'true' != $main_rule['is_product_based'] ) && ( $is_role || $is_customer ) ) {
				$mov_rule_limit = array_merge( $mov_rule_limit, $main_rule['innerRule'] );
			} elseif ( ( isset( $main_rule['is_role_based'] ) && 'true' != $main_rule['is_role_based'] ) && ( isset( $main_rule['is_customer_based'] ) && 'true' != $main_rule['is_customer_based'] ) && ( $is_category || $is_product ) ) {
				$mov_rule_limit = array_merge( $mov_rule_limit, $main_rule['innerRule'] );
			} elseif ( ( isset( $main_rule['is_role_based'] ) && 'true' != $main_rule['is_role_based'] ) && ( isset( $main_rule['is_category_based'] ) && 'true' != $main_rule['is_category_based'] ) && ( isset( $main_rule['is_customer_based'] ) && 'true' != $main_rule['is_customer_based'] ) && ( isset( $main_rule['is_product_based'] ) && 'true' != $main_rule['is_product_based'] ) && ! empty( $main_rule['innerRule'] ) ) {
				$mov_rule_limit = array_merge( $mov_rule_limit, $main_rule['innerRule'] );
			}

			if ( $mov_rule_limit && ! $product->is_type( 'variable' ) ) {
				break;
			}
		}
	}

	/*
	@name: b2b_mov_limits
	@desc: Modify B2B Ecommerce Mov Rules applying on respective product.
	@param: (array) $mov_rule_limit B2b Ecommerce MOv Rules array.
	@package: b2b-ecommerce-for-woocommerce
	@module: minimum order quantity
	@type: filter
	*/
	return apply_filters( 'b2b_mov_limits', $mov_rule_limit );
}
