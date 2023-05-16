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

<?php if ( 'phone' == $sign_up_field['type'] ) : ?>
	<input type="tel" name="signup_form_field[<?php echo wp_kses_post( $sign_up_field['type'] ); ?>]" style="width:100%;" <?php echo ( 'true' == $sign_up_field['required'] ) ? 'required' : ''; ?> >
<?php elseif ( in_array( $sign_up_field['type'], $wc_signup_fields ) ) : ?>
	<input type="text" name="signup_form_field[<?php echo wp_kses_post( $sign_up_field['type'] ); ?>]" style="width:100%;" <?php echo ( 'true' == $sign_up_field['required'] ) ? 'required' : ''; ?> >
<?php elseif ( ! in_array( $sign_up_field['type'], $wc_signup_fields ) && 'email' != $sign_up_field['type'] ) : ?>
	<input type="<?php echo wp_kses_post( $sign_up_field['type'] ); ?>" name="signup_form_field[<?php echo wp_kses_post( str_replace( ' ', '_', $sign_up_field['name'] ) . '_' . $sign_up_field['type'] ); ?>]" style="width:100%;" <?php echo ( 'true' == $sign_up_field['required'] ) ? 'required' : ''; ?> >
<?php elseif ( 'email' == $sign_up_field['type'] ) : ?>
	<input type="<?php echo wp_kses_post( $sign_up_field['type'] ); ?>" name="signup_form_field[<?php echo wp_kses_post( $sign_up_field['type'] ); ?>]" style="width:100%;" <?php echo ( 'true' == $sign_up_field['required'] ) ? 'required' : ''; ?> >
<?php endif; ?>
