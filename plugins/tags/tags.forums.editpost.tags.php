<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=forums.editpost.tags
Tags=forums.editpost.tpl:{FORUMS_EDITPOST_FORM_TAGS},{FORUMS_EDITPOST_TOP_TAGS},{FORUMS_EDITPOST_TOP_TAGS_HINT}
[END_COT_EXT]
==================== */

/**
 * Generates tag input when editing a forum post
 *
 * @package Tags
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

if ($cfg['plugin']['tags']['forums'] && cot_auth('plug', 'tags', 'W') && $is_first_post)
{
	require_once cot_incfile('tags', 'plug');
	$tags = cot_tag_list($q, 'forums');
	$tags = implode(', ', $tags);
	$t->assign(array(
		'FORUMS_EDITPOST_TOP_TAGS' => $L['Tags'],
		'FORUMS_EDITPOST_TOP_TAGS_HINT' => $L['tags_comma_separated'],
		'FORUMS_EDITPOST_FORM_TAGS' => cot_rc('tags_input_editpost')
	));
	$t->parse('MAIN.FORUMS_EDITPOST_TAGS');
}
