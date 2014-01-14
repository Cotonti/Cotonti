<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=users.auth.check.done
[END_COT_EXT]
==================== */

/**
 * Removes a guest from online table on login
 *
 * @package whosonline
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2014
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

$db->delete($db_online, '(online_ip=\''.$usr['ip'].'\' AND online_name=\'v\') OR online_name='.$db->quote($row['user_name']));
