<?php defined('COT_CODE') or die('Wrong URL');

if(!(bool)$db->query("SHOW INDEXES FROM `cot_tag_references` WHERE `Key_name`='tag_area_item'")->fetch())
{
	$db->query("ALTER TABLE `cot_tag_references` ADD INDEX `tag_area_item` (`tag_area`, `tag_item`)");
}

if((bool)$db->query("SHOW INDEXES FROM `cot_tag_references` WHERE `Key_name`='tag_item'")->fetch())
{
	$db->query("ALTER TABLE `cot_tag_references` DROP INDEX `tag_item`");
}