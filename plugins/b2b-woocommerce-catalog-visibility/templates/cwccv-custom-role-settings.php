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
	<div id="message" class="error inline"><p><strong><?php esc_html_e( 'Role already exist', 'codup-woocommerce-catalog-visibility' ); ?></strong></p></div>
	<?php
}
?>
</div>
<p>&nbsp;</p>
<h2>
	<?php esc_html_e( 'User Roles', 'codup-woocommerce-catalog-visibility' ); ?>
</h2>
<div class="role_setting" style="display: none">    
	<table class="cwccv_role_and_group_table">
		<?php if ( isset( $user_roles ) && ! empty( $user_roles ) ) { ?>
			<form action="" method="POST">
				<?php foreach ( $user_roles as $key => $value ) { ?>
					<tr>
						<td class="user_role_name"><?php echo esc_attr( $value ); ?></td>
						<td>
							<button type="button" class="<?php echo esc_attr( CWCCV_PLUGIN_PREFIX ); ?>_edit_role_button" title="<?php esc_attr_e( 'Edit', 'codup-woocommerce-catalog-visibility' ); ?>"><i class="dashicons dashicons-edit" aria-hidden="true"></i></button>
							<button type="button" class="<?php echo esc_attr( CWCCV_PLUGIN_PREFIX ); ?>_save_role_button button-success" title="<?php esc_attr_e( 'Save', 'codup-woocommerce-catalog-visibility' ); ?>"><i class="dashicons dashicons-yes" aria-hidden="true"></i></button>
							<button type="button" class="<?php echo esc_attr( CWCCV_PLUGIN_PREFIX ); ?>_cancel_role_button button-grey" title="<?php esc_attr_e( 'Cancel', 'codup-woocommerce-catalog-visibility' ); ?>" data-name="<?php echo esc_attr( $value ); ?>"><i class="dashicons dashicons-no" aria-hidden="true"></i></button>
							<button type="button" class="<?php echo esc_attr( CWCCV_PLUGIN_PREFIX ); ?>_delete_role_button button-danger" title="<?php esc_attr_e( 'Delete', 'codup-woocommerce-catalog-visibility' ); ?>"><i class="dashicons dashicons-trash" aria-hidden="true"></i></button>
						</td>
					</tr>
				<?php } ?>
			</form>
		<?php } ?>
	</table>
	<hr>
	<table class="cwccv_tbl_add_role">
		<tr>
			<td>
				<h3 class="cwccv_role_sub_title">
					<?php esc_html_e( 'Add Role', 'codup-woocommerce-catalog-visibility' ); ?>
				</h3>
			</td>
		</tr>
		<tr>
			<td>
				<?php CWCCV_Helper::get_text_field( $field_args['fields']['add_new_role_text_field'] ); ?>
			</td>
		</tr>
	</table>
</div>
