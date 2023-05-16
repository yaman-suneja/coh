<?php
/**
 * File class-cwccv-settings-tab.php
 *
 * @package catalog-visibility-for-woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'CWCCV_Settings_Tab' ) ) {
	/**
	 * Class CWCCV_Settings_Tab.
	 * All the admin settings are defined here.
	 * Functions to add settings tab & sections and save settings are defined here.
	 */
	class CWCCV_Settings_Tab {
		/**
		 * Settings tab name.
		 *
		 * @var string $settings_tab
		 */
		public static $settings_tab = 'catalog_visibility';

		/**
		 * Constructor.
		 */
		public function __construct() {
			// Adding custom tab to woocommerce settings.
			add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_tab_to_woocommerce_settings' ), 30 );

			// Adding sections to woocommerce settings.
			add_action( 'woocommerce_sections_' . self::$settings_tab, array( $this, 'add_sections_to_custom_tab' ) );

			// Adds admin settings to catalog visibility tab.
			add_action( 'woocommerce_settings_tabs_' . self::$settings_tab, array( $this, 'get_admin_settings' ) );

			// Adding woocommerce admin field custom types call back functions.
			add_action( 'woocommerce_admin_field_visibility_template', array( $this, 'get_visibility_template' ) );
			add_action( 'woocommerce_admin_field_user_roles_template', array( $this, 'get_user_roles_template' ) );
			add_action( 'woocommerce_admin_field_user_groups_template', array( $this, 'get_user_groups_template' ) );

			// Adding save settings actions.
			add_action( 'woocommerce_settings_save_' . self::$settings_tab, array( $this, 'save_settings' ) );
		}

		/**
		 * Adds tab named "Catalog Visibility" to woocommerce settings.
		 *
		 * @param string $settings_tabs args for settings tab name.
		 * @since 1.1.1.0
		 * @return $settings_tabs
		 */
		public function add_settings_tab_to_woocommerce_settings( $settings_tabs ) {
			$settings_tabs [ self::$settings_tab ] = __( 'Catalog Visibility', 'codup-woocommerce-catalog-visibility' );
			return $settings_tabs;
		}

		/**
		 * Add sections to catalog visibility settings tab.
		 *
		 * @since 1.1.1.0
		 */
		public function add_sections_to_custom_tab() {

			global $current_section;

			WC()->session = new WC_Session_Handler();
			WC()->session->init();
			$sections = array(
				''             => __( 'Visibility Settings', 'codup-woocommerce-catalog-visibility' ),
				'roles'        => __( 'Roles', 'codup-woocommerce-catalog-visibility' ),
				'groups'       => __( 'Groups', 'codup-woocommerce-catalog-visibility' ),
			);
			$tab_name = self::$settings_tab;
			include_once CWCCV_ABSPATH . '/templates/cwccv-admin-settings-custom-tab-sections.php';
		}

		/**
		 * Gets the woocommerce settings.
		 *
		 * @since 1.1.1.0
		 */
		public function get_admin_settings() {
			woocommerce_admin_fields( $this->get_settings_template() );
		}

		/**
		 * Saves/updates woocommerce settings.
		 *
		 * @since 1.1.1.0
		 */
		public function save_settings() {
			WC()->session = new WC_Session_Handler();
			WC()->session->init();

			global $current_section;

			switch ( $current_section ) {
				case 'roles':
					$this->save_user_roles();
					break;
				case 'groups':
					$this->save_user_groups();
					break;
				default:
					$this->save_visibility_settings();
					break;
			}
		}

		/**
		 * Saves the user roles setting.
		 *
		 * @since 1.1.1.0
		 */
		public function save_user_roles() {

			$new_role = filter_input( INPUT_POST, CWCCV_PLUGIN_PREFIX . '_add_new_role_text_field', FILTER_SANITIZE_STRING );
			$roles    = get_option( CWCCV_PLUGIN_PREFIX . '_user_roles' );

			$checked = CWCCV_Helper::check_exist_value( trim( $new_role ), (array) $roles );
			if ( $checked ) {
				WC()->session->set( CWCCV_PLUGIN_PREFIX . '_errors', true );
				wp_redirect( admin_url( 'admin.php?page=wc-settings&tab=catalog_visibility&section=roles' ) );
				exit();
			}

			if ( ! empty( trim( $new_role ) ) ) {
				if ( ! empty( $roles ) ) {
					array_push( $roles, $new_role );
					update_option( CWCCV_PLUGIN_PREFIX . '_user_roles', $roles );
				} else {
					$roles = array();
					array_push( $roles, $new_role );
					update_option( CWCCV_PLUGIN_PREFIX . '_user_roles', $roles );
				}

				// add role in user list.
				$custom_user_role = str_replace( ' ', '_', trim( strtolower( $new_role ) ) );
				if ( ! wp_roles()->is_role( $custom_user_role ) ) {

					add_role( $custom_user_role, $new_role, array( 'read' => true ) );
				}
			}

		}

		/**
		 * Saves the user groups setting.
		 *
		 * @since 1.1.1.0
		 */
		public function save_user_groups() {

			$new_group = filter_input( INPUT_POST, CWCCV_PLUGIN_PREFIX . '_add_new_group_text_field', FILTER_SANITIZE_STRING );
			$groups    = get_option( CWCCV_PLUGIN_PREFIX . '_user_groups' );

			$checked = CWCCV_Helper::check_exist_value( trim( $new_group ), (array) $groups );
			if ( $checked ) {
				WC()->session->set( CWCCV_PLUGIN_PREFIX . '_errors', true );
				wp_redirect( admin_url( 'admin.php?page=wc-settings&tab=catalog_visibility&section=groups' ) );
				exit();
			}
			if ( ! empty( trim( $new_group ) ) ) {
				if ( ! empty( $groups ) ) {
					array_push( $groups, $new_group );
					update_option( CWCCV_PLUGIN_PREFIX . '_user_groups', $groups );
				} else {
					$groups = array();
					array_push( $groups, $new_group );
					update_option( CWCCV_PLUGIN_PREFIX . '_user_groups', $groups );
				}
			}
		}

		/**
		 * Saves the visibility setting.
		 *
		 * @since 1.1.1.0
		 */
		public function save_visibility_settings() {

			$individual_user_priority                    = filter_input( INPUT_POST, CWCCV_PLUGIN_PREFIX . '_individual_customer_priority_select', FILTER_DEFAULT, FILTER_SANITIZE_STRING );
			$_individual_customer_settings_enable_toggle = filter_input( INPUT_POST, CWCCV_PLUGIN_PREFIX . '_individual_customer_settings_enable_toggle', FILTER_DEFAULT, FILTER_SANITIZE_STRING );
			$individual_user_is_enable                   = $_individual_customer_settings_enable_toggle ? 'yes' : 'no';
			$individual_user_categories                  = filter_input( INPUT_POST, CWCCV_PLUGIN_PREFIX . '_individual_customer_category_select', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			$individual_user_products                    = filter_input( INPUT_POST, CWCCV_PLUGIN_PREFIX . '_individual_customer_product_select', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			$individual_user_customers                   = filter_input( INPUT_POST, CWCCV_PLUGIN_PREFIX . '_individual_customer_customer_name_select', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			$individual_user_is_shown                    = filter_input( INPUT_POST, CWCCV_PLUGIN_PREFIX . '_individual_customer_products_show_hide_radio', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			$hide_for_individual_user                    = array();

			$user_roles_priority                = filter_input( INPUT_POST, CWCCV_PLUGIN_PREFIX . '_user_roles_priority_select', FILTER_DEFAULT, FILTER_SANITIZE_STRING );
			$_user_roles_settings_enable_toggle = filter_input( INPUT_POST, CWCCV_PLUGIN_PREFIX . '_user_roles_settings_enable_toggle', FILTER_DEFAULT, FILTER_SANITIZE_STRING );
			$user_roles_is_enable               = $_user_roles_settings_enable_toggle ? 'yes' : 'no';
			$user_roles_categories              = filter_input( INPUT_POST, CWCCV_PLUGIN_PREFIX . '_user_roles_category_select', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			$user_roles_products                = filter_input( INPUT_POST, CWCCV_PLUGIN_PREFIX . '_user_roles_product_select', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			$user_roles                         = filter_input( INPUT_POST, CWCCV_PLUGIN_PREFIX . '_user_roles_roles_name_select', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			$user_roles_is_shown                = filter_input( INPUT_POST, CWCCV_PLUGIN_PREFIX . '_user_roles_show_hide_radio', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			$hide_for_user_roles                = array();

			$user_groups_priority                = filter_input( INPUT_POST, CWCCV_PLUGIN_PREFIX . '_user_groups_priority_select', FILTER_DEFAULT, FILTER_SANITIZE_STRING );
			$_user_groups_settings_enable_toggle = filter_input( INPUT_POST, CWCCV_PLUGIN_PREFIX . '_user_groups_settings_enable_toggle', FILTER_DEFAULT, FILTER_SANITIZE_STRING );
			$user_groups_is_enable               = $_user_groups_settings_enable_toggle ? 'yes' : 'no';
			$user_groups_categories              = filter_input( INPUT_POST, CWCCV_PLUGIN_PREFIX . '_user_groups_category_select', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			$user_groups_products                = filter_input( INPUT_POST, CWCCV_PLUGIN_PREFIX . '_user_groups_product_select', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			$user_groups                         = filter_input( INPUT_POST, CWCCV_PLUGIN_PREFIX . '_user_groups_groups_name_select', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			$user_groups_is_shown                = filter_input( INPUT_POST, CWCCV_PLUGIN_PREFIX . '_user_groups_show_hide_radio', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			$hide_for_user_groups                = array();

			$price_tier_priority                = filter_input( INPUT_POST, CWCCV_PLUGIN_PREFIX . '_price_tier_priority_select', FILTER_DEFAULT, FILTER_SANITIZE_STRING );
			$_price_tier_settings_enable_toggle = filter_input( INPUT_POST, CWCCV_PLUGIN_PREFIX . '_price_tier_settings_enable_toggle', FILTER_DEFAULT, FILTER_SANITIZE_STRING );
			$price_tier_is_enable               = $_price_tier_settings_enable_toggle ? 'yes' : 'no';
			$price_tier_from_price              = filter_input( INPUT_POST, CWCCV_PLUGIN_PREFIX . '_price_tier_from_text_field', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			$price_tier_to_price                = filter_input( INPUT_POST, CWCCV_PLUGIN_PREFIX . '_price_tier_to_text_field', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			$price_tier_categories              = filter_input( INPUT_POST, CWCCV_PLUGIN_PREFIX . '_price_tier_category_select', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			$price_tier_products                = filter_input( INPUT_POST, CWCCV_PLUGIN_PREFIX . '_price_tier_product_select', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			$price_tier_is_shown                = filter_input( INPUT_POST, CWCCV_PLUGIN_PREFIX . '_price_tier_show_hide_radio', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			$hide_for_price_tier                = array();

			$geo_location_priority                = filter_input( INPUT_POST, CWCCV_PLUGIN_PREFIX . '_geo_location_priority_select', FILTER_DEFAULT, FILTER_SANITIZE_STRING );
			$_geo_location_settings_enable_toggle = filter_input( INPUT_POST, CWCCV_PLUGIN_PREFIX . '_geo_location_settings_enable_toggle', FILTER_DEFAULT, FILTER_SANITIZE_STRING );
			$geo_location_is_enable               = $_geo_location_settings_enable_toggle ? 'yes' : 'no';
			$geo_location_categories              = filter_input( INPUT_POST, CWCCV_PLUGIN_PREFIX . '_geo_location_category_select', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			$geo_location_products                = filter_input( INPUT_POST, CWCCV_PLUGIN_PREFIX . '_geo_location_product_select', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			$geo_location                         = filter_input( INPUT_POST, CWCCV_PLUGIN_PREFIX . '_geo_location_location_name_select', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			$geo_location_is_shown                = filter_input( INPUT_POST, CWCCV_PLUGIN_PREFIX . '_geo_location_show_hide_radio', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			$hide_for_geo_location                = array();

			// Structured individual customer.
			$hide_for_individual_user['priority']  = $individual_user_priority;
			$hide_for_individual_user['is_enable'] = $individual_user_is_enable;
			if ( is_array( $individual_user_customers ) ) {
				$i = 0;
				foreach ( $individual_user_customers as $key => $value ) {
					$hide_for_individual_user['rules'][ $i ]['customers']  = $individual_user_customers[ $key ] ? array_map( 'intval', $individual_user_customers[ $key ] ) : null;
					$hide_for_individual_user['rules'][ $i ]['is_shown']   = $individual_user_is_shown[ $key ] ? $individual_user_is_shown[ $key ] : null;
					$hide_for_individual_user['rules'][ $i ]['categories'] = $individual_user_categories[ $key ] ? array_map( 'intval', $individual_user_categories[ $key ] ) : null;
					$hide_for_individual_user['rules'][ $i ]['products']   = $individual_user_products[ $key ] ? array_map( 'intval', $individual_user_products[ $key ] ) : null;

					$i++;
				}
			}
			// Saving individual customers.
			update_option( CWCCV_PLUGIN_PREFIX . '_hide_for_individual_customer', $hide_for_individual_user );

			// Structured user roles.
			$hide_for_user_roles['is_enable'] = $user_roles_is_enable;
			$hide_for_user_roles['priority']  = $user_roles_priority;
			if ( is_array( $user_roles ) ) {
				$i = 0;
				foreach ( $user_roles as $key => $value ) {
					$hide_for_user_roles['rules'][ $i ]['user_roles'] = $user_roles[ $key ] ? $user_roles[ $key ] : null;
					$hide_for_user_roles['rules'][ $i ]['is_shown']   = $user_roles_is_shown[ $key ] ? $user_roles_is_shown[ $key ] : null;
					$hide_for_user_roles['rules'][ $i ]['categories'] = $user_roles_categories[ $key ] ? array_map( 'intval', $user_roles_categories[ $key ] ) : null;
					$hide_for_user_roles['rules'][ $i ]['products']   = $user_roles_products[ $key ] ? array_map( 'intval', $user_roles_products[ $key ] ) : null;
					$i++;
				}
			}
			// Saving user roles.
			update_option( CWCCV_PLUGIN_PREFIX . '_hide_for_user_roles', $hide_for_user_roles );

			// Structured user groups.
			$hide_for_user_groups['is_enable'] = $user_groups_is_enable;
			$hide_for_user_groups['priority']  = $user_groups_priority;
			if ( is_array( $user_groups ) ) {
				$i = 0;
				foreach ( $user_groups as $key => $value ) {
					$hide_for_user_groups['rules'][ $i ]['user_groups'] = $user_groups[ $key ] ? $user_groups[ $key ] : null;
					$hide_for_user_groups['rules'][ $i ]['is_shown']    = $user_groups_is_shown[ $key ] ? $user_groups_is_shown[ $key ] : null;
					$hide_for_user_groups['rules'][ $i ]['categories']  = $user_groups_categories[ $key ] ? array_map( 'intval', $user_groups_categories[ $key ] ) : null;
					$hide_for_user_groups['rules'][ $i ]['products']    = $user_groups_products[ $key ] ? array_map( 'intval', $user_groups_products[ $key ] ) : null;
					$i++;
				}
			}
			// Saving user groups.
			update_option( CWCCV_PLUGIN_PREFIX . '_hide_for_user_groups', $hide_for_user_groups );

			// Structured price tier.
			$hide_for_price_tier['is_enable'] = $price_tier_is_enable;
			$hide_for_price_tier['priority']  = $price_tier_priority;
			if ( is_array( $price_tier_from_price ) && ! empty( $price_tier_from_price[0] ) ) {
				$i = 0;
				foreach ( $price_tier_from_price as $key => $value ) {
					$hide_for_price_tier['rules'][ $i ]['price']['from'] = $price_tier_from_price[ $key ] ? intval( $price_tier_from_price[ $key ] ) : null;
					$hide_for_price_tier['rules'][ $i ]['price']['to']   = $price_tier_to_price[ $key ] ? intval( $price_tier_to_price[ $key ] ) : null;
					$hide_for_price_tier['rules'][ $i ]['is_shown']      = $price_tier_is_shown[ $key ] ? $price_tier_is_shown[ $key ] : null;
					$hide_for_price_tier['rules'][ $i ]['categories']    = $price_tier_categories[ $key ] ? array_map( 'intval', $price_tier_categories[ $key ] ) : null;
					$hide_for_price_tier['rules'][ $i ]['products']      = $price_tier_products[ $key ] ? array_map( 'intval', $price_tier_products[ $key ] ) : null;
					$i++;
				}
			}
			// Saving price tier.
			update_option( CWCCV_PLUGIN_PREFIX . '_hide_for_price_tier', $hide_for_price_tier );

			// Structured price tier.
			$hide_for_geo_location['is_enable'] = $geo_location_is_enable;
			$hide_for_geo_location['priority']  = $geo_location_priority;
			if ( is_array( $geo_location ) ) {
				$i = 0;
				foreach ( $geo_location as $key => $value ) {
					$hide_for_geo_location['rules'][ $key ]['location']   = $geo_location[ $key ] ? $geo_location[ $key ] : null;
					$hide_for_geo_location['rules'][ $key ]['is_shown']   = $geo_location_is_shown[ $key ] ? $geo_location_is_shown[ $key ] : null;
					$hide_for_geo_location['rules'][ $key ]['categories'] = $geo_location_categories[ $key ] ? array_map( 'intval', $geo_location_categories[ $key ] ) : null;
					$hide_for_geo_location['rules'][ $key ]['products']   = $geo_location_products[ $key ] ? array_map( 'intval', $geo_location_products[ $key ] ) : null;
					$i++;
				}
			}
			// Saving price tier.
			update_option( CWCCV_PLUGIN_PREFIX . '_hide_for_geo_location', $hide_for_geo_location );

			// HIDE PRICE FOR NON-LOGIN USERS.
			// Update categories and products for hide catalog option.
			$products   = filter_input( INPUT_POST, CWCCV_PLUGIN_PREFIX . '_hide_catalog_product_select', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			$categories = filter_input( INPUT_POST, CWCCV_PLUGIN_PREFIX . '_hide_catalog_category_select', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );

			$_hide_price_for_non_login_toggle = filter_input( INPUT_POST, CWCCV_PLUGIN_PREFIX . '_hide_price_for_non_login_toggle', FILTER_DEFAULT, FILTER_SANITIZE_STRING );
			$_hide_whole_catalog_price_toggle = filter_input( INPUT_POST, CWCCV_PLUGIN_PREFIX . '_hide_whole_catalog_price_toggle', FILTER_DEFAULT, FILTER_SANITIZE_STRING );

			$hide_catalog_price_for_non_login_option = $_hide_price_for_non_login_toggle ? 'yes' : 'no';
			$hide_whole_catalog_price_option         = $_hide_whole_catalog_price_toggle ? 'yes' : 'no';

			update_option( CWCCV_PLUGIN_PREFIX . '_hide_price_for_non_login', $hide_catalog_price_for_non_login_option );
			update_option( CWCCV_PLUGIN_PREFIX . '_hide_whole_catalog_price', $hide_whole_catalog_price_option );

			update_option( CWCCV_PLUGIN_PREFIX . '_hide_catalog_for_products', $products );
			update_option( CWCCV_PLUGIN_PREFIX . '_hide_catalog_for_categories', $categories );

			$products   = filter_input( INPUT_POST, CWCCV_PLUGIN_PREFIX . '_hide_catalog_product_select_by_product', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			$categories = filter_input( INPUT_POST, CWCCV_PLUGIN_PREFIX . '_hide_catalog_category_select_by_product', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );

			$_hide_product_for_non_login_toggle = filter_input( INPUT_POST, CWCCV_PLUGIN_PREFIX . '_hide_product_for_non_login_toggle', FILTER_DEFAULT, FILTER_SANITIZE_STRING );
			$_hide_whole_catalog_product_toggle = filter_input( INPUT_POST, CWCCV_PLUGIN_PREFIX . '_hide_whole_catalog_product_toggle', FILTER_DEFAULT, FILTER_SANITIZE_STRING );

			$hide_catalog_product_for_non_login_option = $_hide_product_for_non_login_toggle ? 'yes' : 'no';
			$hide_whole_catalog_product_option         = $_hide_whole_catalog_product_toggle ? 'yes' : 'no';

			update_option( CWCCV_PLUGIN_PREFIX . '_hide_product_for_non_login', $hide_catalog_product_for_non_login_option );
			update_option( CWCCV_PLUGIN_PREFIX . '_hide_whole_catalog_product', $hide_whole_catalog_product_option );

			update_option( CWCCV_PLUGIN_PREFIX . '_hide_catalog_for_products_by_product', $products );
			update_option( CWCCV_PLUGIN_PREFIX . '_hide_catalog_for_categories_by_product', $categories );

		}

		/**
		 * Adds visibility settings template to custom tab section.
		 *
		 * @param array $field_args arguments for visibility template.
		 * @since 1.1.1.0
		 */
		public function get_visibility_template( $field_args ) {
			$field_args['fields'] = $this->get_fields_args();

			require_once CWCCV_ABSPATH . '/templates/cwccv-custom-visibility-settings.php';
		}

		/**
		 * Adds user roles settings template to custom tab section.
		 *
		 * @since 1.1.1.0
		 */
		public function get_user_roles_template() {

			$user_roles           = get_option( CWCCV_PLUGIN_PREFIX . '_user_roles' );
			$field_args['fields'] = $this->get_fields_args();
			require_once CWCCV_ABSPATH . '/templates/cwccv-custom-role-settings.php';
		}

		/**
		 * Adds user groups settings template to custom tab section.
		 *
		 * @since 1.1.1.0
		 */
		public function get_user_groups_template() {

			$user_groups          = get_option( CWCCV_PLUGIN_PREFIX . '_user_groups' );
			$field_args['fields'] = $this->get_fields_args();
			require_once CWCCV_ABSPATH . '/templates/cwccv-custom-group-settings.php';
		}

		/**
		 * Add settings to custom sections.
		 *
		 * @since 1.1.1.0
		 */
		public function get_settings_template() {
			global $current_section;
			switch ( $current_section ) {
				case 'roles':
					$settings = array(
						'cwccv_roles_template' => array(
							'type' => 'user_roles_template',
							'id'   => CWCCV_PLUGIN_PREFIX . '_title',
						),
						'cwccv_section_end'    => array(
							'type' => 'sectionend',
							'id'   => CWCCV_PLUGIN_PREFIX . '_title',
						),
					);
					break;
				case 'groups':
					$settings = array(
						'cwccv_groups_template' => array(
							'type' => 'user_groups_template',
							'id'   => CWCCV_PLUGIN_PREFIX . '_title',
						),
						'cwccv_section_end'     => array(
							'type' => 'sectionend',
							'id'   => CWCCV_PLUGIN_PREFIX . '_title',
						),
					);
					break;
				default:
					$settings = array(
						'cwccv_visibility_template' => array(
							'type' => 'visibility_template',
							'id'   => CWCCV_PLUGIN_PREFIX . '_title',
						),
						'cwccv_section_end'         => array(
							'type' => 'sectionend',
							'id'   => CWCCV_PLUGIN_PREFIX . '_title',
						),
					);
					break;
			}

			return apply_filters( 'wc_settings_tab_' . self::$settings_tab, $settings, $current_section );
		}

		/**
		 * Gets custom fields args for admin fields
		 *
		 * @since 1.1.1.0
		 * @return array $field_args
		 */
		public function get_fields_args() {
			global $current_section;

			switch ( $current_section ) {
				case 'roles':
					$field_args = array(
						'add_new_role_text_field' => array(
							'name'        => CWCCV_PLUGIN_PREFIX . '_add_new_role_text_field',
							'class'       => CWCCV_PLUGIN_PREFIX . '_add_new_role_text_field',
							'placeholder' => __( 'Enter Role Name', 'codup-woocommerce-catalog-visibility' ),
							'is_required' => true,
						),
						'edit_role_fields'        => array(
							'name'  => CWCCV_PLUGIN_PREFIX . '_edit_role_text_fields',
							'class' => CWCCV_PLUGIN_PREFIX . '_edit_role_text_fields',
						),
						'edit_role_button'        => array(
							'name'  => CWCCV_PLUGIN_PREFIX . '_edit_role_button',
							'class' => CWCCV_PLUGIN_PREFIX . '_edit_role_button',
						),
						'delete_role_button'      => array(
							'name'  => CWCCV_PLUGIN_PREFIX . '_delete_role_button',
							'class' => CWCCV_PLUGIN_PREFIX . '_delete_role_button',
						),
					);
					break;

				case 'groups':
					$field_args = array(
						'add_new_group_text_field' => array(
							'name'        => CWCCV_PLUGIN_PREFIX . '_add_new_group_text_field',
							'class'       => CWCCV_PLUGIN_PREFIX . '_add_new_group_text_field',
							'placeholder' => __( 'Enter Group Name', 'codup-woocommerce-catalog-visibility' ),
							'is_required' => true,
						),
						'edit_group_fields'        => array(
							'name'  => CWCCV_PLUGIN_PREFIX . '_edit_group_text_fields',
							'class' => CWCCV_PLUGIN_PREFIX . '_edit_group_text_fields',
						),
						'edit_group_button'        => array(
							'name'  => CWCCV_PLUGIN_PREFIX . '_edit_group_button',
							'class' => CWCCV_PLUGIN_PREFIX . '_edit_group_button',
						),
						'delete_group_button'      => array(
							'name'  => CWCCV_PLUGIN_PREFIX . '_delete_group_button',
							'class' => CWCCV_PLUGIN_PREFIX . '_delete_group_button',
						),
					);
					break;

				default:
					$field_args = array(
						// Args for hide catalog option.
						'hide_whole_catalog_price_toggle'  => array(
							'name'       => CWCCV_PLUGIN_PREFIX . '_hide_whole_catalog_price_toggle',
							'id'         => CWCCV_PLUGIN_PREFIX . '_hide_whole_catalog_price_toggle',
							'class'      => CWCCV_PLUGIN_PREFIX . '_hide_whole_catalog_price_toggle',
							'is_checked' => get_option( CWCCV_PLUGIN_PREFIX . '_hide_whole_catalog_price' ),
						),
						'hide_price_for_non_login_toggle'  => array(
							'name'       => CWCCV_PLUGIN_PREFIX . '_hide_price_for_non_login_toggle',
							'id'         => CWCCV_PLUGIN_PREFIX . '_hide_price_for_non_login_toggle',
							'class'      => CWCCV_PLUGIN_PREFIX . '_hide_price_for_non_login_toggle',
							'is_checked' => get_option( CWCCV_PLUGIN_PREFIX . '_hide_price_for_non_login' ),
						),
						'hide_catalog_category_select'     => array(
							'name'                  => CWCCV_PLUGIN_PREFIX . '_hide_catalog_category_select',
							'class'                 => CWCCV_PLUGIN_PREFIX . '_hide_catalog_category_select',
							'option'                => CWCCV_Helper::get_categories(),
							'selected_option_value' => get_option( CWCCV_PLUGIN_PREFIX . '_hide_catalog_for_categories' ),
							'is_disabled'           => ( 'yes' == get_option( CWCCV_PLUGIN_PREFIX . '_hide_whole_catalog_price' ) ) ? 'disabled="disabled"' : '',
							'is_required'           => true,
						),
						'hide_catalog_product_select'      => array(
							'name'                  => CWCCV_PLUGIN_PREFIX . '_hide_catalog_product_select',
							'class'                 => CWCCV_PLUGIN_PREFIX . '_hide_catalog_product_select',
							'option'                => CWCCV_Helper::get_products(),
							'selected_option_value' => get_option( CWCCV_PLUGIN_PREFIX . '_hide_catalog_for_products' ),
							'is_disabled'           => ( 'yes' == get_option( CWCCV_PLUGIN_PREFIX . '_hide_whole_catalog_price' ) ) ? 'disabled="disabled"' : '',
							'is_required'           => true,
						),

						'hide_whole_catalog_product_toggle' => array(
							'name'       => CWCCV_PLUGIN_PREFIX . '_hide_whole_catalog_product_toggle',
							'id'         => CWCCV_PLUGIN_PREFIX . '_hide_whole_catalog_product_toggle',
							'class'      => CWCCV_PLUGIN_PREFIX . '_hide_whole_catalog_product_toggle',
							'is_checked' => get_option( CWCCV_PLUGIN_PREFIX . '_hide_whole_catalog_product' ),
						),
						'hide_product_for_non_login_toggle' => array(
							'name'       => CWCCV_PLUGIN_PREFIX . '_hide_product_for_non_login_toggle',
							'id'         => CWCCV_PLUGIN_PREFIX . '_hide_product_for_non_login_toggle',
							'class'      => CWCCV_PLUGIN_PREFIX . '_hide_product_for_non_login_toggle',
							'is_checked' => get_option( CWCCV_PLUGIN_PREFIX . '_hide_product_for_non_login' ),
						),
						'hide_catalog_category_select_by_product' => array(
							'name'                  => CWCCV_PLUGIN_PREFIX . '_hide_catalog_category_select_by_product',
							'class'                 => CWCCV_PLUGIN_PREFIX . '_hide_catalog_category_select_by_product',
							'option'                => CWCCV_Helper::get_categories(),
							'selected_option_value' => get_option( CWCCV_PLUGIN_PREFIX . '_hide_catalog_for_categories_by_product' ),
							'is_disabled'           => ( 'yes' == get_option( CWCCV_PLUGIN_PREFIX . '_hide_whole_catalog_product' ) ) ? 'disabled="disabled"' : '',
							'is_required'           => true,
						),
						'hide_catalog_product_select_by_product' => array(
							'name'                  => CWCCV_PLUGIN_PREFIX . '_hide_catalog_product_select_by_product',
							'class'                 => CWCCV_PLUGIN_PREFIX . '_hide_catalog_product_select_by_product',
							'option'                => CWCCV_Helper::get_products(),
							'selected_option_value' => get_option( CWCCV_PLUGIN_PREFIX . '_hide_catalog_for_products_by_product' ),
							'is_disabled'           => ( 'yes' == get_option( CWCCV_PLUGIN_PREFIX . '_hide_whole_catalog_product' ) ) ? 'disabled="disabled"' : '',
							'is_required'           => true,
						),
						// End of args for hide catalog option.

						// Args for individual customer settings.
						'individual_customer_settings_enable_toggle' => array(
							'name'       => CWCCV_PLUGIN_PREFIX . '_individual_customer_settings_enable_toggle',
							'class'      => CWCCV_PLUGIN_PREFIX . '_individual_customer_settings_enable_toggle',
							'is_checked' => false,
							'title'      => __( 'Turning on the toggle will let you create visibility rule by selecting individual customers and defining which products/categories to show/hide to them.', 'codup-woocommerce-catalog-visibility' ),
						),
						'individual_customer_product_show_radio' => array(
							'name'       => CWCCV_PLUGIN_PREFIX . '_individual_customer_products_show_hide_radio',
							'id'         => CWCCV_PLUGIN_PREFIX . '_individual_customer_show_products_radio',
							'class'      => CWCCV_PLUGIN_PREFIX . '_individual_customer_products_show_hide_radio',
							'is_checked' => false,
							'value'      => 'yes',
						),
						'individual_customer_product_hide_radio' => array(
							'name'       => CWCCV_PLUGIN_PREFIX . '_individual_customer_products_show_hide_radio',
							'id'         => CWCCV_PLUGIN_PREFIX . '_individual_customer_hide_products_radio',
							'class'      => CWCCV_PLUGIN_PREFIX . '_individual_customer_products_show_hide_radio',
							'is_checked' => false,
							'value'      => 'no',
						),
						'individual_customer_priority_select' => array(
							'name'  => CWCCV_PLUGIN_PREFIX . '_individual_customer_priority_select',
							'class' => CWCCV_PLUGIN_PREFIX . '_individual_customer_priority_select',
							'title' => __( 'Select Priority from 1-5. In case of conflicting conditions, the rule with higher priority will be executed.', 'codup-woocommerce-catalog-visibility' ),
						),
						'individual_customer_customer_name_select' => array(
							'name'                  => CWCCV_PLUGIN_PREFIX . '_individual_customer_customer_name_select',
							'class'                 => CWCCV_PLUGIN_PREFIX . '_individual_customer_customer_name_select',
							'option'                => CWCCV_Helper::get_users(),
							'placeholder'           => __( 'Search Customers', 'codup-woocommerce-catalog-visibility' ),
							'selected_option_value' => array(),
						),
						'individual_customer_category_select' => array(
							'name'                  => CWCCV_PLUGIN_PREFIX . '_individual_customer_category_select',
							'class'                 => CWCCV_PLUGIN_PREFIX . '_individual_customer_category_select',
							'option'                => CWCCV_Helper::get_categories(),
							'placeholder'           => __( 'Search Categories', 'codup-woocommerce-catalog-visibility' ),
							'selected_option_value' => array(),
						),
						'individual_customer_product_select' => array(
							'name'                  => CWCCV_PLUGIN_PREFIX . '_individual_customer_product_select',
							'class'                 => CWCCV_PLUGIN_PREFIX . '_individual_customer_product_select',
							'option'                => CWCCV_Helper::get_products(),
							'placeholder'           => __( 'Search Products', 'codup-woocommerce-catalog-visibility' ),
							'selected_option_value' => array(),
						),
						'individual_customer_repeater_field_button' => array(
							'name'  => CWCCV_PLUGIN_PREFIX . '_individual_customer_repeater_field_button',
							'class' => CWCCV_PLUGIN_PREFIX . '_individual_customer_repeater_field_button',
						),
						// End Args for individual customer settings.

						// Args for user roles settings.
						'user_roles_settings_enable_toggle' => array(
							'name'       => CWCCV_PLUGIN_PREFIX . '_user_roles_settings_enable_toggle',
							'class'      => CWCCV_PLUGIN_PREFIX . '_user_roles_settings_enable_toggle',
							'is_checked' => false,
						),
						'user_roles_priority_select'       => array(
							'name'  => CWCCV_PLUGIN_PREFIX . '_user_roles_priority_select',
							'class' => CWCCV_PLUGIN_PREFIX . '_user_roles_priority_select',
							'title' => __( 'Select Priority from 1-5. In case of conflicting conditions, the rule with higher priority will be executed.', 'codup-woocommerce-catalog-visibility' ),
						),
						'user_roles_roles_name_select'     => array(
							'name'                  => CWCCV_PLUGIN_PREFIX . '_user_roles_roles_name_select',
							'class'                 => CWCCV_PLUGIN_PREFIX . '_user_roles_roles_name_select',
							'option'                => CWCCV_Helper::get_user_roles(),
							'selected_option_value' => array(),
						),
						'user_roles_product_show_radio'    => array(
							'name'       => CWCCV_PLUGIN_PREFIX . '_user_roles_show_hide_radio',
							'id'         => CWCCV_PLUGIN_PREFIX . '_user_roles_show_radio',
							'class'      => CWCCV_PLUGIN_PREFIX . '_user_roles_show_hide_radio',
							'is_checked' => false,
							'value'      => 'yes',
						),
						'user_roles_product_hide_radio'    => array(
							'name'       => CWCCV_PLUGIN_PREFIX . '_user_roles_show_hide_radio',
							'id'         => CWCCV_PLUGIN_PREFIX . '_user_roles_hide_radio',
							'class'      => CWCCV_PLUGIN_PREFIX . '_user_roles_show_hide_radio',
							'is_checked' => false,
							'value'      => 'no',
						),
						'user_roles_category_select'       => array(
							'name'                  => CWCCV_PLUGIN_PREFIX . '_user_roles_category_select',
							'class'                 => CWCCV_PLUGIN_PREFIX . '_user_roles_category_select',
							'option'                => CWCCV_Helper::get_categories(),
							'selected_option_value' => array(),
						),
						'user_roles_product_select'        => array(
							'name'                  => CWCCV_PLUGIN_PREFIX . '_user_roles_product_select',
							'class'                 => CWCCV_PLUGIN_PREFIX . '_user_roles_product_select',
							'option'                => CWCCV_Helper::get_products(),
							'selected_option_value' => array(),
						),
						'user_roles_repeater_field_button' => array(
							'name'  => CWCCV_PLUGIN_PREFIX . '_user_roles_repeater_field_button',
							'class' => CWCCV_PLUGIN_PREFIX . '_user_roles_repeater_field_button',
						),
						// End user roles args.

						// Args for user group settings.
						'user_groups_settings_enable_toggle' => array(
							'name'       => CWCCV_PLUGIN_PREFIX . '_user_groups_settings_enable_toggle',
							'class'      => CWCCV_PLUGIN_PREFIX . '_user_groups_settings_enable_toggle',
							'is_checked' => false,
						),
						'user_groups_priority_select'      => array(
							'name'  => CWCCV_PLUGIN_PREFIX . '_user_groups_priority_select',
							'class' => CWCCV_PLUGIN_PREFIX . '_user_groups_priority_select',
							'title' => __( 'Select Priority from 1-5. In case of conflicting conditions, the rule with higher priority will be executed.', 'codup-woocommerce-catalog-visibility' ),
						),
						'user_groups_groups_name_select'   => array(
							'name'                  => CWCCV_PLUGIN_PREFIX . '_user_groups_groups_name_select',
							'class'                 => CWCCV_PLUGIN_PREFIX . '_user_groups_groups_name_select',
							'option'                => CWCCV_Helper::get_user_groups(),
							'selected_option_value' => array(),
						),
						'user_groups_product_show_radio'   => array(
							'name'       => CWCCV_PLUGIN_PREFIX . '_user_groups_show_hide_radio',
							'id'         => CWCCV_PLUGIN_PREFIX . '_user_groups_show_radio',
							'class'      => CWCCV_PLUGIN_PREFIX . '_user_groups_show_hide_radio',
							'is_checked' => false,
							'value'      => 'yes',
						),
						'user_groups_product_hide_radio'   => array(
							'name'       => CWCCV_PLUGIN_PREFIX . '_user_groups_show_hide_radio',
							'id'         => CWCCV_PLUGIN_PREFIX . '_user_groups_hide_radio',
							'class'      => CWCCV_PLUGIN_PREFIX . '_user_groups_show_hide_radio',
							'is_checked' => false,
							'value'      => 'no',
						),
						'user_groups_category_select'      => array(
							'name'                  => CWCCV_PLUGIN_PREFIX . '_user_groups_category_select',
							'class'                 => CWCCV_PLUGIN_PREFIX . '_user_groups_category_select',
							'option'                => CWCCV_Helper::get_categories(),
							'selected_option_value' => array(),
						),
						'user_groups_product_select'       => array(
							'name'                  => CWCCV_PLUGIN_PREFIX . '_user_groups_product_select',
							'class'                 => CWCCV_PLUGIN_PREFIX . '_user_groups_product_select',
							'option'                => CWCCV_Helper::get_products(),
							'selected_option_value' => array(),
						),
						'user_groups_repeater_field_button' => array(
							'name'  => CWCCV_PLUGIN_PREFIX . '_user_groups_repeater_field_button',
							'class' => CWCCV_PLUGIN_PREFIX . '_user_groups_repeater_field_button',
						),
						// End user groups settings.

						// Args for price tier settings.
						'price_tier_settings_enable_toggle' => array(
							'name'       => CWCCV_PLUGIN_PREFIX . '_price_tier_settings_enable_toggle',
							'class'      => CWCCV_PLUGIN_PREFIX . '_price_tier_settings_enable_toggle',
							'is_checked' => false,
						),
						'price_tier_priority_select'       => array(
							'name'  => CWCCV_PLUGIN_PREFIX . '_price_tier_priority_select',
							'class' => CWCCV_PLUGIN_PREFIX . '_price_tier_priority_select',
							'title' => __( 'Select Priority from 1-5. In case of conflicting conditions, the rule with higher priority will be executed.', 'codup-woocommerce-catalog-visibility' ),
						),
						'price_tier_from_text_field'       => array(
							'name'  => CWCCV_PLUGIN_PREFIX . '_price_tier_from_text_field',
							'class' => CWCCV_PLUGIN_PREFIX . '_price_tier_from_text_field',
							'value' => '',
						),
						'price_tier_to_text_field'         => array(
							'name'  => CWCCV_PLUGIN_PREFIX . '_price_tier_to_text_field',
							'class' => CWCCV_PLUGIN_PREFIX . '_price_tier_to_text_field',
							'value' => '',
						),
						'price_tier_product_show_radio'    => array(
							'name'       => CWCCV_PLUGIN_PREFIX . '_price_tier_show_hide_radio',
							'id'         => CWCCV_PLUGIN_PREFIX . '_price_tier_show_radio',
							'class'      => CWCCV_PLUGIN_PREFIX . '_price_tier_show_hide_radio',
							'is_checked' => false,
							'value'      => 'yes',
						),
						'price_tier_product_hide_radio'    => array(
							'name'       => CWCCV_PLUGIN_PREFIX . '_price_tier_show_hide_radio',
							'id'         => CWCCV_PLUGIN_PREFIX . '_price_tier_hide_radio',
							'class'      => CWCCV_PLUGIN_PREFIX . '_price_tier_show_hide_radio',
							'is_checked' => false,
							'value'      => 'no',
						),
						'price_tier_category_select'       => array(
							'name'                  => CWCCV_PLUGIN_PREFIX . '_price_tier_category_select',
							'class'                 => CWCCV_PLUGIN_PREFIX . '_price_tier_category_select',
							'option'                => CWCCV_Helper::get_categories(),
							'selected_option_value' => array(),
						),
						'price_tier_product_select'        => array(
							'name'                  => CWCCV_PLUGIN_PREFIX . '_price_tier_product_select',
							'class'                 => CWCCV_PLUGIN_PREFIX . '_price_tier_product_select',
							'option'                => CWCCV_Helper::get_products(),
							'selected_option_value' => array(),
						),
						'price_tier_repeater_field_button' => array(
							'name'  => CWCCV_PLUGIN_PREFIX . '_price_tier_repeater_field_button',
							'class' => CWCCV_PLUGIN_PREFIX . '_price_tier_repeater_field_button',
						),
						// End price tier settings.

						// Args for geo location settings.
						'geo_location_settings_enable_toggle' => array(
							'name'       => CWCCV_PLUGIN_PREFIX . '_geo_location_settings_enable_toggle',
							'class'      => CWCCV_PLUGIN_PREFIX . '_geo_location_settings_enable_toggle',
							'is_checked' => false,
						),
						'geo_location_priority_select'     => array(
							'name'  => CWCCV_PLUGIN_PREFIX . '_geo_location_priority_select',
							'class' => CWCCV_PLUGIN_PREFIX . '_geo_location_priority_select',
							'title' => __( 'Select Priority from 1-5. In case of conflicting conditions, the rule with higher priority will be executed.', 'codup-woocommerce-catalog-visibility' ),
						),
						'geo_location_location_name_select' => array(
							'name'                  => CWCCV_PLUGIN_PREFIX . '_geo_location_location_name_select',
							'id'                    => CWCCV_PLUGIN_PREFIX . '_geo_location_location_name_select',
							'class'                 => CWCCV_PLUGIN_PREFIX . '_geo_location_location_name_select',
							'option'                => CWCCV_Helper::get_country_list(),
							'selected_option_value' => array(),
						),
						'geo_location_product_show_radio'  => array(
							'name'       => CWCCV_PLUGIN_PREFIX . '_geo_location_show_hide_radio',
							'id'         => CWCCV_PLUGIN_PREFIX . '_geo_location_show_radio',
							'class'      => CWCCV_PLUGIN_PREFIX . '_geo_location_show_hide_radio',
							'is_checked' => false,
							'value'      => 'yes',
						),
						'geo_location_product_hide_radio'  => array(
							'name'       => CWCCV_PLUGIN_PREFIX . '_geo_location_show_hide_radio',
							'id'         => CWCCV_PLUGIN_PREFIX . '_geo_location_hide_radio',
							'class'      => CWCCV_PLUGIN_PREFIX . '_geo_location_show_hide_radio',
							'is_checked' => false,
							'value'      => 'no',
						),
						'geo_location_category_select'     => array(
							'name'                  => CWCCV_PLUGIN_PREFIX . '_geo_location_category_select',
							'class'                 => CWCCV_PLUGIN_PREFIX . '_geo_location_category_select',
							'option'                => CWCCV_Helper::get_categories(),
							'selected_option_value' => array(),
						),
						'geo_location_product_select'      => array(
							'name'                  => CWCCV_PLUGIN_PREFIX . '_geo_location_product_select',
							'class'                 => CWCCV_PLUGIN_PREFIX . '_geo_location_product_select',
							'option'                => CWCCV_Helper::get_products(),
							'selected_option_value' => array(),
						),
						'geo_location_repeater_field_button' => array(
							'name'  => CWCCV_PLUGIN_PREFIX . '_geo_location_repeater_field_button',
							'class' => CWCCV_PLUGIN_PREFIX . '_geo_location_repeater_field_button',
						),
						// End geo location settings.

					);
					break;
			}
			return $field_args;
		}

	}
	new CWCCV_Settings_Tab();
}
