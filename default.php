<?php

/*
 * This file is part of the PRONTO core.
 *
 * (c) Thomas Rasshofer <tr@prontocms.com>
 *
 * For the full copyright and license information, please view http://prontocms.com/license.
 */

use Pronto\ConfigContainer;

/**
 * Default configuration
 */
ConfigContainer::set(array(

	/**
	 * Use XHTML tags if true, do not if otherwise
	 */
	'xhtml' => false,

	/**
	 * Use as home page if provided, do not if false
	 */
	'home' => false,

	/**
	 * Use as error page if provided, do not if false
	 */
	'error' => false,
	
	/**
	 * Send 404 header on errors if true, do not if otherwise
	 */
	'error-header' => true,

	/**
	 * File extension of content- and meta-files
	 */
	'extension' => 'md',

	/**
	 * Pattern for splitting lines
	 */
	'split-line' => '-----',

	/**
	 * Pattern for splitting keys and values
	 */
	'split-key-value' => ':',

	/**
	 * Use mod_rewrite if true, do not if otherwise
	 */
	'rewrite' => true,

	/**
	 * Show PHP errors if true, hide otherwise
	 */
	'debug' => false,

	/**
	 * Use Markdown if true, do not if false
	 */
	'markdown' => true,
	
	/**
	 * File extensions
	 */
	'images' => array(
		'jpg',
		'jpeg',
		'gif',
		'png'
	),
	'videos' => array(
		'mpg',
		'mpeg',
		'mp4',
		'mov',
		'avi',
		'flv'
	),
	'documents' => array(
		'pdf',
		'doc',
		'xls',
		'ppt',
		'docx',
		'xlsx',
		'pptx'
	),
	'sounds' => array(
		'mp3',
		'wav',
		'm4a'
	)

));

?>