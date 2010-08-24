<?php
/**
 * Global header
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

sed_uriredir_store();

/* === Hook === */
foreach (sed_getextplugins('header.first') as $pl)
{
	include $pl;
}
/* ===== */

$out['logstatus'] = ($usr['id'] > 0) ? $L['hea_youareloggedas'].' '.$usr['name'] : $L['hea_youarenotlogged'];
$out['userlist'] = (sed_auth('users', 'a', 'R')) ? sed_rc_link(sed_url('users'), $L['Users']) : '';
$out['compopup'] = sed_javascript($morejavascript);

unset($title_tags, $title_data);
$title_params = array(
	'MAINTITLE' => $cfg['maintitle'],
	'DESCRIPTION' => $cfg['subtitle'],
	'SUBTITLE' => $out['subtitle']
);
if (defined('SED_INDEX'))
{
	$out['fulltitle'] = sed_title('title_header_index', $title_params);
}
else
{
	$out['fulltitle'] = sed_title('title_header', $title_params);
}

$out['meta_contenttype'] = $cfg['xmlclient'] ? 'application/xml' : 'text/html';
$out['basehref'] = $R['code_basehref'];
$out['meta_charset'] = $cfg['charset'];
$out['meta_desc'] = htmlspecialchars($out['desc']);
$out['meta_keywords'] = empty($out['keywords']) ? $cfg['metakeywords'] : htmlspecialchars($out['keywords']);
$out['meta_lastmod'] = gmdate('D, d M Y H:i:s');
$out['head_head'] = $out['head'];

sed_sendheaders($out['meta_contenttype']);

if (!SED_AJAX)
{
	if ($usr['id'] > 0 && $cfg['module']['page'] && !$cfg['disable_page'] && sed_auth('page', 'any', 'A'))
	{
		sed_require('page');
		$sqltmp2 = sed_sql_query("SELECT COUNT(*) FROM $db_pages WHERE page_state=1");
		$sys['pagesqueued'] = sed_sql_result($sqltmp2, 0, 'COUNT(*)');

		if ($sys['pagesqueued'] > 0)
		{
			$out['notices'] .= $L['hea_valqueues'];

			if ($sys['pagesqueued'] == 1)
			{
				$out['notices'] .= sed_rc_link(sed_url('admin', 'm=page'), '1 ' . $L['Page']);
			}
			elseif ($sys['pagesqueued'] > 1)
			{
				$out['notices'] .= sed_rc_link(sed_url('admin', 'm=page'), $sys['pagesqueued'] . ' ' . $L['Pages']);
			}
		}
	}
	elseif ($usr['id'] > 0 && $cfg['module']['page'] && !$cfg['disable_page'] && sed_auth('page', 'any', 'W'))
	{
		sed_require('page');
		$sqltmp2 = sed_sql_query("SELECT COUNT(*) FROM $db_pages WHERE page_state=1 AND page_ownerid = " . $usr['id']);
		$sys['pagesqueued'] = sed_sql_result($sqltmp2, 0, 'COUNT(*)');

		if ($sys['pagesqueued'] > 0)
		{
			$out['notices'] .= $L['hea_valqueues'];

			if ($sys['pagesqueued'] == 1)
			{
				$out['notices'] .= sed_rc_link(sed_url('list', 'c=unvalidated'), '1 ' . $L['Page']);
			}
			elseif ($sys['pagesqueued'] > 1)
			{
				$out['notices'] .= sed_rc_link(sed_url('list', 'c=unvalidated'), $sys['pagesqueued'] . ' ' . $L['Pages']);
			}
		}
	}

	/* === Hook === */
	foreach (sed_getextplugins('header.main') as $pl)
	{
		include $pl;
	}
	/* ===== */

	$mskin = sed_skinfile($cfg['enablecustomhf'] ? array('header', mb_strtolower($location)) : 'header', '+', defined('SED_ADMIN'));
	$t = new XTemplate($mskin);

	$t->assign(array(
		'HEADER_TITLE' => $plug_title . $out['fulltitle'],
		'HEADER_DOCTYPE' => $cfg['doctype'],
		'HEADER_CSS' => $cfg['css'],
		'HEADER_COMPOPUP' => $out['compopup'],
		'HEADER_LOGSTATUS' => $out['logstatus'],
		'HEADER_WHOSONLINE' => $out['whosonline'],
		'HEADER_TOPLINE' => $cfg['topline'],
		'HEADER_BANNER' => $cfg['banner'],
		'HEADER_GMTTIME' => $usr['gmttime'],
		'HEADER_USERLIST' => $out['userlist'],
		'HEADER_NOTICES' => $out['notices'],
		'HEADER_BASEHREF' => $out['basehref'],
		'HEADER_META_CONTENTTYPE' => $out['meta_contenttype'],
		'HEADER_META_CHARSET' => $out['meta_charset'],
		'HEADER_META_DESCRIPTION' => $out['meta_desc'],
		'HEADER_META_KEYWORDS' => $out['meta_keywords'],
		'HEADER_META_LASTMODIFIED' => $out['meta_lastmod'],
		'HEADER_HEAD' => $out['head_head']
	));

	/* === Hook === */
	foreach (sed_getextplugins('header.body') as $pl)
	{
		include $pl;
	}
	/* ===== */

	if ($usr['id'] > 0)
	{
		$out['adminpanel'] = (sed_auth('admin', 'any', 'R')) ? sed_rc_link(sed_url('admin'), $L['Administration']) : '';
		$out['loginout_url'] = sed_url('users', 'm=logout&' . sed_xg());
		$out['loginout'] = sed_rc_link($out['loginout_url'], $L['Logout']);
		$out['profile'] = sed_rc_link(sed_url('users', 'm=profile'), $L['Profile']);
		$out['pms'] = ($cfg['disable_pm']) ? '' : sed_rc_link(sed_url('pm'), $L['Private_Messages']);
		$out['pfs'] = ($cfg['disable_pfs'] || !sed_auth('pfs', 'a', 'R') || $sed_groups[$usr['maingrp']]['pfs_maxtotal'] == 0 || $sed_groups[$usr['maingrp']]['pfs_maxfile'] == 0) ? '' : sed_rc_link(sed_url('pfs'), $L['Mypfs']);

		if ($cfg['module']['pm'] && !$cfg['disable_pm'])
		{
			sed_require('pm');
			if ($usr['newpm'])
			{
				$sqlpm = sed_sql_query("SELECT COUNT(*) FROM $db_pm WHERE pm_touserid='".$usr['id']."' AND pm_tostate=0");
				$usr['messages'] = sed_sql_result($sqlpm, 0, 'COUNT(*)');
			}
			$out['pmreminder'] = sed_rc_link(sed_url('pm'),
				($usr['messages'] > 0) ? sed_declension($usr['messages'], $Ls['Privatemessages']) : $L['hea_noprivatemessages']
			);
		}

		$t->assign(array(
			'HEADER_USER_NAME' => $usr['name'],
			'HEADER_USER_ADMINPANEL' => $out['adminpanel'],
			'HEADER_USER_LOGINOUT' => $out['loginout'],
			'HEADER_USER_PROFILE' => $out['profile'],
			'HEADER_USER_PMS' => $out['pms'],
			'HEADER_USER_PFS' => $out['pfs'],
			'HEADER_USER_PMREMINDER' => $out['pmreminder'],
			'HEADER_USER_MESSAGES' => $usr['messages']
		));

		$t->parse('HEADER.USER');
	}
	else
	{
		$out['guest_username'] = $R['form_guest_username'];
		$out['guest_password'] = $R['form_guest_password'];
		$out['guest_register'] = sed_rc_link(sed_url('users', 'm=register'), $L['Register']);
		$out['guest_cookiettl'] = $cfg['forcerememberme'] ? $R['form_guest_remember_forced']
			: $R['form_guest_remember'];

		$t->assign(array (
			'HEADER_GUEST_SEND' => sed_url('users', 'm=auth&a=check&' . $sys['url_redirect']),
			'HEADER_GUEST_USERNAME' => $out['guest_username'],
			'HEADER_GUEST_PASSWORD' => $out['guest_password'],
			'HEADER_GUEST_REGISTER' => $out['guest_register'],
			'HEADER_GUEST_COOKIETTL' => $out['guest_cookiettl']
		));

		$t->parse('HEADER.GUEST');
	}

	/* === Hook === */
	foreach (sed_getextplugins('header.tags') as $pl)
	{
		include $pl;
	}
	/* ===== */

	$t->parse('HEADER');
	$t->out('HEADER');
}
?>