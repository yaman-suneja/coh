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

if ( ! class_exists( 'User_Signup_Pending', false ) ) :

	/**
	 *  Class User_Signup_Pending
	 */
	class User_Signup_Pending extends WC_Email {

		/**
		 * Constructor.
		 */
		public function __construct() {
			$this->id            = 'pending_user';
			$this->title         = __( 'User Sign Up Pending', 'codup-wcrfq' );
			$this->description   = __( 'User pending emails are sent to chosen recipient(s) when user apply for signup.', 'codup-wcrfq' );
			$this->template_html = 'emails/customer-signup-pending.php';
			$this->template_base = CWSFG_TEMPLATE_DIR;
			$this->placeholders  = array(
				'{user_date}'      => '',
				'{user_number}'    => '',
				'{user_full_name}' => '',
			);

			// Call parent constructor.
			parent::__construct();

		}

		/**
		 * Get email subject.
		 *
		 * @since  3.1.0
		 * @return string
		 */
		public function get_default_subject() {
			return __( '[{site_title}]: User Request Is Pending', 'codup-wcrfq' );
		}

		/**
		 * Get email heading.
		 *
		 * @since  3.1.0
		 * @return string
		 */
		public function get_default_heading() {
			return __( 'User Request Pending', 'codup-wcrfq' );
		}

		/**
		 * Trigger the sending of this email.
		 *
		 * @param int  $user_id The order ID.
		 * @param bool $user The Quote.
		 */
		public function trigger( $user_id, $user = false ) {
			$this->setup_locale();

			if ( ! empty( $user_id ) ) {
				$user = get_userdata( $user_id );
			}

			$this->object                        = $user;
			$this->placeholders['{user_date}']   = wc_format_datetime( $this->object->get_date_created() );
			$this->placeholders['{user_number}'] = $user_id;
			$this->placeholders['{email}']       = $this->object->user_email;

			if ( $this->is_enabled() ) {
				$this->send( $this->object->user_email, $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
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
					'user'          => $this->object,
					'email_heading' => $this->get_heading(),
					'sent_to_admin' => false,
					'plain_text'    => false,
					'email'         => $this,
				),
				'b2b-ecommerce-for-woocommerce',
				CWSFG_TEMPLATE_DIR
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
					'user'          => $this->object,
					'email_heading' => $this->get_heading(),
					'sent_to_admin' => true,
					'plain_text'    => true,
					'email'         => $this,
				),
				'b2b-ecommerce-for-woocommerce',
				CWSFG_TEMPLATE_DIR
			);
		}

		/**
		 * Default content to show below main email content.
		 *
		 * @since 3.7.0
		 * @return string
		 */
		public function get_default_additional_content() {
			return __( 'Thanks for reading.', 'codup-wcrfq' );
		}

		/**
		 * Initialise settings form fields.
		 */
		public function init_form_fields() {
			/* translators: %s: list of placeholders */
			$placeholder_text  = sprintf( __( 'Available placeholders: %s', 'codup-wcrfq' ), '<code>' . esc_html( implode( '</code>, <code>', array_keys( $this->placeholders ) ) ) . '</code>' );
			$this->form_fields = array(
				'enabled'            => array(
					'title'   => __( 'Enable/Disable', 'codup-wcrfq' ),
					'type'    => 'checkbox',
					'label'   => __( 'Enable this email notification', 'codup-wcrfq' ),
					'default' => 'yes',
				),
				'recipient'          => array(
					'title'       => __( 'Recipient(s)', 'codup-wcrfq' ),
					'type'        => 'text',
					/* translators: %s: admin email */
					'description' => sprintf( __( 'Enter recipients (comma separated) for this email. Defaults to %s.', 'codup-wcrfq' ), '<code>' . esc_attr( get_option( 'admin_email' ) ) . '</code>' ),
					'placeholder' => '',
					'default'     => '',
					'desc_tip'    => true,
				),
				'subject'            => array(
					'title'       => __( 'Subject', 'codup-wcrfq' ),
					'type'        => 'text',
					'desc_tip'    => true,
					'description' => $placeholder_text,
					'placeholder' => $this->get_default_subject(),
					'default'     => '',
				),
				'heading'            => array(
					'title'       => __( 'Email heading', 'codup-wcrfq' ),
					'type'        => 'text',
					'desc_tip'    => true,
					'description' => $placeholder_text,
					'placeholder' => $this->get_default_heading(),
					'default'     => '',
				),
				'additional_content' => array(
					'title'       => __( 'Additional content', 'codup-wcrfq' ),
					'description' => __( 'Text to appear below the main email content.', 'codup-wcrfq' ) . ' ' . $placeholder_text,
					'css'         => 'width:400px; height: 75px;',
					'placeholder' => __( 'N/A', 'codup-wcrfq' ),
					'type'        => 'textarea',
					'default'     => $this->get_default_additional_content(),
					'desc_tip'    => true,
				),
				'email_type'         => array(
					'title'       => __( 'Email type', 'codup-wcrfq' ),
					'type'        => 'select',
					'description' => __( 'Choose which format of email to send.', 'codup-wcrfq' ),
					'default'     => 'html',
					'class'       => 'email_type wc-enhanced-select',
					'options'     => $this->get_email_type_options(),
					'desc_tip'    => true,
				),
			);
		}
	}

endif;

return new RFQ_Email_Quote_Rejected();
