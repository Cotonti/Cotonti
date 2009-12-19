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
 * Part of plug tags
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Trustmaster - Vladimir Sibirov
 * @copyright All rights reserved. 2008-2009
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

if($cfg['plugin']['tags']['forums'] && sed_auth('plug', 'tags', 'W'))
{
	sed_tag_remove_all($q, 'forums');
}

?>