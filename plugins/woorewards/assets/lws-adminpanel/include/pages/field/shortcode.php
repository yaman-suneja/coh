<?php
namespace LWS\Adminpanel\Pages\Field;
if( !defined( 'ABSPATH' ) ) exit();

/** Extra expect:
 * 'shortcode' => an exemple string.
 * 'description' => string.
 * 'options' => array of array [option => desc] with arguments of shortcode.
 * 'flags' => an optional array passed to the hook applied on options array just before display.
 * 'style_url' => a link to the appearance/stygen page
 *  */
class Shortcode extends \LWS\Adminpanel\Pages\Field
{
	public function __construct($id='', $title='', $extra=null)
	{
		parent::__construct($id, $title, $extra);
		$this->gizmo = true;
	}

	public function input()
	{
		$id = $this->getExtraValue('id', $this->m_Id);
		$content = "<div class='lws-shortcode-description-wrapper' id='{$id}'>";
		$texts = array(
			'title' => __("Shortcode", LWS_ADMIN_PANEL_DOMAIN),
			'options' => __("Attributes", LWS_ADMIN_PANEL_DOMAIN),
			'desc' => __("Description", LWS_ADMIN_PANEL_DOMAIN),
			'style' => __("Styling", LWS_ADMIN_PANEL_DOMAIN),
			'styledesc' => __("Customize the look of this shortcode", LWS_ADMIN_PANEL_DOMAIN),
		);

		if(isset($this->extra['shortcode']))
		{
			$title = $this->extra['shortcode'];
			if (\is_object($title))
				$title = $title->toText();
			$content .= <<<EOT
			<div class="shortcode-wrapper lws_ui_value_copy">
				<div class="title">{$texts['title']}</div>
				<div class="content">{$title}</div>
				<div class="copy-icon lws-icon-copy copy"></div>
			</div>
EOT;
		}

		if(isset($this->extra['description']))
		{
			if( \is_array($this->extra['description']) )
				$this->extra['description'] = \lws_array_to_html($this->extra['description']);
			$content .= <<<EOT
			<div class="description-wrapper">
				<div class="title">{$texts['desc']}</div>
				<div class="content">{$this->extra['description']}</div>
			</div>
EOT;
		}

		if(isset($this->extra['options']))
		{
			$code = \trim(explode(' ', $this->getExtraValue('shortcode', ''))[0], "[] \n\r\t\v\0");
			$options = \apply_filters('lws_adminpanel_field_shortcode_options', $this->extra['options'], $code, $this->getExtraValue('flags', array()));
			$optContent = '';
			foreach ($options as $option) {
				if (\is_array($option['desc'])) {
					$option['desc'] = \lws_array_to_html($option['desc']);
				}
				$optContent .= "<div class='name'>{$option['option']}</div>";
				if (isset($option['options']) && $option['options']) {
					$subopts = $option['options'];
					$optContent .= "<div class='desc-grid'>";
					$optContent .= "<div class='desc strong'>{$option['desc']}</div>";
					foreach ($subopts as $subopt) {
						if (\is_array($subopt)) {
							$optContent .= "<div class='sub-name'>{$subopt['option']}</div>";
							$optContent .= "<div class='sub-desc'>{$subopt['desc']}</div>";
						} else {
							$optContent .= "<div class='desc'>{$subopt['desc']}</div>";
						}
					}
					$optContent .= "</div>";
				} else {
					$optContent .= "<div class='desc'>{$option['desc']}</div>";
				}
				if (isset($option['example']) && $option['example']) {
					$example = $option['example'];
					if (\is_object($example))
						$example = $example->toText();

					$optContent .= "<div class='opt-example label'>" . __("Example", LWS_ADMIN_PANEL_DOMAIN) . "</div>";
					$optContent .= "<div class='opt-example value'>{$example}</div>";
				}
			}
			$content .= <<<EOT
			<div class="options-wrapper">
				<div class="title">{$texts['options']}</div>
				<div class="content">$optContent</div>
			</div>
EOT;
		}

		if(isset($this->extra['style_url']))
		{
			$content .= <<<EOT
			<div class="style-wrapper">
				<div class="title">{$texts['style']}</div>
				<div class="content"><a href="{$this->extra['style_url']}" target="_blank">{$texts['styledesc']}</a></div>
			</div>
EOT;
		}

		$content .= '</div>';

		echo $content;
	}
}
