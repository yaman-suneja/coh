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

<?php $rfq_admin_name = ! empty( get_userdata( 1 )->display_name ) ? get_userdata( 1 )->display_name : bloginfo( 'admin_email' ); ?> 
<?php /* translators: %s: Customer billing full name */ ?>
<p><?php printf( esc_html__( 'You’ve received the following comment from site owner %s:', 'b2b-ecommerce' ), esc_html( $rfq_admin_name ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
<?php
// if(wp_verify_nonce()){}.

if ( ! empty( $_POST['admin-comment-meta-action'] ) ) {
	wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['admin-comment-meta-action'] ) ) );
}

if ( isset( $_POST['rfq_admin_comment_textarea'] ) ) {
	$comment_textarea = sanitize_text_field( wp_unslash( $_POST['rfq_admin_comment_textarea'] ) );
}
echo '<strong>' . esc_html( $comment_textarea ) . '</strong>';

/*
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );
