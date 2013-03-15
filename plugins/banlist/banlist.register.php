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
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2013
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');
require_once cot_langfile('banlist', 'plug');

$db_banlist = (isset($db_banlist)) ? $db_banlist : $db_x . 'banlist';
$ruser['user_email'] = cot_import('ruseremail','P','TXT',64, TRUE);
$ruser['user_email'] = mb_strtolower($ruser['user_email']);
$banlist_email_mask = mb_strstr($ruser['user_email'], '@');
$banlist_email_mask_multi = explode('.', $banlist_email_mask);

$sql = $db->query("SELECT banlist_reason, banlist_email FROM $db_banlist WHERE banlist_email='".$db->prep($banlist_email_mask)."' ".
	"OR banlist_email='".$db->prep($banlist_email_mask_multi[0])."' LIMIT 1");
if ($row = $sql->fetch())
{
	cot_error($L['aut_emailbanned'].$row['banlist_reason']);
}
$sql->closeCursor();
