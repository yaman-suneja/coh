<?php
if ( ! class_exists( 'ADDF_VF_Roles_Admin_Class' ) ) {
	/**
	 * Class Start
	 */
	class ADDF_VF_Roles_Admin_Class extends ADDF_VF_Roles_Main_Class {
		public function __construct() {
			add_action( 'admin_enqueue_scripts', array( $this, 'addf_vf_roles_Admin_enqueue_scripts' ) );
			// Add Fields into Variable Product's Variations
			add_action('woocommerce_variation_options', array($this, 'addf_vf_roles_add_fields_in_variation_products'), 10, 3);
			//Save Variations custom fields 
			add_action('woocommerce_save_product_variation', array($this, 'addf_vf_roles_save_custom_field_variations'), 10, 2);
		}
		public function addf_vf_roles_Admin_enqueue_scripts() {
			wp_enqueue_style( 'addf_vf_roles_admins', plugins_url( '../includes/css/addf_vf_roles-style.css', __FILE__ ), false, '1.0.0' );
			wp_enqueue_script( 'jquery' );
			// Enqueue Select2 JS CSS.
			wp_enqueue_style( 'select2', plugins_url( '../includes/css/select2.css', __FILE__ ), true, '1.0.0' );
			wp_enqueue_script( 'select2', plugins_url( '../includes/js/select2.js', __FILE__ ), false, '1.0.0', array( 'jquery' ) );
			// Enqueue WP_MEDIA.
			wp_enqueue_media();
		}
		public function addf_vf_roles_add_fields_in_variation_products( $loop, $variation_data, $variation) {
			global $wp_roles , $post;
			$addf_vfr_roles = $wp_roles->get_names();
			$addf_vf_roles  = get_post_meta( $variation->ID , 'addf_vf_roles_select_roles' , true  );                
			?>
					<table class="addf_vf_role_table">
						<tr>
							<td colspan="2" class="align-center-vfr-heading ">
								<div class="addf_vfr_center">
								<?php echo esc_html__( 'Variation restriction for user roles' , 'addf_vf_roles_dl' ); ?> 
								</div>
							</td>
						</tr>
						<tr>
							<td>
								<h2><?php echo esc_html__( 'Select restriction type' , 'addf_vf_roles_dl' ); ?> </h2>
							</td>
							<td>
							<?php
							$addf_vf_roles_op = get_post_meta( $variation->ID , 'addf_vf_roles_restriction_type' , true );
							?>
								<input type="radio" value="unselectable" 
								<?php 
								if ( 'unselectable' === $addf_vf_roles_op ) {
									echo 'checked'; } 
								?>
								 name="addf_vf_roles_restriction_type<?php echo esc_attr( $variation->ID ); ?>" class="checkbox addf_vfr_checkbox addf_vf_roles_restriction_type-fst" >
								<span > <?php echo esc_html__( 'Unselectable' , 'addf_vf_roles_dl' ); ?> </span>
								&nbsp;&nbsp;&nbsp;
								<input type="radio" value="completely_hide" 
								<?php 
								if ( 'completely_hide' === $addf_vf_roles_op ) {
									echo 'checked'; } 
								?>
								 name="addf_vf_roles_restriction_type<?php echo esc_attr( $variation->ID ); ?>" class="checkbox addf_vfr_checkbox addf_vf_roles_restriction_type-scnd" >
								<span > <?php echo esc_html__( 'completely hide' , 'addf_vf_roles_dl' ); ?> </span>
								<p class="description"> <?php echo esc_html__( 'Select a restriction type for user roles' , 'addf_vf_roles_dl' ); ?> </p>
							   
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<label class="" for="addf_vf_roles_select_roles"><?php echo esc_html__( 'Select Roles' , 'addf_vf_roles_dl' ); ?> </label>
								<select class="wc-enhanced-select addf_vf_roles_select_roles" name="addf_vf_roles_select_roles<?php echo esc_attr( $variation->ID ); ?>[]" multiple>
								<?php
								foreach ($addf_vfr_roles as $key => $addf_vfr_single_role) {
									?>
									<option value="<?php echo esc_attr($key); ?>" 
															  <?php 
																if ( in_array( $key , (array) $addf_vf_roles ) ) {
																	echo 'selected="selected"'; } 
																?>
									>
									<?php echo esc_html__( $addf_vfr_single_role , 'addf_vf_roles_dl' ); ?></option>
									<?php } ?>
									<option value="guest"  
									<?php 
									if ( in_array( 'guest' , (array) $addf_vf_roles ) ) {
										echo 'selected="selected"'; } 
									?>
									> <?php echo esc_html__( 'Guest' , 'addf_vf_roles_dl' ); ?></option>
								</select>
								<p class="description"><?php echo esc_html__( 'Select roles you want to restrict this variation' , 'addf_vf_roles_dl' ); ?></p>
							</td>
						</tr>
					</table>
				<?php
		}
		public function addf_vf_roles_save_custom_field_variations( $variation_id, $i) {
			global $wp_roles;
			if (! defined('ABSPATH') ) {
				exit; 
			}
			if ( isset( $_POST['vf_roles_nonce_field'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['vf_roles_nonce_field'] ) ), 'cor_field' ) ) {
				echo '';
			}
			if ( isset( $_POST['addf_vf_roles_select_roles' . $variation_id] ) ) {
				$addf_vf_roles_vl_countries = sanitize_meta( '', wp_unslash( $_POST['addf_vf_roles_select_roles' . $variation_id] ), '' );
			} else {
				$addf_vf_roles_vl_countries = '';
			}
			update_post_meta( $variation_id, 'addf_vf_roles_select_roles', $addf_vf_roles_vl_countries );
			if ( isset( $_POST['addf_vf_roles_restriction_type' . $variation_id] ) ) {
				$addf_vf_roles_vl_restriction_op = sanitize_meta( '', wp_unslash( $_POST['addf_vf_roles_restriction_type' . $variation_id] ), '' );
			}
			update_post_meta( $variation_id, 'addf_vf_roles_restriction_type', $addf_vf_roles_vl_restriction_op );
		}
	}
	new ADDF_VF_Roles_Admin_Class();
}
