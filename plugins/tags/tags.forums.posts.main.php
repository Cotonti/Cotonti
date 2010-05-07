<?php
/* ====================
[BEGIN_SED_EXTPLUGIN]
Code=tags
Part=forums.posts.main
File=tags.forums.posts.main
Hooks=forums.posts.main
Tags=
Order=10
[END_SED_EXTPLUGIN]
==================== */

/**
 * Generates keywords from topic tags
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Trustmaster - Vladimir Sibirov
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

if ($cfg['plugin']['tags']['forums'])
{
	require_once $cfg['system_dir'] . '/tags.php';
	require_once sed_langfile('tags', 'plug');
	require_once $cfg['plugins_dir'].'/tags/inc/resources.php';
	$tags = sed_tag_list($q, 'forums');
	$tag_keywords = implode(', ', $tags);
	$out['keywords'] = $tag_keywords;
}

?>