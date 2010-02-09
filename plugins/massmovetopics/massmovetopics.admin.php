<?PHP
/* ====================
[BEGIN_SED_EXTPLUGIN]
Code=massmovetopics
Part=admin
File=massmovetopics.admin
Hooks=tools
Tags=
Order=10
[END_SED_EXTPLUGIN]
==================== */

/**
 * Move all topics from one section to another section in forums.
 *
 * @package Cotonti
 * @version 0.0.3
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

require_once $cfg['modules_dir'] . '/forums/functions.php';

$plugin_title = "Mass-move topics in forums";

$sourceid = sed_import('sourceid','P','INT');
$targetid = sed_import('targetid','P','INT');

$t = new XTemplate(sed_skinfile('massmovetopics', true));

if($a == 'move')
{
	$sql = sed_sql_query("UPDATE $db_forum_topics SET ft_sectionid='$targetid' WHERE ft_sectionid='$sourceid'");
	$sql = sed_sql_query("UPDATE $db_forum_posts SET fp_sectionid='$targetid' WHERE fp_sectionid='$sourceid'");
	sed_forum_sectionsetlast($sourceid);
	sed_forum_sectionsetlast($targetid);
	sed_forum_resync($sourceid);
	sed_forum_resync($targetid);
	$t -> parse('MAIN.MASSMOVETOPICS_MOVE_DONE');
}

$sql = sed_sql_query("SELECT s.fs_id, s.fs_title, s.fs_category FROM $db_forum_sections AS s
LEFT JOIN $db_forum_structure AS n ON n.fn_code=s.fs_category
ORDER by fn_path ASC, fs_order ASC");

while($row = sed_sql_fetcharray($sql))
{
	$t -> assign(array(
		'MASSMOVETOPICS_SELECT_SOURCE_NAME' => sed_build_forums($row['fs_id'], $row['fs_title'], $row['fs_category']),
		'MASSMOVETOPICS_SELECT_SOURCE_FS_ID' => $row['fs_id']
	));
	$t -> parse('MAIN.MASSMOVETOPICS_SELECT_SOURCE');
	$t -> assign(array(
		'MASSMOVETOPICS_SELECT_TARGET_NAME' => sed_build_forums($row['fs_id'], $row['fs_title'], $row['fs_category']),
		'MASSMOVETOPICS_SELECT_TARGET_FS_ID' => $row['fs_id']
	));
	$t -> parse('MAIN.MASSMOVETOPICS_SELECT_TARGET');
}

$t -> assign(array('MASSMOVETOPICS_FORM_URL' => sed_url('admin', 'm=tools&p=massmovetopics&a=move')));
$t -> parse("MAIN");
$plugin_body .= $t -> text("MAIN");

?>