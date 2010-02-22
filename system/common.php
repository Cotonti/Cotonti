<?php
/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
==================== */

/**
 * @package Cotonti
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

/* ======== First... ======== */

if (version_compare(PHP_VERSION, '6.0.0', '<='))
{
	if (get_magic_quotes_gpc())
	{
		function sed_disable_mqgpc(&$value, $key)
		{
			$value = stripslashes($value);
		}
		$gpc = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
		array_walk_recursive($gpc, 'sed_disable_mqgpc');
	}
}
define('MQGPC', FALSE);
error_reporting(E_ALL ^ E_NOTICE);
if (SED_DEBUG) require_once $cfg['system_dir'].'/debug.php';

register_shutdown_function('sed_shutdown');

$sys['day'] = @date('Y-m-d');
$sys['now'] = time();
$sys['now_offset'] = $sys['now'] - $cfg['servertimezone']*3600;
/* ======== Connect to the SQL DB======== */

require_once $cfg['system_dir'].'/database.'.$cfg['sqldb'].'.php';
$sed_dbc = sed_sql_connect($cfg['mysqlhost'], $cfg['mysqluser'], $cfg['mysqlpassword'], $cfg['mysqldb']);
unset($cfg['mysqlhost'], $cfg['mysqluser'], $cfg['mysqlpassword']);

/* ======== Cache Subsystem ======== */

if ($cfg['cache'])
{
	require_once $cfg['system_dir'].'/cache.php';
	$cot_cache = new Cache();
}
else
{
	$cot_cache = false;
}

/* ======== Configuration settings ======== */

if ($cfg['cache'] && $cot_cfg)
{
	$cfg = array_merge($cot_cfg, $cfg);
	unset($cot_cfg);
}
else
{
	$sql_config = sed_sql_query("SELECT config_owner, config_cat, config_name, config_value FROM $db_config");

	while ($row = sed_sql_fetcharray($sql_config))
	{
		if ($row['config_owner'] == 'core')
		{
			$cfg[$row['config_name']] = $row['config_value'];
		}
		else
		{
			$cfg['plugin'][$row['config_cat']][$row['config_name']] = $row['config_value'];
		}
	}
	$cfg['doctype'] = sed_setdoctype($cfg['doctypeid']);
	$cfg['css'] = $cfg['defaultskin'];

	$cfg['cache'] && $cot_cache->db_set('cot_cfg', $cfg, 'system');
}
// Mbstring options
mb_internal_encoding($cfg['charset']);

/* ======== Extra settings (the other presets are in functions.php) ======== */

$online_timedout = $sys['now'] - $cfg['timedout'];
if ($cfg['clustermode'])
{
	if (isset($_SERVER['HTTP_X_CLUSTER_CLIENT_IP'])) $usr['ip'] = $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
	elseif (isset($_SERVER['HTTP_X_REAL_IP'])) $usr['ip'] = $_SERVER['HTTP_X_REAL_IP'];
	elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) $usr['ip'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
	else $usr['ip'] = $_SERVER['REMOTE_ADDR'];
}
else
{
	$usr['ip'] = $_SERVER['REMOTE_ADDR'];
}
$sys['unique'] = sed_unique(16);

// Getting the server-relative path
$url = parse_url($cfg['mainurl']);
$sys['secure'] = $url['scheme'] == 'https' ? true : false;
$sys['site_uri'] = $url['path'];
$sys['domain'] = preg_replace('#^www\.#', '', $url['host']);
if (empty($cfg['cookiedomain'])) $cfg['cookiedomain'] = $sys['domain'];
if ($sys['site_uri'][mb_strlen($sys['site_uri']) - 1] != '/') $sys['site_uri'] .= '/';
define('SED_SITE_URI', $sys['site_uri']);
if (empty($cfg['cookiepath'])) $cfg['cookiepath'] = $sys['site_uri'];
// Absolute site url
$sys['host'] = (mb_stripos($_SERVER['HTTP_HOST'], $sys['domain']) !== false) ? $_SERVER['HTTP_HOST'] : $sys['domain'];
$sys['abs_url'] = $url['scheme'].'://'.$sys['host']. $sys['site_uri'];
define('SED_ABSOLUTE_URL', $sys['abs_url']);

$sys['uri_curr'] = (mb_stripos($_SERVER['REQUEST_URI'], $sys['site_uri']) === 0) ? mb_substr($_SERVER['REQUEST_URI'], mb_strlen($sys['site_uri'])) : ltrim($_SERVER['REQUEST_URI'], '/');
$sys['uri_redir'] = base64_encode($sys['uri_curr']);
$sys['url_redirect'] = 'redirect='.$sys['uri_redir'];
$redirect = sed_import('redirect','G','SLU');
$out['uri'] = str_replace('&', '&amp;', $sys['uri_curr']);

define('SED_AJAX', !empty($_SERVER['HTTP_X_REQUESTED_WITH']) || !empty($_SERVER['X-Requested-With']));

/* ======== Plugins ======== */

if (!$sed_plugins)
{
	$sql = sed_sql_query("SELECT pl_code, pl_file, pl_hook FROM $db_plugins
		WHERE pl_active = 1 ORDER BY pl_hook ASC, pl_order ASC");
	if (sed_sql_numrows($sql) > 0)
	{
		while ($row = sed_sql_fetcharray($sql))
		{
			$sed_plugins[$row['pl_hook']][] = $row;
		}
	}
	$cfg['cache'] && $cot_cache->db_set('sed_plugins', $sed_plugins, 'system');
}

if (!is_array($sed_urltrans))
{
	sed_load_urltrans();
	$cfg['cache'] && $cot_cache->db_set('sed_urltrans', $sed_urltrans, 'system', 1200);
}

/* ======== Gzip and output filtering ======== */

if ($cfg['gzip'])
{
	@ob_start('ob_gzhandler');
}
else
{
	ob_start();
}

ob_start('sed_outputfilters');

/* ======== Check the banlist ======== */

$userip = explode('.', $usr['ip']);
$ipmasks = "('".$userip[0].'.'.$userip[1].'.'.$userip[2].'.'.$userip[3]."','".$userip[0].'.'.$userip[1].'.'.$userip[2].".*','".$userip[0].'.'.$userip[1].".*.*','".$userip[0].".*.*.*')";

$sql = sed_sql_query("SELECT banlist_id, banlist_ip, banlist_reason, banlist_expire FROM $db_banlist WHERE banlist_ip IN ".$ipmasks);

if (sed_sql_numrows($sql) > 0)
{
	$row = sed_sql_fetcharray($sql);
	if ($sys['now'] > $row['banlist_expire'] && $row['banlist_expire'] > 0)
	{
		$sql = sed_sql_query("DELETE FROM $db_banlist WHERE banlist_id='".$row['banlist_id']."' LIMIT 1");
	}
	else
	{
		// TODO internationalize this
		$disp = 'Your IP is banned.<br />Reason: '.$row['banlist_reason'].'<br />Until: ';
		$disp .= ($row['banlist_expire'] > 0) ? @date($cfg['dateformat'], $row['banlist_expire']).' GMT' : 'Never expire.';
		sed_diefatal($disp);
	}
}

/* ======== Groups ======== */

if (!$sed_groups )
{
	$sql = sed_sql_query("SELECT * FROM $db_groups WHERE grp_disabled=0 ORDER BY grp_level DESC");

	if (sed_sql_numrows($sql) > 0)
	{
		while ($row = sed_sql_fetcharray($sql))
		{
			$sed_groups[$row['grp_id']] = array(
				'id' => $row['grp_id'],
				'alias' => $row['grp_alias'],
				'level' => $row['grp_level'],
   				'disabled' => $row['grp_disabled'],
   				'hidden' => $row['grp_hidden'],
				'state' => $row['grp_state'],
				'title' => htmlspecialchars($row['grp_title']),
				'desc' => htmlspecialchars($row['grp_desc']),
				'icon' => $row['grp_icon'],
				'pfs_maxfile' => $row['grp_pfs_maxfile'],
				'pfs_maxtotal' => $row['grp_pfs_maxtotal'],
				'ownerid' => $row['grp_ownerid']
			);
		}
	}
	else
	{
		sed_diefatal('No groups found.'); // TODO: Need translate
	}

	$cfg['cache'] && $cot_cache->db_set('sed_groups', $sed_groups, 'system');
}

/* ======== User/Guest ======== */

$usr['id'] = 0;
$usr['sessionid'] = '';
$usr['name'] = '';
$usr['level'] = 0;
$usr['lastvisit'] = 30000000000;
$usr['lastlog'] = 0;
$usr['timezone'] = $cfg['defaulttimezone'];
$usr['newpm'] = 0;
$usr['messages'] = 0;

$site_id = 'ct'.substr(md5($cfg['mainurl']), 0, 10);
$sys['site_id'] = $site_id;

session_start();

if (!defined('SED_MESSAGE'))
{
	$_SESSION['s_run_admin'] = defined('SED_ADMIN');
}

if (!empty($_COOKIE[$site_id]) || !empty($_SESSION[$site_id]))
{
	$u = empty($_SESSION[$site_id]) ? base64_decode($_COOKIE[$site_id]) : base64_decode($_SESSION[$site_id]);
	$u = explode(':_:', $u);
	$u_id = (int) sed_import($u[0], 'D', 'INT');
	$u_passhash = sed_import($u[1], 'D', 'ALP');
	if ($u_id > 0)
	{
		$sql = sed_sql_query("SELECT * FROM $db_users WHERE user_id = $u_id");

		if ($row = sed_sql_fetcharray($sql))
		{
			if (($u_passhash == md5($row['user_password'].$row['user_hashsalt'])
					|| ($sys['now_offset'] - $_SESSION['saltstamp'] < 60
						&& $u_passhash == $_SESSION['oldhash']))
				&& $row['user_maingrp'] > 3
				&& ($cfg['ipcheck'] == FALSE || $row['user_lastip'] == $usr['ip']))
			{
				$usr['id'] = (int) $row['user_id'];
				$usr['name'] = $row['user_name'];
				$usr['maingrp'] = $row['user_maingrp'];
				$usr['lastvisit'] = $row['user_lastvisit'];
				$usr['lastlog'] = $row['user_lastlog'];
				$usr['timezone'] = $row['user_timezone'];
				$usr['skin'] = ($cfg['forcedefaultskin']) ? $cfg['defaultskin'] : $row['user_skin'];
				$usr['theme'] = $row['user_theme'];
				$usr['lang'] = ($cfg['forcedefaultlang']) ? $cfg['defaultlang'] : $row['user_lang'];
				$usr['newpm'] = $row['user_newpm'];
				$usr['auth'] = unserialize($row['user_auth']);
				$usr['level'] = $sed_groups[$usr['maingrp']]['level'];
				$usr['profile'] = $row;

				$_SESSION['user_id'] = $usr['id'];

				if ($usr['lastlog'] + $cfg['timedout'] < $sys['now_offset'])
				{
					$sys['comingback'] = TRUE;
					if ($usr['lastlog'] > $usr['lastvisit'])
					{
						$usr['lastvisit'] = $usr['lastlog'];
						$update_lastvisit = ", user_lastvisit = ".$usr['lastvisit'];
					}
				}


				if (!$cfg['authcache'] || empty($row['user_auth']))
				{
					$usr['auth'] = sed_auth_build($usr['id'], $usr['maingrp']);
					if ($cfg['authcache']) $update_auth = ", user_auth='".serialize($usr['auth'])."'";
				}

				if (empty($_SESSION['saltstamp']) || $sys['now_offset'] - $_SESSION['saltstamp'] > 60)
				{
					$_SESSION['saltstamp'] = $sys['now_offset'];
					$_SESSION['oldhash'] = $u_passhash;
					$hashsalt = sed_unique(16);
					$passhash = md5($row['user_password'].$hashsalt);
					$u = base64_encode($usr['id'].':_:'.$passhash);
					if (empty($_SESSION[$site_id]))
					{
						sed_setcookie($site_id, $u, time() + $cfg['cookielifetime'], $cfg['cookiepath'], $cfg['cookiedomain'], $sys['secure'], true);
					}
					else
					{
						$_SESSION[$site_id] = $u;
					}
					$update_hashsalt = ", user_hashsalt = '$hashsalt'";
				}

				if (empty($_COOKIE['sourcekey']))
				{
					$sys['xk'] = mb_strtoupper(sed_unique(8));
					$update_sid = ", user_sid = '{$sys['xk']}'";
					sed_setcookie('sourcekey', $sys['xk'], time() + $cfg['cookielifetime'], $cfg['cookiepath'], $cfg['cookiedomain'], $sys['secure'], true);
				}
				else
				{
					$sys['xk'] = $_COOKIE['sourcekey'];
					$update_sid = '';
				}

				sed_sql_query("UPDATE $db_users
					SET user_lastlog = {$sys['now_offset']} $update_lastvisit $update_sid $update_hashsalt $update_auth
					WHERE user_id='{$usr['id']}'");

				unset($u, $passhash, $update_auth, $update_hashsalt, $update_lastvisit, $update_sid);
			}
		}
	}
	else
	{
		$usr['skin'] = sed_import($u[0], 'D', 'ALP');
		$usr['theme'] = sed_import($u[1], 'D', 'ALP');
		$usr['lang'] = sed_import($u[2], 'D', 'ALP');
	}
}

if ($usr['id'] == 0)
{
	if (!$sed_guest_auth)
	{
		$sed_guest_auth = sed_auth_build(0);
		$cfg['cache'] && $cot_cache->db_set('sed_guest_auth', $sed_guest_auth, 'system');
	}
	$usr['auth'] = $sed_guest_auth;
	unset($sed_guest_auth);
	$usr['skin'] = empty($usr['skin']) ? $cfg['defaultskin'] : $usr['skin'];
	$usr['theme'] = empty($usr['theme']) ? $cfg['defaulttheme'] : $usr['theme'];
	$usr['lang'] = empty($usr['lang']) ? $cfg['defaultlang'] : $usr['lang'];
	$sys['xk'] = mb_strtoupper(dechex(crc32($sys['abs_url']))); // Site related key for guests
}

/* === Hook === */
$extp = sed_getextplugins('input');
foreach ($extp as $pl)
{
	include $pl;
}
/* ======================== */


/* ======== Maintenance mode ======== */

if ($cfg['maintenance'])
{
	$sqll = sed_sql_query("SELECT grp_maintenance FROM $db_groups WHERE grp_id='".$usr['maingrp']."' ");
	$roow = sed_sql_fetcharray($sqll);

	if (!$roow['grp_maintenance'] && !defined('SED_AUTH'))
	{
		sed_redirect(sed_url('users', 'm=auth', '', true));
	}
}

/* ======== Zone variables ======== */

$z_tmp = sed_import('z', 'G',' ALP', 32);
$z = empty($z_tmp) ? $z : $z_tmp;
$m = sed_import('m', 'G', 'ALP', 24);
$n = sed_import('n', 'G', 'ALP', 24);
$a = sed_import('a', 'G', 'ALP', 24);
$b = sed_import('b', 'G', 'ALP', 24);

/* ======== Who's online (part 1) and shield protection ======== */

if (!$cfg['disablewhosonline'] || $cfg['shieldenabled'])
{
	if ($usr['id'] > 0)
	{
		$sql = sed_sql_query("SELECT * FROM $db_online WHERE online_userid=".$usr['id']);

		if ($row = sed_sql_fetcharray($sql))
		{
			$online_count = 1;
			$sys['online_location'] = $row['online_location'];
			$sys['online_subloc'] = $row['online_subloc'];
			if ($cfg['shieldenabled'] && (!sed_auth('admin', 'a', 'A') || SED_SHIELD_FORCE))
			{
				$shield_limit = $row['online_shield'];
				$shield_action = $row['online_action'];
				$shield_hammer = sed_shield_hammer($row['online_hammer'], $shield_action, $row['online_lastseen']);
				$sys['online_hammer'] = $shield_hammer;
			}
		}
	}
	else
	{
		$sql = sed_sql_query("SELECT * FROM $db_online WHERE online_ip='".$usr['ip']."'");
		$online_count = sed_sql_numrows($sql);

		if ($online_count > 0)
		{
			if ($row = sed_sql_fetcharray($sql))
			{
				$sys['online_location'] = $row['online_location'];
				$sys['online_subloc'] = $row['online_subloc'];
				if ($cfg['shieldenabled'])
				{
					$shield_limit = $row['online_shield'];
					$shield_action = $row['online_action'];
					$shield_hammer = sed_shield_hammer($row['online_hammer'], $shield_action, $row['online_lastseen']);
					$sys['online_hammer'] = $shield_hammer;
				}
			}
		}
	}
}

/* ======== Max users ======== */

if (!$cfg['disablehitstats'])
{
	if ($cot_cache->mem_available && $cot_cache->mem_isset('maxusers', 'system'))
	{
		$maxusers = $cot_cache->mem_get('maxusers', 'system');
	}
	else
	{
		$sql = sed_sql_query("SELECT stat_value FROM $db_stats where stat_name='maxusers' LIMIT 1");
		$maxusers = (int) @sed_sql_result($sql, 0, 0);
		$cot_cache->mem_available && $cot_cache->mem_set('maxusers', $maxusers, 'system', 0);
	}

	if ($maxusers < $sys['whosonline_all_count'])
	{
		$sql = sed_sql_query("UPDATE $db_stats SET stat_value='".$sys['whosonline_all_count']."'
			WHERE stat_name='maxusers'");
	}
}

/* ======== Language ======== */
$lang = $usr['lang'];
require_once sed_langfile('main', 'core');

/* ======== Who's online part 2 ======== */

$out['whosonline'] = ($cfg['disablewhosonline']) ? '' : sed_declension($sys['whosonline_reg_count'], $Ls['Members']).', '.sed_declension($sys['whosonline_vis_count'], $Ls['Guests']);

/* ======== Skin ======== */

$usr['skin_raw'] = $usr['skin'];

if (@file_exists('./skins/'.$usr['skin'].'.'.$usr['lang'].'/header.tpl'))
{
	$usr['skin'] = $usr['skin'].'.'.$usr['lang'];
}

$mskin = './skins/'.$usr['skin'].'/header.tpl';
if (!file_exists($mskin))
{
	$out['notices'] .= $L['com_skinfail'].'<br />';
	$usr['skin'] = $cfg['defaultskin'];
	$mskin = './skins/'.$usr['skin'].'/header.tpl';
	if (!file_exists($mskin))
	{
		sed_diefatal('Default skin not found.'); // TODO: Need translate
	}
}

$mtheme = sed_themefile();
if (!$mtheme)
{
	sed_diefatal('Default theme not found.'); // TODO: Need translate
}

require_once sed_langfile('skin', 'core');

$usr['def_skin_lang'] = './skins/'.$usr['skin'].'/'.$usr['skin_raw'].'.en.lang.php';
$usr['skin_lang'] = './skins/'.$usr['skin'].'/'.$usr['skin_raw'].'.'.$usr['lang'].'.lang.php';
if ($usr['skin_lang'] != $usr['def_skin_lang'] && @file_exists($usr['skin_lang']))
{
	require_once $usr['skin_lang'];
}
elseif (@file_exists($usr['def_skin_lang']))
{
	require_once $usr['def_skin_lang'];
}

$skin = $usr['skin'];
$theme = $usr['theme'];

// Resource strings
require_once $cfg['system_dir'].'/resources.php';
// Skin resources
require_once './skins/'.$usr['skin'].'/'.$usr['skin'].'.php';

$out['copyright'] = "<a href=\"http://www.cotonti.com\">".$L['foo_poweredby']." Cotonti</a>";

/* ======== Basic statistics ======== */

if (!$cfg['disablehitstats'])
{
	if ($cfg['cache'] && $cot_cache->mem_available)
	{
		$hits = $cot_cache->mem_inc('hits', 'system');
		$cfg['hit_precision'] > 0 || $cfg['hit_precision'] = 100;
		if ($hits % $cfg['hit_precision'] == 0)
		{
			sed_stat_inc('totalpages', $cfg['hit_precision']);
			sed_stat_inc($sys['day'], $cfg['hit_precision']);
		}
	}
	else
	{
		sed_stat_inc('totalpages');
		sed_stat_update($sys['day']);
	}

	$sys['referer'] = substr($_SERVER['HTTP_REFERER'], 0, 255);

	if (!empty($sys['referer'])
		&& mb_stripos($sys['referer'], $cfg['mainurl']) === false
		&& mb_stripos($sys['referer'], $cfg['hostip']) === false
		&& mb_stripos($sys['referer'], str_ireplace('//www.', '//', $cfg['mainurl'])) === false
		&& mb_stripos(str_ireplace('//www.', '//', $sys['referer']), $cfg['mainurl']) === false)
	{
		sed_sql_query("INSERT INTO $db_referers
				(ref_url, ref_count, ref_date)
			VALUES
				('".sed_sql_prep($sys['referer'])."', 1, {$sys['now_offset']})
			ON DUPLICATE KEY UPDATE
				ref_count=ref_count+1, ref_date={$sys['now_offset']}");
	}
}

/* ======== Categories ======== */

if (!$sed_cat && !$cfg['disable_page'])
{
	sed_load_structure();
	$cfg['cache'] && $cot_cache->db_set('sed_cat', $sed_cat, 'system');
}

/* ======== Various ======== */

$sed_yesno[0] = $L['No'];
$sed_yesno[1] = $L['Yes'];
$sed_img_up = $R['icon_up'];
$sed_img_down = $R['icon_down'];
$sed_img_left = $R['icon_left'];
$sed_img_right = $R['icon_right'];

/* ======== Local/GMT time ======== */

$usr['timetext'] = sed_build_timezone($usr['timezone']);
$usr['gmttime'] = @date($cfg['dateformat'], $sys['now_offset']).' GMT';

/* ======== Anti-XSS protection ======== */

$x = empty($_POST['x']) ? $_GET['x'] : $_POST['x'];
if (!defined('SED_NO_ANTIXSS') && !defined('SED_AUTH') && ($_SERVER['REQUEST_METHOD'] == 'POST' && $x != $sys['xk'] || isset($_GET['x']) && $_GET['x'] != $sys['xk']))
{
	sed_redirect(sed_url('message', 'msg=950', '', true));
}

/* ======== Global hook ======== */

$extp = sed_getextplugins('global');
foreach ($extp as $pl)
{
	include $pl;
}

/* ======== Pre-loads ======== */

if ($cfg['parser_custom'])
{
	include_once $cfg['system_dir'].'/parser.php';
}

if (!$cfg['parser_disable'])
{
	if (!is_array($sed_smilies))
	{
		sed_load_smilies();
		$cfg['cache'] && $cot_cache->db_set('sed_smilies', $sed_smilies, 'system');
	}
	if (!is_array($sed_bbcodes))
	{
		sed_bbcode_load();
		if ($cfg['cache'])
		{
			$cot_cache->db_set('sed_bbcodes', $sed_bbcodes, 'system');
			$cot_cache->db_set('sed_bbcodes_post', $sed_bbcodes_post, 'system');
			$cot_cache->db_set('sed_bbcode_containers', $sed_bbcode_containers, 'system');
		}
	}
}

?>