<?php
/**
 * Trashcan API
 *
 * @package trash
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

$GLOBALS['db_trash'] = (isset($GLOBALS['db_trash'])) ? $GLOBALS['db_trash'] : $GLOBALS['db_x'] . 'trash';

/**
 * Sends item to trash
 *
 * @param string $type Item type
 * @param string $title Title
 * @param int $itemid Item ID
 * @param mixed $datas Item contents
 */
function cot_trash_put($type, $title, $itemid, $datas)
{
	global $db_trash, $sys, $usr;

	$sql = cot_db_query("INSERT INTO $db_trash (tr_date, tr_type, tr_title, tr_itemid, tr_trashedby, tr_datas)
	VALUES
	(".$sys['now_offset'].", '".cot_db_prep($type)."', '".cot_db_prep($title)."', '".cot_db_prep($itemid)."', ".$usr['id'].", '".cot_db_prep(serialize($datas))."')");
}

/**
 * Restores a trash item
 *
 * @param int $id Trash item ID
 * @return bool Operation success or failure
 */
function cot_trash_restore($id)
{
	global $db_forum_topics, $db_forum_posts, $db_trash;

	$columns = array();
	$datas = array();

	$tsql = cot_db_query("SELECT * FROM $db_trash WHERE tr_id='$id' LIMIT 1");
	if ($res = cot_db_fetchassoc($tsql))
	{
		$res['tr_datas'] = unserialize($res['tr_datas']);
	}

	switch($res['tr_type'])
	{
		case 'comment':
			global $db_com;
			cot_db_insert($db_com, $res['tr_datas']);
			cot_log("Comment #".$res['tr_itemid']." restored from the trash can.", 'adm');
			return (TRUE);
			break;

		case 'forumpost':
			global $db_forum_posts;
			$sql = cot_db_query("SELECT ft_id FROM $db_forum_topics WHERE ft_id='".$res['tr_datas']['fp_topicid']."'");

			if ($row = cot_db_fetcharray($sql))
			{
				cot_db_insert($db_forum_posts, $res['tr_datas']);
				cot_log("Post #".$res['tr_itemid']." restored from the trash can.", 'adm');
				cot_forum_resynctopic($res['tr_datas']['fp_topicid']);
				cot_forum_sectionsetlast($res['tr_datas']['fp_sectionid']);
				cot_forum_resync($res['tr_datas']['fp_sectionid']);
				return TRUE;
			}
			else
			{
				$sql1 = cot_db_query("SELECT tr_id FROM $db_trash WHERE tr_type='forumtopic' AND tr_itemid='q".$res['tr_datas']['fp_topicid']."'");
				if ($row1 = cot_db_fetcharray($sql1))
				{
					cot_trash_restore($row1['tr_id']);
					cot_db_delete($db_trash, "tr_id='".$row1['tr_id']."'");
				}
			}
			break;

		case 'forumtopic':
			global $db_forum_topics;
			cot_db_insert($db_forum_topics, $res['tr_datas']);
			cot_log("Topic #".$res['tr_datas']['ft_id']." restored from the trash can.", 'adm');

			$sql = cot_db_query("SELECT tr_id FROM $db_trash WHERE tr_type='forumpost' AND tr_itemid LIKE '%-".$res['tr_itemid']."'");

			while ($row = cot_db_fetcharray($sql))
			{
				$tsql = cot_db_query("SELECT * FROM $db_trash WHERE tr_id='{$row['tr_id']}' LIMIT 1");
				if ($res2 = cot_db_fetchassoc($tsql))
				{
					$res2['tr_datas'] = unserialize($res2['tr_datas']);
				}
				cot_db_insert($db_forum_posts, $res2['tr_datas']);
				cot_db_delete($db_trash, "tr_id='".$row['tr_id']."'");
				cot_log("Post #".$res2['tr_datas']['fp_id']." restored from the trash can (belongs to topic #".$res2['tr_datas']['fp_topicid'].").", 'adm');
			}

			cot_forum_resynctopic($res['tr_itemid']);
			cot_forum_sectionsetlast($res['tr_datas']['ft_sectionid']);
			cot_forum_resync($res['tr_datas']['ft_sectionid']);
			return TRUE;
			break;

		case 'page':
			global $db_pages, $db_structure;
			cot_db_insert($db_pages, $res['tr_datas']);
			cot_log("Page #".$res['tr_itemid']." restored from the trash can.", 'adm');
			$sql = cot_db_query("SELECT page_cat FROM $db_pages WHERE page_id='".$res['tr_itemid']."'");
			$row = cot_db_fetcharray($sql);
			$sql = cot_db_query("SELECT structure_id FROM $db_structure WHERE structure_code='".$row['page_cat']."'");
			if (cot_db_numrows($sql) == 0)
			{
				$sql = cot_db_query("UPDATE $db_pages SET page_cat='restored' WHERE page_id='".$res['tr_itemid']."'");
			}
			return TRUE;
			break;

		case 'user':
			global $db_users;
			cot_db_insert($db_users, $res['tr_datas']);
			cot_log("User #".$res['tr_itemid']." restored from the trash can.", 'adm');
			return TRUE;
			break;

		default:
			cot_db_insert($res['tr_type'], $res['tr_datas']);
			cot_log("RES #".$res['tr_itemid']." restored from the trash can.", 'adm');
			return TRUE;
			break;
	}
}

?>
