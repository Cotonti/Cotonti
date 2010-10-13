<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=tools
[END_COT_EXT]
==================== */

/**
 * Move all topics from one section to another section in forums.
 *
 * @package massmovetopics
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

cot_require('forums');

$plugin_title = "Mass-move topics in forums";

$sourceid = cot_import('sourceid', 'P', 'INT');
$targetid = cot_import('targetid', 'P', 'INT');

$t = new XTemplate(cot_skinfile('massmovetopics', true));

if ($a == 'move')
{
	$sql = $cot_db->query("UPDATE $db_forum_topics SET ft_sectionid='$targetid' WHERE ft_sectionid='$sourceid'");
	$sql = $cot_db->query("UPDATE $db_forum_posts SET fp_sectionid='$targetid' WHERE fp_sectionid='$sourceid'");
	cot_forum_sectionsetlast($sourceid);
	cot_forum_sectionsetlast($targetid);
	cot_forum_resync($sourceid);
	cot_forum_resync($targetid);
	$t->parse('MAIN.MASSMOVETOPICS_MOVE_DONE');
}

$sql = $cot_db->query("SELECT s.fs_id, s.fs_title, s.fs_category FROM $db_forum_sections AS s
LEFT JOIN $db_forum_structure AS n ON n.fn_code=s.fs_category
ORDER by fn_path ASC, fs_order ASC");

while ($row = $sql->fetch())
{
	$t->assign(array(
		'MASSMOVETOPICS_SELECT_SOURCE_NAME' => cot_build_forums($row['fs_id'], $row['fs_title'], $row['fs_category']),
		'MASSMOVETOPICS_SELECT_SOURCE_FS_ID' => $row['fs_id']
	));
	$t->parse('MAIN.MASSMOVETOPICS_SELECT_SOURCE');
	$t->assign(array(
		'MASSMOVETOPICS_SELECT_TARGET_NAME' => cot_build_forums($row['fs_id'], $row['fs_title'], $row['fs_category']),
		'MASSMOVETOPICS_SELECT_TARGET_FS_ID' => $row['fs_id']
	));
	$t->parse('MAIN.MASSMOVETOPICS_SELECT_TARGET');
}

$t->assign(array('MASSMOVETOPICS_FORM_URL' => cot_url('admin', 'm=tools&p=massmovetopics&a=move')));
$t->parse("MAIN");
$plugin_body .= $t->text("MAIN");

?>