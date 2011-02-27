<?php
/**
 * Global header
 *
 * @package Cotonti
 * @version 0.0.6
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

sed_uriredir_store();

/* === Hook === */
$extp = sed_getextplugins('header.first');
if (is_array($extp))
{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

$out['logstatus'] = ($usr['id']>0) ? $L['hea_youareloggedas'].' '.$usr['name'] : $L['hea_youarenotlogged'];
$out['userlist'] = (sed_auth('users', 'a', 'R')) ? "<a href=\"".sed_url('users')."\">".$L['Users']."</a>" : '';
$out['compopup'] = sed_javascript($morejavascript);

unset($title_tags, $title_data);
$title_tags[] = array('{MAINTITLE}', '{DESCRIPTION}', '{SUBTITLE}');
$title_tags[] = array('%1$s', '%2$s', '%3$s');
$title_data = array($cfg['maintitle'], $cfg['subtitle'], $out['subtitle']);
if(defined('SED_INDEX'))
{
	$out['fulltitle'] = sed_title('title_header_index', $title_tags, $title_data);
}
else
{
	$out['fulltitle'] = sed_title('title_header', $title_tags, $title_data);
}

$out['meta_contenttype'] = ($cfg['doctypeid']>2 && $cfg['xmlclient']) ? "application/xhtml+xml" : "text/html";
$out['basehref'] = '<base href="'.$cfg['mainurl'].'/" />';
$out['meta_charset'] = $cfg['charset'];
$out['meta_desc'] = empty($plug_desc) ? htmlspecialchars($cfg['maintitle'])." - ".htmlspecialchars($cfg['subtitle']) : $plug_desc;
$out['meta_keywords'] = empty($plug_keywords) ? $cfg['metakeywords'] : $plug_keywords;
$out['meta_lastmod'] = gmdate("D, d M Y H:i:s");
$out['head_head'] = $plug_head;

sed_sendheaders();

if (!SED_AJAX)
{
	if (sed_auth('page', 'any', 'A'))
	{
		$sqltmp2 = sed_sql_query("SELECT COUNT(*) FROM $db_pages WHERE page_state=1");
		$sys['pagesqueued'] = sed_sql_result($sqltmp2,0,'COUNT(*)');

		if ($sys['pagesqueued']>0)
		{
			$out['notices'] .= $L['hea_valqueues'];

			if ($sys['pagesqueued']==1)
			{ $out['notices'] .= "<a href=\"".sed_url('admin', 'm=page&s=queue')."\">"."1 ".$L['Page']."</a> "; }
			elseif ($sys['pagesqueued']>1)
			{ $out['notices'] .= "<a href=\"".sed_url('admin', 'm=page&s=queue')."\">".$sys['pagesqueued']." ".$L['Pages']."</a> "; }
		}
	}
	elseif ($usr['id'] > 0 && sed_auth('page', 'any', 'W'))
	{
		$sqltmp2 = sed_sql_query("SELECT COUNT(*) FROM $db_pages WHERE page_state=1 AND page_ownerid = " . $usr['id']);
		$sys['pagesqueued'] = sed_sql_result($sqltmp2,0,'COUNT(*)');

		if ($sys['pagesqueued']>0)
		{
			$out['notices'] .= $L['hea_valqueues'];

			if ($sys['pagesqueued']==1)
			{ $out['notices'] .= "<a href=\"".sed_url('list', 'c=unvalidated')."\">"."1 ".$L['Page']."</a> "; }
			elseif ($sys['pagesqueued']>1)
			{ $out['notices'] .= "<a href=\"".sed_url('list', 'c=unvalidated')."\">".$sys['pagesqueued']." ".$L['Pages']."</a> "; }
		}
	}

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
		"HEADER_BASEHREF" => $out['basehref'],
		"HEADER_META_CONTENTTYPE" => $out['meta_contenttype'],
		"HEADER_META_CHARSET" => $out['meta_charset'],
		"HEADER_META_DESCRIPTION" => $out['meta_desc'],
		"HEADER_META_KEYWORDS" => $out['meta_keywords'],
		"HEADER_META_LASTMODIFIED" => $out['meta_lastmod'],
		"HEADER_HEAD" => $out['head_head'],
		"HEADER_CANONICAL_URL" => str_replace('&', '&amp;', $sys['canonical_uri'])
	));

	/* === Hook === */
	$extp = sed_getextplugins('header.body');
	if (is_array($extp))
		{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
	/* ===== */


	if ($usr['id']>0)
	{
		$out['adminpanel'] = (sed_auth('admin', 'any', 'R')) ? "<a href=\"".sed_url('admin')."\">".$L['Administration']."</a>" : '';
		$out['loginout_url'] = sed_url('users', 'm=logout&'.sed_xg());
		$out['loginout'] = "<a href=\"".$out['loginout_url']."\">".$L['Logout']."</a>";
		$out['profile'] = "<a href=\"".sed_url('users', 'm=profile')."\">".$L['Profile']."</a>";
		$out['pms'] = ($cfg['disable_pm']) ? '' : "<a href=\"".sed_url('pm')."\">".$L['Private_Messages']."</a>";
		$out['pfs'] = ($cfg['disable_pfs'] || !sed_auth('pfs', 'a', 'R') || $sed_groups[$usr['maingrp']]['pfs_maxtotal']==0 || 	$sed_groups[$usr['maingrp']]['pfs_maxfile']==0) ? '' : "<a href=\"".sed_url('pfs')."\">".$L['Mypfs']."</a>";

		if (!$cfg['disable_pm'])
		{
			if ($usr['newpm'])
			{
				$sqlpm = sed_sql_query("SELECT COUNT(*) FROM $db_pm WHERE pm_touserid='".$usr['id']."' AND pm_state=0");
				$usr['messages'] = sed_sql_result($sqlpm,0,'COUNT(*)');
			}
			$out['pmreminder'] = "<a href=\"".sed_url('pm')."\">";
			$out['pmreminder'] .= ($usr['messages']>0) ? sed_declension($usr['messages'],$L['hea_privatemessages']) : $L['hea_noprivatemessages'];
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
		$out['guest_username'] = "<input type=\"text\" name=\"rusername\" size=\"12\" maxlength=\"100\" />";
		$out['guest_password'] = "<input type=\"password\" name=\"rpassword\" size=\"12\" maxlength=\"32\" />";
		$out['guest_register'] = "<a href=\"".sed_url('users', 'm=register')."\">".$L["Register"]."</a>";
		$out['guest_cookiettl'] = $cfg['forcerememberme'] ?
			'<input type="checkbox" name="rremember" checked="checked" disabled="disabled" />'
			: '<input type="checkbox" name="rremember" />';

		$t->assign(array (
			"HEADER_GUEST_SEND" => sed_url('users', 'm=auth&a=check&'.$sys['url_redirect']),
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
}
?>