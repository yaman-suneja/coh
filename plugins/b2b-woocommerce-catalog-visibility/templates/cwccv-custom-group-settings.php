<?php
/**
 * File cwccv-custom-group-settings.
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
	<div id="message" class="error inline"><p><strong>Group already exist</strong></p></div>
	<?php
}
?>
</div>
<p>&nbsp;</p>
<h2>
	<?php esc_html_e( 'User Groups', 'codup-woocommerce-catalog-visibility' ); ?>
</h2>
<div class="group_setting" style="display: none">    
	<table class="cwccv_role_and_group_table">
		<?php if ( isset( $user_groups ) && ! empty( $user_groups ) ) { ?>
			<form action="" method="POST">
				<?php foreach ( $user_groups as $key => $value ) { ?>
					<tr>
						<td class="user_group_name"><?php echo esc_attr( $value ); ?></td>
						<td>
							<button type="button" class="<?php echo esc_attr( CWCCV_PLUGIN_PREFIX ); ?>_edit_group_button"  title="<?php esc_attr_e( 'Edit', 'codup-woocommerce-catalog-visibility' ); ?>"><i class="dashicons dashicons-edit" aria-hidden="true"></i></button>
							<button type="button" class="<?php echo esc_attr( CWCCV_PLUGIN_PREFIX ); ?>_save_group_button button-success" hidden="hidden" title="<?php esc_attr_e( 'Save', 'codup-woocommerce-catalog-visibility' ); ?>"><i class="dashicons dashicons-yes" aria-hidden="true"></i></button>
							<button type="button" class="<?php echo esc_attr( CWCCV_PLUGIN_PREFIX ); ?>_cancel_group_button button-grey" hidden="hidden" title="<?php esc_attr_e( 'Cancel', 'codup-woocommerce-catalog-visibility' ); ?>" data-name="<?php echo esc_attr( $value ); ?>"><i class="dashicons dashicons-no" aria-hidden="true"></i></button>
							<button type="button" class="<?php echo esc_attr( CWCCV_PLUGIN_PREFIX ); ?>_delete_group_button button-danger" title="<?php esc_attr_e( 'Delete', 'codup-woocommerce-catalog-visibility' ); ?>"><i class="dashicons dashicons-trash" aria-hidden="true"></i></button>
						</td>
					</tr>
				<?php } ?>
			</form>
		<?php } ?>
	</table>
	<hr>
	<table class="cwccv_tbl_add_group">
		<tr>
			<td>
				<h3 class="cwccv_role_sub_title" >
					<?php esc_html_e( 'Add Group', 'codup-woocommerce-catalog-visibility' ); ?>
				</h3>
			</td>
		</tr>
		<tr>
			<td>
				<?php CWCCV_Helper::get_text_field( $field_args['fields']['add_new_group_text_field'] ); ?>
			</td>
		</tr>
	</table>
</div>
