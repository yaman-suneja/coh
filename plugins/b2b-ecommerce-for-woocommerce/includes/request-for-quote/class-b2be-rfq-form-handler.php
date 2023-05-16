<?php
/**
 * Handle frontend forms.
 *
 * @package WooCommerce/Classes/
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Form_Handler class.
 */
class B2BE_RFQ_Form_Handler {

	/**
	 * Hook in methods.
	 */
	public static function init() {
		add_action( 'wp_loaded', array( __CLASS__, 'add_to_rfq_action' ), 12 );
		add_action( 'wp_loaded', array( __CLASS__, 'update_cart_action' ), 12 );
		add_action( 'wp_loaded', array( __CLASS__, 'submit_rfq' ), 12 );
		add_action( 'wp_loaded', array( __CLASS__, 'quote_need_revision' ), 12 );
		add_action( 'cwcrfq_quote_marked_accepted', array( __CLASS__, 'populate_cart' ), 11, 2 );
		add_action( 'woocommerce_checkout_create_order_line_item', array( __CLASS__, 'add_quote_to_item' ), 10, 3 );

	}

	/**
	 * Add to rfq action.
	 *
	 * Checks for a valid request, does validation (via hooks) and then redirects if valid.
	 *
	 * @param bool  $url (default: false) URL to redirect to.
	 * @param array $ajax_param Ajax parameter array.
	 */
	public static function add_to_rfq_action( $url = false, $ajax_param = array() ) {
		if ( ! empty( $ajax_param ) ) {
			$_REQUEST['add-to-rfq'] = $ajax_param['add_to_rfq'];
			$_REQUEST['quantity']   = $ajax_param['quantity'];
		}

		if ( ! isset( $_REQUEST['add-to-rfq'] ) || ! is_numeric( sanitize_text_field( wp_unslash( $_REQUEST['add-to-rfq'] ) ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			return;
		}

		wc_nocache_headers();

		$product_id = apply_filters( 'b2be_add_to_rfq_product_id', absint( wp_unslash( $_REQUEST['add-to-rfq'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification

		$was_added_to_cart = false;
		$adding_to_cart    = wc_get_product( $product_id );

		if ( ! $adding_to_cart ) {
			return;
		}

		if ( defined( 'DOING_AJAX' ) ) {
			self::load_cart_from_session();
		}

		$add_to_cart_handler = apply_filters( 'b2be_add_to_rfq_handler', $adding_to_cart->get_type(), $adding_to_cart );

		if ( 'variable' === $add_to_cart_handler || 'variation' === $add_to_cart_handler ) {
			$was_added_to_cart = self::add_to_cart_handler_variable( $product_id );
			unset( $_REQUEST['add-to-cart'] );
		} elseif ( 'grouped' === $add_to_cart_handler ) {
			$was_added_to_cart = self::add_to_cart_handler_grouped( $product_id );
			unset( $_REQUEST['add-to-cart'] );
		} elseif ( has_action( 'b2be_add_to_rfq_handler_' . $add_to_cart_handler ) ) {
			do_action( 'b2be_add_to_rfq_handler_' . $add_to_cart_handler, $url ); // Custom handler.
		} else {
			if ( isset( $ajax_param['quantity'] ) ) {
				if ( isset( $_REQUEST['quantity'] ) ) {
					$was_added_to_cart = self::add_to_cart_handler_simple( $product_id, sanitize_text_field( wp_unslash( $_REQUEST['quantity'] ) ) );

					if ( isset( $ajax_param['multi'] ) && true == $ajax_param['multi'] ) {
						return;
					}
				}
			} else {
				if ( isset( $_REQUEST['qty'] ) ) {
					$was_added_to_cart = self::add_to_cart_handler_simple( $product_id, sanitize_text_field( wp_unslash( $_REQUEST['qty'] ) ) );
				} else {
					$was_added_to_cart = self::add_to_cart_handler_simple( $product_id );
				}
			}
		}

		$arr_params = array( 'add-to-rfq', 'quantity' );
		$url        = esc_url( remove_query_arg( $arr_params ) );

		// If we added the product to the cart we can now optionally do a redirect.
		if ( $was_added_to_cart && 0 == wc_notice_count( 'error' ) ) {
			$url = apply_filters( 'b2be_add_to_cart_redirect', $url, $adding_to_cart );

			if ( $url ) {
				wp_safe_redirect( $url );
				exit;
			} elseif ( 'yes' === get_option( 'woocommerce_cart_redirect_after_add' ) ) {
				wp_safe_redirect( wc_get_cart_url() );
				exit;
			}
		}

		if ( ! empty( $ajax_param ) ) {

			if ( isset( $ajax_param['multi'] ) && true == $ajax_param['multi'] ) {
				return;
			}

			$response['name']     = $adding_to_cart->get_name();
			$response['rfq_page'] = get_site_url();
			wp_die( json_encode( $response ) );

		}

	}

	/**
	 * Handle adding simple products to the cart.
	 *
	 * @since 2.4.6 Split from add_to_cart_action.
	 * @param int $product_id Product ID to add to the cart.
	 * @param int $ajax_quantity Product Quantity to add to the cart.
	 * @return bool success or not
	 */
	private static function add_to_cart_handler_simple( $product_id, $ajax_quantity = 0 ) {
		$quantity = empty( $_REQUEST['quantity'] ) ? 1 : wc_stock_amount( sanitize_text_field( wp_unslash( $_REQUEST['quantity'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification
		if ( $ajax_quantity > 0 ) {
			$quantity = $ajax_quantity;
		}

		$product = wc_get_product( $product_id );
		if ( $product->is_type( 'simple' ) ) {
			$b2be_moq_rules = b2be_get_moq_limit( $product );
			if ( isset( $b2be_moq_rules[0]['minQuantity'] ) && empty( $_REQUEST['quantity'] ) ) {
				$quantity = $b2be_moq_rules[0]['minQuantity'];
			}
		}

		/*
		@name: b2be_add_to_rfq_validation
		@desc: Modify the rfq cart data.
		@param: (int) $check Check if its validated or not.
		@param: (int) $product_id Product Id of product being added in rfq cart.
		@param: (int) $quantity Quantity of product being added in rfq cart.
		@param: (int) $variation_id Variation Id of product being added in rfq cart..
		@param: (array) $variation Variation of product being added in rfq cart..
		@package: b2b-ecommerce-for-woocommerce
		@module: request for quote
		@type: filter
		*/
		$passed_validation = apply_filters( 'b2be_add_to_rfq_validation', true, $product_id, $quantity, '', '', true );
		if ( $passed_validation ) {
			if ( false != WC()->rfq->add_to_cart( $product_id, $quantity ) ) {
				wc_add_to_rfq_message( array( $product_id => $quantity ), true );
				return true;
			}
		}
		return false;
	}

	/**
	 * Handle adding variable products to the cart.
	 *
	 * @since 2.4.6 Split from add_to_cart_action.
	 * @throws Exception If add to cart fails.
	 * @param int $product_id Product ID to add to the cart.
	 * @return bool success or not
	 */
	private static function add_to_cart_handler_variable( $product_id ) {
		try {
			$variation_id       = empty( $_REQUEST['variation_id'] ) ? '' : absint( wp_unslash( $_REQUEST['variation_id'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification
			$quantity           = empty( $_REQUEST['quantity'] ) ? 1 : wc_stock_amount( sanitize_text_field( wp_unslash( $_REQUEST['quantity'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification
			$missing_attributes = array();
			$variations         = array();
			$adding_to_cart     = wc_get_product( $product_id );

			if ( ! $adding_to_cart ) {
				return false;
			}

			// If the $product_id was in fact a variation ID, update the variables.
			if ( $adding_to_cart->is_type( 'variation' ) ) {
				$variation_id   = $product_id;
				$product_id     = $adding_to_cart->get_parent_id();
				$adding_to_cart = wc_get_product( $product_id );

				if ( ! $adding_to_cart ) {
					return false;
				}
			}

			// Gather posted attributes.
			$posted_attributes = array();

			foreach ( $adding_to_cart->get_attributes() as $attribute ) {
				if ( ! $attribute['is_variation'] ) {
					continue;
				}
				$attribute_key = 'attribute_' . sanitize_title( $attribute['name'] );

				if ( isset( $_REQUEST[ $attribute_key ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification
					if ( $attribute['is_taxonomy'] ) {
						// Don't use wc_clean as it destroys sanitized characters.
						$value = sanitize_title( wp_unslash( $_REQUEST[ $attribute_key ] ) ); // phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification
					} else {
						$value = html_entity_decode( wc_clean( sanitize_text_field( wp_unslash( $_REQUEST[ $attribute_key ] ) ) ), ENT_QUOTES, get_bloginfo( 'charset' ) ); // phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification
					}

					$posted_attributes[ $attribute_key ] = $value;
				}
			}

			// If no variation ID is set, attempt to get a variation ID from posted attributes.
			if ( empty( $variation_id ) ) {
				$data_store   = WC_Data_Store::load( 'product' );
				$variation_id = $data_store->find_matching_product_variation( $adding_to_cart, $posted_attributes );
			}

			// Do we have a variation ID?
			if ( empty( $variation_id ) ) {
				throw new Exception( __( 'Please choose product options&hellip;', 'b2b-ecommerce' ) );
			}

			// Check the data we have is valid.
			$variation_data = wc_get_product_variation_attributes( $variation_id );

			foreach ( $adding_to_cart->get_attributes() as $attribute ) {
				if ( ! $attribute['is_variation'] ) {
					continue;
				}

				// Get valid value from variation data.
				$attribute_key = 'attribute_' . sanitize_title( $attribute['name'] );
				$valid_value   = isset( $variation_data[ $attribute_key ] ) ? $variation_data[ $attribute_key ] : '';

				/**
				 * If the attribute value was posted, check if it's valid.
				 *
				 * If no attribute was posted, only error if the variation has an 'any' attribute which requires a value.
				 */
				if ( isset( $posted_attributes[ $attribute_key ] ) ) {
					$value = $posted_attributes[ $attribute_key ];

					// Allow if valid or show error.
					if ( $valid_value === $value ) {
						$variations[ $attribute_key ] = $value;
					} elseif ( '' === $valid_value && in_array( $value, $attribute->get_slugs(), true ) ) {
						// If valid values are empty, this is an 'any' variation so get all possible values.
						$variations[ $attribute_key ] = $value;
					} else {
						/* translators: %s: Attribute name. */
						throw new Exception( sprintf( __( 'Invalid value posted for %s', 'b2b-ecommerce' ), wc_attribute_label( $attribute['name'] ) ) );
					}
				} elseif ( '' === $valid_value ) {
					$missing_attributes[] = wc_attribute_label( $attribute['name'] );
				}
			}
			if ( ! empty( $missing_attributes ) ) {
				/* translators: %s: Attribute name. */
				throw new Exception( sprintf( _n( '%s is a required field', '%s are required fields', count( $missing_attributes ), 'b2b-ecommerce' ), wc_format_list_of_items( $missing_attributes ) ) );
			}
		} catch ( Exception $e ) {
			wc_add_notice( $e->getMessage(), 'error' );
			return false;
		}

		$passed_validation = apply_filters( 'b2be_add_to_rfq_validation', true, $product_id, $quantity, $variation_id, $variations );

		if ( $passed_validation && false !== WC()->rfq->add_to_cart( $product_id, $quantity, $variation_id, $variations ) ) {
			wc_add_to_rfq_message( array( $product_id => $quantity ), true );
			return true;
		}

		return false;
	}

	/**
	 * Handle adding grouped products to the cart.
	 *
	 * @since 2.4.6 Split from add_to_cart_action.
	 * @param int $product_id Product ID to add to the cart.
	 * @return bool success or not
	 */
	private static function add_to_cart_handler_grouped( $product_id ) {
		$was_added_to_cart = false;
		$added_to_cart     = array();
		$items             = isset( $_REQUEST['quantity'] ) && is_array( $_REQUEST['quantity'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['quantity'] ) ) : array(); // phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		if ( ! empty( $items ) ) {
			$quantity_set = false;

			foreach ( $items as $item => $quantity ) {
				if ( $quantity <= 0 ) {
					continue;
				}
				$quantity_set = true;

				// Add to cart validation.
				$passed_validation = apply_filters( 'b2be_add_to_rfq_validation', true, $item, $quantity );

				// Suppress total recalculation until finished.
				remove_action( 'woocommerce_add_to_cart', array( WC()->rfq, 'calculate_totals' ), 20, 0 );

				if ( $passed_validation && false !== WC()->rfq->add_to_cart( $item, $quantity ) ) {
					$was_added_to_cart      = true;
					$added_to_cart[ $item ] = $quantity;
				}

				add_action( 'woocommerce_add_to_cart', array( WC()->rfq, 'calculate_totals' ), 20, 0 );
			}

			if ( ! $was_added_to_cart && ! $quantity_set ) {
				wc_add_notice( __( 'Please choose the quantity of items you wish to add to your cart&hellip;', 'b2b-ecommerce' ), 'error' );
			} elseif ( $was_added_to_cart ) {
				wc_add_to_rfq_message( $added_to_cart );
				WC()->rfq->calculate_totals();
				return true;
			}
		} elseif ( $product_id ) {
			/* Link on product archives */
			wc_add_notice( __( 'Please choose a product to add to your cart&hellip;', 'b2b-ecommerce' ), 'error' );
		}
		return false;
	}

	/**
	 * Remove from cart/update.
	 */
	public static function update_cart_action() {
		if ( ! ( isset( $_REQUEST['apply_coupon'] ) || isset( $_REQUEST['remove_coupon'] ) || isset( $_REQUEST['remove_rfq_item'] ) || isset( $_REQUEST['undo_item'] ) || isset( $_REQUEST['update_rfq_cart'] ) || isset( $_REQUEST['proceed'] ) ) ) {
			return;
		}

		wc_nocache_headers();

		$nonce_value = ( isset( $_REQUEST['woocommerce-cart-nonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['woocommerce-cart-nonce'] ) ) : filter_input( INPUT_GET, '_wpnonce' ) ); // @codingStandardsIgnoreLine.

		if ( ! empty( $_POST['apply_coupon'] ) && ! empty( $_POST['coupon_code'] ) ) {
			WC()->cart->add_discount( wc_format_coupon_code( sanitize_text_field( wp_unslash( $_POST['coupon_code'] ) ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		} elseif ( isset( $_GET['remove_coupon'] ) ) {
			WC()->cart->remove_coupon( wc_format_coupon_code( urldecode( sanitize_text_field( wp_unslash( $_GET['remove_coupon'] ) ) ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		} elseif ( ! empty( $_GET['remove_rfq_item'] ) && wp_verify_nonce( $nonce_value, 'woocommerce-cart' ) ) {
			$cart_item_key = sanitize_text_field( wp_unslash( $_GET['remove_rfq_item'] ) );
			$cart_item     = WC()->rfq->get_cart_item( $cart_item_key );

			if ( $cart_item ) {
				WC()->rfq->remove_cart_item( $cart_item_key );
				WC()->rfq->set_session();
				$product = wc_get_product( $cart_item['product_id'] );

				/* translators: %s: Item name. */
				$item_removed_title = apply_filters( 'b2be_cart_item_removed_title', $product ? sprintf( _x( '&ldquo;%s&rdquo;', 'Item name in quotes', 'b2b-ecommerce' ), $product->get_name() ) : __( 'Item', 'b2b-ecommerce' ), $cart_item );
				// Don't show undo link if removed item is out of stock.
				if ( $product && $product->is_in_stock() && $product->has_enough_stock( $cart_item['quantity'] ) ) {
					/* Translators: %s Product title. */
					$removed_notice = sprintf( __( '%s removed.', 'b2b-ecommerce' ), $item_removed_title );
					// $removed_notice .= ' <a href="' . esc_url( wc_get_cart_undo_url( $cart_item_key ) ) . '" class="restore-item">' . __( 'Undo?', 'b2b-ecommerce' ) . '</a>';
				} else {
					/* Translators: %s Product title. */
					$removed_notice = sprintf( __( '%s removed.', 'b2b-ecommerce' ), $item_removed_title );
				}

				wc_add_notice( $removed_notice, apply_filters( 'b2be_cart_item_removed_notice_type', 'success' ) );
			}

			$referer = wp_get_referer() ? remove_query_arg( array( 'remove_rfq_item', 'add-to-cart', 'added-to-cart', 'order_again', '_wpnonce' ), add_query_arg( 'removed_item', '1', wp_get_referer() ) ) : wc_get_cart_url();
			wp_safe_redirect( $referer );
			exit;
		} elseif ( ! empty( $_GET['undo_item'] ) && isset( $_GET['_wpnonce'] ) && wp_verify_nonce( $nonce_value, 'woocommerce-cart' ) ) {

			// Undo Cart Item.
			$cart_item_key = sanitize_text_field( wp_unslash( $_GET['undo_item'] ) );

			WC()->cart->restore_cart_item( $cart_item_key );

			$referer = wp_get_referer() ? remove_query_arg( array( 'undo_item', '_wpnonce' ), wp_get_referer() ) : wc_get_cart_url();
			wp_safe_redirect( $referer );
			exit;
		}

		// Update Cart - checks apply_coupon too because they are in the same form.
		if ( ( ! empty( $_POST['apply_coupon'] ) || ! empty( $_POST['update_rfq_cart'] ) || ! empty( $_POST['proceed'] ) ) && wp_verify_nonce( $nonce_value, 'woocommerce-cart' ) ) {
			$cart_updated = false;
			$cart_totals  = isset( $_POST['cart'] ) ? filter_input( INPUT_POST, 'cart', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY ) : ''; // PHPCS: input var ok, CSRF ok, sanitization ok.

			if ( ! WC()->rfq->is_empty() && is_array( $cart_totals ) ) {
				foreach ( WC()->rfq->get_cart() as $cart_item_key => $values ) {

					$_product = $values['data'];

					// Skip product if no updated quantity was posted.
					if ( ! isset( $cart_totals[ $cart_item_key ] ) || ! isset( $cart_totals[ $cart_item_key ]['qty'] ) ) {
						continue;
					}

					// Sanitize.
					$quantity = apply_filters( 'b2be_stock_amount_cart_item', wc_stock_amount( preg_replace( '/[^0-9\.]/', '', $cart_totals[ $cart_item_key ]['qty'] ) ), $cart_item_key );

					if ( '' === $quantity || $quantity === $values['quantity'] ) {
						continue;
					}

					// Update cart validation.
					$passed_validation = apply_filters( 'woocommerce_update_cart_validation', true, $cart_item_key, $values, $quantity );

					// is_sold_individually.
					if ( $_product->is_sold_individually() && $quantity > 1 ) {
						/* Translators: %s Product title. */
						wc_add_notice( sprintf( __( 'You can only have 1 %s in your cart.', 'b2b-ecommerce' ), $_product->get_name() ), 'error' );
						$passed_validation = false;
					}

					if ( $passed_validation ) {
						WC()->rfq->set_quantity( $cart_item_key, $quantity, false );
						$cart_updated = true;
					}
				}
			}

			// Trigger action - let 3rd parties update the cart if they need to and update the $cart_updated variable.
			$cart_updated = apply_filters( 'b2be_update_cart_action_cart_updated', $cart_updated );

			if ( $cart_updated ) {
				WC()->rfq->set_session();
			}

			if ( ! empty( $_POST['proceed'] ) ) {
				wp_safe_redirect( wc_get_checkout_url() );
				exit;
			} elseif ( $cart_updated ) {
				wc_add_notice( __( 'RFQ updated.', 'b2b-ecommerce' ), apply_filters( 'b2be_cart_updated_notice_type', 'success' ) );
				$cart_page_url = wc_get_page_permalink( 'rfq' );
				$referer       = remove_query_arg( array( 'remove_coupon', 'add-to-cart' ), ( wp_get_referer() ? wp_get_referer() : $cart_page_url ) );
				wp_safe_redirect( b2be_get_rfq_cart_url() );
				exit;
			}
		}
	}

	/**
	 * Submit RFQ
	 */
	public static function submit_rfq() {
		if ( isset( $_POST['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ) ) ) {
			if ( empty( ( sanitize_text_field( wp_unslash( $_POST['submit_rfq'] ) ) ) ) ) {
				return;
			}

			if ( '' != ( ( sanitize_text_field( wp_unslash( $_POST['submit_rfq'] ) ) ) ) ) {
				self::create();
			}
		}
	}
	/**
	 * Create Function
	 */
	public static function create() {

		if ( isset( $_POST['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ) ) ) {
			$customer_first_name = ( isset( $_POST['rfq_first_name'] ) ) ? ( sanitize_text_field( wp_unslash( $_POST['rfq_first_name'] ) ) ) : '';

			$quote_data = array(
				/* translators: %s post title */
				'post_title'  => sprintf( __( 'Quote from %s', 'codup-wcrfq' ), $customer_first_name ),
				'post_status' => 'requested',
				'post_type'   => 'quote',
				'post_author' => 1,
				'meta_input'  => array(
					'first_name' => $customer_first_name,
					'last_name'  => ( isset( $_POST['rfq_last_name'] ) ) ? ( sanitize_text_field( wp_unslash( $_POST['rfq_last_name'] ) ) ) : '',
					'email'      => ( isset( $_POST['rfq_email'] ) ) ? ( sanitize_text_field( wp_unslash( $_POST['rfq_email'] ) ) ) : '',
					'message'    => ( isset( $_POST['rfq_message'] ) ) ? ( sanitize_text_field( wp_unslash( $_POST['rfq_message'] ) ) ) : '',
				),
			);
		}

		do_action( 'b2be_rfq_quote_data_before_submitting', $quote_data );

		$quote = wp_insert_post( $quote_data );

		do_action( 'b2be_rfq_quote_data_after_submitting', $quote_data, $quote );

		if ( ! is_wp_error( $quote ) ) {
			$post_name = sprintf( '#%s %s %s', $quote, $customer_first_name, ( sanitize_text_field( wp_unslash( $_POST['rfq_last_name'] ) ) ) );
			wp_update_post(
				array(
					'ID'         => $quote,
					'post_title' => $post_name,
				)
			);

			self::save_quote_meta( $quote );
			if ( ! is_user_logged_in() ) {
				$first_name = ( sanitize_text_field( wp_unslash( $_POST['rfq_first_name'] ) ) );
				$last_name  = ( sanitize_text_field( wp_unslash( $_POST['rfq_last_name'] ) ) );

				$email = ( sanitize_text_field( wp_unslash( $_POST['rfq_email'] ) ) );

				$characters      = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
				$random_password = '';
				$random_id       = '';

				for ( $i = 0; $i < 32; $i++ ) {
					$index            = rand( 0, strlen( $characters ) - 1 );
					$random_password .= $characters[ $index ];
				}

				for ( $i = 0; $i < 3; $i++ ) {
					$index      = rand( 0, strlen( $characters ) - 1 );
					$random_id .= $characters[ $index ];
				}

				$username = strtolower( $first_name ) . '_' . strtolower( $last_name ) . $random_id;

				$userdata = array(
					'user_pass'  => $random_password,
					'user_login' => $username,
					'user_email' => $email,
					'first_name' => $first_name,
					'last_name'  => $last_name,
					'role'       => 'customer',

				);
				$new_user = wp_insert_user( $userdata );

				update_post_meta( $quote, '_customer_user', $new_user );
				wp_new_user_notification( $new_user, null, 'user' );
			}

			$quote = wc_get_quote( $quote );
			do_action( 'wcrfq_rfq_created', $quote );

			WC()->session->set( 'rfq', null );

			$redirect_url = get_permalink( get_page_by_path( 'thank-you' ) );

		} else {
			$redirect_url = wc_get_endpoint_url( CWRFQ_QUOTE_ENDPOINT, '', wc_get_page_permalink( 'myaccount' ) );
			wc_add_notice( 'MEh.', 'error' );
		}
		wp_safe_redirect( $redirect_url );
		exit;
	}
	/**
	 * Save Quote Meta
	 *
	 * @param int $quote_id Quote Id.
	 */
	public static function save_quote_meta( $quote_id ) {
		if ( ! WC()->rfq->is_empty() ) {
			$cart  = WC()->rfq->get_cart_contents();
			$items = array();
			foreach ( $cart as $cart_item_key => $cart_item ) {
				$_product = apply_filters( 'b2be_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
				$price    = apply_filters( 'b2be_product_regular_price', $_product->get_price(), $_product, true );
				if ( $_product ) {
					$items[ $cart_item_key ] = apply_filters(
						'b2be_quote_item',
						array(
							'name'            => $_product->get_name(),
							'tax_class'       => $_product->get_tax_class(),
							'product_id'      => $_product->is_type( 'variation' ) ? $_product->get_parent_id() : $_product->get_id(),
							'variation_id'    => $_product->is_type( 'variation' ) ? $_product->get_id() : 0,
							'item_product_id' => $cart_item['product_id'],
							'qty'             => $cart_item['quantity'],
							'variation'       => $cart_item['variation'],
							'subtotal'        => $cart_item['quantity'] * $price,
							'total'           => $cart_item['quantity'] * $price,
						),
						$_product,
						$cart_item,
						$cart_item_key
					);
				}
			}
			update_post_meta( $quote_id, 'items', $items );
		}

		if ( is_user_logged_in() ) {
			update_post_meta( $quote_id, '_customer_user', get_current_user_id() );
		}
	}

	/**
	 * Quote Need Revision
	 */
	public static function quote_need_revision() {

		if ( ! ( isset( $_GET['revise_quote'] ) || isset( $_GET['accept_quote'] ) || isset( $_GET['reject_quote'] ) || isset( $_GET['rfq_check_out'] ) || isset( $_GET['accept_and_check_out'] ) )
			|| ! is_user_logged_in() || ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( filter_input( INPUT_GET, '_wpnonce' ), 'cwcrfq-quote_action' ) ) {
			return;
		}
		if ( isset( $_GET['revise_quote'] ) ) {
			$quote = wc_get_quote( absint( $_GET['revise_quote'] ) );

			if ( ! $quote->get_id() ) {
				return;
			}

			$quote->set_status( 'need-revision' );
			$quote->save();
			$quote = wc_get_quote( $quote->get_id() );
			wc_add_notice( __( 'Quote is marked as Need Revision.', 'b2b-ecommerce' ) );
			do_action( 'cwcrfq_quote_marked_need_revision', $quote->get_id(), $quote );
			$quotes_account_url = wc_get_endpoint_url( CWRFQ_QUOTE_ENDPOINT, '', wc_get_page_permalink( 'myaccount' ) );
			wp_safe_redirect( $quotes_account_url );
			exit;
		}
		if ( isset( $_GET['accept_quote'] ) ) {
			$quote = wc_get_quote( absint( $_GET['accept_quote'] ) );

			if ( ! $quote->get_id() ) {
				return;
			}
			$quote->set_status( 'accepted' );
			do_action( 'cwcrfq_quote_marked_accepted_email', $quote->get_id(), $quote );
			$quote->save();

			$accept_quotes_account_url = wc_get_endpoint_url( CWRFQ_QUOTE_ENDPOINT, '', wc_get_page_permalink( 'myaccount' ) );
			wp_safe_redirect( $accept_quotes_account_url );
			exit;
		}
		if ( isset( $_GET['accept_and_check_out'] ) ) {
			$quote = wc_get_quote( absint( $_GET['accept_and_check_out'] ) );

			if ( ! $quote->get_id() ) {
				return;
			}
			$quote->set_status( 'accepted' );
			$quote->save();
			$quote = wc_get_quote( $quote->get_id() );
			do_action( 'cwcrfq_quote_marked_accepted', $quote->get_id(), $quote );
			do_action( 'cwcrfq_quote_marked_accepted_email', $quote->get_id(), $quote );
			$quotes_checkout_url = wc_get_endpoint_url( CWRFQ_QUOTE_ENDPOINT, '', wc_get_page_permalink( 'checkout' ) );
			wp_safe_redirect( $quotes_checkout_url );
			exit;
		}
		if ( isset( $_GET['reject_quote'] ) ) {
			$quote = wc_get_quote( absint( $_GET['reject_quote'] ) );
			if ( ! $quote->get_id() ) {
				return;
			}

			$quote->set_status( 'rejected' );
			$quote->save();
			$quote = wc_get_quote( $quote->get_id() );
			wc_add_notice( __( 'Quote is marked as Reject.', 'b2b-ecommerce' ) );
			do_action( 'cwcrfq_quote_marked_rejected', $quote->get_id(), $quote );
			$quotes_account_url = wc_get_endpoint_url( CWRFQ_QUOTE_ENDPOINT, '', wc_get_page_permalink( 'myaccount' ) );
			wp_safe_redirect( $quotes_account_url );
			exit;
		}
		if ( isset( $_GET['rfq_check_out'] ) ) {
			$quote = wc_get_quote( absint( $_GET['rfq_check_out'] ) );

			if ( ! $quote->get_id() ) {
				return;
			}
			$quote = wc_get_quote( $quote->get_id() );
			do_action( 'cwcrfq_quote_marked_accepted', $quote->get_id(), $quote );
			$quotes_checkout_url = wc_get_endpoint_url( CWRFQ_QUOTE_ENDPOINT, '', wc_get_page_permalink( 'checkout' ) );
			wp_safe_redirect( $quotes_checkout_url );
			exit;
		}
	}
	/**
	 * Function Populate Cart
	 *
	 * @param int    $quote_id Quote Id.
	 * @param object $quote Quote Object.
	 */
	public static function populate_cart( $quote_id, $quote ) {
		global $woocommerce;
		$woocommerce->cart->empty_cart();
		$items = $quote->get_quote_items();

		foreach ( $items as $item ) {
			$item = (object) $item;

			$product = wc_get_product( $item->item_product_id );
			if ( $product->get_type() == 'variable' ) {
				self::variable_product_add_to_cart( $item->item_product_id, $item->qty, $item->variation_id, $item->variation, array( 'quote' => $quote_id ) );
			} else {
				self::product_add_to_cart( $item->item_product_id, $item->qty, array( 'quote' => $quote_id ) );
			}
			do_action( 'cwrfq_added_quote_item_to_cart', $product, $item, $quote_id );
		}
	}
	/**
	 * Function Add to Cart
	 *
	 * @param int   $product_id product Id.
	 * @param int   $quantity item quantity.
	 * @param array $cart_item_data cart item array.
	 */
	public static function product_add_to_cart( $product_id, $quantity, $cart_item_data = array() ) {
		WC()->cart->add_to_cart( $product_id, $quantity, 0, array(), $cart_item_data );
	}

	/**
	 * Function Variable Product Add to Cart
	 *
	 * @param int   $product_id product Id.
	 * @param int   $quantity item quantity.
	 * @param int   $variation_id item quantity.
	 * @param array $variation item quantity.
	 * @param array $cart_item_data cart item array.
	 */
	public static function variable_product_add_to_cart( $product_id = 0, $quantity = 1, $variation_id = 0, $variation = array(), $cart_item_data = array() ) {

		WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variation, $cart_item_data );
	}

	/**
	 * Function Populate Cart
	 *
	 * @param object $item item Object.
	 * @param array  $cart_item_key cart item array.
	 * @param array  $values quote values.
	 */
	public static function add_quote_to_item( $item, $cart_item_key, $values ) {
		if ( isset( $values['quote'] ) ) {
			$item->add_meta_data( 'quote', $values['quote'], true );
		}
	}

	/**
	 * Function to load cart session.
	 */
	public static function load_cart_from_session() {
		$cart          = WC()->session->get( 'rfq', null );
		$new_cart      = new B2BE_RFQ_Cart();
		$cart_contents = array();
		foreach ( $cart as $key => $values ) {
			if ( ! is_customize_preview() && 'customize-preview' === $key ) {
				continue;
			}

			$product = wc_get_product( $values['variation_id'] ? $values['variation_id'] : $values['product_id'] );

			if ( empty( $product ) || ! $product->exists() || 0 >= $values['quantity'] ) {
				continue;
			}

			/**
			 * Allow 3rd parties to validate this item before it's added to cart and add their own notices.
			 *
			 * @since 3.6.0
			 *
			 * @param bool $remove_cart_item_from_session If true, the item will not be added to the cart. Default: false.
			 * @param string $key Cart item key.
			 * @param array $values Cart item values e.g. quantity and product_id.
			 */
			if ( apply_filters( 'b2be_pre_remove_rfq_item_from_session', false, $key, $values ) ) {
				$update_cart_session = true;
				do_action( 'woocommerce_remove_rfq_item_from_session', $key, $values );
			} elseif ( ! empty( $values['data_hash'] ) && ! hash_equals( $values['data_hash'], wc_get_cart_item_data_hash( $product ) ) ) { // phpcs:ignore PHPCompatibility.PHP.NewFunctions.hash_equalsFound.
				$update_cart_session = true;
				/* translators: %1$s: product name. %2$s product permalink. */
				wc_add_notice( sprintf( __( '%1$s has been removed from your cart because it has since been modified. You can add it back to your cart <a href="%2$s">here</a>.', 'b2b-ecommerce' ), $product->get_name(), $product->get_permalink() ), 'notice' );
				do_action( 'woocommerce_remove_rfq_item_from_session', $key, $values );
			} else {
				// Put session data into array. Run through filter so other plugins can load their own session data.
				$session_data = array_merge(
					$values,
					array(
						'data' => $product,
					)
				);

				$cart_contents[ $key ] = apply_filters( 'b2be_get_rfq_item_from_session', $session_data, $values, $key );
				// originally  woocommerce_get_cart_item_from_session.

				// Add to cart right away so the product is visible in woocommerce_get_cart_item_from_session hook.
				$new_cart->set_cart_contents( $cart_contents );
			}
		}
		if ( ! empty( $cart_contents ) ) {
			$new_cart->set_cart_contents( apply_filters( 'b2be_rfq_contents_changed', $cart_contents ) );
				// originally  woocommerce_cart_contents_changed.
			$new_cart->calculate_totals();

			WC()->rfq = $new_cart;
		}
	}

}

B2BE_RFQ_Form_Handler::init();
