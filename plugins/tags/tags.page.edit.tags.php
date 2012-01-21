<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=page.edit.tags,i18n.page.edit.tags
Tags=page.edit.tpl:{PAGEEDIT_FORM_TAGS},{PAGEEDIT_TOP_TAGS},{PAGEEDIT_TOP_TAGS_HINT}
[END_COT_EXT]
==================== */

/**
 * Generates tags input when editing a page
 *
 * @package tags
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

if ($cfg['plugin']['tags']['pages'] && cot_auth('plug', 'tags', 'W'))
{
	require_once cot_incfile('tags', 'plug');
	$tags_caller = cot_get_caller();
	if ($tags_caller == 'i18n.page')
	{
		$tags_extra = array('tag_locale' => $i18n_locale);
	}
	else
	{
		$tags_extra = null;
	}
	$tags = cot_tag_list($id, 'pages', $tags_extra);
	$tags = implode(', ', $tags);
	$t->assign(array(
		'PAGEEDIT_TOP_TAGS' => $L['Tags'],
		'PAGEEDIT_TOP_TAGS_HINT' => $L['tags_comma_separated'],
		'PAGEEDIT_FORM_TAGS' => cot_rc('tags_input_editpage')
	));
	if ($tags_caller == 'i18n.page')
	{
		$t->assign(array(
			'I18N_PAGE_TAGS' => implode(', ', cot_tag_list($id)),
			'I18N_IPAGE_TAGS' => cot_rc('tags_input_editpage')
		));
	}
	$t->parse('MAIN.TAGS');
}

?>