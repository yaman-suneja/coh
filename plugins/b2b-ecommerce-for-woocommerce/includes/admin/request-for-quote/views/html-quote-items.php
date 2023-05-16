<?php
/**
 * Quote Items Html Template.
 *
 * @package codupio-request-for-quote-d659b8ba1ef2
 */

defined( 'ABSPATH' ) || exit;

global $wpdb;

$line_items = ! empty( $quote->get_quote_items() ) ? $quote->get_quote_items() : '';
if ( ! empty( $line_items ) ) {
	?>
<div class="woocommerce_quote_items_wrapper wcrfq-quote-items-editable">
	<table cellpadding="0" cellspacing="0" class="woocommerce_quote_items">
		<thead>
			<tr>
				<th class="item image" style="width:1px" ><?php esc_html_e( 'Image', 'b2b-ecommerce' ); ?></th>
				<th style="width:550px" class="item sortable"  data-sort="string-ins"><?php esc_html_e( 'Item', 'b2b-ecommerce' ); ?></th>
				<?php
				/*
				@name: woocommerce_admin_quote_item_headers
				@desc: Run in the header of the Quote post type.
				@param: (object) $quote Object of current quote.
				@package: b2b-ecommerce-for-woocommerce
				@module: request for quote
				@type: action
				*/
				?>
				<?php do_action( 'b2be_admin_quote_item_headers', $quote ); ?>
				<th style="width:100px" class="item_cost sortable" data-sort="float"><?php esc_html_e( 'Actual Price', 'b2b-ecommerce' ); ?></th>
				<th style="width:100px" class="quantity sortable" data-sort="int"><?php esc_html_e( 'Qty', 'b2b-ecommerce' ); ?></th>
				<th style="width:150px" class="line_cost sortable" data-sort="float"><?php esc_html_e( 'Actual Total', 'b2b-ecommerce' ); ?></th>
				<th style="width:100px" class="quoted_total sortable" data-sort="float"><?php esc_html_e( 'Quoted Total', 'b2b-ecommerce' ); ?></th>
				<th class="wc-order-edit-line-item" width="1%">&nbsp;</th>
			</tr>
		</thead>
		<tbody id="quote_line_items" style="text-align:center" >
			<?php
			foreach ( $line_items as $item_id => $item ) {
				if ( ! empty( $item ) ) {
					include 'html-quote-item.php';
				}
			}
			?>
			<input type="hidden" id="admin_quote_id" value="<?php echo wp_kses_post( $quote_id ); ?>">
			<?php
			/*
			@name: woocommerce_admin_quote_items_after_line_items
			@desc: Run after each line item in Quote post type.
			@param: (object) $quote_id Id of current quote.
			@package: b2b-ecommerce-for-woocommerce
			@module: request for quote
			@type: action
			*/
			do_action( 'b2be_admin_quote_items_after_line_items', $quote->get_id() );
			?>
	</tbody>
	</table>   
</div>
<?php } ?>
<div class="woocommerce_quote_items_wrapper cwrfq-quote-items-editable">
	<?php $submit_button_text = ( $quote->get_status() == 'quoted' ) ? __( 'Submit Quotation Again', 'b2b-ecommerce' ) : __( 'Submit Quotation', 'b2b-ecommerce' ); ?>
	<?php
	if ( ! empty( $line_items ) ) {
		?>
		<button type="button" class="button button-primary submit-quote-action"><?php echo esc_attr( $submit_button_text ); ?></button>
		<button type="button" style="float: right;margin-right: 25px;" id="admin_save_qoute_button" class="button button-primary save-quote-action"><?php esc_html_e( 'Save', 'b2b-ecommerce' ); ?></button>
	<?php } ?>
</div>
<?php return; ?>
<div class="woocommerce_qu_items_wrapper wc-order-items-editable">
	<table cellpadding="0" cellspacing="0" class="woocommerce_order_items">
		<thead>
			<tr>
				<th class="item sortable" colspan="2" data-sort="string-ins"><?php esc_html_e( 'Item', 'b2b-ecommerce' ); ?></th>
				<?php do_action( 'b2be_admin_order_item_headers', $order ); ?>
				<th class="item_cost sortable" data-sort="float"><?php esc_html_e( 'Cost', 'b2b-ecommerce' ); ?></th>
				<th class="quantity sortable" data-sort="int"><?php esc_html_e( 'Qty', 'b2b-ecommerce' ); ?></th>
				<th class="line_cost sortable" data-sort="float"><?php esc_html_e( 'Total', 'b2b-ecommerce' ); ?></th>
				<?php
				if ( ! empty( $order_taxes ) ) :
					foreach ( $order_taxes as $tax_id => $tax_item ) :
						$tax_class      = wc_get_tax_class_by_tax_id( $tax_item['rate_id'] );
						$tax_class_name = isset( $classes_options[ $tax_class ] ) ? $classes_options[ $tax_class ] : __( 'Tax', 'b2b-ecommerce' );
						$column_label   = ! empty( $tax_item['label'] ) ? $tax_item['label'] : __( 'Tax', 'b2b-ecommerce' );
						/* translators: %1$s: tax item name %2$s: tax class name  */
						$column_tip = sprintf( esc_html__( '%1$s (%2$s)', 'b2b-ecommerce' ), $tax_item['name'], $tax_class_name );
						?>
						<th class="line_tax tips" data-tip="<?php echo esc_attr( $column_tip ); ?>">
							<?php echo esc_attr( $column_label ); ?>
							<input type="hidden" class="order-tax-id" name="order_taxes[<?php echo esc_attr( $tax_id ); ?>]" value="<?php echo esc_attr( $tax_item['rate_id'] ); ?>">
							<?php if ( $order->is_editable() ) : ?>
								<a class="delete-order-tax" href="#" data-rate_id="<?php echo esc_attr( $tax_id ); ?>"></a>
							<?php endif; ?>
						</th>
						<?php
					endforeach;
				endif;
				?>
				<th class="wc-order-edit-line-item" width="1%">&nbsp;</th>
			</tr>
		</thead>
		<tbody id="order_line_items">
			<?php
			foreach ( $line_items as $item_id => $item ) {
				/*
				@name: b2be_before_order_item_{$prouct_type}_html
				@desc: Run before quote item html in Quote post type.
				@param: (int) $product_id Id of current quote.
				@param: (object) $product Object of current line item in Quote.
				@param: (object) $quote Object of current quote.
				@package: b2b-ecommerce-for-woocommerce
				@module: request for quote
				@type: action
				*/
				do_action( 'b2be_before_order_item_' . $item->get_type() . '_html', $item_id, $item, $order );

				include 'html-order-item.php';

				/*
				@name: b2be_before_order_item_{$prouct_type}_html
				@desc: Run after quote item html in Quote post type.
				@param: (int) $product_id Id of current quote.
				@param: (object) $product Object of current line item in Quote.
				@param: (object) $quote Object of current quote.
				@package: b2b-ecommerce-for-woocommerce
				@module: request for quote
				@type: action
				*/
				do_action( 'b2be_order_item_' . $item->get_type() . '_html', $item_id, $item, $order );
			}

			/*
			@name: b2be_admin_order_items_after_line_items.
			@desc: Run after line items in quote post type.
			@param: (int) $product_id Id of current quote.
			@package: b2b-ecommerce-for-woocommerce
			@module: request for quote
			@type: action
			*/
			do_action( 'b2be_admin_order_items_after_line_items', $order->get_id() );
			?>
		</tbody>
		<tbody id="order_shipping_line_items">
			<?php
			$shipping_methods = WC()->shipping() ? WC()->shipping()->load_shipping_methods() : array();
			foreach ( $line_items_shipping as $item_id => $item ) {
				include 'html-order-shipping.php';
			}

			/*
			@name: b2be_admin_order_items_after_shipping.
			@desc: Run after shipping template is enqueued in quote post type.
			@param: (int) $product_id Id of current quote.
			@package: b2b-ecommerce-for-woocommerce
			@module: request for quote
			@type: action
			*/
			do_action( 'b2be_admin_order_items_after_shipping', $order->get_id() );
			?>
		</tbody>
		
		
	</table>
</div>
<div class="wc-order-data-row wc-order-totals-items wc-order-items-editable">
	<?php
	$coupons = $order->get_items( 'coupon' );
	if ( $coupons ) :
		?>
		<div class="wc-used-coupons">
			<ul class="wc_coupon_list">
				<li><strong><?php esc_html_e( 'Coupon(s)', 'b2b-ecommerce' ); ?></strong></li>
				<?php
				foreach ( $coupons as $item_id => $item ) :
					$rfq_post_id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_title = %s AND post_type = 'shop_coupon' AND post_status = 'publish' LIMIT 1;", $item->get_code() ) ); // phpcs:disable WordPress.WP.GlobalVariablesOverride.OverrideProhibited
					$class       = $order->is_editable() ? 'code editable' : 'code';
					?>
					<li class="<?php echo esc_attr( $class ); ?>">
						<?php if ( $rfq_post_id ) : ?>
							<?php
							$post_url = apply_filters(
								'woocommerce_admin_order_item_coupon_url',
								add_query_arg(
									array(
										'post'   => $rfq_post_id,
										'action' => 'edit',
									),
									admin_url( 'post.php' )
								),
								$item,
								$order
							);
							?>
							<a href="<?php echo esc_url( $post_url ); ?>" class="tips" data-tip="<?php echo esc_attr( wc_price( $item->get_discount(), array( 'currency' => $order->get_currency() ) ) ); ?>">
								<span><?php echo esc_html( $item->get_code() ); ?></span>
							</a>
						<?php else : ?>
							<span class="tips" data-tip="<?php echo esc_attr( wc_price( $item->get_discount(), array( 'currency' => $order->get_currency() ) ) ); ?>">
								<span><?php echo esc_html( $item->get_code() ); ?></span>
							</span>
						<?php endif; ?>
						<?php if ( $order->is_editable() ) : ?>
							<a class="remove-coupon" href="javascript:void(0)" aria-label="Remove" data-code="<?php echo esc_attr( $item->get_code() ); ?>"></a>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>
	<table class="wc-order-totals">
		<?php if ( 0 < $order->get_total_discount() ) : ?>
			<tr>
				<td class="label"><?php esc_html_e( 'Discount:', 'b2b-ecommerce' ); ?></td>
				<td width="1%"></td>
				<td class="total">
					<?php echo wp_kses_post( wc_price( $order->get_total_discount(), array( 'currency' => $order->get_currency() ) ) ); // WPCS: XSS ok. ?>
				</td>
			</tr>
		<?php endif; ?>

		<?php
		/*
		@name: b2be_admin_order_totals_after_discount.
		@desc: Run after line items.
		@param: (int) $quote_id Id of current quote.
		@package: b2b-ecommerce-for-woocommerce
		@module: request for quote
		@type: action
		*/
		?>
		<?php do_action( 'b2be_admin_order_totals_after_discount', $order->get_id() ); ?>

		<?php if ( $order->get_shipping_methods() ) : ?>
			<tr>
				<td class="label"><?php esc_html_e( 'Shipping:', 'b2b-ecommerce' ); ?></td>
				<td width="1%"></td>
				<td class="total">
					<?php
					$refunded = $order->get_total_shipping_refunded();
					if ( $refunded > 0 ) {
						echo '<del>' . wp_kses_post( wp_strip_all_tags( wp_kses_post( wc_price( $order->get_shipping_total(), array( 'currency' => $order->get_currency() ) ) ) ) ) . '</del> <ins>' . wp_kses_post( wc_price( $order->get_shipping_total() - $refunded, array( 'currency' => $order->get_currency() ) ) ) . '</ins>'; // WPCS: XSS ok.
					} else {
						echo wp_kses_post( wc_price( $order->get_shipping_total(), array( 'currency' => $order->get_currency() ) ) ); // WPCS: XSS ok.
					}
					?>
				</td>
			</tr>
		<?php endif; ?>
		<?php do_action( 'b2be_admin_order_totals_after_shipping', $order->get_id() ); ?>

		<?php if ( wc_tax_enabled() ) : ?>
			<?php foreach ( $order->get_tax_totals() as $code => $tax_total ) : ?>
				<tr>
					<td class="label"><?php echo esc_html( $tax_total->label ); ?>:</td>
					<td width="1%"></td>
					<td class="total">
						<?php
						$refunded = $order->get_total_tax_refunded_by_rate_id( $tax_total->rate_id );
						if ( $refunded > 0 ) {
							echo '<del>' . wp_kses_post( wp_strip_all_tags( $tax_total->formatted_amount ) ) . '</del> <ins>' . wp_kses_post( wc_price( WC_Tax::round( $tax_total->amount, wc_get_price_decimals() ) ) - WC_Tax::round( $refunded, wc_get_price_decimals() ), array( 'currency' => $order->get_currency() ) ) . '</ins>'; // WPCS: XSS ok.
						} else {
							echo wp_kses_post( $tax_total->formatted_amount );
						}
						?>
					</td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
		<?php
		/*
		@name: b2be_admin_order_totals_after_tax.
		@desc: Run after quote items tax in quote post type.
		@param: (int) $quote_id Id of current quote.
		@package: b2b-ecommerce-for-woocommerce
		@module: request for quote
		@type: action
		*/
		do_action( 'b2be_admin_order_totals_after_tax', $order->get_id() );
		?>

		<tr>
			<td class="label"><?php esc_html_e( 'Total', 'b2b-ecommerce' ); ?>:</td>
			<td width="1%"></td>
			<td class="total">
				<?php echo wp_kses_post( $order->get_formatted_order_total() ); // WPCS: XSS ok. ?>
			</td>
		</tr>
		<?php
		/*
		@name: b2be_admin_order_totals_after_total.
		@desc: Run after quote items totals in quote post type.
		@param: (int) $quote_id Id of current quote.
		@package: b2b-ecommerce-for-woocommerce
		@module: request for quote
		@type: action
		*/
		do_action( 'b2be_admin_order_totals_after_total', $order->get_id() );
		?>

		<?php if ( $order->get_total_refunded() ) : ?>
			<tr>
				<td class="label refunded-total"><?php esc_html_e( 'Refunded', 'b2b-ecommerce' ); ?>:</td>
				<td width="1%"></td>
				<td class="total refunded-total">-<?php echo wp_kses_post( wc_price( $order->get_total_refunded(), array( 'currency' => $order->get_currency() ) ) ); // WPCS: XSS ok. ?></td>
			</tr>
		<?php endif; ?>
		<?php
		/*
		@name: b2be_admin_order_totals_after_refunded.
		@desc: Run after quote items rfunded amount in quote post type.
		@param: (int) $quote_id Id of current quote.
		@package: b2b-ecommerce-for-woocommerce
		@module: request for quote
		@type: action
		*/
		?>
		<?php do_action( 'b2be_admin_order_totals_after_refunded', $order->get_id() ); ?>

	</table>
	<div class="clear"></div>
</div>
<div class="wc-order-data-row wc-order-bulk-actions wc-order-data-row-toggle">
	<p class="add-items">
		<?php if ( $order->is_editable() ) : ?>
			<button type="button" class="button add-line-item"><?php esc_html_e( 'Add item(s)', 'b2b-ecommerce' ); ?></button>
			<?php if ( wc_coupons_enabled() ) : ?>
				<button type="button" class="button add-coupon"><?php esc_html_e( 'Apply coupon', 'b2b-ecommerce' ); ?></button>
			<?php endif; ?>
		<?php else : ?>
			<span class="description"><?php echo wp_kses_post( wc_help_tip( __( 'To edit this order change the status back to "Pending"', 'b2b-ecommerce' ) ) ); ?> <?php esc_html_e( 'This order is no longer editable.', 'b2b-ecommerce' ); ?></span>
		<?php endif; ?>
		<?php if ( 0 < $order->get_total() - $order->get_total_refunded() || 0 < absint( $order->get_item_count() - $order->get_item_count_refunded() ) ) : ?>
			<button type="button" class="button refund-items"><?php esc_html_e( 'Refund', 'b2b-ecommerce' ); ?></button>
		<?php endif; ?>
		<?php
			// Allow adding custom buttons.

			/*
			@name: b2be_order_item_add_action_buttons.
			@desc: Add custom buttom in quote data area.
			@param: (object) $quote Object of current quote.
			@package: b2b-ecommerce-for-woocommerce
			@module: request for quote
			@type: action
			*/
			do_action( 'b2be_order_item_add_action_buttons', $order );
		?>
		<?php if ( $order->is_editable() ) : ?>
			<button type="button" class="button button-primary calculate-action"><?php esc_html_e( 'Recalculate', 'b2b-ecommerce' ); ?></button>
		<?php endif; ?>
	</p>
</div>
<div class="wc-order-data-row wc-order-add-item wc-order-data-row-toggle" style="display:none;">
	<button type="button" class="button add-order-item"><?php esc_html_e( 'Add product(s)', 'b2b-ecommerce' ); ?></button>
	<button type="button" class="button add-order-fee"><?php esc_html_e( 'Add fee', 'b2b-ecommerce' ); ?></button>
	<button type="button" class="button add-order-shipping"><?php esc_html_e( 'Add shipping', 'b2b-ecommerce' ); ?></button>
	<?php if ( wc_tax_enabled() ) : ?>
		<button type="button" class="button add-order-tax"><?php esc_html_e( 'Add tax', 'b2b-ecommerce' ); ?></button>
	<?php endif; ?>
	<?php
		// Allow adding custom buttons.

		/*
		@name: b2be_order_item_add_line_buttons.
		@desc: Add custom buttom in quote data area.
		@param: (object) $quote Object of current quote.
		@package: b2b-ecommerce-for-woocommerce
		@module: request for quote
		@type: action
		*/

		do_action( 'b2be_order_item_add_line_buttons', $order );
	?>
	<button type="button" class="button cancel-action"><?php esc_html_e( 'Cancel', 'b2b-ecommerce' ); ?></button>
	<button type="button" class="button button-primary save-action"><?php esc_html_e( 'Save', 'b2b-ecommerce' ); ?></button>
</div>
<?php if ( 0 < $order->get_total() - $order->get_total_refunded() || 0 < absint( $order->get_item_count() - $order->get_item_count_refunded() ) ) : ?>
<div class="wc-order-data-row wc-order-refund-items wc-order-data-row-toggle" style="display: none;">
	<table class="wc-order-totals">
		<?php if ( 'yes' === get_option( 'woocommerce_manage_stock' ) ) : ?>
			<tr>
				<td class="label"><label for="restock_refunded_items"><?php esc_html_e( 'Restock refunded items', 'b2b-ecommerce' ); ?>:</label></td>
				<td class="total"><input type="checkbox" id="restock_refunded_items" name="restock_refunded_items" <?php checked( apply_filters( 'b2be_restock_refunded_items', true ) ); ?> /></td>
			</tr>
		<?php endif; ?>
		<tr>
			<td class="label"><?php esc_html_e( 'Amount already refunded', 'b2b-ecommerce' ); ?>:</td>
			<td class="total">-<?php echo wp_kses_post( wc_price( $order->get_total_refunded(), array( 'currency' => $order->get_currency() ) ) ); // WPCS: XSS ok. ?></td>
		</tr>
		<tr>
			<td class="label"><?php esc_html_e( 'Total available to refund', 'b2b-ecommerce' ); ?>:</td>
			<td class="total"><?php echo wp_kses_post( wc_price( $order->get_total() - $order->get_total_refunded(), array( 'currency' => $order->get_currency() ) ) ); // WPCS: XSS ok. ?></td>
		</tr>
		<tr>
			<td class="label">
				<label for="refund_amount">
					<?php echo wp_kses_post( wc_help_tip( __( 'Refund the line items above. This will show the total amount to be refunded', 'b2b-ecommerce' ) ) ); ?>
					<?php esc_html_e( 'Refund amount', 'b2b-ecommerce' ); ?>:
				</label>
			</td>
			<td class="total">
				<input type="text" id="refund_amount" name="refund_amount" class="wc_input_price"
				<?php
				if ( wc_tax_enabled() ) {
					// If taxes are enabled, using this refund amount can cause issues due to taxes not being refunded also.
					// The refunds should be added to the line items, not the order as a whole.
					echo 'readonly';
				}
				?>
				/>
				<div class="clear"></div>
			</td>
		</tr>
		<tr>
			<td class="label">
				<label for="refund_reason">
					<?php echo wp_kses_post( wc_help_tip( __( 'Note: the refund reason will be visible by the customer.', 'b2b-ecommerce' ) ) ); ?>
					<?php esc_html_e( 'Reason for refund (optional):', 'b2b-ecommerce' ); ?>
				</label>
			</td>
			<td class="total">
				<input type="text" id="refund_reason" name="refund_reason" />
				<div class="clear"></div>
			</td>
		</tr>
	</table>
	<div class="clear"></div>
	<div class="refund-actions">
		<?php
		$refund_amount = '<span class="wc-order-refund-amount">' . wc_price( 0, array( 'currency' => $order->get_currency() ) ) . '</span>';
		$gateway_name  = false !== $payment_gateway ? ( ! empty( $payment_gateway->method_title ) ? $payment_gateway->method_title : $payment_gateway->get_title() ) : __( 'Payment gateway', 'b2b-ecommerce' );

		if ( false !== $payment_gateway && $payment_gateway->can_refund_order( $order ) ) {
			/* translators: refund amount, gateway name */
			echo '<button type="button" class="button button-primary do-api-refund">' . sprintf( esc_html__( 'Refund %1$s via %2$s', 'b2b-ecommerce' ), wp_kses_post( $refund_amount ), esc_html( $gateway_name ) ) . '</button>';
		}
		?>
		<?php /* translators: refund amount  */ ?>
		<button type="button" class="button button-primary do-manual-refund tips" data-tip="<?php esc_attr_e( 'You will need to manually issue a refund through your payment gateway after using this.', 'b2b-ecommerce' ); ?>"><?php printf( esc_html__( 'Refund %s manually', 'b2b-ecommerce' ), wp_kses_post( $refund_amount ) ); ?></button>
		<button type="button" class="button cancel-action"><?php esc_html_e( 'Cancel', 'b2b-ecommerce' ); ?></button>
		<input type="hidden" id="refunded_amount" name="refunded_amount" value="<?php echo esc_attr( $order->get_total_refunded() ); ?>" />
		<div class="clear"></div>
	</div>
</div>
<?php endif; ?>

<script type="text/template" id="tmpl-wc-modal-add-products">
	<div class="wc-backbone-modal">
		<div class="wc-backbone-modal-content">
			<section class="wc-backbone-modal-main" role="main">
				<header class="wc-backbone-modal-header">
					<h1><?php esc_html_e( 'Add products', 'b2b-ecommerce' ); ?></h1>
					<button class="modal-close modal-close-link dashicons dashicons-no-alt">
						<span class="screen-reader-text">Close modal panel</span>
					</button>
				</header>
				<article>
					<form action="" method="post">
						<table class="widefat">
							<thead>
								<tr>
									<th><?php esc_html_e( 'Product', 'b2b-ecommerce' ); ?></th>
									<th><?php esc_html_e( 'Quantity', 'b2b-ecommerce' ); ?></th>
								</tr>
							</thead>
							<?php
								$row = '
									<td><select class="wc-product-search" name="item_id" data-allow_clear="true" data-display_stock="true" data-placeholder="' . esc_attr__( 'Search for a product&hellip;', 'b2b-ecommerce' ) . '"></select></td>
									<td><input type="number" step="1" min="0" max="9999" autocomplete="off" name="item_qty" placeholder="1" size="4" class="quantity" /></td>';
							?>
							<tbody data-row="<?php echo esc_attr( $row ); ?>">
								<tr>
									<?php echo wp_kses_post( $row ); // WPCS: XSS ok. ?>
								</tr>
							</tbody>
						</table>
					</form>
				</article>
				<footer>
					<div class="inner">
						<button id="btn-ok" class="button button-primary button-large"><?php esc_html_e( 'Add', 'b2b-ecommerce' ); ?></button>
					</div>
				</footer>
			</section>
		</div>
	</div>
	<div class="wc-backbone-modal-backdrop modal-close"></div>
</script>

<script type="text/template" id="tmpl-wc-modal-add-tax">
	<div class="wc-backbone-modal">
		<div class="wc-backbone-modal-content">
			<section class="wc-backbone-modal-main" role="main">
				<header class="wc-backbone-modal-header">
					<h1><?php esc_html_e( 'Add tax', 'b2b-ecommerce' ); ?></h1>
					<button class="modal-close modal-close-link dashicons dashicons-no-alt">
						<span class="screen-reader-text">Close modal panel</span>
					</button>
				</header>
				<article>
					<form action="" method="post">
						<table class="widefat">
							<thead>
								<tr>
									<th>&nbsp;</th>
									<th><?php esc_html_e( 'Rate name', 'b2b-ecommerce' ); ?></th>
									<th><?php esc_html_e( 'Tax class', 'b2b-ecommerce' ); ?></th>
									<th><?php esc_html_e( 'Rate code', 'b2b-ecommerce' ); ?></th>
									<th><?php esc_html_e( 'Rate %', 'b2b-ecommerce' ); ?></th>
								</tr>
							</thead>
						<?php
							$rates = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}woocommerce_tax_rates ORDER BY tax_rate_name LIMIT 100" );

						foreach ( $rates as $rate ) {
							echo '
									<tr>
										<td><input type="radio" id="add_order_tax_' . absint( $rate->tax_rate_id ) . '" name="add_order_tax" value="' . absint( $rate->tax_rate_id ) . '" /></td>
										<td><label for="add_order_tax_' . absint( $rate->tax_rate_id ) . '">' . wp_kses_post( WC_Tax::get_rate_label( $rate ) ) . '</label></td>
										<td>' . ( isset( $classes_options[ $rate->tax_rate_class ] ) ? wp_kses_post( $classes_options[ $rate->tax_rate_class ] ) : '-' ) . '</td>
										<td>' . wp_kses_post( WC_Tax::get_rate_code( $rate ) ) . '</td>
										<td>' . wp_kses_post( WC_Tax::get_rate_percent( $rate ) ) . '</td>
									</tr>
								'; // WPCS: XSS ok.
						}
						?>
						</table>
						<?php if ( absint( $wpdb->get_var( "SELECT COUNT(tax_rate_id) FROM {$wpdb->prefix}woocommerce_tax_rates;" ) ) > 100 ) : ?>
							<p>
								<label for="manual_tax_rate_id"><?php esc_html_e( 'Or, enter tax rate ID:', 'b2b-ecommerce' ); ?></label><br/>
								<input type="number" name="manual_tax_rate_id" id="manual_tax_rate_id" step="1" placeholder="<?php esc_attr_e( 'Optional', 'b2b-ecommerce' ); ?>" />
							</p>
						<?php endif; ?>
					</form>
				</article>
				<footer>
					<div class="inner">
						<button id="btn-ok" class="button button-primary button-large"><?php esc_html_e( 'Add', 'b2b-ecommerce' ); ?></button>
					</div>
				</footer>
			</section>
		</div>
	</div>
	<div class="wc-backbone-modal-backdrop modal-close"></div>
</script>
