<?php
/**
 * Auto coupon public
 *
 * @link       
 * @since 1.4.1
 *
 * @package  Wt_Smart_Coupon  
 */
if (!defined('ABSPATH')) {
    exit;
}
class Wt_Smart_Coupon_Auto_Coupon_Public
{
    public $module_base='auto_coupon';
    public $module_id='';
    public static $module_id_static='';
    private static $instance = null;

    protected $overwrite_coupon_message=array();
    protected $_user_emails=array();
    protected $_session_data=array();
    private $coupon_is_processing = false;
    private $auto_coupon_session_hash = '';
    protected $autocoupons = null;

    public static $coupon_added_msg = array();

    private $available_auto_coupons = false;

    public function __construct()
    {
        $this->module_id=Wt_Smart_Coupon::get_module_id($this->module_base);
        self::$module_id_static=$this->module_id;
        
        add_action('woocommerce_checkout_update_order_review', array($this, 'store_billing_email_into_session'), 10); 
        add_action('woocommerce_after_checkout_validation', array($this, 'store_billing_email_into_session'), 10); 
        add_action('woocommerce_check_cart_items', array($this, 'woocommerce_check_cart_items'), 0, 0);
       
        add_filter('woocommerce_cart_totals_coupon_html', array($this, 'coupon_html'), 10, 2);
       
        add_action('woocommerce_checkout_update_order_review', array( $this, 'reset_auto_coupon_hash'));
        add_action('woocommerce_cart_loaded_from_session', array( $this, 'action_woocommerce_cart_loaded_from_session' ) );
        
        add_action('wp_loaded', array( $this, 'auto_apply_coupons' ));
        add_action('woocommerce_after_calculate_totals', array($this, 'maybe_apply_auto_coupons'), 1000);
    }

    /**
     * Get Instance
     */
    public static function get_instance()
    {
        if(self::$instance==null)
        {
            self::$instance=new Wt_Smart_Coupon_Auto_Coupon_Public();
        }
        return self::$instance;
    }

    /**
     * Function to check specified coupon is autocoupon
     * @since 1.4.1
     */
    public function is_auto_coupon($coupon)
    {
        if(is_object($coupon))
        {
            $coupon = $coupon->get_id();
        }

        $is_auto_coupon=get_post_meta($coupon, '_wt_make_auto_coupon', true);
        return ('yes'==$is_auto_coupon || 1==$is_auto_coupon);
    }

    /**
     * Get all available auto coupons.
     * @since 1.4.1
     * @since 1.4.2 Saving the already fetched coupons list to avoid multiple DB call 
     * @since 1.4.3 Recursive functionality added to reach the limit of allowed coupons
     * @since 1.4.4 Saving the already fetched coupons list to avoid multiple DB call 
     *              Recursive functionality added to reach the limit of allowed coupons
     *              Added new filter to alter the coupon list: (wt_sc_auto_coupons_list)
     */
    public function get_available_auto_coupons($return = "OBJECT", $offset=0, $limit=5)
    {       
        global $wpdb;
        $user = wp_get_current_user();
        
        if($user)
        {
            $user_id = $user->ID; //id will be zero for guest users 
            $email = $user->user_email;
        }else
        {
            return array();
        }
        
        $cart = WC()->cart;
        
        if(is_null($cart))
        {
            return array();
        }

        /**
         *  Alter the auto coupon list. This will be helpfull to avoid caching in some areas
         *  @since 1.4.4
         *  @param bool|array  available coupons 
         */
        $this->available_auto_coupons = apply_filters('wt_sc_auto_coupons_list', $this->available_auto_coupons);

        if(false !== $this->available_auto_coupons) /* List was already fetched so return the existing list */
        {          
            return ("OBJECT" === $return ? $this->available_auto_coupons : array_keys($this->available_auto_coupons));
        }

        $limit = apply_filters('wt_smartcoupon_max_auto_coupons_limit', $limit);
        $this->available_auto_coupons = $this->prepare_auto_coupons_list($cart, $user, $offset, $limit);
        
        return ("OBJECT" === $return ? $this->available_auto_coupons : array_keys($this->available_auto_coupons));
    }

    /**
     *  Recursively fetch the auto coupons
     *  @since 1.4.3
     */
    private function prepare_auto_coupons_list($cart, $user, $offset, $limit, $out = array())
    {
        $post_ids = Wt_Smart_Coupon_Public::get_user_coupons($user, $offset, $limit, array('type'=>'auto_coupons'));

        if(!empty($post_ids))
        {
            $discounts = new WC_Discounts($cart); 

            foreach($post_ids as $post_id)
            {
                $post=get_post($post_id);
                $coupon_obj = new WC_Coupon($post->ID);

                if(is_wp_error($discounts->is_coupon_valid($coupon_obj)))
                {
                    continue;
                }

                if(!apply_filters('wt_is_valid_coupon', true, $coupon_obj))
                {
                    continue;
                }

                $out[wc_sanitize_coupon_code($coupon_obj->get_code())] = $coupon_obj;

                if(count($out) === $limit) //limit reached
                {
                    break;
                }
            }
        }else
        {
            //no data found     
            return $out;
        }

        if(count($out) === $limit) //limit reached so return
        {
            return $out;
        }else 
        {
            //recursively take the data
            return $this->prepare_auto_coupons_list($cart, $user, ($offset+$limit), $limit, $out);
        }
    }

    /**
     * Store the userdata into session
     * @since 1.4.1
     */
    public function store_billing_email_into_session($post_data)
    {
        if (!is_array($post_data)) {
            parse_str($post_data, $posted);
        } else {
            $posted = $post_data;
        }

        if (isset($posted['billing_email'])) {
            $this->set_session('billing_email', $posted['billing_email']);
        }
    }

    /**
     * Set smartcoupon session.
     * @since 1.4.1
     */
    public function set_session($key, $value)
    {
        if (!isset($this->_session_data)) {
            if (!isset(WC()->session)) {
                return null;
            }
            $this->_session_data = WC()->session->get('_wt_smart_coupon_session_data', array());
        }
        if (is_null($value)) {
            unset($this->_session_data[$key]);
        } else {
            $this->_session_data[$key] = $value;
        }

        WC()->session->set('_wt_smart_coupon_session_data', $this->_session_data);
    }

    /**
     * Cache the session data into private variable
     * @since 1.1.0
     */
    public function get_session($key = null, $default = false)
    {
        if (!isset($this->_session_data)) {
            if (!isset(WC()->session)) {
                return null;
            }
            $this->_session_data = WC()->session->get('_wt_smart_coupon_session_data', array());
        }

        if (!isset($key)) {
            return $this->_session_data;
        }
        if (!isset($this->_session_data[$key])) {
            return $default;
        }
        return $this->_session_data[$key];
    }

    /** 
     * Removed unmatched cart item ( Before removing woocommmerce )
     * @since 1.1.0
     */
    public function woocommerce_check_cart_items()
    {
        $this->remove_unmatched_autocoupons();
    }

    /**
     * Remove unmatched coupons silentley
     *  @since 1.1.0
     *  @since 1.4.1  [Bug fix] Removes valid autocoupons 
     *                Added functionality to accept coupon objects
     */
    private function remove_unmatched_autocoupons($valid_coupon_codes = null)
    {       
        $cart = WC()->cart;

        if(is_null($cart))
        {
            return;
        }

        if(is_null($valid_coupon_codes))
        {
            $valid_coupon_codes = $this->get_available_auto_coupons("CODE");
        }

        //Remove invalids
        $calc_needed = false;
        $applied_coupons = $cart->get_applied_coupons();
        $applied_coupons = (isset($applied_coupons) && is_array($applied_coupons)) ? $applied_coupons : array(); 
        
        foreach($applied_coupons as $coupon_code)
        {
            if(in_array($coupon_code, $valid_coupon_codes)) //valid
            {
                continue;
            }

            $coupon = new WC_Coupon($coupon_code);
            $is_auto_coupon = $this->is_auto_coupon($coupon);

            if(!$is_auto_coupon) //not an auto coupon
            {
                continue;
            }

            if(!empty($coupon->get_email_restrictions()))
            {
                /**
                 *  Skip removal of auto coupons with email restriction, this coupon is added by the customer manually so WC will do the validation in the final checkout process
                 *  @since 1.4.2
                 */
                continue;
            }

            if(!apply_filters('wt_remove_invalid_coupon_automatically', true, $coupon)) //do not remove invalid auto coupon
            {
                continue;
            }

            $cart->remove_coupon($coupon_code);
            $calc_needed = true;
        }

        return $calc_needed;
    }

    /**
     * Check whether to apply auto coupons
     * @throws Exception Error message.
     */
    public function action_woocommerce_cart_loaded_from_session()
    {
        $this->auto_coupon_session_hash = $this->get_session('wt_smart_coupon_auto_coupon_hash', '');
    }

    public function maybe_apply_auto_coupons()
    {   
        if( $this->cart_contains_subscription() === true ) {
            return;
         }
        if ($this->coupon_is_processing) {
            return;
        }
        $current_hash = $this->get_current_hash_values();
        if ($current_hash === $this->auto_coupon_session_hash) {
            return;
        }
        $this->coupon_is_processing = true;
        $this->auto_apply_coupons();
        $this->coupon_is_processing = false;
        $this->auto_coupon_session_hash = $current_hash;
        $this->set_session('wt_smart_coupon_auto_coupon_hash', $current_hash);
    }

    public function reset_auto_coupon_hash()
    {
        $this->auto_coupon_session_hash = '';
    }

    public function get_current_hash_values()
    {

        $combined_hash = array(
            'cart' => WC()->cart->get_cart_for_session(),
            'current_coupons' => WC()->cart->get_applied_coupons(),
            'current_payment_method' => isset(WC()->session->chosen_payment_method) ? WC()->session->chosen_payment_method : array(),
            'current_shipping_method' => isset(WC()->session->chosen_shipping_methods) ? WC()->session->chosen_shipping_methods : array(),
            'current_date' => current_time('Y-m-d'),
        );
        $combined_hash = apply_filters('wt_smart_coupon_auto_coupon_triggers', $combined_hash);
        return md5(wp_json_encode($combined_hash));
    }

    /**
     *  Auto apply coupons
     *  @since 1.1.0 
     *  @since 1.4.1    [Bug fix] Conflict with `auto apply` and `coupon individual use`. 
     *                  [Bug fix] Not automatically applying coupons with wild card(*) email restriction. 
     */
    public function auto_apply_coupons()
    {    
        if(is_admin())
        {
            return;   
        }

        global $wt_sc_just_added_coupons; /* this using in giveaway module, If currently added product and giveaway product are same for the newly added coupon. This is currently applicable for `Specific category`, `Any product from store` options */
        
        $cart = (is_object(WC()) && isset(WC()->cart)) ? WC()->cart : null;
        
        if(is_object($cart) && is_callable(array($cart, 'is_empty')) && !$cart->is_empty())
        {
            $available_coupons = $this->get_available_auto_coupons();
                    
            if($this->remove_unmatched_autocoupons(array_keys($available_coupons)))
            {
                $cart->calculate_totals();
            }

            /* Check there is any individual coupon already applied */
            $individual_coupon_applied = $this->wt_sc_check_individual_coupon_applied($cart);
            if($individual_coupon_applied)
            {
                return;  
            }

            $auto_coupons = array();
            foreach($available_coupons as $coupon)
            {
                $coupon_code = wc_sanitize_coupon_code($coupon->get_code());

                $auto_coupons[$coupon_code] = $coupon;

                if($coupon->get_individual_use()) /* Individual use then clear the list and keep only the current one */
                {
                    $auto_coupons = array();
                    $auto_coupons[$coupon_code] = $coupon;
                    break; /* break the loop. No need to check again */
                }
            }

            $smart_coupon_obj=Wt_Smart_Coupon::get_instance();
            $user= wp_get_current_user(); 
            
            foreach($auto_coupons as $coupon_code => $coupon)
            {
                $cart_total = $cart->get_cart_contents_total();

                // Check if cart still requires a coupon discount and does not have coupon already applied.
                if($cart_total > 0 && !WC()->cart->has_discount($coupon_code))
                {                                         
                    /**
                    *   Limit to defined email addresses. If logged in
                    */
                    $restrictions = $coupon->get_email_restrictions(); 
                    
                    if(!empty($restrictions) && !$user) /* auto apply functionality will not work when email restriction exists and user is not logged in */
                    {
                        continue;
                    }

                    if($user && !Wt_Smart_Coupon_Public::is_coupon_emails_allowed(array($user->user_email), $coupon))
                    {
                        continue;
                    }

                    $coupon_desc = $coupon->get_description();
                    
                    if($coupon_desc)
                    {
                        $coupon_desc = ': ' . $coupon_desc;
                    }
                    
                    $new_message = apply_filters('wt_smart_coupon_auto_coupon_message', __('Coupon code applied successfully', 'wt-smart-coupons-for-woocommerce') . ' ' . $coupon_desc, $coupon);               
                    $new_message = (in_array($coupon_code, self::$coupon_added_msg) ? '' : $new_message); //this is to prevent multiple messages

                    $smart_coupon_obj->plugin_public->start_overwrite_coupon_success_message($coupon_code, $new_message);

                    WC()->cart->add_discount($coupon_code);

                    $smart_coupon_obj->plugin_public->stop_overwrite_coupon_success_message();

                    $wt_sc_just_added_coupons[] = $coupon_code;
                    self::$coupon_added_msg[] = $coupon_code;
                }
            }
        }
    }

    public function wt_sc_check_individual_coupon_applied($cart)
    {
        $applied = false;
        $coupons = $cart->get_applied_coupons();

        $coupons = (isset($coupons) && is_array($coupons)) ? $coupons : array();
        foreach ($coupons as $code) { 
            $coupon = new WC_Coupon($code);
            if ($coupon->get_individual_use()) {
                $applied = true;
                break;
            }
        }
        return $applied;
    }

    /**
     * Update coupon HTML on cart total
     * @since 1.1.0
     */
    public function coupon_html($originaltext, $coupon)
    {
        if ($this->is_auto_coupon($coupon)) {
            
            if(!in_array($coupon->get_code(), $this->get_available_auto_coupons("CODE")) && !empty($coupon->get_email_restrictions()))
            {
                /**
                 * An auto coupon that is not in the valid auto coupons list and also have email restrictions, so it must be a manually applied auto coupon. 
                 * Here we are returning the original text to keep the `coupon remove` link.
                 * @since 1.4.2
                 */
                return $originaltext;
            }

            $value = array();

            $amount = (float) WC()->cart->get_coupon_discount_amount($coupon->get_code(), WC()->cart->display_cart_ex_tax);
            if($amount)
            {
                $discount_html = '-' . wc_price($amount);
            } else {
                $discount_html = wc_price(0);
            }

            $value[] = apply_filters('woocommerce_coupon_discount_amount_html', $discount_html, $coupon);

            if ($coupon->get_free_shipping()) {
                $value[] = __('Free shipping coupon', 'wt-smart-coupons-for-woocommerce');
            }

            return implode(', ', array_filter($value));
        } else {
            return $originaltext;
        }
    }
    /**
    * Check if cart contains subscription and switch autocoupon apply method to call directly before calculate_totals hook.
    *
    * @since  1.3.2
    * @access public
    * @throws Exception Error message.
    */
    public function cart_contains_subscription() {
        $has_subscription = false;
        $cart = ( is_object( WC() ) && isset( WC()->cart ) ) ? WC()->cart : null;
        if ( is_object( $cart ) && is_callable( array( $cart, 'is_empty' ) ) && ! $cart->is_empty() ) {
            foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
                $product = $cart_item['data'];
                if ( is_a( $product, 'WC_Product_Subscription' ) || is_a( $product, 'WC_Product_Subscription_Variation' ) ) {
                    $has_subscription = true;
                }
            }
        }
        return $has_subscription;
        
    }
}
Wt_Smart_Coupon_Auto_Coupon_Public::get_instance();