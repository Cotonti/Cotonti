<?php

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('configuration');

/**
 * Get confuration for user image types
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
	if (empty($code))
	{
		return FALSE;
	}
	$exists = $db->query("SHOW COLUMNS FROM $db_users LIKE 'user_".$db->prep($code)."'")->rowCount() > 0;
	if(!$exists)
	{
		$db->query("ALTER TABLE $db_users ADD `user_".$db->prep($code)."`
					varchar(255) collate utf8_unicode_ci NOT NULL default ''");
	}
	if(!$exists || $force)
	{
		$cfg = array(strval($width), strval($height));
		if($crop) $cfg[] = $crop;
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
function cot_userimages_build($src, $code='')
{
	global $R, $L;
	include cot_incfile('userimages', 'plug', 'resources');
	if($src && $code && $R["userimg_img_$code"])
	{
		return cot_rc("userimg_img_$code", array('src' => $src, 'alt' => $L[$code], 'class' => $code));
	}
	if($src && $code)
	{
		return cot_rc('userimg_img', array('src' => $src, 'alt' => $L[$code], 'class' => $code));
	}
	if($src)
	{
		return cot_rc('userimg_img', array('src' => $src, 'alt' => '', 'class' => ''));
	}
	if($R["userimg_default_$code"])
	{
		return cot_rc("userimg_default_$code");
	}
	return '';
}

?>