<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=module
[END_COT_EXT]
==================== */

/**
 * PM module
 *
 * @package pm
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

sed_dieifdisabled($cfg['disable_pm']);

// Environment setup
define('SED_PM', TRUE);
$location = 'Private_Messages';

// Additional API requirements
sed_require_api('extrafields');
sed_require('users');

// Mode choice
if (!in_array($m, array('send', 'message')))
{
	$m = 'folder';
}

require_once sed_incfile($z, $m);
?>
