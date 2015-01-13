<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=admin.users.edit.tags
[END_COT_EXT]
==================== */

/**
 * Users admin edit tags
 *
 * @package PFS
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL.');

$pfs_is_active = true;

$t->assign(array(
	'ADMIN_USERS_EDITFORM_GRP_PFS_MAXFILE' => cot_inputbox('text', 'rmaxfile', htmlspecialchars($row['grp_pfs_maxfile']), 'size="16" maxlength="16"'),
	'ADMIN_USERS_EDITFORM_GRP_PFS_MAXTOTAL' => cot_inputbox('text', 'rmaxtotal', htmlspecialchars($row['grp_pfs_maxtotal']), 'size="16" maxlength="16"')
));
