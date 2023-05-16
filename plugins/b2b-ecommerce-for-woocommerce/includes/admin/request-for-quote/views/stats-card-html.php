<?php
/**
 * Quote Items Template.
 *
 * @package codupio-request-for-quote-d659b8ba1ef2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


?>

<table class='rfq-stat-table table-responsive striped widefat text-center' style='text-align:center;'>
		<thead style=''>
			<tr>
				<th><?php esc_html_e( 'Requested', 'b2b-ecommerce' ); ?></th>
				<th><?php esc_html_e( 'Quoted', 'b2b-ecommerce' ); ?></th>
				<th><?php esc_html_e( 'Accepted', 'b2b-ecommerce' ); ?></th>
				<th><?php esc_html_e( 'Need Revision', 'b2b-ecommerce' ); ?></th>
				<th><?php esc_html_e( 'Rejected', 'b2b-ecommerce' ); ?></th>
			</tr>    
		</thead>
		<tbody>
			<tr>
				<td class='card_rfq_td'><?php echo wp_kses_post( b2be_get_quote_count_by_status( 'requested' ) ); ?></td>
				<td class='card_rfq_td'><?php echo wp_kses_post( b2be_get_quote_count_by_status( 'quoted' ) ); ?></td>
				<td class='card_rfq_td'><?php echo wp_kses_post( b2be_get_quote_count_by_status( 'accepted' ) ); ?></td>
				<td class='card_rfq_td'><?php echo wp_kses_post( b2be_get_quote_count_by_status( 'need-revision' ) ); ?></td>
				<td class='card_rfq_td'><?php echo wp_kses_post( b2be_get_quote_count_by_status( 'rejected' ) ); ?></td>
			</tr>
		</tbody>
		</table>
