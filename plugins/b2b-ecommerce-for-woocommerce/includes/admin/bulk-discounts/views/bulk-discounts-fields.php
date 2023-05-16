<?php
/**
 * Template for the Bulk Discount field.
 *
 * @var $field_config string[]
 * @package B2B_E-commerce_For_WooCommerce/templates
 */

// prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
wp_nonce_field( 'bulk_dicount_global', 'bulk_dicount_global_nonce' );
?>
<h2><?php echo esc_html__( 'Discount Options', 'b2b-ecommerce' ); ?></h2>
<p><?php echo esc_html__( 'Create Rules to apply discounts and set custom pricing for different buyers', 'b2b-ecommerce' ); ?></p><br>
<span style=""><?php echo esc_html__( 'Enable Discount Options', 'b2b-ecommerce' ); ?></span>
<label class="switch">
  <input type="checkbox" class="enable-bulk-discount" id="enable-bulk-discount" <?php echo ( 'true' == $b2be_is_enable ) ? 'checked' : ''; ?> >
  <span class="bulk-discount-toggle slider round"></span>
</label>

<div class="bulk-template" id="bulk-template">
	<?php
		$count = 1;
	?>
	<?php foreach ( $b2be_discount_rules as $index => $value ) { ?>
		<div style="<?php echo ( 'true' == $b2be_is_enable ) ? '' : 'display:none'; ?>" class="bulk-inner-template" id="bulk-inner-template-<?php echo ( ! empty( $count ) ) ? wp_kses_post( $count ) : '1'; ?>" data-template-id="<?php echo ( ! empty( $count ) ) ? wp_kses_post( $count ) : '1'; ?>">
			<div id="bulk-title-discounts">
				<h2>
					<select class="bulk-priority" id="bulk-rule-priority">
						<?php for ( $i = 10; $i > 0; $i-- ) { ?>
							<option <?php echo ( $i == $value['priority'] ) ? 'selected=selected' : ''; ?> value="<?php echo wp_kses_post( $i ); ?>"><?php echo wp_kses_post( $i ); ?></option>
						<?php } ?>
					</select>
					<span>
						<?php echo esc_html__( 'Rule ', 'b2b-ecommerce' ); ?>
						<?php echo ( ! empty( $value['ruleId'] ) ) ? wp_kses_post( $value['ruleId'] ) : '1'; ?>
					</span>
					<input type="button" style="float:right" name="remove-rule-1" id="remove-rule-1" class="remove-rule button-primary" value="X">
				</h2>
			</div>
			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row" style="width:210px" class="titledesc">
							<?php echo esc_html__( 'Role Based Discount', 'b2b-ecommerce' ); ?>
							<?php echo wp_kses_post( wc_help_tip( __( 'Apply role based discounts.', 'b2b-ecommerce' ) ) ); ?>
						</th>
						<td class="forminp forminp-checkbox">
							<fieldset>
								<legend class="screen-reader-text"><span>Role Based Discount</span></legend>
								<label for="">
									<input name="bulk-role-based-enable" class="bulk-role-based-enable" id="bulk-role-based-enable" type="checkbox" <?php echo ( 'true' == $value['is_role_based'] ) ? 'checked' : ''; ?>><?php echo esc_html__( 'Enable Role Based Discount Functionality', 'b2b-ecommerce' ); ?>
								</label>
							</fieldset>
						</td>
					</tr>
					<tr class="role-discount">
						<th>
							<label for=""><b><?php echo esc_html__( 'Roles', 'b2b-ecommerce' ); ?></b></label>
						</th>
						<td>
							<fieldset>
								<div>
									<select multiple name="bulk-b2be-role-selection" class="bulk-b2be-role-selection" id="bulk-b2be-role-selection-<?php echo ( ! empty( $value['ruleId'] ) ) ? wp_kses_post( $value['ruleId'] ) : '1'; ?>-<?php echo wp_kses_post( $index ); ?>">
										<?php foreach ( get_b2be_roles() as $role_id => $role_name ) { ?>
											<option <?php echo ( isset( $value['roles'] ) && in_array( $role_id, $value['roles'] ) ) ? 'selected=selected' : ''; ?> value="<?php echo wp_kses_post( $role_id ); ?>"><?php echo wp_kses_post( $role_name ); ?></option>
										<?php } ?>
									</select>
								</div>
							</fieldset>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" style="width:210px" class="titledesc">
							<?php echo esc_html__( 'Customer Based Discount', 'b2b-ecommerce' ); ?>					
							<?php echo wp_kses_post( wc_help_tip( __( 'Apply Customer Based Discount.', 'b2b-ecommerce' ) ) ); ?>
						</th>
						<td class="forminp forminp-checkbox">
							<fieldset>
								<legend class="screen-reader-text"><span>Customer Based Discount</span></legend>
								<label for="">
									<input name="bulk-customer-based-enable" class="bulk-customer-based-enable" id="bulk-customer-based-enable" type="checkbox" <?php echo ( 'true' == $value['is_customer_based'] ) ? 'checked' : ''; ?>><?php echo esc_html__( 'Enable Customer Based Discount Functionality', 'b2b-ecommerce' ); ?>
								</label>
							</fieldset>
						</td>
					</tr>
					<tr class="customer-discount">
						<th>
							<label for=""><b><?php echo esc_html__( 'Customers', 'b2b-ecommerce' ); ?></b></label><br>
						</th>
						<td>
							<fieldset>
								<div>
									<select multiple name="bulk-b2be-customer-selection" class="bulk-b2be-customer-selection" id="bulk-b2be-customer-selection-<?php echo ( ! empty( $value['ruleId'] ) ) ? wp_kses_post( $value['ruleId'] ) : '1'; ?>-<?php echo wp_kses_post( $index ); ?>" style="width:max-content">
										<?php
										foreach ( get_b2be_users() as $user_id => $user_data ) {
											?>
											<option <?php echo ( isset( $value['customer'] ) && in_array( $user_id, $value['customer'] ) ) ? 'selected=selected' : ''; ?> value="<?php echo wp_kses_post( $user_id ); ?>"><?php echo '#' . wp_kses_post( $user_id ) . '-(' . wp_kses_post( $user_data->data->user_email ) . ')'; ?></option>
										<?php } ?>
									</select>
								</div>
							</fieldset>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" style="width:210px" class="titledesc">
							<?php echo esc_html__( 'Category Based Discount', 'b2b-ecommerce' ); ?>
							<?php echo wp_kses_post( wc_help_tip( __( 'Apply Category Based Discount.', 'b2b-ecommerce' ) ) ); ?>
						</th>
						<td class="forminp forminp-checkbox">
							<fieldset>
								<legend class="screen-reader-text"><span>Category Based Discount</span></legend>
								<label for="">
									<input name="bulk-category-based-enable" class="bulk-category-based-enable" id="bulk-category-based-enable" type="checkbox" <?php echo ( 'true' == $value['is_category_based'] ) ? 'checked' : ''; ?>><?php echo esc_html__( 'Enable Category Based Discount Functionality', 'b2b-ecommerce' ); ?>
								</label>
							</fieldset>
						</td>
					</tr>
					<tr class="category-discount" >
						<th>
							<label for=""><b><?php echo esc_html__( 'Categories', 'b2b-ecommerce' ); ?></b></label><br>
						</th>
						<td>
							<fieldset>
								<div>
									<select multiple name="bulk-b2be-category-selection" class="bulk-b2be-category-selection" id="bulk-b2be-category-selection-<?php echo ( ! empty( $value['ruleId'] ) ) ? wp_kses_post( $value['ruleId'] ) : '1'; ?>-<?php echo wp_kses_post( $index ); ?>">
										<?php foreach ( get_b2be_categories() as $b2be_cat_id => $cat_name ) { ?>
											<option <?php echo ( isset( $value['categories'] ) && in_array( $b2be_cat_id, $value['categories'] ) ) ? 'selected=selected' : ''; ?> value="<?php echo wp_kses_post( $b2be_cat_id ); ?>"><?php echo wp_kses_post( $cat_name ); ?></option>
										<?php } ?>
									</select>
								</div>
							</fieldset>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" style="width:210px" class="titledesc">
							<?php echo esc_html__( 'Product Based Discount', 'b2b-ecommerce' ); ?>
							<?php echo wp_kses_post( wc_help_tip( __( 'Apply Product Based Discount.', 'b2b-ecommerce' ) ) ); ?>
						</th>
						<td class="forminp forminp-checkbox">
							<fieldset>
								<legend class="screen-reader-text"><span>Product Based Discount</span></legend>
								<label for="">
									<input name="bulk-product-based-enable" class="bulk-product-based-enable" id="bulk-product-based-enable" type="checkbox" <?php echo ( isset( $value['is_product_based'] ) && 'true' == $value['is_product_based'] ) ? 'checked' : ''; ?>><?php echo esc_html__( 'Enable Product Based Discount Functionality', 'b2b-ecommerce' ); ?>
								</label>
							</fieldset>
						</td>
					</tr>
					<tr class="product-discount" >
						<th>
							<label for=""><b><?php echo esc_html__( 'Products', 'b2b-ecommerce' ); ?></b></label><br>
						</th>
						<td>
							<fieldset>
								<div>
									<select multiple name="bulk-b2be-product-selection" class="bulk-b2be-product-selection" id="bulk-b2be-product-selection-<?php echo ( ! empty( $value['ruleId'] ) ) ? wp_kses_post( $value['ruleId'] ) : '1'; ?>-<?php echo wp_kses_post( $index ); ?>">
										<?php foreach ( get_b2be_products() as $product_id => $product_name ) { ?>
											<?php if ( wc_get_product( $product_id )->is_type( 'simple' ) ) { ?>
												<option <?php echo ( isset( $value['products'] ) && in_array( $product_id, $value['products'] ) ) ? 'selected=selected' : ''; ?> value="<?php echo wp_kses_post( $product_id ); ?>"><?php echo wp_kses_post( $product_name ); ?></option>
											<?php } elseif ( wc_get_product( $product_id )->is_type( 'variable' ) ) { ?>
												<?php
												if ( ! empty( get_b2be_products_variration( intval( $product_id ) ) ) ) {
													?>
													<?php foreach ( get_b2be_products_variration( intval( $product_id ) ) as $variation_id => $variation_name ) { ?>
														<option class="b2be-vari" <?php echo ( isset( $value['innerRule'][0]['variation_ids'] ) && in_array( $variation_id, $value['innerRule'][0]['variation_ids'] ) ) ? 'selected=selected' : ''; ?> value="<?php echo wp_kses_post( $variation_id ); ?>"> <?php echo wp_kses_post( $variation_name ); ?></option>
													<?php } ?>
												<?php } ?>
											<?php } ?>
										<?php } ?>
									</select>
								</div>
							</fieldset>
						</td>
					</tr>
					<tr class="quantity-discount" valign="top">
						<th scope="row" style="width:210px" class="titledesc">
							<?php echo esc_html__( 'Discount Format', 'b2b-ecommerce' ); ?>
							<?php echo wp_kses_post( wc_help_tip( __( 'Change the format of discount being shown in variation table.', 'b2b-ecommerce' ) ) ); ?>
						</th>
						<td class="forminp forminp-checkbox">
							<fieldset>
								<legend class="screen-reader-text"><span>Discount Format</span></legend>
								<input type="radio" name="bulk-discount-format-<?php echo ( ! empty( $value['ruleId'] ) ) ? wp_kses_post( $value['ruleId'] ) : '1'; ?>" id="bulk-discount-format-default" class="bulk-discount-format" value="default" <?php echo ( ! isset( $value['discount_format'] ) || 'per-piece' != $value['discount_format'] ) ? "checked='checked'" : ''; ?>><?php echo esc_html__( 'Default', 'b2b-ecommerce' ); ?><br><br>
								<input type="radio" name="bulk-discount-format-<?php echo ( ! empty( $value['ruleId'] ) ) ? wp_kses_post( $value['ruleId'] ) : '1'; ?>" id="bulk-discount-format-per-piece" class="bulk-discount-format" value="per-piece" <?php echo ( isset( $value['discount_format'] ) && 'per-piece' == $value['discount_format'] ) ? "checked='checked'" : ''; ?>><?php echo esc_html__( 'Per Unit Price', 'b2b-ecommerce' ); ?>
							</fieldset>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" style="width:210px" class="titledesc">
							<?php echo esc_html__( 'Quantity Based Discount', 'b2b-ecommerce' ); ?>
							<?php echo wp_kses_post( wc_help_tip( __( 'Apply Quantity Based Discounts.', 'b2b-ecommerce' ) ) ); ?>
						</th>
						<td class="forminp forminp-checkbox">
							<fieldset>
								<legend class="screen-reader-text"><span>Quantity Based Discount</span></legend>
								<label for="">
									<input name="bulk-quantity-based-enable" class="bulk-quantity-based-enable" id="bulk-quantity-based-enable" type="checkbox" <?php echo ( 'true' == $value['is_quantity_based'] ) ? 'checked' : ''; ?>><?php echo esc_html__( 'Enable Quantity Based Discount Functionality', 'b2b-ecommerce' ); ?>
								</label>
							</fieldset>
						</td>
					</tr>		
				</tbody>
			</table>

			<table id="bulk-inner-rows">
				<?php foreach ( $value['innerRule'] as $key => $inner_value ) { ?>
					<tr class="hide-by-default">
						<td class="quantity-discount" >
							<fieldset>
								<div  class="bulk-quantity-base">
									<label for=""><b><?php echo esc_html__( 'Min Quantity', 'b2b-ecommerce' ); ?></b></label><br>
									<input name="bulk-quantity-based-discount-min" id="bulk-quantity-based-discount-min" style="width:auto;padding:5px;" type="number" min="0" value="<?php echo ( ! empty( $inner_value['minQuantity'] ) ) ? wp_kses_post( intval( $inner_value['minQuantity'] ) ) : ''; ?>">
								</div>			
							</fieldset>
						</td>
						<td class="quantity-discount" >
							<fieldset>
								<div class="bulk-quantity-base">
									<label for=""><b><?php echo esc_html__( 'Max Quantity', 'b2b-ecommerce' ); ?></b></label><br>
									<input name="bulk-quantity-based-discount-max" id="bulk-quantity-based-discount-max" style="width:auto;padding:5px;" type="number" min="0" value="<?php echo ( ! empty( $inner_value['maxQuantity'] ) ) ? wp_kses_post( intval( $inner_value['maxQuantity'] ) ) : ''; ?>">
								</div>
							</fieldset>
						</td>
						<td class="forminp forminp-checkbox" >
							<fieldset>
								<div class="bulk-quantity-base">
									<label for=""><b><?php echo esc_html__( 'Discount', 'b2b-ecommerce' ); ?></b></label><br>
									<input name="bulk-quantity-based-discount-amount" id="bulk-quantity-based-discount-amount" style="width:auto;padding:5px;" type="number" min="0" step="any" style="width: 100%;" value="<?php echo ( ! empty( $inner_value['discount'] ) ) ? wp_kses_post( $inner_value['discount'] ) : ''; ?>"> 
								</div>
							</fieldset>
						</td>
						<td class="forminp forminp-checkbox">
							<fieldset>
								<label for=""><b><?php echo esc_html__( 'Type', 'b2b-ecommerce' ); ?></b></label><br>
								<select name="bulk-type" id="bulk-type">
									<option <?php echo ( 'percentage' == $inner_value['type'] ) ? 'selected=selected' : ''; ?> value="percentage"><?php echo esc_html__( 'Percentage', 'b2b-ecommerce' ); ?></option>
									<option <?php echo ( 'fixed' == $inner_value['type'] ) ? 'selected=selected' : ''; ?> value="fixed"><?php echo esc_html__( 'Fixed Price', 'b2b-ecommerce' ); ?></option>
								</select>
							</fieldset>
						</td>

						<td>
							<div class="quantity-discount bulk-quantity-base">
								<?php if ( 0 == $key ) { ?>
									<input type="button" name="add_bulk_quantity_range" id="add_bulk_quantity_range" class="add_bulk_quantity_range button-secondary" value="+" > 
								<?php } else { ?>
									<input type="button" name="remove_bulk_quantity_range" id="remove_bulk_quantity_range" class="button-secondary" value="-" >
								<?php } ?>
							</div>
						</td>
					</tr>
				<?php } ?>
			</table>
		</div>
		<?php $count++; ?>
	<?php } ?>
		
</div>
<input style="" id="save_discount_rule" type="button" class="button-secondary"value="<?php echo esc_html__( 'Save', 'b2b-ecommerce' ); ?>">
<input style="<?php echo ( 'true' == $b2be_is_enable ) ? '' : 'display:none'; ?>" id="add_discount_rule" type="button" class="button-primary"value="<?php echo esc_html__( 'Add Rule', 'b2b-ecommerce' ); ?>">

<!-- Template -->
<div id="template" style="display:none;">
	<div class="bulk-inner-template" id="bulk-inner-template-1" data-template-id="<?php echo count( $b2be_discount_rules ); ?>">
		<div id="bulk-title-discounts">
			<h2>
				<select class="bulk-priority" id="bulk-rule-priority">
					<?php for ( $i = 10; $i > 0; $i-- ) { ?>
						<option value="<?php echo wp_kses_post( $i ); ?>"><?php echo wp_kses_post( $i ); ?></option>
					<?php } ?>
				</select>
				<span>
					<?php echo esc_html__( 'Rule 1', 'b2b-ecommerce' ); ?>
				</span>
				<input type="button" style="float:right" name="remove-rule-2" id="remove-rule-2" class="remove-rule button-primary" value="X">
			</h2>
		</div>
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row" style="width:210px" class="titledesc">
						<?php echo esc_html__( 'Role Based Discount', 'b2b-ecommerce' ); ?>
						<?php echo wp_kses_post( wc_help_tip( __( 'Apply role based discounts.', 'b2b-ecommerce' ) ) ); ?>
					</th>
					<td class="forminp forminp-checkbox">
						<fieldset>
							<legend class="screen-reader-text"><span>Role Based Discount</span></legend>
							<label for="">
								<input name="bulk-role-based-enable" class="bulk-role-based-enable" id="bulk-role-based-enable" type="checkbox"><?php echo esc_html__( 'Enable Role Based Discount Functionality', 'b2b-ecommerce' ); ?>
							</label>
						</fieldset>
					</td>
				</tr>
				<tr class="role-discount">
					<th>
						<label for=""><b><?php echo esc_html__( 'Roles', 'b2b-ecommerce' ); ?></b></label>
					</th>
					<td>
						<fieldset>
							<div>
								<select multiple name="bulk-b2be-role-selection" class="bulk-b2be-role-selection" id="bulk-b2be-role-selection-<?php echo ( ! empty( $value['ruleId'] ) ) ? wp_kses_post( $value['ruleId'] ) : '1'; ?>-<?php echo wp_kses_post( $index ); ?>">
									<?php foreach ( get_b2be_roles() as $role_id => $role_name ) { ?>
										<option value="<?php echo wp_kses_post( $role_id ); ?>"><?php echo wp_kses_post( $role_name ); ?></option>
									<?php } ?>
								</select>
							</div>
						</fieldset>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" style="width:210px" class="titledesc">
						<?php echo esc_html__( 'Customer Based Discount', 'b2b-ecommerce' ); ?>					
						<?php echo wp_kses_post( wc_help_tip( __( 'Apply Customer Based Discount.', 'b2b-ecommerce' ) ) ); ?>
					</th>
					<td class="forminp forminp-checkbox">
						<fieldset>
							<legend class="screen-reader-text"><span>Customer Based Discount</span></legend>
							<label for="">
								<input name="bulk-customer-based-enable" class="bulk-customer-based-enable" id="bulk-customer-based-enable" type="checkbox" ><?php echo esc_html__( 'Enable Customer Based Discount Functionality', 'b2b-ecommerce' ); ?>
							</label>
						</fieldset>
					</td>
				</tr>
				<tr class="customer-discount">
					<th>
						<label for=""><b><?php echo esc_html__( 'Customers', 'b2b-ecommerce' ); ?></b></label><br>
					</th>
					<td>
						<fieldset>
							<div>
								<select multiple name="bulk-b2be-customer-selection" class="bulk-b2be-customer-selection" id="bulk-b2be-customer-selection-<?php echo ( ! empty( $value['ruleId'] ) ) ? wp_kses_post( $value['ruleId'] ) : '1'; ?>-<?php echo wp_kses_post( $index ); ?>" style="width:max-content">
									<?php
									foreach ( get_b2be_users() as $user_id => $user_data ) {
										?>
										<option value="<?php echo wp_kses_post( $user_id ); ?>"><?php echo '#' . wp_kses_post( $user_id ) . '-(' . wp_kses_post( $user_data->data->user_email ) . ')'; ?></option>
									<?php } ?>
								</select>
							</div>
						</fieldset>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" style="width:210px" class="titledesc">
						<?php echo esc_html__( 'Category Based Discount', 'b2b-ecommerce' ); ?>
						<?php echo wp_kses_post( wc_help_tip( __( 'Apply Category Based Discount.', 'b2b-ecommerce' ) ) ); ?>
					</th>
					<td class="forminp forminp-checkbox">
						<fieldset>
							<legend class="screen-reader-text"><span>Category Based Discount</span></legend>
							<label for="">
								<input name="bulk-category-based-enable" class="bulk-category-based-enable" id="bulk-category-based-enable" type="checkbox" ><?php echo esc_html__( 'Enable Category Based Discount Functionality', 'b2b-ecommerce' ); ?>
							</label>
						</fieldset>
					</td>
				</tr>
				<tr class="category-discount" >
					<th>
						<label for=""><b><?php echo esc_html__( 'Categories', 'b2b-ecommerce' ); ?></b></label><br>
					</th>
					<td>
						<fieldset>
							<div>
								<select multiple name="bulk-b2be-category-selection" class="bulk-b2be-category-selection" id="bulk-b2be-category-selection-<?php echo ( ! empty( $value['ruleId'] ) ) ? wp_kses_post( $value['ruleId'] ) : '1'; ?>-<?php echo wp_kses_post( $index ); ?>">
									<?php foreach ( get_b2be_categories() as $b2be_cat_id => $cat_name ) { ?>
										<option value="<?php echo wp_kses_post( $b2be_cat_id ); ?>"><?php echo wp_kses_post( $cat_name ); ?></option>
									<?php } ?>
								</select>
							</div>
						</fieldset>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" style="width:210px" class="titledesc">
						<?php echo esc_html__( 'Product Based Discount', 'b2b-ecommerce' ); ?>
						<?php echo wp_kses_post( wc_help_tip( __( 'Apply Product Based Discount.', 'b2b-ecommerce' ) ) ); ?>
					</th>
					<td class="forminp forminp-checkbox">
						<fieldset>
							<legend class="screen-reader-text"><span>Product Based Discount</span></legend>
							<label for="">
								<input name="bulk-product-based-enable" class="bulk-product-based-enable" id="bulk-product-based-enable" type="checkbox"><?php echo esc_html__( 'Enable Product Based Discount Functionality', 'b2b-ecommerce' ); ?>
							</label>
						</fieldset>
					</td>
				</tr>
				<tr class="product-discount" >
					<th>
						<label for=""><b><?php echo esc_html__( 'Products', 'b2b-ecommerce' ); ?></b></label><br>
					</th>
					<td>
						<fieldset>
							<div>
								<select multiple name="bulk-b2be-product-selection" class="bulk-b2be-product-selection" id="bulk-b2be-product-selection-<?php echo ( ! empty( $value['ruleId'] ) ) ? wp_kses_post( $value['ruleId'] ) : '1'; ?>-<?php echo wp_kses_post( $index ); ?>">
									<?php foreach ( get_b2be_products() as $product_id => $product_name ) { ?>
										<?php if ( wc_get_product( $product_id )->is_type( 'simple' ) ) { ?>
											<option value="<?php echo wp_kses_post( $product_id ); ?>"><?php echo wp_kses_post( $product_name ); ?></option>
										<?php } else { ?>
											<?php foreach ( get_b2be_products_variration( $product_id ) as $variation_id => $variation_name ) { ?>
												<option value="<?php echo wp_kses_post( $variation_id ); ?>"><?php echo wp_kses_post( $variation_name ); ?></option>
											<?php } ?>
										<?php } ?>
									<?php } ?>
								</select>
							</div>
						</fieldset>
					</td>
				</tr>
				<tr class="quantity-discount" valign="top">
					<th scope="row" style="width:210px" class="titledesc">
						<?php echo esc_html__( 'Discount Format', 'b2b-ecommerce' ); ?>
						<?php echo wp_kses_post( wc_help_tip( __( 'Change the format of discount being shown in variation table.', 'b2b-ecommerce' ) ) ); ?>
					</th>
					<td class="forminp forminp-checkbox">
						<fieldset>
							<legend class="screen-reader-text"><span>Discount Format</span></legend>
							<input type="radio" name="bulk-discount-format-0" id="bulk-discount-format-default" class="bulk-discount-format" value="default"><?php echo esc_html__( 'Default', 'b2b-ecommerce' ); ?><br><br>
							<input type="radio" name="bulk-discount-format-0" id="bulk-discount-format-per-piece" class="bulk-discount-format" value="per-piece"><?php echo esc_html__( 'Per Unit Price', 'b2b-ecommerce' ); ?>
						</fieldset>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" style="width:210px" class="titledesc">
						<?php echo esc_html__( 'Quantity Based Discount', 'b2b-ecommerce' ); ?>
						<?php echo wp_kses_post( wc_help_tip( __( 'Apply Quantity Based Discounts.', 'b2b-ecommerce' ) ) ); ?>
					</th>
					<td class="forminp forminp-checkbox">
						<fieldset>
							<legend class="screen-reader-text"><span>Quantity Based Discount</span></legend>
							<label for="">
								<input name="bulk-quantity-based-enable" class="bulk-quantity-based-enable" id="bulk-quantity-based-enable" type="checkbox" ><?php echo esc_html__( 'Enable Quantity Based Discount Functionality', 'b2b-ecommerce' ); ?>
							</label>
						</fieldset>
					</td>
				</tr>
			</tbody>
		</table>

		<table id="bulk-inner-rows">
			<tr class="hide-by-default">
				<td class="quantity-discount" style="display:none;">
					<fieldset>
						<div  class="bulk-quantity-base">
							<label for=""><b><?php echo esc_html__( 'Min Quantity', 'b2b-ecommerce' ); ?></b></label><br>
							<input name="bulk-quantity-based-discount-min" id="bulk-quantity-based-discount-min" style="width:auto;padding:5px;" type="number" min="0" >
						</div>			
					</fieldset>
				</td>
				<td class="quantity-discount" style="display:none;">
					<fieldset>
						<div class="bulk-quantity-base">
							<label for=""><b><?php echo esc_html__( 'Max Quantity', 'b2b-ecommerce' ); ?></b></label><br>
							<input name="bulk-quantity-based-discount-max" id="bulk-quantity-based-discount-max" style="width:auto;padding:5px;" type="number" min="0" >
						</div>
					</fieldset>
				</td>
				<td class="forminp forminp-checkbox" >
					<fieldset>
						<div class="bulk-quantity-base">
							<label for=""><b><?php echo esc_html__( 'Discount', 'b2b-ecommerce' ); ?></b></label><br>
							<input name="bulk-quantity-based-discount-amount" id="bulk-quantity-based-discount-amount" style="width:auto;padding:5px;" type="number" min="0" step="any" style="width: 100%;" > 
						</div>
					</fieldset>
				</td>
				<td class="forminp forminp-checkbox">
					<fieldset>
						<label for=""><b><?php echo esc_html__( 'Type', 'b2b-ecommerce' ); ?></b></label><br>
						<select name="bulk-type" id="bulk-type">
							<option value="percentage"><?php echo esc_html__( 'Percentage', 'b2b-ecommerce' ); ?></option>
							<option value="fixed"><?php echo esc_html__( 'Fixed Price', 'b2b-ecommerce' ); ?></option>
						</select>
					</fieldset>
				</td>

				<td>
					<div class="quantity-discount bulk-quantity-base">
							<input type="button" name="add_bulk_quantity_range" id="add_bulk_quantity_range" class="add_bulk_quantity_range button-secondary" value="+" > 
					</div>
				</td>
			</tr>
		</table>
	</div>
</div>
