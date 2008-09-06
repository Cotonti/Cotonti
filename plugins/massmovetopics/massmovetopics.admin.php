<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net

[BEGIN_SED]
File=plugins/massmovetopics/massmovetopics.php
Version=110
Updated=2006-sep-28
Type=Plugin
Author=Neocrome
Description=
[END_SED]

[BEGIN_SED_EXTPLUGIN]
Code=massmovetopics
Part=admin
File=massmovetopics.admin
Hooks=tools
Tags=
Order=10
[END_SED_EXTPLUGIN]

==================== */

if (!defined('SED_CODE')) { die('Wrong URL.'); }

$plugin_title = "Mass-move topics in forums";

$sourceid = sed_import('sourceid','P','INT');
$targetid = sed_import('targetid','P','INT');

if ($a=='move')
	{
	$sql = sed_sql_query("UPDATE $db_forum_topics SET ft_sectionid='$targetid' WHERE ft_sectionid='$sourceid'");
	$sql = sed_sql_query("UPDATE $db_forum_posts SET fp_sectionid='$targetid' WHERE fp_sectionid='$sourceid'");
	sed_forum_sectionsetlast($sourceid);
	sed_forum_sectionsetlast($targetid);
	sed_forum_resync($sourceid);
	sed_forum_resync($targetid);
	$plugin_body .= "Done !";
	}
else
	{
	$sql = sed_sql_query("SELECT s.fs_id, s.fs_title, s.fs_category FROM $db_forum_sections AS s 
		LEFT JOIN $db_forum_structure AS n ON n.fn_code=s.fs_category
    	ORDER by fn_path ASC, fs_order ASC");
	
	$select_source = "<select name=\"sourceid\">";
	$select_target = "<select name=\"targetid\">";

	while ($row = sed_sql_fetcharray($sql))
		{
		$select_option = "<option value=\"".$row['fs_id']."\">".sed_build_forums($row['fs_id'], $row['fs_title'], $row['fs_category'])."</option>";
		$select_source .= $select_option;
		$select_target .= $select_option;
		}
	$select_source .= "</select>";
	$select_target .= "</select>";

	$plugin_body .= "<form id=\"massmovetopics\" action=\"admin.php?m=tools&amp;p=massmovetopics&amp;a=move\" method=\"post\">";
	$plugin_body .= "Move all the topics and posts from the section : ".$select_source."<br />&nbsp;<br />";
	$plugin_body .= "... to the section : ".$select_target."<br />&nbsp;<br />";
	$plugin_body .= "<input type=\"submit\" class=\"submit\" value=\"".$L['Move']."\" />";
	$plugin_body .= "</form>";
	}

?>
