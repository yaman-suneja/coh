<?php
/**
 * WC RFQ.
 *
 * @package b2b-ecommerce-for-woocomerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'B2BE_Bulk_Discount' ) ) {
	/**
	 * Class B2BE_Bulk_Discount.
	 */
	class B2BE_Bulk_Discount {

		/**
		 * Initialize Discount Rule Array.
		 *
		 * @var array $discunt_rules Discount Rule Array.
		 */
		public $b2be_discount_rules = array();

		/**
		 * Construct.
		 */
		public function __construct() {

			B2BE_Bulk_Discounts_Settings::init();

			add_action( 'woocommerce_before_add_to_cart_form', array( $this, 'b2be_apply_bulk_discounts_table' ), 100, 1 );
			add_action( 'woocommerce_before_calculate_totals', array( $this, 'b2be_apply_bulk_discounts_on_cart' ), 10 );
			add_filter( 'woocommerce_get_price_html', array( $this, 'b2be_apply_bulk_discounts' ), 100, 3 );
			add_filter( 'woocommerce_update_cart_validation', array( $this, 'bulk_update_cart_validation' ), 1, 4 );
			add_filter( 'woocommerce_widget_cart_item_quantity', array( $this, 'mini_cart_quantity' ), 10, 3 );
		}

		/**
		 * Function to update price in mini cart
		 *
		 * @param string $quantity Product Quantity html in minicartsour.
		 * @param object $product Product object.
		 */
		public function mini_cart_quantity( $quantity, $cart_item, $cart_item_key ) {

			$product = wc_get_product( $cart_item['product_id'] );

			$product_price = b2be_get_discounted_price( $product->get_price(), $product, true );
			$quantity      = '<span class="quantity">' . sprintf( '%s &times; %s', $cart_item['quantity'], wc_price( $product_price ) ) . '</span>';

			return $quantity;
		}

		/**
		 * Function to Apply discount on products.
		 *
		 * @param string $price Price Html.
		 * @param object $product Product object.
		 */
		public function b2be_apply_bulk_discounts( $price, $product, $only_sale_price = false ) {

			if ( is_admin() ) {
				return $price;
			}
			$price = b2be_get_discounted_price( $price, $product, $only_sale_price );

			return $price;
		}

		/**
		 * Function to Apply discount on products.
		 */
		public function b2be_apply_bulk_discounts_table() {

			global $product;
			$discounts = get_global_bulk_discounts( $product );

			if ( ! isset( $discounts['mode'] ) ) {
				return;
			}

			if ( 'q' == $discounts['mode'] ) {
				$discount_format = isset( $discounts['discount_format'] ) ? $discounts['discount_format'] : 'default';
				unset( $discounts['discount_format'] );
				unset( $discounts['mode'] );

								 /*
				@name: b2be_before_variation_table
				@desc: Runs before varaition table.
				@param: (array) $discounts Discounts applying on the current product according to the discount rules.
				@param: (object) $product Current Product Object.
				@package: b2b-ecommerce-for-woocommerce
				@module: discount options
				@type: action
				*/
				do_action( 'b2be_before_variation_table', $discounts, $product );

				wc_get_template(
					'bulk-discount/discount-table.php',
					array(
						'product'                 => $product,
						'product_id'              => $product->get_id(),
						'discounts'               => $discounts,
						'discount_format'         => $discount_format,
						'allowed'                 => false,
						'same_var_ids'            => array(),

						/*
						@name: b2be_varaition_table_columns
						@desc: Modify B2B variation table column name.
						@param: (array) $columns varaition table column names.
						@package: b2b-ecommerce-for-woocommerce
						@module: discount options
						@type: filter
						*/
						'variation_table_columns' => apply_filters(
							'b2be_varaition_table_columns',
							array(
								'variations' => esc_html__( 'Variations', 'b2b-ecommerce' ),
								'min'        => esc_html__( 'Min', 'b2b-ecommerce' ),
								'max'        => esc_html__( 'Max', 'b2b-ecommerce' ),
								'discount'   => ( 'per-piece' == $discount_format ) ? esc_html__( 'Per Unit Price', 'b2b-ecommerce' ) : esc_html__( 'Discount', 'b2b-ecommerce' ),
							)
						),
					),
					'b2b-ecommerce-for-woocommerce',
					CWRFQ_PLUGIN_DIR . '/templates/'
				);

								 /*
				@name: b2be_before_variation_table
				@desc: Runs before varaition table.
				@param: (array) $discounts Discounts applying on the current product according to the discount rules.
				@param: (object) $product Current Product Object.
				@package: b2b-ecommerce-for-woocommerce
				@module: discount options
				@type: action
				*/
				do_action( 'b2be_after_variation_table', $discounts, $product );
			}

		}

		/**
		 * Funtion To Recalulate Cart total after apply discount.
		 *
		 * @param object $cart_object Cart object.
		 */
		public function b2be_apply_bulk_discounts_on_cart( $cart_object ) {

			if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
				return;
			}

			foreach ( $cart_object->get_cart() as $hash => $value ) {

				$discount          = 0;
				$product           = wc_get_product( $value['product_id'] );
				$variation_product = ( isset( $value['variation_id'] ) && 0 != $value['variation_id'] ) ? wc_get_product( $value['variation_id'] ) : wc_get_product( $value['product_id'] );
				$product_quantity  = $value['quantity'];
				$discounts         = get_global_bulk_discounts( $product );
				$discount_type     = '';

				if ( isset( $discounts['mode'] ) ) {
					if ( 'q' == $discounts['mode'] ) {
						unset( $discounts['mode'] );
						unset( $discounts['discount_format'] );
						foreach ( $discounts as $key => $quantity_rule ) {
							if ( $product_quantity >= $quantity_rule['minQuantity'] && $product_quantity <= $quantity_rule['maxQuantity'] ) {
								if ( $product->is_type( 'variable' ) && $quantity_rule['variation_ids'] ) {
									if ( in_array( $value['variation_id'], $quantity_rule['variation_ids'] ) ) {
										$discount      = ! empty( $quantity_rule['discount'] ) ? $quantity_rule['discount'] : 0;
										$discount_type = $quantity_rule['type'];
										break;
									}
								} else {
									$discount      = ! empty( $quantity_rule['discount'] ) ? $quantity_rule['discount'] : 0;
									$discount_type = $quantity_rule['type'];
									break;
								}
							}
						}
					} elseif ( 'd' == $discounts['mode'] ) {
						if ( $product->is_type( 'variable' ) && $discounts['variation_ids'] ) {
							if ( in_array( $value['variation_id'], $discounts['variation_ids'] ) ) {
								$discount      = ! empty( $discounts['discount'] ) ? $discounts['discount'] : 0;
								$discount_type = $discounts['type'];
							}
						} else {
							$discount      = ! empty( $discounts['discount'] ) ? $discounts['discount'] : 0;
							$discount_type = $discounts['type'];
						}
					}

					if ( isset( $value['variation_id'] ) && 0 != $value['variation_id'] ) {
						$product_price = apply_filters( 'b2be_product_regular_price', $value['data']->get_regular_price(), $variation_product, true );
					} else {
						$product_price = apply_filters( 'b2be_product_regular_price', $value['data']->get_regular_price(), $product, true );
					}

					if ( 'fixed' == $discount_type ) {
						$newprice = $product_price - $discount;
					} else {
						$newprice = $product_price - ( $product_price * ( $discount / 100 ) );
					}

					if ( $newprice <= 0 ) {
						$newprice = 0.00;
					}

					if ( 0 != $discount ) {
						$value['data']->set_price( $newprice );
					}
				}
			}

		}

		/**
		 * Validate on update cart.
		 *
		 * @param bool   $passed Is allowed to passed.
		 * @param int    $cart_item_key Cart item key.
		 * @param object $value Cart object.
		 * @param int    $product_quantity Product Quantity.
		 */
		public function bulk_update_cart_validation( $passed, $cart_item_key, $value, $product_quantity ) {

			$variation_product = ( isset( $value['variation_id'] ) && 0 != $value['variation_id'] ) ? wc_get_product( $value['variation_id'] ) : wc_get_product( $value['product_id'] );
			$product           = wc_get_product( $value['product_id'] );
			$discounts         = get_global_bulk_discounts( $product );
			$discount_type     = '';
			$discount          = 0;

			if ( ! isset( $discounts['mode'] ) ) {
				return true;
			}

			if ( $discounts ) {
				if ( 'q' == $discounts['mode'] ) {
					unset( $discounts['mode'] );
					unset( $discounts['discount_format'] );
					foreach ( $discounts as $key => $quantity_rule ) {

						if ( $product_quantity >= $quantity_rule['minQuantity'] && $product_quantity <= $quantity_rule['maxQuantity'] ) {
							if ( $product->is_type( 'variable' ) && $quantity_rule['variation_ids'] ) {
								if ( in_array( $value['variation_id'], $quantity_rule['variation_ids'] ) ) {
									$discount      = ! empty( $quantity_rule['discount'] ) ? $quantity_rule['discount'] : 0;
									$discount_type = $quantity_rule['type'];
									break;
								}
							} else {
								$discount      = ! empty( $quantity_rule['discount'] ) ? $quantity_rule['discount'] : 0;
								$discount_type = $quantity_rule['type'];
								break;
							}
						}
					}
				} elseif ( 'd' == $discounts['mode'] ) {

					if ( $product->is_type( 'variable' ) && $discounts['variation_ids'] ) {
						if ( in_array( $value['variation_id'], $discounts['variation_ids'] ) ) {
							$discount      = ! empty( $discounts['discount'] ) ? $discounts['discount'] : 0;
							$discount_type = $discounts['type'];
						}
					} else {
						$discount      = ! empty( $discounts['discount'] ) ? $discounts['discount'] : 0;
						$discount_type = $discounts['type'];
					}
				}

				if ( isset( $value['variation_id'] ) && 0 != $value['variation_id'] ) {
					$product_price = apply_filters( 'b2be_product_regular_price', $value['data']->get_regular_price(), $variation_product, true );
				} else {
					$product_price = apply_filters( 'b2be_product_regular_price', $value['data']->get_regular_price(), $product, true );
				}

				if ( 'fixed' == $discount_type ) {
					$newprice = $product_price - $discount;
				} else {
					$newprice = $product_price - ( $product_price * ( $discount / 100 ) );
				}

				if ( $newprice < 0 ) {
					$newprice = 0.00;
				}

				if ( 0 != $discount ) {
					$value['data']->set_price( $newprice );
				}
			}
			return $passed;
		}

		/**
		 * Get Given Product cart quantity.
		 *
		 * @param int $value['product_id'] Product Id.
		 */
		public function wc_qty_get_cart_qty( $product_id ) {
			global $woocommerce;
			$running_qty = 0; // iniializing quantity to 0...

			// search the cart for the product in and calculate quantity.
			foreach ( $woocommerce->cart->get_cart() as $other_cart_item_keys => $values ) {
				if ( $product_id == $values['product_id'] ) {
					$running_qty += (int) $values['quantity'];
				}
			}
			return $running_qty;
		}

	}
}
new B2BE_Bulk_Discount();
