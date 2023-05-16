<?php
/**
 * WC RFQ.
 *
 * @package b2b-ecommerce-for-woocomerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'B2BE_RFQ' ) ) {
	/**
	 * Class B2BE_RFQ.
	 */
	class B2BE_RFQ {
		/**
		 * Cart Variable.
		 *
		 * @var string $cart Cart Variable.
		 */
		public $cart;

		/**
		 * Construct.
		 */
		public function __construct() {
			B2BE_RFQ_Settings::init();
			add_action( 'woocommerce_init', array( $this, 'woocommerce_init' ) );
			add_action( 'wp_loaded', array( $this, 'load_rfq_cart_from_session' ), 11 );
			if ( ( CWRFQ_PLUGIN_NAME !== null ) && is_admin() ) {
				add_action( 'woocommerce_init', array( $this, 'cwrfq_create_pages' ), 10, 1 );
			}
			add_action( 'woocommerce_before_add_to_cart_quantity', array( $this, 'hide_add_cart' ) );
			add_action( 'woocommerce_after_shop_loop_item_title', array( $this, 'hide_add_cart_loop' ) );
			add_action( 'woocommerce_after_shop_loop_item', array( $this, 'hide_add_rfq_loop' ), 30 );
			add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'add_to_rfq_button' ), 30 );
			add_action( 'woocommerce_rfq_is_empty', array( $this, 'empty_cart_message' ), 10 );
			add_shortcode( 'codup_rfq', __CLASS__ . '::rfq' );
			add_filter( 'woocommerce_account_menu_items', array( $this, 'quotes_menu_item' ), 10 );
			add_action( 'woocommerce_account_' . $this->get_endpoint() . '_endpoint', array( $this, 'endpoint_content' ) );
			add_action( 'init', array( $this, 'add_view_quote_endpoint' ) );
			add_filter( 'woocommerce_get_query_vars', array( $this, 'add_view_quote_query_vars' ) );
			add_action( 'woocommerce_account_' . CWRFQ_VIEW_QUOTE_ENDPOINT . '_endpoint', array( $this, 'woocommerce_account_view_quote' ) );
			add_filter( 'woocommerce_endpoint_' . CWRFQ_VIEW_QUOTE_ENDPOINT . '_title', array( $this, 'view_quote_page_title' ), 10, 2 );
			add_filter( 'woocommerce_account_menu_items', array( $this, 'rearrange_logout_menu' ), 10, 1 );
			add_action( 'cwrfq_view_quote', array( $this, 'woocommerce_quote_details_table' ), 10 );
			add_action( 'wp_ajax_codup_rfq_submit_quote', array( $this, 'rfq_submit_quote' ) );

		}

		/**
		 * Hide Add to cart button if disabled.
		 *
		 * @param string $is_purchasable Is Poduct Purchasable.
		 * @param object $product product Object.
		 * @return string
		 */
		public function hide_add_to_cart( $is_purchasable, $product ) {
			if ( empty( $product->get_price() ) ) {
				$is_purchasable = 1;
			}
			$disabled = $this->can_add_to_cart( $product );
			return ( $disabled ) ? false : $is_purchasable;
		}

		/**
		 * Check if add to cart is disabled from product, or from its categories or globally.
		 *
		 * @param type $product Product Object.
		 * @return boolean
		 */
		public function can_add_to_cart( $product ) {
			$product_disabled = get_post_meta( $product->get_id(), 'disable_add_to_cart', true ); // Applying product level setting.
			if ( is_user_logged_in() ) {

				$role_disabled         = 'no';
				$curennt_userrole_name = b2be_get_formated_userrole_name();
				$role_id               = $this->post_exists( $curennt_userrole_name );

				if ( $role_id ) {
					$role_disabled = get_post_meta( $role_id, 'disable_add_to_cart', true ); // Applying role level setting.
				}

				if ( 'yes' == $role_disabled ) {
					return true;
				}
			}

			if ( 'yes' == $product_disabled ) {
				return true;
			}
			return false;

		}

		/**
		 * Show 'ADD TO RFQ' button on single product page.
		 *
		 * @global object $product Product Object.
		 * @return null
		 */
		public function add_to_rfq_button() {
			global $product;
			$enabled            = $this->can_add_to_rfq( $product );
			$add_to_rfq_btn_txt = get_option( 'codup-rfq_add_to_rfq_button_text' );

			if ( ! $enabled ) {
				return;
			}
			?>
			<button type="submit" name="add-to-rfq" value="<?php echo esc_attr( $product->get_id() ); ?>" class="single_add_to_rfq_button button alt"><?php echo wp_kses_post( ( '' !== $add_to_rfq_btn_txt ) ? esc_html( $add_to_rfq_btn_txt ) : esc_html_e( 'Add To RFQ', 'codup-wcrfq' ) ); ?></button>        
			<?php
		}

		/**
		 *  Check if add to RFQ is enabled from product, or from product's categories or globally.
		 *
		 * @param object $product Product Object.
		 * @return boolean
		 */
		public function can_add_to_rfq( $product ) {

			$product_enabled = get_post_meta( $product->get_id(), 'enable_rfq', true ); // Applying product level setting.

			if ( is_user_logged_in() ) {

				$role_disabled         = 'no';
				$curennt_userrole_name = b2be_get_formated_userrole_name();
				$role_id               = $this->post_exists( $curennt_userrole_name );
				if ( $role_id ) {
					$role_disabled = get_post_meta( $role_id, 'enable_rfq', true ); // Applying role level setting.
				}

				if ( 'yes' == $role_disabled ) {
					return apply_filters( 'b2be_enable_rfq', true );
				}
			}

			if ( 'yes' == $product_enabled ) {
				return apply_filters( 'b2be_enable_rfq', true );
			}
			return apply_filters( 'b2be_enable_rfq', false );

		}

		/**
		 * Display Empty Crat Message.
		 */
		public function empty_cart_message() {
			/*
			@name: wc_empty_rfq_cart_message
			@desc: Modify empty rfq cart message.
			@param: (string) $message Empty Rfq cart message.
			@package: b2b-ecommerce-for-woocommerce
			@module: request for quote
			@type: filter
			*/
			echo '<p class="cart-empty woocommerce-info">' . wp_kses_post( apply_filters( 'wc_empty_rfq_cart_message', __( 'Your RFQ is currently empty.', 'b2b-ecommerce' ) ) ) . '</p>';
		}
		/**
		 * Create Admin Side Pages For Rfq.
		 */
		public function cwrfq_create_pages() {

			$thankyou_page_template = ''; // ex. template-custom.php. Leave blank if you don't want a custom page template.
			$rfq_page_template      = ''; // ex. template-custom.php. Leave blank if you don't want a custom page template.

			$thankyou_page_check = get_page_by_title( __( 'Thank You', 'codup-wcrfq' ) );
			$rfq_page_check      = get_page_by_title( __( 'RFQ', 'codup-wcrfq' ) );
			$thankyou_page       = array(
				'post_type'    => 'page',
				'post_title'   => 'Thank You',
				'post_content' => 'Thank you for submitting the RFQ from us',
				'post_status'  => 'publish',
				'post_author'  => 1,
			);
			$rfq_page            = array(
				'post_type'    => 'page',
				'post_title'   => __( 'RFQ', 'codup-wcrfq' ),
				'post_content' => '<!-- wp:shortcode -->[codup_rfq]<!-- /wp:shortcode -->',
				'post_status'  => 'publish',
				'post_author'  => 1,
			);
			if ( ! isset( $thankyou_page_check->ID ) ) {
				$thankyou_page_id = wp_insert_post( $thankyou_page );
			}
			if ( ! isset( $rfq_page_check->ID ) ) {
				$rfq_page_id = wp_insert_post( $rfq_page );
			}

		}
		/**
		 * Rfq Cart Shortcode.
		 *
		 * @param string $atts Attributes.
		 */
		public static function rfq( $atts ) {
			return WC_Shortcodes::shortcode_wrapper( array( 'B2BE_RFQ_Cart_ShortCode', 'output' ), $atts );
		}
		/**
		 * Rfq Cart Template.
		 */
		public function woocommerce_init() {
			global $woocommerce;

			if ( ! is_admin() || defined( 'DOING_AJAX' ) ) {

				include CWRFQ_PLUGIN_DIR . '/includes/request-for-quote/class-b2be-rfq-cart.php';
				$new_cart   = new B2BE_RFQ_Cart();
				$this->cart = $new_cart;
				WC()->rfq   = $this->cart;

			}
		}
		/**
		 * Load Cart Data.
		 */
		public function load_rfq_cart_from_session() {

			if ( is_admin() ) {
				return;
			}
			if ( ! WC()->session ) {
				return;
			}
			$cart = WC()->session->get( 'rfq', null );

			$cart_contents = array();
			if ( null == $cart ) {
				return;
			}
			foreach ( $cart as $key => $values ) {
				if ( ! is_customize_preview() && 'customize-preview' === $key ) {
					continue;
				}

				$product = wc_get_product( $values['variation_id'] ? $values['variation_id'] : $values['product_id'] );

				if ( empty( $product ) || ! $product->exists() || 0 >= $values['quantity'] ) {
					continue;
				}

				/**
				 * Allow 3rd parties to validate this item before it's added to cart and add their own notices.
				 *
				 * @since 3.6.0
				 *
				 * @param bool $remove_cart_item_from_session If true, the item will not be added to the cart. Default: false.
				 * @param string $key Cart item key.
				 * @param array $values Cart item values e.g. quantity and product_id.
				 */
				if ( apply_filters( 'b2be_pre_remove_rfq_item_from_session', false, $key, $values ) ) {
					$update_cart_session = true;

					/*
					@name: b2be_remove_rfq_item_from_session
					@desc: Runs before removing rfq product in woocommerce session.
					@param: (int) $cart_item_key Current Cart item key.
					@param: (object) $cart Current Cart Object.
					@package: b2b-ecommerce-for-woocommerce
					@module: request for quote
					@type: action
					*/
					do_action( 'b2be_remove_rfq_item_from_session', $key, $values );
				} elseif ( ! empty( $values['data_hash'] ) && ! hash_equals( $values['data_hash'], wc_get_cart_item_data_hash( $product ) ) ) { // phpcs:ignore PHPCompatibility.PHP.NewFunctions.hash_equalsFound.
					$update_cart_session = true;
					/* translators: %1$s: product name. %2$s product permalink. */
					wc_add_notice( sprintf( __( '%1$s has been removed from your cart because it has since been modified. You can add it back to your cart <a href="%2$s">here</a>.', 'b2b-ecommerce' ), $product->get_name(), $product->get_permalink() ), 'notice' );
					do_action( 'b2be_remove_rfq_item_from_session', $key, $values );

				} else {
					// Put session data into array. Run through filter so other plugins can load their own session data.
					$session_data = array_merge(
						$values,
						array(
							'data' => $product,
						)
					);

					$cart_contents[ $key ] = apply_filters( 'b2be_get_rfq_item_from_session', $session_data, $values, $key );
					// originally  woocommerce_get_cart_item_from_session.

					// Add to cart right away so the product is visible in woocommerce_get_cart_item_from_session hook.

					$this->cart->set_cart_contents( $cart_contents );

				}
			}

			// If it's not empty, it's been already populated by the loop above.
			if ( ! empty( $cart_contents ) ) {
				$this->cart->set_cart_contents( apply_filters( 'b2be_rfq_contents_changed', $cart_contents ) );
				// originally  woocommerce_cart_contents_changed.
				$this->cart->calculate_totals();

				WC()->rfq = $this->cart;
			}

		}
		/**
		 * Quote Items Template.
		 *
		 * @param object $quote Quote Object.
		 */
		public static function quote_items_output( $quote ) {
			include 'admin/views/quote-items.php';
		}
		/**
		 * Quote Menu Items.
		 *
		 * @param array $items Items Array.
		 */
		public function quotes_menu_item( $items ) {
			$items[ $this->get_endpoint() ] = __( 'Quotes', 'b2b-ecommerce' );
			return $items;
		}
		/**
		 * End Point.
		 */
		public function get_endpoint() {
			return CWRFQ_QUOTE_ENDPOINT;
		}
		/**
		 * End Point Content.
		 *
		 * @param bool $type Type.
		 */
		public function endpoint_content( $type = false ) {
			wc_get_template( 'myaccount/quotes.php', array(), 'b2b-ecommerce-for-woocommerce', CWRFQ_TEMPLATE_DIR );
		}
		/**
		 * View Quote End Point.
		 */
		public function add_view_quote_endpoint() {
			add_rewrite_endpoint( 'view-quote', EP_ROOT | EP_PAGES );
		}
		/**
		 * View Quote Query Vars.
		 *
		 * @param string $query_vars Query Vars.
		 */
		public function add_view_quote_query_vars( $query_vars ) {
			$query_vars[ CWRFQ_VIEW_QUOTE_ENDPOINT ] = CWRFQ_VIEW_QUOTE_ENDPOINT;
			return $query_vars;
		}
		/**
		 * Account View Quote.
		 *
		 * @param int $quote_id Quote ID.
		 */
		public function woocommerce_account_view_quote( $quote_id ) {

			wc_get_template(
				'myaccount/view-quote.php',
				array(
					'quote'    => wc_get_quote( $quote_id ),
					'quote_id' => $quote_id,
				),
				'b2b-ecommerce-for-woocommerce',
				CWRFQ_TEMPLATE_DIR
			);
		}
		/**
		 * Display Empty Crat Message.
		 *
		 * @param string $title Title.
		 * @param string $endpoint Endpoint.
		 */
		public function view_quote_page_title( $title, $endpoint ) {
			$value = get_query_var( $endpoint );
			return $title;
		}

		/**
		 * Display Empty Crat Message.
		 *
		 * @param int $quote_id Quote ID.
		 */
		public function woocommerce_quote_details_table( $quote_id ) {

			if ( ! $quote_id ) {
				return;
			}
			wc_get_template(
				'/quote/quote-details.php',
				array(
					'quote_id' => $quote_id,
				),
				'b2b-ecommerce-for-woocommerce',
				CWRFQ_TEMPLATE_DIR
			);
		}
		/**
		 * Submit Quote.
		 */
		public function rfq_submit_quote() {

			if ( ! empty( $_POST['wpnonce'] ) ) {
				wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wpnonce'] ) ) );
			}

			if ( isset( $_POST['quote_id'] ) ) {
				$quote_id = ( sanitize_text_field( wp_unslash( $_POST['quote_id'] ) ) );
			}
			if ( ! current_user_can( 'edit_shop_orders' ) || '' == $quote_id ) {
				wp_die( -1 );
			}
			$quote = wc_get_quote( $quote_id );
			$quote->submit_quote();
			$updated = wc_get_quote( $quote_id );

			/*
			@name: b2be_rfq_quote_sumitted
			@desc: Runs after submitting rfq.
			@param: (int) $quote_id Current Quote Id.
			@param: (object) $quote Current Quote Object.
			@package: b2b-ecommerce-for-woocommerce
			@module: request for quote
			@type: action
			*/
			do_action( 'b2be_rfq_quote_sumitted', $quote_id, $updated );
			wp_die();
		}
		/**
		 * Hide Add to Cart Button.
		 */
		public function hide_add_cart() {
			global $product;

			$disabled = $this->can_add_to_cart( $product );
			if ( $disabled ) {
				echo '<style>button.single_add_to_cart_button{ display: none !important;}</style>';
			}
		}
		/**
		 * Hide Add To Cart Loop.
		 */
		public function hide_add_cart_loop() {
			global $product;

			$disabled = $this->can_add_to_cart( $product );
			$enabled  = $this->can_add_to_rfq( $product );
			if ( $product->is_type( 'variable' ) && $enabled ) {
				return;
			}
			if ( $disabled ) {
				echo ( '<style>.post-' . esc_attr( $product->get_id() ) . ' .button.add_to_cart_button{ display: none !important;}</style>' );
			}

		}

		/**
		 * Hide Add To Cart Loop.
		 */
		public function hide_add_rfq_loop() {
			global $product;
			if ( $product->is_type( 'simple' ) ) {
				$enabled            = $this->can_add_to_rfq( $product );
				$add_to_rfq_btn_txt = get_option( 'codup-rfq_add_to_rfq_button_text' );

				if ( ! $enabled ) {
					return;
				}

				$b2be_moq_rules = b2be_get_moq_limit( $product );
				$quantity       = 1;
				if ( isset( $b2be_moq_rules[0]['minQuantity'] ) ) {
					$quantity = $b2be_moq_rules[0]['minQuantity'];
				}
				?>
				<a
				style="display:inline-block;" 
				href="?add-to-rfq=<?php echo wp_kses_post( $product->get_id() ); ?>&qty=1"
				data-quantity="<?php echo wp_kses_post( $quantity ); ?>" 
				name="add-to-rfq"
				data-product_id="<?php echo wp_kses_post( $product->get_id() ); ?>" 
				data-product_sku="<?php echo wp_kses_post( $product->get_sku() ); ?>" 
				aria-label="Add “<?php echo wp_kses_post( $product->get_name() ); ?>” to your rfq" 
				rel="nofollow"
				value="<?php echo esc_attr( $product->get_id() ); ?>" 
				class="button single_add_to_rfq_button_ajax alt"
				>
				<?php echo ( '' !== $add_to_rfq_btn_txt ) ? esc_html( $add_to_rfq_btn_txt ) : 'Add To RFQ'; ?>
			</a>        
				<?php
			}
		}

		/**
		 * Function to return post object by slug.
		 *
		 * @param string $title Title of post type.
		 * @param string $content Content of post type.
		 * @param string $date Date of post type.
		 * @param string $type Type of post type.
		 */
		public function post_exists( $title, $content = '', $date = '', $type = '' ) {
			global $wpdb;

			$post_title = wp_unslash( sanitize_post_field( 'post_title', $title, 0, 'db' ) );
			$args       = array();

			if ( ! empty( $title ) ) {
				return (int) $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE 1=1 AND post_title = %s", $post_title ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped.
			}
			return 0;
		}

		/**
		 * Function to rearrange menu items.
		 *
		 * @param array $menu_links menu_links.
		 */
		public function rearrange_logout_menu( $menu_links ) {

			// Remove the logout menu item, will re-add later.
			$logout_link = $menu_links ['customer-logout'];
			unset( $menu_links ['customer-logout'] );

			// Insert back the logout item.
			$menu_links ['customer-logout'] = $logout_link;

			return $menu_links;
		}
	}
}
new B2BE_RFQ();
