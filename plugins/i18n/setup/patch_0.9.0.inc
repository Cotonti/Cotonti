<?php
/*
 * Patch for tags plugin integration
 */

if (cot_plugin_active('tags'))
{
	require_once cot_incfile('tags', 'plug');
	global $db_tag_references;
	$db->query("ALTER TABLE $db_tag_references DROP PRIMARY KEY");
	$db->query("ALTER TABLE $db_tag_references ADD PRIMARY KEY (`tag`,`tag_area`,`tag_item`, `tag_locale`)");
}
?>
