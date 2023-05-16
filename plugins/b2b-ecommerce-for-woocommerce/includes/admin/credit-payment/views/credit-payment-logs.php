<?php
/**
 * The Template for displaying Credit Payment Log content tab on WooCommerce My Account page.
 *
 * @package B2b Ecommerce For Woocommerce/Credit Payment.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class='my_credit_logs_content' style="padding-right: 30px;">
	<h2 id="my_credit_logs_heading"><?php esc_html_e( "Credit Payment Log's", 'b2b-ecommerce' ); ?></h2>

				<table class="wp-list-table widefat fixed striped table-view-list ">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Custom Role ID', 'b2b-ecommerce' ); ?></th>
							<th><?php esc_html_e( 'Event', 'b2b-ecommerce' ); ?></th>
							<th><?php esc_html_e( 'Amount', 'b2b-ecommerce' ); ?></th>
							<th><?php esc_html_e( 'Time', 'b2b-ecommerce' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						if ( $logs ) {
							foreach ( $logs as $log ) {

								if ( ! empty( filter_input( INPUT_GET, 'role_id', FILTER_SANITIZE_STRING ) ) && filter_input( INPUT_GET, 'role_id', FILTER_SANITIZE_STRING ) != $log->post_id ) {
									continue;
								}

								?>
						<tr>
							<td><?php echo esc_html( $log->post_id ); ?></td>
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
