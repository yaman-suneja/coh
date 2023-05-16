<?php
/**
 * Template for getbalance form.
 *
 * @package balance-integration\balance-checkout.php
 */

get_header();
?>
<style>
	#blnce-checkout {
		z-index: 9999;
		position: relative;
	}
</style>
<div class="woocommerce-order">
	<?php
	$order_id   = isset( $_GET['order_id'] ) ? intval( $_GET['order_id'] ) : 0; // Get the order ID...
	$curr_order = wc_get_order( $order_id ); // Get an instance of the WC_Order object...
	if ( $curr_order ) :

		do_action( 'woocommerce_before_thankyou', $curr_order->get_id() );
		do_action( 'woocommerce_thankyou_' . $curr_order->get_payment_method(), $curr_order->get_id() );
		do_action( 'woocommerce_thankyou', $curr_order->get_id() );

	else :
		?>

		<p class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received">
			<?php echo wp_kses_post( apply_filters( 'woocommerce_thankyou_order_received_text', esc_html__( 'Thank you. Your order has been received.', 'b2b-ecommerce' ), null ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped... ?>
		</p>

	<?php endif; ?>

</div>
<div id="blnce-checkout"></div>

<?php
get_footer();
