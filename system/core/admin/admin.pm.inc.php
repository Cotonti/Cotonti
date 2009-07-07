<?php
/**
 * Administration panel - PM manager
 *
 * @package Cotonti
 * @version 0.1.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

(defined('SED_CODE') && defined('SED_ADMIN')) or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('pm', 'a');
sed_block($usr['isadmin']);

$t = new XTemplate(sed_skinfile('admin.pm.inc', false, true));

$adminpath[] = array(sed_url('admin', 'm=other'), $L['Other']);
$adminpath[] = array(sed_url('admin', 'm=pm'), $L['Private_Messages']);
$adminhelp = $L['adm_help_pm'];

$totalpmdb = sed_sql_rowcount($db_pm);
$totalpmsent = sed_stat_get('totalpms');

$t -> assign(array(
	"ADMIN_PM_URL_CONFIG" => sed_url('admin', "m=config&n=edit&o=core&p=pm"),
	"ADMIN_PM_TOTALPMDB" => $totalpmdb,
	"ADMIN_PM_TOTALPMSENT" => $totalpmsent
));

/* === Hook === */
$extp = sed_getextplugins('admin.pm.tags');
if(is_array($extp))
{
	foreach($extp as $k => $pl)
	{
		include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php');
	}
}
/* ===== */

$t -> parse("PM");
$adminmain = $t -> text("PM");

?>