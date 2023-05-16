<?php
/**
 * B2BE_Credit_Payment_Loader Class.
 *
 * @package class-b2be-credit-payment-loader.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'B2BE_Credit_Payment_Loader' ) ) {
	/**
	 * Class B2BE_Credit_Payment.
	 */
	class B2BE_Credit_Payment_Loader {

		/**
		 * Construct.
		 */
		public function __construct() {

			add_action( 'init', array( $this, 'init' ) );
			add_action( 'wp', array( $this, 'init_front' ) );
			add_action( 'plugins_loaded', array( $this, 'init_admin' ) );

			// Assign credit on user role change.
			add_action( 'set_user_role', array( $this, 'b2b_assign_credit_on_change_role' ), 10, 3 );

		}

		/**
		 * Initialize Admin only.
		 */
		public function init_admin() {
			add_filter( 'woocommerce_payment_gateways', array( $this, 'wc_credit_payment_to_gateways' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_b2b_credit_payment_scripts' ) );
		}

		/**
		 * Initialize Frontend.
		 */
		public function init_front() {

			if ( is_user_logged_in() && true == b2be_user_credit_payments_enable( get_current_user_id() ) ) {
				add_action( 'woocommerce_after_checkout_validation', array( $this, 'bcp_validate_balance' ), 10, 2 );
				add_filter( 'woocommerce_payment_gateways', array( $this, 'wc_credit_payment_to_gateways' ) );
			}

		}

		/**
		 * Include style and scripts in all over pluign.
		 */
		public function enqueue_b2b_credit_payment_scripts() {
			wp_enqueue_script( 'b2b_credit_payment_script', CWRFQ_ASSETS_DIR_URL . '/js/credit-payment/admin/admin.js', array(), rand() );
		}

		/**
		 * Initialize Gateway Settings Form Fields.
		 */
		public function init() {

			// Order Status change handling.
			add_action( 'woocommerce_order_status_changed', array( $this, 'adjust_credit_balance_status' ), 10, 4 );

			// Adding Credit Logs menu in my Account.
			if ( is_user_logged_in() && true == b2be_user_credit_payments_enable( get_current_user_id() ) ) {
				$this->add_my_credit_logs_endpoint();
				add_action( 'woocommerce_account_my-credit-payment-logs_endpoint', array( $this, 'my_credit_logs_tab_content' ) );
				add_filter( 'woocommerce_account_menu_items', array( $this, 'my_credit_logs_tab' ), 9 );
			}
		}

		/**
		 * Function to adjust credits on order status change.
		 *
		 * @param int    $user_id user_id.
		 * @param string $role role.
		 * @param array  $old_roles old_roles.
		 */
		public function b2b_assign_credit_on_change_role( $user_id, $role, $old_roles ) {

			$default = array(
				'post_type'      => 'codup-custom-roles',
				'posts_per_page' => -1,
			);

			$custom_roles_cpt = get_posts( $default );
			$new_credit       = 0;
			foreach ( $custom_roles_cpt as $key => $value ) {
				if ( $value->post_name == $role ) {
					$new_credit += b2be_get_total_assign_credit_in_role( $value->ID );
				}
			}

			if ( ! empty( $new_credit ) ) {

				$credit_balance = $new_credit;
				update_user_meta( $user_id, 'credit_payment_bal', $credit_balance );

			}

		}

		/**
		 * Function to adjust credits on order status change.
		 *
		 * @param int    $order_id order_id.
		 * @param string $old_status old_status.
		 * @param string $new_status new_status.
		 */
		public function adjust_credit_balance_status( $order_id, $old_status, $new_status ) {
			$order = wc_get_order( $order_id );

			$user_credit_balance = get_user_meta( $order->get_user_id(), 'credit_payment_bal', true, 0 );

			$_user = get_user_by( 'id', $order->get_user_id() );

			$default = array(
				'post_type'      => 'codup-custom-roles',
				'posts_per_page' => -1,
			);

			$custom_roles_cpt = get_posts( $default );

			foreach ( $_user->roles as $post_name => $post_title ) {

				foreach ( $custom_roles_cpt as $key => $value ) {
					if ( $value->post_name == $post_title ) {
						$assign_bal += b2be_get_total_assign_credit_in_role( $value->ID );
					}
				}
			}

			$credit_deducted_array = array( 'pending', 'processing', 'on-hold' );
			$credit_reverted_array = array( 'completed', 'cancelled', 'refunded', 'failed' );

			if ( 'credit_payment' == $order->get_payment_method() ) {

				if ( in_array( $new_status, $credit_deducted_array ) ) {

					if ( in_array( $old_status, $credit_reverted_array ) ) {
						if ( $user_credit_balance ) {
							if ( ( $user_credit_balance - $order->get_total() ) < 0 ) {
								return;
							}
						}

						b2be_maintain_credit_payments( $order_id, $order->get_user_id(), $order->get_total(), 'Credit Deducted' );
					}
				} elseif ( in_array( $new_status, $credit_reverted_array ) ) {

					if ( in_array( $old_status, $credit_deducted_array ) ) {
						if ( ! empty( $assign_bal ) && ! empty( $user_credit_balance ) ) {
							if ( ( $user_credit_balance + $order->get_total() ) > $assign_bal ) {
								return;
							}
						}

						b2be_maintain_credit_payments( $order_id, $order->get_user_id(), $order->get_total(), 'Credit Reverted' );
					}
				}
			}
		}

		/**
		 * Validated balance for credit payment.
		 *
		 * @param array $data Data array.
		 * @param array $errors Errors array.
		 */
		public function bcp_validate_balance( $data, $errors ) {

			if ( 'credit_payment' == $data['payment_method'] ) {
				$amount  = WC()->cart->total;
				$user_id = get_current_user_id();

				$passed = b2be_credit_validation( $user_id, floatval( $amount ) );

				if ( $passed ) {
					$errors->add( 'validation', $passed );
				}
			}

		}

		/**
		 * Add credit payment as payment gateway in options
		 *
		 * @param array $gateways Gateways array.
		 */
		public function wc_credit_payment_to_gateways( $gateways ) {

			// credit payment feature.
			include 'class-b2be-credit-payment.php';
			$gateways[] = 'B2BE_Credit_Payment';

			return $gateways;
		}


		/**
		 * Make end-point for Credit Payment Logs.
		 */
		public function add_my_credit_logs_endpoint() {
			add_rewrite_endpoint( 'my-credit-payment-logs', EP_ROOT | EP_PAGES );
		}


		/**
		 * Adding My Coupons tab on my account page menu.
		 *
		 * @param array $items items.
		 */
		public function my_credit_logs_tab( $items ) {
			$items['my-credit-payment-logs'] = __( 'Credit Payment', 'b2b-ecommerce' );
			return $items;
		}

		/**
		 * Add My Coupons tab content on my account page.
		 */
		public function my_credit_logs_tab_content() {

			$user_id = get_current_user_id();
			$logs    = b2be_users_credit_payments_logs( $user_id );

			require_once CWRFQ_PLUGIN_DIR . '/templates/credit-payment/b2be-credit-logs-content.php';
		}

	}
}
new B2BE_Credit_Payment_Loader();
