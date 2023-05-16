<?php
/**
 * WC RFQ.
 *
 * @package b2b-ecommerce-for-woocomerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'B2BE_Bulk_Discounts_Loader' ) ) {
	/**
	 * Class B2BE_Bulk_Discounts.
	 */
	class B2BE_Bulk_Discounts_Loader {
		/**
		 * Cart Variable.
		 */
		public function __construct() {

			$this->includes();
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_bulk_discount_backend_scripts' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_bulk_discount_frontend_scripts' ) );

		}

		/**
		 *  Function Includes.
		 */
		public function includes() {

			include_once 'class-b2be-bulk-discounts-settings.php';
			include_once 'class-b2be-bulk-discount.php';
			include_once 'b2be-bulk-discounts-function.php';

		}

		/**
		 * Enqueue backend script.
		 *
		 * @param int $page_id Current Page Id.
		 */
		public function enqueue_bulk_discount_backend_scripts( $page_id ) {
			if ( 'woocommerce_page_wc-settings' == $page_id ) {
				if ( isset( $_GET['section'] ) && 'codup-bulk-discounts' == $_GET['section'] ) {

					wp_enqueue_script( 'bulk_discounts_settings_script', CWRFQ_ASSETS_DIR_URL . '/js/bulk-discounts/admin/bulk-discounts-settings.js', array( 'jquery' ), rand() );
					wp_enqueue_style( 'bulk_discounts_settings_style', CWRFQ_ASSETS_DIR_URL . '/css/bulk-discount/bulk-discounts-settings.css', '', rand() );
					wp_localize_script(
						'bulk_discounts_settings_script',
						'bulkAjax',
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
		public function enqueue_bulk_discount_frontend_scripts() {

			global $post;

			$post_id         = ! empty( $post ) ? $post->ID : '';
			$product         = wc_get_product( $post_id );
			$b2be_bulk_rules = array();

			if ( ! empty( $product ) && $product->is_type( 'variable' ) ) {
				$b2be_bulk_rules       = get_global_bulk_discounts( $product );
				$b2be_variations_price = b2be_variations_price( $product );
			}
			wp_enqueue_script( 'bulk_discounts_front_script', CWRFQ_ASSETS_DIR_URL . '/js/bulk-discounts/bulk-discounts.js', array( 'jquery' ), true );
			wp_localize_script(
				'bulk_discounts_front_script',
				'bulk_rule',
				array(
					'ajaxurl'               => admin_url( 'admin-ajax.php' ),
					'b2be_bulk_rules'       => $b2be_bulk_rules,
					'b2be_variations_price' => ! empty( $b2be_variations_price ) ? $b2be_variations_price : array(),
					'price_symbol'          => get_woocommerce_currency_symbol(),
				)
			);
		}

	}

}
new B2BE_Bulk_Discounts_Loader();
