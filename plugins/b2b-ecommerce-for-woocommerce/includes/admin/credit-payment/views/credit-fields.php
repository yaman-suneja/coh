<?php
/**
 * Template for the Credit field.
 *
 * @package B2b Ecommerce For Woocommerce/Credit Payments Field.
 */

// prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$checked = get_post_meta( $post_id, 'enable_b2b_credit_payment', true, 'false' );
if ( 'on' == $checked ) {
	$checked = 'checked';
}

?>
<p>
	<span>
		<?php echo esc_html__( 'Enable Credit Payment ', 'b2b-ecommerce' ); ?>
	</span>
	<input type="checkbox" name="enable_b2b_credit_payment" <?php echo esc_attr( $checked ); ?> />
</p>
<span id="ccr_credit_payment_heading">
	<?php echo esc_html__( 'Award Credit to Users of this Role ', 'b2b-ecommerce' ); ?>
</span>
<input type="number" name="ccr_credit_value" id="ccr_credit_value" min="0" >
<p>
	<?php echo esc_html__( 'Credit Assign :', 'b2b-ecommerce' ); ?>
	<span>
		<b>
			<?php echo esc_html( b2be_get_total_assign_credit_in_role( $post_id ) ); ?>
		</b>
	</span>
</p>
<p>
	<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=codup-custom-roles&page=credit-logs&role_id=' . $post->ID ) ); ?>" target="_blank" class="button button-primary" > 
		<?php esc_html_e( 'View Logs', 'b2b-ecommerce' ); ?> 
	</a>
</p>

