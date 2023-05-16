<?php
/**
 * File For B2b Ecommerce For Woocomerce custom Roles Post Type.
 *
 * @package class-b2be-custom-roles-cpt.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

if ( ! class_exists( 'B2BE_Custom_Roles_CPT' ) ) {

	/**
	 * Main Class For Custom Role.
	 */
	class B2BE_Custom_Roles_CPT {

		/**
		 * Main Function.
		 */
		public function __construct() {

			add_action( 'init', array( __CLASS__, 'register_custom_roles_post_type' ), 0 );
			add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 30 );
			add_action( 'publish_codup-custom-roles', array( $this, 'b2be_add_custom_role' ), 10, 2 );
			add_action( 'wp_trash_post', array( $this, 'b2be_delete_custom_role' ), 10 );
			add_action( 'admin_head', array( $this, 'b2be_custom_role_remove_cpt_trash_button' ), 10, 1 );
			add_filter( 'post_row_actions', array( $this, 'b2be_custom_role_remove_cpt_row_actions_post' ), 10, 2 );

			add_action( 'admin_menu', array( $this, 'b2be_create_submenu_page' ) );

		}

		/**
		 * Register core post types.
		 */
		public static function register_custom_roles_post_type() {

			$labels = array(
				'name'               => _x( 'Custom Role', 'post type general name', 'b2b-ecommerce' ),
				'singular_name'      => _x( 'Role', 'post type singular name', 'b2b-ecommerce' ),
				'menu_name'          => _x( 'Custom Role', 'admin menu', 'b2b-ecommerce' ),
				'name_admin_bar'     => _x( 'Custom Role', 'add new on admin bar', 'b2b-ecommerce' ),
				'add_new'            => _x( 'Add New', 'quiz', 'b2b-ecommerce' ),
				'add_new_item'       => __( 'Add New Role', 'b2b-ecommerce' ),
				'new_item'           => __( 'New Role', 'b2b-ecommerce' ),
				'edit_item'          => __( 'Edit Role', 'b2b-ecommerce' ),
				'view_item'          => __( 'View Role', 'b2b-ecommerce' ),
				'all_items'          => __( 'All Roles', 'b2b-ecommerce' ),
				'search_items'       => __( 'Search Roles', 'b2b-ecommerce' ),
				'parent_item_colon'  => __( 'Parent Roles:', 'b2b-ecommerce' ),
				'not_found'          => __( 'No custom roles found.', 'b2b-ecommerce' ),
				'not_found_in_trash' => __( 'No custom roles found in Trash.', 'b2b-ecommerce' ),
			);
			$args   = array(
				'labels'             => $labels,
				'description'        => __( 'Roles for WooCommerce products.', 'b2b-ecommerce' ),
				'public'             => true,
				'publicly_queryable' => true,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'query_var'          => true,
				'rewrite'            => array( 'slug' => 'cc-roles' ),
				'capability_type'    => 'post',
				'has_archive'        => true,
				'hierarchical'       => false,
				'menu_position'      => 70,
				'menu_icon'          => 'dashicons-groups',
				'supports'           => array( 'title' ),
			);
			register_post_type( 'codup-custom-roles', $args );
		}

		/**
		 * Add WC Meta boxes.
		 */
		public function add_meta_boxes() {

			if ( isset( $_GET['post'] ) ) {
				add_meta_box( 'cc-custom-roles-rfq', sprintf( __( 'RFQ', 'b2b-ecommerce' ) ), array( $this, 'b2be_add_custom_role_rfq' ), 'codup-custom-roles', 'normal', 'high' );
				add_meta_box( 'cc-custom-roles-shipping-exempts', sprintf( __( 'Shipping Exemption', 'b2b-ecommerce' ) ), array( $this, 'b2be_add_custom_role_shipping_exempts' ), 'codup-custom-roles', 'normal', 'high' );
				add_meta_box( 'cc-custom-roles-tax-exempts', sprintf( __( 'Tax Exemption', 'b2b-ecommerce' ) ), array( $this, 'b2be_add_custom_role_tax_exempts' ), 'codup-custom-roles', 'normal', 'high' );
				add_meta_box( 'cc-custom-roles-custom-payment-method', sprintf( __( 'WooCommerce Payment Method(s)', 'b2b-ecommerce' ) ), array( $this, 'b2be_woocomerce_payment_fields' ), 'codup-custom-roles', 'normal', 'low' );
				add_meta_box( 'cc-custom-roles-default-payment-method', sprintf( __( 'B2B Ecommerce Payment Method(s)', 'b2b-ecommerce' ) ), array( $this, 'b2be_custom_role_has_term_payment_fields' ), 'codup-custom-roles', 'normal', 'low' );

				add_meta_box( 'cc-credit', sprintf( __( 'Credit Payment(s)', 'b2b-ecommerce' ) ), array( $this, 'b2be_add_custom_role_credit' ), 'codup-custom-roles', 'side' );
			}
			remove_meta_box( 'slugdiv', 'codup-custom-roles', 'normal' );
			remove_meta_box( 'astra_settings_meta_box', 'codup-custom-roles', 'side' );

		}

		/**
		 * Function to render fields.
		 *
		 * @param object $post Post object.
		 */
		public function b2be_add_custom_role_credit( $post ) {
			$post_id = $post->ID;
			include CWRFQ_PLUGIN_DIR . '/includes/admin/credit-payment/views/credit-fields.php';

		}

		/**
		 * Create sub menu in company post type.
		 *
		 * @since 1.1.1.0
		 */
		public function b2be_create_submenu_page() {
			add_submenu_page(
				'edit.php?post_type=codup-custom-roles',
				__( ' Credit Logs', 'b2b-ecommerce' ),
				__( 'Credit Logs', 'b2b-ecommerce' ),
				'manage_options',
				'credit-logs',
				array( $this, 'b2be_view_credit_payment_logs_list' )
			);
		}

		/**
		 * Create Credit logs.
		 *
		 * @since 1.1.1.0
		 */
		public function b2be_view_credit_payment_logs_list() {

			$user_id = get_current_user_id();
			$logs    = b2be_users_credit_payments_logs( $user_id );

			include CWRFQ_PLUGIN_DIR . '/includes/admin/credit-payment/views/credit-payment-logs.php';
		}

		/**
		 * Function to render fields.
		 *
		 * @param object $post Post object.
		 */
		public function b2be_add_custom_role_rfq( $post ) {

			$post_id = $post->ID;
			include CWRFQ_PLUGIN_DIR . '/includes/admin/user-role/views/b2be-user-role-fields.php';

		}

		/**
		 * Function to render shipping_exempts fields.
		 *
		 * @param object $post Post Object.
		 */
		public function b2be_add_custom_role_shipping_exempts( $post ) {

			$post_id          = $post->ID;
			$shipping_methods = WC()->shipping->get_shipping_methods();

			include CWRFQ_PLUGIN_DIR . '/includes/admin/user-role/views/b2be-user-role-shipping-exempts.php';
		}

		/**
		 * Function to render shipping_exempts fields.
		 *
		 * @param object $post Post Object.
		 */
		public function b2be_add_custom_role_tax_exempts( $post ) {

			$post_id        = $post->ID;
			$tax_classes    = WC_Tax::get_tax_classes();
			$avatax_enable  = false;
			$active_plugins = (array) get_option( 'active_plugins', array() );

			if ( is_multisite() ) {
				$active_plugins = array_merge( self::$active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
			}

			if ( in_array( 'woocommerce-avatax/woocommerce-avatax.php', $active_plugins ) || array_key_exists( 'woocommerce-avatax/woocommerce-avatax.php', $active_plugins ) ) {
				$avatax_enable = true;
			}

			include CWRFQ_PLUGIN_DIR . '/includes/admin/user-role/views/b2be-user-role-tax-exempts.php';
		}

		/**
		 * Funtion to create custom roles.
		 *
		 * @param int    $post_id Post id.
		 * @param object $post Post Object.
		 */
		public function b2be_add_custom_role( $post_id, $post ) {

			if ( ! empty( $_POST['_wpnonce'] ) ) {
				wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ) );
			}

			global $wp_roles;
			$default_roles = array( 'administrator', 'editor', 'author', 'contributor', 'subscriber', 'customer', 'shop_manager' );

			if ( ! role_exists( $post->post_name ) ) {
				add_role( $post->post_name, $post->post_title, array( 'read' => true ) );
			} else {
				if ( $post->post_title != $wp_roles->roles[ $post->post_name ]['name'] ) {
					if ( ! in_array( $post->post_name, $default_roles ) ) {
						remove_role( $post->post_name );
						add_role( $post->post_name, $post->post_title, array( 'read' => true ) );
					}
				}
			}

			if ( isset( $_POST['enable_rfq'] ) && 'on' == $_POST['enable_rfq'] ) {
				update_post_meta( $post_id, 'enable_rfq', 'yes' );
			} else {
				update_post_meta( $post_id, 'enable_rfq', 'no' );
			}

			if ( isset( $_POST['disable_add_to_cart'] ) && 'on' == $_POST['disable_add_to_cart'] ) {
				update_post_meta( $post_id, 'disable_add_to_cart', 'yes' );
			} else {
				update_post_meta( $post_id, 'disable_add_to_cart', 'no' );
			}

			if ( isset( $_POST['shipping_exempt'] ) ) {
				update_post_meta( $post_id, 'shipping_exempt', filter_input( INPUT_POST, 'shipping_exempt', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY ) );
			} else {
				update_post_meta( $post_id, 'shipping_exempt', array() );
			}

			if ( isset( $_POST['tax_exempt'] ) ) {
				update_post_meta( $post_id, 'tax_exempt', filter_input( INPUT_POST, 'tax_exempt', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY ) );
			} else {
				update_post_meta( $post_id, 'tax_exempt', array() );
			}

			if ( isset( $_POST['ccr_credit_value'] ) && sanitize_text_field( wp_unslash( $_POST['ccr_credit_value'] ) ) > 0 ) {

				update_post_meta( $post_id, 'ccr_credit_value', sanitize_text_field( wp_unslash( $_POST['ccr_credit_value'] ) ) );
				// maintain credit logs.
				b2be_maintain_credit_payments( $post_id, get_current_user_id(), sanitize_text_field( wp_unslash( $_POST['ccr_credit_value'] ) ), 'Add Credit Balance' );

				$all_users = get_users( array( 'role__in' => array( $post->post_name ) ) );
				foreach ( $all_users as $_user ) {
					$credit_balance = get_user_meta( $_user->ID, 'credit_payment_bal', true, 0 ) + sanitize_text_field( wp_unslash( $_POST['ccr_credit_value'] ) );
					update_user_meta( $_user->ID, 'credit_payment_bal', $credit_balance );
				}
			}

			if ( isset( $_POST['enable_b2b_credit_payment'] ) ) {
				update_post_meta( $post_id, 'enable_b2b_credit_payment', sanitize_text_field( wp_unslash( $_POST['enable_b2b_credit_payment'] ) ) );
			} else {
				update_post_meta( $post_id, 'enable_b2b_credit_payment', 'unchecked' );
			}

			if ( isset( $_POST['avalara_tax_exempt'] ) ) {
				update_post_meta( $post_id, 'avalara_tax_exempt', sanitize_text_field( wp_unslash( $_POST['avalara_tax_exempt'] ) ) );
			} else {
				update_post_meta( $post_id, 'avalara_tax_exempt', 'no' );
			}

			$global_has_term_enabled = get_option( 'codup-rfq_enable_has_terms', 'no' );

			$gateways = b2be_get_formatted_payment_methods( 'woocommerce' );
			$users    = get_users( array( 'role__in' => $post->post_name ) ); // This role users...

			if ( ! empty( $gateways ) ) {
				foreach ( $gateways as $id => $payment_method_name ) {
					update_post_meta( $post_id, $id, 'no' );
					if ( isset( $_POST[ $id ] ) ) {
						update_post_meta( $post_id, $id, 'yes' );
					}

					if ( $users ) {
						foreach ( $users as $key => $user ) {
							$user_id = $user->ID;
							update_user_meta( $user_id, $id, 'no' );
							if ( isset( $_POST[ $id ] ) ) {
								update_user_meta( $user_id, $id, 'yes' );
							}
						}
					}
				}
			}

			if ( 'yes' === $global_has_term_enabled ) {

				// Saving the role settings...
				if ( isset( $_POST['b2be_role_based_payment_method'] ) ) {
					update_post_meta( $post_id, 'b2be_role_based_payment_method', sanitize_text_field( wp_unslash( $_POST['b2be_role_based_payment_method'] ) ) );
				} else {
					update_post_meta( $post_id, 'b2be_role_based_payment_method', '' );
				}

				// Applying the role settings on users...
				if ( $users ) {
					foreach ( $users as $key => $user ) {
						$user_id = $user->ID;
						if ( isset( $_POST['b2be_role_based_payment_method'] ) ) {
							update_user_meta( $user_id, 'b2be_user_based_payment_method', sanitize_text_field( wp_unslash( $_POST['b2be_role_based_payment_method'] ) ) );
						} else {
							update_user_meta( $user_id, 'b2be_user_based_payment_method', '' );
						}
					}
				}
			}

		}

		/**
		 * Function to delete custom roile.
		 *
		 * @param int $post_id Post Id.
		 */
		public function b2be_delete_custom_role( $post_id ) {

			if ( 'codup-custom-roles' != get_post_type( $post_id ) ) {
				return;
			}

			$default_roles = array( 'administrator', 'editor', 'author', 'contributor', 'subscriber', 'customer', 'shop_manager' );
			if ( in_array( explode( '__trashed', get_post( $post_id )->post_name )[0], $default_roles ) ) {

				wp_die( esc_html__( 'This is a WordPress default role. You cannot delete it.', 'b2b-ecommerce' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped.

			} else {

				remove_role( explode( '__trashed', get_post( $post_id )->post_name )[0] );

			}

		}

		/**
		 * Function To remove trash buton from WordPress default roles.
		 */
		public function b2be_custom_role_remove_cpt_trash_button() {
			$current_screen = get_current_screen();

			// Hides the "Move to Trash" link on the post edit page.
			if ( 'post' === $current_screen->base && 'codup-custom-roles' === $current_screen->post_type ) {
				?><style>#post-body #post-body-content #titlediv .inside, #preview-action{  display: none !important;  }</style>
				<?php
				$default_roles = array( 'administrator', 'editor', 'author', 'contributor', 'subscriber', 'customer', 'shop_manager' );
				if ( isset( $_GET['post'] ) && in_array( explode( '__trashed', get_post( $_GET['post'] )->post_name )[0], $default_roles ) ) {
					?>
						<style>#delete-action { display: none !important; }</style>
						<script>
							jQuery(document).ready(function ($) {
								$( "input[name='post_title']" ).prop( "disabled", true );	
							});
						</script>
					<?php
				}
			}
		}

		/**
		 * Function to remove trash button from rows.
		 *
		 * @param array  $actions Actions array.
		 * @param object $post Post object.
		 */
		public function b2be_custom_role_remove_cpt_row_actions_post( $actions, $post ) {

			if ( 'codup-custom-roles' === $post->post_type ) {
				$default_roles = array( 'administrator', 'editor', 'author', 'contributor', 'subscriber', 'customer', 'shop_manager' );
				if ( in_array( explode( '__trashed', $post->post_name )[0], $default_roles ) ) {
					unset( $actions['clone'] );
					unset( $actions['trash'] );
				}
				unset( $actions['view'] );
				unset( $actions['inline hide-if-no-js'] );
			}

			return $actions;
		}

		/**
		 * Function to render default payment methods.
		 *
		 * @param object $post Post object.
		 */
		public function b2be_woocomerce_payment_fields( $post ) {

			$gateways = b2be_get_formatted_payment_methods( 'woocommerce' );
			if ( empty( $gateways ) ) {
				echo '<p style="font-size: 15px;text-align: center;">' . esc_html__( 'No Payment Methods are available.', 'b2b-ecommerce' ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped. 
				return;
			}

			$post_id = $post->ID;
			foreach ( $gateways as $id => $payment_method ) {
				?>
				<div class="options_group">
					<div class="form-field enable_rfq_field">
						<div class="title">
							<?php echo wp_kses_post( $payment_method->title ); ?>
						</div>
						<div class="desc-options">
							<input type="checkbox" name="<?php echo wp_kses_post( $payment_method->id ); ?>" id="<?php echo wp_kses_post( $payment_method->id ); ?>" value="1" <?php echo ( get_post_meta( $post_id, $payment_method->id, true ) == 'yes' ) ? 'checked="checked"' : ''; ?> >
							<span><?php echo esc_html__( 'This will enable ', 'b2b-ecommerce' ) . wp_kses_post( $payment_method->title ) . esc_html__( ' payment method ', 'b2b-ecommerce' ); ?></span>
						</div>
					</div>
				</div>
				<?php
			}
		}

		/**
		 * Function to render default payment methods.
		 *
		 * @param object $post Post object.
		 */
		public function b2be_custom_role_has_term_payment_fields( $post ) {

			// Send empty parameter to get all payment method...
			$gateways                = b2be_get_formatted_payment_methods( 'b2be_ecommerce' );
			$global_has_term_enabled = get_option( 'codup-rfq_enable_has_terms', 'no' );

			if ( 'no' == $global_has_term_enabled ) {
				echo '<p style="font-size: 15px;text-align: center;">' . esc_html__( 'To use this feature enable it from ', 'b2b-ecommerce' ) . '<a href="' . wp_kses_post( site_url() ) . '/wp-admin/admin.php?page=wc-settings&tab=codup-b2b-ecommerce&section=codup-payment-method">' . esc_html__( 'Payment Method\'s settings', 'b2b-ecommerce' ) . '</a></p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped. 
				return;
			}

			if ( empty( $gateways ) ) {
				echo '<p style="font-size: 15px;text-align: center;">' . esc_html__( 'No Payment Methods are available. Add some from ', 'b2b-ecommerce' ) . '<a href="' . wp_kses_post( site_url() ) . '/wp-admin/admin.php?page=wc-settings&tab=codup-b2b-ecommerce&section=codup-payment-method">' . esc_html__( 'Payment Method\'s settings', 'b2b-ecommerce' ) . '</a></p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped. 
				return;
			}

			$post_id = $post->ID;
			if ( 'yes' === $global_has_term_enabled ) {
				?>
				<script>
					jQuery(document).ready( function($) {
						$( ".b2be_role_gateways" ).on( "click", function() {
							let checked = $(this).prop("checked");
							if ( checked ) {
								$(this).closest( ".options_group" ).siblings( ".options_group" ).find( ".b2be_role_gateways" ).removeAttr( "checked" );
							}
						})
					})
				</script>
				<?php
				foreach ( $gateways as $id => $payment_method ) {
					?>
					<div class="options_group">
						<div class="form-field enable_rfq_field">
							<div class="title">
								<?php echo wp_kses_post( $payment_method->title ); ?>
							</div>
							<div class="desc-options">
								<input class="b2be_role_gateways" type="checkbox" name="b2be_role_based_payment_method" id="<?php echo wp_kses_post( $payment_method->id ); ?>" value="<?php echo wp_kses_post( $payment_method->id ); ?>" <?php echo ( get_post_meta( $post_id, 'b2be_role_based_payment_method', true ) == $payment_method->id ) ? 'checked="checked"' : ''; ?> >
								<span><?php echo esc_html__( 'This will enable ', 'b2b-ecommerce' ) . wp_kses_post( $payment_method->title ) . esc_html__( ' payment method ', 'b2b-ecommerce' ); ?></span>
							</div>
						</div>
					</div>
					<?php
				}
			} else {
				echo '<p style="font-size: 15px;text-align: center;">' . esc_html__( 'Enable The Payment Methods from ', 'b2b-ecommerce' ) . '<a href="' . wp_kses_post( site_url() ) . '/wp-admin/admin.php?page=wc-settings&tab=codup-b2b-ecommerce&section=codup-payment-method">' . esc_html__( 'Payment Method\'s settings', 'b2b-ecommerce' ) . '</a></p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped. 
				return;
			}
		}

	}
	new B2BE_Custom_Roles_CPT();
}
