<?php
/**
 * B2BE_RFQ_Quote.
 *
 * @package codupio-request-for-quote-d659b8ba1ef2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'B2BE_RFQ_Quote' ) ) {

	/**
	 * B2BE_RFQ_Quote.
	 */
	class B2BE_RFQ_Quote {

		/**
		 * Quote ID.
		 *
		 * @var int $id Quote ID.
		 */
		protected $id = 0;

		/**
		 * Data Array
		 *
		 * @var array $data Data Array.
		 */
		protected $data = array(
			'status'              => '',
			'version'             => '',
			'date_created'        => null,
			'date_modified'       => null,
			'total'               => 0,
			'total_tax'           => 0,
			'customer_id'         => 0,
			'first_name'          => '',
			'last_name'           => '',
			'email'               => '',
			'message'             => '',
			'customer_ip_address' => '',
			'customer_user_agent' => '',
			'customer_note'       => '',
			'cart_hash'           => '',
		);
		/**
		 * Default Data Array
		 *
		 * @var array $default_data Default Data Array.
		 */
		protected $default_data = array();

		/**
		 * Default Data Array
		 *
		 * @var array $default_data Default Data Array.
		 */
		protected $object_read = false;

		/**
		 * Changes
		 *
		 * @var array $changes Changes.
		 */
		protected $changes = array();

		/**
		 * Items Array
		 *
		 * @var array $items Items Array.
		 */
		protected $items = array();

		/**
		 * Quote Object
		 *
		 * @param object $quote Quote Object.
		 */
		public function __construct( $quote ) {

			if ( is_numeric( $quote ) && $quote > 0 ) {
				$this->set_id( $quote );
			} elseif ( $quote instanceof self ) {
				$this->set_id( $quote->get_id() );
			} elseif ( ! empty( $quote->ID ) ) {
				$this->set_id( $quote->ID );
			}
			if ( $this->get_id() ) {
				$this->load_meta_data();
			}
		}

		/**
		 * Function Set Defaults.
		 */
		public function set_defaults() {
			$this->data = $this->default_data;
		}

		/**
		 * Function get Id.
		 */
		public function get_id() {
			return $this->id;
		}

		/**
		 * Function Set Id.
		 *
		 * @param int $id Id To be Set.
		 */
		public function set_id( $id ) {
			$this->id = absint( $id );
		}

		/**
		 * Function get the property.
		 *
		 * @param string $prop get the property .
		 *
		 * Get the Context.
		 * @param string $context get the Context.
		 *
		 * @return $value
		 */
		public function get_prop( $prop, $context = 'view' ) {
			$value = null;
			if ( array_key_exists( $prop, $this->data ) ) {
				$value = array_key_exists( $prop, $this->changes ) ? $this->changes[ $prop ] : $this->data[ $prop ];

				if ( 'view' === $context ) {
					$value = apply_filters( 'cwrfq_quote_get_' . $prop, $value, $this );
				}
			}

			return $value;
		}

		/**
		 * Function Set prop.
		 * Get the prop.
		 *
		 * @param string $prop get the prop.
		 *
		 * Get the value.
		 * @param string $value get the value.
		 */
		protected function set_prop( $prop, $value ) {
			if ( array_key_exists( $prop, $this->data ) ) {

				if ( true === $this->object_read ) {

					if ( $value !== $this->data[ $prop ] || array_key_exists( $prop, $this->changes ) ) {
						$this->changes[ $prop ] = $value;
					}
				} else {

					$this->data[ $prop ] = $value;
				}
			}
		}

		/**
		 * Return the order statuses without wc- internal prefix.
		 *
		 * @param  string $context View or edit context.
		 * @return string
		 */
		public function get_status( $context = 'view' ) {
			$status = $this->get_prop( 'status', $context );

			if ( empty( $status ) && 'view' === $context ) {
				/*
				@name: b2be_default_quote_status
				@desc: Modify default quote status.
				@param: (string) $quote_status default quote status of Quotes.
				@package: b2b-ecommerce-for-woocommerce
				@module: request for quote
				@type: filter
				*/
				$status = apply_filters( 'b2be_default_quote_status', 'requested' );
			}
			return $status;
		}

		/**
		 * Set order status.
		 *
		 * @since 3.0.0
		 * @param string $new_status Status to change the order to. No internal wc- prefix is required.
		 * @return array details of change
		 */
		public function set_status( $new_status ) {
			$old_status = $this->get_status();

			// If setting the status, ensure it's set to a valid status.
			if ( true === $this->object_read ) {
				// Only allow valid new status.
				if ( ! in_array( $new_status, $this->get_valid_statuses(), true ) && 'trash' !== $new_status ) {
					$new_status = 'requested';
				}

				// If the old status is set but unknown (e.g. draft) assume its pending for action usage.
				if ( $old_status && ! in_array( $old_status, $this->get_valid_statuses(), true ) && 'trash' !== $old_status ) {
					$old_status = 'requested';
				}
			}

			$this->set_prop( 'status', $new_status );

			return array(
				'from' => $old_status,
				'to'   => $new_status,
			);
		}

		/**
		 * Set order status.
		 *
		 * Get the prop.
		 *
		 * @param string $prop get the prop.
		 *
		 * Get the value.
		 * @param string $value get the value.
		 */
		public function set_date( $prop, $value ) {
			try {
				if ( empty( $value ) ) {
					$this->set_prop( $prop, null );
					return;
				}

				if ( is_a( $value, 'WC_DateTime' ) ) {
					$datetime = $value;
				} elseif ( is_numeric( $value ) ) {
					// Timestamps are handled as UTC timestamps in all cases.
					$datetime = new WC_DateTime( "@{$value}", new DateTimeZone( 'UTC' ) );
				} else {
					// Strings are defined in local WP timezone. Convert to UTC.
					if ( 1 === preg_match( '/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2})(Z|((-|\+)\d{2}:\d{2}))$/', $value, $date_bits ) ) {
						$offset    = ! empty( $date_bits[7] ) ? iso8601_timezone_to_offset( $date_bits[7] ) : wc_timezone_offset();
						$timestamp = gmmktime( $date_bits[4], $date_bits[5], $date_bits[6], $date_bits[2], $date_bits[3], $date_bits[1] ) - $offset;
					} else {
						$timestamp = wc_string_to_timestamp( get_gmt_from_date( gmdate( 'Y-m-d H:i:s', wc_string_to_timestamp( $value ) ) ) );
					}
					$datetime = new WC_DateTime( "@{$timestamp}", new DateTimeZone( 'UTC' ) );
				}

				// Set local timezone or offset.
				if ( get_option( 'timezone_string' ) ) {
					$datetime->setTimezone( new DateTimeZone( wc_timezone_string() ) );
				} else {
					$datetime->set_utc_offset( wc_timezone_offset() );
				}

				$this->set_prop( $prop, $datetime );
			} catch ( Exception $e ) {
				echo esc_attr( $e );// No Neeed To Handle the exception.
			}
		}

		/**
		 * Get date_created.
		 *
		 * @param  string $context View or edit context.
		 * @return WC_DateTime|NULL object if the date is set or null if there is no date.
		 */
		public function get_date_created( $context = 'view' ) {
			return $this->get_prop( 'date_created', $context );
		}
		/**
		 * Get date_created.
		 *
		 * @param  string $context View or edit context.
		 * @return WC_DateTime|NULL object if the date is set or null if there is no date.
		 */
		public function get_date_modified( $context = 'view' ) {
			return $this->get_prop( 'date_modified', $context );
		}
		/**
		 * Gets the order number for display (by default, order ID).
		 *
		 * @return string
		 */
		public function get_quote_number() {
			/*
			@name: b2be_quote_number
			@desc: Modify quote number of rfq quotes.
			@param: (string) $quote_id Id Of Quote.
			@param: (object) $quote Quote Object.
			@package: b2b-ecommerce-for-woocommerce
			@module: request for quote
			@type: filter
			*/
			return (string) apply_filters( 'b2be_quote_number', $this->get_id(), $this );
		}

		/**
		 * Get The Quotes
		 */
		public function wc_get_quotes() {
			$user_id = get_current_user_id();
			$args    = array(
				'meta_query' => array(
					array(
						'key'     => '_customer_user',
						'value'   => $user_id,
						'compare' => '=',
					),
				),
			);
			$query   = new WP_Query( $args );
		}

		/**
		 * Get The Formatted Quote Total
		 */
		public function get_formatted_quote_total() {
			$items = get_post_meta( $this->get_id(), 'items', true );
			$total = 0;
			if ( $items ) {
				foreach ( $items as $item ) {
					$total += $item['total'];
				}
				return ( $total );
			}
			return false;
		}

		/**
		 * Get Item Count
		 */
		public function get_item_count() {
			$items = get_post_meta( $this->get_id(), 'items', true );
			if ( $items ) {
				$total_items = 0;
				foreach ( $items as $item ) {
					$total_items += $item['qty'];
				}
				return $total_items;
			}
			return false;
		}

		/**
		 * Get The Quote Items
		 */
		public function get_quote_items() {
			$items = get_post_meta( $this->get_id(), 'items', true );
			if ( $items ) {

				return $items;
			}
			return false;
		}

		/**
		 * Get The customer notes
		 */
		public function get_customer_note() {
			return get_post_meta( $this->get_id(), 'message', true );
		}

		/**
		 * Get The Formatted Quote Total
		 */
		protected function get_valid_statuses() {
			return array_keys( wc_get_quote_statuses() );
		}

		/**
		 * Get The customer Details
		 */
		public function get_customer_details() {
			return array(
				'first_name' => $this->data['first_name'],
				'last_name'  => $this->data['last_name'],
				'email'      => $this->data['email'],
				'message'    => $this->data['message'],
			);
		}

		/**
		 * Get The View Quote url
		 */
		public function get_view_quote_url() {
			/*
			@name: cwrfq_get_view_quote_url.
			@desc: Modify view quote url.
			@param: (string) $view_quote_endpoint Rfq View Quote Endpoint.
			@param: (object) $quote Quote Object.
			@package: b2b-ecommerce-for-woocommerce
			@module: request for quote
			@type: filter
			*/
			return apply_filters( 'cwrfq_get_view_quote_url', wc_get_endpoint_url( CWRFQ_VIEW_QUOTE_ENDPOINT, $this->get_id(), wc_get_page_permalink( 'myaccount' ) ), $this );
		}

		/**
		 * Save Quote
		 */
		public function save() {

			$changes = array();

			$post_data = array(
				'post_date'         => gmdate( 'Y-m-d H:i:s', $this->get_date_created( 'edit' )->getOffsetTimestamp() ),
				'post_date_gmt'     => gmdate( 'Y-m-d H:i:s', $this->get_date_created( 'edit' )->getTimestamp() ),
				'post_status'       => $this->get_status( $this ),
				'post_modified'     => isset( $changes['date_modified'] ) ? gmdate( 'Y-m-d H:i:s', $this->get_date_modified( 'edit' )->getOffsetTimestamp() ) : current_time( 'mysql' ),
				'post_modified_gmt' => isset( $changes['date_modified'] ) ? gmdate( 'Y-m-d H:i:s', $this->get_date_modified( 'edit' )->getTimestamp() ) : current_time( 'mysql', 1 ),

			);
			if ( doing_action( 'save_post' ) ) {
				$GLOBALS['wpdb']->update( $GLOBALS['wpdb']->posts, $post_data, array( 'ID' => $this->get_id() ) );
				clean_post_cache( $this->get_id() );
			} else {
				wp_update_post( array_merge( array( 'ID' => $this->get_id() ), $post_data ) );
			}
			return $this->get_id();
		}

		/**
		 * Submit Quote
		 */
		public function submit_quote() {

			$this->set_status( 'quoted' );
			return $this->save();
		}

		/**
		 * Load Meta Data
		 */
		protected function load_meta_data() {
			$id = $this->get_id();
			$q  = get_post( $id );
			$this->set_status( $q->post_status );

			$metas = array( 'first_name', 'last_name', 'email', 'message' );
			foreach ( $metas as $meta ) {
				$this->set_prop( $meta, get_post_meta( $id, $meta, true ) );
			}
			$this->set_date( 'date_created', $q->post_date );
			$this->set_date( 'date_modified', $q->post_modified );
		}

		/**
		 * Get requester Email
		 */
		public function get_requester_email() {
			return $this->data['email'];
		}

		/**
		 * Get Requester First Name
		 */
		public function get_requester_first_name() {
			return $this->data['first_name'];
		}

		/**
		 * Get Requester Last Name
		 */
		public function get_requester_last_name() {
			return $this->data['last_name'];
		}

		/**
		 * Get Formtted Full Name
		 */
		public function get_formatted_full_name() {
			/* translators: 1: first name 2: last name */
			return sprintf( _x( '%1$s %2$s', 'full name', 'b2b-ecommerce' ), $this->get_requester_first_name(), $this->get_requester_last_name() );
		}

		/**
		 * Get Item Total
		 * Item Array.
		 *
		 * @param array $item item Array.
		 */
		public function get_item_total( $item ) {
			return $item->total;
		}

		/**
		 * Get Item Sub Total
		 * Item Array.
		 *
		 * @param array $item item Array.
		 */
		public function get_item_subtotal( $item ) {
			return $item->subtotal;
		}

	}

}
