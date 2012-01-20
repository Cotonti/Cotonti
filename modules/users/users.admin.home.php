<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=admin.home.sidepanel
[END_COT_EXT]
==================== */

/**
 * Users module
 *
 * @package users
 * @version 0.9.4
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */
defined('COT_CODE') or die('Wrong URL');

$tt = new XTemplate(cot_tplfile('users.admin.home', 'module', true));

require_once cot_incfile('users', 'module');

$tt->parse('MAIN');

$line = $tt->text('MAIN');

?>