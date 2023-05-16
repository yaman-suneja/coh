<?php
/**
 * Quote Items Meta Box.
 *
 * @package b2b-ecommerce-for-woocomerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * RFQ_Meta_Box_Quote_Items Class.
 */
class RFQ_Meta_Box_Quote_Items {
	/**
	 * Construct.
	 */
	public function __construct() {

		add_action( 'wp_ajax_save_item_meta_box_values', array( $this, 'save_item_meta_box_values' ) );
		add_action( 'wp_ajax_nopriv_save_item_meta_box_values', array( $this, 'save_item_meta_box_values' ) );
		add_action( 'wp_ajax_delete_line_item', array( $this, 'delete_line_item' ) );
		add_action( 'wp_ajax_nopriv_delete_line_item', array( $this, 'delete_line_item' ) );

	}

	/**
	 * Output the metabox.
	 *
	 * @param WP_Post $post Post Object.
	 */
	public static function output( $post ) {
		global $post, $thepostid;

		if ( ! is_int( $thepostid ) ) {
				$thepostid = $post->ID;
		}

		$quote = wc_get_quote( $thepostid );
		$data  = get_post_meta( $post->ID );

		include 'views/html-quote-items.php';
	}

	/**
	 * Save meta box data.
	 */
	public function save_item_meta_box_values() {

		if ( isset( $_POST['rfq_nonce'] ) ) {
			$nonce = sanitize_text_field( wp_unslash( $_POST['rfq_nonce'] ) );
			if ( ! wp_verify_nonce( $nonce, 'rfq_metabox_item_settings' ) ) {
				return;
			}
		}

		$post_ids = ( isset( $_POST['rfq_admin_qoute_id'] ) ) ? sanitize_text_field( wp_unslash( $_POST['rfq_admin_qoute_id'] ) ) : '';
		$products = ( isset( $_POST['products_detail'] ) ) ? filter_input( INPUT_POST, 'products_detail', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY ) : '';

		$abc       = get_post_meta( $post_ids, 'items', true );
		$final_arr = array();
		foreach ( $abc as $product ) {
			$final_arr[] = $product;
		}
		$i = 0;

		foreach ( $products as $product ) {
			if ( key( $product ) == $final_arr[ $i ]['item_product_id'] || key( $product ) == $final_arr[ $i ]['variation_id'] ) {
				$key                         = key( $product );
				$actual_total                = $product[ $key ]['actual_price'] * $product[ $key ]['qty'];
				$final_arr[ $i ]['qty']      = $product[ $key ]['qty'];
				$final_arr[ $i ]['total']    = $product[ $key ]['price'];
				$final_arr[ $i ]['subtotal'] = $actual_total;
			}
			$i++;
		}

		$i = 0;
		foreach ( $final_arr as $key => $final_arrays ) {
			$key_values = $this->generate_random_string( 32 );

			$final_arr[ $key_values ] = $final_arr[ $key ];
			unset( $final_arr[ $key ] );
			$i++;
		}
		update_post_meta( $post_ids, 'items', $final_arr );
	}
	/**
	 * Generate Random String
	 */
	public function delete_line_item() {

		if ( isset( $_POST['rfq_nonce'] ) ) {
			$nonce = sanitize_text_field( wp_unslash( $_POST['rfq_nonce'] ) );
			if ( ! wp_verify_nonce( $nonce, 'rfq_metabox_item_settings' ) ) {
				return;
			}
		}
		$post_ids       = ( isset( $_POST['rfq_admin_qoute_id'] ) ) ? sanitize_text_field( wp_unslash( $_POST['rfq_admin_qoute_id'] ) ) : '';
		$product_id     = ( isset( $_POST['product_id'] ) ) ? sanitize_text_field( wp_unslash( $_POST['product_id'] ) ) : '';
		$products_array = get_post_meta( $post_ids, 'items', true );

		foreach ( $products_array as $key => $product_array ) {
			if ( $product_array['item_product_id'] == $product_id ) {
				unset( $products_array[ $key ] );
			}
		};
		update_post_meta( $post_ids, 'items', $products_array );
	}
	/**
	 * Generate Random
	 *
	 * @param int $length Length Of String.
	 */
	public function generate_random_string( $length ) {
		$characters        = '0123456789abcdefghijklmnopqrstuvwxyz';
		$characters_length = strlen( $characters );
		$random_string     = '';
		for ( $i = 0; $i < $length; $i++ ) {
			$random_string .= $characters[ rand( 0, $characters_length - 1 ) ];
		}
		return $random_string;
	}
}
new RFQ_Meta_Box_Quote_Items();
