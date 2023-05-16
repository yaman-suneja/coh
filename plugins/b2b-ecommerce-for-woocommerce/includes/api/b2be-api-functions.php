<?php
/**
 * B2B Ecommerce API Functions File.
 *
 * @package class-b2be-api-fucntions.php
 */

/**
 * Function for verfication of consumer key and user.
 */
function b2be_auth_verification() {

	error_reporting( 0 );
	require_once WooCommerce::plugin_path() . '/includes/legacy/api/v3/class-wc-api-authentication.php';
	$test   = new WC_API_Authentication();
	$user   = get_current_user();
	$result = $test->authenticate( $user );

	$result_array = (array) $result;

	if ( ! empty( $result_array['data']->ID ) && 0 != $result_array['data']->ID ) {
		return true;
	}

	return $result;

}

/**
 * Function to return given quote id.
 *
 * @param object $quote Quote Object.
 */
function get_quote_items_id( $quote ) {

	$i           = 0;
	$item_ids    = array();
	$quote_items = $quote->get_quote_items();
	foreach ( $quote_items as $key => $quote_item ) {
		$item_ids[ $i ] = $quote_item['product_id'];
		$i++;
	}
	return $item_ids ? $item_ids : false;
}

/**
 * Return user_id if auth goes right else return error.
 */
function b2be_user_id_for_api() {
	error_reporting( 0 );

	require_once WooCommerce::plugin_path() . '/includes/legacy/api/v3/class-wc-api-authentication.php';
	$test = new WC_API_Authentication();

	$user   = get_current_user();
	$result = $test->authenticate( $user );

	$result_array = (array) $result;

	return $result_array['ID'];
}
