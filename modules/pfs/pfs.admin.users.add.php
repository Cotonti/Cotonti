<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=admin.users.add.first
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

$rgroups['grp_pfs_maxfile'] = (int)min(cot_import('rmaxfile', 'P', 'INT'), cot_get_uploadmax());
$rgroups['grp_pfs_maxtotal'] = (int)cot_import('rmaxtotal', 'P', 'INT');
