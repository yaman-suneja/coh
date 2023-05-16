<?php
/**
 * Cart Page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 1.1.5.0
 */

defined( 'ABSPATH' ) || exit;

// do_action( 'b2be_before_cart' );.
if ( function_exists( 'wc_print_notices' ) ) {
	wc_print_notices();
}
?>

<form class="woocommerce-rfq-form" method="post">
	<table class="shop_table shop_table_responsive rfq woocommerce-cart-form__contents" cellspacing="0">
		<thead>
			<tr>
				<th class="product-remove">&nbsp;</th>
				<th class="product-thumbnail">&nbsp;</th>
				<th class="product-name"><?php esc_html_e( 'Product', 'b2b-ecommerce' ); ?></th>
				<th class="product-price"><?php esc_html_e( 'Price', 'b2b-ecommerce' ); ?></th>
				<th class="product-quantity"><?php esc_html_e( 'Quantity', 'b2b-ecommerce' ); ?></th>
				<th class="product-subtotal"><?php esc_html_e( 'Total', 'b2b-ecommerce' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
			/*
			@name: b2be_before_cart_contents
			@desc: Runs before cart contents on rfq cart page.
			@package: b2b-ecommerce-for-woocommerce
			@module: request for quote
			@type: action
			*/
			?>
			<?php do_action( 'b2be_before_cart_contents' ); ?>

			<?php
			foreach ( WC()->rfq->get_cart() as $cart_item_key => $cart_item ) {
				/*
				@name: b2be_cart_item_product
				@desc: Modify the product object in rfq cart.
				@param: (object) $cart_item_data Cart Item data.
				@param: (object) $cart_item Product Object.
				@param: (int) $cart_item_key Cart Item Key.
				@package: b2b-ecommerce-for-woocommerce
				@module: request for quote
				@type: filter
				*/
				$_product   = apply_filters( 'b2be_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
				$product_id = apply_filters( 'b2be_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

				if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'b2be_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
					$product_permalink = apply_filters( 'b2be_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
					?>
					<tr class="woocommerce-cart-form__cart-item <?php echo esc_attr( apply_filters( 'b2be_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">

						<td class="product-remove">
							<?php
								/*
								@name: b2be_rfq_item_remove_link
								@desc: Modify the remove item link in rfq cart.
								@param: (string) $link Remove Line item from rfq cart button
								@param: (int) $cart_item_key Cart Item Key.
								@package: b2b-ecommerce-for-woocommerce
								@module: request for quote
								@type: filter
								*/
								echo wp_kses_post(
									apply_filters(
										// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped.
										'b2be_rfq_item_remove_link',
										sprintf(
											'<a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s">&times;</a>',
											esc_url( wcrfq_get_cart_remove_url( $cart_item_key ) ),
											esc_html__( 'Remove this item', 'b2b-ecommerce' ),
											esc_attr( $product_id ),
											esc_attr( $_product->get_sku() )
										),
										$cart_item_key
									)
								);
							?>
						</td>

						<td class="product-thumbnail">
							<?php
							/*
							@name: b2be_cart_item_thumbnail
							@desc: Modify the thumbnail for rfq line item.
							@param: (string) $thumbnail Thumbnail for rfq cart line item
							@param: (object) $cart_item Current Rfq Cart Item.
							@param: (int) $cart_item_key Cart Item Key.
							@package: b2b-ecommerce-for-woocommerce
							@module: request for quote
							@type: filter
							*/
							$thumbnail = apply_filters( 'b2be_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );

							if ( ! $product_permalink ) {
								echo wp_kses_post( $thumbnail ); // PHPCS: XSS ok.
							} else {
								printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), wp_kses_post( $thumbnail ) ); // PHPCS: XSS ok.
							}
							?>
						</td>

						<td class="product-name" data-title="<?php esc_attr_e( 'Product', 'b2b-ecommerce' ); ?>">
							<?php
							if ( ! $product_permalink ) {
								/*
								@name: b2be_cart_item_name
								@desc: Modify the name of rfq line item.
								@param: (string) $product_name Name of rfq cart line item.
								@param: (object) $cart_item Current Rfq Cart Item.
								@param: (int) $cart_item_key Cart Item Key.
								@package: b2b-ecommerce-for-woocommerce
								@module: request for quote
								@type: filter
								*/
								echo wp_kses_post( apply_filters( 'b2be_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . '&nbsp;' );
							} else {
								echo wp_kses_post( apply_filters( 'b2be_cart_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_name() ), $cart_item, $cart_item_key ) );
							}

							/*
							@name: b2be_after_cart_item_name
							@desc: Runs after rfq cart item name.
							@param: (object) $product current Line item object in rfq cart.
							@param: (int) $quote Cart item key
							@package: b2b-ecommerce-for-woocommerce
							@module: request for quote
							@type: action
							*/
							do_action( 'b2be_after_cart_item_name', $cart_item, $cart_item_key );

							// Meta data.
							echo wp_kses_post( wc_get_formatted_cart_item_data( $cart_item ) ); // PHPCS: XSS ok.
							// Backorder notification.
							if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
								echo wp_kses_post( apply_filters( 'b2be_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'b2b-ecommerce' ) . '</p>', $product_id ) );
							}
							?>
						</td>

						<td class="product-price" data-title="<?php esc_attr_e( 'Price', 'b2b-ecommerce' ); ?>">
							<?php
							if ( ! is_user_logged_in() && b2be_is_required_login( $_product ) ) {
								$my_account_page_url = get_permalink( get_option( 'woocommerce_myaccount_page_id' ) );
								?>
								<a style="width:max-content" href="<?php echo wp_kses_post( $my_account_page_url . '?returnPage=' . base64_encode( 'shop' ) ); ?>" name="required_login" value="<?php echo esc_attr( $_product->get_id() ); ?>"  class="single_required_login_button button alt"><?php echo wp_kses_post( apply_filters( 'b2be_signin_to_view_button_text', esc_html__( 'Sign In To View', 'b2b-ecommerce' ) ) ); ?></a>
								<?php
							} else {
								/*
								@name: b2be_cart_item_price
								@desc: Modify the name of rfq line item.
								@param: (string) $product_price Price of the rfq cart line item.
								@param: (object) $cart_item Current Rfq Cart Item.
								@param: (int) $cart_item_key Cart Item Key.
								@package: b2b-ecommerce-for-woocommerce
								@module: request for quote
								@type: filter
								*/

								echo wp_kses_post( apply_filters( 'b2be_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ) ); // PHPCS: XSS ok.
							}
							?>
						</td>

						<td class="product-quantity" data-title="<?php esc_attr_e( 'Quantity', 'b2b-ecommerce' ); ?>">
							<?php
							if ( $_product->is_sold_individually() ) {
								$product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
							} else {
								?>
								 
								<?php
								$product_quantity = woocommerce_quantity_input(
									array(
										'input_name'   => "cart[{$cart_item_key}][qty]",
										'input_value'  => $cart_item['quantity'],
										'max_value'    => $_product->get_max_purchase_quantity(),
										'min_value'    => '0',
										'product_name' => $_product->get_name(),
									),
									$_product,
									true
								);
							}

							/*
							@name: b2be_cart_item_quantity
							@desc: Modify the name of rfq line item.
							@param: (string) $product_quantity Quantity of the rfq cart line item.
							@param: (int) $cart_item_key Cart Item Key.
							@param: (object) $cart_item Current Rfq Cart Item.
							@package: b2b-ecommerce-for-woocommerce
							@module: request for quote
							@type: filter
							*/
							echo wp_kses_post( apply_filters( 'b2be_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item ) ); // PHPCS: XSS ok.
							?>
						</td>

						<td class="product-subtotal" data-title="<?php esc_attr_e( 'Total', 'b2b-ecommerce' ); ?>">
							<?php
							if ( ! is_user_logged_in() && b2be_is_required_login( $_product ) ) {
								$my_account_page_url = get_permalink( get_option( 'woocommerce_myaccount_page_id' ) );
								?>
								<a style="width:max-content" href="<?php echo wp_kses_post( $my_account_page_url . '?returnPage=' . base64_encode( 'shop' ) ); ?>" name="required_login" value="<?php echo esc_attr( $_product->get_id() ); ?>"  class="single_required_login_button button alt"><?php echo wp_kses_post( apply_filters( 'b2be_signin_to_view_button_text', esc_html__( 'Sign In To View', 'b2b-ecommerce' ) ) ); ?></a>
								<?php
							} else {
								/*
								@name: b2be_cart_item_subtotal
								@desc: Modify the name of rfq line item.
								@param: (string) $cart_subtotal Subtotal of the rfq cart.
								@param: (object) $cart_item Current Rfq Cart Item.
								@param: (int) $cart_item_key Cart Item Key.
								@package: b2b-ecommerce-for-woocommerce
								@module: request for quote
								@type: filter
								*/
								echo wp_kses_data( apply_filters( 'b2be_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ) ); // PHPCS: XSS ok.
							}
							?>
						</td>
					</tr>
							<?php
				}
			}
			?>
			<?php
			/*
			@name: b2be_rfq_cart_contents
			@desc: Allows third party plugins to attach external functioanlity here.
			@package: b2b-ecommerce-for-woocommerce
			@module: request for quote
			@type: action
			*/
			?>
			<?php do_action( 'b2be_rfq_cart_contents' ); ?>

			<tr>
				<td colspan="6" class="actions">

					<button type="submit" class="button" name="update_rfq_cart" value="<?php esc_attr_e( 'Update RFQ', 'b2b-ecommerce' ); ?>"><?php esc_html_e( 'Update RFQ', 'b2b-ecommerce' ); ?></button>


			<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
				</td>
			</tr>

		</tbody>
	</table>
	</form>
<form method="post" class="rfq-class">
	<?php wp_nonce_field(); ?>
	<h3>RFQ details</h3>
	<div class="rfq-details-wrapper form-group">
		<?php do_action( 'b2be_before_rfq_details_fields' ); ?>
		<?php
			$user_info = wp_get_current_user();
		?>
		<p><input class="form-control" type=<?php echo ( is_user_logged_in() && ! empty( $user_info->first_name ) ) ? 'hidden' : 'text'; ?> name="rfq_first_name" placeholder="First name" value="<?php echo ( is_user_logged_in() ) ? esc_attr( $user_info->first_name ) : ''; ?>" <?php echo ( is_user_logged_in() && ! empty( $user_info->first_name ) ) ? 'hidden="hidden"' : 'required="required"'; ?>/></p>
		<p><input class="form-control" type=<?php echo ( is_user_logged_in() && ! empty( $user_info->last_name ) ) ? 'hidden' : 'text'; ?> name="rfq_last_name" placeholder="Last name" value="<?php echo ( is_user_logged_in() ) ? esc_attr( $user_info->last_name ) : ''; ?>" <?php echo ( is_user_logged_in() && ! empty( $user_info->last_name ) ) ? 'hidden="hidden"' : 'required="required"'; ?>/></p>
		<p><input class="form-control" type=<?php echo ( is_user_logged_in() && ! empty( $user_info->user_email ) ) ? 'hidden' : 'email'; ?> name="rfq_email" placeholder="Email" value="<?php echo ( is_user_logged_in() ) ? esc_attr( $user_info->user_email ) : ''; ?>" <?php echo ( is_user_logged_in() ) ? 'hidden="hidden"' : 'required="required"'; ?>/></p>
		<p><textarea  name="rfq_message" required placeholder="Message" rows="3" cols="53"></textarea></p>
		
	</div>
	<button type="submit" class="button" name="submit_rfq" value="<?php esc_attr_e( 'Submit RFQ', 'b2b-ecommerce' ); ?>"><?php esc_html_e( 'Submit RFQ', 'b2b-ecommerce' ); ?></button>
</form>


<div class="cart-collaterals">
<?php
/**
 * Cart collaterals hook.
 *
 * @hooked woocommerce_cross_sell_display
 * @hooked woocommerce_cart_totals - 10
 */
// do_action( 'b2be_cart_collaterals' );.
?>
</div>
