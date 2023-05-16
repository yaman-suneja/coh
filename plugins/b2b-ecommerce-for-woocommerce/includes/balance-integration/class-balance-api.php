<?php
/**
 * File to render the getBalance transaction api.
 *
 * @package woocommerce-getbalance-gateway
 */

if ( ! class_exists( 'Balance_Api' ) ) {

	/**
	 * Class Balance Api
	 */
	class Balance_Api {

		/**
		 * Api key to connect to the get balance account.
		 *
		 * @var int $api_key Api key to connect to the get balance account.
		 */
		public $api_key;

		/**
		 * Api key base url
		 *
		 * @var int $url Api key base url.
		 */
		public $url;

		/**
		 * Function constructor...
		 *
		 * @param string $api_key Api key for the balance account.
		 * @param string $url Url for the balance account whether production or test.
		 */
		public function __construct( $api_key = '', $url = '' ) {
			$this->api_key = $api_key;
			$this->url     = $url;
		}

		/**
		 * Function to create the order transaction for get balance transaction api.
		 *
		 * @param int    $order_id Id of the order being processed.
		 * @param string $payment_gateway_id Current gateway id.
		 */
		public function create_order_transaction( $order_id, $payment_gateway_id = 'getbalance' ) {

			$order           = wc_get_order( $order_id );
			$payment_gateway = WC()->payment_gateways->payment_gateways()[ $payment_gateway_id ];

			$allowed_payment_methods       = get_balance_gateway_payment_methods( $payment_gateway_id );
			$allowed_terms_payment_methods = get_balance_gateway_payment_methods( $payment_gateway_id, 'terms' );

			$line_item_data = array();
			foreach ( $order->get_items() as $item_id => $item ) {
				$product          = wc_get_product( $item->get_product_id() );
				$line_item_data[] = array(
					'title'       => $item->get_name(),
					'quantity'    => $item->get_quantity(),
					'productId'   => strval( $item->get_product_id() ),
					'productSku'  => $product->get_sku(),
					'variationId' => strval( $item->get_variation_id() ),
					'itemType'    => $product->get_type(),
					'price'       => floatval( $item->get_subtotal() / intval( $item->get_quantity() ) ),
					'tax'         => floatval( $item->get_subtotal_tax() ),
				);
			}
			$data = array(
				'externalReferenceId'        => strval( $order_id ),
				'notes'                      => $order->get_customer_note(),
				'buyer'                      => array(
					'email' => $order->get_billing_email(),
				),
				'plan'                       => array(
					'planType'   => 'invoice',
					'chargeDate' => current_time( 'Y-m-d' ),
				),
				'currency'                   => strval( $order->get_currency() ),
				'totalPrice'                 => floatval( $order->get_total() ),
				'totalDiscount'              => floatval( $order->get_total_discount() ),
				'lines'                      => array(
					array(
						'shippingPrice' => floatval( $order->get_shipping_total() ),
						'tax'           => floatval( $order->get_total_tax() ),
						'lineItems'     => $line_item_data,
					),
				),
				'financingConfig'            => array(
					'financingNetDays' => intval( $payment_gateway->get_option( 'term_days' ) ),
				),
				'autoPayouts'                => true,
				'communicationConfig'        => array(
					'emailsTo' => array(
						$order->get_billing_email(),
					),
				),
				'allowedPaymentMethods'      => $allowed_payment_methods,
				'allowedTermsPaymentMethods' => $allowed_terms_payment_methods,
				'billingAddress'             => array(
					'firstName'    => $order->get_billing_first_name(),
					'lastName'     => $order->get_billing_last_name(),
					'addressLine1' => $order->get_billing_address_1(),
					'addressLine2' => $order->get_billing_address_2(),
					'zipCode'      => $order->get_billing_postcode(),
					'company'      => $order->get_billing_company(),
					'countryCode'  => $order->get_billing_country(),
					'state'        => $order->get_billing_state(),
					'city'         => $order->get_billing_city(),
				),
			);
			if ( empty( $allowed_terms_payment_methods ) ) {
				unset( $data['allowedTermsPaymentMethods'] );
			}
			$response = $this->send_request( 'v1/transactions', 'POST', $data );

			return $response;
		}

		/**
		 * Function to process the transaction through get balance api.
		 *
		 * @param string $endpoint Endpoint of the api.
		 * @param string $method Methid being used for the api.
		 * @param array  $data Array of data to be sent to process the transaction.
		 */
		public function send_request( $endpoint, $method, $data ) {

			$headers                 = array();
			$headers['Content-Type'] = 'application/json';
			$headers['x-api-key']    = $this->api_key;
			$response                = wp_remote_post(
				$this->url . $endpoint,
				array(
					'method'  => $method,
					'headers' => $headers,
					'body'    => json_encode( $data ),
				)
			);

			if ( is_wp_error( $response ) ) {
				$error_message = $response->get_error_message();
				echo wp_kses_post( "Something went wrong: $error_message" );

				if ( 'yes' === $payment_gateway->get_option( 'logging' ) ) {
					wc_get_balance_logger( $error_message ); // Logging he error message in woocommerce logs...
				}
				return false;
			} else {
				$response_data = json_decode( wp_remote_retrieve_body( $response ), true );
				return $response_data;
			}
		}


	}
}
