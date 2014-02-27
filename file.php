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
use Pronto\HelperContainer;
use Pronto\Page;

/**
 * File is a file. Surprise, surprise.
 *
 * @author Thomas Rasshofer <tr@prontocms.com>
 */
class File
{

	/**
	 * Content collection
	 *
	 * @var array
	 */
	protected $data = array();

    /**
     * Constructor
     *
     * @param String $path Path to the file in the filesystem
     * @param Page $page The related page
     */
	public function __construct($path, Page $page)
	{
		if (file_exists($path)) {
			$info = pathinfo($path);
			$this->path = $path;
			$this->name = $info['filename'];
			$this->filename = basename($path);
			$this->extension = $info['extension'];
			$this->url = $page->folder().$this->filename;
			$this->modified = filemtime($path);
			$this->size = filesize($path);
			$file = $path.'.'.ConfigContainer::get('extension');
			if (file_exists($file)) {
				$this->content = HelperContainer::parser($file);
				foreach ($this->content as $key => $val) {
					$this->$key = $val;
				}
			}
		}
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
	 * Returns the file as a HTML element.
	 *
	 * @return string The HTML element
	 */
	public function __toString()
	{
		return '<a href="'.$this->url().'">'.$this->url().'</a>';
	}

	/**
	 * Returns the formatted file size.
	 *
	 * @param int $decimals Number of decimals
	 * @param string $decimalPoint Decimal point
	 * @param string $thousandsSeparator Thousands separator
	 *
	 * @return string The formatted file size
	 */
	public function niceSize($decimals = 2, $decimalPoint = '.', $thousandsSeparator = '')
	{
		return HelperContainer::niceSize($this->size, $decimals, $decimalPoint, $thousandsSeparator);
	}

}

?>
