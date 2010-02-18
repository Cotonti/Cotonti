<?php
/**
 * Error message display and redirect
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD License
 */

defined('SED_CODE') or die('Wrong URL');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('message', 'a');
sed_block($usr['auth_read']);

$msg = sed_import('msg', 'G', 'INT');
$num = sed_import('num', 'G', 'INT');
$rc = sed_import('rc', 'G', 'INT');

require_once(sed_langfile('message', 'core'));

unset($r, $rd, $ru);

$title = $L['msg'.$msg.'_title'];
$body = $L['msg'.$msg.'_body'];

/* === Hook === */
$extp = sed_getextplugins('message.first');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

switch( $msg )
{
	/* ======== Users ======== */

	case '100':
		$rd = 2;
		$ru = sed_url('users', 'm=auth'.(empty($redirect) ? '' : "&redirect=$redirect"));
	break;

	case '102':
		$r = 1;
		$rd = 2;
		$ru = sed_url('index');
	break;

	case '153':
		if ($num > 0)
		{
			$body .= "<br />(-> ".date($cfg['dateformat'], $num)."GMT".")";
		}
	break;

	/* ======== Error Pages ========= */

	case '400':
	case '401':
	case '403':
	case '404':
	case '500':
		$rd = 5;
		$ru = empty($redirect) ? sed_url('index') : str_replace('&', '&amp;', base64_decode($redirect));
	break;

	/* ======== System messages ======== */

	case '916':
		$rd = 2;
		$ru = sed_url('admin');
	break;

	case '930':
		if ($usr['id'] > 0)
		{
			break;
		}
		$rd = 2;
		if (!empty($redirect))
		{
			$uri_redirect = base64_decode($redirect);
			if (mb_strpos($uri_redirect, '&x=') !== false || mb_strpos($uri_redirect, '?x=') !== false)
			{
				$ru = sed_url('index'); // xg, not redirect to form action/GET or to command from GET
				break;
			}
		}
		$ru = sed_url('users', 'm=auth'.(empty($redirect) ? '' : "&redirect=$redirect"));
	break;
}

/* ============= */
if (empty($title) || empty($body))
{
	$title = $L['msg950_title'];
	$body = $L['msg950_body'];
	unset($rc, $rd);
}
if (empty($rc) && empty($rd))
{
	$rd = '5';
	$ru = sed_url('index');
}

switch ($rc)
{
	case '100':
		$r['100'] = sed_url('admin', "m=plug");
	break;

	case '101':
		$r['101'] = sed_url('admin', "m=hitsperday");
	break;

	case '102':
		$r['102'] = sed_url('admin', "m=polls");
	break;

	case '103':
		$r['103'] = sed_url('admin', "m=forums");
	break;

	case '200':
		$r['200'] = sed_url('users');
	break;

	default:
		$rc = '';
	break;
}

if ($rc != '')
{
	if (mb_strpos($r["$rc"], '://') === false)
	{
		$r["$rc"] = SED_ABSOLUTE_URL . $r["$rc"];
	}
	$plug_head .= "<meta http-equiv=\"refresh\" content=\"2;url=".$r["$rc"]."\" /><br />"; // TODO: in resources
	$body .= "<br />&nbsp;<br />".$L['msgredir'];
}

elseif ($rd != '')
{
	if (mb_strpos($ru, '://') === false)
	{
		$ru = SED_ABSOLUTE_URL.ltrim($ru, '/');
	}
	$plug_head .= "<meta http-equiv=\"refresh\" content=\"".$rd.";url=".$ru."\" />"; // TODO: in resources
	$body .= "<br />&nbsp;<br />".$L['msgredir'];
}

/* === Hook === */
$extp = sed_getextplugins('message.main');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

$out['head'] .= $R['code_noindex'];
$out['subtitle'] = $title;
require_once $cfg['system_dir'].'/header.php';
$t = new XTemplate(sed_skinfile('message'));

$errmsg = $title;
$title .= ($usr['isadmin']) ? " (#".$msg.")" : '';

$t->assign("MESSAGE_TITLE", $title);
$t->assign("MESSAGE_BODY", $body);

/* === Hook === */
$extp = sed_getextplugins('message.tags');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

$t->parse("MAIN");
$t->out("MAIN");

require_once $cfg['system_dir'].'/footer.php';

?>