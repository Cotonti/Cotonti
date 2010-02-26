<?php
/* ====================
[BEGIN_SED_EXTPLUGIN]
Code=comments
Part=indexpolls.get_polls.tags
File=comments.indexpolls.get_polls.tags
Hooks=indexpolls.get_polls.tags
Tags=indexpolls.tpl:{IPOLLS_COMMENTS}
Order=10
[END_SED_EXTPLUGIN]
==================== */

/**
 * Comments system for Cotonti
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

list($comments_link, $comments_display) = sed_build_comments('v'.$poll_id, sed_url('polls', 'id='.$poll_id), true);
$indexpolls->assign(array(
	"IPOLLS_COMMENTS" => $comments_link
));

?>