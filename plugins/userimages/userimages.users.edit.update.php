<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=users.edit.update.first
[END_COT_EXT]
==================== */

/**
 * Avatar and photo for users
 *
 * @package userimages
 * @version 1.1
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2013
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('userimages', 'plug');
$userimages = cot_userimages_config_get();
$ruserdelete = cot_import('ruserdelete','P','BOL');

foreach($userimages as $code => $settings)
{
	$ruser["user_$code"] = cot_import("ruser$code",'P','TXT');
	if($ruserdelete)
	{
		if(file_exists($ruser["user_$code"]))
		{
			@unlink($ruser["user_$code"]);
		}
	}
}
