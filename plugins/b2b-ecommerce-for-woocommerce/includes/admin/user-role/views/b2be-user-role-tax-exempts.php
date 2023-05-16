<?php
/**
 * Tax exempts Fields
 *
 * @package b2be-user-role-tax-exempts.php
 */

// prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div id="b2be-custom-role-options" class="panel woocommerce_options_panel b2be-custom-role-options" style="display: block;">
	<div class="options_group">
		<div class="form-field tax-exempt-field">   
			<div class="title">
				<?php echo esc_html__( 'Standard rate', 'b2b-ecommerce' ); ?>
			</div>
			<div class="desc-options">
				<input type="checkbox" class="checkbox" name="tax_exempt[<?php echo 'standard'; ?>]" id="<?php echo 'standard'; ?>" <?php echo ( isset( get_post_meta( $post_id, 'tax_exempt', true )['standard'] ) && 'on' == get_post_meta( $post_id, 'tax_exempt', true )['standard'] ) ? 'checked' : ''; ?> >
				<span class="description"><?php echo esc_html__( 'Exempt Standard rate', 'b2b-ecommerce' ); ?></span>
			</div>
		</div>
		<?php if ( $tax_classes ) { ?>
			<?php foreach ( $tax_classes as $key => $value ) { ?>
				<div class="form-field tax-exempt-field">
					<div class="title">
						<?php echo wp_kses_post( $value ); ?>
					</div>
					<div class="desc-options">
						<?php $tax_class_name = wp_kses_post( strtolower( str_replace( ' ', '-', $value ) ) ); ?>
						<input type="checkbox" class="checkbox" name="tax_exempt[<?php echo wp_kses_post( $tax_class_name ); ?>]" id="<?php echo wp_kses_post( $tax_class_name ); ?>" <?php echo ( isset( get_post_meta( $post_id, 'tax_exempt', true )[ $tax_class_name ] ) && 'on' == get_post_meta( $post_id, 'tax_exempt', true )[ $tax_class_name ] ) ? 'checked' : ''; ?> >
						<span class="description"><?php echo esc_html__( 'Exempt ', 'b2b-ecommerce' ) . wp_kses_post( $value ); ?></span>
					</div>
				</div>
			<?php } ?>
		<?php } ?>
		<?php if ( true == $avatax_enable ) { ?>
			<div class="form-field tax-exempt-field">   
				<div class="title">
					<?php echo esc_html__( 'Avalara Tax', 'b2b-ecommerce' ); ?>
				</div>
				<div class="desc-options">
					<input type="checkbox" class="checkbox" name="avalara_tax_exempt" 
					<?php
					if ( 'on' == get_post_meta( $post_id, 'avalara_tax_exempt', true ) ) {
						echo 'checked'; }
					?>
					 >
					<span class="description"><?php echo esc_html__( 'Exempt Tax Calculated by Avalara Plugin', 'b2b-ecommerce' ); ?></span>
				</div>
			</div>

		<?php } ?>
	</div>
</div>
