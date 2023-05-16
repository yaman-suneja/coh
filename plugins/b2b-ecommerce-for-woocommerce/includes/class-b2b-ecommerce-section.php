<?php
/**
 * Class B2B_Ecommerce_Section file.
 *
 * @package b2b-ecommerce-for-woocomerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'B2B_Ecommerce_Section' ) ) {

	/**
	 *  Class B2B_Ecommerce_Section.
	 */
	class B2B_Ecommerce_Section {


		/**
		 * Settings Tab
		 *
		 * @var static $settings_tab Settings Tab.
		 */
		public static $settings_tab = 'codup-b2b-ecommerce';

		/**
		 *  Constructor.
		 */
		public static function init() {

			add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::add_b2b_ecommerce_settings_tab', 50 );
			add_action( 'woocommerce_settings_' . self::$settings_tab, __CLASS__ . '::b2b_ecommerce_settings' );
			add_action( 'woocommerce_sections_' . self::$settings_tab, __CLASS__ . '::add_sections_to_custom_tab' );
			add_action( 'woocommerce_settings_save_' . self::$settings_tab, __CLASS__ . '::save' );

		}

		/**
		 * Woocommerce setting tab for RFQ.
		 *
		 * @param array $settings_tabs Settings Tab slug.
		 * @return array
		 */
		public static function add_b2b_ecommerce_settings_tab( $settings_tabs ) {
			$settings_tabs[ self::$settings_tab ] = __( 'B2B Ecommerce', 'b2b-ecommerce' );
			return $settings_tabs;
		}

		/**
		 * Display woocommerce RFQ settings.
		 */
		public static function b2b_ecommerce_settings() {

			global $current_section;
			$settings = array();
			if ( '' == $current_section ) {

				$settings = B2BE_RFQ_Settings::get_settings();

			} elseif ( 'codup-signup-generator' == $current_section ) {

				B2BE_Sign_Up_Form_Settings::get_settings();

			} elseif ( 'b2be-catalogue-visibility' == $current_section ) {

				$settings = B2BE_Catalogue_Visibility_Settings::get_settings();

			} elseif ( 'codup-payment-method' == $current_section ) {

				$settings = B2BE_Payment_Method_Settings::get_settings();

			} elseif ( 'codup-bulk-discounts' == $current_section ) {

				B2BE_Bulk_Discounts_Settings::get_settings();

			} elseif ( 'codup-moq' == $current_section ) {

				B2BE_MOQ_Settings::get_settings();

			} elseif ( 'codup-mov' == $current_section ) {

				B2BE_MOV_Settings::get_settings();

			} elseif ( 'codup-reorder' == $current_section ) {

				$settings = B2BE_Re_Order_Settings::get_settings();

			} else {
				/*
				@name: b2be_get_settings_codup-b2b-ecommerce
				@desc: Modify the current settings.
				@param: (array) $settings General Settings Array.
				@param: (array) $current_section B2B Ecommerce Current Sections Array.
				@package: b2b-ecommerce-for-woocommerce
				@module: request for quote
				@type: filter
				*/
				$settings = apply_filters( 'b2be_get_settings_' . self::$settings_tab, $settings, $current_section );
			}
			WC_Admin_Settings::output_fields( $settings );
		}



		/**
		 *  Function Includes.
		 */
		public static function add_sections_to_custom_tab() {

			global $current_section;

			$sections = array(
				''                          => __( 'RFQ', 'b2b-ecommerce' ),
				'codup-signup-generator'    => __( 'Signup Form Generator', 'b2b-ecommerce' ),
				'b2be-catalogue-visibility' => __( 'Product & Price Visibility', 'b2b-ecommerce' ),
				'codup-payment-method'      => __( 'Payment Method', 'b2b-ecommerce' ),
				'codup-bulk-discounts'      => __( 'Discount Option', 'b2b-ecommerce' ),
				'codup-moq'                 => __( 'MOQ', 'b2b-ecommerce' ),
				'codup-mov'                 => __( 'MOV', 'b2b-ecommerce' ),
				'codup-reorder'             => __( 'Re-Order', 'b2b-ecommerce' ),
			);

			/*
			@name: b2be_get_sections_codup-b2b-ecommerce
			@desc: Modify the current sections.
			@param: (array) $sections B2B Ecommerce Current Sections Array.
			@package: b2b-ecommerce-for-woocommerce
			@module: request for quote
			@type: filter
			*/
			$sections = apply_filters( 'b2be_get_sections_' . self::$settings_tab, $sections );
			echo '<ul class="subsubsub">';

			$array_keys = array_keys( $sections );

			foreach ( $sections as $id => $label ) {
				echo '<li><a href="' . wp_kses_post( admin_url( 'admin.php?page=wc-settings&tab=' . self::$settings_tab . '&section=' . sanitize_title( $id ) ) ) . '" class="' . ( $current_section == $id ? 'current' : '' ) . '">' . wp_kses_post( $label ) . '</a> ' . ( end( $array_keys ) == $id ? '' : '|' ) . ' </li>';
			}

			echo '</ul><br class="clear" />';

		}

		/**
		 *  Process save
		 */
		public static function save() {

			global $current_section;

			if ( '' == $current_section ) {

				if ( ! empty( $_POST['_wpnonce'] ) ) {
					wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ) );
				}

				$args        = array(
					'posts_per_page' => -1,
					'post_type'      => 'product',
					'fields'         => 'ids',
				);
				$posts_array = get_posts( $args );
				foreach ( $posts_array as $post_array ) {
					if ( isset( $_POST['codup-rfq_enable_rfq'] ) ) {
						update_post_meta( $post_array, 'enable_rfq', 'yes' );
					} else {
						update_post_meta( $post_array, 'enable_rfq', 'no' );
					}
				}
				foreach ( $posts_array as $post_array ) {
					if ( isset( $_POST['codup-rfq_disable_add_to_cart'] ) ) {
						update_post_meta( $post_array, 'disable_add_to_cart', 'yes' );
					} else {
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

				$codup_rfq = array();
				if ( isset( $_POST['codup-rfq_enable_rfq'] ) ) {
					$codup_rfq['enable_rfq'] = sanitize_text_field( wp_unslash( $_POST['codup-rfq_enable_rfq'] ) );
				}
				if ( isset( $_POST['codup-rfq_disable_add_to_cart'] ) ) {
					$codup_rfq['disable_add_to_cart'] = sanitize_text_field( wp_unslash( $_POST['codup-rfq_disable_add_to_cart'] ) );
				}
				if ( empty( $codup_rfq ) ) {
					foreach ( $category_ids as $cat_id ) {
						delete_term_meta( $cat_id, 'taxonomy_setting' );
					}
				}

				foreach ( $category_ids as $cat_id ) {
					update_term_meta( $cat_id, 'taxonomy_setting', $codup_rfq );
				}

				$args         = array(
					'posts_per_page' => -1,
					'post_type'      => 'codup-custom-roles',
					'fields'         => 'ids',
				);
				$custom_roles = get_posts( $args );

				foreach ( $custom_roles as $custom_role ) {
					if ( isset( $_POST['codup-rfq_enable_rfq'] ) ) {
						update_post_meta( $custom_role, 'enable_rfq', 'yes' );
					} else {
						update_post_meta( $custom_role, 'enable_rfq', 'no' );
					}
				}
				foreach ( $custom_roles as $custom_role ) {
					if ( isset( $_POST['codup-rfq_disable_add_to_cart'] ) ) {
						update_post_meta( $custom_role, 'disable_add_to_cart', 'yes' );
					} else {
						update_post_meta( $custom_role, 'disable_add_to_cart', 'no' );
					}
				}

				$settings = B2BE_RFQ_Settings::get_settings();

			} elseif ( 'b2be-catalogue-visibility' == $current_section ) {

				B2BE_Catalogue_Visibility_Settings::save_visibility_settings();
				return;

			} elseif ( 'codup-payment-method' == $current_section ) {

				$settings       = B2BE_Payment_Method_Settings::get_settings();
				$payment_method = isset( $_POST['codup_ecommerce_payment_method_settings'] ) ? filter_input( INPUT_POST, 'codup_ecommerce_payment_method_settings', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY ) : '';
				update_option( 'b2be_payment_method', $payment_method );

				$gateways    = b2be_get_formatted_payment_methods( 'woocommerce' );
				$users       = get_users(); // This role users...
				$roles_names = wp_roles()->role_names;

				if ( ! empty( $gateways ) ) {
					foreach ( $gateways as $id => $payment_method_name ) {

						// Enable all payment methods on each role...
						if ( $roles_names ) {
							foreach ( $roles_names as $post_name => $post_title ) {
								$post_id = b2be_custom_role_exists( $post_name );
								if ( isset( $_POST['codup-rfq_enable_has_terms'] ) ) {
									update_post_meta( $post_id, $id, 'yes' );
									if ( isset( $_POST['codup_ecommerce_payment_method_settings'] ) ) {
										$payment_method_settings = filter_input( INPUT_POST, 'codup_ecommerce_payment_method_settings', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
										$value                   = strtolower( str_replace( ' ', '_', array_values( $payment_method_settings )[0] ) );
										update_post_meta( $post_id, 'b2be_role_based_payment_method', $value );
									}
								}
							}
						}

						// Enable all payment methods on each user...
						if ( $users ) {
							foreach ( $users as $key => $user ) {
								$user_id = $user->ID;
								if ( isset( $_POST['codup-rfq_enable_has_terms'] ) ) {
									update_user_meta( $user_id, $id, 'yes' );
									if ( isset( $_POST['codup_ecommerce_payment_method_settings'] ) ) {
										$payment_method_settings = filter_input( INPUT_POST, 'codup_ecommerce_payment_method_settings', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
										$value                   = strtolower( str_replace( ' ', '_', array_values( $payment_method_settings )[0] ) );
										update_user_meta( $user_id, 'b2be_user_based_payment_method', $value );
									}
								}
							}
						}
					}
				}
			} elseif ( 'codup-reorder' == $current_section ) {

				$settings = B2BE_Re_Order_Settings::get_settings();

			} else {
				$settings = apply_filters( 'b2be_get_settings_' . self::$settings_tab, $settings, $current_section );
			}

			WC_Admin_Settings::save_fields( $settings );

			do_action( 'woocommerce_update_options_' . self::$settings_tab . '_' . $current_section );

		}


	}
}
B2B_Ecommerce_Section::init();
