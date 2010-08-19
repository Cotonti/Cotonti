<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=forums.topics.delete.done
[END_COT_EXT]
==================== */

/**
 * Removes tags linked to a forum post
 *
 * @package tags
 * @version 0.7.0
 * @author Trustmaster - Vladimir Sibirov
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

if ($cfg['plugin']['tags']['forums'] && sed_auth('plug', 'tags', 'W'))
{
	require_once $cfg['system_dir'] . '/tags.php';
	sed_tag_remove_all($q, 'forums');
}

?>