<?php
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Wpccl_Helper' ) ) {
	class Wpccl_Helper {
		protected static $settings = [];
		protected static $localization = [];

		function __construct() {
			self::$settings     = (array) get_option( 'wpccl_settings', [] );
			self::$localization = (array) get_option( 'wpccl_localization', [] );
		}

		public static function get_settings() {
			return apply_filters( 'wpccl_get_settings', self::$settings );
		}

		public static function get_setting( $name, $default = false ) {
			if ( ! empty( self::$settings ) && isset( self::$settings[ $name ] ) ) {
				$setting = self::$settings[ $name ];
			} else {
				$setting = get_option( 'wpccl_' . $name, $default );
			}

			return apply_filters( 'wpccl_get_setting', $setting, $name, $default );
		}

		public static function localization( $key = '', $default = '' ) {
			$str = '';

			if ( ! empty( $key ) && ! empty( self::$localization[ $key ] ) ) {
				$str = self::$localization[ $key ];
			} elseif ( ! empty( $default ) ) {
				$str = $default;
			}

			return esc_html( apply_filters( 'wpccl_localization_' . $key, $str ) );
		}
	}
}

new Wpccl_Helper();
