<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net

[BEGIN_SED]
File=plugins/comedit/comedit.php
Version=120
Updated=2007-mar-01
Type=Plugin
Author=Neocrome
Description=
[END_SED]

[BEGIN_SED_EXTPLUGIN]
Code=comedit
Part=edit
File=comedit
Hooks=standalone
Tags=
Minlevel=0
Order=10
[END_SED_EXTPLUGIN]

==================== */

if (!defined('SED_CODE')) { die('Wrong URL.'); }

$m   = sed_import('m',   'G', 'ALP');
$a   = sed_import('a',   'G', 'ALP');
$cid = sed_import('cid', 'G', 'ALP');
$pid = sed_import('pid', 'G', 'ALP');



$plugin_title = $L['plu_title'];

if ($a=='update') {

	sed_check_xg();

	$sql1 = sed_sql_query("SELECT * FROM $db_com WHERE com_id='$cid' AND com_code='$pid' LIMIT 1");
	sed_die(sed_sql_numrows($sql1)==0);
	$row = sed_sql_fetcharray($sql1);

	$time_limit = ($sys['now_offset']<($row['com_date']+$cfg['plugin']['comments']['time']*60)) ? TRUE : FALSE;
	$usr['isowner'] = ($row['com_authorid'] == $usr['id'] && $time_limit);
	$usr['allow_write'] = ($usr['isadmin'] || $usr['isowner']);
	sed_block($usr['allow_write']);

	$comtext = sed_import('comtext', 'P', 'TXT');

    $error_string .= (empty($comtext)) ? $L['plu_comtooshort']."<br />" : '';

    if (empty($error_string)) {
	$sql = sed_sql_query("UPDATE $db_com SET com_text = '".sed_sql_prep($comtext)."' WHERE com_id='$cid' AND  com_code='$pid'");

		if($cfg['plugin']['comedit']['mail']) {
		$sql2 = sed_sql_query("SELECT * FROM $db_users WHERE user_maingrp=5");

		$email_title = $L['plu_comlive'].$cfg['main_url'];
		$email_body  = $L['User']." ".$usr['name'].", ".$L['plu_comlive3'];
		$email_body .= $cfg['mainurl'].'/'.sed_url('page', 'id='.substr($pid, 1).'&comments=1', '#c'.$cid)."\n\n";

			while ($adm = sed_sql_fetcharray($sql2)) {
			sed_mail($adm['user_email'], $email_title, $email_body);
			}
		}

	$com_grp = ($usr['isadmin']) ? "adm" : "usr";
	sed_log("Edited comment #".$cid, $com_grp);
	header('Location: ' . SED_ABSOLUTE_URL . sed_url('page', 'id='.substr($pid, 1).'&comments=1', '#c'.$cid, true));
	exit;
	}
}


$sql = sed_sql_query("SELECT * FROM $db_com WHERE com_id='$cid' AND com_code='$pid' LIMIT 1");
sed_die(sed_sql_numrows($sql)==0);
$com = sed_sql_fetcharray($sql);

$com_limit = ($sys['now_offset']<($com['com_date']+$cfg['plugin']['comments']['time']*60)) ? TRUE : FALSE;
$usr['isowner'] = ($com['com_authorid'] == $usr['id'] && $com_limit);

$usr['allow_write'] = ($usr['isadmin'] || $usr['isowner']);
sed_block($usr['allow_write']);

$com_date = @date($cfg['dateformat'], $com['com_date'] + $usr['timezone'] * 3600);
$bbcodes = ($cfg['parsebbcodecom']) ? sed_build_bbcodes("comedit", "comtext", $L['BBcodes']) : '';
$smilies = ($cfg['parsesmiliescom']) ? sed_build_smilies("comedit", "comtext", $L['Smilies']) : '';
$pfs = ($usr['id']>0) ? sed_build_pfs($usr['id'], "comedit", "comtext", $L['Mypfs']) : '';
$pfs .= (sed_auth('pfs', 'a', 'A')) ? " &nbsp; ".sed_build_pfs(0, "comedit", "comtext", $L['SFS']) : '';

$plugin_body .="<table class=\"cells\"><form id=\"comedit\" name=\"comedit\" action=\"".sed_url('plug', "e=comedit&amp;m=edit&amp;pid=".$com['com_code']."&amp;cid=".$com['com_id']."&amp;a=update&amp;".sed_xg())."\" method=\"POST\">";
$plugin_body .="<tr><td>".$L['Poster'].":</td><td>".$com['com_author']."</td></tr>";
$plugin_body .="<tr><td>".$L['Ip'].":</td><td>".$com['com_authorip']."</td></tr>";
$plugin_body .="<tr><td>".$L['Date'].":</td><td>".$com_date."</td></tr>";
$plugin_body .="<tr><td colspan=\"2\"><textarea rows=\"7\" cols=\"40\" style=\"width:99%\" id=\"comtext\" name=\"comtext\">".sed_cc($com['com_text'])."</textarea><br />".$bbcodes." ".$smilies." ".$pfs."</td></tr>";
$plugin_body .="<tr><td colspan=\"2\" class=\"valid\"><input type=\"submit\" class=\"submit\" value=\"".$L['Update']."\"></td></tr>";
$plugin_body .="</form></table>";


?>