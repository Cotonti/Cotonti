<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=admin.users.update
[END_COT_EXT]
==================== */

/**
 * Users admin handler
 *
 * @package pfs
 * @version 0.9.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2011-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');

$rgroups['grp_pfs_maxfile'] = (int)min(cot_import('rmaxfile', 'P', 'INT'), cot_get_uploadmax());
$rgroups['grp_pfs_maxtotal'] = (int)cot_import('rmaxtotal', 'P', 'INT');

?>
