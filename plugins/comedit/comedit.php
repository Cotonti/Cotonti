<?PHP
/* ====================
[BEGIN_SED_EXTPLUGIN]
Code=comedit
Part=edit
File=comedit
Hooks=standalone
Tags=
Order=10
[END_SED_EXTPLUGIN]
==================== */

/**
 * Comedit plug
 *
 * @package Cotonti
 * @version 0.0.3
 * @author Asmo (Edited by motor2hg), Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

defined('SED_CODE') && defined('SED_PLUG') or die('Wrong URL');

$m	 = sed_import('m', 'G', 'ALP');
$a	 = sed_import('a', 'G', 'ALP');
$cid = (int) sed_import('cid', 'G', 'INT');
$pid = sed_import('pid', 'G', 'ALP');

$plugin_title = $L['plu_title'];
$t -> assign(array(
	'COMEDIT_TITLE' => $plugin_title,
	'COMEDIT_TITLE_URL' => sed_url('plug', 'e=comedit')
));
$t -> parse("MAIN.COMEDIT_TITLE");

if($a == 'update')
{
	sed_check_xg();

	$sql1 = sed_sql_query("SELECT * FROM $db_com WHERE com_id=$cid AND com_code='$pid' LIMIT 1");
	sed_die(sed_sql_numrows($sql1) == 0);
	$row = sed_sql_fetcharray($sql1);

	$time_limit = ($sys['now_offset'] < ($row['com_date'] + $cfg['plugin']['comedit']['time'] * 60)) ? TRUE : FALSE;
	$usr['isowner'] = ($row['com_authorid'] == $usr['id'] && $time_limit);
	$usr['allow_write'] = ($usr['isadmin'] || $usr['isowner']);
	sed_block($usr['allow_write']);

	$comtext = sed_import('comtext', 'P', 'TXT');

	$error_string .= (empty($comtext)) ? $L['plu_comtooshort']."<br />" : '';

	if(isset($error_string))
	{
		$t -> assign('COMEDIT_ERROR_BODY', $error_string);
		$t -> parse('MAIN.COMEDIT_ERROR');
	}

	if(empty($error_string))
	{
		$comhtml = $cfg['parser_cache'] ?
				sed_parse(htmlspecialchars($comtext), $cfg['parsebbcodecom'], $cfg['parsesmiliescom'], true) : '';
		$sql = sed_sql_query("UPDATE $db_com SET com_text = '".sed_sql_prep($comtext)."',
			com_html = '".sed_sql_prep($comhtml)."' WHERE com_id=$cid AND com_code='$pid'");

		if($cfg['plugin']['comedit']['mail'])
		{
			$sql2 = sed_sql_query("SELECT * FROM $db_users WHERE user_maingrp=5");

			$email_title = $L['plu_comlive'].$cfg['main_url'];
			$email_body  = $L['User']." ".$usr['name'].", ".$L['plu_comlive3'];
			$email_body .= $cfg['mainurl'].'/'.sed_url('page', 'id='.substr($pid, 1).'&comments=1', '#c'.$cid)."\n\n";

			while($adm = sed_sql_fetcharray($sql2))
			{
				sed_mail($adm['user_email'], $email_title, $email_body);
			}
		}

		$com_grp = ($usr['isadmin']) ? "adm" : "usr";
		sed_log("Edited comment #".$cid, $com_grp);
		sed_redirect(sed_url('page', 'id='.substr($pid, 1).'&comments=1', '#c'.$cid, true));
	}
}

$sql = sed_sql_query("SELECT * FROM $db_com WHERE com_id=$cid AND com_code='$pid' LIMIT 1");
sed_die(sed_sql_numrows($sql) == 0);
$com = sed_sql_fetcharray($sql);

$com_limit = ($sys['now_offset']<($com['com_date']+$cfg['plugin']['comedit']['time']*60)) ? TRUE : FALSE;
$usr['isowner'] = ($com['com_authorid'] == $usr['id'] && $com_limit);

$usr['allow_write'] = ($usr['isadmin'] || $usr['isowner']);
sed_block($usr['allow_write']);

$com_date = @date($cfg['dateformat'], $com['com_date'] + $usr['timezone'] * 3600);
$pfs = ($usr['id']>0) ? sed_build_pfs($usr['id'], "comedit", "comtext", $L['Mypfs']) : '';
$pfs .= (sed_auth('pfs', 'a', 'A')) ? " &nbsp; ".sed_build_pfs(0, "comedit", "comtext", $L['SFS']) : '';

$t -> assign(array(
	"COMEDIT_FORM_POST" => sed_url('plug', "e=comedit&amp;m=edit&amp;pid=".$com['com_code']."&amp;cid=".$com['com_id']."&amp;a=update&amp;".sed_xg()),
	"COMEDIT_POSTER_TITLE" => $L['Poster'],
	"COMEDIT_POSTER" => $com['com_author'],
	"COMEDIT_IP_TITLE" => $L['Ip'],
	"COMEDIT_IP" => $com['com_authorip'],
	"COMEDIT_DATE_TITLE" => $L['Date'],
	"COMEDIT_DATE" => $com_date,
	"COMEDIT_FORM_UPDATE_BUTTON" => $L['Update']
));

if($cfg['plugin']['comedit']['markitup'] == "No")
{
	$t -> assign(array("COMEDIT_FORM_TEXT" => "<textarea rows=\"8\" cols=\"64\" style=\"width:100%\" id=\"comtext\" name=\"comtext\">".htmlspecialchars($com['com_text'])."</textarea><br />".$pfs));
}
elseif($cfg['plugin']['comedit']['markitup'] == "Yes")
{
	$t -> assign(array("COMEDIT_FORM_TEXT" => "<textarea class=\"minieditor\" rows=\"8\" cols=\"64\" style=\"width:100%\" id=\"comtext\" name=\"comtext\">".htmlspecialchars($com['com_text'])."</textarea><br />".$pfs));
}

$t -> parse("MAIN.COMEDIT_FORM_EDIT");

?>