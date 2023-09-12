<?php
/**
 * Structure manipulation API
 *
 * @package API - Structure
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('auth');

/**
 * Adds a new category
 *
 * @global Cache $cache
 * @global CotDB $db
 * @global string $db_structure
 * @param string $extension Extension code
 * @param array $data Structure entry data as array('structure_key' => 'value')
 * @param bool $is_module TRUE for modules, FALSE for plugins
 * @return mixed TRUE on success, cot_error() arguments as array on specific error, FALSE on generic error
 * @global CotDB $db
 * @global Cache $cache
 */
function cot_structure_add($extension, $data, $is_module = true)
{
    // $L, $Ls, $R are needed for hook includes
    global $L, $Ls, $R;

	global $cache, $db, $db_structure;

	/* === Hook === */
	foreach (cot_getextplugins('structure.add') as $pl) {
		include $pl;
	}
	/* ===== */

	if (!empty($data['structure_title']) && !empty($data['structure_code']) && !empty($data['structure_path']) && $data['structure_code'] != 'all') {
		$sql = $db->query("SELECT COUNT(*) FROM $db_structure WHERE structure_area=? AND structure_code=?", [$extension, $data['structure_code']]);
		if ($sql->fetchColumn() == 0) {
			$sql = $db->insert($db_structure, $data);
			$auth_permit = [COT_GROUP_DEFAULT => 'RW', COT_GROUP_GUESTS => 'R', COT_GROUP_MEMBERS => 'RW'];
			$auth_lock = [COT_GROUP_DEFAULT => '0', COT_GROUP_GUESTS => 'A', COT_GROUP_MEMBERS => '0'];
			$is_module && cot_auth_add_item($extension, $data['structure_code'], $auth_permit, $auth_lock);
			$area_addcat = 'cot_'.$extension.'_addcat';
			(function_exists($area_addcat)) ? $area_addcat($data['structure_code']) : FALSE;

			cot_log("Structure. Add category: '$extension' - '".$data['structure_code']."'", 'adm', 'structure', 'add');

			if (\Cot::$cache) {
                $cache->clear();
            }

			return true;
		} else {
			return ['adm_cat_exists', 'rstructurecode'];
		}
	} else {
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
 * @param string $extension Extension code
 * @param string $code Category code
 * @param bool $is_module TRUE for modules, FALSE for plugins
 * @return bool
 * @global CotDB $db
 * @global Cache $cache
 */
function cot_structure_delete($extension, $code, $is_module = true)
{
    // $L, $Ls, $R are needed for hook includes
    global $L, $Ls, $R;

	global $db, $structure;

	/* === Hook === */
	foreach (cot_getextplugins('structure.delete') as $pl) {
		include $pl;
	}
	/* ===== */

    $data = Cot::$db->query(
        'SELECT structure_code, structure_count FROM ' . Cot::$db->structure .
        ' WHERE structure_area = :area AND structure_code = :code',
        ['area' => $extension, 'code' => $code]
    )->fetch();

    if (empty($data)) {
        return false;
    }

    if (
        $data['structure_count'] > 0
        || !empty(cot_structure_children($extension, $code, true, false, false))
    ) {
        return false;
    }

    Cot::$db->delete(Cot::$db->structure, "structure_area=? AND structure_code=?", [$extension, $code]);
    Cot::$db->delete(Cot::$db->config, "config_cat=? AND config_subcat=? AND config_owner='module'", [$extension, $code]);
	$is_module && cot_auth_remove_item($extension, $code);
	$area_deletecat = 'cot_'.$extension.'_deletecat';
	(function_exists($area_deletecat)) ? $area_deletecat($code) : FALSE;

    cot_log("Structure. Deleted category: '$extension' - '$code'", 'adm', 'structure', 'delete');

	unset(Cot::$structure[$extension][$code]);
	if (Cot::$cache) {
        Cot::$cache->clear();
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
 * @param string $extension Extension code
 * @param int $id Category structure_id
 * @param array $old_data Data row already present in the database
 * @param array $new_data Submitted category data
 * @param bool $is_module TRUE for modules, FALSE for plugins
 * @return mixed TRUE on success, cot_error() arguments as array on specific error, FALSE on generic error
 * @global CotDB $db
 * @global Cache $cache
 */
function cot_structure_update($extension, $id, $old_data, $new_data, $is_module = true)
{
	global $cache, $db, $db_auth, $db_config, $db_structure;
	/* === Hook === */
	foreach (cot_getextplugins('structure.update') as $pl) {
		include $pl;
	}
	/* ===== */

	if ($old_data['structure_code'] != $new_data['structure_code']) {
		if ($db->query("SELECT COUNT(*) FROM $db_structure WHERE structure_area=? AND structure_code=?", [$extension, $new_data['structure_code']])->fetchColumn() == 0) {
			$is_module && $db->update($db_auth, ['auth_option' => $new_data['structure_code']],
				"auth_code=? AND auth_option=?", [$extension, $old_data['structure_code']]);
			$db->update($db_config, ['config_subcat' => $new_data['structure_code']],
				"config_cat=? AND config_subcat=? AND config_owner='module'", [$extension, $old_data['structure_code']]);
			$area_updatecat = 'cot_' . $extension . '_updatecat';
			(function_exists($area_updatecat)) ? $area_updatecat($old_data['structure_code'], $new_data['structure_code']) : FALSE;
			cot_auth_reorder();
		} else {
			unset($new_data['structure_code']);
			return ['adm_cat_exists', 'default'];
		}
	}

	$area_sync = 'cot_' . $extension . '_sync';
	$new_data['structure_count'] = (function_exists($area_sync)) ? $area_sync($new_data['structure_code']) : 0;

	$sql1 = $db->update($db_structure, $new_data, 'structure_id=' . (int) $id);

	$updated = $sql1 > 0;

	if ($updated) cot_log("Structure. Edited category: '$extension' - '".$new_data['structure_code']."'", 'adm', 'structure', 'update');

	/* === Hook === */
	foreach (cot_getextplugins('structure.update.done') as $pl) {
		include $pl;
	}
	/* ===== */

	return $updated;
}