<?php

namespace LWS\WOOREWARDS;

// don't call the file directly
if (!defined('ABSPATH')) exit();

/** Conveniences Class for shared functions*/
class Conveniences
{
	public static function install()
	{
		$me = &self::instance();
		\add_filter('lws_woorewards_get_pools_by_args', array($me, 'getPrefabPool'), 20, 2); //Lowest priority than WooRewards Pro
	}

	/** @return singleton instance */
	static function &instance()
	{
		static $_inst = false;
		if (false === $_inst)
			$_inst = new self();
		return $_inst;
	}

	/** prevent outside instanciation */
	protected function __construct()
	{
	}

	/** Test if the content should be displayed or not
	 *	@param $stepVersion → Version after which the legacy content is not displayed anymore
	 *	@return (bool) if we assume current install should use legacy feature. */
	function isLegacyShown($stepVersion)
	{
		static $installVers = false;
		if (false === $installVers)
			$installVers = \get_option('lws_woorewards_install_version', '0');
		return (!$installVers || \version_compare($installVers, $stepVersion, '<'));
	}

	/** Returns the Prefab Pool if it exists
	 *	Hooked to 'lws_woorewards_get_pools_by_args' */
	function getPrefabPool($pools, $atts)
	{
		if (false === $pools) {
			$pools = \LWS\WOOREWARDS\Collections\Pools::instanciate()->load(array(
				'numberposts' => 1,
				'meta_query'  => array(
					array(
						'key'     => 'wre_pool_prefab',
						'value'   => 'yes',
						'compare' => 'LIKE'
					),
					array(
						'key'     => 'wre_pool_type',
						'value'   => \LWS\WOOREWARDS\Core\Pool::T_STANDARD,
						'compare' => 'LIKE'
					)
				),
				'deep' => true,
			));

			if (!(isset($atts['force']) && $atts['force']))
				$pools = $pools->filterByUserCan(\get_current_user_id());
		}
		return $pools;
	}

	/* Get Available WooCommerce coupons for the provided user */
	public function getCoupons($userId)
	{
		$user = \get_user_by('ID', $userId);
		if (empty($user->user_email))
			return array();
		$todayDate = strtotime(date('Y-m-d'));
		global $wpdb;
		$query = <<<EOT
			SELECT p.ID, p.post_content, p.post_title, p.post_excerpt, e.meta_value AS expiry_date
			FROM {$wpdb->posts} as p
			INNER JOIN {$wpdb->postmeta} as m ON p.ID = m.post_id AND m.meta_key='customer_email'
			LEFT JOIN {$wpdb->postmeta} as l ON p.ID = l.post_id AND l.meta_key='usage_limit'
			LEFT JOIN {$wpdb->postmeta} as u ON p.ID = u.post_id AND u.meta_key='usage_count'
			LEFT JOIN {$wpdb->postmeta} as e ON p.ID = e.post_id AND e.meta_key='date_expires'
			WHERE m.meta_value=%s AND post_type = 'shop_coupon' AND post_status = 'publish'
			AND (e.meta_value is NULL OR e.meta_value = '' OR e.meta_value >= '{$todayDate}')
			AND (u.meta_value < l.meta_value OR u.meta_value IS NULL OR l.meta_value IS NULL OR l.meta_value=0)
EOT;
		$result = $wpdb->get_results($wpdb->prepare($query, serialize(array($user->user_email))), OBJECT_K);
		if (empty($result))
			return $result;

		$ids = implode(",", array_map('intval', array_keys($result)));
		$query = <<<EOT
			SELECT p.ID, v.meta_value AS coupon_amount, o.meta_value AS product_ids, w.meta_value AS discount_type
			FROM {$wpdb->posts} as p
			LEFT JOIN {$wpdb->postmeta} as w ON p.ID = w.post_id AND w.meta_key='discount_type'
			LEFT JOIN {$wpdb->postmeta} as v ON p.ID = v.post_id AND v.meta_key='coupon_amount'
			LEFT JOIN {$wpdb->postmeta} as o ON p.ID = o.post_id AND o.meta_key='product_ids'
			WHERE p.ID IN ({$ids})
EOT;
		$sub = $wpdb->get_results($query, OBJECT_K);
		foreach ($sub as $id => $info) {
			foreach ($info as $k => $v)
				$result[$id]->$k = $v;
		}
		return $result;
	}

	/**	Avoid overstock WC_Order::add_order_note and pack them in our own metabox.
	 *	As WC, comment the order.
	 *	@param $order (WC_Order|int)
	 *	@param $note (string) the message
	 *	@param $source (Pool|string|false) the pool, the stack id or any relevant origin
	 *	@return the new comment id or false on error. */
	public static function addOrderNote($order, $note, $source=false)
	{
		return \LWS\WOOREWARDS\Core\OrderNote::add($order, $note, $source);
	}
}
