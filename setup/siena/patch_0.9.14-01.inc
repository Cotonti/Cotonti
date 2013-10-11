<?php defined('COT_CODE') or die('Wrong URL');

$users_add_indexes = array(
	'user_name',
	'user_maingrp',
	'user_email',
	'user_sid',
	'user_lostpass'
);

foreach($users_add_indexes as $index)
{
	if(!(bool)$db->query("SHOW INDEXES FROM `cot_users` WHERE `Key_name`='".$index."'")->fetch())
	{
		$db->query("ALTER TABLE `cot_users` ADD INDEX `".$index."` (`".$index."`)");
	}
}