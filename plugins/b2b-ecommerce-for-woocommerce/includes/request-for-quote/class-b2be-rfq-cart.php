<?php
/**
 * WC RFQ Cart.
 *
 * @package b2b-ecommerce-for-woocomerce
 */

defined( 'ABSPATH' ) || exit;

/**
 * RFQ cart class.
 */
class B2BE_RFQ_Cart extends WC_Cart {
	/**
	 * Function construct.
	 */
	public function __construct() {
		$this->fees_api         = new WC_Cart_Fees( $this );
		$this->tax_display_cart = $this->is_tax_displayed();
		add_action( 'woocommerce_cart_calculate_fees', array( $this, 'prefix_add_discount_line' ), 10, 1 );
	}

	/**
	 * Returns the contents of the cart in an array.
	 *
	 * @return array contents of the cart
	 */
	public function get_cart() {
		if ( ! did_action( 'wp_loaded' ) ) {
			wc_doing_it_wrong( __FUNCTION__, __( 'Get cart should not be called before the wp_loaded action.', 'b2b-ecommerce' ), '2.3' );
		}
		if ( ! did_action( 'woocommerce_load_cart_from_session' ) ) {
			$this->session->get_cart_from_session();
		}
		return array_filter( $this->get_cart_contents() );
	}

	/**
	 * Add a product to the cart.
	 *
	 * @throws Exception Plugins can throw an exception to prevent adding to cart.
	 * @param int   $product_id contains the id of the product to add to the cart.
	 * @param int   $quantity contains the quantity of the item to add.
	 * @param int   $variation_id ID of the variation being added to the cart.
	 * @param array $variation attribute values.
	 * @param array $cart_item_data extra cart item data we want to pass into the item.
	 * @return string|bool $cart_item_key
	 */
	public function add_to_cart( $product_id = 0, $quantity = 1, $variation_id = 0, $variation = array(), $cart_item_data = array() ) {

		try {
			$product_id   = absint( $product_id );
			$variation_id = absint( $variation_id );

			// Ensure we don't add a variation to the cart directly by variation ID.
			if ( 'product_variation' === get_post_type( $product_id ) ) {
				$variation_id = $product_id;
				$product_id   = wp_get_post_parent_id( $variation_id );
			}

			$product_data = wc_get_product( $variation_id ? $variation_id : $product_id );

			/*
			@name: b2be_add_to_cart_quantity
			@desc: Modify the quantity of product while adding to rfq cart.
			@param: (int) $quantity Quantity of product being added in rfq cart.
			@param: (int) $product_id Product Id of product being added in rfq cart.
			@package: b2b-ecommerce-for-woocommerce
			@module: request for quote
			@type: filter
			*/
			$quantity = apply_filters( 'b2be_add_to_cart_quantity', $quantity, $product_id );
			if ( $quantity <= 0 || ! $product_data || 'trash' === $product_data->get_status() ) {
				return false;
			}

			/*
			@name: b2be_add_rfq_item_data
			@desc: Modify the rfq cart data.
			@param: (int) $cart_item_data Quantity of product being added in rfq cart.
			@param: (int) $product_id Product Id of product being added in rfq cart.
			@param: (int) $variation_id Variation Id of product being added in rfq cart.
			@param: (int) $quantity Quantity of product being added in rfq cart.
			@package: b2b-ecommerce-for-woocommerce
			@module: request for quote
			@type: filter
			*/
			$cart_item_data = (array) apply_filters( 'b2be_add_rfq_item_data', $cart_item_data, $product_id, $variation_id, $quantity );

			// Generate a ID based on product ID, variation ID, variation data, and other cart item data.
			$cart_id = $this->generate_cart_id( $product_id, $variation_id, $variation, $cart_item_data );

			// Find the cart item key in the existing cart.
			$cart_item_key = $this->find_product_in_cart( $cart_id );

			// Force quantity to 1 if sold individually and check for existing item in cart.
			if ( $product_data->is_sold_individually() ) {
				$quantity      = apply_filters( 'b2be_add_to_rfq_sold_individually_quantity', 1, $quantity, $product_id, $variation_id, $cart_item_data );
				$found_in_cart = apply_filters( 'b2be_add_to_rfq_sold_individually_found_in_cart', $cart_item_key && $this->cart_contents[ $cart_item_key ]['quantity'] > 0, $product_id, $variation_id, $cart_item_data, $cart_id );

				if ( $found_in_cart ) {
					/* translators: %s: product name */
					throw new Exception( sprintf( '<a href="%s" class="button wc-forward">%s</a> %s', wc_get_cart_url(), __( 'View cart', 'b2b-ecommerce' ), sprintf( __( 'You cannot add another "%s" to your cart.', 'b2b-ecommerce' ), $product_data->get_name() ) ) );
				}
			}

			if ( ! $product_data->is_purchasable() ) {
				/* translators: %s: product name */
				throw new Exception( sprintf( __( 'Sorry, &quot;%s&quot; product cannot be purchased.', 'b2b-ecommerce' ), $product_data->get_name() ) );
			}

			// Stock check - only check if we're managing stock and backorders are not allowed.
			if ( ! $product_data->is_in_stock() ) {
				/* translators: %s: product name */
				throw new Exception( sprintf( __( 'You cannot add &quot;%s&quot; to the cart because the product is out of stock.', 'b2b-ecommerce' ), $product_data->get_name() ) );
			}

			if ( ! $product_data->has_enough_stock( $quantity ) ) {
				/* translators: 1: product name 2: quantity in stock */
				throw new Exception( sprintf( __( 'You cannot add that amount of &quot;%1$s&quot; to the cart because there is not enough stock (%2$s remaining).', 'b2b-ecommerce' ), $product_data->get_name(), wc_format_stock_quantity_for_display( $product_data->get_stock_quantity(), $product_data ) ) );
			}

			// Stock check - this time accounting for whats already in-cart.
			if ( $product_data->managing_stock() ) {
				$products_qty_in_cart = $this->get_cart_item_quantities();

				if ( isset( $products_qty_in_cart[ $product_data->get_stock_managed_by_id() ] ) && ! $product_data->has_enough_stock( $products_qty_in_cart[ $product_data->get_stock_managed_by_id() ] + $quantity ) ) {
					throw new Exception(
						sprintf(
							'<a href="%s" class="button wc-forward">%s</a> %s',
							wc_get_cart_url(),
							__( 'View cart', 'b2b-ecommerce' ),
							/* translators: 1: quantity in stock 2: current quantity */ sprintf( __( 'You cannot add that amount to the cart &mdash; we have %1$s in stock and you already have %2$s in your cart.', 'b2b-ecommerce' ), wc_format_stock_quantity_for_display( $product_data->get_stock_quantity(), $product_data ), wc_format_stock_quantity_for_display( $products_qty_in_cart[ $product_data->get_stock_managed_by_id() ], $product_data ) )
						)
					);
				}
			}
			// If cart_item_key is set, the item is already in the cart.

			if ( $cart_item_key ) {
				$new_quantity = $quantity + $this->cart_contents[ $cart_item_key ]['quantity'];
				$this->set_quantity( $cart_item_key, $new_quantity, false );
			} else {
				$cart_item_key = $cart_id;

				// Add item after merging with $cart_item_data - hook to allow plugins to modify cart item.
				$this->cart_contents[ $cart_item_key ] = apply_filters(
					'woocommerce_add_cart_item',
					array_merge(
						$cart_item_data,
						array(
							'key'          => $cart_item_key,
							'product_id'   => $product_id,
							'variation_id' => $variation_id,
							'variation'    => $variation,
							'quantity'     => $quantity,
							'data'         => $product_data,
							'data_hash'    => wc_get_cart_item_data_hash( $product_data ),
						)
					),
					$cart_item_key
				);

			}
			$this->cart_contents = apply_filters( 'b2be_rfq_contents_changed', $this->cart_contents );

			if ( ! WC()->session->has_session() ) {
				WC()->session->set_customer_session_cookie( true );
			}
			WC()->session->set( 'rfq', $this->get_cart_for_session() );

			do_action( 'woocommerce_add_to_cart', $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data );

			return $cart_item_key;
		} catch ( Exception $e ) {
			if ( $e->getMessage() ) {
				wc_add_notice( $e->getMessage(), 'error' );
			}
			return false;
		}
	}
	/**
	 * Function Get Cart Session.
	 */
	public function get_cart_for_session() {

		$cart_session = array();

		foreach ( $this->get_cart() as $key => $values ) {
			$cart_session[ $key ] = $values;
			unset( $cart_session[ $key ]['data'] ); // Unset product object.
		}
		return $cart_session;
	}
	/**
	 * Function Find Product In Cart.
	 *
	 * @param bool $cart_id Cart Id.
	 */
	public function find_product_in_cart( $cart_id = false ) {

		if ( false !== $cart_id ) {

			if ( is_array( $this->cart_contents ) && isset( $this->cart_contents[ $cart_id ] ) ) {
				return $cart_id;
			}
		}
		return '';
	}
	/**
	 * Set Session.
	 */
	public function set_session() {
		WC()->session->set( 'rfq', $this->get_cart_for_session() );
	}

	/**
	 * Returns 'incl' if tax should be included in cart, otherwise returns 'excl'.
	 *
	 * @return string
	 */
	private function is_tax_displayed() {
		if ( $this->get_customer() && $this->get_customer()->get_is_vat_exempt() ) {
			return 'excl';
		}

		return get_option( 'woocommerce_tax_display_cart' );
	}

	/**
	 * Calculate totals for the items in the cart.
	 *
	 * @uses WC_Cart_Totals
	 */
	public function calculate_totals() {
		$this->reset_totals();

		if ( $this->is_empty() ) {
			$this->set_session();
			return;
		}

		do_action( 'woocommerce_before_calculate_totals', $this );

		do_action( 'woocommerce_after_calculate_totals', $this );
	}

	/**
	 * Reset cart totals to the defaults. Useful before running calculations.
	 */
	private function reset_totals() {
		$this->totals = $this->default_totals;
		$this->fees_api->remove_all_fees();
		do_action( 'woocommerce_cart_reset', $this, false );
	}

	/**
	 * Empties the cart and optionally the persistent cart too.
	 *
	 * @param bool $clear_persistent_cart Should the persistant cart be cleared too. Defaults to true.
	 */
	public function empty_cart( $clear_persistent_cart = true ) {

		// do_action( 'woocommerce_before_cart_emptied' );.

		$this->cart_contents              = array();
		$this->removed_cart_contents      = array();
		$this->shipping_methods           = array();
		$this->coupon_discount_totals     = array();
		$this->coupon_discount_tax_totals = array();
		$this->applied_coupons            = array();
		$this->totals                     = $this->default_totals;

	}

	/**
	 * Empties the cart and optionally the persistent cart too.
	 *
	 * @param array $cart_object Get cart object.
	 */
	public function prefix_add_discount_line( $cart_object ) {

		$quote = '';

		foreach ( $cart_object->cart_contents as $cart_item_key => $value ) {
			if ( array_key_exists( 'quote', $value ) ) {

				$quote = wc_get_quote( $value['quote'] );

			}
		}
		if ( $quote ) {

			$current_cart_total  = $cart_object->totals['cart_contents_total'];
			$current_cart_total -= $quote->get_formatted_quote_total();
			$cart_object->add_fee( 'Quoted Discount', -$current_cart_total );

		}

	}

}
