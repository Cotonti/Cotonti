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
$site_id = 'ct'.substr(md5(empty($cfg['site_id']) ? $cfg['mainurl'] : $cfg['site_id']), 0, 16);
$sys['site_id'] = $site_id;

if (empty($z))
{
	$z = sed_import('z', 'G', 'ALP');
	$z = empty($z) ? 'index' : $z;
}

session_start();

/* =========== Early page cache ==========*/
if ($cfg['cache'] && !$cfg['devmode'])
{
	require_once $cfg['system_dir'].'/cache.php';
	$cot_cache = new Cache();
	if ($_SERVER['REQUEST_METHOD'] == 'GET' && empty($_COOKIE[$site_id]) && empty($_SESSION[$site_id]))
	{
		$cache_z = ($z == 'list') ? 'page' : $z;
		if ($cfg["cache_$cache_z"])
		{
			$cot_cache->page->init($cache_z, $cfg['defaultskin']);
			$cot_cache->page->read();
		}
	}
}
else
{
	$cot_cache = false;
}

/* ======== Connect to the SQL DB======== */

require_once $cfg['system_dir'].'/database.'.$cfg['sqldb'].'.php';
$sed_dbc = sed_sql_connect($cfg['mysqlhost'], $cfg['mysqluser'], $cfg['mysqlpassword'], $cfg['mysqldb']);
unset($cfg['mysqlhost'], $cfg['mysqluser'], $cfg['mysqlpassword']);

$cot_cache && $cot_cache->init();

/* ======== Configuration settings ======== */

if ($cot_cache && $cot_cfg)
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
		elseif ($row['config_owner'] == 'module')
		{
			$cfg[$row['config_name']] = $row['config_value']; // TODO use ['module'] in modules instead
			$cfg['module'][$row['config_name']] = $row['config_value'];
		}
		else
		{
			$cfg['plugin'][$row['config_cat']][$row['config_name']] = $row['config_value'];
		}
	}
	$cfg['css'] = $cfg['defaultskin'];

	$cot_cache && $cot_cache->db->store('cot_cfg', $cfg, 'system');
}
// Mbstring options
mb_internal_encoding($cfg['charset']);

/* ======== Extra settings (the other presets are in functions.php) ======== */

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
$sys['scheme'] = $url['scheme'];
$sys['site_uri'] = $url['path'];
$sys['domain'] = preg_replace('#^www\.#', '', $url['host']);
if (empty($cfg['cookiedomain'])) $cfg['cookiedomain'] = $sys['domain'];
if ($sys['site_uri'][mb_strlen($sys['site_uri']) - 1] != '/') $sys['site_uri'] .= '/';
define('SED_SITE_URI', $sys['site_uri']);
if (empty($cfg['cookiepath'])) $cfg['cookiepath'] = $sys['site_uri'];
// Absolute site url
$sys['host'] = preg_match('`^(.+\.)?'.preg_quote($sys['domain']).'$`i', $_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST']
	: $sys['domain'];
$sys['abs_url'] = $url['scheme'].'://'.$sys['host']. $sys['site_uri'];
define('SED_ABSOLUTE_URL', $sys['abs_url']);
// URI redirect appliance
$sys['uri_curr'] = (mb_stripos($_SERVER['REQUEST_URI'], $sys['site_uri']) === 0) ?
	mb_substr($_SERVER['REQUEST_URI'], mb_strlen($sys['site_uri'])) : ltrim($_SERVER['REQUEST_URI'], '/');
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
        sed_sql_freeresult($sql);
	}
	$cot_cache && $cot_cache->db->store('sed_plugins', $sed_plugins, 'system');
}

if (!is_array($sed_urltrans))
{
	sed_load_urltrans();
	$cot_cache && $cot_cache->db->store('sed_urltrans', $sed_urltrans, 'system', 1200);
}

if (!$sed_modules)
{
    $sql = sed_sql_query("SELECT ct_code, ct_title FROM $db_core
		WHERE ct_state = 1 AND ct_lock = 0");
	if (sed_sql_numrows($sql) > 0)
	{
		while ($row = sed_sql_fetcharray($sql))
		{
			$sed_modules[$row['ct_code']] = array(
                'code' => $row['ct_code'],
                'title' => $row['ct_title']
            );
		}
        sed_sql_freeresult($sql);
	}
	$cot_cache && $cot_cache->db->store('sed_modules', $sed_modules, 'system');
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

if (!$cfg['disablebanlist'])
{
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

	$cot_cache && $cot_cache->db->store('sed_groups', $sed_groups, 'system');
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

if (!defined('SED_MESSAGE'))
{
	$_SESSION['s_run_admin'] = defined('SED_ADMIN');
}

if (!empty($_COOKIE[$site_id]) || !empty($_SESSION[$site_id]))
{
	$u = empty($_SESSION[$site_id]) ? explode(':', $_COOKIE[$site_id]) : explode(':', $_SESSION[$site_id]);
	$u_id = (int) sed_import($u[0], 'D', 'INT');
	$u_sid = sed_import($u[1], 'D', 'ALP');
	if ($u_id > 0)
	{
		$sql = sed_sql_query("SELECT * FROM $db_users WHERE user_id = $u_id AND user_sid = '$u_sid'");

		if ($row = sed_sql_fetcharray($sql))
		{
			if ($row['user_maingrp'] > 3
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

				$sys['xk'] = $row['user_token'];

				if (!isset($_SESSION['cot_user_id']))
				{
					$_SESSION['cot_user_id'] = $usr['id'];
				}

				if ($usr['lastlog'] + $cfg['timedout'] < $sys['now_offset'])
				{
					$sys['comingback'] = TRUE;
					if ($usr['lastlog'] > $usr['lastvisit'])
					{
						$usr['lastvisit'] = $usr['lastlog'];
						$update_lastvisit = ", user_lastvisit = " . $usr['lastvisit'];
					}

					// Generate new security token
					$token = sed_unique(16);
					$sys['xk_prev'] = $sys['xk'];
					$sys['xk'] = $token;
					$update_token = ", user_token = '$token'";
				}


				if (!$cfg['authcache'] || empty($row['user_auth']))
				{
					$usr['auth'] = sed_auth_build($usr['id'], $usr['maingrp']);
					if($cfg['authcache']) $update_auth = ", user_auth='".serialize($usr['auth'])."'";
				}

				sed_sql_query("UPDATE $db_users
					SET user_lastlog = {$sys['now_offset']} $update_lastvisit $update_token $update_auth
					WHERE user_id='{$usr['id']}'");

				unset($u, $passhash, $oldhash, $hashsalt, $hashsaltprev, $update_auth, $update_hashsalt,
					$update_lastvisit, $update_sid);
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
		$cot_cache && $cot_cache->db->store('sed_guest_auth', $sed_guest_auth, 'system');
	}
	$usr['auth'] = $sed_guest_auth;
	unset($sed_guest_auth);
	$usr['skin'] = empty($usr['skin']) ? $cfg['defaultskin'] : $usr['skin'];
	$usr['theme'] = empty($usr['theme']) ? $cfg['defaulttheme'] : $usr['theme'];
	$usr['lang'] = empty($usr['lang']) ? $cfg['defaultlang'] : $usr['lang'];
	$sys['xk'] = mb_strtoupper(dechex(crc32($sys['abs_url']))); // Site related key for guests
}

/* === Hook === */
foreach (sed_getextplugins('input') as $pl)
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

$z_tmp = sed_import('z', 'G', 'ALP', 32);
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

/* ======== Language ======== */
$lang = $usr['lang'];
require_once sed_langfile('main', 'core');

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
// Iconpack
if (empty($cfg['defaulticons']))
{
	$cfg['defaulticons'] = 'default';
}
if (empty($usr['icons']))
{
	$usr['icons'] = $cfg['defaulticons'];
}
require_once './images/icons/' . $usr['icons'] . '/resources.php';

$out['copyright'] = "<a href=\"http://www.cotonti.com\">".$L['foo_poweredby']." Cotonti</a>";

/* ======== Basic statistics ======== */

if (!$cfg['disablehitstats'])
{
	if ($cot_cache && $cot_cache->mem)
	{
		$hits = $cot_cache->mem->inc('hits', 'system');
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
	$cot_cache && $cot_cache->db->store('sed_cat', $sed_cat, 'system');
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

$x = sed_import('x', 'P', 'ALP');
if (empty($x) && SED_AJAX && $_SERVER['REQUEST_METHOD'] == 'POST')
{
	$x = sed_import('x', 'G', 'ALP');
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !defined('SED_NO_ANTIXSS') && !defined('SED_AUTH')
	&& $x != $sys['xk'] && (empty($sys['xk_prev']) || $x != $sys['xk_prev']))
{
	sed_redirect(sed_url('message', 'msg=950', '', true));
}

/* ======== Global hook ======== */

foreach (sed_getextplugins('global') as $pl)
{
	include $pl;
}

?>