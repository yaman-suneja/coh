<?php
/**
 * Options page for Bulk Shop
 *
 * @package bulkshop/class-wbs-options
 */


defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_Settings_WBS' ) ) :

	function wbs_add_settings() {

		/**
		 * WC_Settings_CYP class
		 */
		class WC_Settings_WBS extends WC_Settings_Page {

			/**
			 * Constructor.
			 */
			public function __construct() {
				$this->id    = 'wbs';
				$this->label = __( 'Bulk Shop', 'woo-bulk-shop' );

				add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
				add_action( 'woocommerce_settings_' . $this->id, array( $this, 'output' ) );
				add_action( 'woocommerce_settings_save_' . $this->id, array( $this, 'save' ) );
				add_action( 'woocommerce_sections_' . $this->id, array( $this, 'output_sections' ) );
			}

			/**
			 * Get sections
			 *
			 * @return array
			 */
			public function get_sections() {
			
				$sections = array(
					''       => __( 'List settings', 'woo-bulk-shop' ),
					'second' => __( 'CSS', 'woo-bulk-shop' ),
					'third'  => __( 'Integrations', 'woo-bulk-shop' ),
				);
				
				return apply_filters( 'woocommerce_get_sections_' . $this->id, $sections );
			}

			/**
			 * Settings array
			 *
			 * @return array
			 */
			public function get_settings( $section = '' ) {

				if ( '' === $section ) {

					$settings = apply_filters( 'wbs_general_settings', array(

						array(
							'name' => __( 'Bulk Shop', 'woo-bulk-shop' ),
							'type' => 'title',
							'desc' => __( 'Settings for table headings and buttons. Use with caution, this options overwrites language settings.', 'woo-bulk-shop' ),
							'id'   => 'wbs_settings',
						),

						array(
							'name'     => __( 'Heading(mobile): Product image', 'woo-bulk-shop' ),
							'desc_tip' => __( 'Set your own label for product image. If blank the default name is used', 'woo-bulk-shop' ),
							'id'       => 'wbs_options[wbs-heading-product-image]',
							'type'     => 'text',
							'css'      => 'min-width:200px;',
						),

						array(
							'name'     => __( 'Heading: Name', 'woo-bulk-shop' ),
							'desc_tip' => __( 'Set your own heading for name. If blank the default name is used', 'woo-bulk-shop' ),
							'id'       => 'wbs_options[wbs-heading-name]',
							'type'     => 'text',
							'css'      => 'min-width:200px;',
						),

						array(
							'name'     => __( 'Heading: Description', 'woo-bulk-shop' ),
							'desc_tip' => __( 'Set your own heading for description. If blank the default name is used', 'woo-bulk-shop' ),
							'id'       => 'wbs_options[wbs-heading-description]',
							'type'     => 'text',
							'css'      => 'min-width:200px;',
						),

						array(
							'name'     => __( 'Heading: Price', 'woo-bulk-shop' ),
							'desc_tip' => __( 'Set your own heading for price. If blank the default name is used', 'woo-bulk-shop' ),
							'id'       => 'wbs_options[wbs-heading-price]',
							'type'     => 'text',
							'css'      => 'min-width:200px;',
						),

						array(
							'name'     => __( 'Heading: Quantity', 'woo-bulk-shop' ),
							'desc_tip' => __( 'Set your own heading for quantity. If blank the default name is used', 'woo-bulk-shop' ),
							'id'       => 'wbs_options[wbs-heading-quantity]',
							'type'     => 'text',
							'css'      => 'min-width:200px;',
						),

						array(
							'name'     => __( 'Heading: Total', 'woo-bulk-shop' ),
							'desc_tip' => __( 'Set your own heading for total. If blank the default name is used', 'woo-bulk-shop' ),
							'id'       => 'wbs_options[wbs-heading-total]',
							'type'     => 'text',
							'css'      => 'min-width:200px;',
						),
						
						array(
							'name'     => __( 'Button: Add', 'woo-bulk-shop' ),
							'desc_tip' => __( 'Set your own button name for Add. If blank the default name is used', 'woo-bulk-shop' ),
							'id'       => 'wbs_options[wbs-button-add]',
							'type'     => 'text',
							'css'      => 'min-width:200px;',
						),

						array(
							'name'     => __( 'Button: Add to cart', 'woo-bulk-shop' ),
							'desc_tip' => __( 'Set your own button name for Add to cart. If blank the default name is used', 'woo-bulk-shop' ),
							'id'       => 'wbs_options[wbs-button-add-to-cart]',
							'type'     => 'text',
							'css'      => 'min-width:200px;',
						),

						array(
							'name'     => __( 'Button: Sales', 'woo-bulk-shop' ),
							'desc_tip' => __( 'Set your own button name for Sales. If blank the default name is used', 'woo-bulk-shop' ),
							'id'       => 'wbs_options[wbs-button-sales]',
							'type'     => 'text',
							'css'      => 'min-width:200px;',
						),

						array(
							'name'     => __( 'Placeholder: Quantity', 'woo-bulk-shop' ),
							'desc_tip' => __( 'Set your own placeholder. If blank the default name is used', 'woo-bulk-shop' ),
							'id'       => 'wbs_options[wbs-placeholder-qty]',
							'type'     => 'text',
							'css'      => 'min-width:200px;',
						),

						array(
							'name'     => __( 'Placeholder: Search', 'woo-bulk-shop' ),
							'desc_tip' => __( 'Set your own placeholder. If blank the default name is used', 'woo-bulk-shop' ),
							'id'       => 'wbs_options[wbs-placeholder-search]',
							'type'     => 'text',
							'css'      => 'min-width:200px;',
						),

						array(
							'name'     => __( 'Search radio: Text', 'woo-bulk-shop' ),
							'desc_tip' => __( 'If blank the default name is used', 'woo-bulk-shop' ),
							'id'       => 'wbs_options[wbs-radio-text]',
							'type'     => 'text',
							'css'      => 'min-width:200px;',
						),

						array(
							'name'     => __( 'Search radio: SKU', 'woo-bulk-shop' ),
							'desc_tip' => __( 'If blank the default name is used', 'woo-bulk-shop' ),
							'id'       => 'wbs_options[wbs-radio-sku]',
							'type'     => 'text',
							'css'      => 'min-width:200px;',
						),

						array(
							'name'     => __( 'Search radio: Tag', 'woo-bulk-shop' ),
							'desc_tip' => __( 'If blank the default name is used', 'woo-bulk-shop' ),
							'id'       => 'wbs_options[wbs-radio-tag]',
							'type'     => 'text',
							'css'      => 'min-width:200px;',
						),

						array(
							'name'     => __( 'Login to see price', 'woo-bulk-shop' ),
							'desc_tip' => __( 'If blank the default name is used', 'woo-bulk-shop' ),
							'id'       => 'wbs_options[wbs-login-see-price]',
							'type'     => 'text',
							'css'      => 'min-width:200px;',
						),




						array(
							'type' => 'sectionend',
							'id'   => 'wbs_gen_settings',
						),
					));

				} elseif ( 'second' === $section ) {

					$settings = apply_filters( 'wbs_css_settings', array(
						
						array(
							'name' => __( 'Bulk Shop', 'woo-bulk-shop' ),
							'type' => 'title',
							'desc' => __( 'Styling options', 'woo-bulk-shop' ),
							'id'   => 'wbs_settings',
						),

						array(
							'name'     => __( 'Remove storefront (shop-table) CSS', 'woo-bulk-shop' ),
							'desc_tip' => __( 'If your theme have truble showing Bulk Shop on i.e mobile', 'woo-bulk-shop' ),
							'id'       => 'wbs_options[wbs-css-shop-table]',
							'type'     => 'checkbox',
							'css'      => 'min-width:200px;',
						),

						array(
							'name'     => __( 'Thumbnail size (max width)', 'woo-bulk-shop' ),
							'desc_tip' => __( 'Set thumbnail image size, default 50px', 'woo-bulk-shop' ),
							'id'       => 'wbs_options[wbs-thumbnail-size]',
							'type'     => 'text',
							'css'      => 'min-width:200px;',
						),

						array(
							'name'     => __( 'Thumbnail size - mobile (max width)', 'woo-bulk-shop' ),
							'desc_tip' => __( 'Set thumbnail image size, default 150px', 'woo-bulk-shop' ),
							'id'       => 'wbs_options[wbs-thumbnail-mobile-size]',
							'type'     => 'text',
							'css'      => 'min-width:200px;',
						),

						array(
							'type' => 'sectionend',
							'id'   => 'wbs_cp_settings',
						),

					));
				} else {
					
					$settings = apply_filters( 'wbs_integration_settings', array(
						
						array(
							'name' => __( 'Bulk Shop', 'woo-bulk-shop' ),
							'type' => 'title',
							'desc' => __( 'Settings for integration options', 'woo-bulk-shop' ),
							'id'   => 'wbs_settings',
						),

						array(
							'name'     => __( 'Name your price: Text', 'woo-bulk-shop' ),
							'desc_tip' => __( 'Set label for Name your price', 'woo-bulk-shop' ),
							'id'       => 'wbs_options[wbs-nyp-name]',
							'type'     => 'text',
							'css'      => 'min-width:200px;',
						),

						array(
							'name'     => __( 'Name your price: Min price', 'woo-bulk-shop' ),
							'desc_tip' => __( 'Set label for minimum price', 'woo-bulk-shop' ),
							'id'       => 'wbs_options[wbs-nyp-minimum]',
							'type'     => 'text',
							'css'      => 'min-width:200px;',
						),

						array(
							'type' => 'sectionend',
							'id'   => 'wbs_integration_settings',
						),

					));
				}

				return apply_filters( 'woocommerce_get_settings_' . $this->id, $settings, $section );
			}
			/**
			 * Output the settings
			 *
			 * @since 1.0
			 */
			public function output() {
			
				global $current_section;
				
				$settings = $this->get_settings( $current_section );
				WC_Admin_Settings::output_fields( $settings );
				echo esc_attr( $this->js_scripts() );
			}
			
			/**
			 * Output JS scripts
			 */
			public function js_scripts() {
				?>
				<p><button class="button" onclick="clearFields();"><?php esc_attr_e( 'Clear fields', 'woo-bulk-shop' ); ?></button></p>
				<script>
				var $ = jQuery;
			
				function clearFields() {
					
					$(':input','#mainform')
						.not(':button, :submit, :reset, :hidden')
						.val('')
						.prop('checked', false)
						.prop('selected', false);
					
					event.preventDefault();
				}
				</script>
				<?php
			}

			/**
			 * Save settings
			*
			* @since 1.0
			*/
			public function save() {

				global $current_section;
				
				$settings = $this->get_settings( $current_section );
				WC_Admin_Settings::save_fields( $settings );
			}

		}

		return new WC_Settings_WBS();
	}
	add_filter( 'woocommerce_get_settings_pages', 'wbs_add_settings', 15 );

endif;



