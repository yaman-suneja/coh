<?php
/**
 * Admin new comment
 *
 * @hooked WC_Emails::order_details() Shows the order details table.
 * @hooked WC_Structured_Data::generate_order_data() Generates structured data.
 * @hooked WC_Structured_Data::output_structured_data() Outputs structured data.
 * @since 2.5.0
 * @package woocomerce/templates
 */

defined( 'ABSPATH' ) || exit;

/*
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<?php /* translators: %s: Customer billing full name */ ?>
<p><?php printf( esc_html__( 'You’ve received the following comment from customer %s:', 'b2b-ecommerce' ), wp_kses_post( $quote->get_formatted_full_name() ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
<?php

if ( ! empty( $_POST['admin-comment-meta-action'] ) ) {
	wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['admin-comment-meta-action'] ) ) );
}

if ( isset( $_POST['rfq_customer_comment_textarea'] ) ) {
	$comment_textarea = sanitize_text_field( wp_unslash( $_POST['rfq_customer_comment_textarea'] ) );
}
echo '<strong>' . esc_html( $comment_textarea ) . '</strong>';

/*
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );
