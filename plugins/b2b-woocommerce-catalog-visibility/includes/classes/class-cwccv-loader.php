<?php
/**
 * File class-cwccv-loader.php
 *
 * @package catalog-visibility-for-woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'CWCCV_Loader' ) ) {
	/**
	 * Class CWCCV_Loader
	 * All the inclusion of files are defines here
	 */
	class CWCCV_Loader {
		/**
		 * Contructor
		 */
		public function __construct() {
			$this->includes();
			add_action( 'admin_enqueue_scripts', array( $this, 'register_backend_scripts' ) );
			add_action( 'plugins_loaded', array( $this, 'cwccv_load_plugin_textdomain' ) );
		}

		/**
		 * All php files include here
		 */
		public function includes() {

			if ( ! is_admin() ) {
				// inluding all frontend classes here.
				require_once CWCCV_ABSPATH . '/includes/classes/class-cwccv-catalog-visibility.php';

			} elseif ( wp_doing_ajax() ) {
				// including all ajax classes here.
				require_once CWCCV_ABSPATH . '/includes/classes/ajax/class-cwccv-admin-settings.php';
			} else {
				// inluding all admin classes here.
				require_once CWCCV_ABSPATH . '/includes/classes/admin/class-cwccv-settings-tab.php';
				require_once CWCCV_ABSPATH . '/includes/classes/admin/class-cwccv-user-settings.php';
			}
		}

		/**
		 * All css and js files for admin panel are included here.
		 *
		 * @param int $page_id restricted scripts only on catalog.
		 */
		public function register_backend_scripts( $page_id ) {

			if ( isset( $_GET['page'] ) && isset( $_GET['tab'] ) && 'wc-settings' == $_GET['page'] && 'catalog_visibility' == $_GET['tab'] ) {

				wp_enqueue_style( 'CWCCV_bootstrap-select2-css', CWCCV_PLUGIN_ASSETS_URL . '/css/select2.min.css', null, rand() );
				wp_enqueue_script( 'CWCCV_bootstrap-select2-js', CWCCV_PLUGIN_ASSETS_URL . '/js/select2.min.js', array( 'jquery' ), true );

				wp_enqueue_script( 'CWCCV_main-js', CWCCV_PLUGIN_ASSETS_URL . '/js/cwccv-main.js', array( 'jquery' ), true );
				wp_localize_script(
					'CWCCV_main-js',
					'main_ajax_var',
					array(
						'ajaxurl'           => admin_url( 'admin-ajax.php' ),
						'hide_show_alert'   => __( 'Please Select to either Hide Or Show.', 'codup-woocommerce-catalog-visibility' ),
						'cat_product_alert' => __( 'Please either select Category or Product to Hide/Show.', 'codup-woocommerce-catalog-visibility' ),
						'price_alert'       => __( 'Please Enter To Price Value.', 'codup-woocommerce-catalog-visibility' ),
					)
				);

				wp_enqueue_style( 'CWCCV_main-css', CWCCV_PLUGIN_ASSETS_URL . '/css/cwccv-main.css', null, rand() );

			} elseif ( 'user-edit.php' == $page_id || 'profile.php' == $page_id ) {
				wp_enqueue_script( 'CWCCV_user-js', CWCCV_PLUGIN_ASSETS_URL . '/js/cwccv-user.js', array( 'jquery' ), true );
			}

		}

		/**
		 * Languages loaded.
		 */
		public function cwccv_load_plugin_textdomain() {
			load_plugin_textdomain( 'codup-woocommerce-catalog-visibility', false, basename( CWCCV_ABSPATH ) . '/languages/' );
		}

	}
}

new CWCCV_Loader();
