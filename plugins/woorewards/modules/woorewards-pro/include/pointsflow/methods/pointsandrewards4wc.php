<?php

namespace LWS\WOOREWARDS\PRO\PointsFlow\Methods;

// don't call the file directly
if (!defined('ABSPATH')) exit();
require_once LWS_WOOREWARDS_PRO_INCLUDES . '/pointsflow/methods/metakey.php';

/** WP Swings
 * Not the official one by WooCommerce
 * @see https://wordpress.org/plugins/points-and-rewards-for-woocommerce/ */
class PointsAndRewards4WC extends \LWS\WOOREWARDS\PRO\PointsFlow\Methods\MetaKey
{
	/** @return (array) the json that will be send,
	 * An array with each entries as {email, points} */
	public function export($value, $arg)
	{
		return parent::export('wps_wpr_points', $arg);
	}

	/** @return (string) human readable name */
	public function getTitle()
	{
		return __("Points and Rewards for WooCommerce (by WP Swings)", 'woorewards-pro');
	}
}
