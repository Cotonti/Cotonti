<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=page.add.tags,i18n.page.translate.tags
Tags=page.add.tpl:{PAGEADD_FORM_TAGS},{PAGEADD_TOP_TAGS},{PAGEADD_TOP_TAGS_HINT}
[END_COT_EXT]
==================== */

/**
 * Generates tag inputs when adding a new page
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
	$t->assign(array(
		'PAGEADD_TOP_TAGS' => $L['Tags'],
		'PAGEADD_TOP_TAGS_HINT' => $L['tags_comma_separated'],
		'PAGEADD_FORM_TAGS' => cot_rc('tags_input_editpage', array('tags' => ''))
	));
	if (cot_get_caller() == 'i18n.page')
	{
		$t->assign(array(
			'I18N_PAGE_TAGS' => implode(', ', cot_tag_list($id)),
			'I18N_IPAGE_TAGS' => cot_rc('tags_input_editpage', array('tags' => ''))
		));
	}
	$t->parse('MAIN.TAGS');
}

?>