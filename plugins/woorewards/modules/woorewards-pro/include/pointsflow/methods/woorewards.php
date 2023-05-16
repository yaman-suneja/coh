<?php
namespace LWS\WOOREWARDS\PRO\PointsFlow\Methods;

// don't call the file directly
if( !defined( 'ABSPATH' ) ) exit();

class WooRewards extends \LWS\WOOREWARDS\PRO\PointsFlow\ExportMethod
{
	/** @return (array) the json that will be send,
	 * An array with each entries as {email, points} */
	public function export($value, $arg)
	{
		$stackName = $value;
		if( \class_exists('\LWS\WOOREWARDS\PRO\Core\Pool') )
		{
			if( $pool = \LWS\WOOREWARDS\PRO\Core\Pool::getOrLoad($value, false) )
				$stackName = $pool->getStackId();
		}

		global $wpdb;
		$sql = <<<EOT
SELECT user_email as `email`, meta_value as `points`
FROM {$wpdb->users}
LEFT JOIN {$wpdb->usermeta} ON ID=user_id AND `meta_key`=%s
EOT;
		return $wpdb->get_results($wpdb->prepare($sql, 'lws_wre_points_'.$stackName));
	}

	/** @return (string) human readable name */
	public function getTitle()
	{
		return __("MyRewards", 'woorewards-pro');
	}

	/** @return (bool) appear in method combobox */
	public function isVisible()
	{
		return false;
	}
}
