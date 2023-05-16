<?php
/**
 * Order Customer Details
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/order/order-details-customer.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.4.4
 */

defined( 'ABSPATH' ) || exit;

?>
<section class="woocommerce-customer-details">

	<h2 class="woocommerce-column__title"><?php esc_html_e( 'Customer Details', 'b2b-ecommerce' ); ?></h2>

	<address>
			<?php echo wp_kses_post( get_post_meta( $quote->get_id(), 'first_name', true ) ); ?>
			<?php echo wp_kses_post( get_post_meta( $quote->get_id(), 'last_name', true ) ); ?>
			<br>   
			<?php echo wp_kses_post( get_post_meta( $quote->get_id(), 'email', true ) ); ?>   
			<br>   
			<?php echo wp_kses_post( get_post_meta( $quote->get_id(), 'message', true ) ); ?>   
	</address>
	<?php
	/*
	@name: woocommerce_quote_details_after_customer_details
	@desc: Runs after Comments section on View quotes page on my account.
	@param: (object) $quote Current Quote object.
	@package: b2b-ecommerce-for-woocommerce
	@module: request for quote
	@type: action
	*/
	?>
	<?php do_action( 'woocommerce_quote_details_after_customer_details', $quote ); ?>
		
</section>
