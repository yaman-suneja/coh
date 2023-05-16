<?php
/**
 * The Template for displaying Credit Payment Log content tab on WooCommerce My Account page.
 *
 * @package B2b Ecommerce For Woocommerce/Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class='my_credit_logs_content'>
	<h2 id="my_credit_logs_heading"><?php esc_html_e( "Credit Payment Log's", 'b2b-ecommerce' ); ?></h2>
	<div>
		<h3><?php esc_html_e( 'Credit Available :', 'b2b-ecommerce' ); ?><span><b><?php echo wp_kses_post( b2be_user_credit_payments_balance( $user_id ) ); ?></b></span></h3>
	</div>
				<table class="table table-striped">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Order #', 'b2b-ecommerce' ); ?></th>
							<th><?php esc_html_e( 'Event', 'b2b-ecommerce' ); ?></th>
							<th><?php esc_html_e( 'Amount', 'b2b-ecommerce' ); ?></th>
							<th><?php esc_html_e( 'Time', 'b2b-ecommerce' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						if ( $logs ) {
							foreach ( $logs as $log ) {

								?>
						<tr>
							<td>
								<?php
								if ( 'Add Credit Balance' != $log->event ) {
									echo esc_html( '#' . $log->post_id );
								} else {
									echo esc_html( '-' );
								}
								?>
									
							</td>
							<td><?php echo esc_html( $log->event ); ?></td>
							<td>
								<?php
								if ( 'Credit Deducted' == $log->event ) {
									echo esc_html( '-' . $log->amount );
								} else {
									echo esc_html( '+' . $log->amount );
								}
								?>
								
							</td>
							<td><?php echo esc_html( $log->created ); ?></td>
						</tr>
								<?php
							}
						}
						?>
					</tbody>
				</table>
			</div>
