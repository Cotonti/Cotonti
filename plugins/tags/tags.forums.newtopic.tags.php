<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=forums.newtopic.tags
Tags=forums.newtopic.tpl:{FORUMS_NEWTOPIC_TOP_TAGS},{FORUMS_NEWTOPIC_TOP_TAGS_HINT},{FORUMS_NEWTOPIC_FORM_TAGS}
[END_COT_EXT]
==================== */

/**
 * Generates tag input when editing a page
 *
 * @package tags
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

if ($cfg['plugin']['tags']['forums'] && cot_auth('plug', 'tags', 'W'))
{
	require_once cot_incfile('tags', 'plug');
	$rtags = cot_import('rtags', 'P', 'TXT');
	$t->assign(array(
		'FORUMS_NEWTOPIC_TOP_TAGS' => $L['Tags'],
		'FORUMS_NEWTOPIC_TOP_TAGS_HINT' => $L['tags_comma_separated'],
		'FORUMS_NEWTOPIC_FORM_TAGS' => cot_rc('tags_input_editpost', array('tags' => $rtags))
	));
	$t->parse('MAIN.FORUMS_NEWTOPIC_TAGS');
}

?>