<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
[BEGIN_SED]
File=system/header.php
Version=102
Updated=2006-apr-17
Type=Core
Author=Neocrome
Description=Global header
[END_SED]
==================== */

if (!defined('SED_CODE')) { die('Wrong URL.'); }

/* === Hook === */
$extp = sed_getextplugins('header.first');
if (is_array($extp))
{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

$out['logstatus'] = ($usr['id']>0) ? $L['hea_youareloggedas'].' '.$usr['name'] : $L['hea_youarenotlogged'];
$out['userlist'] = (sed_auth('users', 'a', 'R')) ? "<a href=\"users.php\">".$L['Users']."</a>" : '';
$out['compopup'] = sed_javascript($morejavascript);
$out['fulltitle'] = $cfg['maintitle'];
$out['subtitle'] = (empty($out['subtitle'])) ? $cfg['subtitle'] : $out['subtitle'];
$out['fulltitle'] .= (empty($out['subtitle'])) ? '' : ' - '.$out['subtitle'];
$out['contenttype'] = ($cfg['doctypeid']>2 && $cfg['xmlclient']) ? "application/xhtml+xml" : "text/html";

if (sed_auth('page', 'any', 'A'))
{
	$sqltmp2 = sed_sql_query("SELECT COUNT(*) FROM $db_pages WHERE page_state=1");
	$sys['pagesqueued'] = sed_sql_result($sqltmp2,0,'COUNT(*)');

	if ($sys['pagesqueued']>0)
	{
		$out['notices'] .= $L['hea_valqueues'];

		if ($sys['pagesqueued']==1)
		{ $out['notices'] .= "<a href=\"admin.php?m=page&amp;s=queue\">"."1 ".$L['Page']."</a> "; }
		elseif ($sys['pagesqueued']>1)
		{ $out['notices'] .= "<a href=\"admin.php?m=page&amp;s=queue\">".$sys['pagesqueued']." ".$L['Pages']."</a> "; }
	}
}

sed_sendheaders();

/* === Hook === */
$extp = sed_getextplugins('header.main');
if (is_array($extp))
{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

if ($cfg['enablecustomhf'])
{ $mskin = sed_skinfile(array('header', mb_strtolower($location))); }
else
{ $mskin = "skins/".$usr['skin']."/header.tpl"; }
$t = new XTemplate($mskin);

$t->assign(array (
	"HEADER_TITLE" => $plug_title.$out['fulltitle'],
	"HEADER_DOCTYPE" => $cfg['doctype'],
	"HEADER_CSS" => $cfg['css'],
	"HEADER_COMPOPUP" => $out['compopup'],
	"HEADER_LOGSTATUS" => $out['logstatus'],
	"HEADER_WHOSONLINE" => $out['whosonline'],
	"HEADER_TOPLINE" => $cfg['topline'],
	"HEADER_BANNER" => $cfg['banner'],
	"HEADER_GMTTIME" => $usr['gmttime'],
	"HEADER_USERLIST" => $out['userlist'],
	"HEADER_NOTICES" => $out['notices'],
	"HEADER_BASEHREF" => '<base href="'.$cfg['mainurl'].'/" />',
	"HEADER_META_CONTENTTYPE" => $out['contenttype'],
	"HEADER_META_CHARSET" => $cfg['charset'],
	"HEADER_META_DESCRIPTION" => $plug_desc.$cfg['maintitle']." - ".$cfg['subtitle'],
	"HEADER_META_KEYWORDS" => $plug_keywords.$cfg['metakeywords'],
	"HEADER_META_LASTMODIFIED" => gmdate("D, d M Y H:i:s"),
	"HEADER_HEAD" => $plug_head,
));

if ($usr['id']>0)
{
	$out['adminpanel'] = (sed_auth('admin', 'any', 'R')) ? "<a href=\"admin.php\">".$L['Administration']."</a>" : '';
	$out['loginout_url'] = "users.php?m=logout&amp;".sed_xg();
	$out['loginout'] = "<a href=\"".$out['loginout_url']."\">".$L['Logout']."</a>";
	$out['profile'] = "<a href=\"users.php?m=profile\">".$L['Profile']."</a>";
	$out['pms'] = ($cfg['disable_pm']) ? '' : "<a href=\"pm.php\">".$L['Private_Messages']."</a>";
	$out['pfs'] = ($cfg['disable_pfs'] || !sed_auth('pfs', 'a', 'R') || $sed_groups[$usr['maingrp']]['pfs_maxtotal']==0 || 	$sed_groups[$usr['maingrp']]['pfs_maxfile']==0) ? '' : "<a href=\"pfs.php\">".$L['Mypfs']."</a>";

	if (!$cfg['disable_pm'])
	{
		if ($usr['newpm'])
		{
			$sqlpm = sed_sql_query("SELECT COUNT(*) FROM $db_pm WHERE pm_touserid='".$usr['id']."' AND pm_state=0");
			$usr['messages'] = sed_sql_result($sqlpm,0,'COUNT(*)');
		}
		$out['pmreminder'] = "<a href=\"pm.php\">";
		$out['pmreminder'] .= ($usr['messages']>0) ? $usr['messages'].' '.$L['hea_privatemessages'] : $L['hea_noprivatemessages'];
		$out['pmreminder'] .= "</a>";
	}

	$t->assign(array (
		"HEADER_USER_NAME" => $usr['name'],
		"HEADER_USER_ADMINPANEL" => $out['adminpanel'],
		"HEADER_USER_LOGINOUT" => $out['loginout'],
		"HEADER_USER_PROFILE" => $out['profile'],
		"HEADER_USER_PMS" => $out['pms'],
		"HEADER_USER_PFS" => $out['pfs'],
		"HEADER_USER_PMREMINDER" => $out['pmreminder'],
		"HEADER_USER_MESSAGES" => $usr['messages']
	));

	$t->parse("HEADER.USER");
}
else
{
	$out['guest_username'] = "<input type=\"text\" name=\"rusername\" size=\"12\" maxlength=\"32\" />";
	$out['guest_password'] = "<input type=\"password\" name=\"rpassword\" size=\"12\" maxlength=\"32\" />";
	$out['guest_register'] = "<a href=\"users.php?m=register\">".$L["Register"]."</a>";
	$out['guest_cookiettl'] = "<select name=\"rcookiettl\" size=\"1\">";
	$out['guest_cookiettl'] .= "<option value=\"0\" selected=\"selected\">".$L['No']."</option>";

	$i =array (1800, 3600, 7200, 14400, 28800, 43200, 86400, 172800, 259200, 604800, 1296000, 2592000, 5184000);

	foreach($i as $k => $x)
	{
		$out['guest_cookiettl'] .= ($x<=$cfg['cookielifetime']) ? "<option value=\"$x\">".sed_build_timegap($sys['now_offset'], $sys['now_offset']+$x)."</option>": '';
	}
	$out['guest_cookiettl'] .= "</select>";

	$t->assign(array (
		"HEADER_GUEST_USERNAME" => $out['guest_username'],
		"HEADER_GUEST_PASSWORD" => $out['guest_password'],
		"HEADER_GUEST_REGISTER" => $out['guest_register'],
		"HEADER_GUEST_COOKIETTL" => $out['guest_cookiettl']
	));

	$t->parse("HEADER.GUEST");
}

/* === Hook === */
$extp = sed_getextplugins('header.tags');
if (is_array($extp))
{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

$t->parse("HEADER");
$t->out("HEADER");

?>