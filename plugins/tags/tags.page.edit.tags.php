<?PHP
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
 * Part of plug tags
 *
 * @package Cotonti
 * @version 0.0.3
 * @author Trustmaster - Vladimir Sibirov
 * @copyright All rights reserved. 2008-2009
 * @license BSD
 */

if (!defined('SED_CODE')) { die('Wrong URL.'); }

if($cfg['plugin']['tags']['pages'] && sed_auth('plug', 'tags', 'W'))
{
	require_once(sed_langfile('tags'));
	$tags = sed_tag_list($id);
	$tags = implode(', ', $tags);
	$t->assign(array(
	'PAGEEDIT_TOP_TAGS' => $L['tags_Tags'],
	'PAGEEDIT_TOP_TAGS_HINT' => $L['tags_comma_separated'],
	'PAGEEDIT_FORM_TAGS' => '<input type="text" name="rtags" value="' . $tags . '" />'
	));
	$t->parse('MAIN.TAGS');
}

?>