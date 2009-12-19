<?php
/* ====================
[BEGIN_SED_EXTPLUGIN]
Code=tags
Part=page.delete
File=tags.page.delete
Hooks=page.edit.delete.done
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

if($cfg['plugin']['tags']['pages'] && sed_auth('plug', 'tags', 'W'))
{
	sed_tag_remove_all($id);
}

?>