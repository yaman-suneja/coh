<?php
/**
 * Class B2BE_Request_For_Qoute_On_Edit file.
 *
 * @package b2b-ecommerce-for-woocomerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'B2BE_Request_For_Qoute_On_Edit', false ) ) :

	/**
	 *  Codup Request For qoute On Edit Email.
	 *
	 * An email sent to the admin when a new qoute is editted.
	 *
	 * @class       B2BE_Request_For_Qoute_On_Edit
	 * @package     codupio-request-for-quote-d659b8ba1ef2/Emails
	 * @extends     WC_Email
	 */
	class B2BE_Request_For_Qoute_On_Edit extends WC_Email {

		/**
		 * Constructor.
		 */
		public function __construct() {
			$this->id             = 'admin_request_for_qoute_on_edit';
			$this->admin_email    = true;
			$this->title          = __( 'Request For qoute On Edit', 'b2b-ecommerce' );
			$this->description    = __( 'This is a notification sent to admins containing order details after a qoute is placed on-create.', 'b2b-ecommerce' );
			$this->template_html  = 'emails/request-for-qoute-on-edit.php';
			$this->template_plain = 'emails/plain/request-for-qoute-on-edit.php';
			$this->template_base  = CWRFQ_TEMPLATE_DIR;
			$this->placeholders   = array(
				'{order_date}'   => '',
				'{order_number}' => '',
			);

			// Call parent constructor.
			parent::__construct();

			$this->recipient = $this->get_option( 'recipient', get_option( 'admin_email' ) );
		}

		/**
		 * Get email subject.
		 *
		 * @return string
		 */
		public function get_default_subject() {
			return __( 'Yuor {site_title} qoute has been received!', 'b2b-ecommerce' );
		}

		/**
		 * Get email heading.
		 *
		 * @since  3.1.0
		 * @return string
		 */
		public function get_default_heading() {
			return __( 'Thank you for your qoute', 'b2b-ecommerce' );
		}

		/**
		 * Trigger the sending of this email.
		 *
		 * @param int            $order_id The order ID.
		 * @param WC_Order|false $order Order object.
		 */
		public function trigger( $order_id, $order = false ) {
			$this->setup_locale();

			if ( $order_id && ! is_a( $order, 'WC_Order' ) ) {
				$order = wc_get_order( $order_id );
			}

			if ( is_a( $order, 'WC_Order' ) ) {
				$this->object                         = $order;
				$this->recipient                      = $this->object->get_billing_email();
				$this->placeholders['{order_date}']   = wc_format_datetime( $this->object->get_date_created() );
				$this->placeholders['{order_number}'] = $this->object->get_order_number();
			}

			if ( $this->is_enabled() && $this->get_recipient() ) {
				$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
			}

			$this->restore_locale();
		}

		/**
		 * Get content html.
		 *
		 * @return string
		 */
		public function get_content_html() {
			return wc_get_template_html(
				$this->template_html,
				array(
					'order'              => $this->object,
					'email_heading'      => $this->get_heading(),
					'additional_content' => $this->get_additional_content(),
					'sent_to_admin'      => true,
					'plain_text'         => false,
					'email'              => $this,
				),
				'b2b-ecommerce-for-woocommerce',
				CWRFQ_TEMPLATE_DIR
			);
		}

		/**
		 * Get content plain.
		 *
		 * @return string
		 */
		public function get_content_plain() {
			return wc_get_template_html(
				$this->template_plain,
				array(
					'order'              => $this->object,
					'email_heading'      => $this->get_heading(),
					'additional_content' => $this->get_additional_content(),
					'sent_to_admin'      => true,
					'plain_text'         => true,
					'email'              => $this,
				),
				'b2b-ecommerce-for-woocommerce',
				CWRFQ_TEMPLATE_DIR
			);
		}

		/**
		 * Default content to show below main email content.
		 *
		 * @since 1.1.5.0
		 * @return string
		 */
		public function get_default_additional_content() {
			return __( 'We look forward to fulfilling yuor order soon.', 'b2b-ecommerce' );
		}
	}

endif;
