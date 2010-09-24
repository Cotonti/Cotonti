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
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('pm', 'a');
cot_block($usr['isadmin']);

$t = new XTemplate(cot_skinfile('pm.admin', 'module'));

$adminpath[] = array(cot_url('admin', 'm=other'), $L['Other']);
$adminpath[] = array(cot_url('admin', 'm=pm'), $L['Private_Messages']);
$adminhelp = $L['adm_help_pm'];

$totalpmdb = cot_db_rowcount($db_pm);
$totalpmsent = cot_stat_get('totalpms');

$t->assign(array(
	'ADMIN_PM_URL_CONFIG' => cot_url('admin', 'm=config&n=edit&o=core&p=pm'),
	'ADMIN_PM_TOTALPMDB' => $totalpmdb,
	'ADMIN_PM_TOTALPMSENT' => $totalpmsent
));

/* === Hook === */
foreach (cot_getextplugins('admin.pm.tags') as $pl)
{
	include $pl;
}
/* ===== */

$t->parse('MAIN');
if (COT_AJAX)
{
	$t->out('MAIN');
}
else
{
	$adminmain = $t->text('MAIN');
}

?>