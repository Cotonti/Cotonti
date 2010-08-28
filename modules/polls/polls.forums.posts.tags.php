<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=forums.posts.tags
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

sed_poll_vote();
$poll_form=sed_poll_form($q, sed_url('forums', 'm=posts&q='.$q), '', 'forum');
$t->assign(array(
	"POLLS_TITLE" => sed_parse(htmlspecialchars($poll_form['poll_text']), 1, 1, 1),
	"POLLS_FORM" => $poll_form['poll_block'],
));

$t->parse("MAIN.POLLS_VIEW");

if ($poll_form['poll_alreadyvoted'])
{ 
	$extra = ($votecasted) ? $L['polls_votecasted'] : $L['polls_alreadyvoted'];
}
else
{ 
	$extra = $L['polls_notyetvoted'];
}
$t->assign("POLLS_EXTRATEXT", $extra);
$t->parse("MAIN.POLLS_EXTRA");

if (!empty($poll_form['poll_text']))
{
	$ft_title = $L['Poll'].": ".$ft_title;
}

$toptitle .= $toppath.' ' . $cfg['separator'] . ' ' . $ft_title;
$toptitle .= ($usr['isadmin']) ? " *" : '';
$t->assign(array(
	"FORUMS_POSTS_PAGETITLE" => $toptitle,
	"FORUMS_POSTS_SHORTTITLE" => $ft_title
));
?>