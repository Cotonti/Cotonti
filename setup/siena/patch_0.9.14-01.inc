<?php defined('COT_CODE') or die('Wrong URL');

$users_add_indexes = array(
	'user_name',
	'user_maingrp',
	'user_email',
	'user_sid',
	'user_lostpass'
);

global $db_users;

foreach($users_add_indexes as $index)
{
	if(!$db->indexExists($db_users, $index, $index))
	{
		$db->addIndex($db_users, $index);
	}
}