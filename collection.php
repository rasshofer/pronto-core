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
 * A Collection represents a set of instances
 *
 * @author Thomas Rasshofer <tr@prontocms.com>
 */
class Collection implements \IteratorAggregate, \Countable
{

	/**
	 * The collection
	 *
	 * @var array
	 */
	protected $data = array();

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
	 * Reduces collection to those items matching $pattern
	 *
	 * @param string $pattern The RegEx pattern
	 *
	 * @return $this
	 */
	public function is($pattern)
	{
		$self = clone $this;
		foreach ($self->data as $link => $page) {
			if (!preg_match('/'.$pattern.'/', $link)) {
				unset($self->data[$link]);
			}		
		}
		return $self;
	}
	
	/**
	 * Reduces collection to those items not matching $pattern
	 * 
	 * @param string $pattern The RegEx pattern
	 *
	 * @return $this
	 */
	public function not($pattern)
	{
		$self = clone $this;
		foreach ($self->data as $link => $page) {
			if (preg_match('/'.$pattern.'/', $link)) {
				unset($self->data[$link]);
			}
		}
		return $self;
	}

	/**
	 * Filters collection
	 *
	 * Accepts different an amount of parameters (1, 2, 3, or 4)
	 * 
	 * @param string $search Search for URL
	 *
	 * @param string $key The content key
	 * @param string $value The value to equal $key
	 *
	 * @param string $key The content key
	 * @param string $operator The operator
	 * @param string $value The value to be compared against $key using $operator
	 * @param boolean $insensitive Case insensitive 
	 *
	 * @return $this
	 */
	public function filter()
	{
		$args = func_get_args();
		$self = clone $this;
		if (count($args) == 1) {
			list($search) = $args;
			if (stristr($search, '*')) {
				$self = $self->is('^'.str_replace('\*', '(.*)', preg_quote($search, '/')).'$');
			} else {
				$self = $self->is('^'.preg_quote($search, '/').'$');
			}
		} elseif (count($args) == 2) {
			list($key, $value) = $args;
			$self = $self->filter($key, '=', $value);
		} elseif (count($args) > 2) {
			list($key, $operator, $value, $insensitive) = $args;
			$operator = trim($operator);
			$insensitive = !empty($insensitive);
			if ($operator == '*=') {
				$modifier = $insensitive ? 'i' : '';
				foreach ($self->data as $link => $page) {
					if (!($page->$key() && preg_match('/'.preg_quote($value).'/'.$modifier, $page->$key()))) {
						unset($self->data[$link]);
					}
				}
			} elseif ($operator == '$=') {
				$modifier = $insensitive ? 'i' : '';
				foreach ($self->data as $link => $page) {					
					if (!($page->$key() && preg_match('/'.preg_quote($value).'$/'.$modifier, $page->$key()))) {
						unset($self->data[$link]);
					}
				}
			} elseif ($operator == '^=') {
				$modifier = $insensitive ? 'i' : '';
				foreach ($self->data as $link => $page) {
					if (!($page->$key() && preg_match('/^'.preg_quote($value).'/'.$modifier, $page->$key()))) {
						unset($self->data[$link]);
					}
				}
			} elseif ($operator == '!=' || $operator == '<>') {
				$modifier = $insensitive ? 'i' : '';
				foreach ($self->data as $link => $page) {
					if (!($page->$key() && preg_match('/^(?!'.preg_quote($value).').*$/'.$modifier, $page->$key()))) {
						unset($self->data[$link]);
					}
				}
			} elseif ($operator == '=' || $operator == '==' || $operator == '===') {
				$modifier = $insensitive ? 'i' : '';
				foreach ($self->data as $link => $page) {
					if (!($page->$key() && preg_match('/^'.preg_quote($value).'$/'.$modifier, $page->$key()))) {
						unset($self->data[$link]);
					}
				}
			} elseif ($operator == '<') {
				foreach ($self->data as $link => $page) {
					if (!($page->$key() && $page->$key() < $value)) {
						unset($self->data[$link]);
					}
				}
			} elseif ($operator == '<=') {
				foreach ($self->data as $link => $page) {
					if (!($page->$key() && $page->$key() <= $value)) {
						unset($self->data[$link]);
					}
				}				
			} elseif ($operator == '>') {
				foreach ($self->data as $link => $page) {
					if (!($page->$key() && $page->$key() > $value)) {
						unset($self->data[$link]);
					}
				}
			} elseif ($operator == '>=') {
				foreach ($self->data as $link => $page) {
					if (!($page->$key() && $page->$key() >= $value)) {
						unset($self->data[$link]);
					}
				}
			}
		}	
		return $self;
	}

	/**
	 * Gets count of current collection
	 *
	 * @return int Count of current collection
	 */
	public function count()
	{
		return count($this->data);
	}
	
	/**
	 * Alias of count()
	 *
	 * @return int Count of current collection
	 */
	public function size()
	{
		return count($this->data);
	}

	/**
	 * Get item of current collection by index
	 *
	 * @param int $index The index
	 *
	 * @return mixed The requested item
	 */
	public function eq($index)
	{
		$self = clone $this;
		if ($index < 0 || $index >= $self->size()) {
			return array();	
		}
		reset($self->data);
		for ($i = 0; $i < $index; $i++) {
			next($self->data);
		}
		return current($self->data);
	}

	/**
	 * Get first item of current collection
	 *
	 * @return mixed The requested item
	 */
	public function first()
	{
		return $this->eq(0);
	}

	/**
	 * Get last item of current collection
	 *
	 * @return mixed The requested item
	 */
	public function last()
	{
		return $this->eq($this->size()-1);
	}

	/**
	 * Reverses the current collection
	 *
	 * @return $this
	 */
	public function reverse()
	{
		$self = clone $this;
		$self->data = array_reverse($self->data, true);
		return $self;	
	}

	/**
	 * Alias of reverse()
	 *
	 * @return $this
	 */
	public function flip()
	{
		$self = clone $this;
		return $self->reverse();
	}

	/**
	 * Slices the current collection
	 *
	 * @param string $offset The offset
	 * @param int|null $length The length
	 *
	 * @return $this
	 */
	public function slice($offset, $length = null)
	{
		$self = clone $this;
		$self->data = array_slice($self->data, $offset, $length, true);
		return $self;
	}

	/**
	 * Limits the current collection
	 *
	 * @param int $limit The limit
	 *
	 * @return $this
	 */
	public function limit($limit)
	{
		$self = clone $this;
		return $self->slice(0, $limit);		
	}

	/**
	 * Get every nth item the current collection
	 *
	 * @param int $n N
	 *
	 * @return $this
	 */
	public function nth($n)
	{
		$self = clone $this;
		$i = 1;
		if ($n < 0 || $n >= $self->size()) {
			$self->data = array();	
		}
		foreach ($self->data as $link => $check) {
			if ($i++%$n != 0) {
				unset($self->data[$link]);	
			}
		}
		return $self;	
	}

}

?>