<?php
/**
 * Administration panel - Other Admin parts listing
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

(defined('SED_CODE') && defined('SED_ADMIN')) or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('admin', 'a');
sed_block($usr['auth_read']);

$t = new XTemplate(sed_skinfile('admin.other'));

$adminpath[] = array(sed_url('admin', 'm=other'), $L['Other']);

/* === Hook === */
foreach (sed_getextplugins('admin.other.first') as $pl)
{
	include $pl;
}
/* ===== */

$t->assign(array(
	'ADMIN_OTHER_URL_CACHE' => sed_url('admin', 'm=cache'),
	'ADMIN_OTHER_URL_DISKCACHE' => sed_url('admin', 'm=cache&s=disk'),
	'ADMIN_OTHER_URL_BBCODE' => sed_url('admin', 'm=bbcode'),
	'ADMIN_OTHER_URL_URLS' => sed_url('admin', 'm=urls'),
	'ADMIN_OTHER_URL_BANLIST' => sed_url('admin', 'm=banlist'),
	'ADMIN_OTHER_URL_HITS' => sed_url('admin', 'm=hits'),
	'ADMIN_OTHER_URL_REFERS' => sed_url('admin', 'm=referers'),
	'ADMIN_OTHER_URL_LOG' => sed_url('admin', 'm=log'),
	'ADMIN_OTHER_URL_INFOS' => sed_url('admin', 'm=infos'),
	'ADMIN_OTHER_URL_RATINGS' => sed_url('admin', 'm=ratings')
));

/* === Hook  === */
foreach (sed_getextplugins('admin.other.tags') as $pl)
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