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

$t = new XTemplate(cot_tplfile('admin.phpinfo', 'core'));

$adminpath[] = [cot_url('admin', 'm=other'), Cot::$L['Other'],];
$adminpath[] = [cot_url('admin', 'm=phpinfo'), 'PHP',];

$adminTitle = Cot::$L['adm_phpinfo'];

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
