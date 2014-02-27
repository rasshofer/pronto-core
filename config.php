<?php

/*
 * This file is part of the PRONTO core.
 *
 * (c) Thomas Rasshofer <tr@prontocms.com>
 *
 * For the full copyright and license information, please view http://prontocms.com/license.
 */

namespace Pronto;

use Pronto\Container;

/**
 * A ConfigContainer represents a set of configuration variables.
 *
 * @author Thomas Rasshofer <tr@prontocms.com>
 */
class ConfigContainer
{

	/**
	 * Data collection
	 *
	 * @var array
	 */
	static private $container = null;

    /**
	 * Returns the container instance
	 *
	 * @return Container The container instance
	 */
	static private function getContainer()
	{
		if (self::$container === null) {
			self::$container = new Container();
		}
		return self::$container;
	}

	/**
	 * Add data to the container
	 *
	 * If $key is an array, multiple assignments can be done
	 *
	 * @param string|array $key The key
	 * @param mixed|null $value The data
	 */
	static public function set($key, $value = null)
	{
		return self::getContainer()->set($key, $value);
	}

	/**
	 * Get data from the container
	 *
	 * @param string|null $key The desired key
	 *
	 * @return array|mixed|boolean The whole data array if $key is null, desired data if $key exists, false otherwise
	 */
	static public function get($key = null)
	{
		return self::getContainer()->get($key);
	}

}

?>
