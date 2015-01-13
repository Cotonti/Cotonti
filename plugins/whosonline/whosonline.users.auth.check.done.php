<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=users.auth.check.done
[END_COT_EXT]
==================== */

/**
 * Removes a guest from online table on login
 *
 * @package WhosOnline
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

$db->delete($db_online, '(online_ip=\''.$usr['ip'].'\' AND online_name=\'v\') OR online_name='.$db->quote($row['user_name']));
