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

<?php if ( 'role' == $sign_up_field['type'] ) : ?>
	<?php $options = b2be_get_field_option( 'role', $sign_up_field['roles'] ); ?>
	<select name="signup_form_field[role]" id="signup_form_role" style="width:100%;" <?php echo ( 'true' == $sign_up_field['required'] ) ? 'required' : ''; ?>>
		<option value="">Select Role</option>
		<?php foreach ( $options as $key => $option ) : ?>
			<option value="<?php echo wp_kses_post( $option ); ?>"><?php echo wp_kses_post( wp_roles()->role_names[ $option ] ); ?></option>
		<?php endforeach; ?>
	</select>
<?php endif; ?>

<?php if ( 'state' == $sign_up_field['type'] ) : ?>
	<?php $options = b2be_get_field_option( 'state' ); ?>
	<select name="signup_form_field[state]" id="signup_form_state" style="width:100%;" <?php echo ( 'true' == $sign_up_field['required'] ) ? 'required' : ''; ?>>
		<option value="">Select State</option>
		<?php foreach ( $options as $key => $option ) : ?>
			<?php foreach ( $option as $code => $value ) : ?>
				<option value="<?php echo wp_kses_post( $code ); ?>"><?php echo wp_kses_post( $value ); ?></option>
			<?php endforeach; ?>
		<?php endforeach; ?>
	</select>
<?php endif; ?>

<?php if ( 'country' == $sign_up_field['type'] ) : ?>
	<?php $options = b2be_get_field_option( 'country' ); ?>
	<select name="signup_form_field[country]" id="signup_form_country" style="width:100%;" <?php echo ( 'true' == $sign_up_field['required'] ) ? 'required' : ''; ?>>
		<option value="">Select Country</option>
		<?php foreach ( $options as $key => $option ) : ?>
			<option value="<?php echo wp_kses_post( $key ); ?>"><?php echo wp_kses_post( $option ); ?></option>
		<?php endforeach; ?>
	</select>
<?php endif; ?>
