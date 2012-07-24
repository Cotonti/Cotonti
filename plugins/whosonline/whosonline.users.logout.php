<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=users.logout
[END_COT_EXT]
==================== */

/**
 * Removes a user from online table on logout
 *
 * @package whosonline
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

if ($usr['id'] > 0)
{
	$db->delete($db_online, "online_userid='{$usr['id']}'");
}

?>