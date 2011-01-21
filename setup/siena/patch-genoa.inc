<?php
/**
 * PHP patchset to upgrade from Genoa to Siena
 */

defined('COT_CODE') && defined('COT_UPDATE') or die('Wrong URL');

/**
 * r1318: datas/cache/templates cleanup for CoTemplate 2.5
 */

$dp = opendir('./datas/cache/templates');
while ($f = readdir($dp))
{
	$path = './datas/cache/templates/' . $f;
	if (is_file($path))
	{
		unlink($path);
	}
}
closedir($dp);

/**
 * r1473: move forum structure to general structure
 */

if (cot_module_installed('forums'))
{
	global $db_structure, $db_forum_stats, $db_forum_structure, $db_forum_sections, $db_x;
	require_once cot_incfile('forums', 'module');

	// Old forum table names, required for update
	$db_forum_structure	= isset($db_forum_structure) ? $db_forum_structure : $db_x . 'forum_structure';
	$db_forum_sections = isset($db_forum_sections) ? $db_forum_sections : $db_x . 'forum_sections';

	$sql = $db->query("SELECT * FROM `$db_forum_structure` WHERE 1");
	while ($row = $sql->fetch())
	{
		$db->query("INSERT INTO `$db_structure` (`structure_area`, `structure_code`, `structure_path`, `structure_tpl`, `structure_title`,
	   `structure_desc`, `structure_icon`, `structure_locked`, `structure_count`) VALUES
		('forums', '".$row['fn_code']."', '".$row['fn_code']."', '', '".$row['fn_title']."', '".$row['fn_desc']."', '".$row['fn_icon']."', 0, 0)");

		$auth_permit = array(COT_GROUP_DEFAULT => 'RW', COT_GROUP_GUESTS => 'R', COT_GROUP_MEMBERS => 'RW');
		$auth_lock = array(COT_GROUP_DEFAULT => '0', COT_GROUP_GUESTS => 'A', COT_GROUP_MEMBERS => '0');
		cot_auth_add_item('forums', $row['fn_code'], $auth_permit, $auth_lock);
	}

	$sql = $db->query("SELECT * FROM `$db_forum_sections` WHERE 1");
	while ($row = $sql->fetch())
	{
		$subpath = ($row['fs_masterid'] > 0) ? $row['fs_masterid']."." : '';
		$db->query("INSERT INTO `$db_structure` (`structure_area`, `structure_code`, `structure_path`, `structure_tpl`, `structure_title`,
	   `structure_desc`, `structure_icon`, `structure_locked`, `structure_count`) VALUES
		('forums', '".$row['fs_id']."', '".$row['fs_category'].$subpath.".".$row['fs_id']."', '', '".$row['fs_title']."',
		'".$row['fs_desc']."', '".$row['fs_icon']."', '".$row['fs_state']."', '".$row['fs_fs_state']."')");

		$db->query("INSERT INTO `$db_forum_stats` (`fs_cat`, `fs_lt_id`, `fs_lt_title`, `fs_lt_date`, `fs_lt_posterid`, `fs_lt_postername`,
		`fs_topiccount`, `fs_postcount`, `fs_viewcount`) VALUES
		('".$row['fs_id']."', '".$row['fs_lt_id']."', '".$row['fs_lt_title']."', '".$row['fs_lt_date']."', '".$row['fs_lt_posterid']."', '".$row['fs_lt_postername']."',
		'".$row['fs_topiccount']."', '".$row['fs_postcount']."', '".$row['fs_viewcount']."')");
	}


	$sql = $db->query("DROP TABLE IF EXISTS `$db_forum_sections`");
	$sql = $db->query("DROP TABLE IF EXISTS `$db_forum_structure`");
}

?>
