<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=forums.editpost.update.first
[END_COT_EXT]
==================== */

/**
 * Polls
 *
 * @package polls
 * @version 0.7.0
 * @author esclkm, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD License
 */

defined('SED_CODE') or die('Wrong URL');
$poll = trim(sed_import('poll_text', 'P', 'HTM'));
$poll_id = sed_import('poll_id','P','TXT');

if(!empy($poll) && $poll_id)
{
	sed_poll_check();
}

?>