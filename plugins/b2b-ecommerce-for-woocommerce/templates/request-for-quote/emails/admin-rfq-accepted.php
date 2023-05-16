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
<p><?php printf( esc_html__( 'Quote #%1$s belonging to %2$s has been accepted.', 'b2b-ecommerce' ), wp_kses_post( $quote->get_id() ), wp_kses_post( $quote->get_formatted_full_name() ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
<?php
/*
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );
