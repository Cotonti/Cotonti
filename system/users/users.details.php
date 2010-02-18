<?php
/**
 * User Details
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

$y = sed_import('y','P','TXT');
$id = sed_import('id','G','INT');
$s = sed_import('s','G','ALP',13);
$w = sed_import('w','G','ALP',4);
$d = sed_import('d','G','INT');
$f = sed_import('f','G','TXT');
$u = sed_import('u','G','TXT');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('users', 'a');
sed_block($usr['auth_read']);

/* === Hook === */
$extp = sed_getextplugins('users.details.first');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

if(!empty($u) && !empty($id))
{
	$sql = sed_sql_query("SELECT user_id FROM $db_users WHERE user_name='".sed_sql_prep($u)."' AND user_id ='$id' LIMIT 1");
	$u = sed_sql_fetcharray($sql);
	$id = $u['user_id'];
}
elseif(!empty($u))
{

	$sql = sed_sql_query("SELECT user_id FROM $db_users WHERE user_name='".sed_sql_prep($u)."' LIMIT 1");
	$u = sed_sql_fetcharray($sql);
	$id = $u['user_id'];
}
elseif(empty($id) && $usr['id']>0)
{
	$id = $usr['id'];
}


$sql = sed_sql_query("SELECT * FROM $db_users WHERE user_id='$id' LIMIT 1");
sed_die(sed_sql_numrows($sql)==0);
$urr = sed_sql_fetcharray($sql);

$urr['user_birthdate'] = sed_date2stamp($urr['user_birthdate']);

$urr['user_text'] = sed_build_usertext(htmlspecialchars($urr['user_text']));
$urr['user_website'] = sed_build_url($urr['user_website']);
$urr['user_age'] = ($urr['user_birthdate']!=0) ? sed_build_age($urr['user_birthdate']) : '';
$urr['user_birthdate'] = ($urr['user_birthdate']!=0) ? @date($cfg['formatyearmonthday'], $urr['user_birthdate']) : '';
$urr['user_gender'] = ($urr['user_gender']=='' || $urr['user_gender']=='U') ?  '' : $L["Gender_".$urr['user_gender']];

$title_params = array(
	'USER' => $L['User'],
	'NAME' => $urr['user_name']
);
$out['subtitle'] = sed_title('title_users_details', $title_params);

/* === Hook === */
$extp = sed_getextplugins('users.details.main');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

require_once $cfg['system_dir'] . '/header.php';

$mskin = sed_skinfile(array('users', 'details'));
$t = new XTemplate($mskin);

$bhome = $cfg['homebreadcrumb'] ? '<a href="'.$cfg['mainurl'].'">'.htmlspecialchars($cfg['maintitle']).'</a> '.$cfg['separator'].' ' : '';

$t->assign(array(
	"USERS_DETAILS_TITLE" => $bhome . "<a href=\"".sed_url('users')."\">".$L['Users']."</a> ".$cfg['separator']." ".sed_build_user($urr['user_id'], htmlspecialchars($urr['user_name'])),
	"USERS_DETAILS_SUBTITLE" => $L['use_subtitle'],
	"USERS_DETAILS_ID" => $urr['user_id'],
	"USERS_DETAILS_PM" => sed_build_pm($urr['user_id']),
	"USERS_DETAILS_NAME" => htmlspecialchars($urr['user_name']),
	"USERS_DETAILS_PASSWORD" => $urr['user_password'],
	"USERS_DETAILS_MAINGRP" => sed_build_group($urr['user_maingrp']),
	"USERS_DETAILS_MAINGRPID" => $urr['user_maingrp'],
	"USERS_DETAILS_MAINGRPSTARS" => sed_build_stars($sed_groups[$urr['user_maingrp']]['level']),
	"USERS_DETAILS_MAINGRPICON" => sed_build_userimage($sed_groups[$urr['user_maingrp']]['icon']),
	"USERS_DETAILS_GROUPS" => sed_build_groupsms($urr['user_id'], FALSE, $urr['user_maingrp']),
	"USERS_DETAILS_COUNTRY" => sed_build_country($urr['user_country']),
	"USERS_DETAILS_COUNTRYFLAG" => sed_build_flag($urr['user_country']),
	"USERS_DETAILS_TEXT" => $cfg['parsebbcodeusertext'] ? sed_bbcode_parse($urr['user_text'], true) : $urr['user_text'],
	"USERS_DETAILS_AVATAR" => sed_build_userimage($urr['user_avatar'], 'avatar'),
	"USERS_DETAILS_PHOTO" => sed_build_userimage($urr['user_photo'], 'photo'),
	"USERS_DETAILS_SIGNATURE" => sed_build_userimage($urr['user_signature'], 'sig'),
	"USERS_DETAILS_EMAIL" => sed_build_email($urr['user_email'], $urr['user_hideemail']),
	"USERS_DETAILS_PMNOTIFY" =>  $sed_yesno[$urr['user_pmnotify']],
	"USERS_DETAILS_SKIN" => $urr['user_skin'],
	"USERS_DETAILS_WEBSITE" => $urr['user_website'],
	"USERS_DETAILS_JOURNAL" => $urr['user_journal'],
	"USERS_DETAILS_ICQ" => sed_build_icq($urr['user_icq']),
	"USERS_DETAILS_MSN" => sed_build_msn($urr['user_msn']),
	"USERS_DETAILS_IRC" => htmlspecialchars($urr['user_irc']),
	"USERS_DETAILS_GENDER" => $urr['user_gender'],
	"USERS_DETAILS_BIRTHDATE" => $urr['user_birthdate'],
	"USERS_DETAILS_AGE" => $urr['user_age'],
	"USERS_DETAILS_TIMEZONE" => sed_build_timezone($urr['user_timezone']),
	"USERS_DETAILS_LOCATION" => htmlspecialchars($urr['user_location']),
	"USERS_DETAILS_OCCUPATION" => htmlspecialchars($urr['user_occupation']),
	"USERS_DETAILS_REGDATE" => @date($cfg['dateformat'], $urr['user_regdate'] + $usr['timezone'] * 3600)." ".$usr['timetext'],
	"USERS_DETAILS_LASTLOG" => @date($cfg['dateformat'], $urr['user_lastlog'] + $usr['timezone'] * 3600)." ".$usr['timetext'],
	"USERS_DETAILS_LOGCOUNT" => $urr['user_logcount'],
	"USERS_DETAILS_POSTCOUNT" => $urr['user_postcount'],
	"USERS_DETAILS_LASTIP" => $urr['user_lastip']
		));

// Extra fields for users
foreach($sed_extrafields['users'] as $i => $row)
{
	$t->assign('USERS_DETAILS_'.strtoupper($row['field_name']), sed_build_extrafields_data('user', $row['field_type'], $row['field_name'], $urr['user_'.$row['field_name']]));
	$t->assign('USERS_DETAILS_'.strtoupper($row['field_name']).'_TITLE', isset($L['user_'.$row['field_name'].'_title']) ? $L['user_'.$row['field_name'].'_title'] : $row['field_description']);
}


/* === Hook === */
$extp = sed_getextplugins('users.details.tags');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

if ($usr['isadmin'])
		{
		$t-> assign(array(
			"USERS_DETAILS_ADMIN_EDIT" => "<a href=\"".sed_url('users', 'm=edit&id='.$urr['user_id'])."\">".$L['Edit']."</a>"
			));

		$t->parse("MAIN.USERS_DETAILS_ADMIN");
		}

$t->parse("MAIN");
$t->out("MAIN");

require_once $cfg['system_dir'] . '/footer.php';

?>