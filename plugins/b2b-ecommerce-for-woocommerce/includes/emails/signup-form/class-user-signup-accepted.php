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

if ( ! class_exists( 'User_Signup_Accepted', false ) ) :

	/**
	 *  Class User_Signup_Accepted
	 */
	class User_Signup_Accepted extends WC_Email {

		/**
		 * Constructor.
		 */
		public function __construct() {
			$this->id            = 'accepted_user';
			$this->title         = __( 'User Sign Up Accepted', 'b2b-ecommerce' );
			$this->description   = __( 'User accepted emails are sent to chosen recipient(s) when user have been accepted by admin.', 'b2b-ecommerce' );
			$this->template_html = 'emails/customer-signup-accepted.php';
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
			return __( '[{site_title}]: User Request Has Been Accepted', 'b2b-ecommerce' );
		}

		/**
		 * Get email heading.
		 *
		 * @since  3.1.0
		 * @return string
		 */
		public function get_default_heading() {
			return __( 'User Request Accepted', 'b2b-ecommerce' );
		}

		/**
		 * Trigger the sending of this email.
		 *
		 * @param int  $user_id The user ID.
		 * @param bool $user The user.
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
		 * @since 1.1.5.0
		 * @return string
		 */
		public function get_default_additional_content() {
			return __( 'Thanks for reading.', 'b2b-ecommerce' );
		}

		/**
		 * Initialise settings form fields.
		 */
		public function init_form_fields() {
			/* translators: %s: list of placeholders */
			$placeholder_text  = sprintf( __( 'Available placeholders: %s', 'b2b-ecommerce' ), '<code>' . esc_html( implode( '</code>, <code>', array_keys( $this->placeholders ) ) ) . '</code>' );
			$this->form_fields = array(
				'enabled'            => array(
					'title'   => __( 'Enable/Disable', 'b2b-ecommerce' ),
					'type'    => 'checkbox',
					'label'   => __( 'Enable this email notification', 'b2b-ecommerce' ),
					'default' => 'yes',
				),
				'recipient'          => array(
					'title'       => __( 'Recipient(s)', 'b2b-ecommerce' ),
					'type'        => 'text',
					/* translators: %s: admin email */
					'description' => sprintf( __( 'Enter recipients (comma separated) for this email. Defaults to %s.', 'b2b-ecommerce' ), '<code>' . esc_attr( get_option( 'admin_email' ) ) . '</code>' ),
					'placeholder' => '',
					'default'     => '',
					'desc_tip'    => true,
				),
				'subject'            => array(
					'title'       => __( 'Subject', 'b2b-ecommerce' ),
					'type'        => 'text',
					'desc_tip'    => true,
					'description' => $placeholder_text,
					'placeholder' => $this->get_default_subject(),
					'default'     => '',
				),
				'heading'            => array(
					'title'       => __( 'Email heading', 'b2b-ecommerce' ),
					'type'        => 'text',
					'desc_tip'    => true,
					'description' => $placeholder_text,
					'placeholder' => $this->get_default_heading(),
					'default'     => '',
				),
				'additional_content' => array(
					'title'       => __( 'Additional content', 'b2b-ecommerce' ),
					'description' => __( 'Text to appear below the main email content.', 'b2b-ecommerce' ) . ' ' . $placeholder_text,
					'css'         => 'width:400px; height: 75px;',
					'placeholder' => __( 'N/A', 'b2b-ecommerce' ),
					'type'        => 'textarea',
					'default'     => $this->get_default_additional_content(),
					'desc_tip'    => true,
				),
				'email_type'         => array(
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
