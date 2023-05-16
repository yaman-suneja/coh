<?php
/**
 * Template for MOQ fields.
 *
 * @var $field_config string[]
 * @package B2B_E-commerce_For_WooCommerce/templates
 */

// prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<h2><?php echo esc_html__( 'Minimum Order Quantity', 'b2b-ecommerce' ); ?></h2>
<p><?php echo esc_html__( 'This will allow you to limit quantity on products', 'b2b-ecommerce' ); ?></p><br>
<span style=""><?php echo esc_html__( 'Enable MOQ Options', 'b2b-ecommerce' ); ?></span>
<label class="switch">
  <input type="checkbox" class="enable-moq-discount" id="enable-moq-discount" <?php echo ( 'true' == $is_b2be_moq_enable ) ? 'checked' : ''; ?> >
  <span class="moq-discount-toggle slider round"></span>
</label>
<div class="moq-template" id="moq-template">
	<?php
		$count = 1;
	?>
	<?php foreach ( $b2be_moq_rules as $index => $value ) { ?>

		<div style="<?php echo ( 'true' == $is_b2be_moq_enable ) ? '' : 'display:none'; ?>" class="moq-inner-template" id="moq-inner-template-<?php echo ( ! empty( $count ) ) ? wp_kses_post( $count ) : '1'; ?>" data-template-id="<?php echo ( ! empty( $count ) ) ? wp_kses_post( $count ) : '1'; ?>">
			<div id="moq-title-discounts">
				<h2>
					<select class="moq-priority" id="moq-rule-priority">
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
							<?php echo esc_html__( 'Role Based MOQ', 'b2b-ecommerce' ); ?>
							<?php echo wp_kses_post( wc_help_tip( __( 'Lets you set MOQ for user roles.', 'b2b-ecommerce' ) ) ); ?>
						</th>
						<td class="forminp forminp-checkbox">
							<fieldset>
								<legend class="screen-reader-text"><span>Role Based Discount</span></legend>
								<label for="">
									<input name="moq-role-based-enable" class="moq-role-based-enable" id="moq-role-based-enable" type="checkbox" <?php echo ( 'true' == $value['is_role_based'] ) ? 'checked' : ''; ?>> <?php echo esc_html__( 'Enable Role Based MOQ Functionality', 'b2b-ecommerce' ); ?>							
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
									<select multiple name="moq-b2be-role-selection" class="moq-b2be-role-selection" id="moq-b2be-role-selection-<?php echo ( ! empty( $value['ruleId'] ) ) ? wp_kses_post( $value['ruleId'] ) : '1'; ?>-<?php echo wp_kses_post( $key ); ?>">
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
							<?php echo esc_html__( 'Customer Based MOQ', 'b2b-ecommerce' ); ?>					
							<?php echo wp_kses_post( wc_help_tip( __( 'Lets you set MOQ for individual customers.', 'b2b-ecommerce' ) ) ); ?>
						</th>
						<td class="forminp forminp-checkbox">
							<fieldset>
								<legend class="screen-reader-text"><span>Customer Based Discount</span></legend>
								<label for="">
									<input name="moq-customer-based-enable" class="moq-customer-based-enable" id="moq-customer-based-enable" type="checkbox" <?php echo ( 'true' == $value['is_customer_based'] ) ? 'checked' : ''; ?>><?php echo esc_html__( 'Enable Customer Based MOQ Functionality', 'b2b-ecommerce' ); ?>							
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
									<select multiple name="moq-b2be-customer-selection" class="moq-b2be-customer-selection" id="moq-b2be-customer-selection-<?php echo ( ! empty( $value['ruleId'] ) ) ? wp_kses_post( $value['ruleId'] ) : '1'; ?>-<?php echo wp_kses_post( $index ); ?>" style="width:max-content">
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
							<?php echo esc_html__( 'Category Based MOQ', 'b2b-ecommerce' ); ?>
							<?php echo wp_kses_post( wc_help_tip( __( 'Lets you set MOQ for different categories.', 'b2b-ecommerce' ) ) ); ?>
						</th>
						<td class="forminp forminp-checkbox">
							<fieldset>
								<legend class="screen-reader-text"><span>Category Based Discount</span></legend>
								<label for="">
									<input name="moq-category-based-enable" class="moq-category-based-enable" id="moq-category-based-enable" type="checkbox" <?php echo ( 'true' == $value['is_category_based'] ) ? 'checked' : ''; ?>><?php echo esc_html__( 'Enable Category Based MOQ Functionality', 'b2b-ecommerce' ); ?>							
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
									<select multiple name="moq-b2be-category-selection" class="moq-b2be-category-selection" id="moq-b2be-category-selection-<?php echo ( ! empty( $value['ruleId'] ) ) ? wp_kses_post( $value['ruleId'] ) : '1'; ?>-<?php echo wp_kses_post( $key ); ?>">
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
							<?php echo esc_html__( 'Product Based MOQ', 'b2b-ecommerce' ); ?>
							<?php echo wp_kses_post( wc_help_tip( __( 'Lets you set MOQ for different products.', 'b2b-ecommerce' ) ) ); ?>
						</th>
						<td class="forminp forminp-checkbox">
							<fieldset>
								<legend class="screen-reader-text"><span>Product Based Discount</span></legend>
								<label for="">
									<input name="moq-product-based-enable" class="moq-product-based-enable" id="moq-product-based-enable" type="checkbox" <?php echo ( isset( $value['is_product_based'] ) && 'true' == $value['is_product_based'] ) ? 'checked' : ''; ?>><?php echo esc_html__( 'Enable Product Based MOQ Functionality', 'b2b-ecommerce' ); ?>
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
									<select multiple name="moq-b2be-product-selection" class="moq-b2be-product-selection" id="moq-b2be-product-selection-<?php echo ( ! empty( $value['ruleId'] ) ) ? wp_kses_post( $value['ruleId'] ) : '1'; ?>-<?php echo wp_kses_post( $key ); ?>">
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
					<tr valign="top">
						<th scope="row" style="width:210px" class="titledesc">
							<?php echo esc_html__( 'Quantity Format', 'b2b-ecommerce' ); ?>
							<?php echo wp_kses_post( wc_help_tip( __( 'Lets you change the format of quantity limit being applied on product.', 'b2b-ecommerce' ) ) ); ?>
						</th>
						<td class="forminp forminp-checkbox">
							<fieldset>
								<legend class="screen-reader-text"><span>Quantity Format</span></legend>
								<label for="">
									<input name="moq-quanity-format-<?php echo wp_kses_post( $value['ruleId'] ); ?>" class="moq-quanity-format" id="moq-quanity-format-range" type="radio" value="range" <?php echo ( isset( $value['quantity_format'] ) && 'range' == $value['quantity_format'] ) ? 'checked' : ''; ?>><?php echo esc_html__( 'Range', 'b2b-ecommerce' ); ?>
								</label><br>
								<label for="">
									<input name="moq-quanity-format-<?php echo wp_kses_post( $value['ruleId'] ); ?>" class="moq-quanity-format" id="moq-quanity-format-multi" type="radio" value="multiplier" <?php echo ( isset( $value['quantity_format'] ) && 'multiplier' == $value['quantity_format'] ) ? 'checked' : ''; ?>><?php echo esc_html__( 'Multipier', 'b2b-ecommerce' ); ?>
								</label>
							</fieldset>
						</td>
					</tr>
				</tbody>
			</table>

			<table id="moq-inner-rows">
				<?php foreach ( $value['innerRule'] as $key => $inner_value ) { ?>
					<tr class="hide-by-default">
						<td class="quantity-discount-multiplier">
							<fieldset>
								<div  class="moq-quantity-base">
									<label for=""><b><?php echo esc_html__( 'Multiplier', 'b2b-ecommerce' ); ?></b></label><br>
									<input name="moq-quantity-based-discount-multiplier" id="moq-quantity-based-discount-multiplier" style="width:auto;padding:5px;" type="number" min="0" value="<?php echo ( ! empty( $inner_value['multiplier'] ) ) ? wp_kses_post( intval( $inner_value['multiplier'] ) ) : ''; ?>">
								</div>			
							</fieldset>
						</td>
						<td class="quantity-discount" >
							<fieldset>
								<div  class="moq-quantity-base">
									<label for=""><b><?php echo esc_html__( 'Min Quantity', 'b2b-ecommerce' ); ?></b></label><br>
									<input name="moq-quantity-based-discount-min" id="moq-quantity-based-discount-min" style="width:auto;padding:5px;" type="number" min="0" value="<?php echo ( ! empty( $inner_value['minQuantity'] ) ) ? wp_kses_post( intval( $inner_value['minQuantity'] ) ) : ''; ?>">
								</div>			
							</fieldset>
						</td>
						<td class="quantity-discount" >
							<fieldset>
								<div class="moq-quantity-base">
									<label for=""><b><?php echo esc_html__( 'Max Quantity', 'b2b-ecommerce' ); ?></b></label><br>
									<input name="moq-quantity-based-discount-max" id="moq-quantity-based-discount-max" style="width:auto;padding:5px;" type="number" min="0" value="<?php echo ( ! empty( $inner_value['maxQuantity'] ) ) ? wp_kses_post( intval( $inner_value['maxQuantity'] ) ) : ''; ?>">
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
<input style="<?php echo ( 'true' == $is_b2be_moq_enable ) ? '' : 'display:none'; ?>" id="add_discount_rule" type="button" class="button-primary"value="<?php echo esc_html__( 'Add Rule', 'b2b-ecommerce' ); ?>">

<!-- Template -->
<div id="template" style="display:none;">
	<div class="moq-inner-template" id="moq-inner-template" data-template-id="<?php echo count( $b2be_moq_rules ); ?>">
		<div id="moq-title-discounts">
			<h2>
				<select class="moq-priority" id="moq-rule-priority">
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
						<?php echo esc_html__( 'Role Based MOQ', 'b2b-ecommerce' ); ?>
						<?php echo wp_kses_post( wc_help_tip( __( 'Lets you set MOQ for user roles.', 'b2b-ecommerce' ) ) ); ?>
					</th>
					<td class="forminp forminp-checkbox">
						<fieldset>
							<legend class="screen-reader-text"><span>Role Based Discount</span></legend>
							<label for="">
								<input name="moq-role-based-enable" class="moq-role-based-enable" id="moq-role-based-enable" type="checkbox"> <?php echo esc_html__( 'Enable Role Based MOQ Functionality', 'b2b-ecommerce' ); ?>							
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
								<select multiple name="moq-b2be-role-selection" class="moq-b2be-role-selection" id="moq-b2be-role-selection-<?php echo ( ! empty( $value['ruleId'] ) ) ? wp_kses_post( $value['ruleId'] ) : '1'; ?>-<?php echo wp_kses_post( $key ); ?>">
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
						<?php echo esc_html__( 'Customer Based MOQ', 'b2b-ecommerce' ); ?>					
						<?php echo wp_kses_post( wc_help_tip( __( 'Lets you set MOQ for individual customers.', 'b2b-ecommerce' ) ) ); ?>
					</th>
					<td class="forminp forminp-checkbox">
						<fieldset>
							<legend class="screen-reader-text"><span>Customer Based Discount</span></legend>
							<label for="">
								<input name="moq-customer-based-enable" class="moq-customer-based-enable" id="moq-customer-based-enable" type="checkbox" ><?php echo esc_html__( 'Enable Customer Based MOQ Functionality', 'b2b-ecommerce' ); ?>							
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
								<select multiple name="moq-b2be-customer-selection" class="moq-b2be-customer-selection" id="moq-b2be-customer-selection-<?php echo ( ! empty( $value['ruleId'] ) ) ? wp_kses_post( $value['ruleId'] ) : '1'; ?>-<?php echo wp_kses_post( $index ); ?>" style="width:max-content">
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
						<?php echo esc_html__( 'Category Based MOQ', 'b2b-ecommerce' ); ?>
						<?php echo wp_kses_post( wc_help_tip( __( 'Lets you set MOQ for different categories.', 'b2b-ecommerce' ) ) ); ?>
					</th>
					<td class="forminp forminp-checkbox">
						<fieldset>
							<legend class="screen-reader-text"><span>Category Based Discount</span></legend>
							<label for="">
								<input name="moq-category-based-enable" class="moq-category-based-enable" id="moq-category-based-enable" type="checkbox" ><?php echo esc_html__( 'Enable Category Based MOQ Functionality', 'b2b-ecommerce' ); ?>							
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
								<select multiple name="moq-b2be-category-selection" class="moq-b2be-category-selection" id="moq-b2be-category-selection-<?php echo ( ! empty( $value['ruleId'] ) ) ? wp_kses_post( $value['ruleId'] ) : '1'; ?>-<?php echo wp_kses_post( $key ); ?>">
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
							<?php echo esc_html__( 'Product Based MOQ', 'b2b-ecommerce' ); ?>
							<?php echo wp_kses_post( wc_help_tip( __( 'Lets you set MOQ for for different products.', 'b2b-ecommerce' ) ) ); ?>
						</th>
						<td class="forminp forminp-checkbox">
							<fieldset>
								<legend class="screen-reader-text"><span>Product Based Discount</span></legend>
								<label for="">
									<input name="moq-product-based-enable" class="moq-product-based-enable" id="moq-product-based-enable" type="checkbox"><?php echo esc_html__( 'Enable Product Based MOQ Functionality', 'b2b-ecommerce' ); ?>
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
									<select multiple name="moq-b2be-product-selection" class="moq-b2be-product-selection" id="moq-b2be-product-selection-<?php echo ( ! empty( $value['ruleId'] ) ) ? wp_kses_post( $value['ruleId'] ) : '1'; ?>-<?php echo wp_kses_post( $key ); ?>">
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
					<tr valign="top">
						<th scope="row" style="width:210px" class="titledesc">
							<?php echo esc_html__( 'Quantity Format', 'b2b-ecommerce' ); ?>
							<?php echo wp_kses_post( wc_help_tip( __( 'Lets you change the format of quantity limit being applied on product.', 'b2b-ecommerce' ) ) ); ?>
						</th>
						<td class="forminp forminp-checkbox">
							<fieldset>
								<legend class="screen-reader-text"><span>Quantity Format</span></legend>
								<label for="">
									<input class="moq-quanity-format" id="moq-quanity-format-range" type="radio" value="range" checked ><?php echo esc_html__( 'Range', 'b2b-ecommerce' ); ?>
								</label><br>
								<label for="">
									<input class="moq-quanity-format" id="moq-quanity-format-multi" type="radio" value="multiplier"><?php echo esc_html__( 'Multipier', 'b2b-ecommerce' ); ?>
								</label>
							</fieldset>
						</td>
					</tr>
			</tbody>
		</table>

		<table id="moq-inner-rows">
			<tr class="hide-by-default">
				<td class="quantity-discount-multiplier">
					<fieldset>
						<div  class="moq-quantity-base">
							<label for=""><b><?php echo esc_html__( 'Multiplier', 'b2b-ecommerce' ); ?></b></label><br>
							<input name="moq-quantity-based-discount-multiplier" id="moq-quantity-based-discount-multiplier" style="width:auto;padding:5px;" type="number" min="0" value="">
						</div>			
					</fieldset>
				</td>
				<td class="quantity-discount" >
					<fieldset>
						<div  class="moq-quantity-base">
							<label for=""><b><?php echo esc_html__( 'Min Quantity', 'b2b-ecommerce' ); ?></b></label><br>
							<input name="moq-quantity-based-discount-min" id="moq-quantity-based-discount-min" style="width:auto;padding:5px;" type="number" min="0" >
						</div>			
					</fieldset>
				</td>
				<td class="quantity-discount" >
					<fieldset>
						<div class="moq-quantity-base">
							<label for=""><b><?php echo esc_html__( 'Max Quantity', 'b2b-ecommerce' ); ?></b></label><br>
							<input name="moq-quantity-based-discount-max" id="moq-quantity-based-discount-max" style="width:auto;padding:5px;" type="number" min="0" >
						</div>
					</fieldset>
				</td>
			</tr>
		</table>
	</div>
</div>
