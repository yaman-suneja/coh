<?php
/**
 * WC Ecommerce For Woocommerce Main Class.
 *
 * @package b2b-ecommerce-for-woocomerce
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'B2BE_Catalogue_Visibility_Settings' ) ) {
	/**
	 * Codup_B2B_Ecommerce_For_Woocommerce class.
	 */
	class B2BE_Catalogue_Visibility_Settings {

		/**
		 * Return all the necessary settings.
		 */
		public static function init() {

			add_action( 'init', array( __CLASS__, 'b2be_create_user_groups_taxonomy' ), 0 );
			add_action( 'admin_menu', array( __CLASS__, 'b2be_add_user_group_page' ) );
			add_filter( 'manage_b2be-user-groups_custom_column', array( __CLASS__, 'manage_b2be_user_group_column' ), 10, 3 );
			add_filter( 'manage_edit-b2be-user-groups_columns', array( __CLASS__, 'b2be_add_user_group_column' ), 10, 3 );
			add_filter( 'parent_file', array( __CLASS__, 'b2be_user_group_change_parent' ) );

			// Add action for show settings on user profile settings.
			add_action( 'edit_user_profile', array( __CLASS__, 'show_association_on_user_settings' ) );
			add_action( 'show_user_profile', array( __CLASS__, 'show_association_on_user_settings' ) );

			// Add action for save and update settings.
			add_action( 'personal_options_update', array( __CLASS__, 'save_user_settings' ) );
			add_action( 'edit_user_profile_update', array( __CLASS__, 'save_user_settings' ) );

			add_action( 'woocommerce_admin_field_b2be_visibility_template', __CLASS__ . '::get_b2be_visibility_template' );

		}

		/**
		 * Adding User group column.
		 *
		 * @param string $columns Column name.
		 */
		public static function b2be_add_user_group_column( $columns ) {
			unset( $columns['posts'] );
			$columns['users'] = __( 'Users' );
			return $columns;
		}

		/**
		 * Managing the user group column.
		 *
		 * @param string $display Display name.
		 * @param string $column Column Name.
		 * @param string $term_id Term Id.
		 */
		public static function manage_b2be_user_group_column( $display, $column, $term_id ) {
			if ( 'users' === $column ) {
				$user_count = 0;
				$term       = get_term( $term_id, 'b2be-user-groups' );
				$users      = new WP_User_Query(
					array(
						'meta_key'     => 'b2be_catalogue_user_groups_select',
						'meta_value'   => $term->name,
						'meta_compare' => 'LIKE',
					)
				);
				if ( $users->get_results() ) {
					$user_count = count( $users->get_results() );
				}
				echo wp_kses_post( $user_count );
			}
		}

		/**
		 * Changing the parent file name for user group.
		 *
		 * @param string $parent_file Parent file template.
		 */
		public static function b2be_user_group_change_parent( $parent_file ) {
			global $submenu_file;
			if ( isset( $_GET['taxonomy'] ) && 'b2be-user-groups' == $_GET['taxonomy'] && 'edit-tags.php?taxonomy=b2be-user-groups' == $submenu_file ) {
				$parent_file = 'users.php';
			}
			return $parent_file;
		}

		/**
		 * Create two taxonomies, genres and writers for the post type "book".
		 *
		 * @see register_post_type() for registering custom post types.
		 */
		public static function b2be_create_user_groups_taxonomy() {

			$labels = array(
				'name'                       => _x( 'User Groups', 'taxonomy general name', 'textdomain' ),
				'singular_name'              => _x( 'User Group', 'taxonomy singular name', 'textdomain' ),
				'search_items'               => __( 'Search User Group', 'textdomain' ),
				'popular_items'              => __( 'Popular User Group', 'textdomain' ),
				'all_items'                  => __( 'All User Group', 'textdomain' ),
				'parent_item'                => null,
				'parent_item_colon'          => null,
				'edit_item'                  => __( 'Edit User Group', 'textdomain' ),
				'update_item'                => __( 'Update User Group', 'textdomain' ),
				'add_new_item'               => __( 'Add New User Group', 'textdomain' ),
				'new_item_name'              => __( 'New User Group Name', 'textdomain' ),
				'separate_items_with_commas' => __( 'Separate User Groups with commas', 'textdomain' ),
				'add_or_remove_items'        => __( 'Add or remove User Groups', 'textdomain' ),
				'choose_from_most_used'      => __( 'Choose from the most used User Groups', 'textdomain' ),
				'not_found'                  => __( 'No User Groups found.', 'textdomain' ),
				'menu_name'                  => __( 'User Group', 'textdomain' ),
			);

			$args = array(
				'hierarchical'          => false,
				'labels'                => $labels,
				'show_ui'               => true,
				'show_admin_column'     => true,
				'update_count_callback' => '_update_post_term_count',
				'query_var'             => true,
				'rewrite'               => array( 'slug' => 'b2be-user-groups' ),
			);

			register_taxonomy( 'b2be-user-groups', 'user', $args );
		}

		/**
		 * Adding user group page.
		 */
		public static function b2be_add_user_group_page() {
			$tax = get_taxonomy( 'b2be-user-groups' );
			add_users_page(
				esc_attr( $tax->labels->menu_name ),
				esc_attr( $tax->labels->menu_name ),
				$tax->cap->manage_terms,
				'edit-tags.php?taxonomy=' . $tax->name
			);
		}

		/**
		 * Return required login setting fields.
		 *
		 * @return type
		 */
		public static function get_settings() {

			$settings = array(
				'b2be_visibility_template' => array(
					'type' => 'b2be_visibility_template',
					'id'   => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_title',
				),
				'b2be_section_end'         => array(
					'type' => 'sectionend',
					'id'   => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_title',
				),
			);

			return $settings;
		}

		/**
		 * Saves the visibility setting.
		 *
		 * @since 1.1.1.0
		 */
		public static function save_visibility_settings() {

			$individual_user_priority                    = filter_input( INPUT_POST, B2BE_CATALOGUE_VISIBILITY_PREFIX . '_individual_customer_priority_select', FILTER_DEFAULT, FILTER_SANITIZE_STRING );
			$_individual_customer_settings_enable_toggle = filter_input( INPUT_POST, B2BE_CATALOGUE_VISIBILITY_PREFIX . '_individual_customer_settings_enable_toggle', FILTER_DEFAULT, FILTER_SANITIZE_STRING );
			$individual_user_is_enable                   = $_individual_customer_settings_enable_toggle ? 'yes' : 'no';
			$individual_user_categories                  = filter_input( INPUT_POST, B2BE_CATALOGUE_VISIBILITY_PREFIX . '_individual_customer_category_select', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			$individual_user_products                    = filter_input( INPUT_POST, B2BE_CATALOGUE_VISIBILITY_PREFIX . '_individual_customer_product_select', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			$individual_user_customers                   = filter_input( INPUT_POST, B2BE_CATALOGUE_VISIBILITY_PREFIX . '_individual_customer_customer_name_select', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			$individual_user_is_shown                    = filter_input( INPUT_POST, B2BE_CATALOGUE_VISIBILITY_PREFIX . '_individual_customer_products_show_hide_radio', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			$hide_for_individual_user                    = array();

			$user_roles_priority                = filter_input( INPUT_POST, B2BE_CATALOGUE_VISIBILITY_PREFIX . '_user_roles_priority_select', FILTER_DEFAULT, FILTER_SANITIZE_STRING );
			$_user_roles_settings_enable_toggle = filter_input( INPUT_POST, B2BE_CATALOGUE_VISIBILITY_PREFIX . '_user_roles_settings_enable_toggle', FILTER_DEFAULT, FILTER_SANITIZE_STRING );
			$user_roles_is_enable               = $_user_roles_settings_enable_toggle ? 'yes' : 'no';
			$user_roles_categories              = filter_input( INPUT_POST, B2BE_CATALOGUE_VISIBILITY_PREFIX . '_user_roles_category_select', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			$user_roles_products                = filter_input( INPUT_POST, B2BE_CATALOGUE_VISIBILITY_PREFIX . '_user_roles_product_select', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			$user_roles                         = filter_input( INPUT_POST, B2BE_CATALOGUE_VISIBILITY_PREFIX . '_user_roles_roles_name_select', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			$user_roles_is_shown                = filter_input( INPUT_POST, B2BE_CATALOGUE_VISIBILITY_PREFIX . '_user_roles_show_hide_radio', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			$hide_for_user_roles                = array();

			$user_groups_priority                = filter_input( INPUT_POST, B2BE_CATALOGUE_VISIBILITY_PREFIX . '_user_groups_priority_select', FILTER_DEFAULT, FILTER_SANITIZE_STRING );
			$_user_groups_settings_enable_toggle = filter_input( INPUT_POST, B2BE_CATALOGUE_VISIBILITY_PREFIX . '_user_groups_settings_enable_toggle', FILTER_DEFAULT, FILTER_SANITIZE_STRING );
			$user_groups_is_enable               = $_user_groups_settings_enable_toggle ? 'yes' : 'no';
			$user_groups_categories              = filter_input( INPUT_POST, B2BE_CATALOGUE_VISIBILITY_PREFIX . '_user_groups_category_select', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			$user_groups_products                = filter_input( INPUT_POST, B2BE_CATALOGUE_VISIBILITY_PREFIX . '_user_groups_product_select', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			$user_groups                         = filter_input( INPUT_POST, B2BE_CATALOGUE_VISIBILITY_PREFIX . '_user_groups_groups_name_select', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			$user_groups_is_shown                = filter_input( INPUT_POST, B2BE_CATALOGUE_VISIBILITY_PREFIX . '_user_groups_show_hide_radio', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			$hide_for_user_groups                = array();

			$price_tier_priority                = filter_input( INPUT_POST, B2BE_CATALOGUE_VISIBILITY_PREFIX . '_price_tier_priority_select', FILTER_DEFAULT, FILTER_SANITIZE_STRING );
			$_price_tier_settings_enable_toggle = filter_input( INPUT_POST, B2BE_CATALOGUE_VISIBILITY_PREFIX . '_price_tier_settings_enable_toggle', FILTER_DEFAULT, FILTER_SANITIZE_STRING );
			$price_tier_is_enable               = $_price_tier_settings_enable_toggle ? 'yes' : 'no';
			$price_tier_from_price              = filter_input( INPUT_POST, B2BE_CATALOGUE_VISIBILITY_PREFIX . '_price_tier_from_text_field', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			$price_tier_to_price                = filter_input( INPUT_POST, B2BE_CATALOGUE_VISIBILITY_PREFIX . '_price_tier_to_text_field', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			$price_tier_categories              = filter_input( INPUT_POST, B2BE_CATALOGUE_VISIBILITY_PREFIX . '_price_tier_category_select', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			$price_tier_products                = filter_input( INPUT_POST, B2BE_CATALOGUE_VISIBILITY_PREFIX . '_price_tier_product_select', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			$price_tier_is_shown                = filter_input( INPUT_POST, B2BE_CATALOGUE_VISIBILITY_PREFIX . '_price_tier_show_hide_radio', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			$hide_for_price_tier                = array();

			$geo_location_priority                = filter_input( INPUT_POST, B2BE_CATALOGUE_VISIBILITY_PREFIX . '_geo_location_priority_select', FILTER_DEFAULT, FILTER_SANITIZE_STRING );
			$_geo_location_settings_enable_toggle = filter_input( INPUT_POST, B2BE_CATALOGUE_VISIBILITY_PREFIX . '_geo_location_settings_enable_toggle', FILTER_DEFAULT, FILTER_SANITIZE_STRING );
			$geo_location_is_enable               = $_geo_location_settings_enable_toggle ? 'yes' : 'no';
			$geo_location_categories              = filter_input( INPUT_POST, B2BE_CATALOGUE_VISIBILITY_PREFIX . '_geo_location_category_select', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			$geo_location_products                = filter_input( INPUT_POST, B2BE_CATALOGUE_VISIBILITY_PREFIX . '_geo_location_product_select', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			$geo_location                         = filter_input( INPUT_POST, B2BE_CATALOGUE_VISIBILITY_PREFIX . '_geo_location_location_name_select', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			$geo_location_is_shown                = filter_input( INPUT_POST, B2BE_CATALOGUE_VISIBILITY_PREFIX . '_geo_location_show_hide_radio', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			$hide_for_geo_location                = array();

			// Structured individual customer.
			$hide_for_individual_user['priority']  = $individual_user_priority;
			$hide_for_individual_user['is_enable'] = $individual_user_is_enable;
			if ( is_array( $individual_user_customers ) ) {
				$i = 0;
				foreach ( $individual_user_customers as $key => $value ) {
					$hide_for_individual_user['rules'][ $i ]['customers']  = isset( $individual_user_customers[ $key ] ) ? array_map( 'intval', $individual_user_customers[ $key ] ) : null;
					$hide_for_individual_user['rules'][ $i ]['is_shown']   = isset( $individual_user_is_shown[ $key ] ) ? $individual_user_is_shown[ $key ] : null;
					$hide_for_individual_user['rules'][ $i ]['categories'] = isset( $individual_user_categories[ $key ] ) ? array_map( 'intval', $individual_user_categories[ $key ] ) : null;
					$hide_for_individual_user['rules'][ $i ]['products']   = isset( $individual_user_products[ $key ] ) ? array_map( 'intval', $individual_user_products[ $key ] ) : null;

					$i++;
				}
			}
			// Saving individual customers.
			update_option( B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_for_individual_customer', $hide_for_individual_user );

			// Structured user roles.
			$hide_for_user_roles['is_enable'] = $user_roles_is_enable;
			$hide_for_user_roles['priority']  = $user_roles_priority;
			if ( is_array( $user_roles ) ) {
				$i = 0;
				foreach ( $user_roles as $key => $value ) {
					$hide_for_user_roles['rules'][ $i ]['user_roles'] = isset( $user_roles[ $key ] ) ? $user_roles[ $key ] : null;
					$hide_for_user_roles['rules'][ $i ]['is_shown']   = isset( $user_roles_is_shown[ $key ] ) ? $user_roles_is_shown[ $key ] : null;
					$hide_for_user_roles['rules'][ $i ]['categories'] = isset( $user_roles_categories[ $key ] ) ? array_map( 'intval', $user_roles_categories[ $key ] ) : null;
					$hide_for_user_roles['rules'][ $i ]['products']   = isset( $user_roles_products[ $key ] ) ? array_map( 'intval', $user_roles_products[ $key ] ) : null;
					$i++;
				}
			}
			// Saving user roles.
			update_option( B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_for_user_roles', $hide_for_user_roles );

			// Structured user groups.
			$hide_for_user_groups['is_enable'] = $user_groups_is_enable;
			$hide_for_user_groups['priority']  = $user_groups_priority;
			if ( is_array( $user_groups ) ) {
				$i = 0;
				foreach ( $user_groups as $key => $value ) {
					$hide_for_user_groups['rules'][ $i ]['user_groups'] = isset( $user_groups[ $key ] ) ? $user_groups[ $key ] : null;
					$hide_for_user_groups['rules'][ $i ]['is_shown']    = isset( $user_groups_is_shown[ $key ] ) ? $user_groups_is_shown[ $key ] : null;
					$hide_for_user_groups['rules'][ $i ]['categories']  = isset( $user_groups_categories[ $key ] ) ? array_map( 'intval', $user_groups_categories[ $key ] ) : null;
					$hide_for_user_groups['rules'][ $i ]['products']    = isset( $user_groups_products[ $key ] ) ? array_map( 'intval', $user_groups_products[ $key ] ) : null;
					$i++;
				}
			}
			// Saving user groups.
			update_option( B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_for_user_groups', $hide_for_user_groups );

			// Structured price tier.
			$hide_for_price_tier['is_enable'] = $price_tier_is_enable;
			$hide_for_price_tier['priority']  = $price_tier_priority;
			if ( is_array( $price_tier_from_price ) && ! empty( $price_tier_from_price[0] ) ) {
				$i = 0;
				foreach ( $price_tier_from_price as $key => $value ) {
					$hide_for_price_tier['rules'][ $i ]['price']['from'] = isset( $price_tier_from_price[ $key ] ) ? intval( $price_tier_from_price[ $key ] ) : null;
					$hide_for_price_tier['rules'][ $i ]['price']['to']   = isset( $price_tier_to_price[ $key ] ) ? intval( $price_tier_to_price[ $key ] ) : null;
					$hide_for_price_tier['rules'][ $i ]['is_shown']      = isset( $price_tier_is_shown[ $key ] ) ? $price_tier_is_shown[ $key ] : null;
					$hide_for_price_tier['rules'][ $i ]['categories']    = isset( $price_tier_categories[ $key ] ) ? array_map( 'intval', $price_tier_categories[ $key ] ) : null;
					$hide_for_price_tier['rules'][ $i ]['products']      = isset( $price_tier_products[ $key ] ) ? array_map( 'intval', $price_tier_products[ $key ] ) : null;
					$i++;
				}
			}
			// Saving price tier.
			update_option( B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_for_price_tier', $hide_for_price_tier );

			// Structured price tier.
			$hide_for_geo_location['is_enable'] = $geo_location_is_enable;
			$hide_for_geo_location['priority']  = $geo_location_priority;
			if ( is_array( $geo_location ) ) {
				$i = 0;
				foreach ( $geo_location as $key => $value ) {
					$hide_for_geo_location['rules'][ $key ]['location']   = isset( $geo_location[ $key ] ) ? $geo_location[ $key ] : null;
					$hide_for_geo_location['rules'][ $key ]['is_shown']   = isset( $geo_location_is_shown[ $key ] ) ? $geo_location_is_shown[ $key ] : null;
					$hide_for_geo_location['rules'][ $key ]['categories'] = isset( $geo_location_categories[ $key ] ) ? array_map( 'intval', $geo_location_categories[ $key ] ) : null;
					$hide_for_geo_location['rules'][ $key ]['products']   = isset( $geo_location_products[ $key ] ) ? array_map( 'intval', $geo_location_products[ $key ] ) : null;
					$i++;
				}
			}
			// Saving price tier.
			update_option( B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_for_geo_location', $hide_for_geo_location );

			// HIDE PRICE FOR NON-LOGIN USERS.
			// Update categories and products for hide catalog option.
			$products   = filter_input( INPUT_POST, B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_catalog_product_select', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			$categories = filter_input( INPUT_POST, B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_catalog_category_select', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );

			$_hide_price_for_non_login_toggle = filter_input( INPUT_POST, B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_price_for_non_login_toggle', FILTER_DEFAULT, FILTER_SANITIZE_STRING );
			$_hide_whole_catalog_price_toggle = filter_input( INPUT_POST, B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_whole_catalog_price_toggle', FILTER_DEFAULT, FILTER_SANITIZE_STRING );

			$hide_catalog_price_for_non_login_option = $_hide_price_for_non_login_toggle ? 'yes' : 'no';
			$hide_whole_catalog_price_option         = $_hide_whole_catalog_price_toggle ? 'yes' : 'no';

			update_option( B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_price_for_non_login', $hide_catalog_price_for_non_login_option );
			update_option( B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_whole_catalog_price', $hide_whole_catalog_price_option );

			update_option( B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_catalog_for_products', $products );
			update_option( B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_catalog_for_categories', $categories );

			$products   = filter_input( INPUT_POST, B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_catalog_product_select_by_product', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			$categories = filter_input( INPUT_POST, B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_catalog_category_select_by_product', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );

			$_hide_product_for_non_login_toggle = filter_input( INPUT_POST, B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_product_for_non_login_toggle', FILTER_DEFAULT, FILTER_SANITIZE_STRING );
			$_hide_whole_catalog_product_toggle = filter_input( INPUT_POST, B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_whole_catalog_product_toggle', FILTER_DEFAULT, FILTER_SANITIZE_STRING );

			$hide_catalog_product_for_non_login_option = $_hide_product_for_non_login_toggle ? 'yes' : 'no';
			$hide_whole_catalog_product_option         = $_hide_whole_catalog_product_toggle ? 'yes' : 'no';

			update_option( B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_product_for_non_login', $hide_catalog_product_for_non_login_option );
			update_option( B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_whole_catalog_product', $hide_whole_catalog_product_option );

			update_option( B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_catalog_for_products_by_product', $products );
			update_option( B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_catalog_for_categories_by_product', $categories );

			$pages                            = filter_input( INPUT_POST, B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_catalog_pages', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			$_hide_pages_for_non_login_toggle = filter_input( INPUT_POST, B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_pages_for_non_login_toggle', FILTER_DEFAULT, FILTER_SANITIZE_STRING );
			$hide_pages_for_non_login_option  = $_hide_pages_for_non_login_toggle ? 'yes' : 'no';

			update_option( B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_catalog_pages', $pages );
			update_option( B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_pages_for_non_login_toggle', $hide_pages_for_non_login_option );

			$redirction_pages                       = filter_input( INPUT_POST, B2BE_CATALOGUE_VISIBILITY_PREFIX . '_page_for_redirection', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			$_hide_whole_store_for_non_login_toggle = filter_input( INPUT_POST, B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_whole_store_for_non_login_toggle', FILTER_DEFAULT, FILTER_SANITIZE_STRING );
			$_hide_whole_store_for_non_login_option = $_hide_whole_store_for_non_login_toggle ? 'yes' : 'no';

			update_option( B2BE_CATALOGUE_VISIBILITY_PREFIX . '_page_for_redirection', $redirction_pages );
			update_option( B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_whole_store_for_non_login_toggle', $_hide_whole_store_for_non_login_option );

		}

		/**
		 * Adds visibility settings template to custom tab section.
		 *
		 * @param array $field_args arguments for visibility template.
		 * @since 1.1.1.0
		 */
		public static function get_b2be_visibility_template( $field_args ) {

			$field_args['fields'] = self::get_fields_args();
			include CWRFQ_PLUGIN_DIR . '/includes/admin/catalogue-visibility/views/b2be-catalogue-visibility-settings.php';

		}

		/**
		 * Getting fields arguments.
		 */
		public static function get_fields_args() {
			return array(
				// Args for hide catalog option.
				'hide_whole_catalog_price_toggle'          => array(
					'name'       => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_whole_catalog_price_toggle',
					'id'         => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_whole_catalog_price_toggle',
					'class'      => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_whole_catalog_price_toggle',
					'is_checked' => get_option( B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_whole_catalog_price' ),
				),
				'hide_price_for_non_login_toggle'          => array(
					'name'       => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_price_for_non_login_toggle',
					'id'         => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_price_for_non_login_toggle',
					'class'      => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_price_for_non_login_toggle',
					'is_checked' => get_option( B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_price_for_non_login' ),
				),
				'hide_catalog_category_select'             => array(
					'name'                  => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_catalog_category_select',
					'class'                 => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_catalog_category_select',
					'option'                => B2BE_Catalogue_Visibility_Helper::get_categories(),
					'selected_option_value' => get_option( B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_catalog_for_categories' ),
					'is_disabled'           => ( 'yes' == get_option( B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_whole_catalog_price' ) ) ? 'disabled="disabled"' : '',
					'is_required'           => true,
				),
				'hide_catalog_product_select'              => array(
					'name'                  => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_catalog_product_select',
					'class'                 => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_catalog_product_select',
					'option'                => B2BE_Catalogue_Visibility_Helper::get_products(),
					'selected_option_value' => get_option( B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_catalog_for_products' ),
					'is_disabled'           => ( 'yes' == get_option( B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_whole_catalog_price' ) ) ? 'disabled="disabled"' : '',
					'is_required'           => true,
				),

				'hide_whole_catalog_product_toggle'        => array(
					'name'       => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_whole_catalog_product_toggle',
					'id'         => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_whole_catalog_product_toggle',
					'class'      => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_whole_catalog_product_toggle',
					'is_checked' => get_option( B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_whole_catalog_product' ),
				),
				'hide_product_for_non_login_toggle'        => array(
					'name'       => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_product_for_non_login_toggle',
					'id'         => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_product_for_non_login_toggle',
					'class'      => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_product_for_non_login_toggle',
					'is_checked' => get_option( B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_product_for_non_login' ),
				),
				'hide_catalog_category_select_by_product'  => array(
					'name'                  => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_catalog_category_select_by_product',
					'class'                 => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_catalog_category_select_by_product',
					'option'                => B2BE_Catalogue_Visibility_Helper::get_categories(),
					'selected_option_value' => get_option( B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_catalog_for_categories_by_product' ),
					'is_disabled'           => ( 'yes' == get_option( B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_whole_catalog_product' ) ) ? 'disabled="disabled"' : '',
					'is_required'           => true,
				),
				'hide_catalog_product_select_by_product'   => array(
					'name'                  => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_catalog_product_select_by_product',
					'class'                 => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_catalog_product_select_by_product',
					'option'                => B2BE_Catalogue_Visibility_Helper::get_products(),
					'selected_option_value' => get_option( B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_catalog_for_products_by_product' ),
					'is_disabled'           => ( 'yes' == get_option( B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_whole_catalog_product' ) ) ? 'disabled="disabled"' : '',
					'is_required'           => true,
				),

				'hide_pages_for_non_login_toggle'          => array(
					'name'       => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_pages_for_non_login_toggle',
					'id'         => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_pages_for_non_login_toggle',
					'class'      => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_pages_for_non_login_toggle',
					'is_checked' => get_option( B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_pages_for_non_login_toggle' ),
				),
				'hide_catalog_pages'                       => array(
					'name'                  => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_catalog_pages',
					'class'                 => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_catalog_pages',
					'option'                => B2BE_Catalogue_Visibility_Helper::get_pages(),
					'selected_option_value' => get_option( B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_catalog_pages' ),
					'is_required'           => true,
				),
				'hide_whole_store_for_non_login_toggle'    => array(
					'name'       => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_whole_store_for_non_login_toggle',
					'id'         => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_whole_store_for_non_login_toggle',
					'class'      => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_whole_store_for_non_login_toggle',
					'is_checked' => get_option( B2BE_CATALOGUE_VISIBILITY_PREFIX . '_hide_whole_store_for_non_login_toggle' ),
				),
				'page_for_redirection'                     => array(
					'name'                  => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_page_for_redirection',
					'class'                 => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_page_for_redirection',
					'option'                => B2BE_Catalogue_Visibility_Helper::get_pages(),
					'selected_option_value' => get_option( B2BE_CATALOGUE_VISIBILITY_PREFIX . '_page_for_redirection' ),
					'is_required'           => true,
				),
				// End of args for hide catalog option.

				// Args for individual customer settings.
				'individual_customer_settings_enable_toggle' => array(
					'name'       => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_individual_customer_settings_enable_toggle',
					'class'      => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_individual_customer_settings_enable_toggle',
					'is_checked' => false,
					'title'      => __( 'Turning on the toggle will let you create visibility rule by selecting individual customers and defining which products/categories to show/hide to them.', 'codup-woocommerce-catalog-visibility' ),
				),
				'individual_customer_product_show_radio'   => array(
					'name'       => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_individual_customer_products_show_hide_radio',
					'id'         => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_individual_customer_show_products_radio',
					'class'      => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_individual_customer_products_show_hide_radio',
					'is_checked' => false,
					'value'      => 'yes',
				),
				'individual_customer_product_hide_radio'   => array(
					'name'       => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_individual_customer_products_show_hide_radio',
					'id'         => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_individual_customer_hide_products_radio',
					'class'      => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_individual_customer_products_show_hide_radio',
					'is_checked' => false,
					'value'      => 'no',
				),
				'individual_customer_priority_select'      => array(
					'name'  => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_individual_customer_priority_select',
					'class' => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_individual_customer_priority_select',
					'title' => __( 'Select Priority from 1-5. In case of conflicting conditions, the rule with higher priority will be executed.', 'codup-woocommerce-catalog-visibility' ),
				),
				'individual_customer_customer_name_select' => array(
					'name'                  => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_individual_customer_customer_name_select',
					'class'                 => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_individual_customer_customer_name_select',
					'option'                => B2BE_Catalogue_Visibility_Helper::get_users(),
					'placeholder'           => __( 'Search Customers', 'codup-woocommerce-catalog-visibility' ),
					'selected_option_value' => array(),
				),
				'individual_customer_category_select'      => array(
					'name'                  => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_individual_customer_category_select',
					'class'                 => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_individual_customer_category_select',
					'option'                => B2BE_Catalogue_Visibility_Helper::get_categories(),
					'placeholder'           => __( 'Search Categories', 'codup-woocommerce-catalog-visibility' ),
					'selected_option_value' => array(),
				),
				'individual_customer_product_select'       => array(
					'name'                  => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_individual_customer_product_select',
					'class'                 => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_individual_customer_product_select',
					'option'                => B2BE_Catalogue_Visibility_Helper::get_products(),
					'placeholder'           => __( 'Search Products', 'codup-woocommerce-catalog-visibility' ),
					'selected_option_value' => array(),
				),
				'individual_customer_repeater_field_button' => array(
					'name'  => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_individual_customer_repeater_field_button',
					'class' => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_individual_customer_repeater_field_button',
				),
				// End Args for individual customer settings.

				// Args for user roles settings.
				'user_roles_settings_enable_toggle'        => array(
					'name'       => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_user_roles_settings_enable_toggle',
					'class'      => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_user_roles_settings_enable_toggle',
					'is_checked' => false,
				),
				'user_roles_priority_select'               => array(
					'name'  => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_user_roles_priority_select',
					'class' => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_user_roles_priority_select',
					'title' => __( 'Select Priority from 1-5. In case of conflicting conditions, the rule with higher priority will be executed.', 'codup-woocommerce-catalog-visibility' ),
				),
				'user_roles_roles_name_select'             => array(
					'name'                  => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_user_roles_roles_name_select',
					'class'                 => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_user_roles_roles_name_select',
					'option'                => B2BE_Catalogue_Visibility_Helper::get_user_roles(),
					'selected_option_value' => array(),
				),
				'user_roles_product_show_radio'            => array(
					'name'       => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_user_roles_show_hide_radio',
					'id'         => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_user_roles_show_radio',
					'class'      => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_user_roles_show_hide_radio',
					'is_checked' => false,
					'value'      => 'yes',
				),
				'user_roles_product_hide_radio'            => array(
					'name'       => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_user_roles_show_hide_radio',
					'id'         => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_user_roles_hide_radio',
					'class'      => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_user_roles_show_hide_radio',
					'is_checked' => false,
					'value'      => 'no',
				),
				'user_roles_category_select'               => array(
					'name'                  => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_user_roles_category_select',
					'class'                 => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_user_roles_category_select',
					'option'                => B2BE_Catalogue_Visibility_Helper::get_categories(),
					'selected_option_value' => array(),
				),
				'user_roles_product_select'                => array(
					'name'                  => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_user_roles_product_select',
					'class'                 => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_user_roles_product_select',
					'option'                => B2BE_Catalogue_Visibility_Helper::get_products(),
					'selected_option_value' => array(),
				),
				'user_roles_repeater_field_button'         => array(
					'name'  => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_user_roles_repeater_field_button',
					'class' => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_user_roles_repeater_field_button',
				),
				// End user roles args.

				// Args for user group settings.
				'user_groups_settings_enable_toggle'       => array(
					'name'       => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_user_groups_settings_enable_toggle',
					'class'      => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_user_groups_settings_enable_toggle',
					'is_checked' => false,
				),
				'user_groups_priority_select'              => array(
					'name'  => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_user_groups_priority_select',
					'class' => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_user_groups_priority_select',
					'title' => __( 'Select Priority from 1-5. In case of conflicting conditions, the rule with higher priority will be executed.', 'codup-woocommerce-catalog-visibility' ),
				),
				'user_groups_groups_name_select'           => array(
					'name'                  => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_user_groups_groups_name_select',
					'class'                 => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_user_groups_groups_name_select',
					'option'                => B2BE_Catalogue_Visibility_Helper::get_user_groups(),
					'selected_option_value' => array(),
				),
				'user_groups_product_show_radio'           => array(
					'name'       => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_user_groups_show_hide_radio',
					'id'         => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_user_groups_show_radio',
					'class'      => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_user_groups_show_hide_radio',
					'is_checked' => false,
					'value'      => 'yes',
				),
				'user_groups_product_hide_radio'           => array(
					'name'       => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_user_groups_show_hide_radio',
					'id'         => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_user_groups_hide_radio',
					'class'      => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_user_groups_show_hide_radio',
					'is_checked' => false,
					'value'      => 'no',
				),
				'user_groups_category_select'              => array(
					'name'                  => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_user_groups_category_select',
					'class'                 => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_user_groups_category_select',
					'option'                => B2BE_Catalogue_Visibility_Helper::get_categories(),
					'selected_option_value' => array(),
				),
				'user_groups_product_select'               => array(
					'name'                  => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_user_groups_product_select',
					'class'                 => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_user_groups_product_select',
					'option'                => B2BE_Catalogue_Visibility_Helper::get_products(),
					'selected_option_value' => array(),
				),
				'user_groups_repeater_field_button'        => array(
					'name'  => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_user_groups_repeater_field_button',
					'class' => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_user_groups_repeater_field_button',
				),
				// End user groups settings.

				// Args for price tier settings.
				'price_tier_settings_enable_toggle'        => array(
					'name'       => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_price_tier_settings_enable_toggle',
					'class'      => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_price_tier_settings_enable_toggle',
					'is_checked' => false,
				),
				'price_tier_priority_select'               => array(
					'name'  => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_price_tier_priority_select',
					'class' => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_price_tier_priority_select',
					'title' => __( 'Select Priority from 1-5. In case of conflicting conditions, the rule with higher priority will be executed.', 'codup-woocommerce-catalog-visibility' ),
				),
				'price_tier_from_text_field'               => array(
					'name'  => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_price_tier_from_text_field',
					'class' => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_price_tier_from_text_field',
					'value' => '',
				),
				'price_tier_to_text_field'                 => array(
					'name'  => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_price_tier_to_text_field',
					'class' => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_price_tier_to_text_field',
					'value' => '',
				),
				'price_tier_product_show_radio'            => array(
					'name'       => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_price_tier_show_hide_radio',
					'id'         => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_price_tier_show_radio',
					'class'      => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_price_tier_show_hide_radio',
					'is_checked' => false,
					'value'      => 'yes',
				),
				'price_tier_product_hide_radio'            => array(
					'name'       => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_price_tier_show_hide_radio',
					'id'         => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_price_tier_hide_radio',
					'class'      => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_price_tier_show_hide_radio',
					'is_checked' => false,
					'value'      => 'no',
				),
				'price_tier_category_select'               => array(
					'name'                  => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_price_tier_category_select',
					'class'                 => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_price_tier_category_select',
					'option'                => B2BE_Catalogue_Visibility_Helper::get_categories(),
					'selected_option_value' => array(),
				),
				'price_tier_product_select'                => array(
					'name'                  => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_price_tier_product_select',
					'class'                 => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_price_tier_product_select',
					'option'                => B2BE_Catalogue_Visibility_Helper::get_products(),
					'selected_option_value' => array(),
				),
				'price_tier_repeater_field_button'         => array(
					'name'  => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_price_tier_repeater_field_button',
					'class' => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_price_tier_repeater_field_button',
				),
				// End price tier settings.

				// Args for geo location settings.
				'geo_location_settings_enable_toggle'      => array(
					'name'       => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_geo_location_settings_enable_toggle',
					'class'      => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_geo_location_settings_enable_toggle',
					'is_checked' => false,
				),
				'geo_location_priority_select'             => array(
					'name'  => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_geo_location_priority_select',
					'class' => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_geo_location_priority_select',
					'title' => __( 'Select Priority from 1-5. In case of conflicting conditions, the rule with higher priority will be executed.', 'codup-woocommerce-catalog-visibility' ),
				),
				'geo_location_location_name_select'        => array(
					'name'                  => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_geo_location_location_name_select',
					'id'                    => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_geo_location_location_name_select',
					'class'                 => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_geo_location_location_name_select',
					'option'                => B2BE_Catalogue_Visibility_Helper::get_country_list(),
					'selected_option_value' => array(),
				),
				'geo_location_product_show_radio'          => array(
					'name'       => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_geo_location_show_hide_radio',
					'id'         => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_geo_location_show_radio',
					'class'      => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_geo_location_show_hide_radio',
					'is_checked' => false,
					'value'      => 'yes',
				),
				'geo_location_product_hide_radio'          => array(
					'name'       => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_geo_location_show_hide_radio',
					'id'         => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_geo_location_hide_radio',
					'class'      => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_geo_location_show_hide_radio',
					'is_checked' => false,
					'value'      => 'no',
				),
				'geo_location_category_select'             => array(
					'name'                  => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_geo_location_category_select',
					'class'                 => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_geo_location_category_select',
					'option'                => B2BE_Catalogue_Visibility_Helper::get_categories(),
					'selected_option_value' => array(),
				),
				'geo_location_product_select'              => array(
					'name'                  => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_geo_location_product_select',
					'class'                 => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_geo_location_product_select',
					'option'                => B2BE_Catalogue_Visibility_Helper::get_products(),
					'selected_option_value' => array(),
				),
				'geo_location_repeater_field_button'       => array(
					'name'  => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_geo_location_repeater_field_button',
					'class' => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_geo_location_repeater_field_button',
				),
				// End geo location settings.

			);
		}

		/**
		 * Shows association of catalog visibility roles and groups on user profile pages.
		 *
		 * @since 1.1.1.0
		 */
		public static function show_association_on_user_settings() {
			$field_args = self::get_field_args();
			require_once CWRFQ_PLUGIN_DIR . '/includes/admin/catalogue-visibility/views/b2be-catalogue-visibility-user-settings.php';
		}

		/**
		 * Save settings of custom fields on user profile pages.
		 *
		 * @param string $user_id Represents current user id.
		 * @since 1.1.1.0
		 */
		public static function save_user_settings( $user_id ) {
			// Sanitize values.
			$roles  = filter_input( INPUT_POST, B2BE_CATALOGUE_VISIBILITY_PREFIX . '_user_roles_select', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			$groups = filter_input( INPUT_POST, B2BE_CATALOGUE_VISIBILITY_PREFIX . '_user_groups_select', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );

			// Update values for user roles and user groups.
			if ( false !== $roles ) {
				update_user_meta( $user_id, B2BE_CATALOGUE_VISIBILITY_PREFIX . '_user_roles_select', $roles );
			}
			if ( false !== $groups ) {
				update_user_meta( $user_id, B2BE_CATALOGUE_VISIBILITY_PREFIX . '_user_groups_select', $groups );

				$user_groups_slug = array();
				$group_args       = array(
					'posts_per_page' => -1,
					'taxonomy'       => 'b2be-user-groups',
					'hide_empty'     => false,
				);

				$saved_groups = get_terms( $group_args );
				if ( $saved_groups ) {
					foreach ( $saved_groups as $key => $group ) {
						if ( in_array( $group->name, $groups ) ) {
							$user_groups_slug[ $key ] = $group->slug;
						}
					}
					wp_set_object_terms( $user_id, $user_groups_slug, 'b2be-user-groups', false );
					clean_object_term_cache( $user_id, 'b2be-user-groups' );
				}
			}
		}

		/**
		 * Returns field arguments for user profile setting custom fields.
		 *
		 * @since 1.1.1.0
		 */
		public static function get_field_args() {
			global $user_id;

			$user_groups = array();
			$group_args  = array(
				'posts_per_page' => -1,
				'taxonomy'       => 'b2be-user-groups',
				'hide_empty'     => false,
			);

			$groups = get_terms( $group_args );
			if ( $groups ) {
				foreach ( $groups as $key => $group ) {
					$user_groups[ $key ] = $group->name;
				}
			}

			$field_args = array(
				'user_groups_select' => array(
					'name'                  => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_user_groups_select',
					'class'                 => B2BE_CATALOGUE_VISIBILITY_PREFIX . '_user_groups_select',
					'option'                => $user_groups,
					'selected_option_value' => get_user_meta( $user_id, B2BE_CATALOGUE_VISIBILITY_PREFIX . '_user_groups_select', true ),
				),
			);
			return $field_args;
		}

	}
}
return B2BE_Catalogue_Visibility_Settings::init();
