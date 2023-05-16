<?php
/**
 * WC RFQ.
 *
 * @package b2b-ecommerce-for-woocomerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'B2BE_MOQ' ) ) {
	/**
	 * Class B2BE_MOQ.
	 */
	class B2BE_MOQ {

		/**
		 * Initialize Discount rule array.
		 *
		 * @var array $discunt_rules Discount Rule Array.
		 */
		public $b2be_moq_rules = array();

		/**
		 * Construct.
		 */
		public function __construct() {

			if ( is_admin() ) {
				B2BE_MOQ_Settings::init();
			}

			$is_b2be_moq_enable = get_option( 'b2be_enable_moq' );

			if ( 'true' == $is_b2be_moq_enable ) {
				add_filter( 'woocommerce_quantity_input_args', array( $this, 'moq_limit_product_quantity' ), 10, 2 );
				add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'moq_limit_add_to_cart_validation' ), 1, 6 );
				add_filter( 'b2be_add_to_rfq_validation', array( $this, 'moq_limit_add_to_cart_validation' ), 1, 6 );
				add_filter( 'woocommerce_update_cart_validation', array( $this, 'moq_limit_update_cart_validation' ), 1, 4 );
				add_filter( 'woocommerce_cart_item_quantity', array( $this, 'moq_quantity_field_validation' ), 10, 2 );
				add_filter( 'woocommerce_loop_add_to_cart_link', array( $this, 'update_quantity_on_shop' ), 20, 3 );
			}
		}

		/**
		 * Updating the quantity arguments on shop page cart button.
		 *
		 * @param array  $quantity Add to cart button html.
		 * @param object $product Current product object.
		 * @param object $args Argument array of add to cart button.
		 */
		public function update_quantity_on_shop( $quantity, $product, $args ) {

			$b2be_moq_rules = b2be_get_moq_limit( $product );
			if ( $product->is_type( 'simple' ) ) {
				if ( isset( $b2be_moq_rules[0]['minQuantity'] ) ) {
					$args['quantity'] = $b2be_moq_rules[0]['minQuantity'];
				} elseif ( isset( $b2be_moq_rules[0]['multiplier'] ) ) {
					$args['quantity'] = $b2be_moq_rules[0]['multiplier'];
				}
				$quantity = sprintf(
					'<a href="%s" data-quantity="%s" class="%s" %s>%s</a>',
					esc_url( $product->add_to_cart_url() ),
					esc_attr( isset( $args['quantity'] ) ? $args['quantity'] : 1 ),
					esc_attr( isset( $args['class'] ) ? $args['class'] : 'button' ),
					isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '',
					esc_html( $product->add_to_cart_text() )
				);
			}
			return $quantity;
		}

		/**
		 * Function to limit product Quantity.
		 *
		 * @param array  $args Arguments array.
		 * @param object $product Product object.
		 */
		public function moq_limit_product_quantity( $args, $product ) {

			if ( ! $product ) {
				return $args;
			}

			$b2be_moq_rules = b2be_get_moq_limit( $product );
			if ( ! $b2be_moq_rules ) {
				return $args;
			}

			foreach ( $b2be_moq_rules as $key => $b2be_moq_rule ) {

				if ( isset( $b2be_moq_rule['minQuantity'] ) ) {
					$args['min_value'] = $b2be_moq_rule['minQuantity'];
				}
				if ( isset( $b2be_moq_rule['maxQuantity'] ) ) {
					$args['max_value'] = $b2be_moq_rule['maxQuantity'];
				} elseif ( isset( $b2be_moq_rule['multiplier'] ) ) {
					$args['min_value'] = $b2be_moq_rule['multiplier'];
					$args['step']      = $b2be_moq_rule['multiplier'];
				}
			}

			/*
			@name: b2b_moq_quantity_input_args
			@desc: Modify quantity limit on input field after moq is applied.
			@param: (array) $args Array of arguments passed to quantity field.
			@package: b2b-ecommerce-for-woocommerce
			@module: minimum order quantity
			@type: filter
			*/
			return apply_filters( 'b2b_moq_quantity_input_args', $args );

		}

		/**
		 * Validate quantity fields according to MOQ rules.
		 *
		 * @param int $product_quantity Product Quantity.
		 * @param int $cart_item_key Cart Item Key.
		 */
		public function moq_quantity_field_validation( $product_quantity, $cart_item_key ) {

			if ( ! is_user_logged_in() ) {
				return $product_quantity;
			}

			foreach ( WC()->cart->get_cart() as $cart_key => $cart_item ) {

				$product_quantity = '';

								 /*
				@name: b2be_cart_item_product
				@desc: Modify Product before MOQ validation on adding product to cart.
				@param: (array) $cart_item_data Object of cart.
				@param: (array) $cart_item Current line item of cart.
				@param: (array) $cart_item_key Unique key for cart item.
				@package: b2b-ecommerce-for-woocommerce
				@module: minimum order quantity
				@type: filter
				*/
				$_product    = apply_filters( 'b2be_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
				$product_min = 0;
				$product_max = '';

				if ( 'variation' == $_product->get_type() ) {
					$product_id   = $_product->get_parent_id();
					$variation_id = $_product->get_id();
				} else {
					$product_id = $_product->get_id();
				}

				$product        = wc_get_product( $product_id );
				$b2be_moq_rules = b2be_get_moq_limit( $product );

				if ( ! empty( $b2be_moq_rules ) ) {
					foreach ( $b2be_moq_rules as $key => $b2be_moq_rule ) {

						if ( ! isset( $b2be_moq_rule['multiplier'] ) ) {
							$product_min = $b2be_moq_rule['minQuantity'];
							$product_max = $b2be_moq_rule['maxQuantity'];
						} elseif ( isset( $b2be_moq_rule['multiplier'] ) ) {
							$product_min = $b2be_moq_rule['multiplier'];
							$product_max = '';
						}

						if ( $product->is_type( 'variable' ) ) {
							if ( isset( $b2be_moq_rule['variation_ids'] ) ) {
								if ( ! in_array( $variation_id, $b2be_moq_rule['variation_ids'] ) ) {
									$product_min = 0;
									$product_max = '';
								}
							}
						}
					}
				}

				if ( $cart_key === $cart_item_key ) {
					return woocommerce_quantity_input(
						array(
							'input_name'  => "cart[{$cart_item_key}][qty]",
							'input_value' => $cart_item['quantity'],
							'max_value'   => $product_max,
							'min_value'   => $product_min,
							'step'        => $product_min,
						),
						$product,
						false
					);
				}
			}
		}

		/**
		 * Validate On add to cart according to MOQ rules.
		 *
		 * @param bool  $passed Pass the validation or not?.
		 * @param int   $product_id Product id.
		 * @param int   $quantity Product Quantity.
		 * @param int   $variation_id Variation Id.
		 * @param array $variations Variation Array.
		 * @param bool  $is_rfq Is the function called from rfq.
		 */
		public function moq_limit_add_to_cart_validation( $passed, $product_id, $quantity, $variation_id = '', $variations = '', $is_rfq = false ) {

			$product       = wc_get_product( $product_id );
			$product_title = $product->get_name();

			$already_in_cart = $this->wc_qty_get_cart_qty( $product_id, $is_rfq );

			$b2be_moq_rules = b2be_get_moq_limit( $product );
			$product_min    = 0;
			$product_max    = '';

			if ( ! is_user_logged_in() ) {
				return true;
			}
			if ( ! isset( $b2be_moq_rules[0]['multiplier'] ) ) {
				if ( ! empty( $b2be_moq_rules ) ) {
					foreach ( $b2be_moq_rules as $key => $b2be_moq_rule ) {

						if ( isset( $b2be_moq_rule['multiplier'] ) ) {
							$product_min = $b2be_moq_rule['multiplier'];
							$product_max = '';
						}

						if ( $product->is_type( 'variable' ) ) {
							if ( isset( $b2be_moq_rule['variation_ids'] ) ) {
								if ( ! in_array( $variation_id, $b2be_moq_rule['variation_ids'] ) ) {
									$product_min = 0;
									$product_max = '';
								}
							}
						}
					}
				}

				if ( '' != $product_min ) {
					$new_min = $product_min;
				} else {
					return $passed;
				}

				if ( '' != $product_max ) {
					$new_max = $product_max;
				} else {
					return $passed;
				}

				if ( ! is_null( $new_max ) && ! empty( $already_in_cart ) ) {

					if ( ( $already_in_cart + $quantity ) > $new_max ) {

						if ( ! $is_rfq ) {
							$cart_url = '<a href="' . esc_url( wc_get_cart_url() ) . '">' . __( 'your cart', 'b2b-ecommerce' ) . '</a>';
						} else {
							$cart_url = '<a href="' . esc_url( b2be_get_rfq_cart_url() ) . '">' . __( 'your rfq', 'b2b-ecommerce' ) . '</a>';
						}

						/*
						@name: isa_b2be_max_qty_error_message_already_had
						@desc: Modify the MOQ validation error message.
						@param: (string) $message MOQ Validation Error Message.
						@param: (int) $quantity Maximum allowed quantity by moq rule.
						@param: (int) $already_in_cart Quantity of current product in cart.
						@package: b2b-ecommerce-for-woocommerce
						@module: minimum order quantity
						@type: filter
						*/
						wc_add_notice(
							/* translators: %1$s: max quantity %2$s: product name %3$s: cart link %4$s: product in cart quantity */
							apply_filters( 'isa_b2be_max_qty_error_message_already_had', sprintf( __( 'You can add a maximum of %1$s %2$s\'s to %3$s. You already have %4$s.', 'b2b-ecommerce' ), $new_max, $product_title, $cart_url, $already_in_cart ), $new_max, $already_in_cart ),
							'error'
						);
						return false;
					}
				}

				if ( $quantity < $new_min ) {
					$passed = false;
					/* translators: %s: min quantity */
					wc_add_notice( sprintf( __( 'You can add a minimum of %1$s quantity in cart.', 'b2b-ecommerce' ), $new_min ), 'error' );
				} elseif ( $quantity > $new_max ) {
					$passed = false;
					/* translators: %s: max quantity */
					wc_add_notice( sprintf( __( 'You can add a maximum of %1$s quantity in cart.', 'b2b-ecommerce' ), $new_max ), 'error' );
				}
			} elseif ( isset( $b2be_moq_rules[0]['multiplier'] ) ) {
				$multiplier  = $b2be_moq_rules[0]['multiplier'];
				$product_max = '';

				if ( ! empty( $multiplier ) && 0 != ( $quantity % $multiplier ) ) {
					$passed = false;

					/*
					@name: b2be_qty_multiplier_error_message
					@desc: Modify the moq validation error message on updating cart.
					@param: (string) $message Moq Validation Message.
					@param: (int) $quantity Max quantity allowed by moq rules.
					@package: b2b-ecommerce-for-woocommerce
					@module: minimum order quantity
					@type: filter
					*/
					wc_add_notice(
						/* translators: %1$s: max quantity %2$s: product name %3$s: cart link */
						apply_filters( 'b2be_qty_multiplier_error_message', sprintf( __( 'You can add a multiple of %1$s %2$s\'s to %3$s.', 'woocommerce-max-quantity' ), $multiplier, $product->get_name(), '<a href="' . esc_url( wc_get_cart_url() ) . '">' . __( 'your cart', 'woocommerce-max-quantity' ) . '</a>' ), $multiplier ),
						'error'
					);
				}
			}

			return $passed;
		}

		/**
		 * Return the quantity of given product in cart.
		 *
		 * @param int  $product_id Product Id.
		 * @param bool $rfq Check if its an rfq product.
		 */
		public function wc_qty_get_cart_qty( $product_id, $rfq ) {
			global $woocommerce;
			$running_qty = 0; // iniializing quantity to 0.

			if ( ! $rfq ) {
				$cart = $woocommerce->cart->get_cart();
			} else {
				$cart = $woocommerce->rfq->get_cart();
			}

			// search the cart for the product in and calculate quantity.
			foreach ( $cart as $other_cart_item_keys => $values ) {
				if ( $product_id == $values['product_id'] ) {
					$running_qty += (int) $values['quantity'];
				}
			}

			/*
			@name: b2b_moq_already_in_cart_check
			@desc: Modify the quantity of current product in cart.
			@param: (string) $running_qty Quantity of product in cart.
			@package: b2b-ecommerce-for-woocommerce
			@module: minimum order quantity
			@type: filter
			*/
			return apply_filters( 'b2b_moq_already_in_cart_check', $running_qty );
		}

		/**
		 * Validate On add to cart according to MOQ rules.
		 *
		 * @param bool  $passed Pass the validation or not?.
		 * @param int   $cart_item_key Cart item key.
		 * @param array $values Cart object.
		 * @param array $quantity Cart Items Quantity.
		 * @param bool  $is_rfq Check if rfq product.
		 */
		public function moq_limit_update_cart_validation( $passed, $cart_item_key, $values, $quantity, $is_rfq = false ) {

			$product        = wc_get_product( $values['product_id'] );
			$b2be_moq_rules = b2be_get_moq_limit( $product );
			$passed         = true;

			if ( isset( $b2be_moq_rules[0]['minQuantity'] ) || isset( $b2be_moq_rules[0]['maxQuantity'] ) ) {
				$product_min = isset( $b2be_moq_rules[0]['minQuantity'] ) ? $b2be_moq_rules[0]['minQuantity'] : '';
				$product_max = isset( $b2be_moq_rules[0]['maxQuantity'] ) ? $b2be_moq_rules[0]['maxQuantity'] : '';
				if ( ! empty( $product_min ) ) {
					$new_min = $product_min;
				} else {
					return $passed;
				}

				if ( ! empty( $product_max ) ) {
					$new_max = $product_max;
				} else {
					return $passed;
				}

				if ( isset( $new_max ) && $quantity > $new_max ) {
					/*
					@name: b2be_qty_error_message
					@desc: Modify the moq validation error message on updating cart.
					@param: (string) $message Moq Validation Message.
					@param: (int) $quantity Max quantity allowed by moq rules.
					@package: b2b-ecommerce-for-woocommerce
					@module: minimum order quantity
					@type: filter
					*/
					wc_add_notice(
						/* translators: %1$s: max quantity %2$s: product name %3$s: cart link */
						apply_filters( 'b2be_qty_error_message', sprintf( __( 'You can add a maximum of %1$s %2$s\'s to %3$s.', 'woocommerce-max-quantity' ), $new_max, $product->get_name(), '<a href="' . esc_url( wc_get_cart_url() ) . '">' . __( 'your cart', 'woocommerce-max-quantity' ) . '</a>' ), $new_max ),
						'error'
					);
					$passed = false;
				}

				if ( isset( $new_min ) && $quantity < $new_min ) {
					wc_add_notice(
						apply_filters(
							'b2be_qty_error_message',
							sprintf(
								/* translators: %1$s: min quantity %2$s: product name %3$s: cart link */
								__( 'You should have minimum of %1$s %2$s\'s to %3$s.', 'woocommerce-max-quantity' ),
								$new_min,
								$product->get_name(),
								'<a href="' . esc_url( wc_get_cart_url() ) . '">' . __( 'your cart', 'woocommerce-max-quantity' ) . '</a>'
							),
							$new_min
						),
						'error'
					);
					$passed = false;
				}
			} elseif ( isset( $b2be_moq_rules[0]['multiplier'] ) ) {
				$multiplier  = $b2be_moq_rules[0]['multiplier'];
				$product_max = '';

				if ( ! empty( $multiplier ) && 0 != ( $quantity % $multiplier ) ) {
					$passed = false;

					/*
					@name: b2be_qty_multiplier_error_message
					@desc: Modify the moq validation error message on updating cart.
					@param: (string) $message Moq Validation Message.
					@param: (int) $quantity Max quantity allowed by moq rules.
					@package: b2b-ecommerce-for-woocommerce
					@module: minimum order quantity
					@type: filter
					*/
					wc_add_notice(
						/* translators: %1$s: max quantity %2$s: product name %3$s: cart link */
						apply_filters( 'b2be_qty_multiplier_error_message', sprintf( __( 'You can add a multiple of %1$s %2$s\'s to %3$s.', 'woocommerce-max-quantity' ), $multiplier, $product->get_name(), '<a href="' . esc_url( wc_get_cart_url() ) . '">' . __( 'your cart', 'woocommerce-max-quantity' ) . '</a>' ), $multiplier ),
						'error'
					);
				}
			}
			return $passed;
		}

	}
}
new B2BE_MOQ();
