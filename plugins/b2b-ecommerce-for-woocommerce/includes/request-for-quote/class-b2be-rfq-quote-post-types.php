<?php
/**
 * Post Types
 *
 * Registers post types and taxonomies.
 *
 * @package codupio-request-for-quote-d659b8ba1ef2
 */

defined( 'ABSPATH' ) || exit;

/**
 * Post types Class.
 */
class B2BE_RFQ_Quote_Post_Types {

	/**
	 * Hook in methods.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_quote_post_type' ), 0 );
		add_action( 'init', array( __CLASS__, 'register_post_status' ), 9 );
	}

	/**
	 * Register core post types.
	 */
	public static function register_quote_post_type() {
			add_rewrite_endpoint( CWRFQ_QUOTE_ENDPOINT, EP_PAGES );
			$requested_quote_count = b2be_get_requested_quote_count();
			$labels                = array(
				'name'               => _x( 'Quotes', 'post type general name', 'b2b-ecommerce' ),
				'singular_name'      => _x( 'Quote', 'post type singular name', 'b2b-ecommerce' ),
				'menu_name'          => _x( 'Quotes', 'admin menu', 'b2b-ecommerce' ),
				'name_admin_bar'     => _x( 'Quote', 'add new on admin bar', 'b2b-ecommerce' ),
				'add_new'            => _x( 'Add New', 'quote', 'b2b-ecommerce' ),
				'add_new_item'       => __( 'Add New Quote', 'b2b-ecommerce' ),
				'new_item'           => __( 'New Quote', 'b2b-ecommerce' ),
				'edit_item'          => __( 'Edit Quote', 'b2b-ecommerce' ),
				'view_item'          => __( 'View Quote', 'b2b-ecommerce' ),
				'all_items'          => $requested_quote_count ? 'All Quotes <span class="awaiting-mod">' . $requested_quote_count . '</span>' : __( 'All Quotes', 'b2b-ecommerce' ),
				'search_items'       => __( 'Search Quotes', 'b2b-ecommerce' ),
				'parent_item_colon'  => __( 'Parent Quotes:', 'b2b-ecommerce' ),
				'not_found'          => __( 'No quotes found.', 'b2b-ecommerce' ),
				'not_found_in_trash' => __( 'No quotes found in Trash.', 'b2b-ecommerce' ),
			);
			$args                  = array(
				'labels'             => $labels,
				'description'        => __( 'Quotes for WooCommerce products.', 'b2b-ecommerce' ),
				'public'             => true,
				'publicly_queryable' => true,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'query_var'          => true,
				'rewrite'            => array( 'slug' => 'quote' ),
				'capability_type'    => 'post',
				'has_archive'        => true,
				'hierarchical'       => false,
				'menu_position'      => null,
				'menu_icon'          => 'dashicons-media-text',
				'supports'           => array( 'title', 'author', 'comments' ),
			);
			register_post_type( 'quote', $args );
	}

	/**
	 * Register our custom post statuses, used for quote status.
	 */
	public static function register_post_status() {
			$quote_statuses = apply_filters(
				'wcrfq_register_quote_post_statuses',
				array(
					'requested'     => array(
						'label'                     => _x( 'Requested', 'Ouote status', 'b2b-ecommerce' ),
						'public'                    => true,
						'exclude_from_search'       => false,
						'show_in_admin_all_list'    => true,
						'show_in_admin_status_list' => true,
						'label_count'               => /* translators: %s: slug */_n_noop( 'Requested <span class="count">(%s)</span>', 'Requested <span class="count">(%s)</span>', 'b2b-ecommerce' ),
					),
					'quoted'        => array(
						'label'                     => _x( 'Quoted', 'Quote status', 'b2b-ecommerce' ),
						'public'                    => true,
						'exclude_from_search'       => false,
						'show_in_admin_all_list'    => true,
						'show_in_admin_status_list' => true,

						'label_count'               => /* translators: %s: slug */_n_noop( 'Quoted <span class="count">(%s)</span>', 'Quoted <span class="count">(%s)</span>', 'b2b-ecommerce' ),
					),
					'accepted'      => array(
						'label'                     => _x( 'Accepted', 'Quote status', 'b2b-ecommerce' ),
						'public'                    => true,
						'exclude_from_search'       => false,
						'show_in_admin_all_list'    => true,
						'show_in_admin_status_list' => true,
						'label_count'               => /* translators: %s: slug */_n_noop( 'Accepted <span class="count">(%s)</span>', 'Accepted <span class="count">(%s)</span>', 'b2b-ecommerce' ),
					),
					'need-revision' => array(
						'label'                     => _x( 'Need Revision', 'Quote status', 'b2b-ecommerce' ),
						'public'                    => true,
						'exclude_from_search'       => false,
						'show_in_admin_all_list'    => true,
						'show_in_admin_status_list' => true,
						'label_count'               => /* translators: %s: slug */_n_noop( 'Need Revision <span class="count">(%s)</span>', 'Need Revision <span class="count">(%s)</span>', 'b2b-ecommerce' ),
					),
					'rejected'      => array(
						'label'                     => _x( 'Rejected', 'Quote status', 'b2b-ecommerce' ),
						'public'                    => true,
						'exclude_from_search'       => false,
						'show_in_admin_all_list'    => true,
						'show_in_admin_status_list' => true,
						'label_count'               => /* translators: %s: slug */_n_noop( 'Rejected <span class="count">(%s)</span>', 'Rejected <span class="count">(%s)</span>', 'b2b-ecommerce' ),
					),

				)
			);

		foreach ( $quote_statuses as $quote_status => $values ) {
					register_post_status( $quote_status, $values );
		}
	}

}

B2BE_RFQ_Quote_Post_Types::init();
