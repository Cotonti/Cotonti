<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=admin.extrafields.first
[END_COT_EXT]
==================== */

/**
 * Users module
 *
 * @package users
 * @version 0.9.4
 * @author esclkm, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2011
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

$extra_whitelist[$db_users] = array('name' => $db_users, 'caption' => $L['Module']. ' Users', 'help' => $L['adm_help_users_extrafield']);

?>