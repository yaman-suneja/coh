<?php

namespace Varunsridharan\WordPress;

use RapidAddon;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( '\RapidAddon' ) ) {
	require_once __DIR__ . '/rapid-addon.php';
}

if ( ! class_exists( '\Varunsridharan\WordPress\WPAllImport' ) ) {
	/**
	 * Class WPAllImport
	 *
	 * @package Varunsridharan\WordPress
	 * @author Varun Sridharan <varunsridharan23@gmail.com>
	 */
	class WPAllImport extends RapidAddon {
		/**
		 * Stores All WPO Fields.
		 *
		 * @var array
		 */
		protected $wpo_fields = array();

		/**
		 * WPAllImport constructor.
		 *
		 * @param $name
		 * @param $slug
		 *
		 * @uses content_to_update
		 * @uses load_wponion_assets
		 */
		public function __construct( $name, $slug ) {
			parent::__construct( $name, $slug );
			add_action( 'admin_enqueue_scripts', array( &$this, 'load_wponion_assets' ) );
			add_action( 'pmxi_reimport', array( $this, 'content_to_update' ), 10, 2 );
		}

		/**
		 * Provides All Screenids To Load Assets.
		 *
		 * @return string[]
		 */
		public function assets_screen_ids() {
			return array( 'all-import_page_pmxi-admin-import', 'all-import_page_pmxi-admin-manage' );
		}

		/**
		 * Adds Custom Option To Choose What To Update.
		 *
		 * @param $post_type
		 * @param $post
		 */
		public function content_to_update( $post_type, $post ) {
			if ( $this->is_active_addon( $post_type ) ) {
				$slug    = $this->slug;
				$checked = ( isset( $post[ $slug ] ) ) ? 'checked="checked"' : '';
				echo <<<HTML
<div class="input">
	<input type="hidden" name="is_update_${slug}" value="0"/>
	<input type="checkbox" id="is_update_${slug}" name="is_update_${slug}" value="1" $checked />
	<label for="is_update_${slug}">{$this->name}</label>
</div>
HTML;
			}
		}

		/**
		 * Creates Customized Name for wpo field with its parent.
		 *
		 * @param $field
		 * @param $parent
		 *
		 * @return string
		 */
		private function wpo_get_field_name( $field, $parent ) {
			return ( ! wponion_is_unarrayed( $field ) ) ? $parent . wponion_field_id( $field ) : $parent;
		}

		/**
		 * Adds Each And Every Field To WPAllImport And Changes The Slug.
		 *
		 * @param        $fields
		 * @param string $parent
		 *
		 * @return array
		 */
		private function fix_wpo_field_names( $fields, $parent = '' ) {
			$parent = ( ! empty( $parent ) ) ? rtrim( $parent, '_' ) . '_' : '';

			if ( isset( $fields['id'] ) && ! isset( $fields['fields'] ) ) {
				$fields['id']        = $this->wpo_get_field_name( $fields, $parent );
				$fields['part_name'] = $fields['id'];
				$this->add_field( $fields['id'], $fields['title'], $fields['type'] );
			} elseif ( isset( $fields['fields'] ) ) {
				$this->fix_wpo_field_names( $fields['fields'], $this->wpo_get_field_name( $fields, $parent ) );
			} elseif ( ! isset( $fields['id'] ) && is_array( $fields ) ) {
				foreach ( $fields as $id => $_field ) {
					$this->fix_wpo_field_names( $_field, $parent );
				}
			}
			return $fields;
		}

		/**
		 * Adds WPO Field.
		 *
		 * @param $field
		 *
		 * @return $this
		 */
		public function add_wpo_field( $field ) {
			$this->fix_wpo_field_names( $field );
			$this->wpo_fields[] = $field;
			return $this;
		}

		/**
		 * Loads WPOnion Assets.
		 */
		public function load_wponion_assets() {
			$screen = get_current_screen();
			if ( isset( $screen->id ) && in_array( $screen->id, $this->assets_screen_ids() ) ) {
				wponion_load_core_assets();
			}
		}

		/**
		 * @param $post_type
		 * @param $current_values
		 */
		public function wpai_api_metabox( $post_type, $current_values ) {
			if ( empty( $this->wpo_fields ) ) {
				parent::wpai_api_metabox( $post_type, $current_values );
				return;
			}

			if ( ! $this->is_active_addon( $post_type ) ) {
				return;
			}

			$this->handle_wponion_fields( $current_values );
		}

		/**
		 * Renders WPOnion Fields.
		 */
		protected function handle_wponion_fields( $values ) {
			echo $this->helper_metabox_top( $this->name );
			echo '<div id="wponion-module-' . $this->slug . '" class="wponion-module-' . $this->slug . ' wponion-framework wponion-module-wpallimport">';
			/* @var \WPO\Field $field */
			foreach ( $this->wpo_fields as $field ) {
				echo wponion_add_element( $field, $values[ $this->slug ], $this->slug );
			}
			echo '</div>';
			echo $this->helper_metabox_bottom();
		}
	}
}
