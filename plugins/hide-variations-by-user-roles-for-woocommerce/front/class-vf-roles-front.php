<?php
if ( ! class_exists( 'ADDF_VF_Roles_Front_Class' ) ) {
	/**
	 * Class Start
	 */
	class ADDF_VF_Roles_Front_Class extends ADDF_VF_Roles_Main_Class {

		
		public function __construct() {
			
			add_filter( 'woocommerce_variation_is_active', array( $this, 'addf_vf_roles_variation_is_active' ) , 10, 2 );
			add_filter( 'woocommerce_variation_is_visible', array( $this, 'addf_vf_roles_variation_is_visible'), 10, 2 );
		}


		public function addf_vf_roles_variation_is_active( $active, $variation ) {

			if ( is_user_logged_in() ) {
				$user                    = wp_get_current_user();
				$addf_vf_role_curr_array = $user->roles ;
				$addf_vf_role_curr_user  = current( $addf_vf_role_curr_array );
			} else {
				$addf_vf_role_curr_user = 'guest';
			}
			$addf_vf_roles_opt       = get_post_meta( $variation->get_id() , 'addf_vf_roles_restriction_type' , true );
			$addf_vf_roles_sel_users = get_post_meta( $variation->get_id() , 'addf_vf_roles_select_roles' , true );
			if ( in_array( $addf_vf_role_curr_user , (array) $addf_vf_roles_sel_users ) ) {
				if (  'unselectable' ==  $addf_vf_roles_opt  ) {
					return false;
				}
			}

			return $active;
		}

		public function addf_vf_roles_variation_is_visible( $visible, $variation_id ) {

			if ( is_user_logged_in() ) {
				$user                    = wp_get_current_user();
				$addf_vf_role_curr_array = $user->roles ;
				$addf_vf_role_curr_user  = current( $addf_vf_role_curr_array );
			} else {
				$addf_vf_role_curr_user = 'guest';
			}
			$addf_vf_roles_opt       = get_post_meta( $variation_id , 'addf_vf_roles_restriction_type' , true );
			$addf_vf_roles_sel_users = get_post_meta( $variation_id , 'addf_vf_roles_select_roles' , true );
			if ( in_array( $addf_vf_role_curr_user , (array) $addf_vf_roles_sel_users ) ) {
				if (  'completely_hide' ==  $addf_vf_roles_opt  ) {
					return false;
				}
			}

			return $visible;
		}
	}
	new ADDF_VF_Roles_Front_Class();
}
