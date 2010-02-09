<?php
/* ====================
[BEGIN_SED_EXTPLUGIN]
Code=tags
Part=page.edit.tags
File=tags.page.edit.tags
Hooks=page.edit.tags
Tags=page.edit.tpl:{PAGEEDIT_FORM_TAGS},{PAGEEDIT_TOP_TAGS},{PAGEEDIT_TOP_TAGS_HINT}
Order=10
[END_SED_EXTPLUGIN]
==================== */

/**
 * Generates tags input when editing a page
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Trustmaster - Vladimir Sibirov
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

if($cfg['plugin']['tags']['pages'] && sed_auth('plug', 'tags', 'W'))
{
	require_once $cfg['system_dir'] . '/tags.php';
	require_once sed_langfile('tags');
	require_once $cfg['plugins_dir'].'/tags/inc/resources.php';
	$tags = sed_tag_list($id);
	$tags = implode(', ', $tags);
	$t->assign(array(
		'PAGEEDIT_TOP_TAGS' => $L['Tags'],
		'PAGEEDIT_TOP_TAGS_HINT' => $L['tags_comma_separated'],
		'PAGEEDIT_FORM_TAGS' => sed_rc('tags_input_editpage')
	));
	$t->parse('MAIN.TAGS');
}

?>