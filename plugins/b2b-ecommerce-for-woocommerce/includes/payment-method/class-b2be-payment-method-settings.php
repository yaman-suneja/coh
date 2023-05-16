<?php
/**
 * WC Ecommerce For Woocommerce Main Class.
 *
 * @package b2b-ecommerce-for-woocomerce
 */

defined( 'ABSPATH' ) || exit;

/**
 * Codup_B2B_Ecommerce_For_Woocommerce class.
 */
class B2BE_Payment_Method_Settings {

	/**
	 * Function Calculate Shipping.
	 */
	public static function init() {
		add_action( 'plugins_loaded', __CLASS__ . '::b2be_custom_gateways_init', 11 );
		add_action( 'woocommerce_admin_field_codup_payment_method', __CLASS__ . '::output_payment_method_fields' );
		add_action( 'woocommerce_admin_field_codup_payment_method_footer', __CLASS__ . '::output_payment_method_fields_footer' );
		add_filter( 'woocommerce_payment_gateways', __CLASS__ . '::b2be_offline_gateways' );

	}

	/**
	 * Return RFQ setting fields.
	 *
	 * @return type
	 */
	public static function get_settings() {

		$settings = self::get_payment_method_fields();

		return $settings;
	}

	/**
	 * Return User Role setting fields.
	 *
	 * @return type
	 */
	public static function get_payment_method_fields() {

		$fields = array(
			'general_title'             => array(
				'title' => __( 'B2B Ecommerce Payment Methods', 'b2b-ecommerce' ),
				'type'  => 'title',
				'id'    => 'b2be_payment_method_general_title',
				'desc'  => __( 'Adds a customized payment method for B2B customers who have payment terms with you e.g. Net 30, Net 60.<br/>Customers can select and proceed with chekout without any immediate monetory	transaction.', 'b2b-ecommerce' ),
			),

			'enable_has_terms'          => array(
				'name'     => __( 'Payment Method', 'b2b-ecommerce' ),
				'desc_tip' => __( 'This will enable  Payment Method option.', 'b2b-ecommerce' ),
				'type'     => 'checkbox',
				'desc'     => __( 'Enable Payment Method', 'b2b-ecommerce' ),
				'id'       => 'codup-rfq_enable_has_terms',
			),
			'integrate_balance_gateway' => array(
				'name'     => __( 'Integrate With Balance', 'b2b-ecommerce' ),
				'desc_tip' => __( 'This will integrate your B2B Payment Methods with Balance.', 'b2b-ecommerce' ),
				'type'     => 'checkbox',
				'desc'     => __( 'Enable To Integrate With Balance', 'b2b-ecommerce' ),
				'id'       => 'b2be_integrate_balance',
			),
		);
		$payment_method = get_option( 'b2be_payment_method' );
		if ( null == $payment_method || 0 == count( $payment_method ) ) {
			$payment_method = array( '' );
		}
		$fields['payment_method']        = array(
			'type'           => 'codup_payment_method',
			'id'             => 'codup_ecommerce_payment_method_settings',
			'class'          => 'codup-ecommerce-payment-method-mode',
			'css'            => 'width:100%',
			'payment_method' => $payment_method,
		);
		$fields['payment_method_footer'] = array(
			'type' => 'codup_payment_method_footer',
			'id'   => 'codup-payment-method-footer',
		);
		$fields['general_title_end']     = array(
			'type' => 'sectionend',
			'id'   => 'b2be_payment_method_general_title',
		);

		$fields = array_merge(
			$fields,
			array(
				'api_credentials_title'     => array(
					'title' => __( 'Balance Payment Method', 'b2b-ecommerce' ),
					'type'  => 'title',
					'id'    => 'get_balance_account_keys',
					'desc'  => 'To Get your API Key <a href="https://b2bwoo.com/contact-us/">Create Your Balance</a> Account. You can also configure your gateway settings individually from the <a href="' . site_url() . '/wp-admin/admin.php?page=wc-settings&tab=checkout">Payments</a> Tab.',
				),
				'enable_balance_gateway'    => array(
					'name'     => __( 'Balance Gateway', 'b2b-ecommerce' ),
					'desc_tip' => __( 'This will enable the standalone Balance Gateway.', 'b2b-ecommerce' ),
					'type'     => 'checkbox',
					'desc'     => __( 'Enable Balance Gateway', 'b2b-ecommerce' ),
					'id'       => 'b2be_enable_balance_gateway',
				),
				'testmode'                  => array(
					'title'    => __( 'Test mode', 'b2b-ecommerce' ),
					'type'     => 'checkbox',
					'desc'     => __( 'Enable Test Mode', 'b2b-ecommerce' ),
					'id'       => 'b2be_balance_testmode',
					'default'  => 'no',
					'desc_tip' => __( 'Place the payment gateway in test mode using test API keys.', 'b2b-ecommerce' ),
				),
				'test_account_id'           => array(
					'title'    => __( 'Test API Key', 'b2b-ecommerce' ),
					'id'       => 'b2be_balance_test_account_id',
					'type'     => 'text',
					'desc_tip' => __( 'Get your API keys from your balance account.', 'b2b-ecommerce' ),
				),
				'live_account_id'           => array(
					'title'    => __( 'Live API Key', 'b2b-ecommerce' ),
					'id'       => 'b2be_balance_live_account_id',
					'type'     => 'text',
					'desc_tip' => __( 'Get your API keys from your balance account.', 'b2b-ecommerce' ),
				),
				'api_credentials_title_end' => array(
					'type' => 'sectionend',
					'id'   => 'get_balance_account_keys',
				),
			)
		);

		return $fields;
	}

	/**
	 * Output User Role setting fields.
	 *
	 * @param array $field_config Role Based Settings Tab array.
	 */
	public static function output_payment_method_fields( $field_config ) {
		include CWRFQ_PLUGIN_DIR . '/includes/admin/payment-method/views/payment-method-fields.php';
	}

	/**
	 * Output User Role setting fields footer.
	 *
	 * @param array $field_config Role Based Settings Tab array.
	 */
	public static function output_payment_method_fields_footer( $field_config ) {
		include CWRFQ_PLUGIN_DIR . '/includes/admin/payment-method/views/payment-method-footer.php';
	}

	/**
	 * Function to create custom payment gateways classes.
	 */
	public static function b2be_custom_gateways_init() {
		$b2b_custom_gateways = get_option( 'b2be_payment_method' );
		if ( ! $b2b_custom_gateways ) {
			return;
		}

		$i = 1;
		foreach ( $b2b_custom_gateways as $id => $b2b_custom_gateway ) {

			include "gateways/class-b2be-payment-gateway-{$i}.php";
			$i++;

			if ( 6 == $i ) {
				break;
			}
		}
	}

	/**
	 * Function to register custom payment gateways.
	 *
	 * @param array $gateways Payment Gateways.
	 */
	public static function b2be_offline_gateways( $gateways ) {

		$global_has_term_enabled = get_option( 'codup-rfq_enable_has_terms', 'no' );
		if ( 'no' == $global_has_term_enabled ) {
			return $gateways;
		}

		$b2b_custom_gateways = get_option( 'b2be_payment_method' );
		if ( ! $b2b_custom_gateways ) {
			return $gateways;
		}

		$i = 1;
		foreach ( $b2b_custom_gateways as $id => $b2b_custom_gateway ) {

			$class = 'B2BE_Payment_Gateway_' . $i;
			if ( class_exists( $class ) ) {
				$b2b_custom_gateway_id = strtolower( str_replace( ' ', '_', $b2b_custom_gateway ) );
				$gateways[]            = new $class( $b2b_custom_gateway );
			}
			$i++;
			if ( 6 == $i ) {
				break;
			}
		}

		return $gateways;

	}
}
