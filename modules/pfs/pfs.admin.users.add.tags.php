<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=admin.users.add.tags
[END_COT_EXT]
==================== */

/**
 * Users admin add tags
 *
 * @package PFS
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL.');

$pfs_is_active = true;

$t->assign(array(
	'ADMIN_USERS_NGRP_PFS_MAXFILE' => cot_inputbox('text', 'rmaxfile', '', 'size="16" maxlength="16"'),
	'ADMIN_USERS_NGRP_PFS_MAXTOTAL' => cot_inputbox('text', 'rmaxtotal', '', 'size="16" maxlength="16"')
));
