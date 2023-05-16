<?php
/**
 * Script for the template rendering of admin settings are defined here.
 *
 * @package templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<?php
	$hide_for_individual_user = get_option( CWCCV_PLUGIN_PREFIX . '_hide_for_individual_customer' );
	$hide_for_user_roles      = get_option( CWCCV_PLUGIN_PREFIX . '_hide_for_user_roles' );
	$hide_for_user_groups     = get_option( CWCCV_PLUGIN_PREFIX . '_hide_for_user_groups' );
	$hide_for_price_tier      = get_option( CWCCV_PLUGIN_PREFIX . '_hide_for_price_tier' );
	$hide_for_geo_location    = get_option( CWCCV_PLUGIN_PREFIX . '_hide_for_geo_location' );
?>
<div>
	<h3>
		<?php __( 'Visibility Settings', 'codup-woocommerce-catalog-visibility' ); ?>
	</h3>
	<table id="cwccv_visibility_table">
		<tr>
			<td>
				<?php
					$field_args['fields']['individual_customer_priority_select']['selected_option_value'] = isset( $hide_for_individual_user['priority'] ) ? $hide_for_individual_user['priority'] : '';
					CWCCV_Helper::get_priority_select( $field_args['fields']['individual_customer_priority_select'] );
				?>
			</td>
			<td>
				<h4 style="display:inline;">
					<?php esc_html_e( 'Individual Customer', 'codup-woocommerce-catalog-visibility' ); ?>
				</h4>
				<?php
					$field_args['fields']['individual_customer_settings_enable_toggle']['is_checked'] = $hide_for_individual_user['is_enable'];
					CWCCV_Helper::get_checkbox( $field_args['fields']['individual_customer_settings_enable_toggle'] );
				?>
			</td>
			<td>
				<div class="cwccv_tooltip"><span class="dashicons dashicons-editor-help"></span><span class="cwccv_tooltiptext cwccv_tooltip_bottom"><?php esc_html_e( 'Turning on the toggle will let you create visibility rule by selecting individual customers and defining which products/categories to show/hide to them.', 'codup-woocommerce-catalog-visibility' ); ?></span></div>
			</td>
		</tr>
		<!-- if individual customer toggle's value is set to no then hide the individual customer settings  -->
		<?php $is_hidden = ( ! isset( $hide_for_individual_user ) || ! $hide_for_individual_user['is_enable'] || 'no' == $hide_for_individual_user['is_enable'] ) ? 'hidden' : ''; ?>
		<tr class="cwccv_individual_customer_titles" <?php echo esc_attr( $is_hidden ); ?> >
			<td></td>
			<td>
				<h4>
					<?php esc_html_e( 'Customer Name', 'codup-woocommerce-catalog-visibility' ); ?><div class="cwccv_tooltip"><span class="dashicons dashicons-editor-help"></span><span class="cwccv_tooltiptext cwccv_tooltip_bottom"><?php esc_html_e( 'Search Customers', 'codup-woocommerce-catalog-visibility' ); ?></span></div>
				</h4>
			</td>
			<td>
				<h4>
					<?php esc_html_e( 'Action', 'codup-woocommerce-catalog-visibility' ); ?><div class="cwccv_tooltip"><span class="dashicons dashicons-editor-help"></span><span class="cwccv_tooltiptext cwccv_tooltip_bottom"><?php esc_html_e( 'Choose whether you want to hide or show the products or categories you will define.', 'codup-woocommerce-catalog-visibility' ); ?></span></div>
				</h4>
			</td>
			<td>
				<h4>
					<?php esc_html_e( 'By Category', 'codup-woocommerce-catalog-visibility' ); ?><div class="cwccv_tooltip"><span class="dashicons dashicons-editor-help"></span><span class="cwccv_tooltiptext cwccv_tooltip_bottom"><?php esc_html_e( 'Search Categories', 'codup-woocommerce-catalog-visibility' ); ?></span></div>
				</h4>
			</td>
			<td>
				<h4>
					<?php esc_html_e( 'By Product', 'codup-woocommerce-catalog-visibility' ); ?><div class="cwccv_tooltip"><span class="dashicons dashicons-editor-help"></span><span class="cwccv_tooltiptext cwccv_tooltip_bottom"><?php esc_html_e( 'Search Products', 'codup-woocommerce-catalog-visibility' ); ?></span></div>
				</h4>
			</td>
			<td>
				<?php CWCCV_Helper::get_repeater_fields_button( $field_args['fields']['individual_customer_repeater_field_button'] ); ?>
			</td>
		</tr>

		<?php if ( isset( $hide_for_individual_user ) && $hide_for_individual_user && isset( $hide_for_individual_user['rules'] ) ) { ?>
			<?php foreach ( $hide_for_individual_user['rules'] as $loop => $value ) { ?>
				<tr class="cwccv_individual_user_row" data-id="<?php echo esc_attr( $loop ); ?>" <?php echo esc_attr( $is_hidden ); ?> >
					<td></td>
					<td>
						<?php
							$field_args['fields']['individual_customer_customer_name_select']['selected_option_value'] = isset( $value['customers'] ) ? $value['customers'] : '';
							$customer_name = $field_args['fields']['individual_customer_customer_name_select']['name'] . '[' . $loop . '][]';
							$select_id     = $field_args['fields']['individual_customer_customer_name_select']['name'] . '_' . $loop;
							CWCCV_Helper::get_select( $customer_name, $field_args['fields']['individual_customer_customer_name_select'], $select_id );
						?>
					</td>
					<td>
						<div class="cwccv_show_hide_radio_buttons" >
							<label for="">
								<?php esc_html_e( 'Show', 'codup-woocommerce-catalog-visibility' ); ?>
							</label>
							<?php
							if ( isset( $value['is_shown'] ) && 'yes' == $value['is_shown'] ) {
								$field_args['fields']['individual_customer_product_show_radio']['is_checked'] = true;
							} else {
								$field_args['fields']['individual_customer_product_show_radio']['is_checked'] = false;
							}
								$show_hide_button = $field_args['fields']['individual_customer_product_show_radio']['name'] . '[' . $loop . ']';
								CWCCV_Helper::get_radio_button( $show_hide_button, $field_args['fields']['individual_customer_product_show_radio'] );
							?>
						</div>
						<div class="cwccv_show_hide_radio_buttons" >
							<label for="">
								<?php esc_html_e( 'Hide', 'codup-woocommerce-catalog-visibility' ); ?>
							</label>
							<?php
							if ( isset( $value['is_shown'] ) && 'no' == $value['is_shown'] ) {
								$field_args['fields']['individual_customer_product_hide_radio']['is_checked'] = true;
							} else {
								$field_args['fields']['individual_customer_product_hide_radio']['is_checked'] = false; }
								$show_hide_button = $field_args['fields']['individual_customer_product_hide_radio']['name'] . '[' . $loop . ']';
								CWCCV_Helper::get_radio_button( $show_hide_button, $field_args['fields']['individual_customer_product_hide_radio'] );
							?>
						</div>
					</td>
					<td>
						<?php
							$field_args['fields']['individual_customer_category_select']['selected_option_value'] = isset( $value['categories'] ) ? $value['categories'] : '';
							$category_name = $field_args['fields']['individual_customer_category_select']['name'] . '[' . $loop . '][]';
							CWCCV_Helper::get_select( $category_name, $field_args['fields']['individual_customer_category_select'] );
						?>
					</td>
					<td>
						<?php
							$field_args['fields']['individual_customer_product_select']['selected_option_value'] = isset( $value['products'] ) ? $value['products'] : '';
							$product_name = $field_args['fields']['individual_customer_product_select']['name'] . '[' . $loop . '][]';
							CWCCV_Helper::get_select( $product_name, $field_args['fields']['individual_customer_product_select'] );
						?>
					</td>
					<td>
						<button type="button" class="cwccv_ind_user_remove_row_btn cwccv_remove_row_btn">x</button>
					</td>
				</tr>
			<?php } ?>
			<?php
		} else {
			$index = 0;
			?>
			<tr class="cwccv_individual_user_row" data-id="<?php echo esc_attr( $index ); ?>" <?php echo esc_attr( $is_hidden ); ?> >
				<td></td>
				<td>
					<?php
						$customer_name = $field_args['fields']['individual_customer_customer_name_select']['name'] . '[' . $index . '][]';
						CWCCV_Helper::get_select( $customer_name, $field_args['fields']['individual_customer_customer_name_select'] );
					?>
				</td>
				<td>
					<div class="cwccv_show_hide_radio_buttons" >
						<label for="">
							<?php esc_html_e( 'Show', 'codup-woocommerce-catalog-visibility' ); ?>
						</label>
						<?php
							$radio_button_name = $field_args['fields']['individual_customer_product_show_radio']['name'] . '[' . $index . ']';
							CWCCV_Helper::get_radio_button( $radio_button_name, $field_args['fields']['individual_customer_product_show_radio'] );
						?>
					</div>
					<div class="cwccv_show_hide_radio_buttons" >
						<label for="">
							<?php esc_html_e( 'Hide', 'codup-woocommerce-catalog-visibility' ); ?>
						</label>
						<?php
							$radio_button_name = $field_args['fields']['individual_customer_product_show_radio']['name'] . '[' . $index . ']';
							CWCCV_Helper::get_radio_button( $radio_button_name, $field_args['fields']['individual_customer_product_hide_radio'] );
						?>
					</div>
				</td>
				<td>
					<?php
						$category_name = $field_args['fields']['individual_customer_category_select']['name'] . '[' . $index . '][]';
						CWCCV_Helper::get_select( $category_name, $field_args['fields']['individual_customer_category_select'] );
					?>
				</td>
				<td>
					<?php
						$product_name = $field_args['fields']['individual_customer_product_select']['name'] . '[' . $index . '][]';
						CWCCV_Helper::get_select( $product_name, $field_args['fields']['individual_customer_product_select'] );
					?>
				</td>
				<td>
					<button type="button" class="cwccv_ind_user_remove_row_btn cwccv_remove_row_btn">x</button>
				</td>
			</tr>
		<?php } ?>
		<!-- End individual visibility settings -->
		<br>
		<!-- User Roles settings -->
		<tr>
			<td>
				<?php
					$field_args['fields']['user_roles_priority_select']['selected_option_value'] = isset( $hide_for_user_roles['priority'] ) ? $hide_for_user_roles['priority'] : '2';
					CWCCV_Helper::get_priority_select( $field_args['fields']['user_roles_priority_select'] );
				?>
			</td>
			<td>
				<h4 style="display:inline;">
					<?php esc_html_e( 'User Roles', 'codup-woocommerce-catalog-visibility' ); ?>
				</h4>
				<?php
					$field_args['fields']['user_roles_settings_enable_toggle']['is_checked'] = $hide_for_user_roles['is_enable'];
					CWCCV_Helper::get_checkbox( $field_args['fields']['user_roles_settings_enable_toggle'] );
				?>
			</td>
			<td>
				<div class="cwccv_tooltip"><span class="dashicons dashicons-editor-help"></span><span class="cwccv_tooltiptext cwccv_tooltip_bottom"><?php esc_html_e( 'Turning on the toggle will let you create visibility rule by selecting custom user roles and defining which products/categories to show/hide to them.', 'codup-woocommerce-catalog-visibility' ); ?></span></div>
			</td>
		</tr>
		<!-- If user roles toggle's value is set to no, then hide user roles settings -->
		<?php $is_hidden = ( ! isset( $hide_for_user_roles ) || ! $hide_for_user_roles['is_enable'] || 'no' == $hide_for_user_roles['is_enable'] ) ? 'hidden' : ''; ?>
		<!-- User roles settings titles -->
		<tr class="cwccv_user_roles_titles" <?php echo esc_attr( $is_hidden ); ?> >
			<td></td>
			<td>
				<h4>
					<?php esc_html_e( 'User Roles', 'codup-woocommerce-catalog-visibility' ); ?><div class="cwccv_tooltip"><span class="dashicons dashicons-editor-help"></span><span class="cwccv_tooltiptext cwccv_tooltip_bottom"><?php esc_html_e( 'Search User Roles', 'codup-woocommerce-catalog-visibility' ); ?></span></div>
				</h4>
			</td>
			<td>
				<h4>
					<?php esc_html_e( 'Action', 'codup-woocommerce-catalog-visibility' ); ?><div class="cwccv_tooltip"><span class="dashicons dashicons-editor-help"></span><span class="cwccv_tooltiptext cwccv_tooltip_bottom"><?php esc_html_e( 'Choose whether you want to hide or show the products or categories you’ll define.', 'codup-woocommerce-catalog-visibility' ); ?></span></div>
				</h4>
			</td>
			<td>
				<h4>
					<?php esc_html_e( 'By Category', 'codup-woocommerce-catalog-visibility' ); ?><div class="cwccv_tooltip"><span class="dashicons dashicons-editor-help"></span><span class="cwccv_tooltiptext cwccv_tooltip_bottom"><?php esc_html_e( 'Search Categories', 'codup-woocommerce-catalog-visibility' ); ?></span></div>
				</h4>
			</td>
			<td>
				<h4>
					<?php esc_html_e( 'By Product', 'codup-woocommerce-catalog-visibility' ); ?><div class="cwccv_tooltip"><span class="dashicons dashicons-editor-help"></span><span class="cwccv_tooltiptext cwccv_tooltip_bottom"><?php esc_html_e( 'Search Products', 'codup-woocommerce-catalog-visibility' ); ?></span></div>
				</h4>
			</td>
			<td>
				<?php CWCCV_Helper::get_repeater_fields_button( $field_args['fields']['user_roles_repeater_field_button'] ); ?>
			</td>
		</tr>

		<?php if ( isset( $hide_for_user_roles ) && ! empty( $hide_for_user_roles ) && isset( $hide_for_user_roles['rules'] ) ) { ?>
			<?php foreach ( $hide_for_user_roles['rules'] as $loop => $value ) { ?>
				<tr class="cwccv_user_roles_row" data-id="<?php echo esc_attr( $loop ); ?>" <?php echo esc_attr( $is_hidden ); ?> >
					<td></td>
					<td>
						<?php
							$field_args['fields']['user_roles_roles_name_select']['selected_option_value'] = isset( $value['user_roles'] ) ? $value['user_roles'] : '';
							$customer_name = $field_args['fields']['user_roles_roles_name_select']['name'] . '[' . $loop . '][]';
							CWCCV_Helper::get_select( $customer_name, $field_args['fields']['user_roles_roles_name_select'] );
						?>
					</td>
					<td>
						<div class="cwccv_show_hide_radio_buttons" >
							<label for="">
								<?php esc_html_e( 'Show', 'codup-woocommerce-catalog-visibility' ); ?>
							</label>
							<?php
							if ( isset( $value['is_shown'] ) && 'yes' == $value['is_shown'] ) {
								$field_args['fields']['user_roles_product_show_radio']['is_checked'] = true;
							} else {
								$field_args['fields']['user_roles_product_show_radio']['is_checked'] = false;
							}
								$show_hide_button = $field_args['fields']['user_roles_product_show_radio']['name'] . '[' . $loop . ']';
								CWCCV_Helper::get_radio_button( $show_hide_button, $field_args['fields']['user_roles_product_show_radio'] );
							?>
						</div>
						<div class="cwccv_show_hide_radio_buttons" >
							<label for="">
								<?php esc_html_e( 'Hide', 'codup-woocommerce-catalog-visibility' ); ?>
							</label>
							<?php
							if ( isset( $value['is_shown'] ) && 'no' == $value['is_shown'] ) {
								$field_args['fields']['user_roles_product_hide_radio']['is_checked'] = true;
							} else {
								$field_args['fields']['user_roles_product_hide_radio']['is_checked'] = false;
							}
								$show_hide_button = $field_args['fields']['user_roles_product_hide_radio']['name'] . '[' . $loop . ']';
								CWCCV_Helper::get_radio_button( $show_hide_button, $field_args['fields']['user_roles_product_hide_radio'] );
							?>
						</div>
					</td>
					<td>
						<?php
							$field_args['fields']['user_roles_category_select']['selected_option_value'] = isset( $value['categories'] ) ? $value['categories'] : '';
							$category_name = $field_args['fields']['user_roles_category_select']['name'] . '[' . $loop . '][]';
							CWCCV_Helper::get_select( $category_name, $field_args['fields']['user_roles_category_select'] );
						?>
					</td>
					<td>
						<?php
							$field_args['fields']['user_roles_product_select']['selected_option_value'] = isset( $value['products'] ) ? $value['products'] : '';
							$product_name = $field_args['fields']['user_roles_product_select']['name'] . '[' . $loop . '][]';
							CWCCV_Helper::get_select( $product_name, $field_args['fields']['user_roles_product_select'] );
						?>
					</td>
					<td>
						<button type="button" class="cwccv_user_roles_remove_row_btn cwccv_remove_row_btn">x</button>
					</td>
				</tr>
			<?php } ?>
			<?php
		} else {
			$index = 0;
			?>
			<tr class="cwccv_user_roles_row" data-id="<?php echo esc_attr( $index ); ?>" <?php echo esc_attr( $is_hidden ); ?> >
				<td></td>
				<td>
					<?php
						$user_role_name = $field_args['fields']['user_roles_roles_name_select']['name'] . '[' . $index . '][]';
						CWCCV_Helper::get_select( $user_role_name, $field_args['fields']['user_roles_roles_name_select'] );
					?>
				</td>
				<td>
					<div class="cwccv_show_hide_radio_buttons" >
						<label for="">
							<?php esc_html_e( 'Show', 'codup-woocommerce-catalog-visibility' ); ?>
						</label>
						<?php
							$radio_button_name = $field_args['fields']['user_roles_product_show_radio']['name'] . '[' . $index . ']';
							CWCCV_Helper::get_radio_button( $radio_button_name, $field_args['fields']['user_roles_product_show_radio'] );
						?>
					</div>
					<div class="cwccv_show_hide_radio_buttons" >
						<label for="">
							<?php esc_html_e( 'Hide', 'codup-woocommerce-catalog-visibility' ); ?>
						</label>
						<?php
							$radio_button_name = $field_args['fields']['user_roles_product_hide_radio']['name'] . '[' . $index . ']';
							CWCCV_Helper::get_radio_button( $radio_button_name, $field_args['fields']['user_roles_product_hide_radio'] );
						?>
					</div>
				</td>
				<td>
					<?php
						$category_name = $field_args['fields']['user_roles_category_select']['name'] . '[' . $index . '][]';
						CWCCV_Helper::get_select( $category_name, $field_args['fields']['user_roles_category_select'] );
					?>
				</td>
				<td>
					<?php
						$product_name = $field_args['fields']['user_roles_product_select']['name'] . '[' . $index . '][]';
						CWCCV_Helper::get_select( $product_name, $field_args['fields']['user_roles_product_select'] );
					?>
				</td>
				<td>
					<button type="button" class="cwccv_user_roles_remove_row_btn cwccv_remove_row_btn">x</button>
				</td>
			</tr>
		<?php } ?>
		<!-- End user roles settings -->

		<!-- User groups settings -->
		<tr>
			<td>
				<?php
					$field_args['fields']['user_groups_priority_select']['selected_option_value'] = isset( $hide_for_user_groups['priority'] ) ? $hide_for_user_groups['priority'] : '3';
					CWCCV_Helper::get_priority_select( $field_args['fields']['user_groups_priority_select'] );
				?>
			</td>
			<td>
				<h4 style="display:inline;">
					<?php esc_html_e( 'User Groups', 'codup-woocommerce-catalog-visibility' ); ?>
				</h4>
				<?php $field_args['fields']['user_groups_settings_enable_toggle']['is_checked'] = $hide_for_user_groups['is_enable']; ?>
				<?php CWCCV_Helper::get_checkbox( $field_args['fields']['user_groups_settings_enable_toggle'] ); ?>
			</td>
			<td>
				<div class="cwccv_tooltip"><span class="dashicons dashicons-editor-help"></span><span class="cwccv_tooltiptext cwccv_tooltip_bottom"><?php esc_html_e( 'Turning on the toggle will let you create visibility rule by selecting custom user groups and defining which products/categories to show/hide to them.', 'codup-woocommerce-catalog-visibility' ); ?></span></div>
			</td>
		</tr>
		<!-- If user groups toggle's value is set to no then hide the user group settings -->
		<?php $is_hidden = ( ! isset( $hide_for_user_groups ) || ! $hide_for_user_groups['is_enable'] || 'no' == $hide_for_user_groups['is_enable'] ) ? 'hidden' : ''; ?>
		<tr class="cwccv_user_groups_titles" <?php echo esc_attr( $is_hidden ); ?> >
			<td></td>
			<td>
				<h4>
					<?php esc_html_e( 'User Groups', 'codup-woocommerce-catalog-visibility' ); ?><div class="cwccv_tooltip"><span class="dashicons dashicons-editor-help"></span><span class="cwccv_tooltiptext cwccv_tooltip_bottom"><?php esc_html_e( 'Search User Groups', 'codup-woocommerce-catalog-visibility' ); ?></span></div>
				</h4>
			</td>
			<td>
				<h4>
					<?php esc_html_e( 'Action', 'codup-woocommerce-catalog-visibility' ); ?><div class="cwccv_tooltip"><span class="dashicons dashicons-editor-help"></span><span class="cwccv_tooltiptext cwccv_tooltip_bottom"><?php esc_html_e( 'Choose whether you want to hide or show the products or categories you’ll define.', 'codup-woocommerce-catalog-visibility' ); ?></span></div>
				</h4>
			</td>
			<td>
				<h4>
					<?php esc_html_e( 'By Category', 'codup-woocommerce-catalog-visibility' ); ?><div class="cwccv_tooltip"><span class="dashicons dashicons-editor-help"></span><span class="cwccv_tooltiptext cwccv_tooltip_bottom"><?php esc_html_e( 'Search Categories', 'codup-woocommerce-catalog-visibility' ); ?></span></div>
				</h4>
			</td>
			<td>
				<h4>
					<?php esc_html_e( 'By Product', 'codup-woocommerce-catalog-visibility' ); ?><div class="cwccv_tooltip"><span class="dashicons dashicons-editor-help"></span><span class="cwccv_tooltiptext cwccv_tooltip_bottom"><?php esc_html_e( 'Search Products', 'codup-woocommerce-catalog-visibility' ); ?></span></div>
				</h4>
			</td>
			<td>
				<?php CWCCV_Helper::get_repeater_fields_button( $field_args['fields']['user_groups_repeater_field_button'] ); ?>
			</td>
		</tr>
		<?php if ( isset( $hide_for_user_groups ) && ! empty( $hide_for_user_groups ) && isset( $hide_for_user_groups['rules'] ) ) { ?>
			<?php foreach ( $hide_for_user_groups['rules'] as $loop => $value ) { ?>
				<tr class="cwccv_user_groups_row" data-id="<?php echo esc_attr( $loop ); ?>" <?php echo esc_attr( $is_hidden ); ?> >
					<td></td>
					<td>
						<?php
							$field_args['fields']['user_groups_groups_name_select']['selected_option_value'] = isset( $value['user_groups'] ) ? $value['user_groups'] : '';
							$customer_name = $field_args['fields']['user_groups_groups_name_select']['name'] . '[' . $loop . '][]';
							CWCCV_Helper::get_select( $customer_name, $field_args['fields']['user_groups_groups_name_select'] );
						?>
					</td>
					<td>
						<div class="cwccv_show_hide_radio_buttons" >
							<label for="">
								<?php esc_html_e( 'Show', 'codup-woocommerce-catalog-visibility' ); ?>
							</label>
							<?php
							if ( isset( $value['is_shown'] ) && 'yes' == $value['is_shown'] ) {
								$field_args['fields']['user_groups_product_show_radio']['is_checked'] = true;
							} else {
								$field_args['fields']['user_groups_product_show_radio']['is_checked'] = false;
							}
								$show_hide_button = $field_args['fields']['user_groups_product_show_radio']['name'] . '[' . $loop . ']';
								CWCCV_Helper::get_radio_button( $show_hide_button, $field_args['fields']['user_groups_product_show_radio'] );
							?>
						</div>
						<div class="cwccv_show_hide_radio_buttons" >
							<label for="">
								<?php esc_html_e( 'Hide', 'codup-woocommerce-catalog-visibility' ); ?>
							</label>
							<?php
							if ( isset( $value['is_shown'] ) && 'no' == $value['is_shown'] ) {
								$field_args['fields']['user_groups_product_hide_radio']['is_checked'] = true;
							} else {
								$field_args['fields']['user_groups_product_hide_radio']['is_checked'] = false;
							}
								$show_hide_button = $field_args['fields']['user_groups_product_hide_radio']['name'] . '[' . $loop . ']';
								CWCCV_Helper::get_radio_button( $show_hide_button, $field_args['fields']['user_groups_product_hide_radio'] );
							?>
						</div>
					</td>
					<td>
						<?php
							$field_args['fields']['user_groups_category_select']['selected_option_value'] = isset( $value['categories'] ) ? $value['categories'] : '';
							$category_name = $field_args['fields']['user_groups_category_select']['name'] . '[' . $loop . '][]';
							CWCCV_Helper::get_select( $category_name, $field_args['fields']['user_groups_category_select'] );
						?>
					</td>
					<td>
						<?php
							$field_args['fields']['user_groups_product_select']['selected_option_value'] = isset( $value['products'] ) ? $value['products'] : '';
							$product_name = $field_args['fields']['user_groups_product_select']['name'] . '[' . $loop . '][]';
							CWCCV_Helper::get_select( $product_name, $field_args['fields']['user_groups_product_select'] );
						?>
					</td>
					<td>
						<button type="button" class="cwccv_user_groups_remove_row_btn cwccv_remove_row_btn">x</button>
					</td>
				</tr>
			<?php } ?>
			<?php
		} else {
			$index = 0;
			?>
			<tr class="cwccv_user_groups_row" data-id="<?php echo esc_attr( $index ); ?>" <?php echo esc_attr( $is_hidden ); ?> >
				<td></td>
				<td>
					<?php
						$user_role_name = $field_args['fields']['user_groups_groups_name_select']['name'] . '[' . $index . '][]';
						CWCCV_Helper::get_select( $user_role_name, $field_args['fields']['user_groups_groups_name_select'] );
					?>
				</td>
				<td>
					<div class="cwccv_show_hide_radio_buttons" >
						<label for="">
							<?php esc_html_e( 'Show', 'codup-woocommerce-catalog-visibility' ); ?>
						</label>
						<?php
							$radio_button_name = $field_args['fields']['user_groups_product_show_radio']['name'] . '[' . $index . ']';
							CWCCV_Helper::get_radio_button( $radio_button_name, $field_args['fields']['user_groups_product_show_radio'] );
						?>
					</div>
					<div class="cwccv_show_hide_radio_buttons" >
						<label for="">
							<?php esc_html_e( 'Hide', 'codup-woocommerce-catalog-visibility' ); ?>
						</label>
						<?php
							$radio_button_name = $field_args['fields']['user_groups_product_show_radio']['name'] . '[' . $index . ']';
							CWCCV_Helper::get_radio_button( $radio_button_name, $field_args['fields']['user_groups_product_hide_radio'] );
						?>
					</div>
				</td>
				<td>
					<?php
						$category_name = $field_args['fields']['user_groups_category_select']['name'] . '[' . $index . '][]';
						CWCCV_Helper::get_select( $category_name, $field_args['fields']['user_groups_category_select'] );
					?>
				</td>
				<td>
					<?php
						$product_name = $field_args['fields']['user_groups_product_select']['name'] . '[' . $index . '][]';
						CWCCV_Helper::get_select( $product_name, $field_args['fields']['user_groups_product_select'] );
					?>
				</td>
				<td>
					<button type="button" class="cwccv_user_groups_remove_row_btn cwccv_remove_row_btn">x</button>
				</td>
			</tr>
		<?php } ?>
		<!-- End user groups settings -->

		<!-- Price Tier settings -->
		<tr>
			<td>
				<?php
					$field_args['fields']['price_tier_priority_select']['selected_option_value'] = isset( $hide_for_price_tier['priority'] ) ? $hide_for_price_tier['priority'] : '4';
					CWCCV_Helper::get_priority_select( $field_args['fields']['price_tier_priority_select'] );
				?>
			</td>
			<td>
				<h4 style="display:inline;">
					<?php esc_html_e( 'Price Tier', 'codup-woocommerce-catalog-visibility' ); ?>
				</h4>
				<?php
					$field_args['fields']['price_tier_settings_enable_toggle']['is_checked'] = $hide_for_price_tier['is_enable'];
					CWCCV_Helper::get_checkbox( $field_args['fields']['price_tier_settings_enable_toggle'] );
				?>
			</td>
			<td>
				<div class="cwccv_tooltip"><span class="dashicons dashicons-editor-help"></span><span class="cwccv_tooltiptext cwccv_tooltip_bottom"><?php esc_html_e( 'Turning on the toggle will let you create visibility rule by creating a custom purchase tier and defining which products/categories to show/hide to customers falling in that tier.', 'codup-woocommerce-catalog-visibility' ); ?></span></div>
			</td>
		</tr>
		<!-- If user groups toggle's value is set to no then hide the user group settings -->
		<?php $is_hidden = ( ! isset( $hide_for_price_tier ) || ! $hide_for_price_tier['is_enable'] || 'no' == $hide_for_price_tier['is_enable'] ) ? 'hidden' : ''; ?>
		<tr class="cwccv_price_tier_titles" <?php echo esc_attr( $is_hidden ); ?> >
			<td></td>
			<td>
				<h4>
					<?php esc_html_e( 'Total order Volume', 'codup-woocommerce-catalog-visibility' ); ?><div class="cwccv_tooltip"><span class="dashicons dashicons-editor-help"></span><span class="cwccv_tooltiptext cwccv_tooltip_bottom"><?php esc_html_e( 'Set Order Volumn', 'codup-woocommerce-catalog-visibility' ); ?></span></div>
				</h4>
			</td>
			<td>
				<h4>
					<?php esc_html_e( 'Action', 'codup-woocommerce-catalog-visibility' ); ?><div class="cwccv_tooltip"><span class="dashicons dashicons-editor-help"></span><span class="cwccv_tooltiptext cwccv_tooltip_bottom"><?php esc_html_e( 'Choose whether you want to hide or show the products or categories you’ll define.', 'codup-woocommerce-catalog-visibility' ); ?></span></div>
				</h4>
			</td>
			<td>
				<h4>
					<?php esc_html_e( 'By Category', 'codup-woocommerce-catalog-visibility' ); ?><div class="cwccv_tooltip"><span class="dashicons dashicons-editor-help"></span><span class="cwccv_tooltiptext cwccv_tooltip_bottom"><?php esc_html_e( 'Search Categories', 'codup-woocommerce-catalog-visibility' ); ?></span></div>
				</h4>
			</td>
			<td>
				<h4>
					<?php esc_html_e( 'By Product', 'codup-woocommerce-catalog-visibility' ); ?><div class="cwccv_tooltip"><span class="dashicons dashicons-editor-help"></span><span class="cwccv_tooltiptext cwccv_tooltip_bottom"><?php esc_html_e( 'Search Products', 'codup-woocommerce-catalog-visibility' ); ?></span></div>
				</h4>
			</td>
			<td>
				<?php CWCCV_Helper::get_repeater_fields_button( $field_args['fields']['price_tier_repeater_field_button'] ); ?>
			</td>
		</tr>
		<?php if ( isset( $hide_for_price_tier ) && ! empty( $hide_for_price_tier ) && isset( $hide_for_price_tier['rules'][0]['is_shown'] ) ) { ?>
			<?php
			foreach ( $hide_for_price_tier['rules'] as $loop => $value ) {
				if ( empty( $value['price']['from'] ) && empty( $value['price']['to'] ) ) {
					continue;
				}
				?>
				<tr class="cwccv_price_tier_row" data-id="<?php echo esc_attr( $loop ); ?>" <?php echo esc_attr( $is_hidden ); ?> >
					<td></td>
					<td>
						<?php
							$field_args['fields']['price_tier_from_text_field']['value'] = isset( $value['price_from'] ) ? $value['price_from'] : '';
							$price_from_name  = $field_args['fields']['price_tier_from_text_field']['name'] . '[]';
							$price_from_class = $field_args['fields']['price_tier_from_text_field']['class'];
							$price_from_value = $value['price']['from'];
						?>
						<input type="number" name = "<?php echo esc_attr( $price_from_name ); ?>" class="<?php echo esc_attr( $price_from_class ); ?>" value="<?php echo esc_attr( $price_from_value ); ?>" data-ind="<?php echo esc_attr( $loop ); ?>">
						<?php
							$field_args['fields']['price_tier_to_text_field']['value'] = isset( $value['price_to'] ) ? $value['price_to'] : '';
							$price_to_name  = $field_args['fields']['price_tier_to_text_field']['name'] . '[]';
							$price_to_class = $field_args['fields']['price_tier_to_text_field']['class'];
							$price_to_value = $value['price']['to'];
						?>
						<input type="number" name = "<?php echo esc_attr( $price_to_name ); ?>" class="<?php echo esc_attr( $price_to_class ); ?>" value="<?php echo esc_attr( $price_to_value ); ?>" data-ind="<?php echo esc_attr( $loop ); ?>">
					</td>
					<td>
						<div class="cwccv_show_hide_radio_buttons" >
							<label for="">
								<?php esc_html_e( 'Show', 'codup-woocommerce-catalog-visibility' ); ?>
							</label>
							<?php
							if ( isset( $value['is_shown'] ) && 'yes' == $value['is_shown'] ) {
								$field_args['fields']['price_tier_product_show_radio']['is_checked'] = true;
							} else {
								$field_args['fields']['price_tier_product_show_radio']['is_checked'] = false; }
								$show_hide_button = $field_args['fields']['price_tier_product_show_radio']['name'] . '[' . $loop . ']';
								CWCCV_Helper::get_radio_button( $show_hide_button, $field_args['fields']['price_tier_product_show_radio'] );
							?>
						</div>
						<div class="cwccv_show_hide_radio_buttons" >
							<label for="">
								<?php esc_html_e( 'Hide', 'codup-woocommerce-catalog-visibility' ); ?>
							</label>
							<?php
							if ( isset( $value['is_shown'] ) && 'no' == $value['is_shown'] ) {
								$field_args['fields']['price_tier_product_hide_radio']['is_checked'] = true;
							} else {
								$field_args['fields']['price_tier_product_hide_radio']['is_checked'] = false;
							}
								$show_hide_button = $field_args['fields']['price_tier_product_hide_radio']['name'] . '[' . $loop . ']';
								CWCCV_Helper::get_radio_button( $show_hide_button, $field_args['fields']['price_tier_product_hide_radio'] );
							?>
						</div>
					</td>
					<td>
						<?php
							$field_args['fields']['price_tier_category_select']['selected_option_value'] = isset( $value['categories'] ) ? $value['categories'] : '';
							$category_name = $field_args['fields']['price_tier_category_select']['name'] . '[' . $loop . '][]';
							CWCCV_Helper::get_select( $category_name, $field_args['fields']['price_tier_category_select'] );
						?>
					</td>
					<td>
						<?php
							$field_args['fields']['price_tier_product_select']['selected_option_value'] = isset( $value['products'] ) ? $value['products'] : '';
							$product_name = $field_args['fields']['price_tier_product_select']['name'] . '[' . $loop . '][]';
							CWCCV_Helper::get_select( $product_name, $field_args['fields']['price_tier_product_select'] );
						?>
					</td>
					<td>
						<button type="button" class="cwccv_price_tier_remove_row_btn cwccv_remove_row_btn">x</button>
					</td>
				</tr>
			<?php } ?>
			<?php
		} else {
			$index = 0;
			?>
			<tr class="cwccv_price_tier_row" data-id="<?php echo esc_attr( $index ); ?>" <?php echo esc_attr( $is_hidden ); ?> >
				<td></td>
				<td>
					<?php
						$field_args['fields']['price_tier_from_text_field']['value'] = isset( $value['price_from'] ) ? $value['price_from'] : '';
						$price_from_name  = $field_args['fields']['price_tier_from_text_field']['name'] . '[]';
						$price_from_class = $field_args['fields']['price_tier_from_text_field']['class'];
					?>
					<input style="width:50%;display:inline;" type="number" name = "<?php echo esc_attr( $price_from_name ); ?>" class="<?php echo esc_attr( $price_from_class ); ?>" placeholder="Price From" >
					<?php
						$field_args['fields']['price_tier_to_text_field']['value'] = isset( $value['price_to'] ) ? $value['price_to'] : '';
						$price_to_name  = $field_args['fields']['price_tier_to_text_field']['name'] . '[]';
						$price_to_class = $field_args['fields']['price_tier_to_text_field']['class'];
					?>
					<input style="width:50%;display:inline;" type="number" name = "<?php echo esc_attr( $price_to_name ); ?>" class="<?php echo esc_attr( $price_to_class ); ?>" placeholder="Price To">
				</td>
				<td>
					<div class="cwccv_show_hide_radio_buttons" >
						<label for="">
							<?php esc_html_e( 'Show', 'codup-woocommerce-catalog-visibility' ); ?>
						</label>
						<?php
							$radio_button_name = $field_args['fields']['price_tier_product_show_radio']['name'] . '[' . $index . ']';
							CWCCV_Helper::get_radio_button( $radio_button_name, $field_args['fields']['price_tier_product_show_radio'] );
						?>
					</div>
					<div class="cwccv_show_hide_radio_buttons" >
						<label for="">
							<?php esc_html_e( 'Hide', 'codup-woocommerce-catalog-visibility' ); ?>
						</label>
						<?php
							$radio_button_name = $field_args['fields']['price_tier_product_show_radio']['name'] . '[' . $index . ']';
							CWCCV_Helper::get_radio_button( $radio_button_name, $field_args['fields']['price_tier_product_hide_radio'] );
						?>
					</div>
				</td>
				<td>
					<?php
						$category_name = $field_args['fields']['price_tier_category_select']['name'] . '[' . $index . '][]';
						CWCCV_Helper::get_select( $category_name, $field_args['fields']['price_tier_category_select'] );
					?>
				</td>
				<td>
					<?php
						$product_name = $field_args['fields']['price_tier_product_select']['name'] . '[' . $index . '][]';
						CWCCV_Helper::get_select( $product_name, $field_args['fields']['price_tier_product_select'] );
					?>
				</td>
				<td>
					<button type="button" class="cwccv_price_tier_remove_row_btn cwccv_remove_row_btn">x</button>
				</td>
			</tr>
		<?php } ?>
		<!-- End price tier settings -->

		<!-- Geo Location settings -->
		<tr>
			<td>
				<?php
					$field_args['fields']['geo_location_priority_select']['selected_option_value'] = isset( $hide_for_geo_location['priority'] ) ? $hide_for_geo_location['priority'] : '5';
					CWCCV_Helper::get_priority_select( $field_args['fields']['geo_location_priority_select'] );
				?>
			</td>
			<td>
				<h4 style="display:inline;">
					<?php esc_html_e( 'Geo Location', 'codup-woocommerce-catalog-visibility' ); ?>
				</h4>
				<!-- $hide_for_geo_location['is_enable']; -->
				<?php $field_args['fields']['geo_location_settings_enable_toggle']['is_checked'] = $hide_for_geo_location['is_enable']; ?>
				<?php CWCCV_Helper::get_checkbox( $field_args['fields']['geo_location_settings_enable_toggle'] ); ?>
			</td>
			<td>
				<div class="cwccv_tooltip"><span class="dashicons dashicons-editor-help"></span><span class="cwccv_tooltiptext cwccv_tooltip_bottom"><?php esc_html_e( 'Turning on the toggle will let you create visibility rule by selecting countries and defining which products/categories to show/hide to customers from those countries.', 'codup-woocommerce-catalog-visibility' ); ?></span></div>
			</td>
		</tr>
		<!-- If geo location toggle's value is set to no then hide the user group settings -->

		<?php $is_hidden = ( ! isset( $hide_for_geo_location ) || ! $hide_for_geo_location['is_enable'] || 'no' == $hide_for_geo_location['is_enable'] ) ? 'hidden' : ''; ?>
		<tr class="cwccv_geo_location_titles" <?php echo esc_attr( $is_hidden ); ?> >
			<td></td>
			<td>
				<h4>
					<?php esc_html_e( 'Country', 'codup-woocommerce-catalog-visibility' ); ?><div class="cwccv_tooltip"><span class="dashicons dashicons-editor-help"></span><span class="cwccv_tooltiptext cwccv_tooltip_bottom"><?php esc_html_e( 'Select Countries', 'codup-woocommerce-catalog-visibility' ); ?></span></div>
				</h4>
			</td>
			<td>
				<h4>
					<?php esc_html_e( 'Action', 'codup-woocommerce-catalog-visibility' ); ?><div class="cwccv_tooltip"><span class="dashicons dashicons-editor-help"></span><span class="cwccv_tooltiptext cwccv_tooltip_bottom"><?php esc_html_e( 'Choose whether you want to hide or show the products or categories you’ll define.', 'codup-woocommerce-catalog-visibility' ); ?></span></div>
				</h4>
			</td>
			<td>
				<h4>
					<?php esc_html_e( 'By Category', 'codup-woocommerce-catalog-visibility' ); ?><div class="cwccv_tooltip"><span class="dashicons dashicons-editor-help"></span><span class="cwccv_tooltiptext cwccv_tooltip_bottom"><?php esc_html_e( 'Search Categories', 'codup-woocommerce-catalog-visibility' ); ?></span></div>
				</h4>
			</td>
			<td>
				<h4>
					<?php esc_html_e( 'By Product', 'codup-woocommerce-catalog-visibility' ); ?><div class="cwccv_tooltip"><span class="dashicons dashicons-editor-help"></span><span class="cwccv_tooltiptext cwccv_tooltip_bottom"><?php esc_html_e( 'Search Products', 'codup-woocommerce-catalog-visibility' ); ?></span></div>
				</h4>
			</td>
			<td>
				<?php CWCCV_Helper::get_repeater_fields_button( $field_args['fields']['geo_location_repeater_field_button'] ); ?>
			</td>
		</tr>
		<?php if ( isset( $hide_for_geo_location ) && ! empty( $hide_for_geo_location ) && isset( $hide_for_geo_location['rules'] ) ) { ?>
			<?php foreach ( $hide_for_geo_location['rules'] as $loop => $value ) { ?>
				<tr class="cwccv_geo_location_row" data-id="<?php echo esc_attr( $loop ); ?>" <?php echo esc_attr( $is_hidden ); ?> >
					<td></td>
					<td>
						<?php
							$field_args['fields']['geo_location_location_name_select']['selected_option_value'] = isset( $value['location'] ) ? $value['location'] : '';
							$customer_name = $field_args['fields']['geo_location_location_name_select']['name'] . '[' . $loop . '][]';
							CWCCV_Helper::get_select( $customer_name, $field_args['fields']['geo_location_location_name_select'] );
						?>
					</td>
					<td>
						<div class="cwccv_show_hide_radio_buttons" >
							<label for="">
								<?php esc_html_e( 'Show', 'codup-woocommerce-catalog-visibility' ); ?>
							</label>
							<?php
							if ( isset( $value['is_shown'] ) && 'yes' == $value['is_shown'] ) {
								$field_args['fields']['geo_location_product_show_radio']['is_checked'] = true;
							} else {
								$field_args['fields']['geo_location_product_show_radio']['is_checked'] = false;
							}
							$show_hide_button = $field_args['fields']['geo_location_product_show_radio']['name'] . '[' . $loop . ']';
								CWCCV_Helper::get_radio_button( $show_hide_button, $field_args['fields']['geo_location_product_show_radio'] );
							?>
						</div>
						<div class="cwccv_show_hide_radio_buttons" >
							<label for="">
								<?php esc_html_e( 'Hide', 'codup-woocommerce-catalog-visibility' ); ?>
							</label>
							<?php
							if ( isset( $value['is_shown'] ) && 'no' == $value['is_shown'] ) {
								$field_args['fields']['geo_location_product_hide_radio']['is_checked'] = true;
							} else {
								$field_args['fields']['geo_location_product_hide_radio']['is_checked'] = false;
							}
								$show_hide_button = $field_args['fields']['geo_location_product_hide_radio']['name'] . '[' . $loop . ']';
								CWCCV_Helper::get_radio_button( $show_hide_button, $field_args['fields']['geo_location_product_hide_radio'] );
							?>
						</div>
					</td>
					<td>
						<?php
							$field_args['fields']['geo_location_category_select']['selected_option_value'] = isset( $value['categories'] ) ? $value['categories'] : '';
							$category_name = $field_args['fields']['geo_location_category_select']['name'] . '[' . $loop . '][]';
							CWCCV_Helper::get_select( $category_name, $field_args['fields']['geo_location_category_select'] );
						?>
					</td>
					<td>
						<?php
							$field_args['fields']['geo_location_product_select']['selected_option_value'] = isset( $value['products'] ) ? $value['products'] : '';
							$product_name = $field_args['fields']['geo_location_product_select']['name'] . '[' . $loop . '][]';
							CWCCV_Helper::get_select( $product_name, $field_args['fields']['geo_location_product_select'] );
						?>
					</td>
					<td>
						<button type="button" class="cwccv_geo_location_remove_row_btn cwccv_remove_row_btn">x</button>
					</td>
				</tr>
			<?php } ?>
			<?php
		} else {
			$index = 0;
			?>
			<tr class="cwccv_geo_location_row" data-id="<?php echo esc_attr( $index ); ?>" <?php echo esc_attr( $is_hidden ); ?> >
				<td></td>
				<td>
					<?php
						$user_role_name = $field_args['fields']['geo_location_location_name_select']['name'] . '[' . $index . '][]';
						CWCCV_Helper::get_select( $user_role_name, $field_args['fields']['geo_location_location_name_select'] );
					?>
				</td>
				<td>
					<div class="cwccv_show_hide_radio_buttons" >
						<label for="">
							<?php esc_html_e( 'Show', 'codup-woocommerce-catalog-visibility' ); ?>
						</label>
						<?php
							$radio_button_name = $field_args['fields']['geo_location_product_show_radio']['name'] . '[' . $index . ']';
							CWCCV_Helper::get_radio_button( $radio_button_name, $field_args['fields']['geo_location_product_show_radio'] );
						?>
					</div>
					<div class="cwccv_show_hide_radio_buttons" >
						<label for="">
							<?php esc_html_e( 'Hide', 'codup-woocommerce-catalog-visibility' ); ?>
						</label>
						<?php
							$radio_button_name = $field_args['fields']['geo_location_product_show_radio']['name'] . '[' . $index . ']';
							CWCCV_Helper::get_radio_button( $radio_button_name, $field_args['fields']['geo_location_product_hide_radio'] );
						?>
					</div>
				</td>
				<td>
					<?php
						$category_name = $field_args['fields']['geo_location_category_select']['name'] . '[' . $index . '][]';
						CWCCV_Helper::get_select( $category_name, $field_args['fields']['geo_location_category_select'] );
					?>
				</td>
				<td>
					<?php
						$product_name = $field_args['fields']['geo_location_product_select']['name'] . '[' . $index . '][]';
						CWCCV_Helper::get_select( $product_name, $field_args['fields']['geo_location_product_select'] );
					?>
				</td>
				<td>
					<button type="button" class="cwccv_geo_location_remove_row_btn cwccv_remove_row_btn">x</button>
				</td>
			</tr>

		<?php } ?>
		<!-- End price tier settings -->

	</table>

	<hr>
	<table id="cwccv_visibility_settings_hide_catalog_section">
		<tr style="margin-bottom:20px;">
			<td style="">
				<h4>
					<?php esc_html_e( 'Hide catalog price for Non-Logged-in Users', 'codup-woocommerce-catalog-visibility' ); ?>
				</h4>
			</td>
			<td>
				<?php CWCCV_Helper::get_checkbox( $field_args['fields']['hide_price_for_non_login_toggle'] ); ?>
			</td>
			<td>

			</td>
		</tr>
		<?php $is_hidden = $field_args['fields']['hide_price_for_non_login_toggle']['is_checked'] ? ( 'no' == $field_args['fields']['hide_price_for_non_login_toggle']['is_checked'] ? 'hidden' : '' ) : 'hidden'; ?>
		<tr class="cwccv_hide_price_option" <?php echo esc_attr( $is_hidden ); ?> >
			<td>
				<h4 style="display:inline;">
					<?php esc_html_e( 'Hide whole catalog price', 'codup-woocommerce-catalog-visibility' ); ?>
				</h4>
				<?php CWCCV_Helper::get_checkbox( $field_args['fields']['hide_whole_catalog_price_toggle'] ); ?>
			</td>
			<td>
				<label for="">
					<?php esc_html_e( 'By Category', 'codup-woocommerce-catalog-visibility' ); ?><div class="cwccv_tooltip"><span class="dashicons dashicons-editor-help"></span><span class="cwccv_tooltiptext cwccv_tooltip_bottom"><?php esc_html_e( 'Search Categories', 'codup-woocommerce-catalog-visibility' ); ?></span></div>
				</label>
			</td>
			<td>
				<label for="">
					<?php esc_html_e( 'By Products', 'codup-woocommerce-catalog-visibility' ); ?><div class="cwccv_tooltip"><span class="dashicons dashicons-editor-help"></span><span class="cwccv_tooltiptext cwccv_tooltip_bottom"><?php esc_html_e( 'Search Products', 'codup-woocommerce-catalog-visibility' ); ?></span></div>
				</label>
			</td>
		</tr>
		<?php $is_hidden = $field_args['fields']['hide_price_for_non_login_toggle']['is_checked'] ? ( 'no' == $field_args['fields']['hide_price_for_non_login_toggle']['is_checked'] ? 'hidden' : '' ) : 'hidden'; ?>
		<tr class="cwccv_hide_price_option" <?php echo esc_attr( $is_hidden ); ?> >
			<td>

			</td>
			<td>
				<?php
					$category_name = $field_args['fields']['hide_catalog_category_select']['name'] . '[]';
					CWCCV_Helper::get_select( $category_name, $field_args['fields']['hide_catalog_category_select'] );
				?>
			</td>
			<td>
				<?php
					$product_name = $field_args['fields']['hide_catalog_product_select']['name'] . '[]';
					CWCCV_Helper::get_select( $product_name, $field_args['fields']['hide_catalog_product_select'] );
				?>
			</td>
		</tr>
	</table>
	
	<table id="cwccv_visibility_settings_hide_catalog_section">
		<tr style="margin-bottom:20px;">
			<td style="">
				<h4>
					<?php esc_html_e( 'Hide catalog product for Non-Logged-in Users', 'codup-woocommerce-catalog-visibility' ); ?>
				</h4>
			</td>
			<td>
				<?php CWCCV_Helper::get_checkbox( $field_args['fields']['hide_product_for_non_login_toggle'] ); ?>
			</td>
			<td>

			</td>
		</tr>
		<?php $is_hidden = $field_args['fields']['hide_product_for_non_login_toggle']['is_checked'] ? ( 'no' == $field_args['fields']['hide_product_for_non_login_toggle']['is_checked'] ? 'hidden' : '' ) : 'hidden'; ?>
		<tr class="cwccv_hide_product_option" <?php echo esc_attr( $is_hidden ); ?> >
			<td>
				<h4 style="display:inline;">
					<?php esc_html_e( 'Hide whole catalog product', 'codup-woocommerce-catalog-visibility' ); ?>
				</h4>
				<?php CWCCV_Helper::get_checkbox( $field_args['fields']['hide_whole_catalog_product_toggle'] ); ?>
			</td>
			<td>
				<label for="">
					<?php esc_html_e( 'By Category', 'codup-woocommerce-catalog-visibility' ); ?><div class="cwccv_tooltip"><span class="dashicons dashicons-editor-help"></span><span class="cwccv_tooltiptext cwccv_tooltip_bottom"><?php esc_html_e( 'Search Categories', 'codup-woocommerce-catalog-visibility' ); ?></span></div>
				</label>
			</td>
			<td>
				<label for="">
					<?php esc_html_e( 'By Products', 'codup-woocommerce-catalog-visibility' ); ?><div class="cwccv_tooltip"><span class="dashicons dashicons-editor-help"></span><span class="cwccv_tooltiptext cwccv_tooltip_bottom"><?php esc_html_e( 'Search Products', 'codup-woocommerce-catalog-visibility' ); ?></span></div>
				</label>
			</td>
		</tr>
		<?php $is_hidden = $field_args['fields']['hide_product_for_non_login_toggle']['is_checked'] ? ( 'no' == $field_args['fields']['hide_product_for_non_login_toggle']['is_checked'] ? 'hidden' : '' ) : 'hidden'; ?>
		<tr class="cwccv_hide_product_option" <?php echo esc_attr( $is_hidden ); ?> >
			<td>

			</td>
			<td>
				<?php
					$category_name = $field_args['fields']['hide_catalog_category_select_by_product']['name'] . '[]';
					CWCCV_Helper::get_select( $category_name, $field_args['fields']['hide_catalog_category_select_by_product'] );
				?>
			</td>
			<td>
				<?php
					$product_name = $field_args['fields']['hide_catalog_product_select_by_product']['name'] . '[]';
					CWCCV_Helper::get_select( $product_name, $field_args['fields']['hide_catalog_product_select_by_product'] );
				?>
			</td>
		</tr>
	</table>
	<br>
	<div class="cwccv_custom_error_message">
		
	</div>
</div>

<!-- Template For Settings Clonning -->
<div class="cwccv-template" style="display:none">
	<table>
		<tr class="cwccv_individual_user_row_template" data-id='0' >
			<td></td>
			<td>
				<select name="<?php echo wp_kses_post( $field_args['fields']['individual_customer_customer_name_select']['name'] ); ?>" data-id="0" class="<?php echo wp_kses_post( $field_args['fields']['individual_customer_customer_name_select']['name'] ); ?>" multiple >
					<?php
					$cwccv_users = CWCCV_Helper::get_users();
					foreach ( $cwccv_users as $cwccv_user ) {
						?>
							<option value="<?php echo wp_kses_post( $cwccv_user['id'] ); ?>"><?php echo wp_kses_post( $cwccv_user['name'] ); ?></option>
							<?php
					}
					?>
				</select>
			</td>
			<td>
				<div class="cwccv_show_hide_radio_buttons" >
					<label for="">
						<?php esc_html_e( 'Show', 'codup-woocommerce-catalog-visibility' ); ?>
					</label>
					<?php
						$radio_button = '<input type="radio" name="cwccv_individual_customer_products_show_hide_radio[0]" class="cwccv_individual_customer_products_show_hide_radio" value="no">';
						$allowed_html = array(
							'input' => array(
								'type'    => array(),
								'name'    => array(),
								'class'   => array(),
								'value'   => array(),
								'checked' => array(),
							),
						);
						echo wp_kses( $radio_button, $allowed_html );
						?>
				</div>
				<div class="cwccv_show_hide_radio_buttons" >
					<label for="">
						<?php esc_html_e( 'Hide', 'codup-woocommerce-catalog-visibility' ); ?>
					</label>
					<?php
						$radio_button = '<input type="radio" name="cwccv_individual_customer_products_show_hide_radio[0]" class="cwccv_individual_customer_products_show_hide_radio" value="no">';
						$allowed_html = array(
							'input' => array(
								'type'    => array(),
								'name'    => array(),
								'class'   => array(),
								'value'   => array(),
								'checked' => array(),
							),
						);
						echo wp_kses( $radio_button, $allowed_html );
						?>
				</div>
			</td>
			<td>
				<select name="<?php echo wp_kses_post( $field_args['fields']['individual_customer_category_select']['name'] ); ?>" data-id="0" class="<?php echo wp_kses_post( $field_args['fields']['individual_customer_category_select']['name'] ); ?>" multiple >
					<?php
					$cwccv_users = CWCCV_Helper::get_categories();
					foreach ( $cwccv_users as $cwccv_user ) {
						?>
							<option value="<?php echo wp_kses_post( $cwccv_user['id'] ); ?>"><?php echo wp_kses_post( $cwccv_user['name'] ); ?></option>
							<?php
					}
					?>
				</select>
			</td>
			<td>
				<select name="<?php echo wp_kses_post( $field_args['fields']['individual_customer_product_select']['name'] ); ?>" data-id="0" class="<?php echo wp_kses_post( $field_args['fields']['individual_customer_product_select']['name'] ); ?>" multiple >
					<?php
					$cwccv_users = CWCCV_Helper::get_products();
					foreach ( $cwccv_users as $cwccv_user ) {
						?>
							<option value="<?php echo wp_kses_post( $cwccv_user['id'] ); ?>"><?php echo wp_kses_post( $cwccv_user['name'] ); ?></option>
							<?php
					}
					?>
				</select>
			</td>
			<td>
				<button type="button" class="cwccv_ind_user_remove_row_btn cwccv_remove_row_btn">x</button>
			</td>
		</tr>
		<tr class="cwccv_user_roles_row_template" data-id="0" >
			<td></td>
			<td>
				<select name="<?php echo wp_kses_post( $field_args['fields']['user_roles_roles_name_select']['name'] ); ?>" data-id="0" class="<?php echo wp_kses_post( $field_args['fields']['user_roles_roles_name_select']['name'] ); ?>" multiple >
					<?php
					$cwccv_users = CWCCV_Helper::get_user_roles();
					foreach ( $cwccv_users as $cwccv_user ) {
						?>
							<option value="<?php echo wp_kses_post( $cwccv_user['id'] ); ?>"><?php echo wp_kses_post( $cwccv_user['name'] ); ?></option>
							<?php
					}
					?>
				</select>
			</td>
			<td>
				<div class="cwccv_show_hide_radio_buttons" >
					<label for="">
						<?php esc_html_e( 'Show', 'codup-woocommerce-catalog-visibility' ); ?>
					</label>
					<?php
						$radio_button = '<input type="radio" name="cwccv_user_roles_show_hide_radio[0]" class="cwccv_user_roles_show_hide_radio" value="no">';
						$allowed_html = array(
							'input' => array(
								'type'    => array(),
								'name'    => array(),
								'class'   => array(),
								'value'   => array(),
								'checked' => array(),
							),
						);
						echo wp_kses( $radio_button, $allowed_html );
						?>
				</div>
				<div class="cwccv_show_hide_radio_buttons" >
					<label for="">
						<?php esc_html_e( 'Hide', 'codup-woocommerce-catalog-visibility' ); ?>
					</label>
					<?php
						$radio_button = '<input type="radio" name="cwccv_user_roles_show_hide_radio[0]" class="cwccv_user_roles_show_hide_radio" value="no">';
						$allowed_html = array(
							'input' => array(
								'type'    => array(),
								'name'    => array(),
								'class'   => array(),
								'value'   => array(),
								'checked' => array(),
							),
						);
						echo wp_kses( $radio_button, $allowed_html );
						?>
				</div>
			</td>
			<td>
				<select name="<?php echo wp_kses_post( $field_args['fields']['user_roles_category_select']['name'] ); ?>" data-id="0" class="<?php echo wp_kses_post( $field_args['fields']['user_roles_category_select']['name'] ); ?>" multiple >
					<?php
					$cwccv_users = CWCCV_Helper::get_categories();
					foreach ( $cwccv_users as $cwccv_user ) {
						?>
							<option value="<?php echo wp_kses_post( $cwccv_user['id'] ); ?>"><?php echo wp_kses_post( $cwccv_user['name'] ); ?></option>
							<?php
					}
					?>
				</select>
			</td>
			<td>
				<select name="<?php echo wp_kses_post( $field_args['fields']['user_roles_product_select']['name'] ); ?>" data-id="0" class="<?php echo wp_kses_post( $field_args['fields']['user_roles_product_select']['name'] ); ?>" multiple >
					<?php
					$cwccv_users = CWCCV_Helper::get_products();
					foreach ( $cwccv_users as $cwccv_user ) {
						?>
							<option value="<?php echo wp_kses_post( $cwccv_user['id'] ); ?>"><?php echo wp_kses_post( $cwccv_user['name'] ); ?></option>
							<?php
					}
					?>
				</select>
			</td>
			<td>
				<button type="button" class="cwccv_user_roles_remove_row_btn cwccv_remove_row_btn">x</button>
			</td>
		</tr>
		<tr class="cwccv_user_groups_row_template" data-id="0" >
			<td></td>
			<td>
				<select name="<?php echo wp_kses_post( $field_args['fields']['user_groups_groups_name_select']['name'] ); ?>" data-id="0" class="<?php echo wp_kses_post( $field_args['fields']['user_groups_groups_name_select']['name'] ); ?>" multiple >
					<?php
					$cwccv_users = CWCCV_Helper::get_user_groups();
					foreach ( $cwccv_users as $cwccv_user ) {
						?>
							<option value="<?php echo wp_kses_post( $cwccv_user['id'] ); ?>"><?php echo wp_kses_post( $cwccv_user['name'] ); ?></option>
							<?php
					}
					?>
				</select>
			</td>
			<td>
				<div class="cwccv_show_hide_radio_buttons" >
					<label for="">
						<?php esc_html_e( 'Show', 'codup-woocommerce-catalog-visibility' ); ?>
					</label>
					<?php
						$radio_button = '<input type="radio" name="cwccv_user_groups_show_hide_radio[0]" class="cwccv_user_groups_show_hide_radio" value="no">';
						$allowed_html = array(
							'input' => array(
								'type'    => array(),
								'name'    => array(),
								'class'   => array(),
								'value'   => array(),
								'checked' => array(),
							),
						);
						echo wp_kses( $radio_button, $allowed_html );
						?>
				</div>
				<div class="cwccv_show_hide_radio_buttons" >
					<label for="">
						<?php esc_html_e( 'Hide', 'codup-woocommerce-catalog-visibility' ); ?>
					</label>
					<?php
						$radio_button = '<input type="radio" name="cwccv_user_groups_show_hide_radio[0]" class="cwccv_user_groups_show_hide_radio" value="no">';
						$allowed_html = array(
							'input' => array(
								'type'    => array(),
								'name'    => array(),
								'class'   => array(),
								'value'   => array(),
								'checked' => array(),
							),
						);
						echo wp_kses( $radio_button, $allowed_html );
						?>
				</div>
			</td>
			<td>
				<select name="<?php echo wp_kses_post( $field_args['fields']['user_groups_category_select']['name'] ); ?>" data-id="0" class="<?php echo wp_kses_post( $field_args['fields']['user_groups_category_select']['name'] ); ?>" multiple >
					<?php
					$cwccv_users = CWCCV_Helper::get_categories();
					foreach ( $cwccv_users as $cwccv_user ) {
						?>
							<option value="<?php echo wp_kses_post( $cwccv_user['id'] ); ?>"><?php echo wp_kses_post( $cwccv_user['name'] ); ?></option>
							<?php
					}
					?>
				</select>
			</td>
			<td>
				<select name="<?php echo wp_kses_post( $field_args['fields']['user_groups_product_select']['name'] ); ?>" data-id="0" class="<?php echo wp_kses_post( $field_args['fields']['user_groups_product_select']['name'] ); ?>" multiple >
					<?php
					$cwccv_users = CWCCV_Helper::get_products();
					foreach ( $cwccv_users as $cwccv_user ) {
						?>
							<option value="<?php echo wp_kses_post( $cwccv_user['id'] ); ?>"><?php echo wp_kses_post( $cwccv_user['name'] ); ?></option>
							<?php
					}
					?>
				</select>
			</td>
			<td>
				<button type="button" class="cwccv_user_groups_remove_row_btn cwccv_remove_row_btn">x</button>
			</td>
		</tr>
		<tr class="cwccv_price_tier_row_template" data-id="0" >
			<td></td>
			<td>
				<?php
					// $price_from_name  = $field_args['fields']['price_tier_from_text_field']['name'] . '[]';
					$price_from_class = $field_args['fields']['price_tier_from_text_field']['class'];
				?>
				<input type="number" class="<?php echo esc_attr( $price_from_class ); ?>" >
				<?php
					// $price_to_name  = $field_args['fields']['price_tier_to_text_field']['name'] . '[]';
					$price_to_class = $field_args['fields']['price_tier_to_text_field']['class'];
				?>
				<input type="number" class="<?php echo esc_attr( $price_to_class ); ?>" >
			</td>
			<td>
				<div class="cwccv_show_hide_radio_buttons" >
					<label for="">
						<?php esc_html_e( 'Show', 'codup-woocommerce-catalog-visibility' ); ?>
					</label>
					<?php
						$radio_button = '<input type="radio" name="cwccv_price_tier_show_hide_radio[0]" class="cwccv_price_tier_show_hide_radio" value="no">';
						$allowed_html = array(
							'input' => array(
								'type'    => array(),
								'name'    => array(),
								'class'   => array(),
								'value'   => array(),
								'checked' => array(),
							),
						);
						echo wp_kses( $radio_button, $allowed_html );
						?>
				</div>
				<div class="cwccv_show_hide_radio_buttons" >
					<label for="">
						<?php esc_html_e( 'Hide', 'codup-woocommerce-catalog-visibility' ); ?>
					</label>
					<?php
						$radio_button = '<input type="radio" name="cwccv_price_tier_show_hide_radio[0]" class="cwccv_price_tier_show_hide_radio" value="no">';
						$allowed_html = array(
							'input' => array(
								'type'    => array(),
								'name'    => array(),
								'class'   => array(),
								'value'   => array(),
								'checked' => array(),
							),
						);
						echo wp_kses( $radio_button, $allowed_html );
						?>
				</div>
			</td>
			<td>
				<select name="<?php echo wp_kses_post( $field_args['fields']['price_tier_category_select']['name'] ); ?>" data-id="0" class="<?php echo wp_kses_post( $field_args['fields']['price_tier_category_select']['name'] ); ?>" multiple >
					<?php
					$cwccv_users = CWCCV_Helper::get_categories();
					foreach ( $cwccv_users as $cwccv_user ) {
						?>
							<option value="<?php echo wp_kses_post( $cwccv_user['id'] ); ?>"><?php echo wp_kses_post( $cwccv_user['name'] ); ?></option>
							<?php
					}
					?>
				</select>
			</td>
			<td>
				<select name="<?php echo wp_kses_post( $field_args['fields']['price_tier_product_select']['name'] ); ?>" data-id="0" class="<?php echo wp_kses_post( $field_args['fields']['price_tier_product_select']['name'] ); ?>" multiple >
					<?php
					$cwccv_users = CWCCV_Helper::get_products();
					foreach ( $cwccv_users as $cwccv_user ) {
						?>
							<option value="<?php echo wp_kses_post( $cwccv_user['id'] ); ?>"><?php echo wp_kses_post( $cwccv_user['name'] ); ?></option>
							<?php
					}
					?>
				</select>
			</td>
			<td>
				<button type="button" class="cwccv_price_tier_remove_row_btn cwccv_remove_row_btn">x</button>
			</td>
		</tr>
		<tr class="cwccv_geo_location_row_template" data-id="0" >
			<td></td>
			<td>
				<select name="<?php echo wp_kses_post( $field_args['fields']['geo_location_location_name_select']['name'] ); ?>" data-id="0" class="<?php echo wp_kses_post( $field_args['fields']['geo_location_location_name_select']['name'] ); ?>" multiple >
					<?php
					$cwccv_users = CWCCV_Helper::get_country_list();
					foreach ( $cwccv_users as $cwccv_user ) {
						?>
						<option value="<?php echo wp_kses_post( $cwccv_user['id'] ); ?>"><?php echo wp_kses_post( $cwccv_user['name'] ); ?></option>
						<?php
					}
					?>
				</select>
			</td>
			<td>
				<div class="cwccv_show_hide_radio_buttons" >
					<label for="">
						<?php esc_html_e( 'Show', 'codup-woocommerce-catalog-visibility' ); ?>
					</label>
					<?php
						$radio_button = '<input type="radio" name="cwccv_geo_location_show_hide_radio[0]" class="cwccv_geo_location_show_hide_radio" value="yes">';
						$allowed_html = array(
							'input' => array(
								'type'    => array(),
								'name'    => array(),
								'class'   => array(),
								'value'   => array(),
								'checked' => array(),
							),
						);
						echo wp_kses( $radio_button, $allowed_html );
						?>
				</div>
				<div class="cwccv_show_hide_radio_buttons" >
					<label for="">
						<?php esc_html_e( 'Hide', 'codup-woocommerce-catalog-visibility' ); ?>
					</label>
					<?php
						$radio_button = '<input type="radio" name="cwccv_geo_location_show_hide_radio[0]" class="cwccv_geo_location_show_hide_radio" value="no">';
						$allowed_html = array(
							'input' => array(
								'type'    => array(),
								'name'    => array(),
								'class'   => array(),
								'value'   => array(),
								'checked' => array(),
							),
						);
						echo wp_kses( $radio_button, $allowed_html );
						?>
				</div>
			</td>
			<td>
				<select name="<?php echo wp_kses_post( $field_args['fields']['geo_location_category_select']['name'] ); ?>" data-id="0" class="<?php echo wp_kses_post( $field_args['fields']['geo_location_category_select']['name'] ); ?>" multiple >
					<?php
					$cwccv_users = CWCCV_Helper::get_categories();
					foreach ( $cwccv_users as $cwccv_user ) {
						?>
							<option value="<?php echo wp_kses_post( $cwccv_user['id'] ); ?>"><?php echo wp_kses_post( $cwccv_user['name'] ); ?></option>
							<?php
					}
					?>
				</select>
			</td>
			<td>
				<select name="<?php echo wp_kses_post( $field_args['fields']['geo_location_product_select']['name'] ); ?>" data-id="0" class="<?php echo wp_kses_post( $field_args['fields']['geo_location_product_select']['name'] ); ?>" multiple >
					<?php
					$cwccv_users = CWCCV_Helper::get_products();
					foreach ( $cwccv_users as $cwccv_user ) {
						?>
							<option value="<?php echo wp_kses_post( $cwccv_user['id'] ); ?>"><?php echo wp_kses_post( $cwccv_user['name'] ); ?></option>
							<?php
					}
					?>
				</select>
			</td>
			<td>
				<button type="button" class="cwccv_geo_location_remove_row_btn cwccv_remove_row_btn">x</button>
			</td>
		</tr>
	</table>
</div>
