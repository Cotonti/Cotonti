<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=admin
[END_COT_EXT]
==================== */

/**
 * Administration panel - PM manager
 *
 * @package pm
 * @version 0.1.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('pm', 'a');
cot_block($usr['isadmin']);

require_once cot_incfile('pm', 'module');

$t = new XTemplate(cot_tplfile('pm.admin', 'module', true));

$adminpath[] = array(cot_url('admin', 'm=extensions'), $L['Extensions']);
$adminpath[] = array(cot_url('admin', 'm=extensions&a=details&mod='.$m), $cot_modules[$m]['title']);
$adminpath[] = array(cot_url('admin', 'm='.$m), $L['Administration']);
$adminhelp = $L['adm_help_pm'];

$totalpmdb = $db->countRows($db_pm);
if(function_exists('cot_stat_get'))
{
	$totalpmsent = cot_stat_get('totalpms');
}

$t->assign(array(
	'ADMIN_PM_URL_CONFIG' => cot_url('admin', 'm=config&n=edit&o=module&p=pm'),
	'ADMIN_PM_TOTALPMDB' => $totalpmdb,
	'ADMIN_PM_TOTALPMSENT' => $totalpmsent
));

/* === Hook === */
foreach (cot_getextplugins('pm.admin.tags') as $pl)
{
	include $pl;
}
/* ===== */

$t->parse('MAIN');
$adminmain = $t->text('MAIN');

?>