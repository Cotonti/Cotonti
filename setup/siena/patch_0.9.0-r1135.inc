<?php
/**
 * r1135: insert new Tags plugin parts if the plugin is installed
 */

defined('COT_CODE') && defined('COT_UPDATE') or die('Wrong URL');

$sql = $db->query("SELECT COUNT(*) FROM $db_plugins WHERE pl_code = 'tags'");
$count = $sql->fetchColumn();
if ($count > 0)
{
	$sql = $db->query("SELECT COUNT(*) FROM $db_plugins WHERE pl_code = 'tags' AND pl_hook = 'page.main'");
	$count = $sql->fetchColumn();
	if ($count == 0)
	{
		$db->query("INSERT INTO `sed_plugins` (`pl_hook`, `pl_code`, `pl_part`, `pl_title`, `pl_file`)
			VALUES ('page.main', 'tags', 'page.main', 'Tags', 'tags.page.main')");
	}
	$sql = $db->query("SELECT COUNT(*) FROM $db_plugins WHERE pl_code = 'tags' AND pl_hook = 'forums.posts.main'");
	$count = $sql->fetchColumn();
	if ($count == 0)
	{
		$db->query("INSERT INTO `sed_plugins` (`pl_hook`, `pl_code`, `pl_part`, `pl_title`, `pl_file`)
			VALUES ('forums.posts.main', 'tags', 'forums.posts.main', 'Tags', 'tags.forums.posts.main')");
	}
}
?>
