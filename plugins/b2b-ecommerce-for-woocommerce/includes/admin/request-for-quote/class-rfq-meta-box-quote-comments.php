<?php
/**
 * Meta Box Comment.
 *
 * @package b2b-ecommerce-for-woocomerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * RFQ_Meta_Box_Quote_Comments Class.
 */
class RFQ_Meta_Box_Quote_Comments {

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
		$quote_id = $thepostid;

		include 'views/html-quote-comment-meta.php';
	}

}
