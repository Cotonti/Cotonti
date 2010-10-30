<?php
/**
 * r1473: move forum structure to general structure
 */

defined('COT_CODE') && defined('COT_UPDATE') or die('Wrong URL');

$sql = $db->query("SELECT * FROM cot_forum_structure WHERE 1");
while ($row = $sql->fetch())
{
	$db->query("INSERT INTO cot_structure (`structure_area`, `structure_code`, `structure_path`, `structure_tpl`, `structure_title`,
   `structure_desc`, `structure_icon`, `structure_locked`, `structure_count`) VALUES
	('forums', '".$row['fn_code']."', '".$row['fn_code']."', '', '".$row['fn_title']."', '".$row['fn_desc']."', '".$row['fn_icon']."', 0, 0)");

	$auth_permit = array(COT_GROUP_DEFAULT => 7, COT_GROUP_GUESTS => 5, COT_GROUP_MEMBERS => 7);
	$auth_lock = array(COT_GROUP_DEFAULT => 0, COT_GROUP_GUESTS => 250, COT_GROUP_MEMBERS => 128);
	cot_auth_add_item($n, $rstructure['structure_code'], $auth_permit, $auth_lock);
}

$sql = $db->query("CREATE TABLE IF NOT EXISTS `cot_forum_stats` (
  `fs_id` int(11) unsigned NOT NULL auto_increment,
  `fs_code` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `fs_lt_id` int(11) NOT NULL default '0',
  `fs_lt_title` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `fs_lt_date` int(11) NOT NULL default '0',
  `fs_lt_posterid` int(11) NOT NULL default '-1',
  `fs_lt_postername` varchar(100) collate utf8_unicode_ci NOT NULL,
  `fs_topiccount` mediumint(8) NOT NULL default '0',
  `fs_topiccount_pruned` int(11) default '0',
  `fs_postcount` int(11) NOT NULL default '0',
  `fs_postcount_pruned` int(11) default '0',
  `fs_viewcount` int(11) NOT NULL default '0',
  PRIMARY KEY  (`fs_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

$sql = $db->query("SELECT * FROM cot_forum_sections WHERE 1");
while ($row = $sql->fetch())
{
	$subpath = ($row['fs_masterid'] > 0) ? $row['fs_masterid']."." : '';
	$db->query("INSERT INTO cot_structure (`structure_area`, `structure_code`, `structure_path`, `structure_tpl`, `structure_title`,
   `structure_desc`, `structure_icon`, `structure_locked`, `structure_count`) VALUES
	('forums', '".$row['fs_id']."', '".$row['fs_category'].$subpath.".".$row['fs_id']."', '', '".$row['fs_title']."',
	'".$row['fs_desc']."', '".$row['fs_icon']."', '".$row['fs_state']."', '".$row['fs_fs_state']."')");

	$db->query("INSERT INTO `cot_forum_stats` (`fs_code`, `fs_lt_id`, `fs_lt_title`, `fs_lt_date`, `fs_lt_posterid`, `fs_lt_postername`,
	`fs_topiccount`, `fs_topiccount_pruned`, `fs_postcount`, `fs_postcount_pruned`, `fs_viewcount`) VALUES
	('".$row['fs_id']."', '".$row['fs_lt_id']."', '".$row['fs_lt_title']."', '".$row['fs_lt_date']."', '".$row['fs_lt_posterid']."', '".$row['fs_lt_postername']."',
	'".$row['fs_topiccount']."', '".$row['fs_topiccount_pruned']."', '".$row['fs_postcount']."', '".$row['fs_postcount_pruned']."', '".$row['fs_viewcount']."')");
}


$sql = $db->query("DROP TABLE IF EXISTS `cot_forum_sections`");
$sql = $db->query("DROP TABLE IF EXISTS `cot_forum_structure`");

?>
