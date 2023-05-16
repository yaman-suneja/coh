<?php
/**
 * File class-cwccv-admin-settings.php
 *
 * @package catalog-visibility-for-woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class CWCCV_Admin_Settings.
 * All the ajax functions for admnin settings are defined here.
 *
 * @since 1.1.1.0
 */
if ( ! class_exists( 'CWCCV_Admin_Settings' ) ) {
	/**
	 * Class CWCCV_Admin_Settings.
	 */
	class CWCCV_Admin_Settings {
		/**
		 * Constructor.
		 */
		public function __construct() {
			// Ajax actions for edit user role.
			add_action( 'wp_ajax_update_user_role', array( $this, 'update_user_role' ) );
			add_action( 'wp_ajax_nopriv_update_user_role', array( $this, 'update_user_role' ) );
			// Ajax actions for delete user role.
			add_action( 'wp_ajax_delete_user_role', array( $this, 'delete_user_role' ) );
			add_action( 'wp_ajax_nopriv_delete_user_role', array( $this, 'delete_user_role' ) );

			// Ajax actions for edit user group.
			add_action( 'wp_ajax_update_user_group', array( $this, 'update_user_group' ) );
			add_action( 'wp_ajax_nopriv_update_user_group', array( $this, 'update_user_group' ) );
			// Ajax actions for delete user group.
			add_action( 'wp_ajax_delete_user_group', array( $this, 'delete_user_group' ) );
			add_action( 'wp_ajax_nopriv_delete_user_group', array( $this, 'delete_user_group' ) );

		}

		/**
		 * Updates the user role
		 *
		 * @since 1.1.1.0
		 */
		public function update_user_role() {

			$old_role_name = filter_input( INPUT_POST, 'old_role_name', FILTER_DEFAULT, FILTER_SANITIZE_STRING );
			$new_role_name = filter_input( INPUT_POST, 'role_name', FILTER_DEFAULT, FILTER_SANITIZE_STRING );

			$roles = get_option( CWCCV_PLUGIN_PREFIX . '_user_roles' );

			if ( '' == $new_role_name ) {
				$response = array(
					'status'  => 'fail',
					'message' => '<div id="cwccv_message" class="error inline"><p><strong>' . __( 'Role cannot be an empty value.', 'codup-woocommerce-catalog-visibility' ) . '</strong></p></div>',
				);
				echo json_encode( $response );
			} elseif ( array_search( strtolower( $new_role_name ), array_map( 'strtolower', $roles ), true ) && strtolower( trim( $new_role_name ) ) != strtolower( trim( $old_role_name ) ) ) {
				$response = array(
					'status'  => 'fail',
					'message' => '<div id="cwccv_message" class="error inline"><p><strong>' . __( 'Role already exists.', 'codup-woocommerce-catalog-visibility' ) . '</strong></p></div>',
				);
				echo json_encode( $response );
			} else {
				$array_key           = array_search( $old_role_name, $roles );
				$roles[ $array_key ] = $new_role_name;
				update_option( CWCCV_PLUGIN_PREFIX . '_user_roles', $roles );

				// add role in user list.
				$custom_user_new_role = str_replace( ' ', '_', trim( strtolower( $new_role_name ) ) );
				$custom_user_old_role = str_replace( ' ', '_', trim( strtolower( $old_role_name ) ) );
				if ( ! wp_roles()->is_role( $custom_user_role ) && strtolower( trim( $new_role_name ) ) != strtolower( trim( $old_role_name ) ) ) {
					if ( wp_roles()->is_role( $custom_user_old_role ) ) {
						remove_role( $custom_user_old_role );
					}
					add_role( $custom_user_new_role, $new_role_name, array( 'read' => true ) );
				}

				$response = array(
					'status'  => 'success',
					'message' => '<div id="cwccv_message" class="updated inline"><p><strong>' . __( 'Role has been updated.', 'codup-woocommerce-catalog-visibility' ) . '</strong></p></div>',
				);
				echo json_encode( $response );
			}
			wp_die();

		}

		/**
		 * Deletes the user role
		 *
		 * @since 1.1.1.0
		 */
		public function delete_user_role() {

			$role_name = filter_input( INPUT_POST, 'role_name', FILTER_DEFAULT, FILTER_SANITIZE_STRING );
			$roles     = get_option( CWCCV_PLUGIN_PREFIX . '_user_roles' );
			$key       = array_search( strtolower( $role_name ), array_map( 'strtolower', $roles ) );

			if ( false !== $key ) {
				unset( $roles[ $key ] );
				update_option( CWCCV_PLUGIN_PREFIX . '_user_roles', $roles );

				// delete role from user list.
				$custom_user_role = str_replace( ' ', '_', strtolower( $role_name ) );
				if ( wp_roles()->is_role( $custom_user_role ) ) {
					remove_role( $custom_user_role );

				}

				$response = array(
					'status'  => 'success',
					'message' => '<div id="cwccv_message" class="updated inline"><p><strong>' . __( 'Role has been deleted.', 'codup-woocommerce-catalog-visibility' ) . '</strong></p></div>',
				);
				echo json_encode( $response );
			}
			wp_die();
		}

		/**
		 * Updates the user group
		 *
		 * @since 1.1.1.0
		 */
		public function update_user_group() {

			$old_group_name = filter_input( INPUT_POST, 'old_group_name', FILTER_DEFAULT, FILTER_SANITIZE_STRING );
			$new_group_name = filter_input( INPUT_POST, 'group_name', FILTER_DEFAULT, FILTER_SANITIZE_STRING );

			$groups = get_option( CWCCV_PLUGIN_PREFIX . '_user_groups' );

			if ( '' == $new_group_name ) {
				$response = array(
					'status'  => 'fail',
					'message' => '<div id="cwccv_message" class="error inline"><p><strong>' . __( 'Group cannot be an empty value.', 'codup-woocommerce-catalog-visibility' ) . '</strong></p></div>',
				);
				echo json_encode( $response );
			} elseif ( array_search( strtolower( $new_group_name ), array_map( 'strtolower', $groups ), true ) && strtolower( trim( $new_group_name ) ) != strtolower( trim( $old_group_name ) ) ) {
				$response = array(
					'status'  => 'fail',
					'message' => '<div id="cwccv_message" class="error inline"><p><strong>' . __( 'Group already exist.', 'codup-woocommerce-catalog-visibility' ) . '</strong></p></div>',
				);
				echo json_encode( $response );
			} else {
				$array_key            = array_search( $old_group_name, $groups );
				$groups[ $array_key ] = $new_group_name;
				update_option( CWCCV_PLUGIN_PREFIX . '_user_groups', $groups );
				$response = array(
					'status'  => 'success',
					'message' => '<div id="cwccv_message" class="updated inline"><p><strong>' . __( 'Group has been updated.', 'codup-woocommerce-catalog-visibility' ) . '</strong></p></div>',
				);
				echo json_encode( $response );
			}
			wp_die();
		}

		/**
		 * Deletes the user group
		 *
		 * @since 1.1.1.0
		 */
		public function delete_user_group() {

			$group_name = filter_input( INPUT_POST, 'group_name', FILTER_DEFAULT, FILTER_SANITIZE_STRING );
			$groups     = get_option( CWCCV_PLUGIN_PREFIX . '_user_groups' );
			$key        = array_search( strtolower( $group_name ), array_map( 'strtolower', $groups ) );
			if ( false !== $key ) {
				unset( $groups[ $key ] );
				update_option( CWCCV_PLUGIN_PREFIX . '_user_groups', $groups );
				$response = array(
					'status'  => 'success',
					'message' => '<div id="cwccv_message" class="updated inline"><p><strong>' . __( 'Group has been deleted.', 'codup-woocommerce-catalog-visibility' ) . '</strong></p></div>',
				);
				echo json_encode( $response );
			}
			wp_die();
		}

	}
	new CWCCV_Admin_Settings();
}



