<?php
/**
 * File class-cwccv-catalog-visibility.php
 *
 * @package catalog-visibility-for-woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/**
 * Class CWCCV_Catalog_Visibility.
 * All the implementions of catalog visibility for frontend are defined here.
 *
 * @since 1.1.1.0
 */
if ( ! class_exists( 'CWCCV_Catalog_Visibility' ) ) {
	/**
	 * Class CWCCV_Catalog_Visibility.
	 */
	class CWCCV_Catalog_Visibility {

		 public $hide_products = array();

		 public $hide_categories = array();

		/**
		 * Constructor.
		 */
		public function __construct() {

			// Hide catalog price filter.
			add_action( 'woocommerce_after_shop_loop_item', array( $this, 'hide_product_price' ), 30, 2 );
			add_action( 'woocommerce_after_add_to_cart_quantity', array( $this, 'hide_add_to_card_button' ) );
			add_filter( 'woocommerce_get_price_html', array( $this, 'hide_price_for_non_login' ), 100, 2 );

			// Hide catalog action.
			add_action( 'woocommerce_product_query', array( $this, 'hide_catalog_for_rules' ), 20, 2 );
			add_action( 'template_redirect', array( $this, 'disable_access_of_hidden_products' ), 10 );
			add_action( 'wp_login', array( $this, 'return_back_to_page' ), 10, 2 );

			// new woocommerce shop page feature compatibility.
			add_action( 'init', array( $this, 'init' ) );
			add_action( 'pre_get_posts', array( $this, 'remove_hook_for_lazy_load' ), 10 );

			add_action( 'cwcv_set_hidden_categories', array( $this, 'set_hidden_categories' ), 10, 2 );

		}


		public function set_hidden_categories( $categories, $rule ) {

			$all_categories = get_terms(
				array(
					'taxonomy'   => 'product_cat',
					'hide_empty' => false,
					'fields'     => 'ids',
				)
			);
			if ( $categories ) {
				if ( isset( $rule['is_shown'] ) && 'yes' == $rule['is_shown'] ) {
					$this->hide_categories = array_diff( $all_categories, $categories );
				} else {
					$this->hide_categories = array_intersect( $all_categories, $categories );
				}
			}
			add_filter( 'woocommerce_product_categories_widget_dropdown_args', array( $this, 'widget_arguments' ), 10, 1 );
			add_filter( 'woocommerce_product_categories_widget_args', array( $this, 'widget_arguments' ), 10, 1 );

		}

		public function remove_products_from_shortcode( $query_args, $atts, $loop_name ) {
			$query_args['exclude'] = $this->get_list_of_products_to_be_hidden();
			return $query_args;
		}

		public function init() {

			add_filter( 'woocommerce_shortcode_products_query', array( $this, 'remove_products_from_shortcode' ), 10, 3 );

			$this->hide_products = $this->get_list_of_products_to_be_hidden();

			add_action( 'pre_get_posts', array( $this, 'modify_shortcode_query' ), 10 );

			if ( ! empty( $this->hide_products ) ) {
				if ( is_user_logged_in() ) {
					update_user_meta( get_current_user_id(), 'cvf_hidden_products', $this->hide_products );
				}
				update_option( 'cvf_hidden_products_non_logged_in', $this->hide_products );
			}

		}

		public function modify_shortcode_query( $query ) {
			if ( is_shop() ) {
				$query->set( 'post__not_in', $this->hide_products );
			}
		}

		public function remove_hook_for_lazy_load() {
			//if ( is_shop() ) {
				//remove_action( 'woocommerce_archive_description', 'woocommerce_product_archive_description' );
			//}
		}

		/**
		 * Redirect back to where the user clicked sign in button.
		 *
		 * @param array  $user_login User login details.
		 * @param object $user User object.
		 */
		public function return_back_to_page( $user_login, $user ) {

			if ( isset( $_GET['returnPage'] ) && ! empty( $_GET['returnPage'] ) ) {
				$returnpage = filter_input( INPUT_GET, 'returnPage', FILTER_DEFAULT, FILTER_SANITIZE_STRING );

				wp_redirect( base64_decode( $returnpage ) );
				exit;

			}

		}

		/**
		 * Callback function for the filter "woocommerce_get_price_html".
		 * Hides the catalog price a/c to hide whole catalog price and hide catalog for
		 * non logged in users option.
		 */
		public function hide_product_price() {

			global $product;

			// Getting option for hide catalog price.
			$hide_price_for_non_login_users_option = get_option( CWCCV_PLUGIN_PREFIX . '_hide_price_for_non_login' );
			$hide_whole_catalog_price_option       = get_option( CWCCV_PLUGIN_PREFIX . '_hide_whole_catalog_price' );
			$hide_catalog_price_for_categories     = get_option( CWCCV_PLUGIN_PREFIX . '_hide_catalog_for_categories' );
			$hide_catalog_price_for_products       = get_option( CWCCV_PLUGIN_PREFIX . '_hide_catalog_for_products' );
			$b2be_catalogue_enabled                = false;

			if ( ! is_user_logged_in() ) {

				// Applying rules if user is not logged in.
				if ( function_exists( 'is_required_login' ) ) {
					$b2be_catalogue_enabled = is_required_login( $product );
				}

				if ( 'yes' == $hide_price_for_non_login_users_option && ! $b2be_catalogue_enabled ) {
					if ( 'yes' == $hide_whole_catalog_price_option ) {
						echo ( '<style>.post-' . esc_attr( $product->get_id() ) . ' .button.add_to_cart_button{display: none !important ;}</style>' );
						echo ( '<style>.post-' . esc_attr( $product->get_id() ) . ' .button.product_type_grouped{display: none !important ;}</style>' );
						?>
						<a class="button" href="<?php echo wp_kses_post( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) ) . '?returnPage=' . wp_kses_post( base64_encode( get_the_permalink() ) ); ?>"><?php echo wp_kses_post( __( 'Sign In To View', 'codup-woocommerce-catalog-visibility' ) ); ?></a><br>
						<?php
					} elseif ( ( is_array( $hide_catalog_price_for_categories ) && has_term( $this->get_category_slug( $hide_catalog_price_for_categories ), 'product_cat', $product->get_id() ) ) || ( is_array( $hide_catalog_price_for_products ) && in_array( $product->get_id(), $hide_catalog_price_for_products ) ) ) {
						echo ( '<style>.post-' . esc_attr( $product->get_id() ) . ' .button.add_to_cart_button{display: none !important ;}</style>' );
						echo ( '<style>.post-' . esc_attr( $product->get_id() ) . ' .button.product_type_grouped{display: none !important ;}</style>' );
						?>
						<a class="button" href="<?php echo wp_kses_post( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) ) . '?returnPage=' . wp_kses_post( base64_encode( get_the_permalink() ) ); ?>"><?php echo wp_kses_post( __( 'Sign In To View', 'codup-woocommerce-catalog-visibility' ) ); ?></a><br>
						<?php
					}
				}
			}
		}

		/**
		 * Hides the add to cart button a/c to hide whole catalog price and hide catalog for
		 * non logged in users option.
		 */
		public function hide_add_to_card_button() {

			global $product;

			// Getting option for hide catalog price.
			$hide_price_for_non_login_users_option = get_option( CWCCV_PLUGIN_PREFIX . '_hide_price_for_non_login' );
			$hide_whole_catalog_price_option       = get_option( CWCCV_PLUGIN_PREFIX . '_hide_whole_catalog_price' );
			$hide_catalog_price_for_categories     = get_option( CWCCV_PLUGIN_PREFIX . '_hide_catalog_for_categories' );
			$hide_catalog_price_for_products       = get_option( CWCCV_PLUGIN_PREFIX . '_hide_catalog_for_products' );
			$b2be_catalogue_enabled                = false;

			if ( ! is_user_logged_in() ) {
				// Applying rules if user is not logged in.
				if ( function_exists( 'is_required_login' ) ) {
					$b2be_catalogue_enabled = is_required_login( $product );
				}

				if ( 'yes' == $hide_price_for_non_login_users_option && ! $b2be_catalogue_enabled ) {
					if ( 'yes' === $hide_whole_catalog_price_option ) {
						echo '<style>button.single_add_to_cart_button{ display: none !important;}</style>';
						echo '<style>.auto-add-sample{ display: none !important;}</style>';
						echo '<style>.cwppe-add-to-cart.btn.btn-info{ display: none !important;}</style>';
						?>
						<a class="button" href="<?php echo wp_kses_post( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) ) . '?returnPage=' . wp_kses_post( base64_encode( get_the_permalink() ) ); ?>"><?php echo wp_kses_post( __( 'Sign In To View', 'codup-woocommerce-catalog-visibility' ) ); ?></a>
						<?php
					} elseif ( ( is_array( $hide_catalog_price_for_categories ) && has_term( $this->get_category_slug( $hide_catalog_price_for_categories ), 'product_cat', $product->get_id() ) ) || ( is_array( $hide_catalog_price_for_products ) && in_array( $product->get_id(), $hide_catalog_price_for_products ) ) ) {
						echo '<style>button.single_add_to_cart_button{ display: none !important;}</style>';
						echo '<style>.auto-add-sample{ display: none !important;}</style>';
						echo '<style>.cwppe-add-to-cart.btn.btn-info{ display: none !important;}</style>';
						?>
						<a class="button" href="<?php echo wp_kses_post( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) ) . '?returnPage=' . wp_kses_post( base64_encode( get_the_permalink() ) ); ?>"><?php echo wp_kses_post( __( 'Sign In To View', 'codup-woocommerce-catalog-visibility' ) ); ?></a>
						<?php
					}
				}
			}
		}

		/**
		 * Hides the add to cart button a/c to hide whole catalog price and hide catalog for
		 * non logged in users option.
		 *
		 * @param string $price Price of current product.
		 * @param object $product object of current product.
		 */
		public function hide_price_for_non_login( $price, $product ) {

			// Getting option for hide catalog price.
			$hide_price_for_non_login_users_option = get_option( CWCCV_PLUGIN_PREFIX . '_hide_price_for_non_login' );
			$hide_whole_catalog_price_option       = get_option( CWCCV_PLUGIN_PREFIX . '_hide_whole_catalog_price' );
			$hide_catalog_price_for_categories     = get_option( CWCCV_PLUGIN_PREFIX . '_hide_catalog_for_categories' );
			$hide_catalog_price_for_products       = get_option( CWCCV_PLUGIN_PREFIX . '_hide_catalog_for_products' );
			$b2be_catalogue_enabled                = false;

			if ( ! is_user_logged_in() ) {
				// Applying rules if user is not logged in.
				if ( function_exists( 'is_required_login' ) ) {
					$b2be_catalogue_enabled = is_required_login( $product );
				}

				if ( 'yes' == $hide_price_for_non_login_users_option && ! $b2be_catalogue_enabled ) {
					if ( 'yes' === $hide_whole_catalog_price_option ) {
						return false;
					} elseif ( ( is_array( $hide_catalog_price_for_categories ) && has_term( $this->get_category_slug( $hide_catalog_price_for_categories ), 'product_cat', $product->get_id() ) ) || ( is_array( $hide_catalog_price_for_products ) && in_array( $product->get_id(), $hide_catalog_price_for_products ) ) ) {
						return false;
					}
				}
			}

			return $price;

		}

		/**
		 * Avoids the hidden products to be opened from link.
		 */
		public function disable_access_of_hidden_products() {

			global $post;

			if ( empty( $this->hide_products ) ) {
				return;
			}

			if ( in_array( $post->ID, $this->hide_products ) ) {
				$url = wc_get_page_permalink( 'shop' );
				wp_redirect( $url );
				exit;
			}
		}

		/**
		 * Function to hide categories from wordpress/woocommerce category widget.
		 *
		 * @param array $args Argument array to be passed in category dropdown.
		 */
		public function widget_arguments( $args ) {
			if ( $this->hide_categories ) {
				$args['exclude'] = $this->hide_categories;
			}
			return $args;
		}

		/**
		 * Hide catalog according to rules.
		 *
		 * @param woocommerce $product Woocommerce product.
		 * @param woocommerce $query Woocommerce query.
		 */
		public function hide_catalog_for_rules( $product, $query ) {

			$hide_products = $this->get_list_of_products_to_be_hidden();
			$product->set( 'post__not_in', $hide_products );
		}

		/**
		 * Hide catalog according to rules.
		 *
		 * @return list $hide_products
		 */
		public function get_list_of_products_to_be_hidden() {

			$rules                                 = array(
				get_option( CWCCV_PLUGIN_PREFIX . '_hide_for_individual_customer' ),
				get_option( CWCCV_PLUGIN_PREFIX . '_hide_for_user_roles' ),
				get_option( CWCCV_PLUGIN_PREFIX . '_hide_for_user_groups' ),
				get_option( CWCCV_PLUGIN_PREFIX . '_hide_for_price_tier' ),
				get_option( CWCCV_PLUGIN_PREFIX . '_hide_for_geo_location' ),
			);
			$hide_price_for_non_login_users_option = get_option( CWCCV_PLUGIN_PREFIX . '_hide_product_for_non_login' );
			$hide_whole_catalog_price_option       = get_option( CWCCV_PLUGIN_PREFIX . '_hide_whole_catalog_product' );
			$hide_catalog_price_for_categories     = get_option( CWCCV_PLUGIN_PREFIX . '_hide_catalog_for_categories_by_product' );
			$hide_catalog_price_for_products       = get_option( CWCCV_PLUGIN_PREFIX . '_hide_catalog_for_products_by_product' );
			$b2be_catalogue_enabled                = false;

			$hide_products = array();
			$showed_yes    = array();
			$showed_no     = array();
			$rules         = $this->sort_rules_by_priority( $rules );

			foreach ( $rules as $key => $rule ) {
				if ( isset( $rule['rules'][0]['customers'] ) > 0 && 'yes' === $rule['is_enable'] ) {
					$rules_for_individual_customer                  = $this->get_matched_rules_for_customer_and_location( get_current_user_id(), $rule['rules'], 'customers' );
					$rules_for_individual_customer                  = $this->sort_rules_by_actions( $rules_for_individual_customer );
					list( $hide_products, $showed_yes, $showed_no ) = $this->apply_rules( $rules_for_individual_customer, (array) $hide_products, (array) $showed_yes, (array) $showed_no );

				} elseif ( 'yes' === $rule['is_enable'] && isset( $rule['rules'][0]['user_roles'] ) ) {
					$roles = wp_get_current_user()->roles;
					if ( $roles ) {
						$rules_for_user_roles                           = $this->get_matched_rules_for_user_roles( $roles, $rule['rules'], 'user_roles' );
						$rules_for_user_roles                           = $this->sort_rules_by_actions( $rules_for_user_roles );
						list( $hide_products, $showed_yes, $showed_no ) = $this->apply_rules( $rules_for_user_roles, (array) $hide_products, (array) $showed_yes, (array) $showed_no );
					}
				} elseif ( 'yes' === $rule['is_enable'] && isset( $rule['rules'][0]['user_groups'] ) ) {
					$groups = get_user_meta( get_current_user_id(), CWCCV_PLUGIN_PREFIX . '_user_groups_select', true );
					if ( $groups ) {
						$rules_for_user_groups                          = $this->get_matched_rules_for_user_groups( $groups, $rule['rules'], 'user_groups' );
						$rules_for_user_groups                          = $this->sort_rules_by_actions( $rules_for_user_groups );
						list( $hide_products, $showed_yes, $showed_no ) = $this->apply_rules( $rules_for_user_groups, (array) $hide_products, (array) $showed_yes, (array) $showed_no );
					}
				} elseif ( 'yes' === $rule['is_enable'] && isset( $rule['rules'][0]['price'] ) ) {
					$hide_products                                  = $this->sort_rules_by_actions( $hide_products );
					list( $hide_products, $showed_yes, $showed_no ) = $this->apply_rules_for_price_tier( $rule['rules'], (array) $hide_products, (array) $showed_yes, (array) $showed_no );

				} elseif ( 'yes' === $rule['is_enable'] && isset( array_values( $rule['rules'] )[0]['location'] ) ) {

					$location = $this->get_current_user_location();
					if ( $location ) {
						$rules_for_geo_location                         = $this->get_matched_rules_for_customer_and_location( $location, $rule['rules'], 'location' );
						$rules_for_geo_location                         = $this->sort_rules_by_actions( $rules_for_geo_location );
						list( $hide_products, $showed_yes, $showed_no ) = $this->apply_rules( $rules_for_geo_location, (array) $hide_products, (array) $showed_yes, (array) $showed_no );
					}
				}
			}

			if ( ! is_user_logged_in() ) {

				$products = $this->get_products_id();
				foreach ( $products as $key => $product_id ) {
					if ( 'yes' == $hide_price_for_non_login_users_option ) {
						if ( 'yes' == $hide_whole_catalog_price_option ) {
							array_push( $hide_products, $product_id );
						} elseif ( ( is_array( $hide_catalog_price_for_categories ) && has_term( $this->get_category_slug( $hide_catalog_price_for_categories ), 'product_cat', $product_id ) ) || ( is_array( $hide_catalog_price_for_products ) && in_array( $product_id, $hide_catalog_price_for_products ) ) ) {
							array_push( $hide_products, $product_id );
						}
					}
				}
			}
			return $hide_products;
		}

		/**
		 * Gets the list of category slug.
		 *
		 * @param array $category_ids List of cateogy id.
		 * @return string $category_ids
		 */
		public function get_category_slug( $category_ids, $rule = array() ) {
			$cate = array();
			if ( count( $category_ids ) > 0 ) {
				foreach ( $category_ids as $key => $value ) {
					$term = get_term_by( 'id', $value, 'product_cat' );

					if ( $term ) {
						$cate[]    = $term->slug;
						$cat_ids[] = $value;
					}

					// get child categories if exist.
					$child_cat = get_terms(
						'product_cat',
						array(
							'child_of'     => $value,
							'hierarchical' => true,
							'hide_empty'   => false,
						)
					);

					if ( count( $child_cat ) > 0 && ! isset( $child_cat->errors ) ) {
						foreach ( $child_cat as $child_key => $child_value ) {
							$cate[]    = $child_value->slug;
							$cat_ids[] = $child_value->term_id;
						}
					}
				}
			}

			/*
			@name: cwcv_set_hidden_categories
			@desc: Set hidden categories.
			@param: (array) $cat_ids cat ids.
			@param: (array) $rule rule array.
			@package: catalog-visibility-for-woocommerce
			@module: frontend
			@type: action
			*/
			do_action( 'cwcv_set_hidden_categories', $cat_ids, $rule );

			return $cate;
		}

		/**
		 * Returns matched rules for current logged in user id, role, or group.
		 *
		 * @param int/string $value Calue to be matched.
		 * @param array      $rules List of rules to be filtered.
		 * @param string     $rule_key For rules key.
		 * @return list $matched_rules
		 */
		public function get_matched_rules_for_customer_and_location( $value, $rules, $rule_key ) {

			$matched_rules = array();
			foreach ( $rules as $rule ) {
				if ( is_array( $rule[ $rule_key ] ) && in_array( $value, $rule[ $rule_key ] ) ) {
					array_push( $matched_rules, $rule );
				}
			}
			return $matched_rules;
		}

		/**
		 * Returns matched rules for roles associated to current logged in user.
		 *
		 * @param string $user_roles Current user roles/group.
		 * @param array  $rules List of rules to be filtered.
		 * @param string $role_key Key for user roles and groups.
		 * @return $matched_rules
		 */
		public function get_matched_rules_for_user_roles( $user_roles, $rules, $role_key ) {
			$matched_rules = array();
			foreach ( $rules as $key => $rule ) {

				$role_settings = array_map(
					function( $value ) {
						return $this->get_role_by_name( $value );
					},
					$rule[ $role_key ]
				);

				foreach ( $user_roles as  $user_role ) {
					if ( in_array( $user_role, $role_settings ) ) {
						array_push( $matched_rules, $rule );
						break;
					}
				}
			}
			return $matched_rules;
		}

		public function get_role_by_name( $user_name ) {
			return array_search( $user_name, wp_roles()->role_names );
		}

		/**
		 * Returns matched rules for roles associated to current logged in user.
		 *
		 * @param string $user_roles Current user roles/group.
		 * @param array  $rules List of rules to be filtered.
		 * @param string $role_key Key for user roles and groups.
		 * @return $matched_rules
		 */
		public function get_matched_rules_for_user_groups( $user_roles, $rules, $role_key ) {
			$matched_rules = array();
			foreach ( $rules as $key => $rule ) {
				foreach ( $user_roles as  $user_role ) {
					if ( in_array( $user_role, $rule[ $role_key ] ) ) {
						array_push( $matched_rules, $rule );
						break;
					}
				}
			}
			return $matched_rules;
		}

		/**
		 * Sort the rules by action and returns the list.
		 *
		 * @param list $rules List of rules to be sorted.
		 * @return list $rules.
		 */
		public function sort_rules_by_actions( $rules ) {

			$length_of_rules = count( $rules );

			for ( $i = 0; $i < $length_of_rules; $i++ ) {
				for ( $j = 0; $j < $length_of_rules - 1; $j++ ) {
					if ( 'yes' == $rules[ $j ]['is_shown'] && 'no' == $rules[ $j + 1 ]['is_shown'] ) {
						$temp            = $rules[ $j ];
						$rules[ $j ]     = $rules[ $j + 1 ];
						$rules[ $j + 1 ] = $temp;
					}
				}
			}
			return $rules;
		}

		/**
		 * Gets current user location.
		 *
		 * @param string $ip Ip Address to be sent.
		 * @return string $slug
		 */
		public function get_current_user_location( $ip = null ) {

			$geo           = new WC_Geolocation(); // Get WC_Geolocation instance object
			$user_ip       = $geo->get_ip_address(); // Get user IP
			$user_geo      = $geo->geolocate_ip( $user_ip ); // Get geolocated user data.
			$country_code  = $user_geo['country']; // Get the country code

			return strtolower($country_code);
		}

		/**
		 * Sort rules by proirity
		 *
		 * @param array $rules List of rules to be sort.
		 * @return array $rules
		 */
		public function sort_rules_by_priority( $rules ) {
			$array_count = count( $rules );
			if ( count( $rules ) > 1 ) {
				for ( $i = 0; $i < $array_count; $i++ ) {
					for ( $j = 0; $j < $array_count - 1; $j++ ) {
						if ( $rules[ $j ]['priority'] > $rules[ $j + 1 ]['priority'] ) {
							$temp            = $rules[ $j ];
							$rules[ $j ]     = $rules[ $j + 1 ];
							$rules[ $j + 1 ] = $temp;
						}
					}
				}
			}
			return $rules;
		}

		/**
		 * Applies rules for visibility settings.
		 * Works for every rule except price tier
		 *
		 * @param array $rules List of rules to be applied.
		 * @param array $hide_products ( list of the products to be hidden ).
		 * @param array $showed_yes ( list of the products that has been showed by priority ).
		 * @param array $showed_no ( list of the products that has been hide  by priority).
		 * @return array $hide_products , $showed_yes , $showed_no.
		 */
		public function apply_rules( $rules, $hide_products, $showed_yes, $showed_no ) {
			// if rules are empty then return with existing products to be hidden.
			if ( empty( $rules ) ) {
				return array( $hide_products, $showed_yes, $showed_no );
			}
			$products        = $this->get_products_id();
			$hidden_products = array();

			foreach ( $rules as $rule ) {
				foreach ( $products as $product ) {
					if ( ( is_array( $rule['categories'] ) && has_term( $this->get_category_slug( $rule['categories'], $rule ), 'product_cat', $product ) ) || ( is_array( $rule['products'] ) && in_array( $product, $rule['products'] ) ) ) {

						if ( 'yes' === $rule['is_shown'] ) {
							if ( ! in_array( $product, $showed_yes ) ) {
								array_push( $showed_yes, $product );
							}
						} elseif ( 'no' === $rule['is_shown'] ) {
							if ( ! in_array( $product, $showed_no ) ) {
								array_push( $showed_no, $product );
							}
						}
					}
				}
			}

			// get total hidden products if exist else  get all bydefault.
			if ( count( $showed_no ) > 0 ) {
				if ( count( $hide_products ) > 0 ) {
					foreach ( $showed_no as $show_key => $show_val ) {
						$array_key = array_search( $show_val, $hide_products );
						if ( false === $array_key && ! in_array( $show_val, $showed_yes ) ) {
							array_push( $hide_products, $show_val );
						}
					}
				} else {
					$hidden_products = array_intersect( $products, $showed_no ); // getting hide ids.
				}
			}

			// get total showed products if exist else get return with default hide ids.
			if ( count( $showed_yes ) > 0 ) {
				if ( count( $hide_products ) > 0 ) {
					foreach ( $showed_yes as $show_key => $show_val ) {
						$array_key = array_search( $show_val, $hide_products );
						if ( false !== $array_key && ! in_array( $show_val, $showed_no ) ) {
							unset( $hide_products[ $array_key ] );
						}
					}
				} else {
					$hidden_products = array_diff( $products, $showed_yes );
				}
			}

			$hide_products = array_merge( $hide_products, $hidden_products );

			return array( $hide_products, $showed_yes, $showed_no );
		}

		/**
		 * Applies rule for price tier rule only.
		 *
		 * @param array $rules Rules to be applied.
		 * @param array $hide_products List of products to be hidden.
		 * @param array $showed_yes ( list of the products that has been showed by priority ).
		 * @param array $showed_no ( list of the products that has been hide  by priority).
		 * @return array $hide_products , $showed_yes , $showed_no.
		 */
		public function apply_rules_for_price_tier( $rules, $hide_products, $showed_yes, $showed_no ) {

			$price = $this->get_previous_orders_price();
			if ( ! $price ) {
				return array( $hide_products, $showed_yes, $showed_no );
			}
			$products_with_price = $this->get_products_id( true );
			$products            = $this->get_products_id();
			$hidden_products     = array();
			foreach ( $rules as $rule ) {
				foreach ( $products_with_price as $product ) {
					if ( $price >= $rule['price']['from'] && $price <= $rule['price']['to'] ) {

						if ( ( is_array( $rule['categories'] ) && has_term( $this->get_category_slug( $rule['categories'] ), 'product_cat', $product['id'] ) ) || ( is_array( $rule['products'] ) && in_array( $product['id'], $rule['products'] ) ) ) {
							if ( 'yes' === $rule['is_shown'] ) {

								if ( ! in_array( $product, $showed_yes ) ) {
									array_push( $showed_yes, $product['id'] );
								}
							} elseif ( 'no' === $rule['is_shown'] ) {
								if ( ! in_array( $product, $showed_no ) ) {
									array_push( $showed_no, $product['id'] );
								}
							}
						}
					}
				}
			}

			// get total hidden products if exist else  get all bydefault.
			if ( count( $showed_no ) > 0 ) {
				if ( count( $hide_products ) > 0 ) {
					foreach ( $showed_no as $show_key => $show_val ) {
						$array_key = array_search( $show_val, $hide_products );
						if ( false === $array_key && ! in_array( $show_val, $showed_yes ) ) {
							array_push( $hide_products, $show_val );
						}
					}
				} else {
					$hidden_products = array_intersect( $products, $showed_no ); // getting hide ids.
				}
			}

			// get total showed products if exist else get return with default hide ids.
			if ( count( $showed_yes ) > 0 ) {
				if ( count( $hide_products ) > 0 ) {
					foreach ( $showed_yes as $show_key => $show_val ) {
						$array_key = array_search( $show_val, $hide_products );
						if ( false !== $array_key && ! in_array( $show_val, $showed_no ) ) {
							unset( $hide_products[ $array_key ] );
						}
					}
				} else {
					$hidden_products = array_diff( $products, $showed_yes );
				}
			}
			$hide_products = array_merge( $hide_products, $hidden_products );
			return array( $hide_products, $showed_yes, $showed_no );
		}

		/**
		 * Returns lowest order price and highest order price from previous orders.
		 */
		public function get_previous_orders_price() {

			$product_prices  = 0;
			$customer_orders = get_posts(
				array(
					'numberposts' => -1,
					'meta_key'    => '_customer_user',
					'meta_value'  => get_current_user_id(),
					'post_type'   => wc_get_order_types(),
					'post_status' => 'wc-completed',
				)
			);
			// Gets customer order prices.
			if ( count( $customer_orders ) > 0 ) {
				foreach ( $customer_orders as $customer_order ) {
					$order = wc_get_order( $customer_order->ID );
					$items = $order->get_items();
					foreach ( $items as $item ) {
						$product_prices += $item->get_total();
					}
				}
			}

			return $product_prices;
		}

		/**
		 * Returns list of all products id. if $is_object is set to true then returns the products id with its price.
		 *
		 * @since 1.1.1.0
		 * @param boolean $is_price If we want price along the product or not.
		 * @return array $products
		 */
		public static function get_products_id( $is_price = false ) {

			$products       = array(); // Product list.
			$query_args     = array(
				'post_type'        => 'product',
				'numberposts'      => -1,
				'suppress_filters' => false,
			);
			$products_query = get_posts( $query_args );

			if ( null !== $products_query ) {
				foreach ( $products_query as $key => $value ) {
					if ( true === $is_price ) {
						$product                   = wc_get_product( $value->ID );
						$price                     = $product->get_price();
						$products[ $key ]['id']    = $value->ID;
						$products[ $key ]['price'] = $price;
					} else {
						$products[ $key ] = $value->ID;
					}
				}
			}
			return $products;
		}
	}
	new CWCCV_Catalog_Visibility();
}
