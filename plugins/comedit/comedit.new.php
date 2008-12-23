<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net

[BEGIN_SED]
File=plugins/comedit/comedit.new.php
Version=120
Updated=2007-mar-01
Type=Plugin
Author=Neocrome
Description=
[END_SED]

[BEGIN_SED_EXTPLUGIN]
Code=comedit
Part=new
File=comments.new
Hooks=comments.send.new
Tags=
Minlevel=0
Order=10
[END_SED_EXTPLUGIN]

==================== */

if (!defined('SED_CODE')) { die('Wrong URL.'); }

if (@file_exists($cfg['plugins_dir'].'/comedit/lang/comedit.'.$usr['lang'].'.lang.php')){
  require($cfg['plugins_dir'].'/comedit/lang/comedit.'.$usr['lang'].'.lang.php');
}else{
  require($cfg['plugins_dir'].'/comedit/lang/comedit.en.lang.php');
}

if (empty($error_string) && $cfg['plugin']['comedit']['mail']) {

	$newcomm = sed_sql_insertid($sql);

	$sql = sed_sql_query("SELECT * FROM $db_users WHERE user_maingrp=5");

	$email_title = $L['plu_comlive'].$cfg['main_url'];
	$email_body  = $L['User']." ".$usr['name'].", ".$L['plu_comlive2'];
	$email_body .= $cfg['mainurl']."/".$url."&comments=1#c".$newcomm."\n\n";

	while ($adm = sed_sql_fetcharray($sql)) {
	sed_mail($adm['user_email'], $email_title, $email_body);
	}

}
?>