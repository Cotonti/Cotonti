<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=forums.posts.delete.done
[END_COT_EXT]
==================== */

/**
 * Trashcan delete post
 *
 * @package trashcan
 * @version 0.9.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');
require_once cot_incfile('trashcan', 'plug');
if ($cfg['plugin']['trashcan']['trash_forum'])
{
	cot_trash_put('forumpost', $L['Post']." #".$p." from topic #".$q, $p, $row);
}
?>