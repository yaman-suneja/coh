<?php
/**
 * Quote Details Item
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

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<tr class="<?php echo esc_attr( apply_filters( 'b2be_quote_item_class', 'woocommerce-table__line-item quote_item', $item, $quote ) ); ?>">

	<td class="woocommerce-table__product-name product-name">
		<?php
		$is_visible        = $product && $product->is_visible();
		$product_permalink = apply_filters( 'b2be_quote_item_permalink', $is_visible ? $product->get_permalink( $item ) : '', $item, $quote );

		echo wp_kses_post( apply_filters( 'b2be_quote_item_name', $product_permalink ? sprintf( '<a href="%s">%s</a>', $product_permalink, $product->get_name() ) : $item->get_name(), $item, $is_visible ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		$qty          = $item['qty'];
		$refunded_qty = 0;

		if ( $refunded_qty ) {
			$qty_display = '<del>' . esc_html( $qty ) . '</del> <ins>' . esc_html( $qty - ( $refunded_qty * -1 ) ) . '</ins>';
		} else {
			$qty_display = esc_html( $qty );
		}

		echo wp_kses_post( apply_filters( 'b2be_quote_item_quantity_html', ' <strong class="product-quantity">' . sprintf( '&times; %s', $qty_display ) . '</strong>', $item ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		do_action( 'woocommerce_quote_item_meta_start', $item_id, $item, $quote, false );

		// wc_display_item_meta( $item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped.

		do_action( 'woocommerce_quote_item_meta_end', $item_id, $item, $quote, false );
		?>
	</td>

	<td class="woocommerce-table__product-total product-total">
		<?php echo wp_kses_post( wp_filter_post_kses( wc_price( $item['subtotal'] ) ) );// echo $quote->get_formatted_line_subtotal( $item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped. ?>
	</td>

	<td class="woocommerce-table__product-total product-total">
		<?php echo wp_kses_post( wp_filter_post_kses( wc_price( $item['total'] ) ) );// echo $quote->get_formatted_line_subtotal( $item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped. ?>
	</td>

</tr>

