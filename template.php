<?php

/*
 * This file is part of the PRONTO core.
 *
 * (c) Thomas Rasshofer <tr@prontocms.com>
 *
 * For the full copyright and license information, please view http://prontocms.com/license.
 */

use Pronto\ConfigContainer;
use Pronto\GlobalContainer;
use Pronto\HelperContainer;

/**
 * Fill template with data and print the generated contents
 *
 * @param string $template Filename of the desired template
 * @param array $data Data to be passed to the template
 */
function template($template, $data = array())
{
	$file = PRONTO_TEMPLATES.DS.$template.'.php';
	if (!file_exists($file)) {
		return false;
	}
	@extract(GlobalContainer::get());
	@extract($data);
	include($file);
}

/**
 * Fill template with data and print the generated contents (alias of template())
 *
 * @param string $template Filename of the desired template
 * @param array $data Data to be passed to the template
 */
function snippet($template, $data = array())
{
	template($template, $data);
}

/**
 * Generate absolute URL
 *
 * @param string $url Relative URL
 *
 * @return string Absolute URL
 */
function get_url($url = null)
{
	return is_null($url) ? PRONTO_URL : HelperContainer::relative($url) ? PRONTO_URL.'/'.$url : $url;
}

/**
 * Print absolute URL
 *
 * @param string $url Relative URL
 */
function url($url = null)
{
	echo get_url($url);
}

/**
 * Generate CSS element
 *
 * @param string $url Stylesheet URL
 * @param string|false $media Media attribute
 *
 * @return string CSS element
 */
function get_css($url, $media = false)
{
	$xhtml = ConfigContainer::get('xtml') ? ' /' : '';
	$media = !empty($media) ? ' media="'.$media.'"' : '';
	return '<link rel="stylesheet" href="'.get_url($url).'"'.$media.' type="text/css"'.$xhtml.'>'."\n";
}

/**
 * Print CSS element
 *
 * @param string $url Stylesheet URL
 * @param string|false $url Media attribute
 */
function css($url, $media = false)
{
	echo get_css($url, $media);
}

/**
 * Generate JavaScript element
 *
 * @param string $url JavaScript URL
 * @param boolean|false $async Async attribute
 *
 * @return string JavaScript element
 */
function get_js($url, $async = false)
{
	$async = $async ? ' async="async"' : '';
	return '<script src="'.get_url($url).'" type="text/javascript"'.$async.'></script>'."\n";
}

/**
 * Print JavaScript element
 *
 * @param string $url JavaScript URL
 * @param boolean|false $async Async attribute
 */
function js($url, $async = false)
{
	echo get_js($url, $async);
}

/**
 * Get parameter of the current request
 *
 * @param string $key The desired parameter
 * @param string $default Fallback value
 *
 * @return string The desired parameter or fallback value if desired parameter does not exist
 */
function param($key, $default = null)
{
	$pages = GlobalContainer::get('pages');
	return $pages->param($key, $default);
}

/**
 * Get parameter of the current request (alias of param())
 *
 * @param string $key The desired parameter
 * @param string $default Fallback value
 *
 * @return string The desired parameter or fallback value if desired parameter does not exist
 */
function params($key, $default = null)
{
	return param($key, $default);
}

/**
 * Generate string with converted shortcodes (and Markdown, if set)
 *
 * @param string $string The string
 */
function get_text($string)
{
	if (ConfigContainer::get('markdown')) {
		return shortcodes(GlobalContainer::get('parsedown-extra')->text($string));
	} else {
		return shortcodes($string);
	}
}

/**
 * Print string with converted shortcodes (and Markdown, if set)
 *
 * @param string $string The string
 */
function text($string)
{
	echo get_text($string);
}

/**
 * Generate string with escaped HTML entities
 *
 * @param string $string The string
 */
function get_html($string)
{
	return HelperContainer::html($string);
}

/**
 * Print string with escaped HTML entities
 *
 * @param string $string The string
 */
function html($string)
{
	echo get_html($string);
}

/**
 * Print string with unescaped HTML entities
 *
 * @param string $string The string
 */
function get_unhtml($string)
{
	return HelperContainer::unhtml($string);
}
/**
 * Print string with unescaped HTML entities
 *
 * @param string $string The string
 */
function unhtml($string)
{
	echo get_unhtml($string);
}

/**
 * Print string with converted shortcodes
 *
 * @param string $string The string
 */
function shortcodes($string)
{
	return HelperContainer::shortcodes($string);
}

?>
