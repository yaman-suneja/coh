<?php
namespace LWS\WOOREWARDS\PRO\PointsFlow\Methods;

// don't call the file directly
if( !defined( 'ABSPATH' ) ) exit();

/** From User Meta database table, only need a meta_key */
class MetaKey extends \LWS\WOOREWARDS\PRO\PointsFlow\ExportMethod
{
	/** @return (array) the json that will be send,
	 * An array with each entries as {email, points} */
	public function export($value, $arg)
	{
		global $wpdb;
		$sql = <<<EOT
SELECT user_email as `email`, meta_value as `points` FROM {$wpdb->usermeta}
INNER JOIN {$wpdb->users} ON ID=user_id
WHERE `meta_key`=%s
EOT;
		return $wpdb->get_results($wpdb->prepare($sql, $value));
	}

	/** @return (string) human readable name */
	public function getTitle()
	{
		return __("By User Meta Key", 'woorewards-pro');
	}

	/** @return (bool) appear in method combobox */
	public function isVisible()
	{
		return get_class() != get_class($this);
	}
}
