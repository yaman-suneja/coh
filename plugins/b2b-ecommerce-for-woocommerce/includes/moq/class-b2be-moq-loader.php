<?php
/**
 * WC RFQ.
 *
 * @package b2b-ecommerce-for-woocomerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'B2BE_MOQ_Loader' ) ) {
	/**
	 * Class B2BE_MOQ.
	 */
	class B2BE_MOQ_Loader {
		/**
		 * Cart Variable.
		 */
		public function __construct() {

			$this->includes();
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_moq_backend_scripts' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_moq_frontend_scripts' ) );

		}

		/**
		 *  Function Includes.
		 */
		public function includes() {

			if ( is_admin() ) {
				include_once 'class-b2be-moq-settings.php';
			}

			include_once 'class-b2be-moq.php';
			include_once 'b2be-moq-function.php';

		}

		/**
		 * Enqueue backend script.
		 *
		 * @param int $page_id Current Page Id.
		 */
		public function enqueue_moq_backend_scripts( $page_id ) {
			if ( 'woocommerce_page_wc-settings' == $page_id ) {

				if ( isset( $_GET['section'] ) && 'codup-moq' == $_GET['section'] ) {

					wp_enqueue_script( 'moq_settings_script', CWRFQ_ASSETS_DIR_URL . '/js/moq/admin/moq-settings.js', array( 'jquery' ), true );
					wp_enqueue_style( 'moq_settings_style', CWRFQ_ASSETS_DIR_URL . '/css/moq/moq-settings.css', '', rand() );
					wp_localize_script(
						'moq_settings_script',
						'moqAjax',
						array(
							'ajax_url' => admin_url( 'admin-ajax.php' ),
						)
					);

				}
			}
		}

		/**
		 * Enqueue backend script.
		 */
		public function enqueue_moq_frontend_scripts() {

			global $post;

			$post_id = ! empty( $post ) ? $post->ID : '';
			$product = wc_get_product( $post_id );

			if ( ! empty( $product ) ) {
				$b2be_moq_rules = b2be_get_moq_limit( $product );
			}

			wp_enqueue_script( 'moq_frontend_script', CWRFQ_ASSETS_DIR_URL . '/js/moq/moq.js', '', array( 'jquery' ), true );
			wp_localize_script(
				'moq_frontend_script',
				'moq_limit',
				array(
					'ajaxurl'        => admin_url( 'admin-ajax.php' ),
					'b2be_moq_rules' => ! empty( $b2be_moq_rules ) ? $b2be_moq_rules : array(),
				)
			);

		}

	}

}
new B2BE_MOQ_Loader();
