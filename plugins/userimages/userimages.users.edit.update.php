<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=users.edit.update.first
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

$ruserdelete = cot_import('ruserdelete', 'P', 'BOL');
if ($ruserdelete)
{
	$userimages = cot_userimages_config_get();
	foreach ($userimages as $code => $settings)
	{
		$sql = cot::$db->query("SELECT user_" . cot::$db->prep($code) . " FROM ".cot::$db->users." WHERE user_id=" . $id);
		if ($image = $sql->fetchColumn())
		{
			if (file_exists($image))
			{
				unlink($image);
			}
		}
	}
}
else
{
	cot_userimages_process_uploads($id);
}