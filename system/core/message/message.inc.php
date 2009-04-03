<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
==================== */

/**
 * Error message display and redirect
 *
 * @package Cotonti
 * @version 0.0.3
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) 2008 Cotonti Team
 * @license BSD License
 */

if (!defined('SED_CODE')) { die('Wrong URL.'); }

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('message', 'a');
sed_block($usr['auth_read']);

$msg = sed_import('msg','G','INT');
$num = sed_import('num','G','INT');
$rc = sed_import('rc','G','INT');
$redirect = sed_import('redirect','G','SLU');

require_once($cfg['system_dir']."/lang/en/message.lang.php");
if ($lang!='en')
require_once($cfg['system_dir']."/lang/$lang/message.lang.php");

unset ($r, $rd, $ru);

$title = $L['msg'.$msg.'_title'];
$body = $L['msg'.$msg.'_body'];

/* === Hook === */
$extp = sed_getextplugins('message.first');
if (is_array($extp))
{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

switch( $msg )
{

	/* ======== Users ======== */

	case '100':

		$rd = 2;
		$the_redirect = (!empty($redirect)) ? "&redirect=".$redirect : '';
		$ru = sed_url('users', 'm=auth'.$redirect);
		break;

	/*case '102':
		$r = 1;
		$rd = 2;
		$ru = sed_url('index');
		break;*/

	/*case '104':
		$rd = 2;
		$ru = (empty($redirect)) ? sed_url('index') : base64_decode($redirect);
		break;*/

	/*case '113':
		$rd = 2;
		$ru = sed_url('users', 'm=profile');
		break;*/

	case '153':
		if ($num>0)
		{ $body .= "<br />(-> ".date($cfg['dateformat'],$num)."GMT".")"; }
		break;

		/* ======== Error Pages ========= */

	case '400':
		$rd = 5;
		$ru = (empty($redirect)) ? sed_url('index') : base64_decode($redirect);
		break;

	case '401':
		$rd = 5;
		$ru = (empty($redirect)) ? sed_url('index') : base64_decode($redirect);
		break;

	case '403':
		$rd = 5;
		$ru = (empty($redirect)) ? sed_url('index') : base64_decode($redirect);
		break;

	case '404':
		$rd = 5;
		$ru = (empty($redirect)) ? sed_url('index') : base64_decode($redirect);
		break;

	case '500':
		$rd = 5;
		$ru = (empty($redirect)) ? sed_url('index') : base64_decode($redirect);
		break;

		/* ======== Private messages ======== */

	/*case '502':
		$body = $L['msg502_body']."<a href=\"".sed_url('pm')."\">".$L['msg502_body2']."</a>".$L['msg502_body3'];
		$rd = 2;
		$ru = sed_url('pm');
		break;*/


	case '916':
		$rd = 2;
		$ru = sed_url('admin');
		break;

	case '930':
		if ($usr['id']==0)
		{
			$rd = 2;
			$the_redirect = (!empty($redirect)) ? "&redirect=".$redirect : '';
			$ru = sed_url('users', 'm=auth'.$the_redirect);
		}
		break;
}

/* ============= */
if(empty($title) || empty($body))
{
	$title = $L['msg950_title'];
	$body = $L['msg950_body'];
	unset($rc, $rd);
}
if(empty($rc) && empty($rd))
{
	$rd = '5';
	$ru = sed_url('index');
}

if($rc!='')
{
	$r['100'] = sed_url('admin', "m=plug");
	$r['101'] = sed_url('admin', "m=hitsperday");
	$r['102'] = sed_url('admin', "m=polls");
	$r['103'] = sed_url('admin', "m=forums");
	$r['200'] = sed_url('users');

	if(!strstr($r["$rc"], '://'))
	{
		$r["$rc"] = $cfg['mainurl'] . '/' . $r["$rc"];
	}

	$plug_head .= "<meta http-equiv=\"refresh\" content=\"2;url=".$r["$rc"]."\" /><br />";
	$body .= "<br />&nbsp;<br />".$L['msgredir'];
}

elseif ($rd!='')
{
	if(!strstr($ru, '://'))
	{
		$ru = $cfg['mainurl'] . '/' . $ru;
	}
	$plug_head .= "<meta http-equiv=\"refresh\" content=\"".$rd.";url=".$ru."\" />";
	$body .= "<br />&nbsp;<br />".$L['msgredir'];
}

/* === Hook === */
$extp = sed_getextplugins('message.main');
if (is_array($extp))
{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

$plug_head .= '<meta name="robots" content="noindex" />';
$plug_title = $title." - ";
require_once $cfg['system_dir'] . '/header.php';
$t = new XTemplate(sed_skinfile('message'));

$errmsg = $title;
$title .= ($usr['isadmin']) ? " (#".$msg.")" : '';

$t->assign("MESSAGE_TITLE", $title);
$t->assign("MESSAGE_BODY", $body);

/* === Hook === */
$extp = sed_getextplugins('message.tags');
if (is_array($extp))
{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

$t->parse("MAIN");
$t->out("MAIN");

require_once $cfg['system_dir'] . '/footer.php';

?>
