<?php
/**
 * Class Codup User Profile Settings file.
 *
 * @package b2b-ecommerce-for-woocomerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'B2BE_User_Profile_Settings' ) ) {

	/**
	 * Class B2BE_User_Profile_Settings.
	 */
	class B2BE_User_Profile_Settings {

		/**
		 * Function setup_properties.
		 */
		public function __construct() {

			add_action( 'show_user_profile', array( $this, 'rfq_has_term_payment_fields' ) );
			add_action( 'edit_user_profile', array( $this, 'rfq_has_term_payment_fields' ) );
			add_action( 'user_register', array( $this, 'rfq_has_term_payment' ), 10, 1 );
			add_action( 'profile_update', array( $this, 'rfq_has_term_payment' ), 10, 1 );
			add_filter( 'woocommerce_available_payment_gateways', array( $this, 'unset_has_term_payment_for_user' ) );
		}

		/**
		 * Function init_from_fields.
		 *
		 * @param object $user user object.
		 */
		public function rfq_has_term_payment_fields( $user ) {

			$global_has_term_enabled = get_option( 'codup-rfq_enable_has_terms', 'no' );

			$gateways = b2be_get_formatted_payment_methods( 'woocommerce' );

			if ( ! empty( $gateways ) ) {

				?>
				<h3><?php esc_html_e( 'WooCommerce Payment Methods', 'b2b-ecommerce' ); ?></h3>
				<?php foreach ( $gateways as $id => $payment_method ) { ?>
					<table class="form-table">
						<tr>
							<th><label for=""><?php echo wp_kses_post( $payment_method->title ); ?></label></th>
							<td> 
								<input type="checkbox" name="<?php echo wp_kses_post( $payment_method->id ); ?>" id="<?php echo wp_kses_post( $payment_method->id ); ?>" value="1" <?php echo ( get_user_meta( $user->ID, $payment_method->id, true ) == 'yes' ) ? 'checked="checked"' : ''; ?> >
								<span><?php echo esc_html__( 'Enable ', 'b2b-ecommerce' ) . wp_kses_post( $payment_method->title ); ?></span><br><br>
								<span><i><?php echo esc_html__( 'This will enable ', 'b2b-ecommerce' ) . wp_kses_post( $payment_method->title ) . esc_html__( ' payment method ', 'b2b-ecommerce' ); ?></i></span>
							</td>
						</tr>
					</table>
					<?php
				}
			}

			if ( 'yes' === $global_has_term_enabled ) {

				$gateways = b2be_get_formatted_payment_methods( 'b2be_ecommerce' );
				if ( empty( $gateways ) ) {
					return;
				}
				?>
				<script>
					jQuery(document).ready( function($) {
						$( ".b2be_role_gateways" ).on( "click", function() {
							let checked = $(this).prop("checked");
							if ( checked ) {
								$(this).closest( "tr" ).siblings( "tr" ).find( ".b2be_role_gateways" ).removeAttr( "checked" );
							}
						})
					})
				</script>
				<h3><?php esc_html_e( 'B2B Ecommerce Payment Methods', 'b2b-ecommerce' ); ?></h3>
				<table class="form-table">
					<?php foreach ( $gateways as $id => $payment_method ) { ?>
						<tr>
							<th><label for=""><?php echo wp_kses_post( $payment_method->title ); ?></label></th>
							<td>
								<input class="b2be_role_gateways" type="checkbox" name="b2be_user_based_payment_method" id="<?php echo wp_kses_post( $payment_method->id ); ?>" value="<?php echo wp_kses_post( $payment_method->id ); ?>" <?php echo ( get_user_meta( $user->ID, 'b2be_user_based_payment_method', true ) == $payment_method->id ) ? 'checked="checked"' : ''; ?> >
								<span><?php echo esc_html__( 'Enable ', 'b2b-ecommerce' ) . wp_kses_post( $payment_method->title ); ?></span><br><br>
								<span><i><?php echo esc_html__( 'This will enable ', 'b2b-ecommerce' ) . wp_kses_post( $payment_method->title ) . esc_html__( ' payment method ', 'b2b-ecommerce' ); ?></i></span>
							</td>
						</tr>
					<?php } ?>
				</table>
				<?php
			}
		}

		/**
		 * Function rfq_has_term_payment.
		 *
		 * @param int $user_id user id object.
		 */
		public function rfq_has_term_payment( $user_id ) {
			global $pagenow;
			if ( 'user-edit.php' == $pagenow || 'profile.php' == $pagenow ) {

				if ( ! empty( $_REQUEST['_wpnonce'] ) ) {
					wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ) );
				}

				$global_has_term_enabled = get_option( 'codup-rfq_enable_has_terms', 'no' );
				$gateways                = b2be_get_formatted_payment_methods( 'woocommerce' );

				if ( ! empty( $gateways ) ) {
					foreach ( $gateways as $id => $payment_method_name ) {
						update_user_meta( $user_id, $id, 'no' );
						if ( isset( $_POST[ $id ] ) ) {
							update_user_meta( $user_id, $id, 'yes' );
						}
					}
				}
				if ( 'yes' === $global_has_term_enabled ) {
					if ( isset( $_POST['b2be_user_based_payment_method'] ) ) {
						update_user_meta( $user_id, 'b2be_user_based_payment_method', sanitize_text_field( wp_unslash( $_POST['b2be_user_based_payment_method'] ) ) );
					}
				}
			}
		}

		/**
		 * Function unset_has_term_payment_for_user.
		 *
		 * @param array $available_gateways woocommerce available_gateways array.
		 */
		public function unset_has_term_payment_for_user( $available_gateways ) {

			$global_has_term_enabled = get_option( 'codup-rfq_enable_has_terms', 'no' );
			$wc_gateways             = b2be_get_formatted_payment_methods( 'woocommerce' );

			if ( ! is_user_logged_in() ) {
				unset( $available_gateways['credit_payment'] );
			}

			if ( ! is_user_logged_in() ) {  // Don't show custom gateways if not logged in or disabled...
				foreach ( $available_gateways as $id => $payment_method ) {
					if ( ! in_array( $payment_method->id, array_keys( $wc_gateways ) ) ) {
						unset( $available_gateways[ $payment_method->id ] );
					}
				}
				return $available_gateways;
			}

			$customer = wp_get_current_user();
			$roles    = (array) $customer->roles;

			if ( ! empty( $roles[0] ) && 0 != b2be_custom_role_exists( $roles[0] ) ) {
				$post_id = b2be_custom_role_exists( $roles[0] );
			}

			if ( $wc_gateways ) {

				// For WC payment methods...
				foreach ( $wc_gateways as $id => $payment_method ) {
					$is_user_has_term = get_user_meta( $customer->ID, $payment_method->id, true );
					if ( ! empty( $post_id ) ) {
						$is_role_has_term = get_post_meta( $post_id, $payment_method->id, true );
					}
					if ( empty( $is_role_has_term ) && empty( $is_user_has_term ) ) {
						continue;
					}

					if ( ! empty( $is_user_has_term ) && 'yes' !== $is_user_has_term ) {
						unset( $available_gateways[ $payment_method->id ] );
					}
				}
			}

			if ( 'yes' == $global_has_term_enabled ) {

				$b2be_gateways = b2be_get_formatted_payment_methods( 'b2be_ecommerce' );
				if ( ! $b2be_gateways ) {
					return $available_gateways;
				}

				// For B2B payment methods...
				foreach ( $b2be_gateways as $id => $payment_method ) {
					if ( is_user_logged_in() ) {
						if ( ! empty( get_user_meta( $customer->ID, 'b2be_user_based_payment_method' ) ) ) {
							if ( get_user_meta( $customer->ID, 'b2be_user_based_payment_method', true ) == $payment_method->id ) {
								continue;
							}
						} elseif ( isset( $post_id ) && ! empty( get_post_meta( $post_id, 'b2be_role_based_payment_method' ) ) ) {
							if ( get_post_meta( $post_id, 'b2be_role_based_payment_method', true ) == $payment_method->id ) {
								continue;
							}
						}
					}
					unset( $available_gateways[ $payment_method->id ] );
				}
			}

			return $available_gateways;
		}
	}

}

new B2BE_User_Profile_Settings();
