<?php
/**
 * Discount Table
 *
 * This template can be overridden by copying it to yourtheme/b2b-ecommerce-for-woocommerce/bulk-discount/discount-table.php
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @package B2b Ecommerce For Woocommerce/Templates
 * @version 1.3.9.6
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="b2be_discount_rule_div">
	<?php
		/*
		@name: b2be_varaition_table_title
		@desc: Modify theh variation table title name.
		@param: (string) $table_name Variation Table Name.
		@package: b2b-ecommerce-for-woocommerce
		@module: discount options
		@type: filter
		*/
	?>
	<div class="b2be_discount_rule_div_label"><b><?php echo wp_kses_post( apply_filters( 'b2be_varaition_table_title', esc_html__( 'Quantity Based Discounts', 'b2b-ecommerce' ) ) ); ?></b></div>
	<span style="display:none;" id="b2be_price"><?php echo wp_kses_post( $regular_price ); ?></span>
	<table id="b2be_discount_rule_table" >
		<thead>
			<tr>
				<?php if ( 'variable' == $product->get_type() ) { ?>
					<th><?php echo esc_html( $variation_table_columns['variations'] ); ?></th>
				<?php } ?>
				<th><?php echo esc_html( $variation_table_columns['min'] ); ?></th>
				<th><?php echo esc_html( $variation_table_columns['max'] ); ?></th>
				<th><?php echo esc_html( $variation_table_columns['discount'] ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
			$count_of_same_rule = 0;
			$temp_array         = array();

			// Loop to find count for rowspan.
			foreach ( $discounts as $key => $value ) {
				if ( isset( $temp_array[ $value['ruleId'] ] ) ) {
					$temp_array[ $value['ruleId'] ]++;
				} else {
					$temp_array[ $value['ruleId'] ] = 1;
				}
			}
			$rule_ids = array();
			foreach ( $discounts as $key => $value ) {

				$all_var = array();

				foreach ( $value['variation_ids'] as $key => $var_id ) {
					$variation = wc_get_product( $var_id );

					if ( $variation->get_parent_id() == $product_id ) {
						$all_var[] = $var_id;
					}
				}

				if ( isset( $value['minQuantity'] ) && isset( $value['maxQuantity'] ) ) {

					if ( ! in_array( $value['ruleId'], $rule_ids ) ) {

						array_push( $rule_ids, $value['ruleId'] );
						$allowed      = false;
						$same_var_ids = array();

					}

					if ( 0 != count( array_column( $discounts, 'variation_ids', 'ruleId' ) ) ) {
						$same_var_ids = array_column( $discounts, 'variation_ids', 'ruleId' );
					}

					if ( 'variable' == $product->get_type() ) {
						?>
						<tr>
							<td>
								<span class="b2be_minQuantity">
									<?php
									echo wp_kses_post( b2be_variations_name( $all_var ) );
									?>
								</span>
							</td>
						<?php
					} else {
						?>
						<tr>
						<?php
					}
					?>

					<td>
						<span class="b2be_minQuantity">
						<?php echo wp_kses_post( $value['minQuantity'] ); ?>
						</span>
					</td>
					<td>
						<span class="b2be_maxQuantity">
							<?php echo wp_kses_post( $value['maxQuantity'] ); ?>
						</span>
					</td>
					<td>
						<span class="b2be_discount">
						<?php
							$price    = 0;
							$discount = isset( $value['discount'] ) ? floatval( $value['discount'] ) : 0;


						if ( 'variable' == $product->get_type() ) {

							foreach ( $all_var as $key => $var_id ) {
								$product_discount = 0;
								$_product         = wc_get_product( $var_id );
								$regular_price    = intval( $_product->get_regular_price() );

								if ( 'per-piece' == $discount_format && 'fixed' == $value['type'] ) {
									$product_discount = $regular_price - $discount;
								} elseif ( 'per-piece' == $discount_format && 'percentage' == $value['type'] ) {
									 $product_discount = ( ( $regular_price / 100 ) * ( 100 - $discount ) );
								} elseif ( 'default' == $discount_format && 'percentage' == $value['type'] ) {
										$product_discount = $discount;
								} elseif ( 'default' == $discount_format && 'fixed' == $value['type'] ) {
										$product_discount = $discount;
								}

								if ( 0 > $product_discount ) {
									$product_discount = 0;
								}


								if ( 'per-piece' == $discount_format ) {
									$product_discount = wp_kses_post( wc_price( $product_discount ) );
								} else {
									if ( 'fixed' == $value['type'] ) {
										$product_discount = wp_kses_post( wc_price( $product_discount ) );
									} else {
										$product_discount = wp_kses_post( $product_discount ) . '%';
									}
								}

								echo wp_kses_post( $product_discount . '<br>' );


							}
						} else {

							if ( 'per-piece' == $discount_format && 'fixed' == $value['type'] ) {
								$discount = ( intval( $product->get_regular_price() ) - $discount );
							} elseif ( 'per-piece' == $discount_format && 'percentage' == $value['type'] ) {
								$discount = ( $product->get_regular_price() - ( $product->get_regular_price() * ( $discount * 0.01 ) ) );
							}
							if ( 0 > $discount ) {
								$discount = 0;
							}

							if ( 'per-piece' == $discount_format ) {
								$discount = wp_kses_post( wc_price( $discount ) );
							} else {
								if ( 'fixed' == $value['type'] ) {
									$discount = wp_kses_post( wc_price( $discount ) );
								} else {
									$discount = wp_kses_post( $discount ) . '%';
								}
							}

							echo wp_kses_post( $discount );

						}
						?>
						</span>
					</td>
				</tr>
					<?php
				}
			}
			?>
		</tbody>
	</table>
</div>
