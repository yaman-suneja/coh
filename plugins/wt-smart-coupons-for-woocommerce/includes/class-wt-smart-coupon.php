<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://www.webtoffee.com
 * @since      1.0.0
 *
 * @package    Wt_Smart_Coupon
 * @subpackage Wt_Smart_Coupon/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Wt_Smart_Coupon
 * @subpackage Wt_Smart_Coupon/includes
 * @author     WebToffee <info@webtoffee.com>
 */
if( ! class_exists('Wt_Smart_Coupon') ) {
	class Wt_Smart_Coupon {

		/**
		 * The loader that's responsible for maintaining and registering all hooks that power
		 * the plugin.
		 *
		 * @since    1.0.0
		 * @access   protected
		 * @var      Wt_Smart_Coupon_Loader    $loader    Maintains and registers all hooks for the plugin.
		 */
		protected $loader;
	
		/**
		 * The unique identifier of this plugin.
		 *
		 * @since    1.0.0
		 * @access   protected
		 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
		 */
		protected $plugin_name;
	
		/**
		 * The current version of the plugin.
		 *
		 * @since    1.0.0
		 * @access   protected
		 * @var      string    $version    The current version of the plugin.
		 */
		protected $version;
	
		protected $plugin_base_name = WT_SMARTCOUPON_BASE_NAME;

		public $plugin_admin=null;
		public $plugin_common=null;
		public $plugin_public=null;

		private static $instance = null;
				
		/**
		 * Define the core functionality of the plugin.
		 *
		 * Set the plugin name and the plugin version that can be used throughout the plugin.
		 * Load the dependencies, define the locale, and set the hooks for the admin area and
		 * the public-facing side of the site.
		 *
		 * @since    1.0.0
		 */
		public function __construct() {
				
			if ( defined( 'WEBTOFFEE_SMARTCOUPON_VERSION' ) ) {
				$this->version = WEBTOFFEE_SMARTCOUPON_VERSION;
			} else {
				$this->version = '1.4.5';
			}
			$this->plugin_name = WT_SC_PLUGIN_NAME;
	
			$this->load_dependencies();
			$this->set_locale();
			$this->define_common_hooks();
			$this->define_admin_hooks();
			$this->define_public_hooks();
	
		}
	

		/**
         * Get Instance
         * @since 1.4.1
         */
        public static function get_instance()
        {
            if(self::$instance==null)
            {
                self::$instance=new Wt_Smart_Coupon();
            }

            return self::$instance;
        }



		/**
		 * Load the required dependencies for this plugin.
		 *
		 * Include the following files that make up the plugin:
		 *
		 * - Wt_Smart_Coupon_Loader. Orchestrates the hooks of the plugin.
		 * - Wt_Smart_Coupon_i18n. Defines internationalization functionality.
		 * - Wt_Smart_Coupon_Admin. Defines all hooks for the admin area.
		 * - Wt_Smart_Coupon_Public. Defines all hooks for the public side of the site.
		 *
		 * Create an instance of the loader which will be used to register the hooks
		 * with WordPress.
		 *
		 * @since    1.0.0
		 * @access   private
		 */
		private function load_dependencies() {
	
			/**
			 * The class responsible for orchestrating the actions and filters of the
			 * core plugin.
			 */
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wt-smart-coupon-loader.php';
			
			/**
			 * Webtoffee Security Library
			 * Includes Data sanitization, Access checking
			 */
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wt-security-helper.php';

			/**
			 *  @since 1.4.5
			 *  Compatability to WPML
			 * 
			 * Webtoffee Language Functions
			 * 
			 * Includes functions to manage translations
			 */

			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wt-multi-languages.php';


			/**
			 * The class responsible for defining internationalization functionality
			 * of the plugin.
			 */
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wt-smart-coupon-i18n.php';
	
			  
			/**
			 * @since 1.3.5
			 * The class responsible for defining all actions common to admin/public.
			 */
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'common/class-wt-smart-coupon-common.php';


			/**
			 * The class responsible for defining all actions that occur in the admin area.
			 */
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wt-smart-coupon-admin.php';
	
	
			/**
			 * The class responsible for defining all actions that occur in the public-facing
			 * side of the site.
			 */
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wt-smart-coupon-public.php';
			
			/**
			 * The class responsible for handling review seeking banner
			 * side of the site.
			 */
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wt-smart-coupon-review_request.php';
			$this->loader = new Wt_Smart_Coupon_Loader();
	
		}
	
		/**
		 * Define the locale for this plugin for internationalization.
		 *
		 * Uses the Wt_Smart_Coupon_i18n class in order to set the domain and to register the hook
		 * with WordPress.
		 *
		 * @since    1.0.0
		 * @access   private
		 */
		private function set_locale() {
	
			$plugin_i18n = new Wt_Smart_Coupon_i18n();
	
			$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	
		}

		/**
		 * Register all of the hooks related to the admin/public area functionality
		 * of the plugin.
		 *
		 * @since    1.3.5
		 * @access   private
		 */
		private function define_common_hooks() {

			$this->plugin_common= Wt_Smart_Coupon_Common::get_instance( $this->get_plugin_name(), $this->get_version() );
			$this->plugin_common->register_modules();

			/**
			 * Coupon lookup table
			 * @since 1.4.3
			 */		
			//Insert existing coupon data to lookup table
			$this->loader->add_action('init', $this->plugin_common, 'update_existing_coupon_data_to_lookup_table', 10);

			//Update lookup table on coupon object save
			$this->loader->add_action('woocommerce_after_data_object_save', $this->plugin_common, 'update_coupon_lookup_on_object_save', 1000, 2);

			//Update lookup table on coupon meta data save
			$this->loader->add_action('woocommerce_process_shop_coupon_meta', $this->plugin_common, 'update_coupon_lookup_on_meta_save', 1000, 2);

			//Update lookup table on coupon usage count change
			$this->loader->add_action('woocommerce_increase_coupon_usage_count', $this->plugin_common, 'update_coupon_lookup_on_usage_count_change', 1000, 3);
			$this->loader->add_action('woocommerce_decrease_coupon_usage_count', $this->plugin_common, 'update_coupon_lookup_on_usage_count_change', 1000, 3);

			//Update lookup table on post meta update
			$this->loader->add_action('updated_post_meta', $this->plugin_common, 'update_coupon_lookup_on_postmeta_change', 1000, 4);
			$this->loader->add_action('added_post_meta', $this->plugin_common, 'update_coupon_lookup_on_postmeta_change', 1000, 4);
			$this->loader->add_action('deleted_post_meta', $this->plugin_common, 'update_coupon_lookup_on_postmeta_change', 1000, 4);

			//Update lookup table on post status update
			$this->loader->add_action('transition_post_status', $this->plugin_common, 'update_coupon_lookup_on_post_status_change', 1000, 3);


			/**
			 * 	Check and update lookup table. Priority number must be lower, because data updation hook must fire after this one
			 * 	@since 1.4.4
			 */
			$this->loader->add_action('init', $this->plugin_common, 'check_and_update_lookup_table', 1);
		}
	
		/**
		 * Register all of the hooks related to the admin area functionality
		 * of the plugin.
		 *
		 * @since    1.0.0
		 * @access   private
		 */
		private function define_admin_hooks() {
	
			$this->plugin_admin = Wt_Smart_Coupon_Admin::get_instance( $this->get_plugin_name(), $this->get_version() );
	
			$this->loader->add_action('admin_enqueue_scripts', $this->plugin_admin, 'enqueue_styles' );
			$this->loader->add_action('admin_enqueue_scripts', $this->plugin_admin, 'enqueue_scripts' );
			$this->loader->add_filter('plugin_action_links_' . $this->get_plugin_base_name(), $this->plugin_admin, 'add_plugin_links_wt_smartcoupon');
			$this->loader->add_filter('woocommerce_coupon_data_tabs', $this->plugin_admin, 'admin_coupon_options_tabs', 20, 1);
			$this->loader->add_action('woocommerce_coupon_data_panels', $this->plugin_admin, 'admin_coupon_options_panels', 10, 0);

			$this->loader->add_action('webtoffee_coupon_metabox_checkout',$this->plugin_admin, 'admin_coupon_metabox_checkout2', 10, 2);
			$this->loader->add_action('woocommerce_process_shop_coupon_meta', $this->plugin_admin, 'process_shop_coupon_meta', 10, 2);
	
	
			$this->loader->add_action('woocommerce_coupon_options', $this->plugin_admin,'add_new_coupon_options',10,2);
	
			$this->loader->add_action('wp_ajax_wt_check_product_type',$this->plugin_admin,'check_product_type');
			
			/**
			 * 	@since 1.3.3
			 */
			$this->loader->add_action("add_meta_boxes", $this->plugin_admin, "upgrade_to_pro_meta_box");

			/** 
			*	Initiate admin modules 
			* 	@since 1.3.5
			*/
			$this->plugin_admin->register_modules();


			/**
			 *  Help links meta box
			 * 	@since 1.3.5
			 */
			$this->loader->add_action("add_meta_boxes", $this->plugin_admin, "help_links_meta_box", 8);

			
			/**
			 *  Links under plugin description section of plugins page
			 * 	@since 1.3.9
			 */
			$this->loader->add_filter("plugin_row_meta", $this->plugin_admin, 'plugin_row_meta', 10, 2);

			/**
			 *  Setup video sidebar
			 * 	@since 1.4.0
			 */
			$this->loader->add_action("wt_smart_coupon_admin_form_right_box", $this->plugin_admin, 'setup_video_sidebar', 10);

			/**
			 *  Premium features sidebar
			 * 	@since 1.4.0
			 */
			$this->loader->add_action("wt_smart_coupon_admin_form_right_box", $this->plugin_admin, 'premium_features_sidebar', 11);

			/**
			 * 	Saving new coupon count
	         *  @since 1.4.1
	         */
			$this->loader->add_action('wp_insert_post', $this->plugin_admin, 'save_created_coupon_count', 10, 3);
		
			/**
			 *  Search coupons using email
			 * 	
			 *	@since 1.4.4
			 */
			$this->loader->add_action('parse_request', $this->plugin_admin, 'search_coupon_using_email');


			/**
			* 	Ajax hook for saving settings, Includes plugin main settings and settings from module
			* 	
			* 	@since 1.4.4
			*/
			$this->loader->add_action('wp_ajax_wt_sc_save_settings', $this->plugin_admin, 'save_settings');

			/** 
			*	Admin menu 
			* 	
			* 	@since 1.4.4
			*/
			$this->loader->add_action('admin_menu', $this->plugin_admin, 'admin_menu',11);

			
			/**
			* Smart coupon settings button on coupons page
			* 	
			* @since 1.4.4
			*/
			$this->loader->add_action('admin_head-edit.php', $this->plugin_admin, 'coupon_page_settings_button');


			/**
			* Saving hook for debug tab
			* 	
			* @since 1.4.5
			*/
			$this->loader->add_action('admin_init', $this->plugin_admin, 'debug_save');

			/**
			 *  Lookup table migration in progress message
			 * 	
			 * 	@since 1.4.5
			 */
			$this->loader->add_action('admin_notices', $this->plugin_admin, 'lookup_table_migration_message');
		}
	
		/**
		 * Register all of the hooks related to the public-facing functionality
		 * of the plugin.
		 *
		 * @since    1.0.0
		 * @access   private
		 */
		private function define_public_hooks() {
	
			$this->plugin_public = Wt_Smart_Coupon_Public::get_instance($this->get_plugin_name(), $this->get_version());
	
			$this->loader->add_action('wp_enqueue_scripts', $this->plugin_public, 'enqueue_styles' );
			$this->loader->add_action('wp_enqueue_scripts', $this->plugin_public, 'enqueue_scripts' );
			$this->loader->add_filter('woocommerce_coupon_is_valid', $this->plugin_public,  'wt_woocommerce_coupon_is_valid', 10, 2 );

			/**
			 * 	@since 1.3.7
			 */
			$this->loader->add_action('woocommerce_before_checkout_form', $this->plugin_public, 'display_available_coupon_in_checkout');

			/** 
			*	Display coupons in cart page 
			* 	@since 1.4.3
			*/
			$this->loader->add_action('woocommerce_after_cart_table', $this->plugin_public, 'display_available_coupon_in_cart');

			/** 
			*	Initiate public modules 
			* 	@since 1.4.0
			*/
			$this->plugin_public->register_modules();

		}

		/**
         *  Registers modules    
         *  @since 1.3.5     
         */
		public static function register_modules($modules, $module_option_name, $module_path, &$existing_modules)
		{
			$wt_sc_modules=get_option($module_option_name);
            if($wt_sc_modules===false)
            {
                $wt_sc_modules=array();
            }
            foreach ($modules as $module) //loop through module list and include its file
            {
                $is_active=1;
                if(isset($wt_sc_modules[$module]))
                {
                    $is_active=$wt_sc_modules[$module]; //checking module status
                }else
                {
                    $wt_sc_modules[$module]=1; //default status is active
                }
                $module_file=$module_path."modules/$module/$module.php";
                if(file_exists($module_file) && $is_active==1)
                {
                    $existing_modules[]=$module; //this is for module_exits checking
                    require_once $module_file;
                }else
                {
                    $wt_sc_modules[$module]=0;    
                }
            }
            $out=array();
            foreach($wt_sc_modules as $k=>$m)
            {
                if(in_array($k, $modules))
                {
                    $out[$k]=$m;
                }
            }
            update_option($module_option_name, $out);
		}

		public static function get_module_id($module_base)
		{
			return WT_SC_PLUGIN_NAME.'_'.$module_base;
		}
		
		/**
		*	@since 1.3.5
		*	Get module base from module id
		*/
		public static function get_module_base($module_id)
		{
			if(strpos($module_id, WT_SC_PLUGIN_NAME.'_')!==false) //valid module ID
			{
				return str_replace(WT_SC_PLUGIN_NAME.'_', '', $module_id);
			}
			return false;
		}
	
		/**
		 * Run the loader to execute all of the hooks with WordPress.
		 *
		 * @since    1.0.0
		 */
		public function run() {
			$this->loader->run();
		}
	
		public function get_plugin_name() {
			return $this->plugin_name;
		}
	
		/**
		 * The reference to the class that orchestrates the hooks with the plugin.
		 *
		 * @since     1.0.0
		 * @return    Wt_Smart_Coupon_Loader    Orchestrates the hooks of the plugin.
		 */
		public function get_loader() {
			return $this->loader;
		}
	
	
		public function get_version() {
			return $this->version;
		}
			 
		public function get_plugin_base_name() {
			return $this->plugin_base_name;
		}
		public static function wt_cli_is_woocommerce_prior_to($version) {
			$woocommerce_is_pre_version = (!defined('WC_VERSION') || version_compare(WC_VERSION, $version, '<')) ? true : false;
			return $woocommerce_is_pre_version;
		}
	

		/**
	     *	Install necessary tables
	     *	@since 1.4.3
	     */
	    public static function install_tables()
	    {
	        global $wpdb;   
	        require_once ABSPATH.'wp-admin/includes/upgrade.php';       
	        
	        if(is_multisite()) 
	        {
	            // Get all blogs in the network and activate plugin on each one
	            $blog_ids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
	            
	            foreach($blog_ids as $blog_id) 
	            {
	                switch_to_blog($blog_id);
	                self::install_lookup_table();
	                restore_current_blog();
	            }

	        }else 
	        {
	            self::install_lookup_table();
	        }   
	    }

	    /**
	     * 	Get lookup table name
	     * 	@since 1.4.3
	     */
	    public static function get_lookup_table_name()
	    {
	    	global $wpdb;
	    	return $wpdb->prefix.'wt_sc_coupon_lookup';
	    }

	    public static function is_table_exists($table_name)
	    {
	    	global $wpdb;
	    	return $wpdb->get_results($wpdb->prepare("SHOW TABLES LIKE %s", '%'.$table_name.'%'), ARRAY_N); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
	    }

	    /**
	     * 	Create lookup table for saving coupon data
	     * 	@since 1.4.3
	     */
	    public static function install_lookup_table()
	    {
	    	global $wpdb;
	        $table_name = self::get_lookup_table_name();
	        
	        if(!self::is_table_exists($table_name))  
	        {
	        	require_once ABSPATH.'wp-admin/includes/upgrade.php';

	            $sql_qry = "CREATE TABLE IF NOT EXISTS {$table_name} (
	              `id` bigint(20) NOT NULL AUTO_INCREMENT,
	              `coupon_id` bigint(20) NOT NULL DEFAULT '0',
	              `is_auto_coupon` int(11) NOT NULL DEFAULT '0',
	              `my_account_display` int(11) NOT NULL DEFAULT '0',
	              `cart_display` int(11) NOT NULL DEFAULT '0',
	              `checkout_display` int(11) NOT NULL DEFAULT '0',
				  `post_status` varchar(100) NOT NULL,
				  `email_restriction` text NOT NULL,
				  `user_roles` text NOT NULL,
				  `exclude_user_roles` text NOT NULL,
				  `expiry` varchar(100) NOT NULL,
				  `discount_type` varchar(100) NOT NULL,
				  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
				  `usage_limit` bigint(20) NOT NULL DEFAULT '0',
				  `usage_count` bigint(20) NOT NULL DEFAULT '0',
				  `usage_limit_per_user` int(11) NOT NULL DEFAULT '0',
				  `is_wt_gc_wallet_coupon` int(11) NOT NULL DEFAULT '0',
	              PRIMARY KEY(`id`),
	              INDEX `COUPON_ID`(`coupon_id`)
	            ) DEFAULT CHARSET=utf8;";

	            dbDelta($sql_qry);

	        }else
	        {
	        	if(self::get_lookup_table_version() > self::get_installed_lookup_table_version()) //new version available
            	{

		        	if(2 > self::get_installed_lookup_table_version()) //lesser than 2 so, add update added in version 2
		        	{
			        	/* Update columns to BIGINT */
			        	$wpdb->query("ALTER TABLE {$table_name} CHANGE `id` `id` BIGINT(20) NOT NULL AUTO_INCREMENT, CHANGE `coupon_id` `coupon_id` BIGINT(20) NOT NULL DEFAULT '0', CHANGE `usage_limit` `usage_limit` BIGINT(20) NOT NULL DEFAULT '0', CHANGE `usage_count` `usage_count` BIGINT(20) NOT NULL DEFAULT '0'");

			        	/* Add index to coupon_id column */
			        	$wpdb->query("ALTER TABLE {$table_name} ADD INDEX(`coupon_id`)");

			        	/* add new column for exclude user roles */
			        	$search_query = "SHOW COLUMNS FROM `$table_name` LIKE 'exclude_user_roles'";
				        
				        if(!$wpdb->get_results($search_query, ARRAY_N)) 
				        {
				        	$wpdb->query("ALTER TABLE {$table_name} ADD `exclude_user_roles` TEXT NOT NULL AFTER `user_roles`");
				        }
				    }

				    //future version updates will come here.....

				    //finally update the version number to latest
			    	self::update_lookup_table_version();
			    }
	        }

	        if(!self::is_table_exists($table_name))
	        {
	        	deactivate_plugins(WT_SMARTCOUPON_BASE_NAME);
	        	wp_die(sprintf(__("An error occurred while activating %sSmart Coupons For WooCommerce Coupons%s: Unable to create database table. %s", "wt-smart-coupons-for-woocommerce"), '<b>', '</b>', $table_name), "", array('link_url' => admin_url('plugins.php'), 'link_text' => __('Go to plugins page', 'wt-smart-coupons-for-woocommerce') ));
	        }
	    }

	    /**
	     * 	Installed version of lookup table
	     * 
	     * 	@since 1.4.4
	     * 	@return int 	installed lookup table version
	     */
	    public static function get_installed_lookup_table_version()
	    {
	    	return absint(get_option('wt_sc_coupon_lookup_version', 1));
	    }

	    /**
	     * 	Update lookup table version to latest
	     * 
	     * 	@since 1.4.4
	     */
	    public static function update_lookup_table_version()
	    {
	    	update_option('wt_sc_coupon_lookup_version', self::get_lookup_table_version());
	    }

	    /**
	     * 	New lookup table version for the plugin
	     * 
	     * 	@since 1.4.4
	     * 	@return int 	new lookup table version
	     */
	    public static function get_lookup_table_version()
	    {
	    	return 2;
	    }

	}
}
