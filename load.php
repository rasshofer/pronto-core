<?php

/*
 * This file is part of the PRONTO core.
 *
 * (c) Thomas Rasshofer <tr@prontocms.com>
 *
 * For the full copyright and license information, please view http://prontocms.com/license.
 */

/**
 * Load required components and dependencies
 */
foreach (array(
	'container',
	'collection',
	'config',
	'globals',
	'default',
	'file',
	'image',
	'files',
	'content',
	'helper',
	'page',
	'pages',
	'template',
	'shortcodes',
	'parsedown'.DS.'Parsedown',
	'parsedown-extra'.DS.'ParsedownExtra'
) as $file) {
	require_once(PRONTO_CORE.DS.$file.'.php');
}

?>
