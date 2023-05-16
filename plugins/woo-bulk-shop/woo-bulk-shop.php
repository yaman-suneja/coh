<?php
/**
 * Bulk Shop
 *
 * @package  BulkShop
 *
 * Plugin Name: Bulk Shop for WooCommerce
 * Plugin URI: https://woocommerce.com/products/bulk-shop-for-woocommerce/
 * Description: List products in a table, enables bulk shop of products and variations.
 * Version: 1.3.29
 * Author: Consortia
 * Author URI: https://www.consortia.no/en/
 * Text Domain: woo-bulk-shop
 * Domain Path: /languages
 *
 * Tested up to: 5.3.2
 * Woo: 4830849:5bde27eff82a2b22f44837b2b6321488
 * WC requires at least: 3.5
 * WC tested up to: 4.7.0
 *
 * Copyright: © 2018-2020 Consortia AS.
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

register_activation_hook( __FILE__, 'wbs_install' );
register_deactivation_hook( __FILE__, 'wbs_deactivate' );

add_action( 'init', 'wbs_init' );

require_once __DIR__ . '/includes/class-wbstemplates.php';

/**
 * Install
 */
function wbs_install() {

	global $wp_version;

	if ( version_compare( $wp_version, '4.1', '<' ) ) {
		wp_die( 'This plugin require WordPress 4.1 or higher.' );
	}

	$wbs_options_arr = array(
		'wbs-css-shop-table'        => '',
		'wbs-heading-name'          => '',
		'wbs-heading-description'   => '',
		'wbs-heading-price'         => '',
		'wbs-heading-quantity'      => '',
		'wbs-heading-total'         => '',
		'wbs-button-add'            => '',
		'wbs-button-add-to-cart'    => '',
		'wbs-button-sales'          => '',
		'wbs-placeholder-qty'       => '',
		'wbs-placeholder-search'    => '',
		'wbs-thumbnail-size'        => '',
		'wbs-thumbnail-mobile-size' => '',
		'wbs-heading-product-image' => '',
		'wbs-nyp-name'              => '',
		'wbs-nyp-minimum'           => '',
		'wbs-radio-text'            => '',
		'wbs-radio-sku'             => '',
		'wbs-radio-tag'             => '',
		'wbs-login-see-price'       => '',
		
	);

	update_option( 'wbs_options', $wbs_options_arr );

	set_transient( 'wbs-admin-notice-activated', true );

	/*
	Create page, add content [bulkshop]
	*/
	do_action( 'create_default_page' );

}

/**
 * On activation notice
 */
function wbs_admin_activation_notice_success() {

	if ( get_transient( 'wbs-admin-notice-activated' ) ) {
		?>
		<div class="updated woocommerce-message">
			<a class="woocommerce-message-close notice-dismiss" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'wc-hide-notice', 'wbs_admin_activation_notice_success' ), 'woocommerce_hide_notices_nonce', '_wc_notice_nonce' ) ); ?>"><?php esc_html_e( 'Dismiss', 'woo-bulk-shop' ); ?></a>

			<p>
				<?php esc_html_e( 'Thank you for installing Bulk Shop for WooCommerce. You can now start to create your lists and pages.', 'woo-bulk-shop' ); ?>
				<?php /* translators: %s: url for documentation */ ?>
				<?php printf( esc_html__( 'Read the %1$s extension documentation %2$s for more information.', 'woo-bulk-shop' ), '<a href="https://docs.woocommerce.com/document/bulk-shop" target="_blank">', '</a>' ); ?>
			</p>
			<p class="submit">
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=bulk-shop' ) ); ?>" class="button-primary"><?php esc_html_e( 'Start bulk shop generator', 'woo-bulk-shop' ); ?></a>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=wc-settings&tab=wbs' ) ); ?>" class="button-secondary"><?php esc_html_e( 'Settings', 'woo-bulk-shop' ); ?></a>
			</p>
		</div>
		<?php
		delete_transient( 'wbs-admin-notice-activated' );
	}
}
add_action( 'admin_notices', 'wbs_admin_activation_notice_success' );


/**
 * Deactivate
 */
function wbs_deactivate() {

}

/**
 * Init Bulk Shop
 * Add language support
 */
function wbs_init() {

	load_plugin_textdomain( 'woo-bulk-shop', false, basename( dirname( __FILE__ ) ) . '/languages' );
	include_once dirname( __FILE__ ) . '/includes/class-wbs-options.php';

}

/**
 * Create Bulk Shop default page
 */
function wbs_create_default_page() {

	if ( get_page_by_title( 'Bulk Shop' ) === null ) {

		$page = array(
			'post_title'   => 'Bulk Shop',
			'post_content' => '[bulkshop]',
			'post_status'  => 'publish',
			'post_type'    => 'page',
			'post_author'  => get_current_user_id(),
		);

		wp_insert_post( apply_filters( 'wbs_create_default_page_args', $page ) );

	}
}
add_action( 'create_default_page', 'wbs_create_default_page' );

/**
 * Create menu
 */
function wbs_create_admin_menu() {

	add_menu_page(
		'Bulk Shop',
		'Bulk Shop',
		'manage_woocommerce',
		'bulk-shop',
		'wbs_plugin_shortcodes_page',
		'dashicons-index-card'
	);

	add_submenu_page(
		'bulk-shop',
		__( 'Shortcode generator', 'woo-bulk-shop' ),
		__( 'Create shortcode', 'woo-bulk-shop' ),
		'manage_woocommerce',
		'bulk-shop',
		'wbs_plugin_shortcodes_page',
		1
	);

	add_submenu_page(
		'bulk-shop',
		__( 'Settings', 'woo-bulk-shop' ),
		__( 'Settings', 'woo-bulk-shop' ),
		'manage_woocommerce',
		'bulk-shop-options',
		'wbs_plugin_options_page',
		2
	);

}
add_action( 'admin_menu', 'wbs_create_admin_menu' );

/**
 * Create shortcodes page
 */
function wbs_plugin_shortcodes_page() {

	include_once __DIR__ . '/views/wbs-shortcodes.php';

}

/**
 * Get the options page
 */
function wbs_plugin_options_page() {

	 wp_safe_redirect( admin_url( 'admin.php?page=wc-settings&tab=wbs' ) );
	 exit();
}

/**
 * Create shortcode for bulk shop
 * [bulkshop]
 *
 * @param array $atts Shortcode attributes.
 */
function wbs_bulkshop( $atts ) {

	$wbs_templates = new WbsTemplates();

	$atts = shortcode_atts(
		array(
			'hidevariations'       => '',
			'maxrows'              => '',
			'hidestock'            => '',
			'categories'           => '',
			'hidesalebadge'        => '',
			'hidesku'              => '',
			'hidecategoryselector' => '',
			'hidethumbnail'        => '',
			'hidedescription'      => '',
			'hidecarticon'         => '',
			'price_field'		   => '',
			'price_field_roles'	   => '',
			'price_field1'		   => '',
			'price_field_roles1'   => '',
			'price_field2'		   => '',
			'price_field_roles2'   => '',
			'price_field3'		   => '',
			'price_field_roles3'   => '',
			'price_field4'		   => '',
			'price_field_roles4'   => '',
			'price_field5'		   => '',
			'price_field_roles5'   => '',
			'price_field6'		   => '',
			'price_field_roles6'   => '',
			'price_field7'		   => '',
			'price_field_roles7'   => '',
			'price_field8'		   => '',
			'price_field_roles8'   => '',
			'price_field9'		   => '',
			'price_field_roles9'   => '',
			'hidebulkadd'          => '',
			'product_qty'          => '',
			'product_order'        => '',
			'hideprice'            => '',
			'hidetotal'            => '',
			'hide_price_non_user'  => '',
			'hide_checkboxes'      => '',
			'hide_search'          => '',
			'related_products'     => '',
			'related_count'        => '',
		),
		$atts,
		'bulkshop'
	);
	
	ob_start();
	add_option( 'wbs_atts', $atts );
	$wbs_templates->wbs_get_table( $atts );
	return ob_get_clean();

}
add_shortcode( 'bulkshop', 'wbs_bulkshop' );


/**
 * Add WooCommerce support
 */
function wbs_add_woocommerce_support() {

	add_theme_support( 'woocommerce' );

}
add_action( 'after_setup_theme', 'wbs_add_woocommerce_support' );

/**
 * Add scripts and style
 */
function wbs_header_scripts() {

	wp_register_script( 'wbs_tablecalc', plugins_url( '/js/jquery.tablecalc.min.js', __FILE__ ), array( 'jquery' ), '2.0.5', false );
	wp_enqueue_script( 'wbs_tablecalc' );

	wp_register_script( 'wbs_scripts', plugins_url( '/js/wbs-scripts.js', __FILE__ ), array( 'jquery' ), '2.0.5', false );
	wp_localize_script( 'wbs_scripts', 'wbs_woo_params', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
	wp_enqueue_script( 'wbs_scripts' );
	
	wp_enqueue_style( 'all', 'https://use.fontawesome.com/releases/v5.2.0/css/all.css', array(), '2.0.5' );

	wp_register_style( 'wbs_css', plugins_url( '/css/wbs.css', __FILE__ ), array(), '2.0.5' );
	wp_enqueue_style( 'wbs_css' );

}
add_action( 'wp_head', 'wbs_header_scripts' );

/**
 * Add admin scripts and style
 */
function wbs_admin_header_scripts() {

	wp_enqueue_style( 'all', 'https://use.fontawesome.com/releases/v5.2.0/css/all.css', array(), '2.0.5' );

}
add_action( 'admin_enqueue_scripts', 'wbs_admin_header_scripts' );

/**
 * Add script to footer
 */
function wbs_footer_scripts() {
	?>
	<script>
		var tbl = '#wbs-table';
		var $   = jQuery;
		jQuery(document).ready(function ( $ ) {

			var hiddens = parseInt( getHiddenColumns() );
			var qty_col = eval( 5 - hiddens );
			$(tbl).tableCalc({
				calcColumns: [ qty_col ],
				calcColumn_sum: null,
				rowId: 0, 
				textColumns: [ ], 
				calcType: 'c', 
				calcCustom: '',
				decimals: 2, 
				calcOnLoad: false, 
				onEvent: '', 
			});

			$( tbl + ' tr td input' ).on( 'change', calcChanged );
			calcChanged( true );

			$( tbl + ' td select' ).on( 'change', selectedChanged );

			//Check for MS browsers
			var ua = window.navigator.userAgent;
			if ( ua.indexOf('MSIE ') > 0 || ua.indexOf('Trident/') > 0 || ua.indexOf('Edge/') > 0 ) {
				$('.wbs-table-set').addClass('wbs-table-set-edge').removeClass('wbs-table-set');
			}

			<?php apply_filters( 'wbs_footer_script_after', '' ); ?>
		});

	</script>
	<?php

}
add_action( 'wp_footer', 'wbs_footer_scripts' );

/**
 * Set footer text.
 */
function wbs_set_footer_text( $text ) {

	$page = filter_input( 1, 'page', FILTER_SANITIZE_STRING );

	if ( 'bulk-shop' === $page ) {
		$img_file = plugins_url( 'consortia-100.png', __FILE__ );

		/* translators: %s: url to vendor and logo */ 
		printf( esc_html__( '- developed by %1$s%2$s%3$s', 'woo-bulk-shop' ), '<a href="https://www.consortia.no/en/" target="_blank">', '<img src="' . esc_attr( $img_file ) . '" class="cas-logo">', '</a>' );
	
	} else {

		return $text;

	}

}
add_filter( 'admin_footer_text', 'wbs_set_footer_text' );

/**
 * Add products to cart (ajax)
 */
function wbs_add_products_to_cart_callback() {

	check_ajax_referer( 'wbs_id', 'nonce' );
	$data = wp_unslash( $_POST );

	foreach ( (array) $data['rows'] as $row ) {
		if ( strlen( $row['id'] ) > 0 ) {
			$id        = sanitize_text_field( $row['id'] );
			$qty       = sanitize_text_field( $row['qty'] );
			$suggested = sanitize_text_field( $row['suggested'] );
			
			if ( strlen( $suggested ) > 0 ) {
				
				$product      = wc_get_product( $id );
				$variation_id = null;

				if ( count( $product->get_children() ) > 0 ) {
					$variation    = new WC_Product_Variation( $id );
					$variation_id = $variation->get_id();
				}

				$cart_item_data = array( 'nyp' => (float) $suggested );
				WC()->cart->add_to_cart( $id, $qty, $variation_id, array(), $cart_item_data );		
			} else {
				WC()->cart->add_to_cart( $id, $qty );
			}
		}
	}
	
	die();
}
add_action( 'wp_ajax_wbs_add_products_to_cart', 'wbs_add_products_to_cart_callback' );
add_action( 'wp_ajax_nopriv_wbs_add_products_to_cart', 'wbs_add_products_to_cart_callback' );

/**
 * Get info on product
 */
function wbs_get_variable_data_callback() {

	check_ajax_referer( 'wbs_id', 'nonce' );
	$data = wp_unslash( $_POST );
	$row  = $data['rows'][0];

	require_once __DIR__ . '/includes/class-wbsfunctions.php';
	
	$functions   = new WbsFunctions();
	$atts        = json_decode( $data['atts'][0] );
	$roles_found = ( strlen( $atts['price_field'] ) > 0 ) ? true : false;
	
	if ( strlen( $row['id'] ) > 0 ) {
		
		$id        = sanitize_text_field( $row['id'] );
		$product   = wc_get_product( absint( $id ) );
		$options   = (array) $row['selectedOptions'];
		$attr      = array();
		$variation = null;

		foreach ( $options as $key => $value ) {
		
			$attr[ $value['key'] ] = $value['value'];

		}

		if ( class_exists('WC_Data_Store') ) {

			$data_store = WC_Data_Store::load( 'product' );
			$variation  = $data_store->find_matching_product_variation( $product, $attr );
	
		}

		if ( isset( $variation ) && $variation > 0 ) {
			
			$product_var = new WC_Product_Variation( $variation );
			$price       = $product_var->get_price();
	
			if ( $roles_found ) {
				$price = $functions->wbs_get_custom_price( $price, $product_var, $atts );
			}
			
			/* Check for dynamic pricing */
			$sale_price = $product_var->get_sale_price();
			if ( empty( $sale_price ) ) {
				$sale_price = $price;
			}
		
			$obj                 = ( object ) array();
			$obj->id             = $product_var->get_id();
			$obj->price          = $price;
			$obj->price_formated = wp_strip_all_tags( wc_price( $obj->price ) );
			$obj->is_on_sale     = ( empty( $product_var->is_on_sale() ) ) ? 'false' : 'true';
			$obj->sale_price     = wp_strip_all_tags( wc_price( $sale_price ) );
			$obj->sku            = $product_var->get_sku();
			$obj->in_stock       = ( $product_var->is_in_stock() ) ? 'true' : 'false';
			$obj->user           = '';
			$jsonObj             = wp_json_encode( $obj );

			echo wp_kses( $jsonObj, 'post' );
		}
	}

	die();

}
add_action( 'wp_ajax_wbs_get_variable_data', 'wbs_get_variable_data_callback' );
add_action( 'wp_ajax_nopriv_wbs_get_variable_data', 'wbs_get_variable_data_callback' );

