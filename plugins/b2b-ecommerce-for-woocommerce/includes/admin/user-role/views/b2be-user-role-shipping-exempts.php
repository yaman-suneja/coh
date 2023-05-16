<?php
/**
 * Shipping exempts Fields
 *
 * @package b2be-user-role-shipping-exempts.php
 */

// prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div id="b2be-custom-role-options" class="panel woocommerce_options_panel b2be-custom-role-options" style="display: block;">
	<div class="options_group" >
		<?php
		foreach ( $shipping_methods as $shipping_method_id => $shipping_method ) {
			$shipping_method_name = $shipping_method->method_title;
			?>
				<div class="form-field shipping-exempt-field">
					<div class="title">
					<?php echo wp_kses_post( $shipping_method_name ); ?>
					</div>
					<div class="desc-options">
						<input type="checkbox" class="checkbox" name="shipping_exempt[<?php echo wp_kses_post( $shipping_method_id ); ?>]" id="<?php echo wp_kses_post( $shipping_method_id ); ?>" <?php echo isset( get_post_meta( $post_id, 'shipping_exempt', true )[ $shipping_method_id ] ) && ( 'yes' == get_post_meta( $post_id, 'shipping_exempt', true )[ $shipping_method_id ] || 'on' == get_post_meta( $post_id, 'shipping_exempt', true )[ $shipping_method_id ] ) ? 'checked' : ''; ?> >
						<span class="description"><?php echo esc_html__( 'Exempt ', 'b2b-ecommerce' ) . wp_kses_post( $shipping_method_name ) . esc_html__( ' Method', 'b2b-ecommerce' ); ?></span>
					</div>
				</div>
				<?php
		}
		?>
	</div>
</div>
