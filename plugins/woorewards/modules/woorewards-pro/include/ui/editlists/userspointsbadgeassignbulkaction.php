<?php
namespace LWS\WOOREWARDS\PRO\Ui\Editlists;

// don't call the file directly
if( !defined( 'ABSPATH' ) ) exit();

/** Assign a badge to a selection of user */
class UsersPointsBadgeAssignBulkAction extends \LWS\Adminpanel\EditList\Action
{
	private $key = 'badge_add';

	function input()
	{
		$str = "<label for='{$this->key}' class='wr-user-badge-bulk'>".__("Assign badge", 'woorewards-pro')."</label>";
		$str .= " <input name='{$this->key}' class='lac_select lws-ignore-confirm' data-ajax='lws_woorewards_badge_list'>";
		return $str;
	}

	function apply($user_ids)
	{
		$badgeId = isset($_POST[$this->key]) ? \absint($_POST[$this->key]) : '';
		if( !$badgeId	)
			return __("Please select a badge", 'woorewards-pro');

		$badge = new \LWS\WOOREWARDS\PRO\Core\Badge($badgeId);
		if( !$badge->isValid() )
			return __("The badge seems to not exist anymore", 'woorewards-pro');

		$c = 0;
		foreach( $user_ids as $user_id )
		{
			if( $badge->assignTo($user_id, 'bulk') )
				++$c;
		}

		$msg = _n("%d badge assigned", "%d badges assigned", $c, 'woorewards-pro');
		\lws_admin_add_notice_once('lws-wre-users-badge-add', sprintf($msg, $c), array('level'=>'success'));
		return true;
	}

}

?>