<?php
namespace LWS\WOOREWARDS\PRO\Ui\Editlists;

// don't call the file directly
if( !defined( 'ABSPATH' ) ) exit();

/** Remove a badge from a selection of users */
class UsersPointsBadgeRemoveBulkAction extends \LWS\Adminpanel\EditList\Action
{
	private $key = 'badge_rem';

	function input()
	{
		$str = "<label class='wr-user-badge-bulk'>".__("Remove badge", 'woorewards-pro')."</label>";
		$str .= " <input name='{$this->key}' class='lac_select lws-ignore-confirm' data-ajax='lws_woorewards_badge_list'/>";
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
			if( $badge->removeFrom($user_id) )
				++$c;
		}

		$msg = _n("%d badge removed", "%d badges removed", $c, 'woorewards-pro');
		\lws_admin_add_notice_once('lws-wre-users-badge-rem', sprintf($msg, $c), array('level'=>'success'));
		return true;
	}

}

?>