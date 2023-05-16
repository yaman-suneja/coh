<?php
/**
 * Template for the tier entry. Based on the WooCommerce file class-wc-admin-settings.php.
 *
 * @package B2B_E-commerce_For_WooCommerce/signup-form//views
 */

// prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="b2be-signup-form-entries-wrapper">

	<div class="b2be-signup-form-entries-top">
		<h1>SignUp Form Entries</h1>
	</div>
	<div class="b2be-signup-form-entries-bottom">
		<table id="b2be-signup-form-entry-table" >
			<thead>
				<tr><td> 
					<!-- No Header Needed -->
				</td></tr>
			</thead>
			<tbody>
				<?php
				foreach ( $b2be_user_ids as $user_id ) {
					$user = get_userdata( $user_id );
					?>
					<tr>
						<td>
							<div class="b2be-signup-fomr-user-info">
								<div class="b2be-signup-form-user-email">
									<span class="user-email">#<?php echo wp_kses_post( $user->ID ); ?> <?php echo wp_kses_post( $user->user_email ); ?></span>
									<span class="user-registered-date"><small><i>Registered at: </i> <?php echo wp_kses_post( $user->user_registered ); ?></small></span>
								</div>
								<div class="b2be-signup-form-user-toggle">
									<span class="b2be-signup-user-status">
										<span style="margin-right:10px;"><strong>Status: </strong></span>	
										<span><?php echo wp_kses_post( get_b2be_signup_user_status( $user_id ) ); ?></span>
									</span>
									<span class="dashicons dashicons-arrow-down"></span>
								</div>
							</div>
							<div class="b2be-signup-form-user-entries-wrapper">
								<div class="b2be-signup-form-user-entries">
									<?php
										$user_entries = get_user_meta( $user->ID, 'b2be_sign_up_entries', true );
									if ( ! empty( $user_entries ) ) {
										foreach ( $user_entries as $key => $user_entry ) {
											if ( isset( $user_entry['value'] ) && ! empty( $user_entry['value'] ) ) {
												?>
													 
														<p>
															<label for=""><?php echo wp_kses_post( $user_entry['name'] ); ?></label>
															<span for=""><?php echo wp_kses_post( get_signup_entries_formatted_values( $user_entry['value'], $user_entry['type'], $user ) ); ?></span>
														</p>
													<?php
											}
										}
									}
									?>
								</div>
							</div>
						</td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>

</div>
