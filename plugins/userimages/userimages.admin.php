<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=tools
[END_COT_EXT]
==================== */

/**
 * Avatar and photo for users
 *
 * @package userimages
 * @version 0.9.0
 * @author Koradhil, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('users', 'a');
cot_block($usr['isadmin']);

$tt = new XTemplate(cot_skinfile('userimages.admin', true));
cot_require_lang('userimages', 'plug');

$adminhelp = $L['userimages_help'];

/* === Hook === */
foreach (cot_getextplugins('userimages.admin.first') as $pl)
{
	include $pl;
}
/* ===== */



cot_display_messages($tt); // use cot_message()

/* === Hook  === */
foreach (cot_getextplugins('userimages.admin.tags') as $pl)
{
	include $pl;
}
/* ===== */

$tt->parse('MAIN');
$plugin_body = $tt->text('MAIN');

?>