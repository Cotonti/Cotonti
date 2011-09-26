<?php
/**
 * URL rewriting functions
 *
 * @package urleditor
 * @version 0.9.0
 * @author Trustmaster
 * @copyright Copyright (c) Cotonti Team 2008-2011
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_langfile('urleditor', 'plug');

/**
 * Applies Handly URLs rewrite to current script parameters
 * @global array $cfg 
 */
function cot_apply_rwr()
{
	global $cfg, $structure;
	if (isset($_GET['rwr']) && !empty($_GET['rwr']) && preg_match('`^[\w\p{L}/\-_\ \+]+?$`u', $_GET['rwr']))
	{
		// Ignore ending slash and split the path into parts
		$path = explode('/', (mb_strrpos($_GET['rwr'], '/') == mb_strlen($_GET['rwr']) - 1) ? mb_substr($_GET['rwr'], 0, -1) : $_GET['rwr']);
		$count = count($path);
		
		if ($count == 1)
		{
			if (isset($structure['page'][$path[0]]))
			{
				// Is a category
				$_GET['e'] = 'page';
				$_GET['c'] = $path[0];
			}
			elseif (file_exists($cfg['modules_dir'] . '/' . $path[0]) || file_exists($cfg['plugins_dir'] . '/' . $path[0]))
			{
				// Is an extension
				$_GET['e'] = $path[0];
			}
			else
			{
				// Maybe it is a system page, if not 404 will be given
				$_GET['e'] = 'page';
				$_GET['c'] = 'system';
				$_GET['al'] = urldecode($path[0]);
			}
		}
		else
		{
			// Special shortcut for user profiles
			if ($path[0] == 'users' && $count == 2 && !isset($_GET['m']))
			{
				$_GET['e'] = 'users';
				$_GET['m'] = 'details';
				$_GET['u'] = urldecode($path[1]);
				return;
			}
			$last = $count - 1;
			$ext = (isset($structure['page'][$path[0]])) ? 'page' : $path[0];
			$_GET['e'] = $ext;
			if (isset($structure[$ext][$path[$last]]))
			{
				// Is a category
				$_GET['c'] = $path[$last];
			}
			else
			{
				// Is a page/item
				if ($ext == 'page' || $count > 2)
				{
					$_GET['c'] = $path[$last - 1];
				}
				if (is_numeric($path[$last]))
				{
					$_GET['id'] = $path[$last];
				}
				else
				{
					$_GET['al'] = urldecode($path[$last]);
				}
			}
		}
	}
}

/**
 * Transforms parameters into URL by following user-defined rules.
 * This function can be overloaded by cot_url_custom().
 *
 * @param string $name Module or script name
 * @param mixed $params URL parameters as array or parameter string
 * @param string $tail URL postfix, e.g. anchor
 * @param bool $htmlspecialchars_bypass If TRUE, will not convert & to &amp; and so on.
 * @return string Valid HTTP URL
 * @see cot_url()
 */
function cot_url_custom($name, $params = '', $tail = '', $htmlspecialchars_bypass = false)
{
	global $cfg, $cot_urltrans, $sys;

	// Preprocess arguments
	if (is_string($params))
	{
		$params = cot_parse_str($params);
	}

	// Initialize with something very default
	$url = ($name == 'plug') ? 'index.php' : 'index.php?e=' . $name;
	// Detect search areas
	$areas = array();
	if (isset($cot_urltrans[$name]) && count($cot_urltrans[$name]) > 0)
	{
		$areas[] = $name;
	}
	$areas[] = '*'; // default area rules

	// Find first matching rule
	$rule = array();
	foreach ($areas as $area)
	{
		foreach($cot_urltrans[$area] as $rule)
		{
			$matched = true;
			foreach($rule['params'] as $key => $val)
			{
				if (!isset($params[$key]) || empty($params[$key])
					|| (is_array($val) && !in_array($params[$key], $val))
					|| ($val != '*' && $params[$key] != $val))
				{
					$matched = false;
					break;
				}
			}
			if ($matched)
			{
				$url = $rule['trans'];
				break 2;
			}
		}
	}

	// Some special substitutions
	$spec['_area'] = $name;
	$spec['_host'] = $sys['host'];
	$spec['_rhost'] = $_SERVER['HTTP_HOST'];
	$spec['_path'] = COT_SITE_URI;
	// Transform the data into URL
	if (preg_match_all('#\{(.+?)\}#', $url, $matches, PREG_SET_ORDER))
	{
		foreach($matches as $m)
		{
			if ($p = mb_strpos($m[1], '('))
			{
				// Callback
				$func = mb_substr($m[1], 0, $p);
				$arg = mb_substr($m[1], $p + 1, mb_strpos($m[1], ')') - $p - 1);
				$sub = empty($arg) ? $func($params, $spec) : $func($params, $spec, $arg);
				$url = str_replace($m[0], $sub, $url);
			}
			elseif (mb_strpos($m[1], '!$') === 0)
			{
				// Unset
				$var = mb_substr($m[1], 2);
				$url = str_replace($m[0], '', $url);
				unset($params[$var]);
			}
			else
			{
				// Substitute
				$var = mb_substr($m[1], 1);
				if (isset($spec[$var]))
				{
					$url = str_replace($m[0], urlencode($spec[$var]), $url);
				}
				elseif (isset($params[$var]))
				{
					$url = str_replace($m[0], urlencode($params[$var]), $url);
					unset($params[$var]);
				}
				else
				{
					$url = str_replace($m[0], urlencode($GLOBALS[$var]), $url);
				}
			}
		}
	}
	// Support for i18n parameter
	if (isset($params['l']) && isset($cfg['plugin']['i18n']['rewrite']) && $cfg['plugin']['i18n']['rewrite'])
	{
		// Add with slash at the beginning of the URL
		$p = mb_strpos($url, '://');
		if ($p === false)
		{
			$url = mb_strpos($url, '/') === 0 ? '/' . urlencode($params['l']) . $url : urlencode($params['l']) . '/' . $url;
		}
		else
		{
			$p = mb_strpos($url, '/', $p + 3);
			$url = $p === false ? $url . '/' . urlencode($params['l']) : mb_substr($url, 0, $p) . urlencode($params['l']) . '/' . mb_substr($url, $p + 1);
		}
		unset($params['l']);
	}
	// Append query string if needed
	if (!empty($params))
	{
		$sep = $htmlspecialchars_bypass ? '&' : '&amp;';
		$sep_len = mb_strlen($sep);
		$qs = mb_strpos($url, '?') !== false ? $sep : '?';
		foreach($params as $key => $val)
		{
			// Exclude static parameters that are not used in format,
			// they should be passed by rewrite rule (htaccess)
			if ($rule['params'][$key] != $val)
			{
				$qs .= is_array($val) ? cot_url_encode($key, $val, $sep) : $key . '=' . urlencode($val) . $sep;
			}
		}
		$qs = mb_substr($qs, 0, -$sep_len);
		$url .= $qs;
	}
	// Almost done
	$url .= $tail;
	$url = str_replace('&amp;amp;', '&amp;', $url);
	return $url;
}

/**
 * Category path URL subsitution handler
 * 
 * @global array $structure Site structure categories
 * @param array $params Link parameters
 * @param array $spec Special parameters
 * @param string $arg Callback argument
 * @return string 
 */
function cot_url_catpath(&$params, $spec, $arg = 'c')
{
	global $structure;
	$cat = '';
	$name = $spec['_area'] == 'plug' ? $params['e'] : $spec['_area'];
	if (isset($structure[$name]) && isset($structure[$name][$params[$arg]]))
	{
		$parts = explode('.', $structure[$name][$params[$arg]]['path']);
		$cat = implode('/', array_map('urlencode', $parts));
	}
	else
	{
		$cat = $params[$arg];
	}
	unset($params[$arg]);
	return $cat;
}

/**
 * User name URL subsitution handler
 *
 * @param array $params Link parameters
 * @param array $spec Special parameters
 * @return string 
 */
function cot_url_username(&$params, $spec)
{
	$name = urlencode($params['u']);
	unset($params['m'], $params['id'], $params['u']);
	return $name;
}

?>
