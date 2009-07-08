<?php
/**
 * Administration panel - Pages manager
 *
 * @package Cotonti
 * @version 0.1.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

(defined('SED_CODE') && defined('SED_ADMIN')) or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('page', 'any');
sed_block($usr['isadmin']);

$t = new XTemplate(sed_skinfile('admin.page.inc', false, true));

$adminpath[] = array(sed_url('admin', 'm=page'), $L['Pages']);
$adminhelp = $L['adm_help_page'];

$totaldbpages = sed_sql_rowcount($db_pages);
$sql = sed_sql_query("SELECT COUNT(*) FROM $db_pages WHERE page_state=1");
$sys['pagesqueued'] = sed_sql_result($sql, 0, 'COUNT(*)');

$lincif_conf = sed_auth('admin', 'a', 'A');
$lincif_page = sed_auth('page', 'any', 'A');

$t -> assign(array(
	"ADMIN_PAGE_URL_CONFIG" => sed_url('admin', "m=config&n=edit&o=core&p=page"),
	"ADMIN_PAGE_URL_ADD" => sed_url('page', 'm=add'),
	"ADMIN_PAGE_URL_QUEUE" => sed_url('admin', 'm=page&s=queue'),
	"ADMIN_PAGE_QUEUE" => $sys['pagesqueued'],
	"ADMIN_PAGE_URL_STRUCTURE" => sed_url('admin', 'm=page&s=structure'),
	"ADMIN_PAGE_URL_EXTRAFIELDS" => sed_url('admin', 'm=page&s=extrafields'),
	"ADMIN_PAGE_URL_CATORDER" => sed_url('admin', 'm=page&s=catorder'),
	"ADMIN_PAGE_URL_LIST_ALL" => sed_url('list', 'c=all'),
	"ADMIN_PAGE_TOTALDBPAGES" => $totaldbpages
));

/* === Hook  === */
$extp = sed_getextplugins('admin.page.tags');
if (is_array($extp))
{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

$t -> parse("PAGE");
$adminmain = $t -> text("PAGE");

?>