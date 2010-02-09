<?php
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
 * Generates tag input when editing a forum post
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Trustmaster - Vladimir Sibirov
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

if($cfg['plugin']['tags']['forums'] && sed_auth('plug', 'tags', 'W') && $is_first_post)
{
	require_once $cfg['system_dir'] . '/tags.php';
	require_once sed_langfile('tags');
	$tags = sed_tag_list($q, 'forums');
	$tags = implode(', ', $tags);
	$t->assign(array(
		'FORUMS_EDITPOST_TOP_TAGS' => $L['Tags'],
		'FORUMS_EDITPOST_TOP_TAGS_HINT' => $L['tags_comma_separated'],
		'FORUMS_EDITPOST_FORM_TAGS' => sed_rc('tags_input_editpost')
	));
	$t->parse('MAIN.FORUMS_EDITPOST_TAGS');
}

?>