<?php
/**
 * Functions used by plugins
 *
 * @since 2.5.0
 * @package woocomerce/templates
 */

/**
 * Get all rfq statuses.
 *
 * @since 1.1.1.0
 * @return array
 */
function wcrfq_get_quotes_statuses() {
	$quote_statuses = array(
		'requested'     => _x( 'Requested', 'Quote status', 'b2b-ecommerce' ),
		'quoted'        => _x( 'Quoted', 'Quote status', 'b2b-ecommerce' ),
		'accepted'      => _x( 'Accepted', 'Quote status', 'b2b-ecommerce' ),
		'need-revision' => _x( 'Need Revision', 'Quote status', 'b2b-ecommerce' ),
		'rejected'      => _x( 'Rejected', 'Quote status', 'b2b-ecommerce' ),
	);

	/*
	@name: wcrfq_quote_statuses
	@desc: Modify the b2b ecommerce quote statuses.
	@param: (array) $quote_statuses Array of Statuses of b2b ecommerce quote.
	@package: b2b-ecommerce-for-woocommerce
	@module: request for quote
	@type: filter
	*/
	return apply_filters( 'wcrfq_quote_statuses', $quote_statuses );
}

/**
 * Gets the url to remove an item from the cart.
 *
 * @since 3.3.0
 * @param string $cart_item_key contains the id of the cart item.
 * @return string url to page
 */
function wcrfq_get_cart_remove_url( $cart_item_key ) {
	$cart_page_url = wc_get_page_permalink( CWRFQ_RFQ_CART_SLUG );

	/*
	@name: b2be_get_remove_url
	@desc: Modify the url of remove product from rfq button.
	@param: (string) $cart_page_url Url of rfq page.
	@package: b2b-ecommerce-for-woocommerce
	@module: request for quote
	@type: filter
	*/
	return apply_filters( 'b2be_get_remove_url', $cart_page_url ? wp_nonce_url( add_query_arg( 'remove_rfq_item', $cart_item_key, $cart_page_url ), 'woocommerce-cart' ) : '' );
}

/**
 * Add to cart messages.
 *
 * @param int|array $products Product ID list or single product ID.
 * @param bool      $show_qty Should qty's be shown? Added in 2.6.0.
 * @param bool      $return   Return message rather than add it.
 *
 * @return mixed
 */
function wc_add_to_rfq_message( $products, $show_qty = false, $return = false ) {
	$titles = array();
	$count  = 0;

	if ( ! is_array( $products ) ) {
		$products = array( $products => 1 );
		$show_qty = false;
	}

	if ( ! $show_qty ) {
		$products = array_fill_keys( array_keys( $products ), 1 );
	}

	foreach ( $products as $product_id => $qty ) {
		/*
		@name: b2be_add_to_cart_item_name_in_quotes
		@desc: Modify name of the the line item in rfq cart.
		@param: (int) $product_id Product Id of current line item.
		@package: b2b-ecommerce-for-woocommerce
		@module: request for quote
		@type: filter
		*/
		/* translators: %s: product name */
		$titles[] = apply_filters( 'woocommerce_add_to_cart_qty_html', ( $qty > 1 ? absint( $qty ) . ' &times; ' : '' ), $product_id ) . apply_filters( 'b2be_add_to_cart_item_name_in_quotes', sprintf( _x( '&ldquo;%s&rdquo;', 'Item name in quotes', 'b2b-ecommerce' ), strip_tags( get_the_title( $product_id ) ) ), $product_id );
		$count   += $qty;
	}

	$titles = array_filter( $titles );
	/* translators: %s: product name */
	$added_text = sprintf( _n( '%s has been added to your RFQ.', '%s have been added to your RFQ.', $count, 'b2b-ecommerce' ), wc_format_list_of_items( $titles ) );

	// Output success messages.
	if ( 'yes' === get_option( 'woocommerce_cart_redirect_after_add' ) ) {
		/*
		@name: b2be_continue_shopping_redirect
		@desc: Modify continue shopping button url.
		@param: (int) $redirection_url Url to which user should be redirected after clicking the button.
		@package: b2b-ecommerce-for-woocommerce
		@module: request for quote
		@type: filter
		*/
		$return_to = apply_filters( 'b2be_continue_shopping_redirect', wc_get_raw_referer() ? wp_validate_redirect( wc_get_raw_referer(), false ) : wc_get_page_permalink( 'shop' ) );
		$message   = sprintf( '<a href="%s" tabindex="1" class="button wc-forward">%s</a> %s', esc_url( $return_to ), esc_html__( 'Continue shopping', 'b2b-ecommerce' ), esc_html( $added_text ) );
	} else {
		$message = sprintf( '<a href="%s" tabindex="1" class="button wc-forward">%s</a> %s', esc_url( b2be_get_rfq_cart_url() ), esc_html__( 'View RFQ', 'b2b-ecommerce' ), esc_html( $added_text ) );
	}

	if ( has_filter( 'wc_add_to_cart_message' ) ) {
		wc_deprecated_function( 'The wc_add_to_cart_message filter', '3.0', 'wc_add_to_cart_message_html' );
		$message = apply_filters( 'wc_add_to_cart_message', $message, $product_id );
	}

	$message = apply_filters( 'wc_add_to_cart_message_html', $message, $products, $show_qty );

	if ( $return ) {
		return $message;
	} else {
		/*
		@name: b2be_add_to_rfq_notice_type
		@desc: Modify the notice type of add to rfq message.
		@param: (string) $notice_type Notice Type of add to rfq message.
		@package: b2b-ecommerce-for-woocommerce
		@module: request for quote
		@type: filter
		*/
		wc_add_notice( $message, apply_filters( 'b2be_add_to_rfq_notice_type', 'success' ) );
	}
}
/**
 * Get Quote Status Name.
 *
 * @param string $status variable.
 * @return string
 */
function wc_get_quote_status_name( $status ) {
	$statuses = wcrfq_get_quotes_statuses();
	$status   = isset( $statuses[ $status ] ) ? $statuses[ $status ] : $status;
	return $status;
}
/**
 * Get Quote.
 *
 * @param bool $quote_id variable.
 * @return bool
 */
function wc_get_quote( $quote_id = false ) {
	$quote_id = wc_get_quote_id( $quote_id );
	if ( ! $quote_id ) {
		return false;
	}

	try {
		return new B2BE_RFQ_Quote( $quote_id );
	} catch ( Exception $e ) {
		wc_caught_exception( $e );
		return false;
	}
}
/**
 * Get Quote Id.
 *
 * @param string $quote variable.
 * @return string
 */
function wc_get_quote_id( $quote ) {
	global $post;

	if ( false === $quote && is_a( $post, 'WP_Post' ) && 'quote' === get_post_type( $post ) ) {
		return absint( $post->ID );
	} elseif ( is_numeric( $quote ) ) {
		return $quote;
	} elseif ( $quote instanceof B2BE_RFQ_Quote ) {
		return $quote->get_id();
	} elseif ( ! empty( $quote->ID ) ) {
		return $quote->ID;
	} else {
		return false;
	}
}
/**
 * Get Quote Actions
 *
 * @param string $quote variable.
 * @return bool
 */
function wc_get_account_quotes_actions( $quote ) {
	if ( ! is_object( $quote ) ) {
		$quote_id = absint( $quote );
		$quote    = wc_get_quote( $quote_id );
	}

	$add_to_rfq_btn_txt = get_option( 'codup-rfq_add_to_rfq_button_text' );
	$accept_btn_txt     = get_option( 'codup-rfq_accept_rfq_button_text' );
	$revision_btn_txt   = get_option( 'codup-rfq_revison_rfq_button_text' );
	$view_btn_txt       = get_option( 'codup-rfq_view_rfq_button_text' );
	$reject_btn_txt     = get_option( 'codup-rfq_reject_rfq_button_text' );

	$actions = apply_filters(
		'b2be_get_account_quotes_actions',
		array(
			'accept'               => array(
				'url'  => wp_nonce_url( add_query_arg( 'accept_quote', $quote->get_id() ), 'cwcrfq-quote_action' ),
				'name' => ( '' !== $accept_btn_txt ) ? $accept_btn_txt : __( 'Accept', 'codup-wcrfq' ),
			),
			'need-revision'        => array(
				'url'  => wp_nonce_url( add_query_arg( 'revise_quote', $quote->get_id() ), 'cwcrfq-quote_action' ),
				'name' => ( '' !== $revision_btn_txt ) ? $revision_btn_txt : __( 'Need Revision', 'codup-wcrfq' ),
			),
			'reject'               => array(
				'url'  => wp_nonce_url( add_query_arg( 'reject_quote', $quote->get_id() ), 'cwcrfq-quote_action' ),
				'name' => ( '' !== $reject_btn_txt ) ? $reject_btn_txt : __( 'Reject', 'codup-wcrfq' ),
			),
			'view'                 => array(
				'url'  => $quote->get_view_quote_url(),
				'name' => ( '' !== $view_btn_txt ) ? $view_btn_txt : __( 'View', 'codup-wcrfq' ),
			),
			'check-out'            => array(
				'url'  => wp_nonce_url( add_query_arg( 'rfq_check_out', $quote->get_id() ), 'cwcrfq-quote_action' ),
				'name' => __( 'Check Out', 'b2b-ecommerce' ),
			),
			'accept-and-check-out' => array(
				'url'  => wp_nonce_url( add_query_arg( 'accept_and_check_out', $quote->get_id() ), 'cwcrfq-quote_action' ),
				'name' => __( 'Accept And Check Out', 'b2b-ecommerce' ),
			),
		)
	);

	/*
	@name: cwrfq_valid_order_statuses_for_accept
	@desc: Modify the valid quote status for accepting quote.
	@param: (array) $quote_status Notice Type of add to rfq message.
	@param: (array) $quote Current quote object.
	@package: b2b-ecommerce-for-woocommerce
	@module: request for quote
	@type: filter
	*/
	if ( ! in_array( $quote->get_status(), apply_filters( 'cwrfq_valid_order_statuses_for_accept', array( 'quoted' ), $quote ), true ) ) {
		unset( $actions['accept'], $actions['need-revision'], $actions['reject'], $actions['accept-and-check-out'] );

	}

	/*
	@name: b2be_my_account_my_quotes_actions
	@desc: Modify Quote actions on my account page.
	@param: (array) $actions B2B Quote actions.
	@param: (array) $quote Current quote object.
	@package: b2b-ecommerce-for-woocommerce
	@module: request for quote
	@type: filter
	*/
	return apply_filters( 'b2be_my_account_my_quotes_actions', $actions, $quote );
}
