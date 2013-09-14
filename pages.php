<?php

/*
 * This file is part of the PRONTO core.
 *
 * (c) Thomas Rasshofer <tr@prontocms.com>
 *
 * For the full copyright and license information, please view http://prontocms.com/license.
 */

namespace Pronto;

use Pronto\Collection;
use Pronto\Page;
use Pronto\ConfigContainer;

/**
 * A PageCollection represents a set of Page instances.
 *
 * @author Thomas Rasshofer <tr@prontocms.com>
 */
class PageCollection extends Collection
{

	/**
	 * Current depth of collection
	 *
	 * @var int
	 */
	public $depth = 1;

	/**
	 * Pages as array tree
	 *
	 * @var array
	 */
	protected $tree = array();

	/**
	 * Active page
	 *
	 * @var Page
	 */
	protected $active = null;

	/**
	 * Parameters
	 *
	 * @var array
	 */
	protected $params = array();

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		$this->tree = $this->parse(PRONTO_CONTENT);
		$files = $this->builder($this->tree);
		$pages = $this->builder($this->convert($this->tree));		
		foreach ($pages as $page) {
			$this->data[$page] = new Page($page, array_shift($files), $this);
		}
	}

	/**
	 * Gets the current PageCollection as an Iterator that includes all pages.
	 *
	 * It implements \IteratorAggregate.
	 *
	 * @return \ArrayIterator An \ArrayIterator object for iterating over collection data
	 */
	public function getIterator()
	{
		$self = clone $this;
		return new \ArrayIterator($self->depth($self->depth)->data);
	}

	/**
	 * Get active page
	 *
	 * @param int|null $level The desired level of depth
	 *
	 * @return Page|false The active Page or false when not existing
	 */
	public function active($level = null)
	{
		if (is_null($level)) {
			if (is_null($this->active)) {
				$request = trim(strtok($_SERVER['REQUEST_URI'], '?'), '/');
				$request = trim(substr($request, strlen(PRONTO_SUB)), '/');		
				$query = trim($_SERVER['QUERY_STRING'], '/');
				$page = ConfigContainer::get('rewrite') ? $request : $query;					
				$parts = array();
				$explode = explode('/', $page);
				foreach ($explode as $part)
				{
					if (stristr($part, ':'))
					{
						list($key, $value) = explode(':', $part, 2);
						$this->params[urldecode($key)] = urldecode($value);
					} else {
						$parts[] = urldecode($part);
					}
				}
				$page = implode('/', $parts);
				$page = empty($page) ? (ConfigContainer::get('home') ? ConfigContainer::get('home') : key($this->data)) : $page;				
				// Search for page				
				if (array_key_exists($page, $this->data)) {
					$page = $this->data[$page];
					$page->active = true;
					$this->active =& $page;
					return $page;
				}
				// Send error header?
				if (ConfigContainer::get('error-header')) {
					header('HTTP/1.0 404 Not Found');
				}
				// Use error page if provided
				if (array_key_exists(ConfigContainer::get('error'), $this->data)) {
					$page = $this->data[ConfigContainer::get('error')];
					$page->active = true;
					$this->active =& $page;
					return $page;
				}
				$this->active = false;
				return false;
			} else {
				return $this->active;
			}
		} else {
			$self = clone $this;
			$search = implode('/', array_slice(explode('/', $self->active()->path()), 0, $level));			
			return !empty($search) ? $self->filter($search)->first() : false;
		}
	}

	/**
	 * Get the tree array
	 *
	 * @param string $key The desired key
	 * @param string $ascDesc The desired order
	 *
	 * @return $this
	 */
	public function tree($hide = true)
	{
		if (!$hide) {
			return $this->convert($this->tree);
		}
		return $this->hide($this->tree);
	}

	/**
	 * Parse a directory and return the page tree
	 *
	 * @param string $dir The directory
	 *
	 * @return array The page tree
	 */
	public function parse($dir)
	{		
		$pages = array();
		foreach (new \DirectoryIterator($dir) as $item) {
			if (!$item->isDot() && $item->isDir()) {
				$pages[$item->getFilename()] = $this->parse($item->getPathname());
			}
		}
		$sorted = array();
		$keys = array_keys($pages);
		natcasesort($keys);
		foreach ($keys as $k) {
			$sorted[$k] = $pages[$k];
		}
		return $sorted;
	}

	/**
	 * Convert files to requested URIs
	 *
	 * @param array $array The files
	 *
	 * @return array The pages
	 */
	public function convert($array)
	{
		$pages = array();
		foreach ($array as $key => $val) {
			preg_match('/^([0-9]+)-([\p{L}\-\_]+)$/u', $key, $matches);
			if (!empty($matches)) {
				$pages[$matches[2]] = $this->convert($val);
			} else {
				$pages[$key] = $this->convert($val);
			}
		}
		return $pages;
	}

	/**
	 * Hide pages marked as hidden
	 *
	 * @param array $array The pages
	 *
	 * @return array The visible pages
	 */
	public function hide($array)
	{
		$pages = array();
		foreach ($array as $key => $val) {
			preg_match('/^([0-9]+)-([\p{L}\-\_]+)$/u', $key, $matches);
			if (!empty($matches)) {
				$pages[$matches[2]] = $this->convert($val);
			}
		}
		return $pages;
	}

	/**
	 * Build paths out of the tree array
	 *
	 * @param array $array The tree
	 * @param array $path The current path
	 *
	 * @return array The URLs
	 */
	public function builder($array, $path = array())
	{
		$urls = array();
		$path = HelperContainer::clean($path);
		if (!empty($array)) {
			foreach ($array as $key => $val) {
				$data = $path;
				$data[] = $key;
				$urls[] = implode(DS, $data);
				$urls = array_merge($urls, $this->builder($val, $data));
				unset($data);
			}
		}
		return $urls;
	}

	/**
	 * Reduce current collection to visible pages only
	 *
	 * @return $this
	 */
	public function visible()
	{
		$self = clone $this;
		foreach ($self->data as $link => $page) {
			if ($page->hidden()) {
				unset($self->data[$link]);
			}
		}
		return $self;
	}

	/**
	 * Reduce current collection to invisible pages only
	 *
	 * @return $this
	 */
	public function invisible()
	{
		$self = clone $this;
		foreach ($self->data as $link => $page) {
			if ($page->visible()) {
				unset($self->data[$link]);
			}
		}
		return $self;
	}

	/**
	 * Alias of invisible
	 *
	 * @return $this
	 */
	public function hidden()
	{
		return $self->invisible();
	}

	/**
	 * Reduce current collection to pages with a specified depth only
	 *
	 * @param int $depth The desired depth
	 *
	 * @return $this
	 */
	public function depth($depth)
	{
		$self = clone $this;
		foreach ($self->data as $link => $check) {
			if ($check->depth() != $depth) {
				unset($self->data[$link]);	
			}
		}
		return $self;	
	}

	/**
	 * Sort current collection
	 *
	 * @param string $key The desired key
	 * @param string $ascDesc The desired order
	 *
	 * @return $this
	 */
	public function sortBy($key, $ascDesc = 'asc')
	{
		$self = clone $this;
		uasort($self->data, function($a, $b) use ($key) {
			return strcmp(strval($a->$key()), strval($b->$key()));		
		});
		$ascDesc = strtolower($ascDesc);
		if ($ascDesc == 'desc') {
			$self->reverse();
		}
		return $self;		
	}

	/**
	 * Sort current collection (alias of sortBy())
	 *
	 * @param string $key The desired key
	 * @param string $ascDesc The desired order
	 *
	 * @return $this
	 */
	public function orderBy($key, $ascDesc = 'asc')
	{
		$self = clone $this;
		return $self->sortBy($key, $ascDesc);
	}

	/**
	 * Get children of current collection
	 *
	 * @return $this
	 */
	public function children()
	{
		$self = clone $this;
		return ($self->size() == 1) ? $self->first()->children() : $self;
	}

	/**
	 * Get parameter of the current request
	 *
	 * @param string $key The desired parameter
	 * @param string $default Fallback value 
	 *
	 * @return string The desired parameter or fallback value if desired parameter does not exist 
	 */
	public function param($key, $default)
	{
		return array_key_exists($key, $this->params) ? $this->params[$key] : $default;
	}
	
}

?>