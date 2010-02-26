<?php
/* ====================
[BEGIN_SED_EXTPLUGIN]
Code=comments
Part=polls.viewall.tags
File=comments.polls.viewall.tags
Hooks=polls.viewall.tags
Tags=polls.tpl:{POLLS_COMMENTS}
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

$row['poll_comcount'] = (!$row['poll_comcount']) ? "0" : $row['poll_comcount'];
$row['poll_comments'] = sed_rc_link(sed_url('polls', 'id=' . $row['poll_id'], '#comments'), sed_rc('icon_comments_cnt', array('cnt' => $row['poll_comcount'])));
$t->assign(array(
	"POLLS_COMMENTS" => $row['poll_comments']
));

?>