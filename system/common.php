<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
[BEGIN_SED]
File=system/common.php
Version=125
Updated=2008-may-26
Type=Core
Author=Neocrome
Description=Common
[END_SED]
==================== */

/**
 * @package Seditio-N
 * @copyright Partial copyright (c) Cotonti Team 2008
 */

if (!defined('SED_CODE')) { die('Wrong URL.'); }

/* ======== First... ======== */

set_magic_quotes_runtime(0);
define('MQGPC', get_magic_quotes_gpc());
error_reporting(E_ALL ^ E_NOTICE);

/* ======== Connect to the SQL DB======== */

require_once($cfg['system_dir'].'/database.'.$cfg['sqldb'].'.php');
sed_sql_connect($cfg['mysqlhost'], $cfg['mysqluser'], $cfg['mysqlpassword'], $cfg['mysqldb']);
unset($cfg['mysqlhost'], $cfg['mysqluser'], $cfg['mysqlpassword']);

/* ======== Configuration settings (from the DB) ======== */

$sql_config = sed_sql_query("SELECT config_owner, config_cat, config_name, config_value FROM $db_config");

while ($row = sed_sql_fetcharray($sql_config))
{
	if ($row['config_owner']=='core')
	{ $cfg[$row['config_name']] = $row['config_value']; }
	else
	{ $cfg['plugin'][$row['config_cat']][$row['config_name']] = $row['config_value']; }
}

// Mbstring options
mb_internal_encoding($cfg['charset']);

/* ======== Extra settings (the other presets are in functions.php) ======== */

$sys['day'] = @date('Y-m-d');
$sys['now'] = time();
$sys['now_offset'] = $sys['now'] - $cfg['servertimezone']*3600;
$online_timedout = $sys['now'] - $cfg['timedout'];
$cfg['doctype'] = sed_setdoctype($cfg['doctypeid']);
$cfg['css'] = $cfg['defaultskin'];
$usr['ip'] = ($cfg['clustermode']) ? $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'] : $_SERVER['REMOTE_ADDR'] ;
$sys['unique'] = sed_unique(16);
$sys['url'] = base64_encode($_SERVER['REQUEST_URI']);
$sys['url_redirect'] = 'redirect='.$sys['url'];
$redirect = sed_import('redirect','G','SLU');
// Getting the server-relative path
$sys['site_uri'] = dirname($_SERVER['SCRIPT_NAME']);
$sys['site_uri'] = str_replace('\\', '/', $sys['site_uri']);
if($sys['site_uri'][mb_strlen($sys['site_uri']) - 1] != '/') $sys['site_uri'] .= '/';
define('SED_SITE_URI', $sys['site_uri']);
// Absolute site url
$sys['abs_url'] = ($sys['site_uri'][0] == '/') ? 'http://'.$_SERVER['HTTP_HOST'].$sys['site_uri'] : 'http://'.$_SERVER['HTTP_HOST'].'/'.$sys['site_uri'];
define('SED_ABSOLUTE_URL', $sys['abs_url']);

/* ======== Internal cache ======== */

if ($cfg['cache'])
{
	$sql = sed_cache_getall();
	if ($sql)
	{
		while ($row = sed_sql_fetcharray($sql))
		{ $$row['c_name'] = unserialize($row['c_value']); }
	}
}

/* ======== Plugins ======== */

if (!$sed_plugins)
{
	$sql = sed_sql_query("SELECT * FROM $db_plugins WHERE pl_active=1 ORDER BY pl_hook ASC, pl_order ASC");
	if (sed_sql_numrows($sql)>0)
	{
		while ($row = sed_sql_fetcharray($sql))
		{ $sed_plugins[] = $row; }
	}
	sed_cache_store('sed_plugins', $sed_plugins, 3300);
}

/* ======== Gzip and output filtering ======== */

if ($cfg['gzip'])
{ @ob_start('ob_gzhandler'); }
else
{ ob_start(); }

ob_start('sed_outputfilters');

/* ======== Check the banlist ======== */

$userip = explode('.', $usr['ip']);
$ipmasks = "('".$userip[0].".".$userip[1].".".$userip[2].".".$userip[3]."','".$userip[0].".".$userip[1].".".$userip[2].".*','".$userip[0].".".$userip[1].".*.*','".$userip[0].".*.*.*')";

$sql = sed_sql_query("SELECT banlist_id, banlist_ip, banlist_reason, banlist_expire FROM $db_banlist WHERE banlist_ip IN ".$ipmasks, 'Common/banlist/check');

If (sed_sql_numrows($sql)>0)
{
	$row=sed_sql_fetcharray($sql);
	if ($sys['now']>$row['banlist_expire'] && $row['banlist_expire']>0)
	{
		$sql = sed_sql_query("DELETE FROM $db_banlist WHERE banlist_id='".$row['banlist_id']."' LIMIT 1");
	}
	else
	{
		$disp = "Your IP is banned.<br />Reason: ".$row['banlist_reason']."<br />Until: ";
		$disp .= ($row['banlist_expire']>0) ? @date($cfg['dateformat'], $row['banlist_expire'])." GMT" : "Never expire.";
		sed_diefatal($disp);
	}
}

/* ======== Groups ======== */

if (!$sed_groups )
{
	$sql = sed_sql_query("SELECT * FROM $db_groups WHERE grp_disabled=0 ORDER BY grp_level DESC");

	if (sed_sql_numrows($sql)>0)
	{
		while ($row = sed_sql_fetcharray($sql))
		{
			$sed_groups[$row['grp_id']] = array (
				'id' => $row['grp_id'],
				'alias' => $row['grp_alias'],
				'level' => $row['grp_level'],
   				'disabled' => $row['grp_disabled'],
   				'hidden' => $row['grp_hidden'],
				'state' => $row['grp_state'],
				'title' => sed_cc($row['grp_title']),
				'desc' => sed_cc($row['grp_desc']),
				'icon' => $row['grp_icon'],
				'pfs_maxfile' => $row['grp_pfs_maxfile'],
				'pfs_maxtotal' => $row['grp_pfs_maxtotal'],
				'ownerid' => $row['grp_ownerid']
			);
		}
	}
	else
	{ sed_diefatal('No groups found.'); }

	sed_cache_store('sed_groups',$sed_groups,3600);
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

if ($cfg['authmode']==2 || $cfg['authmode']==3)
{ session_start(); }

if (isset($_SESSION['rsedition']) && ($cfg['authmode']==2 || $cfg['authmode']==3))
{
	$rsedition = $_SESSION['rsedition'];
	$rseditiop = $_SESSION['rseditiop'];
	$rseditios = $_SESSION['rseditios'];
}
elseif (isset($_COOKIE['SEDITIO']) && ($cfg['authmode']==1 || $cfg['authmode']==3))
{
	$u = base64_decode($_COOKIE['SEDITIO']);
	$u = explode(':_:',$u);
	$rsedition = sed_import($u[0],'D','INT');
	$rseditiop = sed_import($u[1],'D','PSW');
	$rseditios = sed_import($u[2],'D','ALP');
}

if ($rsedition>0 && $cfg['authmode']>0)
{
	if (mb_strlen($rseditiop)!=32 || strstr($rseditiop, "'") || strstr($rseditiop, '"'))
	{ sed_diefatal('Wrong value for the password.'); }

	if ($cfg['ipcheck'])
	{ $sql = sed_sql_query("SELECT * FROM $db_users WHERE user_id='$rsedition' AND user_password='$rseditiop' AND user_lastip='".$usr['ip']."'"); }
	else
	{ $sql = sed_sql_query("SELECT * FROM $db_users WHERE user_id='$rsedition' AND user_password='$rseditiop'"); }

	if ($row = sed_sql_fetcharray($sql))
	{
		if ($row['user_maingrp']>3)
		{
			$usr['id'] = $row['user_id'];
			$usr['sessionid'] = ($cfg['authmode']==1) ? md5($row['user_lastvisit']) : session_id();
			$usr['name'] = $row['user_name'];
			$usr['maingrp'] = $row['user_maingrp'];
			$usr['lastvisit'] = $row['user_lastvisit'];
			$usr['lastlog'] = $row['user_lastlog'];
			$usr['timezone'] = $row['user_timezone'];
			$usr['skin'] = ($cfg['forcedefaultskin']) ? $cfg['defaultskin'] : $row['user_skin'];
			$usr['lang'] = ($cfg['forcedefaultlang']) ? $cfg['defaultlang'] : $row['user_lang'];
			$usr['newpm'] = $row['user_newpm'];
			$usr['auth'] = unserialize($row['user_auth']);
			$usr['level'] = $sed_groups[$usr['maingrp']]['level'];
			$usr['profile'] = $row;

			if ($usr['lastlog']+$cfg['timedout'] < $sys['now_offset'])
			{
				$sys['comingback']= TRUE;
				$usr['lastvisit'] = $usr['lastlog'];
				$sys['sql_update_lastvisit'] = ", user_lastvisit='".$usr['lastvisit']."'";
			}

			if (empty($row['user_auth']))
			{
				$usr['auth'] = sed_auth_build($usr['id'], $usr['maingrp']);
				$sys['sql_update_auth'] = ", user_auth='".serialize($usr['auth'])."'";
			}

			$sql = sed_sql_query("UPDATE $db_users SET user_lastlog='".$sys['now_offset']."', user_lastip='".$usr['ip']."', user_sid='".$usr['sessionid']."', user_logcount=user_logcount+1 ".$sys['sql_update_lastvisit']." ".$sys['sql_update_auth']." WHERE user_id='".$usr['id']."'");
		}
	}
}
else
{
	if (empty($rseditios) && ($cfg['authmode']==1 || $cfg['authmode']==3))
	{
		$u = base64_encode('0:_:0:_:'.$cfg['defaultskin']);
		setcookie('SEDITIO',$u,time()+($cfg['cookielifetime']*86400),$cfg['cookiepath'],$cfg['cookiedomain']);
	}
	else
	{
		$skin = ($cfg['forcedefaultskin']) ? $cfg['defaultskin'] : $rseditios;
	}
}

if ($usr['id']==0)
{
	$usr['auth'] = sed_auth_build(0);
	$usr['skin'] = (empty($usr['skin'])) ? $cfg['defaultskin'] : $usr['skin'];
	$usr['lang'] = $cfg['defaultlang'];
}

/* === Hook === */
$extp = sed_getextplugins('input');
if (is_array($extp))
{
	foreach($extp as $k => $pl)
	{
		include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php');
	}
}
/* ======================== */


/* ======== Anti-XSS protection ======== */

$xg = sed_import('x','G','ALP');
$xp = sed_import('x','P','ALP');

if(!defined('SED_NO_ANTIXSS'))
{
	$xk = sed_check_xp();
}

/* ======== Zone variables ======== */

$z = mb_strtolower(sed_import('z','G','ALP',32));
$m = sed_import('m','G','ALP',24);
$n = sed_import('n','G','ALP',24);
$a = sed_import('a','G','ALP',24);
$b = sed_import('b','G','ALP',24);

/* ======== Who's online (part 1) and shield protection ======== */

if (!$cfg['disablewhosonline'] || $cfg['shieldenabled'])
{

	$sql = sed_sql_query("DELETE FROM $db_online WHERE online_lastseen<'$online_timedout'");
	$sql = sed_sql_query("SELECT COUNT(*) FROM $db_online WHERE online_name='v'");
	$sys['whosonline_vis_count'] = sed_sql_result($sql, 0, 'COUNT(*)');
	$sql = sed_sql_query("SELECT o.online_name, o.online_userid FROM $db_online o WHERE o.online_name NOT LIKE 'v' ORDER BY online_name ASC");
	$sys['whosonline_reg_count'] = sed_sql_numrows($sql);
	$sys['whosonline_all_count'] = $sys['whosonline_reg_count'] + $sys['whosonline_vis_count'];

	$ii=0;
	while ($row = sed_sql_fetcharray($sql))
	{
		$out['whosonline_reg_list'] .= ($ii>0) ? ', ' : '';
		$out['whosonline_reg_list'] .= sed_build_user($row['online_userid'], sed_cc($row['online_name']));
		$sed_usersonline[] = $row['online_userid'];
		$ii++;
	}
}

/* ======== Max users ======== */

if (!$cfg['disablehitstats'])
{
	$sql = sed_sql_query("SELECT stat_value FROM $db_stats where stat_name='maxusers' LIMIT 1");

	if ($row = sed_sql_fetcharray($sql))
	{ $maxusers = $row[0]; }
	else
	{ $sql = sed_sql_query("INSERT INTO $db_stats (stat_name, stat_value) VALUES ('maxusers', 1)"); }

	if ($maxusers<$sys['whosonline_all_count'])
	{ $sql = sed_sql_query("UPDATE $db_stats SET stat_value='".$sys['whosonline_all_count']."' WHERE stat_name='maxusers'"); }
}

/* ======== Language ======== */

$mlang = $cfg['system_dir'].'/lang/'.$usr['lang'].'/main.lang.php';

if (!file_exists($mlang))
{
	$usr['lang'] = $cfg['defaultlang'];
	$mlang = $cfg['system_dir'].'/lang/'.$usr['lang'].'/main.lang.php';

	if (!file_exists($mlang))
	{ sed_diefatal('Main language file not found.'); }
}

$lang = $usr['lang'];
require_once($mlang);

/* ======== Who's online part 2 ======== */

$out['whosonline'] = ($cfg['disablewhosonline']) ? '' : $sys['whosonline_reg_count'].' '.$L['com_members'].', '.$sys['whosonline_vis_count'].' '.$L['com_guests'];
$out['copyright'] = "<a href=\"http://www.neocrome.net\">".$L['foo_poweredby']." Seditio</a>";

/* ======== Skin ======== */

$usr['skin_raw'] = $usr['skin'];

if (@file_exists('skins/'.$usr['skin'].'.'.$usr['lang'].'/header.tpl'))
{ $usr['skin'] = $usr['skin'].'.'.$usr['lang']; }

$mskin = 'skins/'.$usr['skin'].'/header.tpl';

if (!file_exists($mskin))
{
	$out['notices'] .= $L['com_skinfail'].'<br />';
	$usr['skin'] = $cfg['defaultskin'];
	$mskin = 'skins/'.$usr['skin'].'/header.tpl';

	if (!file_exists($mskin))
	{ sed_diefatal('Default skin not found.'); }
}

$usr['skin_lang'] = './skins/'.$usr['skin'].'/'.$usr['skin_raw'].'.'.$usr['lang'].'.lang.php';

if (@file_exists($usr['skin_lang']))
{ require_once($usr['skin_lang']); }

require_once('./skins/'.$usr['skin'].'/'.$usr['skin'].'.php');

$skin = $usr['skin'];

/* ======== Basic statistics ======== */

if (!$cfg['disablehitstats'])
{
	sed_stat_inc('totalpages');
	$hits_today = sed_stat_get($sys['day']);

	if ($hits_today>0)
	{ sed_stat_inc($sys['day']); }
	else
	{ sed_stat_create($sys['day']); }

	$sys['referer'] = substr($_SERVER['HTTP_REFERER'], 0, 255);

	if (@!strstr($sys['referer'], $cfg['mainurl'])
	&& @!strstr($sys['referer'], $cfg['hostip'])
	&& @!strstr($sys['referer'], str_replace('www.', '', $cfg['mainurl']))
	&& !empty($sys['referer']))
	{
		$sql = sed_sql_query("SELECT COUNT(*) FROM $db_referers WHERE ref_url = '".sed_sql_prep($sys['referer'])."'");
		$count = sed_sql_result($sql,0,"COUNT(*)");

		if ($count>0)
		{
			$sql = sed_sql_query("UPDATE $db_referers SET ref_count=ref_count+1,
			ref_date='".$sys['now_offset']."'
				WHERE ref_url='".sed_sql_prep($sys['referer'])."'");
		}
		else
		{
			$sql = sed_sql_query("INSERT INTO $db_referers
			(ref_url,
			ref_count,
			ref_date)
			VALUES
			('".sed_sql_prep($sys['referer'])."',
				'1',
				".(int)$sys['now_offset'].")");
		}
	}
}

/* ======== Categories ======== */

if (!$sed_cat && !$cfg['disable_page'])
{
	$sed_cat = sed_load_structure();
	sed_cache_store('sed_cat', $sed_cat, 3600);
}

/* ======== Forums ======== */

if (!$sed_forums_str && !$cfg['disable_forums'])
{
	$sed_forums_str = sed_load_forum_structure();
	sed_cache_store('sed_forums_str', $sed_forums_str, 3600);
}

/* ======== Various ======== */

$out['img_up'] = "<img src=\"skins/".$usr['skin']."/img/system/arrow-up.gif\" alt=\"\" />";
$out['img_down'] = "<img src=\"skins/".$usr['skin']."/img/system/arrow-down.gif\" alt=\"\" />";
$out['img_left'] = "<img src=\"skins/".$usr['skin']."/img/system/arrow-left.gif\" alt=\"\" />";
$out['img_right'] = "<img src=\"skins/".$usr['skin']."/img/system/arrow-right.gif\" alt=\"\" />";

$sed_yesno[0] = $L['No'];
$sed_yesno[1] = $L['Yes'];
$sed_img_up = $out['img_up'];
$sed_img_down = $out['img_down'];
$sed_img_left = $out['img_left'];
$sed_img_right = $out['img_right'];

/* ======== Smilies ======== */

if (!$sed_smilies)
{
	$sql = sed_sql_query("SELECT * FROM $db_smilies ORDER by smilie_order ASC, smilie_id ASC");
	if (sed_sql_numrows($sql)>0)
	{
		while ($row = sed_sql_fetcharray($sql))
		{ $sed_smilies[] = $row; }
	}
	sed_cache_store('sed_smilies',$sed_smilies,3550);
}

/* ======== Local/GMT time ======== */

$usr['timetext'] = sed_build_timezone($usr['timezone']);
$usr['gmttime'] = @date($cfg['dateformat'],$sys['now_offset']).' GMT';

/* ======== Global hook ======== */

$extp = sed_getextplugins('global');
if (is_array($extp))
{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }

/* ======== Pre-loads ======== */

if(!$cfg['parser_custom'])
{
	sed_bbcode_load();
}

?>
