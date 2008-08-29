<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
[BEGIN_SED]
File=message.php
Version=122
Updated=2007-sep-26
Type=Core
Author=Neocrome
Description=Messages
[END_SED]
==================== */

if (!defined('SED_CODE')) { die('Wrong URL.'); }

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('message', 'a');
sed_block($usr['auth_read']);

$msg = sed_import('msg','G','INT');
$num = sed_import('num','G','INT');
$rc = sed_import('rc','G','INT');
$redirect = sed_import('redirect','G','SLU');

require_once($cfg['system_dir']."/lang/$lang/message.lang.php");

unset ($r, $rd, $ru);

switch( $msg )
{

	/* ======== Users ======== */

	case '100':

		$message = $L['msg100_0'];
		$body = $L['msg100_1'];
		$rd = 2;
		$ru = "users.php?m=auth";
		$ru .= (!empty($redirect)) ? "&redirect=".$redirect : '';

		break;

	case '101':
		$message = $L['msg101_0'];
		$body = $L['msg101_1'];
		break;

	case '102':
		$message = $L['msg102_0'];
		$body = $L['msg102_1'];
		$r = 1;
		$rd = 2;
		$ru = "index.php";
		break;

	case '104':
		$message = $L['msg104_0'];
		$body = $L["msg104_1"];
		$rd = 2;
		$ru = (empty($redirect)) ? "index.php" : base64_decode($redirect);
		break;

	case '105':
		$message = $L['msg105_0'];
		$body = $L['msg105_1'];
		break;

	case '106':
		$message = $L['msg106_0'];
		$body = $L['msg106_1'];
		break;

	case '109':
		$message = $L['msg109_0'];
		$body = $L['msg109_1'];
		break;

	case '110':
		$message = $L['msg110_0'];
		$body = $L['msg110_1'];
		break;

	case '113':
		$message = $L['msg113_0'];
		$body = $L['msg113_1'];
		$rd = 2;
		$ru = "users.php?m=profile";
		break;

	case '117':
		$message = $L['msg117_0'];
		$body = $L['msg117_1'];
		break;

	case '118':
		$message = $L['msg118_0'];
		$body = $L['msg118_1'];
		break;

	case '151':
		$message = $L['msg151_0'];
		$body = $L['msg151_1'];
		break;

	case '152':
		$message = $L['msg152_0'];
		$body = $L['msg152_1'];
		break;

	case '153':
		$message = $L['msg153_0'];
		$body = $L['msg153_1'];
		if ($num>0)
		{ $body .= "<br />(-> ".date($cfg['dateformat'],$num)."GMT".")"; }
		break;

	case '157':
		$message = $L['msg157_0'];
		$body = $L['msg157_1'];
		break;

		/* ======== General ======== */

	case '300':
		$message = $L['msg300_0'];
		$body = $L['msg300_1'];
		break;

		/* ======== Error Pages ========= */

	case '400':
		$message = $L['msg400_0'];
		$body = $L["msg400_1"];
		$rd = 5;
		$ru = (empty($redirect)) ? "index.php" : base64_decode($redirect);
		break;

	case '401':
		$message = $L['msg401_0'];
		$body = $L["msg401_1"];
		$rd = 5;
		$ru = (empty($redirect)) ? "index.php" : base64_decode($redirect);
		break;

	case '403':
		$message = $L['msg403_0'];
		$body = $L["msg403_1"];
		$rd = 5;
		$ru = (empty($redirect)) ? "index.php" : base64_decode($redirect);
		break;

	case '404':
		$message = $L['msg404_0'];
		$body = $L["msg404_1"];
		$rd = 5;
		$ru = (empty($redirect)) ? "index.php" : base64_decode($redirect);
		break;

	case '500':
		$message = $L['msg500_0'];
		$body = $L["msg500_1"];
		$rd = 5;
		$ru = (empty($redirect)) ? "index.php" : base64_decode($redirect);
		break;

		/* ======== Private messages ======== */

	case '502':
		$message = $L['msg502_0'];
		$body = $L['msg502_1']."<a href=\"pm.php\">".$L['msg502_2']."</a>".$L['msg502_3'];
		$rd = 2;
		$ru = "pm.php";
		break;

		/* ======== Private messages ======== */

	case '602':
		$message = $L['msg602_0'];
		$body = $L['msg602_1'];
		break;

	case '603':
		$message = $L['msg603_0'];
		$body = $L['msg603_1'];
		break;

		/* ======== System ======== */

	case '900':
		$message = $L['msg900_0'];
		$body = $L['msg900_1'];
		break;

	case '904':
		$message = $L['msg904_0'];
		$body = $L['msg904_1'];
		break;

	case '907':
		$message = $L['msg907_0'];
		$body = $L['msg907_1'];
		break;

	case '911':
		$message = $L['msg911_0'];
		$body = $L['msg911_1'];
		break;

	case '915':
		$message = $L['msg915_0'];
		$body = $L['msg915_1'];
		break;

	case '916':
		$message = $L['msg916_0'];
		$body = $L["msg916_1"];
		$rd = 2;
		$ru = "admin.php";
		break;

	case '930':
		$message = $L['msg930_0'];
		$body = $L['msg930_1'];
		if ($usr['id']==0)
		{
			$rd = 2;
			$ru = "users.php?m=auth";
			$ru .= (!empty($redirect)) ? "&redirect=".$redirect : '';
		}
		break;

	case '940':
		$message = $L['msg940_0'];
		$body = $L["msg940_1"];
		break;

	case '950':
		$message = $L['msg950_0'];
		$body = $L['msg950_1'];
		break;

		/* ======== Default  ======== */

	default:
		$message = $L['msg950_0'];
		$body = $L['msg950_1'];
		break;
}

/* ============= */

if(empty($rc) && empty($rd))
{
	$rd = '5';
	$ru = 'index.php';
}

if($rc!='')
{
	$r['100'] = "admin.php?m=plug";
	$r['101'] = "admin.php?m=hitsperday";
	$r['102'] = "admin.php?m=polls";
	$r['103'] = "admin.php?m=forums";
	$r['200'] = "users.php";

	$moremetas .= "<meta http-equiv=\"refresh\" content=\"2;url=".$r["$rc"]."\" /><br />";
	$body .= "<br />&nbsp;<br />".$L['msgredir'];
}

elseif ($rd!='')
{
	$moremetas .= "<meta http-equiv=\"refresh\" content=\"".$rd.";url=".$ru."\" />";
	$body .= "<br />&nbsp;<br />".$L['msgredir'];
}

/* === Hook === */
$extp = sed_getextplugins('message.main');
if (is_array($extp))
{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

$moremetas .= '<meta name="robots" content="noindex" />';

require_once $cfg['system_dir'] . '/header.php';
$t = new XTemplate("skins/".$skin."/message.tpl");

$errmsg = $message;
$message .= ($usr['isadmin']) ? " (#".$msg.")" : '';

$t->assign("MESSAGE_TITLE", $message);
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