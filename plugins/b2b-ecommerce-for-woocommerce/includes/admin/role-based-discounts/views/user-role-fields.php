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

?>

<tr class="hide-on-disable" valign="top" >
		<th class="titledesc" style="text-align:center"><?php esc_html_e( 'User Role', 'codup-wcrfq' ); ?></th>
		<th class="titledesc role-discount-title" style="padding-left: 11px;text-align: center;"><?php esc_html_e( 'Discount (%)', 'codup-wcrfq' ); ?></th>
</tr>

<?php
$index = 0;
foreach ( $field_config['roles'] as $value ) {
	$b2b_e_user_role = str_replace( ' ', '_', strtolower( $value['role'] ) );
	?>
	<tr valign="top" class="cwl-tier-row" data-role-number="<?php echo esc_attr( $index ); ?>" >
		<td class="forminp forminp-<?php echo wp_kses_post( sanitize_title( $field_config['type'] ) ); ?> input-column" style="padding:0px;">
			<p style="float:left">
				<?php echo wp_kses_post( wc_help_tip( __( 'Enter the role name you want to enter', 'codup-wcrfq' ) ) ); ?>
			</p> 
			<input
				name="<?php echo esc_attr( $field_config['id'] ); ?>[<?php echo wp_kses_post( $index ); ?>][role]"
				type="text"
				dir="ltr"
				style="<?php echo esc_attr( $field_config['css'] ); ?>width: 90%;float: right;margin-bottom: 15px;"
				class="<?php echo esc_attr( $field_config['class'] ); ?> b2be-role-field"
				placeholder="<?php esc_attr_e( 'Enter User Role', 'codup-wcrfq' ); ?>"
				data-current-role="<?php echo esc_attr( $b2b_e_user_role ); ?>"
				value = "<?php echo esc_attr( $value['role'] ); ?>"
				autocomplete = 'off'
				required="required"
			/>
		</td>
		<td class="forminp forminp-<?php echo wp_kses_post( sanitize_title( $field_config['type'] ) ); ?> input-column" style="padding-top:0px;display:none;margin-left:2px;">
			<p style="float:left">
				<?php echo wp_kses_post( wc_help_tip( __( 'Enter a number between 0-100 for a percentage discount.', 'codup-wcrfq' ) ) ); ?>
			</p>
			<input
				name="<?php echo esc_attr( $field_config['id'] ); ?>[<?php echo wp_kses_post( $index ); ?>][discount]"
				type="text"
				style="<?php echo esc_attr( $field_config['css'] ); ?>width: 90%;float: right;"
				class="<?php echo esc_attr( $field_config['class'] ); ?>"
				placeholder="<?php esc_attr_e( 'Enter Discount', 'codup-wcrfq' ); ?>"
				data-current-discount="<?php echo esc_attr( $value['discount'] ); ?>"
				value = "<?php echo esc_attr( $value['discount'] ); ?>"
				autocomplete = 'off'
			/>
		</td>
		<td style="padding-top:0px;">
			<span class="dashicons dashicons-no-alt remove-tier" title="<?php esc_attr_e( 'Remove Role', 'codup-wcrfq' ); ?>"></span>
		</td>
	</tr>
		
	<?php
	$index++;
}

