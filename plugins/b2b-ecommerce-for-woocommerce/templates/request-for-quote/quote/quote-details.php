<?php
/**
 * Quote Details
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

$quote = wc_get_quote( $quote_id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.OverrideProhibited

if ( ! $quote ) {
	return;
}

$quote_items = $quote->get_quote_items();
// $show_purchase_note    = $quote->has_status( apply_filters( 'b2be_purchase_note_quote_statuses', array( 'completed', 'processing' ) ) );.

?>
<section class="woocommerce-quote-details">
	<?php
	/*
	@name: b2be_quote_details_before_quote_table
	@desc: Runs before quote table on View quotes page on my account.
	@param: (object) $quote Current Quote object.
	@package: b2b-ecommerce-for-woocommerce
	@module: request for quote
	@type: action
	*/
	?>
	<?php do_action( 'b2be_quote_details_before_quote_table', $quote ); ?>

	<h2 class="woocommerce-quote-details__title"><?php esc_html_e( 'Quote details', 'b2b-ecommerce' ); ?></h2>

	<table class="woocommerce-table woocommerce-table--quote-details shop_table quote_details">

		<thead>
			<tr>
				<th class="woocommerce-table__product-name product-name"><?php esc_html_e( 'Product', 'b2b-ecommerce' ); ?></th>
				<th class="woocommerce-table__product-table product-subtotal"><?php esc_html_e( 'Actual Total', 'b2b-ecommerce' ); ?></th>
				<th class="woocommerce-table__product-table product-total"><?php esc_html_e( 'Quoted Total', 'b2b-ecommerce' ); ?></th>
			</tr>
		</thead>

		<tbody>
			<?php
			/*
			@name: b2be_quote_details_before_quote_table_items
			@desc: Runs before quote table items on View quotes page on my account.
			@param: (object) $quote Current Quote object.
			@package: b2b-ecommerce-for-woocommerce
			@module: request for quote
			@type: action
			*/
			do_action( 'b2be_quote_details_before_quote_table_items', $quote );
			foreach ( $quote_items as $item_id => $item ) {
							$product = wc_get_product( $item['item_product_id'] );

							wc_get_template(
								'quote/quote-details-item.php',
								array(
									'quote'   => $quote,
									'item_id' => $item_id,
									'item'    => $item,

									'product' => $product,
								),
								'b2b-ecommerce-for-woocommerce',
								CWRFQ_TEMPLATE_DIR
							);
			}

			/*
			@name: b2be_quote_details_after_quote_table_items
			@desc: Runs after quote table items on View quotes page on my account.
			@param: (object) $quote Current Quote object.
			@package: b2b-ecommerce-for-woocommerce
			@module: request for quote
			@type: action
			*/
			do_action( 'woocommerce_quote_details_after_quote_table_items', $quote );
			?>
		</tbody>

		<tfoot>
		</tfoot>
	</table>
	<?php
	/*
	@name: woocommerce_quote_details_after_quote_table
	@desc: Runs after quote table on View quotes page on my account.
	@param: (object) $quote Current Quote object.
	@package: b2b-ecommerce-for-woocommerce
	@module: request for quote
	@type: action
	*/
	?>
	<?php do_action( 'b2be_quote_details_after_quote_table', $quote ); ?>
</section>

<?php
$show_customer_details = true;
if ( $show_customer_details ) {
	wc_get_template( 'quote/quote-details-customer.php', array( 'quote' => $quote ), 'b2b-ecommerce-for-woocommerce', CWRFQ_TEMPLATE_DIR );
}
