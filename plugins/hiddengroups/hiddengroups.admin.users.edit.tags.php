<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=admin.users.edit.tags
[END_COT_EXT]
==================== */

/**
 * Hidden groups
 *
 * @package hiddengroups
 * @version 1.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

$hidden_groups = true;

$t->assign('ADMIN_USERS_EDITFORM_GRP_HIDDEN', cot_radiobox($row['grp_hidden'], 'rhidden', array(1, 0), array($L['Yes'], $L['No'])));

?>