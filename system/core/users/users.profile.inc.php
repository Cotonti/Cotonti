<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
[BEGIN_SED]
File=users.profile.inc.php
Version=125
Updated=2008-mar-20
Type=Core
Author=Neocrome
Description=User profile
[END_SED]
==================== */

if (!defined('SED_CODE')) { die('Wrong URL.'); }

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

$profile_form_avatar = "<a name=\"avatar\" id=\"avatar\"></a>";
$profile_form_photo = "<a name=\"photo\" id=\"photo\"></a>";
$profile_form_signature = "<a name=\"signature\" id=\"signature\"></a>";

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

		if (is_uploaded_file($uav_tmp_name) && $uav_size>0 && $uav_size<=$cfg['av_maxsize'] && ($f_extension=='jpeg' || $f_extension=='jpg' || $f_extension=='gif' || $f_extension=='png'))
			{
			list($w, $h) = @getimagesize($uav_tmp_name);
			if ($w<=$cfg['av_maxx'] && $h<=$cfg['av_maxy'] )
				{
				$avatar = $usr['id']."-avatar.gif";
				$avatarpath = $cfg['av_dir'].$avatar;

				if (file_exists($avatarpath))
					{ unlink($avatarpath); }

				move_uploaded_file($uav_tmp_name, $avatarpath);

				/* === Hook === */
				$extp = sed_getextplugins('profile.update.avatar');
				if (is_array($extp))
				{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
				/* ===== */

				$uav_size = filesize($avatarpath);
				$sql = sed_sql_query("UPDATE $db_users SET user_avatar='$avatarpath' WHERE user_id='".$usr['id']."'");
				$sql = sed_sql_query("DELETE FROM $db_pfs WHERE pfs_file='$avatar'");
				$sql = sed_sql_query("INSERT INTO $db_pfs (pfs_userid, pfs_file, pfs_extension, pfs_folderid, pfs_desc, pfs_size, pfs_count) VALUES (".(int)$usr['id'].", '$avatar', '$f_extension', -1, '', ".(int)$uav_size.", 0)");
				@chmod($avatarpath, 0666);
				}
			}
		}

	if (!empty($uph_tmp_name) && $uph_size>0)
		{
		$dotpos = mb_strrpos($uph_name,".")+1;
		$f_extension = mb_strtolower(mb_substr($uph_name, $dotpos, 5));

		if (is_uploaded_file($uph_tmp_name) && $uph_size>0 && $uph_size<=$cfg['ph_maxsize'] && ($f_extension=='jpeg' || $f_extension=='jpg' || $f_extension=='gif' || $f_extension=='png'))
			{
			list($w, $h) = @getimagesize($uph_tmp_name);
			if ($w<=$cfg['ph_maxx'] && $h<=$cfg['ph_maxy'] )
				{
				$photo = $usr['id']."-photo.gif";
				$photopath = $cfg['photos_dir'].$photo;

				if (file_exists($photopath))
					{ unlink($photopath); }

				move_uploaded_file($uph_tmp_name, $photopath);

				/* === Hook === */
				$extp = sed_getextplugins('profile.update.photo');
				if (is_array($extp))
				{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
				/* ===== */

				$uph_size = filesize($photopath);
				$sql = sed_sql_query("UPDATE $db_users SET user_photo='$photopath' WHERE user_id='".$usr['id']."'");
				$sql = sed_sql_query("DELETE FROM $db_pfs WHERE pfs_file='$photo'");
				$sql = sed_sql_query("INSERT INTO $db_pfs (pfs_userid, pfs_file, pfs_extension, pfs_folderid, pfs_desc, pfs_size, pfs_count) VALUES (".(int)$usr['id'].", '$photo', '$f_extension', -1, '', ".(int)$uph_size.", 0)");
				@chmod($photopath, 0666);
				}
			}
		}

	if (!empty($usig_tmp_name) && $usig_size>0)
		{
		$dotpos = mb_strrpos($usig_name, ".")+1;
		$f_extension = mb_strtolower(mb_substr($usig_name, $dotpos, 5));

		if (is_uploaded_file($usig_tmp_name) && $usig_size>0 && $usig_size<=$cfg['sig_maxsize'] && ($f_extension=='jpeg' || $f_extension=='jpg' || $f_extension=='gif' || $f_extension=='png'))
			{
			list($w, $h) = @getimagesize($usig_tmp_name);
			if ($w<=$cfg['sig_maxx'] && $h<=$cfg['sig_maxy'] )
				{
				$signature = $usr['id']."-signature.gif";
				$signaturepath = $cfg['sig_dir'].$signature;

				if (file_exists($signaturepath))
					{ unlink($signaturepath); }

				move_uploaded_file($usig_tmp_name, $signaturepath);

				/* === Hook === */
				$extp = sed_getextplugins('profile.update.signature');
				if (is_array($extp))
				{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
				/* ===== */

				$usig_size = filesize($signaturepath);
				$sql = sed_sql_query("UPDATE $db_users SET user_signature='$signaturepath' WHERE user_id='".$usr['id']."'");
				$sql = sed_sql_query("DELETE FROM $db_pfs WHERE pfs_file='$signature'");
				$sql = sed_sql_query("INSERT INTO $db_pfs (pfs_userid, pfs_file, pfs_extension, pfs_folderid, pfs_desc, pfs_size, pfs_count) VALUES (".(int)$usr['id'].", '$signature', '$f_extension', -1, '', ".(int)$usig_size.", 0)");
				@chmod($signaturepath, 0666);
				}
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
	$rusertimezone = sed_import('rusertimezone','P','TXT',5);
	$ruserlocation = sed_import('ruserlocation','P','TXT');
	$ruseroccupation = sed_import('ruseroccupation','P','TXT');
	$ruseremail = sed_import('ruseremail','P','TXT');
	$ruserhideemail = sed_import('ruserhideemail','P','BOL');
	$ruserpmnotify = sed_import('ruserpmnotify','P','BOL');
	$ruserextra1 = sed_import('ruserextra1','P','TXT');
	$ruserextra2 = sed_import('ruserextra2','P','TXT');
  	$ruserextra3 = sed_import('ruserextra3','P','TXT');
  	$ruserextra4 = sed_import('ruserextra4','P','TXT');
	$ruserextra5 = sed_import('ruserextra5','P','TXT');
	$ruserextra6 = sed_import('ruserextra6','P','HTM');
  	$ruserextra7 = sed_import('ruserextra7','P','HTM');
	$ruserextra8 = sed_import('ruserextra8','P','HTM');
	$ruserextra9 = sed_import('ruserextra9','P','HTM');
	$ruserextra1_p = sed_import('ruserextra1_p','P','BOL');
	$ruserextra2_p = sed_import('ruserextra2_p','P','BOL');
  	$ruserextra3_p = sed_import('ruserextra3_p','P','BOL');
  	$ruserextra4_p = sed_import('ruserextra4_p','P','BOL');
	$ruserextra5_p = sed_import('ruserextra5_p','P','BOL');
	$ruserextra6_p = sed_import('ruserextra6_p','P','BOL');
  	$ruserextra7_p = sed_import('ruserextra7_p','P','BOL');
	$ruserextra8_p = sed_import('ruserextra8_p','P','BOL');
	$ruserextra9_p = sed_import('ruserextra9_p','P','BOL');
	$roldpass = sed_import('roldpass','P','TXT');
	$rnewpass1 = sed_import('rnewpass1','P','TXT');
	$rnewpass2 = sed_import('rnewpass2','P','TXT');

	$rusertext = mb_substr($rusertext, 0, $cfg['usertextmax']);

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

		if (empty($error_string))
			{
			$rnewpass = md5($rnewpass1);
			$sql = sed_sql_query("UPDATE $db_users SET user_password='$rnewpass' WHERE user_id='".$usr['id']."'");

			if ($cfg['authmode']==1 || $cfg['authmode']==3)
				{
				$u = base64_encode($usr['id'].":_:$rnewpass:_:".$ruserskin.":_:".$rusertheme);
				setcookie("SEDITIO", "$u", time()+63072000, $cfg['cookiepath'], $cfg['cookiedomain']);
				}

			if ($cfg['authmode']==2 || $cfg['authmode']==3)
				{
				$_SESSION['rseditiop'] = $rnewpass;
				}
			}
		}

	if (empty($error_string))
		{
		$ruserextra1 = ($cfg['extra1uchange'] && $ruserextra1_p) ? mb_substr($ruserextra1,0,$cfg['extra1tsetting']) : $urr['user_extra1'];
		$ruserextra2 = ($cfg['extra2uchange'] && $ruserextra2_p) ? mb_substr($ruserextra2,0,$cfg['extra2tsetting']) : $urr['user_extra2'];
		$ruserextra3 = ($cfg['extra3uchange'] && $ruserextra3_p) ? mb_substr($ruserextra3,0,$cfg['extra3tsetting']) : $urr['user_extra3'];
		$ruserextra4 = ($cfg['extra4uchange'] && $ruserextra4_p) ? mb_substr($ruserextra4,0,$cfg['extra4tsetting']) : $urr['user_extra4'];
		$ruserextra5 = ($cfg['extra5uchange'] && $ruserextra5_p) ? mb_substr($ruserextra5,0,$cfg['extra5tsetting']) : $urr['user_extra5'];
		$ruserextra6 = ($cfg['extra6uchange'] && $ruserextra6_p) ? $ruserextra6 : $urr['user_extra6'];
		$ruserextra7 = ($cfg['extra7uchange'] && $ruserextra7_p) ? $ruserextra7 : $urr['user_extra7'];
		$ruserextra8 = ($cfg['extra8uchange'] && $ruserextra8_p) ? $ruserextra8 : $urr['user_extra8'];
		$ruserextra9 = ($cfg['extra9uchange'] && $ruserextra9_p) ? $ruserextra9 : $urr['user_extra9'];

		$ruserbirthdate = ($rmonth==0 || $rday ==0 || $ryear==0) ? 0 : sed_mktime(1, 0, 0, $rmonth, $rday, $ryear);

		if (!$cfg['useremailchange'])
			{ $ruseremail = $urr['user_email']; }

		$sql = sed_sql_query("UPDATE $db_users SET
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
			user_email='".sed_sql_prep($ruseremail)."',
			user_hideemail='$ruserhideemail',
			user_pmnotify='$ruserpmnotify',
			user_extra1='".sed_sql_prep($ruserextra1)."',
			user_extra2='".sed_sql_prep($ruserextra2)."',
			user_extra3='".sed_sql_prep($ruserextra3)."',
			user_extra4='".sed_sql_prep($ruserextra4)."',
			user_extra5='".sed_sql_prep($ruserextra5)."',
			user_extra6='".sed_sql_prep($ruserextra6)."',
			user_extra7='".sed_sql_prep($ruserextra7)."',
			user_extra8='".sed_sql_prep($ruserextra8)."',
			user_extra9='".sed_sql_prep($ruserextra9)."',
			user_auth=''
			WHERE user_id='".$usr['id']."'");

		/* === Hook === */
		$extp = sed_getextplugins('profile.update.done');
		if (is_array($extp))
			{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
		/* ===== */

		header("Location: " . SED_ABSOLUTE_URL . sed_url('message', "msg=113", '', true));
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
while( list($i,$x) = each($timezonelist) )
	{
	$selected = ($x==$urr['user_timezone']) ? "selected=\"selected\"" : '';
	$profile_form_timezone .= "<option value=\"$x\" $selected>GMT".$x."</option>";
	}
$profile_form_timezone .= "</select> ".$usr['gmttime']." / ".date($cfg['dateformat'], $sys['now_offset'] + $usr['timezone']*3600)." ".$usr['timetext'];

$profile_form_countries = sed_selectbox_countries($urr['user_country'], 'rusercountry');
$profile_form_gender = sed_selectbox_gender($urr['user_gender'] ,'rusergender');
$profile_form_birthdate = sed_selectbox_date($urr['user_birthdate'], 'short');
$profile_form_email = ($cfg['useremailchange']) ? "<input type=\"text\" class=\"text\" name=\"ruseremail\" value=\"".sed_cc($urr['user_email'])."\" size=\"32\" maxlength=\"64\" />" : sed_cc($urr['user_email']);

$profile_form_avatar .= (!empty($urr['user_avatar'])) ? "<img src=\"".$urr['user_avatar']."\" alt=\"\" /><br />".$L['Delete']." [<a href=\"users.php?m=profile&amp;a=avatardelete&amp;".sed_xg()."\">x</a>]<br />&nbsp;<br />" : '';
$profile_form_avatar .= $L['pro_avatarsupload']." (".$cfg['av_maxx']."x".$cfg['av_maxy']."x".$cfg['av_maxsize'].$L['b'].")<br />";
$profile_form_avatar .= "<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"".($cfg['av_maxsize']*1024)."\" />";
$profile_form_avatar .= "<input name=\"userfile\" type=\"file\" class=\"file\" size=\"24\" /><br />";

$profile_form_photo .= (!empty($urr['user_photo'])) ? "<img src=\"".$urr['user_photo']."\" alt=\"\" /> ".$L['Delete']." [<a href=\"users.php?m=profile&amp;a=phdelete&amp;".sed_xg()."\">x</a>]" : '';
$profile_form_photo .= $L['pro_photoupload']." (".$cfg['ph_maxx']."x".$cfg['ph_maxy']."x".$cfg['ph_maxsize'].$L['b'].")<br />";
$profile_form_photo .= "<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"".($cfg['ph_maxsize']*1024)."\" />";
$profile_form_photo .= "<input name=\"userphoto\" type=\"file\" class=\"file\" size=\"24\" /><br />";

$profile_form_signature .= (!empty($urr['user_signature'])) ? "<img src=\"".$urr['user_signature']."\" alt=\"\" /> ".$L['Delete']." [<a href=\"users.php?m=profile&amp;a=sigdelete&amp;".sed_xg()."\">x</a>]" : '';
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
			{ $profile_form_avatar .= "<a href=\"users.php?m=profile&amp;a=avatarselect&amp;".sed_xg()."&amp;id=".urlencode($f)."#avatar\"><img src=\"".$cfg['defav_dir'].$f."\" alt=\"\" /></a> "; }
		}
	closedir($handle);
	}
else
	{ $profile_form_avatar .= "<a href=\"users.php?m=profile&amp;a=avatarchoose&amp;".sed_xg()."#list\">".$L['pro_avatarspreset']."</a>"; }

$user_form_extra1 = ($cfg['extra1uchange']) ? "<input type=\"text\" class=\"text\" name=\"ruserextra1\" value=\"".sed_cc($urr['user_extra1'])."\" size=\"32\" maxlength=\"".$cfg['extra1tsetting']."\" /><input type=\"hidden\" name=\"ruserextra1_p\" value=\"1\" />" : $urr['user_extra1'];
$user_form_extra2 = ($cfg['extra2uchange']) ? "<input type=\"text\" class=\"text\" name=\"ruserextra2\" value=\"".sed_cc($urr['user_extra2'])."\" size=\"32\" maxlength=\"".$cfg['extra2tsetting']."\" /><input type=\"hidden\" name=\"ruserextra2_p\" value=\"1\" />" : $urr['user_extra2'];
$user_form_extra3 = ($cfg['extra3uchange']) ? "<input type=\"text\" class=\"text\" name=\"ruserextra3\" value=\"".sed_cc($urr['user_extra3'])."\" size=\"32\" maxlength=\"".$cfg['extra3tsetting']."\" /><input type=\"hidden\" name=\"ruserextra3_p\" value=\"1\" />" : $urr['user_extra3'];
$user_form_extra4 = ($cfg['extra4uchange']) ? "<input type=\"text\" class=\"text\"name=\"ruserextra4\" value=\"".sed_cc($urr['user_extra4'])."\" size=\"32\" maxlength=\"".$cfg['extra4tsetting']."\" /><input type=\"hidden\" name=\"ruserextra4_p\" value=\"1\" />" : $urr['user_extra4'];
$user_form_extra5 = ($cfg['extra5uchange']) ? "<input type=\"text\" class=\"text\" name=\"ruserextra5\" value=\"".sed_cc($urr['user_extra5'])."\" size=\"32\" maxlength=\"".$cfg['extra5tsetting']."\" /><input type=\"hidden\" name=\"ruserextra5_p\" value=\"1\" />" : $urr['user_extra5'];
$user_form_extra6 = ($cfg['extra6uchange']) ?  sed_selectbox($urr['user_extra6'], 'ruserextra6', $cfg['extra6tsetting'])."<input type=\"hidden\" name=\"ruserextra6_p\" value=\"1\" />" : $urr['user_extra6'];
$user_form_extra7 = ($cfg['extra7uchange']) ?  sed_selectbox($urr['user_extra7'], 'ruserextra7', $cfg['extra7tsetting'])."<input type=\"hidden\" name=\"ruserextra7_p\" value=\"1\" />" : $urr['user_extra7'];
$user_form_extra8 = ($cfg['extra8uchange']) ?  sed_selectbox($urr['user_extra8'], 'ruserextra8', $cfg['extra8tsetting'])."<input type=\"hidden\" name=\"ruserextra8_p\" value=\"1\" />" : $urr['user_extra8'];
$user_form_extra9 = ($cfg['extra9uchange']) ? "<textarea name=\"ruserextra9\" rows=\"4\" cols=\"56\">".sed_cc($urr['user_extra9'])."</textarea><input type=\"hidden\" name=\"ruserextra9_p\" value=\"1\" />" : $urr['user_extra9'];

$profile_form_pmnotify = ($urr['user_pmnotify']) ? "<input type=\"radio\" class=\"radio\" name=\"ruserpmnotify\" value=\"1\" checked=\"checked\" />".$L['Yes']." <input type=\"radio\" class=\"radio\" name=\"ruserpmnotify\" value=\"0\" />".$L['No'] : "<input type=\"radio\" class=\"radio\" name=\"ruserpmnotify\" value=\"1\" />".$L['Yes']." <input type=\"radio\" class=\"radio\" name=\"ruserpmnotify\" value=\"0\" checked=\"checked\" />".$L['No'];

$profile_form_hideemail = ($urr['user_hideemail']) ? "<input type=\"radio\" class=\"radio\" name=\"ruserhideemail\" value=\"1\" checked=\"checked\" />".$L['Yes']." <input type=\"radio\" class=\"radio\" name=\"ruserhideemail\" value=\"0\" />".$L['No'] : "<input type=\"radio\" class=\"radio\" name=\"ruserhideemail\" value=\"1\" />".$L['Yes']." <input type=\"radio\" class=\"radio\" name=\"ruserhideemail\" value=\"0\" checked=\"checked\" />".$L['No'];

$out['subtitle'] = $L['Profile'];

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

$t->assign(array(
	"USERS_PROFILE_TITLE" => "<a href=\"users.php?m=profile\">".$L['pro_title']."</a>",
	"USERS_PROFILE_SUBTITLE" => $L['pro_subtitle'],
	"USERS_PROFILE_FORM_SEND" => "users.php?m=profile&amp;a=update&amp;".sed_xg(),
	"USERS_PROFILE_ID" => $urr['user_id'],
	"USERS_PROFILE_NAME" => sed_cc($urr['user_name']),
	"USERS_PROFILE_MAINGRP" => sed_build_group($urr['user_maingrp']),
	"USERS_PROFILE_GROUPS" => sed_build_groupsms($urr['user_id'], FALSE, $urr['user_maingrp']),
	"USERS_PROFILE_COUNTRY" => $profile_form_countries,
	"USERS_PROFILE_AVATAR" => $profile_form_avatar,
	"USERS_PROFILE_PHOTO" => $profile_form_photo,
	"USERS_PROFILE_SIGNATURE" => $profile_form_signature,
	"USERS_PROFILE_EXTRA1" => $user_form_extra1,
	"USERS_PROFILE_EXTRA2" => $user_form_extra2,
	"USERS_PROFILE_EXTRA3" => $user_form_extra3,
	"USERS_PROFILE_EXTRA4" => $user_form_extra4,
	"USERS_PROFILE_EXTRA5" => $user_form_extra5,
	"USERS_PROFILE_EXTRA6" => $user_form_extra6,
	"USERS_PROFILE_EXTRA7" => $user_form_extra7,
	"USERS_PROFILE_EXTRA8" => $user_form_extra8,
	"USERS_PROFILE_EXTRA9" => $user_form_extra9,
	"USERS_PROFILE_EXTRA1_TITLE" => $cfg['extra1title'],
	"USERS_PROFILE_EXTRA2_TITLE" => $cfg['extra2title'],
	"USERS_PROFILE_EXTRA3_TITLE" => $cfg['extra3title'],
	"USERS_PROFILE_EXTRA4_TITLE" => $cfg['extra4title'],
	"USERS_PROFILE_EXTRA5_TITLE" => $cfg['extra5title'],
	"USERS_PROFILE_EXTRA6_TITLE" => $cfg['extra6title'],
	"USERS_PROFILE_EXTRA7_TITLE" => $cfg['extra7title'],
	"USERS_PROFILE_EXTRA8_TITLE" => $cfg['extra8title'],
	"USERS_PROFILE_EXTRA9_TITLE" => $cfg['extra9title'],
	"USERS_PROFILE_TEXT" => "<textarea class=\"editor\" name=\"rusertext\" rows=\"8\" cols=\"56\">".sed_cc($urr['user_text'])."</textarea>",
	"USERS_PROFILE_TEXTBOXER" => "<textarea class=\"editor\" name=\"rusertext\" rows=\"8\" cols=\"56\">".sed_cc($urr['user_text'])."</textarea>",
	"USERS_PROFILE_EMAIL" => $profile_form_email,
	"USERS_PROFILE_HIDEEMAIL" => $profile_form_hideemail,
	"USERS_PROFILE_PMNOTIFY" => $profile_form_pmnotify,
	"USERS_PROFILE_WEBSITE" => "<input type=\"text\" class=\"text\" name=\"ruserwebsite\" value=\"".$urr['user_website']."\" size=\"56\" maxlength=\"128\" />",
	"USERS_PROFILE_SKIN" => $profile_form_skins,
	"USERS_PROFILE_THEME" => $profile_form_themes,
	"USERS_PROFILE_LANG" => $profile_form_langs,
	"USERS_PROFILE_ICQ" => "<input type=\"text\" class=\"text\" name=\"rusericq\" value=\"".sed_cc($urr['user_icq'])."\" size=\"32\" maxlength=\"16\" />",
	"USERS_PROFILE_MSN" => "<input type=\"text\" class=\"text\" name=\"rusermsn\" value=\"".sed_cc($urr['user_msn'])."\" size=\"32\" maxlength=\"64\" />",
	"USERS_PROFILE_IRC" => "<input type=\"text\" class=\"text\" name=\"ruserirc\" value=\"".sed_cc($urr['user_irc'])."\" size=\"56\" maxlength=\"128\" />",
	"USERS_PROFILE_GENDER" => $profile_form_gender,
	"USERS_PROFILE_BIRTHDATE" => $profile_form_birthdate,
	"USERS_PROFILE_TIMEZONE" => $profile_form_timezone,
	"USERS_PROFILE_LOCATION" => "<input type=\"text\" class=\"text\" name=\"ruserlocation\" value=\"".sed_cc($urr['user_location'])."\" size=\"32\" maxlength=\"64\" />",
	"USERS_PROFILE_OCCUPATION" => "<input type=\"text\" class=\"text\" name=\"ruseroccupation\" value=\"".sed_cc($urr['user_occupation'])."\" size=\"32\" maxlength=\"64\" />",
	"USERS_PROFILE_REGDATE" => @date($cfg['dateformat'], $urr['user_regdate'] + $usr['timezone'] * 3600)." ".$usr['timetext'],
	"USERS_PROFILE_LASTLOG" => @date($cfg['dateformat'], $urr['user_lastlog'] + $usr['timezone'] * 3600)." ".$usr['timetext'],
	"USERS_PROFILE_LOGCOUNT" => $urr['user_logcount'],
	"USERS_PROFILE_ADMINRIGHTS" => '',
	"USERS_PROFILE_OLDPASS" => "<input type=\"password\" class=\"password\" name=\"roldpass\" size=\"12\" maxlength=\"16\" autocomplete=\"off\" />",
	"USERS_PROFILE_NEWPASS1" => "<input type=\"password\" class=\"password\" name=\"rnewpass1\" size=\"12\" maxlength=\"16\" />",
	"USERS_PROFILE_NEWPASS2" => "<input type=\"password\" class=\"password\" name=\"rnewpass2\" size=\"12\" maxlength=\"16\" />",
		));

/* === Hook === */
$extp = sed_getextplugins('profile.tags');
if (is_array($extp))
	{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

$t->parse("MAIN");
$t->out("MAIN");

require_once $cfg['system_dir'] . '/footer.php';

?>
