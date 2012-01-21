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
 * @package tags
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
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

?>