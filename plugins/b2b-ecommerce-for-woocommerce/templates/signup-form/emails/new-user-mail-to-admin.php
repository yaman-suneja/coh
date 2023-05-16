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
do_action( 'woocommerce_email_header', $email_heading, $email );

$email = trim( $user->user_email );

echo esc_html__( 'New User Registered', 'b2b-ecommerce' );
?>
<br>
<?php
echo esc_html__( 'User Email: ', 'b2b-ecommerce' ) . wp_kses_post( $email );

/*
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );
