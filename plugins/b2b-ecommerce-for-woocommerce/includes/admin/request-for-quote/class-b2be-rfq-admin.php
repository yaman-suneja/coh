<?php
/**
 * Rfq Admin.
 *
 * @package b2b-ecommerce-for-woocomerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * WC_Admin class.
 */
class B2BE_RFQ_Admin {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'includes' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		add_filter( 'woocommerce_order_item_display_meta_key', array( $this, 'update_meta_key_to_display' ), 11, 2 );
		add_filter( 'woocommerce_order_item_display_meta_value', array( $this, 'update_meta_value_to_display' ), 11, 2 );
	}

	/**
	 * Include any classes we need within admin.
	 */
	public function includes() {
			include_once dirname( __FILE__ ) . '/class-rfq-admin-post-types.php';
	}
	/**
	 * Admin Scripts.
	 */
	public function admin_scripts() {
		global  $post;
		$screen    = get_current_screen();
		$screen_id = $screen ? $screen->id : '';
		if ( 'quote' == $screen_id ) {
			wp_enqueue_script( 'cwcrfq-admin-quote-meta-boxes', CWRFQ_PLUGIN_DIR_URL . 'assets/js/request-for-quote/admin/meta-boxes-quote.js', array( 'jquery' ), true );
			wp_localize_script(
				'cwcrfq-admin-quote-meta-boxes',
				'codup_rfq_admin_meta_boxes_quote',
				array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'post_id'  => isset( $post->ID ) ? $post->ID : '',
				)
			);
			wp_enqueue_style( 'cwcrfq-admin-quote-meta-boxes', CWRFQ_PLUGIN_DIR_URL . 'assets/css/request-for-quote/admin/meta-boxes-quote.css', '', true );
			wp_enqueue_style( 'cwcrfq-admin-comment-meta-boxes', CWRFQ_PLUGIN_DIR_URL . 'assets/css/request-for-quote/admin-comment-metabox.css', '', true );
		}
	}
	/**
	 * Update Meta Key Display.
	 *
	 * @param string $display_key display key.
	 * @param object $meta meta object.
	 */
	public function update_meta_key_to_display( $display_key, $meta ) {

		if ( 'quote' == $display_key ) {
			$display_key = __( 'Associated Quote ID ', 'codup-wcrfq' );
		}
		return $display_key;
	}
	/**
	 * Update Meta Value Display.
	 *
	 * @param string $display_value Display key.
	 * @param object $meta Meta object.
	 */
	public function update_meta_value_to_display( $display_value, $meta ) {

		if ( 'quote' == $meta->get_data()['key'] ) {
			$display_value = sprintf( '<a href="%s">' . $meta->value . '</a>', admin_url( 'post.php?post=' . $display_value . '&action=edit' ) );
		}
		return $display_value;
	}

}

return new B2BE_RFQ_Admin();
