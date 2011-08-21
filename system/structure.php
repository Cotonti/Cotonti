<?php
/**
 * Structure manipulation API
 *
 * @package Cotonti
 * @version 0.9.4
 * @author Trustmaster, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2011
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('auth');

/**
 * Adds a new category
 * 
 * @global Cache $cache
 * @global CotDB $db
 * @global string $db_structure
 * @param string $module Module code
 * @param array $data Structure entry data as array('structure_key' => 'value')
 * @return mixed TRUE on success, cot_error() arguments as array on specific error, FALSE on generic error
 */
function cot_structure_add($module, $data)
{
	global $cache, $db, $db_structure;
	
	/* === Hook === */
	foreach (cot_getextplugins('structure.add') as $pl)
	{
		include $pl;
	}
	/* ===== */

	if (!empty($data['structure_title']) && !empty($data['structure_code']) && !empty($data['structure_path']) && $data['structure_code'] != 'all')
	{
		$sql = $db->query("SELECT COUNT(*) FROM $db_structure WHERE structure_area=? AND structure_code=?", array($module, $data['structure_code']));
		if ($sql->fetchColumn() == 0)
		{
			$sql = $db->insert($db_structure, $data);
			$auth_permit = array(COT_GROUP_DEFAULT => 'RW', COT_GROUP_GUESTS => 'R', COT_GROUP_MEMBERS => 'RW');
			$auth_lock = array(COT_GROUP_DEFAULT => '0', COT_GROUP_GUESTS => 'A', COT_GROUP_MEMBERS => '0');
			cot_auth_add_item($module, $data['structure_code'], $auth_permit, $auth_lock);
			$area_addcat = 'cot_'.$module.'_addcat';
			(function_exists($area_addcat)) ? $area_addcat($data['structure_code']) : FALSE;
			$cache && $cache->clear();
			return true;
		}
		else
		{
			return array('adm_cat_exists', 'rstructurecode');
		}
	}
	else
	{
		return false;
	}
}

/**
 * Removes a category
 * 
 * @global Cache $cache
 * @global CotDB $db
 * @global string $db_structure
 * @global array $structure
 * @param string $module Module code
 * @param string $code Category code
 * @return bool 
 */
function cot_structure_delete($module, $code)
{
	global $cache, $db, $db_config, $db_structure, $structure;
	
	/* === Hook === */
	foreach (cot_getextplugins('structure.delete') as $pl)
	{
		include $pl;
	}
	/* ===== */
	
	$db->delete($db_structure, "structure_area=? AND structure_code=?", array($module, $code));
	$db->delete($db_config, "config_cat=? AND config_subcat=? AND config_owner='module'", array($module, $code));
	cot_auth_remove_item($module, $code);
	$area_deletecat = 'cot_'.$module.'_deletecat';
	(function_exists($area_deletecat)) ? $area_deletecat($code) : FALSE;
	
	unset($structure[$module][$code]);
	if ($cache)
	{
		$cache->clear();
	}
	
	return true;
}

/**
 * Updates an existing category in the database
 *
 * @global Cache $cache
 * @global CotDB $db
 * @global string $db_auth
 * @global string $db_structure
 * @param string $module Module code
 * @param int $id Category structure_id
 * @param array $old_data Data row already present in the database
 * @param array $new_data Submitted category data
 * @return mixed TRUE on success, cot_error() arguments as array on specific error, FALSE on generic error 
 */
function cot_structure_update($module, $id, $old_data, $new_data)
{
	global $cache, $db, $db_auth, $db_config, $db_structure;
	/* === Hook === */
	foreach (cot_getextplugins('structure.update') as $pl)
	{
		include $pl;
	}
	/* ===== */

	if ($old_data['structure_code'] != $new_data['structure_code'])
	{
		if ($db->query("SELECT COUNT(*) FROM $db_structure WHERE structure_area=? AND structure_code=?", array($module, $new_data['structure_code']))->fetchColumn() == 0)
		{
			$db->update($db_auth, array('auth_option' => $new_data['structure_code']),
				"auth_code=? AND auth_option=?", array($module, $old_data['structure_code']));
			$db->update($db_config, array('config_subcat' => $new_data['structure_code']),
				"config_cat=? AND config_subcat=? AND config_owner='module'", array($module, $old_data['structure_code']));
			$area_updatecat = 'cot_' . $module . '_updatecat';
			(function_exists($area_updatecat)) ? $area_updatecat($old_data['structure_code'], $new_data['structure_code']) : FALSE;
			cot_auth_reorder();
		}
		else
		{
			unset($new_data['structure_code']);
			return array('adm_cat_exists', 'default');
		}
	}

	$area_sync = 'cot_' . $module . '_sync';
	$new_data['structure_count'] = (function_exists($area_sync)) ? $area_sync($new_data['structure_code']) : 0;

	$sql1 = $db->update($db_structure, $new_data, 'structure_id=' . (int) $id);
	return true;
}

?>
