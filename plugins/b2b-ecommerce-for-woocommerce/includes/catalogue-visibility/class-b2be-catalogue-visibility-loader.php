<?php
/**
 * File class-b2be_catalogue-loader.php
 *
 * @package catalog-visibility-for-woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'B2BE_Catalogue_Visibility_Loader' ) ) {
	/**
	 * Class B2BE_Catalogue_Visibility_Loader
	 * All the inclusion of files are defines here
	 */
	class B2BE_Catalogue_Visibility_Loader {

		/**
		 * Contructor
		 */
		public function __construct() {

			// Getting option for hide catalog price.
			$this->hide_price_for_non_login_users_option = get_option( B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_price_for_non_login' );
			$this->hide_whole_catalog_price_option       = get_option( B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_whole_catalog_price' );
			$this->hide_catalog_price_for_categories     = get_option( B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_catalog_for_categories' );
			$this->hide_catalog_price_for_products       = get_option( B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_catalog_for_products' );

			$this->includes();
			add_action( 'admin_enqueue_scripts', array( $this, 'register_backend_scripts' ) );

			// Compatible with Woocommerce Product Search Plugin.
			add_action( 'woocommerce_product_search_service_post_ids_for_request', array( $this, 'default_search_override' ), 10, 2 );
			add_filter( 'woocommerce_product_search_field_product_price_html', array( $this, 'change_price_of_search_results' ), 99, 2 );
			add_filter( 'woocommerce_product_search_field_product_add_to_cart_html', array( $this, 'change_add_to_cart_of_search_results' ), 99, 2 );
		}

		/**
		 * All php files include here
		 */
		public function includes() {

			if ( is_admin() ) {
				require_once 'class-b2be-catalogue-visibility-helper.php';
				require_once 'class-b2be-catalogue-visibility-settings.php';
			} else {
				require_once 'class-b2be-catalogue-visibility.php';
			}
		}

		/**
		 * All css and js files for admin panel are included here.
		 *
		 * @param int $page_id restricted scripts only on catalog.
		 */
		public function register_backend_scripts( $page_id ) {

			if ( isset( $_GET['page'] ) && isset( $_GET['section'] ) && 'wc-settings' == $_GET['page'] && 'b2be-catalogue-visibility' == $_GET['section'] ) {

				wp_enqueue_style( 'b2be-catalogue-visibility-select2', CWRFQ_ASSETS_DIR_URL . '/css/select2.min.css', null, rand() );
				wp_enqueue_script( 'b2be-catalogue-visibility-select2', CWRFQ_ASSETS_DIR_URL . '/js/select2.min.js', array( 'jquery' ), true );

				wp_enqueue_script( 'b2be-catalogue-visibility-settings', CWRFQ_ASSETS_DIR_URL . '/js/catalogue-visibility/admin/catalogue-visibility-settings.js', array( 'jquery' ), true );
				wp_localize_script(
					'b2be_catalogue_main-js',
					'main_ajax_var',
					array(
						'ajaxurl'           => admin_url( 'admin-ajax.php' ),
						'hide_show_alert'   => __( 'Please Select to either Hide Or Show.', 'codup-woocommerce-catalog-visibility' ),
						'cat_product_alert' => __( 'Please either select Category or Product to Hide/Show.', 'codup-woocommerce-catalog-visibility' ),
						'price_alert'       => __( 'Please Enter To Price Value.', 'codup-woocommerce-catalog-visibility' ),
					)
				);

				wp_enqueue_style( 'b2be-catalogue-visibility-settings', CWRFQ_ASSETS_DIR_URL . '/css/catalogue-visibility/admin/catalogue-visibility-settings.css', null, rand() );

			} elseif ( 'user-edit.php' == $page_id || 'profile.php' == $page_id ) {
				wp_enqueue_script( 'b2be-catalogue-visibility-user-settings', CWRFQ_ASSETS_DIR_URL . '/js/catalogue-visibility/admin/catalogue-visibility-user-settings.js', array( 'jquery' ), true );
			}
		}

		public function default_search_override( &$include, $cache_context ) {

			if ( is_user_logged_in() ) {
				$hide_products = get_user_meta( get_current_user_id(), 'cvf_hidden_products', false );
				$hide_products = $hide_products[0];
			} else {
				$hide_products = get_option( 'cvf_hidden_products_non_logged_in' );
			}

			if ( ! empty( $hide_products ) ) {

				foreach ( $include as $key => $value ) {

					if ( in_array( $value, $hide_products ) ) {
						unset( $include[ $key ] );
					}
				}
			}

			return $include;
		}

		public function change_price_of_search_results( $price_html, $post_id ) {

			$product = wc_get_product( $post_id );
			$_price  = b2be_get_discounted_price( $product->get_price(), $product, true );

			if ( ! is_user_logged_in() ) {

				$b2be_catalogue_enabled = false;

				// Applying rules if user is not logged in.
				if ( function_exists( 'is_required_login' ) ) {
					$b2be_catalogue_enabled = is_required_login( $product );
				}

				if ( 'yes' == $this->hide_price_for_non_login_users_option && ! $b2be_catalogue_enabled ) {
					if ( 'yes' === $this->hide_whole_catalog_price_option ) {
						return '';
					} elseif ( ! empty( $this->hide_catalog_price_for_categories ) && ( has_term( B2BE_Catalogue_Visibility::get_category_slug( $this->hide_catalog_price_for_categories ), 'product_cat', $product->get_id() ) ) || ( ! empty( $this->hide_catalog_price_for_products ) && in_array( $product->get_id(), $this->hide_catalog_price_for_products ) ) ) {
						return '';
					}
				}
			}

			return wc_price( $_price );
		}

		public function change_add_to_cart_of_search_results( $add_to_cart_html, $post_id ) {

			if ( ! is_user_logged_in() ) {
				$product = wc_get_product( $post_id );

				$b2be_catalogue_enabled = false;

				// Applying rules if user is not logged in.
				if ( function_exists( 'is_required_login' ) ) {
					$b2be_catalogue_enabled = is_required_login( $product );
				}

				if ( 'yes' == $this->hide_price_for_non_login_users_option && ! $b2be_catalogue_enabled ) {
					if ( 'yes' === $this->hide_whole_catalog_price_option ) {
						return '<a class="button" href="' . wp_kses_post( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) ) . '?returnPage=' . wp_kses_post( base64_encode( get_the_permalink( $post_id ) ) ) . '">' . wp_kses_post( __( 'Sign In To View', 'codup-woocommerce-catalog-visibility' ) ) . '</a>';
					} elseif ( ! empty( $this->hide_catalog_price_for_categories ) && ( has_term( B2BE_Catalogue_Visibility::get_category_slug( $this->hide_catalog_price_for_categories ), 'product_cat', $product->get_id() ) ) || ( ! empty( $this->hide_catalog_price_for_products ) && in_array( $product->get_id(), $this->hide_catalog_price_for_products ) ) ) {
						return '<a class="button" href="' . wp_kses_post( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) ) . '?returnPage=' . wp_kses_post( base64_encode( get_the_permalink( $post_id ) ) ) . '">' . wp_kses_post( __( 'Sign In To View', 'codup-woocommerce-catalog-visibility' ) ) . '</a>';
					}
				}
			}

			return $add_to_cart_html;

		}
	}
}

new B2BE_Catalogue_Visibility_Loader();
