<?PHP
/* ====================
[BEGIN_SED_EXTPLUGIN]
Code=passrecover
Part=main
File=passrecover
Hooks=standalone
Tags=
Order=10
[END_SED_EXTPLUGIN]
==================== */

/**
 * Sends emails to users so they can recovery their passwords
 *
 * @package Cotonti
 * @version 0.0.3
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

defined('SED_CODE') && defined('SED_PLUG') or die('Wrong URL');

$a = sed_import('a','G','TXT');
$v = sed_import('v','G','TXT');
$email = sed_import('email','P','TXT');

$plugin_title = $L['plu_title'];
$t->assign(array('PASSRECOVER_TITLE'=> $plugin_title));

/**
*Random password generator for password recovery plugin
*@return string and numbers ($pass)
*/

function sed_randompass()
{
	$abc = "abcdefghijklmnoprstuvyz";
	$vars = $abc.strtoupper($abc)."0123456789";
	srand((double)microtime()*1000000);
	$i = 0;
	while($i <= 7)
	{
		$num = rand() % 33;
		$tmp = substr($vars, $num, 1);
		$pass = $pass . $tmp;
		$i++;
	}
	return $pass;
}

if($a == 'request' && $email != '')
{
	sed_shield_protect();
	$sql = sed_sql_query("SELECT user_id, user_name, user_lostpass FROM $db_users WHERE user_email='".sed_sql_prep($email)."' ORDER BY user_id ASC LIMIT 1");

	if($row = sed_sql_fetcharray($sql))
	{
		$rusername = $row['user_name'];
		$ruserid = $row['user_id'];
		$validationkey = $row['user_lostpass'];

		if(empty($validationkey) || $validationkey == "0")
		{
			$validationkey = md5(microtime());
			$sql = sed_sql_query("UPDATE $db_users SET user_lostpass='$validationkey', user_lastip='".$usr['ip']."' WHERE user_id='$ruserid'");

		}

		sed_shield_update(60,"Password recovery email sent");

		$rinfo = sprintf($L['plu_email1b'], $usr['ip'], date("Y-m-d H:i"));

		$rsubject = $cfg['maintitle']." - ".$L['plu_title'];
		$ractivate = $cfg['mainurl'].'/'.sed_url('plug', 'e=passrecover&a=auth&v='.$validationkey, '', true);
		$rbody = $L['Hi']." ".$rusername.",\n\n".$L['plu_email1']."\n\n".$ractivate. "\n\n".$rinfo. "\n\n ".$L['aut_contactadmin'];
		sed_mail ($email, $rsubject, $rbody);
		$t->parse('MAIN.REQUEST');
	}
	else
	{
		sed_shield_update(10,"Password recovery requested");

		sed_log("Pass recovery failed, user : ".$rusername);
		header("Location: ".SED_ABSOLUTE_URL.sed_url('message', 'msg=151', '', true));
		exit;
	}
}
elseif($a == 'auth' && mb_strlen($v) == 32)
{
	sed_shield_protect();

	$sql = sed_sql_query("SELECT user_name, user_id, user_email, user_password, user_maingrp, user_banexpire FROM $db_users WHERE user_lostpass='".sed_sql_prep($v)."'");

	if($row = sed_sql_fetcharray($sql))
	{
		$rmdpass  = $row['user_password'];
		$rusername = $row['user_name'];
		$ruserid = $row['user_id'];
		$rusermail = $row['user_email'];

		if($row['user_maingrp'] == 2)
		{
			sed_log("Password recovery failed, user inactive : ".$rusername);
			header("Location: " . SED_ABSOLUTE_URL . sed_url('message', 'msg=152', '', true));
			exit;
		}

		if($row['user_maingrp'] == 3)
		{
			sed_log("Password recovery failed, user banned : ".$rusername);
			header("Location: " . SED_ABSOLUTE_URL . sed_url('message', 'msg=153&num='.$row['user_banexpire'], '', true));
			exit;
		}

		$validationkey = md5(microtime());
		$newpass = sed_randompass();
		$sql = sed_sql_query("UPDATE $db_users SET user_password='".md5($newpass)."', user_lostpass='$validationkey' WHERE user_id='$ruserid'");

		$rsubject = $cfg['maintitle']." - ".$L['plu_title'];
		$rbody = $L['Hi']." ".$rusername.",\n\n".$L['plu_email2']."\n\n".$newpass. "\n\n".$L['aut_contactadmin'];
		sed_mail($rusermail, $rsubject, $rbody);
		$t->parse('MAIN.AUTH');
	}
	else
	{
		sed_shield_update(7,"Log in");
		sed_log("Pass recovery failed, user : ".$rusername);
		header("Location: ".SED_ABSOLUTE_URL.sed_url('message', 'msg=151', '', true));
		exit;
	}
}
else
{
	$t->assign(array('PASSRECOVER_URL_FORM'=> sed_url('plug', 'e=passrecover&a=request')));
	$t->parse('MAIN.PASSRECOVER');
}

?>