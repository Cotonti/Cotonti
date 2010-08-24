<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=polls.viewall.tags
Tags=polls.tpl:{POLLS_COMMENTS}
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

defined('SED_CODE') or die('Wrong URL');

sed_require('comments', true);

$t->assign(array(
	'POLLS_COMMENTS' => sed_comments_link('polls', 'id='.$row['poll_id'], 'polls', $row['poll_id'])
));

?>