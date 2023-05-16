<?php
/**
 * Rfq Stats Card.
 *
 * @package b2b-ecommerce-for-woocomerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use \Automattic\WooCommerce\Admin\Notes\Note;

const NOTE_NAME = 'rfq_stats';

/**
 * B2BE_Rfq_Stats_Card class.
 */
class B2BE_Rfq_Stats_Card {

	/**
	 * Constructor.
	 */
	public function __construct() {

		add_action( 'admin_init', array( $this, 'add_activity_panel_inbox_welcome_note' ) );

		add_action( 'wp_ajax_wcb2be_ajax_card_rfq_stats', array( $this, 'wcb2be_ajax_card_rfq_stats' ) );
		add_action( 'wp_ajax_noprev_wcb2be_ajax_card_rfq_stats', array( $this, 'wcb2be_ajax_card_rfq_stats' ) );

	}

	/**
	 * Function for ajax callback for Html Rfq Stats.
	 */
	public function wcb2be_ajax_card_rfq_stats() {

		include_once 'views/stats-card-html.php';

		wp_die();
	}

	/**
	 * Function for Add new Card/Note.
	 */
	public function add_activity_panel_inbox_welcome_note() {

		// Load the Admin Notes from the WooCommerce Data Store.
		$data_store = WC_Data_Store::load( 'admin-note' );

		// Check for existing notes that match our note name and content data.
		// This ensures we don't create a duplicate note.
		$note_ids = $data_store->get_notes_with_name( NOTE_NAME );

		if ( $note_ids ) {
			return;
		}

		// Instantiate a new Note object.
		$note = new Note();

		// Set our note's title.
		$note->set_title( 'RFQ Stats overview' );

		// Set our note's content.
		$note->set_content(
			"<p><a href='#' name='wcb2be_rfq_stats_card'>" . __( 'Loading Stats', 'b2b-ecommerce' ) . ' ....</a></p>'
		);

		$note->set_content_data( (object) array() );

		$note->set_type( Note::E_WC_ADMIN_NOTE_INFORMATIONAL );

		$note->set_layout( 'plain' );

		$note->set_image( '' );

		$note->set_source( 'inbox-note-example' );
		$note->set_name( NOTE_NAME );

		// Save the note to lock in our changes.
		$note->save();
	}

}

return new B2BE_Rfq_Stats_Card();
