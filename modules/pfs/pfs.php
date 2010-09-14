<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=module
[END_COT_EXT]
==================== */

/**
 * PFS module
 *
 * @package pfs
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

cot_dieifdisabled($cfg['disable_pfs']);

// Environment setup
define('COT_PFS', TRUE);
$location = 'PFS';

// Additional API requirements
cot_require_api('uploads');
require_once './datas/extensions.php';

// Mode choice
if (!in_array($m, array('edit', 'editfolder', 'view')))
{
	$m = 'main';
}

require_once cot_incfile($z, $m);
?>
