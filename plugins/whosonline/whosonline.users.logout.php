<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=users.logout
[END_COT_EXT]
==================== */

/**
 * Removes a user from online table on logout
 *
 * @package WhosOnline
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

if ($usr['id'] > 0)
{
	$db->delete($db_online, "online_userid='{$usr['id']}'");
}
