<?php
/**
 * File class-cwccv-user-settings.php
 *
 * @package catalog-visibility-for-woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'CWCCV_User_Settings' ) ) {
	/**
	 * Class CWCCV_User_Settings.
	 * All the functions for adding and saving settings on user profile settings are defined here.
	 *
	 * @since 1.1.1.0
	 */
	class CWCCV_User_Settings {

		/**
		 * Constructor.
		 */
		public function __construct() {
			// Add action for show settings on user profile settings.
			add_action( 'edit_user_profile', array( $this, 'show_association_on_user_settings' ) );
			add_action( 'show_user_profile', array( $this, 'show_association_on_user_settings' ) );

			// Add action for save and update settings.
			add_action( 'personal_options_update', array( $this, 'save_user_settings' ) );
			add_action( 'edit_user_profile_update', array( $this, 'save_user_settings' ) );
		}

		/**
		 * Shows association of catalog visibility roles and groups on user profile pages.
		 *
		 * @since 1.1.1.0
		 */
		public function show_association_on_user_settings() {
			$field_args = $this->get_field_args();
			require_once CWCCV_ABSPATH . '/templates/cwccv-admin-settings-user-settings.php';
		}

		/**
		 * Save settings of custom fields on user profile pages.
		 *
		 * @param string $user_id Represents current user id.
		 * @since 1.1.1.0
		 */
		public function save_user_settings( $user_id ) {
			// Sanitize values.
			$roles  = filter_input( INPUT_POST, CWCCV_PLUGIN_PREFIX . '_user_roles_select', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			$groups = filter_input( INPUT_POST, CWCCV_PLUGIN_PREFIX . '_user_groups_select', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );

			// Update values for user roles and user groups.
			if ( false !== $roles ) {
				update_user_meta( $user_id, CWCCV_PLUGIN_PREFIX . '_user_roles_select', $roles );
			}
			if ( false !== $groups ) {
				update_user_meta( $user_id, CWCCV_PLUGIN_PREFIX . '_user_groups_select', $groups );
			}
		}

		/**
		 * Returns field arguments for user profile setting custom fields.
		 *
		 * @since 1.1.1.0
		 */
		public function get_field_args() {
			global $user_id;
			$field_args = array(
				'user_groups_select' => array(
					'name'                  => CWCCV_PLUGIN_PREFIX . '_user_groups_select',
					'class'                 => CWCCV_PLUGIN_PREFIX . '_user_groups_select',
					'option'                => get_option( CWCCV_PLUGIN_PREFIX . '_user_groups' ),
					'selected_option_value' => get_user_meta( $user_id, CWCCV_PLUGIN_PREFIX . '_user_groups_select', true ),
				),
			);
			return $field_args;
		}
	}
	new CWCCV_User_Settings();
}
