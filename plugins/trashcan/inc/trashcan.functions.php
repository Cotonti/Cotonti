<?php
/**
 * Trashcan API
 *
 * @package TrashCan
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

use cot\plugins\trashcan\inc\TrashcanService;

defined('COT_CODE') or die('Wrong URL');

Cot::$db->registerTable('trash');

$GLOBALS['trash_types'] = array(
	'user' => Cot::$db->users
);

if (isset(Cot::$cfg['legacyMode']) && Cot::$cfg['legacyMode']) {
    // @deprecated in 0.9.26
    /**
     * Sends item to trash
     *
     * @param string $source Item type
     * @param string $title Title
     * @param string $sourceId Item ID
     * @param array|string $itemData Item contents in array or SQL string condition for deleting records
     * @param int $parentId trashcan parent id
     * @return ?int Trash insert id
     * @global CotDB $db
     */
    function cot_trash_put($source, $title, $sourceId, $itemData, $parentId = 0)
    {
        return TrashcanService::getInstance()->put($source, $title, (string) $sourceId, $itemData, $parentId);
    }
}

/**
 * Restores a trash item
 *
 * @param int $id Trash item ID
 * @return bool Operation success or failure
 */

function cot_trash_restore($id)
{
    // $L, $Ls, $R are needed for hook includes
    global $L, $Ls, $R, $cfg, $db, $db_trash, $trash_types;

	/* === Hook  === */
	foreach (cot_getextplugins('trash.restore.first') as $pl) {
		include $pl;
	}
	/* ===== */

	$id = (int) $id;
    if ($id < 1) {
        return false;
    }

	$tsql = Cot::$db->query('SELECT * FROM ' . Cot::$db->trash . ' WHERE tr_id=? LIMIT 1', $id);
	if ($res = $tsql->fetch()) {
		$data = unserialize($res['tr_datas']);
		$type = $res['tr_type'];
		$restore = true;
		$databasename = isset($trash_types[$type]) ? $trash_types[$type] : $type;
		if (isset($trash_types[$type]) && function_exists('cot_trash_'.$type.'_check')) {
			$check = 'cot_trash_'.$type.'_check';
			$restore = $check($data);
		}

		$rsql = $db->query("SELECT * FROM $databasename WHERE 1 LIMIT 1");
		if ($rrow = $rsql->fetch()) {
			$arraydiff = array_diff_key($data, $rrow);
			foreach ($arraydiff as $key => $val) {
				unset($data[$key]);
			}
			if (count($data) == 0 && $restore) {
				$restore = false;
			}
		}

        if ($restore) {
            try {
                $sql = $db->insert($databasename, $data);
            } catch(\Exception $e) {
                cot_log("$type #" . $res['tr_itemid'] . " failed to restore from the trash can.", 'trashcan', 'restore', 'error');
                return false;
            }

			cot_log("$type #" . $res['tr_itemid'] . " restored from the trash can.", 'trashcan', 'restore', 'done');

			if (isset($trash_types[$type]) && function_exists('cot_trash_'.$type.'_sync')) {
				$resync = 'cot_trash_' . $type . '_sync';
				$resync($data);
			}

			if ($sql > 0)
			{
				$db->delete($db_trash, "tr_id='".$res['tr_id']."'");
				$sql2 = $db->query("SELECT tr_id FROM $db_trash WHERE tr_parentid='".(int)$res['tr_id'] ."'");
				while ($row2 = $sql2->fetch())
				{
					cot_trash_restore($row2['tr_id']);
				}
				$sql2->closeCursor();
			}
		}
		/* === Hook  === */
		foreach (cot_getextplugins('trash.restore.done') as $pl)
		{
			include $pl;
		}
		/* ===== */
		return $sql;
	}
	return false;
}

/**
 * Deletes a trash item with subitems
 *
 * @param int $id Trash item ID
 * @return bool Operation success or failure
 * @global CotDB $db
 */
function cot_trash_delete($id)
{
	$id = (int) $id;
    if ($id < 1) {
        return false;
    }

	/* === Hook  === */
	foreach (cot_getextplugins('trash.delete.first') as $pl) {
		include $pl;
	}
	/* ===== */

	$tsql = Cot::$db->query(
        'SELECT * FROM ' . Cot::$db->quoteTableName(Cot::$db->trash) . ' WHERE tr_id = ? LIMIT 1',
        $id
    );
	if ($res = $tsql->fetch()) {
		Cot::$db->delete(Cot::$db->trash, 'tr_id = ' . $res['tr_id']);

		$sql2 = Cot::$db->query('SELECT tr_id FROM ' . Cot::$db->quoteTableName(Cot::$db->trash) .
            ' WHERE tr_parentid = ' . $res['tr_id']);
		while ($row2 = $sql2->fetch()) {
			cot_trash_delete($row2['tr_id']);
		}
		$sql2->closeCursor();

        if (Cot::$db->countRows(Cot::$db->trash) == 0) {
            Cot::$db->query('TRUNCATE ' . Cot::$db->quoteTableName(Cot::$db->trash));
        }
    }

	/* === Hook  === */
	foreach (cot_getextplugins('trash.delete.done') as $pl) {
		include $pl;
	}
	/* ===== */

	return true;
}

/* === Hook === */
foreach (cot_getextplugins('trashcan.api') as $pl) {
	include $pl;
}
/* ===== */
