<?php
/**
 * WC RFQ.
 *
 * @package b2b-ecommerce-for-woocomerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'B2BE_MOV' ) ) {
	/**
	 * Class B2BE_MOV.
	 */
	class B2BE_MOV {

		/**
		 * Initialize Discount rule array.
		 *
		 * @var array $discunt_rules Discount Rule Array.
		 */
		public $b2be_mov_rules = array();

		/**
		 * Construct.
		 */
		public function __construct() {

			B2BE_MOv_Settings::init();
			if ( 'yes' == get_option( 'b2be_enable_mov', 'no' ) ) {
				add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'mov_limit_add_to_cart_validation' ), 1, 6 );
				add_filter( 'b2be_add_to_rfq_validation', array( $this, 'mov_limit_add_to_cart_validation' ), 1, 6 );
				add_filter( 'woocommerce_update_cart_validation', array( $this, 'mov_limit_update_cart_validation' ), 1, 4 );
				add_action( 'woocommerce_after_checkout_validation', array( $this, 'mov_check_order_limit_before_checkout' ), 10, 2 );
			}
		}

		/**
		 * Validate On add to cart according to MOv rules.
		 *
		 * @param bool  $passed Pass the validation or not?.
		 * @param int   $product_id Product id.
		 * @param int   $quantity Product Quantity.
		 * @param int   $variation_id Variation Id.
		 * @param array $variations Variation Array.
		 * @param bool  $is_rfq Is the function called from rfq.
		 */
		public function mov_limit_add_to_cart_validation( $passed, $product_id, $quantity, $variation_id = '', $variations = '', $is_rfq = false ) {

			$product       = wc_get_product( $product_id );
			$product_title = $product->get_name();

			$subtotal       = $this->wc_qty_get_cart_subtotal( $product_id, $is_rfq ) + $product->get_price() * $quantity;
			$b2be_mov_rules = b2be_get_mov_limit( $product );
			$product_min    = 0;
			$product_max    = '';

			if ( ! is_user_logged_in() ) {
				return true;
			}

			if ( ! empty( $b2be_mov_rules ) ) {
				foreach ( $b2be_mov_rules as $key => $b2be_mov_rule ) {
					$product_min = $b2be_mov_rule['minValue'];
					$product_max = $b2be_mov_rule['maxValue'];
					if ( $product->is_type( 'variable' ) ) {
						if ( isset( $b2be_mov_rule['variation_ids'] ) ) {
							if ( ! in_array( $variation_id, $b2be_mov_rule['variation_ids'] ) ) {
								$product_min = 0;
								$product_max = '';
							}
						}
					}
				}
			}

			if ( '' != $product_max ) {

				if ( $product_max < $subtotal ) {
					$passed = false;
					/* translators: %s: min quantity */
					wc_add_notice( sprintf( __( 'Your Cart subtotal for <b>"%1$s"</b> has reached the maximum purchase limit i.e %2$s. ', 'b2b-ecommerce' ), $product->get_name(), $product_max ), 'error' );
				}
			}

			return $passed;
		}

		/**
		 * Validate On add to cart according to MOv rules.
		 *
		 * @param bool  $passed Pass the validation or not?.
		 * @param int   $cart_item_key Cart item key.
		 * @param array $values Cart object.
		 * @param array $quantity Cart Items Quantity.
		 * @param bool  $is_rfq Check if rfq product.
		 */
		public function mov_limit_update_cart_validation( $passed, $cart_item_key, $values, $quantity, $is_rfq = false ) {

			$product        = wc_get_product( $values['product_id'] );
			$subtotal       = $product->get_price() * $quantity;
			$b2be_mov_rules = b2be_get_mov_limit( $product );
			$passed         = true;
			$product_max    = '';

			if ( ! empty( $b2be_mov_rules ) ) {
				foreach ( $b2be_mov_rules as $key => $b2be_mov_rule ) {
					$product_min = $b2be_mov_rule['minValue'];
					$product_max = $b2be_mov_rule['maxValue'];
					if ( $product->is_type( 'variable' ) ) {
						if ( isset( $b2be_mov_rule['variation_ids'] ) ) {
							if ( ! in_array( $variation_id, $b2be_mov_rule['variation_ids'] ) ) {
								$product_min = 0;
								$product_max = '';
							}
						}
					}
				}
			}

			if ( '' != $product_max ) {

				if ( $product_max < $subtotal ) {
					$passed = false;
					/* translators: %s: min quantity */
					wc_add_notice( sprintf( __( 'Your Cart subtotal for <b>"%1$s"</b> has reached the maximum purchase limit i.e %2$s. ', 'b2b-ecommerce' ), $product->get_name(), $product_max ), 'error' );
				}
			}

			return $passed;
		}

		/**
		 * Apply Value limit validation before checkout.
		 *
		 * @param array $data Checkout Items Data.
		 * @param array $errors Errors to be shown if validation fails.
		 */
		public function mov_check_order_limit_before_checkout( $data, $errors ) {

			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
				$product_min    = 0;
				$product_max    = '';
				$product_id     = $cart_item['product_id'];
				$product        = wc_get_product( $product_id );
				$product_name   = $product->get_name();
				$quantity       = $cart_item['quantity'];
				$subtotal       = $product->get_price() * $quantity;
				$b2be_mov_rules = b2be_get_mov_limit( $product );
				if ( ! empty( $b2be_mov_rules ) ) {
					foreach ( $b2be_mov_rules as $key => $b2be_mov_rule ) {
						$product_min = $b2be_mov_rule['minValue'];
						$product_max = $b2be_mov_rule['maxValue'];
						if ( $product->is_type( 'variable' ) ) {
							if ( isset( $b2be_mov_rule['variation_ids'] ) ) {
								if ( ! in_array( $variation_id, $b2be_mov_rule['variation_ids'] ) ) {
									$product_min = 0;
									$product_max = '';
								}
							}
						}
					}
				}

				$validation_errors = '';

				if ( '' != $product_max && '' != $product_min ) {

					if ( ! ( $product_min <= $subtotal && $product_max >= $subtotal ) ) {

						if ( $product_max < $subtotal ) {
							/* translators: %s: max value */
							$validation_errors = sprintf( __( 'Your subtotal for <b>"%1$s"</b> has reached the maximum purchase limit i.e %2$s. ', 'b2b-ecommerce' ), $product_name, $product_max );
						}
						if ( $product_min > $subtotal ) {
							/* translators: %s: min value */
							$validation_errors = sprintf( __( 'Your subtotal for <b>"%1$s"</b> has not reached the minimum purchase limit i.e %2$s. ', 'b2b-ecommerce' ), $product_name, $product_min );
						}
						/* translators: %1$s: min value %2$s: max value */
						$validation_errors .= sprintf( __( 'It must be between %1$s and %2$s', 'b2b-ecommerce' ), $product_min, $product_max );

					}
				} else {
					if ( '' != $product_max ) {

						if ( $product_max < $subtotal ) {
							/* translators: %s: min value */
							$validation_errors = sprintf( __( 'Your subtotal for <b>"%1$s"</b> has reached the maximum purchase limit i.e %2$s. ', 'b2b-ecommerce' ), $product_name, $product_max );
						}
					}
					if ( '' != $product_min ) {

						if ( $product_min > $subtotal ) {
							/* translators: %s: min value */
							$validation_errors = sprintf( __( 'Your subtotal for <b>"%1$s"</b> has not reached the minimum purchase limit i.e %2$s. ', 'b2b-ecommerce' ), $product_name, $product_min );
						}
					}
				}

				if ( $validation_errors ) {
					$errors->add( 'validation', $validation_errors );
				}
			}
		}

		/**
		 * Return the quantity of given product in cart.
		 *
		 * @param int  $product_id Product Id.
		 * @param bool $rfq Check if its an rfq product.
		 */
		public function wc_qty_get_cart_subtotal( $product_id, $rfq ) {
			global $woocommerce;

			$subtotal = 0; // iniializing quantity to 0.
			$cart     = $woocommerce->cart->get_cart();
			if ( $rfq ) {
				$cart = $woocommerce->rfq->get_cart();
			}

			// search the cart for the product in and calculate quantity.
			if ( ! empty( $cart ) ) {
				foreach ( $cart as $other_cart_item_keys => $values ) {
					if ( $values['product_id'] == $product_id ) {
						$product  = wc_get_product( $values['product_id'] );
						$subtotal = (int) $product->get_price() * $values['quantity'];
					}
				}
			}

			/*
			@name: b2b_mov_subtotal_check
			@desc: Modify the quantity of current product in cart.
			@param: (string) $subtotal Quantity of product in cart.
			@package: b2b-ecommerce-for-woocommerce
			@module: minimum order quantity
			@type: filter
			*/
			return apply_filters( 'b2b_mov_subtotal_check', $subtotal );
		}

	}
}
new B2BE_MOV();
