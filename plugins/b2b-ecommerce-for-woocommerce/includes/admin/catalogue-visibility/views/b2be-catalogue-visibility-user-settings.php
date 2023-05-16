<?php
/**
 * File b2be_catalogue-admin-settings-user-settings.php.
 *
 * @package templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<table class="form-table">
	<tr>
		<th>
			<label for="">
				<?php esc_html_e( 'Catalog Visibility User Groups', 'codup-woocommerce-catalog-visibility' ); ?>
			</label>
		</th>
		<td>
			<select name="b2be_catalogue_user_groups_select[]" id="b2be_catalogue_user_groups_select" class="b2be_catalogue_user_selectpicker" multiple>
				<?php if ( $field_args['user_groups_select']['option'] ) { ?>
					<?php foreach ( $field_args['user_groups_select']['option'] as $key => $value ) { ?>
						<?php $selected_group = isset( $field_args['user_groups_select']['selected_option_value'] ) && in_array( $value, $field_args['user_groups_select']['selected_option_value'], true ) ? 'selected="selected"' : ''; ?>
						<option value="<?php echo esc_attr( $value ); ?>" <?php echo esc_attr( $selected_group ); ?> > <?php echo wp_kses_post( $value ); ?></option>
					<?php } ?>
				<?php } ?>
			</select>
		</td>
	</tr>
</table>
