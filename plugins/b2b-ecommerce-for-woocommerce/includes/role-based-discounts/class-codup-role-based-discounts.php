<?php
/**
 * WC RFQ.
 *
 * @package codupio-request-for-quote-d659b8ba1ef2\Emails
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Codup_Role_Based_Discounts' ) ) {
	/**
	 * Class Codup_Role_Based_Discounts.
	 */
	class Codup_Role_Based_Discounts {

		/**
		 * Cart Variable.
		 */
		public function __construct() {
			Codup_Role_Based_Discounts_Settings::init();

			$is_enable_functionality = get_option( 'codup-role-based_enable_user_role' );
			if ( 'yes' == $is_enable_functionality ) {

				add_filter( 'woocommerce_product_data_tabs', __CLASS__ . '::user_role_product_settings_tab' );
				add_filter( 'woocommerce_product_data_panels', __CLASS__ . '::user_role_product_tab_content' );
				add_action( 'woocommerce_process_product_meta', __CLASS__ . '::save_user_role_product_fields' );

				add_action( 'woocommerce_save_product_variation', array( $this, 'save_discount_base_variations_field' ), 10, 2 );
				add_action( 'woocommerce_variation_options_pricing', array( $this, 'discount_base_variations_field' ), 10, 3 );
				add_filter( 'woocommerce_available_variation', array( $this, 'discount_base_variations_field_data' ) );

				add_action( 'wp_ajax_delete_user_role', array( $this, 'delete_user_role' ) );
				add_action( 'wp_ajax_nopriv_delete_user_role', array( $this, 'delete_user_role' ) );

				add_filter( 'woocommerce_get_price_html', array( $this, 'change_price_with_respect_to_user_role' ), 100, 2 );
				add_filter( 'woocommerce_variable_price_html', array( $this, 'change_variable_price_according_to_role' ), 100, 2 );

				add_action( 'woocommerce_before_calculate_totals', array( $this, 'apply_role_based_discount_on_cart_items' ) );

				add_action( 'admin_init', array( $this, 'add_custom_user_role' ), 10, 1 );

			}

		}

		/**
		 * Add RFQ tab in edit product screen.
		 *
		 * @param array $tabs Tabs.
		 * @return array
		 */
		public static function user_role_product_settings_tab( $tabs ) {

			if ( 'yes' == get_option( 'codup-role-baseddiscount_type_product' ) ) {

				$tabs['codup-role-based-discount'] = array(
					'label'  => __( 'Role Based Discount', 'codup-wcrfq' ),
					'target' => 'codup-user-role-options',
				);

			}
			return $tabs;
		}

		/**
		 * Show user role settings in ser role tab.
		 *
		 * @param int $post_id Current post id.
		 */
		public static function user_role_product_tab_content( $post_id ) {

			wp_nonce_field( 'user_role_product_settings', 'user_role_settings_nonce' );
			$user_roles = get_option( 'codup_ecommerce_role_based_settings' );
			?>
			<div id='codup-user-role-options' class='panel woocommerce_options_panel'>
				<?php if ( ! empty( $user_roles[0]['role'] ) ) { ?>
				<div class='options_group'>
					<h2 style="font-size: 20px;"><?php esc_html_e( 'Offer a discounted price for different customer roles.', 'codup-wcrfq' ); ?></h2>
					<p class="form-field">
						<label style="font-size: 15px;" for="custom-role-title"><b><?php esc_html_e( 'Role', 'codup-wcrfq' ); ?></b></label>
						<span style="font-size: 15px;" for="custom-discount-title"><b><?php esc_html_e( 'Discount (%)', 'codup-wcrfq' ); ?></b></span>
					</p>
					<?php
						$post_id          = isset( $_GET['post'] ) ? sanitize_text_field( wp_unslash( $_GET['post'] ) ) : '';
						$all_custom_roles = get_post_meta( $post_id, 'role_based_discounts', true );
					foreach ( $user_roles as $key => $display_name ) {
						$b2b_e_user_role = str_replace( ' ', '_', strtolower( $display_name['role'] ) );
						?>
							<p class="form-field">
							<label for="custom-role"><b><?php echo wp_kses_post( $display_name['role'] ); ?></b></label>
							<?php echo wp_kses_post( wc_help_tip( __( 'Enter a number between 0-100 for a percentage discount.', 'codup-wcrfq' ) ) ); ?>
							<input 
								type="text" 
								class="role-based-dicount" 
								name="user_role_discount[<?php echo wp_kses_post( $b2b_e_user_role ); ?>]" 
								id="user_role_discount[<?php echo wp_kses_post( $b2b_e_user_role ); ?>]" 
								placeholder="<?php esc_html_e( 'Enter Discount (%)', 'codup-wcrfq' ); ?>"
								value=<?php echo ( ! empty( $all_custom_roles[ $b2b_e_user_role ] ) ) ? wp_kses_post( $all_custom_roles[ $b2b_e_user_role ] ) : ''; ?> 
							> 
							</p>
							<?php
					}
					?>
				</div>
					<?php
				} else {
					?>
					<p class="empty-product-role-message"><?php esc_html_e( 'No User Role Found. Add One From Settings Page', 'codup-wcrfq' ); ?> </p>
					<?php
				}
				?>
			</div>
			<?php
		}
		/**
		 * Save Role Based Dicounts For Products.
		 *
		 * @param string $post_id Post ID.
		 */
		public static function save_user_role_product_fields( $post_id ) {

			if ( isset( $_POST['user_role_settings_nonce'] ) ) {
				$nonce = sanitize_text_field( wp_unslash( $_POST['user_role_settings_nonce'] ) );
				if ( ! wp_verify_nonce( $nonce, 'user_role_product_settings' ) ) {
					return;
				}
			}

			if ( isset( $_POST['user_role_discount'] ) ) {

				$role_based_discounts = ( 0 != count( $_POST['user_role_discount'] ) ) ? filter_input( INPUT_POST, 'user_role_discount', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY ) : '';
				update_post_meta( $post_id, 'role_based_discounts', $role_based_discounts );

			}

		}

		/**
		 * Delete Custom User Role.
		 */
		public function delete_user_role() {

			if ( isset( $_POST['user_role_settings_nonce'] ) ) {

				$nonce = sanitize_text_field( wp_unslash( $_POST['user_role_settings_nonce'] ) );
				wp_verify_nonce( $nonce, 'user_role_product_settings' );

			}

			$delete_role      = ( ! empty( $_POST['role_to_delete'] ) ) ? sanitize_text_field( wp_unslash( $_POST['role_to_delete'] ) ) : '';
			$all_custom_roles = get_option( 'codup_ecommerce_role_based_settings' );

			if ( $delete_role ) {

				foreach ( $all_custom_roles as $key => $display_name ) {

					$b2b_e_user_role = str_replace( ' ', '_', strtolower( $display_name['role'] ) );
					if ( $b2b_e_user_role == $delete_role ) {

						unset( $all_custom_roles[ $key ] );

					}
				}
				if ( wp_roles()->is_role( $b2b_e_user_role ) ) {
					remove_role( $delete_role );
				}
				update_option( 'codup_ecommerce_role_based_settings', $all_custom_roles );

			}
			wp_die();
		}

		/**
		 * Show Price Of Product With Respect To User Role.
		 *
		 * @param string $price Price of Current Product.
		 * @param object $product Product Object.
		 */
		public function change_price_with_respect_to_user_role( $price, $product ) {

			if ( is_user_logged_in() ) {
				$user_id                   = get_current_user_id();
				$user_role                 = get_current_user_role_by_id( $user_id );
				$is_product_based_discount = get_option( 'codup-role-baseddiscount_type_product' );
				$is_global_based_discount  = get_option( 'codup-role-baseddiscount_type_global' );

				if ( 'yes' == $is_product_based_discount ) {
					$discount = ( ! empty( get_product_based_discount( $product->get_ID(), $user_role[0] ) ) ) ? get_product_based_discount( $product->get_ID(), $user_role[0] ) : 0;
				}
				if ( 'yes' == $is_global_based_discount ) {
					$discount = ( ! empty( get_globaly_setted_discount( $user_role[0] ) ) ) ? get_globaly_setted_discount( $user_role[0] ) : 0;
				}
				if ( 'yes' == $is_product_based_discount && 'yes' == $is_global_based_discount ) {
					$discount = ( ! empty( get_product_based_discount( $product->get_ID(), $user_role[0] ) ) ) ? get_product_based_discount( $product->get_ID(), $user_role[0] ) : get_globaly_setted_discount( $user_role[0] );
				}

				if ( $discount ) {

					if ( $product->is_type( 'simple' ) ) {

						$product_price = ( ! empty( $product->get_regular_price() ) ) ? $product->get_regular_price() : $product->get_price();
						if ( ! empty( $product_price ) ) {

							$updated_price = $product_price * ( $discount / 100 );
							$updated_price = $product_price - $updated_price;
							$price         = '<del>' . wc_price( $product_price ) . '</del> ' . number_format( $updated_price, 2 );

						}
					}
				}
			}
			return $price;

		}

		/**
		 * Show price according to user role.
		 *
		 * @param int    $price Given price.
		 * @param object $product Product object.
		 */
		public function change_variable_price_according_to_role( $price, $product ) {

			if ( is_user_logged_in() ) {

				$variation_id = get_discounted_variation_price( $product, '' );
				if ( ! empty( $variation_id ) ) {

					$min_discount = get_discounted_variation_price( $product, 'min' );
					$max_discount = get_discounted_variation_price( $product, 'max' );

					if ( $min_discount == $max_discount ) {

						$variable_price   = min( get_variation__regular_price( $product ) );
						$discounted_price = '<del>' . wc_price( $variable_price ) . '</del> <span>' . wc_price( $max_discount ) . '</span>';

					} else {

						$discounted_price = '<span>' . wc_price( $min_discount ) . '</span> - <span>' . wc_price( $max_discount ) . '</span>';

					}

					return $discounted_price;
				}
			}
			return $price;

		}

		/**
		 * Apply Role Based Discount On Cart Items.
		 *
		 * @param object $cart_object Cart Object.
		 */
		public function apply_role_based_discount_on_cart_items( $cart_object ) {
			$cart_items = $cart_object->cart_contents;
			$user_id    = get_current_user_id();
			$user_role  = get_current_user_role_by_id( $user_id );

			$is_product_based_discount = get_option( 'codup-role-baseddiscount_type_product' );
			$is_global_based_discount  = get_option( 'codup-role-baseddiscount_type_global' );

			$discount = 0;

			if ( empty( $user_role ) ) {
				return;
			}

			if ( ! empty( $cart_items ) ) {
				foreach ( $cart_items as $key => $value ) {

					$product = wc_get_product( $value['product_id'] );
					if ( $product->is_type( 'simple' ) ) {

						if ( ! empty( get_product_based_discount( $product->get_ID(), $user_role[0] ) ) && 'yes' == $is_product_based_discount ) {

							$discount = get_product_based_discount( $product->get_ID(), $user_role[0] );

						} elseif ( ! empty( get_globaly_setted_discount( $user_role[0] ) ) && 'yes' == $is_global_based_discount ) {

							$discount = get_globaly_setted_discount( $user_role[0] );

						} else {

							$discount = 0;

						}
					} elseif ( $product->is_type( 'variable' ) ) {

						if ( ! empty( get_discounted_variation_price( $product, '' )[ $value['variation_id'] ] ) && 'yes' == $is_product_based_discount ) {

							$discount = get_discounted_variation_price( $product, '' )[ $value['variation_id'] ];

						} elseif ( ! empty( get_globaly_setted_discount( $user_role[0] ) ) && 'yes' == $is_global_based_discount ) {

							$discount = get_globaly_setted_discount( $user_role[0] );

						} else {

							$discount = 0;

						}
						$product = wc_get_product( $value['variation_id'] );

					}

					if ( $discount ) {

						$product_price = ( ! empty( $product->get_regular_price() ) ) ? $product->get_regular_price() : $product->get_price();

						$updated_price = $discount;

						if ( $product->is_type( 'simple' ) ) {

							$updated_price = $product_price * ( $discount / 100 );
							$updated_price = $product_price - $updated_price;

						}

						$value['data']->set_price( $updated_price );

					}
				}
			}
		}

		/**
		 * Generate discount based variation fields for variable product.
		 *
		 * @param int $loop product id.
		 * @param int $variation_data saved variation data.
		 * @param int $variation variation of the product.
		 */
		public function discount_base_variations_field( $loop, $variation_data, $variation ) {
			if ( is_admin() ) {
				if ( 'yes' == get_option( 'codup-role-baseddiscount_type_product' ) ) {
					$custom_added_role = get_custom_added_roles( '' );
					foreach ( $custom_added_role as $key => $display_name ) {
						$b2b_e_user_role = str_replace( ' ', '_', strtolower( $display_name['role'] ) );
						$variation_roles = maybe_unserialize( $variation_data['role_based_discounts'][0] );
						?>
						<p class="form-field custom-field" style="<?php echo ( 0 == ( $key % 2 ) ) ? 'float:left' : 'float:right'; ?>">
							<label for="custom-role"><b><?php echo wp_kses_post( $display_name['role'] ); ?></b></label>
							<input 
								type="text"
								class="short role-based-dicount" 
								name="user_role_discount[<?php echo wp_kses_post( $b2b_e_user_role ); ?>][<?php echo wp_kses_post( $variation->ID ); ?>]" 
								id="user_role_discount[<?php echo wp_kses_post( $b2b_e_user_role ); ?>][<?php echo wp_kses_post( $variation->ID ); ?>]" 
								placeholder="<?php esc_html_e( 'Enter Discount (%)', 'codup-wcrfq' ); ?>"
								style = "padding: 5px;"
								value=<?php echo ( ! empty( $variation_roles[ $b2b_e_user_role ][ $variation->ID ] ) ) ? wp_kses_post( $variation_roles[ $b2b_e_user_role ][ $variation->ID ] ) : ''; ?> 
							> 
						</p>
						<?php
					}
				}
			}
		}

		/**
		 * Save disconut field for variable product.
		 *
		 * @param array $variation_id variation array of product.
		 * @param int   $i position.
		 */
		public function save_discount_base_variations_field( $variation_id, $i ) {

			if ( ! empty( $_POST['_wpnonce'] ) ) {
				wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ) );
			}

			if ( isset( $_POST['user_role_discount'] ) ) {

				$custom_field = filter_input( INPUT_POST, 'user_role_discount', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
				update_post_meta( $variation_id, 'role_based_discounts', $custom_field );

			}

		}

		/**
		 * Discount based variation field data.
		 *
		 * @param array $variations variations of the product.
		 */
		public function discount_base_variations_field_data( $variations ) {

			$custom_added_role = get_custom_added_roles( '' );
			if ( is_array( $all_custom_user_role ) && count( $all_custom_user_role ) > 0 ) {
				foreach ( $custom_added_role as $key => $display_name ) {
					$b2b_e_user_role = str_replace( ' ', '_', strtolower( $display_name['role'] ) );

					$variation_values                                     = get_post_meta( $variations['variation_id'], 'role_based_discounts', true );
					$role_based_discount                                  = ( ! empty( $variation_values ) && isset( $variation_values[ $b2b_e_user_role ] ) ) ? $variation_values[ $b2b_e_user_role ] : '';
					$variations['user_role_discount'][ $b2b_e_user_role ] = isset( $role_based_discount[ $variations['variation_id'] ] ) ? $role_based_discount[ $variations['variation_id'] ] : '';
				}
			}
			return $variations;
		}

		/**
		 * Add custom user role.
		 */
		public function add_custom_user_role() {

			$user_roles = get_option( 'codup_ecommerce_role_based_settings' );

			foreach ( $user_roles as $display_name ) {

				if ( null != $display_name['role'] ) {

					$b2b_e_user_role = str_replace( ' ', '_', strtolower( $display_name['role'] ) );

					if ( ! wp_roles()->is_role( $b2b_e_user_role ) ) {

						add_role( $b2b_e_user_role, $display_name['role'], array( 'read' => true ) );

					}
				}
			}

		}

	}

}
new Codup_Role_Based_Discounts();
