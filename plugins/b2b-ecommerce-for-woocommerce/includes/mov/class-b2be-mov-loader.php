<?php
/**
 * WC RFQ.
 *
 * @package b2b-ecommerce-for-woocomerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'B2BE_MOV_Loader' ) ) {
	/**
	 * Class B2BE_MOv.
	 */
	class B2BE_MOV_Loader {
		/**
		 * Cart Variable.
		 */
		public function __construct() {

			$this->includes();
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_mov_backend_scripts' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_mov_frontend_scripts' ) );

		}

		/**
		 *  Function Includes.
		 */
		public function includes() {

			include_once 'class-b2be-mov-settings.php';
			include_once 'class-b2be-mov.php';
			include_once 'b2be-mov-function.php';

		}

		/**
		 * Enqueue backend script.
		 *
		 * @param int $page_id Current Page Id.
		 */
		public function enqueue_mov_backend_scripts( $page_id ) {
			if ( 'woocommerce_page_wc-settings' == $page_id ) {

				if ( isset( $_GET['section'] ) && 'codup-mov' == $_GET['section'] ) {

					wp_enqueue_script( 'mov_settings_script', CWRFQ_ASSETS_DIR_URL . '/js/mov/admin/mov-settings.js', array( 'jquery' ), true );
					wp_enqueue_style( 'mov_settings_style', CWRFQ_ASSETS_DIR_URL . '/css/mov/mov-settings.css', '', rand() );
					wp_localize_script(
						'mov_settings_script',
						'movAjax',
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
		public function enqueue_mov_frontend_scripts() {

			global $post;

			$post_id = ! empty( $post ) ? $post->ID : '';
			$product = wc_get_product( $post_id );

			if ( ! empty( $product ) ) {
				$b2be_mov_rules = b2be_get_mov_limit( $product );
			}

			wp_enqueue_script( 'mov_frontend_script', CWRFQ_ASSETS_DIR_URL . '/js/mov/mov.js', '', array( 'jquery' ), true );
			wp_localize_script(
				'mov_frontend_script',
				'mov_limit',
				array(
					'ajaxurl'        => admin_url( 'admin-ajax.php' ),
					'b2be_mov_rules' => ! empty( $b2be_mov_rules ) ? $b2be_mov_rules : array(),
				)
			);

		}

	}

}
new B2BE_MOV_Loader();
