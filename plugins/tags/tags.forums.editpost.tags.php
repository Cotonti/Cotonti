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
 *
 * @var XTemplate $t
 * @var int $q Topic id
 * @var bool $isFirstPost
 */

use cot\modules\forums\inc\ForumsDictionary;

defined('COT_CODE') or die('Wrong URL');

if (Cot::$cfg['plugin']['tags']['forums'] && cot_auth('plug', 'tags', 'W') && $isFirstPost) {
	require_once cot_incfile('tags', 'plug');
	$tags = cot_tag_list($q, ForumsDictionary::SOURCE_TOPIC);
	$tags = implode(', ', $tags);
	$t->assign([
		'FORUMS_EDITPOST_TOP_TAGS' => Cot::$L['Tags'],
		'FORUMS_EDITPOST_TOP_TAGS_HINT' => Cot::$L['tags_comma_separated'],
		'FORUMS_EDITPOST_FORM_TAGS' => cot_rc('tags_input_editpost', ['tags' => $tags]),
	]);
	$t->parse('MAIN.FORUMS_EDITPOST_TAGS');
}
