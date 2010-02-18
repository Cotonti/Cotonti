<?php
/**
 * r1135: insert new Tags plugin parts if the plugin is installed
 */

defined('SED_CODE') && defined('COT_UPDATE') or die('Wrong URL');

$sql = sed_sql_query("SELECT COUNT(*) FROM $db_plugins WHERE pl_code = 'tags'");
$count = sed_sql_result($sql, 0, 0);
if ($count > 0)
{
	$sql = sed_sql_query("SELECT COUNT(*) FROM $db_plugins WHERE pl_code = 'tags' AND pl_hook = 'page.main'");
	$count = sed_sql_result($sql, 0, 0);
	if ($count == 0)
	{
		sed_sql_query("INSERT INTO `sed_plugins` (`pl_hook`, `pl_code`, `pl_part`, `pl_title`, `pl_file`)
			VALUES ('page.main', 'tags', 'page.main', 'Tags', 'tags.page.main')");
	}
	$sql = sed_sql_query("SELECT COUNT(*) FROM $db_plugins WHERE pl_code = 'tags' AND pl_hook = 'forums.posts.main'");
	$count = sed_sql_result($sql, 0, 0);
	if ($count == 0)
	{
		sed_sql_query("INSERT INTO `sed_plugins` (`pl_hook`, `pl_code`, `pl_part`, `pl_title`, `pl_file`)
			VALUES ('forums.posts.main', 'tags', 'forums.posts.main', 'Tags', 'tags.forums.posts.main')");
	}
}
?>
