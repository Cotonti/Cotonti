<?php defined('COT_CODE') or die('Wrong URL');

$page_add_indexes = array(
	'page_ownerid',
	'page_begin',
	'page_expire',
	'page_title'
);

require_once cot_incfile('page', 'module');
global $db_pages;

foreach($page_add_indexes as $index)
{
	if(!$db->indexExists($db_pages, $index, $index))
	{
		$db->addIndex($db_pages, $index);
	}
}