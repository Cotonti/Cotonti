<?php
/**
 * Main function library.
 *
 * @package Cotonti
 * @version 0.9.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD License
 */

defined('COT_CODE') or die('Wrong URL');

// System requirements check
if (!defined('COT_INSTALL'))
{
	(function_exists('version_compare') && version_compare(PHP_VERSION, '5.2.0', '>=')) or die('Cotonti system requirements: PHP 5.2 or above.'); // TODO: Need translate
	extension_loaded('mbstring') or die('Cotonti system requirements: mbstring PHP extension must be loaded.'); // TODO: Need translate
}

// Group constants
define('COT_GROUP_DEFAULT', 0);
define('COT_GROUP_GUESTS', 1);
define('COT_GROUP_INACTIVE', 2);
define('COT_GROUP_BANNED', 3);
define('COT_GROUP_MEMBERS', 4);
define('COT_GROUP_SUPERADMINS', 5);
define('COT_GROUP_MODERATORS', 6);

/* ======== Pre-sets ========= */

$out = array();
$plu = array();
$sys = array();
$usr = array();

$i = explode(' ', microtime());
$sys['starttime'] = $i[1] + $i[0];

$cfg['svnrevision'] = '$Rev$'; //DO NOT MODIFY this is set by SVN automatically
$cfg['version'] = '0.7.0';
$cfg['dbversion'] = '0.7.0';

// Set default file permissions if not present in config
if (!isset($cfg['file_perms']))
{
	$cfg['file_perms'] = 0664;
}
if (!isset($cfg['dir_perms']))
{
	$cfg['dir_perms'] = 0777;
}

/**
 * Array of custom cot_import() filter callbacks
 */
$cot_import_filters = array();

/**
 * Custom e-mail send callbacks
 */
$cot_mail_senders = array();

/**
 * Custom parser functions registry
 */
$cot_parsers = array();

/*
 * =========================== System Functions ===============================
*/

/**
 * Strips everything but alphanumeric, hyphens and underscores
 *
 * @param string $text Input
 * @return string
 */
function cot_alphaonly($text)
{
	return(preg_replace('/[^a-zA-Z0-9\-_]/', '', $text));
}

/**
 * Truncates a string
 *
 * @param string $res Source string
 * @param int $l Length
 * @return unknown
 */
function cot_cutstring($res, $l)
{
	global $cfg;
	if (mb_strlen($res)>$l)
	{
		$res = mb_substr($res, 0, ($l-3)).'...';
	}
	return $res;
}

/**
 * Returns a list of plugins registered for a hook
 *
 * @param string $hook Hook name
 * @param string $cond Permissions
 * @return array
 */
function cot_getextplugins($hook, $cond='R')
{
	global $cot_plugins, $cache, $cot_current_hook;

	$cot_current_hook = $hook;

	$extplugins = array();

	if (is_array($cot_plugins[$hook]))
	{
		foreach($cot_plugins[$hook] as $k)
		{
			if ($k['pl_module'])
			{
				$cat = $k['pl_code'];
				$opt = 'a';
			}
			else
			{
				$cat = 'plug';
				$opt = $k['pl_code'];
			}
			if (cot_auth($cat, $opt, $cond))
			{
				$extplugins[] = $k['pl_file'];
			}
		}
	}

	// Trigger cache handlers
	$cache && $cache->trigger($hook);

	return $extplugins;
}

/**
 * Imports data from the outer world
 *
 * @param string $name Variable name
 * @param string $source Source type: G (GET), P (POST), C (COOKIE) or D (variable filtering)
 * @param string $filter Filter type
 * @param int $maxlen Length limit
 * @param bool $dieonerror Die with fatal error on wrong input
 * @return mixed
 */
function cot_import($name, $source, $filter, $maxlen=0, $dieonerror=FALSE)
{
	global $cot_import_filters;

	switch($source)
	{
		case 'G':
			$v = (isset($_GET[$name])) ? $_GET[$name] : NULL;
			$log = TRUE;
			break;

		case 'P':
			$v = (isset($_POST[$name])) ? $_POST[$name] : NULL;
			$log = TRUE;
			if ($filter=='ARR')
			{
				return($v);
			}
			break;

		case 'R':
			$v = (isset($_REQUEST[$name])) ? $_REQUEST[$name] : NULL;
			$log = TRUE;
			break;

		case 'C':
			$v = (isset($_COOKIE[$name])) ? $_COOKIE[$name] : NULL;
			$log = TRUE;
			break;

		case 'D':
			$v = $name;
			$log = FALSE;
			break;

		default:
			cot_diefatal('Unknown source for a variable : <br />Name = '.$name.'<br />Source = '.$source.' ? (must be G, P, C or D)');
			break;
	}

	if (MQGPC && ($source=='G' || $source=='P' || $source=='C') )
	{
		$v = stripslashes($v);
	}

	if ($v=='' || $v == NULL)
	{
		return($v);
	}

	if ($maxlen>0)
	{
		$v = mb_substr($v, 0, $maxlen);
	}

	$pass = FALSE;
	$defret = NULL;
	$filter = ($filter=='STX') ? 'TXT' : $filter;

	// Custom filter support
	if (is_array($cot_import_filters[$filter]))
	{
		foreach ($cot_import_filters[$filter] as $func)
		{
			$v = $func($v, $name);
		}
		return $v;
	}

	switch($filter)
	{
		case 'INT':
			if (is_numeric($v) && floor($v)==$v)
			{
				$pass = TRUE;
				$v = (int) $v;
			}
			break;

		case 'NUM':
			if (is_numeric($v))
			{
				$pass = TRUE;
			}
			break;

		case 'TXT':
			$v = trim($v);
			if (mb_strpos($v, '<')===FALSE)
			{
				$pass = TRUE;
			}
			else
			{
				$defret = str_replace('<', '&lt;', $v);
			}
			break;

		case 'SLU':
			$v = trim($v);
			$f = preg_replace('/[^a-zA-Z0-9_=\/]/', '', $v);
			if ($v == $f)
			{
				$pass = TRUE;
			}
			else
			{
				$defret = '';
			}
			break;

		case 'ALP':
			$v = trim($v);
			$f = cot_alphaonly($v);
			if ($v == $f)
			{
				$pass = TRUE;
			}
			else
			{
				$defret = $f;
			}
			break;

		case 'PSW':
			$v = trim($v);
			$f = preg_replace('#[\'"&<>]#', '', $v);
			$f = mb_substr($f, 0 ,32);

			if ($v == $f)
			{
				$pass = TRUE;
			}
			else
			{
				$defret = $f;
			}
			break;

		case 'HTM':
			$v = trim($v);
			$pass = TRUE;
			break;

		case 'ARR':
			$pass = TRUE;
			break;

		case 'BOL':
			if ($v == '1' || $v == 'on')
			{
				$pass = TRUE;
				$v = '1';
			}
			elseif ($v=='0' || $v=='off')
			{
				$pass = TRUE;
				$v = '0';
			}
			else
			{
				$defret = '0';
			}
			break;

		case 'LVL':
			if (is_numeric($v) && $v >= 0 && $v <= 100 && floor($v)==$v)
			{
				$pass = TRUE;
			}
			else
			{
				$defret = NULL;
			}
			break;

		case 'NOC':
			$pass = TRUE;
			break;

		default:
			cot_diefatal('Unknown filter for a variable : <br />Var = '.$cv_v.'<br />Filter = &quot;'.$filter.'&quot; ?');
			break;
	}

	$v = preg_replace('/(&#\d+)(?![\d;])/', '$1;', $v);
	if ($pass)
	{
		return $v;
	}
	else
	{
		if ($log)
		{
			cot_log_import($source, $filter, $name, $v);
		}
		if ($dieonerror)
		{
			cot_diefatal('Wrong input.');
		}
		else
		{
			return $defret;
		}
	}
}

/**
 * Puts POST data into the cross-request buffer
 */
function cot_import_buffer_save()
{
	unset($_SESSION['cot_buffer']);
	$_SESSION['cot_buffer'] = $_POST;
}

/**
 * Attempts to fetch a buffered value for a variable previously imported
 * if the currently imported value is empty
 *
 * @param string $name Input name
 * @param mixed $value Currently imported value
 * @return mixed Input value or NULL if the variable is not in the buffer
 */
function cot_import_buffered($name, $value)
{
	if (empty($value))
	{
		if (isset($_SESSION['cot_buffer'][$name]))
		{
			return $_SESSION['cot_buffer'][$name];
		}
		else
		{
			return null;
		}
	}
	else
	{
		return $value;
	}
}

/**
 * Imports date stamp
 *
 * @param string $name Variable name preffix
 * @param bool $usertimezone Use user timezone
 * @param bool $returnarray Return Date Array
 * @param string $source Source type: P (POST), C (COOKIE) or D (variable filtering)
 * @return mixed
 */
function cot_import_date($name, $usertimezone = true, $returnarray = false, $source = 'P')
{
	global $L, $R, $usr;
	$name = preg_match('#^(\w+)\[(.*?)\]$#', $name, $mt) ? $mt[1] : $name;
	$date = cot_import($name, $source, 'ARR');

	$year = cot_import($date['year'], 'D', 'INT');
	$month = cot_import($date['month'], 'D', 'INT');
	$day = cot_import($date['day'], 'D', 'INT');
	$hour = cot_import($date['hour'], 'D', 'INT');
	$minute = cot_import($date['minute'], 'D', 'INT');

	if (((int)($month) > 0 && (int)($day) > 0 && (int)($year) > 0) || ((int)($day) > 0 && (int)($minute) > 0))
	{
		$result = cot_mktime($hour, $minute, 0, $month, $day, $year);
		$result = ($usertimezone) ? ($result - $usr['timezone'] * 3600) : $result;
	}
	else
	{
		$result = 0;
	}

	if($returnarray)
	{
		$result['stamp'] = $result;
		$result['year'] = $year;
		$result['month'] = $month;
		$result['day'] = $day;
		$result['hour'] = $hour;
		$result['minute'] = $minute;
	}

	return $result;
}

/**
 * Imports pagination indexes
 *
 * @param string $var_name URL parameter name, e.g. 'pg' or 'd'
 * @param int $max_items Max items per page
 * @return array Array containing 2 items: page number and database offset
 */
function cot_import_pagenav($var_name, $max_items)
{
	global $cfg;

	if ($cfg['easypagenav'])
	{
		$page = (int) cot_import($var_name, 'G', 'INT');
		if ($page <= 0)
		{
			$page = 1;
		}
		$offset = ($page - 1) * $max_items;
	}
	else
	{
		$offset = (int) cot_import($var_name, 'G', 'INT');
		if ($offset < 0)
		{
			$offset = 0;
		}
		$page = floor($offset / $max_items) + 1;
	}

	return array($page, $offset);
}

/*
 * =========================== Structure functions ===========================
 */

/**
 * Loads comlete category structure into array
 */
function cot_load_structure()
{
	global $db, $db_structure, $db_extra_fields, $cfg, $L, $cot_extrafields, $structure, $R;
	$structure = array();
	$sql = $db->query("SELECT * FROM $db_structure ORDER BY structure_path ASC");

	/* == Hook: Part 1 ==*/
	$extp = cot_getextplugins('structure');
	/* ================= */

	while ($row = $sql->fetch())
	{
		if (!empty($row['structure_icon']))
		{
			$row['structure_icon'] = cot_rc('img_structure_cat', array(
				'icon' => $row['structure_icon'],
				'title' => htmlspecialchars($row['structure_title']),
				'desc' => htmlspecialchars($row['structure_desc'])
			));
		}

		$path2 = mb_strrpos($row['structure_path'], '.');

		$row['structure_tpl'] = (empty($row['structure_tpl'])) ? $row['structure_code'] : $row['structure_tpl'];

		if ($path2 > 0)
		{
			$path1 = mb_substr($row['structure_path'], 0, ($path2));
			$path[$row['structure_path']] = $path[$path1].'.'.$row['structure_code'];
			$tpath[$row['structure_path']] = $tpath[$path1].' '.$cfg['separator'].' '.$row['structure_title'];
			$row['structure_tpl'] = ($row['structure_tpl'] == 'same_as_parent') ? $parent_tpl : $row['structure_tpl'];
		}
		else
		{
			$path[$row['structure_path']] = $row['structure_code'];
			$tpath[$row['structure_path']] = $row['structure_title'];
		}

		$parent_tpl = $row['structure_tpl'];

		$structure[$row['structure_area']][$row['structure_code']] = array(
			'path' => $path[$row['structure_path']],
			'tpath' => $tpath[$row['structure_path']],
			'rpath' => $row['structure_path'],
			'id' => $row['structure_id'],
			'tpl' => $row['structure_tpl'],
			'title' => $row['structure_title'],
			'desc' => $row['structure_desc'],
			'icon' => $row['structure_icon'],
			'locked' => $row['structure_locked']
		);

		if (is_array($cot_extrafields['structure']))
		{
			foreach ($cot_extrafields['structure'] as $row_c)
			{
				$structure[$row['structure_area']][$row_c['field_name']] = $row['structure_'.$row_c['field_name']];
			}
		}

		/* == Hook: Part 2 ==*/
		foreach ($extp as $pl)
		{
			include $pl;
		}
		/* ================= */
	}
}

/**
 * Gets an array of category children
 *
 * @param string $area Area code
 * @param string $cat Cat code
 * @param bool $allsublev All sublevels array
 * @param bool $firstcat Add main cat
 * @param bool $userrights Check userrights
 * @param bool $sqlprep use $db->prep function
 * @return array
 */
function cot_structure_children($area, $cat, $allsublev = true,  $firstcat = true, $userrights = true, $sqlprep = true)
{
	global $structure, $sys, $cfg, $db;

	$mtch = $structure[$area][$cat]['path'].'.';
	$mtchlen = mb_strlen($mtch);
	$mtchlvl = mb_substr_count($mtch,".");

	$catsub = array();
	if ($cat != '' && $firstcat && (($userrights && cot_auth($area, $cat, 'R') || !$userrights)))
	{
		$catsub[] = $cat;
	}

	foreach ($structure[$area] as $i => $x)
	{
		if (($cat == '' || mb_substr($x['path'], 0, $mtchlen) == $mtch) && (($userrights && cot_auth($area, $i, 'R') || !$userrights)))
		{
			$subcat = mb_substr($x['path'], $mtchlen + 1);
			if ($cat == '' || $allsublev || (!$allsublev && mb_substr_count($x['path'],".") == $mtchlvl))
			{
				$i = ($sqlprep) ? $db->prep($i) : $i;
				$catsub[] = $i;
			}
		}
	}
	return($catsub);
}

/**
 * Gets an array of category parents
 *
 * @param string $area Area code
 * @param string $cat Cat code
 * @param string $type Type 'full', 'first', 'last'
 * @return mixed
 */
function cot_structure_parents($area, $cat, $type = 'full')
{
	global $structure, $cfg;
	$pathcodes = explode('.', $structure[$area][$cat]['path']);

	if ($type == 'first')
	{
		reset($pathcodes);
		$pathcodes = current($pathcodes);
	}
	elseif ($type == 'last')
	{
		$pathcodes = end($pathcodes);
	}

	return $pathcodes;
}


/**
 * Renders category dropdown
 *
 * @param string $area Area code
 * @param string $check Seleced value
 * @param string $name Dropdown name
 * @param string $subcat Show only subcats of selected category
 * @param bool $hideprivate Hide private categories
 * @return string
 */
function cot_selectbox_structure($area, $check, $name, $subcat = '', $hideprivate = true)
{
	global $db, $db_structure, $usr, $L, $R, $structure;

	foreach ($structure[$area] as $i => $x)
	{
		$display = ($hideprivate) ? cot_auth($area, $i, 'W') : true;
		if ($display && !empty($subcat) && isset($structure[$area][$subcat]) && !(empty($check)))
		{
			$mtch = $structure[$area][$subcat]['path'].".";
			$mtchlen = mb_strlen($mtch);
			$display = (mb_substr($x['path'], 0, $mtchlen) == $mtch || $i == $check) ? true : false;
		}

		if (cot_auth($area, $i, 'R') && $i!='all' && $display)
		{
			$result_array[$i] = $x['tpath'];
		}
	}
	$result = cot_selectbox($check, $name, array_keys($result_array), array_values($result_array), false);

	return($result);
}

/**
 * Sends mail with standard PHP mail().
 * If cot_mail_custom() function exists, it will be called instead of the PHP
 * function. This way custom mail delivery methods, such as SMTP, are
 * supported.
 *
 * @global $cfg
 * @param string $fmail Recipient
 * @param string $subject Subject
 * @param string $body Message body
 * @param string $headers Message headers
 * @param string $additional_parameters Additional parameters passed to sendmail
 * @return bool
 */
function cot_mail($fmail, $subject, $body, $headers='', $additional_parameters = null)
{
	global $cfg, $cot_mail_senders;

	if (function_exists('cot_mail_custom'))
	{
		return cot_mail_custom($fmail, $subject, $body, $headers, $additional_parameters);
	}

	if (is_array($cot_mail_senders) && count($cot_mail_senders) > 0)
	{
		foreach ($cot_mail_senders as $func)
		{
			$ret &= $func($fmail, $subject, $body, $headers, $additional_parameters);
		}
		return $ret;
	}

	if (empty($fmail))
	{
		return false;
	}
	else
	{
		$sitemaintitle = mb_encode_mimeheader($cfg['maintitle'], 'UTF-8', 'B', "\n");

		$headers = (empty($headers)) ? "From: \"".$sitemaintitle."\" <".$cfg['adminemail'].">\n"."Reply-To: <".$cfg['adminemail'].">\n" : $headers;
		$headers .= "Message-ID: <".md5(uniqid(microtime()))."@".$_SERVER['SERVER_NAME'].">\n";

		$body .= "\n\n".$cfg['maintitle']." - ".$cfg['mainurl']."\n".$cfg['subtitle'];
		$headers .= "Content-Type: text/plain; charset=UTF-8\n";
		$headers .= "Content-Transfer-Encoding: 8bit\n";
		$subject = mb_encode_mimeheader($subject, 'UTF-8', 'B', "\n");
		if (ini_get('safe_mode'))
		{
			mail($fmail, $subject, $body, $headers);
		}
		else
		{
			mail($fmail, $subject, $body, $headers, $additional_parameters);
		}
		return true;
	}
}

/**
 * Updates online users table
 * @global array $cfg
 * @global array $sys
 * @global array $usr
 * @global array $out
 * @global string $db_online
 * @global Cache $cache
 * @global array $cot_usersonline
 * @global array $env
 */
function cot_online_update()
{
	global $db, $cfg, $sys, $usr, $out, $db_online, $cache, $cot_usersonline, $env, $Ls;
	if (!$cfg['disablewhosonline'])
	{
		if ($env['location'] != $sys['online_location']
			|| !empty($sys['sublocaction']) && $sys['sublocaction'] != $sys['online_subloc'])
		{
			if ($usr['id'] > 0)
			{
				if (empty($sys['online_location']))
				{
					$db->insert($db_online, array(
						'online_ip' => $usr['ip'], 
						'online_name' => $usr['name'],
						'online_lastseen' => (int)$sys['now'],
						'online_location' => $env['location'],
						'online_subloc' => $sys['sublocation'],
						'online_userid' => (int)$usr['id'],
						'online_shield' => 0,
						'online_hammer' => 0
						));
				}
				else
				{
					$db->update($db_online, array(
						'online_lastseen' => $sys['now'],
						'online_location' => $db->prep($env['location']), 
						'online_subloc' => $db->prep($sys['sublocation']), 
						'online_hammer' => (int)$sys['online_hammer']
						), "online_userid=".$usr['id']);
				}
			}
			else
			{
				if (empty($sys['online_location']))
				{
					$db->insert($db_online, array(
						'online_ip' => $usr['ip'], 
						'online_name' => 'v',
						'online_lastseen' => (int)$sys['now'],
						'online_location' => $env['location'],
						'online_subloc' => $sys['sublocation'],
						'online_userid' => -1,
						'online_shield' => 0,
						'online_hammer' => 0
						));
				}
				else
				{
					$db->update($db_online, array(
						'online_lastseen' => $sys['now'],
						'online_location' => $db->prep($env['location']), 
						'online_subloc' => $db->prep($sys['sublocation']), 
						'online_hammer' => (int)$sys['online_hammer']
						), "online_ip='".$usr['ip']."'");
				}
			}
		}
		if ($cache && $cache->mem && $cache->mem->exists('whosonline', 'system'))
		{
			$whosonline_data = $cache->mem->get('whosonline', 'system');
			$sys['whosonline_vis_count'] = $whosonline_data['vis_count'];
			$sys['whosonline_reg_count'] = $whosonline_data['reg_count'];
			$out['whosonline_reg_list'] = $whosonline_data['reg_list'];
			unset($whosonline_data);
		}
		else
		{
			$online_timedout = $sys['now'] - $cfg['timedout'];
			$db->delete($db_online, "online_lastseen < $online_timedout");
			$sys['whosonline_vis_count'] = $db->query("SELECT COUNT(*) FROM $db_online WHERE online_name='v'")->fetchColumn();
			$sql_o = $db->query("SELECT DISTINCT o.online_name, o.online_userid FROM $db_online o WHERE o.online_name != 'v' ORDER BY online_name ASC");
			$sys['whosonline_reg_count'] = $sql_o->rowCount();
			$ii_o = 0;
			while ($row_o = $sql_o->fetch())
			{
				$out['whosonline_reg_list'] .= ($ii_o > 0) ? ', ' : '';
				$out['whosonline_reg_list'] .= cot_build_user($row_o['online_userid'], htmlspecialchars($row_o['online_name']));
				$cot_usersonline[] = $row_o['online_userid'];
				$ii_o++;
			}
			$sql_o->closeCursor();
			unset($ii_o, $sql_o, $row_o);
			if ($cache && $cache->mem)
			{
				$whosonline_data = array(
					'vis_count' => $sys['whosonline_vis_count'],
					'reg_count' => $sys['whosonline_reg_count'],
					'reg_list' => $out['whosonline_reg_list']
				);
				$cache->mem->store('whosonline', $whosonline_data, 'system', 30);
			}
		}
		$sys['whosonline_all_count'] = $sys['whosonline_reg_count'] + $sys['whosonline_vis_count'];
		$out['whosonline'] = ($cfg['disablewhosonline']) ? '' : cot_declension($sys['whosonline_reg_count'], $Ls['Members']).', '.cot_declension($sys['whosonline_vis_count'], $Ls['Guests']);
	}
}

/**
 * Standard SED output filters, adds XSS protection to forms
 *
 * @param unknown_type $output
 * @return unknown
 */
function cot_outputfilters($output)
{
	global $cfg;

	/* === Hook === */
	foreach (cot_getextplugins('output') as $pl)
	{
		include $pl;
	}
	/* ==== */

	$output = preg_replace('#<form\s+[^>]*method=["\']?post["\']?[^>]*>#i', '$0' . cot_xp(), $output);

	return($output);
}

/**
 * Removes a directory recursively
 * @param string $dir Directory path
 * @return int Number of files and folders removed
 */
function cot_rmdir($dir)
{
	static $cnt = 0;
	$dp = opendir($dir);
	while ($f = readdir($dp))
	{
		$path = $dir . '/' . $f;
		if ($f != '.' && $f != '..' && is_dir($path))
		{
			cot_rmdir($path);
		}
		elseif ($f != '.' && $f != '..')
		{
			unlink($path);
			$cnt++;
		}
	}
	closedir($dp);
	rmdir($dir);
	$cnt++;
	return $cnt;
}

/**
 * Sends standard HTTP headers and disables browser cache
 *
 * @param string $content_type Content-Type value (without charset)
 * @param string $response_code HTTP response code, e.g. '404 Not Found'
 * @return bool
 */
function cot_sendheaders($content_type = 'text/html', $response_code = '200 OK')
{
	global $cfg;
	header('HTTP/1.1 ' . $response_code);
	header('Expires: Mon, Apr 01 1974 00:00:00 GMT');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: post-check=0,pre-check=0', FALSE);
	header('Content-Type: '.$content_type.'; charset=UTF-8');
	header('Cache-Control: no-store,no-cache,must-revalidate');
	header('Cache-Control: post-check=0,pre-check=0', FALSE);
	header('Pragma: no-cache');
	return TRUE;
}

/**
 * Set cookie with optional HttpOnly flag
 * @param string $name The name of the cookie
 * @param string $value The value of the cookie
 * @param int $expire The time the cookie expires in unixtime
 * @param string $path The path on the server in which the cookie will be available on.
 * @param string $domain The domain that the cookie is available.
 * @param bool $secure Indicates that the cookie should only be transmitted over a secure HTTPS connection. When set to TRUE, the cookie will only be set if a secure connection exists.
 * @param bool $httponly HttpOnly flag
 * @return bool
 */
function cot_setcookie($name, $value, $expire, $path, $domain, $secure = false, $httponly = false)
{
	if (mb_strpos($domain, '.') === FALSE)
	{
		// Some browsers don't support cookies for local domains
		$domain = '';
	}

	if ($domain != '')
	{
		// Make sure www. is stripped and leading dot is added for subdomain support on some browsers
		if (mb_strtolower(mb_substr($domain, 0, 4)) == 'www.')
		{
			$domain = mb_substr($domain, 4);
		}
		if ($domain[0] != '.')
		{
			$domain = '.'.$domain;
		}
	}

	return setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
}

/**
 * Performs actions required right before shutdown
 */
function cot_shutdown()
{
	global $cache, $cot_error;
	// Clear import buffer if everything's OK on POST
	if ($_SERVER['REQUEST_METHOD'] == 'POST' && !$cot_error)
	{
		unset($_SESSION['cot_buffer']);
	}
	while (ob_get_level() > 0)
	{
		ob_end_flush();
	}
	$cache = null; // Need to destroy before DB connection is lost
	$db = null;
}

/**
 * Generates a title string by replacing submasks with assigned values
 *
 * @param string $area Area maskname or actual mask
 * @param array $params An associative array of available parameters
 * @return string
 */
function cot_title($mask, $params = array())
{
	global $cfg;
	$res = (!empty($cfg[$mask])) ? $cfg[$mask] : $mask;
	is_array($params) ? $args = $params : mb_parse_str($params, $args);
	if (preg_match_all('#\{(.+?)\}#', $res, $matches, PREG_SET_ORDER))
	{
		foreach($matches as $m)
		{
			$var = $m[1];
			$res = str_replace($m[0], htmlspecialchars($args[$var], ENT_COMPAT, 'UTF-8', false), $res);
		}
	}
	return $res;
}

/**
 * Generates random string
 *
 * @param int $l Length
 * @return string
 */
function cot_unique($l=16)
{
	return(mb_substr(md5(mt_rand()), 0, $l));
}

/*
 * ================================= Authorization Subsystem ==================================
*/

/**
 * Returns specific access permissions
 *
 * @param string $area Cotonti area
 * @param string $option Option to access
 * @param string $mask Access mask
 * @return mixed
 */
function cot_auth($area, $option, $mask = 'RWA')
{
	global $sys, $usr;

	$mn['R'] = 1;
	$mn['W'] = 2;
	$mn['1'] = 4;
	$mn['2'] = 8;
	$mn['3'] = 16;
	$mn['4'] = 32;
	$mn['5'] = 64;
	$mn['A'] = 128;

	$masks = str_split($mask);
	$res = array();

	foreach ($masks as $k => $ml)
	{
		if (empty($mn[$ml]))
		{
			$sys['auth_log'][] = $area.'.'.$option.'.'.$ml.'=0';
			$res[] = FALSE;
		}
		elseif ($option == 'any')
		{
			$cnt = 0;

			if (is_array($usr['auth'][$area]))
			{
				foreach ($usr['auth'][$area] as $k => $g)
				{
					$cnt += (($g & $mn[$ml]) == $mn[$ml]);
				}
			}
			$cnt = ($cnt == 0 && $usr['auth']['admin']['a'] && $ml == 'A') ? 1 : $cnt;

			$sys['auth_log'][] = ($cnt > 0) ? $area.'.'.$option.'.'.$ml.'=1' : $area.'.'.$option.'.'.$ml.'=0';
			$res[] = ($cnt > 0) ? TRUE : FALSE;
		}
		else
		{
			$sys['auth_log'][] = (($usr['auth'][$area][$option] & $mn[$ml]) == $mn[$ml]) ? $area.'.'.$option.'.'.$ml.'=1' : $area.'.'.$option.'.'.$ml.'=0';
			$res[] = (($usr['auth'][$area][$option] & $mn[$ml]) == $mn[$ml]) ? TRUE : FALSE;
		}
	}
	return (count($res) == 1) ? $res[0] : $res;
}

/**
 * Builds Access Control List (ACL) for a specific user
 *
 * @param int $userid User ID
 * @param int $maingrp User main group
 * @return array
 */
function cot_auth_build($userid, $maingrp = 0)
{
	global $db, $db_auth, $db_groups_users;

	$groups = array();
	$authgrid = array();
	$tmpgrid = array();

	if ($userid == 0 || $maingrp == 0)
	{
		$groups[] = 1;
	}
	else
	{
		$groups[] = $maingrp;
		$sql = $db->query("SELECT gru_groupid FROM $db_groups_users WHERE gru_userid=$userid");

		while ($row = $sql->fetch())
		{
			$groups[] = $row['gru_groupid'];
		}
	}

	$sql_groups = implode(',', $groups);
	$sql = $db->query("SELECT auth_code, auth_option, auth_rights FROM $db_auth WHERE auth_groupid IN (".$sql_groups.") ORDER BY auth_code ASC, auth_option ASC");

	while ($row = $sql->fetch())
	{
		$authgrid[$row['auth_code']][$row['auth_option']] |= $row['auth_rights'];
	}

	return $authgrid;
}

/**
 * Block user if he is not allowed to access the page
 *
 * @param bool $allowed Authorization result
 * @return bool
 */
function cot_block($allowed)
{
	if (!$allowed)
	{
		global $sys, $env;
		$env['status'] = '403 Forbidden';
		cot_redirect(cot_url('message', 'msg=930&'.$sys['url_redirect'], '', true));
	}
	return FALSE;
}


/**
 * Block guests from viewing the page
 *
 * @return bool
 */
function cot_blockguests()
{
	global $env, $usr, $sys;

	if ($usr['id'] < 1)
	{
		$env['status'] = '403 Forbidden';
		cot_redirect(cot_url('message', "msg=930&".$sys['url_redirect'], '', true));
	}
	return FALSE;
}

/*
 * =========================== Output forming functions ===========================
 */

/**
 * Calculates age out of D.O.B.
 *
 * @param int $birth Date of birth as UNIX timestamp
 * @return int
 */
function cot_build_age($birth)
{
	global $sys;

	if ($birth==1)
	{
		return ('?');
	}

	$day1 = @date('d', $birth);
	$month1 = @date('m', $birth);
	$year1 = @date('Y', $birth);

	$day2 = @date('d', $sys['now_offset']);
	$month2 = @date('m', $sys['now_offset']);
	$year2 = @date('Y', $sys['now_offset']);

	$age = ($year2-$year1)-1;

	if ($month1<$month2 || ($month1==$month2 && $day1<=$day2))
	{
		$age++;
	}

	if($age < 0)
	{
		$age += 136;
	}

	return ($age);
}

/**
 * Builds category path
 *
 * @param string $area Area code
 * @param string $cat Category code
 * @param bool $nolast Last element as simple text
 * @return string
 */
function cot_structure_buildpath($area, $cat, $nolast = false)
{
	global $structure, $cfg;
	$mask = 'link_catpath';
	$mask = str_replace('%1$s', '{$url}', $mask);
	$mask = str_replace('%2$s', '{$title}', $mask);
	if ($cfg['homebreadcrumb'])
	{
		$tmp[] = cot_rc('link_catpath', array(
			'url' => $cfg['mainurl'],
			'title' => htmlspecialchars($cfg['maintitle'])
		));
	}
	$pathcodes = explode('.', $structure[$area][$cat]['path']);
	$last = count($pathcodes) - 1;
	foreach ($pathcodes as $k => $x)
	{
		if ($x != 'system')
		{
			$tmp[] = ($nolast && $k === $last) ? htmlspecialchars($structure[$area][$x]['title'])
				: cot_rc($mask, array(
				'url' => cot_url($area, 'c='.$x),
				'title' => htmlspecialchars($structure[$area][$x]['title'])
			));
		}
	}
	return is_array($tmp) ? implode(' '.$cfg['separator'].' ', $tmp) : '';
}

/**
 * Returns country text button
 *
 * @param string $flag Country code
 * @return string
 */
function cot_build_country($flag)
{
	global $cot_countries;
	if (!$cot_countries) include_once cot_langfile('countries', 'core');
	$flag = (empty($flag)) ? '00' : $flag;
	return cot_rc_link(cot_url('users', 'f=country_'.$flag), $cot_countries[$flag], array(
		'title' => $cot_countries[$flag]
	));
}

/**
 * Returns user email link
 *
 * @param string $email E-mail address
 * @param bool $hide Hide email option
 * @return string
 */
function cot_build_email($email, $hide = false)
{
	global $L;
	if ($hide)
	{
		return $L['Hidden'];
	}
	elseif (!empty($email) && preg_match('#^[\w\p{L}][\.\w\p{L}\-]+@[\w\p{L}\.\-]+\.[\w\p{L}]+$#u', $email))
	{
		$link = cot_rc('link_email', array('email' => $email));
		return function_exists('cot_obfuscate') ? cot_obfuscate($link) : $link;
	}
}

/**
 * Returns country flag button
 *
 * @param string $flag Country code
 * @return string
 */
function cot_build_flag($flag)
{
	global $cot_countries;
	if (!$cot_countries) include_once cot_langfile('countries', 'core');
	$flag = (empty($flag)) ? '00' : $flag;
	return cot_rc_link(cot_url('users', 'f=country_'.$flag),
		cot_rc('icon_flag', array('code' => $flag, 'alt' => $flag)),
		array('title' => $cot_countries[$flag])
	);
}

/**
 * Returns IP Search link
 *
 * @param string $ip IP mask
 * @return string
 */
function cot_build_ipsearch($ip)
{
	global $sys;
	if (!empty($ip))
	{
		return cot_rc_link(cot_url('admin', 'm=tools&p=ipsearch&a=search&id='.$ip.'&x='.$sys['xk']), $ip);
	}
	return '';
}

/**
 * Odd/even class choser for row
 *
 * @param int $number Row number
 * @return string
 */
function cot_build_oddeven($number)
{
	return ($number % 2 == 0 ) ? 'even' : 'odd';
}

/**
 * Returns stars image for user level
 *
 * @param int $level User level
 * @return unknown
 */
function cot_build_stars($level)
{
	global $theme, $R;

	if($level>0 and $level<100)
	{
		$stars = floor($level / 10) + 1;
		return cot_rc('icon_stars', array('val' => $stars));
	}
	else
	{
		return '';
	}
}

/**
 * Returns time gap between 2 dates
 *
 * @param int $t1 Stamp 1
 * @param int $t2 Stamp2
 * @return string
 */
function cot_build_timegap($t1,$t2)
{
	global $Ls;

	$gap = $t2 - $t1;

	if ($gap<=0 || !$t2 || $gap>94608000)
	{
		$result = '';
	}
	elseif ($gap<60)
	{
		$result = cot_declension($gap,$Ls['Seconds']);
	}
	elseif ($gap<3600)
	{
		$gap = floor($gap/60);
		$result = cot_declension($gap,$Ls['Minutes']);
	}
	elseif ($gap<86400)
	{
		$gap1 = floor($gap/3600);
		$gap2 = floor(($gap-$gap1*3600)/60);
		$result = cot_declension($gap1,$Ls['Hours']).' ';
		if ($gap2>0)
		{
			$result .= cot_declension($gap2,$Ls['Minutes']);
		}
	}
	else
	{
		$gap = floor($gap/86400);
		$result = cot_declension($gap,$Ls['Days']);
	}

	return $result;
}

/**
 * Returns user timezone offset
 *
 * @param int $tz Timezone
 * @return string
 */
function cot_build_timezone($tz)
{
	global $L;

	$result = 'GMT';

	$result .= cot_declension($tz,$Ls['Hours']);

	return $result;
}

/**
 * Returns link for URL
 *
 * @param string $text URL
 * @param int $maxlen Max. allowed length
 * @return unknown
 */
function cot_build_url($text, $maxlen=64)
{
	global $cfg;

	if (!empty($text))
	{
		if (mb_strpos($text, 'http://') !== 0)
		{
			$text='http://'. $text;
		}
		$text = htmlspecialchars($text);
		$text = cot_rc_link($text, cot_cutstring($text, $maxlen));
	}
	return $text;
}

/**
 * Returns link to user profile
 *
 * @param int $id User ID
 * @param string $user User name
 * @return string
 */
function cot_build_user($id, $user)
{
	global $cfg;

	if ($id == 0 && !empty($user))
	{
		return $user;
	}
	elseif ($id == 0)
	{
		return '';
	}
	else
	{
		return (!empty($user)) ? cot_rc_link(cot_url('users', 'm=details&id='.$id.'&u='.$user), $user) : '?';
	}
}

/**
 * Returns user group icon
 *
 * @param string $src Image file path
 * @return string
 */
function cot_build_groupicon($src)
{
	return ($src) ? cot_rc("icon_group", array('src' => $src)) : '';
}

/**
 * Renders user signature text
 *
 * @param string $text Signature text
 * @return string
 */
function cot_build_usertext($text)
{
	global $cfg;
	return cot_parse($text, $cfg['usertextimg']);
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
function cot_createthumb($img_big, $img_small, $small_x, $small_y, $keepratio, $extension, $filen, $fsize, $textcolor, $textsize, $bgcolor, $bordersize, $jpegquality, $dim_priority="Width")
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

/**
 * Resize an image
 *
 * @param string $source Original image path.
 * @param string $target Target path for saving, or 'return' to return the resized image data directly.
 * @param int $target_width Maximum width of resized image.
 * @param int $target_height Maximum height of resized image.
 * @param string $crop Crop the image to a certain ratio. Set to 'fit' to calculate ratio from target width and height.
 * @param string $fillcolor Color fill a transparent gif or png.
 * @param bool $sharpen Sharpen JPEG image after resize.
 * @return mixed Boolean or image resource, depending on $target
 */
function cot_imageresize($source, $target='return', $target_width=99999, $target_height=99999, $crop='', $fillcolor='', $quality=90, $sharpen=true)
{
	if (!file_exists($source)) return;
	$source_size = getimagesize($source);
	if(!$source_size) return;
	$mimetype = $source_size['mime'];
	if (substr($mimetype, 0, 6) != 'image/') return;

	$source_width = $source_size[0];
	$source_height = $source_size[1];
	if($target_width > $source_width) $target_width = $source_width; $noscaling_x = true;
	if($target_height > $source_height) $target_height = $source_height; $noscaling_y = true;

	$fillcolor = preg_replace('/[^0-9a-fA-F]/', '', (string)$fillcolor);
	if (!$fillcolor && $noscaling_x && $noscaling_y)
	{
		$data = file_get_contents($source);
		if($target == 'return') return $data;
	}

	$offsetX = 0;
	$offsetY = 0;

	if($crop)
	{
		$crop = ($crop == 'fit') ? array($target_width, $target_height) : explode(':', (string)$crop);
		if(count($crop) == 2)
		{
			$source_ratio = $source_width / $source_height;
			$target_ratio = (float)$crop[0] / (float)$crop[1];

			if ($source_ratio < $target_ratio)
			{
				$temp = $source_height;
				$source_height = $source_width / $target_ratio;
				$offsetY = ($temp - $source_height) / 2;
			}
			if ($source_ratio > $target_ratio)
			{
				$temp = $source_width;
				$source_width = $source_height * $target_ratio;
				$offsetX = ($temp - $source_width) / 2;
			}
		}
	}

	$width_ratio = $target_width / $source_width;
	$height_ratio = $target_height / $source_height;
	if ($width_ratio * $source_height < $target_height)
	{
		$target_height = ceil($width_ratio * $source_height);
	}
	else
	{
		$target_width = ceil($height_ratio * $source_width);
	}

	ini_set('memory_limit', '100M');
	$canvas = imagecreatetruecolor($target_width, $target_height);

	switch($mimetype)
	{
		case 'image/gif':
			$fn_create = 'imagecreatefromgif';
			$fn_output = 'imagegif';
			$mimetype = 'image/gif';
			//$quality = round(10 - ($quality / 10));
			$sharpen = false;
		break;

		case 'image/x-png':
		case 'image/png':
			$fn_create = 'imagecreatefrompng';
			$fn_output = 'imagepng';
			$quality = round(10 - ($quality / 10));
			$sharpen = false;
		break;

		default:
			$fn_create = 'imagecreatefromjpeg';
			$fn_output = 'imagejpeg';
		break;
	}
	$source_data = $fn_create($source);

	if (in_array($size['mime'], array('image/gif', 'image/png')))
	{
		if (!$fillcolor)
		{
			imagealphablending($canvas, false);
			imagesavealpha($canvas, true);
		}
		elseif(strlen($fillcolor) == 6 || strlen($fillcolor) == 3)
		{
			$background	= (strlen($fillcolor) == 6) ?
				imagecolorallocate($canvas, hexdec($fillcolor[0].$fillcolor[1]), hexdec($fillcolor[2].$fillcolor[3]), hexdec($fillcolor[4].$fillcolor[5])):
				imagecolorallocate($canvas, hexdec($fillcolor[0].$fillcolor[0]), hexdec($fillcolor[1].$fillcolor[1]), hexdec($fillcolor[2].$fillcolor[2]));
			imagefill($canvas, 0, 0, $background);
		}
	}
	imagecopyresampled($canvas, $source_data, 0, 0, $offsetX, $offsetY, $target_width, $target_height, $source_width, $source_height);
	imagedestroy($source_data);
	$canvas = ($sharpen) ? cot_imagesharpen($canvas, $source_width, $target_width) : $canvas;

	if($target == 'return')
	{
		ob_start();
		$fn_output($canvas, null, $quality);
		$data = ob_get_contents();
		ob_end_clean();
		imagedestroy($canvas);
		return $data;
	}
	else
	{
		$result = $fn_output($canvas, $target, $quality);
		imagedestroy($canvas);
		return $result;
	}
}

/**
 * Sharpen an image after resize
 *
 * @param image resource $imgdata Image resource from an image creation function
 * @param int $source_width Width of image before resize
 * @param int $target_width Width of image to sharpen (after resize)
 * @return image resource
 */
function cot_imagesharpen($imgdata, $source_width, $target_width)
{
	$s = $target_width * (750.0 / $source_width);
	$a = 52;
	$b = -0.27810650887573124;
	$c = .00047337278106508946;
	$sharpness = max(round($a+$b*$s+$c*$s*$s), 0);
	$sharpenmatrix = array(
		array(-1, -2, -1),
		array(-2, $sharpness + 12, -2),
		array(-1, -2, -1)
	);
	imageconvolution($imgdata, $sharpenmatrix, $sharpness, 0);
	return $imgdata;
}

/**
 * Returns Theme/Scheme selection dropdown
 *
 * @param string $selected_theme Seleced theme
 * @param string $selected_scheme Seleced color scheme
 * @param string $name Dropdown name
 * @return string
 */
function cot_selectbox_theme($selected_theme, $selected_scheme, $input_name)
{
	require_once cot_incfile('extensions');
	$handle = opendir('./themes/');
	while ($f = readdir($handle))
	{
		if (mb_strpos($f, '.') === FALSE && is_dir('./themes/'.$f))
		{
			$themelist[] = $f;
		}
	}
	closedir($handle);
	sort($themelist);

	$values = array();
	$titles = array();
	foreach ($themelist as $i => $x)
	{
		$themeinfo = "./themes/$x/$x.php";
		if (file_exists($themeinfo))
		{
			$info = cot_infoget($themeinfo, 'COT_THEME');
			if ($info)
			{
				if (empty($info['Schemes']))
				{
					$values[] = "$x:default";
					$titles[] = $info['Name'];
				}
				else
				{
					$schemes = explode(',', $info['Schemes']);
					sort($schemes);
					foreach ($schemes as $sc)
					{
						$sc = explode(':', $sc);
						$values[] = $x . ':' . $sc[0];
						$titles[] = count($schemes) > 1 ? $info['Name'] .  ' (' . $sc[1] . ')' : $info['Name'];
					}
				}
			}
			else
			{
				$values[] = "$x:default";
				$titles[] = $x;
			}
		}
		else
		{
			$values[] = "$x:default";
			$titles[] = $x;
		}
	}

	return cot_selectbox("$selected_theme:$selected_scheme", $input_name, $values, $titles, false);
}

/*
 * ======================== Error & Message + Logs API ========================
*/

/**
 * Checks if there are messages to display
 *
 * @param string $src If non-emtpy, check messages in this specific source only
 * @param string $class If non-empty, check messages of this specific class only
 * @return bool
 */
function cot_check_messages($src = '', $class = '')
{
	global $error_string;

	if (empty($src) && empty($class))
	{
		return (is_array($_SESSION['cot_messages']) && count($_SESSION['cot_messages']) > 0)
			|| !empty($error_string);
	}

	if (!is_array($_SESSION['cot_messages']))
	{
		return false;
	}

	if (empty($src))
	{
		foreach ($_SESSION['cot_messages'] as $src => $grp)
		{
			foreach ($grp as $msg)
			{
				if ($msg['class'] == $class)
				{
					return true;
				}
			}
		}
	}
	elseif (empty($class))
	{
		return count($_SESSION['cot_messages'][$src]) > 0;
	}
	else
	{
		foreach ($_SESSION['cot_messages'][$src] as $msg)
		{
			if ($msg['class'] == $class)
			{
				return true;
			}
		}
	}

	return false;
}

/**
 * Clears error and other messages after they have bin displayed
 * @param string $src If non-emtpy, clear messages in this specific source only
 * @param string $class If non-empty, clear messages of this specific class only
 * @see cot_error()
 * @see cot_message()
 */
function cot_clear_messages($src = '', $class = '')
{
	global $error_string;

	if (empty($src) && empty($class))
	{
		unset($_SESSION['cot_messages']);
		unset($error_string);
	}

	if (!is_array($_SESSION['cot_messages']))
	{
		return;
	}

	if (empty($src))
	{
		foreach ($_SESSION['cot_messages'] as $src => $grp)
		{
			$new_grp = array();
			foreach ($grp as $msg)
			{
				if ($msg['class'] != $class)
				{
					$new_grp[] = $msg;
				}
			}
			if (count($new_grp) > 0)
			{
				$_SESSION['cot_messages'][$src] = $new_grp;
			}
			else
			{
				unset($_SESSION['cot_messages'][$src]);
			}
		}
	}
	elseif (empty($class))
	{
		unset($_SESSION['cot_messages'][$src]);
	}
	else
	{
		$new_grp = array();
		foreach ($_SESSION['cot_messages'][$src] as $msg)
		{
			if ($msg['class'] != $class)
			{
				$new_grp[] = $msg;
			}
		}
		if (count($new_grp) > 0)
		{
			$_SESSION['cot_messages'][$src] = $new_grp;
		}
		else
		{
			unset($_SESSION['cot_messages'][$src]);
		}
	}
}

/**
 * Terminates script execution and performs redirect
 *
 * @param bool $cond Really die?
 * @return bool
 */
function cot_die($cond=TRUE)
{
	global $env;
	if ($cond)
	{
		$env['status'] = '403 Forbidden';
		cot_redirect(cot_url('message', 'msg=950', '', true));
	}
	return FALSE;
}

/**
 * Terminates script execution with fatal error
 *
 * @param string $text Reason
 * @param string $title Message title
 */
function cot_diefatal($text='Reason is unknown.', $title='Fatal error')
{
	global $cfg;

	if (defined('COT_DEBUG') && COT_DEBUG)
	{
		echo '<br /><pre>';
		debug_print_backtrace();
		echo '</pre>';
	}

	$disp = "<strong><a href=\"".$cfg['mainurl']."\">".$cfg['maintitle']."</a></strong><br />";
	$disp .= @date('Y-m-d H:i').'<br />'.$title.' : '.$text;
	die($disp);
}

/**
 * Renders different messages on page
 *
 * @param XTemplate $tpl Current template object reference
 */
function cot_display_messages($tpl)
{
	global $L;
	if (!cot_check_messages())
	{
		return;
	}
	$errors = cot_get_messages('', 'error');
	if (count($errors) > 0)
	{
		foreach ($errors as $msg)
		{
			$text = isset($L[$msg['text']]) ? $L[$msg['text']] : $msg['text'];
			$tpl->assign('ERROR_ROW_MSG', $text);
			$tpl->parse('MAIN.ERROR.ERROR_ROW');
		}
		$tpl->parse('MAIN.ERROR');
	}
	$warnings = cot_get_messages('', 'warning');
	if (count($warnings) > 0)
	{
		foreach ($warnings as $msg)
		{
			$text = isset($L[$msg['text']]) ? $L[$msg['text']] : $msg['text'];
			$tpl->assign('WARNING_ROW_MSG', $text);
			$tpl->parse('MAIN.WARNING.WARNING_ROW');
		}
		$tpl->parse('MAIN.WARNING');
	}
	$okays = cot_get_messages('', 'ok');
	if (count($okays) > 0)
	{
		foreach ($okays as $msg)
		{
			$text = isset($L[$msg['text']]) ? $L[$msg['text']] : $msg['text'];
			$tpl->assign('DONE_ROW_MSG', $text);
			$tpl->parse('MAIN.DONE.DONE_ROW');
		}
		$tpl->parse('MAIN.DONE');
	}
	cot_clear_messages();
}

/**
 * Records an error message to be displayed on results page
 * @param string $message Message lang string code or full text
 * @param string $src Error source identifier, such as field name for invalid input
 * @see cot_message()
 */
function cot_error($message, $src = 'default')
{
	global $cot_error;
	$cot_error ? $cot_error++ : $cot_error = 1;
	cot_message($message, 'error', $src);
}

/**
 * Returns an array of messages for a specific source and/or class
 *
 * @param string $src Message source identifier. Search in all sources if empty
 * @param string $class Message class. Search for all classes if empty
 * @return array Array of message strings
 */
function cot_get_messages($src = 'default', $class = '')
{
	$messages = array();
	if (empty($src) && empty($class))
	{
		return $_SESSION['cot_messages'];
	}

	if (!is_array($_SESSION['cot_messages']))
	{
		return $messages;
	}

	if (empty($src))
	{
		foreach ($_SESSION['cot_messages'] as $src => $grp)
		{
			foreach ($grp as $msg)
			{
				if (!empty($class) && $msg['class'] != $class)
				{
					continue;
				}
				$messages[] = $msg;
			}
		}
	}
	elseif (is_array($_SESSION['cot_messages'][$src]))
	{
		if (empty($class))
		{
			return $_SESSION['cot_messages'][$src];
		}
		else
		{
			foreach ($_SESSION['cot_messages'][$src] as $msg)
			{
				if ($msg['class'] != $class)
				{
					continue;
				}
				$messages[] = $msg;
			}
		}
	}
	return $messages;
}

/**
 * Collects all messages and implodes them into a single string
 * @param string $src Origin of the target messages
 * @param string $class Group messages of selected class only. Empty to group all
 * @return string Composite HTML string
 * @see cot_error()
 * @see cot_get_messages()
 * @see cot_message()
 */
function cot_implode_messages($src = 'default', $class = '')
{
	global $R, $L, $error_string;
	$res = '';

	if (!is_array($_SESSION['cot_messages']))
	{
		return;
	}

	$messages = cot_get_messages($src, $class);
	foreach ($messages as $msg)
	{
		$text = isset($L[$msg['text']]) ? $L[$msg['text']] : $msg['text'];
		$res .= cot_rc('code_msg_line', array('class' => $msg['class'], 'text' => $text));
	}

	if (!empty($error_string) && (empty($class) || $class == 'error'))
	{
		$res .= cot_rc('code_msg_line', array('class' => 'error', 'text' => $error_string));
	}
	return empty($res) ? '' : cot_rc('code_msg_begin', array('class' => empty($class) ? 'message' : $class))
		. $res . $R['code_msg_end'];
}

/**
 * Logs an event
 *
 * @param string $text Event description
 * @param string $group Event group
 */
function cot_log($text, $group='def')
{
	global $db, $db_logger, $sys, $usr, $_SERVER;

	$db->insert($db_logger, array(
		'log_date' => (int)$sys['now_offset'],
		'log_ip' => $usr['ip'],
		'log_name' => $usr['name'],
		'log_group' => $group,
		'log_text' => $text.' - '.$_SERVER['REQUEST_URI'] 
		));
}

/**
 * Logs wrong input
 *
 * @param string $s Source type
 * @param string $e Filter type
 * @param string $v Variable name
 * @param string $o Value
 */
function cot_log_import($s, $e, $v, $o)
{
	$text = "A variable type check failed, expecting ".$s."/".$e." for '".$v."' : ".$o;
	cot_log($text, 'sec');
}

/**
 * Records a generic message to be displayed on results page
 * @param string $text Message lang string code or full text
 * @param string $class Message class: 'status', 'error', 'ok', 'notice', etc.
 * @param string $src Message source identifier
 * @see cot_error()
 */
function cot_message($text, $class = 'ok', $src = 'default')
{
	global $cfg;
	if (!$cfg['msg_separate'])
	{
		// Force the src to default if all errors are displayed in the same place
		$src = 'default';
	}
	$_SESSION['cot_messages'][$src][] = array(
		'text' => $text,
		'class' => $class
	);
}

/*
 * =============================== File Path Functions ========================
*/

/**
 * Returns path to include file
 *
 * @param string $name Extension or API name
 * @param string $type Extension type: 'module', 'plug' or 'core' for core API
 * @param string $part Name of the extension part
 * @return string File path
 */
function cot_incfile($name, $type = 'core', $part = 'functions')
{
	global $cfg;
	if ($type == 'core')
	{
		return $cfg['system_dir'] . "/$name.php";
	}
	elseif ($type == 'plug')
	{
		return $cfg['plugins_dir']."/$name/inc/$name.$part.php";
	}
	elseif ($name == 'admin' || $name == 'users' || $name == 'message')
	{
		// Built-in extensions
		return $cfg['system_dir']."/$name/$name.$part.php";
	}
	else
	{
		return $cfg['modules_dir']."/$name/inc/$name.$part.php";
	}
}

/**
 * Returns a language file path for a plugin or FALSE on error.
 *
 * @param string $name Plugin name
 * @param bool $type Langfile type: 'plug', 'module' or 'core'
 * @param mixed $default Default (fallback) language code
 * @return bool
 */
function cot_langfile($name, $type = 'plug', $default = 'en')
{
	global $cfg, $lang;
	if ($type == 'module')
	{
		if (@file_exists($cfg['modules_dir']."/$name/lang/$name.$lang.lang.php"))
		{
			return $cfg['modules_dir']."/$name/lang/$name.$lang.lang.php";
		}
		else
		{
			return $cfg['modules_dir']."/$name/lang/$name.$default.lang.php";
		}
	}
	elseif ($type == 'core')
	{
		if (@file_exists($cfg['lang_dir']."/$lang/$name.$lang.lang.php"))
		{
			return $cfg['lang_dir']."/$lang/$name.$lang.lang.php";
		}
		else
		{
			return $cfg['lang_dir']."/$default/$name.$default.lang.php";
		}
	}
	else
	{
		if (@file_exists($cfg['plugins_dir']."/$name/lang/$name.$lang.lang.php"))
		{
			return $cfg['plugins_dir']."/$name/lang/$name.$lang.lang.php";
		}
		else
		{
			return $cfg['plugins_dir']."/$name/lang/$name.$default.lang.php";
		}
	}
}

/**
 * Auxilliary function that returns theme resources as an array
 * 
 * @return array Theme resource strings
 */
function cot_get_rc_theme()
{
	global $usr;
	$R = array();
	if (file_exists('./themes/'.$usr['theme'].'/'.$usr['theme'].'.php'))
	{
		include './themes/'.$usr['theme'].'/'.$usr['theme'].'.php';
	}
	return $R;
}

/**
 * Tries to detect and fetch a user scheme CSS file or returns FALSE on error.
 *
 * @global array $usr User object
 * @global array $cfg Configuration
 * @global array $out Output vars
 * @return mixed
 */
function cot_schemefile()
{
	global $usr, $cfg, $out;

	if (file_exists('./themes/'.$usr['theme'].'/'.$usr['scheme'].'.css'))
	{
		return './themes/'.$usr['theme'].'/'.$usr['scheme'].'.css';
	}
	elseif (file_exists('./themes/'.$usr['theme'].'/css/'))
	{
		if (file_exists('./themes/'.$usr['theme'].'/css/'.$usr['scheme'].'.css'))
		{
			return './themes/'.$usr['theme'].'/css/'.$usr['scheme'].'.css';
		}
		elseif (file_exists('./themes/'.$usr['theme'].'/css/'.$cfg['defaultscheme'].'.css'))
		{
			$out['notices'] .= $L['com_schemefail'];
			$usr['scheme'] = $cfg['defaultscheme'];
			return './themes/'.$usr['theme'].'/css/'.$cfg['defaultscheme'].'.css';
		}
	}
	elseif (file_exists('./themes/'.$usr['theme']))
	{
		if (file_exists('./themes/'.$usr['theme'].'/'.$cfg['defaultscheme'].'.css'))
		{
			$out['notices'] .= $L['com_schemefail'];
			$usr['scheme'] = $cfg['defaultscheme'];
			return './themes/'.$usr['theme'].'/'.$cfg['defaultscheme'].'.css';
		}
		elseif (file_exists('./themes/'.$usr['theme'].'/'.$usr['theme'].'.css'))
		{
			$out['notices'] .= $L['com_schemefail'];
			$usr['scheme'] = $usr['theme'];
			return './themes/'.$usr['theme'].'/'.$usr['theme'].'.css';
		}
		elseif (file_exists('./themes/'.$usr['theme'].'/style.css'))
		{
			$out['notices'] .= $L['com_schemefail'];
			$usr['scheme'] = 'style';
			return './themes/'.$usr['theme'].'/style.css';
		}
	}

	$out['notices'] .= $L['com_schemefail'];
	if (file_exists('./themes/'.$cfg['defaulttheme'].'/'.$cfg['defaultscheme'].'.css'))
	{
		$usr['theme'] = $cfg['defaulttheme'];
		$usr['scheme'] = $cfg['defaultscheme'];
		return './themes/'.$cfg['defaulttheme'].'/'.$cfg['defaultscheme'].'.css';
	}
	elseif (file_exists('./themes/'.$cfg['defaulttheme'].'/css/'.$cfg['defaultscheme'].'.css'))
	{
		$usr['theme'] = $cfg['defaulttheme'];
		$usr['scheme'] = $cfg['defaultscheme'];
		return './themes/'.$cfg['defaulttheme'].'/css/'.$cfg['defaultscheme'].'.css';
	}
	else
	{
		return false;
	}
}

/**
 * Returns path to a template file. The default search order is:
 * 1) Current theme folder (plugins/ subdir for plugins, admin/ subdir for admin)
 * 2) Default theme folder (if current is not default)
 * 3) tpl subdir in module/plugin folder (fallback template)
 *
 * @param mixed $base Item name (string), or base names (array)
 * @param string $type Extension type: 'plug', 'module' or 'core'
 * @return string
 */
function cot_tplfile($base, $type = 'module')
{
	global $usr, $cfg;

	// Get base name parts
	if (is_string($base) && mb_strpos($base, '.') !== false)
	{
		$base = explode('.', $base);
	}
	if (!is_array($base))
	{
		$base = array($base);
	}

	$basename = $base[0];
	$using_alternative_theme = ($usr['theme'] != $cfg['defaulttheme']);

	// Possible search directories depending on extension type
	if ($type == 'plug')
	{
		// Plugin template paths
		$scan_prefix[] = './themes/' . $usr['theme'] . '/plugins/';
		if ($using_alternative_theme)
		{
			$scan_prefix[] = './themes/' . $cfg['defaulttheme'] . '/plugins/';
		}
		$scan_prefix[] = $cfg['plugins_dir'] . '/' . $basename . '/tpl/';
	}
	elseif ($type == 'core')
	{
		// Built-in core modules
		if(in_array($basename, array('admin', 'header', 'footer')))
		{
			$basename = 'admin';
			$scan_prefix[] = './themes/' . $usr['theme'] . '/' . $basename . '/';
			if ($using_alternative_theme)
			{
				$scan_prefix[] = './themes/' . $cfg['defaulttheme'] . '/' . $basename . '/';
			}
			$scan_prefix[] = $cfg['system_dir'] . '/' . $basename . '/tpl/';
		}
		else
		{
			$scan_prefix[] = './themes/' . $usr['theme'] . '/';
			if ($using_alternative_theme)
			{
				$scan_prefix[] = './themes/' . $cfg['defaulttheme'] . '/';
			}
			$scan_prefix[] = $cfg['system_dir'] . '/' . $basename . '/tpl/';
		}
	}
	else
	{
		// Module template paths
		$scan_prefix[] = './themes/' . $usr['theme'] . '/';
		if ($using_alternative_theme)
		{
			$scan_prefix[] = './themes/' . $cfg['defaulttheme'] . '/';
		}
		$scan_prefix[] = $cfg['modules_dir'] . '/' . $basename . '/tpl/';
	}

	// Build template file name from base parts glued with dots
	$base_depth = count($base);
	for ($i = $base_depth; $i > 0; $i--)
	{
		$levels = array_slice($base, 0, $i);
		$themefile = implode('.', $levels) . '.tpl';
		// Search in all available directories
		foreach ($scan_prefix as $pfx)
		{
			if (file_exists($pfx . $themefile))
			{
				return $pfx . $themefile;
			}
		}
	}

	// throw new Exception('Template file '.implode('.', $base).'.tpl ('.$type.') was not found.');
	return false;
}

/*
 * ============================ Date and Time Functions =======================
*/

/**
 * Creates UNIX timestamp out of a date
 *
 * @param int $hour Hours
 * @param int $minute Minutes
 * @param int $second Seconds
 * @param int $month Month
 * @param int $date Day of the month
 * @param int $year Year
 * @return int
 */
function cot_mktime($hour = false, $minute = false, $second = false, $month = false, $date = false, $year = false)
{
	if ($hour === false)  $hour  = date ('G');
	if ($minute === false) $minute = date ('i');
	if ($second === false) $second = date ('s');
	if ($month === false)  $month  = date ('n');
	if ($date === false)  $date  = date ('j');
	if ($year === false)  $year  = date ('Y');

	return mktime ((int) $hour, (int) $minute, (int) $second, (int) $month, (int) $date, (int) $year);
}

/**
 * Converts MySQL date into UNIX timestamp
 *
 * @param string $date Date in MySQL format
 * @return int UNIX timestamp
 */
function cot_date2stamp($date)
{
	if ($date == '0000-00-00') return 0;
	preg_match('#(\d{4})-(\d{2})-(\d{2})#', $date, $m);
	return mktime(0, 0, 0, (int) $m[2], (int) $m[3], (int) $m[1]);
}

/**
 * Converts UNIX timestamp into MySQL date
 *
 * @param int $stamp UNIX timestamp
 * @return string MySQL date
 */
function cot_stamp2date($stamp)
{
	return date('Y-m-d', $stamp);
}

/*
 * ================================== Pagination ==============================
*/

/**
 * Page navigation (pagination) builder. Uses URL transformation and resource strings,
 * returns an associative array, containing:
 * ['prev'] - first and previous page buttons
 * ['main'] - buttons with page numbers, including current
 * ['next'] - next and last page buttons
 * ['last'] - last page with number
 *
 * @param string $module Site area or script name
 * @param mixed $params URL parameters as array or parameter string
 * @param int $current Current page number
 * @param int $entries Total rows
 * @param int $perpage Rows per page
 * @param string $characters It is symbol for parametre which transfer pagination
 * @param string $hash Hash part of the url (including #)
 * @param bool $ajax Add AJAX support
 * @param string $target_div Target div ID if $ajax is true
 * @param string $ajax_module Site area name for ajax if different from $module
 * @param string $ajax_params URL parameters for ajax if $ajax_module is not empty
 * @return array
 */
function cot_pagenav($module, $params, $current, $entries, $perpage, $characters = 'd', $hash = '',
	$ajax = false, $target_div = '', $ajax_module = '', $ajax_params = array())
{
	if (function_exists('cot_pagenav_custom'))
	{
		// For custom pagination functions in plugins
		return cot_pagenav_custom($module, $params, $current, $entries, $perpage, $characters, $hash,
			$ajax, $target_div, $ajax_module, $ajax_params);
	}

	$onpage = $entries - $current;
	if ($onpage > $perpage) $onpage = $perpage;

	if ($entries <= $perpage)
	{
		return array(
			'onpage' => $onpage,
			'entries' => $entries
		);
	}

	global $L, $R, $cfg;

	$each_side = 3; // Links each side

	is_array($params) ? $args = $params : parse_str($params, $args);
	if ($ajax)
	{
		$ajax_rel = !empty($ajax_module);
		$ajax_rel && is_string($ajax_params) ? parse_str($ajax_params, $ajax_args) : $ajax_args = $ajax_params;
		$event = ' class="ajax"';
		if (empty($target_div))
		{
			$base_rel = $ajax_rel ? ' rel="get;' : '';
		}
		else
		{
			$base_rel = $ajax_rel ? ' rel="get-'.$target_div.';' : ' rel="get-'.$target_div.'"';
		}
	}
	else
	{
		$ajax_rel = false;
		$event = '';
	}
	$rel = '';

	$totalpages = ceil($entries / $perpage);
//	if ($cfg['easypagenav'])
//	{
//		$currentpage = $current;
//		if ($currentpage <= 0)
//		{
//			$currentpage = 1;
//		}
//		$current = ($currentpage - 1) * $perpage;
//	}
//	else
//	{
		$currentpage = floor($current / $perpage) + 1;
//	}
	$cur_left = $currentpage - $each_side;
	if ($cur_left < 1) $cur_left = 1;
	$cur_right = $currentpage + $each_side;
	if ($cur_right > $totalpages) $cur_right = $totalpages;

	// Main block

	$before = '';
	$pages = '';
	$after = '';
	$i = 1;
	$n = 0;
	while ($i < $cur_left)
	{
		if ($cfg['easypagenav'])
		{
			$args[$characters] = $i == 1 ? null : $i;
		}
		else
		{
			$args[$characters] = ($i - 1) * $perpage;
		}
		if ($ajax_rel)
		{
			$ajax_args[$characters] = $args[$characters];
			$rel = $base_rel.str_replace('?', ';', cot_url($ajax_module, $ajax_args)).'"';
		}
		else
		{
			$rel = $base_rel;
		}
		$before .= cot_rc('link_pagenav_main', array(
			'url' => cot_url($module, $args, $hash),
			'event' => $event,
			'rel' => $rel,
			'num' => $i
		));
		if ($i < $cur_left - 2)
		{
			$before .= $R['link_pagenav_gap'];
		}
		elseif ($i == $cur_left - 2)
		{
			$args[$characters] = $i * $perpage;
			if ($ajax_rel)
			{
				$ajax_args[$characters] = $args[$characters];
				$rel = $base_rel.str_replace('?', ';', cot_url($ajax_module, $ajax_args)).'"';
			}
			else
			{
				$rel = $base_rel;
			}
			$before .= cot_rc('link_pagenav_main', array(
				'url' => cot_url($module, $args, $hash),
				'event' => $event,
				'rel' => $rel,
				'num' => $i + 1
			));
		}
		$i *= ($n % 2) ? 2 : 5;
		$n++;
	}
	for ($j = $cur_left; $j <= $cur_right; $j++)
	{
		if ($cfg['easypagenav'])
		{
			$args[$characters] = $j == 1 ? null : $j;
		}
		else
		{
			$args[$characters] = ($j - 1) * $perpage;
		}
		if ($ajax_rel)
		{
			$ajax_args[$characters] = $args[$characters];
			$rel = $base_rel.str_replace('?', ';', cot_url($ajax_module, $ajax_args)).'"';
		}
		else
		{
			$rel = $base_rel;
		}
		$rc = $j == $currentpage ? 'current' : 'main';
		$pages .= cot_rc('link_pagenav_'.$rc, array(
			'url' => cot_url($module, $args, $hash),
			'event' => $event,
			'rel' => $rel,
			'num' => $j
		));
	}
	while ($i <= $cur_right)
	{
		$i *= ($n % 2) ? 2 : 5;
		$n++;
	}
	while ($i < $totalpages)
	{
		if ($i > $cur_right + 2)
		{
			$after .= $R['link_pagenav_gap'];
		}
		elseif ($i == $cur_right + 2)
		{
			if ($cfg['easypagenav'])
			{
				$args[$characters] = $i == 2 ? null : $i - 1;
			}
			else
			{
				$args[$characters] = ($i - 2) * $perpage;
			}
			if ($ajax_rel)
			{
				$ajax_args[$characters] = $args[$characters];
				$rel = $base_rel.str_replace('?', ';', cot_url($ajax_module, $ajax_args)).'"';
			}
			else
			{
				$rel = $base_rel;
			}
			$after .= cot_rc('link_pagenav_main', array(
				'url' => cot_url($module, $args, $hash),
				'event' => $event,
				'rel' => $rel,
				'num' => $i - 1
			));
		}
		if ($cfg['easypagenav'])
		{
			$args[$characters] = $i == 1 ? null : $i;
		}
		else
		{
			$args[$characters] = ($i - 1) * $perpage;
		}
		if ($ajax_rel)
		{
			$ajax_args[$characters] = $args[$characters];
			$rel = $base_rel.str_replace('?', ';', cot_url($ajax_module, $ajax_args)).'"';
		}
		else
		{
			$rel = $base_rel;
		}
		$after .= cot_rc('link_pagenav_main', array(
			'url' => cot_url($module, $args, $hash),
			'event' => $event,
			'rel' => $rel,
			'num' => $i
		));
		$i *= ($n % 2) ? 2 : 5;
		$n++;
	}
	$pages = $before.$pages.$after;

	// Previous/next

	if ($current > 0)
	{
		$prev_n = $current - $perpage;
		if ($prev_n < 0)
		{
			$prev_n = 0;
		}
		if ($cfg['easypagenav'])
		{
			$num = floor($prev_n / $perpage) + 1;
			$args[$characters] = $num == 1 ? null : $num;
		}
		else
		{
			$args[$characters] = $prev_n;
		}
		if ($ajax_rel)
		{
			$ajax_args[$characters] = $args[$characters];
			$rel = $base_rel.str_replace('?', ';', cot_url($ajax_module, $ajax_args)).'"';
		}
		else
		{
			$rel = $base_rel;
		}
		$prev = cot_rc('link_pagenav_prev', array(
			'url' => cot_url($module, $args, $hash),
			'event' => $event,
			'rel' => $rel,
			'num' => $prev_n + 1
		));
		$args[$characters] = 0;
		if ($ajax_rel)
		{
			$ajax_args[$characters] = $args[$characters];
			$rel = $base_rel.str_replace('?', ';', cot_url($ajax_module, $ajax_args)).'"';
		}
		else
		{
			$rel = $base_rel;
		}
		$first = cot_rc('link_pagenav_first', array(
			'url' => cot_url($module, $args, $hash),
			'event' => $event,
			'rel' => $rel,
			'num' => 1
		));
	}

	if (($current + $perpage) < $entries)
	{
		$next_n = $current + $perpage;
		if ($cfg['easypagenav'])
		{
			$num = floor($next_n / $perpage) + 1;
			$args[$characters] = $num == 1 ? null : $num;
		}
		else
		{
			$args[$characters] = $next_n;
		}
		if ($ajax_rel)
		{
			$ajax_args[$characters] = $args[$characters];
			$rel = $base_rel.str_replace('?', ';', cot_url($ajax_module, $ajax_args)).'"';
		}
		else
		{
			$rel = $base_rel;
		}
		$next = cot_rc('link_pagenav_next', array(
			'url' => cot_url($module, $args, $hash),
			'event' => $event,
			'rel' => $rel,
			'num' => $next_n + 1
		));
		$last_n = ($totalpages - 1) * $perpage;
		if ($cfg['easypagenav'])
		{
			$num = floor($last_n / $perpage) + 1;
			$args[$characters] = $num == 1 ? null : $num;
		}
		else
		{
			$args[$characters] = $last_n;
		}
		if ($ajax_rel)
		{
			$ajax_args[$characters] = $args[$characters];
			$rel = $base_rel.str_replace('?', ';', cot_url($ajax_module, $ajax_args)).'"';
		}
		else
		{
			$rel = $base_rel;
		}
		$last = cot_rc('link_pagenav_last', array(
			'url' => cot_url($module, $args, $hash),
			'event' => $event,
			'rel' => $rel,
			'num' => $last_n + 1
		));
		$lastn  = (($last +  $perpage)<$totalpages) ?
			cot_rc('link_pagenav_main', array(
			'url' => cot_url($module, $args, $hash),
			'event' => $event,
			'rel' => $rel,
			'num' => floor($last_n / $perpage) + 1
			)): FALSE;
	}

	return array(
		'prev' => $first.$prev,
		'main' => $pages,
		'next' => $next.$last,
		'last' => $lastn,
		'current' => $currentpage,
		'total' => $totalpages,
		'onpage' => $onpage,
		'entries' => $entries
	);
}

/*
 * ============================== Text parsing API ============================
 */

/**
 * Parses text body
 *
 * @param string $text Source text
 * @return string
 */
function cot_parse($text, $enable_markup = true)
{
	global $cfg, $cot_parsers;

	if ($enable_markup)
	{
		foreach ($cot_parsers as $func)
		{
			$text = $func($text);
		}
	}
	else
	{
		$text = htmlspecialchars($text);
	}

	return $text;
}

/**
 * Automatically detect and parse URLs in text into HTML
 *
 * @param string $text Text body
 * @return string
 */
function cot_parse_autourls($text)
{
	$text = preg_replace('`(^|\s)(http|https|ftp)://([^\s"\'\[]+)`', '$1<a href="$2://$3">$2://$3</a>', $text);
	return $text;
}

/**
 * Truncates text.
 *
 * Cuts a string to the length of $length
 *
 * @param string  $text String to truncate.
 * @param integer $length Length of returned string, including ellipsis.
 * @param boolean $considerhtml If true, HTML tags would be handled correctly *
 * @param boolean $exact If false, $text will not be cut mid-word
 * @return string trimmed string.
 */
function cot_string_truncate($text, $length = 100, $considerhtml = true, $exact = false)
{
	if ($considerhtml)
	{
		// if the plain text is shorter than the maximum length, return the whole text
		if (mb_strlen(preg_replace('/<.*?>/', '', $text)) <= $length)
		{
			return $text;
		}
		// splits all html-tags to scanable lines
		preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);

		$total_length = 0;
		$open_tags = array();
		$truncate = '';

		foreach ($lines as $line_matchings)
		{
			// if there is any html-tag in this line, handle it and add it (uncounted) to the output
			if (!empty($line_matchings[1]))
			{
				// if it's an "empty element" with or without xhtml-conform closing slash (f.e. <br/>)
				if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1]))
				{
					// do nothing
					// if tag is a closing tag (f.e. </b>)
				}
				elseif (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings))
				{
					// delete tag from $open_tags list
					$pos = array_search($tag_matchings[1], $open_tags);
					if ($pos !== false)
					{
						unset($open_tags[$pos]);
					}
					// if tag is an opening tag (f.e. <b>)
				}
				elseif (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings))
				{
					// add tag to the beginning of $open_tags list
					array_unshift($open_tags, mb_strtolower($tag_matchings[1]));
				}
				// add html-tag to $truncate'd text
				$truncate .= $line_matchings[1];
			}

			// calculate the length of the plain text part of the line; handle entities as one character
			$content_length = mb_strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
			if ($total_length+$content_length> $length)
			{
				// the number of characters which are left
				$left = $length - $total_length;
				$entities_length = 0;
				// search for html entities
				if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE))
				{
					// calculate the real length of all entities in the legal range
					foreach ($entities[0] as $entity)
					{
						if ($entity[1]+1-$entities_length <= $left)
						{
							$left--;
							$entities_length += mb_strlen($entity[0]);
						}
						else
						{
							// no more characters left
							break;
						}
					}
				}
				$truncate .= mb_substr($line_matchings[2], 0, $left+$entities_length);
				// maximum lenght is reached, so get off the loop
				break;
			}
			else
			{
				$truncate .= $line_matchings[2];
				$total_length += $content_length;
			}

			// if the maximum length is reached, get off the loop
			if ($total_length >= $length)
			{
				break;
			}
		}
	}
	else
	{
		if (mb_strlen($text) <= $length)
		{
			return $text;
		}
		else
		{
			$truncate = mb_substr($text, 0, $length);
		}
	}

	if (!$exact)
	{
		// ...search the last occurance of a space...
		if (mb_strrpos($truncate, ' ') > 0)
		{
			$pos1 = mb_strrpos($truncate, ' ');
			$pos2 = mb_strrpos($truncate, '>');
			$spos = ($pos2 < $pos1) ? $pos1 : ($pos2+1);
			if (isset($spos))
			{
				// ...and cut the text in this position
				$truncate = mb_substr($truncate, 0, $spos);
			}
		}
	}
	if ($considerhtml)
	{
		// close all unclosed html-tags
		foreach ($open_tags as $tag)
		{
			$truncate .= '</'.$tag.'>';
		}
	}
	return $truncate;
}

/**
 * Wraps text
 *
 * @param string $str Source text
 * @param int $wrap Wrapping boundary
 * @return string
 */
function cot_wraptext($str, $wrap = 80)
{
	if (!empty($str))
	{
		$str = preg_replace('/([^\n\r ?&\.\/<>\"\\-]{'.$wrap.'})/', " \\1\n", $str);
	}
	return $str;
}

/*
 * ============================== Resource Strings ============================
 */

/**
 * Resource string formatter function. Takes a string with predefined variable substitution, e.g.
 * 'My {$pet} likes {$food}. And {$pet} is hungry!' and an assotiative array of substitution values, e.g.
 * array('pet' => 'rabbit', 'food' => 'carrots') and assembles a formatted result. If {$var} cannot be found
 * in $args, it will be taken from global scope. You can also use parameter strings instead of arrays, e.g.
 * 'pet=rabbit&food=carrots'. Or omit the second parameter in case all substitutions are globals.
 *
 * @global array $R Resource strings
 * @global array $L Language strings, support resource sequences too
 * @param string $name Name of the $R item or a resource string itself
 * @param array $params Associative array of arguments or a parameter string
 * @return string Assembled resource string
 */
function cot_rc($name, $params = array())
{
	global $R, $L;
	$res = isset($R[$name]) ? $R[$name]
		: (isset($L[$name]) ? $L[$name] : $name);
	is_array($params) ? $args = $params : mb_parse_str($params, $args);
	if (preg_match_all('#\{\$(.+?)\}#', $res, $matches, PREG_SET_ORDER))
	{
		foreach($matches as $m)
		{
			$var = $m[1];
			$res = str_replace($m[0], (isset($args[$var]) ? $args[$var] : $GLOBALS[$var]), $res);
		}
	}
	return $res;
}

/**
 * Converts custom attributes to a string if necessary
 *
 * @param mixed $attrs A string or associative array
 * @return string
 */
function cot_rc_attr_string($attrs)
{
	$attr_str = '';
	if (is_array($attrs))
	{
		foreach ($attrs as $key => $val)
		{
			$attr_str .= ' ' . $key . '="' . htmlspecialchars($val) . '"';
		}
	}
	else
	{
		$attr_str = $attrs;
	}
	return $attr_str;
}

/**
 * Consolidates local JS and CSS resources used during script execution,
 * prepares cache images and links to them
 *
 * @global array $cot_rc_html Blocks of HTML tags to be included in the header and footer of the document
 * @global array $cot_rc_reg Header/Footer resource registry
 * @global array $cfg Configuration
 * @global array $env Environment strings
 * @global array $usr User object
 */
function cot_rc_consolidate()
{
	global $cache, $cfg, $cot_rc_html, $cot_rc_reg, $env, $L, $R, $sys, $usr;

	// Load standard resources
	cot_rc_add_standard();

	// Invoke rc handlers
	foreach (cot_getextplugins('rc') as $pl)
	{
		include $pl;
	}
	if (file_exists('./themes/'.$usr['theme'].'/'.$usr['theme'].'.rc.php'))
	{
		include './themes/'.$usr['theme'].'/'.$usr['theme'].'.rc.php';
	}

	if (!is_array($cot_rc_reg))
	{
		return false;
	}

	clearstatcache();

	// Build the header CSS outputs
	$cot_rc_html = array();

	// Consolidate resources
	foreach ($cot_rc_reg as $type => $scope_data)
	{
		if ($type == 'css')
		{
			$separator = "\n";
		}
		elseif ($type == 'js')
		{
			$separator = "\n;";
		}
		if ($cache && $cfg['headrc_consolidate'])
		{
			// Consolidation
			foreach ($scope_data as $scope => $files)
			{
				$target_path = $cfg['cache_dir'] . '/static/' . $scope . '.' . $type;

				$code = '';
				$modified = false;

				if (!file_exists($target_path))
				{
					// Just compile a new cache file
					$file_list = $files;
					$modified = true;
				}
				else
				{
					// Load the list of files already cached
					$file_list = unserialize(file_get_contents("$target_path.idx"));
					$code = file_get_contents($target_path);

					// Check presense or modification time for each file
					foreach ($files as $path)
					{
						if (!in_array($path, $file_list) || filemtime($path) >= filemtime($target_path))
						{
							$modified = true;
							break;
						}
					}
				}

				if ($modified)
				{
					// Reconsolidate cache
					$current_path = realpath('.');
					foreach ($files as $path)
					{
						// Get file contents and remove BOM
						$file_code = str_replace(pack('CCC', 0xef, 0xbb, 0xbf), '', file_get_contents($path));
						$file_path = dirname(realpath($path));
						$relative_path = str_replace($current_path, '', $file_path);
						if ($relative_path[0] === '/')
						{
							$relative_path = mb_substr($relative_path, 1);
						}

						if ($type == 'css')
						{
							// Apply CSS imports
							if (preg_match_all('#@import\s+url\((\'|")?(.+?\.css)\1?\);#i', $file_code, $mt, PREG_SET_ORDER))
							{
								foreach ($mt as $m)
								{
									$filename = empty($relative_path) ? $m[2] : $relative_path . '/' . $m[2];
									$file_code = str_replace($m[0], file_get_contents($filename), $file_code);
								}
							}
							// Fix URLs
							if (preg_match_all('#\burl\((\'|")?(.+?)\1?\)#i', $file_code, $mt, PREG_SET_ORDER))
							{
								foreach ($mt as $m)
								{
									$filename = empty($relative_path) ? $m[2] : $relative_path . '/' . $m[2];
									$filename = str_replace($current_path, '', realpath($filename));
									if (!$filename)
									{
										continue;
									}
									if ($filename[0] === '/')
									{
										$filename = mb_substr($filename, 1);
									}
									$file_code = str_replace($m[0], 'url("' . $filename . '")', $file_code);
								}
							}
						}
						$code .= $file_code . $separator;
					}

					if ($cfg['headrc_minify'])
					{
						if ($type == 'js')
						{
							require_once './lib/jsmin.php';
							$code = JSMin::minify($code);
						}
						elseif ($type == 'css')
						{
							require_once './lib/cssmin.php';
							$code = CssMin::minify($code);
						}
					}
					file_put_contents($target_path, $code);
					if ($cfg['gzip'])
					{
						file_put_contents("$target_path.gz", gzencode($code));
					}
					file_put_contents("$target_path.idx", serialize($files));
				}

				$rc_url = "rc.php?rc=$scope.$type";
				$cot_rc_html[$scope] .= cot_rc("code_rc_{$type}_file", array('url' => $rc_url));
			}
		}
		else
		{
			if (is_array($cot_rc_reg[$type]['files']))
			{
				foreach ($cot_rc_reg[$type]['files'] as $scope => $scope_data)
				{
					foreach ($scope_data as $file)
					{
						$cot_rc_html[$scope] .= cot_rc("code_rc_{$type}_file", array('url' => $file));
					}
				}
			}
			if (is_array($cot_rc_reg[$type]['embed']))
			{
				foreach ($cot_rc_reg[$type]['embed'] as $scope => $code)
				{
					$cot_rc_html[$scope] .= cot_rc("code_rc_{$type}_embed", array('code' => $code));
				}
			}
		}
	}

	// Save the output
	$cache && $cache->db->store('cot_rc_html', $cot_rc_html);
}

/**
 * Puts a portion of embedded code into the header/footer CSS/JS resource registry.
 *
 * It is strongly recommended to use files for CSS/JS whenever possible
 * and call cot_rc_add_file() function for them instead of embedding code
 * into the page and using this function. This function should be used for
 * dynamically generated code, which cannot be stored in static files.
 *
 * @global array $cot_rc_reg_css Header CSS resource registry
 * @param string $identifier Alphanumeric identifier for the piece, used to control updates, etc.
 * @param string $code Embedded stylesheet or script code
 * @param string $scope Resource scope. See description of this parameter in cot_rc_add_file() docs.
 * @param string $type Resource type: 'js' or 'css'
 * @return bool This function always returns TRUE
 * @see cot_rc_add_file()
 */
function cot_rc_add_embed($identifier, $code, $scope = 'global', $type = 'js')
{
	global $cfg, $cot_rc_reg;

	if ($cfg['headrc_consolidate'])
	{
		// Save as file
		$path = $cfg['cache_dir'] . '/static/' . $identifier . '.' . $type;
		if (!file_exists($path) || md5($code) != md5_file($path))
		{
			file_put_contents($path, $code);
		}
		$cot_rc_reg[$type][$scope][] = $path;
	}
	else
	{
		$separator = $type == 'js' ? "\n;" : "\n";
		$cot_rc_reg[$type]['embed'][$scope] .= $code . $separator;
	}
	return true;
}

/**
 * Puts a JS/CSS file into the footer resource registry to be consolidated with other
 * such resources and stored in cache.
 *
 * It is recommened to use files instead of embedded code and use this function
 * instead of cot_rc_add_js_embed(). Use this way for any sort of static JavaScript or
 * CSS linking.
 *
 * Do not put any private data in any of resource files - it is not secure. If you really need it,
 * then use direct output instead.
 *
 * @global array $cot_rc_reg JavaScript/CSS footer/header resource registry
 * @param string $path Path to a *.js script or *.css stylesheet
 * @param string $scope Resource scope. Scope is a selector of domain where resource is used. Valid scopes are:
 *	'global' - global for entire site, will be included everywhere, this is the most static and persistent scope;
 *	'guest' - for unregistered visitors only;
 *	'user' - for registered members only;
 *	'group_123' - for members of a specific group (maingrp), in this example of group with id=123.
 * You can combine ext scope with other scopes, e.g. 'user&ext=forums' means "for registered users in forums".
 * It is recommended to use 'global' scope whenever possible because it delivers best caching opportunities.
 * @return bool Returns TRUE normally, FALSE is file was not found
 */
function cot_rc_add_file($path, $scope = 'global')
{
	global $cfg, $cot_rc_reg;
	if (!file_exists($path))
	{
		return false;
	}

	$type = preg_match('#\.(js|css)$#i', $path, $m) ? strtolower($m[1]) : 'js';

	if ($cfg['headrc_consolidate'])
	{
		$cot_rc_reg[$type][$scope][] = $path;
	}
	else
	{
		$cot_rc_reg[$type]['files'][$scope][] = $path;
	}
	return true;
}

/**
 * Registers standard resources
 */
function cot_rc_add_standard()
{
	global $cfg;

	if ($cfg['jquery'] && !$cfg['jquery_cdn'])
	{
		cot_rc_add_file('js/jquery.js');
	}

	if ($cfg['jquery'] && $cfg['turnajax'])
	{
		cot_rc_add_file('js/jquery.history.js');
	}

	cot_rc_add_file('js/base.js');

	if ($cfg['jquery'] && $cfg['turnajax'])
	{
		cot_rc_add_file('js/ajax_on.js');
	}
}

/**
 * Sends registered header resources to head output
 *
 * @global array $out Output snippets
 * @global array $cot_rc_html Header HTML
 */
function cot_rc_output()
{
	global $cot_rc_html,  $out, $usr;
	if (is_array($cot_rc_html))
	{
		foreach ($cot_rc_html as $scope => $html)
		{
			switch ($scope)
			{
				case 'global':
					$pass = true;
					break;
				case 'guest':
					$pass = $usr['id'] == 0;
					break;
				case 'user':
					$pass = $usr['id'] > 0;
					break;
				default:
					$parts = explode('_', $scope);
					$pass = count($parts) == 2 && $parts[0] == 'group' && $parts[1] == $usr['maingrp'];
			}
			if ($pass)
			{
				$out['head_head'] .= $html;
			}
		}
	}
}

/**
 * A shortcut for plain output of an embedded stylesheet/javascript in the header of the page
 *
 * @global array $out Output snippets
 * @param string $code Stylesheet or javascript code
 * @param bool $prepend Prepend this file before other head outputs
 * @param string $type Resource type: 'js' or 'css'
 */
function cot_rc_embed($code, $prepend = false, $type = 'js')
{
	global $out;
	$embed = cot_rc("code_rc_{$type}_embed", array('code' => $code));
	$prepend ? $out['head_head'] = $embed . $out['head_head'] : $out['head_head'] .= $embed;
}

/**
 * Quick link resource pattern
 *
 * @param string $url Link href
 * @param string $text Tag contents
 * @param mixed $attrs Additional attributes as a string or an associative array
 * @return string HTML link
 */
function cot_rc_link($url, $text, $attrs = '')
{
	$link_attrs = cot_rc_attr_string($attrs);
	return '<a href="'.$url.'"'.$link_attrs.'>'.$text.'</a>';
}

/**
 * A shortcut for plain output of a link to a CSS/JS file in the header of the page
 *
 * @global array $out Output snippets
 * @param string $path Stylesheet *.css or script *.js path/url
 * @param bool $prepend Prepend this file before other header outputs
 */
function cot_rc_link_file($path, $prepend = false)
{
	global $out;
	$type = preg_match('#\.(js|css)$#i', $path, $m) ? strtolower($m[1]) : 'js';
	$embed = cot_rc("code_rc_{$type}_file", array('url' => $path));
	$prepend ? $out['head_head'] = $embed . $out['head_head'] : $out['head_head'] .= $embed;
}

/*
 * ========================== Security Shield =================================
*/

/**
 * Checks GET anti-XSS parameter
 *
 * @return bool
 */
function cot_check_xg()
{
	global $env, $sys;
	$x = cot_import('x', 'G', 'ALP');
	if ($x != $sys['xk'] && (empty($sys['xk_prev']) || $x != $sys['xk_prev']))
	{
		$env['status'] = '403 Forbidden';
		cot_redirect(cot_url('message', 'msg=950', '', true));
		return false;
	}
	return true;
}

/**
 * Checks POST anti-XSS parameter
 *
 * @return bool
 */
function cot_check_xp()
{
	return (defined('COT_NO_ANTIXSS') || defined('COT_AUTH')) ?
		($_SERVER['REQUEST_METHOD'] == 'POST') : isset($_POST['x']);
}

/**
 * Clears current user action in Who's online.
 *
 */
function cot_shield_clearaction()
{
	global $db, $db_online, $usr;
	$db->update($db_online, array('online_action' => ''), 'online_ip='.$usr['ip']);
}

/**
 * Anti-hammer protection
 *
 * @param int $hammer Hammer rate
 * @param string $action Action type
 * @param int $lastseen User last seen timestamp
 * @return int
 */
function cot_shield_hammer($hammer,$action, $lastseen)
{
	global $cfg, $sys, $usr;

	if ($action=='Hammering')
	{
		cot_shield_protect();
		cot_shield_clearaction();
		cot_stat_inc('totalantihammer');
	}

	if (($sys['now']-$lastseen)<4)
	{
		$hammer++;
		if ($hammer>$cfg['shieldzhammer'])
		{
			cot_shield_update(180, 'Hammering');
			cot_log('IP banned 3 mins, was hammering', 'sec');
			$hammer = 0;
		}
	}
	else
	{
		if ($hammer>0)
		{
			$hammer--;
		}
	}
	return($hammer);
}

/**
 * Warn user of shield protection
 *
 */
function cot_shield_protect()
{
	global $cfg, $sys, $online_count, $shield_limit, $shield_action;

	if ($cfg['shieldenabled'] && $online_count>0 && $shield_limit>$sys['now'])
	{
		cot_diefatal('Shield protection activated, please retry in '.($shield_limit-$sys['now']).' seconds...<br />After this duration, you can refresh the current page to continue.<br />Last action was : '.$shield_action);
	}
}

/**
 * Updates shield state
 *
 * @param int $shield_add Hammer
 * @param string $shield_newaction New action type
 */
function cot_shield_update($shield_add, $shield_newaction)
{
	global $db, $cfg, $usr, $sys, $db_online;
	if ($cfg['shieldenabled'])
	{
		$shield_newlimit = $sys['now'] + floor($shield_add * $cfg['shieldtadjust'] /100);
		$db->update($db_online, array('online_shield' => $shield_newlimit, 'online_action' => $shield_newaction), 'online_ip='.$usr['ip']);
	}
}

/**
 * Returns XSS protection variable for GET URLs
 *
 * @return unknown
 */
function cot_xg()
{
	global $sys;
	return ('x='.$sys['xk']);
}

/**
 * Returns XSS protection field for POST forms
 *
 * @return string
 */
function cot_xp()
{
	global $sys;
	return '<div style="display:inline;margin:0;padding:0"><input type="hidden" name="x" value="'.$sys['xk'].'" /></div>';
}

/*
 * ============================ URL and URI ===================================
*/

/**
 * Displays redirect page
 *
 * @param string $url Target URI
 */
function cot_redirect($url)
{
	global $cfg, $cot_error, $env;

	if ($cot_error && $_SERVER['REQUEST_METHOD'] == 'POST')
	{
		// Save the POST data
		cot_import_buffer_save();
	}

	if (!cot_url_check($url))
	{
		$url = COT_ABSOLUTE_URL . $url;
	}

	if (isset($env['status']))
	{
		header('HTTP/1.1' . $env['status']);
	}

	if ($cfg['redirmode'])
	{
		$output = $cfg['doctype'].<<<HTM
		<html>
		<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		<meta http-equiv="refresh" content="0; url=$url" />
		<title>Redirecting...</title></head>
		<body>Redirecting to <a href="$url">$url</a>
		</body>
		</html>
HTM;
		header('Refresh: 0; URL='.$url);
		echo $output;
		exit;
	}
	else
	{
		header('Location: '.$url);
		exit;
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
 */
function cot_url($name, $params = '', $tail = '', $htmlspecialchars_bypass = false)
{
	if (function_exists('cot_url_custom'))
	{
		return cot_url_custom($name, $params, $tail, $htmlspecialchars_bypass);
	}

	global $cfg;
	// Preprocess arguments
	is_array($params) ? $args = $params : mb_parse_str($params, $args);
	$url = $name . '.php';
	// Append query string if needed
	if (count($args) > 0)
	{
		$sep = $htmlspecialchars_bypass ? '&' : '&amp;';
		$sep_len = mb_strlen($sep);
		$qs = '?';
		$i = 0;
		foreach ($args as $key => $val)
		{
			if ($val != '')
			{
				if ($i > 0)
				{
					$qs .= $sep;
				}
				$qs .= $key . '=' . urlencode($val);
				$i++;
			}
		}
		$url .= $qs;
	}
	$url .= $tail;
	//$url = str_replace('&amp;amp;', '&amp;', $url);
	return $url;
}

/**
 * Checks if an absolute URL belongs to current site or its subdomains
 *
 * @param string $url Absolute URL
 * @return bool
 */
function cot_url_check($url)
{
	global $sys;
	return preg_match('`^'.preg_quote($sys['scheme'].'://').'([^/]+\.)?'.preg_quote($sys['domain']).'`i', $url);
}

/**
 * Encodes a string for use in URLs
 *
 * @param string $str Source string
 * @param bool $translit Transliterate non-English characters
 * @return string
 */

function cot_urlencode($str, $translit = false)
{
	global $lang, $cot_translit;
	if ($translit && $lang != 'en' && is_array($cot_translit))
	{
		// Apply transliteration
		$str = strtr($str, $cot_translit);
	}
	return urlencode($str);
}

/**
 * Decodes a string that has been previously encoded with cot_urlencode()
 *
 * @param string $str Encoded string
 * @param bool $translit Transliteration of non-English characters was used
 * @return string
 */
function cot_urldecode($str, $translit = false)
{
	global $lang, $cot_translitb;
	if ($translit && $lang != 'en' && is_array($cot_translitb))
	{
		// Apply transliteration
		$str = strtr($str, $cot_translitb);
	}
	return urldecode($str);
}

/**
 * Store URI-redir to session
 *
 * @global $sys
 */
function cot_uriredir_store()
{
	global $sys;

	$script = basename($_SERVER['SCRIPT_NAME']);

	if ($_SERVER['REQUEST_METHOD'] != 'POST' // not form action/POST
		&& empty($_GET['x']) // not xg, hence not form action/GET and not command from GET
		&& !empty($script)
		&& $script != 'message.php' // not message location
		&& ($script != 'users.php' // not login/logout location
			|| empty($_GET['m'])
			|| !in_array($_GET['m'], array('auth', 'logout', 'register'))
	)
	)
	{
		$_SESSION['s_uri_redir'] = $sys['uri_redir'];
	}
}

/**
 * Apply URI-redir that stored in session
 *
 * @param bool $cfg_redir Configuration of redirect back
 * @global $redirect
 */
function cot_uriredir_apply($cfg_redir = true)
{
	global $redirect;

	if ($cfg_redir && empty($redirect) && !empty($_SESSION['s_uri_redir']))
	{
		$redirect = $_SESSION['s_uri_redir'];
	}
}

/**
 * Checks URI-redir for xg before redirect
 *
 * @param string $uri Target URI
 */
function cot_uriredir_redirect($uri)
{
	if (mb_strpos($uri, '&x=') !== false || mb_strpos($uri, '?x=') !== false)
	{
		$uri = cot_url('index'); // xg, not redirect to form action/GET or to command from GET
	}
	cot_redirect($uri);
}

/*
 * ========================= Internationalization (i18n) ======================
*/

$cot_languages['cn']= '中文';
$cot_languages['de']= 'Deutsch';
$cot_languages['dk']= 'Dansk';
$cot_languages['en']= 'English';
$cot_languages['es']= 'Español';
$cot_languages['fi']= 'Suomi';
$cot_languages['fr']= 'Français';
$cot_languages['gr']= 'Greek';
$cot_languages['hu']= 'Hungarian';
$cot_languages['it']= 'Italiano';
$cot_languages['jp']= '日本語';
$cot_languages['kr']= '한국어';
$cot_languages['nl']= 'Dutch';
$cot_languages['pl']= 'Polski';
$cot_languages['pt']= 'Portugese';
$cot_languages['ru']= 'Русский';
$cot_languages['se']= 'Svenska';
$cot_languages['uk'] = 'Українська';

/**
 * Makes correct plural forms of words
 *
 * @global string $lang Current language
 * @param int $digit Numeric value
 * @param string $expr Word or expression
 * @param bool $onlyword Return only words, without numbers
 * @param bool $canfrac - Numeric value can be Decimal Fraction
 * @return string
 */
function cot_declension($digit, $expr, $onlyword = false, $canfrac = false)
{
	global $lang;

	if (!is_array($expr))
	{
		return trim(($onlyword ? '' : "$digit ").$expr);
	}

	if ($canfrac)
	{
		$is_frac = floor($digit) != $digit;
		$i = $digit;
	}
	else
	{
		$is_frac = false;
		$i = preg_replace('#\D+#', '', $digit);
	}

	$plural = cot_get_plural($i, $lang, $is_frac);
	$cnt = count($expr);
	return trim(($onlyword ? '' : "$digit ").(($cnt > 0 && $plural < $cnt) ? $expr[$plural] : ''));
}

/**
 * Used in cot_declension to get rules for concrete languages
 *
 * @param int $plural Numeric value
 * @param string $lang Target language code
 * @param bool $is_frac true if numeric value is fraction, otherwise false
 * @return int
 */
function cot_get_plural($plural, $lang, $is_frac = false)
{
	switch ($lang)
	{
		case 'en':
		case 'de':
		case 'nl':
		case 'se':
		case 'us':
			return ($plural == 1) ? 1 : 0;

		case 'fr':
			return ($plural > 1) ? 0 : 1;

		case 'ru':
		case 'ua':
			if ($is_frac)
			{
				return 1;
			}
			$plural %= 100;
			return (5 <= $plural && $plural <= 20) ? 2 : ((1 == ($plural %= 10)) ? 0 : ((2 <= $plural && $plural <= 4) ? 1 : 2));

		default:
			return 0;
	}
}

/*
 * ============================================================================
*/

if (isset($cfg['customfuncs']) && $cfg['customfuncs'])
{
	require_once $cfg['system_dir'] . '/functions.custom.php';
}

?>
