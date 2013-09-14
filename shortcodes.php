<?php

/*
 * This file is part of the PRONTO core.
 *
 * (c) Thomas Rasshofer <tr@prontocms.com>
 *
 * For the full copyright and license information, please view http://prontocms.com/license.
 */

namespace Pronto;

/**
 * A ShortcodeContainer represents a set of shortcodes
 *
 * @author Thomas Rasshofer <tr@prontocms.com>
 */
class ShortcodeContainer
{
	
	/**
	 * Shortcodes
	 *
	 * @var array
	 */
	protected static $data = array(); 

	/**
	 * Caller
	 */
	public static function __callStatic($key, $args)
	{
		if (self::exists($key)) {
			$attributes = array_shift($args);
			return call_user_func(self::$data[$key], $attributes);
		}
		return false;
	}

	/**
	 * Checks if a shortcode exists
	 *
	 * @param string $key Key of the desired shortcode
	 *
	 * @return true|false True if the shortcode exists, false otherwise
	 */
	public static function exists($key)
	{
		return array_key_exists($key, self::$data);
	}
	
	/**
	 * Adds a shortcode to the shortcode container
	 *
	 * @param string $key Key of the shortcode
	 * @param closure $callback The shortcode's callback function
	 */
	public static function add($key, $callback)
	{
		self::$data[$key] = $callback;
	}
	
}

?>