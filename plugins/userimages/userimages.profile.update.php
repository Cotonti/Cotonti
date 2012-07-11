<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=users.profile.update.first
Tags=users.profile.tpl:
[END_COT_EXT]
==================== */

/**
 * Avatar and photo for users
 *
 * @package userimages
 * @version 1.1
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');
require_once cot_incfile('userimages', 'plug');
$userimages = cot_userimages_config_get();

if($_FILES)
{
	@clearstatcache();
	foreach($userimages as $code => $settings)
	{
		if(!$_FILES[$code]) continue;
		$file = $_FILES[$code];
		if (!empty($file['tmp_name']) && $file['size'] > 0 && is_uploaded_file($file['tmp_name']))
		{
			$gd_supported = array('jpg', 'jpeg', 'png', 'gif');
			$file_ext = strtolower(end(explode(".", $file['name'])));
			$fcheck = cot_file_check($file['tmp_name'], $file['name'], $file_ext);
			if(in_array($file_ext, $gd_supported) && $fcheck == 1)
			{
			    $file['name']= cot_safename($file['name'], true);
				$filename_full = $usr['id'].'-'.strtolower($file['name']);
				$filepath = ($code == 'avatar') ?
					$cfg['avatars_dir'].'/'.$filename_full:
					$cfg['photos_dir'].'/'.$filename_full;

				if(file_exists($filepath))
				{
					unlink($filepath);
				}

				move_uploaded_file($file['tmp_name'], $filepath);
				cot_imageresize($filepath, $filepath, $settings['width'], $settings['height'], $settings['crop'], '', 100);
				@chmod($filepath, $cfg['file_perms']);

				/* === Hook === */
				foreach (cot_getextplugins('profile.update.'.$code) as $pl)
				{
					include $pl;
				}
				/* ===== */

				$sql = $db->update($db_users, array("user_".$code => $filepath), "user_id='".$usr['id']."'");
			}
			elseif($fcheck == 2)
			{
				cot_error(sprintf($L['pfs_filemimemissing'], $file_ext), $code);
			}
			else
			{
				cot_error(sprintf($L['userimages_'.$code.'notvalid'], $file_ext), $code);
			}
		}
	}
}

?>