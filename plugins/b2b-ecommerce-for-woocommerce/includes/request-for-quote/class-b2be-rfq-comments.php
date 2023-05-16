<?php
/**
 * WC RFQ settings.
 *
 * @package b2b-ecommerce-for-woocomerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Class B2BE_RFQ_Comments
 */
class B2BE_RFQ_Comments {

	/**
	 * Function Construct.
	 */
	public function __construct() {

		add_action( 'wp_ajax_save_customer_comment', array( $this, 'rfq_save_customer_comment' ) );
		add_action( 'wp_ajax_nopriv_save_customer_comment', array( $this, 'rfq_save_customer_comment' ) );

		add_action( 'wp_ajax_save_admin_comment', array( $this, 'rfq_save_admin_comment' ) );
		add_action( 'wp_ajax_nopriv_save_admin_comment', array( $this, 'rfq_save_admin_comment' ) );

		include 'class-b2be-rfq-form-handler.php';
		add_action( 'wp_ajax_nopriv_add_to_rfq_ajax', array( $this, 'add_to_rfq_ajax' ) );
		add_action( 'wp_ajax_add_to_rfq_ajax', array( $this, 'add_to_rfq_ajax' ) );

		add_action( 'wp_ajax_nopriv_multi_add_to_rfq_ajax', array( $this, 'multi_add_to_rfq_ajax' ) );
		add_action( 'wp_ajax_multi_add_to_rfq_ajax', array( $this, 'multi_add_to_rfq_ajax' ) );

	}

	/**
	 * Add to RFQ Ajax button.
	 */
	public function add_to_rfq_ajax() {

		if ( ! empty( $_POST['wpnonce'] ) ) {
			wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wpnonce'] ) ) );
		}
		B2BE_RFQ_Form_Handler::add_to_rfq_action( false, $_POST );

	}

	/**
	 * Add All to RFQ Ajax button.
	 */
	public function multi_add_to_rfq_ajax() {

		if ( ! empty( $_POST['wpnonce'] ) ) {
			wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wpnonce'] ) ) );
		}

		if ( isset( $_POST['product_array'] ) ) {

			foreach ( filter_input( INPUT_POST, 'product_array', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY ) as $key => $value ) {
				B2BE_RFQ_Form_Handler::add_to_rfq_action( false, $value );
			}
		}

		wp_die();

	}

	/**
	 * Function Save customer Comment.
	 */
	public function rfq_save_customer_comment() {

		if ( ! empty( $_POST['wpnonce'] ) ) {
			wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wpnonce'] ) ) );
		}

		if ( ! empty( $_POST['rfq_customer_comment_textarea'] ) ) {
			$rfq_customer_commentdata = array(
				'comment_content' => ( isset( $_POST['rfq_customer_comment_textarea'] ) ) ? sanitize_text_field( wp_unslash( $_POST['rfq_customer_comment_textarea'] ) ) : '',
				'comment_post_ID' => ( isset( $_POST['rfq_customer_comment_quote_id'] ) ) ? sanitize_text_field( wp_unslash( $_POST['rfq_customer_comment_quote_id'] ) ) : '',
				'user_id'         => get_current_user_id(),
				'comment_author'  => ( isset( $_POST['rfq_customer_comment_author'] ) ) ? sanitize_text_field( wp_unslash( $_POST['rfq_customer_comment_author'] ) ) : '',
			);
			$inserted                 = wp_insert_comment( $rfq_customer_commentdata );
			if ( ! is_wp_error( $inserted ) ) {
				WC()->mailer()->emails['new_comment_submitted']->trigger( wc_get_quote( $rfq_customer_commentdata['comment_post_ID'] ) );
			}
		}
	}
	/**
	 * Function Save Admin Comment.
	 */
	public function rfq_save_admin_comment() {

		if ( ! empty( $_POST['wpnonce'] ) ) {
			wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wpnonce'] ) ) );
		}

		if ( ! empty( $_POST['rfq_admin_comment_textarea'] ) ) {
			$rfq_admin_commentdata = array(
				'comment_content' => ( isset( $_POST['rfq_admin_comment_textarea'] ) ) ? sanitize_text_field( wp_unslash( $_POST['rfq_admin_comment_textarea'] ) ) : '',
				'comment_post_ID' => ( isset( $_POST['rfq_admin_comment_quote_id'] ) ) ? sanitize_text_field( wp_unslash( $_POST['rfq_admin_comment_quote_id'] ) ) : '',
				'user_id'         => get_current_user_id(),
				'comment_author'  => ( isset( $_POST['rfq_admin_comment_author'] ) ) ? sanitize_text_field( wp_unslash( $_POST['rfq_admin_comment_author'] ) ) : '',
			);
			$inserted              = wp_insert_comment( $rfq_admin_commentdata );
			if ( ! is_wp_error( $inserted ) ) {
				WC()->mailer()->emails['new_admin_comment_submitted']->trigger( wc_get_quote( $rfq_admin_commentdata['comment_post_ID'] ) );
			}
		}
	}
}

new B2BE_RFQ_Comments();
