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

defined('SED_CODE') or die('Wrong URL');

sed_dieifdisabled($cfg['disable_pfs']);

// Environment setup
define('SED_PFS', TRUE);
$location = 'PFS';

// Additional API requirements
require_once sed_incfile('uploads');
require_once './datas/extensions.php';

// Mode choice
if (!in_array($m, array('edit', 'editfolder', 'view')))
{
	$m = 'main';
}

require_once sed_incfile($m, $z);
?>
