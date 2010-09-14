<?php
/**
 * System messages and redirect proxy
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

define('COT_CODE', TRUE);
define('COT_MESSAGE', TRUE);
define('COT_CORE', TRUE);
$location = 'Messages';
$z = 'message';

require_once './datas/config.php';
require_once $cfg['system_dir'].'/functions.php';
require_once $cfg['system_dir'] . '/common.php';
cot_require_api('cotemplate');

require_once cot_langfile('message', 'core');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('message', 'a');
cot_block($usr['auth_read']);

$msg = cot_import('msg', 'G', 'INT');
$num = cot_import('num', 'G', 'INT');
$rc = cot_import('rc', 'G', 'INT');

unset($r, $rd, $ru);

$title = $L['msg'.$msg.'_title'];
$body = $L['msg'.$msg.'_body'];

/* === Hook === */
foreach (cot_getextplugins('message.first') as $pl)
{
	include $pl;
}
/* ===== */

switch( $msg )
{
	/* ======== Users ======== */

	case '100':
		$rd = 2;
		$ru = cot_url('users', 'm=auth'.(empty($redirect) ? '' : "&redirect=$redirect"));
	break;

	case '102':
		$r = 1;
		$rd = 2;
		$ru = cot_url('index');
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
		$ru = empty($redirect) ? cot_url('index') : str_replace('&', '&amp;', base64_decode($redirect));
	break;

	/* ======== System messages ======== */

	case '916':
		$rd = 2;
		$ru = cot_url('admin');
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
				$ru = cot_url('index'); // xg, not redirect to form action/GET or to command from GET
				break;
			}
		}
		$ru = cot_url('users', 'm=auth'.(empty($redirect) ? '' : "&redirect=$redirect"));
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
	$ru = cot_url('index');
}

switch ($rc)
{
	case '100':
		$r['100'] = cot_url('admin', "m=plug");
	break;

	case '101':
		$r['101'] = cot_url('admin', "m=hitsperday");
	break;

	case '102':
		$r['102'] = cot_url('admin', "m=polls");
	break;

	case '103':
		$r['103'] = cot_url('admin', "m=forums");
	break;

	case '200':
		$r['200'] = cot_url('users');
	break;

	default:
		$rc = '';
	break;
}

if ($rc != '')
{
	if (mb_strpos($r["$rc"], '://') === false)
	{
		$r["$rc"] = COT_ABSOLUTE_URL . $r["$rc"];
	}
	$plug_head .= "<meta http-equiv=\"refresh\" content=\"2;url=".$r["$rc"]."\" /><br />"; // TODO: in resources
	$body .= "<br />".$L['msgredir'];
}

elseif ($rd != '')
{
	if (mb_strpos($ru, '://') === false)
	{
		$ru = COT_ABSOLUTE_URL.ltrim($ru, '/');
	}
	$plug_head .= "<meta http-equiv=\"refresh\" content=\"".$rd.";url=".$ru."\" />"; // TODO: in resources
	$body .= "<br />".$L['msgredir'];
}

/* === Hook === */
foreach (cot_getextplugins('message.main') as $pl)
{
	include $pl;
}
/* ===== */

$out['head'] .= $R['code_noindex'];
$out['subtitle'] = $title;
require_once $cfg['system_dir'].'/header.php';
$t = new XTemplate(cot_skinfile('message'));

$errmsg = $title;
$title .= ($usr['isadmin']) ? ' (#'.$msg.')' : '';

$t->assign('MESSAGE_TITLE', $title);
$t->assign('MESSAGE_BODY', $body);

/* === Hook === */
foreach (cot_getextplugins('message.tags') as $pl)
{
	include $pl;
}
/* ===== */

$t->parse('MAIN');
$t->out('MAIN');

require_once $cfg['system_dir'].'/footer.php';

?>