<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=admin.users.edit.tags
[END_COT_EXT]
==================== */

/**
 * Hidden groups
 *
 * @package HiddenGroups
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

$hidden_groups = true;

$t->assign('ADMIN_USERS_EDITFORM_GRP_HIDDEN', cot_radiobox($row['grp_hidden'], 'rhidden', array(1, 0), array($L['Yes'], $L['No'])));
