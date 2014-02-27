<?php

/*
 * This file is part of the PRONTO core.
 *
 * (c) Thomas Rasshofer <tr@prontocms.com>
 *
 * For the full copyright and license information, please view http://prontocms.com/license.
 */

namespace Pronto;

use Pronto\ConfigContainer;
use Pronto\FileCollection;
use Pronto\PageCollection;

/**
 * A Page is a page. Surprise, surprise.
 *
 * @author Thomas Rasshofer <tr@prontocms.com>
 */
class Page
{

	/**
	 * Content collection
	 *
	 * @var array
	 */
	protected $data = array();

    /**
	 * Constructor.
	 *
	 * @param string $path Path to the page in the filesystem
	 * @param string $file Path to the file in the filesystem
	 * @param PageCollection $pages The related page collection
	 */
	public function __construct($path, $file, PageCollection &$pages)
	{
		$this->path = $path;
		$this->file = $file;
		$this->pages = $pages;
		$this->url = ConfigContainer::get('rewrite') ? PRONTO_URL.'/'.$path : PRONTO_URL.'/?'.$path;
		$this->id = str_replace('/', '-', $path);
		$this->raw = HelperContainer::content($this->file);
		$dir = dirname($this->raw);
		$this->dir = $dir;
		$info = pathinfo($this->raw);
		$this->template = $info['filename'];
		$this->folder = PRONTO_URL.'/content/'.$this->file.'/';
		$this->visible = !!preg_match('/^([0-9]+)-(.+)$/u', basename($this->dir), $matches);
		$this->hidden = !$this->visible;
		$this->prefix = $this->visible ? intval($matches[1]) : 0;
		$this->content = HelperContainer::parse($this->file);
		foreach ($this->content as $key => $val) {
			if (!in_array($key, array('images', 'videos', 'documents', 'sounds', 'data'))) {
				$this->$key = $val;
			}
		}
		$this->files = new FileCollection($this);
		$files = array(
			'images' => 'Pronto\Image',
			'videos' => 'Pronto\File',
			'documents' => 'Pronto\File',
			'sounds' => 'Pronto\File'
		);
		foreach ($files as $type => $class) {
			$this->$type = new FileCollection($this);
			if(!empty($dir)) {
				foreach (new \DirectoryIterator($dir) as $item) {
					if (!$item->isDot() && !$item->isDir()) {
						if (preg_match('/^(.*)\.('.implode('|', ConfigContainer::get($type)).')$/i', $item->getFilename())) {
							$this->files->add($item->getPathname(), $class);
							$this->$type->add($item->getPathname(), $class);
						}
					}
				}
			}
		}
		$this->depth = (substr_count($this->path, '/')+1);
		$this->active = false;
	}

	/**
	 * Setter
	 */
	public function __set($key, $val)
	{
		$key = strtolower($key);
		$this->data[$key] = $val;
	}

	/**
	 * Getter
	 */
	public function __get($key)
	{
		$key = strtolower($key);
		if (array_key_exists($key, $this->data)) {
			return $this->data[$key];
		}
		return false;
	}

	/**
	 * Caller
	 */
	public function __call($key, $args)
	{
		$key = strtolower($key);
		if (array_key_exists($key, $this->data)) {
			return $this->data[$key];
		}
		return false;
	}

	/**
	 * Is set?
	 */
	public function __isset($property)
	{
		return isset($this->$property);
	}

	/**
	 * Check whether page has files
	 *
	 * @return boolean True if page has files, false if otherwise
	 */
	public function hasFiles()
	{
		return ($this->files()->size() > 0);
	}

	/**
	 * Check whether page has images
	 *
	 * @return boolean True if page has images, false if otherwise
	 */
	public function hasImages()
	{
		return ($this->images()->size() > 0);
	}

	/**
	 * Check whether page has videos
	 *
	 * @return boolean True if page has videos, false if otherwise
	 */
	public function hasVideos()
	{
		return ($this->videos()->size() > 0);
	}

	/**
	 * Check whether page has documents
	 *
	 * @return boolean True if page has documents, false if otherwise
	 */
	public function hasDocuments()
	{
		return ($this->documents()->size() > 0);
	}

	/**
	 * Check whether page has sounds
	 *
	 * @return boolean True if page has sounds, false if otherwise
	 */
	public function hasSounds()
	{
		return ($this->sounds()->size() > 0);
	}

	/**
	 * Get children pages of current page
	 *
	 * @return PageCollection Children pages of current page
	 */
	public function children()
	{
		$self = clone $this;
		$pages = $self->pages;
		$pages->depth++;
		return $pages->filter($self->path.'/*')->is('^'.preg_quote($self->path, '/').'\/([^\/]+)$');
	}

	/**
	 * Check whether page has children pages
	 *
	 * @return boolean True if page has children pages, false if otherwise
	 */
	public function hasChildren()
	{
		return (count($this->children()) > 0);
	}

	/**
	 * Get parent page of current page
	 *
	 * @param boolean $hide Only visible pages
	 *
	 * @return PageCollection Parent page of current page
	 */
	public function parent($hide = false)
	{
		$explode = explode('/', $this->path);
		array_pop($explode);
		$path = implode('/', $explode);
		if (!empty($path)) {
			$return = $this->pages->filter($path.'/*')->is('^'.preg_quote($path, '/').'\/([^\/]+)$');
		} else {
			$return = $this->pages->is('^([^\/]+)$');
		}
		if ($hide) {
			$return = $return->visible();
		}
		return $return;
	}

	/**
	 * Check whether page has previous page
	 *
	 * @return boolean True if page has previous page, false if otherwise
	 */
	public function hasPrev()
	{
		return ($this->prev() !== false);
	}

	/**
	 * Get previous page of current page
	 *
	 * @param boolean $hide Only visible pages
	 *
	 * @return Page|false Previous page of current page if existing, false otherwise
	 */
	public function prev($hide = false)
	{
		$parent = $this->parent($hide);
		foreach ($parent as $check) {
			if ($this->id() != $check->id()) {
				next($array);
			} else {
				break;
			}
		}
		return current($array);
	}

	/**
	 * Check whether page has next page
	 *
	 * @return boolean True if page has next page, false if otherwise
	 */
	public function hasNext()
	{
		return ($this->next() !== false);
	}

	/**
	 * Get next page of current page
	 *
	 * @param boolean $hide Only visible pages
	 *
	 * @return Page|false Next page of current page if existing, false otherwise
	 */
	public function next($hide = false)
	{
		$parent = $this->parent($hide);
		foreach ($parent as $check) {
			if ($this->id() != $check->id()) {
				next($array);
			} else {
				break;
			}
		}
		return current($array);
	}

	/**
	 * Check whether page is the current active page
	 *
	 * @return boolean True if page is the current active page, false if otherwise
	 */
	public function active()
	{
		$path = preg_quote($this->path, '/');
		return $this->active || preg_match('/(^'.$path.'\/(.*))|(^'.$path.'$)/', $this->pages->active()->path);
	}

}

?>
