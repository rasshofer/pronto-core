<?php

/*
 * This file is part of the PRONTO core.
 *
 * (c) Thomas Rasshofer <tr@prontocms.com>
 *
 * For the full copyright and license information, please view http://prontocms.com/license.
 */

namespace Pronto;

use Pronto\File;
use Pronto\Page;
use Pronto\ConfigContainer;

/**
 * Image is an extended file.
 *
 * @author Thomas Rasshofer <tr@prontocms.com>
 */
class Image extends File
{

    /**
	 * Constructor
	 *
	 * @param String $path Path to the image in the filesystem
	 * @param Page $page The related page
	 */
	public function __construct($path, Page $page)
	{
		if (file_exists($path)) {
			$size = getimagesize($path);
			if ($size) {
				parent::__construct($path, $page);
				$this->width = $size[0];
				$this->height = $size[1];
				$this->mime = $size['mime'];
			}
		}
	}

    /**
	 * Returns the image as a HTML element
	 *
	 * @return string The HTML element
	 */
	public function __toString()
	{
		$xhtml = ConfigContainer::get('xtml') ? ' /' : '';
		return '<img src="'.$this->url().'" width="'.$this->width().'" height="'.$this->height().'" alt="'.$this->alt().'"'.$xhtml.'>';
	}

	/**
	 * Resizes the image to a maximum width or height of $max
	 *
	 * @param int $max The maximum width or height
	 *
	 * @return $this
	 */
	public function max($max)
	{
		$self = clone $this;
		if ($self->width > $self->height) {
			$self->maxWidth($max);
		} else {
			$self->maxHeight($max);
		}
		return $self;
	}

	/**
	 * Resizes the image to a maximum width of $max
	 *
	 * @param int $max The maximum width
	 *
	 * @return $this
	 */
	public function maxWidth($max)
	{
		$self = clone $this;
		if ($self->width > $max) {
			$self->height = round($max/$self->width*$self->height);
			$self->width = $max;
		}
		return $self;
	}

	/**
	 * Resizes the image to a maximum height of $max
	 *
	 * @param int $max The maximum height
	 *
	 * @return $this
	 */
	public function maxHeight($max)
	{
		$self = clone $this;
		if ($self->height > $max) {
			$self->width = round($max/$self->height*$self->width);
			$self->height = $max;
		}
		return $self;
	}

}

?>