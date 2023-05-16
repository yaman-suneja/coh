<?php
/**
 * File cwccv-custom-role-settings.
 *
 * @package templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<div class="status">
<?php
if ( WC()->session->get( CWCCV_PLUGIN_PREFIX . '_errors' ) ) {
	WC()->session->__unset( CWCCV_PLUGIN_PREFIX . '_errors' );
	?>
	<div id="message" class="error inline"><p><strong><?php echo wp_kses_post( $invalid_token ); ?></strong></p></div>
	<?php
}
?>
</div>
<h2>
	<?php esc_html_e( 'Location API Token', 'codup-woocommerce-catalog-visibility' ); ?>
</h2>
<div id="codup-rfq_section_title-description">
	<p>
		<?php echo wp_kses_post( __( 'Adding the token here will grant you "50000" requests per month for the geo location feature.To get this token ', 'codup-woocommerce-catalog-visibility' ) ); ?>
		<a target="_blank" href="https://ipinfo.io/signup"><?php echo wp_kses_post( __( 'Signup here', 'codup-woocommerce-catalog-visibility' ) ); ?></a>
		<?php echo wp_kses_post( __( ' for free.', 'codup-woocommerce-catalog-visibility' ) ); ?>
	</p>
</div>
<div class="api_setting">    
	<table class="form-table cwccv_tbl_location_api_token">
		<tr valign="top">
			<th>
				<label class="cwccv_location_api_token_sub_title">
					<?php esc_html_e( 'Add Token', 'codup-woocommerce-catalog-visibility' ); ?>
				</label>
			</th>
			<td>
				<?php $field_args['fields']['add_location_api_token']['value'] = get_option( CWCCV_PLUGIN_PREFIX . '_location_api_token' ); ?>
				<?php CWCCV_Helper::get_text_field( $field_args['fields']['add_location_api_token'] ); ?>
			</td>
		</tr>
	</table>
</div>
