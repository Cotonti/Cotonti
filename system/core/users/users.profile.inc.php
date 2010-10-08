<?PHP

/**
 * User Profile
 *
 * @package Cotonti
 * @version 0.6.10
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

@clearstatcache();

if ($usr['id']<1)
{
	header("Location: " . SED_ABSOLUTE_URL . sed_url('message', "msg=100&".$sys['url_redirect'], '', true));
	exit;
}

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('users', 'a');
sed_block($usr['auth_write']);

/* === Hook === */
$extp = sed_getextplugins('profile.first');
if (is_array($extp))
	{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

$id = sed_import('id','G','TXT');
$a = sed_import('a','G','ALP');

$sql = sed_sql_query("SELECT * FROM $db_users WHERE user_id='".$usr['id']."' LIMIT 1");
sed_die(sed_sql_numrows($sql)==0);
$urr = sed_sql_fetcharray($sql);

$urr['user_birthdate'] = sed_date2stamp($urr['user_birthdate']);

$profile_form_avatar = "<a name=\"avatar\" id=\"avatar\"></a>";
$profile_form_photo = "<a name=\"photo\" id=\"photo\"></a>";
$profile_form_signature = "<a name=\"signature\" id=\"signature\"></a>";

// Extra fields - getting
$extrafields = array();
$fieldsres = sed_sql_query("SELECT * FROM $db_extra_fields WHERE field_location='users'");
while($row = sed_sql_fetchassoc($fieldsres)) $extrafields[] = $row;

switch ($a)
{
	/* ============= */
	case 'avatardelete':
	/* ============= */

	sed_check_xg();
	$avatar = $usr['id']."-avatar.gif";
	$avatarpath = $cfg['av_dir'].$avatar;

	if (file_exists($avatarpath))
		{ unlink($avatarpath); }

	$sql = sed_sql_query("DELETE FROM $db_pfs WHERE pfs_file='$avatar'");
	$sql = sed_sql_query("UPDATE $db_users SET user_avatar='' WHERE user_id='".$usr['id']."'");
	header("Location: " . SED_ABSOLUTE_URL . sed_url('users', "m=profile", '#avatar', true));
	exit;

	break;

	/* ============= */
	case 'phdelete':
	/* ============= */

	sed_check_xg();
	$photo = $usr['id']."-photo.gif";
	$photopath = $cfg['photos_dir'].$photo;

	if (file_exists($photopath))
		{ unlink($photopath); }

	$sql = sed_sql_query("DELETE FROM $db_pfs WHERE pfs_file='$photo'");
	$sql = sed_sql_query("UPDATE $db_users SET user_photo='' WHERE user_id='".$usr['id']."'");
	header("Location: " . SED_ABSOLUTE_URL . sed_url('users', "m=profile", '#photo', true));
	exit;

	break;

	/* ============= */
	case 'sigdelete':
	/* ============= */

	sed_check_xg();
	$signature = $usr['id']."-signature.gif";
	$signaturepath = $cfg['sig_dir'].$signature;

	if (file_exists($signaturepath))
		{ unlink($signaturepath); }

	$sql = sed_sql_query("DELETE FROM $db_pfs WHERE pfs_file='$signature'");
	$sql = sed_sql_query("UPDATE $db_users SET user_signature='' WHERE user_id='".$usr['id']."'");
	header("Location: " . SED_ABSOLUTE_URL . sed_url('users', "m=profile", '#signature', true));
	exit;

	break;

	/* ============= */
	case 'avatarselect':
	/* ============= */

	sed_check_xg();
	$avatar = $cfg['defav_dir'].urldecode($id);
	$avatar = str_replace(array("'", ",", chr(0x00)), "", $avatar);
	if (file_exists($avatar))
		{ $sql = sed_sql_query("UPDATE $db_users SET user_avatar='".sed_sql_prep($avatar)."' WHERE user_id='".$usr['id']."'"); }
	header("Location: " . SED_ABSOLUTE_URL . sed_url('users', "m=profile", '#avatar', true));
	exit;

	break;

	/* ============= */
	case 'update':
	/* ============= */

	sed_check_xg();

	/* === Hook === */
	$extp = sed_getextplugins('profile.update.first');
	if (is_array($extp))
		{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
	/* ===== */

	$uav_tmp_name = $_FILES['userfile']['tmp_name'];
	$uav_type = $_FILES['userfile']['type'];
	$uav_name = $_FILES['userfile']['name'];
	$uav_size = $_FILES['userfile']['size'];

	$uph_tmp_name = $_FILES['userphoto']['tmp_name'];
	$uph_type = $_FILES['userphoto']['type'];
	$uph_name = $_FILES['userphoto']['name'];
	$uph_size = $_FILES['userphoto']['size'];

	$usig_tmp_name = $_FILES['usersig']['tmp_name'];
	$usig_type = $_FILES['usersig']['type'];
	$usig_name = $_FILES['usersig']['name'];
	$usig_size = $_FILES['usersig']['size'];

	if (!empty($uav_tmp_name) || !empty($uph_tmp_name) || !empty($usig_tmp_name))
		{ @clearstatcache(); }

	if (!empty($uav_tmp_name) && $uav_size>0)
	{
		$dotpos = mb_strrpos($uav_name,".")+1;
		$f_extension = mb_strtolower(mb_substr($uav_name, $dotpos, 5));
		$fcheck = sed_file_check($uav_tmp_name, $uav_name, $f_extension);
		if($fcheck == 1)
		{
			if (is_uploaded_file($uav_tmp_name) && $uav_size>0 && ($f_extension=='jpeg' || $f_extension=='jpg' || $f_extension=='gif' || $f_extension=='png'))
			{
				list($w, $h) = @getimagesize($uav_tmp_name);

				$avatar = $usr['id']."-avatar.gif";
				$avatarpath = $cfg['av_dir'].$avatar;

				if (file_exists($avatarpath))
					{ unlink($avatarpath); }

				move_uploaded_file($uav_tmp_name, $avatarpath);

				if ($w>$cfg['av_maxx'] || $h>$cfg['av_maxy'] || $uav_size>$cfg['av_maxsize'])
				{
					$prior = ($w>$h) ? 'Width' : 'Height';
					$percentage = 100;

					sed_createthumb($avatarpath, $avatarpath, $cfg['av_maxx'],$cfg['av_maxy'], 1, $f_extension, $avatar, 0, 0, 0, 0, 0, $percentage, $prior);

					while ( ($f_extension == 'jpeg' || $f_extension == 'jpg') && ($uav_size > $cfg['av_maxsize']))
					{
						$percentage -= 5;
						sed_createthumb($avatarpath, $avatarpath, $cfg['av_maxx'],$cfg['av_maxy'], 1, $f_extension, $avatar, 0, 0, 0, 0, 0, $percentage, $prior);

						clearstatcache();
						$uav_size = filesize($avatarpath);
					}
				}

				/* === Hook === */
				$extp = sed_getextplugins('profile.update.avatar');
				if (is_array($extp))
				{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
				/* ===== */

				$uav_size = filesize($avatarpath);
				if ($uav_size<=$cfg['av_maxsize'])
				{
					$sql = sed_sql_query("UPDATE $db_users SET user_avatar='$avatarpath' WHERE user_id='".$usr['id']."'");
					$sql = sed_sql_query("DELETE FROM $db_pfs WHERE pfs_file='$avatar'");
					$sql = sed_sql_query("INSERT INTO $db_pfs (pfs_userid, pfs_file, pfs_extension, pfs_folderid, pfs_desc, pfs_size, pfs_count) VALUES (".(int)$usr['id'].", '$avatar', '$f_extension', -1, '', ".(int)$uav_size.", 0)");
				}
				else
				{
					unlink($avatarpath);
				}
				@chmod($avatarpath, $cfg['file_perms']);
			}
		}
		elseif($fcheck == 2)
		{
			$disp_errors .= sprintf($L['pfs_filemimemissing'], $f_extension);
		}
		else
		{
			$error_string .= sprintf($L['pro_avatarnotvalid'], $f_extension);
		}
	}

	if (!empty($uph_tmp_name) && $uph_size>0)
	{
		$dotpos = mb_strrpos($uph_name,".")+1;
		$f_extension = mb_strtolower(mb_substr($uph_name, $dotpos, 5));
		$fcheck = sed_file_check($uph_tmp_name, $uph_name, $f_extension);
		if($fcheck == 1)
		{
			if (is_uploaded_file($uph_tmp_name) && $uph_size>0 && ($f_extension=='jpeg' || $f_extension=='jpg' || $f_extension=='gif' || $f_extension=='png'))
			{
				list($w, $h) = @getimagesize($uph_tmp_name);

				$photo = $usr['id']."-photo.gif";
				$photopath = $cfg['photos_dir'].$photo;

				if (file_exists($photopath))
				{ unlink($photopath); }

				move_uploaded_file($uph_tmp_name, $photopath);

				if ($w>$cfg['ph_maxx'] || $h>$cfg['ph_maxy'] || $uph_size>$cfg['ph_maxsize'])
				{
					$prior = ($w>$h) ? 'Width' : 'Height';
					$percentage = 100;

					sed_createthumb($photopath, $photopath, $cfg['ph_maxx'],$cfg['ph_maxy'], 1, $f_extension, $photo, 0, 0, 0, 0, 0, $percentage, $prior);

					while ( ($f_extension == 'jpeg' || $f_extension == 'jpg') && ($uph_size > $cfg['ph_maxsize']) )
					{
						$percentage -= 5;
						sed_createthumb($photopath, $photopath, $cfg['ph_maxx'],$cfg['ph_maxy'], 1, $f_extension, $photo, 0, 0, 0, 0, 0, $percentage, $prior);

						clearstatcache();
						$uph_size = filesize($photopath);
					}
				}

				/* === Hook === */
				$extp = sed_getextplugins('profile.update.photo');
				if (is_array($extp))
				{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
				/* ===== */

				$uph_size = filesize($photopath);
				if ($uph_size<=$cfg['ph_maxsize'])
				{
					$sql = sed_sql_query("UPDATE $db_users SET user_photo='$photopath' WHERE user_id='".$usr['id']."'");
					$sql = sed_sql_query("DELETE FROM $db_pfs WHERE pfs_file='$photo'");
					$sql = sed_sql_query("INSERT INTO $db_pfs (pfs_userid, pfs_file, pfs_extension, pfs_folderid, pfs_desc, pfs_size, pfs_count) VALUES (".(int)$usr['id'].", '$photo', '$f_extension', -1, '', ".(int)$uph_size.", 0)");
				}
				else
				{
					unlink($photopath);
				}
				@chmod($photopath, $cfg['file_perms']);
			}
		}
		elseif($fcheck == 2)
		{
			$disp_errors .= sprintf($L['pfs_filemimemissing'], $f_extension);
		}
		else
		{
			$error_string .= sprintf($L['pro_photonotvalid'], $f_extension);
		}
	}

	if (!empty($usig_tmp_name) && $usig_size>0)
	{
		$dotpos = mb_strrpos($usig_name, ".")+1;
		$f_extension = mb_strtolower(mb_substr($usig_name, $dotpos, 5));
		$fcheck = sed_file_check($usig_tmp_name, $usig_name, $f_extension);
		if($fcheck == 1)
		{
			if (is_uploaded_file($usig_tmp_name) && $usig_size>0 && ($f_extension=='jpeg' || $f_extension=='jpg' || $f_extension=='gif' || $f_extension=='png'))
			{
				list($w, $h) = @getimagesize($usig_tmp_name);

				$signature = $usr['id']."-signature.gif";
				$signaturepath = $cfg['sig_dir'].$signature;

				if (file_exists($signaturepath))
				{ unlink($signaturepath); }

				move_uploaded_file($usig_tmp_name, $signaturepath);

				if ($w>$cfg['sig_maxx'] || $h>$cfg['sig_maxy'] || $usig_size>$cfg['sig_maxsize'])
				{
					$prior = ($w>$h) ? 'Width' : 'Height';
					$percentage = 100;

					sed_createthumb($signaturepath, $signaturepath, $cfg['sig_maxx'],$cfg['sig_maxy'], 1, $f_extension, $signature, 0, 0, 0, 0, 0, $percentage, $prior);

					while ( ($f_extension == 'jpeg' || $f_extension == 'jpg') && ($usig_size > $cfg['sig_maxsize']) )
					{
						$percentage -= 5;
						sed_createthumb($signaturepath, $signaturepath, $cfg['sig_maxx'],$cfg['sig_maxy'], 1, $f_extension, $signature, 0, 0, 0, 0, 0, $percentage, $prior);

						clearstatcache();
						$usig_size = filesize($signaturepath);
					}
				}

				/* === Hook === */
				$extp = sed_getextplugins('profile.update.signature');
				if (is_array($extp))
				{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
				/* ===== */

				$usig_size = filesize($signaturepath);
				if ($usig_size<=$cfg['sig_maxsize'])
				{
					$sql = sed_sql_query("UPDATE $db_users SET user_signature='$signaturepath' WHERE user_id='".$usr['id']."'");
					$sql = sed_sql_query("DELETE FROM $db_pfs WHERE pfs_file='$signature'");
					$sql = sed_sql_query("INSERT INTO $db_pfs (pfs_userid, pfs_file, pfs_extension, pfs_folderid, pfs_desc, pfs_size, pfs_count) VALUES (".(int)$usr['id'].", '$signature', '$f_extension', -1, '', ".(int)$usig_size.", 0)");
				}
				else
				{ unlink($signaturepath); }
				@chmod($signaturepath, $cfg['file_perms']);
			}
		}
		elseif($fcheck == 2)
		{
			$disp_errors .= sprintf($L['pfs_filemimemissing'], $f_extension);
		}
		else
		{
			$error_string .= sprintf($L['pro_signotvalid'], $f_extension);
		}
	}

	$rusertext = sed_import('rusertext','P','TXT');
	$rusercountry = sed_import('rusercountry','P','ALP');
	$ruserskin = sed_import('ruserskin','P','TXT');
	$rusertheme = sed_import('rusertheme','P','TXT');
	$ruserlang = sed_import('ruserlang','P','ALP');
	$ruserwebsite = sed_import('ruserwebsite','P','TXT');
	$rusericq = sed_import('rusericq','P','TXT');
	$ruserirc = sed_import('ruserirc','P','TXT');
	$rusermsn = sed_import('rusermsn','P','TXT');
	$rusergender = sed_import('rusergender','P','ALP');
	$ryear = sed_import('ryear','P','INT');
	$rmonth = sed_import('rmonth','P','INT');
	$rday = sed_import('rday','P','INT');
	$rhour = sed_import('rhour','P','INT');
	$rminute = sed_import('rminute','P','INT');
	$rusertimezone = (float) sed_import('rusertimezone','P','TXT',5);
	$ruserlocation = sed_import('ruserlocation','P','TXT');
	$ruseroccupation = sed_import('ruseroccupation','P','TXT');
	$ruseremail = sed_import('ruseremail','P','TXT');
	$ruserhideemail = sed_import('ruserhideemail','P','BOL');
	$ruserpmnotify = sed_import('ruserpmnotify','P','BOL');
	$roldpass = sed_import('roldpass','P','TXT');
	$rnewpass1 = sed_import('rnewpass1','P','TXT');
	$rnewpass2 = sed_import('rnewpass2','P','TXT');
	$rmailpass = sed_import('rmailpass','P','TXT');

	$rusertext = mb_substr($rusertext, 0, $cfg['usertextmax']);

	// Extra fields
	if(count($extrafields)>0)
	foreach($extrafields as $row)
	{
		$import = sed_import('ruser'.$row['field_name'],'P','HTM');
		if($row['field_type'] == 'checkbox')
		{
			$import = $import != '';
		}
		$ruserextrafields[] = $import;
		$urr['user_'.$row[ 'field_name']] = $import;
	}

	$sql = sed_sql_query("SELECT user_skin FROM $db_users WHERE user_id='".$usr['id']."' ");
	$row = sed_sql_fetcharray($sql);

	$rusertheme = ($ruserskin != $row['user_skin']) ? $ruserskin : $rusertheme;

	if (!empty($rnewpass1) && !empty($rnewpass2) && !empty($roldpass))
	{
		$roldpass = sed_import('roldpass','P','PSW');
		$roldpass = md5($roldpass);

		$rnewpass1 = sed_import('rnewpass1','P','PSW');
		$rnewpass2 = sed_import('rnewpass2','P','PSW');

		$sql = sed_sql_query("SELECT user_password FROM $db_users WHERE user_id='".$usr['id']."' ");
		$row = sed_sql_fetcharray($sql);

		$error_string .= ($rnewpass1!=$rnewpass2) ? $L['pro_passdiffer']."<br />" : '';
		$error_string .= (mb_strlen($rnewpass1)<4 || sed_alphaonly($rnewpass1)!=$rnewpass2) ? $L['pro_passtoshort']."<br />" : '';
		$error_string .= ($roldpass!=$row['user_password']) ? $L['pro_wrongpass']."<br />" : '';

		if (!empty($ruseremail) && !empty($rmailpass) && $cfg['useremailchange'] && $ruseremail != $urr['user_email'])
		{
			$error_string .= $L['pro_emailandpass'].'<br />';
		}

		if (empty($error_string))
		{
			$rnewpass = md5($rnewpass1);

			sed_sql_query("UPDATE $db_users SET user_password='$rnewpass' WHERE user_id={$usr['id']}");
		}
	}

	if (!empty($ruseremail) && (!empty($rmailpass) || $cfg['user_email_noprotection']) && $cfg['useremailchange'] && $ruseremail != $urr['user_email'])
	{

		$sqltmp = sed_sql_query("SELECT COUNT(*) FROM $db_users WHERE user_email='".sed_sql_prep($ruseremail)."'");
		$res = sed_sql_result($sqltmp,0,"COUNT(*)");

		if (!$cfg['user_email_noprotection'])
		{
			$rmailpass = md5($rmailpass);
			$error_string .= ($rmailpass!=$urr['user_password']) ? $L['pro_wrongpass']."<br />" : '';
		}
		
		$error_string .= (mb_strlen($ruseremail)<4 || !preg_match('#^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]{2,})+$#i', $ruseremail)) ? $L['aut_emailtooshort']."<br />" : '';
		$error_string .= ($res>0) ? $L['aut_emailalreadyindb']."<br />" : '';

		if (empty($error_string))
		{

			if (!$cfg['user_email_noprotection'])
			{
				$validationkey = md5(microtime());
				$sql = sed_sql_query("UPDATE $db_users SET user_lostpass='$validationkey', user_email='".sed_sql_prep($ruseremail)."', user_maingrp='-1', user_sid='".sed_sql_prep($urr['user_maingrp'])."' WHERE user_id='".$usr['id']."' ");

				$rsubject = $cfg['maintitle']." - ".$L['aut_mailnoticetitle'];
				$ractivate = $cfg['mainurl'].'/'.sed_url('users', 'm=register&a=validate&v='.$validationkey, '', true);
				$rbody = sprintf($L['aut_emailchange'], $usr['name'], $ractivate);
				$rbody .= "\n\n".$L['aut_contactadmin'];
				sed_mail ($ruseremail, $rsubject, $rbody);

				if(!empty($_COOKIE[$sys['site_id']]))
				{
					sed_setcookie($sys['site_id'], '', time()-63072000, $cfg['cookiepath'], $cfg['cookiedomain'], $sys['secure'], true);
				}

				if (!empty($_SESSION[$sys['site_id']]))
				{
					session_unset();
					session_destroy();
				}

				$sql = sed_sql_query("DELETE FROM $db_online WHERE online_ip='{$usr['ip']}'");
				sed_redirect(sed_url('message', 'msg=102', '', true));
				exit;

			}
			else
			{
				$sql = sed_sql_query("UPDATE $db_users SET user_email='".sed_sql_prep($ruseremail)."' WHERE user_id=".$usr['id']);
			}
		}
	}


	if (empty($error_string))
	{
		if ($rmonth=='x' || $rday=='x' || $ryear=='x' || empty($rmonth) || empty($rday) || empty($ryear))
		{
			$ruserbirthdate = '0000-00-00';
		}
		else
		{
			$bdate = sed_mktime(1, 0, 0, $rmonth, $rday, $ryear);
			if ($bdate > $sys['now_offset'])
			{
				$bdate = sed_mktime(1, 0, 0, $rmonth, $rday, date('Y', $sys['now_offset']) - 1);
			}
			$ruserbirthdate = sed_stamp2date($bdate);
		}
		// Extra fields
		if(count($extrafields)>0)
		{
			foreach($extrafields as $i=>$row)
			{
				if(!is_null($ruserextrafields[$i]))
				{
					$ssql_extra .= "user_".$row['field_name']." = '".sed_sql_prep($ruserextrafields[$i])."',";
				}
			}
		}
		$ssql = "UPDATE $db_users SET
			user_text='".sed_sql_prep($rusertext)."',
			user_country='".sed_sql_prep($rusercountry)."',
			user_skin='".sed_sql_prep($ruserskin)."',
			user_theme='".sed_sql_prep($rusertheme)."',
			user_lang='".sed_sql_prep($ruserlang)."',
			user_website='".sed_sql_prep($ruserwebsite)."',
			user_icq='".sed_sql_prep($rusericq)."',
			user_msn='".sed_sql_prep($rusermsn)."',
			user_irc='".sed_sql_prep($ruserirc)."',
			user_gender='".sed_sql_prep($rusergender)."',
			user_birthdate='".sed_sql_prep($ruserbirthdate)."',
			user_timezone='".sed_sql_prep($rusertimezone)."',
			user_location='".sed_sql_prep($ruserlocation)."',
			user_occupation='".sed_sql_prep($ruseroccupation)."',
			user_hideemail='$ruserhideemail',
			user_pmnotify='$ruserpmnotify',
			".$ssql_extra."
			user_auth=''
			WHERE user_id='".$usr['id']."'";
		$sql = sed_sql_query($ssql);

		/* === Hook === */
		$extp = sed_getextplugins('profile.update.done');
		if (is_array($extp))
			{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
		/* ===== */

		header("Location: " . SED_ABSOLUTE_URL . sed_url('users', 'm=profile'));
		exit;
	}
	break;

	/* ============= */
	default:
	/* ============= */

	break;

}

$profile_form_skins .= sed_selectbox_skin($urr['user_skin'], 'ruserskin');
$profile_form_themes .= sed_selectbox_theme($urr['user_skin'], 'rusertheme', $urr['user_theme']);
$profile_form_langs .= sed_selectbox_lang($urr['user_lang'], 'ruserlang');

$timezonelist = array ('-12', '-11', '-10', '-09', '-08', '-07', '-06', '-05', '-04', '-03',  '-03.5', '-02', '-01', '+00', '+01', '+02', '+03', '+03.5', '+04', '+04.5', '+05', '+05.5', '+06', '+07', '+08', '+09', '+09.5', '+10', '+11', '+12');

$profile_form_timezone = "<select name=\"rusertimezone\" size=\"1\">";
foreach($timezonelist as $x)
{
	$f = (float) $x;
	$selected = ($x==$urr['user_timezone']) ? "selected=\"selected\"" : '';
	$profile_form_timezone .= "<option value=\"$f\" $selected>GMT ".$x.", ".date($cfg['dateformat'], $sys['now_offset'] + $x*3600)."</option>";
}
$profile_form_timezone .= "</select> ".$usr['gmttime']." / ".date($cfg['dateformat'], $sys['now_offset'] + $usr['timezone']*3600)." ".$usr['timetext'];

$profile_form_countries = sed_selectbox_countries($urr['user_country'], 'rusercountry');
$profile_form_gender = sed_selectbox_gender($urr['user_gender'] ,'rusergender');
$profile_form_birthdate = sed_selectbox_date($urr['user_birthdate'], 'short', '', date('Y', $sys['now_offset']));
$profile_form_email = ($cfg['useremailchange']) ? "<input type=\"text\" class=\"text\" name=\"ruseremail\" value=\"".htmlspecialchars($urr['user_email'])."\" size=\"32\" maxlength=\"64\" />" : "<input type=\"text\" class=\"text\" name=\"ruseremail\" value=\"".htmlspecialchars($urr['user_email'])."\" size=\"32\" maxlength=\"64\" disabled=\"disabled\" />";

$profile_form_avatar .= (!empty($urr['user_avatar'])) ? "<img src=\"".$urr['user_avatar']."\" alt=\"\" /><br />".$L['Delete']." [<a href=\"" .sed_url('users', 'm=profile&a=avatardelete&'.sed_xg())."\">x</a>]<br />&nbsp;<br />" : '';
$profile_form_avatar .= $L['pro_avatarsupload']." (".$cfg['av_maxx']."x".$cfg['av_maxy']."x".$cfg['av_maxsize'].$L['b'].")<br />";
$profile_form_avatar .= "<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"".($cfg['av_maxsize']*1024)."\" />";
$profile_form_avatar .= "<input name=\"userfile\" type=\"file\" class=\"file\" size=\"24\" /><br />";

$profile_form_photo .= (!empty($urr['user_photo'])) ? "<img src=\"".$urr['user_photo']."\" alt=\"\" /> ".$L['Delete']." [<a href=\"".sed_url('users', 'm=profile&a=phdelete&'.sed_xg())."\">x</a>]" : '';
$profile_form_photo .= $L['pro_photoupload']." (".$cfg['ph_maxx']."x".$cfg['ph_maxy']."x".$cfg['ph_maxsize'].$L['b'].")<br />";
$profile_form_photo .= "<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"".($cfg['ph_maxsize']*1024)."\" />";
$profile_form_photo .= "<input name=\"userphoto\" type=\"file\" class=\"file\" size=\"24\" /><br />";

$profile_form_signature .= (!empty($urr['user_signature'])) ? "<img src=\"".$urr['user_signature']."\" alt=\"\" /> ".$L['Delete']." [<a href=\"".sed_url('users', 'm=profile&a=sigdelete&'.sed_xg())."\">x</a>]" : '';
$profile_form_signature .= $L['pro_sigupload']." (".$cfg['sig_maxx']."x".$cfg['sig_maxy']."x".$cfg['sig_maxsize'].$L['b'].")<br />";
$profile_form_signature .= "<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"".($cfg['sig_maxsize']*1024)."\" />";
$profile_form_signature .= "<input name=\"usersig\" type=\"file\" class=\"file\" size=\"24\" /><br />";

if ($a=='avatarchoose')
{
	sed_check_xg();
	$profile_form_avatar .=  "<a name=\"list\" id=\"list\"></a><h4>".$L['pro_avatarschoose']." :</h4>";
	$handle = opendir($cfg['defav_dir']);
	while ($f = readdir($handle))
	{
		if ($f != "." && $f != "..")
			{ $profile_form_avatar .= "<a href=\"".sed_url('users', 'm=profile&a=avatarselect&'.sed_xg().'&id='.urlencode($f), '#avatar')."\"><img src=\"".$cfg['defav_dir'].$f."\" alt=\"\" /></a> "; }
	}
	closedir($handle);
}
else
	{ $profile_form_avatar .= "<a href=\"".sed_url('users', 'm=profile&a=avatarchoose&'.sed_xg(), '#list')."\">".$L['pro_avatarspreset']."</a>"; }

$profile_form_pmnotify = ($urr['user_pmnotify']) ? "<input type=\"radio\" class=\"radio\" name=\"ruserpmnotify\" value=\"1\" checked=\"checked\" />".$L['Yes']." <input type=\"radio\" class=\"radio\" name=\"ruserpmnotify\" value=\"0\" />".$L['No'] : "<input type=\"radio\" class=\"radio\" name=\"ruserpmnotify\" value=\"1\" />".$L['Yes']." <input type=\"radio\" class=\"radio\" name=\"ruserpmnotify\" value=\"0\" checked=\"checked\" />".$L['No'];

$profile_form_hideemail = ($urr['user_hideemail']) ? "<input type=\"radio\" class=\"radio\" name=\"ruserhideemail\" value=\"1\" checked=\"checked\" />".$L['Yes']." <input type=\"radio\" class=\"radio\" name=\"ruserhideemail\" value=\"0\" />".$L['No'] : "<input type=\"radio\" class=\"radio\" name=\"ruserhideemail\" value=\"1\" />".$L['Yes']." <input type=\"radio\" class=\"radio\" name=\"ruserhideemail\" value=\"0\" checked=\"checked\" />".$L['No'];

$title_tags[] = array('{PROFILE}', '{NAME}');
$title_tags[] = array('%1$s', '%2$s');
$title_data = array($L['Profile'], htmlspecialchars($urr['user_name']));
$out['subtitle'] = sed_title('title_users_profile', $title_tags, $title_data);

/* === Hook === */
$extp = sed_getextplugins('profile.main');
if (is_array($extp))
	{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

require_once $cfg['system_dir'] . '/header.php';

$mskin = sed_skinfile(array('users', 'profile'));
$t = new XTemplate($mskin);

if (!empty($error_string))
{
	$t->assign("USERS_PROFILE_ERROR_BODY",$error_string);
	$t->parse("MAIN.USERS_PROFILE_ERROR");
}

$editor_class = $cfg['parsebbcodeusertext'] ? 'class="minieditor"' : '';

$useredit_array = array(
	"USERS_PROFILE_TITLE" => "<a href=\"".sed_url('users', 'm=profile')."\">".$L['pro_title']."</a>",
	"USERS_PROFILE_SUBTITLE" => $L['pro_subtitle'],
	"USERS_PROFILE_FORM_SEND" => sed_url('users', "m=profile&a=update&".sed_xg()),
	"USERS_PROFILE_ID" => $urr['user_id'],
	"USERS_PROFILE_NAME" => htmlspecialchars($urr['user_name']),
	"USERS_PROFILE_MAINGRP" => sed_build_group($urr['user_maingrp']),
	"USERS_PROFILE_GROUPS" => sed_build_groupsms($urr['user_id'], FALSE, $urr['user_maingrp']),
	"USERS_PROFILE_COUNTRY" => $profile_form_countries,
	"USERS_PROFILE_AVATAR" => $profile_form_avatar,
	"USERS_PROFILE_PHOTO" => $profile_form_photo,
	"USERS_PROFILE_SIGNATURE" => $profile_form_signature,
	"USERS_PROFILE_TEXT" => "<textarea $editor_class name=\"rusertext\" rows=\"8\" cols=\"56\">".htmlspecialchars($urr['user_text'])."</textarea>",
	"USERS_PROFILE_TEXTBOXER" => "<textarea $editor_class name=\"rusertext\" rows=\"8\" cols=\"56\">".htmlspecialchars($urr['user_text'])."</textarea>",
	"USERS_PROFILE_EMAIL" => $profile_form_email,
	"USERS_PROFILE_EMAILPASS" => "<input type=\"password\" class=\"password\" name=\"rmailpass\" size=\"12\" maxlength=\"16\" />",
	"USERS_PROFILE_HIDEEMAIL" => $profile_form_hideemail,
	"USERS_PROFILE_PMNOTIFY" => $profile_form_pmnotify,
	"USERS_PROFILE_WEBSITE" => "<input type=\"text\" class=\"text\" name=\"ruserwebsite\" value=\"".$urr['user_website']."\" size=\"56\" maxlength=\"128\" />",
	"USERS_PROFILE_SKIN" => $profile_form_skins,
	"USERS_PROFILE_THEME" => $profile_form_themes,
	"USERS_PROFILE_LANG" => $profile_form_langs,
	"USERS_PROFILE_ICQ" => "<input type=\"text\" class=\"text\" name=\"rusericq\" value=\"".htmlspecialchars($urr['user_icq'])."\" size=\"32\" maxlength=\"16\" />",
	"USERS_PROFILE_MSN" => "<input type=\"text\" class=\"text\" name=\"rusermsn\" value=\"".htmlspecialchars($urr['user_msn'])."\" size=\"32\" maxlength=\"64\" />",
	"USERS_PROFILE_IRC" => "<input type=\"text\" class=\"text\" name=\"ruserirc\" value=\"".htmlspecialchars($urr['user_irc'])."\" size=\"56\" maxlength=\"128\" />",
	"USERS_PROFILE_GENDER" => $profile_form_gender,
	"USERS_PROFILE_BIRTHDATE" => $profile_form_birthdate,
	"USERS_PROFILE_TIMEZONE" => $profile_form_timezone,
	"USERS_PROFILE_LOCATION" => "<input type=\"text\" class=\"text\" name=\"ruserlocation\" value=\"".htmlspecialchars($urr['user_location'])."\" size=\"32\" maxlength=\"64\" />",
	"USERS_PROFILE_OCCUPATION" => "<input type=\"text\" class=\"text\" name=\"ruseroccupation\" value=\"".htmlspecialchars($urr['user_occupation'])."\" size=\"32\" maxlength=\"64\" />",
	"USERS_PROFILE_REGDATE" => @date($cfg['dateformat'], $urr['user_regdate'] + $usr['timezone'] * 3600)." ".$usr['timetext'],
	"USERS_PROFILE_LASTLOG" => @date($cfg['dateformat'], $urr['user_lastlog'] + $usr['timezone'] * 3600)." ".$usr['timetext'],
	"USERS_PROFILE_LOGCOUNT" => $urr['user_logcount'],
	"USERS_PROFILE_ADMINRIGHTS" => '',
	"USERS_PROFILE_OLDPASS" => "<input type=\"password\" class=\"password\" name=\"roldpass\" size=\"12\" maxlength=\"16\" />",
	"USERS_PROFILE_NEWPASS1" => "<input type=\"password\" class=\"password\" name=\"rnewpass1\" size=\"12\" maxlength=\"16\" />",
	"USERS_PROFILE_NEWPASS2" => "<input type=\"password\" class=\"password\" name=\"rnewpass2\" size=\"12\" maxlength=\"16\" />",
);
// Extra fields
if(count($extrafields)>0)
{
	$extra_array = sed_build_extrafields('user', 'USERS_PROFILE', $extrafields, $urr);
	$useredit_array = $useredit_array + $extra_array;
}
$t->assign($useredit_array);
/* === Hook === */
$extp = sed_getextplugins('profile.tags');
if (is_array($extp))
	{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
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