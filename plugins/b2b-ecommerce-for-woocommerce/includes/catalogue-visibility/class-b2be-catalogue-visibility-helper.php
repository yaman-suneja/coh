<?php
/**
 * File class-b2be_catalogue-helper.php
 *
 * @package catalog-visibility-for-woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Class B2BE_Catalogue_Visibility_Helper
 * All the functions that can be used anywhere in plugin are defined here.
 */
class B2BE_Catalogue_Visibility_Helper {

	/**
	 * Prints dropdown.
	 *
	 * @param string $name Arguments for dropdown name.
	 * @param array  $args for dropdown element.
	 * @param string $select_id for dropdown element.
	 */
	public static function get_select( $name, $args, $select_id = '', $multiple = 'multiple' ) {
		if ( ! isset( $name ) ) {
			return;
		}
		$dropdown = '<select name="' . $name . '" id="' . $select_id . '" class="' . B2BE_CATALOGUE_VISIBILITY_PREFIX . '_selectpicker ' . ( isset( $args['class'] ) ? $args['class'] : '' ) . '" ' . $multiple . ( isset( $args['is_disabled'] ) ? $args['is_disabled'] : '' ) . ' ' . ( isset( $args['is_required'] ) ? '' : '' ) . ' >';
		if ( isset( $args['option'] ) ) {
			foreach ( $args['option'] as $value ) {
				$selected  = isset( $args['selected_option_value'] ) && is_array( $args['selected_option_value'] ) && in_array( $value['id'], $args['selected_option_value'] ) ? 'selected="selected"' : '';
				$id        = isset( $value['id'] ) ? $value['id'] : $value;
				$name      = isset( $value['name'] ) ? $value['name'] : $value;
				$dropdown .= '<option value="' . $id . '" ' . $selected . '>' . $name . '</option>';
			}
		}
		$dropdown    .= '</select>';
		$allowed_html = array(
			'select' => array(
				'id'       => array(),
				'name'     => array(),
				'class'    => array(),
				'title'    => array(),
				'multiple' => array(),
				'disabled' => array(),
				'required' => array(),
			),
			'option' => array(
				'value'    => array(),
				'selected' => array(),
			),
		);
		echo wp_kses( $dropdown, $allowed_html );
	}

	/**
	 * Returns checkbox
	 *
	 * @param array $args Checkbox arguments.
	 * @since 1.1.1.0
	 */
	public static function get_checkbox( $args ) {
		if ( ! isset( $args['name'] ) ) {
			return;
		}
		$checked   = isset( $args['is_checked'] ) && 'yes' === $args['is_checked'] ? 'checked' : '';
		$checkbox  = '<label class="form-switch">';
		$checkbox .= '<input type="checkbox" name="' . $args['name'] . '" class="' . ( isset( $args['class'] ) ? $args['class'] : '' ) . '" value="' . ( isset( $args['value'] ) ? $args['value'] : '1' ) . '" data-toggle="toggle" ' . $checked . ' >';
		$checkbox .= '<i></i>';
		$checkbox .= '</label>';

		$allowed_html = array(
			'label' => array(
				'class' => array(),
			),
			'i'     => array(),
			'input' => array(
				'type'    => array(),
				'name'    => array(),
				'class'   => array(),
				'value'   => array(),
				'data'    => array(),
				'checked' => array(),
			),
		);
		echo wp_kses( $checkbox, $allowed_html );
	}

	/**
	 * Returns radio button, accepts argument $name and $args.
	 *
	 * @param string $name Radio button name.
	 * @param string $args  Radio button args.
	 * @since 1.1.1.0
	 */
	public static function get_radio_button( $name, $args ) {
		if ( ! isset( $name ) ) {
			return;
		}
		$radio_button = '<input type="radio" name="' . $name . '" class="' . ( isset( $args['class'] ) ? $args['class'] : '' ) . '" value="' . ( isset( $args['value'] ) ? $args['value'] : '' ) . '" ' . ( $args['is_checked'] ? 'checked="checked"' : '' ) . '>';
		$allowed_html = array(
			'input' => array(
				'type'    => array(),
				'name'    => array(),
				'class'   => array(),
				'value'   => array(),
				'checked' => array(),
			),
		);
		echo wp_kses( $radio_button, $allowed_html );
	}

	/**
	 * Returns repeater fields button.
	 *
	 * @param array $args Button args.
	 * @since 1.1.1.0
	 * @return string $repeater_fields_button
	 */
	public static function get_repeater_fields_button( $args ) {

		if ( ! isset( $args['name'] ) ) {
			return;
		}
		$repeater_fields_button = '<button type="button" name="' . $args['name'] . '" class="b2be_catalogue_repeater_field_button ' . ( isset( $args['class'] ) ? $args['class'] : '' ) . '">+</button>';
		$allowed_html           = array(
			'button' => array(
				'name'  => array(),
				'class' => array(),
				'type'  => array(),
			),
		);
		echo wp_kses( $repeater_fields_button, $allowed_html );
	}

	/**
	 * Returns priority dropdown
	 *
	 * @param array $args Proirity select args.
	 * @since 1.1.1.0
	 * @return string $priority_select
	 */
	public static function get_priority_select( $args ) {

		if ( ! isset( $args['name'] ) ) {
			return;
		}
		$priority_select = '<select id="' . ( isset( $args['id'] ) ? $args['id'] : '' ) . '" name="' . $args['name'] . '" class="' . ( isset( $args['class'] ) ? $args['class'] : '' ) . ' ' . B2BE_CATALOGUE_VISIBILITY_PREFIX . '_priority_select" title="' . ( isset( $args['title'] ) ? $args['title'] : '' ) . '" >';

		for ( $i = 1; $i < 6; ++$i ) {
			$selected         = isset( $args['selected_option_value'] ) && $i == $args['selected_option_value'] ? 'selected' : '';
			$priority_select .= '<option value="' . $i . '" ' . $selected . '>' . $i . '</option>';
		}
		$priority_select .= '</select>';

		$allowed_html = array(
			'select' => array(
				'id'    => array(),
				'name'  => array(),
				'class' => array(),
				'title' => array(),
			),
			'option' => array(
				'value'    => array(),
				'selected' => array(),
			),
		);
		echo wp_kses( $priority_select, $allowed_html );
	}

	/**
	 * Returns text field.
	 *
	 * @param array $args Text field args.
	 * @since 1.1.1.0
	 * @return string $text_field
	 */
	public static function get_text_field( &$args ) {
		if ( ! isset( $args['name'] ) ) {
			return;
		}
		$text_field   = '<input type="text" name="' . $args['name'] . '" class="' . ( isset( $args['class'] ) ? $args['class'] : '' ) . '" value="' . ( isset( $args['value'] ) ? $args['value'] : '' ) . '" placeholder="' . ( isset( $args['placeholder'] ) ? $args['placeholder'] : '' ) . '" ' . ( isset( $args['is_required'] ) && $args['is_required'] ? 'required' : '' ) . '>';
		$allowed_html = array(
			'input' => array(
				'type'        => array(),
				'name'        => array(),
				'class'       => array(),
				'value'       => array(),
				'placeholder' => array(),
				'required'    => array(),
			),
		);
		echo wp_kses( $text_field, $allowed_html );
	}

	/**
	 * Returns list of all categories.
	 *
	 * @param string $value Text vaues.
	 * @param array  $options existing saved options.
	 * @since 1.1.1.0
	 * @return bool true/false
	 */
	public static function check_exist_value( $value, $options ) {

		if ( count( $options ) > 0 ) {
			foreach ( $options as $key => $val ) {
				if ( strtolower( $val ) == strtolower( $value ) ) {
					return true;
				}
			}
		}
		return false;
	}

	 /**
	  * Returns list of all categories.
	  *
	  * @since 1.1.1.0
	  * @return array $categories
	  */
	public static function get_categories() {
		$cat_args           = array(
			'orderby'    => 'name',
			'order'      => 'asc',
			'hide_empty' => false,
		);
		$product_categories = get_terms( 'product_cat', $cat_args );
		$categories         = array();
		foreach ( $product_categories as $key => $value ) {
			if ( 'Uncategorized' !== $value->name ) {
				$categories[ $key ]['id']   = $value->term_id;
				$categories[ $key ]['name'] = $value->name;
			}
		}
		return $categories;
	}

	/**
	 * Returns list of all products.
	 *
	 * @since 1.1.1.0
	 * @return array $products
	 */
	public static function get_products() {

		$products       = array(); // Product list.
		$query_args     = array(
			'post_type'        => 'product',
			'numberposts'      => -1,
			'suppress_filters' => false,
		);
		$products_query = get_posts( $query_args );
		if ( null !== $products_query ) {
			foreach ( $products_query as $key => $value ) {
				$products[ $key ]['id']   = $value->ID;
				$products[ $key ]['name'] = $value->post_title;
			}
		}
		return $products;
	}

	/**
	 * Returns list of all pages.
	 *
	 * @since 1.1.1.0
	 * @return array $pages
	 */
	public static function get_pages() {

		$pages       = array(); // page list.
		$pages_query = get_pages(
			array(
				'sort_order'  => 'ASC',
				'sort_column' => 'post_name',
			)
		);
		if ( null !== $pages_query ) {
			foreach ( $pages_query as $key => $value ) {
				$pages[ $key ]['id']   = $value->ID;
				$pages[ $key ]['name'] = $value->post_title;
			}
		}
		return $pages;
	}

	/**
	 * Returns list of all users.
	 *
	 * @since 1.1.1.0
	 * @return array $users
	 */
	public static function get_users() {
		$users     = array();
		$user_list = get_users();

		foreach ( $user_list as $key => $value ) {
			$users[ $key ]['id']    = $value->ID;
			$users[ $key ]['name']  = get_user_meta( $value->ID, 'first_name', true ) . ' ' . get_user_meta( $value->ID, 'last_name', true ) . ' ( ';
			$users[ $key ]['name'] .= $value->user_email . ' )';
		}
		return $users;
	}

	/**
	 * Returns list of all user roles.
	 *
	 * @since 1.1.3.0
	 * @return array $user_roles
	 */
	public static function get_user_roles() {
		global $wp_roles;

		$index      = 0;
		$user_roles = array();
		$roles_name = $wp_roles->get_names();

		if ( $roles_name ) {
			foreach ( $roles_name as $key => $role ) {
				$user_roles[ $key ]['id']   = $role;
				$user_roles[ $key ]['name'] = $role;
			}
			$index++;
		}
		return $user_roles;
	}

	/**
	 * Returns list of all user groups.
	 *
	 * @since 1.1.3.0
	 * @return array $suer_groups
	 */
	public static function get_user_groups() {
		$user_groups = array();
		$group_args  = array(
			'posts_per_page' => -1,
			'taxonomy'       => 'b2be-user-groups',
			'hide_empty'     => false,
		);

		$groups = get_terms( $group_args );
		if ( $groups ) {
			foreach ( $groups as $key => $group ) {
				$user_groups[ $key ]['id']   = $group->name;
				$user_groups[ $key ]['name'] = $group->name;
			}
		}
		return $user_groups;
	}

	/**
	 * Returns country list
	 *
	 * @since 1.1.4.0
	 * @return array
	 */
	public static function get_country_list() {
		return array(
			array(
				'id'   => 'af',
				'name' => 'Afghanistan',
			),
			array(
				'id'   => 'al',
				'name' => 'Albania',
			),
			array(
				'id'   => 'dz',
				'name' => 'Algeria',
			),
			array(
				'id'   => 'as',
				'name' => 'American Samoa',
			),
			array(
				'id'   => 'ad',
				'name' => 'Andorra',
			),
			array(
				'id'   => 'ao',
				'name' => 'Angola',
			),
			array(
				'id'   => 'ai',
				'name' => 'Anguilla',
			),
			array(
				'id'   => 'ag',
				'name' => 'Antigua and Barbuda',
			),
			array(
				'id'   => 'ar',
				'name' => 'Argentina',
			),
			array(
				'id'   => 'am',
				'name' => 'Armenia',
			),
			array(
				'id'   => 'aw',
				'name' => 'Aruba',
			),
			array(
				'id'   => 'au',
				'name' => 'Australia',
			),
			array(
				'id'   => 'at',
				'name' => 'Austria',
			),
			array(
				'id'   => 'az',
				'name' => 'Azerbaijan',
			),
			array(
				'id'   => 'ac',
				'name' => 'Ascension Island',
			),
			array(
				'id'   => 'bs',
				'name' => 'Bahamas',
			),
			array(
				'id'   => 'bh',
				'name' => 'Bahrain',
			),
			array(
				'id'   => 'bd',
				'name' => 'Bangladesh',
			),
			array(
				'id'   => 'bb',
				'name' => 'Barbados',
			),
			array(
				'id'   => 'by',
				'name' => 'Belarus',
			),
			array(
				'id'   => 'be',
				'name' => 'Belgium',
			),
			array(
				'id'   => 'bz',
				'name' => 'Belize',
			),
			array(
				'id'   => 'bj',
				'name' => 'Benin',
			),
			array(
				'id'   => 'bm',
				'name' => 'Bermuda',
			),
			array(
				'id'   => 'bt',
				'name' => 'Bhutan',
			),
			array(
				'id'   => 'bo',
				'name' => 'Bolivia',
			),
			array(
				'id'   => 'bq',
				'name' => 'Bonaire, Sint Eustatius and Saba',
			),
			array(
				'id'   => 'ba',
				'name' => 'Bosnia and Herzegovina',
			),
			array(
				'id'   => 'bw',
				'name' => 'Botswana',
			),
			array(
				'id'   => 'br',
				'name' => 'Brazil',
			),
			array(
				'id'   => 'bn',
				'name' => 'Brunei',
			),
			array(
				'id'   => 'bg',
				'name' => 'Bulgaria',
			),
			array(
				'id'   => 'bf',
				'name' => 'Burkina Faso',
			),
			array(
				'id'   => 'bi',
				'name' => 'Burundi',
			),
			array(
				'id'   => 'kh',
				'name' => 'Cambodia',
			),
			array(
				'id'   => 'cm',
				'name' => 'Cameroon',
			),
			array(
				'id'   => 'ca',
				'name' => 'Canada',
			),
			array(
				'id'   => 'cv',
				'name' => 'Cape Verde',
			),
			array(
				'id'   => 'ky',
				'name' => 'Cayman Islands',
			),
			array(
				'id'   => 'cf',
				'name' => 'Central African Republic',
			),
			array(
				'id'   => 'td',
				'name' => 'Chad',
			),
			array(
				'id'   => 'cl',
				'name' => 'Chile',
			),
			array(
				'id'   => 'cn',
				'name' => 'China',
			),
			array(
				'id'   => 'co',
				'name' => 'Colombia',
			),
			array(
				'id'   => 'km',
				'name' => 'Comoros',
			),
			array(
				'id'   => 'ck',
				'name' => 'Cook Islands',
			),
			array(
				'id'   => 'cr',
				'name' => 'Costa Rica',
			),
			array(
				'id'   => 'hr',
				'name' => 'Croatia',
			),
			array(
				'id'   => 'cu',
				'name' => 'Cuba',
			),
			array(
				'id'   => 'cw',
				'name' => 'Curacao',
			),
			array(
				'id'   => 'cy',
				'name' => 'Cyprus',
			),
			array(
				'id'   => 'cz',
				'name' => 'Czechia',
			),
			array(
				'id'   => 'cd',
				'name' => 'Democratic Republic of the Congo',
			),
			array(
				'id'   => 'ck',
				'name' => 'Denmark',
			),
			array(
				'id'   => 'dj',
				'name' => 'Djibouti',
			),
			array(
				'id'   => 'dm',
				'name' => 'Dominica',
			),
			array(
				'id'   => 'do',
				'name' => 'Dominican Republic',
			),
			array(
				'id'   => 'tl',
				'name' => 'East Timor',
			),
			array(
				'id'   => 'ec',
				'name' => 'Ecuador',
			),
			array(
				'id'   => 'eg',
				'name' => 'Egypt',
			),
			array(
				'id'   => 'sv',
				'name' => 'El Salvador',
			),
			array(
				'id'   => 'gq',
				'name' => 'Equatorial Guinea',
			),
			array(
				'id'   => 'er',
				'name' => 'Eritrea',
			),
			array(
				'id'   => 'ee',
				'name' => 'Estonia',
			),
			array(
				'id'   => 'et',
				'name' => 'Ethiopia',
			),
			array(
				'id'   => 'fo',
				'name' => 'Faroe Islands',
			),
			array(
				'id'   => 'fj',
				'name' => 'Fiji',
			),
			array(
				'id'   => 'fi',
				'name' => 'Finland',
			),
			array(
				'id'   => 'fr',
				'name' => 'France',
			),
			array(
				'id'   => 'gf',
				'name' => 'French Guiana',
			),
			array(
				'id'   => 'pf',
				'name' => 'French Polynesia',
			),
			array(
				'id'   => 'ga',
				'name' => 'Gabon',
			),
			array(
				'id'   => 'gm',
				'name' => 'Gambia',
			),
			array(
				'id'   => 'ge',
				'name' => 'Georgia',
			),
			array(
				'id'   => 'de',
				'name' => 'Germany',
			),
			array(
				'id'   => 'gh',
				'name' => 'Ghana',
			),
			array(
				'id'   => 'gi',
				'name' => 'Gibraltar',
			),
			array(
				'id'   => 'gr',
				'name' => 'Greece',
			),
			array(
				'id'   => 'gl',
				'name' => 'Greenland',
			),
			array(
				'id'   => 'gd',
				'name' => 'Grenada',
			),
			array(
				'id'   => 'gp',
				'name' => 'Guadeloupe',
			),
			array(
				'id'   => 'gu',
				'name' => 'Guam',
			),
			array(
				'id'   => 'gt',
				'name' => 'Guatemala',
			),
			array(
				'id'   => 'gn',
				'name' => 'Guinea',
			),
			array(
				'id'   => 'gw',
				'name' => 'Guinea-Bissau',
			),
			array(
				'id'   => 'gy',
				'name' => 'Guyana',
			),
			array(
				'id'   => 'ht',
				'name' => 'Haiti',
			),
			array(
				'id'   => 'hn',
				'name' => 'Honduras',
			),
			array(
				'id'   => 'hk',
				'name' => 'Hong Kong',
			),
			array(
				'id'   => 'hu',
				'name' => 'Hungary',
			),
			array(
				'id'   => 'is',
				'name' => 'Iceland',
			),
			array(
				'id'   => 'in',
				'name' => 'India',
			),
			array(
				'id'   => 'id',
				'name' => 'Indonesia',
			),
			array(
				'id'   => 'ir',
				'name' => 'Iran',
			),
			array(
				'id'   => 'iq',
				'name' => 'Iraq',
			),
			array(
				'id'   => 'ie',
				'name' => 'Ireland',
			),
			array(
				'id'   => 'il',
				'name' => 'Israel',
			),
			array(
				'id'   => 'it',
				'name' => 'Italy',
			),
			array(
				'id'   => 'ci',
				'name' => 'Ivory Coast',
			),
			array(
				'id'   => 'jm',
				'name' => 'Jamaica',
			),
			array(
				'id'   => 'jp',
				'name' => 'Japan',
			),
			array(
				'id'   => 'jo',
				'name' => 'Jordan',
			),
			array(
				'id'   => 'kz',
				'name' => 'Kazakhstan',
			),
			array(
				'id'   => 'ke',
				'name' => 'Kenya',
			),
			array(
				'id'   => 'ki',
				'name' => 'Kiribati',
			),
			array(
				'id'   => 'xk',
				'name' => 'Kosovo',
			),
			array(
				'id'   => 'kw',
				'name' => 'Kuwait',
			),
			array(
				'id'   => 'kg',
				'name' => 'Kyrgyzstan',
			),
			array(
				'id'   => 'la',
				'name' => 'Laos',
			),
			array(
				'id'   => 'lv',
				'name' => 'Latvia',
			),
			array(
				'id'   => 'lb',
				'name' => 'Lebanon',
			),
			array(
				'id'   => 'ls',
				'name' => 'Lesotho',
			),
			array(
				'id'   => 'lr',
				'name' => 'Liberia',
			),
			array(
				'id'   => 'ly',
				'name' => 'Libya',
			),
			array(
				'id'   => 'li',
				'name' => 'Liechtenstein',
			),
			array(
				'id'   => 'lt',
				'name' => 'Lithuania',
			),
			array(
				'id'   => 'lu',
				'name' => 'Luxembourg',
			),
			array(
				'id'   => 'mo',
				'name' => 'Macau',
			),
			array(
				'id'   => 'mk',
				'name' => 'Macedonia',
			),
			array(
				'id'   => 'mg',
				'name' => 'Madagascar',
			),
			array(
				'id'   => 'mw',
				'name' => 'Malawi',
			),
			array(
				'id'   => 'my',
				'name' => 'Malaysia',
			),
			array(
				'id'   => 'mv',
				'name' => 'Maldives',
			),
			array(
				'id'   => 'ml',
				'name' => 'Mali',
			),
			array(
				'id'   => 'mt',
				'name' => 'Malta',
			),
			array(
				'id'   => 'mh',
				'name' => 'Marshall Islands',
			),
			array(
				'id'   => 'mq',
				'name' => 'Martinique',
			),
			array(
				'id'   => 'mr',
				'name' => 'Mauritania',
			),
			array(
				'id'   => 'mu',
				'name' => 'Mauritius',
			),
			array(
				'id'   => 'yt',
				'name' => 'Mayotte',
			),
			array(
				'id'   => 'mx',
				'name' => 'Mexico',
			),
			array(
				'id'   => 'fm',
				'name' => 'Micronesia',
			),
			array(
				'id'   => 'md',
				'name' => 'Moldova',
			),
			array(
				'id'   => 'mc',
				'name' => 'Monaco',
			),
			array(
				'id'   => 'mn',
				'name' => 'Mongolia',
			),
			array(
				'id'   => 'me',
				'name' => 'Montenegro',
			),
			array(
				'id'   => 'ms',
				'name' => 'Montserrat',
			),
			array(
				'id'   => 'ma',
				'name' => 'Morocco',
			),
			array(
				'id'   => 'mz',
				'name' => 'Mozambique',
			),
			array(
				'id'   => 'mm',
				'name' => 'Myanmar',
			),
			array(
				'id'   => 'na',
				'name' => 'Namibia',
			),
			array(
				'id'   => 'nr',
				'name' => 'Nauru',
			),
			array(
				'id'   => 'np',
				'name' => 'Nepal',
			),
			array(
				'id'   => 'nl',
				'name' => 'Netherlands',
			),
			array(
				'id'   => 'nc',
				'name' => 'New Caledonia',
			),
			array(
				'id'   => 'nz',
				'name' => 'New Zealand',
			),
			array(
				'id'   => 'ni',
				'name' => 'Nicaragua',
			),
			array(
				'id'   => 'ne',
				'name' => 'Niger',
			),
			array(
				'id'   => 'ng',
				'name' => 'Nigeria',
			),
			array(
				'id'   => 'mp',
				'name' => 'Northern Mariana Islands',
			),
			array(
				'id'   => 'no',
				'name' => 'Norway',
			),
			array(
				'id'   => 'om',
				'name' => 'Oman',
			),
			array(
				'id'   => 'pk',
				'name' => 'Pakistan',
			),
			array(
				'id'   => 'pw',
				'name' => 'Palau',
			),
			array(
				'id'   => 'ps',
				'name' => 'Palestinian Territory',
			),
			array(
				'id'   => 'pa',
				'name' => 'Panama',
			),
			array(
				'id'   => 'pg',
				'name' => 'Papua New Guinea',
			),
			array(
				'id'   => 'py',
				'name' => 'Paraguay',
			),
			array(
				'id'   => 'pe',
				'name' => 'Peru',
			),
			array(
				'id'   => 'ph',
				'name' => 'Philippines',
			),
			array(
				'id'   => 'pl',
				'name' => 'Poland',
			),
			array(
				'id'   => 'pt',
				'name' => 'Portugal',
			),
			array(
				'id'   => 'pr',
				'name' => 'Puerto Rico',
			),
			array(
				'id'   => 'qa',
				'name' => 'Qatar',
			),
			array(
				'id'   => 'cg',
				'name' => 'Republic Of The Congo',
			),
			array(
				'id'   => 'ro',
				'name' => 'Romania',
			),
			array(
				'id'   => 'ru',
				'name' => 'Russia',
			),
			array(
				'id'   => 'rw',
				'name' => 'Rwanda',
			),
			array(
				'id'   => 're',
				'name' => 'Réunion Island',
			),
			array(
				'id'   => 'kn',
				'name' => 'Saint Kitts and Nevis',
			),
			array(
				'id'   => 'lc',
				'name' => 'Saint Lucia',
			),
			array(
				'id'   => 'pm',
				'name' => 'Saint Pierre and Miquelon',
			),
			array(
				'id'   => 'vc',
				'name' => 'Saint Vincent and The Grenadines',
			),
			array(
				'id'   => 'ws',
				'name' => 'Samoa',
			),
			array(
				'id'   => 'sm',
				'name' => 'San Marino',
			),
			array(
				'id'   => 'st',
				'name' => 'Sao Tome and Principe',
			),
			array(
				'id'   => 'sa',
				'name' => 'Saudi Arabia',
			),
			array(
				'id'   => 'sn',
				'name' => 'Senegal',
			),
			array(
				'id'   => 'rs',
				'name' => 'Serbia',
			),
			array(
				'id'   => 'sc',
				'name' => 'Seychelles',
			),
			array(
				'id'   => 'sl',
				'name' => 'Sierra Leone',
			),
			array(
				'id'   => 'sg',
				'name' => 'Singapore',
			),
			array(
				'id'   => 'sx',
				'name' => 'Sint Maarten (Dutch Part]',
			),
			array(
				'id'   => 'sk',
				'name' => 'Slovakia',
			),
			array(
				'id'   => 'si',
				'name' => 'Slovenia',
			),
			array(
				'id'   => 'sb',
				'name' => 'Solomon Islands',
			),
			array(
				'id'   => 'so',
				'name' => 'Somalia',
			),
			array(
				'id'   => 'za',
				'name' => 'South Africa',
			),
			array(
				'id'   => 'kr',
				'name' => 'South Korea',
			),
			array(
				'id'   => 'ss',
				'name' => 'South Sudan',
			),
			array(
				'id'   => 'es',
				'name' => 'Spain',
			),
			array(
				'id'   => 'lk',
				'name' => 'Sri Lanka',
			),
			array(
				'id'   => 'sd',
				'name' => 'Sudan',
			),
			array(
				'id'   => 'sr',
				'name' => 'Suriname',
			),
			array(
				'id'   => 'sz',
				'name' => 'Swaziland',
			),
			array(
				'id'   => 'se',
				'name' => 'Sweden',
			),
			array(
				'id'   => 'ch',
				'name' => 'Switzerland',
			),
			array(
				'id'   => 'sy',
				'name' => 'Syria',
			),
			array(
				'id'   => 'tw',
				'name' => 'Taiwan',
			),
			array(
				'id'   => 'tj',
				'name' => 'Tajikistan',
			),
			array(
				'id'   => 'tz',
				'name' => 'Tanzania',
			),
			array(
				'id'   => 'th',
				'name' => 'Thailand',
			),
			array(
				'id'   => 'tg',
				'name' => 'Togo',
			),
			array(
				'id'   => 'to',
				'name' => 'Tonga',
			),
			array(
				'id'   => 'tt',
				'name' => 'Trinidad and Tobago',
			),
			array(
				'id'   => 'tn',
				'name' => 'Tunisia',
			),
			array(
				'id'   => 'tr',
				'name' => 'Turkey',
			),
			array(
				'id'   => 'tm',
				'name' => 'Turkmenistan',
			),
			array(
				'id'   => 'tc',
				'name' => 'Turks and Caicos Islands',
			),
			array(
				'id'   => 'ug',
				'name' => 'Uganda',
			),
			array(
				'id'   => 'ua',
				'name' => 'Ukraine',
			),
			array(
				'id'   => 'ae',
				'name' => 'United Arab Emirates',
			),
			array(
				'id'   => 'gb',
				'name' => 'United Kingdom',
			),
			array(
				'id'   => 'us',
				'name' => 'United States',
			),
			array(
				'id'   => 'uy',
				'name' => 'Uruguay',
			),
			array(
				'id'   => 'uz',
				'name' => 'Uzbekistan',
			),
			array(
				'id'   => 'vu',
				'name' => 'Vanuatu',
			),
			array(
				'id'   => 've',
				'name' => 'Venezuela',
			),
			array(
				'id'   => 'vn',
				'name' => 'Vietnam',
			),
			array(
				'id'   => 'vg',
				'name' => 'Virgin Islands, British',
			),
			array(
				'id'   => 'vi',
				'name' => 'Virgin Islands, US',
			),
			array(
				'id'   => 'ye',
				'name' => 'Yemen',
			),
			array(
				'id'   => 'zm',
				'name' => 'Zambia',
			),
			array(
				'id'   => 'zw',
				'name' => 'Zimbabwe',
			),
		);
	}

}
