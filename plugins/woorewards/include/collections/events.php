<?php
namespace LWS\WOOREWARDS\Collections;

// don't call the file directly
if( !defined( 'ABSPATH' ) ) exit();

require_once LWS_WOOREWARDS_INCLUDES . '/abstracts/event.php';

/** A collection of Event. */
class Events extends \LWS\WOOREWARDS\Abstracts\Collection
{
	/** read event from database.
	 * @param args @see WP_Query::parse_query() */
	public function load($args=array())
	{
		$cache = false;
		if( isset($args['cache']) )
		{
			$cache = $args['cache'];
			unset($args['cache']);
		}
		$this->items = array();
		$args = \wp_parse_args($args, array(
			'numberposts' => -1,
			'post_type' => \LWS\WOOREWARDS\Abstracts\Event::POST_TYPE,
			'post_status' => array('publish', 'private', 'draft', 'pending', 'future'),
			'orderby' => array('menu_order' => 'DESC', 'ID' => 'ASC'),
		));
		if( $cache === true || $cache === false )
		{
			$args = array_merge($args, array(
				'update_post_meta_cache' => $cache,
				'update_post_term_cache' => $cache,
				'cache_results'  => $cache
			));
		}
		$posts = isset($args['post_parent']) ? \get_children($args) : \get_posts($args);
		if( !is_array($posts) )
		{
			error_log("Fail to load events, check posts with for :".print_r($args, true));
		}
		else
		{
			foreach( $posts as $post )
			{
				if( !\wp_is_post_revision($post) && !empty($event = \LWS\WOOREWARDS\Abstracts\Event::fromPost($post, true)) )
					$this->items[] = $event;
			}
		}
		return $this;
	}

	/** Instanciate all registered types. */
	public function create($ref=false)
	{
		if( $ref === false )
		{
			$this->items = array();
			foreach( \LWS\WOOREWARDS\Abstracts\Event::getRegistered() as $reg )
			{
				if( !empty($event = \LWS\WOOREWARDS\Abstracts\Event::instanciate($reg)) )
					$this->items[$event->getName()] = $event;
			}
		}
		else
		{
			$reg = \LWS\WOOREWARDS\Abstracts\Event::getRegisteredByName($ref);
			if( !empty($reg) && !empty($event = \LWS\WOOREWARDS\Abstracts\Event::instanciate($reg)) )
				$this->add($event);
			else
				error_log("Cannot found MyRewards Event type: ".print_r($ref, true));
		}
		return $this;
	}

	/** install each loaded event. */
	public function install()
	{
		foreach( $this->items as &$event )
			$event->install();
		return $this;
	}

	public function save(\LWS\WOOREWARDS\Core\Pool &$pool)
	{
		foreach( $this->items as &$event )
			$event->save($pool);
		return $this;
	}

	public function getTypes()
	{
		$types = array();
		foreach( $this->items as &$item )
			$types[$item->getType()] = true;
		return array_keys($types);
	}

	public function filterByType($type)
	{
		return $this->filter(function($item)use($type){return $item->getType() == $type;});
	}

	public function filterByCategories($cats)
	{
		if (!\is_array($cats))
			$cats = array($cats);
		return $this->filter(function($item)use($cats){
			return \array_intersect(\array_keys($item->getCategories()), $cats);
		});
	}

	/**  Sort by point ASC */
	public function sort()
	{
		static $timestamp = 0;
		$timestamp++;

		$this->usort(function($a, $b)use($timestamp){
			$ca = $a->getGainWithCache(array(), true, $timestamp);
			$cb = $b->getGainWithCache(array(), true, $timestamp);
			if( $ca == $cb )
				return intval($a->getId()) - intval($b->getId());
			else
				return ($ca < $cb) ? -1 : 1;
		});
		return $this;
	}
}
