<?php
/**
 * Trashcan API
 *
 * @package Trashcan
 * @version 0.9.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

global $db_trash, $db_users, $db_x, $trash_types;
$db_trash = isset($db_trash) ? $db_trash : $db_x . 'trash';

$trash_types = array(
	'user' => $db_users
);

/**
 * Sends item to trash
 *
 * @param string $type Item type
 * @param string $title Title
 * @param int $itemid Item ID
 * @param mixed $datas Item contents
 * @param int $parentid trashcan parent id
 * @return int Trash insert id
 * @global CotDB $db
 */
function cot_trash_put($type, $title, $itemid, $datas, $parentid = '0')
{
	global $db, $db_trash, $sys, $usr, $trash_types;

	$trash = array('tr_date' => $sys['now'], 'tr_type' => $type, 'tr_title' => $title, 'tr_itemid' => $itemid,
		'tr_trashedby' => (int)$usr['id'], 'tr_parentid' => $parentid);
	
	/* === Hook  === */
	foreach (cot_getextplugins('trash.put.first') as $pl)
	{
		include $pl;
	}
	/* ===== */
	
	$i = 0;
	if (is_array($datas))
	{
		$i++;
		$trash['tr_datas'] = serialize($datas);
		$sql = $db->insert($db_trash, $trash);
	}
	elseif (is_string($datas))
	{
		$tablename = isset($trash_types[$type]) ? $trash_types[$type] : $type;
		$sql_s = $db->query("SELECT * FROM $tablename WHERE $datas");
		while ($row_s = $sql_s->fetch())
		{
			$i++;
			$trash['tr_datas'] = serialize($row_s);
			$sql = $db->insert($db_trash, $trash);
		}
		$sql_s->closeCursor();
	}

	$id = ($i) ? $db->lastInsertId() : false;
	
	/* === Hook  === */
	foreach (cot_getextplugins('trash.put.done') as $pl)
	{
		include $pl;
	}
	/* ===== */
	
	return $id;
}

/**
 * Restores a trash item
 *
 * @param int $id Trash item ID
 * @return bool Operation success or failure
 */

function cot_trash_restore($id)
{
	global $db, $db_trash, $trash_types;
	/* === Hook  === */
	foreach (cot_getextplugins('trash.restore.first') as $pl)
	{
		include $pl;
	}
	/* ===== */
	$id = (int) $id;
	$tsql = $db->query("SELECT * FROM $db_trash WHERE tr_id=$id LIMIT 1");
	if ($res = $tsql->fetch())
	{
		$data = unserialize($res['tr_datas']);
		$type = $res['tr_type'];
		$restore = true;
		$databasename = isset($trash_types[$type]) ? $trash_types[$type] : $type;
		if(isset($trash_types[$type]) && function_exists('cot_trash_'.$type.'_check'))
		{
			$check = 'cot_trash_'.$type.'_check';
			$restore = $check($data);
		}

		$rsql = $db->query("SELECT * FROM $databasename WHERE 1 LIMIT 1");
		if ($rrow = $rsql->fetch())
		{
			$arraydiff = array_diff_key($data, $rrow);
			foreach ($arraydiff as $key => $val)
			{
				unset($data[$key]);
			}
			if (count($data) == 0 && $restore)
			{
				$restore = false;
			}
		}		
		if ($restore)
		{
			$sql = $db->insert($databasename, $data);
			cot_log("$type #".$res['tr_itemid']." restored from the trash can.", 'adm');

			if(isset($trash_types[$type]) && function_exists('cot_trash_'.$type.'_sync'))
			{
				$resync = 'cot_trash_'.$type.'_sync';
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
	global $db, $db_trash;
	$id = (int) $id;
	/* === Hook  === */
	foreach (cot_getextplugins('trash.delete.first') as $pl)
	{
		include $pl;
	}
	/* ===== */
	$tsql = $db->query("SELECT * FROM $db_trash WHERE tr_id=$id LIMIT 1");
	if ($res = $tsql->fetch())
	{
		$db->delete($db_trash, "tr_id='".$res['tr_id']."'");
		$sql2 = $db->query("SELECT tr_id FROM $db_trash WHERE tr_parentid='".(int)$res['tr_id'] ."'");
		while ($row2 = $sql2->fetch())
		{
			cot_trash_delete($row2['tr_id']);
		}
		$sql2->closeCursor();
	}
	/* === Hook  === */
	foreach (cot_getextplugins('trash.delete.done') as $pl)
	{
		include $pl;
	}
	/* ===== */
	return true;
}

/* === Hook === */
foreach (cot_getextplugins('trashcan.api') as $pl)
{
	include $pl;
}
/* ===== */

?>
