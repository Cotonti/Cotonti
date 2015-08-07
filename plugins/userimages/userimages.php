<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=ajax
[END_COT_EXT]
==================== */

/**
 * Avatar and photo for users
 *
 * @package UserImages
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('userimages', 'plug');
list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('users', 'a');

switch ($a)
{
	case 'delete':
		cot_check_xg();
		$uid = cot_import('uid', 'G', 'INT');
		if ($uid && $uid != $usr['id'] && $m=='edit' && !$usr['isadmin'])
		{
			break;
		}
		if (!$uid) $uid = $usr['id'];
		$code = strtolower(cot_import('code', 'G', 'ALP'));
		if(in_array($code, array_keys(cot_userimages_config_get())))
		{
			$sql = $db->query("SELECT user_".$db->prep($code)." FROM $db_users WHERE user_id=".$uid);
			if($filepath = $sql->fetchColumn())
			{
				if (file_exists($filepath))
				{
					unlink($filepath);
				}
				$sql = $db->update($db_users, array('user_'.$db->prep($code) => ''), "user_id=".$uid);
			}
		}
		break;
}
$redir_param = array(
	'm'  => (!empty($m)) ? $m : 'profile',
	'id' => ($m=='edit') ? $uid : ''
);
cot_redirect(cot_url('users', $redir_param, '', true));
