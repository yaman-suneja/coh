<?php
/**
 * Discount Options Function File.
 *
 * @package b2be-bulk-discounts-function.php
 */

/**
 * Return the categories array.
 */
function get_b2be_categories() {

	$b2be_categories = array();
	$cat_args        = array(
		'posts_per_page' => -1,
		'taxonomy'       => 'product_cat',
		'hide_empty'     => false,
	);
	$terms           = get_terms( $cat_args );
	foreach ( $terms as $key => $value ) {
		$b2be_categories[ $value->term_id ] = $value->name;
	}

	/*
	@name: b2be_rules_categories
	@desc: Modify Category List used for b2b discount rules.
	@param: (array) $b2be_categories Category List of site.
	@package: b2b-ecommerce-for-woocommerce
	@module: discount options
	@type: filter
	*/
	return apply_filters( 'b2be_rules_categories', $b2be_categories );
}

/**
 * Return the users array.
 */
function get_b2be_users() {
	$b2be_users = array();

	$user_args = array(
		'posts_per_page' => -1,
		'fields'         => 'ids',
	);
	$user_ids  = get_users( $user_args );

	foreach ( $user_ids as $user_id ) {

		$b2be_users[ $user_id ] = get_userdata( $user_id );
	}

	/*
	@name: b2be_rules_users_id
	@desc: Modify Users List used for b2b discount rules.
	@param: (array) $b2be_users Users list of site.
	@package: b2b-ecommerce-for-woocommerce
	@module: discount options
	@type: filter
	*/
	return apply_filters( 'b2be_rules_users_id', $b2be_users );
}

/**
 * Return the roles array.
 */
function get_b2be_roles() {
	global $wp_roles;

	/*
	@name: b2be_rules_roles
	@desc: Modify Roles List used for b2b discount rules.
	@param: (array) $b2be_roles Roles list of site.
	@package: b2b-ecommerce-for-woocommerce
	@module: discount options
	@type: filter
	*/
	return apply_filters( 'b2be_rules_roles', $wp_roles->get_names() );
}

/**
 * Return the roles array.
 */
function get_b2be_products() {

	$products_list = array();
	$args          = array(
		'post_type'   => 'product',
		'numberposts' => -1,
		'post_status' => 'publish',
	);
	$products      = get_posts( $args );
	foreach ( $products as $key => $product ) {
		$products_list[ $product->ID ] = $product->post_title;
	}

	/*
	@name: b2be_rules_products
	@desc: Modify Products List used for b2b discount rules.
	@param: (array) $products_list Product list of site.
	@package: b2b-ecommerce-for-woocommerce
	@module: discount options
	@type: filter
	*/
	return apply_filters( 'b2be_rules_products', $products_list );
}

/**
 * Return the product ids array.
 */
function get_b2be_products_id() {

	$args        = array(
		'post_type'   => 'product',
		'numberposts' => -1,
		'post_status' => 'publish',
		'fields'      => 'ids',
	);
	$products_id = get_posts( $args );

	/*
	@name: b2be_rules_products_id
	@desc: Modify Products Ids List used for b2b discount rules.
	@param: (array) $products_id Product Ids list of site.
	@package: b2b-ecommerce-for-woocommerce
	@module: discount options
	@type: filter
	*/
	return apply_filters( 'b2be_rules_products_id', $products_id );
}

/**
 * Return the roles array.
 *
 * @param int $product_id Product id.
 */
function get_b2be_products_variration( $product_id ) {

	$products_variration_array = array();
	$product                   = wc_get_product( $product_id );
	if ( $product->is_type( 'variable' ) ) {
		$variations = $product->get_children();
		foreach ( $variations as $key => $variation_id ) {
			$variation            = wc_get_product( $variation_id );
			$variation_attributes = implode( ', ', $variation->get_variation_attributes() );
			$product_name         = $variation->get_name();
			$products_variration_array[ $variation->get_ID() ] = $product_name;
		}
	}

	/*
	@name: b2be_rules_variaiton_names
	@desc: Modify Products Variation Name List used for b2b discount rules.
	@param: (array) $products_variration_array Product variations name list with id.
	@package: b2b-ecommerce-for-woocommerce
	@module: discount options
	@type: filter
	*/
	return apply_filters( 'b2be_rules_variaiton_names', $products_variration_array );
}

/**
 * Return all the inner rules in an array.
 *
 * @param array $b2be_discount_rules Array of Discount rules.
 */
function get_b2be_inner_rules( $b2be_discount_rules ) {

	$inner_rules = array();

	foreach ( $b2be_discount_rules as $index => $main_rule ) {

		foreach ( $main_rule['innerRule'] as $value ) {
			$value['ruleId'] = $main_rule['ruleId'];
			$inner_rules[]   = $value;

		}
	}
	return $inner_rules;

}

/**
 * Return variation prices.
 *
 * @param object $product Product Object.
 */
function b2be_variations_price( $product ) {

	if ( $product->is_type( 'variable' ) ) {
		$price      = array();
		$variations = $product->get_available_variations();

		foreach ( $variations as $key => $variation ) {
			$price[ $variation['variation_id'] ] = $variation['display_regular_price'];
		}

		/*
		@name: b2be_product_variation_price
		@desc: Modify Products Variation Price.
		@param: (array) $products_variration_array Product variations price.
		@package: b2b-ecommerce-for-woocommerce
		@module: discount options
		@type: filter
		*/
		return apply_filters( 'b2be_product_variation_price', $price );

	}
	return false;
}

/**
 * Return variation prices.
 *
 * @param array $variation_ids Array of Varaition Id.
 */
function b2be_variations_name( $variation_ids = array() ) {
	if ( $variation_ids ) {
		foreach ( $variation_ids as $key => $variation_id ) {
			$variation            = wc_get_product( $variation_id );
			$variation_attributes = implode( ', ', $variation->get_variation_attributes() );
			echo wp_kses_post( $variation->get_name() ) . '<br>';
		}
	}
}

/**
 * Function to retrieve global discount.
 *
 * @param object $product Product Object.
 * @return array
 */
function get_global_bulk_discounts( $product ) {

	$is_b2be_discount_enable = get_option( 'b2be_enable_discount' );

	if ( ( ! $product ) || ( 'true' != $is_b2be_discount_enable ) ) {
		return array();
	}

	// simple product..
	$product_id = $product->get_id();
	// variations..
	$product_variation = $product->get_children(); // get variations...

	// simple..
	$regular_price      = $product->get_regular_price();
	$product_categories = $product->get_category_ids();

	// user data..
	$user      = wp_get_current_user();
	$user_id   = $user->ID;
	$user_role = $user->roles;

	$b2be_discount_rules = get_option( 'b2be_discount_rule' );
	$discounted_rule     = array();
	$discount            = array();
	$priority            = 11;

	if ( empty( $b2be_discount_rules ) ) {
		return;
	}

	$temp_array = array();

	// Loop to find count for rowspan.
	foreach ( $b2be_discount_rules as $key => $value ) {
		if ( isset( $temp_array[ $value['priority'] ] ) ) {
			$temp_array[ $value['priority'] ]++;
		} else {
			$temp_array[ $value['priority'] ] = 1;
		}
	}

	usort(
		$b2be_discount_rules,
		function ( $item1, $item2 ) {
			if ( $item1['priority'] == $item2['priority'] ) {
				return 0;
			}
			return ( $item1['priority'] < $item2['priority'] ) ? -1 : 1;
		}
	);

	foreach ( $b2be_discount_rules as $key => $main_rule ) {

		$is_role      = false;
		$is_category  = false;
		$is_customer  = false;
		$is_product   = false;
		$is_variation = false;
		$got_it       = false;

		if ( isset( $main_rule['is_role_based'] ) && 'true' == $main_rule['is_role_based'] ) {
			if ( array_intersect( $user_role, $main_rule['roles'] ) ) {
				$is_role = true;
			}
		}
		if ( isset( $main_rule['is_category_based'] ) && 'true' == $main_rule['is_category_based'] ) {
			if ( array_intersect( $product_categories, $main_rule['categories'] ) ) {
				$is_category = true;
			}
		}
		if ( isset( $main_rule['is_customer_based'] ) && 'true' == $main_rule['is_customer_based'] ) {
			if ( $main_rule['customer'] ) {
				if ( in_array( $user_id, $main_rule['customer'] ) ) {
					$is_customer = true;
				}
			}
		}

		if ( isset( $main_rule['is_product_based'] ) && 'true' == $main_rule['is_product_based'] ) {
			if ( $product->is_type( 'simple' ) ) {
				if ( isset( $main_rule['products'] ) ) {
					if ( in_array( $product_id, $main_rule['products'] ) ) {
						$is_product = true;
					}
				}
			} elseif ( $product->is_type( 'variable' ) ) {
				if ( isset( $main_rule['innerRule'][0]['variation_ids'] ) ) {
					if ( array_intersect( $product_variation, $main_rule['innerRule'][0]['variation_ids'] ) ) {
						$is_product   = true;
						$is_variation = true;
					}
				}
			}
		}

		foreach ( $main_rule['innerRule'] as $index => $value ) {
			$main_rule['innerRule'][ $index ]['priority'] = $main_rule['priority'];
			$main_rule['innerRule'][ $index ]['ruleId']   = $main_rule['ruleId'];
			if ( ( isset( $main_rule['is_product_based'] ) && isset( $main_rule['innerRule'] ) ) && ( 'false' == $main_rule['is_product_based'] || empty( $main_rule['innerRule'][ $index ]['variation_ids'] ) ) && false == $is_variation ) {
				$main_rule['innerRule'][ $index ]['variation_ids'] = $product_variation;
			}
		}
		if ( ( isset( $main_rule['is_category_based'] ) && isset( $main_rule['is_product_based'] ) ) && ( 'false' == $main_rule['is_category_based'] && 'false' == $main_rule['is_product_based'] ) && empty( $main_rule['products'] ) ) {
			$main_rule['products'] = get_b2be_products_id();
			$is_product            = true;
		}

		if ( ( $is_role || $is_customer ) && ( $is_category || $is_product ) ) {

			$got_it = inner_rule_check( $main_rule, $discounted_rule );

		} elseif ( ( isset( $main_rule['is_role_based'] ) && 'true' != $main_rule['is_role_based'] ) && ( isset( $main_rule['is_customer_based'] ) && 'true' != $main_rule['is_customer_based'] ) && ( isset( $main_rule['is_product_based'] ) && 'true' != $main_rule['is_product_based'] ) && $is_category ) {

			$got_it = inner_rule_check( $main_rule, $discounted_rule );

		} elseif ( 'true' != $main_rule['is_role_based'] && 'true' != $main_rule['is_customer_based'] && 'true' != $main_rule['is_category_based'] && $is_product ) {

			$got_it = inner_rule_check( $main_rule, $discounted_rule );

		} elseif ( ( isset( $main_rule['is_category_based'] ) && 'true' != $main_rule['is_category_based'] ) && ( isset( $main_rule['is_product_based'] ) && 'true' != $main_rule['is_product_based'] ) && ( $is_role || $is_customer ) ) {

			$got_it = inner_rule_check( $main_rule, $discounted_rule );

		} elseif ( 'true' != $main_rule['is_role_based'] && 'true' != $main_rule['is_customer_based'] && ( $is_category || $is_product ) ) {

			$got_it = inner_rule_check( $main_rule, $discounted_rule );

		} elseif ( ( isset( $main_rule['is_role_based'] ) && 'true' != $main_rule['is_role_based'] ) && ( isset( $main_rule['is_category_based'] ) && 'true' != $main_rule['is_category_based'] ) && ( isset( $main_rule['is_customer_based'] ) && 'true' != $main_rule['is_customer_based'] ) && ( isset( $main_rule['is_product_based'] ) && 'true' != $main_rule['is_product_based'] ) ) {

			$got_it = inner_rule_check( $main_rule, $discounted_rule );

		}

		if ( $got_it && ! isset( $discounted_rule['innerRule'] ) && count( $b2be_discount_rules ) != $temp_array[ $main_rule['priority'] ] ) {
			return apply_filters( 'b2be_discount_rules_limit', $discounted_rule );
		}
	}

	/*
	@name: b2be_discount_rules_limit
	@desc: Modify B2B Discount Rules applying on each product.
	@param: (array) $discounted_rule B2B Ecommerce Discount Rules Array.
	@package: b2b-ecommerce-for-woocommerce
	@module: discount options
	@type: filter
	*/
	return apply_filters( 'b2be_discount_rules_limit', $discounted_rule );
}

/**
 * Function to format inner rules.
 *
 * @param array $main_rule Main Rule Array.
 * @param array $quantity_table Array to be formatted.
 * @param bool  $got_it Checkmark to return.
 */
function inner_rule_check( $main_rule, &$quantity_table, $got_it = false ) {
	if ( 'true' == $main_rule['is_quantity_based'] ) {
		$quantity_table['discount_format'] = $main_rule['discount_format'];
		$quantity_table                    = array_merge( $quantity_table, $main_rule['innerRule'] );

		if ( ! isset( $quantity_table['mode'] ) || 'q' != $quantity_table['mode'] ) {
			unset( $quantity_table['discount'] );
			unset( $quantity_table['type'] );
			$quantity_table['mode'] = 'q';
			$got_it                 = true;
		}
	} else {
		$quantity_table = $main_rule['innerRule'][0];
		if ( ! isset( $quantity_table['mode'] ) || 'd' != $quantity_table['mode'] ) {
			$quantity_table['mode'] = 'd';
			$got_it                 = true;
		}
	}
	return $got_it;
}
