<?php
/**
 * Functon File
 *
 * @since 2.5.0
 * @package Woocomerce
 */

if ( ! class_exists( 'WC_Dependencies' ) ) {
	require_once 'class-wc-dependencies.php';
}

/**
 * Check if WooCommerce is activated
 */
if ( ! function_exists( 'b2be_is_woocommerce_activated' ) ) {
	/**
	 *  Is Woocomerce Activated
	 *
	 * @return bool
	 */
	function b2be_is_woocommerce_activated() {
		if ( WC_Dependencies::woocommerce_active_check() ) {
			return true;
		} else {
			add_action( 'admin_notices', 'b2be_woocommerce_inactive_notice' );
			return false;
		}
	}
}

/**
 * Get The Current user role name.
 *
 * @param int $role_id Role Id of current user.
 */
function b2be_get_formated_userrole_name( $role_id = '' ) {

	if ( is_user_logged_in() ) {
		global $wp_roles;
		$all_roles_names = $wp_roles->get_names();

		if ( empty( $role_id ) ) {
			$user_obj = get_user_by( 'id', get_current_user_id() );
			$role_id  = array_values( $user_obj->roles )[0];
		}

		return $all_roles_names[ $role_id ];
	}
	return false;

}

if ( ! function_exists( 'b2be_is_required_login' ) ) {

	/**
	 * Check For Required Login Condition.
	 *
	 * @param object $product current product.
	 */
	function b2be_is_required_login( $product ) {

		$product_id = ! empty( $product->get_parent_id() ) ? $product->get_parent_id() : $product->get_id();

		$required_for_product  = ! empty( get_option( 'codup_hide_by_product' ) ) ? get_option( 'codup_hide_by_product' ) : array( '' );
		$required_for_category = ! empty( get_option( 'codup_hide_by_category' ) ) ? get_option( 'codup_hide_by_category' ) : array( '' );
		$required_for_all      = ! empty( get_option( 'codup_hide_for_all' ) ) ? get_option( 'codup_hide_for_all' ) : array( '' );

		$product_categories = get_the_terms( $product_id, 'product_cat' );

		if ( in_array( $product_id, $required_for_product ) ) {

			return true;

		}

		foreach ( $product_categories as $key => $category ) {

			if ( in_array( ( $category->term_taxonomy_id ), $required_for_category ) ) {

				return true;

			}
		}

		if ( 'yes' == $required_for_all ) {

			return true;

		}

		return false;

	}
}

/**
 * Function to return RFQ cart page url.
 */
function b2be_get_rfq_cart_url() {

	$rfq_cart_url = ! empty( get_option( 'b2be_rfq_cart_page' ) ) ? get_page_link( get_option( 'b2be_rfq_cart_page' ) ) : site_url() . '/rfq';

	/*
	@name: rfq_cart_url
	@desc: Modify rfq cart url.
	@param: (string) $rfq_cart_url Default RFQ Cart Url.
	@package: b2b-ecommerce-for-woocommerce
	@module: request for quote
	@type: filter
	*/
	return apply_filters( 'rfq_cart_url', $rfq_cart_url );

}

/**
 * Get Discounted Price According To Rules Defined In Discount Tab Settings.
 *
 * @param int    $price Current price of product.
 * @param object $product Product on which discount will apply.
 * @param bool   $only_sale_price Check if only sale price is to be returned.
 */
function b2be_get_discounted_price( $price, $product, $only_sale_price = false ) {
	$discounts     = get_global_bulk_discounts( $product );
	$discount_type = '';

	if ( ! isset( $discounts['mode'] ) || ! isset( $discounts['discount'] ) ) {
		return $price;
	}

	if ( 'd' == $discounts['mode'] ) {
		$discount      = ! empty( $discounts['discount'] ) ? $discounts['discount'] : 0;
		$discount_type = $discounts['type'];

		if ( $product->is_type( 'simple' ) ) {

			$product_price = ( ! empty( $product->get_regular_price() ) ) ? $product->get_regular_price() : $product->get_price();
			$product_price = apply_filters( 'b2be_product_regular_price', $product_price, $product, true );

			if ( 'fixed' == $discount_type ) {
				$newprice = $product_price - $discount;
			} else {
				$newprice = $product_price - ( $product_price * ( $discount / 100 ) );
			}

			if ( number_format( $newprice, 2 ) < 0 ) {
				$newprice = 0.00;
			}

			if ( ! $only_sale_price ) {
				$price = '<del>' . wc_price( $product_price ) . '</del> ' . wc_price( $newprice );
			} else {
				$price = round( $newprice, 2 );
			}
		} elseif ( $product->is_type( 'variable' ) ) {

			$product            = new WC_Product_Variable( $product->get_id() );
			$product_variations = $product->get_available_variations();

			if ( $product_variations ) {
				foreach ( $product_variations as $variation ) {
					$product_price = ! empty( $variation['display_regular_price'] ) ? $variation['display_regular_price'] : '';
					if ( 'fixed' == $discount_type ) {
						if ( $discounts['variation_ids'] ) {
							if ( in_array( $variation['variation_id'], $discounts['variation_ids'] ) ) {
								$temp = $product_price - $discount;
								if ( $temp <= 0 ) {
									$temp = 0.00;
								}
								$newprice[] = $temp;
							} else {
								$newprice[] = $product_price;
							}
						}
					} elseif ( $discounts['variation_ids'] ) {
						if ( in_array( $variation['variation_id'], $discounts['variation_ids'] ) ) {
							$temp = $product_price - ( $product_price * ( $discount / 100 ) );
							if ( $temp <= 0 ) {
								$temp = 0.00;
							}
							$newprice[] = $temp;
						} else {
							$newprice[] = $product_price;
						}
					}
				}
			}

			$min_discount = ! empty( $newprice ) ? min( $newprice ) : '';
			$max_discount = ! empty( $newprice ) ? max( $newprice ) : '';
			if ( $min_discount == $max_discount ) {

				$variable_price = min( get_variation__regular_price( $product ) );

				if ( ! $only_sale_price ) {
					$price = '<del>' . wc_price( $variable_price ) . '</del> <span>' . wc_price( $max_discount ) . '</span>';
				} else {
					$price = $max_discount;
				}
			} else {

				if ( ! $only_sale_price ) {
					$price = '<span>' . wc_price( $min_discount ) . '</span> - <span>' . wc_price( $max_discount ) . '</span>';
				} else {
					$price = $max_discount;
				}
			}
		}
	}
	return $price;
}

/**
 *  Function to return user credit balance.
 *
 * @param int $user_id user_id.
 */
function b2be_user_credit_payments_balance( $user_id ) {

	$credit_balance = get_user_meta( $user_id, 'credit_payment_bal', true );

	if ( empty( $credit_balance ) ) {
		$credit_balance = 0;
	}

	return $credit_balance;
}

/**
 *  Function to return enable or disable status for payment gateway.
 *
 * @param int $user_id user_id.
 */
function b2be_user_credit_payments_enable( $user_id ) {

	$_user = get_user_by( 'id', $user_id );

	$default = array(
		'post_type'      => 'codup-custom-roles',
		'posts_per_page' => -1,
	);

	$custom_roles_cpt = get_posts( $default );

	foreach ( $_user->roles as $post_name => $post_title ) {

		foreach ( $custom_roles_cpt as $key => $value ) {
			if ( $value->post_name == $post_title ) {
				$enabled = get_post_meta( $value->ID, 'enable_b2b_credit_payment', true );
				if ( 'on' == $enabled ) {
					return true;
				}
			}
		}
	}
	return false;
}

/**
 *  Function to return total assign credit for role
 *
 * @param int $role_id role_id.
 */
function b2be_get_total_assign_credit_in_role( $role_id ) {

	global $wpdb;

	$placeholders[0] = $wpdb->prefix . 'cwrf_credit_payment_logs';
	$placeholders[1] = $role_id;

	$credits = $wpdb->get_results( $wpdb->prepare( 'SELECT amount FROM %1s WHERE post_id=%d', $placeholders ) );

	$credit = 0;

	if ( $credits ) {

		foreach ( $credits as $key => $value ) {
			$credit += $value->amount;
		}
	}

	return $credit;
}

/**
 *  Function to return user credit logs
 *
 * @param int $user_id user_id.
 */
function b2be_users_credit_payments_logs( $user_id ) {

	global $wpdb;

	$_user = get_user_by( 'id', $user_id );

	$default = array(
		'post_type'      => 'codup-custom-roles',
		'posts_per_page' => -1,
	);

	$custom_roles_cpt = get_posts( $default );
	$logs             = array();
	foreach ( $_user->roles as $post_name => $post_title ) {

		foreach ( $custom_roles_cpt as $key => $value ) {
			if ( $value->post_name == $post_title ) {

				$placeholders[0] = $wpdb->prefix . 'cwrf_credit_payment_logs';
				$placeholders[1] = $value->ID;
				$placeholders[2] = $user_id;

				$logs += $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM %1s WHERE post_id=%d OR user_id =%d ORDER BY created DESC', $placeholders ) );
			}
		}
	}

	return $logs;
}

/**
 *  Function to check ,validate credit
 *
 * @param int $user_id user_id.
 * @param int $amount amount.
 */
function b2be_credit_validation( $user_id, $amount ) {

	$credit_balance = get_user_meta( $user_id, 'credit_payment_bal', true );

	if ( $credit_balance ) {
		if ( $amount > $credit_balance ) {
			return 'Your Available Credit ' . $credit_balance . ' is not enough to proceed!';
		}
	} else {
		return __( 'You are not allowed to use this payment method!', 'b2b-ecommerce' );
	}

}

/**
 *  Function to check ,validate and log credit payment
 *
 * @param int    $order_id Order Id.
 * @param int    $user_id User Id.
 * @param int    $amount Amount.
 * @param string $event String to be printed.
 */
function b2be_maintain_credit_payments( $order_id, $user_id, $amount, $event = 'Order Placed' ) {

	global $wpdb;
	$wpdb->insert(
		"{$wpdb->prefix}cwrf_credit_payment_logs",
		array(
			'post_id' => $order_id,
			'user_id' => $user_id,
			'event'   => $event,
			'amount'  => $amount,
			'created' => wp_date( 'Y-m-d H:i:s' ),
		)
	);

	$credit_balance = get_user_meta( $user_id, 'credit_payment_bal', true, 0 );

	if ( 'Credit Deducted' == $event ) {

		$credit_balance = $credit_balance - $amount;
		update_user_meta( $user_id, 'credit_payment_bal', $credit_balance );
	} elseif ( 'Credit Reverted' == $event ) {
		$credit_balance = $credit_balance + $amount;
		update_user_meta( $user_id, 'credit_payment_bal', $credit_balance );
	}
}


/**
 * Admin notice if WooCommerce is inactive.
 */
function b2be_woocommerce_inactive_notice() {
	if ( current_user_can( 'activate_plugins' ) ) : ?>	
		<div id="message" class="error">
			<p>
			<?php
				$install_url = wp_nonce_url(
					add_query_arg(
						array(
							'action' => 'install-plugin',
							'plugin' => 'woocommerce',
						),
						admin_url( 'update.php' )
					),
					'install-plugin_woocommerce'
				);
				/* translators: %s: is activated */
				printf( esc_html__( '%1$sB2B Ecommerce For Woocommerce is inactive.%2$s The %3$sWooCommerce plugin%4$s must be active for B2B Ecommerce For Woocommerce to work. Please %5$sinstall & activate WooCommerce &raquo;%6$s', 'b2b-ecommerce' ), '<strong>', '</strong>', '<a href="http://wordpress.org/extend/plugins/woocommerce/">', '</a>', '<a href="' . esc_url( $install_url ) . '">', '</a>' );
			?>
			</p>
		</div>
		<?php
	endif;
}

/**
 * Return payment methods.
 *
 * @param string $format Format to get desired payments method.
 */
function b2be_get_formatted_payment_methods( $format = 'b2be_ecommerce' ) {
	$gateways            = array();
	$b2b_custom_gateways = get_option( 'b2be_payment_method' ) ? get_option( 'b2be_payment_method' ) : array();
	foreach ( WC()->payment_gateways->payment_gateways as $_available_gateways ) {

		$b2b_custom_gateways_id = explode( ',', strtolower( str_replace( ' ', '_', implode( ',', array_values( $b2b_custom_gateways ) ) ) ) );

		if ( 'woocommerce' === $format ) {
			if ( in_array( $_available_gateways->id, $b2b_custom_gateways_id ) ) {
				continue;
			}
		} elseif ( 'b2be_ecommerce' === $format ) {
			if ( ! in_array( $_available_gateways->id, $b2b_custom_gateways_id ) ) {
				continue;
			}
		}

		if ( $_available_gateways->is_available() ) {
			if ( ! is_add_payment_method_page() ) {
				$gateways[ $_available_gateways->id ] = $_available_gateways;
			} elseif ( $_available_gateways->supports( 'add_payment_method' ) || $_available_gateways->supports( 'tokenization' ) ) {
				$gateways[ $_available_gateways->id ] = $_available_gateways;
			}
		}
	}
	return $gateways;

}

/**
 * Return payment methods.
 *
 * @param int $user_id User Id For Which Payments Methods Are To Be Returned.
 */
function b2be_get_payment_methods_by_user_id( $user_id ) {

	if ( ! $user_id ) {
		return false;
	}

	$customer = get_user_by( 'ID', $user_id );
	$roles    = (array) $customer->roles;

	$wc_gateways = b2be_get_formatted_payment_methods( 'woocommerce' );

	if ( ! $wc_gateways ) {
		return;
	}

	if ( ! empty( $roles[0] ) && 0 != b2be_custom_role_exists( $roles[0] ) ) {
		$post_id = b2be_custom_role_exists( $roles[0] );
	}

	$gateways = array();
	foreach ( $wc_gateways as $id => $_available_gateways ) {
		if ( 'yes' === get_user_meta( $user_id, $id, true ) ) {
			$gateways[ $id ] = $_available_gateways;
		} elseif ( 'yes' === get_post_meta( $post_id, $id, true ) ) {
			$gateways[ $id ] = $_available_gateways;
		}
	}

	$b2be_gateways     = b2be_get_formatted_payment_methods( 'b2be_ecommerce' );
	$b2be_gateways_ids = array_keys( $b2be_gateways );

	// For B2B payment methods...
	if ( ! empty( get_user_meta( $user_id, 'b2be_user_based_payment_method', true ) ) && in_array( get_user_meta( $user_id, 'b2be_user_based_payment_method', true ), $b2be_gateways_ids ) ) {
		$payment                  = $b2be_gateways[ get_user_meta( $user_id, 'b2be_user_based_payment_method', true ) ];
		$gateways[ $payment->id ] = $payment;
	} elseif ( ! empty( get_post_meta( $post_id, 'b2be_role_based_payment_method', true ) ) && in_array( get_post_meta( $post_id, 'b2be_role_based_payment_method', true ), $b2be_gateways_ids ) ) {
		$payment                  = $b2be_gateways[ get_post_meta( $post_id, 'b2be_role_based_payment_method', true ) ];
		$gateways[ $payment->id ] = $payment;
	}
	return $gateways ? $gateways : array();
}

/**
 * Return payment methods.
 *
 * @param int $role_name Role Name For Which Payments Methods Are To Be Returned.
 */
function b2be_get_payment_methods_by_role( $role_name ) {

	if ( ! empty( $role_name ) && 0 != b2be_custom_role_exists( $role_name ) ) {
		$post_id = b2be_custom_role_exists( $role_name );
	} else {
		return false; // Return if custom role post not found.
	}

	$gateways    = array();
	$wc_gateways = b2be_get_formatted_payment_methods( 'woocommerce' ); // Get WooCommerce Payment Gateways.
	if ( ! empty( $wc_gateways ) ) {
		foreach ( $wc_gateways as $id => $_available_gateways ) {
			if ( 'yes' === get_post_meta( $post_id, $id, true ) ) {
				$gateways[ $id ] = $_available_gateways;
			}
		}
	}

	$b2be_gateways     = b2be_get_formatted_payment_methods( 'b2be_ecommerce' ); // Get B2B Ecommerce Payment Gateways.
	$b2be_gateways_ids = array_keys( $b2be_gateways );

	// For B2B payment methods...
	if ( ! empty( get_post_meta( $post_id, 'b2be_role_based_payment_method', true ) ) && in_array( get_post_meta( $post_id, 'b2be_role_based_payment_method', true ), $b2be_gateways_ids ) ) {
		$payment                  = $b2be_gateways[ get_post_meta( $post_id, 'b2be_role_based_payment_method', true ) ];
		$gateways[ $payment->id ] = $payment;
	}
	return $gateways ? $gateways : array();
}

/**
 * Get all the custom b2be payment methods.
 */
function get_b2be_custom_payment_methods() {

	$b2be_payment_methods = array();
	$b2be_custom_methods  = get_option( 'codup_ecommerce_payment_method_settings' );
	if ( $b2be_custom_methods ) {
		foreach ( $b2be_custom_methods as $key => $b2be_custom_method ) {
			$payment_method_slug                          = str_replace( ' ', '_', strtolower( $b2be_custom_method ) );
			$b2be_payment_methods[ $payment_method_slug ] = $b2be_custom_method;
		}
	}
	return $b2be_payment_methods ? $b2be_payment_methods : false;

}

/**
 * Check if WooCommerce is activated
 */
if ( ! function_exists( 'b2be_register_default_settings_for_rfq' ) ) {

	/**
	 *  Register Default Settings For Rfq.
	 */
	function b2be_register_default_settings_for_rfq() {

		$add_to_rfq_btn_txt = get_option( 'codup-rfq_add_to_rfq_button_text' );
		$accept_btn_txt     = get_option( 'codup-rfq_accept_rfq_button_text' );
		$revision_btn_txt   = get_option( 'codup-rfq_revison_rfq_button_text' );
		$view_btn_txt       = get_option( 'codup-rfq_view_rfq_button_text' );
		$reject_btn_txt     = get_option( 'codup-rfq_reject_rfq_button_text' );

		update_b2be_signup_form_entries(); // Sign up form entries compatibility...

		if ( empty( $add_to_rfq_btn_txt ) ) {

			update_option( 'codup-rfq_add_to_rfq_button_text', 'Add To RFQ' );

		}
		if ( empty( $accept_btn_txt ) ) {

			update_option( 'codup-rfq_accept_rfq_button_text', 'Accept' );

		}
		if ( empty( $revision_btn_txt ) ) {

			update_option( 'codup-rfq_revison_rfq_button_text', 'Need Revision' );

		}
		if ( empty( $view_btn_txt ) ) {

			update_option( 'codup-rfq_view_rfq_button_text', 'View Quote' );

		}
		if ( empty( $reject_btn_txt ) ) {

			update_option( 'codup-rfq_reject_rfq_button_text', 'Reject' );

		}

		if ( empty( get_option( 'codup-rfq_enable_rfq' ) ) ) {
			update_option( 'codup-rfq_enable_rfq', 'yes' );
		}
		if ( empty( get_option( 'codup-rfq_disable_add_to_cart' ) ) ) {
			update_option( 'codup-rfq_disable_add_to_cart', 'no' );
		}

		$args        = array(
			'posts_per_page' => -1,
			'post_type'      => 'product',
			'fields'         => 'ids',
		);
		$posts_array = get_posts( $args );
		foreach ( $posts_array as $post_array ) {

			if ( empty( get_post_meta( $post_array, 'enable_rfq', true ) ) ) {
				update_post_meta( $post_array, 'enable_rfq', 'yes' );
			}

			if ( empty( get_post_meta( $post_array, 'disable_add_to_cart', true ) ) ) {
				update_post_meta( $post_array, 'disable_add_to_cart', 'no' );
			}
		}

		$cat_args     = array(
			'posts_per_page' => -1,
			'taxonomy'       => 'product_cat',
			'fields'         => 'ids',
			'hide_empty'     => false,
		);
		$category_ids = get_terms( $cat_args );

		foreach ( $category_ids as $cat_id ) {
			if ( empty( get_term_meta( $cat_id, 'taxonomy_setting' ) ) ) {
				delete_term_meta( $cat_id, 'taxonomy_setting' );
			}
		}

		$codup_rfq               = array();
		$codup_rfq['enable_rfq'] = 1;
		foreach ( $category_ids as $cat_id ) {
			if ( empty( get_term_meta( $cat_id, 'taxonomy_setting' ) ) ) {
				update_term_meta( $cat_id, 'taxonomy_setting', $codup_rfq );
			}
		}

		foreach ( wp_roles()->role_names as $post_name => $post_title ) {
			if ( 0 == b2be_custom_role_exists( $post_name ) ) {
				$post_arr = array(
					'post_title'     => $post_title,
					'post_name'      => $post_name,
					'post_status'    => 'publish',
					'comment_status' => 'closed',
					'post_type'      => 'codup-custom-roles',
				);
				$post_id  = wp_insert_post( $post_arr );

				if ( empty( get_post_meta( $post_id, 'enable_rfq', true ) ) ) {
					update_post_meta( $post_id, 'enable_rfq', 'yes' );
				}
				if ( empty( get_post_meta( $post_id, 'disable_add_to_cart', true ) ) ) {
					update_post_meta( $post_id, 'disable_add_to_cart', 'no' );
				}

				b2be_update_default_wc_gateways_for_roles( $post_id ); // Enabling the WC Gateways on roles by default.

			} else {

				b2be_update_default_wc_gateways_for_roles( b2be_custom_role_exists( $post_name ) ); // Enabling the WC Gateways on roles by default.
				update_post_meta( b2be_custom_role_exists( $post_name ), 'shipping_exempt', array() );
			}
		}

		b2be_update_default_wc_gateways_for_users(); // Enabling the WC Gateways on users by default.
	}
}

if ( ! function_exists( 'b2be_update_default_wc_gateways_for_users' ) ) {
	/**
	 * Assigning the default wc gateways for all store users.
	 */
	function b2be_update_default_wc_gateways_for_users() {
		$gateways = b2be_get_formatted_payment_methods( 'woocommerce' );
		$users    = get_users();
		if ( $users ) {
			foreach ( $users as $user ) {
				$user_id = $user->ID;
				if ( $user_id ) {
					if ( ! empty( $gateways ) ) {
						foreach ( $gateways as $id => $payment_method_name ) {
							if ( empty( get_user_meta( $user_id, $id, true ) ) ) {
								update_user_meta( $user_id, $id, 'yes' );
							}
						}
					}
				}
			}
		}
	}
}

if ( ! function_exists( 'b2be_update_default_wc_gateways_for_roles' ) ) {
	/**
	 * Assigning the default wc gateways for all store roles.
	 *
	 * @param int $post_id Current Role Id.
	 */
	function b2be_update_default_wc_gateways_for_roles( $post_id ) {
		$gateways = b2be_get_formatted_payment_methods( 'woocommerce' );

		if ( ! empty( $gateways ) ) {
			foreach ( $gateways as $id => $payment_method_name ) {
				if ( empty( get_post_meta( $post_id, $id, true ) ) ) {
					update_post_meta( $post_id, $id, 'yes' );
				}
			}
		}
	}
}

if ( ! function_exists( 'b2be_signup_form_backward_compatibility' ) ) {

	/**
	 * Function to make backward compatibility.
	 * Mapping old fields data to new fields format.
	 */
	function b2be_signup_form_backward_compatibility() {

		$old_signup_fields = get_option( 'codup_ecommerce_signup_field' );
		$new_signup_fields = ! empty( get_option( 'b2be_signup_field' ) ) ? get_option( 'b2be_signup_field' ) : array();
		$temp_array        = array();

		if ( ! empty( $old_signup_fields ) ) {
			foreach ( $old_signup_fields as $key => $old_field ) {

				$temp_array[ $key ]['name']       = $old_field['field_title'];
				$temp_array[ $key ]['type']       = map_old_fields_type( $old_field['field_id'] );
				$temp_array[ $key ]['visibility'] = isset( $old_field['is_visible'] ) ? 'true' : 'false';
				$temp_array[ $key ]['required']   = isset( $old_field['is_required'] ) ? 'true' : 'false';
				$temp_array[ $key ]['size']       = 'large';
				$temp_array[ $key ]['classes']    = '';
				$temp_array[ $key ]['roles']      = ( 'role' == $old_field['field_id'] ) ? array_keys( get_custom_added_roles() ) : '';

			}
			$new_signup_fields = array_merge( $temp_array, $new_signup_fields );
			delete_option( 'codup_ecommerce_signup_field' ); // deleteing old fields...
			update_option( 'b2be_signup_field', $new_signup_fields );
		}

		return $new_signup_fields;
	}
}

if ( ! function_exists( 'update_b2be_signup_form_entries' ) ) {

	/**
	 * Function to make backward compatibility for sign up form entries.
	 */
	function update_b2be_signup_form_entries() {

		$b2be_user_ids = get_users(
			array(
				'meta_key' => 'sfg_user_signup_information',
				'fields'   => 'ID',
			)
		);

		if ( $b2be_user_ids ) {
			foreach ( $b2be_user_ids as $b2be_user_id ) {
				$new_entries = array();
				$old_entries = get_user_meta( $b2be_user_id, 'sfg_user_signup_information', true );
				$i           = 0;
				if ( $old_entries ) {
					foreach ( $old_entries as $field_id => $field ) {
						$new_entries[ $i ]['name']       = isset( $field['field_title'] ) ? $field['field_title'] : '';
						$new_entries[ $i ]['type']       = map_old_fields_type( $field_id );
						$new_entries[ $i ]['visibility'] = 'true';
						$new_entries[ $i ]['required']   = isset( $field['field_required'] ) ? 'true' : 'false';
						$new_entries[ $i ]['size']       = 'large';
						$new_entries[ $i ]['classes']    = '';
						$new_entries[ $i ]['value']      = isset( $field['field_value'] ) ? $field['field_value'] : '';

						$i++;
					}
					update_user_meta( $b2be_user_id, 'b2be_sign_up_entries', $new_entries );
				}
			}
		}

	}
}

if ( ! function_exists( 'map_old_fields_type' ) ) {
	/**
	 * Mapping the old version sign up form fields to new one.
	 *
	 * @param string $field_type Field type of signup form.
	 */
	function map_old_fields_type( $field_type ) {

		$new_type = '';

		switch ( $field_type ) {
			case 'user_name':
				$new_type = 'username';
				break;
			case 'first_name':
				$new_type = 'fname';
				break;
			case 'last_name':
				$new_type = 'lname';
				break;
			case 'email':
				$new_type = 'email';
				break;
			case 'date_of_birth':
				$new_type = 'date';
				break;
			case 'role':
				$new_type = 'role';
				break;
			case 'company':
				$new_type = 'company';
				break;
			case 'address':
				$new_type = 'address_1';
				break;
			case 'phone':
				$new_type = 'phone';
				break;
			case 'number':
				$new_type = 'number';
				break;
			default:
				$new_type = 'text';
				break;
		}
		return $new_type;
	}
}

if ( ! function_exists( 'last_array_key' ) ) {

	/**
	 * Function to return last key of array.
	 *
	 * @param array $array Can be any array.
	 */
	function last_array_key( $array ) {
		if ( ! is_array( $array ) || empty( $array ) ) {
			return null;
		}

		return array_keys( $array )[ count( $array ) - 1 ];
	}
}

/**
 * Function to Get quote count by Status.
 *
 * @param string $status Status of current quote.
 */
function b2be_get_quote_count_by_status( $status ) {

	$args   = array(
		'post_type'   => 'quote',
		'post_status' => $status,
		'numberposts' => -1,
	);
	$quotes = get_posts( $args );

	$count = 0;

	if ( $quotes ) {
		$count = count( $quotes );
	}

	return $count;

}

/**
 * Function to add requested quote count on Quote menu.
 */
function b2be_get_requested_quote_count() {

	$args   = array(
		'post_type'   => 'quote',
		'post_status' => 'requested',
		'numberposts' => -1,
	);
	$quotes = get_posts( $args );

	$count = 0;
	foreach ( $quotes as $key => $quote ) {
		if ( 'requested' == $quote->post_status ) {
			$count++;
		}
	}

	return $count;
}

/**
 * Create log table for credit payments.
 */
function b2be_create_tables() {
	global $wpdb;
	$table_name      = $wpdb->prefix . 'cwrf_credit_payment_logs';
	$charset_collate = $wpdb->get_charset_collate();

	// Create logs.
	$sql = "CREATE TABLE IF NOT EXISTS $table_name (
	id int(11) NOT NULL AUTO_INCREMENT,
	user_id int(11) NOT NULL,
	post_id int(11) DEFAULT NULL,
	event varchar(50) DEFAULT NULL,
	amount varchar(50) DEFAULT NULL,
	`created` datetime NOT NULL,
	PRIMARY KEY  (id)
	) $charset_collate;";

	require_once ABSPATH . '/wp-admin/includes/upgrade.php';
	dbDelta( $sql );
}

if ( ! function_exists( 'b2be_get_field_option' ) ) {

	/**
	 * Getting the options for WC fields dropdowns.
	 *
	 * @param array $field Field's detail.
	 * @param array $options Options Data for dropdown to be passed.
	 */
	function b2be_get_field_option( $field, $options = array() ) {

		switch ( $field ) {
			case 'country':
				$wc_countries = new WC_Countries();
				$field_option = $wc_countries->get_countries();
				break;
			case 'state':
				$wc_countries = new WC_Countries();
				$field_option = $wc_countries->get_states();
				break;
			default:
				$field_option = $options;
				break;
		}
		return apply_filters( 'b2be_signup_form_get_field_options', $field_option, $field );
	}
}
if ( ! function_exists( 'b2be_get_field_size' ) ) {

	/**
	 * Get signup form fields size.
	 *
	 * @param string $size Size of the field ( large|medium|small ).
	 */
	function b2be_get_field_size( $size ) {
		switch ( $size ) {
			case 'medium':
				$style = 'width:50%;float:left;height: 76px;';
				break;
			case 'large':
				$style = 'width:100%;clear:both;';
				break;
			default:
				$style = 'width:33.33%;float:left;height: 76px;';
				break;
		}
		return $style;
	}
}
if ( ! function_exists( 'b2be_get_signup_form_fields_details' ) ) {

	/**
	 * Get sign up form fields details.
	 *
	 * @param array $codup_wc_sfgs Sign up form fields.
	 */
	function b2be_get_signup_form_fields_details( $codup_wc_sfgs ) {

		$signup_form_fields = get_option( 'b2be_signup_field' );
		foreach ( $signup_form_fields as $key => $value ) {
			if ( 'false' == $value['visibility'] ) {
				unset( $signup_form_fields[ $key ] );
			}
		}
		$signup_form_fields = array_values( $signup_form_fields );
		foreach ( $signup_form_fields as $key => $signup_form_field ) {
			if ( 'text' == $signup_form_field['type'] || 'number' == $signup_form_field['type'] ) {
				$signup_form_fields[ $key ]['value'] = $codup_wc_sfgs[ str_replace( ' ', '_', $signup_form_field['name'] ) . '_' . $signup_form_field['type'] ];
			} else {
				$signup_form_fields[ $key ]['value'] = $codup_wc_sfgs[ $signup_form_field['type'] ];
			}
		}

		return $signup_form_fields;
	}
}
if ( ! function_exists( 'get_signup_entries_formatted_values' ) ) {

	/**
	 * Get the sign up entries formmatted values.
	 *
	 * @param string $value Value of field.
	 * @param string $type Type of field.
	 * @param object $user Current User Data.
	 */
	function get_signup_entries_formatted_values( $value, $type, $user ) {

		switch ( $type ) {
			case 'state':
				$country = $user->billing_country;
				if ( $country ) {
					$value = WC()->countries->get_states( $country )[ $value ];
				}
				break;

			case 'country':
				$value = WC()->countries->countries[ $value ];
				break;

			case 'role':
				$value = b2be_get_formated_userrole_name( $value );
				break;

			default:
				// do nothing...
				break;
		}

		return $value;
	}
}
if ( ! function_exists( 'get_b2be_signup_user_status' ) ) {
	/**
	 * Function to get signup user status.
	 *
	 * @param int $user_id Current User Id.
	 */
	function get_b2be_signup_user_status( $user_id ) {

		if ( $user_id ) {
			if ( get_user_meta( $user_id, 'sign_up_request', true ) == 'sign_up_approval' ) {
				$sfg_status = __( 'Approved', 'b2b-ecommerce' );
			} elseif ( get_user_meta( $user_id, 'sign_up_request', true ) == 'sign_up_rejection' ) {
				$sfg_status = __( 'Rejected', 'b2b-ecommerce' );
			} elseif ( get_user_meta( $user_id, 'sign_up_request', true ) == 'sign_up_on_hold' ) {
				$sfg_status = __( 'On Hold', 'b2b-ecommerce' );
			} else {
				$sfg_status = __( 'Pending', 'b2b-ecommerce' );
			}
			return $sfg_status;
		}
		return false;
	}
}
