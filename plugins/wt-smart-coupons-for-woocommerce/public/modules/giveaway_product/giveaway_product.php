<?php
/**
 * Giveaway products public section
 *
 * @link       
 * @since 1.4.0    
 *
 * @package  Wt_Smart_Coupon  
 */
if (!defined('ABSPATH')) {
    exit;
}

if(!class_exists('Wt_Smart_Coupon_Giveaway_Product')) /* common module class not found so return */
{
    return;
}
class Wt_Smart_Coupon_Giveaway_Product_Public extends Wt_Smart_Coupon_Giveaway_Product
{
    public $module_base='giveaway_product';
    public $module_id='';
    public static $module_id_static='';
    private static $instance = null;
    public static $bogo_allowed_options_to_display_products=array('specific_product');
    public static $bogo_eligible_session_id='wt_sc_bogo_eligible';
    public static $break_add_to_cart_loop_session_id='wt_sc_break_add_to_cart_loop'; /* this is used to break add to cart indefinite looping when cart contents convert as giveaway */
    public static $giveaway_count_adjust=false;
    public static $specific_product_addtocart_hooked=false; /* To check single specific product add to cart is hooked already */
    public static $giveaway_fully_availed_flag='fully_availed'; /* value to indicate giveaway was fully availed. This value is used to hide the giveaway eligible message */
    
    public static $bogo_discounts=array(); /* BOGO coupon type giveaway total discount */

    public function __construct()
    {
        $this->module_id=Wt_Smart_Coupon::get_module_id($this->module_base);
        self::$module_id_static=$this->module_id;

        /* Ajax hook to return variation ID on giveaway product attribute change */
        add_action('wp_ajax_update_variation_id', array($this, 'ajax_find_matching_product_variation_id'));
        add_action('wp_ajax_nopriv_update_variation_id', array($this, 'ajax_find_matching_product_variation_id'));

        /* Display giveaway products in the cart page */
        add_filter('woocommerce_coupon_is_valid', array($this, 'add_giveaway_products_with_coupon'), 11, 2);

        /* Ajax function for adding Giveaway products into cart when customer clicks on the product. */
        add_action('wp_ajax_wt_choose_free_product', array($this, 'add_to_cart'));
        add_action('wp_ajax_nopriv_wt_choose_free_product', array($this, 'add_to_cart'));

        /**
         *  Checks and add giveaway to cart when product quantity updated. 
         *  This is applicable for `specific_product` when single giveaway item with 100% discount and apply repeatedly enabled. 
         */
        add_action('woocommerce_after_cart_item_quantity_update', array($this, 'check_to_add_giveaway'), 111, 6);

        /* Mention its a giveaway product in the cart item table */
        add_action('woocommerce_after_cart_item_name', array($this, 'display_giveaway_product_description'), 10, 1);  

        /* Update cart item value for applying price before tax calculation. */
        add_filter('woocommerce_get_cart_item_from_session', array($this, 'update_cart_item_in_session'), 15, 3); 

        /* Update cart Item data */
        add_filter('woocommerce_add_cart_item', array($this, 'update_cart_item_values'), 15, 4); 

        /* Show updated cart item price in the table */
        add_filter('woocommerce_cart_item_price', array($this, 'update_cart_item_price'), 10, 2);

        /* Set cart item quantity as non editable */
        add_filter('woocommerce_cart_item_quantity', array($this, 'update_cart_item_quantity_field'), 5, 3);

        /* Show discount rows in the cart/order checkout summary section */
        add_action('woocommerce_cart_totals_before_shipping', array($this, 'add_give_away_product_discount'), 10, 0); 
        add_action('woocommerce_review_order_before_shipping', array($this, 'add_give_away_product_discount'), 10, 0);

        /* Update subtotal HTML */ 
        add_filter('woocommerce_cart_item_subtotal', array($this, 'add_custom_cart_item_total'), 1000, 2);

        /* Update total after discount */
        add_filter('woocommerce_after_calculate_totals', array($this, 'discounted_calculated_total'), 1000, 1);

        /* Remove free products from the cart if cart is empty */
        add_action('wp_loaded', array($this, 'check_any_free_products_without_coupon'), 15);

        /* Remove free product when coupon removed */
        add_action('woocommerce_removed_coupon', array($this, 'remove_free_product_from_cart'), 10, 1);

        /* Remove giveaway available session if exists */
        add_action('woocommerce_removed_coupon', array($this, 'remove_giveaway_available_session'), 10, 1);
        
        /* Update gift item details as order item meta when creating an order */     
        add_action('woocommerce_checkout_create_order_line_item', array($this, 'add_free_product_details_into_order'), 10, 4); 

        /* Display order item totals */
        add_filter('woocommerce_get_order_item_totals', array($this, 'woocommerce_get_order_item_totals'), 11, 3);

        /* Remove/hide giveaway product meta data from item meta array */
        add_filter('woocommerce_order_item_get_formatted_meta_data', array($this, 'unset_free_product_order_item_meta_data'), 10, 2);

        /* Adjust giveaway count when eligibility changed */
        add_action('wp_loaded', array($this, 'adjust_giveaway_count_when_eligibility_changed'), 15);

        /* Alter the coupon title text when printing the coupon in My account, cart, checkout etc */
        add_filter('wt_smart_coupon_meta_data', array($this, 'alter_coupon_title_text'), 10, 2);

        /* Scripts and styles for giveaway section */
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));

        /**
         *  @since 1.4.1
         *  Alter coupon price section in order summary section 
        */
        add_filter('woocommerce_coupon_discount_amount_html', array($this, 'alter_coupon_discount_amount_html'), 100, 2);

        /** 
         * @since 1.4.1
         * Exclude giveaway products from other coupons 
         */
        add_filter('woocommerce_coupon_is_valid_for_product', array($this, 'exclude_giveaway_from_other_discounts'), 10, 4);
    

        /**
         *  Update giveaway quantity when new cart item added
         *  This is applicable for `specific_product` when single giveaway item with 100% discount and apply repeatedly enabled.
         * 
         *  @since 1.4.4
         * 
         */
        add_action('woocommerce_add_to_cart',  array($this, 'check_and_add_giveaway_on_add_to_cart'), 111, 6);
    }

    /**
     * Get Instance
     */
    public static function get_instance()
    {
        if(self::$instance==null)
        {
            self::$instance=new Wt_Smart_Coupon_Giveaway_Product_Public();
        }
        return self::$instance;
    }
    
    /**
     * Check the coupon valid. 
     * If multiple giveaway products show option to choose products, otherwise add giveaway to cart
     */
    public function add_giveaway_products_with_coupon($valid, $coupon)
    {
        $coupon_code=wc_format_coupon_code($coupon->get_code());
        if(!$valid)
        {
            self::remove_bogo_eligible_session($coupon_code);
            return false;
        }

        $coupon_id  = $coupon->get_id();   
        if(self::is_bogo($coupon))
        {
            $bogo_customer_gets = $this->get_coupon_meta_value($coupon_id, '_wt_sc_bogo_customer_gets');
            if('specific_product'===$bogo_customer_gets)
            {
                $this->process_specific_product_giveaway($coupon_id, $coupon_code);
            } 
        }else
        {
            $this->process_specific_product_giveaway($coupon_id, $coupon_code);
        }
        return $valid;
    }

    /**
     *  This function will decide whether to show or add to cart get the giveaway items
     *  For BOGO specific products and normal coupons
     *  @since 1.4.0
     *  @param      int         $coupon_id          ID of coupon
     *  @param      string      $coupon_code        Coupon code
     */
    public function process_specific_product_giveaway($coupon_id, $coupon_code)
    {
        $free_products=self::get_giveaway_products($coupon_id); 
        if(!empty($free_products))
        {          
            $first_product = wc_get_product($free_products[0]);
            if(sizeof($free_products)== 1 && $this->is_purchasable($first_product) && 'variable'!==$first_product->get_type())
            {
                $giveaway_data=$this->get_product_giveaway_data($free_products[0], $coupon_code);
                if($this->is_full_free_item($first_product, $giveaway_data))
                {
                    $this->set_hook_to_add_giveaway_products($coupon_code); /* add to cart */
                }else
                {
                    $this->set_hook_to_show_giveaway_products();
                }
            }else
            {            
                $this->set_hook_to_show_giveaway_products();
            }
        }
    }
    
    /** 
     *  This function will hook a callback function to show giveaway products in the cart page
     *  @since 1.4.0
     */
    public function set_hook_to_show_giveaway_products()
    { 
        add_action('woocommerce_after_cart_table', array($this, 'display_give_away_products'), 1); 
    }

    /**
     * Callback function for displaying giveaway products in the cart page.
     * @since 1.4.0 
     */
    public function display_give_away_products()
    {
        global $woocommerce;
        $applied_coupons  = $woocommerce->cart->applied_coupons;
        if(empty($applied_coupons))
        {
            return;
        }

        $free_products=array();
        $add_to_cart_all=array();           
        foreach($applied_coupons as $coupon_code)
        {
            $coupon_code=wc_format_coupon_code($coupon_code);
            $coupon = new WC_Coupon($coupon_code);
            if(!$coupon)
            {
                continue;
            }

            $coupon_id=$coupon->get_id();
            $add_to_cart_all[$coupon_id]=false;

            $bogo_customer_gets = $this->get_coupon_meta_value($coupon_id, '_wt_sc_bogo_customer_gets');
            if(self::is_bogo($coupon))
            {  
                if('specific_product'==$bogo_customer_gets)
                {
                    $bogo_product_condition=$this->get_coupon_meta_value($coupon_id, '_wt_sc_bogo_product_condition');

                    $bogo_products=$this->get_all_bogo_giveaway_products($coupon_id);

                    $frequency=$this->get_coupon_applicable_count($coupon_id, $coupon_code);
                   
                    /**
                     *  Giveaway max quantity checking
                     *  Note: `$bogo_products` is a reference argument for the below function 
                     */
                    $this->check_giveaway_max_quantity($coupon_code, $coupon_id, $bogo_customer_gets, $bogo_product_condition, $bogo_products, $frequency);

                    $free_products[$coupon_code]=$bogo_products;                    

                    if('and'===$bogo_product_condition)
                    {
                        $add_to_cart_all[$coupon_id]=true; /* no single add to cart button */
                    }

                }

            }else //non bogo
            {
                $free_product_id_arr=self::get_giveaway_products($coupon_id);
                if(!empty($free_product_id_arr))
                {

                    $qty_price_data=array(
                        'qty'=>1, 
                        'price'=>100, 
                        'price_type'=>'percent',
                    );

                    $qty_price_arr=array_fill(0, count($free_product_id_arr), $qty_price_data);
                    $new_coupon_products=array_combine($free_product_id_arr, $qty_price_arr);
                    $free_products[$coupon_code]=$new_coupon_products;
                }
            }
        }

        if(empty($free_products))
        {
            return;  
        }
        include_once plugin_dir_path( __FILE__ ).'views/_cart_giveaway_products.php';
    }

    /**
     *  Add required scripts/styles for giveaway products
     *  @since 1.4.0
     */
    public function enqueue_scripts()
    { 
        if(function_exists('is_cart') && is_cart())
        {
            wp_enqueue_style('wt-smart-coupon-giveaway', plugin_dir_url( __FILE__ ).'assets/css/main.css', array(), WEBTOFFEE_SMARTCOUPON_VERSION, 'all');
            wp_enqueue_script('wt-smart-coupon-giveaway', plugin_dir_url( __FILE__ ).'assets/js/main.js', array('jquery'), WEBTOFFEE_SMARTCOUPON_VERSION, false);
        }   
    }

    /**
     * Ajax action function for getting variation id
     * @since 1.4.0
     */
    public function ajax_find_matching_product_variation_id()
    {
        $out=array('status'=>false, 'status_msg'=>__('Invalid request', 'wt-smart-coupons-for-woocommerce'));
        
        if(check_ajax_referer( 'wt_smart_coupons_public', '_wpnonce', false))
        {         
            if(isset($_POST['attributes']) && isset($_POST['product']))
            {
                $product_id = Wt_Smart_Coupon_Security_Helper::sanitize_item($_POST['product'], 'int');
                $attributes = Wt_Smart_Coupon_Security_Helper::sanitize_item($_POST['attributes'], 'text_arr');
                if($product_id!='' && !empty($attributes))
                {
                    $variation_id=$this->find_matching_product_variation_id($product_id, $attributes);
                    $_product = wc_get_product($variation_id);
                    if($this->is_purchasable($_product))
                    {
                        $out=array('variation_id'=>$variation_id, 'status'=>true, 'status_msg'=>__('Success', 'wt-smart-coupons-for-woocommerce'));
                    }else
                    {
                        $out['status_msg']=__('Sorry! this product is not available for giveaway.', 'wt-smart-coupons-for-woocommerce');
                    }
                }    
            }
        }

        echo json_encode($out);
        wp_die();
    }

    /**
     * Function for getting variation id from product and selected attributes
     * @param $prodcut_id Given Product Id.
     * @param $attributes Attribute values ad key value pair.
     * @since 1.4.0
     */
    public function find_matching_product_variation_id($product_id, $attributes)
    {
        return (new \WC_Product_Data_Store_CPT())->find_matching_product_variation(
            new \WC_Product($product_id),
            $attributes
        );
    }

    /**
     * Helper function to get giveaway product discount text
     * @since 1.4.0 
     */
    public function get_give_away_discount_text($coupon_code=0, $product_data=array())
    {
        if($coupon_code>0)
        {
            if(is_int($coupon_code))
            {
                $coupon_id = $coupon_code;
            } else {
                $coupon_id  = wc_get_coupon_id_by_code( $coupon_code );
            }
            $wt_product_discount_amount     = 100;
            $wt_product_discount_type       = 'percent';
        
        }else
        {
            $dummy_qty_price=self::get_dummy_qty_price();
            $product_data=(empty($product_data) ? $dummy_qty_price : $product_data); 
            $wt_product_discount_amount=(isset($product_data['price']) ? $product_data['price'] : $dummy_qty_price['price']);
            $wt_product_discount_type=(isset($product_data['price_type']) ? $product_data['price_type'] : $dummy_qty_price['price_type']);
        }
      
        
        if(''==$wt_product_discount_amount  || ''==$wt_product_discount_type)
        {
            return '100%';
        }
        switch($wt_product_discount_type)
        {
            case 'percent': 
                $discount_text = $wt_product_discount_amount.'%';
                break;
            default:
                $discount_text = Wt_Smart_Coupon_Admin::get_formatted_price( $wt_product_discount_amount );
        }
        return $discount_text;
    }

    /** 
     * This function will hook a callback function to add giveaway products to the cart
     */
    public function set_hook_to_add_giveaway_products($coupon_code)
    {
        if(self::$specific_product_addtocart_hooked===false)
        {
            self::$specific_product_addtocart_hooked=true;
            
            /* schedule after coupon applied */ 
            add_action('woocommerce_applied_coupon', array($this, 'add_free_product_into_cart'), 10, 1);
        } 
    }

    /**
     *  When the giveaway scenario: 
     *  1. The giveaway condition is specific product 
     *  2. Only single prodcut with 100% discount
     *  3. Apply repeatedly enabled
     *  This method will be called when product quantity is updated
     *  @since 1.4.0
     *  @param      string      $cart_item_key      Cart item key
     *  @param      int         $quantity
     *  @param      int         $old_quantity
     *  @param      object      $cart
     */
    public function check_to_add_giveaway($cart_item_key, $quantity, $old_quantity, $cart)
    {
        $cart_item_data = isset($cart->cart_contents[$cart_item_key]) ? $cart->cart_contents[$cart_item_key] : null;
        
        if(is_null($cart_item_data))
        {
            return;
        }
        if(self::is_a_free_item($cart_item_data))
        {
            return; /* already a free item so no need to check */
        }
        if($old_quantity<$quantity) //quantity increased
        {
            $cart=WC()->cart;
            $coupons=$cart->get_applied_coupons();
            foreach($coupons as $coupon_code)
            {
                $coupon_code=wc_format_coupon_code($coupon_code);
                $coupon = new WC_Coupon($coupon_code);
                if(!$coupon)
                {
                    continue;
                }
                if(self::is_bogo($coupon))
                {
                    $coupon_id=$coupon->get_id();
                    $bogo_customer_gets = $this->get_coupon_meta_value($coupon_id, '_wt_sc_bogo_customer_gets');
                    if('specific_product'===$bogo_customer_gets)
                    {
                        /* recalculate the apply frequency quantity with the newly added quantity */
                        $this->recalculate_apply_frequency_count($coupon);

                        $this->add_free_product_into_cart($coupon_code);
                    }
                }
            }
        }
    }

    /**
     *  Get free product added success message
     *  @since 1.4.0
     */
    public function get_free_product_added_message($product, $coupon_code, $giveaway_data=array())
    {
        $message='';
        if(is_int($product))
        {
            $product = wc_get_product($product);
        }
        if($product)
        {
            if(empty($giveaway_data))
            {
                $giveaway_data=$this->get_product_giveaway_data($product->get_id(), $coupon_code);
            }
            if($this->is_full_free_item($product, $giveaway_data))
            {
                $message=__("Greetings! you've got a free gift!", 'wt-smart-coupons-for-woocommerce');

            }else
            {
                $discount_text = $this->get_give_away_discount_text(0, $giveaway_data);
                $message=sprintf(__("You're in luck! %s A free product is added to your cart at a %s discount.", 'wt-smart-coupons-for-woocommerce'), '<br/>', $discount_text);
            }
        } 

        /**  
         *  @deprecated 1.4.0 Use the 'wt_sc_alter_free_product_added_message' filter instead.
         */
        $message=apply_filters_deprecated('wt_smart_coupon_free_product_added_message', array($message, $product->get_id(), $coupon_code), '1.4.0', 'wt_sc_alter_free_product_added_message');

        return apply_filters('wt_sc_alter_free_product_added_message', $message, $product, $coupon_code);
    }

    /**
     *  Add Giveaway product into cart ( When product is single )
     *  @since 1.4.0  
     */
    public function add_free_product_into_cart($coupon_code)
    {
        $cart=WC()->cart;
        $coupons=$cart->get_applied_coupons();
        $coupon_code=wc_format_coupon_code($coupon_code);   
        if(!in_array($coupon_code, $coupons))
        {
            return;
        } 
        
        $coupon_id=wc_get_coupon_id_by_code($coupon_code);
        $free_products=self::get_giveaway_products($coupon_id);
        if(!empty($free_products))
        {          
            $first_product = wc_get_product($free_products[0]);
            if(sizeof($free_products)== 1 && $this->is_purchasable($first_product) && 'variable'!==$first_product->get_type()) /* single product with no variations */
            {
                $item_id=$free_products[0];
                $giveaway_data=$this->get_product_giveaway_data($item_id, $coupon_code); 
                if($this->is_full_free_item($first_product, $giveaway_data)) /* add to cart */
                {    
                    /* This function will prepare quantity based on coupon frequency. If apply repeatedly enabled */
                    $giveaway_qty=$this->prepare_quantity_based_on_apply_frequency($coupon_id, $giveaway_data['qty']);

                    //get cart item data
                    $product_cart_item_qty=self::get_product_cart_item_qty($item_id, $coupon_code);
                    
                    if(empty($product_cart_item_qty)) /* product does not exists in the cart */
                    {
                        $this->add_item_to_cart($item_id, $giveaway_qty, $coupon_code);
                        $success_message=$this->get_free_product_added_message($first_product, $coupon_code, $giveaway_data);
                        if($success_message!="")
                        {
                            wc_add_notice($success_message, 'success');
                        }
                    }else
                    {
                        $total_qty_in_cart=array_sum($product_cart_item_qty);
                        if($total_qty_in_cart<$giveaway_qty) //lesser qty in cart. Case when apply repeatedly enabled and customer increased the cart item quantity
                        {                      
                            $this->add_item_to_cart($item_id, ($giveaway_qty-$total_qty_in_cart), $coupon_code);
                        }
                    }
                }else
                {
                    $this->set_hook_to_show_giveaway_products();
                }
            }else
            {
                $this->set_hook_to_show_giveaway_products();
            }
        }
    }

    /**
     *  Remove BOGO eligible session when the corresponding coupon was removed
     *  @since 1.4.0 
     *  @param coupon code
     */
    public static function remove_bogo_eligible_session($coupon_code)
    {
        $bogo_eligible=self::get_bogo_eligible_session();
        $coupon_code=wc_format_coupon_code($coupon_code);
        if(isset($bogo_eligible[$coupon_code]))
        {
            unset($bogo_eligible[$coupon_code]);
            WC()->session->set(self::$bogo_eligible_session_id, $bogo_eligible);
        }
    }

    /**
     *  Get BOGO eligible sessions if exists
     *  @since 1.4.0 
     *  @return     array   Empty array if not exists, otherwise an array with the session info
     */
    public static function get_bogo_eligible_session()
    {
        $bogo_eligible=WC()->session->get(self::$bogo_eligible_session_id);
        return (is_null($bogo_eligible) ? array() : $bogo_eligible);
    }

    /**
     *  Add the coupon code to BOGO eligible session array
     *  @since 1.4.0 
     *  @param int      coupon id
     *  @param string   value for BOGO eligible session. Here BOGO available message, BOGO fully availed info etc
     */
    public static function set_bogo_eligible_session($coupon_id, $data)
    {
        $bogo_eligible=self::get_bogo_eligible_session();
        $coupon_code=wc_format_coupon_code(wc_get_coupon_code_by_id($coupon_id));
        if(!isset($bogo_eligible[$coupon_code]) || (isset($bogo_eligible[$coupon_code]) && $bogo_eligible[$coupon_code]==""))
        {
            $bogo_eligible[$coupon_code]=$data;
            WC()->session->set(self::$bogo_eligible_session_id, $bogo_eligible);
        }
    }

    /**
     *  Error/Validation messages when giveaway products are adding to cart.
     *  @since 1.4.0
     *  @param string $reason reason string
     *  @param array $extra_args extra arguments to process the message
     *  @param string $coupon_type coupon type
     */
    public static function set_add_to_cart_messages($reason, $extra_args=array(), $coupon_type=null)
    {
        $out='';
        switch($reason)
        {
            case "product_id_missing":
            case "coupon_id_missing":
            case "product_not_under_giveaway_list":
                $out=__("Oops! It seems like you've made an invalid request. Please try again.", 'wt-smart-coupons-for-woocommerce');
                break;
            case "product_is_not_a_bogo_product":
                $out=__("Oops! It seems like you've moved an invalid product to cart. Please try again.", 'wt-smart-coupons-for-woocommerce');
                break;
            case "product_max_quantity_reached":
                $out=__("You've exceeded the maximum quantity of products to avail the giveaway.", 'wt-smart-coupons-for-woocommerce');
                break;
            case "coupon_max_quantity_reached":
                $out=__("You've exceeded the maximum quantity allowed as a giveaway.", 'wt-smart-coupons-for-woocommerce');
                break;
            case "no_free_product_in_the_cart":
                $out=__("Something went wrong! It seems like there are no products available for this coupon. Please contact our support team.", 'wt-smart-coupons-for-woocommerce');
                break;
            case "already_availed_bogo":
                $out=__("Seems like you have already moved all the giveaway products in the cart.", 'wt-smart-coupons-for-woocommerce');
                break;
            default:
                $out=__("Oops! It seems like you've made an invalid request. Please try again.", 'wt-smart-coupons-for-woocommerce');
        }

        if(isset($extra_args['apply_frequency']) && 'repeat'==$extra_args['apply_frequency'])
        {
            $out.=" ".__("Please add more products to cart to avail more giveaway.", 'wt-smart-coupons-for-woocommerce');
        }

        $msg=apply_filters('wt_sc_alter_giveaway_addtocart_messages', $out, $reason, $extra_args, $coupon_type);

        wc_add_notice($msg, 'error');
        wc_print_notices();
    }

    /**
     *  Ajax action function for adding Giveaway products into cart.
     *  @since 1.4.0
     */
    public function add_to_cart()
    {
        check_ajax_referer( 'wt_smart_coupons_public', '_wpnonce' );

        $coupon_id = (isset($_POST['coupon_id']) ?  absint($_POST['coupon_id']) : 0);
        $product_id = (isset($_POST['product_id']) ?  absint($_POST['product_id']) : 0);
        $variation_id = (isset($_POST['variation_id']) ?  absint($_POST['variation_id']) : 0);
        $add_to_cart_all = (isset($_POST['add_to_cart_all']) ?  absint($_POST['add_to_cart_all']) : 0);

        if(0===$coupon_id)
        {
            self::set_add_to_cart_messages("coupon_id_missing");
            wp_die();
        }
        if(0===$add_to_cart_all) /* individual add to cart */
        {
            if(0===$product_id)
            {
                self::set_add_to_cart_messages("product_id_missing", array('coupon_id'=>$coupon_id));
                wp_die();
            }
        }

        $coupon=new WC_Coupon($coupon_id);
        $coupon_code=wc_format_coupon_code($coupon->get_code());
        if(self::is_bogo($coupon))
        {
            $bogo_customer_gets = $this->get_coupon_meta_value($coupon_id, '_wt_sc_bogo_customer_gets');
            if('specific_product'===$bogo_customer_gets)
            {
                $this->specific_product_add_to_cart($coupon, $coupon_id, $coupon_code, $bogo_customer_gets, $product_id, $variation_id);
            }

        }else
        {
            $this->non_bogo_add_to_cart($coupon, $coupon_id, $coupon_code, $bogo_customer_gets, $product_id, $variation_id);
        }
        
        $notices=wc_get_notices('error');
        if(count($notices)>0)
        {
            $last_error=end($notices);
            if(isset($last_error['notice']))
            {
                echo '<ul class="woocommerce-error" role="alert">
                        <li>'.wp_kses_post($last_error['notice']).'</li>
                </ul>';
                wc_clear_notices(); /* to avoid notice printing on page refresh */ 
                wp_die();
            }
        }else
        {
            echo 'success'; /* no translation required */
            wp_die();
        }
    }


    /**
     *  Ajax sub function
     *  Add to cart for non BOGO coupon types
     *  @since 1.4.0
     */
    private function non_bogo_add_to_cart($coupon, $coupon_id, $coupon_code, $bogo_customer_gets, $product_id, $variation_id)
    {
        $free_product_id_arr=self::get_giveaway_products($coupon_id);
        $item_id=0;
        if(in_array($variation_id, $free_product_id_arr))
        {
            $item_id=$variation_id;

        }elseif(in_array($product_id, $free_product_id_arr))
        {
            $item_id=$product_id;
        }else
        { 
            self::set_add_to_cart_messages("product_not_under_giveaway_list", array('coupon_id'=>$coupon_id), $coupon->get_discount_type());
            wp_die();
        }

        //get cart item data
        $total_qty=self::get_total_coupon_cart_item_qty($coupon_code); //total cart quantity for the coupon
        
        /* allowed maximum quantity */
        $discount_quantity=1;

        if(empty($total_qty)) /* product does not exists in the cart */
        {
            /* no free product in the cart */
            $this->add_item_to_cart(($variation_id>0 ? $variation_id : $product_id), $discount_quantity, $coupon_code);

        }else
        {           
            $total_qty=array_sum($total_qty);
            if($discount_quantity>$total_qty) /* balance quantity exists */
            {
                $this->add_item_to_cart(($variation_id>0 ? $variation_id : $product_id), ($discount_quantity - $total_qty), $coupon_code);

            }else
            {
                self::set_add_to_cart_messages(
                    "coupon_max_quantity_reached",
                    array(
                        'customer_gets'=>$bogo_customer_gets,
                        'max_qty'=>$discount_quantity,
                        'item_id'=>$item_id,
                        'coupon_id'=>$coupon_id,
                        'apply_frequency'=>'once',
                    ), 
                    $coupon->get_discount_type()
                );
                wp_die();
            }
        }
    }    

    /**
     *  Ajax sub function
     *  Add to cart product on `specific_product` as coupon option
     *  @since 1.4.0
     */
    private function specific_product_add_to_cart($coupon, $coupon_id, $coupon_code, $bogo_customer_gets, $product_id, $variation_id)
    {
        $coupon_code=wc_format_coupon_code($coupon_code);
        $bogo_product_condition = $this->get_coupon_meta_value($coupon_id, '_wt_sc_bogo_product_condition');
        $bogo_products=$this->get_all_bogo_giveaway_products($coupon_id);
        
        $frequency=$this->get_coupon_applicable_count($coupon_id, $coupon_code);

        if('and'==$bogo_product_condition) //Add all to cart
        {
            /**
             *  Giveaway max quantity checking
             *  Note: `$bogo_products` is a reference argument for the below function 
             */
            $is_giveaway_fully_added=$this->check_giveaway_max_quantity($coupon_code, $coupon_id, $bogo_customer_gets, $bogo_product_condition, $bogo_products, $frequency, array('update_quantity'=>true)); 
            
            if(!empty($bogo_products)) /* after checking the existing items, any remaining items to be added */
            {
                $is_giveaway_fully_added=false;
                $product_id_arr=Wt_Smart_Coupon_Security_Helper::sanitize_item($_POST['product_id_arr'], 'absint_arr');
                $variation_id_arr=Wt_Smart_Coupon_Security_Helper::sanitize_item($_POST['variation_id_arr'], 'absint_arr');
                
                foreach($product_id_arr as $key=>$product_id)
                {
                    $variation_id=(isset($variation_id_arr[$key]) ? $variation_id_arr[$key] : 0);
                    if($variation_id>0)
                    {
                        if(isset($bogo_products[$variation_id]))
                        {
                            $giveaway_qty=$bogo_products[$variation_id]['qty'];

                        }elseif(isset($bogo_products[$product_id]))
                        {
                            $giveaway_qty=$bogo_products[$product_id]['qty'];
                        }
                        $giveaway_qty=$this->prepare_quantity_based_on_apply_frequency($coupon_id, $giveaway_qty);
                        $this->add_item_to_cart($variation_id, $giveaway_qty, $coupon_code);

                    }else
                    {
                        if(isset($bogo_products[$product_id]))
                        {
                            $giveaway_qty=$this->prepare_quantity_based_on_apply_frequency($coupon_id, $bogo_products[$product_id]['qty']);
                            $this->add_item_to_cart($product_id, $giveaway_qty, $coupon_code);
                        }
                    }
                }
            }

            if($is_giveaway_fully_added)
            {
                self::set_add_to_cart_messages(
                    "already_availed_bogo", 
                    array(
                        'coupon_id'=>$coupon_id, 
                        'customer_gets'=>$bogo_customer_gets,
                        'apply_frequency'=>$this->get_coupon_meta_value($coupon_id, '_wt_sc_bogo_apply_frequency'),
                    ), 
                    self::$bogo_coupon_type_name);

                wp_die();
            }
        }
    }

    /**
     *  Update cart item quantity
     *  @since 1.4.0
     */
    private function update_cart_qty($cart_item_key, $quantity)
    {
        $cart = WC()->cart; 
        $cart->set_quantity($cart_item_key, $quantity);
    }
    
    /**
     *  Giveaway add to cart function
     *  @since 1.4.0
     *  @param int      $item_id        Product/variation id
     *  @param int      $quantity       Quantity
     *  @param string   $coupon_code    Coupon code
     *  @param int      $category       Category ID, On category wise giveaway [Optional]
     */
    private function add_item_to_cart($item_id, $quantity, $coupon_code, $category='')
    {
        $product = wc_get_product($item_id);
        if($product)
        {
            if(!$this->is_purchasable($product))
            {
                return false;
            }
            if('variable'===$product->get_type())
            {
                return false; /* not possible to add variable parent  */  
            }

            if(!$product->has_enough_stock($quantity))
            {
                $quantity = $product->get_stock_quantity();
                if($quantity===0)
                {
                    return false;
                }
            }
            
            $variation_id   = 0;
            $product_id     = $item_id;
            $variation      = array();

            if($product && 'variation'===$product->get_type())
            {
                $variation_id = $product_id;
                $product_id   = $product->get_parent_id();
                $variation    = $product->get_variation_attributes();
            }

            $cart_item_data = array(
                'free_product'          => 'wt_give_away_product',
                'free_gift_coupon'      => $coupon_code,
                'free_category'         => $category
            );

            $cart_item_data = apply_filters('wt_sc_alter_giveaway_cart_item_data_before_add_to_cart', $cart_item_data, $product_id, $variation_id, $quantity);

            return WC()->cart->add_to_cart($product_id, $quantity, $variation_id, $variation, $cart_item_data);
        }

        return false;
    }

    /**
     *  Checks the product purchasable or not.
     *  If varaible product, checks any of the variation is purchasable, and returns the variation id if successfull, otherwise false will return
     *  @since 1.4.0
     *  @param  Wc_Product
     *  @param  variation attributes    optional    only applicable for variable products, If any of the variation was purchasable, the attributes of first purchasable variation will assigned to this variable.
     *  @return boolean/integer
     */
    public function is_purchasable($_product, &$variation_attributes=array())
    {
        if(is_int($_product))
        {
            $_product=wc_get_product($_product);
        }

        if(!$_product)
        {
            return false;
        }

        if($_product->is_type('variable')) /* variation choosing option */
        {
            $variations=$_product->get_available_variations();
            if(empty($variations) && false!==$variations)
            {
                return false;
            }else
            {
                $is_purchasable=false;
                foreach($variations as $variation)
                { 
                    $variation_id=$variation['variation_id'];
                    $variation_product=wc_get_product($variation_id);
                    if($this->is_purchasable($variation_product)) /* any of the product is purchasable */
                    {
                        $variation_attributes=$variation['attributes'];
                        $is_purchasable=true;
                        break;
                    }
                }

                if(!$is_purchasable) /* all variations are not purchasable */
                {
                    return false;
                }else
                {
                    return $variation_id; // ID of first purchasable variation
                }
            }
        }else
        {
            if(!$_product->has_enough_stock(1))
            {
                $quantity = $_product->get_stock_quantity();
                if($quantity===0)
                {
                    return false;
                }
            }
        }

        return $_product->is_purchasable();
    }

    /**
     * Function for displaying description for Giveaway product on cart page
     */
    public function display_giveaway_product_description( $cart_item )
    {
        $product_id     = $cart_item['product_id'];
        $variation_id   = $cart_item['variation_id'];

        if(self::is_a_free_item($cart_item))
        {
            $coupon_code    = wc_format_coupon_code($cart_item['free_gift_coupon']);
            $item_id        = ($variation_id>0 ? $variation_id : $product_id);
            $product        = wc_get_product($item_id);

            $giveaway_data=$this->get_product_giveaway_data($item_id, $coupon_code, $cart_item);

            if($this->is_full_free_item($product, $giveaway_data))
            {
                $free_gift_text = __("Congrats! you've got a free gift from us!", 'wt-smart-coupons-for-woocommerce');
            }else
            {
                $discount_text = $this->get_give_away_discount_text(0, $giveaway_data); /* set coupon id as zero(first argument) because we have already fetched data */
                $free_gift_text = sprintf(__("You're in luck! A free product is added to the cart with a %s discount.", 'wt-smart-coupons-for-woocommerce'), $discount_text);
            }

            $info_text=apply_filters('wt_sc_alter_giveaway_cart_lineitem_text', '<p style="color:green;clear:both">'.$free_gift_text.'</p>', $cart_item);
            echo wp_kses_post($info_text);
        }

    }

    /**
     * Update Cart item values
     */
    public function update_cart_item_values($cart_item, $product_id = 0, $variation_id = 0, $qty = 1 )
    {
        if(self::is_a_free_item($cart_item))
        {
            $coupon_code = wc_format_coupon_code($cart_item['free_gift_coupon']);
            $coupon=new WC_Coupon($coupon_code);
            if($coupon)
            {
                $coupon_id=$coupon->get_id();
                if(wc_string_to_bool($this->get_coupon_meta_value($coupon_id, 'wt_apply_discount_before_tax_calculation'))===false)
                {
                    return $cart_item;
                } 

                $item_id = ($cart_item['variation_id']>0 ? $cart_item['variation_id'] : $cart_item['product_id']);
                $product = wc_get_product($item_id);
                $giveaway_data=$this->get_product_giveaway_data($item_id, $coupon_code, $cart_item);

                $discount=self::get_available_discount_for_giveaway_product($product, $giveaway_data); 

                $product_price=self::get_product_price($product);

                $discounted_price = ($product_price - $discount);
                $cart_item['data']->set_price($discounted_price);
                $cart_item['data']->set_regular_price($product_price);
            }
        }

        return $cart_item;
    }

    /**
     *  Update cart item value
     */
    public function update_cart_item_in_session( $session_data = array(), $values = array(), $key = '' )
    {
        if(self::is_a_free_item($session_data))
        {
            $coupon_code = wc_format_coupon_code($session_data['free_gift_coupon']);
            $coupon_id =  wc_get_coupon_id_by_code($coupon_code) ;
            if(wc_string_to_bool($this->get_coupon_meta_value($coupon_id, 'wt_apply_discount_before_tax_calculation'))===false)
            {
                return $session_data;
            }
            
            $qty =(isset($session_data['quantity']) ?  $session_data['quantity'] :  1);
            
            $session_data = $this->update_cart_item_values($session_data, $session_data['product_id'], $session_data['variation_id'], $qty);
        }
        return $session_data;
    }

    /**
     *  Function for updating cart item price display.
     */
    public function update_cart_item_price($price, $cart_item)
    {
        return $this->alter_cart_item_price($price, $cart_item, false);
    }

    /**
     * Update cart item quantity field non editable
     */
    public function update_cart_item_quantity_field($product_quantity = '', $cart_item_key = '', $cart_item = array() )
    {
        if(self::is_a_free_item($cart_item))
        {
            $product_quantity = sprintf( '%s <input type="hidden" name="cart[%s][qty]" value="%s" />', $cart_item['quantity'], $cart_item_key, $cart_item['quantity']);
        }
        return $product_quantity;
    }

    /**
     *  Add free gift item price details into cart and checkout.
    */
    public function add_give_away_product_discount()
    {
        $cart_object=WC()->cart;
        if($this->is_cart_contains_free_products('', $cart_object))
        {     
            $cart_items=$cart_object->get_cart();
            foreach($cart_items as $cart_item_key=>$cart_item)
            {
                if(self::is_a_free_item($cart_item))
                {
                    $coupon_code=(isset($cart_item['free_gift_coupon']) ? $cart_item['free_gift_coupon'] : '');
                    if(!empty($coupon_code))
                    {
                        $coupon_code=wc_format_coupon_code($coupon_code);
                        $coupon=new WC_Coupon($coupon_code);
                        if($coupon && !self::is_bogo($coupon))
                        {
                            $coupon_id=$coupon->get_id();
                            if(wc_string_to_bool($this->get_coupon_meta_value($coupon_id, 'wt_apply_discount_before_tax_calculation'))===true) /* currently only applicable for non BOGO */
                            {
                                continue;
                            }

                            $item_id = ($cart_item['variation_id']>0 ? $cart_item['variation_id'] : $cart_item['product_id']);
                            $product = wc_get_product($item_id);
                            $giveaway_data=$this->get_product_giveaway_data($item_id, $coupon_code, $cart_item);

                            $discount=(float) self::get_available_discount_for_giveaway_product($product, $giveaway_data)*$cart_item['quantity'];

                            $label_text=apply_filters('wt_sc_alter_giveaway_cart_summary_label', __('Free gift', 'wt-smart-coupons-for-woocommerce' ), $cart_item);                          
                            
                            $discount_price=Wt_Smart_Coupon_Admin::get_formatted_price((number_format((float) $discount, 2, '.', '')));
                            $discount_price=apply_filters('wt_sc_alter_giveaway_cart_summary_value', '-'.$discount_price, $discount, $cart_item); 
                            ?>
                            <tr class="woocommerce-give_away_product wt_give_away_product">
                                <th><?php echo wp_kses_post($label_text); ?></th>
                                <td><?php echo wp_kses_post($discount_price); ?></td>                      
                            </tr>
                            <?php
                        }
                    }
                } 
            }
        }
    }

    /**
     * Filter function for updating cart item price ( Displaying cart item price in cart and checkout page )
     * @param $price Price html.
     * @param $cart_item Cart item object
     */
    public function add_custom_cart_item_total($price, $cart_item)
    {
        return $this->alter_cart_item_price($price, $cart_item);
    }

    private function alter_cart_item_price($price, $cart_item, $is_total=true)
    {
        $out=$price;
        if(self::is_a_free_item($cart_item))
        {
            $coupon_code    = wc_format_coupon_code($cart_item['free_gift_coupon']);
            $coupon_id      = wc_get_coupon_id_by_code($coupon_code);
            $item_id = ($cart_item['variation_id']>0 ? $cart_item['variation_id'] : $cart_item['product_id']);
            $product = wc_get_product($item_id);
            $product_price = self::get_product_price($product);
            $giveaway_data=$this->get_product_giveaway_data($item_id, $coupon_code, $cart_item);
            
            $discount=self::get_available_discount_for_giveaway_product($product, $giveaway_data);
            $sale_price_after_discount = ($product_price - $discount);
            
            if($is_total)
            {
                $sale_price_after_discount = $sale_price_after_discount * $cart_item['quantity'];
                $product_price=$product_price * $cart_item['quantity'];
                $discount = $discount * $cart_item['quantity'];

                if(!isset(self::$bogo_discounts[$coupon_code]))
                {
                    self::$bogo_discounts[$coupon_code] = 0;
                }

                self::$bogo_discounts[$coupon_code] += $discount;
            }

            $out = '<del><span>'.wc_price($product_price).'</span></del> <br /><span>'.wc_price($sale_price_after_discount).'</span>';
        }

        return $out; 
    }

    /**
     *  Calculate the cart total after reducing the free product price.
    */
    public function discounted_calculated_total($cart_object)
    {
        $new_total = $cart_object->get_total('edit');
        if($this->is_cart_contains_free_products('', $cart_object))
        {     
            $cart_items=$cart_object->get_cart();
            foreach($cart_items as $cart_item_key=>$cart_item)
            {
                if(self::is_a_free_item($cart_item))
                {
                    $coupon_code=$cart_item['free_gift_coupon'];
                    if(!empty($coupon_code))
                    {
                        $coupon_code=wc_format_coupon_code($coupon_code);
                        $coupon=new WC_Coupon($coupon_code);
                        if($coupon)
                        {
                            $coupon_id=$coupon->get_id();
                            if(wc_string_to_bool($this->get_coupon_meta_value($coupon_id, 'wt_apply_discount_before_tax_calculation'))===true)
                            {
                                continue;
                            } 
                            
                            $item_id = ($cart_item['variation_id']>0 ? $cart_item['variation_id'] : $cart_item['product_id']);
                            $product = wc_get_product($item_id);
                            $giveaway_data=$this->get_product_giveaway_data($item_id, $coupon_code, $cart_item);

                            $discount=self::get_available_discount_for_giveaway_product($product, $giveaway_data);
                            $new_total = $new_total-($discount*$cart_item['quantity']);
                        }
                    }
                } 
            }
            $new_total=round($new_total, $cart_object->dp);
            $cart_object->set_total($new_total);
        }
    }

    /**
     *  Removes any free products from the cart if their related coupon is not present in the cart
     */
    public function check_any_free_products_without_coupon()
    {
        if(is_admin())
        {
            return; 
        }
        $cart = ((is_object(WC()) && isset(WC()->cart)) ? WC()->cart : null);
        if(is_object( $cart ) && is_callable(array($cart, 'is_empty')) && ! $cart->is_empty()) 
        {
            $coupons=$cart->get_applied_coupons();           
            $cart_items = $cart->get_cart();
            $cart_items =((isset($cart_items) && is_array($cart_items)) ? $cart_items : array());            
            foreach($cart_items as $cart_item_key => $cart_item)
            {                  
                if(self::is_a_free_item($cart_item))
                {
                    if(!in_array($cart_item['free_gift_coupon'], $coupons)) /* coupon not found in the applied coupon list */
                    {
                        $cart->remove_cart_item($cart_item_key); /* remove the free item */
                    }
                }
            }
        }                
    }

    /**
     * Remove giveaway available session. If already added    
     * @since 1.4.0
     */
    public function remove_giveaway_available_session($coupon_code)
    {
        self::remove_bogo_eligible_session($coupon_code); 
    }

    /**
     * Remove free product from cart (Hook to When Coupon removed)
     */
    public function remove_free_product_from_cart($coupon_code)
    {
        $cart=WC()->cart;
        $applied_coupons  = $cart->get_applied_coupons();
        if(isset($coupon_code) && !empty($coupon_code) && !in_array($coupon_code, $applied_coupons))
        {
            foreach($cart->get_cart() as $cart_item_key => $cart_item )
            {
                if(self::is_a_free_item($cart_item, $coupon_code))
                {
                    $cart->remove_cart_item( $cart_item_key );
                }
            }         
        }
    }


    /**
     * Add free Prodcut details on cart item list.
    */
    public function add_free_product_details_into_order($item, $cart_item_key, $values, $order)
    {
        if(!self::is_a_free_item($values))
        {
            return;
        }        
        $item->add_meta_data('free_product' , $values['free_product']);
        $item->add_meta_data('free_gift_coupon' , $values['free_gift_coupon']);
    }


    /**
     * Display free product discount detail in order details.
     */
    public function woocommerce_get_order_item_totals($total_rows, $order, $tax_display)
    {
        $out=array();
        $order_items = $order->get_items();
        foreach($order_items as $order_item_id=>$order_item)
        {
            $giveaway_info=$this->prepare_giveaway_info_for_order($order_item_id, $order_item, $order);
            if($giveaway_info)
            {
                $label_text=apply_filters('wt_sc_alter_order_detail_giveaway_info_label', __('Free gift:', 'wt-smart-coupons-for-woocommerce'), $order_item, $order_item_id, $order);
                $out['free_product_'.$order_item_id]=array(
                    'label'     => $label_text,
                    'value'     => $giveaway_info,
                );
            }
        }

        if(!empty($out))
        {
            $offset = array_search('shipping', array_keys($total_rows));
            $total_rows = array_merge(
                array_slice($total_rows, 0, $offset),
                $out,
                array_slice($total_rows, $offset, null)
            );
        }

        return $total_rows;
    }

    /**
     * Manage item meta on order page
     */  
    public function unset_free_product_order_item_meta_data($formatted_meta, $item)
    {
        foreach($formatted_meta as $key => $meta)
        {
            if(in_array($meta->key, array('free_product', 'free_gift_coupon', 'free_category')))
            {
                unset($formatted_meta[$key]);
            }            
        }
        return $formatted_meta;
    }


    /**
     *  Get current product cart item quantity
     *  @since 1.4.0
     *  @return array
     */
    public static function get_product_cart_item_qty($item_id, $coupon_code)
    {
        $out=array();
        foreach(WC()->cart->get_cart() as $cart_item_key=>$cart_item)
        {
            if($cart_item['product_id']==$item_id || $cart_item['variation_id']==$item_id) //product found
            {
                if(self::is_a_free_item($cart_item, $coupon_code))
                {
                    $out[$cart_item_key]=$cart_item['quantity'];                    
                }
            }   
        }
        return $out;
    }

    /**
     *  Checks the current cart item is a free item. Or a free item under the given coupon code
     *  @since 1.4.0
     *  @return bool
     */
    public static function is_a_free_item($cart_item, $coupon_code="")
    {
        $out=isset($cart_item['free_gift_coupon']) && isset($cart_item['free_product']) && 'wt_give_away_product'==$cart_item['free_product'];
        if($coupon_code!="" && $out)
        {
            $out=$cart_item['free_gift_coupon']==$coupon_code;
        }
        $out=apply_filters('wt_sc_alter_is_free_cart_item', $out, $cart_item, $coupon_code); /* other plugins to confirm their giveaway item */
        return $out;
    }

    /**
     *  Get total quantity of current coupon free products
     *  @return array 
     */
    public static function get_total_coupon_cart_item_qty($coupon_code)
    {
        $out=array();
        foreach(WC()->cart->get_cart() as $cart_item_key=>$cart_item)
        {
            if(self::is_a_free_item($cart_item, $coupon_code))
            {
                $out[$cart_item_key]=$cart_item['quantity'];                    
            }   
        }
        return $out;
    }

    /**
     * Check whether cart contains any Giveaway products from given coupon
     * @return bool
     */
    public function is_cart_contains_free_products($coupon_code='', $cart=null)
    {
        $cart=(is_null($cart) ? WC()->cart : $cart);
        $cart_items = $cart->get_cart();
        $wt_give_away_meta=array_column($cart_items, 'free_product');
        
        $out=in_array('wt_give_away_product', $wt_give_away_meta); 
        
        if($coupon_code!="" && $out)
        {
            $wt_give_away_coupon_meta=array_column($cart_items, 'free_gift_coupon');
            $out=in_array($coupon_code, $wt_give_away_coupon_meta);
        }

        return $out;
    }


    /**
     *  Remove/Update quantity of giveaway items when eligibility count was changed. This will be called on `wp_loaded` hook
     *  @since  1.4.0
     */
    public function adjust_giveaway_count_when_eligibility_changed()
    {
        $cart=WC()->cart;
        if(self::$giveaway_count_adjust===true || is_admin() || !Wt_Smart_Coupon_Public::module_exists('coupon_restriction') || is_null(WC()->session) || is_null($cart))
        {
            return;
        }       

        self::$giveaway_count_adjust=true;      
        
        $applied_coupons  = $cart->applied_coupons;
        $applied_coupons = (!is_array($applied_coupons) ? array() : $applied_coupons);
        $cart_items=$cart->get_cart(); 
        foreach($applied_coupons as $coupon_code)
        {
            $coupon_code=wc_format_coupon_code($coupon_code);
            $coupon=new WC_Coupon($coupon_code);
            if(self::is_bogo($coupon))
            {
                $coupon_id=$coupon->get_id();
                
                $bogo_product_condition = $this->get_coupon_meta_value($coupon_id, '_wt_sc_bogo_product_condition');
                
                $frequency=$this->get_coupon_applicable_count($coupon_id, $coupon_code);

                $bogo_customer_gets = $this->get_coupon_meta_value($coupon_id, '_wt_sc_bogo_customer_gets');

                if('specific_product'===$bogo_customer_gets)
                {
                    $bogo_products=$this->get_all_bogo_giveaway_products($coupon_id);
                    $cart_available_qty=array();
                    foreach($cart_items as $item_key=>$cart_item)
                    {
                        if(self::is_a_free_item($cart_item, $coupon_code)) /* a free item under the given coupon */
                        {
                            /**
                            * @since 1.4.5
                            */
                            $item_id = $this->check_giveaway_id_match_on_multi_lang_site($cart_item, $coupon_id, $bogo_products);

                            if($item_id>0)
                            {
                                if(!isset($cart_available_qty[$item_id]))
                                {
                                    $cart_available_qty[$item_id]=array();
                                }
                                $cart_available_qty[$item_id][$item_key]=$cart_item['quantity'];

                            }else
                            {
                                //a non giveaway free product. Remove it
                                WC()->cart->remove_cart_item($item_key);
                            }
                        }
                    }

                    $total_eligibility=$frequency;
                    foreach($cart_available_qty as $item_id=>$available_qty_data)
                    {
                        if($total_eligibility<=0) //no eligibility remaining
                        {   
                            foreach($available_qty_data as $cart_item_key=>$quantity)
                            {
                                //eligibility reached. Remove it
                                WC()->cart->remove_cart_item($cart_item_key);
                            } 
                        }
                        $total_qty_in_cart=array_sum($available_qty_data);
                        
                        if('and'==$bogo_product_condition) /* product condition `and` */
                        {
                            $giveaway_qty=$this->prepare_quantity_based_on_apply_frequency($coupon_id, $bogo_products[$item_id]['qty']);
                            if($giveaway_qty<$total_qty_in_cart)
                            {
                                foreach($available_qty_data as $cart_item_key=>$quantity)
                                {
                                    if($giveaway_qty<=0) /* max giveaway quantity reached. So remove it */
                                    {
                                        WC()->cart->remove_cart_item($cart_item_key);
                                        continue;  
                                    }

                                    if($quantity>=$giveaway_qty)
                                    {
                                        $this->update_cart_qty($cart_item_key, $giveaway_qty);
                                        $giveaway_qty=0;
                                    }else
                                    {
                                        $giveaway_qty=$giveaway_qty-$quantity;
                                    }
                                }
                            }     
                            continue; //no need to execute the below codes, Its for product condition `or`
                        }
                        
                        $cr_eligibility=floor($total_qty_in_cart/$bogo_products[$item_id]['qty']);
                        if($cr_eligibility<=$total_eligibility)
                        {
                            $total_eligibility=$total_eligibility-$cr_eligibility;
                        }else /* there are some extra giveaway items */
                        {
                            $max_qty=$total_eligibility*$bogo_products[$item_id]['qty'];
                            foreach($available_qty_data as $cart_item_key=>$quantity)
                            {
                                if($max_qty<=0)
                                {
                                    //eligibile max qty reached. Remove it
                                    WC()->cart->remove_cart_item($cart_item_key);
                                    continue;
                                }

                                if($max_qty>=$quantity)
                                {
                                    $max_qty=$max_qty-$quantity;
                                }else
                                {
                                    $this->update_cart_qty($cart_item_key, $max_qty);
                                    $max_qty=0;
                                }
                            }
                        }
                    }
                }                            
            }
        }    
    }

    /**
     *  Alter coupon block title text.
     *  @since  1.4.0
     *  @param      array     $coupon_data    Coupon data
     *  @param      object    $coupon         WC_Coupon object
     *  @return     array     $coupon_data
     */
    public function alter_coupon_title_text($coupon_data, $coupon)
    {
        if(self::is_bogo($coupon))
        {
            $coupon_data['coupon_amount'] = '';
            $coupon_data['coupon_type'] = apply_filters( 'wt_sc_alter_coupon_title_text', __('Free products', 'wt-smart-coupons-for-woocommerce'), $coupon);
        }
        return $coupon_data;
    }

    /**
     *  Is product/category restriction enabled
     *  @since  1.4.0
     *  @param      int      $coupon_id    Coupon ID 
     *  @return     bool  
     */
    private function is_product_category_restriction_enabled($coupon_id)
    {
        $wt_enable_product_category_restriction='yes';
        if(Wt_Smart_Coupon_Common::module_exists('coupon_restriction'))
        {
            $wt_enable_product_category_restriction =Wt_Smart_Coupon_Restriction::get_coupon_meta_value($coupon_id, '_wt_enable_product_category_restriction');
        }

        return wc_string_to_bool($wt_enable_product_category_restriction);
    }

    /**
     *  Is apply frequency enabled and prepare the quantity based on applicable frequency
     *  @since  1.4.0
     *  @param      int      $coupon_id    Coupon ID 
     *  @param      int      $quantity     Quantity 
     *  @return     int      $quantity     Quantity 
     */
    private function prepare_quantity_based_on_apply_frequency($coupon_id, $quantity)
    {
        $wt_sc_bogo_apply_frequency = $this->get_coupon_meta_value($coupon_id, '_wt_sc_bogo_apply_frequency');
        if('repeat'==$wt_sc_bogo_apply_frequency)
        {
            $coupon_code=wc_get_coupon_code_by_id($coupon_id);
            $frequency=$this->get_coupon_applicable_count($coupon_id, $coupon_code);     
            $quantity=$quantity*$frequency;
        }
        return $quantity;
    }

    /**
     *  This method will take coupon applicable count from session created by coupon restriction module
     *  @since 1.4.0
     */ 
    private function get_coupon_applicable_count($coupon_id, $coupon_code)
    {
        $frequency=1;
        if(Wt_Smart_Coupon_Public::module_exists('coupon_restriction'))
        {
            $bogo_applicable_count=Wt_Smart_Coupon_Restriction_Public::get_bogo_applicable_count_session();
            $coupon_code=wc_format_coupon_code($coupon_code);
            $frequency=absint(isset($bogo_applicable_count[$coupon_code]) ? $bogo_applicable_count[$coupon_code] : 1);
            $frequency=($frequency<1 ? 1 : $frequency);

            $wt_sc_bogo_apply_frequency = $this->get_coupon_meta_value($coupon_id, '_wt_sc_bogo_apply_frequency');
            $frequency=('once'==$wt_sc_bogo_apply_frequency ? 1 : $frequency);
        }

        return $frequency;
    }

    /**
     *  Recalculate apply frequency count.
     *  @since  1.4.0
     *  @param  object      $coupon    WC_Coupon object 
     */
    private function recalculate_apply_frequency_count($coupon)
    {
        $coupon_id=$coupon->get_id();
        $wt_sc_bogo_apply_frequency = $this->get_coupon_meta_value($coupon_id, '_wt_sc_bogo_apply_frequency');
        if('repeat'==$wt_sc_bogo_apply_frequency)
        {
            if(Wt_Smart_Coupon_Public::module_exists('coupon_restriction'))
            {
                $coupon_restriction_obj=Wt_Smart_Coupon_Restriction_Public::get_instance();
                try {
                    $coupon_restriction_obj->wt_woocommerce_coupon_is_valid(true, $coupon);
                }catch(Exception $e)
                {
                   wc_add_notice($e->getMessage(), 'error'); 
                }
            }
        }
    }

    /**
     *  @since 1.4.0
     *  This function is used to check the giveaway max quantity based on the available giveaway quantity in cart and apply repeatedly option
     *  Applicable for `specific_product` condition
     *  
     *  @param  $coupon_code                string              coupon code
     *  @param  $coupon_id                  int                 coupon id
     *  @param  $bogo_customer_gets         string              customer gets option in giveaway
     *  @param  $bogo_product_condition     string              Product condition
     *  @param  $bogo_products              array               Array of giveaway products under the current coupon (reference argument)
     *  @param  $frequency                  int                 Applicable frequency based on apply repeatedly option   
     *  @param  $options                    array               Other optional arguments
     *                                                          $update_qty     boolean     Update existing giveaway product quantiy if mismatch found. Default: false (Do not update quantity) - Applicable for `and` product condition
     *  
     *  @return                             boolean/void        `boolean` when $bogo_product_condition is `and` and $update_quantity is true
     */
    public function check_giveaway_max_quantity($coupon_code, $coupon_id, $bogo_customer_gets, $bogo_product_condition, &$bogo_products, $frequency, $options=array())
    {

        $cart_items=WC()->cart->get_cart();
        if('and'==$bogo_product_condition)
        {
            $update_qty=isset($options['update_quantity']) ? (bool) $options['update_quantity'] : false;

            $is_giveaway_fully_added=true; // only applicable when $update_qty is true

            foreach(WC()->cart->get_cart() as $cart_item_key=>$cart_item) /* this loop is for to check the existing free items in the cart */
            {
                $item_id=0;
                if($cart_item['variation_id']>0 && isset($bogo_products[$cart_item['variation_id']]))
                {
                    $item_id=$cart_item['variation_id'];

                }elseif(isset($bogo_products[$cart_item['product_id']]))
                {
                    $item_id=$cart_item['product_id'];
                }

                if($item_id>0 && self::is_a_free_item($cart_item, $coupon_code)) /* this product is in the bogo list. Check it is a free item */
                {
                    $bogo_item_data=$bogo_products[$item_id];
                    $bogo_item_data['qty']=(absint($bogo_item_data['qty'])===0 ? 1 : $bogo_item_data['qty']);
                    
                    $giveaway_qty=$this->prepare_quantity_based_on_apply_frequency($coupon_id, $bogo_item_data['qty']);
                    if($giveaway_qty!=$cart_item['quantity']) 
                    {
                        if($update_qty)
                        {
                            //quantity mismatch so update
                            $this->update_cart_qty($cart_item_key, $giveaway_qty);
                            $is_giveaway_fully_added=false;
                        }
                    }
                    
                    if($update_qty)
                    {
                        unset($bogo_products[$item_id]); /* remove already added product from bogo list */
                    }else
                    {
                        if($giveaway_qty==$cart_item['quantity']) 
                        {
                            unset($bogo_products[$item_id]); /* remove fully added product from bogo list */
                        }
                    }
                    
                }
            }

            if($update_qty)
            {
                return $is_giveaway_fully_added;
            }

        }

    }

    public function alter_coupon_discount_amount_html($discount_amount_html, $coupon)
    {
        if(self::is_bogo($coupon))
        {
            $coupon_code = wc_format_coupon_code($coupon->get_code());
            $discount = (isset(self::$bogo_discounts[$coupon_code]) ? self::$bogo_discounts[$coupon_code] : 0);
            $discount_amount_html = wc_price($discount);
        }

        return $discount_amount_html;
    }

    /**
     *  
     *  Exclude the free giveaway products from applying other coupons.
     *  This is applicable when product is 'free giveaway`.
     *  @param bool     $valid   is valid or not
     *  @param WC_Product $product   Product instance
     *  @param WC_Product     $coupon   Coupon data
     *  @param array  $values  Cart item values.
     *  @return bool
     *  @since    1.4.1
     */
    public function exclude_giveaway_from_other_discounts($valid, $product, $coupon, $values)
    {
        if(self::is_a_free_item($values))
        {
            $valid = false;
        }

        return $valid;
    }

    /**
     *  When the giveaway scenario: 
     *  1. The giveaway condition is specific product 
     *  2. Only single prodcut with 100% discount
     *  3. Apply repeatedly enabled
     *  Update giveaway quantity when new cart item added
     * 
     *  @since 1.4.4
     * 
     */
    public function check_and_add_giveaway_on_add_to_cart($cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data)
    {
        $this->check_to_add_giveaway($cart_item_key, $quantity, 0, WC()->cart);
    }

    /**
     *  Check cart `giveaway product id` and coupon `giveaway product id` to confirm the current giveaway item belongs to the coupon.
     *  When a multi language plugin(WPML) is active then the function will compare ids of all languages with giveaway product ids to get a match
     * 
     *  @since 1.4.5
     *  @param $cart_item       array       Cart item array
     *  @param $coupon_id       int         Id of coupon
     *  @param $bogo_products   array       Associative array of giveaway products and its data
     *  @return $item_id        int         If any match found then the matched ID will return otherwise 0
     */
    private function check_giveaway_id_match_on_multi_lang_site($cart_item, $coupon_id, $bogo_products = null)
    {
        $bogo_products = is_null($bogo_products) ? self::get_all_bogo_giveaway_products($coupon_id) : $bogo_products;
        $item_id = 0;
        
        if(0 < $cart_item['variation_id'] && isset($bogo_products[$cart_item['variation_id']]))
        {
            $item_id = $cart_item['variation_id'];

        }elseif(isset($bogo_products[$cart_item['product_id']]))
        {
            $item_id = $cart_item['product_id'];
        }

        /**
         *  For multi language compatibility
         */
        if(0 === $item_id)
        {
           
            $multi_lang_obj = Wt_Smart_Coupon_Mulitlanguage::get_instance();

            if($multi_lang_obj->is_multilanguage_plugin_active())
            {
                $bogo_product_ids = array_keys($bogo_products); //product ids

                if(0 < $cart_item['variation_id']) //variable product
                {
                    /**
                     *  Take ids of all languages
                     */
                    $all_lang_ids = $multi_lang_obj->get_all_translations($cart_item['variation_id'], 'post_product');

                    if(!empty($all_lang_ids) && !empty($matching_ids = array_intersect($all_lang_ids, $bogo_product_ids)))
                    {
                        $item_id = (int) reset($matching_ids); //take first item
                    }
                }

                if(0 === $item_id)
                {  
                    /**
                     *  Take ids of all languages
                     */
                    $all_lang_ids = $multi_lang_obj->get_all_translations($cart_item['product_id'], 'post_product');

                    if(!empty($all_lang_ids) && !empty($matching_ids = array_intersect($all_lang_ids, $bogo_product_ids)))
                    {
                        $item_id = (int) reset($matching_ids); //take first item
                    }
                }
            }
        }

        return $item_id;
    }

    /**
     *  Get all giveaway product ids for cart operations.
     * 
     *  @since 1.4.5
     *  @param $post_id     int     Id of coupon
     *  @return $free_products     array     Array of giveaway product ids. Product ids will be updated to current language product ids if multi language plugin(WPML) is active
     */
    public static function get_giveaway_products($post_id)
    {
        $free_products = parent::get_giveaway_products($post_id);
        $free_products_original = $free_products; //assumes main language product id

        $multi_lang_obj = Wt_Smart_Coupon_Mulitlanguage::get_instance();

        if($multi_lang_obj->is_multilanguage_plugin_active())
        {
            $out = array();

            foreach($free_products as $product_id)
            {
                /**
                 *  Take id of product in the current language.
                 * 
                 *  @param  $product_id         int     Id of product
                 *  @param  post type           string  Post type
                 *  @param  Return original     bool    Return original if no translation found in the current language. Default: false
                 * 
                 */
                $out[] = apply_filters('wpml_object_id', $product_id, 'product', TRUE);
            }
            
            $free_products = $out;
        }

        /**
         *  Alter BOGO product ids for cart (Only applicable for frontend functionalities)
         * 
         *  @param  $free_products              array       Array of giveaway product ids. Product ids of this array was converted to current language ids if any multi lang plugin(WPML) exists.
         *  @param  $post_id                    int         Id of coupon
         *  @param  $free_products_original     array       Array of giveaway product ids. Here the product ids are the ids configured by admin from backend.
         * 
         */
        return apply_filters('wt_sc_alter_bogo_giveaway_product_ids_for_cart', $free_products, $post_id, $free_products_original);
    }


    /**
     *  Get all giveaway products and its data for cart operations.
     * 
     *  @since 1.4.5
     *  @param $post_id     int     Id of coupon
     *  @return $bogo_products     array     Associative array of giveaway products and its data. Product ids will be updated to current language product ids if multi language plugin(WPML) is active
     */
    public static function get_all_bogo_giveaway_products($post_id)
    {
        $bogo_products = parent::get_all_bogo_giveaway_products($post_id);
        $bogo_products_original = $bogo_products; //assumes main language product id

       
        $multi_lang_obj = Wt_Smart_Coupon_Mulitlanguage::get_instance();

        if($multi_lang_obj->is_multilanguage_plugin_active())
        {
            $out = array();

            foreach($bogo_products as $product_id => $product_data)
            {
                /**
                 *  Take id of product in the current language.
                 * 
                 *  @param  $product_id         int     Id of product
                 *  @param  post type           string  Post type
                 *  @param  Return original     bool    Return original if no translation found in the current language. Default: false
                 * 
                 */
                $product_id = apply_filters('wpml_object_id', $product_id, 'product', TRUE);

                $out[$product_id] = $product_data;
            }
            
            $bogo_products = $out;
        }

        /**
         *  Alter BOGO products data for cart (Only applicable for frontend functionalities)
         * 
         *  @param  $bogo_products              array       An associative array of giveaway products and its giveaway data. Product ids of this array was converted to current language ids if any multi lang plugin(WPML) exists.
         *  @param  $post_id                    int         Id of coupon
         *  @param  $bogo_products_original     array       An associative array of giveaway products and its giveaway data. Here the product ids are the ids configured by admin from backend.
         * 
         */
        return apply_filters('wt_sc_alter_bogo_giveaway_products_for_cart', $bogo_products, $post_id, $bogo_products_original);
    }
}
Wt_Smart_Coupon_Giveaway_Product_Public::get_instance();