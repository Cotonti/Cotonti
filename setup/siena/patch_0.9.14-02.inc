<?php
/* 0.9.14-02 update default time zone to use a region value instead of offset, #1172 */
defined('COT_CODE') && defined('COT_UPDATE') or die('Wrong URL');

global $db_config, $cfg;
$timezone_new_default_region = is_numeric($cfg['defaulttimezone']) ? cot_timezone_search('', $cfg['defaulttimezone']*3600) : '';
$timezone_new_default_region = empty($timezone_new_default_region) || !is_array($timezone_new_default_region) ? 'GMT' : $timezone_new_default_region[0];
$db->query("UPDATE $db_config SET config_value=?, config_type=?, config_variants=? WHERE config_owner=? AND config_cat=? AND config_name=?", 
	array(
		$timezone_new_default_region, 
		'4',
		'cot_config_timezones()',
		'core', 
		'locale', 
		'defaulttimezone'
	)
);
