<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=profile.update.first
Tags=users.profile.tpl:
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
		$fcheck = cot_file_check($userpic[$key]['tmp_name'], $userpic[$key]['name'], $f_extension);
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

					cot_createthumb($filepath, $filepath, $cfg['av_maxx'],$cfg['av_maxy'], 1, $f_extension, $filename, 0, 0, 0, 0, 0, $percentage, $prior);
					//cot_imageresize($filepath, $filepath, $cfg[$type.'_maxx'], $cfg[$type.'_maxy'], 'fit', '', 100);

					while ( ($f_extension == 'jpeg' || $f_extension == 'jpg') && ($userpic[$key]['size'] > $cfg[$key.'_maxsize']))
					{
						$percentage -= 5;
						cot_createthumb($filepath, $filepath, $cfg[$key.'_maxx'],$cfg[$key.'_maxy'], 1, $f_extension, $filename, 0, 0, 0, 0, 0, $percentage, $prior);

						clearstatcache();
						$userpic[$key]['size'] = filesize($filepath);
					}
				}

				/* === Hook === */
				foreach (cot_getextplugins('profile.update.'.$picfull[$key]) as $pl)
				{
					include $pl;
				}
				/* ===== */

				$userpic[$key]['size'] = filesize($filepath);
				if ($userpic[$key]['size'] <= $cfg[$key.'_maxsize'])
				{
					$sql = $db->update($db_users, array( "user_".$picfull[$key] => $filepath), "user_id='".$usr['id']."'");
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
			cot_error(sprintf($L['pfs_filemimemissing'], $f_extension), 'userfile');
		}
		else
		{
			cot_error(sprintf($L['pro_'.$picfull[$key].'notvalid'], $f_extension), 'userfile');
		}
	}
}

?>