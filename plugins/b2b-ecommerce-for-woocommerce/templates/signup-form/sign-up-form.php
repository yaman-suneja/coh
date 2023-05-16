<?php
/**
 * Sign Up Form
 *
 * This template can be overridden by copying it to yourtheme/b2b-ecommerce-for-woocommerce/signup-form/sign-up-form.php
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

// prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<?php
/*
@name: b2be_before_signup_form
@desc: Runs before sign up form is rendered.
@param: (object) $sign_up_fields Sign up form fields.
@package: b2b-ecommerce-for-woocommerce
@module: sign up form
@type: action
*/
?>
<?php do_action( 'b2be_before_signup_form', $sign_up_fields ); ?>
<?php
if ( function_exists( 'wc_print_notices' ) ) {
	wc_print_notices();
}
wp_enqueue_style( 'b2be-sigup-form-select2' );
wp_enqueue_script( 'b2be-sigup-form-select2' );
wp_enqueue_script( 'b2be-sigup-form' );

?>
<form method="POST">
	<div class="cwl-tier-row" >
	<?php
	foreach ( $sign_up_fields as $key => $sign_up_field ) {
		if ( isset( $sign_up_field['visibility'] ) ) :
			$classes = isset( $sign_up_field['classes'] ) && ! empty( $sign_up_field['classes'] ) ? implode( "\n", $sign_up_field['classes'] ) : '';
			?>
			<p class="forminp input-column <?php echo wp_kses_post( $classes ); ?>" data-field-number="<?php echo esc_attr( $key ); ?>" style="<?php echo wp_kses_post( b2be_get_field_size( $sign_up_field['size'] ) ); ?>;padding:0 10px;" >
				<label>
					<?php
						/*
						@name: b2be_signup_form_{$field_id}
						@desc: Modify the field label of sign up form fields.
						@param: (array) $label Label of current field.
						@package: b2b-ecommerce-for-woocommerce
						@module: sign up form
						@type: filter
						*/
					?>
					<?php echo wp_kses_post( apply_filters( 'b2be_signup_form_' . $sign_up_field['type'], esc_attr( $sign_up_field['name'] ) ) ); ?>
					<?php if ( 'true' == $sign_up_field['required'] ) : ?>
						<span class="required" style="color:red">*</span>
					<?php endif; ?>
				</label><br>
				<?php
				if ( in_array( $sign_up_field['type'], $signup_form_dropdowns ) ) {
					wc_get_template(
						'signup-form/sign-up-form-dropdowns.php',
						array(
							'sign_up_field'         => $sign_up_field,
							'signup_form_dropdowns' => $signup_form_dropdowns,
						),
						'b2b-ecommerce-for-woocommerce',
						CWRFQ_PLUGIN_DIR . '/templates/'
					);
				} else {
					wc_get_template(
						'signup-form/sign-up-form-fields.php',
						array(
							'sign_up_field'    => $sign_up_field,
							'wc_signup_fields' => $wc_signup_fields,
						),
						'b2b-ecommerce-for-woocommerce',
						CWRFQ_PLUGIN_DIR . '/templates/'
					);
				}
				?>
			</p>
			<?php
		endif;
	}

	/*
	@name: b2be_signup_form_button_text
	@desc: Modify the sign up button text for sign up form.
	@param: (array) $button_text Text for sign up button.
	@package: b2b-ecommerce-for-woocommerce
	@module: sign up form
	@type: filter
	*/
	?>
	<p class="forminp input-column" style="padding:0px 10px;clear:both;">
		<input type="submit" class="sign_up_button" id="sign_up_button" name="sign_up_button" value="<?php echo wp_kses_post( apply_filters( 'b2be_signup_form_button_text', 'Sign Up' ) ); ?>">
	</p>
	</div>
</form>
<?php
/*
@name: b2be_after_signup_form
@desc: Runs after sign up form is rendered.
@param: (object) $sign_up_fields Sign up form fields.
@package: b2b-ecommerce-for-woocommerce
@module: sign up form
@type: action
*/
?>
<?php do_action( 'b2be_after_signup_form', $sign_up_fields ); ?>
