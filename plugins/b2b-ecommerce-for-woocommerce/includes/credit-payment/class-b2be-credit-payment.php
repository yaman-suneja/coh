<?php
/**
 * B2b_credit_payment all Admin side work.
 *
 * @package class-b2be-credit-payment.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'B2BE_Credit_Payment' ) ) {
	/**
	 * Class B2BE_Credit_Payment.
	 */
	class B2BE_Credit_Payment extends WC_Payment_Gateway {

		/**
		 * Construct.
		 */
		public function __construct() {

			$this->id = 'credit_payment';

			/*
			@name: b2be_credit_payment_icon
			@desc: Modify B2B payment method icon.
			@param: (string) $icon b2b custom payment method icon.
			@package: b2b-ecommerce-for-woocommerce
			@module: credit payment
			@type: filter
			*/
			$this->icon               = apply_filters( 'b2be_credit_payment_icon', '' );
			$this->method_title       = __( 'Credit Payment', 'b2b-ecommerce' );
			$this->method_description = __( 'Credit Payment Description', 'b2b-ecommerce' );
			$this->has_fields         = false;

			// Load the settings.
			$this->init_form_fields();
			$this->init_settings();

			// Define user set variables.
			$this->title        = $this->get_option( 'title' );
			$this->description  = $this->get_option( 'description' );
			$this->instructions = $this->get_option( 'instructions' );

			// Actions.
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		}

		/**
		 * Initialize Gateway Settings Form Fields.
		 */
		public function init_form_fields() {

			$this->form_fields = array(
				'enabled'      => array(
					'title'       => __( 'Enable/Disable', 'b2b-ecommerce' ),
					'label'       => __( 'Enable Credit Payment', 'b2b-ecommerce' ),
					'type'        => 'checkbox',
					'description' => '',
					'default'     => 'no',
				),
				'title'        => array(
					'title'       => __( 'Title', 'b2b-ecommerce' ),
					'type'        => 'text',
					'description' => __( 'Payment method description that the customer will see on your checkout.', 'b2b-ecommerce' ),
					'default'     => __( 'Credit Payment', 'b2b-ecommerce' ),
					'desc_tip'    => true,
				),
				'description'  => array(
					'title'       => __( 'Description', 'b2b-ecommerce' ),
					'type'        => 'textarea',
					'description' => __( 'Payment method description that the customer will see on your website.', 'b2b-ecommerce' ),
					'default'     => __( 'Credit Payment Description', 'b2b-ecommerce' ),
					'desc_tip'    => true,
				),
				'instructions' => array(
					'title'       => __( 'Instructions', 'b2b-ecommerce' ),
					'type'        => 'textarea',
					'description' => __( 'Payment method instructions that the customer will see on your website.', 'b2b-ecommerce' ),
					'default'     => __( 'Credit Payment Instuctions', 'b2b-ecommerce' ),
					'desc_tip'    => true,
				),
			);
		}

		/**
		 * Function process_payment.
		 *
		 * @param string $order_id The Order Id.
		 */
		public function process_payment( $order_id ) {
			$order = wc_get_order( $order_id );
			$order->update_status( 'processing', 'Credit Payment' );

			// maintain credit logs.
			b2be_maintain_credit_payments( $order_id, $order->get_user_id(), $order->get_total(), 'Credit Deducted' );
			WC()->cart->empty_cart();

			return array(
				'result'   => 'success',
				'redirect' => $this->get_return_url( $order ),
			);
		}

	}
}
new B2BE_Credit_Payment();
