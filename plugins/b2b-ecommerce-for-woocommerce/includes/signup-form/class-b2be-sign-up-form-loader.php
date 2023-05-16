<?php
/**
 * WC RFQ.
 *
 * @package b2b-ecommerce-for-woocomerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'B2BE_Sign_Up_Form_Loader' ) ) {
	/**
	 * Class Codup_Role_Based_Discounts.
	 */
	class B2BE_Sign_Up_Form_Loader {
		/**
		 * Cart Variable.
		 */
		public function __construct() {

			$this->includes();
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_sign_up_form_backend_scripts' ) );
		}

		/**
		 *  Function Includes.
		 */
		public function includes() {

			include_once 'class-b2be-sign-up-form-settings.php';
			include_once 'class-b2be-sign-up-form.php';

		}

		/**
		 * Enqueue backend script.
		 *
		 * @param int $page_id page id.
		 */
		public function enqueue_sign_up_form_backend_scripts( $page_id ) {
			if ( 'profile.php' === $page_id || 'user-edit.php' === $page_id || 'woocommerce_page_signup-form-entries' === $page_id || ( 'woocommerce_page_wc-settings' === $page_id && isset( $_GET['section'] ) && 'codup-signup-generator' === $_GET['section'] ) ) {

				wp_enqueue_style( 'b2be-signup-dataTables.min-css', CWRFQ_ASSETS_DIR_URL . 'css/jquery.dataTables.min.css', '', true );
				wp_enqueue_script( 'b2be-signup-dataTables.min-js', CWRFQ_ASSETS_DIR_URL . 'js/jquery.dataTables.min.js', array( 'jquery' ), true );

				wp_enqueue_script( 'sign-up-form', CWRFQ_ASSETS_DIR_URL . '/js/signup-form/admin/sign-up-form.js', array( 'jquery' ), rand() );
				wp_localize_script(
					'sign-up-form',
					'sign_up_settings',
					array(
						'ajaxurl' => admin_url( 'admin-ajax.php' ),
					)
				);
				wp_enqueue_style( 'sign_up_css', CWRFQ_ASSETS_DIR_URL . 'css/sign-up-form/signup-form-settings.css', '', rand() );
			}
		}

	}

}
new B2BE_Sign_Up_Form_Loader();
