<?php
/**
 * WC RFQ settings.
 *
 * @package b2b-ecommerce-for-woocomerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'B2BE_Re_Order_Settings' ) ) {

	/**
	 * Class B2BE_Re_Order_Settings.
	 */
	class B2BE_Re_Order_Settings {

		/**
		 * Settings Tab
		 *
		 * @var static $settings_tab Settings Tab.
		 */
		public static $settings_tab = 'codup-reorder';

		/**
		 * Init Function.
		 */
		public static function init() {

		}


		/**
		 * Return Re-Order setting fields.
		 *
		 * @return type
		 */
		public static function get_settings() {

			$enable_reorder   = get_option( 'codup-reorder_enable_reorder', 'no' );
			$reorder_btn_text = get_option( 'codup-reorder_reorder_btn_text', 'Re-Order' );

			$settings = array(
				'section_title'    => array(
					'name' => __( 'Re-Order settings', 'b2b-ecommerce' ),
					'type' => 'title',
					'desc' => __( 'This feature of Re-order allow users to make same order again on just 1 click.', 'b2b-ecommerce' ),
					'id'   => self::$settings_tab . '_section_title',
				),
				'enable_reorder'   => array(
					'name'  => __( 'Enable Re-Order Feature', 'b2b-ecommerce' ),
					'type'  => 'checkbox',
					'value' => $enable_reorder,
					'desc'  => '',
					'id'    => self::$settings_tab . '_enable_reorder',
				),
				'reorder_btn_text' => array(
					'name'  => __( 'Text for Re-Order Button', 'b2b-ecommerce' ),
					'type'  => 'text',
					'value' => $reorder_btn_text,
					'desc'  => '',
					'id'    => self::$settings_tab . '_reorder_btn_text',
				),
				'section_end'      => array(
					'type' => 'sectionend',
					'id'   => self::$settings_tab . '_section_end',
				),
			);

			/*
			@name: wc_settings_tab_codup-reorder
			@desc: Modify the b2b ecommerce settings for reorder functionality.
			@param: (array) $settings Reorder Global Settings.
			@package: b2b-ecommerce-for-woocommerce
			@module: re order
			@type: filter
			*/
			return apply_filters( 'wc_settings_tab_' . self::$settings_tab, $settings );
		}
	}
}
