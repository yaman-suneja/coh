<?php
/**
 * Template for the tier entry. Based on the WooCommerce file class-wc-admin-settings.php.
 *
 * @package B2B_E-commerce_For_WooCommerce/templates
 */

// prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="b2be-signup-form-wrapper">
<div class="b2be-signup-form--fields__title">
			<h1>SignUp Form Generator</h1>
			<p>Paste this shortcode to generate SignUp Form: <code><strong>[b2be_signup_form]</strong></code></p>
		</div>
	<div class="b2be-signup-form-top-layer">
		<div class="b2be-signup-form-required-approval-wrapper">
			<table class="b2be-signup-form-required-approval-table form-table">
				<tbody>
					<tr valign="top">
						<th scope="row" style="width:210px" class="titledesc">Required Approval</th>
						<td class="forminp forminp-checkbox">
							<fieldset>
								<label for="b2be-sign-up-approval">
									<input name="b2be_sign_up_approval" class="b2be-sign-up-approval" id="b2be-sign-up-approval" type="checkbox" <?php echo ( 'yes' == get_option( 'codup_signup_admin_apporval' ) ) ? 'checked' : ''; ?>> 
									Enabling it will send the form for admin approval before the user can log in.
								</label>
							</fieldset>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<div class="b2be-signup-form-bottom-layer">
		<div class="b2be-signup-form-fields-wrapper" >
			<div class="b2be-signup-form-fields-main-toggler">
				<div style="padding-left: 10px;">
					<input type="checkbox" name="select_all" class="select-all-signup-form-field"> <a href="javascript:void(0)" id="delete_selected_fields" >Delete</a>
				</div>				
				<div>
					<a href="javascript:void(0)" id="expand_all_fields" >Expand All</a> / <a href="javascript:void(0)" id="close_all_fields" >Close All</a>
				</div>
			</div>
			<div class="b2be-signup-form-fields-inner-wrapper" id="sortable">
				<?php if ( $signup_fields ) : ?>
					<?php foreach ( $signup_fields as $key => $signup_field ) : ?>
						<div class="b2be-signup-form-field">
							<div class="b2be-signup-form-field__header">
								<div class="b2be-signup-form-field-header__label">
								<input type="checkbox" name="select_signup_from_field[<?php echo wp_kses_post( $key ); ?>][remove]" id="select_signup_form_field" class="select-signup-form-field"> <label for=""><span class="field-id"><?php echo wp_kses_post( $key ); ?></span>:<span class="field-name"> <?php echo isset( $signup_field['name'] ) ? wp_kses_post( $signup_field['name'] ) : ''; ?></span></label>
								</div>
								<div class="b2be_signup-form-field-header__icon">
									<span class="dashicons dashicons-arrow-down"></span>
								</div>
							</div>
							<div class="b2be-signup-form-field__body open">
								<div class="b2be-signup-form-field-name-wrapper b2be-signup-form-body-field-wrapper">
									<div class="b2be-signup-form-field-name__label field_labels">
										<label for="">Field Name</label>
									</div>
									<div class="b2be-signup-form-field-name__field">
										<input type="text" name="signup_form_field[<?php echo wp_kses_post( $key ); ?>][name]" id="signup_form_field_name" data-field-number="<?php echo wp_kses_post( $key ); ?>" value="<?php echo isset( $signup_field['name'] ) ? wp_kses_post( $signup_field['name'] ) : ''; ?>">
									</div>
								</div>
								<div class="b2be-signup-form-field-type-wrapper b2be-signup-form-body-field-wrapper">
									<div class="b2be-signup-form-field-type__label field_labels">
										<label for="">Field Type</label>
									</div>
									<div class="b2be-signup-form-field-type__field">
										<select name="signup_form_field[<?php echo wp_kses_post( $key ); ?>][type]" id="signup_form_field_type">
											<option value="">Select Field Type</option>
											<option <?php echo ( isset( $signup_field['type'] ) && 'text' == $signup_field['type'] ) ? 'selected' : ''; ?> value="text">Text</option>
											<option <?php echo ( isset( $signup_field['type'] ) && 'date' == $signup_field['type'] ) ? 'selected' : ''; ?> value="date">Date</option>
											<option <?php echo ( isset( $signup_field['type'] ) && 'number' == $signup_field['type'] ) ? 'selected' : ''; ?> value="number">Number</option>
											<option <?php echo ( isset( $signup_field['type'] ) && 'email' == $signup_field['type'] ) ? 'selected' : ''; ?> value="email">Email</option>
											<option <?php echo ( isset( $signup_field['type'] ) && 'role' == $signup_field['type'] ) ? 'selected' : ''; ?> value="role">Role</option>
											<option <?php echo ( isset( $signup_field['type'] ) && 'username' == $signup_field['type'] ) ? 'selected' : ''; ?> value="username">Username</option>
											<option <?php echo ( isset( $signup_field['type'] ) && 'fname' == $signup_field['type'] ) ? 'selected' : ''; ?> value="fname">First Name</option>
											<option <?php echo ( isset( $signup_field['type'] ) && 'lname' == $signup_field['type'] ) ? 'selected' : ''; ?> value="lname">Last Name</option>
											<option <?php echo ( isset( $signup_field['type'] ) && 'company' == $signup_field['type'] ) ? 'selected' : ''; ?> value="company">Company</option>
											<option <?php echo ( isset( $signup_field['type'] ) && 'country' == $signup_field['type'] ) ? 'selected' : ''; ?> value="country">Country</option>
											<option <?php echo ( isset( $signup_field['type'] ) && 'address_1' == $signup_field['type'] ) ? 'selected' : ''; ?> value="address_1">Address 1</option>
											<option <?php echo ( isset( $signup_field['type'] ) && 'address_2' == $signup_field['type'] ) ? 'selected' : ''; ?> value="address_2">Address 2</option>
											<option <?php echo ( isset( $signup_field['type'] ) && 'city' == $signup_field['type'] ) ? 'selected' : ''; ?> value="city">City</option>
											<option <?php echo ( isset( $signup_field['type'] ) && 'state' == $signup_field['type'] ) ? 'selected' : ''; ?> value="state">State</option>
											<option <?php echo ( isset( $signup_field['type'] ) && 'zip_code' == $signup_field['type'] ) ? 'selected' : ''; ?> value="zip_code">Zip Code</option>
											<option <?php echo ( isset( $signup_field['type'] ) && 'phone' == $signup_field['type'] ) ? 'selected' : ''; ?> value="phone">Phone</option>
										</select>
									</div>
								</div>
								<div class="b2be-signup-form-field-roles-wrapper" style="<?php echo ( 'role' == $signup_field['type'] ) ? '' : 'display:none'; ?>">
									<p class="b2be-signup-form-select-all-roles">
										<span>
											<?php echo wp_kses_post( __( 'Select the role you want to show', 'b2b-ecommerce' ) ); ?>
										</span>
									</p>
									<?php if ( wp_roles()->role_names ) : ?>
										<?php foreach ( wp_roles()->role_names as $role_id => $role_name ) : ?>
											<label for=""><input <?php echo ( isset( $signup_field['roles'] ) && in_array( $role_id, $signup_field['roles'] ) ) ? 'checked' : ''; ?> type="checkbox" data-role-id="<?php echo wp_kses_post( $role_id ); ?>" class="b2be_signup_role" name="b2be_signup_role[<?php echo wp_kses_post( $role_id ); ?>]" ><?php echo wp_kses_post( $role_name ); ?></label>
										<?php endforeach; ?>
									<?php endif; ?>
									<div class="clear"></div>
								</div>
								<div class="b2be-signup-form-field-visibility-wrapper b2be-signup-form-body-field-wrapper">
									<div class="b2be-signup-form-field-visibility__label field_labels">
										<label for="">Is Visible ?</label>
									</div>
									<div class="b2be-signup-form-field-visibility__field">
										<input type="checkbox" name="signup_form_field[<?php echo wp_kses_post( $key ); ?>][visibility]" id="signup_form_field_visibility" <?php echo ( $signup_field['visibility'] && 'true' == $signup_field['visibility'] ) ? 'checked' : ''; ?>>								
									</div>
								</div>
								<div class="b2be-signup-form-field-required-wrapper b2be-signup-form-body-field-wrapper">
									<div class="b2be-signup-form-field-required__label field_labels">
										<label for="">Is Required ?</label>
									</div>
									<div class="b2be-signup-form-field-required__field">
										<input type="checkbox" name="signup_form_field[<?php echo wp_kses_post( $key ); ?>][required]" id="signup_form_field_required" <?php echo ( $signup_field['required'] && 'true' == $signup_field['required'] ) ? 'checked' : ''; ?>>								
									</div>
								</div>
								<div class="b2be-signup-form-field-size-wrapper b2be-signup-form-body-field-wrapper">
									<div class="b2be-signup-form-field-size__label field_labels">
										<label for="">Field Size</label>
									</div>
									<div class="b2be-signup-form-field-size__field">
										<select name="signup_form_field[<?php echo wp_kses_post( $key ); ?>][size]" id="signup_form_field_size">
											<option <?php echo ( isset( $signup_field['size'] ) && 'small' == $signup_field['size'] ) ? 'selected' : ''; ?> value="small">Small</option>
											<option <?php echo ( isset( $signup_field['size'] ) && 'medium' == $signup_field['size'] ) ? 'selected' : ''; ?> value="medium">Medium</option>
											<option <?php echo ( isset( $signup_field['size'] ) && 'large' == $signup_field['size'] ) ? 'selected' : ''; ?> value="large">Large</option>
										</select>	
									</div>
								</div>
								<div class="b2be-signup-form-field-classes-wrapper b2be-signup-form-body-field-wrapper">
									<div class="b2be-signup-form-field-classes__label field_labels">
										<label for="">Custom Class</label>
									</div>
									<div class="b2be-signup-form-field-classes__field">
										<textarea row="10" name="b2be_signup_custom_classes[<?php echo wp_kses_post( $key ); ?>][classes]" id="b2be_signup_custom_classes" ><?php echo isset( $signup_field['classes'] ) && ! empty( $signup_field['classes'] ) ? wp_kses_post( implode( "\n", $signup_field['classes'] ) ) : ''; ?></textarea>
									</div>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>
			<div class="b2be-signup-form-field-add-fields-wrapper">
				<a class="b2be-sign-up add-new-fields" href="javascript: void(0)">Add New Fields</a>
			</div>
		</div>
		<div class="b2be-signup-form-preview-wrapper" id="sortable">
			<div class="preview-overlay"><p style="height:auto;">Preview</p></div>
				<p class="preview-fields-wrapper" style="display:none;"></p>
				<?php if ( $signup_fields ) : ?>
					<?php foreach ( $signup_fields as $key => $signup_field ) : ?>
						<div class="preview-fields-wrapper" id="preview_fields_wrapper_<?php echo wp_kses_post( $key ); ?>" style="<?php echo wp_kses_post( b2be_get_field_size( $signup_field['size'] ) ); ?> <?php echo ( 'false' == $signup_field['visibility'] ) ? 'display:none;' : ''; ?>" >
							<p id="preview_field_label_<?php echo wp_kses_post( $key ); ?>">
								<label for=""><?php echo isset( $signup_field['name'] ) ? wp_kses_post( $signup_field['name'] ) : ''; ?></label>
								<span class="is_required" style="color:red; <?php echo ( 'false' == $signup_field['required'] ) ? 'display:none;' : ''; ?>"> *</span>
							</p>
							<p id="preview_field_<?php echo wp_kses_post( $key ); ?>">
								<input type="text">
							</p>
						</div>
					<?php endforeach; ?>
					<div class="clear"></div>
					<button class="button button-default preview-button">Submit</button>
				<?php endif; ?>
		</div>
		<div class="clear"></div>
	</div>
</div>
<div class="b2be-signup-form-button-wrapper">
	<input type="button" class="button-primary b2be-save-signup-fields" value="Save">
</div>

<div id="template" style="display:none;">
	<div class="b2be-signup-form-field">
		<div class="b2be-signup-form-field__header">
			<div class="b2be-signup-form-field-header__label">
			<input type="checkbox" name="select_signup_from_field[1][remove]" id="select_signup_form_field" class="select-signup-form-field"> <label for=""><span class="field-id">1</span>:<span class="field-name"></span></label>
			</div>
			<div class="b2be_signup-form-field-header__icon">
				<span class="dashicons dashicons-arrow-down"></span>
			</div>
		</div>
		<div class="b2be-signup-form-field__body open">
			<div class="b2be-signup-form-field-name-wrapper b2be-signup-form-body-field-wrapper">
				<div class="b2be-signup-form-field-name__label field_labels">
					<label for="">Field Name</label>
				</div>
				<div class="b2be-signup-form-field-name__field">
					<input type="text" name="signup_form_field[1][name]" id="signup_form_field_name">								
				</div>
			</div>
			<div class="b2be-signup-form-field-type-wrapper b2be-signup-form-body-field-wrapper">
				<div class="b2be-signup-form-field-type__label field_labels">
					<label for="">Field Type</label>
				</div>
				<div class="b2be-signup-form-field-type__field">
					<select name="signup_form_field[1][type]" id="signup_form_field_type">
						<option value="">Select Field Type</option>
						<option value="text">Text</option>
						<option value="date">Date</option>
						<option value="number">Number</option>
						<option value="email">Email</option>
						<option value="role">Role</option>
						<option value="username">Username</option>
						<option value="fname">First Name</option>
						<option value="lname">Last Name</option>
						<option value="company">Company</option>
						<option value="country">Country</option>
						<option value="address_1">Address 1</option>
						<option value="address_2">Address 2</option>
						<option value="city">City</option>
						<option value="state">State</option>
						<option value="zip_code">Zip Code</option>
						<option value="phone">Phone</option>
					</select>
				</div>
			</div>
			<div class="b2be-signup-form-field-roles-wrapper" style="display:none">
				<p><?php echo wp_kses_post( __( 'Select the role you want to show', 'b2b-ecommerce' ) ); ?></p>
				<?php if ( wp_roles()->role_names ) : ?>
					<?php foreach ( wp_roles()->role_names as $role_id => $role_name ) : ?>
						<label for=""><input type="checkbox" class="b2be_signup_role" name="b2be_signup_role[<?php echo wp_kses_post( $role_name ); ?>]" id="b2be_signup_role_<?php echo wp_kses_post( $role_name ); ?>"><?php echo wp_kses_post( $role_name ); ?></label>
					<?php endforeach; ?>
				<?php endif; ?>
				<div class="clear"></div>
			</div>
			<div class="b2be-signup-form-field-visibility-wrapper b2be-signup-form-body-field-wrapper">
				<div class="b2be-signup-form-field-visibility__label field_labels">
					<label for="">Is Visible ?</label>
				</div>
				<div class="b2be-signup-form-field-visibility__field">
					<input type="checkbox" name="signup_form_field[1][visibility]" id="signup_form_field_visibility" checked>								
				</div>
			</div>
			<div class="b2be-signup-form-field-required-wrapper b2be-signup-form-body-field-wrapper">
				<div class="b2be-signup-form-field-required__label field_labels">
					<label for="">Is Required ?</label>
				</div>
				<div class="b2be-signup-form-field-required__field">
					<input type="checkbox" name="signup_form_field[1][required]" id="signup_form_field_required" checked>								
				</div>
			</div>
			<div class="b2be-signup-form-field-size-wrapper b2be-signup-form-body-field-wrapper">
				<div class="b2be-signup-form-field-size__label field_labels">
					<label for="">Field Size</label>
				</div>
				<div class="b2be-signup-form-field-size__field">
					<select name="signup_form_field[1][size]" id="signup_form_field_size">
						<option value="small">Small</option>
						<option value="medium">Medium</option>
						<option value="large">Large</option>
					</select>
				</div>
			</div>
			<div class="b2be-signup-form-field-classes-wrapper b2be-signup-form-body-field-wrapper">
				<div class="b2be-signup-form-field-classes__label field_labels">
					<label for="">Custom Class</label>
				</div>
				<div class="b2be-signup-form-field-classes__field">
					<textarea row="10" name="b2be_signup_custom_classes[1][classes]" id="b2be_signup_custom_classes" ></textarea>
				</div>
			</div>
		</div>
	</div>
</div>
