<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=standalone
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

switch ($a)
{
	case 'avatardelete':
		cot_check_xg();
		$filename = $usr['id']."-avatar.gif";
		$filepath = $cfg['av_dir'].$filename;
		if (file_exists($filepath))
		{
			unlink($filepath);
		}
		$sql = $db->update($db_users, array('user_avatar' => ''), "user_id='".$usr['id']."'");
		cot_redirect(cot_url('users', "m=profile", '#avatar', true));
		break;

	case 'phdelete':
		cot_check_xg();
		$photo = $usr['id']."-photo.gif";
		$photopath = $cfg['photos_dir'].$photo;
		if (file_exists($photopath))
		{
			unlink($photopath);
		}
		$sql = $db->update($db_users, array('user_photo' => ''), "user_id='".$usr['id']."'");
		cot_redirect(cot_url('users', "m=profile", '#photo', true));
		break;

	case 'avatarselect':
		cot_check_xg();
		$filename = $cfg['defav_dir'].urldecode($id);
		$filename = str_replace(array("'", ",", chr(0x00)), "", $filename);
		if (file_exists($filename))
		{
			$sql = $db->update($db_users, array('user_avatar' => $filename), "user_id='".$usr['id']."'");
		}
		cot_redirect(cot_url('users', "m=profile", '#avatar', true));
		break;
}

?>