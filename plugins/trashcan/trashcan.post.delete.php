<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=forums.posts.delete.done
[END_COT_EXT]
==================== */

/**
 * Trashcan delete post
 *
 * @package trash
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');
cot_require('trashcan', true);
if ($cfg['plugin']['trashcan']['trash_forum'])
{
	cot_trash_put('forumpost', $L['Post']." #".$p." from topic #".$q, "p".$p."-q".$q, $row);
}
?>