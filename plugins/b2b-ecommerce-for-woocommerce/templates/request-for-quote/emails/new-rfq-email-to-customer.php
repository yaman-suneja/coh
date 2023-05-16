<?php
/**
 * RFQ Email To Customer
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
<p><?php printf( esc_html__( 'Your following quote has been sent to the Admin ', 'codup-wcrfq' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
<?php

do_action( 'cwrfq_view_quote', $quote->get_id() );

/*
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );
