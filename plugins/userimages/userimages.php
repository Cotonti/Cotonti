<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=ajax
[END_COT_EXT]
==================== */

/**
 * Avatar and photo for users
 *
 * @package userimages
 * @version 1.1
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('userimages', 'plug');

switch ($a)
{
	case 'delete':
		cot_check_xg();
		$code = strtolower(cot_import('code', 'G', 'ALP'));
		if(in_array($code, array_keys(cot_userimages_config_get())))
		{
			$sql = $db->query("SELECT user_".$db->prep($code)." FROM $db_users WHERE user_id=".$usr['id']);
			if($filepath = $sql->fetchColumn())
			{
				if (file_exists($filepath))
				{
					unlink($filepath);
				}
				$sql = $db->update($db_users, array('user_'.$db->prep($code) => ''), "user_id=".$usr['id']);
			}
		}
		break;
}
cot_redirect(cot_url('users', "m=profile", '', true));

?>