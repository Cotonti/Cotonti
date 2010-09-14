<?php
/**
 * Deprecated and obsolete functions library for backwards compatibility
 *
 * @package Cotonti
 * @version 0.7.0
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

// TODO eliminate this function
function cot_build_pfs($id, $c1, $c2, $title)
{
	global $L, $cfg, $usr, $cot_groups;
	if ($cfg['disable_pfs'])
	{ $res = ''; }
	else
	{
		if ($id==0)
		{ $res = "<a href=\"javascript:pfs('0','".$c1."','".$c2."')\">".$title."</a>"; }
		elseif ($cot_groups[$usr['maingrp']]['pfs_maxtotal']>0 && $cot_groups[$usr['maingrp']]['pfs_maxfile']>0 && cot_auth('pfs', 'a', 'R'))
		{ $res = "<a href=\"javascript:pfs('".$id."','".$c1."','".$c2."')\">".$title."</a>"; }
		else
		{ $res = ''; }
	}
	return($res);
}

/**
 * Returns user PM link
 *
 * @param int $user User ID
 * @return string
 */
// TODO this function should be replaced with some hook-based integration
function cot_build_pm($user)
{
	global $usr, $L, $R;
	return cot_rc_link(cot_url('pm', 'm=send&to='.$user), $R['pm_icon'], array('title' => $L['pm_sendnew']));
}

/**
 * Makes HTML sequences safe
 *
 * @deprecated
 * @param string $text Source string
 * @return string
 */
function cot_cc($text)
{
	/*$text = str_replace(
	array('{', '<', '>' , '$', '\'', '"', '\\', '&amp;', '&nbsp;'),
	array('&#123;', '&lt;', '&gt;', '&#036;', '&#039;', '&quot;', '&#92;', '&amp;amp;', '&amp;nbsp;'), $text);
	return $text;*/
	trigger_error('cot_cc() is deprecated since Cotonti Genoa, use htmlspecialchars() instead');
	return htmlspecialchars($text);
}

// FIXME this function is obsolete, or meta/title generation must be reworked
function cot_htmlmetas()
{
		global $cfg;
		$contenttype = ($cfg['doctypeid']>2 && $cfg['xmlclient']) ? "application/xhtml+xml" : "text/html";
		$result = "<meta http-equiv=\"content-type\" content=\"".$contenttype."; charset=UTF-8\" />
<meta name=\"description\" content=\"".$cfg['maintitle']." - ".$cfg['subtitle']."\" />
<meta name=\"keywords\" content=\"".$cfg['metakeywords']."\" />
<meta name=\"generator\" content=\"Cotonti http://www.cotonti.com\" />
<meta http-equiv=\"expires\" content=\"Fri, Apr 01 1974 00:00:00 GMT\" />
<meta http-equiv=\"pragma\" content=\"no-cache\" />
<meta http-equiv=\"cache-control\" content=\"no-cache\" />
<meta http-equiv=\"last-modified\" content=\"".gmdate("D, d M Y H:i:s")." GMT\" />
<link rel=\"shortcut icon\" href=\"favicon.ico\" />
";
		return ($result);
}

/**
 * Renders page navigation bar
 *
 * @deprecated Siena 0.7.0 - 23.01.2010, use cot_pagenav() instead
 * @see cot_pagenav
 * @param string $url Basic URL
 * @param int $current Current page number
 * @param int $entries Total rows
 * @param int $perpage Rows per page
 * @param string $characters It is symbol for parametre which transfer pagination
 * @param bool $ajax Add AJAX support
 * @param string $target_div Target div ID if $ajax is true
 * @return string
 */
function cot_pagination($url, $current, $entries, $perpage, $characters = 'd', $ajax = false, $target_div = '')
{
	if (function_exists('cot_pagination_custom'))
	{
		// For custom pagination functions in plugins
		return cot_pagination_custom($url, $current, $entries, $perpage, $characters, $onclick, $object);
	}

	if ($entries <= $perpage)
	{
		return '';
	}
	$each_side = 3; // Links each side
	$address = $url.((mb_strpos($url, '?') !== false) ? '&amp;' : '?').$characters.'=';

	$totalpages = ceil($entries / $perpage);
	$currentpage = floor($current / $perpage) + 1;
	$cur_left = $currentpage - $each_side;
	if ($cur_left < 1) $cur_left = 1;
	$cur_right = $currentpage + $each_side;
	if ($cur_right > $totalpages) $cur_right = $totalpages;

	$event = $ajax ? ' class="ajax"' : '';
	$name = $ajax && !empty($target_div) ? ' name="'.$taget_div.'"' : '';

	$before = '';
	$pages = '';
	$after = '';
	$i = 1;
	$n = 0;
	while ($i < $cur_left)
	{
		$k = ($i - 1) * $perpage;
		$before .= '<span class="pagenav_pages"><a href="'.$address.$k.'"'.$event.$name.'>'.$i.'</a></span>';
		$i *= ($n % 2) ? 2 : 5;
		$n++;
	}
	for ($j = $cur_left; $j <= $cur_right; $j++)
	{
		$k = ($j - 1) * $perpage;
		$class = $j == $currentpage ? 'current' : 'pages';
		$pages .= '<span class="pagenav_'.$class.'"><a href="'.$address.$k.'"'.$event.$name.'>'.$j.'</a></span>';
	}
	while ($i <= $cur_right)
	{
		$i *= ($n % 2) ? 2 : 5;
		$n++;
	}
	while ($i < $totalpages)
	{
		$k = ($i - 1) * $perpage;
		$after .= '<span class="pagenav_pages"><a href="'.$address.$k.'"'.$event.$name.'>'.$i.'</a></span>';
		$i *= ($n % 2) ? 5 : 2;
		$n++;
	}
	$pages = $before.$pages.$after;

	return $pages;
}

/**
 * Renders page navigation previous/next buttons
 *
 * @deprecated Siena 0.7.0 - 23.01.2010, use cot_pagenav() instead
 * @see cot_pagenav()
 * @param string $url Basic URL
 * @param int $current Current page number
 * @param int $entries Total rows
 * @param int $perpage Rows per page
 * @param bool $res_array Return results as array
 * @param string $characters It is symbol for parametre which transfer pagination
 * @param bool $ajax Add AJAX support
 * @param string $target_div Target div ID if $ajax is true
 * @return mixed
 */
function cot_pagination_pn($url, $current, $entries, $perpage, $res_array = FALSE, $characters = 'd', $ajax = false, $target_div = '')
{
	if (function_exists('cot_pagination_pn_custom'))
	{
		// For custom pagination functions in plugins
		return cot_pagination_pn_custom($url, $current, $entries, $perpage, $res_array, $characters, $onclick, $object);
	}

	global $L;

	$address = $url.((mb_strpos($url, '?') !== false) ? '&amp;' : '?').$characters.'=';
	$totalpages = ceil($entries / $perpage);
	$currentpage = floor($current / $perpage) + 1;

	$event = $ajax ? ' class="ajax"' : '';
	$name = $ajax && !empty($target_div) ? ' name="'.$taget_div.'"' : '';

	if ($current > 0)
	{
		$prev_n = $current - $perpage;
		if ($prev_n < 0) { $prev_n = 0; }
		$prev = '<span class="pagenav_prev"><a href="'.$address.$prev_n.'"'.$event.$name.'>'.$L['pagenav_prev'].'</a></span>';
		$first = '<span class="pagenav_first"><a href="'.$address.'0"'.$event.$name.'>'.$L['pagenav_first'].'</a></span>';
	}

	if (($current + $perpage) < $entries)
	{
		$next_n = $current + $perpage;
		$next = '<span class="pagenav_next"><a href="'.$address.$next_n.'"'.$event.$name.'>'.$L['pagenav_next'].'</a></span>';
		$last_n = ($totalpages - 1) * $perpage;
		$last = '<span class="pagenav_last"><a href="'.$address.$last_n.'"'.$event.$name.'>'.$L['pagenav_last'].'</a></span>';
	}

	$res_l = $first.$prev;
	$res_r = $next.$last;
	return $res_array ? array($res_l, $res_r) : $res_l.' '.$res_r;
}

// TODO this function is obsolete, doctype should be set in header.tpl
function cot_setdoctype($type)
{
	switch($type)
	{
		case '0': // HTML 4.01
			return ("<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\" \"http://www.w3.org/TR/html4/strict.dtd\">");
			break;

		case '1': // HTML 4.01 Transitional
			return ("<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">");
			break;

		case '2': // HTML 4.01 Frameset
			return ("<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Frameset//EN\" \"http://www.w3.org/TR/html4/frameset.dtd\">");
			break;

		case '3': // XHTML 1.0 Strict
			return ("<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">");
			break;

		case '4': // XHTML 1.0 Transitional
			return ("<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">");
			break;

		case '5': // XHTML 1.0 Frameset
			return ("<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Frameset//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd\">");
			break;

		case '6': // XHTML 1.1
			return ("<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.1//EN\" \"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd\">");
			break;

		case '7': // XHTML 2  ;]
			return ("<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 2//EN\" \"http://www.w3.org/TR/xhtml2/DTD/xhtml2.dtd\">");
			break;

		default: // ...
			return ("<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">");
			break;
	}
}

/**
 * Returns XSS protection code
 *
 * @deprecated This function is not needed anymore, use global $sys['xk'] value instead
 * @return string
 */
function cot_sourcekey()
{
	global $sys;
	return $sys['xk'];
}
?>
