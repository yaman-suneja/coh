<?php
/**
 * WC RFQ.
 *
 * @package b2b-ecommerce-for-woocomerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'B2BE_ReOrder_Loader' ) ) {
	/**
	 * Class Codup_Payment_Method.
	 */
	class B2BE_ReOrder_Loader {
		/**
		 * Cart Variable.
		 */
		public function __construct() {

			$this->includes();

		}

		/**
		 *  Function Includes.
		 */
		public function includes() {

			include_once 'class-b2be-re-order-settings.php';
			add_action( 'init', array( $this, 'init' ), 10, 1 );

		}

		/**
		 *  Function Init.
		 */
		public function init() {

			if ( empty( get_option( 'codup-reorder_reorder_btn_text' ) ) ) {
				update_option( 'codup-reorder_reorder_btn_text', 'Re-Order' );

			}

			$enable_reorder = get_option( 'codup-reorder_enable_reorder', 'no' );

			if ( 'yes' == $enable_reorder ) {
				add_filter( 'woocommerce_my_account_my_orders_actions', array( $this, 'b2be_my_account_order_actions' ), 10, 2 );
			}

		}

		/**
		 * My account Order actions.
		 *
		 * @param array  $actions Actions rendered on my account listing page.
		 * @param object $order Current order object.
		 */
		public function b2be_my_account_order_actions( $actions, $order ) {

			$reorder_btn_text = get_option( 'codup-reorder_reorder_btn_text', true );

			/*
			@name: b2be_valid_order_statuses_for_order_again
			@desc: Modify Valid Statues for reorder functionality.
			@param: (array) $valid_statuses Array of Statuses on which reorder is allowed.
			@package: b2b-ecommerce-for-woocommerce
			@module: re order
			@type: filter
			*/
			if ( ! $order || ! $order->has_status( apply_filters( 'b2be_valid_order_statuses_for_order_again', array( 'completed' ) ) ) || ! is_user_logged_in() ) {
				return $actions;
			}

			$actions['reorder'] = array(
				'url'  => wp_nonce_url( add_query_arg( 'order_again', $order->get_id() ), 'woocommerce-order_again' ),
				'name' => esc_html( $reorder_btn_text, 'b2b-ecommerce' ),
			);

			return $actions;
		}


	}

}
new B2BE_ReOrder_Loader();
