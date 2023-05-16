<?php
/**
 * On Quote Submitted
 *
 * Shows quotes on the account page.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/quotes.php.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Executes the e-mail header.
 *
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<?php /* translators: %s: Customer first name */ ?>
<p><?php printf( esc_html__( 'Hi %s,', 'b2b-ecommerce' ), esc_html( $quote->get_requester_first_name() ) ); ?></p>
<p>
<?php

/* translators: %s Quote date */
printf( esc_html__( 'Here are the quotes you\'ve requested on %s:', 'b2b-ecommerce' ), esc_html( wc_format_datetime( $quote->get_date_created() ) ) );
?>
</p>
	
<?php
 do_action( 'cwrfq_view_quote', $quote->get_id() );
/**
 * Executes the email footer.
 *
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );
