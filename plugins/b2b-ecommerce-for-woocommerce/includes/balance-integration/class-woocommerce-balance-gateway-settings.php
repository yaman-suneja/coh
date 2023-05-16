<?php
/**
 * Class to register Get Balance gateway settings...
 *
 * @package woocommerce-getbalance-gateway
 */

if ( ! class_exists( 'Woocommerce_Balance_Gateway_Settings' ) ) {

	/**
	 * Class Woocommerce_Balance_Gateway_Settings.
	 */
	class Woocommerce_Balance_Gateway_Settings {

		/**
		 * Constructor.
		 */
		public function __construct() {
			add_filter( 'b2be_payment_form_fields', array( $this, 'b2be_payment_form_fields' ), 10, 1 );
			add_filter( 'b2be_balance_form_fields', array( $this, 'b2be_payment_form_fields' ), 10, 2 );
		}

		/**
		 * Fucntion to register balance payment fields...
		 *
		 * @param array $fields Fields already registered in b2b and balance gateways.
		 * @param bool  $allow_terms Check if the gateway is a b2b gateway.
		 */
		public function b2be_payment_form_fields( $fields, $allow_terms = true ) {

			$fields = array_merge(
				$fields,
				array(
					'term_days'             => array(
						'title'             => __( 'Term Days', 'b2b-ecommerce' ),
						'type'              => 'number',
						'desc_tip'          => 'Enter the number of days for which you want to finance the customer.',
						'custom_attributes' => array(
							'min' => 0,
						),
						'default'           => 30,
					),
					'logging'               => array(
						'title'    => __( 'Logging', 'b2b-ecommerce' ),
						'type'     => 'checkbox',
						'desc_tip' => 'Save debug messages to the WooCommerce System Status log.',
						'default'  => 'yes',
					),
					'allowedPaymentMethods' => array(
						'title'   => __( 'Allowed Payment Methods', 'b2b-ecommerce' ),
						'type'    => 'title',
						'id'      => 'balance_allowed_payment_methods',
						'default' => 'yes',
					),
					'creditCard'            => array(
						'title'    => __( 'Credit Card Payments', 'b2b-ecommerce' ),
						'type'     => 'checkbox',
						'label'    => __( 'Enable Credit Card Payments', 'b2b-ecommerce' ),
						'default'  => 'yes',
						'desc_tip' => 'This will allow the customer to pay with credit card.',
					),
					'bank'                  => array(
						'title'    => __( 'Bank Transfer', 'b2b-ecommerce' ),
						'type'     => 'checkbox',
						'label'    => __( 'Enable Bank Transfer', 'b2b-ecommerce' ),
						'default'  => 'yes',
						'desc_tip' => 'This will allow the customer to pay through bank transfer.',
					),
					'invoice'               => array(
						'title'    => __( 'Invoice', 'b2b-ecommerce' ),
						'type'     => 'checkbox',
						'label'    => __( 'Enable Invoice', 'b2b-ecommerce' ),
						'default'  => 'yes',
						'desc_tip' => 'This will allow the customer to pay through invoice.',
					),
					'payWithTerms'          => array(
						'title'    => __( 'Pay With Terms', 'b2b-ecommerce' ),
						'type'     => 'checkbox',
						'label'    => __( 'Enable Pay With Terms', 'b2b-ecommerce' ),
						'default'  => 'yes',
						'desc_tip' => 'This will allow the customer to pay with terms',
						'class'    => 'b2be_gateway',
					),
				)
			);

			if ( ! $allow_terms ) {
				unset( $fields['payWithTerms'] );
				unset( $fields['term_days'] );
				return $fields;
			}

			$fields = array_merge(
				$fields,
				array(
					'allowedTermPaymentMethods' => array(
						'title' => __( 'Allowed Term Payment Methods', 'b2b-ecommerce' ),
						'type'  => 'title',
						'id'    => 'balance_allowed_term_payment_methods',
						'class' => 'balance_term_methods',
					),
					'term_creditCard'           => array(
						'title'    => __( 'Credit Card Payments', 'b2b-ecommerce' ),
						'type'     => 'checkbox',
						'label'    => __( 'Enable Credit Card Payments', 'b2b-ecommerce' ),
						'default'  => 'yes',
						'desc_tip' => 'This will allow the customer to pay with credit card.',
						'class'    => 'balance_term_methods',
					),
					'term_bank'                 => array(
						'title'    => __( 'Bank Transfer', 'b2b-ecommerce' ),
						'type'     => 'checkbox',
						'label'    => __( 'Enable Bank Transfer', 'b2b-ecommerce' ),
						'default'  => 'yes',
						'desc_tip' => 'This will allow the customer to pay through bank transfer.',
						'class'    => 'balance_term_methods',
					),
					'term_invoice'              => array(
						'title'    => __( 'Invoice', 'b2b-ecommerce' ),
						'type'     => 'checkbox',
						'label'    => __( 'Enable Invoice', 'b2b-ecommerce' ),
						'default'  => 'yes',
						'desc_tip' => 'This will allow the customer to pay through invoice.',
						'class'    => 'balance_term_methods',
					),
				)
			);
			return $fields;
		}

	}
}
new Woocommerce_Balance_Gateway_Settings();
