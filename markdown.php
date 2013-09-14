<?php

/*
 * This file is part of the PRONTO core.
 *
 * (c) Thomas Rasshofer <tr@prontocms.com>
 *
 * For the full copyright and license information, please view http://prontocms.com/license.
 */

use Pronto\ConfigContainer;

if (ConfigContainer::get('markdown')) {
	define('MARKDOWN_EMPTY_ELEMENT_SUFFIX', (ConfigContainer::get('xtml') ? ' />' : '>'));
}

?>