<?php
namespace LWS\WOOREWARDS\PRO\PointsFlow;

// don't call the file directly
if( !defined( 'ABSPATH' ) ) exit();

/** Point export method extends this abstract and are placed in methods/ subdir. */
class ExportMethods
{
	static function get($metaKey)
	{
		$me = new self();
		return $me->getMethod($metaKey);
	}

	function getMethod($metaKey)
	{
		$methods = $this->getFiles();
		if( isset($methods[$metaKey]) )
			return $methods[$metaKey];
		else if( isset($methods['lws_woorewards_pro_pointsflow_methods_metakey']) )
			return $methods['lws_woorewards_pro_pointsflow_methods_metakey'];
		else
			return false;
	}

	function getArguments()
	{
		$methods = array(array(
			'value' => '—',
			'label' => '—',
		));
		foreach( $this->getFiles() as $method )
		{
			if( $method->instance->isVisible() && ($args = $method->instance->getArgs()) )
			{
				$sub = array();
				foreach( $args as $value => $label )
					$sub[] = array('value' => $value, 'label' => $label);

				$methods[] = array(
					'value' => $method->instance->getKey(),
					'label' => $method->instance->getTitle(),
					'group' => $sub,
				);
			}
		}
		return $methods;
	}

	function getMethods()
	{
		$methods = array(array(
			'value' => '—',
			'label' => '—',
		));
		foreach( $this->getFiles() as $method )
		{
			if( $method->instance->isVisible() )
			{
				$methods[] = array(
					'value' => $method->instance->getKey(),
					'label' => $method->instance->getTitle(),
				);
			}
		}
		return $methods;
	}

	private function getFiles()
	{
		if( !isset($this->methods) )
		{
			$path = LWS_WOOREWARDS_PRO_INCLUDES . '/pointsflow';
			require_once $path.'/exportmethod.php';
			$this->methods = array();

			foreach( glob($path.'/methods/*.php') as $file )
			{
				$basename = basename($file);
				if( !($basename != 'index.php' && false === strpos($basename, '..') && is_file($file)) )
					continue;

				$match = array();
				if( !preg_match('/^class (\w+)\s+/im', file_get_contents($file), $match) )
					continue;

				@include_once $file;
				$classname = '\LWS\WOOREWARDS\PRO\PointsFlow\Methods\\'.$match[1];
				if( class_exists($classname) )
				{
					$method = new $classname();
					if( \is_a($method, '\LWS\WOOREWARDS\PRO\PointsFlow\ExportMethod') )
					{
						$key = $method->getKey();
						$this->methods[$key] = (object)array(
							'classname' => $classname,
							'file'      => $file,
							'instance'  => $method,
						);
					}
				}
			}
		}
		return $this->methods;
	}
}
