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
 * Removes tags when removing a page
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Trustmaster - Vladimir Sibirov
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

if($cfg['plugin']['tags']['pages'] && sed_auth('plug', 'tags', 'W'))
{
	require_once $cfg['system_dir'] . '/tags.php';
	sed_tag_remove_all($id);
}

?>