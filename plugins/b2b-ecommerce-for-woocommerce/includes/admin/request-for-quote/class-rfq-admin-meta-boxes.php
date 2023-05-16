<?php
/**
 * Admin Meta Boxes.
 *
 * @package b2b-ecommerce-for-woocomerce
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Admin_Meta_Boxes.
 */
class RFQ_Admin_Meta_Boxes {

	/**
	 * Saved Meta Box Variable.
	 *
	 * @var bool $saved_meta_boxes Saved Meta Box Variable.
	 */
	private static $saved_meta_boxes = false;

	/**
	 * Function Construct
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 30 );
		add_action( 'save_post', array( $this, 'save_meta_boxes' ), 1, 2 );
		add_action( 'cwrfq_process_quote_meta', 'RFQ_Admin_Meta_Boxes::save', 40, 2 );
	}

	/**
	 * Add WC Meta boxes.
	 */
	public function add_meta_boxes() {
		add_meta_box( 'wcrfq-quote-data', sprintf( __( "Quote's data", 'b2b-ecommerce' ) ), 'RFQ_Admin_Meta_Boxes::quote_data_output', 'quote', 'normal', 'high' );
		add_meta_box( 'wcrfq-quote-item', sprintf( __( 'Items', 'b2b-ecommerce' ) ), 'RFQ_Meta_Box_Quote_Items::output', 'quote', 'normal', 'high' );
		add_meta_box( 'wcrfq-quote-comments', sprintf( __( 'Messages', 'b2b-ecommerce' ) ), 'RFQ_Meta_Box_Quote_Comments::output', 'quote', 'normal', 'high' );
	}
	/**
	 * Quote Data Output.
	 *
	 * @param object $post Post Object.
	 */
	public static function quote_data_output( $post ) {

		$quote            = wc_get_quote( $post->ID );
		$customer_details = $quote->get_customer_details();
		wp_nonce_field( 'rfq_save_meta_box', 'save_meta_box_nonce' );
		?>
		<style type="text/css">
			#post-body-content, #titlediv { display:none }
			a.page-title-action { display:none !important }
		</style>
		<div id="quote_data" class="panel woocommerce-quote-data" >
			<?php /* translators: %s: discount amount */ ?>
			<h2><?php printf( esc_html__( 'Quote #%1$s details', 'b2b-ecommerce' ), esc_html( $quote->get_quote_number() ) ); ?></h2>
			<div class="quote_data_column_container">
				<div class="quote_data_column">
					<h3><?php esc_html_e( 'General', 'b2b-ecommerce' ); ?></h3>
					<p class="form-field form-field-wide">
							<label for="quote_date"><?php esc_html_e( 'Date created:', 'b2b-ecommerce' ); ?></label>
							<input type="text" class="date-picker" name="quote_date" maxlength="10" value="<?php echo esc_attr( date_i18n( 'Y-m-d', strtotime( $post->post_date ) ) ); ?>" pattern="<?php echo esc_attr( apply_filters( 'b2be_date_input_html_pattern', '[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])' ) ); ?>" />@
							&lrm;
							<input type="number" class="hour" placeholder="<?php esc_attr_e( 'h', 'b2b-ecommerce' ); ?>" name="quote_date_hour" min="0" max="23" step="1" value="<?php echo esc_attr( date_i18n( 'H', strtotime( $post->post_date ) ) ); ?>" pattern="([01]?[0-9]{1}|2[0-3]{1})" />:
							<input type="number" class="minute" placeholder="<?php esc_attr_e( 'm', 'b2b-ecommerce' ); ?>" name="quote_date_minute" min="0" max="59" step="1" value="<?php echo esc_attr( date_i18n( 'i', strtotime( $post->post_date ) ) ); ?>" pattern="[0-5]{1}[0-9]{1}" />
							<input type="hidden" name="quote_date_second" value="<?php echo esc_attr( date_i18n( 's', strtotime( $post->post_date ) ) ); ?>" />
					</p>
					<p class="form-field form-field-wide wc-quote-status">
					<label for="quote_status">
						<strong><?php esc_html_e( 'Status:', 'b2b-ecommerce' ); ?></strong>
						<span><?php echo wp_kses_post( ucfirst( $quote->get_status( 'edit' ) ) ); ?></span>
					</label>
					</p>
					<?php do_action( 'b2be_rfq_quote_admin_general_tab__after', $quote ); ?>
				</div>
				<div class="quote_data_column">
					<h3><?php esc_html_e( 'Details', 'b2b-ecommerce' ); ?></h3>
					<?php
					if ( isset( $customer_details['email'] ) ) {
						$rfq_customer_email       = wp_kses_post( $customer_details['email'] );
						$rfq_customer             = get_user_by( 'email', $rfq_customer_email );
						$rfq_customerprofile_link = get_edit_user_link( $rfq_customer->ID );
					}
					?>
					 
					<div class="details">
						<p>
							 <?php echo wp_kses_post( $customer_details['first_name'] ) . ' ' . wp_kses_post( $customer_details['last_name'] ); ?>
						</p>
						<p>
							<strong><?php esc_html_e( 'Email address:', 'b2b-ecommerce' ); ?></strong>
							<a href="<?php echo wp_kses_post( $rfq_customerprofile_link ); ?>"><?php echo wp_kses_post( $customer_details['email'] ); ?></a>
						</p>
						<p>
							<strong><?php esc_html_e( 'Message:', 'b2b-ecommerce' ); ?></strong>
							<?php echo wp_kses_post( $customer_details['message'] ); ?>
						</p>
					</div>
				</div>
				
				
			</div>
			<div class="clear"></div>
		</div>
		<?php
	}
	/**
	 * Check if we're saving, the trigger an action based on the post type.
	 *
	 * @param  int    $post_id Post ID.
	 * @param  object $post Post object.
	 */
	public function save_meta_boxes( $post_id, $post ) {

		if ( isset( $_POST['rfq_settings_nonce'] ) ) {
			$nonce = sanitize_text_field( wp_unslash( $_POST['rfq_settings_nonce'] ) );
			if ( ! wp_verify_nonce( $nonce, 'rfq_cat_settings' ) ) {
				return;
			}
		}

		if ( empty( $post_id ) || empty( $post ) || self::$saved_meta_boxes ) {
			return;
		}

		if ( 'quote' != $post->post_type ) {
			return;
		}
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || is_int( wp_is_post_revision( $post ) ) || is_int( wp_is_post_autosave( $post ) ) ) {
			return;
		}
		if ( empty( $_POST['post_ID'] ) || absint( $_POST['post_ID'] ) !== $post_id ) {
			return;
		}
		// Check user has permission to edit.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
		}
		self::$saved_meta_boxes = true;

		/*
			@name: cwrfq_process_quote_meta
			@desc: Runs after processing Quote meta.
			@param: (int) $quote_id Quote Id of current Quote.
			@param: (object) $quote Object of current quote.
			@package: b2b-ecommerce-for-woocommerce
			@module: request for quote
			@type: action
		*/
		do_action( 'cwrfq_process_quote_meta', $quote_id, $quote );
	}
	/**
	 * Save meta box data.
	 *
	 * @param int $quote_id Quote ID.
	 */
	public static function save( $quote_id ) {
		// Get quote object.
		if ( isset( $_POST['save_meta_box_nonce'] ) ) {
			$nonce = sanitize_text_field( wp_unslash( $_POST['save_meta_box_nonce'] ) );
			if ( ! wp_verify_nonce( $nonce, 'rfq_save_meta_box' ) ) {
				return;
			}
		}
		$quote = wc_get_quote( $quote_id );
		if ( isset( $_POST['quote_status'] ) ) {
			$quote->set_status( wc_clean( sanitize_text_field( wp_unslash( $_POST['quote_status'] ) ) ), '', true );
		}
		$quote->save();
	}


}
new RFQ_Admin_Meta_Boxes();
