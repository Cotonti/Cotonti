<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=tools
[END_COT_EXT]
==================== */

/**
 * Avatar and photo for users
 *
 * @package userimages
 * @version 0.9.0
 * @author Koradhil, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('users', 'a');
cot_block($usr['isadmin']);

$tt = new XTemplate(cot_skinfile('userimages.admin', true));
cot_require('userimages', true);
require_once cot_incfile('userimages', 'resources', true);
cot_require_lang('userimages', 'plug');
cot_require_api('configuration');

$adminhelp = $L['userimages_help'];

/* === Hook === */
foreach (cot_getextplugins('userimages.admin.first') as $pl)
{
	include $pl;
}
/* ===== */

if($a == 'add')
{
	$code = cot_import('userimg_code', 'P', 'ALP');
	$width = cot_import('userimg_width', 'P', 'INT');
	$height = cot_import('userimg_height', 'P', 'INT');
	$crop = cot_import('userimg_crop', 'P', 'TXT');
	cot_userimages_config_add($code, $width, $height, $crop);
	cot_redirect(cot_url('admin', 'm=other&p=userimages', '', true));
}
if($a == 'remove')
{
	$code = cot_import('code', 'G', 'ALP');
	cot_userimages_config_remove($code);
	cot_redirect(cot_url('admin', 'm=other&p=userimages', '', true));
}

$userimg = cot_userimages_config_get(true);
foreach($userimg as $code => $settings)
{
	$tt->assign(array(
		'CODE' => $code,
		'WIDTH' => $settings['width'],
		'HEIGHT' => $settings['height'],
		'CROP' => $settings['crop'],
		'REMOVE' => cot_rc('userimg_remove', array('url' => cot_url('admin', 'm=other&p=userimages&a=remove&code='.$code)))
	));
	$tt->parse('MAIN.USERIMG_LIST');
}

cot_display_messages($tt); // use cot_message()

/* === Hook  === */
foreach (cot_getextplugins('userimages.admin.tags') as $pl)
{
	include $pl;
}
/* ===== */

$tt->parse('MAIN');
$plugin_body = $tt->text('MAIN');

?>