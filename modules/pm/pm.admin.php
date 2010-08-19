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

(defined('SED_CODE') && defined('SED_ADMIN')) or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('pm', 'a');
sed_block($usr['isadmin']);

$t = new XTemplate(sed_skinfile('pm.admin'));

$adminpath[] = array(sed_url('admin', 'm=other'), $L['Other']);
$adminpath[] = array(sed_url('admin', 'm=pm'), $L['Private_Messages']);
$adminhelp = $L['adm_help_pm'];

$totalpmdb = sed_sql_rowcount($db_pm);
$totalpmsent = sed_stat_get('totalpms');

$t->assign(array(
	'ADMIN_PM_URL_CONFIG' => sed_url('admin', 'm=config&n=edit&o=core&p=pm'),
	'ADMIN_PM_TOTALPMDB' => $totalpmdb,
	'ADMIN_PM_TOTALPMSENT' => $totalpmsent
));

/* === Hook === */
$extp = sed_getextplugins('admin.pm.tags');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

$t->parse('MAIN');
if (SED_AJAX)
{
	$t->out('MAIN');
}
else
{
	$adminmain = $t->text('MAIN');
}

?>