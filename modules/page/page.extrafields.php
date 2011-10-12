<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=admin.extrafields.first
[END_COT_EXT]
==================== */

/**
 * Page module
 *
 * @package page
 * @version 0.7.0
 * @author esclkm, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2011
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('page', 'module');
$extra_whitelist[$db_pages] = array('name' => $db_pages, 'caption' => $L['Module'].' Pages', 'help' => $L['adm_help_pages_extrafield']);

?>