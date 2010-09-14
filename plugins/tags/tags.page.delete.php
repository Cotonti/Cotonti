<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=page.edit.delete.done
[END_COT_EXT]
==================== */

/**
 * Removes tags when removing a page
 *
 * @package tags
 * @version 0.7.0
 * @author Trustmaster - Vladimir Sibirov
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

if ($cfg['plugin']['tags']['pages'] && cot_auth('plug', 'tags', 'W'))
{
	cot_require('tags', true);
	cot_tag_remove_all($id);
}

?>