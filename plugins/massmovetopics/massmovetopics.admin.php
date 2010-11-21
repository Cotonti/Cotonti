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
	$sql = $db->query("UPDATE $db_forum_topics SET ft_cat='$targetid' WHERE ft_cat='$sourceid'");
	$sql = $db->query("UPDATE $db_forum_posts SET fp_cat='$targetid' WHERE fp_cat='$sourceid'");
	
	cot_forums_count($sourceid);
	cot_forums_count($targetid);
	$t->parse('MAIN.MASSMOVETOPICS_MOVE_DONE');
}

foreach($structure['forums'] as $key => $val)
{
	$t->assign(array(
		'MASSMOVETOPICS_SELECT_SOURCE_NAME' => $val['tpath'],
		'MASSMOVETOPICS_SELECT_SOURCE_FS_ID' => $key
	));
	$t->parse('MAIN.MASSMOVETOPICS_SELECT_SOURCE');
	$t->assign(array(
		'MASSMOVETOPICS_SELECT_TARGET_NAME' => $val['tpath'],
		'MASSMOVETOPICS_SELECT_TARGET_FS_ID' => $key
	));
	$t->parse('MAIN.MASSMOVETOPICS_SELECT_TARGET');
}

$t->assign(array('MASSMOVETOPICS_FORM_URL' => cot_url('admin', 'm=tools&p=massmovetopics&a=move')));
$t->parse("MAIN");
$plugin_body .= $t->text("MAIN");

?>