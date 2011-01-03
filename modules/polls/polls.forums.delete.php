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
 * @author esclkm, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2011
 * @license BSD License
 */

defined('COT_CODE') or die('Wrong URL');

cot_poll_delete($q, 'forum');

?>