<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=tools
[END_COT_EXT]
==================== */

/**
 * Avatar and photo for users
 *
 * @package UserImages
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('users', 'a');
cot_block($usr['isadmin']);

$tt = new XTemplate(cot_tplfile('userimages.admin', 'plug', true));
require_once cot_incfile('userimages', 'plug');
require_once cot_incfile('userimages', 'plug', 'resources');
require_once cot_langfile('userimages');
require_once cot_incfile('configuration');

$adminHelp = $L['userimages_help'];
$adminTitle = $L['userimages_title'];

/* === Hook === */
foreach (cot_getextplugins('userimages.admin.first') as $pl) {
	include $pl;
}
/* ===== */

if ($a == 'add') {
	$code = cot_import('userimg_code', 'P', 'ALP');
	$width = cot_import('userimg_width', 'P', 'INT');
	$height = cot_import('userimg_height', 'P', 'INT');
	$crop = cot_import('userimg_crop', 'P', 'TXT');
	if (!cot_userimages_config_add($code, $width, $height, $crop)) {
		cot_error('userimages_emptycode', 'userimg_code');
	} else {
		cot_message('alreadyaddnewentry');
	}
	cot_redirect(cot_url('admin', 'm=other&p=userimages', '', true));
}
if ($a == 'edit') {
	$code = cot_import('code', 'G', 'ALP');
	$width = cot_import('userimg_width', 'P', 'INT');
	$height = cot_import('userimg_height', 'P', 'INT');
	$crop = cot_import('userimg_crop', 'P', 'TXT');
	if (!cot_userimages_config_edit($code, $width, $height, $crop))
	{
		cot_error('userimages_emptycode', 'code');
	} else {
		cot_message('alreadyupdatednewentry');
	}
	cot_redirect(cot_url('admin', 'm=other&p=userimages', '', true));
}
if ($a == 'remove') {
	$code = cot_import('code', 'G', 'ALP');
	if (!cot_userimages_config_remove($code)) {
		cot_error('userimages_emptycode');
	} else {
		cot_message('alreadydeletednewentry');
	}
	cot_redirect(cot_url('admin', 'm=other&p=userimages', '', true));
}

$userimg = cot_userimages_config_get(true);
foreach ($userimg as $code => $settings) {
	$tt->assign([
		'CODE' => $code,
		'WIDTH' => $settings['width'],
		'HEIGHT' => $settings['height'],
		'CROP' => $settings['crop'],
		'EDIT_URL' => cot_url('admin', 'm=other&p=userimages&a=edit&code='.$code),
		'REMOVE' => cot_rc('userimg_remove', ['url' => cot_url('admin', 'm=other&p=userimages&a=remove&code='.$code)]),
	]);
	$tt->parse('MAIN.USERIMG_LIST');
}

cot_display_messages($tt); // use cot_message()

/* === Hook  === */
foreach (cot_getextplugins('userimages.admin.tags') as $pl) {
	include $pl;
}
/* ===== */

$tt->parse('MAIN');
$pluginBody = $tt->text('MAIN');