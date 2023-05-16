<?php
/**
 * Template for the tier entry. Based on the WooCommerce file class-wc-admin-settings.php.
 *
 * @var $this CodupWooLoyaltyTiersFields
 * @var $field_config string[]
 * @package B2B_E-commerce_For_WooCommerce/templates
 */

// prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$index = 0;
foreach ( $field_config['payment_method'] as $key => $value ) {
	$b2b_e_payment_method   = str_replace( ' ', '_', strtolower( $value ) );
	$b2be_unique_payment_id = ( 0 !== $key ) ? $key : 'b2be_payment_' . mt_rand();
	?>
	<tr valign="top" class="b2be_payment_method" data-method-number="<?php echo esc_attr( $b2be_unique_payment_id ); ?>" >
		<th scope="row" class="titledesc">
			<?php if ( 0 == $index ) { ?>
				<label for="codup_payment_method_name"><?php echo esc_html__( 'Payment Method Name', 'b2b-ecommerce' ); ?><?php echo wp_kses_post( wc_help_tip( __( 'This will create a new payment method on your store according to the given name', 'b2b-ecommerce' ) ) ); ?></label>
			<?php } ?>		
		</th>
		<td class="forminp forminp-<?php echo wp_kses_post( sanitize_title( $field_config['type'] ) ); ?> input-column" style="padding-top:0px;padding-bottom:0px;">
			<input
				name="<?php echo esc_attr( $field_config['id'] ); ?>[<?php echo wp_kses_post( $b2be_unique_payment_id ); ?>]"
				type="text"
				dir="ltr"
				style="<?php echo esc_attr( $field_config['css'] ); ?>"
				class="<?php echo esc_attr( $field_config['class'] ); ?>"
				placeholder="Enter Method"
				data-current-method="<?php echo esc_attr( $b2b_e_payment_method ); ?>"
				value = "<?php echo esc_attr( $value ); ?>"
				autocomplete = 'off'
				required="required"
			/>
		</td>
		<td>
			<span class="dashicons dashicons-no-alt remove-method" title="Remove Method" style="cursor:pointer"></span>
		</td>
	</tr>
	<?php $index++; ?>
<?php } ?>
<!--Template Row To Clone-->
<tr valign="top" class="b2be_payment_method b2be-payment-template-row" data-method-number="" style="display:none;">
	<th scope="row" class="titledesc"></th>
	<td class="forminp forminp-<?php echo wp_kses_post( sanitize_title( $field_config['type'] ) ); ?> input-column" style="padding-top:0px;padding-bottom:0px;">
		<input
			type="text"
			dir="ltr"
			style="<?php echo esc_attr( $field_config['css'] ); ?>"
			class="<?php echo esc_attr( $field_config['class'] ); ?>"
			placeholder="Enter Method"
			data-current-method="<?php echo esc_attr( $b2b_e_payment_method ); ?>"
			value = "<?php echo esc_attr( $value ); ?>"
			autocomplete = 'off'
		/>
	</td>
	<td>
		<span class="dashicons dashicons-no-alt remove-method" title="Remove Method" style="cursor:pointer"></span>
	</td>
</tr>

