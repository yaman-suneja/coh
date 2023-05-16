<?php
/**
 * WC RFQ.
 *
 * @package b2b-ecommerce-for-woocomerce
 */

 /**
  * Function to return post object by slug.
  *
  * @param string $name Title of post type.
  * @param string $content Content of post type.
  * @param string $date Date of post type.
  * @param string $type Type of post type.
  */
function b2be_custom_role_exists( $name, $content = '', $date = '', $type = '' ) {
	global $wpdb;

	$post_name = wp_unslash( sanitize_post_field( 'post_name', $name, 0, 'db' ) );
	$args      = array();
	if ( ! empty( $name ) ) {
		return (int) $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE 1=1 AND post_name = %s", $post_name ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped.
	}
	return 0;
}

/**
 * Custom Added Role Details.
 */
function get_custom_added_roles() {

	$default_roles = array(
		'administrator',
		'editor',
		'author',
		'contributor',
		'subscriber',
		'customer',
		'shop_manager',
	);

	$role_names = wp_roles()->role_names;

	foreach ( $role_names as $role_id => $role_name ) {

		if ( in_array( $role_id, $default_roles ) ) {
			unset( $role_names[ $role_id ] );
		}
	}

	return ! empty( $role_names ) ? $role_names : false;
}

/**
 * Get Current User role by user id.
 *
 * @param int $user_id User id.
 */
function get_current_user_role_by_id( $user_id ) {

	$user = get_userdata( $user_id );

	return empty( $user ) ? '' : $user->roles;

}

/**
 * Get globally setted discount with respect to role.
 *
 * @param int $user_role user role.
 */
function get_globaly_setted_discount( $user_role ) {

	$all_custom_user_role = get_custom_added_roles( '' );

	if ( is_array( $all_custom_user_role ) && count( $all_custom_user_role ) > 0 ) {
		foreach ( $all_custom_user_role as $key => $custom_user_role ) {
			$b2b_e_user_role = str_replace( ' ', '_', strtolower( $custom_user_role['role'] ) );
			if ( $b2b_e_user_role == $user_role ) {

				return $custom_user_role['discount'];

			}
		}
	}
	return false;
}

/**
 * Get Product Based Discount.
 *
 * @param int $product_id Product id.
 * @param int $user_role User role.
 */
function get_product_based_discount( $product_id, $user_role ) {

	$product_based_settings = get_custom_added_roles( $product_id );
	if ( $product_based_settings ) {
		foreach ( $product_based_settings as $key => $custom_user_role ) {
			if ( $key == $user_role ) {

				return $custom_user_role;

			}
		}
	}
	return false;

}

/**
 * Get regular price of the given variation product.
 *
 * @param object $product Product Object.
 */
function get_variation__regular_price( $product ) {

	$variation__regular_price = array();
	if ( $product->is_type( 'variable' ) ) {

		$product_variations = $product->get_available_variations();
		foreach ( $product_variations as $variation ) {
			$reqular_price = ! empty( $variation['display_regular_price'] ) ? $variation['display_regular_price'] : '';

			$variation__regular_price[ $variation['variation_id'] ] = $reqular_price;
		}
		return $variation__regular_price;
	}

}

/**
 * Get discounted variation price of the given variable product.
 *
 * @param object $product Product object.
 * @param int    $format format to return.
 */
function get_discounted_variation_price( $product, $format ) {

	$user_id                   = get_current_user_id();
	$user_role                 = get_current_user_role_by_id( $user_id );
	$is_product_based_discount = get_option( 'codup-role-baseddiscount_type_product' );
	$is_global_based_discount  = get_option( 'codup-role-baseddiscount_type_global' );
	$variation_based_discount  = '';
	if ( ! empty( $user_role ) ) {
		$all_variations_discounted_prices = array();

		if ( $product->is_type( 'variable' ) ) {

			$product            = new WC_Product_Variable( $product->get_id() );
			$product_variations = $product->get_available_variations();

			if ( $product_variations ) {
				foreach ( $product_variations as $variation ) {

					$reqular_price = ! empty( $variation['display_regular_price'] ) ? $variation['display_regular_price'] : '';

					if ( 'yes' == $is_product_based_discount ) {
						$variation_based_discount = ( isset( $variation['user_role_discount'][ $user_role[0] ] ) && key_exists( $user_role[0], $variation['user_role_discount'] ) && ! empty( $variation['user_role_discount'][ $user_role[0] ] ) ) ? $variation['user_role_discount'][ $user_role[0] ] : 0;
					}
					if ( 'yes' == $is_global_based_discount ) {
						$variation_based_discount = ( ! empty( get_globaly_setted_discount( $user_role[0] ) ) ) ? get_globaly_setted_discount( $user_role[0] ) : 0;
					}
					if ( 'yes' == $is_product_based_discount && 'yes' == $is_global_based_discount ) {
						$variation_based_discount = ( isset( $variation['user_role_discount'][ $user_role[0] ] ) && key_exists( $user_role[0], $variation['user_role_discount'] ) && ! empty( $variation['user_role_discount'][ $user_role[0] ] ) ) ? $variation['user_role_discount'][ $user_role[0] ] : get_globaly_setted_discount( $user_role[0] );
					}
					if ( $variation_based_discount ) {

						$discounted_price = $reqular_price * ( $variation_based_discount / 100 );
						$discounted_price = $reqular_price - $discounted_price;

						$all_variations_discounted_prices[ $variation['variation_id'] ] = $discounted_price;

					} else {

						$all_variations_discounted_prices[ $variation['variation_id'] ] = $reqular_price;

					}
				}
			}

			if ( 'min' == $format ) {

				return ! empty( $all_variations_discounted_prices ) ? min( $all_variations_discounted_prices ) : '';

			} elseif ( 'max' == $format ) {

				return ! empty( $all_variations_discounted_prices ) ? max( $all_variations_discounted_prices ) : '';

			} elseif ( '' == $format ) {

				return $all_variations_discounted_prices;

			}
		}
	}
}

/**
 * Check if current page isblog page or not.
 */
function is_blog() {
	global $post;
	$posttype = get_post_type( $post );

	return ( ( ( is_archive() ) || ( is_author() ) || ( is_category() ) || ( is_home() ) || ( is_single() ) || ( is_tag() ) ) && ( 'post' == $posttype ) ) ? true : false;
}

/**
 * Check if role exist or not.
 *
 * @param string $role Current user role.
 */
if ( ! function_exists( 'role_exists' ) ) {
	function role_exists( $role ) {

		if ( ! empty( $role ) ) {
			return $GLOBALS['wp_roles']->is_role( $role );
		}

		return false;
	}
}
