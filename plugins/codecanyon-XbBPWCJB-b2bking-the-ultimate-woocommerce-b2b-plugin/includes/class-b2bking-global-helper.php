<?php

class B2bking_Globalhelper{

	private static $instance = null;

	public static function init() {
	    if ( self::$instance === null ) {
	        self::$instance = new self();
	    }

	    return self::$instance;
	}

	public static function get_user_group($user_id = 0){
		if ($user_id === 0){
			$user_id = get_current_user_id();
		}

		$meta_key = apply_filters('b2bking_group_key_name', 'b2bking_customergroup');

		$group = get_user_meta($user_id, $meta_key, true);
		return $group;
	}

	public static function update_user_group($user_id, $value){

		$meta_key = apply_filters('b2bking_group_key_name', 'b2bking_customergroup');

		update_user_meta($user_id, $meta_key, $value);
	}

	public static function custom_modulo($nr1, $nr2){
		$evenlyDivisable = abs(($nr1 / $nr2) - round($nr1 / $nr2, 0)) < 0.00001;

		if ($evenlyDivisable){
			// number has no decimals, therefore remainder is 0
			return 0;
		} else {
			return 1;
		}
	}

	public static function b2bking_wc_get_price_to_display( $product, $args = array() ) {

		if (is_a($product,'WC_Product_Variation') || is_a($product,'WC_Product')){

			// Modify WC function to consider user's vat exempt status
			global $woocommerce;
			$customertest = $woocommerce->customer;

			if (is_a($customertest, 'WC_Customer')){
				$customer = WC()->customer;
				$vat_exempt = $customer->is_vat_exempt();
			} else {
				$vat_exempt = false;
			}
		    $args = wp_parse_args(
		        $args,
		        array(
		            'qty'   => 1,
		            'price' => $product->get_price(),
		        )
		    );

		    $price = $args['price'];
		    $qty   = $args['qty'];

		    if (is_cart() || is_checkout()){
		    	if ( 'incl' === get_option( 'woocommerce_tax_display_cart' ) && !$vat_exempt ){
		    		return 
		    	    wc_get_price_including_tax(
		    	        $product,
		    	        array(
		    	            'qty'   => $qty,
		    	            'price' => $price,
		    	        )
		    	    );
		    	} else {
		    		return
		    	    wc_get_price_excluding_tax(
		    	        $product,
		    	        array(
		    	            'qty'   => $qty,
		    	            'price' => $price,
		    	        )
		    	    );
		    	}
		    } else {
		    	//shop
		    	if ( 'incl' === get_option( 'woocommerce_tax_display_shop' ) && !$vat_exempt ){
		    		return 
		    	    wc_get_price_including_tax(
		    	        $product,
		    	        array(
		    	            'qty'   => $qty,
		    	            'price' => $price,
		    	        )
		    	    );
		    	} else {
		    		return
		    	    wc_get_price_excluding_tax(
		    	        $product,
		    	        array(
		    	            'qty'   => $qty,
		    	            'price' => $price,
		    	        )
		    	    );
		    	}
		    }
		} else {
			return 0;
		}
	    
	}

	public static function get_woocs_price( $price ) {

		if (class_exists('WOOCS')) {
			global $WOOCS;
			$currrent = $WOOCS->current_currency;
			if ($currrent != $WOOCS->default_currency) {
				$currencies = $WOOCS->get_currencies();
				$rate = $currencies[$currrent]['rate'];
				$price = $price * $rate;
			}
		}

		// WPML integration
		$current_currency = apply_filters('wcml_price_currency', NULL );
		if ($current_currency !== NULL){
			$price = apply_filters( 'wcml_raw_price_amount', $price, $current_currency );
		}



		return $price;
		
	}

	public static function is_rest_api_request() {
	    if ( empty( $_SERVER['REQUEST_URI'] ) ) {
	        // Probably a CLI request
	        return false;
	    }

	    $rest_prefix         = trailingslashit( rest_get_url_prefix() );
	    $is_rest_api_request = strpos( $_SERVER['REQUEST_URI'], $rest_prefix ) !== false;

	    if (defined('REST_REQUEST')){
	    	$is_rest_api_request = true;
	    }

	    return apply_filters( 'is_rest_api_request', $is_rest_api_request );
	}

	// returns an array of all categories including all parent categories of subcategories a product belongs to
	public static function get_all_product_categories($product_id){

		$all_categories = $direct_categories = wc_get_product_term_ids($product_id, 'product_cat');

		// set via code snippets that rule apply to the direct categories only (And not apply to parent/sub categories)
		if (apply_filters('b2bking_apply_rules_to_direct_categories_only', false)){
			return $all_categories;
		}

		foreach ($direct_categories as $directcat){
			// find all parents
			$term = get_term($directcat, 'product_cat');
			while ($term->parent !== 0){
				array_push($all_categories, $term->parent);
				$term = get_term($term->parent, 'product_cat');
			}
		}

		return array_filter(array_unique($all_categories));
	}

	public static function b2bking_has_category( $category_id, $taxonomy, $product_id ) {
	    $product = wc_get_product( $product_id );

	    if (is_a($product,'WC_Product_Variation') || is_a($product,'WC_Product')){
	 
		    $all_categories = $direct_categories = wc_get_product_term_ids($product_id, 'product_cat');

		    // set via code snippets that rule apply to the direct categories only (And not apply to parent/sub categories)
		    if (apply_filters('b2bking_apply_rules_to_direct_categories_only', false)){
		    	if (in_array($category_id, $all_categories)){
		    		return true;
		    	}
		    } else {
		    	// continue here
		    	foreach ($direct_categories as $directcat){
		    		// find all parents
		    		$term = get_term($directcat, 'product_cat');
		    		while ($term->parent !== 0){
		    			array_push($all_categories, $term->parent);
		    			$term = get_term($term->parent, 'product_cat');
		    		}
		    	}

		    	$all_categories = array_filter(array_unique($all_categories));

		    	if (in_array($category_id, $all_categories)){
		    		return true;
		    	}
		    }
		   

		}

		return false;
	}

	public static function is_side_cart(){
		$side_cart = false;

		global $b2bking_is_mini_cart; 

		if ($b2bking_is_mini_cart === true){
			$side_cart = true;
		}


		return $side_cart;
	}

	public static function clear_caches_transients(){
		// set that rules have changed so that pricing cache can be updated
		update_option('b2bking_commission_rules_have_changed', 'yes');
		update_option('b2bking_dynamic_rules_have_changed', 'yes');

		// delete all b2bking transients
		global $wpdb;
		$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '%transient_b2bking%'" );

		wp_cache_flush();

		// force permalinks
		update_option('b2bking_force_permalinks_flushing_setting', 1);
	}

	// get all rules by user
	// returns array of rule IDs
	public static function get_all_rules($rule_type = 'all', $user_id = 'current'){

		if ($user_id === 'current'){
			$user_id = get_current_user_id();
		}

		$account_type = get_user_meta($user_id,'b2bking_account_type', true);
		if ($account_type === 'subaccount'){
			// for all intents and purposes set current user as the subaccount parent
			$parent_user_id = get_user_meta($user_id, 'b2bking_account_parent', true);
			$user_id = $parent_user_id;
		}

		$currentusergroupidnr = b2bking()->get_user_group($user_id);
		if (!$currentusergroupidnr || empty($currentusergroupidnr)){
			$currentusergroupidnr = 'invalid';
		}

		$array_who_multiple = array(
			'relation' => 'OR',
			array(
				'key' => 'b2bking_rule_who_multiple_options',
				'value' => 'group_'.$currentusergroupidnr,
				'compare' => 'LIKE'
			),
			array(
				'key' => 'b2bking_rule_who_multiple_options',
				'value' => 'user_'.$user_id,
				'compare' => 'LIKE'
			),
		);

		if ($user_id !== 0){
			array_push($array_who_multiple, array(
							'key' => 'b2bking_rule_who_multiple_options',
							'value' => 'all_registered',
							'compare' => 'LIKE'
						));

			// add rules that apply to all registered b2b/b2c users
			$user_is_b2b = get_user_meta($user_id, 'b2bking_b2buser', true);
			if ($user_is_b2b === 'yes'){
				array_push($array_who_multiple, array(
							'key' => 'b2bking_rule_who_multiple_options',
							'value' => 'everyone_registered_b2b',
							'compare' => 'LIKE'
						));
			} else {
				array_push($array_who_multiple, array(
							'key' => 'b2bking_rule_who_multiple_options',
							'value' => 'everyone_registered_b2c',
							'compare' => 'LIKE'
						));
			}

		}

		$array_who = array(
						'relation' => 'OR',
						array(
							'key' => 'b2bking_rule_who',
							'value' => 'group_'.$currentusergroupidnr
						),
						array(
							'key' => 'b2bking_rule_who',
							'value' => 'user_'.$user_id
						),
						array(
							'relation' => 'AND',
							array(
								'key' => 'b2bking_rule_who',
								'value' => 'multiple_options'
							),
							$array_who_multiple
						),
					);

		// if user is registered, also select rules that apply to all registered users
		if ($user_id !== 0){
			array_push($array_who, array(
							'key' => 'b2bking_rule_who',
							'value' => 'all_registered'
						));

			// add rules that apply to all registered b2b/b2c users
			$user_is_b2b = get_user_meta($user_id, 'b2bking_b2buser', true);
			if ($user_is_b2b === 'yes'){
				array_push($array_who, array(
							'key' => 'b2bking_rule_who',
							'value' => 'everyone_registered_b2b'
						));
			} else {
				array_push($array_who, array(
							'key' => 'b2bking_rule_who',
							'value' => 'everyone_registered_b2c'
						));
			}

		}

		$rules = get_posts([
			'post_type' => 'b2bking_rule',
			'post_status' => 'publish',
			'numberposts' => -1,
			'fields' 	  => 'ids',
			'meta_query'=> array(
				'relation' => 'AND',
				$array_who,
			)
		]);


		if ($rule_type !== 'all'){
			// remove rules that don't match rule type.
			foreach ($rules as $index=>$rule){
				$type = get_post_meta($rule,'b2bking_rule_what', true);
				if ($rule_type !== $type){
					unset($rules[$index]);
				}
			}
		}

		return $rules;


	}

}