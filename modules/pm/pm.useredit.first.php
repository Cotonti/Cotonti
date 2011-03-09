<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=users.profile.update.first,users.edit.update.first
[END_COT_EXT]
==================== */

/**
 * PM user edit profile first
 *
 * @package pm
 * @version 0.9.0
 * @author esclkm, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2011
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');

$ruser['user_pmnotify'] = cot_import('ruserpmnotify','P','BOL');

?>