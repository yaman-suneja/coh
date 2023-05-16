<?php
/**
 * Admin Post Types.
 *
 * @package b2b-ecommerce-for-woocomerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * RFQ_Admin_Post_Types class.
 */
class RFQ_Admin_Post_Types {
	/**
	 * Constructor.
	 */
	public function __construct() {
		include_once 'class-rfq-admin-meta-boxes.php';
		include_once 'class-rfq-meta-box-quote-items.php';
		include_once 'class-rfq-meta-box-quote-comments.php';
	}
}

new RFQ_Admin_Post_Types();
