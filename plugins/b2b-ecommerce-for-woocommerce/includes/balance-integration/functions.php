<?php
/**
 * File to include helper functions of balance integration.
 *
 * @package balance-integration/functions.php
 */

if ( ! function_exists( 'wc_get_balance_logger' ) ) {

	/**
	 * Logging the errors occured durinf api connections with balance platform.
	 *
	 * @param string $message Message to be logged.
	 */
	function wc_get_balance_logger( $message ) {

		$logger    = wc_get_logger();
		$log_entry = print_r( $message, 1 );

		$context = array( 'source' => 'woocommerce-getbalance-gateway' );

		$logger->error( $log_entry, $context );
	}
}

if ( ! function_exists( 'get_balance_gateway_payment_methods' ) ) {

	/**
	 * Getting the balance methods enabled.
	 *
	 * @param string $gateway_id Current Gateway Id.
	 * @param string $type Type of method.
	 */
	function get_balance_gateway_payment_methods( $gateway_id, $type = '' ) {

		// Getting the balance gateway object for default settings...
		$balance_gateway = WC()->payment_gateways->payment_gateways()[ $gateway_id ];
		if ( 'terms' !== $type ) {
			$allowed_payment_gateways = array();

			if ( 'yes' == $balance_gateway->get_option( 'payWithTerms' ) ) {
				$allowed_payment_gateways[] = 'payWithTerms';
			}
			if ( 'yes' == $balance_gateway->get_option( 'creditCard' ) ) {
				$allowed_payment_gateways[] = 'creditCard';
			}
			if ( 'yes' == $balance_gateway->get_option( 'bank' ) ) {
				$allowed_payment_gateways[] = 'bank';
			}
			if ( 'yes' == $balance_gateway->get_option( 'invoice' ) ) {
				$allowed_payment_gateways[] = 'invoice';
			}

			return $allowed_payment_gateways;
		} elseif ( 'terms' === $type ) {

			$allowed_term_payment_gateways = array();

			if ( 'yes' == $balance_gateway->get_option( 'term_creditCard' ) ) {
				$allowed_term_payment_gateways[] = 'creditCard';
			}
			if ( 'yes' == $balance_gateway->get_option( 'term_bank' ) ) {
				$allowed_term_payment_gateways[] = 'bank';
			}
			if ( 'yes' == $balance_gateway->get_option( 'term_invoice' ) ) {
				$allowed_term_payment_gateways[] = 'invoice';
			}

			return $allowed_term_payment_gateways;
		}

	}
}
