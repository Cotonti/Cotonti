<?php

/**
 * User Profile
 *
 * @package Cotonti
 * @version 0.9.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('uploads');

@clearstatcache();

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('users', 'a');
cot_block($usr['auth_write']);

/* === Hook === */
foreach (cot_getextplugins('users.profile.first') as $pl)
{
	include $pl;
}
/* ===== */

$id = cot_import('id','G','TXT');
$a = cot_import('a','G','ALP');

$sql = $db->query("SELECT * FROM $db_users WHERE user_id='".$usr['id']."' LIMIT 1");
cot_die($sql->rowCount()==0);
$urr = $sql->fetch();

if($a == 'update')
{
	cot_check_xg();

	/* === Hook === */
	foreach (cot_getextplugins('users.profile.update.first') as $pl)
	{
		include $pl;
	}
	/* ===== */

	$ruser['user_text'] = cot_import('rusertext','P','HTM', $cfg['users']['usertextmax']);
	$ruser['user_country'] = cot_import('rusercountry','P','ALP');
	$rtheme = explode(':', cot_import('rusertheme','P','TXT'));
	$ruser['user_theme'] = $rtheme[0];
	$ruser['user_scheme'] = $rtheme[1];
	$ruser['user_lang'] = cot_import('ruserlang','P','ALP');
	$ruser['user_gender'] = cot_import('rusergender','P','ALP');
	$ruser['user_timezone'] = (float) cot_import('rusertimezone','P','TXT',5);
	$ruser['user_hideemail'] = cot_import('ruserhideemail','P','BOL');
	
	// Extra fields
	foreach($cot_extrafields[$db_users] as $row)
	{
		$ruser['user_'.$row['field_name']] = cot_import_extrafields('ruser'.$row['field_name'], $row, 'P', $urr['user_'.$row['field_name']]);
	}
	$ruser['user_birthdate'] = (int) cot_import_date('ruserbirthdate', false);

	$roldpass = cot_import('roldpass','P','TXT');
	$rnewpass1 = cot_import('rnewpass1','P','TXT');
	$rnewpass2 = cot_import('rnewpass2','P','TXT');
	$rmailpass = cot_import('rmailpass','P','TXT');
	$ruseremail = cot_import('ruseremail','P','TXT');

	//$ruser['user_scheme'] = ($ruser['user_theme'] != $urr['user_theme']) ? $ruser['user_theme'] : $ruser['user_scheme'];

	if (!empty($rnewpass1) && !empty($rnewpass2) && !empty($roldpass))
	{
		if ($rnewpass1 != $rnewpass2) cot_error('pro_passdiffer', 'rnewpass2');
		if (mb_strlen($rnewpass1) < 4 || cot_alphaonly($rnewpass1) != $rnewpass2) cot_error('pro_passtoshort', 'rnewpass1');
		if (md5($roldpass) != $urr['user_password']) cot_error('pro_wrongpass', 'roldpass');

		if (!empty($ruseremail) && !empty($rmailpass) && $cfg['users']['useremailchange'] && $ruseremail != $urr['user_email'])
		{
			cot_error('pro_emailandpass', 'ruseremail');
		}
		if (!cot_error_found())
		{
			$db->update($db_users, array('user_password' => md5($rnewpass1)), "user_id='".$usr['id']."'");
		}
	}
	if (!empty($ruseremail) && (!empty($rmailpass) || $cfg['users']['user_email_noprotection']) && $cfg['users']['useremailchange'] && $ruseremail != $urr['user_email'])
	{
		$sqltmp = $db->query("SELECT COUNT(*) FROM $db_users WHERE user_email='".$db->prep($ruseremail)."'");
		$res = $sqltmp->fetchColumn();

		if (!$cfg['users']['user_email_noprotection'])
		{
			$rmailpass = md5($rmailpass);
			if ($rmailpass != $urr['user_password']) cot_error('pro_wrongpass', 'rmailpass');
		}

		if (!cot_check_email($ruseremail))
			cot_error('aut_emailtooshort', 'ruseremail');
		if ($res > 0) cot_error('aut_emailalreadyindb', 'ruseremail');

		if (!cot_error_found())
		{
			if (!$cfg['users']['user_email_noprotection'])
			{
				$validationkey = md5(microtime());
				$db->update($db_users, array('user_lostpass' => $validationkey, 'user_maingrp' => '-1', 'user_sid' => $urr['user_maingrp']), "user_id='".$usr['id']."'");

				$rsubject = $L['aut_mailnoticetitle'];
				$ractivate = $cfg['mainurl'].'/'.cot_url('users', 'm=register&a=validate&v='.$validationkey, '', true);
				$rbody = sprintf($L['aut_emailchange'], $usr['name'], $ractivate);
				$rbody .= "\n\n".$L['aut_contactadmin'];
				cot_mail($ruseremail, $rsubject, $rbody);

				if(!empty($_COOKIE[$sys['site_id']]))
				{
					cot_setcookie($sys['site_id'], '', time()-63072000, $cfg['cookiepath'], $cfg['cookiedomain'], $sys['secure'], true);
				}

				if (!empty($_SESSION[$sys['site_id']]))
				{
					session_unset();
					session_destroy();
				}
				$db->delete($db_online, "online_ip='{$usr['ip']}'");
				cot_redirect(cot_url('message', 'msg=102', '', true));
			}
			else
			{
				$db->update($db_users, array('user_email' => $ruseremail), "user_id='".$usr['id']."'");
			}
		}
	}
	if (!cot_error_found())
	{
		$ruser['user_birthdate'] = ($ruser['user_birthdate'] > $sys['now_offset']) ? ($sys['now_offset'] - 31536000) : $ruser['user_birthdate'];
		$ruser['user_birthdate'] = ($ruser['user_birthdate'] == '0') ? '0000-00-00' : cot_stamp2date($ruser['user_birthdate']);
		$ruser['user_auth'] ='';
		$db->update($db_users, $ruser, "user_id='".$usr['id']."'");
		cot_extrafield_movefiles();
		
		/* === Hook === */
		foreach (cot_getextplugins('users.profile.update.done') as $pl)
		{
			include $pl;
		}
		/* ===== */
		cot_redirect(cot_url('users', 'm=profile', '', true));
	}
}

$sql = $db->query("SELECT * FROM $db_users WHERE user_id='".$usr['id']."' LIMIT 1");
$urr = $sql->fetch();

$out['subtitle'] = $L['Profile'];
$out['head'] .= $R['code_noindex'];

/* === Hook === */
foreach (cot_getextplugins('users.profile.main') as $pl)
{
	include $pl;
}
/* ===== */

require_once $cfg['system_dir'] . '/header.php';

$mskin = cot_tplfile(array('users', 'profile'), 'module');
$t = new XTemplate($mskin);

require_once cot_incfile('forms');

$timezonelist = array('-12', '-11', '-10', '-09', '-08', '-07', '-06', '-05', '-04', '-03',  '-03.5', '-02', '-01', '+00', '+01', '+02', '+03', '+03.5', '+04', '+04.5', '+05', '+05.5', '+06', '+07', '+08', '+09', '+09.5', '+10', '+11', '+12');
foreach($timezonelist as $x)
{
	$timezonename[] = 'GMT '.$x.', '.cot_date('datetime_medium', $sys['now_offset'] + $x*3600);
}
$profile_form_timezone = cot_selectbox($urr['user_timezone'], 'rusertimezone', $timezonelist, $timezonename, false);
$profile_form_timezone .= ' '.$usr['gmttime'].' / '.cot_date('datetime_medium', $sys['now_offset']).' '.$usr['timetext'];

$protected = !$cfg['users']['useremailchange'] ? array('disabled' => 'disabled') : array();
$profile_form_email = cot_inputbox('text', 'ruseremail', $urr['user_email'], array('size' => 32, 'maxlength' => 64)
	+ $protected);

$editor_class = $cfg['usertextimg'] ? 'minieditor' : '';

$t->assign(array(
	'USERS_PROFILE_TITLE' => cot_rc_link(cot_url('users', 'm=profile'), $L['pro_title']),
	'USERS_PROFILE_SUBTITLE' => $L['pro_subtitle'],
	'USERS_PROFILE_DETAILSLINK' => cot_url('users', 'm=details&id='.$urr['user_id']),
	'USERS_PROFILE_EDITLINK' => cot_url('users', 'm=edit&id='.$urr['user_id']),
	'USERS_PROFILE_FORM_SEND' => cot_url('users', "m=profile&a=update&".cot_xg()),
	'USERS_PROFILE_ID' => $urr['user_id'],
	'USERS_PROFILE_NAME' => htmlspecialchars($urr['user_name']),
	'USERS_PROFILE_MAINGRP' => cot_build_group($urr['user_maingrp']),
	'USERS_PROFILE_GROUPS' => cot_build_groupsms($urr['user_id'], FALSE, $urr['user_maingrp']),
	'USERS_PROFILE_COUNTRY' => cot_selectbox_countries($urr['user_country'], 'rusercountry'),
	'USERS_PROFILE_TEXT' => cot_textarea('rusertext', $urr['user_text'], 8, 56, array('class' => $editor_class)),
	'USERS_PROFILE_EMAIL' => $profile_form_email,
	'USERS_PROFILE_EMAILPASS' => cot_inputbox('password', 'rmailpass', '', array('size' => 12, 'maxlength' => 32)),
	'USERS_PROFILE_HIDEEMAIL' => cot_radiobox($urr['user_hideemail'], 'ruserhideemail', array(1, 0), array($L['Yes'], $L['No'])),
	'USERS_PROFILE_THEME' => cot_selectbox_theme($urr['user_theme'], $urr['user_scheme'], 'rusertheme'),
	'USERS_PROFILE_LANG' => cot_selectbox_lang($urr['user_lang'], 'ruserlang'),
	'USERS_PROFILE_GENDER' => cot_selectbox_gender($urr['user_gender'] ,'rusergender'),
	'USERS_PROFILE_BIRTHDATE' => cot_selectbox_date(cot_date2stamp($urr['user_birthdate']), 'short', 'ruserbirthdate', cot_date('Y', $sys['now_offset']), cot_date('Y', $sys['now_offset']) - 100, false),
	'USERS_PROFILE_TIMEZONE' => $profile_form_timezone,
	'USERS_PROFILE_REGDATE' => cot_date('datetime_medium', $urr['user_regdate']),
	'USERS_PROFILE_REGDATE_STAMP' => $urr['user_regdate'],
	'USERS_PROFILE_LASTLOG' => cot_date('datetime_medium', $urr['user_lastlog']),
	'USERS_PROFILE_LASTLOG_STAMP' => $urr['user_lastlog'],
	'USERS_PROFILE_LOGCOUNT' => $urr['user_logcount'],
	'USERS_PROFILE_ADMINRIGHTS' => '',
	'USERS_PROFILE_OLDPASS' => cot_inputbox('password', 'roldpass', '', array('size' => 12, 'maxlength' => 32)),
	'USERS_PROFILE_NEWPASS1' => cot_inputbox('password', 'rnewpass1', '', array('size' => 12, 'maxlength' => 32)),
	'USERS_PROFILE_NEWPASS2' => cot_inputbox('password', 'rnewpass2', '', array('size' => 12, 'maxlength' => 32)),
));

// Extra fields
foreach($cot_extrafields[$db_users] as $i => $row)
{
	$t->assign('USERS_PROFILE_'.strtoupper($row['field_name']), cot_build_extrafields('ruser'.$row['field_name'], $row, $urr['user_'.$row['field_name']]));
	$t->assign('USERS_PROFILE_'.strtoupper($row['field_name']).'_TITLE', isset($L['user_'.$row['field_name'].'_title']) ? $L['user_'.$row['field_name'].'_title'] : $row['field_description']);
}

/* === Hook === */
foreach (cot_getextplugins('users.profile.tags') as $pl)
{
	include $pl;
}
/* ===== */

// Error handling
cot_display_messages($t);

if ($cfg['users']['useremailchange'])
{
	if (!$cfg['users']['user_email_noprotection'])
	{
		$t->parse('MAIN.USERS_PROFILE_EMAILCHANGE.USERS_PROFILE_EMAILPROTECTION');
	}
	$t->parse('MAIN.USERS_PROFILE_EMAILCHANGE');
}

$t->parse('MAIN');
$t->out('MAIN');

require_once $cfg['system_dir'] . '/footer.php';

?>