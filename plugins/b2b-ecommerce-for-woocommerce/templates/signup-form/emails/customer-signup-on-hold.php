<?php
/**
 * On Sign Up On Hold Email.
 *
 * Shows quotes on the account page.
 *
 * This template can be overridden by copying it to yourtheme/customer-signup-on-hold.php
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
<?php
$firstname = $user->first_name;
$lastname  = $user->last_name;

echo esc_html__( 'Dear ', 'b2b-ecommerce' ) . wp_kses_post( $firstname ) . ' ' . wp_kses_post( $lastname );

?>
<p><?php esc_html_e( 'Your account has been marked On Hold by Site Admin', 'b2b-ecommerce' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
<?php
/*
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );
