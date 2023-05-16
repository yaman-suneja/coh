<?php
/**
 * On Customer New Comment
 *
 * Shows quotes on the account page.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/quotes.php.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 */

defined( 'ABSPATH' ) || exit;

/*
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<?php /* translators: %s: Customer billing full name */ ?>
<p><?php esc_html_e( 'Your Request For Signup has been Accepted by Site Admin', 'b2b-ecommerce' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>

<?php

	$firstname = $user->first_name;
	$email     = trim( $user->user_email );

	$adt_rp_key        = get_password_reset_key( $user );
	$wcb2be_user_login = $user->user_login;
	$rp_link           = '<a href="' . network_site_url( "wp-login.php?action=rp&key=$adt_rp_key&login=" . rawurlencode( $wcb2be_user_login ), 'login' ) . '">' . network_site_url( "wp-login.php?action=rp&key=$adt_rp_key&login=" . rawurlencode( $wcb2be_user_login ), 'login' ) . '</a>';

if ( '' == $firstname ) {
	$firstname = 'There';
}

	$message  = esc_html__( 'Hi ', 'b2b-ecommerce' ) . $firstname . ',<br>';
	$message .= esc_html__( 'An account has been created on ', 'b2b-ecommerce' ) . get_bloginfo( 'name' ) . esc_html__( ' for email address ', 'b2b-ecommerce' ) . $email . '<br>';
	$message .= esc_html__( 'Click here to set the password for your account: ', 'b2b-ecommerce' ) . '<br>';
	$message .= $rp_link . '<br>';

	echo wp_kses_post( $message );

?>

<?php
/*
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );
