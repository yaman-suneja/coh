<?php
/**
 * B2B Custom Payment Method Class File.
 *
 * @package B2B Ecommerce For Woocommerce/Gateways
 */

if ( ! class_exists( 'B2BE_Payment_Gateway_1' ) ) {

	/**
	 * B2B Custom Payment Method Class.
	 */
	class B2BE_Payment_Gateway_1 extends WC_Payment_Gateway {

		/**
		 * Constructor for the gateway.
		 *
		 * @param string $b2b_custom_gateway Gateway Name.
		 */
		public function __construct( $b2b_custom_gateway ) {

			$this->id           = strtolower( str_replace( ' ', '_', $b2b_custom_gateway ) );
			$this->icon         = apply_filters( 'b2be_payment_gateway_1_icon', '' );
			$this->has_fields   = true;
			$this->method_title = $b2b_custom_gateway;

			// Load the settings.
			$this->init_form_fields();
			$this->init_settings();

			// Define user set variables.
			$this->title        = $this->get_option( 'title' );
			$this->description  = $this->get_option( 'description' );
			$this->instructions = $this->get_option( 'instructions', $this->description );

			// Actions.
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
			add_action( 'woocommerce_thankyou_' . $this->id, array( $this, 'thankyou_page' ) );

			// Customer Emails.
			add_action( 'woocommerce_email_before_order_table', array( $this, 'email_instructions' ), 10, 3 );
		}


		/**
		 * Initialize Gateway Settings Form Fields.
		 */
		public function init_form_fields() {

			$this->form_fields = apply_filters(
				'b2be_payment_form_fields',
				array(
					'enabled'     => array(
						'title'   => __( 'Enable/Disable', 'b2b-ecommerce' ),
						'type'    => 'checkbox',
						'label'   => 'Enable ' . $this->method_title,
						'default' => 'yes',
					),
					'title'       => array(
						'title'       => __( 'Title', 'b2b-ecommerce' ),
						'type'        => 'text',
						'description' => __( 'This controls the title for the payment method the customer sees during checkout.', 'b2b-ecommerce' ),
						'default'     => $this->method_title,
						'desc_tip'    => true,
					),
					'description' => array(
						'title'       => __( 'Description', 'b2b-ecommerce' ),
						'type'        => 'text',
						'description' => __( 'Payment method description that the customer will see on your checkout.', 'b2b-ecommerce' ),
						'default'     => null,
						'desc_tip'    => true,
					),
				)
			);

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
		 * @param bool     $sent_to_admin Send email to admin.
		 * @param bool     $plain_text Plain text.
		 */
		public function email_instructions( $order, $sent_to_admin, $plain_text = false ) {

			if ( $this->instructions && ! $sent_to_admin && $this->id === $order->payment_method && $order->has_status( 'on-hold' ) ) {
				echo wp_kses_post( wpautop( wptexturize( $this->instructions ) ) ) . PHP_EOL;
			}
		}

		/**
		 * Process the payment and return the result
		 *
		 * @param int $order_id Order Id.
		 * @return array
		 */
		public function process_payment( $order_id ) {

			$response = apply_filters( 'b2be_payment_process_response', '', $order_id, $this->id );

			if ( ! empty( $response ) ) {
				return $response;
			}

			$order = wc_get_order( $order_id );

			$response = apply_filters( 'b2be_payment_process_response', '', $order_id, $this->id );

			$status = apply_filters( 'b2be_payment_method_status', 'completed' );

			$order->update_status( $status, $this->method_title );

			// Reduce stock levels.
			$order->reduce_order_stock();

			// Remove cart.
			WC()->cart->empty_cart();

			// Return thankyou redirect.
			return array(
				'result'   => 'success',
				'redirect' => $this->get_return_url( $order ),
			);
		}

	}
}
