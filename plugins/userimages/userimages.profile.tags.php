<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=profile.tags
[END_COT_EXT]
==================== */

/**
 * Avatar and photo for users
 *
 * @package userimages
 * @version 0.9.0
 * @author Koradhil, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

$profile_form_avatar = $R['users_link_avatar'];
$profile_form_photo = $R['users_link_photo'];

$profile_form_avatar_existing = !empty($urr['user_avatar']) ? cot_rc('users_code_avatar_existing', array(
	'avatar_url' => $urr['user_avatar'],
	'delete_url' => cot_url('users', 'm=profile&a=avatardelete&'.cot_xg())
	)) : '';
$profile_form_avatar = cot_rc('users_code_avatar', array(
	'avatar_existing' => $profile_form_avatar_existing,
	'input_maxsize' => cot_inputbox('hidden', 'MAX_FILE_SIZE', $cfg['av_maxsize']*1024),
	'input_file' => cot_inputbox('file', 'userfile', '', array('size' => 24))
));

$profile_form_photo_existing = !empty($urr['user_photo']) ? cot_rc('users_code_photo_existing', array(
	'photo_url' => $urr['user_photo'],
	'delete_url' => cot_url('users', 'm=profile&a=phdelete&'.cot_xg())
	)) : '';
$profile_form_photo = cot_rc('users_code_photo', array(
	'photo_existing' => $profile_form_photo_existing,
	'input_maxsize' => cot_inputbox('hidden', 'MAX_FILE_SIZE', $cfg['ph_maxsize']*1024),
	'input_file' => cot_inputbox('file', 'userphoto', '', array('size' => 24))
));

if ($a=='avatarchoose')
{
	cot_check_xg();
	$profile_form_avatar .= $R['users_code_avatarchoose_title'];
	$handle = opendir($cfg['defav_dir']);
	while ($f = readdir($handle))
	{
		if ($f != "." && $f != "..")
		{
			$profile_form_avatar .= cot_rc('users_link_avatarselect', array(
				'url' => cot_url('users', 'm=profile&a=avatarselect&'.cot_xg().'&id='.urlencode($f), '#avatar'),
				'f' => $f
			));
		}
	}
	closedir($handle);
}
else
{
	$profile_form_avatar .= cot_rc('users_link_avatarchoose', array(
		'url' => cot_url('users', 'm=profile&a=avatarchoose&'.cot_xg(), '#list')
	));
}

$t->assign(array(
	"USERS_PROFILE_AVATAR" => $profile_form_avatar,
	"USERS_PROFILE_PHOTO" => $profile_form_photo,
	"USERS_PROFILE_SIGNATURE" => $profile_form_signature
));

?>