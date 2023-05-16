<?php
/**
 * Quote Comment Meta.
 *
 * @package b2b-ecommerce-for-woocomerce
 */

defined( 'ABSPATH' ) || exit;
?>
<?php
	$comments_content = get_comments(
		array(
			'post_id' => $quote_id,
			'order'   => 'asc',
		)
	);
	?>
<div style="padding:20px">
	<div style="overflow-y: scroll;height:<?php echo ( $comments_content ) ? '300px' : 'auto'; ?>;" class="rfq_customer_comments_box" id="rfq_customer_comments_box">
		<table id="rfq_admin_comment_box" >
			<tbody>
				<?php
				foreach ( $comments_content as $comment_text ) {
					?>
						<tr >
							<td id="rfq_admin_comment_box_author_column" >
								<div id="rfq_admin_comment_box_author_column_div" >
									<strong><?php echo esc_html( $comment_text->comment_author ); ?></strong>
								</div>
							</td>
							<td id="rfq_admin_comment_box_comment_column" >
								<div style="width: 380px;" id="rfq_admin_comment_box_comment_column_div" >
								<?php echo esc_html( $comment_text->comment_content ); ?>
								</div>
							</td>
						</tr>
					<?php
				}
				?>
			</tbody>
		</table>
	</div><br>

	<?php $current_rfq_user = get_current_user(); ?>

	<form method="post" id="customer_comment_form">
		<?php wp_nonce_field( 'admin-comment-meta', 'admin-comment-meta-action' ); ?>
		<textarea style="width:100%" name="rfq_admin_comment_textarea" id="rfq_admin_comment_textarea" cols="30" rows="5"></textarea><br><br>
		<input type="hidden" name="rfq_admin_comment_quote_id" id="rfq_admin_comment_quote_id" value="<?php echo esc_html( $quote_id ); ?>">
		<input type="hidden" name="rfq_admin_comment_author" id="rfq_admin_comment_author" value="<?php echo esc_html( $current_rfq_user ); ?>">
		<input type="button" name="rfq_admin_comment_submit_button" id="rfq_admin_comment_submit_button" value="<?php echo esc_html__( 'Send', 'b2b-ecommerce' ); ?>">
	</form>
</div>
