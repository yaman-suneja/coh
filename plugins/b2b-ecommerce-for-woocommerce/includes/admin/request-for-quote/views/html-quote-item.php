<?php
/**
 * Shows an order item
 *
 * @package WooCommerce/Admin
 * @var object $item The item being displayed
 * @var int $item_id The id of the item being displayed
 */

defined( 'ABSPATH' ) || exit;


if ( ! empty( $item['item_product_id'] ) ) {
	$product      = wc_get_product( $item['item_product_id'] );
	$product_link = $product ? admin_url( 'post.php?post=' . $product->get_id() . '&action=edit' ) : '';
	$thumbnail    = $product ? $product->get_image( 'thumbnail', array( 'title' => '' ), false ) : '';
	$row_class    = apply_filters( 'b2be_admin_html_quote_item_class', ! empty( $class ) ? $class : '', $item, $quote );
	$item         = (object) $item;
	$currency     = get_woocommerce_currency();
	$quote_id     = ( isset( $_GET['post'] ) ) ? sanitize_text_field( wp_unslash( $_GET['post'] ) ) : '';
	wp_nonce_field( 'rfq_metabox_item_settings', 'rfq_metabox_item_settings_nonce' );
	?>

	<tr class="item <?php echo esc_attr( $row_class ); ?>" data-order_item_id="<?php echo esc_attr( $item_id ); ?>">
		<td class="thumb">
			<?php if ( '' != $thumbnail ) { ?>
				<?php echo '<div class="wc-order-item-thumbnail">' . wp_kses_post( $thumbnail ) . '</div>'; ?>
			<?php } else { ?>
				<?php echo '<div class="wc-order-item-thumbnail"><img style="height:150px;width:150px;" src="' . wp_kses_post( wc_placeholder_img_src() ) . '"></div>'; ?>
			<?php } ?>
		</td>
		<td class="name" style="text-align:center" data-sort-value="<?php echo esc_attr( $item->name ); ?>">
			<?php

			echo '<div class="wc-order-item-name">' . wp_kses_post( $item->name ) . '</div>';

			if ( $product && $product->get_sku() ) {
				echo '<div class="wc-order-item-sku"><strong>' . esc_html__( 'SKU:', 'b2b-ecommerce' ) . '</strong> ' . esc_html( $product->get_sku() ) . '</div>';
			}

			if ( $item->variation_id ) {
				echo '<div class="wc-order-item-variation"><strong>' . esc_html__( 'Variation ID:', 'b2b-ecommerce' ) . '</strong> ';
				if ( 'product_variation' === get_post_type( $item->variation_id ) ) {
					echo esc_html( $item->variation_id );
				} else {
					/* translators: %s: variation id */
					printf( esc_html__( '%s (No longer exists)', 'b2b-ecommerce' ), esc_html( $item->variation_id ) );
				}
				echo '</div>';
			}
			?>
			<?php
			if ( $product->is_type( 'variable' ) ) {
				$product_id[] = $item->variation_id;
			} else {
				$product_id[] = $product->get_id();
			}
				echo '<script>var arrayFromPhp = ' . json_encode( $product_id ) . ';</script>';
			?>

			<input type="hidden" class="order_item_id" name="order_item_id[]" value="<?php echo esc_attr( $item_id ); ?>" />
			<input type="hidden" name="order_item_tax_class[<?php echo absint( $item_id ); ?>]" value="<?php echo esc_attr( $item->tax_class ); ?>" />

			<?php do_action( 'b2be_rfq_after_quote_line_item_name', $product, $item ); ?>
			
		</td>

		<td class="item_cost" data-sort-value="<?php echo esc_attr( $quote->get_item_subtotal( $item, false, true ) ); ?>" >
			<div class="view">
				<?php
					$_role            = '';
					$customer_details = $quote->get_customer_details();
				if ( isset( $customer_details['email'] ) ) {
					$rfq_customer_email = wp_kses_post( $customer_details['email'] );
					$rfq_customer       = get_user_by( 'email', $rfq_customer_email );
					$_role              = (array) $rfq_customer->roles[0];
				}
				?>
				<?php if ( $product->is_type( 'variable' ) ) { ?>
					<span class="item_cost_span" id="actual_price_<?php echo esc_attr( $item->variation_id ); ?>" ><?php echo ( esc_attr( apply_filters( 'b2be_product_regular_price', wc_get_product( $item->variation_id )->get_price(), $product, true, $_role ) ) ); ?></span>
				<?php } else { ?>
					<span class="item_cost_span" id="actual_price_<?php echo esc_attr( $product->get_id() ); ?>" ><?php echo ( esc_attr( apply_filters( 'b2be_product_regular_price', $product->get_price(), $product, true, $_role ) ) ); ?></span>
				<?php } ?>
			</div>
		</td>
		<td class="quantity">
			<div class="view">
			</div>
			<div class="edit" >
				<?php if ( $product->is_type( 'variable' ) ) { ?>
					<?php
					/*
					@name: b2be_quantity_input_step
					@desc: Modify Quote Line item quantity.
					@param: (int) $quantity Quote Line Item Quantity.
					@param: (object) $product Quote Line Item Object.
					@package: b2b-ecommerce-for-woocommerce
					@module: request for quote
					@type: filter
					*/
					?>
					<input type="number" id="edit_qty_field_value_<?php echo esc_attr( $item->variation_id ); ?>" style="width: 60px;margin-right: 10px;margin-left: 10px;" step="<?php echo esc_attr( apply_filters( 'b2be_quantity_input_step', '1', $product ) ); ?>" min="0" autocomplete="off" name="order_item_qty[<?php echo absint( $item_id ); ?>]" placeholder="0" value="<?php echo esc_attr( $item->qty ); ?>" data-qty="<?php echo esc_attr( $item->qty ); ?>" size="4" class="input-quantity" />
				<?php } else { ?>
					<input type="number" id="edit_qty_field_value_<?php echo esc_attr( $product->get_id() ); ?>" style="width: 60px;margin-right: 10px;margin-left: 10px;" step="<?php echo esc_attr( apply_filters( 'b2be_quantity_input_step', '1', $product ) ); ?>" min="0" autocomplete="off" name="order_item_qty[<?php echo absint( $item_id ); ?>]" placeholder="0" value="<?php echo esc_attr( $item->qty ); ?>" data-qty="<?php echo esc_attr( $item->qty ); ?>" size="4" class="input-quantity" />
				<?php } ?>
			</div>
			<div class="refund" style="display: none;">
				<input type="number" step="<?php echo esc_attr( apply_filters( 'b2be_quantity_input_step', '1', $product ) ); ?>" min="0" max="<?php echo absint( $item->qty ); ?>" autocomplete="off" name="refund_order_item_qty[<?php echo absint( $item_id ); ?>]" placeholder="0" size="4" class="refund_order_item_qty" />
			</div>
		</td>
		<td class="actual_total" data-sort-value="<?php echo esc_attr( $item->total ); ?>">
			<div class="view">
				<?php
				$refunded = 0;
				if ( $refunded ) {
					echo '<small class="refunded">-' . wp_kses_post( wc_price( $refunded, array( 'currency' => $order->get_currency() ) ) ) . '</small>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
				?>
			</div>
			<div class="edit">
				<div class="split-input">
					<div class="input"  style="display: none;">
						<label><?php esc_attr_e( 'Before discount', 'b2b-ecommerce' ); ?></label>
						<input type="text" name="line_subtotal[<?php echo absint( $item_id ); ?>]" placeholder="<?php echo esc_attr( wc_format_localized_price( 0 ) ); ?>" value="<?php echo esc_attr( wc_format_localized_price( $item->total ) ); ?>" class="line_subtotal wc_input_price" data-subtotal="<?php echo esc_attr( wc_format_localized_price( $item->subtotal ) ); ?>" />
					</div>
					<div class="input">
						<?php $item_total[] = $item->total; ?>
						<?php if ( $product->is_type( 'variable' ) ) { ?>
							<p class="item_subtotal_<?php echo esc_attr( $item->variation_id ); ?>" ><?php echo esc_attr( wc_format_localized_price( wc_get_product( $item->variation_id )->get_price() * $item->qty ) ); ?>
						<?php } else { ?>
							<p class="item_subtotal_<?php echo esc_attr( $product->get_id() ); ?>" ><?php echo esc_attr( wc_format_localized_price( $product->get_price() * $item->qty ) ); ?>
						<?php } ?>
					</div>
				</div>
			</div>
			<div class="refund" style="display: none;">
				<input type="text" name="refund_line_total[<?php echo absint( $item_id ); ?>]" placeholder="<?php echo esc_attr( wc_format_localized_price( 0 ) ); ?>" class="refund_line_total wc_input_price" />
			</div>
		</td>

		<td class="line_cost" data-sort-value="<?php echo esc_attr( $item->total ); ?>">
			<div class="view">
				<?php
				$refunded = 0;
				if ( $refunded ) {
					echo '<small class="refunded">-' . wp_kses_post( wc_price( $refunded, array( 'currency' => $order->get_currency() ) ) ) . '</small>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
				?>
			</div>
			<div class="edit">
				<div class="split-input">
					<div class="input"  style="display: none;">
						<label><?php esc_attr_e( 'Before discount', 'b2b-ecommerce' ); ?></label>
						<input type="text" name="line_subtotal[<?php echo absint( $item_id ); ?>]" placeholder="<?php echo esc_attr( wc_format_localized_price( 0 ) ); ?>" value="<?php echo esc_attr( wc_format_localized_price( $item->total ) ); ?>" class="line_subtotal wc_input_price" data-subtotal="<?php echo esc_attr( wc_format_localized_price( $item->subtotal ) ); ?>" />
					</div>
					<div class="input">
						<?php $item_total[] = $item->total; ?>
						<?php if ( $product->is_type( 'variable' ) ) { ?>
							<input id="edit_price_field_value_<?php echo absint( $item->variation_id ); ?>" style="width: 60px;margin-right: 10px;margin-left: 10px;" type="number" name="line_total[<?php echo absint( $item_id ); ?>]" placeholder="<?php echo esc_attr( wc_format_localized_price( 0 ) ); ?>" min="0" value="<?php echo esc_attr( wc_format_localized_price( $item->total ) ); ?>" class="line_total wc_input_price" data-tip="<?php esc_attr_e( 'After pre-tax discounts.', 'b2b-ecommerce' ); ?>" data-total="<?php echo esc_attr( wc_format_localized_price( $item->total ) ); ?>" />
						<?php } else { ?>
							<input id="edit_price_field_value_<?php echo absint( $product->get_id() ); ?>" style="width: 60px;margin-right: 10px;margin-left: 10px;" type="number" name="line_total[<?php echo absint( $item_id ); ?>]" placeholder="<?php echo esc_attr( wc_format_localized_price( 0 ) ); ?>" min="0" value="<?php echo esc_attr( wc_format_localized_price( $item->total ) ); ?>" class="line_total wc_input_price" data-tip="<?php esc_attr_e( 'After pre-tax discounts.', 'b2b-ecommerce' ); ?>" data-total="<?php echo esc_attr( wc_format_localized_price( $item->total ) ); ?>" />
						<?php } ?>
					</div>
				</div>
			</div>
			<div class="refund" style="display: none;">
				<input type="text" name="refund_line_total[<?php echo absint( $item_id ); ?>]" placeholder="<?php echo esc_attr( wc_format_localized_price( 0 ) ); ?>" class="refund_line_total wc_input_price" />
			</div>
		</td>


		<?php
		$tax_data = false;

		if ( $tax_data ) {
			foreach ( $order_taxes as $tax_item ) {
				$tax_item_id       = $tax_item->get_rate_id();
				$tax_item_total    = isset( $tax_data['total'][ $tax_item_id ] ) ? $tax_data['total'][ $tax_item_id ] : '';
				$tax_item_subtotal = isset( $tax_data['subtotal'][ $tax_item_id ] ) ? $tax_data['subtotal'][ $tax_item_id ] : '';
				?>
				<td class="line_tax">
					<div class="view">
						<?php
						if ( '' !== $tax_item_total ) {
							echo wp_kses_post( wc_price( wc_round_tax_total( $tax_item_total ), array( 'currency' => $order->get_currency() ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						} else {
							echo '&ndash;';
						}

						$refunded = $order->get_tax_refunded_for_item( $item_id, $tax_item_id );

						if ( $refunded ) {
							echo '<small class="refunded">-' . wp_kses_post( wc_price( $refunded, array( 'currency' => $order->get_currency() ) ) ) . '</small>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						}
						?>
					</div>
					<div class="edit" style="display: none;">
						<div class="split-input">
							<div class="input">
								<label><?php esc_attr_e( 'Before discount', 'b2b-ecommerce' ); ?></label>
								<input type="text" name="line_subtotal_tax[<?php echo absint( $item_id ); ?>][<?php echo esc_attr( $tax_item_id ); ?>]" placeholder="<?php echo esc_attr( wc_format_localized_price( 0 ) ); ?>" value="<?php echo esc_attr( wc_format_localized_price( $tax_item_subtotal ) ); ?>" class="line_subtotal_tax wc_input_price" data-subtotal_tax="<?php echo esc_attr( wc_format_localized_price( $tax_item_subtotal ) ); ?>" data-tax_id="<?php echo esc_attr( $tax_item_id ); ?>" />
							</div>
							<div class="input">
								<label><?php esc_attr_e( 'Total', 'b2b-ecommerce' ); ?></label>
								<input type="text" name="line_tax[<?php echo absint( $item_id ); ?>][<?php echo esc_attr( $tax_item_id ); ?>]" placeholder="<?php echo esc_attr( wc_format_localized_price( 0 ) ); ?>" value="<?php echo esc_attr( wc_format_localized_price( $tax_item_total ) ); ?>" class="line_tax wc_input_price" data-total_tax="<?php echo esc_attr( wc_format_localized_price( $tax_item_total ) ); ?>" data-tax_id="<?php echo esc_attr( $tax_item_id ); ?>" />
							</div>
						</div>
					</div>
					<div class="refund" style="display: none;">
						<input type="text" name="refund_line_tax[<?php echo absint( $item_id ); ?>][<?php echo esc_attr( $tax_item_id ); ?>]" placeholder="<?php echo esc_attr( wc_format_localized_price( 0 ) ); ?>" class="refund_line_tax wc_input_price" data-tax_id="<?php echo esc_attr( $tax_item_id ); ?>" />
					</div>
				</td>
				<?php
			}
		}
		?>
		<td class="wc-order-edit-line-item">
			<div class="wc-order-edit-line-item-actions">
				<?php if ( true ) : ?>
					<a style="cursor:pointer;" class="delete-order-item tips" id="<?php echo absint( $product->get_id() ); ?>" data-tip="<?php esc_attr_e( 'Delete item', 'b2b-ecommerce' ); ?>">X</a>
				<?php endif; ?>
			</div>
		</td>
	</tr>
<?php } ?>
