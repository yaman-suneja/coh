<?php

namespace LWS\WOOREWARDS\PRO\Unlockables;

// don't call the file directly
if (!defined('ABSPATH')) exit();

/** Shared options through all kind of shop_coupon unlockable.
 * use @see FreeProduct @see Coupon
 *
 * Manage options:
 * * indivdual use (bool)
 * * exclude on sale items (bool)
 * * minimum order amount to use the shop coupon in an order (float) (!= min amount to earn points in an Event)
 */
trait T_DiscountOptions
{
	public function isIndividualUse()
	{
		return isset($this->individualUse) ? $this->individualUse : false;
	}

	public function isExcludeSaleItems()
	{
		return isset($this->excludeSaleItems) ? $this->excludeSaleItems : false;
	}

	public function getOrderMinimumAmount()
	{
		return isset($this->orderMinimumAmount) ? $this->orderMinimumAmount : '';
	}

	public function getOrderMaximumAmount()
	{
		return isset($this->orderMaximumAmount) ? $this->orderMaximumAmount : '';
	}

	public function isRelativeOrderMinimumAmount()
	{
		return isset($this->relativeMinimumAmount) ? $this->relativeMinimumAmount : false;
	}

	public function isRelativeOrderMaximumAmount()
	{
		return isset($this->relativeMaximumAmount) ? $this->relativeMaximumAmount : false;
	}

	public function getCouponExcerpt()
	{
		return isset($this->couponExcerpt) ? $this->couponExcerpt : '';
	}

	public function setIndividualUse($yes = false)
	{
		$this->individualUse = boolval($yes);
		return $this;
	}

	function getCouponCategoryIds()
	{
		return isset($this->couponCategoryIds) ? (array)$this->couponCategoryIds : array();
	}

	/** @param $categories (array|string) as string, it should be a json base64 encoded array. */
	function setCouponCategoryIds($cats=array())
	{
		if (!\is_array($cats))
			$cats = @json_decode(@base64_decode($cats));
		if (\is_array($cats))
			$this->couponCategoryIds = $cats;
		return $this;
	}

	public function setExcludeSaleItems($yes = false)
	{
		$this->excludeSaleItems = boolval($yes);
		return $this;
	}

	public function setOrderMinimumAmount($amount = 0.0)
	{
		$this->orderMinimumAmount = max(0.0, floatval(str_replace(',', '.', $amount)));
		if ($this->orderMinimumAmount == 0.0)
			$this->orderMinimumAmount = '';
		return $this;
	}

	public function setOrderMaximumAmount($amount = 0.0)
	{
		$this->orderMaximumAmount = max(0.0, floatval(str_replace(',', '.', $amount)));
		if ($this->orderMaximumAmount == 0.0)
			$this->orderMaximumAmount = '';
		return $this;
	}

	/** A relative Order Minimum Amount means the minimum is computed
	 * 	at coupon generation time add use
	 *  (fix discount amount + min order setting) as total order minimum amount.
	 *	@param $amount (string|bool|float) empty, float or false means not relative,
	 *	true or string 'on' or starting with the '+' char means relative. */
	public function setRelativeOrderMinimumAmount($amount = false)
	{
		if ($amount === true)
			$this->relativeMinimumAmount = true;
		else if ($amount === false)
			$this->relativeMinimumAmount = false;
		else if ($amount === 'on' || (strlen($amount) > 1 && substr(ltrim($amount), 0, 1) === '+'))
			$this->relativeMinimumAmount = true;
		else
			$this->relativeMinimumAmount = false;
		return $this;
	}

	public function setRelativeOrderMaximumAmount($amount = false)
	{
		if ($amount === true)
			$this->relativeMaximumAmount = true;
		else if ($amount === false)
			$this->relativeMaximumAmount = false;
		else if ($amount === 'on' || (strlen($amount) > 1 && substr(ltrim($amount), 0, 1) === '+'))
			$this->relativeMaximumAmount = true;
		else
			$this->relativeMaximumAmount = false;
		return $this;
	}

	public function setCouponExcerpt($excerpt)
	{
		$this->couponExcerpt = $excerpt;
		return $this;
	}

	protected function filterCouponPostData($coupon, $code, \WP_User $user)
	{
		$minAmount = $this->getOrderMinimumAmount();
		$maxAmount = $this->getOrderMaximumAmount();
		if (strlen($minAmount) && $this->isRelativeOrderMinimumAmount() && $coupon->get_discount_type() != 'percent')
			$minAmount = \floatval($minAmount) + \floatval($coupon->get_amount());
		if (strlen($maxAmount) && $this->isRelativeOrderMaximumAmount() && $coupon->get_discount_type() != 'percent')
			$maxAmount = \floatval($maxAmount) + \floatval($coupon->get_amount());

		$coupon->set_props(array(
			'minimum_amount'     => $minAmount,
			'maximum_amount'     => $maxAmount,
			'exclude_sale_items' => $this->isExcludeSaleItems(),
			'individual_use'     => $this->isIndividualUse()
		));
		return $coupon;
	}

	protected function filterData($data = array(), $prefix = '', $min = false)
	{
		$minAmount = $this->getOrderMinimumAmount();
		$maxAmount = $this->getOrderMaximumAmount();
		if (strlen($minAmount) && $this->isRelativeOrderMinimumAmount())
			$minAmount = "+{$minAmount}";
		if (strlen($maxAmount) && $this->isRelativeOrderMaximumAmount())
			$minAmount = "+{$maxAmount}";

		$data[$prefix . 'minimum_amount']     = $minAmount;
		$data[$prefix . 'maximum_amount']     = $maxAmount;
		$data[$prefix . 'exclude_sale_items'] = $this->isExcludeSaleItems() ? 'on' : '';
		$data[$prefix . 'individual_use']     = $this->isIndividualUse() ? 'on' : '';
		$data[$prefix . 'coupon_excerpt']     = $this->getCouponExcerpt();
		$data[$prefix . 'coupon_cat']         = \base64_encode(\json_encode($this->getCouponCategoryIds()));
		return $data;
	}

	protected function filterForm($content = '', $prefix = '', $context = 'editlist', $column = 2)
	{
		// coupon description
		$label = _x("Coupon description", "Unlockable coupon buildup", 'woorewards-pro');
		$tooltip = __("Text used for generated coupon.<br/>Here, the balise <b>[expiry]</b> will be replaced by the computed coupon expiry date.<br/>If omitted, reward description will be used.", 'woorewards-pro');
		$value = isset($this->couponExcerpt) ? \htmlspecialchars($this->couponExcerpt, ENT_QUOTES) : '';
		$descr = "<div class='field-help'>$tooltip</div>";
		$descr .= "<div class='lws-$context-opt-title label'>$label<div class='bt-field-help'>?</div></div>";
		$descr .= "<div class='value lws-$context-opt-input value'>";
		$descr .= "<textarea id='{$prefix}coupon_excerpt' name='{$prefix}coupon_excerpt' >$value</textarea>";
		$descr .= "</div>";
		$descr .= $this->getFieldsetPlaceholder(false, 0);
		$content = str_replace($this->getFieldsetPlaceholder(false, 0), $descr, $content);

		$str = '';

		// minimum amount
		$label = _x("Minimum spend", "Coupon Unlockable", 'woorewards-pro');
		$tooltip = __("Add the + sign before the value to set a minimal amount equal to the generated coupon amount + this value.", 'woorewards-pro');
		$value = \esc_attr($this->getOrderMinimumAmount());
		$str .= "<div class='field-help'>$tooltip</div>";
		$str .= "<div class='lws-$context-opt-title label'>$label<div class='bt-field-help'>?</div></div>";
		$str .= "<div class='lws-$context-opt-input value'><input type='text' id='{$prefix}minimum_amount' name='{$prefix}minimum_amount' value='$value' placeholder='' pattern='\\+?\\d*(\\.|,)?\\d*' /></div>";

		// maximum amount
		$label = _x("Maximum spend", "Coupon Unlockable", 'woorewards-pro');
		$tooltip = __("Add the + sign before the value to set a maximal amount equal to the generated coupon amount + this value.", 'woorewards-pro');
		$value = \esc_attr($this->getOrderMaximumAmount());
		$str .= "<div class='field-help'>$tooltip</div>";
		$str .= "<div class='lws-$context-opt-title label'>$label<div class='bt-field-help'>?</div></div>";
		$str .= "<div class='lws-$context-opt-input value'><input type='text' id='{$prefix}maximum_amount' name='{$prefix}maximum_amount' value='$value' placeholder='' pattern='\\+?\\d*(\\.|,)?\\d*' /></div>";

		// individual use on/off
		$label = _x("Individual use only", "Coupon Unlockable", 'woorewards-pro');
		$toggle = \LWS\Adminpanel\Pages\Field\Checkbox::compose($prefix . 'individual_use', array(
			'id'      => $prefix . 'individual_use',
			'layout'  => 'toggle',
			'checked' => ($this->isIndividualUse() ? ' checked' : '')
		));
		$str .= "<div class='lws-$context-opt-title label'>$label</div>";
		$str .= "<div class='lws-$context-opt-input value'>$toggle</div>";

		if (\apply_filters('lws_coupon_individual_use_solver_exists', false)) {
			$label   = _x("Exclusive categories", "Coupon category", 'woorewards-pro');
			$tooltip = __("Exclusive categories that the coupon will be applied to. Extends the <i>“Individual use only”</i> rule.", 'woorewards-pro');
			$input = \LWS\Adminpanel\Pages\Field\LacChecklist::compose($prefix . 'coupon_cat', array(
				'comprehensive' => true,
				'ajax'          => 'lws_coupon_individual_use_solver_categories',
			));
			$str .= <<<EOT
<div class='field-help'>{$tooltip}</div>
<div class='lws-{$context}-opt-title label'>{$label}<div class='bt-field-help'>?</div></div>
<div class='lws-{$context}-opt-input value'>{$input}</div>
EOT;
		}

		// exclude sale items on/off
		$label = _x("Exclude sale items", "Coupon Unlockable", 'woorewards-pro');
		$toggle = \LWS\Adminpanel\Pages\Field\Checkbox::compose($prefix . 'exclude_sale_items', array(
			'id'      => $prefix . 'exclude_sale_items',
			'layout'  => 'toggle',
			'checked' => ($this->isExcludeSaleItems() ? ' checked' : '')
		));
		$str .= "<div class='lws-$context-opt-title label'>$label</div>";
		$str .= "<div class='lws-$context-opt-input value'>$toggle</div>";

		$str .= $this->getFieldsetPlaceholder(false, $column);
		return str_replace($this->getFieldsetPlaceholder(false, $column), $str, $content);
	}

	/** @return bool */
	protected function optSubmit($prefix = '', $form = array(), $source = 'editlist')
	{
		$values = \apply_filters('lws_adminpanel_arg_parse', array(
			'post'     => ($source == 'post'),
			'values'   => $form,
			'format'   => array(
				$prefix . 'minimum_amount'     => "/^\\s*\\+?(\\s*\\d*)*[,\\.]?(\\s*\\d*)*$/",
				$prefix . 'maximum_amount'     => "/^\\s*\\+?(\\s*\\d*)*[,\\.]?(\\s*\\d*)*$/",
				$prefix . 'individual_use'     => 's',
				$prefix . 'coupon_cat'         => array('D'),
				$prefix . 'exclude_sale_items' => 's',
				$prefix . 'coupon_excerpt'     => 't',
			),
			'defaults' => array(
				$prefix . 'minimum_amount'     => '',
				$prefix . 'maximum_amount'     => '',
				$prefix . 'individual_use'     => '',
				$prefix . 'coupon_cat'         => array(),
				$prefix . 'exclude_sale_items' => '',
				$prefix . 'coupon_excerpt'     => '',
			),
			'labels'   => array(
				$prefix . 'minimum_amount'     => __("Minimum spend", 'woorewards-pro'),
				$prefix . 'maximum_amount'     => __("Maximum spend", 'woorewards-pro'),
				$prefix . 'individual_use'     => __("Individual use only", 'woorewards-pro'),
				$prefix . 'coupon_cat'         => __("Exclusive categories", 'woorewards-pro'),
				$prefix . 'exclude_sale_items' => __("Exclude sale items", 'woorewards-pro'),
				$prefix . 'coupon_excerpt'     => __("Coupon description", 'woorewards-pro'),
			)
		));
		if (!(isset($values['valid']) && $values['valid']))
			return isset($values['error']) ? $values['error'] : false;
		$minAmount = \preg_replace('/\s/', '', $values['values'][$prefix . 'minimum_amount']);
		$maxAmount = \preg_replace('/\s/', '', $values['values'][$prefix . 'maximum_amount']);
		$this->setRelativeOrderMinimumAmount($minAmount);
		$this->setRelativeOrderMaximumAmount($maxAmount);
		$this->setOrderMinimumAmount(ltrim($minAmount, '+'));
		$this->setOrderMaximumAmount(ltrim($maxAmount, '+'));
		$this->setIndividualUse($values['values'][$prefix . 'individual_use']);
		$this->setCouponCategoryIds($values['values'][$prefix . 'coupon_cat']);
		$this->setExcludeSaleItems($values['values'][$prefix . 'exclude_sale_items']);
		$this->setCouponExcerpt($values['values'][$prefix . 'coupon_excerpt']);
		return true;
	}

	protected function optFromPost(\WP_Post $post)
	{
		$this->setRelativeOrderMinimumAmount(\get_post_meta($post->ID, 'relative_minimum_amount', true));
		$this->setOrderMinimumAmount(\get_post_meta($post->ID, 'minimum_amount', true));
		$this->setRelativeOrderMaximumAmount(\get_post_meta($post->ID, 'relative_maximum_amount', true));
		$this->setOrderMaximumAmount(\get_post_meta($post->ID, 'maximum_amount', true));
		$this->setIndividualUse(\get_post_meta($post->ID, 'individual_use', true));
		$this->setCouponCategoryIds(\get_post_meta($post->ID, 'coupon_cat', true));
		$this->setExcludeSaleItems(\get_post_meta($post->ID, 'exclude_sale_items', true));
		$this->setCouponExcerpt(\get_post_meta($post->ID, 'coupon_excerpt', true));
		return $this;
	}

	protected function optSave($id)
	{
		\update_post_meta($id, 'relative_minimum_amount',  $this->isRelativeOrderMinimumAmount() ? 'on' : '');
		\update_post_meta($id, 'minimum_amount',     $this->getOrderMinimumAmount());
		\update_post_meta($id, 'relative_maximum_amount',  $this->isRelativeOrderMaximumAmount() ? 'on' : '');
		\update_post_meta($id, 'maximum_amount',     $this->getOrderMaximumAmount());
		\update_post_meta($id, 'individual_use',     $this->isIndividualUse() ? 'on' : '');
		\update_post_meta($id, 'coupon_cat',         $this->getCouponCategoryIds());
		\update_post_meta($id, 'exclude_sale_items', $this->isExcludeSaleItems() ? 'on' : '');
		\update_post_meta($id, 'coupon_excerpt',     $this->getCouponExcerpt());
		return $this;
	}

	protected function getCustomExcerpt($user)
	{
		$txt = $this->getCouponExcerpt();
		if (!empty($txt)) {
			$expiry = !$this->getTimeout()->isNull() ? $this->getTimeout()->getEndingDate() : false;
			$txt = $this->expiryInText($txt, $expiry);
		} else {
			$txt = $this->getCustomDescription(false);
			if (empty($txt))
				$txt = $this->getCouponDescription('frontend', \date_create());
		}
		return $txt;
	}

	function applyOnCoupon($coupon, $user, $poolId=false, $demo=false)
	{
		if ($coupon->get_id()) {
			\do_action('lws_coupon_individual_use_solver_apply', $coupon->get_id(), $this->getCouponCategoryIds());
		}
	}

	/** if permanent, invalidates the old ones.
	 * A permanent has auto_apply on, and no usage limit. */
	function setPermanentcoupon($coupon, $user, $unlockType, $poolId=false)
	{
		\update_post_meta($coupon->get_id(), 'woorewards_permanent', 'on');
		if ($poolId)
			\update_post_meta($coupon->get_id(), 'woorewards_pool_origin_id', $poolId);

		global $wpdb; // get all other post coming from same unlockable type with permanent mark.
		$request = \LWS\Adminpanel\Tools\Request::from($wpdb->posts, 'p');
		$request->select('p.ID, ucount.meta_value as usage_count');
		$request->innerJoin($wpdb->postmeta, 'orig', array(
			"p.ID=orig.post_id",
			"orig.meta_key='reward_origin'",
			"orig.meta_value=%s",
		))->arg($unlockType);
		$request->innerJoin($wpdb->postmeta, 'perm', array(
			"p.ID=perm.post_id",
			"perm.meta_key='woorewards_permanent'",
			"perm.meta_value='on'",
		));
		$request->innerJoin($wpdb->postmeta, 'mail', array(
			"p.ID=mail.post_id",
			"mail.meta_key='customer_email'",
			"mail.meta_value=%s",
		))->arg(\serialize(array($user->user_email)));
		$request->leftJoin($wpdb->postmeta, 'ucount', array(
			"p.ID=ucount.post_id",
			"ucount.meta_key='usage_count'",
		));

		if ($poolId && !\get_option('lws_woorewards_permanents_through_levels')) {
			// test same pool id
			$request->leftJoin($wpdb->postmeta, 'pool', array(
				"p.ID=pool.post_id",
				"pool.meta_key='woorewards_pool_origin_id'",
			));
			$request->where('(pool.meta_value IS NULL OR pool.meta_value=%s)')->arg($poolId);
		}

		$request->where('p.ID<>%d')->arg($coupon->get_id());
		foreach ($request->getResults() as $old) {
			$limit = max(intval($old->usage_count), 1);
			\update_post_meta($old->ID, 'usage_count', $limit);
			\update_post_meta($old->ID, 'usage_limit', $limit);
		}
	}

	function getPartialDescription($context = 'backend')
	{
		$descr = array();
		if ($min = $this->isIndividualUse())
			$descr[] = __("individual use only", 'woorewards-pro');
		if ($min = $this->isExcludeSaleItems())
			$descr[] = __("exclude sale items", 'woorewards-pro');
		if (floatval($min = $this->getOrderMinimumAmount()) > 0.0) {
			if (isset($this->lastAmount)) {
				if ($this->isRelativeOrderMinimumAmount() && (!\method_exists($this, 'getInPercent') || !$this->getInPercent()))
					$min += \floatval($this->lastAmount);
				$value = (\LWS_WooRewards::isWC() && $context != 'edit') ? \wc_price($min) : \number_format_i18n($min, 2);
			} else {
				$value = (\LWS_WooRewards::isWC() && $context != 'edit') ? \wc_price($min) : \number_format_i18n($min, 2);
				if ($this->isRelativeOrderMinimumAmount() && (!\method_exists($this, 'getInPercent') || !$this->getInPercent()))
					$value = sprintf(__("the coupon amount + %s of", 'woorewards-pro'), $value);
			}
			$descr[] = sprintf(__("required %s minimal amount", 'woorewards-pro'), $value);
		}
		if (floatval($max = $this->getOrderMaximumAmount()) > 0.0) {
			if (isset($this->lastAmount)) {
				if ($this->isRelativeOrderMaximumAmount() && (!\method_exists($this, 'getInPercent') || !$this->getInPercent()))
					$max += \floatval($this->lastAmount);
				$value = (\LWS_WooRewards::isWC() && $context != 'edit') ? \wc_price($max) : \number_format_i18n($max, 2);
			} else {
				$value = (\LWS_WooRewards::isWC() && $context != 'edit') ? \wc_price($max) : \number_format_i18n($max, 2);
				if ($this->isRelativeOrderMaximumAmount() && (!\method_exists($this, 'getInPercent') || !$this->getInPercent()))
					$value = sprintf(__("the coupon amount + %s of", 'woorewards-pro'), $value);
			}
			$descr[] = sprintf(__("required %s maximal amount", 'woorewards-pro'), $value);
		}
		return implode(', ', $descr);
	}

	/** replace the balise [expiry] by the computed expiration date */
	protected function expiryInText($text, $expiry, $dft = '')
	{
		$date = $expiry ? \date_i18n(\get_option('date_format'), $expiry->getTimestamp()) : $dft;
		return str_replace('[expiry]', $date, $text);
	}
}
