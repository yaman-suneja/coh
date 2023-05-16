<?php
/**
 * WC Ecommerce For Woocommerce Main Class.
 *
 * @package codupio-request-for-quote-d659b8ba1ef2\Emails
 */

defined( 'ABSPATH' ) || exit;

/**
 * Codup_B2B_Ecommerce_For_Woocommerce class.
 */
class Codup_Role_Based_Discounts_Settings {

	/**
	 * Settings Tab
	 *
	 * @var static $settings_tab Settings Tab.
	 */
	public static $settings_tab = 'codup-role-based';

	/**
	 * Function Calculate Shipping.
	 */
	public static function init() {

		add_action( 'woocommerce_admin_field_codup_user_role', __CLASS__ . '::output_user_role_fields' );
		add_action( 'woocommerce_admin_field_codup_user_role_footer', __CLASS__ . '::output_user_role_fields_footer' );

	}

	/**
	 * Return RFQ setting fields.
	 *
	 * @return type
	 */
	public static function get_settings() {

		$settings = self::get_user_role_fields();

		return $settings;
	}

	/**
	 * Return User Role setting fields.
	 *
	 * @return type
	 */
	public static function get_user_role_fields() {
		$fields = array(
			'general_title'         => array(
				'title' => __( 'Role Based Discount', 'codup-wcrfq' ),
				'type'  => 'title',
				'id'    => 'codup_user_role_title',
				'desc'  => __( 'This will allow you to create user roles and assign discount to them globally and on product level aswell', 'codup-wcrfq' ),
			),

			'enable_user_role'      => array(
				'name'     => __( 'Role Based Discount', 'codup-wcrfq' ),
				'type'     => 'checkbox',
				'desc'     => __( 'Enable Role Based Discount Functionality', 'codup-wcrfq' ),
				'desc_tip' => __( 'Lets you create user roles and apply role based discounts.', 'codup-wcrfq' ),
				'id'       => self::$settings_tab . '_enable_user_role',
			),

			'discount_type_global'  => array(
				'name'     => __( 'Apply Discount Globally', 'codup-wcrfq' ),
				'type'     => 'checkbox',
				'id'       => self::$settings_tab . 'discount_type_global',
				'desc'     => __( 'Enable Role Based Discount on Global Level.', 'codup-wcrfq' ),
				'desc_tip' => __( 'Lets you apply role based discounts on all products globally.', 'codup-wcrfq' ),
				'class'    => 'hide-on-disable',
			),
			'discount_type_product' => array(
				'name'     => __( 'Apply Discounts On Product Level', 'codup-wcrfq' ),
				'type'     => 'checkbox',
				'id'       => self::$settings_tab . 'discount_type_product',
				'desc'     => __( 'Enable Role Based Discount on Product Level.', 'codup-wcrfq' ),
				/* translators: %s descount type description */
				'desc_tip' => sprintf( __( 'Lets you apply role based discounts for individual products from product pages. %s Product Level discounts have higher priority	when both global and product level discounts are applied.', 'codup-wcrfq' ), '<br/>' ),
				'class'    => 'hide-on-disable',
			),
			'general_title_end'     => array(
				'type' => 'sectionend',
				'id'   => 'codup_user_role_title',
			),
			'discount_title'        => array(
				'title' => __( 'User Roles', 'codup-wcrfq' ),
				/* translators: %s discount title description */
				'desc'  => sprintf( __( 'Create %s by entering a name for your custom role. Assign your custom roles to individual customers from Edit Users Settings page. Apply global discounts to your custom roles, if Apply Discounts Globally option is enabled.', 'codup-wcrfq' ), '<span style="font-weight:700">' . esc_html_e( 'User Roles', 'codup-wcrfq' ) . '</span>' ),
				'type'  => 'title',
				'id'    => 'codup_user_role_discount',
			),
		);

		$user_roles = get_option( 'codup_ecommerce_role_based_settings' );

		if ( null == $user_roles || 0 == count( $user_roles ) ) {

			$user_roles = array(
				array(
					'role'     => '',
					'discount' => '',
				),
			);
		}

		$fields['roles'] = array(
			'type'  => 'codup_user_role',
			'id'    => 'codup_ecommerce_role_based_settings',
			'class' => 'codup-ecommerce-user-role-mode hide-on-disable',
			'css'   => '',
			'roles' => $user_roles,
		);

		$fields['roles_footer'] = array(
			'type'  => 'codup_user_role_footer',
			'id'    => 'codup-user-role-footer',
			'class' => 'hide-on-disable',
		);

		$fields['discount_title_end'] = array(
			'type'  => 'sectionend',
			'id'    => 'codup_user_role_discount',
			'class' => 'hide-on-disable',
		);
		return $fields;
	}

	/**
	 * Output User Role setting fields.
	 *
	 * @param array $field_config Role Based Settings Tab array.
	 */
	public static function output_user_role_fields( $field_config ) {
		include CWRFQ_PLUGIN_DIR . '/includes/admin/role-based-discounts/views/user-role-fields.php';
	}

	/**
	 * Output User Role setting fields footer.
	 *
	 * @param array $field_config Role Based Settings Tab array.
	 */
	public static function output_user_role_fields_footer( $field_config ) {
		include CWRFQ_PLUGIN_DIR . '/includes/admin/role-based-discounts/views/user-role-fields-footer.php';
	}

}
