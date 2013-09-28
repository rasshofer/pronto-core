<?php

/*
 * This file is part of the PRONTO core.
 *
 * (c) Thomas Rasshofer <tr@prontocms.com>
 *
 * For the full copyright and license information, please view http://prontocms.com/license.
 */

/**
 * Define shortcuts
 */
define('DS', DIRECTORY_SEPARATOR);
define('PRONTO_ROOT', dirname($_SERVER['SCRIPT_FILENAME']));
define('PRONTO_CORE', PRONTO_ROOT.DS.'pronto');
define('PRONTO_ADDONS', PRONTO_ROOT.DS.'addons');
define('PRONTO_CONTENT', PRONTO_ROOT.DS.'content');
define('PRONTO_TEMPLATES', PRONTO_ROOT.DS.'templates');
define('PRONTO_SCHEME', ((isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ? 'https' : 'http'));
define('PRONTO_SUB', trim(substr(PRONTO_ROOT, strlen($_SERVER['DOCUMENT_ROOT'])), '/'));
define('PRONTO_URL', trim(PRONTO_SCHEME.'://'.$_SERVER['HTTP_HOST'].'/'.PRONTO_SUB, '/'));

/**
 * Check PHP version
 */
if (version_compare(PHP_VERSION, '5.3.0') < 0)
{
	die('Sorry, PRONTO needs at least PHP 5.3');
}

/**
 * Load dependencies and configurations to initialize the core.
 */
require_once(PRONTO_CORE.DS.'load.php');
require_once(PRONTO_ROOT.DS.'config.php');
require_once(PRONTO_CORE.DS.'addons.php');

use Pronto\ConfigContainer;
use Pronto\GlobalContainer;
use Pronto\PageCollection;

/**
 * Enable error reporting if debug mode acitve, disable otherwise
 */
if (ConfigContainer::get('debug')) {
	error_reporting(E_ALL);
} else {
	error_reporting(0);
}

/**
 * Initialize
 */
$pages = new PageCollection();
$page = $pages->active();

/**
 * Store $pages and $page for global use
 */
GlobalContainer::set(array(
	'pages' => $pages,
	'page' => $page
));

/**
 * Print requested page
 */
if ($page) {
	template($page->template());
}

?>