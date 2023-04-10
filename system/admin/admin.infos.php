<?php
/**
 * Administration panel - PHP Infos
 *
 * @package Cotonti
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

list(Cot::$usr['auth_read'], Cot::$usr['auth_write'], Cot::$usr['isadmin']) = cot_auth('admin', 'a');
cot_block(Cot::$usr['auth_read']);

$t = new XTemplate(cot_tplfile('admin.infos', 'core'));

$adminpath[] = array(cot_url('admin', 'm=other'), Cot::$L['Other']);
$adminpath[] = array(cot_url('admin', 'm=infos'), Cot::$L['adm_infos']);
$adminhelp = Cot::$L['adm_help_versions'];
$adminTitle = Cot::$L['adm_infos'];

/* === Hook === */
foreach (cot_getextplugins('admin.infos.first') as $pl)
{
	include $pl;
}
/* ===== */

@error_reporting(0);

$t->assign(cot_generate_infotags('ADMIN_INFOS_'));

$t->assign(array(
	'ADMIN_INFOS_PHPVER' => (function_exists('phpversion')) ? phpversion() : Cot::$L['adm_help_config'],
	'ADMIN_INFOS_ZENDVER' => (function_exists('zend_version')) ? zend_version() : Cot::$L['adm_help_config'],
	'ADMIN_INFOS_INTERFACE' => (function_exists('php_sapi_name')) ? php_sapi_name() : Cot::$L['adm_help_config'],
	'ADMIN_INFOS_CACHEDRIVERS' => (isset($cot_cache_drivers) && is_array($cot_cache_drivers)) ? implode(', ', $cot_cache_drivers) : '',
	'ADMIN_INFOS_OS' => (function_exists('php_uname')) ? php_uname() : $L['adm_help_config'],
	'ADMIN_INFOS_DATE' => cot_date('datetime_medium', Cot::$sys['now'], false),
	'ADMIN_INFOS_GMDATE' => gmdate('Y-m-d H:i'),
	'ADMIN_INFOS_GMTTIME' => Cot::$usr['gmttime'],
	'ADMIN_INFOS_USRTIME' => Cot::$usr['localtime'],
	'ADMIN_INFOS_TIMETEXT' => Cot::$usr['timetext']
));

/* === Hook === */
foreach (cot_getextplugins('admin.infos.tags') as $pl)
{
	include $pl;
}
/* ===== */

$t->parse('MAIN');
$adminmain = $t->text('MAIN');

@error_reporting(7);
