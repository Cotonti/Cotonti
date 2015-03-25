<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=users.profile.update.first,users.edit.update.first
[END_COT_EXT]
==================== */

/**
 * PM user edit profile first
 *
 * @package PM
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL.');

$ruser['user_pmnotify'] = (int)cot_import('ruserpmnotify','P','BOL');
