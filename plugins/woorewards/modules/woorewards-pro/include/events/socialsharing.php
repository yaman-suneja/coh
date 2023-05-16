<?php
namespace LWS\WOOREWARDS\PRO\Events;

// don't call the file directly
if( !defined( 'ABSPATH' ) ) exit();

/** Earn points for sharing on social networks.
 * Use our local sharing widget/shortcode */
class SocialSharing extends \LWS\WOOREWARDS\Abstracts\Event
{
	function getInformation()
	{
		return array_merge(parent::getInformation(), array(
			'icon'  => 'lws-icon-a-share',
			'short' => __("The customer will earn points sharing a link on social media.", 'woorewards-pro'),
			'help'  => __("Warning, there is no actual way to verify if a user goes through with the social share", 'woorewards-pro'),
		));
	}

	function isRuleSupportedCooldown() { return true; }

	function getData()
	{
		$prefix = $this->getDataKeyPrefix();
		$data = parent::getData();
		$data[$prefix.'socials'] = base64_encode(json_encode($this->getSocials()));
		return $data;
	}

	function getForm($context='editlist')
	{
		$prefix = $this->getDataKeyPrefix();
		$form = parent::getForm($context);
		$form .= $this->getFieldsetBegin(2, __("Social network", 'woorewards-pro'));

		// The social networks
		$label   = _x("Social network", "Social network sharing Event", 'woorewards-pro');
		$form .= "<div class='lws-$context-opt-title label'>$label</div>";
		$form .= "<div class='lws-$context-opt-input value'>";
		$form .= \LWS\Adminpanel\Pages\Field\LacChecklist::compose($prefix.'socials', array(
			'comprehensive' => true,
			'source' => \LWS\WOOREWARDS\PRO\Core\Socials::instance()->asDataSource(),
			'value' => $this->getSocials(),
			'class' => 'above',
		));
		$form .= "</div>";

		$form .= $this->getFieldsetEnd(2);
		return $form;
	}

	function submit($form=array(), $source='editlist')
	{
		$prefix = $this->getDataKeyPrefix();
		$values = \apply_filters('lws_adminpanel_arg_parse', array(
			'post'     => ($source == 'post'),
			'values'   => $form,
			'format'   => array(
				$prefix.'socials' => array('S'),
			),
			'defaults' => array(
				$prefix.'socials' => \LWS\WOOREWARDS\PRO\Core\Socials::instance()->getSupportedNetworks()
			),
			'labels'   => array(
				$prefix.'socials' => __("Social networks", 'woorewards-pro'),
			)
		));
		if( !(isset($values['valid']) && $values['valid']) )
			return isset($values['error']) ? $values['error'] : false;

		$valid = parent::submit($form, $source);
		if( $valid === true )
		{
			$this->setSocials($values['values'][$prefix.'socials']);
		}
		return $valid;
	}

	public function getSocials()
	{
		return isset($this->socials) ? $this->socials : \LWS\WOOREWARDS\PRO\Core\Socials::instance()->getSupportedNetworks();
	}

	public function setSocials($socials)
	{
		if( !is_array($socials) )
			$socials = @json_decode(@base64_decode($socials));
		if( is_array($socials) )
			$this->socials = $socials;
		return $this;
	}

	/** Inhereted Event already instanciated from WP_Post, $this->id is availble. It is up to you to load any extra configuration. */
	protected function _fromPost(\WP_Post $post)
	{
		$this->setSocials(\get_post_meta($post->ID, 'woorewards_socials', true));
		return $this;
	}

	/** Event already saved as WP_Post, $this->id is availble. It is up to you to save any extra configuration. */
	protected function _save($id)
	{
		\update_post_meta($id, 'woorewards_socials', $this->getSocials());
		return $this;
	}

	/** @return a human readable type for UI */
	public function getDisplayType()
	{
		return _x("Share on social networks", "getDisplayType", 'woorewards-pro');
	}

	/** Add hook to grab events and add points. */
	protected function _install()
	{
		\add_action('wp_ajax_lws_woorewards_social_sharing', array($this, 'trigger'));
	}

	/** expect an ajax request */
	function trigger()
	{
		$userId = \get_current_user_id();
		$social = isset($_GET['s']) ? \sanitize_key($_GET['s']) : false;
		$pageHash = isset($_GET['p']) ? \sanitize_key($_GET['p']) : false;

		if( $userId && $social
		&& in_array($social, $this->getSocials())
		&& \check_ajax_referer('lws_woorewards_socialshare', 'nonce') )
		{
			if (!$this->isCool($userId))
				return;
			if (!\LWS\WOOREWARDS\PRO\Core\Socials::instance()->isPageUntouched('lws_woorewards_socialsharing_once_'.$this->getId(), $userId, $pageHash))
				return;

			if( $points = \apply_filters('trigger_'.$this->getType(), 1, $this, $userId, $social, $pageHash) )
			{
				$name = \LWS\WOOREWARDS\PRO\Core\Socials::instance()->getLabel($social);
				$reason = \LWS\WOOREWARDS\Core\Trace::byReason(array("Sharing on '%s'", $name), 'woorewards-pro');
				$this->addPoint($userId, $reason, $points);
			}
		}
	}

	/** Never call, only to have poedit/wpml able to extract the sentance. */
	private function poeditDeclare()
	{
		__("Sharing on '%s'", 'woorewards-pro');
	}

	function getDescription($context='backend')
	{
		$names = \LWS\WOOREWARDS\PRO\Core\Socials::instance()->getLabel($this->getSocials(), ', ');
		$str = sprintf(__("User shares on %s", 'woorewards-pro'), $names);
		return $str;
	}

	public function getCategories()
	{
		return array_merge(parent::getCategories(), array(
			'social' => __("Social network", 'woorewards-pro')
		));
	}
}
