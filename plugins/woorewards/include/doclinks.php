<?php

namespace LWS\WOOREWARDS;

// don't call the file directly
if (!defined('ABSPATH')) exit();

/** All Documentation Links*/
class DocLinks
{
	const FALLBACK = 'home';

	public static $doclinks = array(
		'adv-features'   => "https://plugins.longwatchstudio.com/docs/woorewards-4/features/advanced-features/",
		'customers'      => "https://plugins.longwatchstudio.com/docs/woorewards-4/customers-management/",
		'disp-points'    => "https://plugins.longwatchstudio.com/docs/woorewards-4/legacy/widgets/display-points/",
		'home'           => "https://plugins.longwatchstudio.com/docs/woorewards-4/",
		'points'         => "https://plugins.longwatchstudio.com/docs/woorewards-4/points-and-rewards-systems/points/",
		'pools'          => "https://plugins.longwatchstudio.com/docs/woorewards-4/points-and-rewards-systems/",
		'referral'       => "https://plugins.longwatchstudio.com/docs/woorewards-4/features/referral-sponsorship/",
		'rewards'        => "https://plugins.longwatchstudio.com/docs/woorewards-4/points-and-rewards-systems/rewards/",
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