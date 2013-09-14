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
use Pronto\ShortcodeContainer;
use Pronto\ContentContainer;

/**
 * HelperContainer represents a set of helper functions
 *
 * @author Thomas Rasshofer <tr@prontocms.com>
 */
class HelperContainer
{
	
	/**
	 * Cleans an array and removes empty items
	 *
	 * @param array $level The array
	 *
	 * @return array Cleaned array
	 */			
	public static function clean($var)
	{
		return array_filter($var, function($var) {
			return !empty($var);
		});
	}

	/**
	 * Returns full path
	 *
	 * @param string $path Relative path
	 *
	 * @return string The full path
	 */
	public static function path($path)
	{
		return PRONTO_ROOT.DS.$path;
	}

	/**
	 * Returns full path
	 *
	 * @param string $path Relative path
	 *
	 * @return string The full path
	 */
	public static function file($path)
	{
		return PRONTO_ROOT.DS.$path;
	}
	
	/**
	 * Searches a directory for content files
	 *
	 * @param string $path The directory
	 *
	 * @return string The path to the first content file found
	 */
	public static function content($path)
	{
		$dir = PRONTO_CONTENT.DS.$path;		
		foreach (new \DirectoryIterator($dir) as $item) {
			if ($item->isFile()) {
				if (preg_match('/^(.*)\.'.ConfigContainer::get('extension').'$/i', $item->getFilename())) {
					return $item->getPathname();
				}
			}
		}
	}

	/**
	 * Check whether content file exists
	 *
	 * @param int|null $level The desired level of depth
	 *
	 * @return Page|false The active Page or false when not existing
	 */
	public static function exists($path)
	{
		return file_exists(self::content($path));
	}

	/**
	 * Passes a content file to the parser
	 *
	 * @param string $path The content file
	 *
	 * @return ContentContainer The content container
	 */
	public static function parse($path)
	{
		$return = new ContentContainer();
		if (self::exists($path)) {
			$return = self::parser(self::content($path));			
		}
		return $return;
	}
	
	/**
	 * Parses a content file into a ContentContainer
	 *
	 * @param string $path The content file
	 *
	 * @return ContentContainer The content container
	 */
	public static function parser($path)
	{
		$return = new ContentContainer();
		$content = file_get_contents($path);
		$explode = array_map('trim', explode(ConfigContainer::get('split-line'), $content));
		foreach ($explode as $line) {
			list($key, $value) = array_map('trim', explode(ConfigContainer::get('split-key-value'), $line, 2));
			$key = strtolower($key);
			$return->$key = $value;
		}
		return $return;
	}

	/**
	 * Check whether an URL is relative
	 *
	 * @param string $url The URL
	 *
	 * @return boolean True if URL is relative, false otherwise
	 */
	public static function relative($url)
	{
		return !((substr($url, 0, 1) == '/') || (substr($url, 0, 2) == '//') || stristr($url, '://'));
	}

	/**
	 * Returns string with escaped HTML entities
	 *
	 * @param string $string The string
	 *
	 * @return string The resulting string
	 */
	public static function html($string)
	{
		return htmlspecialchars($string, ENT_COMPAT, 'utf-8');
	}
	
	/**
	 * Returns string with unescaped HTML entities
	 *
	 * @param string $string The string
	 *
	 * @return string The resulting string
	 */
	public static function unhtml($string)
	{
		return htmlspecialchars_decode(strip_tags($string), ENT_COMPAT, 'utf-8');
	}
	
	/**
	 * Detect and execute shortcodes
	 *
	 * @param string $string String to analyze for shortcodes
	 *
	 * @return string The resulting string after executing all found shortcodes
	 */
	public static function shortcodes($string)
	{
		preg_match_all('/\(([^\)]+)\)/s', $string, $matches);		
		if (!empty($matches[0])) {
			foreach ($matches[0] as $key => $val) {
				$search = $matches[0][$key];
				$cache = ' '.$matches[1][$key].' ';
				$tags = preg_split('( ([a-zA-Z0-9\_\-]+)\: )', $cache, null, PREG_SPLIT_DELIM_CAPTURE);
				if (!empty($tags)) {
					array_shift($tags);
					$attributes = array();
					for ($i = 0; $i < count($tags); $i += 2) {
						$key = trim($tags[$i]);
						$value = trim($tags[($i+1)]);
						$attributes[$key] = $value;
					}
					if (!empty($attributes)) {
						$keys = array_keys($attributes);
						$type = array_shift($keys);
						$callback = array(__NAMESPACE__.'\ShortcodeContainer', $type);
						if (ShortcodeContainer::exists($type) && is_callable($callback)) {
							$parsed = call_user_func($callback, $attributes);							
							if ($parsed) {
								$string = str_replace($search, $parsed, $string);
							}
						}
					}
				}
			}
		}
		return $string;
	}
	
	/**
	 * Returns the formatted file size.
	 *
	 * @param int $size Filesize in bytes
	 * @param int $decimals Number of decimals
	 * @param string $decimalPoint Decimal point
	 * @param string $thousandsSeparator Thousands separator
	 *
	 * @return string The formatted file size
	 */
	public function niceSize($size, $decimals = 2, $decimalPoint = '.', $thousandsSeparator = '')
	{
		$sizes = array(
			' B',
			' KB',
			' MB',
			' GB',
			' TB',
			' PB',
			' EB',
			' ZB',
			' YB'
		);
		if (empty($size)) {
			return '0'.$decimalPoint.'00 MB';
		} else {
			return (number_format($size/pow(1024, ($i = floor(log($size, 1024)))), $decimals, $decimalPoint, $thousandsSeparator).$sizes[$i]);
		}
	}

}

?>