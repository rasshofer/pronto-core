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
 * A Container represents a set of data.
 *
 * @author Thomas Rasshofer <tr@prontocms.com>
 */
class Container implements \IteratorAggregate
{

	/**
	 * The collection
	 *
	 * @var array
	 */
	private $data = array();

	/**
	 * Setter
	 */
	public function __set($key, $val)
	{
		$this->data[$key] = $val;
	}

	/**
	 * Getter
	 */
	public function __get($key)
	{
		if (array_key_exists($key, $this->data)) {
			return $this->data[$key];
		}
		return false;
	}

	/**
	 * Gets the current Collection as an Iterator that includes all instances
	 *
	 * It implements \IteratorAggregate
	 *
	 * @return \ArrayIterator An \ArrayIterator object for iterating over collection data
	 */
	public function getIterator()
	{
		return new \ArrayIterator($this->data);
	}

	/**
	 * Add data to the container
	 *
	 * If $key is an array, multiple assignments can be done
	 *
	 * @param string|array $key The key
	 * @param mixed|null $value The data
	 */
	public function set($key, $value = null)
	{
		if (is_array($key)) {
			foreach ($key as $k => $v) {
				$this->data[$k] = $v;
			}
		} else {
			$this->data[$key] = $value;
		}
	}

	/**
	 * Get data from the container
	 *
	 * @param string|null $key The desired key
	 *
	 * @return array|mixed|boolean The whole data array if $key is null, desired data if $key exists, false otherwise
	 */
	public function get($key = null)
	{
		if (is_null($key)) {
			return $this->data;
		}
		if (array_key_exists($key, $this->data)) {
			return $this->data[$key];
		}
		return false;
	}

}

?>
