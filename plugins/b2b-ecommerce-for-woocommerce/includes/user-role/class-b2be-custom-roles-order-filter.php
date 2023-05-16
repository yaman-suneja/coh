<?php
/**
 * File For B2b Ecommerce For Woocomerce custom Roles Post Type.
 *
 * @package class-b2be-custom-roles-cpt.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

if ( ! class_exists( 'B2BE_Custom_Roles_Order_Filter' ) ) {

	/**
	 * Main Class For Custom Role.
	 */
	class B2BE_Custom_Roles_Order_Filter {

		/**
		 * Main Function.
		 */
		public function __construct() {

			add_action( 'restrict_manage_posts', array( $this, 'role_based_filter_dropdown' ), 9 );
			add_filter( 'request', array( $this, 'request_query' ) );
		}

		/**
		 * Drop Down For Filter Orders Based On Role.
		 */
		public function role_based_filter_dropdown() {

			global $post_type;
			$custom_roles = get_custom_added_roles();
			if ( 'shop_order' == $post_type ) {
				?>
				<select name="filter_by_roles">
					<option value=""><?php echo esc_html__( 'All Roles', 'b2b-ecommerce' ); ?></option>
					<?php foreach ( $custom_roles as $role_id => $role_name ) { ?>
						<option value="<?php echo wp_kses_post( $role_id ); ?>" <?php echo ( isset( $_GET['filter_by_roles'] ) && $role_id == $_GET['filter_by_roles'] ) ? 'selected' : ''; ?>><?php echo wp_kses_post( $role_name ); ?></option>
					<?php } ?>
				</select> 
				<?php
			}
		}

		/**
		 * Function To Filter Orders Based On Role.
		 *
		 * @param object $query_vars Query Object For The Order Listing.
		 */
		public function request_query( $query_vars ) {

			global $pagenow;
			$order_ids    = array();
			$post__not_in = array();
			if ( 'edit.php' == $pagenow && isset( $_GET['filter_by_roles'] ) && isset( $_GET['post_type'] ) && '' != $_GET['filter_by_roles'] && 'shop_order' == $_GET['post_type'] ) {
				$args   = array(
					'limit' => -1,
				);
				$orders = wc_get_orders( $args );
				foreach ( $orders as $key => $order ) {
					$customer_email = get_post_meta( $order->get_id(), '_billing_email', true );
					$user           = get_user_by( 'email', $customer_email );
					if ( $user ) {
						$customer_id   = $user->ID;
						$customer_role = get_current_user_role_by_id( $customer_id );
						if ( isset( $customer_role[0] ) && $_GET['filter_by_roles'] == $customer_role[0] ) {
							$order_ids[] = $order->get_id();
						} else {
							$post__not_in[] = $order->get_id();
						}
					} else {
						$post__not_in[] = $order->get_id();
					}
				}
				$query_vars['post__in']     = $order_ids;
				$query_vars['post__not_in'] = $post__not_in;

			}
			return $query_vars;
		}

	}
}
new B2BE_Custom_Roles_Order_Filter();
