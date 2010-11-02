<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=global
[END_COT_EXT]
==================== */

/**
 * Overloads standard cot_url() function and loads URL
 * transformation rules
 *
 * @package urleditor
 * @version 0.7.0
 * @author Trustmaster
 * @copyright Copyright (c) Cotonti Team 2010
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

if (!is_array($cot_urltrans))
{
	$cot_urltrans = array();
	$fp = fopen('./datas/urltrans.dat', 'r');
	while ($line = trim(fgets($fp), " \t\r\n"))
	{
		$parts = explode("\t", $line);
		$rule = array();
		$rule['trans'] = $parts[2];
		$parts[1] == '*' ? $rule['params'] = array() : mb_parse_str($parts[1], $rule['params']);
		foreach($rule['params'] as $key => $val)
		{
			if (mb_strpos($val, '|') !== false)
			{
				$rule['params'][$key] = explode('|', $val);
			}
		}
		$cot_urltrans[$parts[0]][] = $rule;
	}
	fclose($fp);
	$cache && $cache->db->store('cot_urltrans', $cot_urltrans, 'system', 1200);
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
	global $cfg, $cot_urltrans;
	// Preprocess arguments
	is_array($params) ? $args = $params : mb_parse_str($params, $args);
	$area = empty($cot_urltrans[$name]) ? '*' : $name;
	// Find first matching rule
	$url = $cot_urltrans['*'][0]['trans']; // default rule
	$rule = array();
	if (!empty($cot_urltrans[$area]))
	{
		foreach($cot_urltrans[$area] as $rule)
		{
			$matched = true;
			foreach($rule['params'] as $key => $val)
			{
				if (empty($args[$key])
					|| (is_array($val) && !in_array($args[$key], $val))
					|| ($val != '*' && $args[$key] != $val))
				{
					$matched = false;
					break;
				}
			}
			if ($matched)
			{
				$url = $rule['trans'];
				break;
			}
		}
	}
	// Some special substitutions
	$mainurl = parse_url($cfg['mainurl']);
	$spec['_area'] = $name;
	$spec['_host'] = $mainurl['host'];
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
				$url = str_replace($m[0], $func($args, $spec), $url);
			}
			elseif (mb_strpos($m[1], '!$') === 0)
			{
				// Unset
				$var = mb_substr($m[1], 2);
				$url = str_replace($m[0], '', $url);
				unset($args[$var]);
			}
			else
			{
				// Substitute
				$var = mb_substr($m[1], 1);
				if (isset($spec[$var]))
				{
					$url = str_replace($m[0], urlencode($spec[$var]), $url);
				}
				elseif (isset($args[$var]))
				{
					$url = str_replace($m[0], urlencode($args[$var]), $url);
					unset($args[$var]);
				}
				else
				{
					$url = str_replace($m[0], urlencode($GLOBALS[$var]), $url);
				}
			}
		}
	}
	// Append query string if needed
	if (!empty($args))
	{
		$sep = $htmlspecialchars_bypass ? '&' : '&amp;';
		$sep_len = strlen($sep);
		$qs = mb_strpos($url, '?') !== false ? $sep : '?';
		foreach($args as $key => $val)
		{
			// Exclude static parameters that are not used in format,
			// they should be passed by rewrite rule (htaccess)
			if ($rule['params'][$key] != $val)
			{
				$qs .= $key .'='.urlencode($val).$sep;
			}
		}
		$qs = substr($qs, 0, -$sep_len);
		$url .= $qs;
	}
	// Almost done
	$url .= $tail;
	$url = str_replace('&amp;amp;', '&amp;', $url);
	return $url;
}

?>
