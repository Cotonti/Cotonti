<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=users.register.add.first
[END_COT_EXT]
==================== */

/**
 * Banlist
 *
 * @package Banlist
 * @version 0.9.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');
require_once cot_langfile('banlist', 'plug');

$db_banlist = (isset($db_banlist)) ? $db_banlist : $db_x . 'banlist';
$ruser['user_email'] = cot_import('ruseremail','P','TXT',64, TRUE);
$ruser['user_email'] = mb_strtolower($ruser['user_email']);

$sql = $db->query("SELECT banlist_reason, banlist_email FROM $db_banlist WHERE banlist_email LIKE'%".$db->prep($ruser['user_email'])."%'");
if ($row = $sql->fetch())
{
	cot_error($L['aut_emailbanned'].$row['banlist_reason']);
}
$sql->closeCursor();


?>