<?php
/* ====================
[BEGIN_SED_EXTPLUGIN]
Code=tags
Part=forums.delete
File=tags.forums.delete
Hooks=forums.topics.delete.done
Tags=
Order=10
[END_SED_EXTPLUGIN]
==================== */

/**
 * Removes tags linked to a forum post
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Trustmaster - Vladimir Sibirov
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

if($cfg['plugin']['tags']['forums'] && sed_auth('plug', 'tags', 'W'))
{
	sed_tag_remove_all($q, 'forums');
}

?>