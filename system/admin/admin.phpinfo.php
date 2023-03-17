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

$t = new XTemplate(cot_tplfile('admin.phpinfo', 'core'));

$adminpath[] = array(cot_url('admin', 'm=other'), cot::$L['Other']);
$adminpath[] = array(cot_url('admin', 'm=phpinfo'), 'PHP');

$adminTitle = cot::$L['adm_phpinfo'];

ob_start();
ob_implicit_flush(false);
phpinfo();
$result = ob_get_clean();

$result = preg_replace('/\s?<!DOCTYPE[^>]*?>\s?/si', '', $result);
$result = preg_replace('/\s?<head[^>]*?>.*?<\/head>\s?/si', '', $result);
$result = preg_replace('/\s?<html[^>]*?>\s?/si', '', $result);
$result = str_replace(['<body>', '</body>', '</html>'], '', $result);

$t->assign(array(
	'ADMIN_PHPINFO' => $result,
));

/* === Hook === */
foreach (cot_getextplugins('admin.infos.tags') as $pl) {
	include $pl;
}
/* ===== */

$t->parse('MAIN');
$adminmain = $t->text('MAIN');
