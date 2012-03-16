<?php
/**
 * Deprecated and obsolete functions library for backwards compatibility
 *
 * @package genoa
 * @version 0.9.0
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

// Requirements

require_once cot_incfile('auth');
require_once cot_incfile('extensions');
require_once cot_incfile('forms');
require_once cot_incfile('uploads');
require_once cot_incfile('admin',		'module');
require_once cot_incfile('page',		'module');
require_once cot_incfile('pfs',			'module');
require_once cot_incfile('users',		'module');
require_once cot_incfile('comments',	'plug');
require_once cot_incfile('ratings',		'plug');
require_once cot_incfile('tags',		'plug');
require_once cot_incfile('trashcan',	'plug');
require_once cot_incfile('userimages',	'plug');

// SED flags

define('SED_CODE', true);
define('SED_PLUG', true);

// Some obsolete globals
$sed_cat = &$structure['page'];
$sed_groups = &$cot_groups;
$sed_extensions = &$cot_extensions;
$sed_countries = &$cot_countries;
$sed_usersonline = &$cot_usersonline;
$sed_plugins = &$cot_plugins;
$cfg['maxlistsperpage'] = $cfg['page']['maxlistsperpage'];

// Functions

function sed_alphaonly($text)
{
	return cot_alphaonly($text);
}

/*
 * ================================= Authorization Subsystem ==================================
 */

function sed_auth($area, $option, $mask = 'RWA')
{
	return cot_auth($area, $option, $mask);
}

function sed_auth_build($userid, $maingrp=0)
{
	return cot_auth_build($userid, $maingrp);
}

function sed_auth_clear($id='all')
{
	return cot_auth_clear($id);
}

function sed_block($allowed)
{
	return cot_block($allowed);
}

function sed_blockguests()
{
	return cot_blockguests();
}

/*
 * ================================= BBCode Parser API ==================================
 */

function sed_bbcode_add($name, $mode, $pattern, $replacement, $container = true, $priority = 128, $plug = '', $postrender = false)
{
	return cot_bbcode_add($name, $mode, $pattern, $replacement, $container, $priority, $plug, $postrender);
}

function sed_bbcode_remove($id = 0, $plug = '')
{
	return cot_bbcode_remove($id, $plug);
}

function sed_bbcode_update($id, $enabled, $name, $mode, $pattern, $replacement, $container, $priority = 128, $postrender = false)
{
	return cot_bbcode_update($id, $enabled, $name, $mode, $pattern, $replacement, $container, $priority, $postrender);
}

function sed_bbcode_load()
{
	return cot_bbcode_load();
}

function sed_bbcode_clearcache()
{
	return cot_bbcode_clearcache();
}

function sed_bbcode_parse($text, $post = false)
{
	if ($post)
	{
		return $text;
	}
	else
	{
		return cot_bbcode_parse($text);
	}
}

function sed_bbcode_cdata($text)
{
	return cot_bbcode_cdata($text);
}

function sed_parse_autourls($text)
{
	return cot_parse_autourls($text);
}

function sed_parse($text, $parse_bbcodes = TRUE, $parse_smilies = TRUE, $parse_newlines = TRUE)
{
	$enable_markup = $parse_bbcodes;
	return cot_parse($text, $enable_markup, 'bbcode');
}

function sed_post_parse($text, $area = '')
{
	return $text;
}

/*
 * =========================== Output forming functions ===========================
 */

function sed_build_addtxt($c1, $c2)
{
	return '';
}

function sed_build_age($birth)
{
	return cot_build_age($birth);
}

function sed_build_catpath($cat, $mask)
{
	global $cfg;
	return cot_breadcrumbs(cot_structure_buildpath('page', $cat), $cfg['homebreadcrumb']);
}

function sed_build_comments($code, $url, $display = true)
{
	global $cfg;

	$ext_prefix = mb_substr($code, 0, 1);
	$code = mb_substr($code, 1);
	switch ($ext_prefix)
	{
		case 'p':
			$ext_name = 'page';
			break;
		case 'g':
			$ext_name = 'gal';
			break;
		case 'u':
			$ext_name = 'userwall';
			break;
		case 'v':
			$ext_name = 'polls';
			break;
		default:
			trigger_error('sed_build_comments() is deprecated. Use comments plugin and its functions instead.');
			break;
	}

	$com_display = $display ? cot_comments_display($ext_name, $code) : '';
	$com_count = cot_comments_count($ext_name, $code);
	$com_link = cot_rc('comments_link', array(
		'url' => $url . '#comments',
		'count' => $cfg['plugin']['comments']['countcomments'] ? cot_comments_count($ext_name, $code) : ''
	));
	return array($com_link, $com_display, $com_count);
}

function sed_build_country($flag)
{
	return cot_build_country($flag);
}

function sed_build_email($email, $hide = false)
{
	return cot_build_email($email, $hide);
}

function sed_build_extrafields($rowname, $tpl_tag, $extrafields, $data=array(), $importnew=FALSE)
{
	trigger_error('Extrafields API has been changed in Siena. Please use system/extrafields.php API.');
}

function sed_build_extrafields_data($rowname, $type, $field_name, $value)
{
	trigger_error('Extrafields API has been changed in Siena. Please use system/extrafields.php API.');
}

function sed_build_flag($flag)
{
	return cot_build_flag($flag);
}

function sed_build_forums($sectionid, $title, $category, $link = TRUE, $master = false)
{
	trigger_error('Forums are now using category structure, sed_build_forums() function is invalid. See cot_forums_buildpath().');
}

function sed_build_group($grpid)
{
	return cot_build_group($grpid);
}

function sed_build_groupsms($userid, $edit=FALSE, $maingrp=0)
{
	return cot_build_groupsms($userid, $edit, $maingrp);
}

function sed_build_icq($text)
{
	trigger_error('sed_build_icq() is deprecated.');
	return '';
}

function sed_build_ipsearch($ip)
{
	return cot_build_ipsearch($ip);
}

function sed_build_msn($msn)
{
	return sed_build_email($msn);
}

function sed_build_oddeven($number)
{
	return cot_build_oddeven($number);
}

function sed_build_pfs($id, $c1, $c2, $title)
{
	return cot_build_pfs($id, $c1, $c2, $title);
}

/**
 * Returns user PM link
 *
 * @param int $user User ID
 * @return string
 */
function sed_build_pm($user)
{
	global $usr, $L, $R;
	return cot_rc_link(cot_url('pm', 'm=send&to='.$user), $R['pm_icon'], array('title' => $L['pm_sendnew']));
}

function sed_build_ratings($code, $url, $display)
{
	$ext_prefix = mb_substr($code, 0, 1);
	$code = mb_substr($code, 1);
	switch ($ext_prefix)
	{
		case 'p':
			$ext_name = 'page';
			break;
		case 'g':
			$ext_name = 'gal';
			break;
		case 'u':
			$ext_name = 'userwall';
			break;
		case 'v':
			$ext_name = 'polls';
			break;
		default:
			trigger_error('sed_build_ratings() is deprecated. Use ratings plugin and its functions instead.');
			break;
	}

	return array(cot_ratings_display($ext_name, $code, '', $display == false), '');
}

function sed_build_stars($level)
{
	return cot_build_stars($level);
}

function sed_build_timegap($t1,$t2)
{
	return cot_build_timegap($t1, $t2);
}

function sed_build_timezone($tz)
{
	return cot_build_timezone($tz);
}

function sed_build_url($text, $maxlen=64)
{
	return cot_build_url($text, $maxlen);
}

function sed_build_user($id, $user)
{
	return cot_build_user($id, $user);
}

function sed_build_userimage($image, $type='none')
{
	return cot_userimages_build($image);
}

function sed_build_usertext($text)
{
	return cot_build_usertext($text);
}

/*
 * ================================ Cache Subsystem ================================
 */

/**
 * Clears cache item
 *
 * @param string $name Item name
 * @return bool
 * @global CotDB $db
 */
function sed_cache_clear($name)
{
	global $db, $db_cache;
	//trigger_error('Deprecated since 0.7.0, use $cache->db object instead');
	$db->query("DELETE FROM $db_cache WHERE c_name='$name'");
	return(TRUE);
}

/**
 * Clears cache completely
 *
 * @return bool
 * @global CotDB $db
 */
function sed_cache_clearall()
{
	global $db, $db_cache;
	//trigger_error('Deprecated since 0.7.0, use $cache->db object instead');
	$db->query("DELETE FROM $db_cache");
	return TRUE;
}

/**
 * Clears HTML-cache
 *
 * @todo Add trigger support here to clean non-standard html fields
 * @return bool
 */
function sed_cache_clearhtml()
{
	// There is no HTML-cache in Siena
	return true;
}

/**
 * Fetches cache value
 *
 * @param string $name Item name
 * @return mixed
 * @global CotDB $db
 */
function sed_cache_get($name)
{
	global $db, $cfg, $sys, $db_cache;
	//trigger_error('Deprecated since 0.7.0, use $cache->db object instead');
	$sql = $db->query("SELECT c_value FROM $db_cache WHERE c_name='$name' AND c_expire>'".$sys['now']."'");
	if ($row = $sql->fetch())
	{
		$sql->closeCursor();
		return(unserialize($row['c_value']));
	}
	else
	{
		return(FALSE);
	}
}

/**
 * Get all cache data and import it into global scope
 *
 * @param int $auto Only with autoload flag
 * @return mixed
 * @global CotDB $db
 */
function sed_cache_getall($auto = 1)
{
	global $db, $cfg, $sys, $db_cache;
	//trigger_error('Deprecated since 0.7.0, use $cache->db object instead');
	$sql = $db->query("DELETE FROM $db_cache WHERE c_expire<'".$sys['now']."'");
	if ($auto)
	{
		$sql = $db->query("SELECT c_name, c_value FROM $db_cache WHERE c_auto=1");
	}
	else
	{
		$sql = $db->query("SELECT c_name, c_value FROM $db_cache");
	}
	if ($sql->rowCount() > 0)
	{
		return($sql);
	}
	else
	{
		return(FALSE);
	}
}

/**
 * Puts an item into cache
 *
 * @param string $name Item name
 * @param mixed $value Item value
 * @param int $expire Expires in seconds
 * @param int $auto Autload flag
 * @return bool
 * @global CotDB $db
 */
function sed_cache_store($name,$value,$expire,$auto="1")
{
	global $db, $db_cache, $sys, $cfg;
	//trigger_error('Deprecated since 0.7.0, use $cache->db object instead');
	if (!$cfg['cache']) return(FALSE);
	$sql = $db->query("REPLACE INTO $db_cache (c_name, c_value, c_expire, c_auto) VALUES ('$name', '".$db->prep(serialize($value))."', '".($expire + $sys['now'])."', '$auto')");
	return(TRUE);
}

/*
 * ===============================================================================
 */

/**
 * Makes HTML sequences safe
 *
 * @deprecated
 * @param string $text Source string
 * @return string
 */
function sed_cc($text)
{
	/*$text = str_replace(
	array('{', '<', '>' , '$', '\'', '"', '\\', '&amp;', '&nbsp;'),
	array('&#123;', '&lt;', '&gt;', '&#036;', '&#039;', '&quot;', '&#92;', '&amp;amp;', '&amp;nbsp;'), $text);
	return $text;*/
	trigger_error('cot_cc() is deprecated since Cotonti Genoa, use htmlspecialchars() instead');
	return htmlspecialchars($text);
}

function sed_check_xg()
{
	return cot_check_xg();
}

function sed_check_xp()
{
	return cot_check_xp();
}

/**
 * Truncates a post and makes sure parsing is correct
 *
 * @param string $text Post text
 * @param int $max_chars Max. length
 * @param bool $parse_bbcodes Parse bbcodes
 * @return unknown
 */
function sed_cutpost($text, $max_chars, $parse_bbcodes = true)
{
	$text = $max_chars == 0 ? $text : cot_cutstring(strip_tags($text), $max_chars);
	// Fix partial cuttoff
	$text = preg_replace('#\[[^\]]*?$#', '...', $text);
	// Parse the BB-codes or skip them
	if ($parse_bbcodes)
	{
		// Parse it
		$text = cot_parse($text);
	}
	else $text = preg_replace('#\[[^\]]+?\]#', '', $text);
	return $text;
}

function sed_cutstring($res, $l)
{
	return cot_cutstring($res, $l);
}

/**
 * Creates image thumbnail
 *
 * @param string $img_big Original image path
 * @param string $img_small Thumbnail path
 * @param int $small_x Thumbnail width
 * @param int $small_y Thumbnail height
 * @param bool $keepratio Keep original ratio
 * @param string $extension Image type
 * @param string $filen Original file name
 * @param int $fsize File size in kB
 * @param string $textcolor Text color
 * @param int $textsize Text size
 * @param string $bgcolor Background color
 * @param int $bordersize Border thickness
 * @param int $jpegquality JPEG quality in %
 * @param string $dim_priority Resize priority dimension
 */
function sed_createthumb($img_big, $img_small, $small_x, $small_y, $keepratio, $extension, $filen, $fsize, $textcolor, $textsize, $bgcolor, $bordersize, $jpegquality, $dim_priority="Width")
{
	if (!function_exists('gd_info'))
	{
		return;
	}

	global $cfg;

	$gd_supported = array('jpg', 'jpeg', 'png', 'gif');

	switch($extension)
	{
		case 'gif':
			$source = imagecreatefromgif ($img_big);
			break;

		case 'png':
			$source = imagecreatefrompng($img_big);
			break;

		default:
			$source = imagecreatefromjpeg($img_big);
			break;
	}

	$big_x = imagesx($source);
	$big_y = imagesy($source);

	if (!$keepratio)
	{
		$thumb_x = $small_x;
		$thumb_y = $small_y;
	}
	elseif ($dim_priority=="Width")
	{
		$thumb_x = $small_x;
		$thumb_y = floor($big_y * ($small_x / $big_x));
	}
	else
	{
		$thumb_x = floor($big_x * ($small_y / $big_y));
		$thumb_y = $small_y;
	}

	if ($textsize==0)
	{
		if ($cfg['th_amode']=='GD1')
		{
			$new = imagecreate($thumb_x+$bordersize*2, $thumb_y+$bordersize*2);
		}
		else
		{
			$new = imagecreatetruecolor($thumb_x+$bordersize*2, $thumb_y+$bordersize*2);
		}

		$background_color = imagecolorallocate ($new, $bgcolor[0], $bgcolor[1] ,$bgcolor[2]);
		imagefilledrectangle ($new, 0,0, $thumb_x+$bordersize*2, $thumb_y+$bordersize*2, $background_color);

		if ($cfg['th_amode']=='GD1')
		{
			imagecopyresized($new, $source, $bordersize, $bordersize, 0, 0, $thumb_x, $thumb_y, $big_x, $big_y);
		}
		else
		{
			imagecopyresampled($new, $source, $bordersize, $bordersize, 0, 0, $thumb_x, $thumb_y, $big_x, $big_y);
		}

	}
	else
	{
		if ($cfg['th_amode']=='GD1')
		{
			$new = imagecreate($thumb_x+$bordersize*2, $thumb_y+$bordersize*2+$textsize*3.5+6);
		}
		else
		{
			$new = imagecreatetruecolor($thumb_x+$bordersize*2, $thumb_y+$bordersize*2+$textsize*3.5+6);
		}

		$background_color = imagecolorallocate($new, $bgcolor[0], $bgcolor[1] ,$bgcolor[2]);
		imagefilledrectangle ($new, 0,0, $thumb_x+$bordersize*2, $thumb_y+$bordersize*2+$textsize*4+14, $background_color);
		$text_color = imagecolorallocate($new, $textcolor[0],$textcolor[1],$textcolor[2]);

		if ($cfg['th_amode']=='GD1')
		{
			imagecopyresized($new, $source, $bordersize, $bordersize, 0, 0, $thumb_x, $thumb_y, $big_x, $big_y);
		}
		else
		{
			imagecopyresampled($new, $source, $bordersize, $bordersize, 0, 0, $thumb_x, $thumb_y, $big_x, $big_y);
		}

		imagestring ($new, $textsize, $bordersize, $thumb_y+$bordersize+$textsize+1, $big_x."x".$big_y." ".$fsize."kb", $text_color);
	}

	switch($extension)
	{
		case 'gif':
			imagegif($new, $img_small);
			break;

		case 'png':
			imagepng($new, $img_small);
			break;

		default:
			imagejpeg($new, $img_small, $jpegquality);
			break;
	}

	imagedestroy($new);
	imagedestroy($source);
}

function sed_die($cond=TRUE)
{
	return cot_die($cond);
}

function sed_diefatal($text='Reason is unknown.', $title='Fatal error')
{
	return cot_diefatal($text, $title);
}

/**
 * Terminates with "disabled" error
 *
 * @param unknown_type $disabled
 */
function sed_dieifdisabled($disabled)
{
	global $env;
	if ($disabled)
	{
		$env['status'] = '403 Forbidden';
		cot_redirect(cot_url('message', "msg=940", '', true));
	}
}

function sed_file_check($path, $name, $ext)
{
	return cot_file_check($path, $name, $ext);
}

/*
 * ==================================== Forum Functions ==================================
 */

function sed_forum_info($id)
{
	trigger_error('Forums are now using category structure. sed_forum_info() is no longer valid.');
}

function sed_forum_prunetopics($mode, $section, $param)
{
	if (is_int($section))
	{
		trigger_error('Forums are now using category structure. String section key expected, integer given.');
	}
	return cot_forums_prunetopics($mode, $section, $param);
}

function sed_forum_sectionsetlast($id)
{
	if (is_int($id))
	{
		trigger_error('Forums are now using category structure. String section key expected, integer given.');
	}
	return cot_forums_sectionsetlast($id);
}

/*
 * =======================================================================================
 */

function sed_getextplugins($hook, $cond='R')
{
	return cot_getextplugins($hook, $cond);
}

function sed_get_comcount($code)
{
	trigger_error('sed_get_comcount() is deprecated. Use comments plugin and its functions instead.');
	return null;
}

function sed_get_uploadmax()
{
	return cot_get_uploadmax();
}

function sed_import($name, $source, $filter, $maxlen=0, $dieonerror=FALSE)
{
	if ($filter == 'STX' || $filter == 'SLU')
	{
		$filter = 'TXT';
	}
	return cot_import($name, $source, $filter, $maxlen, $dieonerror);
}

function sed_infoget($file, $limiter='SED', $maxsize=32768)
{
	return cot_infoget($file, $limiter, $maxsize);
}

/**
 * Outputs standard javascript
 *
 * @deprecated
 * @param string $more Extra javascript
 * @return string
 */
function sed_javascript($more='')
{
	return '';
}

/**
 * Returns a language file path for a plugin or FALSE on error.
 *
 * @param string $name Plugin name
 * @param bool $core Use core module rather than a plugin
 * @return bool
 */
function sed_langfile($name, $core = false, $loadlang=false)
{
	$type = $core ? 'core' : 'plug';
	if ($core && !preg_match('#^admin#', $name) && !preg_match('#^users#', $name))
	{
		$type = 'module';
	}
	return cot_langfile($name, $type, $loadlang);
}

function sed_log($text, $group='def')
{
	return cot_log($text, $group);
}

function sed_log_sed_import($s, $e, $v, $o)
{
	return cot_log_import($s, $e, $v, $o);
}

function sed_mail($fmail, $subject, $body, $headers='', $additional_parameters = null)
{
	return cot_mail($fmail, $subject, $body, $headers, true, $additional_parameters);
}

function sed_htmlmetas()
{
	trigger_error('sed_htmlmetas() is deprecated completely. Use new way of metas output.');
	return '';
}

function sed_mktime($hour = false, $minute = false, $second = false, $month = false, $date = false, $year = false)
{
	return cot_mktime($hour, $minute, $second, $month, $date, $year);
}

function sed_date2stamp($date)
{
	return cot_date2stamp($date);
}

function sed_stamp2date($stamp)
{
	return cot_stamp2date($stamp);
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
function sed_pagination($url, $current, $entries, $perpage, $characters = 'd', $ajax = false, $target_div = '')
{
	if (function_exists('sed_pagination_custom'))
	{
		// For custom pagination functions in plugins
		return sed_pagination_custom($url, $current, $entries, $perpage, $characters, $onclick, $object);
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
function sed_pagination_pn($url, $current, $entries, $perpage, $res_array = FALSE, $characters = 'd', $ajax = false, $target_div = '')
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

function sed_pfs_deleteall($userid)
{
	return cot_pfs_deleteall($userid);
}

function sed_pfs_path($userid)
{
	return cot_pfs_path($userid);
}

function sed_pfs_relpath($userid)
{
	return cot_pfs_relpath($userid);
}

function sed_pfs_thumbpath($userid)
{
	return cot_pfs_thumbpath($userid);
}

function sed_readraw($file)
{
	return cot_readraw($file);
}

function sed_redirect($url)
{
	return cot_redirect($url);
}

function sed_safename($basename, $underscore = true, $postfix = '')
{
	return cot_safename($basename, $underscore, $postfix);
}

function sed_selectbox($check, $name, $values)
{
	return cot_selectbox($check, $name, $values);
}

function sed_selectbox_categories($check, $name, $hideprivate=TRUE)
{
	return cot_selectbox_categories($check, $name, '', $hideprivate);
}

function sed_selectbox_countries($check,$name)
{
	return cot_selectbox_countries($check, $name);
}

function sed_selectbox_date($utime, $mode, $ext='', $max_year = 2030)
{
	return cot_selectbox_date($utime, $mode, $ext, $max_year);
}

function sed_selectbox_folders($user, $skip, $check)
{
	return cot_selectbox_folders($user, $skip, $check);
}

function sed_selectbox_forumcat($check, $name)
{
	trigger_error('Forums now use category structure. Function sed_selectbox_forumcat() is deprecated.');
}

function sed_selectbox_gender($check,$name)
{
	return cot_selectbox_gender($check, $name);
}

function sed_selectbox_groups($check, $name, $skip=array(0))
{
	return cot_selectbox_groups($check, $name, $skip);
}

function sed_selectbox_lang($check, $name)
{
	return cot_selectbox_lang($check, $name);
}

function sed_selectbox_sections($check, $name)
{
	trigger_error('Forums now use category structure. Function sed_selectbox_sections() is deprecated.');
	return null;
}

function sed_selectbox_users($to)
{
	trigger_error('This function is deprecated.');
}

function sed_sendheaders()
{
	return cot_sendheaders();
}

function sed_setdoctype($type)
{
	trigger_error('This function is deprecated.');
}

function sed_shield_clearaction()
{
	return cot_shield_clearaction();
}

function sed_shield_hammer($hammer,$action, $lastseen)
{
	return cot_shield_hammer($hammer, $action, $lastseen);
}

function sed_shield_protect()
{
	return cot_shield_protect();
}

function sed_shield_update($shield_add, $shield_newaction)
{
	return cot_shield_update($shield_add, $shield_newaction);
}

function sed_skinfile($base, $plug = false, $admn = false)
{
	if ($admn)
	{
		return cot_tplfile($base, 'core');
	}
	if ($plug)
	{
		return cot_tplfile($base, 'plug');
	}
	$bname = is_array($base) ? $base[0] : $base;
	if (preg_match('#^users#', $bname))
	{
		return cot_tplfile($base, 'core');
	}
	else
	{
		return cot_tplfile($base, 'module');
	}
}

function sed_smilies($res)
{
	return cot_smilies($res);
}

/**
 * Returns XSS protection code
 *
 * @deprecated This function is not needed anymore, use global $sys['xk'] value instead
 * @return string
 */
function sed_sourcekey()
{
	global $sys;
	return $sys['xk'];
}

/*
 * ===================================== Statistics API ==========================================
 */

function sed_stat_create($name)
{
	return cot_stat_create($name);
}

function sed_stat_get($name)
{
	return cot_stat_get($name);
}

function sed_stat_inc($name)
{
	return cot_stat_inc($name);
}

function sed_stat_update($name)
{
	return cot_stat_update($name);
}

/*
 * =========================================================================================
 */

function sed_stringinfile($file, $str, $maxsize=32768)
{
	return cot_stringinfile($file, $str, $maxsize);
}

/*
 * ===================================== Tags API ==========================================
 */

function sed_tag($tag, $item, $area = 'pages')
{
	return cot_tag($tag, $item, $area);
}

function sed_tag_cloud($area = 'all', $order = 'tag', $limit = null)
{
	return cot_tag_cloud($area, $order, $limit);
}

function sed_tag_complete($tag, $min_length = 3)
{
	return cot_tag_complete($tag, $min_length);
}

function sed_tag_count($tag, $area = '')
{
	return cot_tag_count($tag, $area);
}

function sed_tag_exists($tag)
{
	return cot_tag_exists($tag);
}

function sed_tag_isset($tag, $item, $area = 'pages')
{
	return cot_tag_isset($tag, $item, $area);
}

function sed_tag_list($item, $area = 'pages')
{
	return cot_tag_list($item, $area);
}

function sed_tag_parse($input)
{
	return cot_tag_parse($input);
}

function sed_tag_prep($tag)
{
	return cot_tag_prep($tag);
}

function sed_tag_register($tag)
{
	return cot_tag_register($tag);
}

function sed_tag_remove($tag, $item, $area = 'pages')
{
	return cot_tag_remove($tag, $item, $area);
}

function sed_tag_remove_all($item = 0, $area = 'pages')
{
	return cot_tag_remove_all($item, $area);
}

function sed_tag_title($tag)
{
	return cot_tag_title($tag);
}

function sed_tag_unregister($tag)
{
	return cot_tag_unregister($tag);
}

/*
 * ==========================================================================================
 */

function sed_themefile()
{
	return cot_schemefile();
}

function sed_title($mask, $tags, $data)
{
	trigger_error('Function sed_title() is deprecated. Use cot_title() instead');
}

function sed_trash_put($type, $title, $itemid, $datas)
{
	return cot_trash_put($type, $title, $itemid, $datas);
}

function sed_unique($l=16)
{
	return cot_unique($l);
}

function sed_url($name, $params = '', $tail = '', $header = false)
{
	return cot_url($name, $params, $tail, $header);
}

function sed_url_check($url)
{
	return cot_url_check($url);
}

function sed_urlencode($str, $translit = false)
{
	return $translit ? cot_translit_encode($str) : $str;
}

function sed_urldecode($str, $translit = false)
{
	return $translit ? cot_translit_decode($str) : $str;
}

function sed_uriredir_store()
{
	return cot_uriredir_store();
}

function sed_uriredir_apply($cfg_redir = true)
{
	return cot_uriredir_apply($cfg_redir);
}

function sed_uriredir_redirect($uri)
{
	return cot_uriredir_redirect($uri);
}

function sed_userinfo($id)
{
	return cot_userinfo($id);
}

function sed_userisonline($id)
{
	return cot_userisonline($id);
}

function sed_wraptext($str,$wrap=128)
{
	return cot_wraptext($str, $wrap);
}

function sed_xg()
{
	return cot_xg();
}

function sed_xp()
{
	return cot_xp();
}

function sed_setcookie($name, $value, $expire, $path, $domain, $secure = false, $httponly = false)
{
	return cot_setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
}

function sed_declension($digit, $expr, $onlyword = false, $canfrac = false)
{
    return cot_declension($digit, $expr, $onlyword, $canfrac);
}

function sed_get_plural($plural, $lang, $is_frac = false)
{
   return cot_get_plural($plural, $lang, $is_frac);
}

/**
 * JavaScript HTML obfuscator to protect some parts (like email) from bots
 *
 * @param string $text Source text
 * @return string
 */
function sed_obfuscate($text)
{
	$enc_string = '[';
	$ut = utf8ToUnicode($text);
	$length = count($ut);
	for ($i = 0; $i < $length; $i++)
	{
		$enc_string .= $ut[$i].',';
	}
	$enc_string = substr($enc_string, 0, -1).']';
	$name = 'a'.cot_unique(8);
	$script = '<script type="text/javascript">var '.$name.' = '.$enc_string.','.$name.'_d = ""; for (var ii = 0; ii < '.$name.'.length; ii++) { var c = '.$name.'[ii]; '.$name.'_d += String.fromCharCode(c); } document.write('.$name.'_d)</script>';
	return $script;
}

/**
 * Supplimentary email obfuscator callback
 *
 * @param array $m PCRE entry
 * @return string
 */
function sed_obfuscate_eml($m)
{
	return $m[1] . sed_obfuscate('<a href="mailto:'.$m[2].'">'.$m[2].'</a>');
}

/**
 * Takes an UTF-8 string and returns an array of ints representing the
 * Unicode characters. Astral planes are supported ie. the ints in the
 * output can be > 0xFFFF. Occurrances of the BOM are ignored. Surrogates
 * are not allowed.
 *
 * Returns false if the input string isn't a valid UTF-8 octet sequence.
 *
 * @author Cotonti Team
 * @license Mozilla Public License (MPL)
 * @copyright (c) 2003 Henri Sivonen
 * @param string $str Unicode string
 * @return array
 */
function utf8ToUnicode(&$str)
{
	$mState = 0; // cached expected number of octets after the current octet
	// until the beginning of the next UTF8 character sequence
	$mUcs4  = 0; // cached Unicode character
	$mBytes = 1; // cached expected number of octets in the current sequence

	$out = array();

	$len = strlen($str);
	for ($i = 0; $i < $len; $i++)
	{
		$in = ord($str{$i});
		if (0 == $mState)
		{
			// When mState is zero we expect either a US-ASCII character or a
			// multi-octet sequence.
			if (0 == (0x80 & ($in)))
			{
				// US-ASCII, pass straight through.
				$out[] = $in;
				$mBytes = 1;
			}
			elseif (0xC0 == (0xE0 & ($in)))
			{
				// First octet of 2 octet sequence
				$mUcs4 = ($in);
				$mUcs4 = ($mUcs4 & 0x1F) << 6;
				$mState = 1;
				$mBytes = 2;
			}
			elseif (0xE0 == (0xF0 & ($in)))
			{
				// First octet of 3 octet sequence
				$mUcs4 = ($in);
				$mUcs4 = ($mUcs4 & 0x0F) << 12;
				$mState = 2;
				$mBytes = 3;
			}
			elseif (0xF0 == (0xF8 & ($in)))
			{
				// First octet of 4 octet sequence
				$mUcs4 = ($in);
				$mUcs4 = ($mUcs4 & 0x07) << 18;
				$mState = 3;
				$mBytes = 4;
			}
			elseif (0xF8 == (0xFC & ($in)))
			{
				/* First octet of 5 octet sequence.
				 *
				 * This is illegal because the encoded codepoint must be either
				 * (a) not the shortest form or
				 * (b) outside the Unicode range of 0-0x10FFFF.
				 * Rather than trying to resynchronize, we will carry on until the end
				 * of the sequence and let the later error handling code catch it.
				 */
				$mUcs4 = ($in);
				$mUcs4 = ($mUcs4 & 0x03) << 24;
				$mState = 4;
				$mBytes = 5;
			}
			elseif (0xFC == (0xFE & ($in)))
			{
				// First octet of 6 octet sequence, see comments for 5 octet sequence.
				$mUcs4 = ($in);
				$mUcs4 = ($mUcs4 & 1) << 30;
				$mState = 5;
				$mBytes = 6;
			}
			else
			{
				/* Current octet is neither in the US-ASCII range nor a legal first
				 * octet of a multi-octet sequence.
				 */
				return false;
			}
		}
		else
		{
			// When mState is non-zero, we expect a continuation of the multi-octet
			// sequence
			if (0x80 == (0xC0 & ($in)))
			{
				// Legal continuation.
				$shift = ($mState - 1) * 6;
				$tmp = $in;
				$tmp = ($tmp & 0x0000003F) << $shift;
				$mUcs4 |= $tmp;

				if (0 == --$mState)
				{
					/* End of the multi-octet sequence. mUcs4 now contains the final
					 * Unicode codepoint to be output
					 *
					 * Check for illegal sequences and codepoints.
					 */

					// From Unicode 3.1, non-shortest form is illegal
					if (((2 == $mBytes) && ($mUcs4 < 0x0080)) ||
						((3 == $mBytes) && ($mUcs4 < 0x0800)) ||
						((4 == $mBytes) && ($mUcs4 < 0x10000)) ||
						(4 < $mBytes) ||
						// From Unicode 3.2, surrogate characters are illegal
						(($mUcs4 & 0xFFFFF800) == 0xD800) ||
						// Codepoints outside the Unicode range are illegal
						($mUcs4 > 0x10FFFF))
					{
						return false;
					}
					if (0xFEFF != $mUcs4)
					{
						// BOM is legal but we don't want to output it
						$out[] = $mUcs4;
					}
					//initialize UTF8 cache
					$mState = 0;
					$mUcs4  = 0;
					$mBytes = 1;
				}
			}
			else
			{
				/* ((0xC0 & (*in) != 0x80) && (mState != 0))
				 *
				 * Incomplete multi-octet sequence.
				 */
				return false;
			}
		}
	}
	return $out;
}

/**
 * Returns number of rows affected by last query
 *
 * @return int
 * @global CotDB $db
 */
function sed_sql_affectedrows($conn = null)
{
	global $db;

	return $db->affectedRows;
}

/**
 * Returns last error number
 *
 * @return int
 * @global CotDB $db
 */
function sed_sql_errno()
{
	global $db;

	$error_info = $db->errorInfo();
	return $error_info[1];
}

/**
 * Returns last SQL error message
 *
 * @return string
 * @global CotDB $db
 */
function sed_sql_error()
{
	global $db;

	$error_info = $db->errorInfo();
	return $error_info[2];
}

/**
 * Fetches result row as mixed numeric/associative array
 *
 * @param PDOStatement $res Query result
 * @return array
 */
function sed_sql_fetcharray($res)
{
	return $res->fetch();
}

/**
 * Returns result row as associative array
 *
 * @param PDOStatement $res Query result
 * @return array
 */
function sed_sql_fetchassoc($res)
{
	return $res->fetch(PDO::FETCH_ASSOC);
}

/**
 * Returns result row as numeric array
 *
 * @param PDOStatement $res Query result
 * @return array
 */
function sed_sql_fetchrow($res)
{
	return $res->fetch(PDO::FETCH_NUM);
}

/**
 * Returns number of records total for last query with SQL_CALC_FOUND_ROWS
 *
 * @param PDO $conn Custom connection
 * @return int
 * @global CotDB $db
 */
function sed_sql_foundrows($conn = NULL)
{
	global $db;
	return (int) $db->query('SELECT FOUND_ROWS()')->fetchColumn();
}

/**
 * Frees result resources
 *
 * @param PDOStatement $res Query result
 */
function sed_sql_freeresult($res)
{
	$res = null;
}

/**
 * Returns ID of last INSERT query
 *
 * @return int
 * @global CotDB $db
 */
function sed_sql_insertid()
{
	global $db;

	return $db->lastInsertId();
}

/**
 * Returns list of tables for a database. Use sed_sql_fetcharray() to get table names from result
 *
 * @param string $db_name Database name
 * @return PDOStatement
 * @global CotDB $db
 */
function sed_sql_listtables($db_name)
{
	global $db;

	return $db->query("SHOW TABLES");
}

/**
 * Returns number of rows in result set
 *
 * @param PDOStatement $res Query result
 * @return int
 */
function sed_sql_numrows($res)
{
	return $res->rowCount();
}

/**
 * Escapes potentially insecure sequences in string
 *
 * @param string $str
 * @return string
 * @global CotDB $db
 */
function sed_sql_prep($str)
{
	global $db;

	return preg_replace("#^'(.*)'\$#", '$1', $db->quote($str));
}

/**
 * Executes SQL query
 *
 * @global $sys
 * @global $cfg
 * @global $usr
 * @param string $query SQL query
 * @return PDOStatement
 * @global CotDB $db
 */
function sed_sql_query($query)
{
	global $db;

	return $db->query($query);
}

/**
 * Fetches a single cell from result
 *
 * @param PDOStatement $res Result set
 * @param int $row Row number
 * @param mixed $col Column name or index (null-based)
 * @return mixed
 */
function sed_sql_result($res, $row = 0, $col = 0)
{
	$r = $res->fetch(PDO::FETCH_BOTH, PDO::FETCH_ORI_ABS, $row);
	return $r[$col];
}

function sed_sql_rowcount($table)
{
	global $db;

	return (int) $db->query("SELECT COUNT(*) FROM $table")->fetchColumn();
}

function sed_sql_runscript($script)
{
	global $db;

	return $db->runScript($script);
}


function sed_sql_insert($table_name, $data, $prefix = '')
{
	global $db;

	$data_tmp = array();
	foreach ($data as $key => $val)
	{
		$data_tmp[$prefix.$key] = $val;
	}
	return $db->insert($table_name, $data_tmp);
}


function sed_sql_delete($table_name, $condition = '')
{
	global $db;

	return $db->delete($table_name, $condition);
}


function sed_sql_update($table_name, $condition, $data, $prefix = '', $update_null = false)
{
	global $db;

	$data_tmp = array();
	foreach ($data as $key => $val)
	{
		$data_tmp[$prefix.$key] = $val;
	}
	return $db->update($table_name, $data_tmp, $condition, array(), $update_null);
}

?>
