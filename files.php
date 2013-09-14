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
use Pronto\File;
use Pronto\Image;
use Pronto\Page;

/**
 * FileCollection is a collection of files.
 *
 * @author Thomas Rasshofer <tr@prontocms.com>
 */
class FileCollection extends Collection
{

	/**
	 * The related page
	 *
	 * @var Page
	 */
	protected $page = null;

    /**
	 * Constructor
	 *
	 * @param Page $page The related page
	 */
	public function __construct(Page &$page)
	{
		$this->page = $page;
	}
	
    /**
	 * Adds a file to the file collection
	 *
	 * @param String $path Path to the file in the filesystem
	 * @param String $type The file's class name for creating an appropriate instance
	 */
	public function add($path, $type)
	{
		$this->data[basename($path)] = new $type($path, $this->page);
	}

}

?>