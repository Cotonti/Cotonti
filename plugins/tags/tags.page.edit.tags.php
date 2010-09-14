<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=page.edit.tags
Tags=page.edit.tpl:{PAGEEDIT_FORM_TAGS},{PAGEEDIT_TOP_TAGS},{PAGEEDIT_TOP_TAGS_HINT}
[END_COT_EXT]
==================== */

/**
 * Generates tags input when editing a page
 *
 * @package tags
 * @version 0.7.0
 * @author Trustmaster - Vladimir Sibirov
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

if ($cfg['plugin']['tags']['pages'] && cot_auth('plug', 'tags', 'W'))
{
	cot_require('tags', true);
	$tags = cot_tag_list($id);
	$tags = implode(', ', $tags);
	$t->assign(array(
		'PAGEEDIT_TOP_TAGS' => $L['Tags'],
		'PAGEEDIT_TOP_TAGS_HINT' => $L['tags_comma_separated'],
		'PAGEEDIT_FORM_TAGS' => cot_rc('tags_input_editpage')
	));
	$t->parse('MAIN.TAGS');
}

?>