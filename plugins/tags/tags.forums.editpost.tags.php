<?PHP
/* ====================
[BEGIN_SED_EXTPLUGIN]
Code=tags
Part=forums.editpost.tags
File=tags.forums.editpost.tags
Hooks=forums.editpost.tags
Tags=forums.editpost.tpl:{FORUMS_EDITPOST_FORM_TAGS},{FORUMS_EDITPOST_TOP_TAGS},{FORUMS_EDITPOST_TOP_TAGS_HINT}
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

if($cfg['plugin']['tags']['forums'] && sed_auth('plug', 'tags', 'W') && $is_first_post)
{
	require_once(sed_langfile('tags'));
	$tags = sed_tag_list($q, 'forums');
	$tags = implode(', ', $tags);
	$t->assign(array(
	'FORUMS_EDITPOST_TOP_TAGS' => $L['tags_Tags'],
	'FORUMS_EDITPOST_TOP_TAGS_HINT' => $L['tags_comma_separated'],
	'FORUMS_EDITPOST_FORM_TAGS' => '<input type="text" name="rtags" value="' . $tags . '" />'
	));
	$t->parse('MAIN.FORUMS_EDITPOST_TAGS');
}

?>