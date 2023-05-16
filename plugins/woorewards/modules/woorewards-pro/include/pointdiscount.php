<?php
namespace LWS\WOOREWARDS\PRO;

// don't call the file directly
if( !defined( 'ABSPATH' ) ) exit();

/** Fake a WC_Coupon to consume points.
 *	Add few restriction on Free version.
 *	@see \LWS\WOOREWARDS\PointDiscount */
class PointDiscount
{
	static function register()
	{
		$me = new self();
		\add_filter('lws_woorewards_pointdiscount_from_code', array($me, 'fromCode'), 10, 2);
		\add_filter('lws_woorewards_pointdiscount_max_points', array($me, 'getMaxPoints'), 10, 5);
		\add_filter('lws_woorewards_pointsoncart_pools', array($me, 'getPools'), 10, 1);
		\add_filter('lws_woorewards_pointsoncart_template_info', array($me, 'templateInfo'), 10, 1);
		\add_filter('woocommerce_get_shop_coupon_data', array($me, 'asData'), PHP_INT_MAX - 5, 3);

		// multi currency virtual coupon, check conflict with wallet feature
		if (\get_option('lws_woorewards_convert_virtual_coupon_currency')) {
			\add_action('setup_theme', function()use($me){
				// If option is already active in WooVirtualWallet, no need to hook ourselves here
				if (!(defined('LWS_WOOVIRTUALWALLET_ACTIVATED') && LWS_WOOVIRTUALWALLET_ACTIVATED
				&& \get_option('lws_woovirtualwallet_convert_virtual_coupon_currency', false))) {
					\add_filter('woocommerce_coupon_get_amount', array($me, 'getVirtualCouponAmount'), 10, 2);
				}
			});
		}
	}

	/** @return \LWS\WOOREWARDS\Collections\Pools instance */
	function getPools($pools)
	{
		return \LWS_WooRewards_Pro::getBuyablePools();
	}

	/** Check restrictions ok and recompute the value. */
	function fromCode($discount, $code)
	{
		if( !\WC()->cart )
			return $discount;

		$pool = \LWS\WOOREWARDS\PointDiscount::getPool($discount);
		$points = $discount['points'];
		$max = \apply_filters('lws_woorewards_pointdiscount_max_points', $points, $discount['rate'], $pool, $discount['user_id'], \WC()->cart);
		if( $max <= 0 )
			return false;

		if ($max < $points) {
			$discount['points'] = $max;
			$discount['value'] = (float)$max * $discount['rate'];
		}
		$min = \intval($pool->getOption('direct_reward_min_points_on_cart'));
		if ($min > 0 && $points < $min) {
			if ($min > $pool->getPoints($discount['user_id']))
				return false;
			$discount['points'] = $min;
			$discount['value'] = (float)$min * $discount['rate'];
		}
		return $discount;
	}

	/** Check restrictions for max usable points.
	 *	return 0 if minimal requirement is not fulfilled,
	 *	else return the max point amount */
	function getMaxPoints($points, $rate, $pool, $userId, $cart)
	{
		if( 0.0 == $rate )
			return 0;

		$subtotal = $cart->get_subtotal();
		$incTax = ('yes' === \get_option('woocommerce_prices_include_tax'));
		if ($incTax)//if( $cart->display_prices_including_tax() )
			$subtotal += $cart->get_subtotal_tax();
		foreach ($cart->get_applied_coupons() as $otherCode)
		{
			if (strpos($otherCode, 'wr_points_on_cart') === false) {
				$value = $cart->get_coupon_discount_amount($otherCode, !$incTax);
				$subtotal -= $value;
			}
		}

		// check min subtotal
		$min = $pool->getOption('direct_reward_min_subtotal');
		if( $min && $min > 0.0 )
		{
			if( $subtotal < $min )
				return 0;
		}

		$currencyRate = \LWS\Adminpanel\Tools\Conveniences::getCurrencyPrice(1.0, true, false);
		if (0.0 != $currencyRate)
			$subtotal = ($subtotal / $currencyRate);

		// round up since we cannot exceed subtotal,
		// but can spare the last point to reach it (with some lost)
		$max = (int)\ceil((float)$subtotal / $rate);

		// clamp max percent
		$percent = $pool->getOption('direct_reward_max_percent_of_cart');
		if( $percent && \is_numeric($percent) )
		{
			$amount = ($subtotal * (float)$percent / 100.0);
			// round down since we absolutly don't want to go under the amount
			$max = \min((int)\floor((float)$amount / $rate), $max);
		}

		// dont make price go lower than
		$floor = $pool->getOption('direct_reward_total_floor');
		if( $floor && \is_numeric($floor) )
		{
			$amount = $subtotal - $floor;
			if( $amount <= 0 )
				return 0;
			// round down since we absolutly don't want to go under the amount
			$max = \min((int)\floor((float)$amount / $rate), $max);
		}

		// if the amount still exceeds the max amount of points that can be used, cut it
		$maxpoints = $pool->getOption('direct_reward_max_points_on_cart');
		if ($maxpoints && \is_numeric($maxpoints)) {
			$max = ($maxpoints < $max) ? $maxpoints : $max;
		}

		// if max still greater than min, abort
		$minPts = \intval($pool->getOption('direct_reward_min_points_on_cart'));
		if ($minPts > 0 && $minPts > $max)
			return 0;

		return \min($max, $points);
	}

	function templateInfo($info)
	{
		$info['pool']->setOptions(array(
			'direct_reward_max_percent_of_cart' => 50.0,
			'direct_reward_min_points_on_cart'  => 7,
			'direct_reward_max_points_on_cart'  => 200,
			'direct_reward_total_floor'         => 5.0,
			'direct_reward_min_subtotal'        => 10.0,
			'direct_reward_discount_cats'       => array(),
		));
		return $info;
	}

	public function getVirtualCouponAmount($amount, $coupon)
	{
		if ($coupon && \is_a($coupon, '\WC_Coupon')) {
			if ($coupon->get_virtual()
			&& false !== strpos($coupon->get_discount_type(), 'fixed')
			&& 0 === strpos($coupon->get_code(), 'wr_points_on_cart-')) {
				$amount = \LWS\Adminpanel\Tools\Conveniences::getCurrencyPrice($amount, true, false);
			}
		}
		return $amount;
	}

	function asData($coupon, $data, $instance)
	{
		if ($instance && isset($instance->wr_discount_data) && $instance->wr_discount_data && \is_array($instance->wr_discount_data)) {
			$pool = false;
			if (isset($instance->wr_discount_data['pool']) && $instance->wr_discount_data['pool']) {
				$pool = $instance->wr_discount_data['pool'];
			} elseif (isset($instance->wr_discount_data['pool_name']) && $instance->wr_discount_data['pool_name']) {
				$pool = \LWS\WOOREWARDS\PRO\Core\Pool::getOrLoad($instance->wr_discount_data['pool_name'], false);
			}
			if ($pool) {
				$instance->update_meta_data('_lws_coupon_virtual_taxonomy', $pool->getOption('direct_reward_discount_cats'));
			}
		}
		return $coupon;
	}
}
