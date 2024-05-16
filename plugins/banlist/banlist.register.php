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
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_langfile('banlist', 'plug');

Cot::$db->registerTable('banlist');

$ruser['user_email'] = cot_import('ruseremail', 'P', 'TXT', 64, TRUE);
if (!empty($ruser['user_email'])) {
    $ruser['user_email'] = mb_strtolower($ruser['user_email']);
    $banlist_email_mask = mb_strstr($ruser['user_email'], '@');
    $banlist_email_mask_multi = explode('.', $banlist_email_mask);

    $sql = Cot::$db->query(
        'SELECT banlist_reason, banlist_email FROM ' . Cot::$db->banlist
        . ' WHERE banlist_email = :email1 OR banlist_email = :email2 LIMIT 1',
        ['email1' => $banlist_email_mask, 'email2' => $banlist_email_mask_multi[0]]
    );
    if ($row = $sql->fetch()) {
        cot_error(Cot::$L['aut_emailbanned'] . $row['banlist_reason']);
    }
    $sql->closeCursor();
}