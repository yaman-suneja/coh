<?php
/**
 * Functions used by plugins
 *
 * @since 2.5.0
 * @package woocomerce/templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WC_Email_New_RFQ' ) ) :
	/**
	 * WC_Email_New_RFQ
	 */
	class WC_Email_New_RFQ extends WC_Email {

		/**
		 * Constructor.
		 */
		public function __construct() {
			$this->id            = 'new_rfq';
			$this->title         = __( 'New RFQ', 'b2b-ecommerce' );
			$this->description   = __( 'New RFQ emails are sent to chosen recipient(s) when a new RFQ is received.', 'b2b-ecommerce' );
			$this->template_html = 'emails/admin-new-rfq.php';
			// $this->template_plain = 'emails/plain/admin-new-quote.php';
			$this->template_base          = CWRFQ_TEMPLATE_DIR;
			$this->placeholders           = array(
				'{quote_date}'   => '',
				'{quote_number}' => '',
			);
						$this->email_type = $this->get_option( 'email_type' );

			// Triggers for this email.
			// add_action( 'wcrfq_rfq_created', array( $this, 'trigger' ), 10, 1 );.

			// Call parent constructor.
			parent::__construct();

			// Other settings.
			$this->recipient = $this->get_option( 'recipient', get_option( 'admin_email' ) );
		}

		/**
		 * Get email subject.
		 *
		 * @since  3.1.0
		 * @return string
		 */
		public function get_default_subject() {
			return __( '[{site_title}]: New quote #{quote_number}', 'b2b-ecommerce' );
		}

		/**
		 * Get email heading.
		 *
		 * @since  3.1.0
		 * @return string
		 */
		public function get_default_heading() {
			return __( 'New RFQ: #{quote_number}', 'b2b-ecommerce' );
		}

		/**
		 * Trigger the sending of this email.
		 *
		 * @param string $quote Order object.
		 */
		public function trigger( $quote ) {
			$this->setup_locale();

			if ( $quote && ! is_a( $quote, 'B2BE_RFQ_Quote' ) ) {
				$quote = wc_get_quote( $quote );
			}

			if ( is_a( $quote, 'B2BE_RFQ_Quote' ) ) {
				$this->object                         = $quote;
				$this->placeholders['{quote_date}']   = wc_format_datetime( $this->object->get_date_created() );
				$this->placeholders['{quote_number}'] = $this->object->get_quote_number();
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
					'quote'         => $this->object,
					'email_heading' => $this->get_heading(),

					'sent_to_admin' => true,
					'plain_text'    => false,
					'email'         => $this,
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
					'quote'              => $this->object,
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
		 * Initialise settings form fields.
		 */
		public function init_form_fields() {
			/* translators: %s: list of placeholders */
			$placeholder_text  = sprintf( __( 'Available placeholders: %s', 'b2b-ecommerce' ), '<code>' . implode( '</code>, <code>', array_keys( $this->placeholders ) ) . '</code>' );
			$this->form_fields = array(
				'enabled'    => array(
					'title'   => __( 'Enable/Disable', 'b2b-ecommerce' ),
					'type'    => 'checkbox',
					'label'   => __( 'Enable this email notification', 'b2b-ecommerce' ),
					'default' => 'yes',
				),
				'recipient'  => array(
					'title'       => __( 'Recipient(s)', 'b2b-ecommerce' ),
					'type'        => 'text',
					/* translators: %s: WP admin email */
					'description' => sprintf( __( 'Enter recipients (comma separated) for this email. Defaults to %s.', 'b2b-ecommerce' ), '<code>' . esc_attr( get_option( 'admin_email' ) ) . '</code>' ),
					'placeholder' => '',
					'default'     => '',
					'desc_tip'    => true,
				),
				'subject'    => array(
					'title'       => __( 'Subject', 'b2b-ecommerce' ),
					'type'        => 'text',
					'desc_tip'    => true,
					'description' => $placeholder_text,
					'placeholder' => $this->get_default_subject(),
					'default'     => '',
				),
				'heading'    => array(
					'title'       => __( 'Email heading', 'b2b-ecommerce' ),
					'type'        => 'text',
					'desc_tip'    => true,
					'description' => $placeholder_text,
					'placeholder' => $this->get_default_heading(),
					'default'     => '',
				),
				'email_type' => array(
					'title'       => __( 'Email type', 'b2b-ecommerce' ),
					'type'        => 'select',
					'description' => __( 'Choose which format of email to send.', 'b2b-ecommerce' ),
					'default'     => 'html',
					'class'       => 'email_type wc-enhanced-select',
					'options'     => $this->get_email_type_options(),
					'desc_tip'    => true,
				),
			);
		}
	}

endif;

return new WC_Email_New_RFQ();
