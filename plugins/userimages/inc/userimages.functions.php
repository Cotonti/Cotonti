<?php

defined('COT_CODE') or die('Wrong URL');

cot_require_api('configuration');

/**
 * Get confuration for user image types
 *
 * @return array
 */
function cot_userimages_config_get()
{
	global $cache;

	if($cache && $cache->db->exists('cot_userimages_config', 'users'))
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
 * @return Entries modified
 */
function cot_userimages_config_add($code, $width, $height, $crop='')
{
	$cfg = array($width, $height);
	if($crop) $cfg[] = $crop;
	$options = array(
		strtolower($code) => implode('x', $cfg)
	);
	$result = cot_config_set('userimages', $options);
	$cache && $cache->db->remove('cot_userimages_config', 'users');
	return $result;
}

/**
 * Remove a user image type
 *
 * @param string $code User image code
 * @return DB query result
 */
function cot_userimages_config_remove($code)
{
	$result = cot_config_remove('userimages', false, strtolower($code));
	$cache && $cache->db->remove('cot_userimages_config', 'users');
	return $result;
}

?>