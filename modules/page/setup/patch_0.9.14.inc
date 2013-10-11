<?php defined('COT_CODE') or die('Wrong URL');

$page_add_indexes = array(
	'page_ownerid',
	'page_begin',
	'page_expire',
	'page_title'
);

foreach($page_add_indexes as $index)
{
	if(!(bool)$db->query("SHOW INDEXES FROM `cot_pages` WHERE `Key_name`='".$index."'")->fetch())
	{
		$db->query("ALTER TABLE `cot_pages` ADD INDEX `".$index."` (`".$index."`)");
	}
}