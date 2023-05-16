<?php
/**
 * Codup RFQ Emails.
 *
 * @package codupio-request-for-quote-d659b8ba1ef2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'B2BE_RFQ_Emails' ) ) {
	/**
	 * B2BE_RFQ_Emails.
	 */
	class B2BE_RFQ_Emails {

		/**
		 * Construct.
		 */
		public function __construct() {
			add_filter( 'woocommerce_locate_core_template', array( $this, 'locate_core_template' ), 10, 3 );
			add_filter( 'woocommerce_email_classes', array( $this, 'add_request_for_quote_email' ) );
			add_action( 'b2be_rfq_quote_sumitted', array( $this, 'trigger_quote_submitted_email' ), 11, 2 );
			add_action( 'wcrfq_rfq_created', array( $this, 'trigger_new_rfq_email' ), 11, 1 );
			add_action( 'wcrfq_comment_created', array( $this, 'trigger_new_comment_email' ), 11, 1 );
			add_action( 'cwcrfq_quote_marked_accepted_email', array( $this, 'trigger_quoted_accepted' ), 10, 2 );
			add_action( 'cwcrfq_quote_marked_rejected', array( $this, 'trigger_quoted_rejected' ), 10, 2 );
			add_action( 'cwcrfq_quote_marked_need_revision', array( $this, 'trigger_quote_need_revision' ), 10, 2 );

			add_action( 'sfg_signup_rejected', array( $this, 'trigger_signup_rejected_email' ), 10, 2 );
			add_action( 'sfg_signup_accepted', array( $this, 'trigger_signup_accepted_email' ), 10, 2 );
			add_action( 'sfg_signup_on_hold', array( $this, 'trigger_signup_on_hold_email' ), 10, 2 );
			add_action( 'sfg_signup_pending', array( $this, 'trigger_signup_pending_email' ), 10, 2 );
			add_action( 'sfg_signup_email_to_admin', array( $this, 'trigger_signup_email_to_admin' ), 10, 2 );

		}
		/**
		 * Core Email Templates.
		 * Core files.
		 *
		 * @param string $core_file core files .
		 * Templates.
		 * @param string $template templates .
		 * Template base.
		 * @param string $template_base template base .
		 */
		public function locate_core_template( $core_file, $template, $template_base ) {

			$rfq_email_template = array(
				'request-for-qoute/emails/request-for-qoute-on-message-send.php',
				'request-for-qoute/emails/plain/request-for-qoute-on-message-send.php',
				'request-for-qoute/emails/request-for-qoute-on-edit.php',
				'request-for-qoute/emails/request-for-qoute-on-status-change.php',
				'request-for-qoute/emails/plain/request-for-qoute-on-edit.php',
				'request-for-qoute/emails/customer-quote-submitted.php',
				'request-for-qoute/emails/admin-new-rfq.php',
				'request-for-qoute/emails/new-rfq-email-to-customer.php',
				'request-for-qoute/emails/admin-new-comment.php',
				'request-for-qoute/emails/customer-new-comment.php',
				'request-for-qoute/emails/admin-rfq-accepted.php',
				'request-for-qoute/emails/admin-rfq-rejected.php',
				'request-for-qoute/emails/admin-rfq-need-revision.php',
				'request-for-qoute/emails/customer-signup-rejected.php',
				'request-for-qoute/emails/customer-signup-accepted.php',
				'request-for-qoute/emails/customer-signup-pending.php',
				'request-for-qoute/emails/customer-signup-on-hold.php',
			);

			if ( in_array( $template, $rfq_email_template ) ) {
				$core_file = trailingslashit( CWRFQ_TEMPLATE_DIR ) . $template;
			}

			return $core_file;
		}
		/**
		 * Add Emails File.
		 *
		 * Core files.
		 *
		 * @param string $email_classes core files .
		 */
		public function add_request_for_quote_email( $email_classes ) {

			require CWRFQ_PLUGIN_DIR . '/includes/emails/request-for-quote/class-wc-email-new-rfq.php';
			require CWRFQ_PLUGIN_DIR . '/includes/emails/request-for-quote/class-wc-email-new-rfq-email-to-customer.php';
			require CWRFQ_PLUGIN_DIR . '/includes/emails/request-for-quote/class-wc-email-new-comment.php';
			require CWRFQ_PLUGIN_DIR . '/includes/emails/request-for-quote/class-wc-email-new-admin-comment.php';
			require CWRFQ_PLUGIN_DIR . '/includes/emails/request-for-quote/class-b2be-request-for-qoute-on-message-send.php';
			require CWRFQ_PLUGIN_DIR . '/includes/emails/request-for-quote/class-b2be-request-for-qoute-status-change.php';
			require CWRFQ_PLUGIN_DIR . '/includes/emails/request-for-quote/class-rfq-email-quote-accepted.php';
			require CWRFQ_PLUGIN_DIR . '/includes/emails/request-for-quote/class-rfq-email-quote-rejected.php';
			require CWRFQ_PLUGIN_DIR . '/includes/emails/request-for-quote/class-rfq-email-customer-quote-submitted.php';
			require CWRFQ_PLUGIN_DIR . '/includes/emails/request-for-quote/class-rfq-email-quote-need-revision.php';

			require CWRFQ_PLUGIN_DIR . '/includes/emails/signup-form/class-user-signup-rejected.php';
			require CWRFQ_PLUGIN_DIR . '/includes/emails/signup-form/class-user-signup-accepted.php';
			require CWRFQ_PLUGIN_DIR . '/includes/emails/signup-form/class-user-signup-on-hold.php';
			require CWRFQ_PLUGIN_DIR . '/includes/emails/signup-form/class-user-signup-pending.php';
			require CWRFQ_PLUGIN_DIR . '/includes/emails/signup-form/class-user-signup-email-to-admin.php';

			$email_classes['B2BE_Request_For_Qoute_On_Message_Send'] = new B2BE_Request_For_Qoute_On_Message_Send();
			$email_classes['B2BE_Request_For_Qoute_Status_Change']   = new B2BE_Request_For_Qoute_Status_Change();
			$email_classes['rfq_quote_submitted']                    = new RFQ_Email_Customer_Quote_Submitted();
			$email_classes['new_rfq_submission_email_to_customer']   = new WC_Email_New_RFQ_Email_To_Customer();
			$email_classes['new_rfq_submitted']                      = new WC_Email_New_RFQ();
			$email_classes['new_comment_submitted']                  = new WC_Email_New_Comment();
			$email_classes['new_admin_comment_submitted']            = new WC_Email_New_Admin_Comment();
			$email_classes['rfq_accepted']                           = new RFQ_Email_Quote_Accepted();
			$email_classes['rfq_rejected']                           = new RFQ_Email_Quote_Rejected();
			$email_classes['rfq_need_revision']                      = new RFQ_Email_Quote_Need_Revision();

			$email_classes['signup_accepted']       = new User_Signup_Accepted();
			$email_classes['signup_rejected']       = new User_Signup_Rejected();
			$email_classes['signup_on_hold']        = new User_Signup_On_Hold();
			$email_classes['signup_pending']        = new User_Signup_Pending();
			$email_classes['signup_email_to_admin'] = new User_Signup_Email_To_Admin();

			return $email_classes;

		}

		/**
		 * Trigger.
		 *
		 * @param int    $user_id User Id .
		 * @param object $user User object .
		 */
		public function trigger_signup_rejected_email( $user_id, $user ) {
			WC()->mailer()->emails['signup_rejected']->trigger( $user_id, $user );
		}

		/**
		 * Trigger.
		 *
		 * @param int    $user_id User Id .
		 * @param object $user User object .
		 */
		public function trigger_signup_accepted_email( $user_id, $user ) {
			WC()->mailer()->emails['signup_accepted']->trigger( $user_id, $user );
		}

		/**
		 * Trigger.
		 *
		 * @param int    $user_id User Id .
		 * @param object $user User object .
		 */
		public function trigger_signup_on_hold_email( $user_id, $user ) {
			WC()->mailer()->emails['signup_on_hold']->trigger( $user_id, $user );
		}

		/**
		 * Trigger.
		 *
		 * @param int    $user_id User Id.
		 * @param object $user User object.
		 */
		public function trigger_signup_pending_email( $user_id, $user ) {
			WC()->mailer()->emails['signup_pending']->trigger( $user_id, $user );
		}

		/**
		 * Trigger.
		 *
		 * @param int    $user_id User Id.
		 * @param object $user User object.
		 */
		public function trigger_signup_email_to_admin( $user_id, $user ) {
			WC()->mailer()->emails['signup_email_to_admin']->trigger( $user_id, $user );
		}

		/**
		 * Trigger.
		 *
		 * @param int    $quote_id Quote Id .
		 * @param object $quote Quote object .
		 */
		public function trigger_quote_submitted_email( $quote_id, $quote ) {
			WC()->mailer()->emails['rfq_quote_submitted']->trigger( $quote_id, $quote );
		}
		/**
		 * Trigger.
		 *
		 * @param object $quote Quote object .
		 */
		public function trigger_new_rfq_email( $quote ) {
			WC()->mailer()->emails['new_rfq_submitted']->trigger( $quote );
			WC()->mailer()->emails['new_rfq_submission_email_to_customer']->trigger( $quote );
		}
		/**
		 * Trigger.
		 *
		 * @param int    $quote_id Quote_Id .
		 * @param object $quote Quote object .
		 */
		public function trigger_quoted_accepted( $quote_id, $quote ) {
			WC()->mailer()->emails['rfq_accepted']->trigger( $quote_id, $quote );
		}
		/**
		 * Trigger.
		 *
		 * @param int    $quote_id Quote ID .
		 * @param object $quote Quote object .
		 */
		public function trigger_quoted_rejected( $quote_id, $quote ) {
			WC()->mailer()->emails['rfq_rejected']->trigger( $quote_id, $quote );
		}
		/**
		 * Trigger.
		 *
		 * @param int    $quote_id Quote ID .
		 * @param object $quote Quote object .
		 */
		public function trigger_quote_need_revision( $quote_id, $quote ) {
			WC()->mailer()->emails['rfq_need_revision']->trigger( $quote_id, $quote );
		}
		/**
		 * Trigger.
		 *
		 * @param object $quote Quote object .
		 */
		public function trigger_new_comment_email( $quote ) {
			WC()->mailer()->emails['new_comment_submitted']->trigger( $quote );
		}
		/**
		 * Trigger.
		 *
		 * @param object $quote Quote object .
		 */
		public function trigger_new_admin_comment_email( $quote ) {
			WC()->mailer()->emails['new_admin_comment_submitted']->trigger( $quote );
		}
	}

}
new B2BE_RFQ_Emails();
