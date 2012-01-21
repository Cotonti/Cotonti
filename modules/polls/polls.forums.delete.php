<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=forums.posts.emptytopicdel, forums.functions.prunetopics
[END_COT_EXT]
==================== */

/**
 * Polls
 *
 * @package polls
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD License
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('polls', 'module');

cot_poll_delete($q, 'forum');

?>