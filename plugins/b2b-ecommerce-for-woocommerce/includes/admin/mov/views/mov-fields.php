<?php
/**
 * Template for MOV fields.
 *
 * @var $field_config string[]
 * @package B2B_E-commerce_For_WooCommerce/templates
 */

// prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<h2><?php echo esc_html__( 'Minimum Order Value', 'b2b-ecommerce' ); ?></h2>
<p><?php echo esc_html__( 'This will allow you to limit quantity on orders', 'b2b-ecommerce' ); ?></p><br>
<span style=""><?php echo esc_html__( 'Enable MOV Options', 'b2b-ecommerce' ); ?></span>
<label class="switch">
  <input type="checkbox" class="enable-mov-discount" id="enable-mov-discount" <?php echo ( 'true' == $is_b2be_mov_enable ) ? 'checked' : ''; ?> >
  <span class="mov-discount-toggle slider round"></span>
</label>
<div class="mov-template" id="mov-template">
	<?php
		$count = 1;
	?>
	<?php foreach ( $b2be_mov_rules as $index => $value ) { ?>

		<div style="<?php echo ( 'true' == $is_b2be_mov_enable ) ? '' : 'display:none'; ?>" class="mov-inner-template" id="mov-inner-template-<?php echo ( ! empty( $count ) ) ? wp_kses_post( $count ) : '1'; ?>" data-template-id="<?php echo ( ! empty( $count ) ) ? wp_kses_post( $count ) : '1'; ?>">
			<div id="mov-title-discounts">
				<h2>
					<select class="mov-priority" id="mov-rule-priority">
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
							<?php echo esc_html__( 'Role Based MOV', 'b2b-ecommerce' ); ?>
							<?php echo wp_kses_post( wc_help_tip( __( 'Lets you set MOV for user roles.', 'b2b-ecommerce' ) ) ); ?>
						</th>
						<td class="forminp forminp-checkbox">
							<fieldset>
								<legend class="screen-reader-text"><span>Role Based Discount</span></legend>
								<label for="">
									<input name="mov-role-based-enable" class="mov-role-based-enable" id="mov-role-based-enable" type="checkbox" <?php echo ( 'true' == $value['is_role_based'] ) ? 'checked' : ''; ?>> <?php echo esc_html__( 'Enable Role Based MOV Functionality', 'b2b-ecommerce' ); ?>							
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
									<select multiple name="mov-b2be-role-selection" class="mov-b2be-role-selection" id="mov-b2be-role-selection-<?php echo ( ! empty( $value['ruleId'] ) ) ? wp_kses_post( $value['ruleId'] ) : '1'; ?>-<?php echo wp_kses_post( $key ); ?>">
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
							<?php echo esc_html__( 'Customer Based MOV', 'b2b-ecommerce' ); ?>					
							<?php echo wp_kses_post( wc_help_tip( __( 'Lets you set MOV for individual customers.', 'b2b-ecommerce' ) ) ); ?>
						</th>
						<td class="forminp forminp-checkbox">
							<fieldset>
								<legend class="screen-reader-text"><span>Customer Based Discount</span></legend>
								<label for="">
									<input name="mov-customer-based-enable" class="mov-customer-based-enable" id="mov-customer-based-enable" type="checkbox" <?php echo ( 'true' == $value['is_customer_based'] ) ? 'checked' : ''; ?>><?php echo esc_html__( 'Enable Customer Based MOV Functionality', 'b2b-ecommerce' ); ?>							
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
									<select multiple name="mov-b2be-customer-selection" class="mov-b2be-customer-selection" id="mov-b2be-customer-selection-<?php echo ( ! empty( $value['ruleId'] ) ) ? wp_kses_post( $value['ruleId'] ) : '1'; ?>-<?php echo wp_kses_post( $index ); ?>" style="width:max-content">
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
							<?php echo esc_html__( 'Category Based MOV', 'b2b-ecommerce' ); ?>
							<?php echo wp_kses_post( wc_help_tip( __( 'Lets you set MOV for different categories.', 'b2b-ecommerce' ) ) ); ?>
						</th>
						<td class="forminp forminp-checkbox">
							<fieldset>
								<legend class="screen-reader-text"><span>Category Based Discount</span></legend>
								<label for="">
									<input name="mov-category-based-enable" class="mov-category-based-enable" id="mov-category-based-enable" type="checkbox" <?php echo ( 'true' == $value['is_category_based'] ) ? 'checked' : ''; ?>><?php echo esc_html__( 'Enable Category Based MOV Functionality', 'b2b-ecommerce' ); ?>							
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
									<select multiple name="mov-b2be-category-selection" class="mov-b2be-category-selection" id="mov-b2be-category-selection-<?php echo ( ! empty( $value['ruleId'] ) ) ? wp_kses_post( $value['ruleId'] ) : '1'; ?>-<?php echo wp_kses_post( $key ); ?>">
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
							<?php echo esc_html__( 'Product Based MOV', 'b2b-ecommerce' ); ?>
							<?php echo wp_kses_post( wc_help_tip( __( 'Lets you set MOV for different products.', 'b2b-ecommerce' ) ) ); ?>
						</th>
						<td class="forminp forminp-checkbox">
							<fieldset>
								<legend class="screen-reader-text"><span>Product Based Discount</span></legend>
								<label for="">
									<input name="mov-product-based-enable" class="mov-product-based-enable" id="mov-product-based-enable" type="checkbox" <?php echo ( isset( $value['is_product_based'] ) && 'true' == $value['is_product_based'] ) ? 'checked' : ''; ?>><?php echo esc_html__( 'Enable Product Based MOV Functionality', 'b2b-ecommerce' ); ?>
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
									<select multiple name="mov-b2be-product-selection" class="mov-b2be-product-selection" id="mov-b2be-product-selection-<?php echo ( ! empty( $value['ruleId'] ) ) ? wp_kses_post( $value['ruleId'] ) : '1'; ?>-<?php echo wp_kses_post( $key ); ?>">
										<?php foreach ( get_b2be_products() as $product_id => $product_name ) { ?>
											<?php if ( wc_get_product( $product_id )->is_type( 'simple' ) ) { ?>
												<option <?php echo ( isset( $value['products'] ) && in_array( $product_id, $value['products'] ) ) ? 'selected=selected' : ''; ?> value="<?php echo wp_kses_post( $product_id ); ?>"><?php echo wp_kses_post( $product_name ); ?></option>
											<?php } elseif ( wc_get_product( $product_id )->is_type( 'variable' ) ) { ?>
												<?php
												if ( ! empty( get_b2be_products_variration( intval( $product_id ) ) ) ) {
													?>
													<?php foreach ( get_b2be_products_variration( $product_id ) as $variation_id => $variation_name ) { ?>
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
				</tbody>
			</table>

			<table id="mov-inner-rows">
				<?php foreach ( $value['innerRule'] as $key => $inner_value ) { ?>
					<tr class="hide-by-default">
						<td class="quantity-discount" >
							<fieldset>
								<div  class="mov-quantity-base">
									<label for=""><b><?php echo esc_html__( 'Min Value', 'b2b-ecommerce' ); ?></b></label><br>
									<input name="mov-value-based-limit-min" id="mov-value-based-limit-min" style="width:auto;padding:5px;" type="number" min="0" value="<?php echo ( ! empty( $inner_value['minValue'] ) ) ? wp_kses_post( intval( $inner_value['minValue'] ) ) : ''; ?>">
								</div>			
							</fieldset>
						</td>
						<td class="quantity-discount" >
							<fieldset>
								<div class="mov-quantity-base">
									<label for=""><b><?php echo esc_html__( 'Max Value', 'b2b-ecommerce' ); ?></b></label><br>
									<input name="mov-value-based-limit-max" id="mov-value-based-limit-max" style="width:auto;padding:5px;" type="number" min="0" value="<?php echo ( ! empty( $inner_value['maxValue'] ) ) ? wp_kses_post( intval( $inner_value['maxValue'] ) ) : ''; ?>">
								</div>
							</fieldset>
						</td>
					</tr>
				<?php } ?>
			</table>
		</div>
		<?php $count++; ?>
	<?php } ?>
		
</div>
<input style="" id="save_discount_rule" type="button" class="button-secondary"value="<?php echo esc_html__( 'Save', 'b2b-ecommerce' ); ?>">
<input style="<?php echo ( 'true' == $is_b2be_mov_enable ) ? '' : 'display:none'; ?>" id="add_discount_rule" type="button" class="button-primary"value="<?php echo esc_html__( 'Add Rule', 'b2b-ecommerce' ); ?>">

<!-- Template -->
<div id="template" style="display:none;">
	<div class="mov-inner-template" id="mov-inner-template-1" data-template-id="<?php echo count( $b2be_mov_rules ); ?>">
		<div id="mov-title-discounts">
			<h2>
				<select class="mov-priority" id="mov-rule-priority">
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
						<?php echo esc_html__( 'Role Based MOV', 'b2b-ecommerce' ); ?>
						<?php echo wp_kses_post( wc_help_tip( __( 'Lets you set MOV for user roles.', 'b2b-ecommerce' ) ) ); ?>
					</th>
					<td class="forminp forminp-checkbox">
						<fieldset>
							<legend class="screen-reader-text"><span>Role Based Discount</span></legend>
							<label for="">
								<input name="mov-role-based-enable" class="mov-role-based-enable" id="mov-role-based-enable" type="checkbox"> <?php echo esc_html__( 'Enable Role Based MOV Functionality', 'b2b-ecommerce' ); ?>							
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
								<select multiple name="mov-b2be-role-selection" class="mov-b2be-role-selection" id="mov-b2be-role-selection-<?php echo ( ! empty( $value['ruleId'] ) ) ? wp_kses_post( $value['ruleId'] ) : '1'; ?>-<?php echo wp_kses_post( $key ); ?>">
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
						<?php echo esc_html__( 'Customer Based MOV', 'b2b-ecommerce' ); ?>					
						<?php echo wp_kses_post( wc_help_tip( __( 'Lets you set MOV for individual customers.', 'b2b-ecommerce' ) ) ); ?>
					</th>
					<td class="forminp forminp-checkbox">
						<fieldset>
							<legend class="screen-reader-text"><span>Customer Based Discount</span></legend>
							<label for="">
								<input name="mov-customer-based-enable" class="mov-customer-based-enable" id="mov-customer-based-enable" type="checkbox" ><?php echo esc_html__( 'Enable Customer Based MOV Functionality', 'b2b-ecommerce' ); ?>							
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
								<select multiple name="mov-b2be-customer-selection" class="mov-b2be-customer-selection" id="mov-b2be-customer-selection-<?php echo ( ! empty( $value['ruleId'] ) ) ? wp_kses_post( $value['ruleId'] ) : '1'; ?>-<?php echo wp_kses_post( $index ); ?>" style="width:max-content">
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
						<?php echo esc_html__( 'Category Based MOV', 'b2b-ecommerce' ); ?>
						<?php echo wp_kses_post( wc_help_tip( __( 'Lets you set MOV for different categories.', 'b2b-ecommerce' ) ) ); ?>
					</th>
					<td class="forminp forminp-checkbox">
						<fieldset>
							<legend class="screen-reader-text"><span>Category Based Discount</span></legend>
							<label for="">
								<input name="mov-category-based-enable" class="mov-category-based-enable" id="mov-category-based-enable" type="checkbox" ><?php echo esc_html__( 'Enable Category Based MOV Functionality', 'b2b-ecommerce' ); ?>							
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
								<select multiple name="mov-b2be-category-selection" class="mov-b2be-category-selection" id="mov-b2be-category-selection-<?php echo ( ! empty( $value['ruleId'] ) ) ? wp_kses_post( $value['ruleId'] ) : '1'; ?>-<?php echo wp_kses_post( $key ); ?>">
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
							<?php echo esc_html__( 'Product Based MOV', 'b2b-ecommerce' ); ?>
							<?php echo wp_kses_post( wc_help_tip( __( 'Lets you set MOV for for different products.', 'b2b-ecommerce' ) ) ); ?>
						</th>
						<td class="forminp forminp-checkbox">
							<fieldset>
								<legend class="screen-reader-text"><span>Product Based Discount</span></legend>
								<label for="">
									<input name="mov-product-based-enable" class="mov-product-based-enable" id="mov-product-based-enable" type="checkbox"><?php echo esc_html__( 'Enable Product Based MOV Functionality', 'b2b-ecommerce' ); ?>
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
									<select multiple name="mov-b2be-product-selection" class="mov-b2be-product-selection" id="mov-b2be-product-selection-<?php echo ( ! empty( $value['ruleId'] ) ) ? wp_kses_post( $value['ruleId'] ) : '1'; ?>-<?php echo wp_kses_post( $key ); ?>">
										<?php foreach ( get_b2be_products() as $product_id => $product_name ) { ?>
											<?php if ( wc_get_product( $product_id )->is_type( 'simple' ) ) { ?>
												<option value="<?php echo wp_kses_post( $product_id ); ?>"><?php echo wp_kses_post( $product_name ); ?></option>
											<?php } elseif ( wc_get_product( $product_id )->is_type( 'variable' ) ) { ?>
												<?php
												if ( ! empty( get_b2be_products_variration( intval( $product_id ) ) ) ) {
													?>
													<?php foreach ( get_b2be_products_variration( $product_id ) as $variation_id => $variation_name ) { ?>
														<option class="b2be-vari" value="<?php echo wp_kses_post( $variation_id ); ?>"> <?php echo wp_kses_post( $variation_name ); ?></option>
													<?php } ?>
												<?php } ?>
											<?php } ?>
										<?php } ?>
									</select>
								</div>
							</fieldset>
						</td>
					</tr>
			</tbody>
		</table>

		<table id="mov-inner-rows">
			<tr class="hide-by-default">
				<td class="quantity-discount" >
					<fieldset>
						<div  class="mov-quantity-base">
							<label for=""><b><?php echo esc_html__( 'Min Value', 'b2b-ecommerce' ); ?></b></label><br>
							<input name="mov-value-based-limit-min" id="mov-value-based-limit-min" style="width:auto;padding:5px;" type="number" min="0" >
						</div>			
					</fieldset>
				</td>
				<td class="quantity-discount" >
					<fieldset>
						<div class="mov-quantity-base">
							<label for=""><b><?php echo esc_html__( 'Max Value', 'b2b-ecommerce' ); ?></b></label><br>
							<input name="mov-value-based-limit-max" id="mov-value-based-limit-max" style="width:auto;padding:5px;" type="number" min="0" >
						</div>
					</fieldset>
				</td>
			</tr>
		</table>
	</div>
</div>
