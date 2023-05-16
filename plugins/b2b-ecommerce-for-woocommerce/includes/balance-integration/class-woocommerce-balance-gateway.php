<?php
/**
 * Class to register Balance gateway...
 *
 * @package woocommerce-getbalance-gateway
 */

if ( ! class_exists( 'Woocommerce_Balance_Gateway' ) ) {

	/**
	 * Class Woocommerce_Balance_Gateway.
	 */
	class Woocommerce_Balance_Gateway extends WC_Payment_Gateway {

		/**
		 * Constructor for the gateway.
		 */
		public function __construct() {

			// Setup general properties.
			$this->setup_properties();

			// Load the settings.
			$this->init_form_fields();
			$this->init_settings();

			// Get settings.
			$this->title       = $this->get_option( 'title' );
			$this->description = $this->get_option( 'description' );
			$this->testmode    = 'yes' === get_option( 'b2be_balance_testmode', 'no' );
			$this->api_key     = 'yes' === get_option( 'b2be_balance_testmode', 'no' ) ? get_option( 'b2be_balance_test_account_id', '' ) : get_option( 'b2be_balance_live_account_id', '' );
			$this->base_url    = 'yes' === get_option( 'b2be_balance_testmode', 'no' ) ? 'https://sandbox.app.blnce.io/api/' : '';

			$this->instructions       = $this->get_option( 'instructions' );
			$this->enable_for_methods = $this->get_option( 'enable_for_methods', array() );
			$this->enable_for_virtual = $this->get_option( 'enable_for_virtual', 'yes' ) === 'yes';

			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
			add_action( 'woocommerce_thankyou_' . $this->id, array( $this, 'thankyou_page' ) );

			// Customer Emails.
			add_action( 'woocommerce_email_before_order_table', array( $this, 'email_instructions' ), 10, 3 );
			add_action( 'wp_head', array( $this, 'header_style' ) );

			add_action( 'wp_enqueue_scripts', array( $this, 'payment_scripts' ) );

		}

		/**
		 * Setup general properties for the gateway.
		 */
		protected function setup_properties() {
			$this->id                 = 'getbalance';
			$this->icon               = apply_filters( 'woocommerce_getbalance_icon', '' );
			$this->method_title       = __( 'Balance', 'woocommerce' );
			$this->method_description = __( 'Have your customers pay with balance.', 'woocommerce' );
			$this->has_fields         = true;
			$this->testmode           = 'no';
		}

		/**
		 * Initialize Gateway Settings Form Fields
		 */
		public function init_form_fields() {

			$this->form_fields = apply_filters(
				'b2be_balance_form_fields',
				array(
					'enabled'     => array(
						'title'   => __( 'Enable/Disable', 'wcgb' ),
						'type'    => 'checkbox',
						'label'   => __( 'Enable balance Payment', 'wcgb' ),
						'default' => 'yes',
					),
					'title'       => array(
						'title'       => __( 'Title', 'wcgb' ),
						'type'        => 'text',
						'description' => __( 'This controls the title for the payment method the customer sees during checkout.', 'wcgb' ),
						'default'     => __( 'Balance', 'wcgb' ),
						'desc_tip'    => true,
					),
					'description' => array(
						'title'       => __( 'Description', 'wcgb' ),
						'type'        => 'text',
						'description' => __( 'Payment method description that the customer will see on your checkout.', 'wcgb' ),
						'default'     => __( 'Please remit payment to Store Name upon pickup or delivery.', 'wcgb' ),
						'desc_tip'    => true,
					),
				),
				false
			);
		}

		/**
		 * Function to proceess gateway payment.
		 *
		 * @param int    $order_id Id of current order being placed.
		 * @param string $payment_gateway_id Current gateway Id.
		 */
		public function process_payment( $order_id, $payment_gateway_id = 'getbalance' ) {
			include_once 'class-balance-api.php';

			$order = wc_get_order( $order_id );
			$order->update_status( 'pending', __( 'Awaiting Balance payment', 'wcgb' ) );

			// Reduce stock levels.
			$order->reduce_order_stock();

			// Remove cart.
			// WC()->cart->empty_cart();.

			$getbalance_api = new Balance_Api( $this->api_key, $this->base_url );

			$response = $getbalance_api->create_order_transaction( $order_id, $payment_gateway_id ); // Creating the transaction through getBalance.

			if ( isset( $response['statusCode'] ) && 201 != $response['statusCode'] ) {

				if ( 'yes' === $this->get_option( 'logging' ) ) {
					wc_get_balance_logger( $response['message'] );   // Logging he error message in woocommerce logs...
				}

				$order_key    = $order->get_order_key();
				$redirect_url = wc_get_checkout_url() . 'order-pay/' . $order_id . '/?pay_for_order=true&key=' . $order_key;

				wc_add_notice( 'Something went wrong during the payment process. Please try a different payment method.', 'error' );

				return array(
					'result'   => 'success',
					'redirect' => $redirect_url,
				);
			}

			$token          = $response['token'];
			$transaction_id = ( isset( $response['id'] ) ) ? $response['id'] : null;

			update_post_meta( $order_id, '_getbalance_transaction_id', $transaction_id );

			// Return thankyou redirect.
			return array(
				'result'         => 'success',
				'redirect'       => $this->wcgb_get_payment_url( $order, $order_id, $token, $transaction_id ),
				'transaction_id' => $transaction_id,
				'respponse'      => $response,
				'getbalance'     => true,
			);
		}

		/**
		 * Fucntion to generate balance checkout url.
		 *
		 * @param object $order Current order being processed.
		 * @param int    $order_id Id of current order being processed.
		 * @param int    $token Checkout token for transaction api response.
		 * @param int    $transaction_id Checkout transaction id for transaction api response.
		 */
		public function wcgb_get_payment_url( $order, $order_id, $token, $transaction_id ) {
			$url = wc_get_checkout_url() . 'get-balance/?order_id=' . $order_id . '&key=' . $order->order_key . '&token=' . $token . '&transaction_id=' . $transaction_id;
			return $url;
		}

		/**
		 * Output for the order received page.
		 */
		public function thankyou_page() {
			if ( $this->instructions ) {
				echo wp_kses_post( wpautop( wptexturize( $this->instructions ) ) );
			}
		}

		/**
		 * Add content to the WC emails.
		 *
		 * @param WC_Order $order Order object.
		 * @param bool     $sent_to_admin Check if sent to admin.
		 * @param bool     $plain_text Check if plain text or html.
		 */
		public function email_instructions( $order, $sent_to_admin, $plain_text = false ) {

			if ( $this->instructions && ! $sent_to_admin && 'getbalance' === $order->payment_method && $order->has_status( 'on-hold' ) ) {
				echo wp_kses_post( wpautop( wptexturize( $this->instructions ) ) . PHP_EOL );
			}
		}

		public function payment_scripts() {
			if ( $this->testmode ) {
				wp_enqueue_script( 'getbalance_test', 'https://checkout.sandbox.getbalance.com/blnceSDK.js', array(), rand() );
			} else {
				wp_enqueue_script( 'getbalance_live', 'https://checkout.getbalance.com/blnceSDK.js', array(), rand() );
			}
		}

		/**
		 * Function to register styling and balance sdk file.
		 */
		public function header_style() {
			?>
			<style>
				#blnce-checkout > div {
					position: fixed;
					top: 0;
					right: 0;
					bottom: 0;
					left: 0;
				}
			</style>

			<?php
		}

	}
}
