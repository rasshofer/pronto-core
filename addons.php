<?php

/*
 * This file is part of the PRONTO core.
 *
 * (c) Thomas Rasshofer <tr@prontocms.com>
 *
 * For the full copyright and license information, please view http://prontocms.com/license.
 */

/**
 * Load custom add-ons
 */
if (file_exists(PRONTO_ADDONS) && is_dir(PRONTO_ADDONS)) {
	foreach (new GlobIterator(PRONTO_ADDONS.DS.'*.php') as $item) {
		require_once($item->getPathname());
	}
}

?>