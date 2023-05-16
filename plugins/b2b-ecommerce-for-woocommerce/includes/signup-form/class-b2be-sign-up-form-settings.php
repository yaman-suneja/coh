<?php
/**
 * WC Ecommerce For Woocommerce Main Class.
 *
 * @package b2b-ecommerce-for-woocomerce
 */

defined( 'ABSPATH' ) || exit;

/**
 * Codup_B2B_Ecommerce_For_Woocommerce class.
 */
class B2BE_Sign_Up_Form_Settings {

	/**
	 * Function Calculate Shipping.
	 */
	public static function init() {

		add_action( 'show_user_profile', __CLASS__ . '::sfg_admin_approval_field' );
		add_action( 'edit_user_profile', __CLASS__ . '::sfg_admin_approval_field' );

		add_action( 'wp_ajax_save_signup_form', __CLASS__ . '::save_signup_form' );
		add_action( 'wp_ajax_nopriv_save_signup_form', __CLASS__ . '::save_signup_form' );

		add_action( 'wp_ajax_sfg_request_action', __CLASS__ . '::sfg_request_action' );
		add_action( 'wp_ajax_nopriv_sfg_request_action', __CLASS__ . '::sfg_request_action' );

		add_action( 'restrict_manage_users', __CLASS__ . '::filter_users_by_request_status' );
		add_filter( 'pre_get_users', __CLASS__ . '::filter_users_by_request_status_section' );

		add_action( 'admin_menu', __CLASS__ . '::register_signup_form_entires_menu' );

	}

	/**
	 * Construct.
	 */
	public function __construct() {

		add_filter( 'manage_users_custom_column', array( $this, 'sfg_user_table_row' ), 10, 3 );
		add_filter( 'manage_users_columns', array( $this, 'sfg_user_table' ), 20 );

	}

	/**
	 * Return RFQ setting fields.
	 *
	 * @return type
	 */
	public static function get_settings() {

		$settings = self::get_sign_up_form_fields();

		return $settings;
	}

	/**
	 * Return User Role setting fields.
	 */
	public static function get_sign_up_form_fields() {

		$signup_fields = b2be_signup_form_backward_compatibility();  // sign up form backward compatibility...

		include CWRFQ_PLUGIN_DIR . '/includes/admin/signup-form/views/sign-up-form-fields.php';

	}

	/**
	 * Creating Sign up form entries.
	 */
	public static function register_signup_form_entires_menu() {

		add_submenu_page( 'woocommerce', 'SignUp Form Entries', 'SignUp Form Entries', 'manage_options', 'signup-form-entries', array( __CLASS__, 'render_signup_form_entries' ) );

	}

	/**
	 * Rendering signup form fields.
	 */
	public static function render_signup_form_entries() {

		$b2be_user_ids = get_users(
			array(
				'meta_key' => 'b2be_sign_up_entries',
				'fields'   => 'ID',
				'orderby'  => 'ID',
				'order'    => 'ASC',
			)
		);

		include_once CWRFQ_PLUGIN_DIR . '/includes/admin/signup-form/views/sign-up-form-entries.php';

	}

	/**
	 * Delete SignUp Field.
	 */
	public static function save_signup_form() {

		if ( ! empty( $_REQUEST['_wpnonce'] ) ) {
			wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ) );
		}
		$required_approval = filter_input( INPUT_POST, 'required_approval', FILTER_DEFAULT, FILTER_SANITIZE_STRING );
		if ( 'true' == $required_approval ) {
			update_option( 'codup_signup_admin_apporval', 'yes' );
		} else {
			update_option( 'codup_signup_admin_apporval', 'no' );
		}

		$signup_fields = filter_input( INPUT_POST, 'signup_form_fields', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		delete_option( 'b2be_signup_field' );
		update_option( 'b2be_signup_field', $signup_fields );

		wp_die( 'true' );

	}

	/**
	 * Signup form request action.
	 */
	public function sfg_request_action() {

		$field_id = ! empty( $_POST['sfg_request'] ) ? sanitize_text_field( wp_unslash( $_POST['sfg_request'] ) ) : '';
		global $pagenow;

		$user_id = ! empty( $_POST['user_id'] ) ? sanitize_text_field( wp_unslash( $_POST['user_id'] ) ) : '';
		if ( ! empty( $user_id ) ) {

			$user = get_user_by( 'id', $user_id );
			if ( ! empty( $_REQUEST['_wpnonce'] ) ) {
				wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ) );
			}
			if ( 'sign_up_approval' == $field_id ) {

				if ( 'sign_up_approval' != get_user_meta( $user_id, 'sign_up_request', true ) ) {
					/*
					@name: sfg_signup_accepted
					@desc: Runs after sign up request is accepted by admin.
					@param: (int) $user_id User Id of the user being registered.
					@param: (object) $user Data of user being registered.
					@package: b2b-ecommerce-for-woocommerce
					@module: sign up form
					@type: action
					*/
					do_action( 'sfg_signup_accepted', $user_id, $user );
					$done = update_user_meta( $user_id, 'sign_up_request', 'sign_up_approval' );
				}
			} elseif ( 'sign_up_rejection' == $field_id ) {

				if ( 'sign_up_rejection' != get_user_meta( $user_id, 'sign_up_request', true ) ) {
					/*
					@name: sfg_signup_rejected
					@desc: Runs after sign up request is on hold by admin.
					@param: (int) $user_id User Id of the user being registered.
					@param: (object) $user Data of user being registered.
					@package: b2b-ecommerce-for-woocommerce
					@module: sign up form
					@type: action
					*/
					do_action( 'sfg_signup_rejected', $user_id, $user );
					$done = update_user_meta( $user_id, 'sign_up_request', 'sign_up_rejection' );
				}
			} elseif ( 'sign_up_on_hold' == $field_id ) {

				if ( 'sign_up_on_hold' != get_user_meta( $user_id, 'sign_up_request', true ) ) {
					/*
					@name: sfg_signup_on_hold
					@desc: Runs after sign up request is rejected by admin.
					@param: (int) $user_id User Id of the user being registered.
					@param: (object) $user Data of user being registered.
					@package: b2b-ecommerce-for-woocommerce
					@module: sign up form
					@type: action
					*/
					do_action( 'sfg_signup_on_hold', $user_id, $user );
					$done = update_user_meta( $user_id, 'sign_up_request', 'sign_up_on_hold' );
				}
			}

			if ( $done ) {
				wp_die( esc_attr__( 'Confirmation Email Sent To Customer', 'codup-wcrfq' ) );
			}
		}
	}

	/**
	 * Signup form admin approval field.
	 *
	 * @param object $user for user object.
	 */
	public static function sfg_admin_approval_field( $user ) {

		if ( 'yes' != get_option( 'codup_signup_admin_apporval' ) ) :
			return;
		endif;

		?>
		<h3><?php esc_html_e( 'Signup Approval', 'b2b-ecommerce' ); ?></h3>

		<table class="form-table">
			<tr>
				<th><label for="sign_up_approval"><?php esc_html_e( 'User Sign Up Action', 'b2b-ecommerce' ); ?></label></th>
				<td>
					<a class="button-secondary sfg_request" id="sign_up_approval" <?php echo ( get_user_meta( $user->ID, 'sign_up_request', true ) == 'sign_up_approval' ) ? 'disabled="disabled"' : ''; ?> ><?php echo ( get_user_meta( $user->ID, 'sign_up_request', true ) == 'sign_up_approval' ) ? 'Approved' : 'Approve'; ?></a>
					<a class="button-secondary sfg_request" id="sign_up_rejection" <?php echo ( get_user_meta( $user->ID, 'sign_up_request', true ) == 'sign_up_rejection' ) ? 'disabled="disabled"' : ''; ?> > <?php echo ( get_user_meta( $user->ID, 'sign_up_request', true ) == 'sign_up_rejection' ) ? 'Rejected' : 'Reject'; ?></a>
					<a class="button-secondary sfg_request" id="sign_up_on_hold" <?php echo ( get_user_meta( $user->ID, 'sign_up_request', true ) == 'sign_up_on_hold' ) ? 'disabled="disabled"' : ''; ?> > <?php echo ( get_user_meta( $user->ID, 'sign_up_request', true ) == 'sign_up_on_hold' ) ? 'On hold' : 'On Hold'; ?></a>
					<p>
						<span id="signup_success_message" ></span>
					</p>
					<?php
					if ( get_user_meta( $user->ID, 'sign_up_request', true ) == 'sign_up_approval' ) {
						$sfg_status = esc_attr__( 'Approved', 'codup-wcrfq' );
					} elseif ( get_user_meta( $user->ID, 'sign_up_request', true ) == 'sign_up_rejection' ) {
						$sfg_status = esc_attr__( 'Rejected', 'codup-wcrfq' );
					} elseif ( get_user_meta( $user->ID, 'sign_up_request', true ) == 'sign_up_on_hold' ) {
						$sfg_status = esc_attr__( 'On Hold', 'codup-wcrfq' );
					} else {
						$sfg_status = esc_attr__( 'Pending', 'codup-wcrfq' );
					}
					?>
					<p>
						<span id="sfg_user_status" ><i><?php echo esc_html__( 'Current user status:', 'codup-wcrfq' ) . ' ' . wp_kses_post( $sfg_status ); ?></i></span>
					</p>
				</td>
			</tr>
		</table>
		<?php
	}

	/**
	 * Filter Functionality.
	 *
	 * @param object $position for position object.
	 */
	public static function filter_users_by_request_status( $position ) {
		?>
		<select name="filter_by_request_status_<?php echo wp_kses_post( $position ); ?>" style="float: none;">
			<option value=""><?php esc_html_e( 'Filter By Request Status', 'b2b-ecommerce' ); ?></option>
			<option value="sign_up_pending"><?php echo esc_html__( 'Pending', 'b2b-ecommerce' ); ?></option>
			<option value="sign_up_approval"><?php echo esc_html__( 'Accepted', 'b2b-ecommerce' ); ?></option>
			<option value="sign_up_on_hold"><?php echo esc_html__( 'On Hold', 'b2b-ecommerce' ); ?></option>
			<option value="sign_up_rejection"><?php echo esc_html__( 'Rejected', 'b2b-ecommerce' ); ?></option>
		</select> 

		<input id="post-query-submit" class="button" type="submit" value="<?php esc_html_e( 'Filter', 'codup-rfq' ); ?>" name="">

		<?php
	}

	/**
	 * Filter Functionality.
	 *
	 * @param object $query query object.
	 */
	public static function filter_users_by_request_status_section( $query ) {
		global $pagenow;
		if ( is_admin() && 'users.php' == $pagenow ) {

			$top    = isset( $_GET['filter_by_request_status_top'] ) ? sanitize_text_field( wp_unslash( $_GET['filter_by_request_status_top'] ) ) : null;
			$bottom = isset( $_GET['filter_by_request_status_bottom'] ) ? sanitize_text_field( wp_unslash( $_GET['filter_by_request_status_bottom'] ) ) : null;

			if ( ! empty( $top ) || ! empty( $bottom ) ) {
				$section = ! empty( $top ) ? $top : $bottom;

				$meta_query = array(
					array(
						'key'     => 'sign_up_request',
						'value'   => $section,
						'compare' => 'LIKE',
					),
				);
				$query->set( 'meta_query', $meta_query );
			}
		}
	}

	/**
	 * Filter Functionality.
	 *
	 * @param array $column Column Array.
	 */
	public function sfg_user_table( $column ) {
		$column['approval'] = __( 'Request Status', 'codup-rfq' );
		return $column;
	}

	/**
	 * Filter Functionality.
	 *
	 * @param string $val Value.
	 * @param string $column_name Column Name.
	 * @param int    $user_id User Id.
	 */
	public function sfg_user_table_row( $val, $column_name, $user_id ) {

		if ( get_user_meta( $user_id, 'sign_up_request', true ) == 'sign_up_approval' ) {
			$sfg_status = __( 'Approved', 'b2b-ecommerce' );
		} elseif ( get_user_meta( $user_id, 'sign_up_request', true ) == 'sign_up_rejection' ) {
			$sfg_status = __( 'Rejected', 'b2b-ecommerce' );
		} elseif ( get_user_meta( $user_id, 'sign_up_request', true ) == 'sign_up_on_hold' ) {
			$sfg_status = __( 'On Hold', 'b2b-ecommerce' );
		} else {
			$sfg_status = __( 'Pending', 'b2b-ecommerce' );
		}
		return $sfg_status;
	}

}
new B2BE_Sign_Up_Form_Settings();
