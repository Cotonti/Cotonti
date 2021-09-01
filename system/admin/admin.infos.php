<?php
/**
 * Administration panel - PHP Infos
 *
 * @package Cotonti
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

list(cot::$usr['auth_read'], cot::$usr['auth_write'], cot::$usr['isadmin']) = cot_auth('admin', 'a');
cot_block(cot::$usr['auth_read']);

$t = new XTemplate(cot_tplfile('admin.infos', 'core'));

$adminpath[] = array(cot_url('admin', 'm=other'), cot::$L['Other']);
$adminpath[] = array(cot_url('admin', 'm=infos'), cot::$L['adm_infos']);
$adminhelp = cot::$L['adm_help_versions'];
$adminsubtitle = cot::$L['adm_infos'];

/* === Hook === */
foreach (cot_getextplugins('admin.infos.first') as $pl)
{
	include $pl;
}
/* ===== */

@error_reporting(0);

$t->assign(array(
	'ADMIN_INFOS_PHPVER' => (function_exists('phpversion')) ? phpversion() : cot::$L['adm_help_config'],
	'ADMIN_INFOS_ZENDVER' => (function_exists('zend_version')) ? zend_version() : cot::$L['adm_help_config'],
	'ADMIN_INFOS_INTERFACE' => (function_exists('php_sapi_name')) ? php_sapi_name() : cot::$L['adm_help_config'],
	'ADMIN_INFOS_CACHEDRIVERS' => (isset($cot_cache_drivers) && is_array($cot_cache_drivers)) ? implode(', ', $cot_cache_drivers) : '',
	'ADMIN_INFOS_OS' => (function_exists('php_uname')) ? php_uname() : $L['adm_help_config'],
	'ADMIN_INFOS_DATE' => cot_date('datetime_medium', cot::$sys['now'], false),
	'ADMIN_INFOS_GMDATE' => gmdate('Y-m-d H:i'),
	'ADMIN_INFOS_GMTTIME' => cot::$usr['gmttime'],
	'ADMIN_INFOS_USRTIME' => cot::$usr['localtime'],
	'ADMIN_INFOS_TIMETEXT' => cot::$usr['timetext']
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
