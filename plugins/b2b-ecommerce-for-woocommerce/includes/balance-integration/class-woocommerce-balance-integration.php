<?php
/**
 * Class to register Get Balance Integration...
 *
 * @package woocommerce-getbalance-gateway
 */

if ( ! class_exists( 'Woocommerce_Balance_Integration' ) ) {

	/**
	 * Class Woocommerce_Balance_Integration.
	 */
	class Woocommerce_Balance_Integration {

		/**
		 * Constructor.
		 */
		public function __construct() {

			add_filter( 'woocommerce_payment_gateways', array( $this, 'wc_getbalance_add_to_gateways' ) );

			add_action( 'init', array( $this, 'wc_getbalance_endpoints' ) );
			add_filter( 'query_vars', array( $this, 'wc_getbalance_query_vars' ), 0 );
			add_action( 'wp_loaded', array( $this, 'wc_getbalance_flush_rewrite_rules' ) );

			add_action( 'wp_ajax_wcgb_mark_order_payment', array( $this, 'wcgb_mark_order_payment' ) );
			add_action( 'wp_ajax_nopriv_wcgb_mark_order_payment', array( $this, 'wcgb_mark_order_payment' ) );

			add_filter( 'template_include', array( $this, 'override_template' ), 200 );
			add_filter( 'b2be_payment_process_response', array( $this, 'wc_getbalance_payment_processor' ), 1, 3 );

		}

		/**
		 * Function to register get balance gateway.
		 *
		 * @param array $gateways Gateways array.
		 */
		public function wc_getbalance_add_to_gateways( $gateways ) {

			require_once 'class-woocommerce-balance-gateway.php';
			if ( 'yes' == get_option( 'b2be_enable_balance_gateway' ) ) {
				$gateways[] = 'Woocommerce_Balance_Gateway';
			}

			return $gateways;
		}

		/**
		 * Function to register custom endpoint to checkout page.
		 */
		public function wc_getbalance_endpoints() {
			add_rewrite_endpoint( 'get-balance', EP_ROOT | EP_PAGES );
		}

		/**
		 * Function to register custom endpoint to checkout page.
		 *
		 * @param array $vars Query var array for woocommerce.
		 */
		public function wc_getbalance_query_vars( $vars ) {

			$vars[] = 'get-balance';
			return $vars;
		}

		/**
		 * Function to register custom endpoint to checkout page.
		 */
		public function wc_getbalance_flush_rewrite_rules() {
			flush_rewrite_rules();
		}

		/**
		 * Function to redirect the user to get balance payment page.
		 */
		public function wcgb_mark_order_payment() {

			if ( ! empty( $_POST['_wpnonce'] ) ) {
				wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ) );
			}

			if ( isset( $_POST['order_key'] ) && isset( $_POST['checkoutToken'] ) ) {

				$order_key = sanitize_text_field( wp_unslash( $_POST['order_key'] ) );
				$order_id  = wc_get_order_id_by_order_key( $order_key ); // Get the order ID.
				$order     = wc_get_order( $order_id ); // Get an instance of the WC_Order object.

				if ( $order->has_status( 'pending' ) ) {
					$order          = new WC_Order( $order_id );
					$transaction_id = get_post_meta( $order_id, '_getbalance_transaction_id', true );
					$order->payment_complete( $transaction_id );

					// The text for the note.
					$note = __( 'Payment completed through Get Balance by ', 'b2b-ecommerce' ) . $transaction_id;
					// Add the note.
					$order->add_order_note( $note );
				}
			}

		}

		/**
		 * Oveririding Chekout template for balance.
		 *
		 * @param string $template Path for the template.
		 */
		public function override_template( $template ) {

			if ( isset( $_GET['key'] ) && isset( $_GET['token'] ) ) {
				$template = CWRFQ_PLUGIN_DIR . '/templates/balance-integration/balance-checkout.php';
			}
			return $template;
		}

		/**
		 * Get the payment processor reesponse from balance gateway.
		 *
		 * @param string $response Array of data in response.
		 * @param int    $order_id Current Order Id.
		 * @param int    $payment_gateway_id Array of data in response.
		 */
		public function wc_getbalance_payment_processor( $response, $order_id, $payment_gateway_id ) {

			$getbalance_gateway = new Woocommerce_Balance_Gateway();
			return $getbalance_gateway->process_payment( $order_id, $payment_gateway_id );

		}

		/**
		 * Prevent redirection on checkout if user is paying for pending order.
		 *
		 * @param string $redirect Redirect url.
		 * @return boolean
		 */
		public function prevent_redirect( $redirect ) {
			global $wp;
			if ( isset( $wp->query_vars['get-balance'] ) ) {
				$order_id = absint( $wp->query_vars['get-balance'] );
				$order    = wc_get_order( $order_id );

				if ( isset( $_GET['key'] ) && isset( $_GET['token'] ) && $order->needs_payment() && 'getbalance' === $order->get_payment_method() ) {
					return false;
				}
			}
			return $redirect;
		}
	}
}
new Woocommerce_Balance_Integration();
