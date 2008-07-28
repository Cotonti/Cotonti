<?PHP
/*
|****		Cotonti Engine					****|
|****		Copyright Cotonti 2008				****|
|****		http://www.cotonti.com/			****|
*/
/*
|****		Security Defines  Check			****|
*/
if (!defined('COTONTI_CORE')) { header("Location: /"); }
/*
|****		File Information					****|
*/
$file['name'] 		= "Common";
$file['path']		= "/system/";
$file['filename']	= "common.php";
$file['version']	= "0.0.1";
$file['updated']	= "04-08-08";
$file['type']		= "core";

set_magic_quotes_runtime(0);
define('MQGPC', get_magic_quotes_gpc());
error_reporting(E_ALL ^ E_NOTICE);

/*
|****		Initialize Database Connection		****|
*/
require('system/database.sql.php');
cot_connect($db_info['host'], $db_info['username'], $db_info['password'], $db_info['database'])
unset($db_info);

/*
|****		Common Variables					****|
*/
$sys['time'] = time();
$sys['url'] = base64_encode($_SERVER['REQUEST_URI']);
/*
|****		Users or Guests Login				****|
*/
$usr['id'] = 0;
$usr['sessionid'] = '';
$usr['name'] = '';
$usr['level'] = 0;
$usr['lastvisit'] = $sys['time'];
$usr['lastlog'] = 0;
$usr['timezone'] = $cfg['defaulttimezone'];
$usr['newpm'] = 0;
$usr['messages'] = 0;
$usr['ip'] = $_SERVER['REMOTE_ADDR'];

if($cfg['authmode']==2 || $cfg['authmode']==3)
	{ session_start(); }

if(isset($_SESSION['cotses_id']) && ($cfg['authmode']==2 || $cfg['authmode']==3))
	{
		$cotses_id = $_SESSION['cotses_id'];
		$cotses_psw = $_SESSION['cotses_psw'];
		$cotses_skin = $_SESSION['cotses_skin'];
		$cotses_lang = $_SESSION['cotses_lang'];
	}
if(isset($_COOKIE['COTONTI']) && ($cfg['authmode']==1 || $cfg['authmode']==3))
	{
		$cot_cookie = base64_decode($_COOKIE['COTONTI']);
		$cot_cookie = explode(':_:', $cot_cookie);
		$cotcookie_id = cot_filter($cot_cookie[0], "INT");
		$cotcookie_psw = cot_filter($cot_cookie[1], "PSW");
		$cotcookie_skin = cot_filter($cot_cookie[2], "ALP");
		$cotcookie_lang = cot_filter($cot_cookie[3], "ALP");
	}
if($cfg['authmode']==3)
	{
		$cotlogin_id = ($cotses_id != $cotcookie_id) ? 0 : $cotses_id;
		$cotlogin_psw = ($cotses_psw != $cotcookie_psw) ? 0 : $cotses_psw;
		if(!empty($cotses_skin))
			{ $cotlogin_skin = $cotses_skin; }
		elseif(!empty($cotcookie_skin))
			{ $cotlogin_skin = $cotcookie_skin; }
		else{ $cotlogin_skin = $cfg['defaultskin']; }
		if(!empty($cotses_lang))
			{ $cotlogin_lang = $cotses_lang; }
		elseif(!empty($cotcookie_lang))
			{ $cotlogin_lang = $cotcookie_lang; }
		else{ $cotlogin_lang = $cfg['defaultlang']; }
		$cotlogin_sid = session_id();
	}
elseif($cfg['authmode']==2)
	{
		$cotlogin_id = $cotses_id;
		$cotlogin_psw = $cotses_psw;
		$cotlogin_skin = $cotses_skin;
		$cotlogin_lang = $cotses_lang;
		$cotlogin_sid = session_id();
	}
elseif($cfg['authmode']==1)
	{
		$cotlogin_id = $cotcookie_id;
		$cotlogin_psw = $cotcookie_psw;
		$cotlogin_skin = $cotcookie_skin;
		$cotlogin_lang = $cotcookie_lang;
	}
if($cotlogin_id>0 && $cfg['authmode']>0)
	{
		if(strlen($cotlogin_psw)!=32)
			{ cot_die($L['cot_login_wrongpsw'], "fatal"); }
		if(!empty($cotlogin_sid))
			{ $sqllogin_sid = " AND `cot_sid`='".$cotlogin_sid."'"; }
		if($cfg['ipcheck'])
			{ $sql_login = cot_sql_query("SELECT * FROM `".$db['users']."` WHERE `user_id`='".$cotlogin_id."' AND `user_password`='".$cotlogin_psw."' AND `user_lastip`='".$usr['ip']."'".$sqllogin_sid); }
		else{ $sql_login = cot_sql_query("SELECT * FROM `".$db['users']."` WHERE `user_id`='".$cotlogin_id."' AND `user_password`='".$cotlogin_psw."'".$sqllogin_sid); }
		if ($fa_login = cot_sql_fetcharray($sql_login))
			{
				if ($fa_login['user_maingrp']>3)
					{
						$usr['id'] = $cotlogin_id;
						$usr['sessionid'] = ($cfg['authmode']==1) ? md5($fa_login['user_lastvisit']) : $cotlogin_sid;
						$usr['name'] = $fa_login['user_name'];
						//$usr['maingrp'] = $fa_login['user_maingrp'];
						$usr['lastvisit'] = $fa_login['user_lastvisit'];
						$usr['timezone'] = $fa_login['user_timezone'];
						$usr['skin'] = ($cfg['forcedefaultskin']) ? $cfg['defaultskin'] : $cotlogin_skin;
						$usr['lang'] = ($cfg['forcedefaultlang']) ? $cfg['defaultlang'] : $cotlogin_lang;
						$usr['newpm'] = $fa_login['user_newpm'];
						$usr['auth'] = unserialize($fa_login['user_auth']);
						if ($usr['lastvisit']+$cfg['timedout'] < $sys['time'])
							{ $sql_update_lastvisit = " , `user_lastvisit`='".$sys['time']."'"; }
						if (empty($fa_login['user_auth']))
							{
								$usr['auth'] = cot_auth_build($usr['id'], $usr['maingrp']);
								$sql_update_auth = " , `user_auth`='".serialize($usr['auth'])."'";
							}
						cot_sql_query("UPDATE `".$db['users']."` SET `user_lastlog`='".$sys['time']."', `user_lastip`='".$usr['ip']."', `user_sid`='".$usr['sessionid']."'".$sql_update_lastvisit."".$sql_update_auth." WHERE `user_id`='".$usr['id']."'");
					}
			}
	}
else{
		if(empty($cotlogin_skin) && ($cfg['authmode']==1 || $cfg['authmode']==3))
			{
				$cot_cookie = base64_encode("0:_:0:_:".$cfg['defaultskin'].":_:".$cfg['defaultlang']);
				setcookie('COTONTI', $cot_cookie, $sys['now']+($cfg['cookielifetime']*86400), $cfg['cookiepath'], $cfg['cookiedomain']);
			}
		else{
				$usr['skin'] = ($cfg['forcedefaultskin']) ? $cfg['defaultskin'] : $cotlogin_skin;
				$usr['auth'] = cot_auth_build(0);
				$usr['lang'] = ($cfg['forcedefaultlang']) ? $cfg['defaultlang'] : $cotlogin_lang;
			}
	}
?>