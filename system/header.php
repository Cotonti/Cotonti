<?php
/**
 * Global header
 *
 * @package Cotonti
 * @version 0.9.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2011
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

cot_uriredir_store();

/* === Hook === */
foreach (cot_getextplugins('header.first') as $pl)
{
	include $pl;
}
/* ===== */

cot_online_update();

$out['logstatus'] = ($usr['id'] > 0) ? $L['hea_youareloggedas'].' '.$usr['name'] : $L['hea_youarenotlogged'];
$out['userlist'] = (cot_auth('users', 'a', 'R')) ? cot_rc_link(cot_url('users'), $L['Users']) : '';

unset($title_tags, $title_data);

if (is_int($pg) && $pg > 1)
{
	// Append page number to subtitle
	$out['subtitle'] .= cot_rc('code_title_page_num', array('num' => $pg));
}

$title_params = array(
	'MAINTITLE' => $cfg['maintitle'],
	'DESCRIPTION' => $cfg['subtitle'],
	'SUBTITLE' => $out['subtitle']
);
if (defined('COT_INDEX'))
{
	$out['fulltitle'] = cot_title('title_header_index', $title_params);
}
else
{
	$out['fulltitle'] = cot_title('title_header', $title_params);
}

$out['meta_contenttype'] = $cfg['xmlclient'] ? 'application/xml' : 'text/html';
$out['basehref'] = $R['code_basehref'];
$out['meta_charset'] = 'UTF-8';
$out['meta_desc'] = htmlspecialchars($out['desc']);
$out['meta_keywords'] = empty($out['keywords']) ? $cfg['metakeywords'] : htmlspecialchars($out['keywords']);
$out['meta_lastmod'] = gmdate('D, d M Y H:i:s');
$out['head_head'] .= $out['head'];

if ($cfg['jquery'] && $cfg['jquery_cdn'])
{
	cot_rc_link_file('https://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js', true);
}
cot_rc_output();

if (isset($env['status']))
{
	cot_sendheaders($out['meta_contenttype'], $env['status']);
}
else
{
	cot_sendheaders($out['meta_contenttype']);
}

if (!COT_AJAX)
{
	$mtpl_type = defined('COT_ADMIN') || defined('COT_MESSAGE') && $_SESSION['s_run_admin'] && cot_auth('admin', 'any', 'R') ? 'core' : 'module';
	if ($cfg['enablecustomhf'])
	{
		$mtpl_base = (defined('COT_PLUG') && !empty($e)) ? array('header', $e) : array('header', $env['location']);
	}
	else
	{
		$mtpl_base = 'header';
	}
	$t = new XTemplate(cot_tplfile($mtpl_base, $mtpl_type));	
	
	/* === Hook === */
	foreach (cot_getextplugins('header.main') as $pl)
	{
		include $pl;
	}
	/* ===== */

	$t->assign(array(
		'HEADER_TITLE' => $plug_title . $out['fulltitle'],
		'HEADER_DOCTYPE' => $cfg['doctype'],
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
		'HEADER_HEAD' => $out['head_head'],
		'HEADER_CANONICAL_URL' => str_replace('&', '&amp;', $sys['canonical_uri'])
	));

	/* === Hook === */
	foreach (cot_getextplugins('header.body') as $pl)
	{
		include $pl;
	}
	/* ===== */

	if ($usr['id'] > 0)
	{
		$out['adminpanel'] = (cot_auth('admin', 'any', 'R')) ? cot_rc_link(cot_url('admin'), $L['Administration']) : '';
		$out['loginout_url'] = cot_url('users', 'm=logout&' . cot_xg());
		$out['loginout'] = cot_rc_link($out['loginout_url'], $L['Logout']);
		$out['profile'] = cot_rc_link(cot_url('users', 'm=profile'), $L['Profile']);

		$t->assign(array(
			'HEADER_USER_NAME' => $usr['name'],
			'HEADER_USER_ADMINPANEL' => $out['adminpanel'],
			'HEADER_USER_ADMINPANEL_URL' => cot_url('admin'),
			'HEADER_USER_LOGINOUT' => $out['loginout'],
			'HEADER_USER_LOGINOUT_URL' => $out['loginout_url'],
			'HEADER_USER_PROFILE' => $out['profile'],
			'HEADER_USER_PROFILE_URL' => cot_url('users', 'm=profile'),
			'HEADER_USER_MESSAGES' => $usr['messages']
		));

		/* === Hook === */
		foreach (cot_getextplugins('header.user.tags') as $pl)
		{
			include $pl;
		}
		/* ===== */

		$t->parse('HEADER.USER');
	}
	else
	{
		$out['guest_username'] = $R['form_guest_username'];
		$out['guest_password'] = $R['form_guest_password'];
		$out['guest_register'] = cot_rc_link(cot_url('users', 'm=register'), $L['Register']);
		$out['guest_cookiettl'] = $cfg['forcerememberme'] ? $R['form_guest_remember_forced']
			: $R['form_guest_remember'];

		$t->assign(array (
			'HEADER_GUEST_SEND' => cot_url('users', 'm=auth&a=check&' . $sys['url_redirect']),
			'HEADER_GUEST_USERNAME' => $out['guest_username'],
			'HEADER_GUEST_PASSWORD' => $out['guest_password'],
			'HEADER_GUEST_REGISTER' => $out['guest_register'],
			'HEADER_GUEST_REGISTER_URL' => cot_url('users', 'm=register'),
			'HEADER_GUEST_COOKIETTL' => $out['guest_cookiettl']
		));

		/* === Hook === */
		foreach (cot_getextplugins('header.guest.tags') as $pl)
		{
			include $pl;
		}
		/* ===== */

		$t->parse('HEADER.GUEST');
	}

	/* === Hook === */
	foreach (cot_getextplugins('header.tags') as $pl)
	{
		include $pl;
	}
	/* ===== */
	
	$t->parse('HEADER');
	$t->out('HEADER');
}
?>