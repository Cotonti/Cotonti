<?php

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('configuration');

/**
 * Get configuration for user image types
 *
 * @return array
 * @global Cache $cache
 */
function cot_userimages_config_get($ignorecache=false)
{
	global $cache;

	if($cache && !$ignorecache && $cache->db->exists('cot_userimages_config', 'users'))
	{
		$cfg = $cache->db->get('cot_userimages_config', 'users');
		if(is_array($cfg)) return $cfg;
	}

	$cfg = array();
	$cot_cfg = cot_config_load('userimages');
	foreach($cot_cfg as $entry)
	{
		$imagesettings = explode('x', $entry['value']);
		$cfg[$entry['name']] = array(
			'width' => $imagesettings[0],
			'height' => $imagesettings[1],
			'crop' => (isset($imagesettings[2])) ? $imagesettings[2] : ''
		);
	}
	$cache && $cache->db->store('cot_userimages_config', $cfg, 'users');
	return $cfg;
}

/**
 * Add a user image type
 *
 * @param string $code Code for image, also used for tpl tags
 * @param int $width Image maximum width
 * @param int $height Image maximum height
 * @param string $crop Crop ratio, or 'fit' to use width/height to calculate ratio
 * @return bool
 * @global CotDB $db
 * @global Cache $cache
 */
function cot_userimages_config_add($code, $width, $height, $crop='', $force=false)
{
	global $cache, $db, $db_users;

	if (empty($code)) {
		return FALSE;
	}
	$exists = $db->query("SHOW COLUMNS FROM $db_users LIKE 'user_" . $db->prep($code)."'")->rowCount() > 0;
	if (!$exists) {
		$db->query("ALTER TABLE $db_users ADD `user_" . $db->prep($code) . "` varchar(255) NOT NULL default ''");
	}
	if (!$exists || $force) {
		$cfg = array(strval($width), strval($height));
		if ($crop) $cfg[] = $crop;
		$options = array(array(
			'name' => strtolower($code),
			'type' => COT_CONFIG_TYPE_HIDDEN,
			'default' => implode('x', $cfg),
			'text' => $code
		));
		$result = cot_config_add('userimages', $options);
		$cache && $cache->db->remove('cot_userimages_config', 'users');
		return $result;
	}
	return FALSE;
}

/**
 * Edit a user image type
 *
 * @param string $code Code for image, also used for tpl tags
 * @param int $width Image maximum width
 * @param int $height Image maximum height
 * @param string $crop Crop ratio, or 'fit' to use width/height to calculate ratio
 * @return Entries modified
 * @global Cache $cache
 */
function cot_userimages_config_edit($code, $width, $height, $crop='')
{
	global $cache;
	if (empty($code))
	{
		return FALSE;
	}
	$cfg = array(strval($width), strval($height));
	if($crop) $cfg[] = $crop;
	$options = array(strtolower($code) => implode('x', $cfg));
	$result = cot_config_set('userimages', $options);
	$cache && $cache->db->remove('cot_userimages_config', 'users');
	return $result;
}

/**
 * Remove a user image type
 *
 * @param string $code User image code
 * @return DB query result
 * @global CotDB $db
 * @global Cache $cache
 */
function cot_userimages_config_remove($code, $dropcolumn=true)
{
	global $cache, $db, $db_users;
	if (empty($code))
	{
		return FALSE;
	}
	$result = cot_config_remove('userimages', false, strtolower($code));
	if($result && $dropcolumn)
	{
		$db->query("ALTER TABLE $db_users DROP `user_".$db->prep($code)."`");
	}
	$cache && $cache->db->remove('cot_userimages_config', 'users');
	return $result;
}

/**
 * Returns user image html code
 *
 * @param string $src File path
 * @param string $code Userimage code
 * @return string
 */
function cot_userimages_build($src, $code = '')
{
	require_once cot_incfile('userimages', 'plug', 'resources');

	if ($src && $code && isset(Cot::$R["userimg_img_$code"])) {
        $alt =  isset(Cot::$L[$code]) ? Cot::$L[$code] : '';
		return cot_rc("userimg_img_$code", array('src' => $src, 'alt' => $alt, 'class' => $code));
	}

	if ($src && $code) {
        $alt =  isset(Cot::$L[$code]) ? Cot::$L[$code] : '';
		return cot_rc('userimg_img', array('src' => $src, 'alt' => $alt, 'class' => $code));
	}

	if ($src) {
		return cot_rc('userimg_img', array('src' => $src, 'alt' => '', 'class' => ''));
	}

	if (isset(Cot::$R["userimg_default_$code"]) && Cot::$R["userimg_default_$code"] != '') {
		return cot_rc("userimg_default_$code");
	}

	return '';
}

/**
 * Returns UserImages tags for coTemplate
 *
 * @param array $user_data User info array
 * @param string $tag_prefix Prefix for tags
 * @return array
 */
function cot_userimages_tags($user_data, $tag_prefix='')
{
	global $m;

	$temp_array = array();
	$userimages = cot_userimages_config_get();
	$uid = $user_data['user_id'];
	$usermode = $m == 'edit' || ($uid != Cot::$usr['id']);

	foreach($userimages as $code => $settings)
	{
		if (!empty($user_data['user_'.$code]))
		{
			$delete_params = 'r=userimages'
				.'&a=delete'
				.'&uid='.($usermode ? $uid : '')
				.'&m='.$m
				.'&code='.$code
				.'&'.cot_xg();
			$userimg_existing = cot_rc('userimg_existing', array(
				'url_file' => $user_data['user_'.$code],
				'url_delete' => cot_url('plug', $delete_params)
			));
		}
		else
		{
			$userimg_existing = '';
		}
		$userimg_selectfile = cot_rc('userimg_selectfile', array(
			'form_input' => cot_inputbox('file', $usermode ? $code.':'.$uid : $code, '', array('size' => 24))
		));
		$userimg_html = cot_rc('userimg_html', array(
			'code' => $usermode ? $code.' uid_'.$uid: $code,
			'existing' => $userimg_existing,
			'selectfile' => $userimg_selectfile
		));

		$temp_array[$tag_prefix . strtoupper($code)] = $userimg_html;
		$temp_array[$tag_prefix . strtoupper($code) . '_SELECT'] = $userimg_selectfile;
	}

	return $temp_array;
}

/**
 * Process uploaded user images files for certain User
 *
 * @param number $uid User ID for uploads to be attached
 * @return boolean|number Number of uploaded images or false for incorrect $uid
 */
function cot_userimages_process_uploads($uid = null)
{
	global $m;

	$files = 0;
    $usermode = false;

	if (!empty($_FILES)) {
		if (empty($uid)) {
            $uid = Cot::$usr['id'];
        }
		if (!is_numeric($uid) || $uid != (int) $uid || $uid < 1) {
            return false;
        }

        // user edit mode
		if ($uid != Cot::$usr['id'] || $m == 'edit') {
			list(Cot::$usr['auth_read'], Cot::$usr['auth_write'], Cot::$usr['isadmin']) = cot_auth('users', 'a');
			if (!Cot::$usr['isadmin']) {
                return 0;
            }
			$usermode = true;
		}

		@clearstatcache();
		$userimages = cot_userimages_config_get();
		foreach ($userimages as $code => $settings) {
            $key = $usermode ? $code . ':' . $uid : $code;
			$file = isset($_FILES[$key]) ? $_FILES[$key] : null;
			if (empty($file)) {
                continue;
            }

			if (!empty($file['tmp_name']) && $file['size'] > 0 && is_uploaded_file($file['tmp_name'])) {
				$gd_supported = array('jpg', 'jpeg', 'png', 'gif');
				$var = explode(".", $file['name']);
				$file_ext = strtolower(array_pop($var));
				$fcheck = cot_file_check($file['tmp_name'], $file['name'], $file_ext);
				if (in_array($file_ext, $gd_supported) && $fcheck == 1) {
					$file['name'] = cot_safename($file['name'], true);
					$path = ($code == 'avatar') ? Cot::$cfg['avatars_dir'] : Cot::$cfg['photos_dir'];
					$filename_full = $uid . '-' . strtolower(($code != 'avatar') ? $code . '-' . $file['name'] : $file['name']);
					$filepath = $path . '/' . $filename_full;

					if (file_exists($filepath)) {
						unlink($filepath);
					}

					move_uploaded_file($file['tmp_name'], $filepath);
					cot_imageresize($filepath, $filepath, $settings['width'], $settings['height'], $settings['crop'], '', 100);
					@chmod($filepath, Cot::$cfg['file_perms']);

					/* === Hook === */
					foreach (cot_getextplugins('profile.update.' . $code) as $pl) {
						include $pl;
					}
					/* ===== */
					$sql = Cot::$db->query("SELECT user_" . Cot::$db->prep($code) . " FROM " . Cot::$db->users .
                        " WHERE user_id=" . $uid);
					if ($oldimage = $sql->fetchColumn()) {
						if (file_exists($oldimage)) {
							unlink($oldimage);
						}
					}

					$sql = Cot::$db->update(Cot::$db->users, array("user_" . $code => $filepath), "user_id='" .
                        $uid . "'");
					$files++;

                } elseif ($fcheck == 2) {
					cot_error(sprintf(Cot::$L['pfs_filemimemissing'], $file_ext), $code);

				} else {
                    $msg = isset(Cot::$L['userimages_' . $code . 'notvalid']) ?
                        Cot::$L['userimages_' . $code . 'notvalid'] :
                        Cot::$L['userimages_photonotvalid'];
					cot_error(sprintf(Cot::$L['userimages_' . $code . 'notvalid'], $file_ext), $code);
				}
			}
		}
	}

	return $files;
}
