<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=module
[END_COT_EXT]
==================== */

/**
 * Page module
 *
 * @package page
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

sed_dieifdisabled($cfg['disable_page']);

// Environment setup
define('SED_FORUMS', TRUE);
$location = 'Forums';

// Additional API requirements
require_once sed_incfile('extrafields');
require_once sed_incfile('functions', 'users');

// Mode choice
if (!in_array($m, array('add', 'edit')))
{
	if (isset($_GET['c']))
	{
		$m = 'list';
	}
	else
	{
		$m = 'main';
	}
}

require_once sed_incfile($m, $z);
?>