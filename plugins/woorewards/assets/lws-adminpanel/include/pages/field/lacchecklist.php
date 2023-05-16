<?php
namespace LWS\Adminpanel\Pages\Field;
if( !defined( 'ABSPATH' ) ) exit();




/** Expose an enhanced checklist editor
 * Data can be preloaded or read from ajax call.
 * All extra are optionnal.
 * @param $extra['ajax'] the ajax action to grab data list.
 * @param $extra['source'] the preload data list as array of array('value'=>…, 'label'=>…, 'html'=>…)
 * 	value is the recorded value, label is displayed (and search string) to user in input field,
 * @param $extra['class'] css class list transfered to autocomplete wrapper.
 * @param $extra['name'] input name set to autocomplete wrapper input (in case label is relevant too).
 * @param $extra['shared'] defines if the input shares his source with other inputs of the same type.
 * @param $extra['minsearch'] the minimal search string length before ajax call instead of local options.
 * @param $extra['minoption'] if local filter result count is less or equal, ajax call (if any) is attempt.
 * @param $extra['delay'] hit key delay before search trigger (let user finish its term before loading).
 * @param $extra['minlength'] minimal input length before autocomplete starts (default 1).
 * @param $extra['placeholder'] is a input placeholder.
 * @param $extra['spec'] any value transfered as json base64 encoded to ajax.
 * @param $extra['value'] if is set, use this as input value, else try a get_option($id).
 * @param $extra['prebuild'] compute a source if source is omitted @see prebuild.
 * @param $extra['predefined'] precomputed values for extra @see predefined.
 *
 * @note soure is an array of object or array with value, label and optionnaly html for complex html item in unfold list.
 * It is recommended to have at least the selected value described in source.
 * @note if user entry is not found in preload source and an ajax is set, ajax will be call to complete source. */

class LacChecklist extends \LWS\Adminpanel\Pages\LAC
{
	private static $scriptAdded = false;

	public function __construct($id, $title, $extra=null)
	{
		parent::__construct($id, $title, $extra);
		add_action('admin_enqueue_scripts', array($this, 'script'), 9);
	}

	protected function html()
	{
		if( $this->isValid(true) )
		{
			$attrs = implode('', array(
				$this->getExtraAttr('sourceurl', 'data-sourceurl'),
				$this->getExtraAttr('ajax', 'data-ajax'),
				$this->getExtraCss('class', 'data-class'),
				$this->getExtraAttr('name', 'data-name'),
				$this->getExtraAttr('shared', 'data-shared'),
				$this->getExtraAttr('addlabel', 'data-addlabel'),
				$this->getExtraAttr('minsearch', 'data-minsearch'),
				$this->getExtraAttr('minoption', 'data-minoption'),
				$this->getExtraAttr('delay', 'data-delay'),
				$this->getExtraAttr('minlength', 'data-minlength')
			));
			$originalValue = $this->readOption(false);
			$value = base64_encode(json_encode($originalValue));
			$name = esc_attr($this->m_Id);
			$source = $this->data('source');
			$spec = $this->data('spec');
			$placeholder = __("Select...", LWS_ADMIN_PANEL_DOMAIN);
			$inputph = __("Search...", LWS_ADMIN_PANEL_DOMAIN);
			if( empty($source) && $this->hasExtra('prebuild') )
			{
				$source = $this->prebuild($originalValue, $this->hasExtra('spec', 'a') ? $this->extra['spec'] : array());
			}
			$this->script();
			$inputClass = $this->ignoreConfirm('lac_checklist');
			if ($ic = $this->getExtraValue('rootclass')) {
				$inputClass .= (' ' . $ic);
			}
			$id = isset($this->extra['id']) ? (" id='".\esc_attr($this->extra['id'])."'") : '';
			$input = <<<EOT
			<input class='{$inputClass}' name='{$name}' data-value='{$value}'$attrs$source$spec data-lw_name='{$name}'{$id}>
			<div class='lac-checklist-wrapper'>
				<div class='top-line'>
					<div class='selector'>
						<div class='anim-icon lws-icon-nav-down'></div>
						<div class='text'>{$placeholder}</div>
					</div>
					<div class='search-div'>
						<div class='search-btn lws-icon-search'></div>
						<div class='search-input' contenteditable='true' data-placeholder='{$inputph}'>
							<div class="lac-loading"></div>
						</div>
						<div class='reset-btn lws-icon-undo'></div>
					</div>
					<div class='close-btn lws-icon-checkmark'></div>
					<div class='result-list'></div>
					</div>
				<div class='values-list'></div>
				<div class='values'></div>
			</div>
EOT;
			return $input;

		}
	}

	public function script()
	{
		if (!self::$scriptAdded) {
			self::$scriptAdded = true;
			if (\did_action('admin_enqueue_scripts')) {
				\LWS\Adminpanel\Pages\LAC::modelScript();
				\wp_enqueue_script('lws-lac-checklist');
			} else {
				\add_action('admin_enqueue_scripts', function() {
					\LWS\Adminpanel\Pages\LAC::modelScript();
					\wp_enqueue_script('lws-lac-checklist');
				});
			}
		}
	}
}
