<?php
/**
 * r2008: Add missing auth options for page and forums after upgrade
 */

defined('COT_CODE') && defined('COT_UPDATE') or die('Wrong URL');

global $db_auth;

if ($db->query("SELECT COUNT(*) FROM `$db_auth` WHERE auth_code = 'page' AND auth_option = 'a'")->fetchColumn() == 0)
{
	cot_auth_add_item('page', 'a');
}

if ($db->query("SELECT COUNT(*) FROM `$db_auth` WHERE auth_code = 'forums' AND auth_option = 'a'")->fetchColumn() == 0)
{
	cot_auth_add_item('forums', 'a');
}

?>