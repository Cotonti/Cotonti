<?php
/**
 * Administration panel - Home page for administrators
 *
 * @package Cotonti
 * @version 0.1.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

(defined('SED_CODE') && defined('SED_ADMIN')) or die('Wrong URL.');

//Version Checking
preg_match('/Rev: ([0-9]+)/', $cfg['svnrevision'], $revmatch);
$cfg['svnrevision'] = $revmatch[1];
unset($revmatch);
if($cfg['svnrevision'] > $cfg['revision'])
{
	$cfg['revision'] = $cfg['svnrevision'];
	sed_sql_query("UPDATE ".$db_config." SET `config_value`= ".(int)$cfg['svnrevision']." WHERE `config_owner` = 'core' AND `config_cat` = 'version' AND `config_name` = 'revision' LIMIT 1");
}

$t = new XTemplate(sed_skinfile('admin.home.inc', false, true));

$adminpath[] = array(sed_url('admin', 'm=home'), $L['Home']);

$pagesqueued = sed_sql_query("SELECT COUNT(*) FROM $db_pages WHERE page_state='1'");
$pagesqueued = sed_sql_result($pagesqueued, 0, "COUNT(*)");

if(!function_exists('gd_info') && $cfg['th_amode'] != 'Disabled')
{
	$is_adminwarnings = true;
}

$t -> assign(array(

	"ADMIN_HOME_URL" => sed_url('admin', "m=page&s=queue"),
	"ADMIN_HOME_PAGESQUEUED" => $pagesqueued
));
$t -> parse("HOME");
$adminmain = $t -> text("HOME");

/* === Hook for the plugins === */
$extp = sed_getextplugins('admin.home', 'R');
if(is_array($extp))
{
	foreach($extp as $k => $pl)
	{
		include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php');
	}
}

if($cfg['trash_prunedelay'] > 0)
{
	$timeago = $sys['now_offset'] - ($cfg['trash_prunedelay'] * 86400);
	$sqltmp = sed_sql_query("DELETE FROM $db_trash WHERE tr_date<$timeago");
	$deleted = mysql_affected_rows();
	if($deleted > 0)
	{
		sed_log($deleted.' old item(s) removed from the trashcan, older than '.$cfg['trash_prunedelay'].' days', 'adm');
	}
}

?>