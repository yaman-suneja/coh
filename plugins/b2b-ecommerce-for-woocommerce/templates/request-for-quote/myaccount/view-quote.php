<?php
/**
 * View Order
 *
 * Shows the details of a particular order on the account page.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/view-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.0.0
 */

defined( 'ABSPATH' ) || exit;

// $notes = $order->get_customer_order_notes();
?>
<p>
<?php
printf(
	/* translators: 1: order number 2: order date 3: order status */
	esc_html__( 'Quote #%1$s was submitted on %2$s and is currently %3$s.', 'b2b-ecommerce' ),
	'<mark class="order-number">' . wp_kses_post( $quote->get_quote_number() ) . '</mark>', // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	'<mark class="order-date">' . wp_kses_post( wc_format_datetime( $quote->get_date_created() ) ) . '</mark>', // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	'<mark class="order-status">' . wp_kses_post( wc_get_quote_status_name( $quote->get_status() ) ) . '</mark>' // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
);
?>
</p>
<?php
/*
@name: cwrfq_view_quote
@desc: Runs before Quote title on View quotes page on my account.
@param: (int) $quote_id Current Quote Id.
@package: b2b-ecommerce-for-woocommerce
@module: request for quote
@type: action
*/
?>
<?php do_action( 'cwrfq_view_quote', $quote_id ); ?>
<?php
	$comments_content = get_comments(
		array(
			'post_id' => $quote_id,
			'order'   => 'asc',
		)
	);
	?>
<h2 class="woocommerce-column__title"><?php esc_html_e( 'Messages', 'b2b-ecommerce' ); ?></h2>
<div style="padding:20px;background-color: rgba(0, 0, 0, 0.0125);">
	<div style="overflow-y: scroll;height:<?php echo ( $comments_content ) ? '300px' : 'auto'; ?>;" class="rfq_customer_comments_box" id="rfq_customer_comments_box">
		<table id="rfq_admin_comment_box" >
			<tbody>
				<?php
				foreach ( $comments_content as $comment_text ) {
					?>
						<tr >
							<td id="rfq_admin_comment_box_author_column" >
								<div id="rfq_admin_comment_box_author_column_div" >
									<strong><?php echo wp_kses_post( $comment_text->comment_author ); ?></strong>
								</div>
							</td>
							<td id="rfq_admin_comment_box_comment_column" >
								<div style="width: 380px;" id="rfq_admin_comment_box_comment_column_div" >
								<?php echo wp_kses_post( $comment_text->comment_content ); ?>
								</div>
								<div style="font-size: 13px;color: rosybrown;width:30%;float:right" id="rfq_admin_comment_box_comment_column_time_div" >
								<?php echo wp_kses_post( comment_date( 'n-j-Y H:i:s', $comment_text->comment_ID ) ); ?>
								</div>
							</td>
						</tr>
					<?php
				}
				?>
			</tbody>
		</table>
	</div><br>

	<?php
	if ( is_admin() ) {
		$current_rfq_user = get_current_user();
	} else {
		$current_rfq_user = get_post_meta( $quote->get_id(), 'first_name', true );
	}
	?>

	<form method="post" id="customer_comment_form">
		<?php wp_nonce_field( 'admin-comment-meta', 'admin-comment-meta-action' ); ?>
		<textarea style="width:100%" name="rfq_customer_comment_textarea" id="rfq_customer_comment_textarea" cols="30" rows="5"></textarea><br><br>
		<input type="hidden" name="rfq_customer_comment_quote_id" id="rfq_customer_comment_quote_id" value="<?php echo wp_kses_post( $quote_id ); ?>">
		<input type="hidden" name="rfq_customer_comment_author" id="rfq_customer_comment_author" value="<?php echo wp_kses_post( $current_rfq_user ); ?>">
		<input type="button" name="rfq_customer_comment_submit_button" id="rfq_customer_comment_submit_button" value="<?php esc_attr_e( 'Send', 'codup-wcrfq' ); ?>">
	</form>
</div>
