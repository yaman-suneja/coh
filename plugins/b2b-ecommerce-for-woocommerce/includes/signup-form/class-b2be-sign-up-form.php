<?php
/**
 * WC RFQ.
 *
 * @package b2b-ecommerce-for-woocomerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'B2BE_Sign_Up_Form' ) ) {
	/**
	 * Class B2BE_Sign_Up_Form.
	 */
	class B2BE_Sign_Up_Form {

		/**
		 * Cart Variable.
		 */
		public function __construct() {

			B2BE_Sign_Up_Form_Settings::init();
			add_shortcode( 'b2be_signup_form', array( $this, 'b2be_sign_up_form' ) );
			add_action( 'wp_loaded', array( $this, 'submit_sign_up_form' ), 10, 1 );
			add_action( 'woocommerce_edit_account_form', array( $this, 'add_field_edit_account_form' ) );
			add_action( 'woocommerce_save_account_details', array( $this, 'save_account_details' ) );
			add_filter( 'authenticate', array( $this, 'b2be_sign_in_authentication' ), 30, 3 );
			add_action( 'woocommerce_created_customer', array( $this, 'b2be_sign_in_approval' ), 10, 4 );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_front_end_scripts' ) );

		}

		/**
		 * Enqueue Front End scripts.
		 */
		public function enqueue_front_end_scripts() {

			wp_register_style( 'b2be-sigup-form-select2', CWRFQ_ASSETS_DIR_URL . '/css/select2.min.css', null, rand() );
			wp_register_script( 'b2be-sigup-form-select2', CWRFQ_ASSETS_DIR_URL . '/js/select2.min.js', array( 'jquery' ), true );
			wp_register_script( 'b2be-sigup-form', CWRFQ_ASSETS_DIR_URL . '/js/signup-form/b2be-sigup-form.js', array( 'jquery' ), true );
			wp_localize_script(
				'b2be-sigup-form',
				'sign_up_form',
				array(
					'states' => b2be_get_field_option( 'state' ),
				)
			);
		}

		/**
		 * Login Authentication.
		 *
		 * @param Object $user User Object.
		 * @param string $username User Name.
		 * @param string $password User Pasword.
		 */
		public function b2be_sign_in_authentication( $user, $username, $password ) {
			if ( ! empty( $user->data ) ) {
				$current_user_id = $user->data->ID;
				if ( ! empty( $current_user_id ) ) {
					$is_rejected = get_user_meta( $current_user_id, 'sign_up_request', true );
					if ( ! empty( $is_rejected ) && 'sign_up_approval' != $is_rejected ) {
						$user = new WP_Error( 'authentication_failed', __( '<strong>ERROR</strong>: You cannnot access your account until admin <b>Approves</b> your request', 'b2b-ecommerce' ) );
					}
				}
			}
			return $user;

		}

		/**
		 * Sign Up form .
		 */
		public function b2be_sign_up_form() {

			if ( ! is_user_logged_in() || is_admin() ) {
				/*
				@name: b2be_sign_up_fields
				@desc: Modify sign up form before rendering on page.
				@param: (array) $signup_fields Sign Up fields to render.
				@package: b2b-ecommerce-for-woocommerce
				@module: sign up form
				@type: filter
				*/
				$output                = '';
				$sign_up_fields        = apply_filters( 'b2be_sign_up_fields', get_option( 'b2be_signup_field' ) );
				$signup_form_dropdowns = array( 'country', 'state', 'role' );
				$wc_signup_fields      = array( 'city', 'username', 'fname', 'lname', 'address_1', 'address_2', 'zip_code', 'company' );

				if ( ! empty( $sign_up_fields ) && count( $sign_up_fields ) != 0 ) {
					ob_start();
					wc_get_template(
						'signup-form/sign-up-form.php',
						array(
							'sign_up_fields'        => $sign_up_fields,
							'wc_signup_fields'      => $wc_signup_fields,
							'signup_form_dropdowns' => $signup_form_dropdowns,
						),
						'b2b-ecommerce-for-woocommerce',
						CWRFQ_PLUGIN_DIR . '/templates/'
					);
					$output = ob_get_clean();
				} else {
					$output = __( 'There Is Nothing To Show Please Select Some Fields', 'b2b-ecommerce' );
				}
				return $output;
			}
		}

		/**
		 * Submit Sign Up Form .
		 */
		public function submit_sign_up_form() {

			if ( isset( $_POST['wcb2be_signup_nonce'] ) ) {
				$nonce = sanitize_text_field( wp_unslash( $_POST['wcb2be_signup_nonce'] ) );
				wp_verify_nonce( $nonce, 'wcb2be_signup_setting' );
			}

			$submitted_data = ! empty( $_POST['signup_form_field'] ) ? filter_input( INPUT_POST, 'signup_form_field', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY ) : '';

			if ( ! empty( $submitted_data ) ) {

				// Getting the full details about the fields includeing submitted value...
				$field_details = b2be_get_signup_form_fields_details( $submitted_data );

				// Registering the user according to submitted data...
				$user_id = $this->register_b2be_user( $submitted_data );

				if ( ! $user_id ) {
					return;
				}

				// saving the signup form entries...
				$this->update_user_meta_entries( $user_id, $field_details );

				// Send email notifications to user on form submission...
				$this->send_submission_notifications( $user_id );

				/*
				@name: b2be_after_sfg_form_submitted
				@desc: Runs after Sign up form is submitted.
				@param: (object) $custom_signup_fields Sign form data filled by user.
				@package: b2b-ecommerce-for-woocommerce
				@module: sign up form
				@type: action
				*/
				do_action( 'b2be_after_sfg_form_submitted', $field_details );
				return;
			}
		}

		/**
		 * Function to register the user...
		 *
		 * @param array $submitted_data Sign up form detailed entries.
		 */
		public function register_b2be_user( $submitted_data ) {

			$email      = $this->get_user_email( $submitted_data );
			$username   = $this->get_username( $submitted_data );
			$first_name = $this->get_first_name( $submitted_data );
			$last_name  = $this->get_last_name( $submitted_data );
			$user_role  = $this->get_user_role( $submitted_data );
			$password   = $this->create_password();

			if ( ! $email || ! $username ) {
				return;
			}

			$userdata = array(
				'user_pass'  => $password,
				'user_login' => $username,
				'user_email' => $email,
				'first_name' => $first_name,
				'last_name'  => $last_name,
				'role'       => $user_role,
			);
			$user_id  = wp_insert_user( $userdata );

			return apply_filters( 'b2be_signup_form_user_id', $user_id );
		}

		/**
		 * Function to get the user name...
		 *
		 * @param array $submitted_data Sign up form detailed entries.
		 */
		public function get_username( $submitted_data ) {

			$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$username   = '';
			$random_id  = '';

			if ( isset( $submitted_data['username'] ) ) {
				if ( empty( $submitted_data['username'] ) ) {
					wc_add_notice( __( 'You Cannot Sign Up.. Username Not Provided!', 'b2b-ecommerce' ), 'error' );
					return false;
				}
				$username = $submitted_data['username'];
			} else {
				for ( $i = 0; $i < 3; $i++ ) {
					$index      = rand( 0, strlen( $characters ) - 1 );
					$random_id .= $characters[ $index ];
				}
				if ( isset( $submitted_data['fname'] ) ) {
					$username .= $submitted_data['fname'] . '_';
				}
				if ( isset( $submitted_data['lname'] ) ) {
					$username .= $submitted_data['lname'] . '_';
				}
				$username .= $random_id;
			}

			if ( username_exists( $username ) ) {
				wc_add_notice( __( 'You Cannot Sign Up.. Username Already Exist!', 'b2b-ecommerce' ), 'error' );
				return false;
			}
			return apply_filters( 'b2be_signup_form_username', $username );
		}

		/**
		 * Function to get the user email...
		 *
		 * @param array $submitted_data Sign up form detailed entries.
		 */
		public function get_user_email( $submitted_data ) {

			$email = '';
			if ( isset( $submitted_data['email'] ) ) {
				$email = $submitted_data['email'];
			}

			if ( email_exists( $email ) ) {
				wc_add_notice( __( 'You Cannot Sign Up.. Email Already Exist!', 'b2b-ecommerce' ), 'error' );
				return false;
			} elseif ( empty( $email ) ) {
				wc_add_notice( __( 'You Cannot Sign Up.. No Email Provided!', 'b2b-ecommerce' ), 'error' );
				return false;
			}

			return apply_filters( 'b2be_signup_form_user_email', $email );

		}

		/**
		 * Function to ceate a random password...
		 */
		private function create_password() {
			$characters      = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$random_password = '';

			for ( $i = 0; $i < 32; $i++ ) {
				$index            = rand( 0, strlen( $characters ) - 1 );
				$random_password .= $characters[ $index ];
			}
			return $random_password;
		}

		/**
		 * Function to get the user first name...
		 *
		 * @param array $submitted_data Sign up form detailed entries.
		 */
		public function get_first_name( $submitted_data ) {

			$first_name = '';
			if ( isset( $submitted_data['fname'] ) ) {
				$first_name = $submitted_data['fname'];
			}
			return apply_filters( 'b2be_signup_first_name', $first_name );
		}

		/**
		 * Function to get the user last name...
		 *
		 * @param array $submitted_data Sign up form detailed entries.
		 */
		public function get_last_name( $submitted_data ) {

			$last_name = '';
			if ( isset( $submitted_data['lname'] ) ) {
				$last_name = $submitted_data['lname'];
			}
			return apply_filters( 'b2be_signup_last_name', $last_name );
		}

		/**
		 * Function to get the user role...
		 *
		 * @param array $submitted_data Sign up form detailed entries.
		 */
		public function get_user_role( $submitted_data ) {

			$user_role = 'customer';
			if ( isset( $submitted_data['role'] ) ) {
				$user_role = $submitted_data['role'];
			}
			return apply_filters( 'b2be_signup_user_role', $user_role );
		}

		/**
		 * Function to check if admin approval is required...
		 */
		public function get_admin_approval() {
			$admin_approval = false;
			if ( 'yes' == get_option( 'codup_signup_admin_apporval' ) ) {
				$admin_approval = true;
			}
			return apply_filters( 'b2be_signup_admin_approval', $admin_approval );
		}

		/**
		 * Function to send email notfication to user after form submission...
		 *
		 * @param int $new_user_id Created user id.
		 */
		public function send_submission_notifications( $new_user_id ) {

			$user           = get_user_by( 'id', $new_user_id );
			$admin_approval = $this->get_admin_approval();          // Checking if admin approval is required...

			if ( ! $admin_approval ) {

				wp_new_user_notification( $new_user_id, null, 'both' );
				update_user_meta( $new_user_id, 'sign_up_request', 'sign_up_approval' );
				wc_add_notice( __( 'User Registered Successfully. Please check your email to set password.', 'b2b-ecommerce' ), 'success' );
				return;

			} else {
				/*
				@name: sfg_signup_email_to_admin
				@desc: Runs after Sign up form is submitted and requires admin approval.
				@param: (int) $user_id User Id of the user being registered.
				@param: (object) $user Data of user being registered.
				@package: b2b-ecommerce-for-woocommerce
				@module: sign up form
				@type: action
				*/
				do_action( 'sfg_signup_email_to_admin', $new_user_id, $user );

				/*
				@name: sfg_signup_pending
				@desc: Runs after Sign up form is submitted and requires admin approval.
				@param: (int) $user_id User Id of the user being registered.
				@param: (object) $user Data of user being registered.
				@package: b2b-ecommerce-for-woocommerce
				@module: sign up form
				@type: action
				*/
				do_action( 'sfg_signup_pending', $new_user_id, $user );
				update_user_meta( $new_user_id, 'sign_up_request', 'sign_up_pending' );

				wc_add_notice( __( 'User Registered Successfully.Please check your email to see your status.', 'b2b-ecommerce' ), 'success' );

			}
		}

		/**
		 * Function to save sign up form entries in usermeta...
		 *
		 * @param int   $user_id Current User id.
		 * @param array $field_details Sign up form detailed entries.
		 */
		public function update_user_meta_entries( $user_id, $field_details ) {

			foreach ( $field_details as $key => $field ) {
				$this->b2be_map_wc_user_fields( $user_id, $field['type'], $field['value'] );
			}
			update_user_meta( $user_id, 'b2be_sign_up_entries', $field_details );

		}

		/**
		 * Function to map user entries to WooCommerce user meta fields.
		 *
		 * @param int    $user_id Current created user id.
		 * @param string $field_type Current field type.
		 * @param string $field_value Current field value.
		 */
		public function b2be_map_wc_user_fields( $user_id, $field_type, $field_value ) {

			switch ( $field_type ) {
				case 'fname':
					update_user_meta( $user_id, 'billing_first_name', $field_value );
					break;

				case 'lname':
					update_user_meta( $user_id, 'billing_last_name', $field_value );
					break;

				case 'company':
					update_user_meta( $user_id, 'billing_company', $field_value );
					break;

				case 'email':
					update_user_meta( $user_id, 'billing_email', $field_value );
					break;

				case 'address_1':
					update_user_meta( $user_id, 'billing_address_1', $field_value );
					break;

				case 'address_2':
					update_user_meta( $user_id, 'billing_address_2', $field_value );
					break;

				case 'country':
					update_user_meta( $user_id, 'billing_country', $field_value );
					break;

				case 'city':
					update_user_meta( $user_id, 'billing_city', $field_value );
					break;

				case 'state':
					update_user_meta( $user_id, 'billing_state', $field_value );
					break;

				case 'zip_code':
					update_user_meta( $user_id, 'billing_postcode', $field_value );
					break;

				case 'phone':
					update_user_meta( $user_id, 'billing_phone', $field_value );
					break;

				default:
					// Nothing to do...
					break;
			}

		}

		/**
		 * Function to approve user on guest checkout...
		 *
		 * @param int    $customer_id Current Customer Id.
		 * @param array  $new_customer_data Current Customer data.
		 * @param string $password_generated Curently generated password.
		 */
		public function b2be_sign_in_approval( $customer_id, $new_customer_data, $password_generated ) {

			$admin_approval = ( 'yes' == get_option( 'codup_signup_admin_apporval' ) ) ? 1 : 0;

			if ( ! $admin_approval ) {
				update_user_meta( $customer_id, 'sign_up_request', 'sign_up_approval' );
			} else {
				update_user_meta( $customer_id, 'sign_up_request', 'sign_up_pending' );
			}

		}

		/**
		 * Add fields on my account page...
		 */
		public function add_field_edit_account_form() {

			$b2be_sign_up_entries = get_user_meta( get_current_user_id(), 'b2be_sign_up_entries', true );
			$fields_to_skip       = array( 'username', 'fname', 'lname', 'email', 'role' );
			$text_fields          = array( 'phone', 'address_1', 'address_2', 'city', 'zip_code', 'company' );

			if ( empty( $b2be_sign_up_entries ) ) {
				return;
			}

			foreach ( $b2be_sign_up_entries as $key => $value ) {

				if ( in_array( $value['type'], $fields_to_skip ) ) {
					continue;
				}

				if ( in_array( $value['type'], $text_fields ) ) {
					$type = 'text';
				} elseif ( 'phone' == $value['type'] ) {
					$type = 'tel';
				}

				woocommerce_form_field(
					$value['type'] . '_' . $key,
					array(
						'type'     => $type,
						'required' => $value['required'],
						'label'    => $value['name'],
					),
					$value['value']
				);

			}

		}

		/**
		 * Save field value
		 *
		 * @param int $user_id User Id.
		 */
		public function save_account_details( $user_id ) {

			if ( isset( $_POST['wcb2be_account_nonce'] ) ) {
				$nonce = sanitize_text_field( wp_unslash( $_POST['wcb2be_account_nonce'] ) );
				wp_verify_nonce( $nonce, 'wcb2be_account_page' );
			}

			$b2be_sign_up_entries = get_user_meta( get_current_user_id(), 'b2be_sign_up_entries', true );

			if ( ! empty( $b2be_sign_up_entries ) ) {

				foreach ( $b2be_sign_up_entries as $index => $value ) {

					if ( 'username' == $value['type'] || 'role' == $value['type'] ) {
						continue;
					}

					if ( 'fname' == $value['type'] ) {
						$type = 'first_name';
					} elseif ( 'lname' == $value['type'] ) {
						$type = 'last_name';
					} else {
						$type = $value['type'];
					}
					$key = $value['type'] . '_' . $index;

					if ( isset( $_POST[ 'account_' . $type ] ) ) {
						$b2be_sign_up_entries[ $index ]['value'] = sanitize_text_field( wp_unslash( $_POST[ 'account_' . $type ] ) );
					} elseif ( isset( $_POST[ $key ] ) ) {
						$b2be_sign_up_entries[ $index ]['value'] = sanitize_text_field( wp_unslash( $_POST[ $key ] ) );
					} else {
						$b2be_sign_up_entries[ $index ]['value'] = '';
					}
				}
				update_user_meta( $user_id, 'b2be_sign_up_entries', $b2be_sign_up_entries );
			}

		}

	}
}
new B2BE_Sign_Up_Form();
