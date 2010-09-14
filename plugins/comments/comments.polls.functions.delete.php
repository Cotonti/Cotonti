<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=polls.functions.delete
[END_COT_EXT]
==================== */

/**
 * Comments system for Cotonti
 *
 * @package comments
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

cot_require('comments', true);

cot_comments_remove('polls', $id2);

?>