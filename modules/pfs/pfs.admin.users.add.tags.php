<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=admin.users.add.tags
[END_COT_EXT]
==================== */

/**
 * Users admin add tags
 *
 * @package pfs
 * @version 0.9.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2011-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');

$pfs_is_active = true;

$t->assign(array(
	'ADMIN_USERS_NGRP_PFS_MAXFILE' => cot_inputbox('text', 'rmaxfile', '', 'size="16" maxlength="16"'),
	'ADMIN_USERS_NGRP_PFS_MAXTOTAL' => cot_inputbox('text', 'rmaxtotal', '', 'size="16" maxlength="16"')
));

?>
