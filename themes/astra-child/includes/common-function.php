<?php
function custom_enqueue_options_style() {
	wp_enqueue_style( 'new-custom-style', get_stylesheet_directory_uri() . '/assets/css/custom.css' );
	wp_enqueue_style( 'mytheme-bootstrap-style', get_stylesheet_directory_uri() . '/assets/css/bootstrap.min.css' );
	wp_enqueue_script( 'jquery-bootstrap-popup-js', get_stylesheet_directory_uri() . '/assets/js/bootstrap.min.js' );
	wp_enqueue_script( 'jquery-custom-js', get_stylesheet_directory_uri() . '/assets/js/custom.js' );
}
add_action( 'wp_enqueue_scripts', 'custom_enqueue_options_style', 500 );

function custom_admin_enqueue(){
    wp_enqueue_script('customscript-custom', get_stylesheet_directory_uri() . '/assets/js/admin-custom.js', array(), '1.0.0', true );
}
add_action('admin_enqueue_scripts', 'custom_admin_enqueue');

add_filter( 'woocommerce_product_upsells_products_heading', 'bbloomer_translate_may_also_like' );
function bbloomer_translate_may_also_like() {
   return 'Works well with';
}

add_filter( 'woocommerce_product_add_to_cart_text', 'custom_add_to_cart_price', 20, 2 ); // Shop and other archives pages
add_filter( 'woocommerce_product_single_add_to_cart_text', 'custom_add_to_cart_price', 20, 2 ); // Single product pages
function custom_add_to_cart_price( $button_text, $product ) {
    // Variable products
    if( $product->is_type('variable') ) {
        // shop and archives
        if( ! is_product() ){
            $product_price = wc_price( wc_get_price_to_display( $product, array( 'price' => $product->get_variation_price() ) ) );
            //return $button_text . ' - From ' . strip_tags( $product_price );
            return 'From ' . strip_tags( $product_price );
        } 
        // Single product pages
        else {
            return $button_text;
        }
    } 
    // All other product types
    else {
        $product_price = wc_price( wc_get_price_to_display( $product ) );
        //return 'Add To Bag - ' . strip_tags( $product_price );
        return 'Add To Bag';
    }
}

/*add_filter( 'woocommerce_product_tabs', 'woo_custom_description_tab', 98 );
function woo_custom_description_tab( $tabs ) {

	$tabs['reviews']['callback'] = 'woo_custom_description_tab_content';	// Custom description callback

	return $tabs;
}

function woo_custom_description_tab_content() {
    echo do_shortcode('[wp_social_ninja id="36299" platform="reviews"]');
}*/

add_filter( 'woocommerce_product_tabs', 'misha_rename_additional_info_tab' );
function misha_rename_additional_info_tab( $tabs ) {
	$tabs['reviews']['title'] = 'Reviews';
	return $tabs;
}

add_filter( 'woocommerce_product_tabs', 'bbloomer_remove_desc_tab', 9999 );
function bbloomer_remove_desc_tab( $tabs ) {
   unset( $tabs['description'] );
   unset( $tabs['additional_information'] );
   return $tabs;
}

add_action('wp_head',function (){
    if(!is_page('contact')){
        echo '<script src="https://www.google.com/recaptcha/api.js" async defer></script>';
    }
    }
);


function remove_checkout_fields( $fields ){
     unset($fields['billing']['billing_state']);
     unset($fields['billing']['billing_postcode']);
     return $fields;
}
add_filter( 'woocommerce_checkout_fields' , 'remove_checkout_fields' );

add_filter('b2bking_bulkorder_indigo_search_name_display', function($name, $product){
    $name = $product->get_name();
    return $name;
}, 10, 2);

  
function custom_login_form() {
   if ( is_admin() ) return;
   if ( is_user_logged_in() ) return; 
   ob_start();
   woocommerce_login_form();
   return ob_get_clean();
}
add_shortcode( 'wc_login_form_custom', 'custom_login_form' );


function change_login_form_label( $translated_text, $text, $domain ) {
    if ( ! is_user_logged_in() ) {
        $original_text = 'Username or email address';

        if ( $text === $original_text )
            $translated_text = esc_html__('Email address', 'astra-child' );
    }
    return $translated_text;
}
add_filter( 'gettext', 'change_login_form_label', 10, 3 );


function login_redirect() {
    if(  is_user_logged_in() && !is_admin() && (is_page('login') || is_page('pro-register')) ) {
        wp_redirect(site_url('my-account'));
    }
}
add_action('wp', 'login_redirect');

add_action('um_registration_complete', 'um_121721_change_registration_role' ,1);

function um_121721_change_registration_role( $user_id ){
	um_fetch_user( $user_id );
	UM()->user()->auto_login( $user_id );
	wp_redirect( site_url('my-account') ); 
	exit;
}

function get_attachment_url($user_id) {
	$btb_doc = get_user_meta( $user_id, "btb_doc", true );
	$btb_doc_metadata = get_user_meta( $user_id, "btb_doc_metadata", true );
	
	$btb_url = $btb_doc_metadata['name'];	
	$url_array = explode("/", $btb_url);	
	$url_array_length = count($url_array);	
	$url_array[$url_array_length-2] = $user_id;
	$url_array[$url_array_length-1] = $btb_doc;
	return join("/",$url_array);
} 

add_action( 'um_registration_set_extra_data', 'my_registration_set_extra_data', 10, 2 );
function my_registration_set_extra_data( $user_id, $args ) {
	if($args['form_id'] == 36494) 	{

		update_user_meta( $user_id, 'b2bking_default_approval_manual', 'group_37185' );
		update_user_meta( $user_id, 'b2bking_registration_role', 'role_37210' );
		
		update_user_meta( $user_id, 'account_status', 'approved' );
		update_user_meta( $user_id, 'b2bking_account_approved', 'no' );
		update_user_meta( $user_id, 'b2bking_custom_field_37227', get_attachment_url($user_id) );
	}

	$date = get_user_meta( $user_id, "billing_birth_date", true );
    update_user_meta( $user_id, "billing_birth_date", date_i18n("Y-m-d", strtotime( $date ) ) );
	
    return $user_id;
}

function coh_role_shortcode($atts, $content = null) { 
	extract( shortcode_atts( array('customer_group' => ''), $atts ) );
    
    $customer_group = str_replace(' ', '', $customer_group);
    $selected_customer_group = explode(",", $customer_group);
    
    //check if user is logged in
    if ( is_user_logged_in() ) {
		
		$user_id = get_current_user_id();
		$user_customer_group = get_user_meta( $user_id, "b2bking_customergroup", true );
		
        //loop through selected user ids
        foreach ($selected_customer_group as $cg) {
            //check if user is selected
            if ($user_customer_group == $cg) {
                return do_shortcode($content);
            }
        } 
        //hide content to non-selected users
        return '';
    	//hide content to guests
    } else {   
		//loop through selected user ids
        foreach ($selected_customer_group as $cg) {
            //check if user is selected
            if ("unlogged" == $cg) {
                return do_shortcode($content);
            }
        } 
        return '';
    }
}
// coh custom shortcodes
add_shortcode('coh_role', 'coh_role_shortcode');

function coh_footer_contact_section_shortcode($atts, $content = null) { 
	 $output = "<div class='contact-us-form contact-us-form-footer'> <h1> CONTACT US </h1> <p>LET'S GET IN TOUCH</p>
	 	[wpforms id='38929'] <h2> <a href='mailto:help@codeofharmony.com' target='_blank'>help@codeofharmony.com</a>
		</h2> <h2> <a href='tel:+15623490775' target='_blank'>(562) 349-0775</a> </h2> </div>";
	return do_shortcode($output);
}

// coh custom shortcodes
add_shortcode('coh_footer_contact_section', 'coh_footer_contact_section_shortcode');


// remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
// add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 30 ); 