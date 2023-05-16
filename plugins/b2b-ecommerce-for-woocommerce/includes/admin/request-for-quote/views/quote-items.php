<?php
/**
 * Quote Items Template.
 *
 * @package codupio-request-for-quote-d659b8ba1ef2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$line_items = array();
$product    = get_product();
?>
<div class="">
	<table cellpadding="0" cellspacing="0" class="">
		<thead>
			<tr>
				<th colspan="2"  ><?php esc_html_e( 'Item', 'b2b-ecommerce' ); ?></th>
				<th><?php esc_html_e( 'Cost', 'b2b-ecommerce' ); ?></th>
				<th><?php esc_html_e( 'Total', 'b2b-ecommerce' ); ?></th>
				<th><?php esc_html_e( 'Total', 'b2b-ecommerce' ); ?></th>
				<th width="1%">&nbsp;</th>
			</tr>
		</thead>
		<tbody id="">
			<tr class="item " data-order_item_id="1">
				<td class="thumb">
					<div class="wc-order-item-thumbnail"></div>	</td>
				<td class="name" data-sort-value="Gluten free salad">
					<a href="http://localhost/rfq/wp-admin/post.php?post=10&amp;action=edit" class="wc-order-item-name">Gluten free salad</a><input type="hidden" class="order_item_id" name="order_item_id[]" value="1">
					<input type="hidden" name="order_item_tax_class[1]" value="">

					<div class="view">
					</div>
					<div class="edit" style="display: none;">
						<table class="meta" cellspacing="0">
							<tbody class="meta_items">
							</tbody>
							<tfoot>
								<tr>
									<td colspan="4"><button class="add_order_item_meta button">Add&nbsp;meta</button></td>
								</tr>
							</tfoot>
						</table>
					</div>
				</td>


				<td class="item_cost" width="1%" data-sort-value="1.50">
					<div class="view">
						<span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">$</span>1.50</span>		</div>
				</td>
				<td class="quantity" width="1%">
					<div class="view">
						<small class="times">�</small> 1		</div>
					<div class="edit" style="display: none;">
						<input type="number" step="1" min="0" autocomplete="off" name="order_item_qty[1]" placeholder="0" value="1" data-qty="1" size="4" class="quantity">
					</div>
					<div class="refund" style="display: none;">
						<input type="number" step="1" min="0" max="1" autocomplete="off" name="refund_order_item_qty[1]" placeholder="0" size="4" class="refund_order_item_qty">
					</div>
				</td>
				<td class="line_cost" width="1%" data-sort-value="1.5">
					<div class="view">
						<span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">$</span>1.50</span>		</div>
					<div class="edit" style="display: none;">
						<div class="split-input">
							<div class="input">
								<label>Before discount</label>
								<input type="text" name="line_subtotal[1]" placeholder="0" value="1.5" class="line_subtotal wc_input_price" data-subtotal="1.5">
							</div>
							<div class="input">
								<label>Total</label>
								<input type="text" name="line_total[1]" placeholder="0" value="1.5" class="line_total wc_input_price" data-tip="After pre-tax discounts." data-total="1.5">
							</div>
						</div>
					</div>
					<div class="refund" style="display: none;">
						<input type="text" name="refund_line_total[1]" placeholder="0" class="refund_line_total wc_input_price">
					</div>
				</td>

				<td class="wc-order-edit-line-item" width="1%">
					<div class="wc-order-edit-line-item-actions">
					</div>
				</td>
			</tr>
			<?php
			foreach ( $line_items as $item_id => $item ) {

				include 'html-order-item.php';
			}
			?>
		</tbody>
	</table>
</div>    
