<?php

/**
 * User Profile
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

sed_require('pfs'); // FIXME hard PFS dependency

@clearstatcache();

if ($usr['id']<1)
{
	sed_redirect(sed_url('message', "msg=100&".$sys['url_redirect'], '', true));
}

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('users', 'a');
sed_block($usr['auth_write']);

/* === Hook === */
foreach (sed_getextplugins('profile.first') as $pl)
{
	include $pl;
}
/* ===== */

$id = sed_import('id','G','TXT');
$a = sed_import('a','G','ALP');

$sql = sed_sql_query("SELECT * FROM $db_users WHERE user_id='".$usr['id']."' LIMIT 1");
sed_die(sed_sql_numrows($sql)==0);
$urr = sed_sql_fetcharray($sql);

switch ($a)
{
	/* ============= */
	case 'avatardelete':
	/* ============= */

		sed_check_xg();
		$filename = $usr['id']."-avatar.gif";
		$filepath = $cfg['av_dir'].$filename;
		if (file_exists($filepath))
		{
			unlink($filepath);
		}
		$sql = sed_sql_update($db_users, array('user_avatar' => ''), "user_id='".$usr['id']."'");
		sed_redirect(sed_url('users', "m=profile", '#avatar', true));
		break;

	/* ============= */
	case 'phdelete':
	/* ============= */

		sed_check_xg();
		$photo = $usr['id']."-photo.gif";
		$photopath = $cfg['photos_dir'].$photo;
		if (file_exists($photopath))
		{
			unlink($photopath);
		}
		$sql = sed_sql_update($db_users, array('user_photo' => ''), "user_id='".$usr['id']."'");
		sed_redirect(sed_url('users', "m=profile", '#photo', true));
		break;

	/* ============= */
	case 'sigdelete':
	/* ============= */

		sed_check_xg();
		$signature = $usr['id']."-signature.gif";
		$signaturepath = $cfg['sig_dir'].$signature;
		if (file_exists($signaturepath))
		{
			unlink($signaturepath);
		}

		$sql = sed_sql_update($db_users, array('user_signature' => ''), "user_id='".$usr['id']."'");
		sed_redirect(sed_url('users', "m=profile", '#signature', true));
		break;

	/* ============= */
	case 'avatarselect':
	/* ============= */

		sed_check_xg();
		$filename = $cfg['defav_dir'].urldecode($id);
		$filename = str_replace(array("'", ",", chr(0x00)), "", $filename);
		if (file_exists($filename))
		{
			$sql = sed_sql_update($db_users, array('user_avatar' => $filename), "user_id='".$usr['id']."'");
		}
		sed_redirect(sed_url('users', "m=profile", '#avatar', true));
		break;

	/* ============= */
	case 'update':
	/* ============= */

		sed_check_xg();

		/* === Hook === */
		foreach (sed_getextplugins('profile.update.first') as $pl)
		{
			include $pl;
		}
		/* ===== */

		$userpic['av'] = $_FILES['userfile'];
		$userpic['ph'] = $_FILES['userphoto'];
		$userpic['sig'] = $_FILES['usersig'];
		$picfull = array('av' =>'avatar', 'ph' => 'photo', 'sig' => 'signature');
		$gd_graf = array('jpeg', 'jpg', 'jpg', 'png');

		if (!empty($userpic['av']['tmp_name']) || !empty($userpic['ph']['tmp_name']) || !empty($userpic['sig']['tmp_name']))
		{
			@clearstatcache();
		}

		foreach($userpic as $key => $val)
		{
			if (!empty($userpic[$key]['tmp_name']) && $userpic[$key]['size']>0)
			{
				$f_extension = end(explode(".", $userpic[$key]['name']));
				$fcheck = sed_file_check($userpic[$key]['tmp_name'], $userpic[$key]['name'], $f_extension);
				if($fcheck == 1)
				{
					if (is_uploaded_file($userpic[$key]['tmp_name']) && $userpic[$key]['size']>0 && in_array($f_extension, $gd_graf))
					{
						list($w, $h) = @getimagesize($userpic[$key]['tmp_name']);

						$filename = $usr['id']."-".$picfull[$key].".gif";
						$filepath = (($key == 'ph') ? $cfg['photos_dir'] : $cfg[$key.'_dir']).$filename;

						if (file_exists($filepath))
						{
							unlink($filepath);
						}

						move_uploaded_file($userpic[$key]['tmp_name'], $filepath);

						if ($w > $cfg[$key.'_maxx'] || $h > $cfg[$key.'_maxy'] || $userpic[$key]['size'] > $cfg[$key.'_maxsize'])
						{
							$prior = ($w > $h) ? 'Width' : 'Height';
							$percentage = 100;

							sed_createthumb($filepath, $filepath, $cfg['av_maxx'],$cfg['av_maxy'], 1, $f_extension, $filename, 0, 0, 0, 0, 0, $percentage, $prior);

							while ( ($f_extension == 'jpeg' || $f_extension == 'jpg') && ($userpic[$key]['size'] > $cfg[$key.'_maxsize']))
							{
								$percentage -= 5;
								sed_createthumb($filepath, $filepath, $cfg[$key.'_maxx'],$cfg[$key.'_maxy'], 1, $f_extension, $filename, 0, 0, 0, 0, 0, $percentage, $prior);

								clearstatcache();
								$userpic[$key]['size'] = filesize($filepath);
							}
						}

						/* === Hook === */
						foreach (sed_getextplugins('profile.update.'.$picfull[$key]) as $pl)
						{
							include $pl;
						}
						/* ===== */

						$userpic[$key]['size'] = filesize($filepath);
						if ($userpic[$key]['size'] <= $cfg[$key.'_maxsize'])
						{
							$sql = sed_sql_update($db_users, array( "user_".$picfull[$key] => $filepath), "user_id='".$usr['id']."'");
						}
						else
						{
							unlink($filepath);
						}
						@chmod($filepath, $cfg['file_perms']);
					}
				}
				elseif($fcheck == 2)
				{
					sed_error(sprintf($L['pfs_filemimemissing'], $f_extension), 'userfile');
				}
				else
				{
					sed_error(sprintf($L['pro_'.$picfull[$key].'notvalid'], $f_extension), 'userfile');
				}
			}
		}

		$ruser['text'] = sed_import('rusertext','P','TXT', $cfg['usertextmax']);
		$ruser['country'] = sed_import('rusercountry','P','ALP');
		$rtheme = explode(':', sed_import('rusertheme','P','TXT'));
		$ruser['theme'] = $rtheme[0];
		$ruset['scheme'] = $rtheme[1];
		$ruser['lang'] = sed_import('ruserlang','P','ALP');
		$ruser['gender'] = sed_import('rusergender','P','ALP');
		$ruser['timezone'] = (float) sed_import('rusertimezone','P','TXT',5);	
		$ruser['hideemail'] = sed_import('ruserhideemail','P','BOL');
		$ruser['pmnotify'] = sed_import('ruserpmnotify','P','BOL');
		// Extra fields
		foreach($sed_extrafields['users'] as $row)
		{
			$ruser[$row['field_name']] = sed_import_extrafields('user', $row);
		}
		$ruser['birthdate'] = (int)sed_import_date('ruserbirthdate');		
		
		$roldpass = sed_import('roldpass','P','PSW');
		$rnewpass1 = sed_import('rnewpass1','P','PSW');
		$rnewpass2 = sed_import('rnewpass2','P','PSW');
		$rmailpass = sed_import('rmailpass','P','TXT');
		$ruseremail = sed_import('ruseremail','P','TXT');

		//$ruser['scheme'] = ($ruser['theme'] != $urr['user_theme']) ? $ruser['theme'] : $ruser['scheme'];

		if (!empty($rnewpass1) && !empty($rnewpass2) && !empty($roldpass))
		{
			if ($rnewpass1 != $rnewpass2) sed_error('pro_passdiffer', 'rnewpass2');
			if (mb_strlen($rnewpass1) < 4 || sed_alphaonly($rnewpass1) != $rnewpass2) sed_error('pro_passtoshort', 'rnewpass1');
			if (md5($roldpass) != $urr['user_password']) sed_error('pro_wrongpass', 'roldpass');

			if (!empty($ruseremail) && !empty($rmailpass) && $cfg['useremailchange'] && $ruseremail != $urr['user_email'])
			{
				sed_error('pro_emailandpass', 'ruseremail');
			}

			if (!$cot_error)
			{
				sed_sql_update($db_users, array('user_password' => md5($rnewpass1)), "user_id='".$usr['id']."'");
			}
		}

		if (!empty($ruseremail) && (!empty($rmailpass) || $cfg['user_email_noprotection']) && $cfg['useremailchange'] && $ruseremail != $urr['user_email'])
		{

			$sqltmp = sed_sql_query("SELECT COUNT(*) FROM $db_users WHERE user_email='".sed_sql_prep($ruseremail)."'");
			$res = sed_sql_result($sqltmp, 0, "COUNT(*)");

			if (!$cfg['user_email_noprotection'])
			{
				$rmailpass = md5($rmailpass);
				if ($rmailpass != $urr['user_password']) sed_error('pro_wrongpass', 'rmailpass');
			}

			if (mb_strlen($ruseremail) < 4 || !preg_match('#^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]{2,})+$#i', $ruseremail))
				sed_error('aut_emailtooshort', 'ruseremail');
			if ($res > 0) sed_error('aut_emailalreadyindb', 'ruseremail');

			if (!$cot_error)
			{
				if (!$cfg['user_email_noprotection'])
				{
					$validationkey = md5(microtime());
					sed_sql_update($db_users, array('user_lostpass' => $validationkey, 'user_maingrp' => '-1', 'user_sid' => $urr['user_maingrp']), "user_id='".$usr['id']."'");

					$rsubject = $cfg['maintitle']." - ".$L['aut_mailnoticetitle'];
					$ractivate = $cfg['mainurl'].'/'.sed_url('users', 'm=register&a=validate&v='.$validationkey, '', true);
					$rbody = sprintf($L['aut_emailchange'], $usr['name'], $ractivate);
					$rbody .= "\n\n".$L['aut_contactadmin'];
					sed_mail($ruseremail, $rsubject, $rbody);

					if(!empty($_COOKIE[$sys['site_id']]))
					{
						sed_setcookie($sys['site_id'], '', time()-63072000, $cfg['cookiepath'], $cfg['cookiedomain'], $sys['secure'], true);
					}

					if (!empty($_SESSION[$sys['site_id']]))
					{
						session_unset();
						session_destroy();
					}
					sed_sql_delete($db_online, "online_ip='{$usr['ip']}'");
					sed_redirect(sed_url('message', 'msg=102', '', true));
				}
				else
				{
					sed_sql_update($db_users, array('user_email' => $ruseremail), "user_id='".$usr['id']."'");
				}
			}
		}


		if (!$cot_error)
		{
			$ruser['birthdate'] = ($ruser['birthdate'] > $sys['now_offset']) ? ($sys['now_offset'] - 31536000) : $ruser['birthdate'];
			$ruser['birthdate'] = ($ruser['birthdate'] == '0') ? '0000-00-00' : sed_stamp2date($ruser['birthdate']);
			$ruser['auth'] ='';
			sed_sql_update($db_users, $ruser, "user_id='".$usr['id']."'", 'user_');

			/* === Hook === */
			foreach (sed_getextplugins('profile.update.done') as $pl)
			{
				include $pl;
			}
			/* ===== */

			sed_redirect(sed_url('users', 'm=profile'));
		}
		break;

	/* ============= */
	default:
	/* ============= */

		break;

}

$sql = sed_sql_query("SELECT * FROM $db_users WHERE user_id='".$usr['id']."' LIMIT 1");
$urr = sed_sql_fetcharray($sql);

$title_params = array(
	'PROFILE' => $L['Profile'],
	'NAME' => $urr['user_name']
);
$out['subtitle'] = sed_title('title_users_profile', $title_params);
$out['head'] .= $R['code_noindex'];

/* === Hook === */
foreach (sed_getextplugins('profile.main') as $pl)
{
	include $pl;
}
/* ===== */

require_once $cfg['system_dir'] . '/header.php';

$mskin = sed_skinfile(array('users', 'profile'));
$t = new XTemplate($mskin);

sed_require_api('forms');

$profile_form_avatar = $R['users_link_avatar'];
$profile_form_photo = $R['users_link_photo'];
$profile_form_signature = $R['users_link_signature'];

$timezonelist = array('-12', '-11', '-10', '-09', '-08', '-07', '-06', '-05', '-04', '-03',  '-03.5', '-02', '-01', '+00', '+01', '+02', '+03', '+03.5', '+04', '+04.5', '+05', '+05.5', '+06', '+07', '+08', '+09', '+09.5', '+10', '+11', '+12');
foreach($timezonelist as $x)
{
	$timezonename[] = 'GMT '.$x.', '.date($cfg['dateformat'], $sys['now_offset'] + $x*3600);
}
$profile_form_timezone = sed_selectbox($urr['user_timezone'], 'rusertimezone', $timezonelist, $timezonename, false);
$profile_form_timezone .= ' '.$usr['gmttime'].' / '.date($cfg['dateformat'], $sys['now_offset'] + $usr['timezone']*3600).' '.$usr['timetext'];

$protected = !$cfg['useremailchange'] ? array('disabled' => 'disabled') : array();
$profile_form_email = sed_inputbox('text', 'ruseremail', $urr['user_email'], array('size' => 32, 'maxlength' => 64)
	+ $protected);

$profile_form_avatar_existing = !empty($urr['user_avatar']) ? sed_rc('users_code_avatar_existing', array(
	'avatar_url' => $urr['user_avatar'],
	'delete_url' => sed_url('users', 'm=profile&a=avatardelete&'.sed_xg())
	)) : '';
$profile_form_avatar = sed_rc('users_code_avatar', array(
	'avatar_existing' => $profile_form_avatar_existing,
	'input_maxsize' => sed_inputbox('hidden', 'MAX_FILE_SIZE', $cfg['av_maxsize']*1024),
	'input_file' => sed_inputbox('file', 'userfile', '', array('size' => 24))
));

$profile_form_photo_existing = !empty($urr['user_photo']) ? sed_rc('users_code_photo_existing', array(
	'photo_url' => $urr['user_photo'],
	'delete_url' => sed_url('users', 'm=profile&a=phdelete&'.sed_xg())
	)) : '';
$profile_form_photo = sed_rc('users_code_photo', array(
	'photo_existing' => $profile_form_photo_existing,
	'input_maxsize' => sed_inputbox('hidden', 'MAX_FILE_SIZE', $cfg['ph_maxsize']*1024),
	'input_file' => sed_inputbox('file', 'userphoto', '', array('size' => 24))
));

$profile_form_signature_existing = !empty($urr['user_signature']) ? sed_rc('users_code_signature_existing', array(
	'signature_url' => $urr['user_signature'],
	'delete_url' => sed_url('users', 'm=profile&a=sigdelete&'.sed_xg())
	)) : '';
$profile_form_signature = sed_rc('users_code_signature', array(
	'signature_existing' => $profile_form_signature_existing,
	'input_maxsize' => sed_inputbox('hidden', 'MAX_FILE_SIZE', $cfg['sig_maxsize']*1024),
	'input_file' => sed_inputbox('file', 'usersig', '', array('size' => 24))
));

if ($a=='avatarchoose')
{
	sed_check_xg();
	$profile_form_avatar .= $R['users_code_avatarchoose_title'];
	$handle = opendir($cfg['defav_dir']);
	while ($f = readdir($handle))
	{
		if ($f != "." && $f != "..")
		{
			$profile_form_avatar .= sed_rc('users_link_avatarselect', array(
				'url' => sed_url('users', 'm=profile&a=avatarselect&'.sed_xg().'&id='.urlencode($f), '#avatar'),
				'f' => $f
			));
		}
	}
	closedir($handle);
}
else
{
	$profile_form_avatar .= sed_rc('users_link_avatarchoose', array(
		'url' => sed_url('users', 'm=profile&a=avatarchoose&'.sed_xg(), '#list')
	));
}

$editor_class = $cfg['parsebbcodeusertext'] ? 'minieditor' : '';

$useredit_array = array(
	"USERS_PROFILE_TITLE" => sed_rc_link(sed_url('users', 'm=profile'), $L['pro_title']),
	"USERS_PROFILE_SUBTITLE" => $L['pro_subtitle'],
	"USERS_PROFILE_FORM_SEND" => sed_url('users', "m=profile&a=update&".sed_xg()),
	"USERS_PROFILE_ID" => $urr['user_id'],
	"USERS_PROFILE_NAME" => htmlspecialchars($urr['user_name']),
	"USERS_PROFILE_MAINGRP" => sed_build_group($urr['user_maingrp']),
	"USERS_PROFILE_GROUPS" => sed_build_groupsms($urr['user_id'], FALSE, $urr['user_maingrp']),
	"USERS_PROFILE_COUNTRY" => sed_selectbox_countries($urr['user_country'], 'rusercountry'),
	"USERS_PROFILE_AVATAR" => $profile_form_avatar,
	"USERS_PROFILE_PHOTO" => $profile_form_photo,
	"USERS_PROFILE_SIGNATURE" => $profile_form_signature,
	"USERS_PROFILE_TEXT" => sed_textarea('rusertext', $urr['user_text'], 8, 56, array('class' => $editor_class)),
	"USERS_PROFILE_EMAIL" => sed_radiobox($urr['user_hideemail'], 'ruserhideemail', array(1, 0), array($L['Yes'], $L['No'])),
	"USERS_PROFILE_EMAILPASS" => sed_inputbox('password', 'rmailpass', '', array('size' => 12, 'maxlength' => 32)),
	"USERS_PROFILE_HIDEEMAIL" => $profile_form_hideemail,
	"USERS_PROFILE_PMNOTIFY" => sed_radiobox($urr['user_pmnotify'], 'ruserpmnotify', array(1, 0), array($L['Yes'], $L['No'])),
	"USERS_PROFILE_THEME" => sed_selectbox_theme($urr['user_theme'], $urr['user_scheme'], 'rusertheme'),
	"USERS_PROFILE_LANG" => sed_selectbox_lang($urr['user_lang'], 'ruserlang'),
	"USERS_PROFILE_GENDER" => sed_selectbox_gender($urr['user_gender'] ,'rusergender'),
	"USERS_PROFILE_BIRTHDATE" => sed_selectbox_date(sed_date2stamp($urr['user_birthdate']), 'short', '', date('Y', $sys['now_offset']), 1910),
	"USERS_PROFILE_TIMEZONE" => $profile_form_timezone,
	"USERS_PROFILE_REGDATE" => @date($cfg['dateformat'], $urr['user_regdate'] + $usr['timezone'] * 3600)." ".$usr['timetext'],
	"USERS_PROFILE_LASTLOG" => @date($cfg['dateformat'], $urr['user_lastlog'] + $usr['timezone'] * 3600)." ".$usr['timetext'],
	"USERS_PROFILE_LOGCOUNT" => $urr['user_logcount'],
	"USERS_PROFILE_ADMINRIGHTS" => '',
	"USERS_PROFILE_OLDPASS" => sed_inputbox('password', 'roldpass', '', array('size' => 12, 'maxlength' => 32)),
	"USERS_PROFILE_NEWPASS1" => sed_inputbox('password', 'rnewpass1', '', array('size' => 12, 'maxlength' => 32)),
	"USERS_PROFILE_NEWPASS2" => sed_inputbox('password', 'rnewpass2', '', array('size' => 12, 'maxlength' => 32)),
);
$t->assign($useredit_array);

// Extra fields
foreach($sed_extrafields['users'] as $i => $row)
{
	$t->assign('USERS_PROFILE_'.strtoupper($row['field_name']), sed_build_extrafields('user',  $row, $urr['user_'.$row['field_name']]));
	$t->assign('USERS_PROFILE_'.strtoupper($row['field_name']).'_TITLE', isset($L['user_'.$row['field_name'].'_title']) ? $L['user_'.$row['field_name'].'_title'] : $row['field_description']);
}

// Error handling
sed_display_messages($t);

/* === Hook === */
foreach (sed_getextplugins('profile.tags') as $pl)
{
	include $pl;
}
/* ===== */

if ($cfg['useremailchange'])
{
	if (!$cfg['user_email_noprotection'])
	{
		$t->parse('MAIN.USERS_PROFILE_EMAILCHANGE.USERS_PROFILE_EMAILPROTECTION');
	}
	$t->parse('MAIN.USERS_PROFILE_EMAILCHANGE');
}

$t->parse("MAIN");
$t->out("MAIN");

require_once $cfg['system_dir'] . '/footer.php';

?>