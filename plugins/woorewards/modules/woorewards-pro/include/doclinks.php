<?php

namespace LWS\WOOREWARDS\PRO;

// don't call the file directly
if (!defined('ABSPATH')) exit();

/** All Documentation Links*/
class DocLinks
{
	const FALLBACK = 'home';

	public static $doclinks = array(
		'achievements'   => "https://plugins.longwatchstudio.com/docs/woorewards-4/features/achievements/",
		'api'            => "https://plugins.longwatchstudio.com/docs/woorewards-4/api/",
		'badges'         => "https://plugins.longwatchstudio.com/docs/woorewards-4/features/badges/",
		'birthday'       => "https://plugins.longwatchstudio.com/docs/woorewards-4/points/birthday/",
		'customers'      => "https://plugins.longwatchstudio.com/docs/woorewards-4/customers-management/",
		'emails'         => "https://plugins.longwatchstudio.com/docs/woorewards-4/appearance/emails/",
		'home'           => "https://plugins.longwatchstudio.com/docs/woorewards-4/",
		'points'         => "https://plugins.longwatchstudio.com/docs/woorewards-4/points-and-rewards-systems/points/",
		'points-ex'      => "https://plugins.longwatchstudio.com/docs/woorewards-4/points-and-rewards-systems/#points-expiration",
		'pools'          => "https://plugins.longwatchstudio.com/docs/woorewards-4/points-and-rewards-systems/",
		'pools-as'       => "https://plugins.longwatchstudio.com/docs/woorewards-4/points-and-rewards-systems/#advanced-settings",
		'pools-cur'      => "https://plugins.longwatchstudio.com/docs/woorewards-4/points-and-rewards-systems/#points-currency",
		'referral'       => "https://plugins.longwatchstudio.com/docs/woorewards-4/features/referral-sponsorship/",
		'rewards'        => "https://plugins.longwatchstudio.com/docs/woorewards-4/points-and-rewards-systems/rewards/",
		'shortcodes'     => "https://plugins.longwatchstudio.com/docs/woorewards-4/wr-shortcodes/",
		'social'         => "https://plugins.longwatchstudio.com/docs/woorewards-4/legacy/widgets/social-share/",
		'wc-account'     => "https://plugins.longwatchstudio.com/docs/woorewards-4/appearance/woocommerce-integration/my-account/",
		'wc-order-email' => "https://plugins.longwatchstudio.com/docs/woorewards-4/appearance/woocommerce-integration/points-information-new-order/",
		'legacy-oc'      => "https://plugins.longwatchstudio.com/docs/woorewards-4/widgets/owned-coupons/",
	);

	static function get($index=false, $escape = true)
	{
		if (!($index && isset(self::$doclinks[$index])))
			$index = self::FALLBACK;
		if ($escape)
			return \esc_attr(self::$doclinks[$index]);
		else
			return self::$doclinks[$index];
	}

	static function toFields()
	{
		$fields = array();
		$prefix = (\get_class() . ':');
		foreach (self::$doclinks as $key => $url) {
			$fields[$key] = array(
				'id'    => $prefix . $key,
				'title' => $key,
				'type'  => 'custom',
				'extra' => array(
					'gizmo'   => true,
					'content' => sprintf('<a href="%s" target="_blank">%s</a>', \esc_attr($url), $url),
				),
			);
		}
		return $fields;
	}
}