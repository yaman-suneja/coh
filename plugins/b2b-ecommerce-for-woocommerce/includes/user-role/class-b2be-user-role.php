<?php
/**
 * WC RFQ.
 *
 * @package b2b-ecommerce-for-woocomerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'B2BE_User_Role' ) ) {
	/**
	 * Class B2BE_User_Role.
	 */
	class B2BE_User_Role {

		/**
		 * Construct.
		 */
		public function __construct() {

			add_filter( 'wc_avatax_is_available', array( $this, 'exempt_avatax' ) );

			add_action( 'init', array( $this, 'exemption_init' ), 10 );
			add_filter( 'woocommerce_package_rates', array( $this, 'b2be_exempt_shipping_methods' ), 10, 2 );

		}

		/**
		 * Function to render exemption hooks.
		 */
		public function exemption_init() {

			add_filter( 'woocommerce_product_get_tax_class', array( $this, 'b2be_exempt_tax_class' ), 1, 2 );
			add_filter( 'woocommerce_product_variation_get_tax_class', array( $this, 'b2be_exempt_tax_class' ), 1, 2 );
			add_filter( 'woocommerce_product_needs_shipping', array( $this, 'disable_shipping_in_cart_page' ), 10, 2 );

			add_filter( 'woocommerce_matched_tax_rates', array( $this, 'b2be_exempt_shipping_tax' ), 1, 6 );
		}

		public function b2be_exempt_shipping_tax( $matched_tax_rates, $country, $state, $postcode, $city, $tax_class ) {

			$user  = wp_get_current_user();
			$roles = (array) $user->roles;
			if ( ! empty( $roles[0] ) && 0 != b2be_custom_role_exists( $roles[0] ) ) {
				$post_id = b2be_custom_role_exists( $roles[0] );

				$exempted_tax_classes = get_post_meta( $post_id, 'tax_exempt', true );

				if ( empty( $tax_class ) ) {
					$tax_class = 'standard';
				}

				$shipping_tax_rule = get_option( 'woocommerce_shipping_tax_class' );

				if ( empty( $shipping_tax_rule ) ) {
					$shipping_tax_rule = 'standard';
				}

				if ( 'inherit' != $shipping_tax_rule ) {
					foreach ( $matched_tax_rates as $rate_id => $rate ) {
						if ( ! empty( $exempted_tax_classes ) && in_array( $tax_class, array_keys( $exempted_tax_classes ) ) ) {
							$matched_tax_rates[ $rate_id ]['shipping'] = 'no';
						}
					}
				}
			}

			return $matched_tax_rates;

		}

		/**
		 * Function to exempt Avalara Tax for user role.
		 */
		public function exempt_avatax() {
			if ( is_user_logged_in() ) {

				$user  = wp_get_current_user();
				$roles = (array) $user->roles;
				if ( ! empty( $roles[0] ) && 0 != b2be_custom_role_exists( $roles[0] ) ) {
					$post_id            = b2be_custom_role_exists( $roles[0] );
					$avalara_tax_exempt = get_post_meta( $post_id, 'avalara_tax_exempt', true );
					if ( 'on' == $avalara_tax_exempt ) {
						return 0;
					}
				}

				return 1;
			}
		}

		/**
		 * Function to exempt shipping for user role.
		 *
		 * @param bool   $needs_shipping Need shipping or not?.
		 * @param object $product Product Object.
		 */
		public function disable_shipping_in_cart_page( $needs_shipping, $product ) {

			if ( ! is_user_logged_in() ) {
				return $needs_shipping;
			}

			$user  = wp_get_current_user();
			$roles = (array) $user->roles;
			if ( ! empty( $roles[0] ) && 0 != b2be_custom_role_exists( $roles[0] ) ) {
				$post_id            = b2be_custom_role_exists( $roles[0] );
				$is_exempt_shipping = get_post_meta( $post_id, 'shipping_exempt', true );
				if ( 'on' == $is_exempt_shipping ) {
					$needs_shipping = false;
				}
			}
			return $needs_shipping;
		}

		/**
		 * Function to exempt tax for user role.
		 *
		 * @param array  $tax_class Tax Classes.
		 * @param object $product Product object.
		 */
		public function b2be_exempt_tax_class( $tax_class, $product ) {

			if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
				return $tax_class;
			}

			if ( ! is_user_logged_in() ) {
				return $tax_class;
			}

			$user  = wp_get_current_user();
			$roles = (array) $user->roles;
			if ( ! empty( $roles[0] ) && 0 != b2be_custom_role_exists( $roles[0] ) ) {
				$post_id = b2be_custom_role_exists( $roles[0] );

				$exempted_tax_classes = get_post_meta( $post_id, 'tax_exempt', true );

				if ( ! empty( $exempted_tax_classes ) && in_array( $tax_class, array_keys( $exempted_tax_classes ) ) ) {
					return 'none';
				} elseif ( ! empty( $exempted_tax_classes ) && in_array( 'standard', array_keys( $exempted_tax_classes ) ) && empty( $tax_class ) ) {
					return 'none';
				} else {
					return $tax_class;
				}
			}
		}

		/**
		 * Function to exempt shipping methods.
		 *
		 * @param array $rates Shipping method rates object array.
		 * @param array $package Array of packages.
		 */
		public function b2be_exempt_shipping_methods( $rates, $package ) {

			if ( ! is_user_logged_in() ) {
				return $rates;
			}

			if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
				return $rates;
			}

			$user_id = get_current_user_id();
			if ( ! $user_id ) {
				return $rates;
			}

			$role = get_current_user_role_by_id( $user_id );
			if ( 0 == count( $role ) ) {
				return $rates;
			}

			$role_id           = b2be_custom_role_exists( $role[0] );
			$methods_to_exempt = get_post_meta( $role_id, 'shipping_exempt', true );

			if ( ! empty( $methods_to_exempt ) && is_array( $methods_to_exempt ) ) {
				foreach ( $rates as $key => $rate ) {
					$method_id = $rate->get_method_id();
					if ( in_array( $method_id, array_keys( $methods_to_exempt ) ) ) {
						unset( $rates[ $key ] );
					}
				}
			}
			return $rates;
		}

	}
}
new B2BE_User_Role();
