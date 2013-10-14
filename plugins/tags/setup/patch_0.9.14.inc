<?php defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('tags', 'plug');

if(!$db->indexExists($db_tag_references, 'tag_area_item', array('tag_area', 'tag_item')))
{
	$db->addIndex($db_tag_references, 'tag_area_item', array('tag_area', 'tag_item'));
}

if($db->indexExists($db_tag_references, 'tag_item'))
{
	$db->query("ALTER TABLE `cot_tag_references` DROP INDEX `tag_item`");
}