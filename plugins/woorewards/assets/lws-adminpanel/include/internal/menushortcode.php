<?php
namespace LWS\Adminpanel\Internal;
if( !defined( 'ABSPATH' ) ) exit();

/** At least, add a menu item that manage shortcodes. */
class MenuShortcode extends \LWS\Adminpanel\Internal\MenuItems
{
	static public function register()
	{
		\add_filter('lws_admimpanel_menuitem_types', function($types) {
			$types['lws-adm-shortcode'] = new \LWS\Adminpanel\Internal\MenuShortcode();
			return $types;
		});
	}

	protected function getTitle($item=false)
	{
		$title = __('Shortcode item', LWS_ADMIN_PANEL_DOMAIN);
		if ($item && $item->classes) {
			if (\in_array('lws-admpnl-logged-only', $item->classes))
				$title .= sprintf(' (%s)', __("Logged", LWS_ADMIN_PANEL_DOMAIN));
			elseif (\in_array('lws-admpnl-guest-only', $item->classes))
				$title .= sprintf(' (%s)', __("Guest", LWS_ADMIN_PANEL_DOMAIN));
		}
		return $title;
	}

	protected function finalizeSetup(&$item, $meta, $subtype='')
	{
		if (!\is_array($item->classes))
			$item->classes = array();

		if (isset($meta['lws_shortcode_visitor'])) {
			if ('guest' == $meta['lws_shortcode_visitor'])
				$item->classes[] = 'lws-admpnl-guest-only';
			elseif ('logged' == $meta['lws_shortcode_visitor'])
				$item->classes[] = 'lws-admpnl-logged-only';
		}
	}

	protected function acceptedMeta()
	{
		return array(
			'lws_shortcode_visitor' => false,
		);
	}

	protected function getForm($index, $inputClass)
	{
		$texts = array(
			'label'   => __("Item content", LWS_ADMIN_PANEL_DOMAIN),
			'ph'      => '[my_shortcode]',
			'visitor' => array(
				'label'  => __("Visitor type", LWS_ADMIN_PANEL_DOMAIN),
				'guest'  => __("Guest", LWS_ADMIN_PANEL_DOMAIN),
				'all'    => __("All", LWS_ADMIN_PANEL_DOMAIN),
				'logged' => __("Logged", LWS_ADMIN_PANEL_DOMAIN),
			),
		);

		return <<<EOT
<p class="setupdiv">
	<label class="howto">{$texts['label']}
		<textarea class="menu-item-title lws-required" name="menu-item[{$index}][menu-item-title]" placeholder="{$texts['ph']}" style="width:100%;"></textarea>
	</label>
</p>
<div class="visitortypediv">
	<ul class="add-menu-item-tabs"><li class="tabs">{$texts['visitor']['label']}</li></ul>
	<div class="wp-tab-panel tabs-panel-active"><ul>
		<li><label><input type="radio" value="" checked="" data-dft="on" class="{$inputClass}" name="lws_shortcode_visitor">{$texts['visitor']['all']}</label></li>
		<li><label><input type="radio" value="guest" class="{$inputClass}" name="lws_shortcode_visitor">{$texts['visitor']['guest']}</label></li>
		<li><label><input type="radio" value="logged" class="{$inputClass}" name="lws_shortcode_visitor">{$texts['visitor']['logged']}</label></li>
	</ul></div>
</div>
EOT;
	}
}